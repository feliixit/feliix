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

$type = (isset($_GET['type']) ?  urldecode($_GET['type']) : '');


$merged_results = array();

$query = "SELECT pm.id, 
                q.title,
                q.kind,
                q.project_id,
                pm.status, 
                pm.quotation_id,
                COALESCE((SELECT project_name FROM project_main WHERE id = q.project_id and q.kind = ''), '') AS project_name,
                COALESCE((SELECT title FROM project_other_task_a WHERE id = q.project_id and q.kind = 'a'), '') AS project_name_a,
                COALESCE((SELECT title FROM project_other_task_d WHERE id = q.project_id and q.kind = 'd'), '') AS project_name_d,
                COALESCE((SELECT title FROM project_other_task_l WHERE id = q.project_id and q.kind = 'l'), '') AS project_name_l,
                COALESCE((SELECT title FROM project_other_task_o WHERE id = q.project_id and q.kind = 'o'), '') AS project_name_o,
                COALESCE((SELECT title FROM project_other_task_sl WHERE id = q.project_id and q.kind = 'sl'), '') AS project_name_sl,
                COALESCE((SELECT title FROM project_other_task_sv WHERE id = q.project_id and q.kind = 'sv'), '') AS project_name_sv,
                c_user.username AS created_by, 
                u_user.username AS updated_by,
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
          FROM work_schedule_eng pm 
                LEFT JOIN quotation_eng q on pm.quotation_id = q.id
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                left join project_main p on q.project_id = p.id and q.kind = ''
                where pm.status <> -1 ";

$query_cnt = "SELECT count(*) cnt 
FROM work_schedule_eng pm 
    LEFT JOIN quotation_eng q on pm.quotation_id = q.id
    LEFT JOIN user c_user ON pm.create_id = c_user.id 
    LEFT JOIN user u_user ON pm.updated_id = u_user.id 
    left join project_main p on q.project_id = p.id
where pm.status <> -1";

if($fpt != "")
{
    $query = $query . " and c_user.username = '" . $fpt . "' ";
    $query_cnt = $query_cnt . " and c_user.username = '" . $fpt . "' ";
}

if($fpc != "")
{
    $query = $query . " and p.create_id = " . $fpc . " ";
    $query_cnt = $query_cnt . " and p.create_id = " . $fpc . " ";
}

if($kind != "")
{
    $query = $query . " and q.kind = '" . $kind . "' ";
    $query_cnt = $query_cnt . " and q.kind = '" . $kind . "' ";
}


if($fc != "")
{
    $query = $query . " and p.catagory_id = " . $fc . " ";
    $query_cnt = $query_cnt . " and p.catagory_id = " . $fc . " ";
}

if($key != "")
{
    $query = $query . " and (COALESCE((SELECT project_name FROM project_main WHERE id = q.project_id and q.kind = ''), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_a WHERE id = q.project_id and q.kind = 'a'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_d WHERE id = q.project_id and q.kind = 'd'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_l WHERE id = q.project_id and q.kind = 'l'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_o WHERE id = q.project_id and q.kind = 'o'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sl WHERE id = q.project_id and q.kind = 'sl'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sv WHERE id = q.project_id and q.kind = 'sv'), '') like '%" . $key . "%' or q.quotation_no like '%" . $key . "%'  or q.title like '%" . $key . "%' or pm.id = '". $key . "' or q.id = '" .$key . "') ";
    $query_cnt = $query_cnt . " and (COALESCE((SELECT project_name FROM project_main WHERE id = q.project_id and q.kind = ''), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_a WHERE id = q.project_id and q.kind = 'a'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_d WHERE id = q.project_id and q.kind = 'd'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_l WHERE id = q.project_id and q.kind = 'l'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_o WHERE id = q.project_id and q.kind = 'o'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sl WHERE id = q.project_id and q.kind = 'sl'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sv WHERE id = q.project_id and q.kind = 'sv'), '') like '%" . $key . "%' or q.quotation_no like '%" . $key . "%'  or q.title like '%" . $key . "%' or pm.id = '". $key . "' or q.id = '" .$key . "') ";
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
        case 3:
            if($od1 == 2)
                $sOrder = "pm.id desc";
            else
                $sOrder = "pm.id ";
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
        case 3:
            if($od2 == 2)
                $sOrder .= ", pm.id desc";
            else
                $sOrder .= ", pm.id ";
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
        case 3:
            if($od2 == 2)
                $sOrder = "pm.id desc";
            else
                $sOrder = "pm.id ";
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

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();
while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $title = $row['title'];
    $kind = $row['kind'];
    $project_id = $row['project_id'];
    $status = $row['status'];
    $quotation_id = $row['quotation_id'];
    $project_name = $row['project_name'];
    $project_name_a = $row['project_name_a'];
    $project_name_d = $row['project_name_d'];
    $project_name_l = $row['project_name_l'];
    $project_name_o = $row['project_name_o'];
    $project_name_sl = $row['project_name_sl'];
    $project_name_sv = $row['project_name_sv'];
    $created_by = $row['created_by'];
    $updated_by = $row['updated_by'];
    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];

    $merged_results[] = array(
        "is_edited" => 1,
        "id" => $id,
        "title" => $title,
        "kind" => $kind,
        "project_id" => $project_id,
        "status" => $status,
        "quotation_id" => $quotation_id,
        "project_name" => $project_name,
        "project_name_a" => $project_name_a,
        "project_name_d" => $project_name_d,
        "project_name_l" => $project_name_l,
        "project_name_o" => $project_name_o,
        "project_name_sl" => $project_name_sl,
        "project_name_sv" => $project_name_sv,
        "created_by" => $created_by,
        "updated_by" => $updated_by,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
        "cnt" => $cnt,
     
    );
}

// $filter_result = [];

// if($key != "")
// {
//     foreach ($merged_results as &$value) {
//         if(
//             preg_match("/{$key}/i", $value['project_name']) ||
//             preg_match("/{$key}/i", $value['project_name_a']) ||
//             preg_match("/{$key}/i", $value['project_name_d']) ||
//             preg_match("/{$key}/i", $value['project_name_l']) ||
//             preg_match("/{$key}/i", $value['project_name_o']) ||
//             preg_match("/{$key}/i", $value['project_name_sl']) ||
//             preg_match("/{$key}/i", $value['project_name_sv']) ||
//             preg_match("/{$key}/i", $value['title']) ||
//             preg_match("/{$key}/i", $value['quotation_no']))
//         {
//             $filter_result[] = $value;
//         }
//     }
// }
// else
//     $filter_result = $merged_results;

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);




?>