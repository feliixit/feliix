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
        $GLOBALS["user_name"] = $decoded->data->username;
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

        $id = (isset($_GET['id']) ?  $_GET['id'] : null);
        $username = $decoded->data->username;

        $approver = false;
        $checker = false;

        $ret = "";

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT inventory_checker, inventory_approver FROM access_control";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(strpos($row['inventory_checker'], $username) !== false)
                $checker = true;
            if(strpos($row['inventory_approver'], $username) !== false)
                $approver = true;
        }

        $query = "SELECT `status` FROM office_item_inventory_check WHERE id = :id";
        $stmt = $db->prepare( $query );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $status = 0;

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = $row['status'];
        }

        if($status == 1 && !$checker)
            $ret = "Only Checker is allowed to visit this inventory check record.";

        if($status == 2 && !$checker)
            $ret = "Only Checker is allowed to visit this inventory check record.";

        if($status == 3 && !$approver)
            $ret = "Only Approver is allowed to visit this inventory check record.";

        $jsonEncodedReturnArray = json_encode($ret, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;

    
}


