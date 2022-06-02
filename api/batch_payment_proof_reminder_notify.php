<?php
include_once 'config/core.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';
include_once 'config/database.php';

include_once 'mail.php';

date_default_timezone_set('Asia/Taipei');

$database = new Database();
$db = $database->getConnection();

$sql = "select u.username, u.email , pm.project_name , pm.catagory_id, ps.remark , ps.created_at , ps.kind  from project_proof ps
            left JOIN project_main pm ON ps.project_id = pm.id 
            left join `user` u on u.id = ps.create_id  
            where ps.`status` = 0
            AND ps.created_at <= DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 DAY), '%Y-%m-%d 23:59:59')";

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $username = $row['username'];
    $email = $row['email'];
    $project_name = $row['project_name'];
    $catagory_id = $row['catagory_id'];
    $remark = $row['remark'];
    $created_at = $row['created_at'];
    $kind = $row['kind'];
  
    send_pay_reminder_mail_new($username, $email,  $username, $project_name, $remark, $created_at, $catagory_id, $kind);
}
