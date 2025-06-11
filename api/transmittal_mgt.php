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

$fto = (isset($_GET['fto']) ?  $_GET['fto'] : '');
$fto = urldecode($fto);

$fdf = (isset($_GET['fdf']) ?  $_GET['fdf'] : '');
$fdt = (isset($_GET['fdt']) ?  $_GET['fdt'] : '');

$fpc = (isset($_GET['fpc']) ?  $_GET['fpc'] : '');
$fpc = urldecode($fpc);

$key = (isset($_GET['key']) ?  $_GET['key'] : '');

$key = urldecode($key);

$app = (isset($_GET['app']) ? $_GET['app'] : '');

$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : "");
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : "");

$kind = (isset($_GET['kind']) ?  urldecode($_GET['kind']) : "");

$type = (isset($_GET['type']) ?  urldecode($_GET['type']) : '');

$followup = (isset($_GET['up']) ?  urldecode($_GET['up']) : '');

$all = (isset($_GET['all']) ?  urldecode($_GET['all']) : '');

$merged_results = array();

$query = "SELECT pm.id, 
                pm.q_id,
                pm.title,
                pm.serial_name,
                pm.status, 
                pm.project_id,
                pm.kind,
                pm.followup,
                COALESCE((SELECT project_name FROM project_main WHERE id = pm.project_id and pm.kind = ''), '') AS project_name,
                COALESCE((SELECT title FROM project_other_task_a WHERE id = pm.project_id and pm.kind = 'a'), '') AS project_name_a,
                COALESCE((SELECT title FROM project_other_task_d WHERE id = pm.project_id and pm.kind = 'd'), '') AS project_name_d,
                COALESCE((SELECT title FROM project_other_task_l WHERE id = pm.project_id and pm.kind = 'l'), '') AS project_name_l,
                COALESCE((SELECT title FROM project_other_task_o WHERE id = pm.project_id and pm.kind = 'o'), '') AS project_name_o,
                COALESCE((SELECT title FROM project_other_task_sl WHERE id = pm.project_id and pm.kind = 'sl'), '') AS project_name_sl,
                COALESCE((SELECT title FROM project_other_task_sv WHERE id = pm.project_id and pm.kind = 'sv'), '') AS project_name_sv,
                pm.quotation_no,
                c_user.username AS created_by, 
                u_user.username AS updated_by,
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at,
                COALESCE((SELECT quotation_no FROM quotation WHERE id = pm.q_id), '') AS q_no,
                COALESCE((SELECT title FROM quotation WHERE id = pm.q_id), '') AS q_title,
                COALESCE((SELECT pageless FROM quotation WHERE id = pm.q_id), '') AS pageless,
                (SELECT COUNT(*) FROM gcp_storage_file f where f.batch_id = pm.id AND f.batch_type = 'transmittal'and f.`status` <> -1) AS q_count
          FROM transmittal pm 
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                left join project_main p on pm.project_id = p.id
                where pm.status <> -1  ";

// if($all != "all")
// {
//     $query = $query . "  ";
// }

$query_cnt = "SELECT count(*) cnt 
                FROM transmittal pm 
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                left join project_main p on pm.project_id = p.id
                where pm.status <> -1  ";

if($fpt != "")
{
    $query = $query . " and c_user.username = '" . $fpt . "' ";
    $query_cnt = $query_cnt . " and c_user.username = '" . $fpt . "' ";
}

if($fto != "")
{
    $query = $query . " and pm.transmittal_to like '%" . $fto . "%' ";
    $query_cnt = $query_cnt . " and pm.transmittal_to like '%" . $fto . "%' ";
}

if($fdf != "")
{
    $query = $query . " and STR_TO_DATE(pm.transmittal_date, '%Y-%m-%d') >= '" . $fdf . "' ";
    $query_cnt = $query_cnt . " and STR_TO_DATE(pm.transmittal_date, '%Y-%m-%d') >= '" . $fdf . "' ";
}

if($fdt != "")
{
    $query = $query . " and STR_TO_DATE(pm.transmittal_date, '%Y-%m-%d') <= '" . $fdt . "' ";
    $query_cnt = $query_cnt . " and STR_TO_DATE(pm.transmittal_date, '%Y-%m-%d') <= '" . $fdt . "' ";
}

if($fpc != "")
{
    $query = $query . " and p.create_id = " . $fpc . " ";
    $query_cnt = $query_cnt . " and p.create_id = " . $fpc . " ";
}

if($kind != "")
{
    $query = $query . " and pm.kind = '" . $kind . "' ";
    $query_cnt = $query_cnt . " and pm.kind = '" . $kind . "' ";
}

if($followup != "")
{
    if($followup == "N")
        $followup = "";
    
    $query = $query . " and pm.followup = '" . $followup . "' ";
    $query_cnt = $query_cnt . " and pm.followup = '" . $followup . "' ";
}

if($fc != "")
{
    $query = $query . " and p.catagory_id = " . $fc . " ";
    $query_cnt = $query_cnt . " and p.catagory_id = " . $fc . " ";
}

if($key != "")
{
    $query = $query . " and (COALESCE((SELECT project_name FROM project_main WHERE id = pm.project_id and pm.kind = ''), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_a WHERE id = pm.project_id and pm.kind = 'a'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_d WHERE id = pm.project_id and pm.kind = 'd'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_l WHERE id = pm.project_id and pm.kind = 'l'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_o WHERE id = pm.project_id and pm.kind = 'o'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sl WHERE id = pm.project_id and pm.kind = 'sl'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sv WHERE id = pm.project_id and pm.kind = 'sv'), '') like '%" . $key . "%' or pm.serial_name like '%" . $key . "%'  or pm.title like '%" . $key . "%' ) ";
    $query_cnt = $query_cnt . " and (COALESCE((SELECT project_name FROM project_main WHERE id = pm.project_id and pm.kind = ''), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_a WHERE id = pm.project_id and pm.kind = 'a'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_d WHERE id = pm.project_id and pm.kind = 'd'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_l WHERE id = pm.project_id and pm.kind = 'l'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_o WHERE id = pm.project_id and pm.kind = 'o'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sl WHERE id = pm.project_id and pm.kind = 'sl'), '') like '%" . $key . "%' or COALESCE((SELECT title FROM project_other_task_sv WHERE id = pm.project_id and pm.kind = 'sv'), '') like '%" . $key . "%' or pm.serial_name like '%" . $key . "%'  or pm.title like '%" . $key . "%' ) ";
}

if($app == "Y")
{
    $query = $query . " and (SELECT COUNT(*) FROM gcp_storage_file f where f.batch_id = pm.id AND f.batch_type = 'transmittal' and status <> -1) > 0 ";
    $query_cnt = $query_cnt . " and (SELECT COUNT(*) FROM gcp_storage_file f where f.batch_id = pm.id AND f.batch_type = 'transmittal' and status <> -1) > 0";
}

if($app == "N")
{
    $query = $query . " and (SELECT COUNT(*) FROM gcp_storage_file f where f.batch_id = pm.id AND f.batch_type = 'transmittal' and status <> -1) = 0 ";
    $query_cnt = $query_cnt . " and (SELECT COUNT(*) FROM gcp_storage_file f where f.batch_id = pm.id AND f.batch_type = 'transmittal' and status <> -1) = 0";
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
                $sOrder = "serial_name desc";
            else
                $sOrder = "serial_name ";
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
                $sOrder .= ", serial_name desc";
            else
                $sOrder .= ", serial_name ";
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
                $sOrder = "serial_name desc";
            else
                $sOrder = "serial_name ";
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
    $q_id = $row['q_id'];
    $title = $row['title'];
    $serial_name = $row['serial_name'];
    $kind = $row['kind'];
    $status = $row['status'];
    $project_id = $row['project_id'];
    $project_name = $row['project_name'];
    $project_name_a = $row['project_name_a'];
    $project_name_d = $row['project_name_d'];
    $project_name_l = $row['project_name_l'];
    $project_name_o = $row['project_name_o'];
    $project_name_sl = $row['project_name_sl'];
    $project_name_sv = $row['project_name_sv'];
    $quotation_no = $row['quotation_no'];
    $created_by = $row['created_by'];
    $updated_by = $row['updated_by'];
    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];
    $q_no = $row['q_no'];
    $q_title = $row['q_title'];
    $pageless = $row['pageless'];

    $followup = $row['followup'];

    $post = GetRecentPost($row['id'], $db);
    $files = GetRecentFiles($row['id'], $db);

    $merged_results[] = array(
        "is_edited" => 1,
        "id" => $id,
        "q_id" => $q_id,
        "title" => $title,
        "serial_name" => $serial_name,
        "kind" => $kind,
        "followup" => $followup, 
        "status" => $status,
        "project_id" => $project_id,
        "project_name" => $project_name,
        "project_name_a" => $project_name_a,
        "project_name_d" => $project_name_d,
        "project_name_l" => $project_name_l,
        "project_name_o" => $project_name_o,
        "project_name_sl" => $project_name_sl,
        "project_name_sv" => $project_name_sv,
        "quotation_no" => $quotation_no,
        "created_by" => $created_by,
        "updated_by" => $updated_by,
        "created_at" => $created_at,
        "updated_at" => $updated_at,
        "post" => $post,
        "files" => $files,
        "q_no" => $q_no,
        "q_title" => $q_title,
        "pageless" => $pageless,
        "cnt" => $cnt,
    );
}

// $filter_result = [];

// if($app != "")
// {
//     if($app == "Y")
//     {
//         foreach ($merged_results as &$value) {
//             if(count($value['files']) > 0)
//             {
//                 $filter_result[] = $value;
//             }
//         }
//     }
    
//     if($app == "N")
//     {
//         foreach ($merged_results as &$value) {
//             if(count($value['files']) == 0)
//             {
//                 $filter_result[] = $value;
//             }
//         }
//     }
// }
// else
//     $filter_result = $merged_results;

// $key_results = array();

// if($key != "")
// {
//     foreach ($filter_result as &$value) {
//         if(
//             preg_match("/{$key}/i", $value['project_name']) ||
//             preg_match("/{$key}/i", $value['project_name_a']) ||
//             preg_match("/{$key}/i", $value['project_name_d']) ||
//             preg_match("/{$key}/i", $value['project_name_l']) ||
//             preg_match("/{$key}/i", $value['project_name_o']) ||
//             preg_match("/{$key}/i", $value['project_name_sl']) ||
//             preg_match("/{$key}/i", $value['project_name_sv']) ||
//             preg_match("/{$key}/i", $value['title']) ||
//             preg_match("/{$key}/i", $value['q_title']) ||
//             preg_match("/{$key}/i", $value['quotation_no']))
//         {
//             $key_results[] = $value;
//         }
//     }
// }
// else
//     $key_results = $filter_result;

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

function GetRecentFiles($pid, $db){
    // $sql = "SELECT f.id, COALESCE(f.filename, '') filename, COALESCE(f.bucketname, '') bucket, COALESCE(f.gcp_name, '') gcp_name, u.username, pm.created_at FROM transmittal pm LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id left join user u on u.id = f.create_id  AND f.batch_type = 'transmittal' where pm.id = " . $pid . " and pm.status <> -1 and f.status <> -1";
    $sql = "SELECT f.id, COALESCE(f.filename, '') filename, COALESCE(f.bucketname, '') bucket, COALESCE(f.gcp_name, '') gcp_name, (select username from user where id = f.create_id) username, f.created_at FROM transmittal pm  LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'transmittal' where pm.id = " . $pid . " and pm.status <> -1 and f.status <> -1";
    $stmt = $db->prepare( $sql );

    $stmt->execute();

    $result = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "id" => $row['id'],
            "filename" => $row['filename'],
            "bucket" => $row['bucket'],
            "gcp_name" => $row['gcp_name'],
            "username" => $row['username'],
            "created_at" => $row['created_at'],
        );
    }

    return $result;
}


function GetRecentPost($quotation_id, $db){
    $query = "SELECT username, p.updated_at FROM transmittal p LEFT JOIN user u ON p.updated_id = u.id  WHERE p.id = " . $quotation_id . " and p.status <> -1  
    UNION all
              SELECT username, p.created_at updated_at  FROM transmittal_page p LEFT JOIN user u ON p.create_id = u.id  WHERE p.quotation_id = " . $quotation_id . " and p.status <> -1  
    UNION all
              SELECT username, p.created_at updated_at FROM transmittal_page_type p LEFT JOIN user u ON p.create_id = u.id  WHERE p.quotation_id = " . $quotation_id . " and p.status <> -1  
    UNION all
              SELECT username, p.created_at updated_at FROM transmittal_page_type_block p LEFT JOIN user u ON p.create_id = u.id  WHERE p.quotation_id = " . $quotation_id . " and p.status <> -1  
    UNION all
              SELECT username, p.created_at updated_at FROM transmittal_term p LEFT JOIN user u ON p.create_id = u.id  WHERE p.quotation_id = " . $quotation_id . " and p.status <> -1  
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    $filter_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    // if($key != "")
    // {
    //     foreach ($merged_results as &$value) {
    //         if(
    //             preg_match("/{$key}/i", $value['username']) || 
    //             ($key == substr($value['created_at'], 0, 10)))
    //         {
    //             $filter_result[] = $value;
    //         }
    //     }
    // }
    // else
        $filter_result = $merged_results;

    $sorted_result = [];

    if(count($filter_result) > 0)
    {
        usort($filter_result, function ($item1, $item2) {
            return $item2['updated_at'] <=> $item1['updated_at'];
        });
    
        foreach ($filter_result as $arr)
        {
            $sorted_result[] = array(
                "updated_at" => $arr['updated_at'],
                "username" => $arr['username'],
            
            );
         
            break;
        }
    }

    return $sorted_result;
}

?>