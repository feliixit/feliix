<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$token = (isset($_GET['token']) ?  $_GET['token'] : null);

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);


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

if (!isset($token)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    // $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = 0;

    $step = (isset($_GET['step']) ?  $_GET['step'] : 0);

    $merged_results = array();

    // add if not exists

    $query = "SELECT *
                FROM leadership_assessment_questions pr
              WHERE pr.status <> -1  " . ($step != 0 ? " and pr.page=$step" : ' ') . " order by pr.sequence";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}
