<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;

        $user_name = $decoded->data->username;
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

$conf = new Conf();

$item_id = (isset($_POST['item_id']) ?  $_POST['item_id'] : 0);

$notes = GetShipping($item_id, $db);
if ($notes == "") {
    $notes = "[]";
} 
$jsonEncodedReturnArray = json_encode($notes, JSON_PRETTY_PRINT);
echo $jsonEncodedReturnArray;


function GetShipping($id, $db){

    $query = "
            SELECT n.received_list
                FROM   od_item n
            WHERE  n.id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $received_list = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $received_list = $row['received_list'];
    }

    if($received_list == ""){
        return "[]";
    }
    $obj = json_decode($received_list, true);

    $items = array();
    // get only status == 1
    foreach ($obj['items'] as $key => $value) {
        if ($value['status'] == 1) {
            $items[] = $value;
        }
    }

    $obj['items'] = $items;

    return json_encode($obj, JSON_PRETTY_PRINT);
}
