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

$yesterday = date("Y-m-d", strtotime("-1 days"));

// get last year by "Y-m-d" minus 1 day
$last_year = date("Y-m-d", strtotime("-1 year -1 days"));

$sql = "select id, username, email, date_start_company, seniority from `user` where status = 1;";

$user_array = array();

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $username = $row['username'];
    $email = $row['email'];
    $date_start_company = $row['date_start_company'];
    $seniority = $row['seniority'];

    if($date_start_company == "")
    {
        continue;
    }   

    $seniority_new = round(((date_diff(date_create($date_start_company), date_create($yesterday))->y * 12) + date_diff(date_create($date_start_company), date_create($yesterday))->format('%m')) / 12, 1);

    // if one year, send email
    if ($seniority != $seniority_new && ($seniority_new == 0.5 || $seniority_new == 1.0 || ($seniority_new > 1.0 && filter_var($seniority_new, FILTER_VALIDATE_INT))))
    {
        $user_array[] = [
            'id' => $id,
            'username' => $username,
            'email' => $email,
            'date_start_company' => $date_start_company,
            'seniority_old' => $seniority,
            'seniority_new' => $seniority_new,
        ];
    }

    if($seniority != $seniority_new && $seniority_new == 0.5)
    {
        $sql = "update `user` set seniority = $seniority_new where id = $id;";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();
    
        $sql = "update `user` set sil = 5 where id = $id and leave_level = 'A';";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();
    
    }

    if($seniority != $seniority_new && $seniority_new == 1.0)
    {
        $sql = "update `user` set seniority = $seniority_new where id = $id;";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();
    
        $sql = "update `user` set vl_sl = 5 where id = $id and leave_level = 'A';";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();

    }

    if($seniority != $seniority_new && $seniority_new > 1 && filter_var($seniority_new, FILTER_VALIDATE_INT))
    {
        $sql = "update `user` set seniority = $seniority_new where id = $id;";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();

        $sql = "update `user` set vl_sl = vl_sl + 2 where id = $id and leave_level = 'A';";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute();

       
    }

}

if(count($user_array) > 0)
    {
        batch_date_start_company_notify_mail($user_array);
    }





