<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
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

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");

$pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

$merged_results = array();



$query = "SELECT id, username  FROM `user` where status = 1 and title_id in (21, 22, 56, 57, 66) ";



if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by username ";

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
