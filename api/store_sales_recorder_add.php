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

$sales_date = (isset($_POST['sales_date']) ?  $_POST['sales_date'] : '');
$sales_name = (isset($_POST['sales_name']) ?  $_POST['sales_name'] : '');
$customer_name = (isset($_POST['customer_name']) ?  $_POST['customer_name'] : '');
$discount = (isset($_POST['discount']) ?  $_POST['discount'] : '');
$invoice = (isset($_POST['invoice']) ?  $_POST['invoice'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$payment_method = (isset($_POST['payment_method']) ?  $_POST['payment_method'] : '');
$teminal = (isset($_POST['teminal']) ?  $_POST['teminal'] : '');

$payment = (isset($_POST['payment']) ?  $_POST['payment'] : '[]');
$payment_array = json_decode($payment, true);


try {
    // now you can apply
    $query = "INSERT INTO store_sales
        SET
    `sales_date` = :sales_date,
    `sales_name` = :sales_name,
    `customer_name` = :customer_name,
    `invoice` = :invoice, ";
    if ($discount != ''  && !is_null($discount)) {
        $query .= "`discount` = :discount, ";
    }

    $query .= "
    `remark` = :remark,
    `payment_method` = :payment_method,
    `teminal` = :teminal,
    `status` = 1,
    `crt_user` = :crt_user,
    `crt_time` = now()";


    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':sales_date', $sales_date);
    $stmt->bindParam(':sales_name', $sales_name);
    $stmt->bindParam(':customer_name', $customer_name);
    $stmt->bindParam(':invoice', $invoice);
    if ($discount != '' && !is_null($discount)) {
        $stmt->bindParam(':discount', $discount);
    }

    $stmt->bindParam(':remark', $remark);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':teminal', $teminal);
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
        $query = "INSERT INTO store_sales_record
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
