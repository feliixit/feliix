<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$item_id = (isset($_GET['item_id']) ?  $_GET['item_id'] : 0);
$receive_id = (isset($_GET['receive_id']) ?  $_GET['receive_id'] : 0);

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

    $merged_results = array();

    // get receive_list json from od_item
    $query = "SELECT received_list
                    FROM od_item 
                    WHERE id = :item_id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':item_id', $item_id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $received_list = $row['received_list'];

    if ($received_list == "") {
        $received_list = "{items:[]}";
    }
    if ($received_list == null) {
        $received_list = "{items:[]}";
    }

    $received_list = json_decode($received_list, true);
    
    $item = array();
    foreach ($received_list['items'] as $it) {
        if ($it['id'] == $receive_id) {
            $item = $it;
            break;
        }
    }

    $code = "";
    $brief = "";
    $listing = "";
    $project_name = "";
    $desc = "";

    if (count($item) == 0) {
        $code = "";
        $brief = "";
        $listing = "";
        $project_name = "";
        $desc = "";
    } else {
        $code = $item['code'];
        $brief = $item['brief'];
        $listing = $item['listing'];
        if(isset($item['project_name'])) {
            $project_name = $item['project_name'];
        } else {
            $project_name = "";
        }
        $desc = $item['desc'];
    }

    $cnt = 0;
    $query_cnt = "SELECT COUNT(*) as cnt
                    FROM order_receive_item rec left join order_tracking_item tra on rec.id = tra.item_id
                    WHERE rec.item_id = :item_id and rec.receive_id = :receive_id and tra.status <> -1  order by tra.barcode ";
    $stmt_cnt = $db->prepare($query_cnt);
    $stmt_cnt->bindParam(':item_id', $item_id);
    $stmt_cnt->bindParam(':receive_id', $receive_id);
    $stmt_cnt->execute();

    $row_cnt = $stmt_cnt->fetch(PDO::FETCH_ASSOC);
    $cnt = $row_cnt['cnt'];
    
    $query = "SELECT tra.id,
                    rec.id as rec_id,
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
                    tra.barcode,
                    tra.status
                    FROM order_receive_item rec left join order_tracking_item tra on rec.id = tra.item_id
                    WHERE rec.item_id = :item_id and rec.receive_id = :receive_id  and tra.status <> -1 ";

    $query .= "  order by tra.barcode ";

    $query .= " LIMIT :pg, :size ";
    $stmt = $db->prepare($query);

    if($pg == 0) {
        $pg = 0;
    } else {
        $pg = ($pg - 1) * $size;
    }
    if($size == 0) {
        $size = 10;
    } else {
        $size = $size;
    }

    $stmt->bindParam(':item_id', $item_id);
    $stmt->bindParam(':receive_id', $receive_id);
    $stmt->bindParam(':pg', $pg, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $od_id = $row['od_id'];
        $rec_id = $row['rec_id'];
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
        $barcode = $row['barcode'];
        $status = $row['status'];
        
        $merged_results[] = array(
            "is_checked" => "",
            "id" => $id,
            "od_id" => $od_id,
            "rec_id" => $rec_id,
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
            "barcode" => $barcode,
            "status" => $status,
            "brief" => $brief,
            "listing" => $listing,
            "project_name" => $project_name,
            "desc" => $desc,
            "cnt" => $cnt,
        );
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

