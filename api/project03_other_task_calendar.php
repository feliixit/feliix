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
        $user_id = $decoded->data->id;
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
        $category = (isset($_POST['category']) ?  $_POST['category'] : '');
   
        $sql = "SELECT pm.stage_id, 
                        pm.title, 
                        pm.due_date, 
                        pm.due_time, 
                        pm.`status` task_status, 
                        pm.create_id, 
                        pc.category, 
                        p.project_name, 
                        pm.collaborator, 
                        pm.assignee
                from project_other_task pm 
                    LEFT JOIN project_stages ps ON pm.stage_id = ps.id
                    LEFT JOIN project_main p ON ps.project_id = p.id
                    LEFT JOIN project_category pc ON p.catagory_id = pc.id
                where pm.`status` <> -1 AND pm.due_date <> '' ";

        if ($category == 'os') {
            $sql = $sql . " and LOWER(pc.category) = 'office systems' ";
        }

        if ($category == 'lt') {
            $sql = $sql . " and LOWER(pc.category) = 'lighting' ";
        }

        $merged_results = array();
        $return_result = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $stage_id = $row['stage_id'];
            $title = $row['due_time'] . ' ' . 
                    '[' . ($row['category'] == 'Lighting' ? 'LT' : 'OS') . ']' .
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

            $merged_results[] = array(
                "stage_id" => $stage_id,
                "title" => $title,
                "due_date" => $due_date,
                "due_time" => ($due_time == '' ? '23:59:59' : $due_time . '00'),
                "task_status" => $task_status,
                "create_id" => $create_id,
                "category" => $category,
                "project_name" => $project_name,
                "assignee" => $assignee,
                "collaborator" => $collaborator,
              
                "color" => $color,
            );
        }

        if($uid != 0)
        {
            foreach ($merged_results as &$value) {
                if(
                    in_array($user_id , $value['assignee']) ||
                    in_array($user_id , $value['collaborator'])  || 
                    $user_id == $value['create_id'])
                {
                    $return_result[] = $value;
                }
            }
        }
        else
            $return_result = $merged_results;

        echo json_encode($return_result, JSON_UNESCAPED_SLASHES);

        break;

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

