<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
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
    $GLOBALS["user_id"] = $decoded->data->id;

    $merged_results = array();
    

    $query = "SELECT id,
                    `gtag`, 
                    tag, 
                    sn
                    FROM tags
                    WHERE status <> -1 order by sn
                    ";
                    


    $stmt = $db->prepare($query);
    $stmt->execute();

    $gtag = "";
    $group_tags = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        //group tags by gtag put tags in array
        if($gtag != $row['gtag']){
            if($gtag != ""){
                $merged_results[] = array(
                    "gtag" => $gtag,
                    "tags" => $group_tags,
                );
            }
            $gtag = $row['gtag'];
            $group_tags = array();
        }
        

        $group_tags[] = array(
            "id" => $row['id'],
            "tag" => $row['tag'],
            "sn" => $row['sn'],
        );

    }

    //add last group
    $merged_results[] = array(
        "gtag" => $gtag,
        "tags" => $group_tags,
    );


    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

?>
