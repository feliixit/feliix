<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : false);
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : false);
$srp = (isset($_POST['srp']) ?  $_POST['srp'] : false);
$qp = (isset($_POST['qp']) ?  $_POST['qp'] : false);

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$user_id = 0;

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

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $option = [
        'pid' => $pid,
        'brand' => $brand,
        'srp' => $srp,
        'qp' => $qp
    ];

    // save print_option into product_category
    $query = "UPDATE product_category SET print_option = :print_option WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':print_option', json_encode($option));
    $stmt->execute();

}


?>
