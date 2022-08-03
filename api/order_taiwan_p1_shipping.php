<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = 0;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;
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

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case "GET":
        $task_id = (isset($_GET['id']) ?  $_GET['id'] : 0);
        $notes = [];
        $notes = GetShipping($task_id, $db);
        $jsonEncodedReturnArray = json_encode($notes, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
        break;
    
    case 'POST':
        // get database connection
        $uid = $user_id;
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
        $shipping_way = (isset($_POST['shipping_way']) ?  $_POST['shipping_way'] : '');
        $shipping_number = (isset($_POST['shipping_number']) ?  $_POST['shipping_number'] : '');
        $eta = (isset($_POST['eta']) ?  $_POST['eta'] : '');
        $arrive = (isset($_POST['arrive']) ?  $_POST['arrive'] : '');
        $charge = (isset($_POST['charge']) ?  $_POST['charge'] : '');
        $test = (isset($_POST['test']) ?  $_POST['test'] : '');
        $delivery = (isset($_POST['delivery']) ?  $_POST['delivery'] : '');
        $final = (isset($_POST['final']) ?  $_POST['final'] : '');
        $remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
        $remark_t = (isset($_POST['remark_t']) ?  $_POST['remark_t'] : '');
        $remark_d = (isset($_POST['remark_d']) ?  $_POST['remark_d'] : '');
        $check_t = (isset($_POST['check_t']) ?  $_POST['check_t'] : '');
        $check_d = (isset($_POST['check_d']) ?  $_POST['check_d'] : '');

        if($shipping_way == 'air')
            $shipping_number = "";

    
        $query = "update od_item
        SET
            `shipping_way` = :shipping_way,
            `shipping_number` = :shipping_number,
            `eta` = :eta,
            `arrive` = :arrive,
            `charge` = :charge,
            `test` = :test,
            `delivery` = :delivery,
            `final` = :final,
            `remark` = :remark,
            `remark_t` = :remark_t,
            `remark_d` = :remark_d,
            `check_t` = :check_t,
            `check_d` = :check_d,
          
            `updated_id` = :updated_id,
            `updated_at` = now()
            where id = :id";
         

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':shipping_way', $shipping_way);
        $stmt->bindParam(':shipping_number', $shipping_number);
        $stmt->bindParam(':eta', $eta);
        $stmt->bindParam(':arrive', $arrive);
        $stmt->bindParam(':charge', $charge);
        $stmt->bindParam(':test', $test);
        $stmt->bindParam(':delivery', $delivery);
        $stmt->bindParam(':final', $final);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':remark_t', $remark_t);
        $stmt->bindParam(':remark_d', $remark_d);
        $stmt->bindParam(':check_t', $check_t);
        $stmt->bindParam(':check_d', $check_d);
       
        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $id);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}


function GetShipping($id, $db){

    $query = "
            SELECT n.id,
                n.shipping_way,
                n.shipping_number,
                n.eta,
                n.arrive,
                n.charge,
                n.test,
                n.delivery,
                n.final,
                n.remark,
                n.remart_t,
                n.remart_d,
                n.check_t,
                n.check_d,
                n.create_id,
                u.username,
                n.created_at
            FROM   od_item n
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $shipping_way = $row['shipping_way'];
        $shipping_number = $row['shipping_number'];
        $eta = $row['eta'];
        $arrive = $row['arrive'];
        $charge = $row['charge'];
        $test = $row['test'];
        $delivery = $row['delivery'];
        $final = $row['final'];
        $remark = $row['remark'];
        $remart_t = $row['remart_t'];
        $remart_d = $row['remart_d'];
        $check_t = $row['check_t'];
        $check_d = $row['check_d'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $merged_results[] = array(
            "id" => $id,
            "shipping_way" => $shipping_way,
            "shipping_number" => $shipping_number,
            "eta" => $eta,
            "arrive" => $arrive,
            "charge" => $charge,
            "test" => $test,
            "delivery" => $delivery,
            "final" => $final,
            "remark" => $remark,
            "remart_t" => $remart_t,
            "remart_d" => $remart_d,
            "check_t" => $check_t,
            "check_d" => $check_d,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,

        );
    }

    return $merged_results;
}


