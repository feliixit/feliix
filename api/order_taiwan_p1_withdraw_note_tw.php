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

$action = 'withdraw_note_tw';

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


try{

    for($i=0; $i<count($items_array); $i++) 
    {
        $item_id = $items_array[$i]['id'];

        if($item_id != 0)
        {
            $query = "update od_item
            SET
                `status` = 0,
                `status_at` = now()
            where id = :id ";

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

    order_notification($user_name, 'access2', 'access1', $project_name, $serial_name, $od_name, 'Order - Close Deal', $comment, $action, $items_array, $od_id);

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}

