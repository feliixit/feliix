<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

$user_id = 0;

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
    } catch (Exception $e) {

        error_log($e->getMessage());
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }

    $merged_results = [];
    

    $query = "SELECT id, 
                    first_line, 
                    second_line, 
                    project_category, 
                    quotation_no, 
                    quotation_date, 
                    prepare_for_first_line, 
                    prepare_for_second_line, 
                    prepare_for_third_line,
                    prepare_by_first_line,
                    prepare_by_second_line,
                    footer_first_line,
                    footer_second_line,
                    pixa_s,
                    show_s,
                    pixa_t,
                    show_t,
                    pixa_p,
                    show_p,
                    pixa_r,
                    show_r,
                    pixa_i,
                    show_i,
                    pixa_c,
                    show_c
                    FROM quotation_eng
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by quotation_eng.created_at desc ";

    if (!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (false === $page) {
            $page = 1;
        }
    }

    if (!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if (false === $size) {
            $size = 10;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $first_line = $row['first_line'];
        $second_line = $row['second_line'];
        $project_category = $row['project_category'];
        $quotation_no = $row['quotation_no'];
        $quotation_date = $row['quotation_date'];
        $prepare_for_first_line = $row['prepare_for_first_line'];
        $prepare_for_second_line = $row['prepare_for_second_line'];
        $prepare_for_third_line = $row['prepare_for_third_line'];
        $prepare_by_first_line = $row['prepare_by_first_line'];
        $prepare_by_second_line = $row['prepare_by_second_line'];
        $footer_first_line = $row['footer_first_line'];
        $footer_second_line = $row['footer_second_line'];

        $pixa_s = $row['pixa_s'] == '' ? 30 : $row['pixa_s'];
        $show_s = $row['show_s'];
        $pixa_t = $row['pixa_t'] == '' ? 30 : $row['pixa_t'];
        $show_t = $row['show_t'];
        $pixa_p = $row['pixa_p'] == '' ? 30 : $row['pixa_p'];
        $show_p = $row['show_p'];
        $pixa_r = $row['pixa_r'] == '' ? 30 : $row['pixa_r'];
        $show_r = $row['show_r'];
        $pixa_i = $row['pixa_i'] == '' ? 30 : $row['pixa_i'];
        $show_i = $row['show_i'];
        $pixa_c = $row['pixa_c'] == '' ? 30 : $row['pixa_c'];
        $show_c = $row['show_c'];

        $general_requirement = GetGeneralRequirement($row['id'], $db, $show_r, $pixa_r);
        $general_requirement_total = 0;
        foreach($general_requirement['block'] as $item)
        {
            if($item['not_show'] == '' && is_numeric($item['total']))
                $general_requirement_total += $item['total'];
        }

        $general_requirement['general_requirement_total'] = $general_requirement_total;

        $consumable = GetConsumable($row['id'], $db, $show_c, $pixa_c);
        $consumable_total = 0;
        foreach($consumable['block'] as $item)
        {
            if($item['not_show'] == '' && is_numeric($item['total']))
                $consumable_total += $item['total'];
        }

        $consumable['consumable_total'] = $consumable_total;

        
        $installation = GetInstallation($row['id'], $db, $show_i, $pixa_i);
        $installation_total = 0;
        foreach($installation['block'] as $item)
        {
            if(is_numeric($item['total']))
                $installation_total += $item['total'];
        }

        $new_consumable_ary = array();
        $skip_group = array();
        // count for row span
        foreach($installation['block'] as $key => $value)
        {
            if($installation['block'][$key]['group'] == '')
            {
                $installation['block'][$key]['gp_cnt'] = 1;
                $installation['block'][$key]['gp_cost'] = $installation['block'][$key]['labor_price'];
                $installation['block'][$key]['gp_total'] = $installation['block'][$key]['total'];

            }
            else if(array_key_exists($installation['block'][$key]['group'], $skip_group))
            {
                $installation['block'][$key]['gp_cnt'] = 0;
            }
            else
            {
                $group_ary = GetSameGroupOfItem($installation['block'], $installation['block'][$key]['group']);
                $skip_group[$installation['block'][$key]['group']] = 1;

                $installation['block'][$key]['gp_cnt'] = count($group_ary);
                $gp_cost = 0;
                $gp_total = 0;
                for($i = 0; $i < count($group_ary); $i++)
                {
                    if(is_numeric($group_ary[$i]['labor_price']))
                        $gp_cost += $group_ary[$i]['labor_price'];
                    if(is_numeric($group_ary[$i]['total']))
                        $gp_total += $group_ary[$i]['total'];
                }
                $installation['block'][$key]['gp_cost'] = $gp_cost;
                $installation['block'][$key]['gp_total'] = $gp_total;
            }
        }

        $installation['installation_total'] = $installation_total;

        $total_info = GetTotalInfo($row['id'], $db);
        $total_info['real_total'] = $general_requirement_total + $consumable_total + $installation_total;
        $total_info['back_total'] = $general_requirement_total + $consumable_total + $installation_total;
        $total_info['subtotal_info_not_show_a'] = $general_requirement_total;
        $total_info['subtotal_info_not_show_b'] = $consumable_total;
        $total_info['subtotal_info_not_show_c'] = $installation_total;

        $amount_to_word = "";
        if(is_numeric($total_info['real_total']) && ($total_info['total'] == '' || $total_info['total'] == 0))
        {
            $amount_to_word = $total_info['real_total'];
            $amount_to_word = (100 - ($total_info['discount'] != "" ? $total_info['discount'] : 0 )) / 100 * $amount_to_word;

            if($total_info['vat'] == 'Y')
                $amount_to_word += $amount_to_word * 0.12;

            if($amount_to_word != '')
                $amount_to_word = number_format($amount_to_word, 2, '.', '');
        }
        else if($total_info['total'] > 0)
            $amount_to_word = $total_info['total'];

        if($amount_to_word != '')
        {

            $words = convertNumberToWord($amount_to_word);

            // if the total is not integer
            if(substr($amount_to_word, strpos($amount_to_word, '.') + 1) != "00")
            {
                $total_info['total_text'] = $words . " and " . substr($amount_to_word, strpos($amount_to_word, '.') + 1) . "/100";
            }
            else
                $total_info['total_text'] = $words;

            if($total_info['total_text'] != '')
                $total_info['total_text'] .= " Pesos Only"; 
        }
        
        
        $term_info = GetTermInfo($row['id'], $db);
        $payment_term_info = GetPaymentTermInfo($row['id'], $db);

        $payment_method = array();
        $payment_method = explode(';', $payment_term_info['payment_method']);
        // remove empty
        $payment_method = array_filter($payment_method);
        $payment_term_info['payment_method_list'] = $payment_method;

        $sig_info = GetSigInfo($row['id'], $db);

        $work_schedule = GetWorkScheduleEng($row['id'], $db);


        // $subtotal_info = GetSubTotalInfo($row['id'], $db);
        // $subtotal_novat_a = GetSubTotalNoVatA($row['id'], $db);
        // $subtotal_novat_b = GetSubTotalNoVatB($row['id'], $db);

        // $subtotal_info_not_show_a = GetSubTotalInfoNotShowA($row['id'], $db);
        // $subtotal_info_not_show_b = GetSubTotalInfoNotShowB($row['id'], $db);

        // $total_info['back_total'] = ($subtotal_info_not_show_a + $subtotal_info_not_show_b) * (100 - $total_info['discount']) / 100;
        // if($total_info['vat'] == 'Y')
        //     $total_info['back_total'] += $subtotal_info_not_show_a * (100 - $total_info['discount']) / 100 * 0.12;

        // $total_info['back_total'] = number_format($total_info['back_total'], 2, '.', '');

        // // print
        // $product_array = GetProductItems($pages, $row['id'], $db);

        $merged_results[] = array(
            "id" => $id,
            "first_line" => $first_line,
            "second_line" => $second_line,
            "project_category" => $project_category,
            "quotation_no" => $quotation_no,
            "quotation_date" => $quotation_date,
            "prepare_for_first_line" => $prepare_for_first_line,
            "prepare_for_second_line" => $prepare_for_second_line,
            "prepare_for_third_line" => $prepare_for_third_line,
            "prepare_by_first_line" => $prepare_by_first_line,
            "prepare_by_second_line" => $prepare_by_second_line,
            "footer_first_line" => $footer_first_line,
            "footer_second_line" => $footer_second_line,

            "pixa_s" => $pixa_s,
            "show_s" => $show_s,
            "pixa_t" => $pixa_t,
            "show_t" => $show_t,
            "pixa_p" => $pixa_p,
            "show_p" => $show_p,
            "pixa_r" => $pixa_r,
            "show_r" => $show_r,
            "pixa_i" => $pixa_i,
            "show_i" => $show_i,
            "pixa_c" => $pixa_c,
            "show_c" => $show_c,

            "general_requirement" => $general_requirement,
            "installation" => $installation,
            "consumable" => $consumable,

            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "sig_info" => $sig_info,
            "work_schedule" => $work_schedule,
       
        );
    }

    if($merged_results == [])
    {
        $id = 0;
        $first_line = '';
        $second_line = '';
        $project_category = '';
        $quotation_no = '';
        $quotation_date = '';
        $prepare_for_first_line = '';
        $prepare_for_second_line = '';
        $prepare_for_third_line = '';
        $prepare_by_first_line = '';
        $prepare_by_second_line = '';
        $footer_first_line = '';
        $footer_second_line = '';
        
        $pixa_s = 30;
        $show_s = '';
        $pixa_t = 30;
        $show_t = '';
        $pixa_p = 30;
        $show_p = '';
        $pixa_r = 30;
        $show_r = '';
        $pixa_i = 30;
        $show_i = '';
        $pixa_c = 30;
        $show_c = '';

        $general_requirement = GetGeneralRequirement(0, $db, $show_r, $pixa_r);
        $general_requirement['general_requirement_total'] = 0;
       
        $consumable = GetConsumable(0, $db, $show_c, $pixa_c);
        $consumable['consumable_total'] = 0;

        $total_info = GetTotalInfo(0, $db);
        $term_info = GetTermInfo(0, $db);
        $payment_term_info = GetPaymentTermInfo(0, $db);
        $sig_info = GetSigInfo(0, $db);

        $installation = GetInstallation(0, $db, $show_i, $pixa_i);
        $installation['installation_total'] = 0;

        $work_schedule = array();

        //
        // empty 
        $merged_results[] = array(
            "id" => $id,
            "first_line" => $first_line,
            "second_line" => $second_line,
            "project_category" => $project_category,
            "quotation_no" => $quotation_no,
            "quotation_date" => $quotation_date,
            "prepare_for_first_line" => $prepare_for_first_line,
            "prepare_for_second_line" => $prepare_for_second_line,
            "prepare_for_third_line" => $prepare_for_third_line,
            "prepare_by_first_line" => $prepare_by_first_line,
            "prepare_by_second_line" => $prepare_by_second_line,
            "footer_first_line" => $footer_first_line,
            "footer_second_line" => $footer_second_line,

            "pixa_s" => $pixa_s,
            "show_s" => $show_s,
            "pixa_t" => $pixa_t,
            "show_t" => $show_t,
            "pixa_p" => $pixa_p,
            "show_p" => $show_p,
            "pixa_r" => $pixa_r,
            "show_r" => $show_r,
            "pixa_i" => $pixa_i,
            "show_i" => $show_i,
            "pixa_c" => $pixa_c,
            "show_c" => $show_c,

            "general_requirement" => $general_requirement,

            "consumable" => $consumable,

            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "sig_info" => $sig_info,

            "work_schedule" => $work_schedule,
       
        );
    }


    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

function GetSubTotalInfo($qid, $db)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from quotation_page_type_block
            WHERE `status` <> -1 and  type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}

function GetSubTotalInfoNotShowA($qid, $db)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'A'  and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'A'  and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}


function GetSubTotalInfoNotShowB($qid, $db)
{
    $total = 0;

    $query = "
            select COALESCE(sum(amount), 0) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'B'  and real_amount = 0 and status <> -1)
            union all
            select COALESCE(sum(real_amount), 0) from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'B'  and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total += $row['amt'];

    }

    return $total;
}

function GetSubTotalNoVat($qid, $db)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = ''  and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetSubTotalNoVatA($qid, $db)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'A' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetSubTotalNoVatB($qid, $db)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100) * ratio) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and block_type = 'B' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}

function GetSubTotalNoVatNotShow($qid, $db)
{
    $total = 0;

    $query = "
            select COALESCE(sum(qty * ratio * price * (1 - discount / 100)), 0) amt from quotation_page_type_block
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and status <> -1)
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}



function GetSubtotal($ary)
{
    $total = 0;
    foreach ($ary as $item) {
        $total += $item['amount'];
    }

    return $total;
}




function GetSigInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   quotation_eng_signature
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}


function GetSig($qid, $db)
{
    $query = "
        SELECT 
        id,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   quotation_signature
        WHERE  quotation_id = " . $qid . "

        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;

    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(

        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}

function GetTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        title,
        brief,
        list 
        FROM   quotation_eng_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $title = '';
    $brief = '';
    $list = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item[] = array(
            "id" => $id,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
        
    }

    $merged_results = array(
        "item" => $item,
               
    );

    return $merged_results;
}


function GetPaymentTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        payment_method,
        brief,
        list 
        FROM   quotation_eng_payment_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
 
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
    $brief = '50% Downpayment & another 50% balance a day before the delivery';
    $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":"Address: 512 Edsa near Corner Urbano Plata St., Caloocan City"}]';

    $item = json_decode($list, TRUE); 
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item = json_decode($list, TRUE); 
        
    }

    $merged_results = array(
        "payment_method" => $payment_method,
        "brief" => $brief,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetTotalInfo($qid, $db){
    $query = "
        SELECT 
        id,
        discount,
        vat,
        show_vat,
        valid,
        total,
        pixa,
        `show`,
        show_word
        FROM   quotation_eng_total
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    
    $id = 0;
    $discount = 0;
    $vat = '';
    $show_vat = '';
    $valid = '';
    $total = '';
    $pixa = 0;
    $show = '';
    $total_text = "";
    $show_word = '';

   
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];
        $pixa = $row['pixa'];
        $show = $row['show'];
        $show_word = $row['show_word'];
    }

    $merged_results = array(
        "id" => $id,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "valid" => $valid,
        "total" => $total,
        "pixa" => $pixa == '' ? 30 : $pixa,
        "show" => $show,

        "real_total" => 0,
        "total_text" => $total_text,
        "show_word" => $show_word,

        "subtotal_info_not_show_a"  => 0,
        "subtotal_info_not_show_b"  => 0,
        "subtotal_info_not_show_c"  => 0,

        "back_total" => 0,
    );

    return $merged_results;
}

function GetTypes($qid, $db){
    $query = "
        SELECT id,
        block_type,
        block_name,
        not_show,
        pixa,
        real_amount
        FROM   quotation_page_type
        WHERE  page_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];
        $pixa = $row['pixa'];
        $not_show = $row['not_show'];
        $real_amount = $row['real_amount'];

        $blocks = [];

        $blocks = GetBlocks($id, $db);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "org_id" => $id,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "pixa" => $pixa == '' ? 0 : $pixa,
            "blocks" => $blocks,
            "subtotal" => $subtotal,
        );
    }

    return $merged_results;
}

function GetBlocks($qid, $db){
    $query = "
        SELECT id,
        type_id,
        `type`,
        code,
        photo,
        photo2,
        photo3,
        qty,
        ratio,
        price,
        discount,
        amount,
        description,
        v1,
        v2,
        v3,
        v4,
        ps_var,
        listing,
        num,
        notes,
        pid
        FROM   quotation_page_type_block
        WHERE  type_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $type_id = $row['type_id'];
        $type = $row['type'];
        $code = $row['code'];
        $photo = $row['photo'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $qty = $row['qty'];
        $notes = $row['notes'];
        $ratio = $row['ratio'];
        $price = $row['price'];
        $num = $row['num'];
        $pid = $row['pid'];
        $discount = $row['discount'];
        $amount = $row['amount'];
        $description = $row['description'];
        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $v4 = $row['v4'];
        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);
        $listing = $row['listing'];

        $srp = GetProductPrice($row['pid'], $row['v1'], $row['v2'], $row['v3'], $row['v4'], $db);
    
        $type == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
        $url2 = $photo2 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo2;
        $url3 = $photo3 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo3;
  
        $merged_results[] = array(
            "id" => $id,
            "type_id" => $type_id,
            "code" => $code,
            "type" => $type,
            "photo" => $photo,
            "photo2" => $photo2,
            "photo3" => $photo3,
            "type" => $type,
            "url" => $url,
            "url2" => $url2,
            "url3" => $url3,
            "qty" => $qty,
            "notes" => $notes,
            "ratio" => $ratio,
            "num" => $num,
            "pid" => $pid,
            "price" => $price,
            "discount" => $discount,
            "amount" => $amount,
            "desc" => $description,
            "v1" => $v1,
            "v2" => $v2,
            "v3" => $v3,
            "v4" => $v4,
            "ps_var" => $ps_var,
            "list" => $listing,
            "srp" => $srp,
        );
    }

    return $merged_results;
}


function GetProducts($pid, $v1, $v2, $v3, $v4, $db)  {

    $query = "SELECT price,
            1st_variation,
            2rd_variation,
            3th_variation,
            4th_variation
        FROM   product
        WHERE  product_id = " . $pid . "
        AND `status` <> -1 
        ORDER BY id";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $price = 0;
    $val1 = "";
    $val2 = "";
    $val3 = "";
    $val4 = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price = $row['price'];
        $val1 = GetValue($row['1st_variation']);
        $val2 = GetValue($row['2rd_variation']);
        $val3 = GetValue($row['3th_variation']);
        $val4 = GetValue($row['4th_variation']);

        if($val1 == $v1 && $val2 == $v2 && $val3 == $v3 && $val4 == $v4)
            break;
    }

    return $price;

}

function GetValue($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[1]) ? $obj[1] : "";
}

function GetProductPrice($pid, $v1, $v2, $v3, $v4, $db)
{
    $srp = 0;
    $p_srp = 0;

    if($v1 != '' || $v2 != '' || $v3 != '' || $v4 != '')
        $p_srp = GetProducts($pid, $v1, $v2, $v3, $v4, $db);

    if($p_srp > 0)
    {
        $srp = $p_srp;
    }
    else
    {
        
        $query = "
            SELECT price
            FROM   product_category
            WHERE  id = " . $pid . "
            AND `status` <> -1 
            ORDER BY id
        ";

        // prepare the query
        $stmt = $db->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
            $srp = $row['price'];
        }
    }

    return $srp;
}

function GetQuotationExport($q_id, $db)
{
    $items = "[]";

    $query = "
        SELECT items
        FROM   quotation_export
        WHERE  quotation_id = " . $q_id . "
        AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $items = $row['items'];
    }

    // json to php array
    $items = json_decode($items, true);

    return $items;

}


function GetGeneralRequirement($qid, $db, $show_r, $pixa_r)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_general_requirement
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            $row['block'] = $item;
            
            $row["show_r"] = $show_r;
            $row["pixa_r"] = $pixa_r;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $block[] = array(
            "id" => 1,
            "no" => "",
            "desc" => "Mobilization/Demobilization",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 2,
            "no" => "",
            "desc" => "Project Supervision",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 3,
            "no" => "",
            "desc" => "Tools & Equipment",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 4,
            "no" => "",
            "desc" => "Functionality Test",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "General Requirements",
            "show_r" => $show_r,
            "pixa_r" => $pixa_r,
            "block" => $block,
        );
    }

    return $merged_results;
}

function GetConsumable($qid, $db, $show_c, $pixa_c)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_consumable
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            $row['block'] = $item;

            $row["show_c"] = $show_c;
            $row["pixa_c"] = $pixa_c;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $block[] = array(
            "id" => 1,
            "no" => "",
            "desc" => "Consumables (Base on BOM)",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "ratio" => "1.00",
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "Consumables",
            "show_c" => $show_c,
            "pixa_c" => $pixa_c,
            "block" => $block,
        );
    }

    return $merged_results;
}


function GetInstallation($qid, $db, $show_i, $pixa_i)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_installation
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            // add gp_cnt field to each item
            foreach($item as $key => $value)
            {
                $item[$key]['gp_cnt'] = 1;
                $item[$key]['gp_cost'] = 0;
                $item[$key]['gp_total'] = 0;
            }
             
            $row['block'] = $item;

            $row["show_i"] = $show_i;
            $row["pixa_i"] = $pixa_i;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "Lighting Fixtures Installation",
            "show_i" => $show_i,
            "pixa_i" => $pixa_i,
            "block" => [],
        );
    }

    return $merged_results;
}

function convertNumberToWord($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();

    $list1 = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven',
        'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');

    $list2 = array('', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety', 'Hundred');

    $list3 = array('', 'Thousand', 'Million', 'Billion', 'Trillion', 'Quadrillion', 'Quintillion', 'Sextillion', 'Septillion',
        'Octillion', 'Nonillion', 'Decillion', 'Undecillion', 'Duodecillion', 'Tredecillion', 'Quattuordecillion',
        'Quindecillion', 'Sexdecillion', 'Septendecillion', 'Octodecillion', 'Novemdecillion', 'Vigintillion');

    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }

    return implode(' ', $words);
}

function GetSameGroupOfItem($ary, $group) {
    $result = array();
    foreach($ary as $item)
    {
        if($item['group'] == $group)
            $result[] = $item;
    }
    return $result;
}

function GetWorkScheduleEng($id, $db)
{
    $items = array();
    $man_power = array();
    $rate_leadman = 0;
    $rate_sr_technician = 0;
    $rate_technician = 0;
    $rate_electrician = 0;
    $rate_helper = 0;

    $sum_man_power = 0;

    $query = "
        SELECT id, rate_leadman, rate_sr_technician, rate_technician, rate_electrician, rate_helper,
        man_power_weekly
        FROM   work_schedule_eng
        WHERE  quotation_id = " . $id . "
        AND `status` <> -1 order by created_at
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sum_man_power = 0;
        if($row['man_power_weekly'] != null)
            $man_power = json_decode($row['man_power_weekly'], true);
        else
            $man_power = array();

        $pid = $row['id'];
        $rate_leadman = $row['rate_leadman'];
        $rate_sr_technician = $row['rate_sr_technician'];
        $rate_technician = $row['rate_technician'];
        $rate_electrician = $row['rate_electrician'];
        $rate_helper = $row['rate_helper'];

        foreach($man_power as $item)
        {
            $sum_man_power += $item['man_power2'] * $rate_leadman;
            $sum_man_power += $item['man_power3'] * $rate_sr_technician;
            $sum_man_power += $item['man_power4'] * $rate_technician;
            $sum_man_power += $item['man_power5'] * $rate_electrician;
            $sum_man_power += $item['man_power6'] * $rate_helper;
        }

        $items[] = array("id" => $pid, "week1" => $sum_man_power, "week2" => $sum_man_power * 2, "week3" => $sum_man_power * 3);
    }

    return $items;
}