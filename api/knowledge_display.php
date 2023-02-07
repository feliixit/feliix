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
    
    $user_name = $decoded->data->username;
    $user_department = $decoded->data->department;

    $merged_results = array();
    

    $query = "SELECT pm.id,
                    pm.cover, 
                    pm.title, 
                    pm.category, 
                    pm.access, 
                    pm.`type`, 
                    pm.link, 
                    pm.attach,
                    pm.duration, 
                    pm.watch,
                    pm.desciption,
                    pm.`status`,
                    c_user.username AS created_by, 
                    u_user.username AS updated_by,
                    DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                    DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
                    FROM knowledge pm
                    LEFT JOIN user c_user ON pm.create_id = c_user.id 
                    LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                    WHERE pm.status <> -1 and (pm.access like '%".$user_name."%' or pm.access like '%".$user_department."%')
                    order by pm.created_at desc
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
        $duration = $row['duration'];
        $desciption = $row['desciption'];
        $watch = $row['watch'];
        $status = $row['status'];
        
        $created_by = $row['created_by'];
        $updated_by = $row['updated_by'];
        $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];

        $duration_str = '';
        if($duration > 0){
            $duration_in_huours = round($duration/60, 1);
            $duration_in_minutes = floor($duration % 60);
            
            if($duration_in_huours > 0){
                $duration_str = $duration_in_huours . '-hr ';
            }

            //if($duration_in_minutes > 0){
            //    $duration_str .= $duration_in_minutes . '-min';
            //}
        }
        
        
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
            "status" => $status,

            "duration_str" => $duration_str,

            "created_by" => $created_by,
            "updated_by" => $updated_by,
            "created_at" => $created_at,
            "updated_at" => $updated_at,
         
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

?>
