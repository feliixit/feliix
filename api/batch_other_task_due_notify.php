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
        $stages = $row["stage"];
        $create_id = $row["create_id"];
        $created_at = $row["created_at"];
        $assignee = $row["assignee"];
        $collaborator = $row["collaborator"];

        $due_date = str_replace("-", "/", $row["due_date"]);
        $detail = $row["detail"];

        $stage_id = $row["stage_id"];

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
        }
    }
}