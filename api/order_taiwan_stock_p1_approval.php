<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once 'mail.php';
use \Firebase\JWT\JWT;
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
        $user_name = $decoded->data->username;
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
$conf = new Conf();

$uid = $user_id;

$od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
$items = (isset($_POST['items']) ?  $_POST['items'] : '[]');

$comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
$od_name = (isset($_POST['od_name']) ? $_POST['od_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$action = 'approval';

$items_array = json_decode($items,true);

// update main table
$query = "UPDATE od_main SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':updated_id', $uid);
$stmt->bindParam(':id', $od_id);
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


// seperate the items from 'C' and 'J'
$c_items = array();
$w_items = array();

for($i=0; $i<count($items_array); $i++) 
{
    $item_id = $items_array[$i]['id'];
    $item_status = $items_array[$i]['confirm'];

    if($item_status == 'C')
    {
        array_push($c_items, $items_array[$i]);
    }
    else if($item_status == 'J')
    {
        array_push($w_items, $items_array[$i]);
    }
}

try{

    for($i=0; $i<count($c_items); $i++) 
    {
        $item_id = $c_items[$i]['id'];

        if($item_id != 0)
        {
            $query = "update od_item
            SET
            `status_at` = now(),
                `status` = 2
            where id = :id and confirm = 'C' ";

            // prepare the query
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $item_id);
        }
    
        $jsonEncodedReturnArray = "";
        if ($stmt->execute()) {
            $returnArray = array('ret' => $item_id);
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }

    }

    
    for($i=0; $i<count($w_items); $i++) 
    {
        $item_id = $w_items[$i]['id'];

        if($item_id != 0)
        {
            $query = "update od_item
            SET
                `status` = 3,
                `status_at` = now()
            where id = :id and confirm = 'J' ";

            // prepare the query
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $item_id);
        }
    
        $jsonEncodedReturnArray = "";
        if ($stmt->execute()) {
            $returnArray = array('ret' => $item_id);
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        }
        else
        {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
        }

    }


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

    $access7 = GetAccess7($db, $od_id);
    $access7 = ltrim($access7, ',');

    if(count($c_items) > 0)
        order_type_notification($user_name, 'access3', 'access1, access2', $project_name, $serial_name, $od_name, 'Order - Stocks', $comment, $action, $c_items, $od_id, "stock");

    if(count($w_items) > 0)
        order_type_notification_warehouse($user_name, 'access1, access3, access4, access5', 'access2', $project_name, $serial_name, $od_name, 'Order - Stocks', $comment, $action, $w_items, $od_id, "stock", $access7);

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}

function GetAccess7($db, $uid)
{
    $query = "SELECT access7 FROM od_main WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $uid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['access7'];
}
