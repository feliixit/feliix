<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
use Google\Service\BigtableAdmin\Split;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$fil_tracking = (isset($_GET['tid']) ?  $_GET['tid'] : '');
$fil_prod_id = (isset($_GET['fpi']) ?  $_GET['fpi'] : '');
$fil_prod_code = (isset($_GET['fpc']) ?  $_GET['fpc'] : '');
$fil_prod_code = urldecode($fil_prod_code);
$fil_pool = (isset($_GET['fp']) ?  $_GET['fp'] : '');
$fil_project_related = (isset($_GET['fpr']) ?  $_GET['fpr'] : '');
$fil_project_related = urldecode($fil_project_related);
$fil_location = (isset($_GET['loc']) ?  $_GET['loc'] : '');
$fil_sample = (isset($_GET['sap']) ?  $_GET['sap'] : '');
$fil_status = (isset($_GET['fs']) ? $_GET['fs'] : '');
$fil_order = (isset($_GET['fo']) ? $_GET['fo'] : '');
$fil_date_from = (isset($_GET['fdf']) ? $_GET['fdf'] : '');
$fil_date_to = (isset($_GET['fdt']) ? $_GET['fdt'] : '');


$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : "");
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : "");


$merged_results = array();

$query = "SELECT tra.id,
                    rec.id as rec_id,
                    rec.od_id,
                    rec.item_id,
                    rec.receive_id,
                    rec.product_id,
                    pc.`code`,
                    pc.brand,
                    od.od_name,
                    od.task_id,
                    od.order_type,
                    od.serial_name,
                    oi.received_list,
                    pm.project_name,
                    rec.v1,
                    rec.v2,
                    rec.v3,
                    rec.v4,
                    rec.pic,
                    rec.qty,
                    rec.which_pool,
                    rec.as_sample,
                    rec.location,
                    rec.project_id,
                    tra.barcode,
                    tra.status,
                    c_user.username AS created_by, 
                    u_user.username AS updated_by,
                    DATE_FORMAT(rec.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                    DATE_FORMAT(rec.updated_at, '%Y-%m-%d %H:%i:%s') updated_at,
                    tra.which_pool as tra_which_pool,
                    tra.location as tra_location,
                    tra.project_id as tra_project_id,
                    tra.as_sample as tra_as_sample,
                    DATE_FORMAT(tra.updated_at, '%Y-%m-%d %H:%i:%s') as tra_updated_at,
                    t_user.username AS tra_updated_by
                    FROM order_receive_item rec left join order_tracking_item tra on rec.id = tra.item_id
                    LEFT JOIN product_category pc ON rec.product_id = pc.id
                    LEFT JOIN od_main od ON rec.od_id = od.id
                    Left JOIN od_item oi ON rec.item_id = oi.id
                    Left JOIN project_main pm ON rec.project_id = pm.id
                    LEFT JOIN user c_user ON rec.create_id = c_user.id 
                    LEFT JOIN user u_user ON rec.updated_id = u_user.id 
                    LEFT JOIN user t_user ON tra.updated_id = t_user.id 
                    WHERE 1=0 ";

// if($all != "all")
// {
//     $query = $query . "  ";
// }

$query_cnt = "SELECT COUNT(*) as cnt
                    FROM order_receive_item rec left join order_tracking_item tra on rec.id = tra.item_id
                    LEFT JOIN product_category pc ON rec.product_id = pc.id
                    LEFT JOIN od_main od ON rec.od_id = od.id
                    Left JOIN od_item oi ON rec.item_id = oi.id
                    LEFT JOIN user c_user ON rec.create_id = c_user.id 
                    LEFT JOIN user u_user ON rec.updated_id = u_user.id 
                    WHERE 1=0 ";



if($fil_tracking != "")
{
    $tracking_ids = explode(';', $fil_tracking);

    $tracking_sql = implode("','", $tracking_ids);
    $tracking_sql = str_replace(" ", "", $tracking_sql);

    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and tra.barcode in ('" . $tracking_sql . "') ";
    $query_cnt = $query_cnt . " and tra.barcode in ('" . $tracking_sql . "') ";
}

if($fil_prod_id != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.product_id = '" . $fil_prod_id . "' ";
    $query_cnt = $query_cnt . " and rec.product_id = '" . $fil_prod_id . "' ";
}

if($fil_prod_code != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and pc.`code` like '%" . $fil_prod_code . "%' ";
    $query_cnt = $query_cnt . " and pc.`code` like '%" . $fil_prod_code . "%' ";
}

if($fil_pool != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.which_pool = '" . $fil_pool . "' ";
    $query_cnt = $query_cnt . " and rec.which_pool = '" . $fil_pool . "' ";
}

if($fil_location != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.location = '" . $fil_location . "' ";
    $query_cnt = $query_cnt . " and rec.location = '" . $fil_location . "' ";
}

if($fil_project_related != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.project_id = '" . $fil_project_related . "' ";
    $query_cnt = $query_cnt . " and rec.project_id = '" . $fil_project_related . "' ";
}

if($fil_sample != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.as_sample = '" . $fil_sample . "' ";
    $query_cnt = $query_cnt . " and rec.as_sample = '" . $fil_sample . "' ";
}

if($fil_status != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and tra.status = '" . $fil_status . "' ";
    $query_cnt = $query_cnt . " and tra.status = '" . $fil_status . "' ";
}

if($fil_order != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and rec.od_id = '" . $fil_order . "' ";
    $query_cnt = $query_cnt . " and rec.od_id = '" . $fil_order . "' ";
}

if($fil_date_from != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);

    $query = $query . " and STR_TO_DATE(rec.created_at, '%Y-%m-%d') >= '" . $fil_date_from . "' ";
    $query_cnt = $query_cnt . " and STR_TO_DATE(rec.created_at, '%Y-%m-%d') >= '" . $fil_date_from . "' ";
}

if($fil_date_to != "")
{
    // remove 1=0 from query and query_cnt
    $query = str_replace("1=0", "1=1", $query);
    $query_cnt = str_replace("1=0", "1=1", $query_cnt);
    
    $query = $query . " and STR_TO_DATE(rec.created_at, '%Y-%m-%d') <= '" . $fil_date_to . "' ";
    $query_cnt = $query_cnt . " and STR_TO_DATE(rec.created_at, '%Y-%m-%d') <= '" . $fil_date_to . "' ";
}


$sOrder = "";
if($op1 != "" && $op1 != "0")
{
    switch ($op1)
    {
        case 1:
            if($od1 == 2)
                $sOrder = "tra.barcode desc";
            else
                $sOrder = "tra.barcode ";
            break;  
        case 2:
            if($od1 == 2)
                $sOrder = "Coalesce(rec.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(rec.created_at, '9999-99-99') ";
            break;  
        
        default:
    }
}

if($op2 != "" && $op2 != "0" && $sOrder != "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder .= ", tra.barcode desc";
            else
                $sOrder .= ", tra.barcode ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder .= ", Coalesce(rec.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(rec.created_at, '9999-99-99') ";
            break;  
        
        default:
    }
}


if($op2 != "" && $op2 != "0" && $sOrder == "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder = "tra.barcode desc";
            else
                $sOrder = "tra.barcode ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder = "Coalesce(rec.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(rec.created_at, '9999-99-99') ";
            break;  
        
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by tra.created_at desc ";


if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}

$stmt = $db->prepare( $query );
$stmt->execute();

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();
while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $rec_id = $row['rec_id'];
    $od_id = $row['od_id'];
    $item_id = $row['item_id'];
    $receive_id = $row['receive_id'];
    $product_id = $row['product_id'];
    $code = $row['code'];
    $brand = $row['brand'];
    $v1 = $row['v1'];
    $v2 = $row['v2'];
    $v3 = $row['v3'];
    $v4 = $row['v4'];
    $pic = $row['pic'];
    $qty = $row['qty'];
    $which_pool = $row['which_pool'];
    $as_sample = $row['as_sample'];
    $location = $row['location'];
    $project_id = $row['project_id'];
    $barcode = $row['barcode'];
    $format_bar = substr($barcode, 0, 6) . ' ' . substr($barcode, 6, 5) . ' ' . substr($barcode, 11);
    $created_by = $row['created_by'];
    $updated_by = $row['updated_by'];
    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];

    $tra_which_pool = $row['tra_which_pool'];
    $tra_location = $row['tra_location'];
    $tra_project_id = $row['tra_project_id'];
    $tra_as_sample = $row['tra_as_sample'];

    $tra_updated_at = $row['tra_updated_at'];
    $tra_updated_by = $row['tra_updated_by'];

    if($tra_which_pool != "")
    {
        $which_pool = $tra_which_pool;
    }
    if($tra_location != "")
    {
        $location = $tra_location;
    }
    
    if($tra_as_sample != "")
    {
        $as_sample = $tra_as_sample;
    }

    if($tra_updated_at != "")
    {
        $updated_at = $tra_updated_at;
    }
    if($tra_updated_by != "")
    {
        $updated_by = $tra_updated_by;
    }

    $status = $row['status'];
    $status_text = getStatus($status);

    $receive_list = $row['received_list'];

    if ($receive_list == "") {
        $receive_list = "{items:[]}";
    }
    if ($receive_list == null) {
        $receive_list = "{items:[]}";
    }

    $receive_json = json_decode($receive_list, true);

    $item_1 = array();
    if($receive_json != null && isset($receive_json['items']))
    {
        foreach ($receive_json['items'] as $it) {
        if ($it['id'] == $receive_id) {
            $item_1 = $it;
            break;
        }
    }
    }
    

    $remark = "";
    $listing = "";

    if($item_1 != null)
    {
        if($item_1['brief'] != "")
        {
            $listing = $item_1['brief'];
        }
        if($item_1['listing'] != "")
        {
            $listing = $item_1['listing'];
        }
    }

    if (count($item_1) > 0) {
        $remark = $item_1['desc'];
    }

    $order_url = "";
    if($row['order_type'] == 'taiwan')
    {
        $order_url = "order_taiwan_p4?id=" . $od_id;
    }
    if($row['order_type'] == 'stock')
    {
        $order_url = "order_taiwan_stock_p4?id=" . $od_id;
    }
    if($row['order_type'] == 'sample')
    {
        $order_url = "order_taiwan_sample_p4?id=" . $od_id;
    }
    if($row['order_type'] == 'mockup')
    {
        $order_url = "order_taiwan_mockup_p4?id=" . $od_id;
    }

    $order_name = $row['serial_name'] . " " . $row['od_name'];

    $project_id = $row['project_id'];
    $project_name = $row['project_name'];

    if($tra_project_id != 0)
    {
        $project_id = $tra_project_id;
        $project_name = getProjectName($db, $tra_project_id);
    }

    $merged_results[] = array(
        "is_edited" => 1,
        "id" => $id,
        "rec_id" => $rec_id,
        "od_id" => $od_id,
        "item_id" => $item_id,
        "receive_id" => $receive_id,
        "product_id" => $product_id,
        "project_id" => $project_id,
        "project_name" => $project_name,
        "code" => $code,
        "brand" => $brand,
        "v1" => $v1,
        "v2" => $v2,
        "v3" => $v3,
        "v4" => $v4,
        "pic" => $pic,
        "qty" => $qty,
        "which_pool" => $which_pool,
        "as_sample" => $as_sample,
        "location" => $location,
        "barcode" => $barcode,
        "format_bar" => $format_bar,
        "created_by" => $created_by,
        "updated_by" => $updated_by,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
        "status" => $status,
        "status_text" => $status_text,
        "order_url" => $order_url,
        "order_name" => $order_name,
        "remark" => $remark,
        "listing" => $listing,
        "cnt" => $cnt,
    );
}


echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

function getStatus($status)
{
    switch ($status) {
        case 0:
            return "On Hand";
        case 1:
            return "Lost";
        case 2:
            return "Delivered to Client";
        case 3:
            return "Scrapped";
        case -1:
            return "Voided";
        default:
            return "";
    }
}

function getProjectName($db, $id)
{
    $project_name = "";
    $query = "SELECT project_name FROM project_main WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $project_name = $row['project_name'];
    }
    return $project_name;
}

?>