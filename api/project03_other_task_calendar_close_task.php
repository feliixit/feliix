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

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$uid = $user_id;
$category = (isset($_POST['category']) ?  $_POST['category'] : '');

if($category != 'AD' && $category != 'DS' && $category != 'LT_T' && $category != 'OS_T' && $category != 'SLS' && $category != 'SVC')
    $_record = GetTaskDetailOrg($id, $db);

$mail_type = 1;

try{

    $query = "update project_other_task
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";

    if($category == 'AD')
    {
        $_record = GetTaskDetailOrg_a($id, $db);
        $query = "update project_other_task_a
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    if($category == 'DS')
    {
        $_record = GetTaskDetailOrg_d($id, $db);
        $query = "update project_other_task_d
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    if($category == 'OS_T')
    {
        $_record = GetTaskDetailOrg_o($id, $db);
        $query = "update project_other_task_o
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    if($category == 'LT_T')
    {
        $_record = GetTaskDetailOrg_l($id, $db);
        $query = "update project_other_task_l
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    if($category == 'SLS')
    {
        $_record = GetTaskDetailOrg_sls($id, $db);
        $query = "update project_other_task_sl
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    if($category == 'SVC')
    {
        $_record = GetTaskDetailOrg_svc($id, $db);
        $query = "update project_other_task_sv
                SET
                    `status` = :status,
                    updated_id = :updated_id,
                    updated_at = now()
                where id = :id ";
    }

    $status = 2;

    // prepare the query
    $stmt = $db->prepare($query);

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':updated_id', $uid);

    $jsonEncodedReturnArray = "";

    if ($stmt->execute()) {

        // send notify mail
        if($mail_type == 1)
            SendNotifyMail01($id, $_record[0]["status"], $username, $category);

        $returnArray = array('batch_id' => $id);
       
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


function SendNotifyMail01($last_id, $old_status_id, $username, $category)
{
    $task_name = "";
    $create_id = "";
    $assignee = "";
    $collaborator = "";

    $due_date = "";
    $detail = "";

    $stage_id = 0;

    $_record = array();

    $database = new Database();
    $db = $database->getConnection();

    if($category != 'AD' && $category != 'DS' && $category != 'LT_T' && $category != 'OS_T')
        $_record = GetTaskDetail($last_id, $db);

    if($category == 'AD')
        $_record = GetTaskDetail_a($last_id, $db);

    if($category == 'DS')
        $_record = GetTaskDetail_d($last_id, $db);

    if($category == 'LT_T')
        $_record = GetTaskDetail_l($last_id, $db);

    if($category == 'OS_T')
        $_record = GetTaskDetail_o($last_id, $db);

    if($category == 'SLS')
        $_record = GetTaskDetail_sls($last_id, $db);

    if($category == 'SVC')
        $_record = GetTaskDetail_svc($last_id, $db);

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
 
    $task_name = $_record[0]["task_name"];
 
    $create_id = $_record[0]["create_id"];

    $assignee = $_record[0]["assignee"];
    $collaborator = $_record[0]["collaborator"];

    $due_date = str_replace("-", "/", $_record[0]["due_date"]);
    $detail = $_record[0]["detail"];

    $task_status = $_record[0]["task_status"];

    task_notify01_admin($old_status, $task_status, $username, $task_name, $create_id, $assignee, $collaborator, $due_date, $detail, $last_id, $category);

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
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_a pt
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

function GetTaskDetail_a($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_a pt
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

function GetTaskDetail_d($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_d pt
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

function GetTaskDetail_l($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
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

function GetTaskDetail_o($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_o pt
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
function GetTaskDetail_svc($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_svc pt
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
function GetTaskDetail_sls($id, $db)
{
    $sql = "SELECT 0 stage_id, '' project_name, title task_name, 
            '' `stages_status`, 
            pt.create_id,
            pt.assignee,
            pt.collaborator,
            due_date,
            due_time,
            '' stage,
            (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
            detail
            FROM project_other_task_sl pt
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

function GetTaskDetailOrg_d($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_d pt

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
            FROM project_other_task pt

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

function GetTaskDetailOrg_a($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_a pt

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


function GetTaskDetailOrg_l($id, $db)
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


function GetTaskDetailOrg_o($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_o pt

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

function GetTaskDetailOrg_sls($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_sl pt

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

function GetTaskDetailOrg_svc($id, $db)
{
    $sql = "SELECT *
            FROM project_other_task_sv pt

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
