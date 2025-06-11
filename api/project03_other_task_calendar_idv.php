<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'mail.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $uid = $decoded->data->id;

        $department = $decoded->data->department;
        $position = $decoded->data->position;
        $my_department = GetDepartments($department);
        $my_level = GetTitleLevel($position);
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';


$database = new Database();
$db = $database->getConnection();


switch ($method) {
    case 'POST':
        $uid = (isset($_POST['uid']) ?  $_POST['uid'] : 0);
        $cat = (isset($_POST['category']) ?  $_POST['category'] : '');
        $sdate = (isset($_POST['sdate']) ?  $_POST['sdate'] : '');
        $edate = (isset($_POST['edate']) ?  $_POST['edate'] : '');
   
        $sql = "SELECT  pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        pc.category, 
                        p.project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

                        GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id

                from project_other_task pm 
                    LEFT JOIN project_stages ps ON pm.stage_id = ps.id
                    LEFT JOIN project_main p ON ps.project_id = p.id
                    LEFT JOIN project_category pc ON p.catagory_id = pc.id
                    LEFT JOIN user u on pm.create_id = u.id
                    LEFT JOIN user_title ut ON u.title_id = ut.id

                    left join user au on FIND_IN_SET(au.id, pm.assignee) 

                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    // edate be the last day of the month
    $edate = date("Y-m-t", strtotime($edate . "-01"));

    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "
                group by 
                    	pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pc.category, 
                        p.project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title ";


        $merged_results = array();
        $return_result = array();
 

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $stage_id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $stage_id = $row['stage_id'];
            $title = $row['due_time'] . ' ' . 
                    '[' . $priority . ']' .
                    '[' . GetTags($row['apartment_id']) . ']' .
                    '[ ' . $row['project_name'] . ' ] ' .
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => $stage_id,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $uid,
                "apartment_id" => $row['apartment_id'],
            );
        }

        $merged_results = array_merge($merged_results, CombineWithAD($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        
        $merged_results = array_merge($merged_results, CombineWithDS($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        
        $merged_results = array_merge($merged_results, CombineWithLT($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        
        $merged_results = array_merge($merged_results, CombineWithOS($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));

        $merged_results = array_merge($merged_results, CombineWithSLS($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        
        $merged_results = array_merge($merged_results, CombineWithSVC($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        
        $merged_results = array_merge($merged_results, CombineWithC($db, $my_department, $my_level, $uid, $uid, $sdate, $edate));
        

        if($uid != 0)
        {
            foreach ($merged_results as &$value) {
                if(
                    in_array($uid , $value['assignee']) )
                {
                    $return_result[] = $value;
                }
            }
        }

        if($cat != "")
        {
            $s2v = [
                'sls' => 1,
                'lt' => 2,
                'os' => 3,
                'ds' => 4,
                'eng' => 5,
                'ad' => 6,
                'store' => 7,
                'tw' => 8,
            ];

            $val = $s2v[$cat];

            foreach ($merged_results as &$value) {
                $ary = preg_split ("/\,/", $value['apartment_id']);
                if(in_array($val, $ary))
                {
                    $return_result[] = $value;
                }
            }

        }


        if($cat == "" && $uid == 0)
            $return_result = $merged_results;

        echo json_encode($return_result, JSON_UNESCAPED_SLASHES);

        break;

}

function CombineWithAD($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'AD' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id

                from project_other_task_a pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id

                left join user au on FIND_IN_SET(au.id, pm.assignee) 

                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "                
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title
                ";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' . 
                    '[' . $priority . ']' .
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function GetDepartments($department)
{
    $department = trim(strtoupper($department));
    $dep = "";
    switch ($department) {
        case "LIGHTING":
            $dep = "LT";
            break;
        case "OFFICE":
            $dep = "OS";
            break;
        case "DESIGN":
            $dep = "DS";
            break;
        case "ADMIN":
            $dep = "AD";
            break;
        case "STORE":
            $dep = "SLS";
            break;
    }

    return $dep;
}

function GetTitleLevel($title)
{
    $title = trim(strtoupper($title));

    return $title;
    
    // $level = 0;
    // switch ($title) {
    //     case "MANAGING DIRECTOR":
    //         $level = 5;
    //         break;
    //     case "CHIEF ADVISOR":
    //         $level = 5;
    //         break;
    //     case "LIGHTING VALUE CREATION DIRECTOR":
    //         $level = 4;
    //         break;
    //     case "OFFICE SPACE VALUE CREATION DIRECTOR":
    //         $level = 4;
    //         break;
    //     case "OPERATIONS MANAGER":
    //         $level = 4;
    //         break;
    //     case "BRAND MANAGER":
    //         $level = 4;
    //         break;
    //     case "ASSISTANT LIGHTING VALUE CREATION DIRECTOR":
    //         $level = 3;
    //         break;
    //     case "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR":
    //         $level = 3;
    //         break;
    //     case "ASSISTANT OPERATIONS MANAGER":
    //         $level = 3;
    //         break;
    //     case "ASSISTANT BRAND MANAGER":
    //         $level = 3;
    //         break;
    //     case "SENIOR LIGHTING VALUE CREATION SUPERVISOR":
    //         $level = 2;
    //         break;
    //     case "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR":
    //         $level = 2;
    //         break;
    //     case "JR. ACCOUNT EXECUTIVE":
    //         $level = 1;
    //         break;
    //     case "CUSTOMER VALUE SUPERVISOR":
    //         $level = 1;
    //         break;
    //     case "SENIOR CUSTOMER VALUE SUPERVISOR":
    //         $level = 1;
    //         break;
    // }

    // return $level;
}

function CombineWithDS($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'DS' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id

                from project_other_task_d pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' . 
                    '[' . $priority . ']' .
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function CombineWithLT($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'LT_T' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id
                from project_other_task_l pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' . 
                    '[' . $priority . ']' .
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function CombineWithOS($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'OS_T' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id
                from project_other_task_o pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' .
                    '[' . $priority . ']' . 
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function CombineWithSLS($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'SLS' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id
                from project_other_task_sl pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' .
                    '[' . $priority . ']' . 
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function CombineWithSVC($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        'ENG' category, 
                        '' project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id
                from project_other_task_sv pm 
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' .
                    '[' . $priority . ']' . 
                    '[' . GetTags($row['apartment_id']) . ']' .
                    
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => 0,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}
function CombineWithC($db, $my_department, $my_level, $my_id, $uid, $sdate, $edate)
{

        $sql = "SELECT pm.id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.stage_id,
                        pm.create_id, 
                        'C' category, 
                        p.project_name, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title user_title,

GROUP_CONCAT(DISTINCT au.apartment_id) apartment_id
                from project_other_task_c pm 
                LEFT JOIN project_stages ps ON pm.stage_id = ps.id
                LEFT JOIN project_main p ON ps.project_id = p.id
                LEFT JOIN project_category pc ON p.catagory_id = pc.id
                LEFT JOIN user u on pm.create_id = u.id
                LEFT JOIN user_title ut ON u.title_id = ut.id
                left join user au on FIND_IN_SET(au.id, pm.assignee) 
                where pm.`status` <> -1 AND pm.due_date <> '' 
                and FIND_IN_SET(" . $uid . ", pm.assignee) > 0";

if($sdate != ""){
    $sql .= " and pm.due_date >= '" . $sdate . "-01' ";
}

if($edate != ""){
    $sql .= " and pm.due_date <= '" . $edate . "' ";
    
}
    $sql .= "    
                group by 
                        pm.id,
                        pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status`, 
                        pm.create_id, 
                        pm.collaborator, 
                        pm.assignee,
                        pm.priority,
                        ut.title";


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $title = "";
        $due_date = "";
        $due_time = "";
        $task_status = "";
        $create_id = 0;
        $stage_id = 0;
        $category = "";
        $project_name = "";
        $assignee = [];
        $collaborator = [];
        $color = "";
        $priority = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $id = $row['id'];
            $priority = GetPriority($row['priority']);
            $title = $row['due_time'] . ' ' .
                    '[' . $priority . ']' . 
                    '[' . GetTags($row['apartment_id']) . ']' .
                    '[ ' . $row['project_name'] . ' ] ' .
                     $row['title'];
            $due_date = $row['due_date'];
            $due_time = $row['due_time'];
            $task_status = $row['task_status'];
            $create_id = $row['create_id'];
            $category = $row['category'];
            $project_name = $row['project_name'];
            $assignee = explode(",", $row['assignee']);
            $collaborator = explode(",", $row['collaborator']);
            $color = GetTaskColor($task_status, $due_date, $due_time);
            $level = GetTitleLevel($row['user_title']);
            $stage_id = $row['stage_id'];
            

            $merged_results[] = array(
                "id" => $id,
                "stage_id" => $stage_id,
                "title" => $title,
                "priority" => $priority,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
                "level" => $level,
                "color" => $color,
                "my_d" => $my_department,
                "my_l" => $my_level,
                "my_i" => $my_id,
                "apartment_id" => $row['apartment_id'],
            );
        }

        return $merged_results;
}

function GetTaskColor($task_status, $due_date, $due_time)
{
    $color = 'red';

    if($task_status == '1')
        $color = 'gray';

    if($task_status == '2')
        $color = 'green';

    if($task_status == '0')
    {
        $dueDate = $due_date . " 23:59";

        if($due_time != '')
            $dueDate = $due_date . " " . $due_time;

        $nowDate = date('Y-m-d H:i');

        if($dueDate >= $nowDate)
            $color = 'blue';
        else
            $color = 'red';
        
    }

    return $color;
}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT  project_name, title task_name, 
            (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            stage,
            detail
            FROM project_other_task pt
            LEFT JOIN project_stages ps ON pt.stage_id = ps.id
            LEFT JOIN project_stage psg ON ps.stage_id = psg.id
            left JOIN project_main pm ON ps.project_id = pm.id 
            WHERE pt.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetUserInfo($users, $db)
{
    $sql = "SELECT id, username, pic_url FROM user WHERE id IN (" . $users . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetPriority($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "No Priority";
            break;
        case "2":
            $location = "Low";
            break;
        case "3":
            $location = "Normal";
            break;
        case "4":
            $location = "High";
            break;
        case "5":
            $location = "Urgent";
            break;
        
    }

    return $location;
}

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case "0":
            $location = "Ongoing";
            break;
        case "1":
            $location = "Pending";
            break;
        case "2":
            $location = "Close";
            break;
                
    }

    return $location;
}

function GetTags($tag_str)
{
    if($tag_str == '')
        return "";
    $v2s = [
        0 => '',
        1  => 'SLS',
        2 => 'LT',
        3 => 'OS',
        4 => 'DS',
        5 => 'ENG',
        6 => 'AD',
        7 => 'STORE',
        8 => 'TW',
        13 => 'IT',
        14 => 'IT',
    ];

    $val_arr = [];
    $tag_arr = preg_split ("/\,/", $tag_str); 

    foreach ($tag_arr as &$value) {
        array_push($val_arr, $v2s[$value]);
    }

    return implode('][', $val_arr);

}