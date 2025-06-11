<?php
include_once 'config/core.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'config/database.php';

include_once 'mail.php';

date_default_timezone_set('Asia/Taipei');

$database = new Database();
$db = $database->getConnection();

$id = 0;

$review_start_date = date("Y-m-d H:i:s", strtotime("+1 day -1 hour"));
$review_end_date = date("Y-m-d H:i:s", strtotime("+1 day"));

SendNotifyMail($review_start_date, $review_end_date, "", $db);
SendNotifyMail($review_start_date, $review_end_date, "_a", $db);
SendNotifyMail($review_start_date, $review_end_date, "_d", $db);
SendNotifyMail($review_start_date, $review_end_date, "_l", $db);
SendNotifyMail($review_start_date, $review_end_date, "_o", $db);
SendNotifyMail($review_start_date, $review_end_date, "_sl", $db);
SendNotifyMail($review_start_date, $review_end_date, "_sv", $db);
SendNotifyMail($review_start_date, $review_end_date, "_c", $db);


function GetOrderInfo($task_id, $db)
{
    $sql = "select id, od_name, order_type, serial_name
            from od_main
            where task_id = " . $task_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "od_name" => $row['od_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}


function GetOrderType($order_type)
{
    $order_type_name = "";

    switch ($order_type) {
        case 'taiwan':
            $order_type_name = "Order - Close Deal";
            break;
        case 'stock':
            $order_type_name = "Order - Stocks";
            break;
        case 'sample':
            $order_type_name = "Order - Sample";
            break;
        case 'mockup':
            $order_type_name = "Order - Mockup";
            break;
        case 'inquiry':
            $order_type_name = "Inquiry";
            break;
    }

    return $order_type_name;
}

function SendNotifyMail($review_start_date, $review_end_date, $kind, $db)
{

    $sql = "SELECT pt.id id, ps.id stage_id, project_name, title task_name, 
        (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, 
        pt.created_at,
        pt.create_id,
        pt.assignee,
        pt.collaborator,
        due_date,
        stage,
        (CASE pt.`status` WHEN '0' THEN 'Ongoing' WHEN '1' THEN 'Pending' WHEN '2' THEN 'Close' when '-1' then 'DEL' END ) as `task_status`, 
        detail
    FROM project_other_task" . $kind . " pt
        LEFT JOIN project_stages ps ON pt.stage_id = ps.id
        LEFT JOIN project_stage psg ON ps.stage_id = psg.id
        left JOIN project_main pm ON ps.project_id = pm.id 
    WHERE (CONCAT(due_date, ' ', IF(LENGTH(due_time), due_time, '12:00:00')) BETWEEN '$review_start_date' AND '$review_end_date')
    AND pt.status <> -1
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $project_name = $row["project_name"];
        $task_name = $row["task_name"];
        $stages_status = $row["stages_status"];
        $task_status = $row["task_status"];
        $stages = $row["stage"];
        $create_id = $row["create_id"];
        $created_at = $row["created_at"];
        $assignee = $row["assignee"];
        $collaborator = $row["collaborator"];

        $order = GetOrderInfo($id, $db);

        $due_date = str_replace("-", "/", $row["due_date"]);
        $detail = $row["detail"];

        $stage_id = $row["stage_id"];

        if(count($order) > 0 && $kind == "")
            task_notify_order("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at, GetOrderType($order[0]["order_type"]), $order[0]["od_name"]);
        else
        {
            switch ($kind) {
                case "":
                    task_notify("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, $created_at);
                    break;
                case "_a":
                    task_notify_admin("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_d":
                    task_notify_admin_d("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_l":
                    task_notify_admin_l("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_o":
                    task_notify_admin_o("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_sl":
                    task_notify_admin_sl("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_sv":
                    task_notify_admin_sv("notify", $project_name, $task_name, $stages, $create_id, $assignee, $collaborator, $due_date, $detail, $id, 0, 0, $created_at);
                    break;
                case "_c":
                    task_notify_admin_c("notify", $project_name, $task_name, $task_status, $create_id, $assignee, $collaborator, $due_date, $detail, $stage_id, 0, 0, $created_at);
                    break;
            }
        }
    }
}