<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : '');

$id == "" ? $id = 0 : $id = $id;


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
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;

        $merged_results = array();
        // get quotation
        $merged_results = GetQuotation($id, $db);
        
        // insert quotation
        InsertQuotation($id, $user_id, $merged_results, $db);

        $db->commit();
        

        http_response_code(200);
        echo json_encode(array("message" => " Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function InsertQuotation($id, $user_id, $merged_results, $db)
{
    if(count($merged_results) == 0) {
        return;
    }

    $check_name = $merged_results[0]['check_name'];
    /* request no */
    $query = "SELECT id from office_item_inventory_modify order by id desc limit 1";

    $stmt = $db->prepare( $query );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row_id = $row['id'];
    }

    $row_id += 1;

    $request_no = "OIM-" . str_pad($row_id, 5, '0', STR_PAD_LEFT);

    $note_1 = "";
    $note_2 = "";
    $note_3 = "";
    $note_4 = "";

    $phase_1 = $merged_results[0]['phase_1'];
    $phase_2 = $merged_results[0]['phase_2'];
    $phase_3 = $merged_results[0]['phase_3'];
    $phase_4 = $merged_results[0]['phase_4'];

    $checker = 0;
    $approver = 0;

    if($phase_1 == "")
    {
        $phase_1 = "[]";
    }

    $phase_1 = UpdateQty($phase_1, $db);
    
    $query = "INSERT INTO office_item_inventory_modify
        SET
            `request_no` = :request_no,
            `check_name` = :check_name,
            `note_1` = :note_1,
            `note_2` = :note_2,
            `note_3` = :note_3,
            `note_4` = :note_4,

            `phase_1` = :phase_1,
            `phase_2` = :phase_2,
            `phase_3` = :phase_3,
            `phase_4` = :phase_4,

            `checker` = :checker,
            `approver` = :approver,

            `status` = 1,
            `create_id` = :create_id,
            `created_at` =  now()";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':request_no', $request_no);
    $stmt->bindParam(':check_name', $check_name);
    $stmt->bindParam(':note_1', $note_1);
    $stmt->bindParam(':note_2', $note_2);
    $stmt->bindParam(':note_3', $note_3);
    $stmt->bindParam(':note_4', $note_4);

    $stmt->bindParam(':phase_1', $phase_1);
    $stmt->bindParam(':phase_2', $phase_2);
    $stmt->bindParam(':phase_3', $phase_3);
    $stmt->bindParam(':phase_4', $phase_4);

    $stmt->bindParam(':checker', $checker);
    $stmt->bindParam(':approver', $approver);

    $stmt->bindParam(':create_id', $user_id);

    $last_id = 0;
    // execute the query, also check if query was successful
    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function UpdateQty($list, $db)
{
    $list = json_decode($list, true);

    foreach($list as &$item)
    {
        $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

        $sql = "select qty, reserve_qty from office_items_stock where code = '" . $code . "'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $row['qty'];
            $reserve_qty = $row['reserve_qty'];

            $item['qty'] = $qty;
            $item['reserve_qty'] = $reserve_qty;

            $item['qty2'] = "";
            $item['qty1'] = "";

            $item['sign'] = "";
            $item['sign2'] = "";

            $item['note'] = "";
            $item['comment'] = "";
        }
    }

    $list = json_encode($list);

    return $list;
}

function GetQuotation($id, $db) {
    $merged_results = array();
    

    $query = "SELECT *
                    FROM office_item_inventory_modify
                    WHERE status <> -1 and id=$id";


    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

