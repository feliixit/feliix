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
$fpt = (isset($_GET['fpt']) ?  $_GET['fpt'] : '');

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");


$type = (isset($_GET['type']) ?  $_GET['type'] : '');

$merged_results = array();



$query = "SELECT pm.id, COALESCE(pc.category, '') category, pct.client_type, pct.class_name pct_class, pp.priority, pp.class_name pp_class, pm.project_name, COALESCE(ps.project_status, '') project_status, COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) estimate_close_prob, user.username, DATE_FORMAT(pm.created_at, '%Y-%m-%d') created_at, COALESCE((SELECT project_stage.stage FROM project_stages LEFT JOIN project_stage ON project_stage.id = project_stages.stage_id WHERE project_stages.project_id = pm.id and project_stages.stages_status_id = 1 ORDER BY `sequence` desc LIMIT 1), '') stage FROM project_main pm LEFT JOIN project_category pc ON pm.catagory_id = pc.id LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id LEFT JOIN project_priority pp ON pm.priority_id = pp.id LEFT JOIN project_status ps ON pm.project_status_id = ps.id LEFT JOIN project_stage pst ON pm.stage_id = pst.id LEFT JOIN user ON pm.create_id = user.id where 1= 1 ";

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

if($fpt != "")
{
    $query = $query . " and user.username = '" . $fpt . "' ";
}

$query = $query . " order by pm.created_at desc ";

if($fcs != "")
{
    $query = "SELECT * FROM ( SELECT pm.id, COALESCE(pc.category, '') category, pct.client_type, pct.class_name pct_class, pp.priority, pp.class_name pp_class, pm.project_name, COALESCE(ps.project_status, '') project_status, COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) estimate_close_prob, user.username, DATE_FORMAT(pm.created_at, '%Y-%m-%d') created_at, COALESCE((SELECT project_stage.stage FROM project_stages LEFT JOIN project_stage ON project_stage.id = project_stages.stage_id WHERE project_stages.project_id = pm.id and project_stages.stages_status_id = 1 ORDER BY `sequence` desc LIMIT 1), '') stage FROM project_main pm LEFT JOIN project_category pc ON pm.catagory_id = pc.id LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id LEFT JOIN project_priority pp ON pm.priority_id = pp.id LEFT JOIN project_status ps ON pm.project_status_id = ps.id LEFT JOIN project_stage pst ON pm.stage_id = pst.id LEFT JOIN user ON pm.create_id = user.id where 1= 1 ";

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

    if($fpt != "")
    {
        $query = $query . " and user.username = '" . $fpt . "' ";
    }

    if($fcs == 'Empty')
        $query = $query . " ) t  WHERE t.stage = '' ";
    else
    $query = $query . " ) t  WHERE t.stage = '" . $fcs . "' ";

    $query = $query . " order by t.created_at desc ";
}

if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}



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
    $id = $row['id'];
    $category = $row['category'];
    $client_type = $row['client_type'];
    $pct_class = $row['pct_class'];
    $priority = $row['priority'];

    $pp_class = $row['pp_class'];
    $project_name = $row['project_name'];
    $project_status = $row['project_status'];
    $estimate_close_prob = $row['estimate_close_prob'];
    $username = $row['username'];

    $created_at = $row['created_at'];
    $stage = $row['stage'];
    $recent = GetRecentPost($row['id'], $db);

    $merged_results[] = array(
        "id" => $id,
        "category" => $category,
        "client_type" => $client_type,
        "pct_class" => $pct_class,
        "priority" => $priority,
        "pp_class" => $pp_class,
        "project_name" => $project_name,
        "project_status" => $project_status,
        "estimate_close_prob" => $estimate_close_prob,
        "username" => $username,
        "created_at" => $created_at,
        "stage" => $stage,
        "recent" => $recent,
    );
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


function GetRecentPost($project_id, $db){
    $query = "SELECT u.username, pm.created_at FROM project_stage_client pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id WHERE p.project_id = " . $project_id . " and pm.status <> -1  
    UNION all
    SELECT  u.username, pc.created_at FROM project_stage_client_task pc left join user u on u.id = pc.create_id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pc.status <> -1 
    UNION ALL
    SELECT  u.username, pt.created_at FROM project_stage_client_task_comment pt left join user u on u.id = pt.create_id LEFT JOIN project_stage_client_task pc ON pt.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pc.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_message pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task pc ON pm.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    UNION all
    SELECT  u.username, pm.created_at FROM project_other_task_message_reply pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message ps ON pm.message_id = ps.id  LEFT JOIN project_other_task pc ON ps.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_r pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_message_r pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task_r pc ON pm.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    UNION all
    SELECT  u.username, pm.created_at FROM project_other_task_message_reply_r pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message_r ps ON pm.message_id = ps.id  LEFT JOIN project_other_task_r pc ON ps.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pm.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $str = "";

    if(count($merged_results) > 0)
    {
        usort($merged_results, function ($item1, $item2) {
            return $item2['created_at'] <=> $item1['created_at'];
        });
    
        foreach ($merged_results as $arr)
        {
            $str = $arr['created_at'] . " " . $arr['username'];
            break;
        }
    }

    return $str;
}