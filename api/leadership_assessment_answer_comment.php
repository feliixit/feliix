<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : '');
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

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = $decoded->data->id;

    $merged_results = array();

    $query = "SELECT answer
                FROM leadership_assessment_review pr
              WHERE pr.status <> -1 and pr.pid = " . $id;

    $stmt = $db->prepare($query);
    $stmt->execute();

    $comment1 = array();
    $comment2 = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $anwer = json_decode($row['answer'], true);

        if($anwer['comment1'] != '')
            $comment1[] = $anwer['comment1'];
        if($anwer['comment2'] != '')
            $comment2[] = $anwer['comment2'];
    }

    $merged_results['comment1'] = $comment1;
    $merged_results['comment2'] = $comment2;

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}
