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
        $item_str = (isset($_POST['items']) ?  $_POST['items'] : []);
        $uid = $user_id;
        $o_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
        $type = (isset($_POST['type']) ? $_POST['type'] : '');

        $items = json_decode($item_str, true);

        $diff = [];
       
        for($i=0; $i<count($items); $i++) {

            $id = $items[$i]['id'];

            $pre_item = GetShipping($id, $db);

            add_process($o_id, $comment, $type, json_encode($pre_item, JSON_PRETTY_PRINT), $uid, $db);

            if(count($pre_item) > 0)
            {
                $ret = show_diff($pre_item[0], $items[$i]);
                if($ret != "")
                {
                    $diff[] = $ret;
                }
            }

            $shipping_way = $items[$i]['shipping_way'];
            $shipping_number = $items[$i]['shipping_number'];
            $eta = $items[$i]['eta'];
            $arrive = $items[$i]['arrive'];
            $charge = $items[$i]['charge'];
            $test = $items[$i]['test'];
            $delivery = $items[$i]['delivery'];
            $final = $items[$i]['final'];
            $remark = $items[$i]['remark'];
            $remark_t = $items[$i]['remark_t'];
            $remark_d = $items[$i]['remark_d'];
            $check_t = $items[$i]['check_t'];
            $check_d = $items[$i]['check_d'];

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

        }

        $jsonEncodedReturnArray = json_encode($diff, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}

function show_diff($pre_item, $item)
{
    $diff = [];

    foreach(array_keys($pre_item) as $key) {
        if(array_key_exists($key, $item)) {
            if($pre_item[$key] != $item[$key]) {
                $diff[] = "'" . $key . "' : '" . $pre_item[$key] . " -> " . $item[$key] . "'";
            }
        }
    }

    if(count($diff) > 0)
        $diff = "'id' : " . $pre_item['id'] . ", 'items':"  . implode(",",$diff);
    else
        $diff = "";

    return $diff;
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
            n.remark_t,
            n.remark_d,
            n.check_t,
            n.check_d,
            n.create_id,
            n.created_at,
            n.updated_id,
            n.updated_at
                FROM   od_item n
            WHERE  n.id = " . $id . "
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
        $remart_t = $row['remark_t'];
        $remart_d = $row['remark_d'];
        $check_t = $row['check_t'];
        $check_d = $row['check_d'];
        $create_id = $row['create_id'];


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
 
            "created_at" => $created_at,

        );
    }

    return $merged_results;
}

function add_process($od_id, $comment, $action, $items, $user_id, $db)
{
    
    $query = "INSERT INTO od_process
    SET
        `od_id` = :od_id,
        `comment` = :comment,
        `action` = :action,
        `items` = :items,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':od_id', $od_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':items', $items);
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