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
                    cover, 
                    title, 
                    category, 
                    access, 
                    `type`, 
                    link, 
                    attach,
                    duration, 
                    watch,
                    desciption,
                    `status`,
                    create_id,
                    created_at,
                    updated_id,
                    updated_at,
                    deleted_id,
                    deleted_time
                    FROM knowledge
                    WHERE status <> -1 and id=$id 
                    ";
                    


    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $title = $row['title'];
        $cover = ($row['cover'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['cover'] : '';
        $category = explode(',', $row['category']);
        $access = explode(',', $row['access']);
        $type = $row['type'];
        $link = $row['link'];
        $attach = ($row['attach'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['attach'] : '';
        $filename = $row['attach'];
        $duration = $row['duration'];
        $desciption = $row['desciption'];
        $watch = $row['watch'];
        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
        $updated_id = $row['updated_id'];
        $updated_at = $row['updated_at'];
        $deleted_id = $row['deleted_id'];
        $deleted_time = $row['deleted_time'];

        
        
        $merged_results[] = array(
            "id" => $id,
            "cover" => $cover,
            "title" => $title,
            "category" => $category,
            "access" => $access,
            "type" => $type,
            "link" => $link,
            "attach" => $attach,
            "duration" => $duration,
            "watch" => $watch,
            "desciption" => $desciption,
            "filename" => $filename,
            "status" => $status,
            "create_id" => $create_id,
            "created_at" => $created_at,
            "updated_id" => $updated_id,
            "updated_at" => $updated_at,
            "deleted_id" => $deleted_id,
            "deleted_time" => $deleted_time,
         
        );
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

?>
