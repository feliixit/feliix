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

$task_id_to_del = (isset($_POST['task_id_to_del']) ?  $_POST['task_id_to_del'] : '');
$order_id = (isset($_POST['order_id']) ?  $_POST['order_id'] : 0);

try{
    $query = "update project_other_task
    SET
        status = -1,
        updated_id = :updated_id,
        updated_at = now()
    where id = :id ";

    // prepare the query
    $stmt = $db->prepare($query);

    $stmt->bindParam(':updated_id', $uid);
    $stmt->bindParam(':id', $task_id_to_del);

    $jsonEncodedReturnArray = "";
    if ($stmt->execute()) {

        // send notify mail
        SendNotifyMail($task_id_to_del);

        $returnArray = array('ret' => $task_id_to_del);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    }
    else
    {
        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }

    $query = "update od_main
    SET
        status = -1,
        updated_id = :updated_id,
        updated_at = now()
    where id = :id ";

    // prepare the query
    $stmt = $db->prepare($query);

    $stmt->bindParam(':updated_id', $uid);
    $stmt->bindParam(':id', $order_id);

    if (!$stmt->execute()) {

        $arr = $stmt->errorInfo();
        error_log($arr[2]);
    }

    echo $jsonEncodedReturnArray;
}
catch (Exception $e)
{
    error_log($e->getMessage());
}



function SendNotifyMail($last_id)
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

    $_od_main = GetOdMain($last_id, $db);
 
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

    $order_type = $_od_main[0]["order_type"];
    $order_name = $_od_main[0]["order_name"];
  

    task_notify_order("del", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, GetOrderType($order_type), $order_name);

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT ps.id stage_id, project_name, title task_name, 
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
    $sql = "select * from od_main
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
 
    }

    return $order_type_name;
}