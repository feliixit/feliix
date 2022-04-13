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
                    footer_first_line,
                    footer_second_line,
                    (SELECT COUNT(*) FROM quotation_page WHERE quotation_id = quotation.id and quotation_page.status <> -1) page_count
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
        $page_count = $row['page_count'];
        $pages = GetPages($row['id'], $db);

        $block_names = GetBlockNames($row['id'], $db);
        $total_info = GetTotalInfo($row['id'], $db);
        $term_info = GetTermInfo($row['id'], $db);
        $payment_term_info = GetPaymentTermInfo($row['id'], $db);
        $sig_info = GetSigInfo($row['id'], $db);

        $subtotal_info = GetSubTotalInfo($row['id'], $db);
        $subtotal_novat = GetSubTotalNoVat($row['id'], $db);

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
            "page_count" => $page_count,
            "pages" => $pages,
            "block_names" => $block_names,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "sig_info" => $sig_info,
            "subtotal_info" => $subtotal_info,
            "subtotal_novat" => $subtotal_novat
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

function GetSubTotalInfo($qid, $db)
{
    $total = 0;

    $query = "
            select sum(amount) amt from quotation_page_type_block
            WHERE type_id in (select id from quotation_page_type where quotation_id = " . $qid . ")
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


function GetSubTotalNoVat($qid, $db)
{
    $total = 0;

    $query = "
            select sum(qty * price * (1 - discount / 100)) amt from quotation_page_type_block
            WHERE type_id in (select id from quotation_page_type where quotation_id = " . $qid . ")
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


function GetBlockNames($qid, $db){
    $query = "
            SELECT qpt.id,
                qp.page,
                block_type,
                block_name,
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

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];
        $page_id = $row['page_id'];

        $blocks = [];

        $blocks = GetBlocks($id, $db);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "type" => $block_type,
            "name" => $block_name,
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

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = GetTypes($id, $db);
        $total = GetTotal($qid, $page, $db);
        $term = GetTerm($qid, $page, $db);
        $payment_term = GetPaymentTerm($qid, $page, $db);
        $sig = GetSig($qid, $page, $db);
  
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
            "total" => $total,
            "term" => $term,
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
                "url" =>  $photo != '' ? 'https://storage.cloud.google.com/feliiximg/' . $photo : '',
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
                "url" =>  $photo != '' ? 'https://storage.cloud.google.com/feliiximg/' . $photo : '',
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

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
    $brief = '50% Downpayment & another 50% balance a day before the delivery';
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
        
        
    );

    return $merged_results;
}

function GetTypes($qid, $db){
    $query = "
        SELECT id,
        block_type,
        block_name
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

        $blocks = [];

        $blocks = GetBlocks($id, $db);
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "org_id" => $id,
            "type" => $block_type,
            "name" => $block_name,
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
        qty,
        price,
        discount,
        amount,
        description,
        listing
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
        $qty = $row['qty'];
        $price = $row['price'];
        $discount = $row['discount'];
        $amount = $row['amount'];
        $description = $row['description'];
        $listing = $row['listing'];
    
        $type = $photo == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.cloud.google.com/feliiximg/" . $photo;
  
        $merged_results[] = array(
            "id" => $id,
            "type_id" => $type_id,
            "code" => $code,
            "type" => $type,
            "photo" => $photo,
            "type" => $type,
            "url" => $url,
            "qty" => $qty,
            "price" => $price,
            "discount" => $discount,
            "amount" => $amount,
            "desc" => $description,
            "list" => $listing,
          
        );
    }

    return $merged_results;
}
