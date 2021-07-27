<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
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

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    $merged_results = array();
    $return_result = array();

    $level  = (isset($_GET['level']) ?  $_GET['level'] : "1");
    $parent  = (isset($_GET['parent']) ?  $_GET['parent'] : "");

    $query = "SELECT cat_id, category 
                FROM product_category_attribute
            where status <> -1 
                and level = " . $level;

    if($parent != "" && $parent != "0")
        $query = $query . " and cat_id like '" . rtrim($parent, "0") . "%' ";

    if($parent == "0")
        $query = $query . " and cat_id = '0' ";

    $query = $query . " order by cat_id ";

    $stmt = $db->prepare($query);
    $stmt->execute();


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $return_result = $merged_results;

    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}
