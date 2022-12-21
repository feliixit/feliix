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
        // get database connection
        $uid = $user_id;
        $stage_id = (isset($_POST['stage_id']) ?  $_POST['stage_id'] : 0);
        $title = (isset($_POST['title']) ?  $_POST['title'] : '');
        $priority = (isset($_POST['priority']) ?  $_POST['priority'] : 0);
        $assignee = (isset($_POST['assignee']) ?  $_POST['assignee'] : '');
        $collaborator = (isset($_POST['collaborator']) ?  $_POST['collaborator'] : '');
        $due_date = (isset($_POST['due_date']) ?  $_POST['due_date'] : '');
        $due_time = (isset($_POST['due_time']) ?  $_POST['due_time'] : '');
        $detail = (isset($_POST['detail']) ?  $_POST['detail'] : '');

        $order = (isset($_POST['order']) ?  $_POST['order'] : '');
        $order_type = (isset($_POST['order_type']) ?  $_POST['order_type'] : '');
        $category = (isset($_POST['category']) ?  $_POST['category'] : '');

        $query = "INSERT INTO project_other_task
        SET
            `stage_id` = :stage_id,
            `title` = :title,
            `priority` = :priority,
            `assignee` = :assignee,
            `collaborator` = :collaborator,
            `due_date` = :due_date,
            `due_time` = :due_time,
            `detail` = :detail,
            
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':stage_id', $stage_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':assignee', $assignee);
        $stmt->bindParam(':collaborator', $collaborator);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':due_time', $due_time);
        $stmt->bindParam(':detail', $detail);

        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();

                
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        $serial = 0;
        $query = "select count(*) + 1 from iq_main where order_type = :order_type and SUBSTRING(serial_name, 1, 1) = :category";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_type', $order_type);
        $stmt->bindParam(':category', substr($category, 0, 1));
        $stmt->execute();
        $serial = $stmt->fetchColumn();
        $serial = str_pad($serial, 4, '0', STR_PAD_LEFT);

        if($category == 'Office Systems')
            $serial = 'OI-' . $serial;

        if($category == 'Lighting')
            $serial = 'LI-' . $serial;
    

        $query = "INSERT INTO iq_main
        SET
            `iq_name` = :iq_name,
            `task_id` = :task_id,
            `order_type` = :order_type,
            `serial_name` = :serial_name,
          
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':iq_name', $order);
        $stmt->bindParam(':task_id', $last_id);
        $stmt->bindParam(':order_type', $order_type);
        $stmt->bindParam(':serial_name', $serial);
    
        $stmt->bindParam(':create_id', $uid);

        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {

            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        // send notify mail
        SendNotifyMail($last_id, $stage_id, $order_type, $order, $serial);

        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        echo $jsonEncodedReturnArray;

        break;
}




function SendNotifyMail($last_id, $stage_id, $order_type, $order_name)
{
    $project_name = "";
    $task_name = "";
    $stages_status = "";
    $create_id = "";

    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);
    //$_iq_main = GetOdMain($last_id, $db);
 
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

    //$order_type = $_iq_main[0]["order_type"];
    //$order_name = $_iq_main[0]["order_name"];

    task_notify_inquiry("create", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, GetOrderType($order_type), $order_name);

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT  project_name, title task_name, 
            (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, 
            pt.created_at,
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


function GetOdMain($id, $db)
{
    $sql = "select * from iq_main
            WHERE task_id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetOrderType($order_type)
{
    $order_type_name = "";

    switch ($order_type) {
        case 'taiwan':
            $order_type_name = "Order - Taiwan";
            break;
        case 'stock':
            $order_type_name = "Order - Stocks";
            break;
        case 'sample':
            $order_type_name = "Order - Sample";
            break;
        case 'inquiry':
            $order_type_name = "Inquiry";
            break;
    }

    return $order_type_name;
}