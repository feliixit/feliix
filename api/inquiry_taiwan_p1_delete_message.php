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

$iq_id = (isset($_POST['iq_id']) ?  $_POST['iq_id'] : 0);
$iq_name = (isset($_POST['iq_name']) ? $_POST['iq_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$page = (isset($_POST['page']) ? $_POST['page'] : 1);

try{

    if($message_id != 0)
    {
        $query = "update iq_message
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

    // find message in array of $items[0]['notes'] by message_id
    $message = "";
    $index = array_search($message_id, array_column($items['notes'], 'id'));
    if($index !== false)
    {
        $message = $items['notes'][$index]['message'];
    }


    if($page == 1)
        order_notification03Access7($user_name, 'access1,access2', '', $project_name, $serial_name, $iq_name, 'Order - Close Deal', $message, 'new_message_18', $items, $iq_id, GetAccess7($iq_id, $db));
        //order_notification03($user_name, 'access1,access2,access7', '', $project_name, $serial_name, $iq_name, 'Order - Close Deal', $message, 'new_message_18', $items, $iq_id);

    if($page == 2)
        order_notification03($user_name, 'access1,access2,access3', '', $project_name, $serial_name, $iq_name, 'Order - Close Deal', $message, 'new_message_20', $items, $iq_id);
    
    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}

function GetAccess7($iq_id, $db)
{
    $access7 = "";
    $query = "select a.access7 from iq_main a where a.id = :iq_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':iq_id', $iq_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $access7 = $row['access7'];

    $access7 = ltrim($access7, ',');

    $access7_array = explode(',', $access7);
    $access7 = implode("','", $access7_array);

    return $access7;
}