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

$uid = (isset($_GET['uid']) ?  $_GET['uid'] : '');
$fpc = (isset($_GET['fpc']) ?  $_GET['fpc'] : '');
$fct = (isset($_GET['fct']) ?  $_GET['fct'] : '');
$fp = (isset($_GET['fp']) ?  $_GET['fp'] : '');
$fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
$fcs = (isset($_GET['fcs']) ?  $_GET['fcs'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");


$type = (isset($_GET['type']) ?  $_GET['type'] : '');

$merged_results = array();



$query = "SELECT pm.id, pc.category, pct.client_type, pct.class_name pct_class, pp.priority, pp.class_name pp_class, pm.project_name, ps.project_status, pm.estimate_close_prob, user.username, DATE_FORMAT(pm.created_at, '%Y-%m-%d') FROM project_main pm LEFT JOIN project_category pc ON pm.catagory_id = pc.id LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id LEFT JOIN project_priority pp ON pm.priority_id = pp.id LEFT JOIN project_status ps ON pm.project_status_id = ps.id LEFT JOIN user ON pm.create_id = user.id where 1= 1 ";

if($fpc != "")
{
    $query = $query . " and pm.catagory_id = " . $fpc . " ";
}

if($fct != "")
{
    $query = $query . " and pm.client_type_id = '" . $fct . "' ";
}

if($fp != "")
{
    $query = $query . " and pm.priority_id = '" . $fp . "' ";
}

if($fs != "")
{
    $query = $query . " and pm.project_status_id = '" . $fs . "' ";
}

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by pm.created_at desc ";

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
