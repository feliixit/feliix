<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'mail.php';

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
        $username = $decoded->data->username;

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
$conf = new Conf();

$uid = $user_id;

$task_id = (isset($_POST['task_id']) ?  $_POST['task_id'] : 0);
$uid = $user_id;
$title = (isset($_POST['title']) ?  $_POST['title'] : '');
$priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);
$assignee = (isset($_POST['assignee']) ?  $_POST['assignee'] : '');
$collaborator = (isset($_POST['collaborator']) ?  $_POST['collaborator'] : '');
$due_date = (isset($_POST['due_date']) ?  $_POST['due_date'] : '');
$due_time = (isset($_POST['due_time']) ?  $_POST['due_time'] : '');
$detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');

$_record = GetTaskDetailOrg($task_id, $db);

$mail_type = 0;

if($_record[0]["status"] != $status)
    $mail_type = 1;

if($_record[0]["title"] != $title)
    $mail_type = 2;

if($_record[0]["priority"] != $priority)
    $mail_type = 2;

if($_record[0]["assignee"] != $assignee)
    $mail_type = 2;

if($_record[0]["collaborator"] != $collaborator)
    $mail_type = 2;

if($_record[0]["due_date"] != $due_date)
    $mail_type = 2;

if($_record[0]["due_time"] != $due_time)
    $mail_type = 2;

if($_record[0]["detail"] != $detail)
    $mail_type = 2;


try{
    $query = "update project_other_task_l
    SET
        `title` = :title,
        `priority` = :priority,
        `status` = :status,
        `assignee` = :assignee,
        `collaborator` = :collaborator,
        `due_date` = :due_date,
        `due_time` = :due_time,
        `detail` = :detail,
        updated_id = :updated_id,
        updated_at = now()
    where id = :id ";

    // prepare the query
    $stmt = $db->prepare($query);

    $stmt->bindParam(':id', $task_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':assignee', $assignee);
    $stmt->bindParam(':collaborator', $collaborator);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':due_time', $due_time);
    $stmt->bindParam(':detail', $detail);

    $stmt->bindParam(':updated_id', $uid);

    $jsonEncodedReturnArray = "";

    if ($stmt->execute()) {

        // send notify mail
        if($mail_type == 1)
            SendNotifyMail01($task_id, $_record[0]["status"], $username);

        //if($mail_type == 2)
        //    SendNotifyMail02($task_id, $_record[0]["status"]);

        if($mail_type == 2)
        {
            $task_status = "";
            switch ($status) {
                case "0":
                    $task_status = "Ongoing";
                    break;
                case "1":
                    $task_status = "Pending";
                    break;
                case "2":
                    $task_status = "Close";
                    break;
                case "-1":
                    $task_status = "DEL";
                    break;
            }
            $_record = GetTaskDetail($task_id, $db);
            task_notify_admin_l("edit", $task_status, $title, "", $_record[0]["create_id"], $assignee, $collaborator, $due_date . " " . $due_time, $detail, $task_id, $uid, 0, $_record[0]["created_at"]);
        }

        $returnArray = array('batch_id' => $task_id);
       
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    }
    else
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}


function SendNotifyMail01($last_id, $old_status_id, $username)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $stage_id = 0;

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);

    $old_status = "";
    switch ($old_status_id) {
        case "0":
            $old_status = "Ongoing";
            break;
        case "1":
            $old_status = "Pending";
            break;
        case "2":
            $old_status = "Close";
            break;
        case "-1":
            $old_status = "DEL";
            break;
    }
 
    $project_name = $_record[0]["project_name"];
    $task_name = $_record[0]["task_name"];
    $stages_status = $_record[0]["stages_status"];
    $stages = $_record[0]["stage"];
    $create_id = $_record[0]["create_id"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $due_date = str_replace("-", "/", $_record[0]["due_date"]);
    $detail = $_record[0]["detail"];

    $stage_id = $_record[0]["stage_id"];
    $task_status = $_record[0]["task_status"];

    task_notify01_admin_l($old_status, $task_status, $username, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $last_id);

}


function SendNotifyMail02($last_id, $old_status_id)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $stage_id = 0;

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);

    $old_status = "";
    switch ($old_status_id) {
        case "0":
            $old_status = "Ongoing";
            break;
        case "1":
            $old_status = "Pending";
            break;
        case "2":
            $old_status = "Close";
            break;
        case "-1":
            $old_status = "DEL";
            break;
    }
 
    $project_name = $_record[0]["project_name"];
    $task_name = $_record[0]["task_name"];
    $stages_status = $_record[0]["stages_status"];
    $stages = $_record[0]["stage"];
    $create_id = $_record[0]["create_id"];
    $created_at = $_record[0]["created_at"];
    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $due_date = str_replace("-", "/", $_record[0]["due_date"]);
    $detail = $_record[0]["detail"];

    $stage_id = $_record[0]["stage_id"];
    $task_status = $_record[0]["task_status"];

    task_notify02($old_status, $task_status, $project_name, $task_name, $stages, $stages_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id);

}


function GetTaskDetail($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.created_at,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_l pt
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


function GetTaskDetailOrg($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_l pt

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
