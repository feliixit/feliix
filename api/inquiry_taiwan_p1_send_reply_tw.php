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
        $user_name = $decoded->data->username;

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

$iq_id = (isset($_POST['iq_id']) ?  $_POST['iq_id'] : 0);
$items = (isset($_POST['items']) ?  $_POST['items'] : '[]');
$comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
$iq_name = (isset($_POST['iq_name']) ? $_POST['iq_name'] : '');
$serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

$action = 'send_note_reply_tw';

$items_array = json_decode($items,true);

try{

    // update main table
    $query = "UPDATE iq_main SET `status` = 1, `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':updated_id', $uid);
    $stmt->bindParam(':id', $iq_id);
    try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }


    for($i=0; $i<count($items_array); $i++) 
    {
        $item_id = $items_array[$i]['id'];

        if($item_id != 0)
        {

            $pre_status = GetCurrentStatus($item_id, $db);
            $json = json_encode($pre_status);

            $query = "update iq_item
            SET
                `status` = 1
            where id = :id ";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $item_id);

            if($stmt->execute())
            {
                
            }
        }
    
    }


    $query = "INSERT INTO iq_process
    SET
        `iq_id` = :iq_id,
        `comment` = :comment,
        `action` = :action,
        `items` = :items,
        `status` = 0,
        `create_id` = :create_id,
        `created_at` =  now() ";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':iq_id', $iq_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':items', $items);
    $stmt->bindParam(':create_id', $user_id);


    $last_id = 0;
    // execute the query, also check if query was successful
    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }


    $task = GetTaskInfo($iq_id, $db);
    if(count($task) == 0) {
        $task_table_info = "";
        $task_department_info = "";
    }
    else {
        $task_table_info = GetTaskTableInfo($task[0]['task_type']);
        $task_department_info = GetTaskDepartmentInfo($task[0]['task_type']);
        }
    $users = GetTaskUsersInfo($iq_id, $db, $task_table_info);

    $users .= ", " . $user_id;

    inquiry_notification_reply($user_name, '48', $users, $project_name, $serial_name, $iq_name, 'Inquiry - Taiwan', $comment, $action, $items_array, $iq_id, $task_department_info);

    $returnArray = array('ret' => $item_id);
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}


function GetTaskInfo($id, $db)
{
    $query = "SELECT task_id, task_type FROM iq_main WHERE id = :id ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    $merged_results = array();

    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetTaskUsersInfo($pid, $db, $task_type)
{
    $users = "";

    $query = "select create_id, assignee, collaborator from project_other_task" . $task_type . " where id = (select task_id from iq_main where id = :pid)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pid', $pid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row['create_id'] != '')
    {
        $users .= $row['create_id'] . ',';
    }
    if($row['assignee'] != '')
    {
        $users .= $row['assignee'] . ',';
    }
    if($row['collaborator'] != '')
    {
        $users .= $row['collaborator'] . ',';
    }

    $users = rtrim($users, ",");

    return $users;
}

function GetTaskTableInfo($task_type)
{
    $info = "";

    if($task_type == "LT")
    {
        $info = "_l";
    }
    else if($task_type == "OS")
    {
        $info = "_o";
    }
    else if($task_type == "SLS")
    {
        $info = "_sl";
    }

    return $info;
}

function GetTaskDepartmentInfo($task_type)
{
    $info = "";

    if($task_type == "LT")
    {
        $info = "Lighting";
    }
    else if($task_type == "OS")
    {
        $info = "Office System";
    }
    else if($task_type == "SLS")
    {
        $info = "Sales";
    }

    return $info;
}

function GetCurrentStatus($id, $db)
{
    $query = "select confirm, status from iq_item where id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $arr = array('confirm' => $row["confirm"], 'status' => $row["status"]);
    
    return $arr;
}