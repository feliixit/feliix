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

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

$logFile = 'quotation.log';

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    $merged_results = array();
    

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
                    prepare_by_third_line,
                    footer_first_line,
                    footer_second_line,
                    (SELECT COUNT(*) FROM quotation_page WHERE quotation_id = quotation.id and quotation_page.status <> -1) page_count,
                    can_view,
                    can_duplicate,
                    kind,
                    COALESCE((SELECT project_name FROM project_main WHERE id = project_id and kind = ''), '') AS project_name,
                    COALESCE((SELECT title FROM project_other_task_a WHERE id = project_id and kind = 'a'), '') AS project_name_a,
                    COALESCE((SELECT title FROM project_other_task_d WHERE id = project_id and kind = 'd'), '') AS project_name_d,
                    COALESCE((SELECT title FROM project_other_task_l WHERE id = project_id and kind = 'l'), '') AS project_name_l,
                    COALESCE((SELECT title FROM project_other_task_o WHERE id = project_id and kind = 'o'), '') AS project_name_o,
                    COALESCE((SELECT title FROM project_other_task_sl WHERE id = project_id and kind = 'sl'), '') AS project_name_sl,
                    COALESCE((SELECT title FROM project_other_task_sv WHERE id = project_id and kind = 'sv'), '') AS project_name_sv
                    FROM quotation
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by quotation.created_at desc ";

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

    //file_put_contents($logFile, $query, FILE_APPEND);

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
        $prepare_by_third_line = $row['prepare_by_third_line'];
        $footer_first_line = $row['footer_first_line'];
        $footer_second_line = $row['footer_second_line'];
        $page_count = $row['page_count'];
        $pages = GetPages($row['id'], $db);

        $block_names = GetBlockNames($row['id'], $db);
        $total_info = GetTotalInfo($row['id'], $db);
        $term_info = GetTermInfo($row['id'], $db);
        $slogan_info = GetSloganInfo($row['id'], $db);
        $payment_term_info = GetPaymentTermInfo($row['id'], $db);
        $sig_info = GetSigInfo($row['id'], $db);

        $led_driver = GetLedDriver($row['id'], $db);

        $subtotal_info = GetSubTotalInfo($row['id'], $db);
        $subtotal_novat_a = GetSubTotalNoVatA($row['id'], $db);
        $subtotal_novat_b = GetSubTotalNoVatB($row['id'], $db);

        $subtotal_info_not_show_a = GetSubTotalInfoNotShowA($row['id'], $db);
        $subtotal_info_not_show_b = GetSubTotalInfoNotShowB($row['id'], $db);

        $total_info['back_total'] = ($subtotal_info_not_show_a + $subtotal_info_not_show_b) * (100 - $total_info['discount']) / 100;
        if($total_info['vat'] == 'Y')
            $total_info['back_total'] += $subtotal_info_not_show_a * (100 - $total_info['discount']) / 100 * 0.12;

        $total_info['back_total'] = number_format($total_info['back_total'], 2, '.', '');

        // print
        $product_array = GetProductItems($pages, $row['id'], $db);

        $can_view = $row['can_view'];
        $can_duplicate = $row['can_duplicate'];

        $kind = $row['kind'];
        $project_name = $row['project_name'];
        $project_name_a = $row['project_name_a'];
        $project_name_d = $row['project_name_d'];
        $project_name_l = $row['project_name_l'];
        $project_name_o = $row['project_name_o'];
        $project_name_sl = $row['project_name_sl'];
        $project_name_sv = $row['project_name_sv'];

        if($kind == 'a')
            $project_name = $project_name_a;
        if($kind == 'd')
            $project_name = $project_name_d;
        if($kind == 'l')
            $project_name = $project_name_l;
        if($kind == 'o')
            $project_name = $project_name_o;
        if($kind == 'sl')
            $project_name = $project_name_sl;
        if($kind == 'sv')
            $project_name = $project_name_sv;

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
            "prepare_by_third_line" => $prepare_by_third_line,
            "footer_first_line" => $footer_first_line,
            "footer_second_line" => $footer_second_line,
            "page_count" => $page_count,
            "pages" => $pages,
            "block_names" => $block_names,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "slogan_info" => $slogan_info,
            "sig_info" => $sig_info,
            "subtotal_info" => $subtotal_info,
         
            "subtotal_novat_a" => $subtotal_novat_a,
            "subtotal_novat_b" => $subtotal_novat_b,
            "subtotal_info_not_show_a" => $subtotal_info_not_show_a,
            "subtotal_info_not_show_b" => $subtotal_info_not_show_b,

            "can_view" => $can_view,
            "can_duplicate" => $can_duplicate,
            "led_driver" => $led_driver,
            "product_array" => $product_array,

            "project_name" => $project_name,

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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
            select COALESCE(sum(real_amount), 0) from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'A' and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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
            select COALESCE(sum(real_amount), 0) from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and real_amount <> 0 and block_type = 'B' and status <> -1
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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
            WHERE `status` <> -1 and type_id in (select id from quotation_page_type where quotation_id = " . $qid . " and not_show = '' and status <> -1 )
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);
    
    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetBlockNames($qid, $db){
    $query = "
            SELECT qpt.id,
                qp.page,
                block_type,
                block_name,
                not_show,
                real_amount,
                page_id
            FROM   quotation_page_type qpt
            left join quotation_page qp on qpt.page_id = qp.id
            WHERE  qpt.quotation_id = " . $qid . "
            AND qpt.`status` <> -1 
            and qp.`status` <> -1
            ORDER BY qpt.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];
        $page_id = $row['page_id'];

        $not_show = $row['not_show'];
        $real_amount = $row['real_amount'];

        $blocks = [];

        $blocks = GetBlocks($id, $db);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "blocks" => $blocks,
            "page_id" => $page_id,
            "subtotal" => $subtotal,
        );
    }

    return $merged_results;
}

function GetSubtotal($ary)
{
    $total = 0;
    foreach ($ary as $item) {
        $total += $item['amount'];
    }

    return $total;
}

function GetPages($qid, $db){
    $query = "
        SELECT id,
            page
        FROM   quotation_page
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = GetTypes($id, $db);
        $total = GetTotal($qid, $page, $db);
        $term = GetTerm($qid, $page, $db);
        $slogan = GetSlogan($qid, $page, $db);
        $payment_term = GetPaymentTerm($qid, $page, $db);
        $sig = GetSig($qid, $page, $db);
  
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
            "total" => $total,
            "term" => $term,
            "slogan" => $slogan,
            "payment_term" => $payment_term,
            "sig" => $sig,
        );
    }

    return $merged_results;
}

function GetTotal($qid, $page, $db){
    $query = "
        SELECT 
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM   quotation_total
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    
    $discount = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];

        $merged_results[] = array(
            "page" => $page,
            "discount" => $discount,
            "vat" => $vat,
            "show_vat" => $show_vat,
            "valid" => $valid,
            "total" => $total,
            
            
        );
    }

    return $merged_results;
}

function GetLedDriver($qid, $db){
    $query = "
        SELECT 
        `led_driver`
        FROM   quotation_led_driver
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $led_driver = $row['led_driver'];   

        $merged_results[] = array(
            "led_driver" => $led_driver,
        );
    }

    return $merged_results;

}


function GetTerm($qid, $page, $db){
    $query = "
        SELECT 
        `page`,
        title,
        brief,
        list 
        FROM   quotation_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
    

        $merged_results[] = array(
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
    }

    return $merged_results;
}

function GetSlogan($qid, $page, $db){
    $query = "
        SELECT 
        id,
        `page`,
        border
        FROM   quotation_slogan
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $slogan = [];


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $border = $row['border'];

        $slogan = array(
            "id" => $id,
            "page" => $page,
            "border" => $border,
          
        );
    }

    return $slogan;
}


function GetPaymentTerm($qid, $page, $db){
    $query = "
        SELECT 
        `page`,
        payment_method,
        brief,
        list 
        FROM   quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $payment = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
    
        $payment_method = explode (";", $payment);
        $payment_method= array_filter($payment_method);
        $payment_method = array_map('trim', $payment_method);
        $item = json_decode($list, TRUE); 

        $merged_results = array(
            "page" => $page,
            "payment_method" => $payment_method,
            "brief" => $brief,
            "list" => $item,
          
        );
    }

    return $merged_results;
}


function GetSigInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
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
        "page" => $page,
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}


function GetSig($qid, $page, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   quotation_signature
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
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
        "page" => $page,
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
        `page`,
        title,
        brief,
        list 
        FROM   quotation_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $title = '';
    $brief = '';
    $list = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item[] = array(
            "id" => $id,
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
        
    }

    $merged_results = array(
        "page" => $page,
        "item" => $item,
               
    );

    return $merged_results;
}

function GetSloganInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        border
        FROM   quotation_slogan
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
    $page = 0;
    $border = '';

  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $border = $row['border'];
       
        $item[] = array(
            "id" => $id,
            "page" => $page,
            "border" => $border,
          
        );
        
    }

    $merged_results = array(
        "page" => $page,
        "item" => $item,
    );

    return $merged_results;
}


function GetPaymentTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        payment_method,
        brief,
        list 
        FROM   quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring; GCash';
    $brief = '50% Down payment upon order confirmation, 50% Balance upon delivery';
    $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":"Address: 512 Edsa near Corner Urbano Plata St., Caloocan City"}]';

    $item = json_decode($list, TRUE); 
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $page = $row['page'];
        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item = json_decode($list, TRUE); 
        
    }

    $merged_results = array(
        "page" => $page,
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
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM   quotation_total
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $discount = 0;
    $vat = '';
    $show_vat = '';
    $valid = '';
    $total = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];
        
        
    }

    $merged_results = array(
        "id" => $id,
        "page" => $page,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "valid" => $valid,
        "total" => $total,
        
        "real_total" => 0,

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
        real_amount
        FROM   quotation_page_type
        WHERE  page_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

        $last_order_name = "";
        $last_order_at = "";
        $last_order_url = "";
        $moq = "";
        $status = "";
        $is_last_order = "";
        if($pid != 0)
        {
            $pd = GetProductMain($pid, $db);

            if(count($pd) > 0)
            {
                $last_order_name = $pd[0]['last_order_name'];
                $last_order_at = $pd[0]['last_order_at'];
                $last_order_url = $pd[0]['last_order_url'];
                $is_last_order = $pd[0]['is_last_order'];
                $moq = $pd[0]['moq'];
                $status = $pd[0]['status'];
            }
        }
    
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
            "last_order_name" => $last_order_name,
            "last_order_at" => $last_order_at,
            "last_order_url" => $last_order_url,
            "is_last_order" => $is_last_order,
            "moq" => $moq,
            "status" => $status,
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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

    if($pid == 0)
        return 0;

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

        global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

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

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $items = $row['items'];
    }

    // json to php array
    $items = json_decode($items, true);

    return $items;

}

function GetProductItems($pages, $q_id, $db)
{
    $merged_results = [];

    $cache_results = GetQuotationExport($q_id, $db);

    foreach($pages as $page)
    {
        foreach($page['types'] as $type)
        {
            foreach($type['blocks'] as $row)
            {
            
                $id = $row['id'];
                $type_id = $row['type_id'];
                $type = $row['type'];
                $code = $row['code'];
                $photo = $row['photo'];
                $qty = $row['qty'];
                $notes = $row['notes'];
                $price = $row['price'];
                $num = $row['num'];
                $pid = $row['pid'];
                if($pid == 0)
                {
                   // search project_category for product_id 
                     $pid = GetProductId($code, $db);
                }
                $discount = $row['discount'];
                $amount = $row['amount'];
                $description = $row['desc'];
                $v1 = $row['v1'];
                $v2 = $row['v2'];
                $v3 = $row['v3'];
                $v4 = $row['v4'];
                // $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);
                $listing = $row['list'];
            
                $type == "" ? "" : "image";
                $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
            
                $merged_results[] = array(
                    "id" => $id,
                    "is_selected" => "",
                    "type_id" => $type_id,
                    "code" => $code,
                    "type" => $type,
                    "photo" => $photo,
                    "type" => $type,
                    "url" => $url,
                    "qty" => $qty,
                    "notes" => $notes,
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
                    "list" => $listing,
                );
                
            }
        }
    }

    $return_result = array();
    foreach ($cache_results as $result)
    {
        // if result[id] is in merged_results, then remove it from merged_results
        $index = array_search($result['id'], array_column($merged_results, 'id'));
        if($index !== false)
        {
            $return_result[] = $merged_results[$index];
        }
    }

    foreach ($merged_results as $result)
    {
        $index = array_search($result['id'], array_column($cache_results, 'id'));
        if($index === false)
        {
            $return_result[] = $result;
        }
     
    }

    return $return_result;
}

function GetProductId($code, $db)
{
    $pid = 0;

    $query = "
        SELECT id
        FROM   product_category
        WHERE  code = '" . $code . "'
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $query, FILE_APPEND);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $pid = $row['id'];
    }

    return $pid;
}

function getOrderInfo($od_id, $db)
{
    $sql = "select order_type, serial_name, status from od_main WHERE id = ". $od_id;

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    global $logFile;
    //file_put_contents($logFile, $sql, FILE_APPEND);

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row;
    }

    return $merged_results;
}


function GetProductMain($id, $db){

    $time_start = microtime(true); 

    // product main
    $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  p.id = ". $id;

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $sql, FILE_APPEND);

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];
        $last_order_url = "";
        $moq = $row['moq'];
        $status = $row['status'];

        $product = GetProduct($id, $db);

        // for last order
        $is_last_order_product = "";
        $order_sn = 1;
        
        for($i = 0; $i < count($product); $i++)
        {
            

            if($product[$i]['last_order_name'] != '')
            {
                $order_info = getOrderInfo($product[$i]['last_order'], $db);
                $url = "";

                if(isset($order_info["order_type"]))
                {
                    if($order_info["order_type"] == "taiwan")
                    $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $product[$i]['last_order'];
                
                    if($order_info["order_type"] == "mockup")
                        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $product[$i]['last_order'];
                    
                    if($order_info["order_type"] == "sample")
                        $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $product[$i]['last_order'];
                    
                    if($order_info["order_type"] == "stock")
                        $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $product[$i]['last_order'];
                }

                $params = str_replace("=>", " = ", $product[$i]['1st_variation']);
                if($product[$i]['2rd_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['2rd_variation']);
                if($product[$i]['3th_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['3th_variation']);
                if($product[$i]['4th_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['4th_variation']);

                $is_last_order_product .= "(" . $order_sn++ . ") " . $params . ": <br>" . substr($product[$i]['last_order_at'], 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $product[$i]['last_order_name'] . "</a><br><br>";
            }
        }


        // for last order
        $is_last_order_main = "";
        if($last_order_name != '')
        {
            $order_info = getOrderInfo($last_order, $db);
            $url = "";

            if(isset($order_info["order_type"]))
            {
                if($order_info["order_type"] == "taiwan")
                $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
            
                if($order_info["order_type"] == "mockup")
                    $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "sample")
                    $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "stock")
                    $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
            }

            $last_order_url = $url;


            $is_last_order_main = "Main Product: <br>" . substr($last_order_at, 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $last_order_name . "</a><br>";
        }

        $is_last_order = $is_last_order_main . $is_last_order_product;

        
        $merged_results[] = array( "id" => $id,
                            

                            "last_order" => $last_order,
                            "last_order_name" => $last_order_name,
                            "is_last_order" => $is_last_order,
                            "last_order_at" => substr($last_order_at,0, 10),
                            "last_order_url" => $last_order_url,
                            "last_have_spec" => true,
                            "moq" => $moq,
                            "status" => $status,

        );
    }

        $time_end = microtime(true);
    
        $execution_time = ($time_end - $time_start);
        // echo '<b>GetProduct</b> '.$execution_time.'<br/>';
        
        return $merged_results;
}



function GetProduct($id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT *, CONCAT('https://storage.googleapis.com/feliiximg/' , photo) url FROM product WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    global $logFile;
    //file_put_contents($logFile, $sql, FILE_APPEND);

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
 
        $fir = $row['1st_variation'];
        $sec = $row['2rd_variation'];
        $thi = $row['3th_variation'];
        $fth = $row['4th_variation'];

        $checked = '';
        $code = $row['code'];
        $price = $row['price'];
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price_change = $row['price_change'] != '' ? substr($row['price_change'], 0, 10) : '';
        $price_ntd_change = $row['price_ntd_change'] != '' ? substr($row['price_ntd_change'], 0, 10) : '';
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        $enabled = $row['enabled'];
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];

        if($last_order != "")
        {
            $order_info = getOrderInfo($last_order, $db);
            $last_order_url = "";

            if(isset($order_info["order_type"]))
            {
                if($order_info["order_type"] == "taiwan")
                    $last_order_url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "mockup")
                    $last_order_url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "sample")
                    $last_order_url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "stock")
                    $last_order_url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
            }
        }
        else
        {
            $last_order_url = "";
        }

        $merged_results[] = array(  "id" => $id, 
  
                                    "1st_variation" => $fir,
                                    "2rd_variation" => $sec,
                                    "3th_variation" => $thi,
                                    "4th_variation" => $fth,

                                    "checked" => $checked, 
                                    "code" => $code, 
                                    "price" => $price, 
                                    "price_ntd" => $price_ntd, 
                                    "price_org" => $price_org, 
                                    "price_ntd_org" => $price_ntd_org, 
                                    "price_change" => $price_change, 
                                    "price_ntd_change" => $price_ntd_change, 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 
                                    "enabled" => $enabled,

                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                   
                                    "file" => array( "value" => ''),
                                   "last_order" => $last_order,
                                    "last_order_name" => $last_order_name,
                                    "last_order_at" => substr($last_order_at,0, 10),
                                    "last_order_url" => $last_order_url,
                                    "last_have_spec" => true,

            );
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetProduct</b> '.$execution_time.'<br/>';
    
    return $merged_results;
}
