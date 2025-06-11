<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$pg = (isset($_GET['pg']) ?  $_GET['pg'] : 0);
$size = (isset($_GET['size']) ?  $_GET['size'] : 10);

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

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $GLOBALS["user_id"] = $decoded->data->id;

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

    // $code = "";
    // $brief = "";
    // $listing = "";
    // $project_name = "";
    // $desc = "";

    // if (count($item) == 0) {
    //     $code = "";
    //     $brief = "";
    //     $listing = "";
    //     $project_name = "";
    //     $desc = "";
    // } else {
    //     $code = $item['code'];
    //     $brief = $item['brief'];
    //     $listing = $item['listing'];
    //     if(isset($item['project_name'])) {
    //         $project_name = $item['project_name'];
    //     } else {
    //         $project_name = "";
    //     }
    //     $desc = $item['desc'];
    // }

    $query_cnt = "SELECT COUNT(*) as cnt
                    FROM order_receive_item rec 
                    LEFT JOIN product_category pc ON rec.product_id = pc.id
                    WHERE from_old = 'Y' and rec.status <> -1 ";

    
    $query = "SELECT rec.id,
                    rec.od_id,
                    rec.item_id,
                    rec.receive_id,
                    rec.product_id,
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
                    rec.remark_old,
                    rec.status,
                    cuser.username as created_by,
                    uuser.username as updated_by,
                    rec.created_at,
                    rec.updated_at
                    FROM order_receive_item rec 
                    LEFT JOIN product_category pc ON rec.product_id = pc.id
                    LEFT JOIN user cuser ON rec.create_id = cuser.id
                    LEFT JOIN user uuser ON rec.updated_id = uuser.id
                    WHERE from_old = 'Y' and rec.status <> -1  ";

    if($fil_prod_id != "")
    {
        $query = $query . " and rec.product_id = '" . $fil_prod_id . "' ";
        $query_cnt = $query_cnt . " and rec.product_id = '" . $fil_prod_id . "' ";
    }

    if($fil_prod_code != "")
    {
        $query = $query . " and pc.`code` like '%" . $fil_prod_code . "%' ";
        $query_cnt = $query_cnt . " and pc.`code` like '%" . $fil_prod_code . "%' ";
    }

    if($fil_pool != "")
    {

        $query = $query . " and rec.which_pool = '" . $fil_pool . "' ";
        $query_cnt = $query_cnt . " and rec.which_pool = '" . $fil_pool . "' ";
    }

    if($fil_location != "")
    {
        $query = $query . " and rec.location = '" . $fil_location . "' ";
        $query_cnt = $query_cnt . " and rec.location = '" . $fil_location . "' ";
    }

    if($fil_project_related != "")
    {
        $query = $query . " and rec.project_id = '" . $fil_project_related . "' ";
        $query_cnt = $query_cnt . " and rec.project_id = '" . $fil_project_related . "' ";
    }

    if($fil_sample != "")
    {
        $query = $query . " and rec.as_sample = '" . $fil_sample . "' ";
        $query_cnt = $query_cnt . " and rec.as_sample = '" . $fil_sample . "' ";
    }

    if($fil_date_from != "")
    {
        $query = $query . " and STR_TO_DATE(rec.received_date, '%Y-%m-%d') >= '" . $fil_date_from . "' ";
        $query_cnt = $query_cnt . " and STR_TO_DATE(rec.received_date, '%Y-%m-%d') >= '" . $fil_date_from . "' ";
    }

    if($fil_date_to != "")
    {
        $query = $query . " and STR_TO_DATE(rec.received_date, '%Y-%m-%d') <= '" . $fil_date_to . "' ";
        $query_cnt = $query_cnt . " and STR_TO_DATE(rec.received_date, '%Y-%m-%d') <= '" . $fil_date_to . "' ";
    }

        
    $sOrder = "";
    if($op1 != "" && $op1 != "0")
    {
        switch ($op1)
        {
            case 1:
                if($od1 == 2)
                    $sOrder = "Coalesce(rec.created_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(rec.created_at, '9999-99-99') ";
                break;  
            case 2:
                if($od1 == 2)
                    $sOrder = "Coalesce(rec.updated_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(rec.updated_at, '9999-99-99') ";
                break;  
            case 3:
                if($od1 == 2)
                    $sOrder = "rec.product_id desc";
                else
                    $sOrder = "rec.product_id ";
                break;  
            case 4:
                if($od1 == 2)
                    $sOrder = "Coalesce(rec.received_date, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(rec.received_date, '9999-99-99') ";
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
                    $sOrder .= ", Coalesce(rec.created_at, '0000-00-00') desc";
                else
                    $sOrder .= ", Coalesce(rec.created_at, '9999-99-99') ";
                break;  
            case 2:
                if($od2 == 2)
                    $sOrder .= ", Coalesce(rec.updated_at, '0000-00-00') desc";
                else
                    $sOrder .= ", Coalesce(rec.updated_at, '9999-99-99') ";
                break;  
            case 3:
                if($od1 == 2)
                    $sOrder = ", rec.product_id desc";
                else
                    $sOrder = ", rec.product_id ";
                break;  
            case 4:
                if($od2 == 2)
                    $sOrder .= ", Coalesce(rec.received_date, '0000-00-00') desc";
                else
                    $sOrder .= ", Coalesce(rec.received_date, '9999-99-99') ";
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
        $query = $query . " order by rec.created_at desc ";


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


    $stmt = $db->prepare($query);
    $stmt->execute();

    $cnt = 0;
    $stmt_cnt = $db->prepare($query_cnt);
    $stmt_cnt->execute();
    $row_cnt = $stmt_cnt->fetch(PDO::FETCH_ASSOC);
    $cnt = $row_cnt['cnt'];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $od_id = $row['od_id'];

        $item_id = $row['item_id'];
        $receive_id = $row['receive_id'];
        $product_id = $row['product_id'];
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

        $remark_old = $row['remark_old'];

        $created_by = $row['created_by'];
        $updated_by = $row['updated_by'];

        $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];

        $desc = [];

        if($remark_old != '') 
            $desc = json_decode($remark_old, true);
        
        $code = "";
        $brief = "";
        $listing = "";
        $project_name = "";
        $old_desc = "";
        $photo1 = "";
        $receive_date = "";

        if($desc['code'] != '') 
            $code = $desc['code'];

        if($desc['brief'] != '') 
            $brief = $desc['brief'];

        if($desc['listing'] != '') 
            $listing = $desc['listing'];

        if($desc['project_name'] != '') 
            $project_name = $desc['project_name'];

        if($desc['desc'] != '') 
            $old_desc = $desc['desc'];

        if($desc['photo1'] != '') 
            $photo1 = $desc['photo1'];

        if($desc['receive_date'] != '')
            $receive_date = $desc['receive_date'];
     

        $barcodes = [];

        $barcodes = GetBarcodes($id, $db, $desc);
        
        $merged_results[] = array(
            "is_checked" => "",
            "id" => $id,
            "photo1" => $photo1,
            "od_id" => $od_id,
            "item_id" => $item_id,
            "receive_id" => $receive_id,
            "product_id" => $product_id,
            "code" => $code,
            "v1" => $v1,
            "v2" => $v2,
            "v3" => $v3,
            "v4" => $v4,
            "pic" => $pic,
            "qty" => $qty,
            "which_pool" => $which_pool,
            "as_sample" => $as_sample,
            "location" => $location,
            "project_id" => $project_id,
            "status" => 1,
            "brief" => $brief,
            "listing" => $listing,
            "project_name" => $project_name,
            "desc" => $old_desc,
            "receive_date" => $receive_date,
            "info" => $desc,
            "barcodes" => $barcodes,
            "created_by" => $created_by,
            "updated_by" => $updated_by,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
            "cnt" => $cnt,
        );
    
    }
    
    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}


function GetBarcodes($item_id, $db, $desc)
{
    $cnt = 0;
    $sql = "SELECT * FROM order_tracking_item WHERE item_id = :id and STATUS <> -1 order by barcode";

    $sql_cnt = "SELECT COUNT(*) as cnt FROM order_tracking_item WHERE item_id = :id and STATUS <> -1";
    
    $stmt = $db->prepare( $sql );
    $stmt->bindParam(':id', $item_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt_cnt = $db->prepare( $sql_cnt );
    $stmt_cnt->bindParam(':id', $item_id, PDO::PARAM_INT);
    $stmt_cnt->execute();

    $row_cnt = $stmt_cnt->fetch(PDO::FETCH_ASSOC);
    $cnt = $row_cnt['cnt'];
    
    $merged_results = array();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $barcode = $row['barcode'];
        $id = $row['id'];
        $status = $row['status'];

        $merged_results[] = array(
            "is_checked" => "",
            "id" => $id,
            "od_id" => 0,
            "rec_id" => 0,
            "item_id" => $item_id,
            "receive_id" => 0,
            "product_id" => $desc['pid'],
            "code" => $desc['code'],
            "v1" => $desc['v1'],
            "v2" => $desc['v2'],
            "v3" => $desc['v3'],
            "v4" => $desc['v4'],
            "pic" => $desc['photo1'],
            "qty" =>  $desc['qty'],
            "which_pool" => $desc['which_pool'],
            "as_sample" => $desc['as_sample'],
            "location" => $desc['location'],
            "project_id" => $desc['project_id'],
            "barcode" => $barcode,
            "status" => $status,
            "brief" => $desc['brief'],
            "listing" => $desc['listing'],
            "project_name" => $desc['project_name'],
            "desc" => $desc['desc'],
            "cnt" => $cnt,
        );
    }
    
    return $merged_results;
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
