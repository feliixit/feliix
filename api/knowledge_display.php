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

$uid = (isset($_GET['uid']) ?  urldecode($_GET['uid']) : '');
$ft = (isset($_GET['ft']) ?  urldecode($_GET['ft']) : '');
$fc = (isset($_GET['fc']) ?  urldecode($_GET['fc']) : []);
$fta = (isset($_GET['fta']) ?  urldecode($_GET['fta']) : []);
$fty = (isset($_GET['fty']) ?  urldecode($_GET['fty']) : '');
$fw = (isset($_GET['fw']) ?  urldecode($_GET['fw']) : '');

$fcf = (isset($_GET['fcf']) ?  urldecode($_GET['fcf']) : '');
$fct = (isset($_GET['fct']) ?  urldecode($_GET['fct']) : '');

if($fcf != "") {
    $fcf = date("Y/m/d", strtotime($fcf));
}

if($fct != "") {
    $fct = date("Y/m/d", strtotime($fct));
}

$fdf = (isset($_GET['fdf']) ?  urldecode($_GET['fdf']) : '');
$fdt = (isset($_GET['fdt']) ?  urldecode($_GET['fdt']) : '');

$fpt = urldecode($fpt);


$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : 1);
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : 8);


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
                    WHERE pm.status <> -1 and (pm.access like '%".$user_name."%' or pm.access like '%".$user_department."%' or pm.access like '%All%')";
    // for record size
    $query_cnt = "SELECT count(*) cnt 
                    FROM knowledge pm
                        LEFT JOIN user c_user ON pm.create_id = c_user.id 
                        LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                        WHERE pm.status <> -1 and (pm.access like '%".$user_name."%' or pm.access like '%".$user_department."%' or pm.access like '%All%')";

if ($ft != '') {
    $query = $query . " and pm.`title` like '%" . $ft . "%' ";

    $query_cnt = $query_cnt . " and pm.`title` like '%" . $ft . "%' ";
}

if($fc != "")
{
    // split by comma
    $fc = explode(",", $fc);
    $query = $query . " and c_user.username in ('" . implode("','", $fc) . "') ";
    $query_cnt = $query_cnt . " and c_user.username in ('" . implode("','", $fc) . "') ";
}

if($fta != "")
{
    // split by comma
    $fta = explode(",", $fta);

    $or_status = "";

    for($i = 0; $i < count($fta); $i++)
    {
        $or_status .= " pm.category like '%" . $fta[$i] . "%' or";
    }

    if($or_status != "")
    {
        $or_status = substr($or_status, 0, -2);
        $query = $query . " and (" . $or_status . ") ";
        $query_cnt = $query_cnt . " and (" . $or_status . ") ";
    }
}

if ($fty != '') {
    $query = $query . " and pm.`type` = '" . $fty . "' ";
    $query_cnt = $query_cnt . " and pm.`type` = '" . $fty . "' ";
}

if($fw != "")
{
    $query = $query . " and pm.watch = '" . $fw . "' ";
    $query_cnt = $query_cnt . " and pm.watch = '" . $fw . "' ";
}


if($fcf != "")
{
    $query = $query . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fcf . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fcf . "' ";
}

if($fct != "")
{
    $query = $query . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fct . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fct . "' ";
}

if($fdf != "")
{
    $query = $query . " and (0 + CAST(pm.duration AS UNSIGNED)) >= '" . $fdf . "' ";
    $query_cnt = $query_cnt . " and (0 + CAST(pm.duration AS UNSIGNED)) >= '" . $fdf . "' ";
}

if($fdt != "")
{
    $query = $query . " and (0 + CAST(pm.duration AS UNSIGNED)) <= '" . $fdt . "' ";
    $query_cnt = $query_cnt . " (0 + CAST(pm.duration AS UNSIGNED)) <= '" . $fdt . "' ";
}


$sOrder = "";
if($op1 != "" && $op1 != "0")
{
    switch ($op1)
    {
        case 1:
            if($od1 == 2)
                $sOrder = "pm.title desc";
            else
                $sOrder = "pm.title ";
            break;  
        case 2:
            if($od1 == 2)
                $sOrder = "c_user.username desc";
            else
                $sOrder = "c_user.username ";
            break;  
        case 3:
            if($od1 == 2)
                $sOrder = "(0 + CAST(pm.duration AS UNSIGNED)) desc";
            else
                $sOrder = "(0 + CAST(pm.duration AS UNSIGNED)) ";
            break;  
        case 4:
            if($od1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 5:
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
                $sOrder .= ", pm.title desc ";
            else
                $sOrder .= ", pm.title ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder .= ", c_user.username desc ";
            else
                $sOrder .= ", c_user.username ";
            break;  
        case 3:
            if($od2 == 2)
                $sOrder = ", (0 + CAST(pm.duration AS UNSIGNED)) desc ";
            else
                $sOrder = ", (0 + CAST(pm.duration AS UNSIGNED)) ";
            break;  
        case 4:
            if($od2 == 2)
                $sOrder = ", Coalesce(pm.created_at, '0000-00-00') desc ";
            else
                $sOrder = ", Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 5:
            if($od2 == 2)
                $sOrder = ", Coalesce(pm.updated_at, '0000-00-00') desc ";
            else
                $sOrder = ", Coalesce(pm.updated_at, '9999-99-99') ";
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
                $sOrder = "pm.title desc";
            else
                $sOrder = "pm.title ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder = "c_user.username desc";
            else
                $sOrder = "c_user.username ";
            break;  
        case 3:
            if($od2 == 2)
                $sOrder = "(0 + CAST(pm.duration AS UNSIGNED)) desc ";
            else
                $sOrder = "(0 + CAST(pm.duration AS UNSIGNED)) ";
            break;  
        case 4:
            if($od2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 5:
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
        $size = 8;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


    $stmt = $db->prepare($query);
    $stmt->execute();

    $cnt = 0;
    $stmt_cnt = $db->prepare( $query_cnt );
    $stmt_cnt->execute();
    while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
        $cnt = $row['cnt'];
    }

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
            
            if($duration_in_huours > 1){
                $duration_str = $duration_in_huours . '-hr ';
            }
            else
            {
                $duration_str = $duration_in_minutes . '-min';
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
            "cnt" => $cnt,
        );
    }



    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

?>
