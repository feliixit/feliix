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

$items = (isset($_POST['items']) ?  $_POST['items'] : '[]');
$items_array = json_decode($items,true);

$msg = "";

try{

    for($i=0; $i<count($items_array); $i++) 
    {
        $moq = 0;
        $pid = $items_array[$i]['pid'] ? $items_array[$i]['pid'] : 0;

        $qty = $items_array[$i]['qty'] ? $items_array[$i]['qty'] : 0;
        $back_qty = $items_array[$i]['backup_qty'] ? $items_array[$i]['backup_qty'] : 0;

        if($pid != 0)
        {
            $moq = 0;
            $query = "select moq from product_category where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $pid);
            $stmt->execute();

            $moq = $stmt->fetchColumn();

            if($moq == "" || $moq == null)
            {
                $moq = 0;
            }

            if($qty + $back_qty < $moq)
            {
                $msg = $msg . "MOQ for ITEM #" . $items_array[$i]['serial_number'] . ", ID: " . $pid . " is " . $moq . ". <br>";
            }

        }
    
    }

    if($msg != "")
    {
        $query = "insert into order_taiwan_moq_check (items, msg, create_id, created_at) values (:items, :msg, :user_id, NOW()) ";

        $err = array('ret' => $msg);
        $err_msg = json_encode($err, JSON_PRETTY_PRINT);

        $stmt = $db->prepare($query);
        $stmt->bindParam(':items', $items);
        $stmt->bindParam(':msg', $err_msg);
        $stmt->bindParam(':user_id', $user_id);

        $stmt->execute();
    }


    $returnArray = array('ret' => $msg);
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}
