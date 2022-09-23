<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);

$_id = isset($_POST['note']) ?  $_POST['note'] : 0;
$act = isset($_POST['act']) ?  $_POST['act'] : 0;

$sales_date = (isset($_POST['sales_date']) ?  $_POST['sales_date'] : '');
$company = (isset($_POST['company']) ?  $_POST['company'] : '');
$client = (isset($_POST['client']) ?  $_POST['client'] : '');
$sales_name = (isset($_POST['sales_name']) ?  $_POST['sales_name'] : '');
$total_amount = (isset($_POST['total_amount']) ?  $_POST['total_amount'] : 0);
$po = (isset($_POST['po']) ?  $_POST['po'] : '');
$dr = (isset($_POST['dr']) ?  $_POST['dr'] : '');
$note = (isset($_POST['note']) ?  $_POST['note'] : '');

$payment = (isset($_POST['payment']) ?  $_POST['payment'] : '[]');
$payment_array = json_decode($payment, true);

$total_amount == '' ? $total_amount = 0 : $total_amount = $total_amount;

if($act == 1 && $_id != 0) {
    $sql = "UPDATE store_sales_recorder SET sales_date = :sales_date, company = :company, client = :client, sales_name = :sales_name, total_amount = :total_amount, po = :po, dr = :dr, note = :note WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $_id);
    $stmt->bindParam(':sales_date', $sales_date);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':client', $client);
    $stmt->bindParam(':sales_name', $sales_name);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':po', $po);
    $stmt->bindParam(':dr', $dr);
    $stmt->bindParam(':note', $note);
    $stmt->execute();
    $stmt->closeCursor();

    $stmt = null;
    $sql = "DELETE FROM store_sales_recorder_payment WHERE sales_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $_id);
    $stmt->execute();
    $stmt->closeCursor();
    $stmt = null;

    $sql = "INSERT INTO store_sales_recorder_payment (sales_id, payment_date, payment_amount, payment_note) VALUES (:sales_id, :payment_date, :payment_amount, :payment_note)";
    $stmt = $db->prepare($sql);
    foreach($payment_array as $payment) {
        $stmt->bindParam(':sales_id', $_id);
        $stmt->bindParam(':payment_date', $payment['payment_date']);
        $stmt->bindParam(':payment_amount', $payment['payment_amount']);
        $stmt->bindParam(':payment_note', $payment['payment_note']);
        $stmt->execute();
    }
    $stmt->closeCursor();
    $stmt = null;

    $db->commit();
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Sales Recorder Updated."));

    die();
} 

try {
    // now you can apply
    $query = "INSERT INTO store_sales_lai
        SET
    `sales_date` = :sales_date,
    `company` = :company,
    `client` = :client,
    `sales_name` = :sales_name, 
    `total_amount` = :total_amount,
    `po` = :po,
    `dr` = :dr,
    `note` = :note,
    `status` = 1,
    `crt_user` = :crt_user,
    `crt_time` = now()";


    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':sales_date', $sales_date);
    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':client', $client);
    $stmt->bindParam(':sales_name', $sales_name);
 
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':po', $po);
    $stmt->bindParam(':dr', $dr);
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':crt_user', $user_name);

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

    for ($i = 0; $i < count($payment_array); $i++) {
        $query = "INSERT INTO store_sales_record_lai
            SET
                `sales_id` = :sales_id,
                `product_name` = :product_name,
                `price` = :price,
                `qty` = :qty,
                `free` = :free,
                
                `status` = 1,
                `crt_time` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':sales_id', $last_id);
        $stmt->bindParam(':product_name', $payment_array[$i]['product_name']);
        $stmt->bindParam(':price', $payment_array[$i]['price']);
        $stmt->bindParam(':qty', $payment_array[$i]['qty']);
        $stmt->bindParam(':free', $payment_array[$i]['free']);

        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
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


    $db->commit();

    http_response_code(200);
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
} catch (Exception $e) {

    error_log($e->getMessage());
    $db->rollback();
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
    die();
}
