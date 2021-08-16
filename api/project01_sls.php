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
$fpc = (isset($_GET['fpc']) ?  urldecode($_GET['fpc']) : '');
$fct = (isset($_GET['fct']) ?  urldecode($_GET['fct']) : '');
$fp = (isset($_GET['fp']) ?  urldecode($_GET['fp']) : '');
$fs = (isset($_GET['fs']) ?  urldecode($_GET['fs']) : '');
$fcs = (isset($_GET['fcs']) ?  urldecode($_GET['fcs']) : '');
$fpt = (isset($_GET['fpt']) ?  $_GET['fpt'] : '');

$fpt = urldecode($fpt);

$flo = (isset($_GET['flo']) ?  urldecode($_GET['flo']) : '');
$fup = (isset($_GET['fup']) ?  urldecode($_GET['fup']) : '');
$key = (isset($_GET['key']) ?  $_GET['key'] : '');

$key = urldecode($key);

$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : "");
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : "");


$type = (isset($_GET['type']) ?  urldecode($_GET['type']) : '');

$merged_results = array();

$query = "SELECT pm.id, 
                COALESCE(pc.category, '') category, 
                pct.client_type, 
                pct.class_name pct_class, 
                pp.priority, 
                pp.class_name pp_class, 
                pm.project_name, 
                COALESCE(ps.project_status, '') project_status, 
                COALESCE((SELECT project_est_prob.prob 
                            FROM project_est_prob 
                            WHERE project_est_prob.project_id = pm.id 
                            order by created_at desc limit 1), pm.estimate_close_prob) estimate_close_prob, 
                user.username, 
                DATE_FORMAT(pm.created_at, '%Y-%m-%d') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d') updated_at, 
                COALESCE((SELECT project_stage.stage 
                            FROM project_stages 
                            LEFT JOIN project_stage ON project_stage.id = project_stages.stage_id 
                            WHERE project_stages.project_id = pm.id and project_stages.stages_status_id = 1 
                            ORDER BY `sequence` desc LIMIT 1), '') stage 
                FROM project_main pm 
                LEFT JOIN project_category pc ON pm.catagory_id = pc.id 
                LEFT JOIN project_client_type pct ON pm.client_type_id = pct.id 
                LEFT JOIN project_priority pp ON pm.priority_id = pp.id 
                LEFT JOIN project_status ps ON pm.project_status_id = ps.id 
                LEFT JOIN project_stage pst ON pm.stage_id = pst.id 
                LEFT JOIN user ON pm.create_id = user.id where 1=1 ";

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

if($flo != "" && $flo != "0")
{
    $query = $query . " and COALESCE((SELECT project_est_prob.prob 
                                        FROM project_est_prob 
                                        WHERE project_est_prob.project_id = pm.id 
                                    order by created_at desc limit 1), pm.estimate_close_prob) >= " . $flo . " ";
}

if($fup != "" && $fup != "0")
{
    $query = $query . " and COALESCE((SELECT project_est_prob.prob 
                                        FROM project_est_prob 
                                        WHERE project_est_prob.project_id = pm.id 
                                    order by created_at desc limit 1), pm.estimate_close_prob) <= " . $fup . " ";
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
                $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) desc";
            else
                $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) ";
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
                $sOrder = ", COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) desc";
            else
                $sOrder = ", COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) ";
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
                $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) desc";
            else
                $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = pm.id order by created_at desc limit 1), pm.estimate_close_prob) ";
            break;  
        
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by pm.created_at desc ";

if($fcs != "")
{
    $query = "SELECT *
    FROM   (
                     SELECT    pm.id,
                               Coalesce(pc.category, '') category,
                               pct.client_type,
                               pct.class_name pct_class,
                               pp.priority,
                               pp.class_name pp_class,
                               pm.project_name,
                               Coalesce(ps.project_status, '') project_status,
                               Coalesce(
                                         (
                                         SELECT   project_est_prob.prob
                                         FROM     project_est_prob
                                         WHERE    project_est_prob.project_id = pm.id
                                         ORDER BY created_at DESC
                                         LIMIT    1), pm.estimate_close_prob) estimate_close_prob, 
                                         user.username, 
                                         Date_format(pm.created_at, '%Y-%m-%d') created_at, 
                                         DATE_FORMAT(pm.updated_at, '%Y-%m-%d') updated_at, 
                                         Coalesce(
                                        (
                                        SELECT    project_stage.stage
                                        FROM      project_stages
                                        LEFT JOIN project_stage
                                        ON        project_stage.id = project_stages.stage_id
                                        WHERE     project_stages.project_id = pm.id
                                        AND       project_stages.stages_status_id = 1
                                        ORDER BY  `sequence` DESC
                                        LIMIT     1), '') stage
                     FROM      project_main pm
                     LEFT JOIN project_category pc
                     ON        pm.catagory_id = pc.id
                     LEFT JOIN project_client_type pct
                     ON        pm.client_type_id = pct.id
                     LEFT JOIN project_priority pp
                     ON        pm.priority_id = pp.id
                     LEFT JOIN project_status ps
                     ON        pm.project_status_id = ps.id
                     LEFT JOIN project_stage pst
                     ON        pm.stage_id = pst.id
                     LEFT JOIN user
                     ON        pm.create_id = user.id
                     WHERE     1=1 ";

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

    if($flo != "" && $flo != "0")
    {
        $query = $query . " and COALESCE((SELECT project_est_prob.prob 
                                            FROM project_est_prob 
                                            WHERE project_est_prob.project_id = pm.id 
                                        order by created_at desc limit 1), pm.estimate_close_prob) >= " . $flo . " ";
    }
    
    if($fup != "" && $fup != "0")
    {
        $query = $query . " and COALESCE((SELECT project_est_prob.prob 
                                            FROM project_est_prob 
                                            WHERE project_est_prob.project_id = pm.id 
                                        order by created_at desc limit 1), pm.estimate_close_prob) <= " . $fup . " ";
    }

    $sOrder = "";
    if($op1 != "" && $op1 != "0")
    {
        switch ($op1)
        {
            case 1:
                if($od1 == 2)
                    $sOrder = "Coalesce(t.created_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(t.created_at, '9999-99-99') ";
                break;  
            case 2:
                if($od1 == 2)
                    $sOrder = "Coalesce(t.updated_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(t.updated_at, '9999-99-99') ";
                break;  
            case 3:
                if($od1 == 2)
                    $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) desc";
                else
                    $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) ";
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
                    $sOrder .= ", Coalesce(t.created_at, '0000-00-00') desc";
                else
                    $sOrder .= ", Coalesce(t.created_at, '9999-99-99') ";
                break;  
            case 2:
                if($od2 == 2)
                    $sOrder .= ", Coalesce(t.updated_at, '0000-00-00') desc";
                else
                    $sOrder .= ", Coalesce(t.updated_at, '9999-99-99') ";
                break;  
            case 3:
                if($od2 == 2)
                    $sOrder = ", COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) desc";
                else
                    $sOrder = ", COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) ";
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
                    $sOrder = "Coalesce(t.created_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(t.created_at, '9999-99-99') ";
                break;  
            case 2:
                if($od2 == 2)
                    $sOrder = "Coalesce(t.updated_at, '0000-00-00') desc";
                else
                    $sOrder = "Coalesce(t.updated_at, '9999-99-99') ";
                break;  
            case 3:
                if($od2 == 2)
                    $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) desc";
                else
                    $sOrder = "COALESCE((SELECT project_est_prob.prob FROM project_est_prob WHERE project_est_prob.project_id = t.id order by created_at desc limit 1), t.estimate_close_prob) ";
                break;  
           
            default:
        }
    }

    if($sOrder != "")
        $query = $query . " order by  " . $sOrder;
    else
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
    $updated_at = $row['updated_at'];
    $stage = $row['stage'];
    $recent = GetRecentPost($row['id'], $db, $key);

    if(count($recent) > 0)
    {
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
            "updated_at" => $updated_at,
            "stage" => $stage,
            "recent" => $recent,
        );
    }
}

$filter_result = [];

if($key != "")
{
    foreach ($merged_results as &$value) {
        if(
            preg_match("/{$key}/i", $value['project_name']) ||
            $key == (count($value['recent']) == 1 ? substr($value['recent'][0]['created_at'], 0, 10) : "") ||
            preg_match("/{$key}/i", (count($value['recent']) == 1 ? substr($value['recent'][0]['username'], 0, 10) : "")))
        {
            $filter_result[] = $value;
        }
    }
}
else
    $filter_result = $merged_results;

echo json_encode($filter_result, JSON_UNESCAPED_SLASHES);


function GetRecentPost($project_id, $db, $key){
    $query = "
    SELECT  u.username, pc.created_at, CONCAT('project03_client?sid=', pc.stage_id) `url` FROM project_stage_client_task pc left join user u on u.id = pc.create_id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pc.status <> -1 
    UNION ALL
    SELECT  u.username, pt.created_at, CONCAT('project03_client?sid=', pc.stage_id) `url` FROM project_stage_client_task_comment pt left join user u on u.id = pt.create_id LEFT JOIN project_stage_client_task pc ON pt.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id where p.project_id = " . $project_id . " and pc.status <> -1 
    
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
            return $item2['created_at'] <=> $item1['created_at'];
        });
    
        foreach ($filter_result as $arr)
        {
            $sorted_result[] = array(
                "created_at" => $arr['created_at'],
                "username" => $arr['username'],
                "url" => $arr['url'],
             
            );
         
            break;
        }
    }

    return $sorted_result;
}