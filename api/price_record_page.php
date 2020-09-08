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

$category = (isset($_GET['category']) ?  $_GET['category'] : '');
$sub_category = (isset($_GET['sub_category']) ?  $_GET['sub_category'] : '');
$start_date = (isset($_GET['start_date']) ?  $_GET['start_date'] : '');
$end_date = (isset($_GET['end_date']) ?  $_GET['end_date'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
//$size = (isset($_GET['size']) ?  $_GET['size'] : "");


$merged_results = array();



$query = "SELECT * from price_record where is_enabled = true and account =1";
            if(!empty($start_date)) {
                $query = $query . " and created_at >= '$start_date' ";
            }

            if(!empty($end_date)) {
                $query = $query . " and created_at <= '$end_date' ";
            }
            
            if(!empty($category)) {
                $query = $query . " and category = '$category' ";
            }
            
            if(!empty($sub_category)) {
                $query = $query . " and sub_category = '$sub_category' ";
            }
            
            $query = $query . " order by created_at asc";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

//$query = $query . " order by created_at desc ";

//if(!empty($_GET['size'])) {
//    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
//    if(false === $size) {
//        $size = 10;
//    }
//
//    $offset = ($page - 1) * $size;
//
//    $query = $query . " LIMIT " . $offset . "," . $size;
//}


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

$query = "SELECT * from price_record where is_enabled = true and account =2";
            if(!empty($start_date)) {
                $query = $query . " and created_at >= '$start_date' ";
            }

            if(!empty($end_date)) {
                $query = $query . " and created_at <= '$end_date' ";
            }
            
            if(!empty($category)) {
                $query = $query . " and category = '$category' ";
            }
            
            if(!empty($sub_category)) {
                $query = $query . " and sub_category = '$sub_category' ";
            }
            $query = $query . " order by created_at asc";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

//$query = $query . " order by created_at desc ";

//if(!empty($_GET['size'])) {
//    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
//    if(false === $size) {
//        $size = 10;
//    }
//
//    $offset = ($page - 1) * $size;
//
//    $query = $query . " LIMIT " . $offset . "," . $size;
//}


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    array_push($merged_results,$row);
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
