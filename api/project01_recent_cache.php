<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// task = ''
$query = "delete from project_main_recent_tmp where kind = ''";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, '' `url` FROM project_stage_client pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id left join project_main m on p.project_id = m.id WHERE pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', '', '')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'task'
$query = "delete from project_main_recent_tmp where kind = 'task'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pc.created_at, CONCAT('project03_client?sid=', pc.stage_id) `url` FROM project_stage_client_task pc left join user u on u.id = pc.create_id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pc.status <> -1 order by m.id, pc.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'task', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'task_comment'
$query = "delete from project_main_recent_tmp where kind = 'task_comment'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pt.created_at, CONCAT('project03_client?sid=', pc.stage_id) `url` FROM project_stage_client_task_comment pt left join user u on u.id = pt.create_id LEFT JOIN project_stage_client_task pc ON pt.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id  where pt.status <> -1 order by m.id, pt.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'task_comment', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other'
$query = "delete from project_main_recent_tmp where kind = 'other'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pm.stage_id) `url` FROM project_other_task pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id left join project_main m on p.project_id = m.id  where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other_message'
$query = "delete from project_main_recent_tmp where kind = 'other_message'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pc.stage_id) `url` FROM project_other_task_message pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task pc ON pm.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other_message', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other_message_reply'
$query = "delete from project_main_recent_tmp where kind = 'other_message_reply'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pc.stage_id) `url` FROM project_other_task_message_reply pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message ps ON pm.message_id = ps.id  LEFT JOIN project_other_task pc ON ps.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other_message_reply', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other_r'
$query = "delete from project_main_recent_tmp where kind = 'other_r'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pm.stage_id) `url` FROM project_other_task_r pm left join user u on u.id = pm.create_id LEFT JOIN project_stages p ON pm.stage_id = p.id left join project_main m on p.project_id = m.id  where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other_r', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other_message_r'
$query = "delete from project_main_recent_tmp where kind = 'other_message_r'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pc.stage_id) `url` FROM project_other_task_message_r pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task_r pc ON pm.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];
        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other_message_r', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'other_message_reply_r'
$query = "delete from project_main_recent_tmp where kind = 'other_message_reply_r'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pm.created_at, CONCAT('project03_other?sid=', pc.stage_id) `url` FROM project_other_task_message_reply_r pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message_r ps ON pm.message_id = ps.id  LEFT JOIN project_other_task_r pc ON ps.task_id = pc.id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pm.status <> -1 order by m.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if($id != $row['id'] && $row['id'] != null)
    {
        $id = $row['id'];

        $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'other_message_reply_r', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }
}

// task = 'action_comment'
$query = "delete from project_main_recent_tmp where kind = 'action_comment'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT ps.id, ps.project_name, u.username, pm.created_at, CONCAT('project02?p=', ps.id) `url` FROM project_action_comment pm left join user u on u.id = pm.create_id  LEFT JOIN project_main ps ON pm.project_id = ps.id  where pm.status <> -1 order by ps.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'action_comment', '" . $row['url'] . "')";
    $stmt1 = $db->prepare($query);
    $stmt1->execute();
}

// task = 'est_prob'
$query = "delete from project_main_recent_tmp where kind = 'est_prob'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT ps.id, ps.project_name, u.username, pm.created_at, CONCAT('project02?p=', ps.id) `url` FROM project_est_prob pm left join user u on u.id = pm.create_id  LEFT JOIN project_main ps ON pm.project_id = ps.id  where pm.status <> -1 order by ps.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'est_prob', '" . $row['url'] . "')";
    $stmt1 = $db->prepare($query);
    $stmt1->execute();
}

// task = 'action_detail'
$query = "delete from project_main_recent_tmp where kind = 'action_detail'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT ps.id, ps.project_name, u.username, pm.created_at, CONCAT('project02?p=', ps.id) `url` FROM project_action_detail pm left join user u on u.id = pm.create_id  LEFT JOIN project_main ps ON pm.project_id = ps.id  where  pm.status <> -1 order by ps.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'action_detail', '" . $row['url'] . "')";
    $stmt1 = $db->prepare($query);
    $stmt1->execute();
}

// task = 'approve'
$query = "delete from project_main_recent_tmp where kind = 'approve'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT ps.id, ps.project_name, u.username, pm.created_at, CONCAT('project02?p=', ps.id) `url` FROM project_approve pm left join user u on u.id = pm.create_id  LEFT JOIN project_main ps ON pm.project_id = ps.id  where ps.status <> -1 order by ps.id, pm.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'approve', '" . $row['url'] . "')";
    $stmt1 = $db->prepare($query);
    $stmt1->execute();
}

// task = 'ameeting'
$query = "delete from project_main_recent_tmp where kind = 'ameeting'";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "SELECT m.id, m.project_name, u.username, pc.created_at, CONCAT('project03_ameeting?sid=', pc.stage_id) `url` FROM project_a_meeting pc left join user u on u.id = pc.create_id LEFT JOIN project_stages p ON pc.stage_id = p.id left join project_main m on p.project_id = m.id where pc.status <> -1 order by m.id, pc.created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $query = "insert into project_main_recent_tmp (project_id, project_name, username, created_at, kind, url) values (" . $row['id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', 'ameeting', '" . $row['url'] . "')";
    $stmt1 = $db->prepare($query);
    $stmt1->execute();
}

$query = "truncate table project_main_recent";
$stmt = $db->prepare($query);
$stmt->execute();

$query = "select * from project_main_recent_tmp order by project_id, created_at desc";
$stmt = $db->prepare($query);
$stmt->execute();

$project_id = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if($project_id != $row['project_id'])
    {
        $project_id = $row['project_id'];
        $query = "insert into project_main_recent (project_id, project_name, username, created_at, kind, url) values (" . $row['project_id'] . ", '" . $row['project_name'] . "', '" . $row['username'] . "', '" . $row['created_at'] . "', '" . $row['kind'] . "', '" . $row['url'] . "')";
        $stmt1 = $db->prepare($query);
        $stmt1->execute();
    }

}