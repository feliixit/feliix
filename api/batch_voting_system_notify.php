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

// get today by "Y-m-d"
$today = date("Y-m-d");

$sql = "select pm.id,
            pm.topic, 
            pm.access, 
            pm.start_date, 
            pm.end_date, 
            pm.rule, 
            pm.`status`,
            pm.create_id,
            c_user.username AS created_by, 
            DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at 
        from voting_template pm 
        LEFT JOIN user c_user ON pm.create_id = c_user.id and pm.status <> -1 ";

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $username = $row['created_by'];
    $created_at = $row['created_at'];
    $topic = $row['topic'];
    $review_start_date = $row['start_date'];
    $review_end_date = $row['end_date'];
    $od_id = $row['id'];
    $cc_id = $row['create_id'];
    $rule = $row['rule'];

    // if today is review_start_date, send email
    if ($today == $review_start_date)
    {
        // json to array
        $access = json_decode($row['access'], true);
        // convert to string
        $access_str = implode(",", $access);
        $receiver = knowledge_access_get($access_str, $db);

        batch_voting_system_notify_mail($receiver, $cc_id, $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "start");
    }

    // if today is review_start_date, send email
    if ($today == $review_end_date)
    {
        // json to array
        $access = json_decode($row['access'], true);
        // convert to string
        $access_str = implode(",", $access);
        $receiver = knowledge_access_get($access_str, $db);

        batch_voting_system_notify_mail($receiver, "",  $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "end");
        batch_voting_system_notify_mail($cc_id, "",  $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "mgt");
    }
}

function knowledge_access_get($access, $db)
{
    $users = array();

    $query = "select `user`.id,
                username , email, department from `user` 
            left join `user_department` on `user`.apartment_id = `user_department`.id
            where `user`.status = 1";

    $username = "";
    $email = "";
    $department = "";

    $access_up = strtoupper($access); 

    $stmt_cnt = $db->prepare( $query );
    $stmt_cnt->execute();
    while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
        $uid = $row['id'];
        $username = $row['username'];
        $email = $row['email'];
        $department = $row['department'];

        // if username or department part of access then add to uses
        if(strpos($access_up, strtoupper($username)) !== false || strpos($access_up, strtoupper($department)) !== false || strpos($access_up, "ALL") !== false)
        {
            $users[] = $uid;
        }
    }

    return implode(",", $users);
}

function GetRuleText($rule)
{
    $rule_text = "";

    if($rule == "1")
        $rule_text = "one person - one vote";
    else if($rule == "2")
        $rule_text = "one person - two votes";
    else if($rule == "3")
        $rule_text = "one person - three votes";

    return $rule_text;
}