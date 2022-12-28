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

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);
$iq_name = (isset($_POST['iq_name']) ?  $_POST['iq_name'] : '');

try{
    $query = "update iq_main
    SET
    `status` = :status,
    `iq_name` = :iq_name,
    `updated_id` = :updated_id,
    `updated_at` = now()

    where id = :id  ";
    
    // prepare the query
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':iq_name', $iq_name);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':updated_id', $uid);
    
    if ($stmt->execute()) {
        $returnArray = array('ret' => $id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
        
    }
    else
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }
    
    
} catch (Exception $e) {
    error_log($e->getMessage());
    $db->rollback();
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
    die();
}