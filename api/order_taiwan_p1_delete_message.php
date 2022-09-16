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

$message_id = (isset($_POST['message_id']) ?  $_POST['message_id'] : 0);

$item = (isset($_POST['item']) ?  $_POST['item'] : []);
$items = json_decode($item, true);

$od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
$od_name = (isset($_POST['od_name']) ? $_POST['od_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$page = (isset($_POST['page']) ? $_POST['page'] : 1);

try{

    if($message_id != 0)
    {
        $query = "update od_message
        SET
            status = -1,
            updated_id = :updated_id,
            updated_at = now()
        where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':updated_id', $uid);
        $stmt->bindParam(':id', $message_id);
    }
   

    $jsonEncodedReturnArray = "";
    if ($stmt->execute()) {
        $returnArray = array('ret' => $message_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

    }
    else
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }

    if($page == 1)
        order_notification03($user_name, 'access1,access2,access7', '', $project_name, $serial_name, $od_name, 'Order - Taiwan', $message, 'new_message_18', $items, $od_id);

    if($page == 2)
        order_notification03($user_name, 'access1,access2,access7', '', $project_name, $serial_name, $od_name, 'Order - Taiwan', $message, 'new_message_20', $items, $od_id);
    
    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}
