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

$database = new Database_Sea();
$db = $database->getConnection();

$car = isset($_GET["car"]) ? $_GET["car"] : "";

switch ($method) {
    case "GET":
        if ($car == "") {
            $access[$car] = false;
            $jsonEncodedReturnArray = json_encode($access, JSON_PRETTY_PRINT);
            echo $jsonEncodedReturnArray;
            die();
        }
        $ret = GetAccess($car, $db);
        $access[$car] = $ret;

        $jsonEncodedReturnArray = json_encode($access, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
        break;
    
}


function GetAccess($car, $db){
    $access = false;

    $query = "SELECT " . $car . " FROM access_control ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access = $row[$car] == -1 ? true : false;
        }
    return $access;
}


