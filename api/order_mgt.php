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

$uid = (isset($_GET['uid']) ?  urldecode($_GET['uid']) : '');

$fpt = (isset($_GET['fpt']) ?  $_GET['fpt'] : '');

$fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');

$fpt = urldecode($fpt);

$fg = (isset($_GET['fg']) ?  $_GET['fg'] : '');

$fpc = (isset($_GET['fpc']) ?  $_GET['fpc'] : '');
$fpc = urldecode($fpc);

$key = (isset($_GET['key']) ?  $_GET['key'] : '');

$key = urldecode($key);

$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : "");
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : "");

$kind = (isset($_GET['kind']) ?  urldecode($_GET['kind']) : "");

$tp = (isset($_GET['tp']) ?  urldecode($_GET['tp']) : '');

if($tp == "l")
 $tp="LT";
if($tp == "o")
 $tp="OS";
if($tp == "s")
 $tp="SLS";

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);

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
                p.pic1,
                p.pic2,
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
                where pm.status <> -1 ";

if($id != 0){
    $query .= " and pm.id = $id ";
}


if($fpt != "")
{
    $query = $query . " and c_user.username = '" . $fpt . "' ";
}

if($fpc != "")
{
    $query = $query . " and p.create_id = " . $fpc . " ";
}

if($fg != "")
{
    $query = $query . " and p.group_id = " . $fg . " ";
}

if($kind != "")
{
    $query = $query . " and pm.order_type = '" . $kind . "' ";
}

if($tp != "")
{
    $query = $query . " and pm.task_type = '" . $tp . "' ";
}


if($fc != "")
{
    $query = $query . " and p.catagory_id = " . $fc . " ";
}

$sOrder = "";
if($op1 != "" && $op1 != "0")
{
    switch ($op1)
    {
        case 1:
            if($od1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od1 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
       
        
        default:
    }
}

if($op2 != "" && $op2 != "0" && $sOrder != "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
      
        
        default:
    }
}


if($op2 != "" && $op2 != "0" && $sOrder == "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
       
        
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by pm.created_at desc ";


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
    $pic1 = $row['pic1'];
    $pic2 = $row['pic2'];

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
        "pic1"=> $pic1,
        "pic2"=> $pic2
     
    );
}

$filter_result = [];

if($key != "")
{
    foreach ($merged_results as &$value) {
        if(
            preg_match("/{$key}/i", $value['project_name']) ||
   
            preg_match("/{$key}/i", $value['od_name']) ||
            preg_match("/{$key}/i", $value['serial_name']))
        {
            $filter_result[] = $value;
        }
    }
}
else
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

    if($task_type == 'SVC')
        $table = "project_other_task_sv";

    $query = "select title from " . $table . " where id = " . $task_id;
    $stmt = $db->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row)
        $title = $row['title'];
  
    return $title;
}



?>