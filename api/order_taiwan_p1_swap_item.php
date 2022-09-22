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

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$item_id = (isset($_POST['item_id']) ?  $_POST['item_id'] : 0);
$item_sn = (isset($_POST['item_sn']) ?  $_POST['item_sn'] : 0);
$sitem_id = (isset($_POST['sitem_id']) ?  $_POST['sitem_id'] : 0);
$sitem_sn = (isset($_POST['sitem_sn']) ?  $_POST['sitem_sn'] : 0);

try{

    if($item_id != 0)
    {
        $query = "update od_item
        SET
            sn = :sn
        where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':sn', $item_sn);
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


    if($sitem_id != 0)
    {
        $query = "update od_item
        SET
            sn = :sn
        where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':sn', $sitem_sn);
        $stmt->bindParam(':id', $sitem_id);
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

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}
