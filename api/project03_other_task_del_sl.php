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

$task_id_to_del = (isset($_POST['task_id_to_del']) ?  $_POST['task_id_to_del'] : '');


try{
    $query = "update project_other_task_sl
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
        SendNotifyMail($task_id_to_del, $uid);

        $returnArray = array('ret' => $task_id_to_del);
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

function SendNotifyMail($last_id, $uid)
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
 
    $project_name = $_record[0]["project_name"];
    $task_name = $_record[0]["task_name"];
    $stages_status = $_record[0]["stages_status"];
    $stages = $_record[0]["stage"];
    $create_id = $_record[0]["create_id"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $due_date = str_replace("-", "/", $_record[0]["due_date"]) . " " . $_record[0]["due_time"];
    $detail = $_record[0]["detail"];

    $stage_id = $_record[0]["stage_id"];

    task_notify_admin_sl("del", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $last_id, 0, $uid);

}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
                '' `stages_status`, 
                pt.create_id,
                pt.assignee,
                pt.collaborator,
                due_date,
                due_time,
                '' stage,
                detail
            FROM project_other_task_sl pt
            WHERE pt.id  = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}
