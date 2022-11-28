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


$merged_results = array();

$query = "SELECT pm.id, 
                pm.od_name,
                pm.status, 
                pm.task_id,
                pm.order_type,
                p.project_name,
                p.id as project_id,
                ps.id as stage_id,
                pm.serial_name,
                pm.task_type,
                c_user.username AS created_by, 
                u_user.username AS updated_by,
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
          FROM od_main pm 
                left join project_other_task pot on pm.task_id = pot.id
                left join project_stages ps on pot.stage_id = ps.id
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                left join project_main p on ps.project_id = p.id
                where pm.status <> -1 order by pm.serial_name ";


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $od_name = $row['od_name'];
    $task_id = $row['task_id'];
    $status = $row['status'];
    $order_type = $row['order_type'];
    $project_id = $row['project_id'];
    $stage_id = $row['stage_id'];
    $project_name = $row['project_name'];
    $serial_name = $row['serial_name'];
 
    $created_by = $row['created_by'];
    $updated_by = $row['updated_by'];
    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];

    $task_type = $row['task_type'];

    if($task_type != "")
    {
        $project_name = GetTaskDetail($task_id, $task_type, $db);
        $stage_id = $task_id;
    }
 
    $merged_results[] = array(
        "is_edited" => 1,
        "id" => $id,
        "od_name" => $od_name,
        "task_id" => $task_id,
        "status" => $status,
        "order_type" => $order_type,
        "project_id" => $project_id,
        "stage_id" => $stage_id,
        "project_name" => $project_name,
        "serial_name" => $serial_name,
      
        "created_by" => $created_by,
        "updated_by" => $updated_by,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
 
        "task_type" => $task_type,
    );
}

$filter_result = [];

    $filter_result = $merged_results;

echo json_encode($filter_result, JSON_UNESCAPED_SLASHES);


function GetTaskDetail($task_id, $task_type, $db)
{
    $title = "";
    $table = "project_other_task_l";
    if($task_type == 'LT')
        $table = "project_other_task_l";
    
    if($task_type == 'SLS')
        $table = "project_other_task_sl";

    if($task_type == 'OS')
        $table = "project_other_task_o";

    $query = "select title from " . $table . " where id = " . $task_id;
    $stmt = $db->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $title = $row['title'];
  
    return $title;
}




?>