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
        $apartment_id = $decoded->data->apartment_id;
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

$query = "SELECT pm.id, pm.project_name, COALESCE(pp.status, 0) status, COALESCE(f.filename, '') filename, pp.remark, COALESCE(f.gcp_name, '') gcp_name, user.username, user.id uid, DATE_FORMAT(pp.created_at, '%Y-%m-%d %H:%i:%s') created_at FROM project_proof pp LEFT JOIN project_main pm ON pp.project_id = pm.id LEFT JOIN user ON pp.create_id = user.id LEFT JOIN gcp_storage_file f ON f.batch_id = pp.id AND f.batch_type = 'proof' where 1= 1 ";

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by pm.id, status ";

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 5;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();

$is_checked = 0;
$sid = 0;
$id = 0;
$project_name = "";
$filename = "";
$gcp_name = "";
$remark = "";
$status = 0;
$username = "";
$created_at = "";
$items = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if(($id . $status != $row['id'] . $row['status']) && $id != 0)
    {
        $sid = $sid + 1;

        $merged_results[] = array( 
                                "is_checked" => 0,
                                "id" => $id,
                                "sid" => $sid,
                                "project_name" => $project_name,
                                "status" => $status,
                                "remark" => $remark,
                                "items" => $items,
                                "username" => $username,
                                "created_at" => $created_at
        );

        $items = [];

    }

    $id = $row['id'];
    $created_at = $row['created_at'];
    $username = $row['username'];
    $gcp_name = $row['gcp_name'];
    $filename = $row['filename'];
    $remark = $row['remark'];
    $project_name = $row['project_name'];
    $status = $row['status'];

    if($filename != "")
      $items[] = array('filename' => $filename,
                     'gcp_name' => $gcp_name );
}

if($id != 0)
{
    $sid = $sid + 1;

    $merged_results[] = array( "is_checked" => 0,
                                "id" => $id,
                                "sid" => $sid,
                                "project_name" => $project_name,
                                "status" => $status,
                                "remark" => $remark,
                                "items" => $items,
                                "username" => $username,
                                "created_at" => $created_at
            );
}


while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $merged_results[] = $row;
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


