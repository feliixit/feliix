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

$sql = "SELECT user.id,
            username,
            Coalesce(department, '') department,
            user.email,
            Coalesce(title, '')      title,
            user.`status`
            FROM   user
            LEFT JOIN user_department
                ON user.apartment_id = user_department.id
            LEFT JOIN user_title
                ON user.title_id = user_title.id
            WHERE  user.`status` = 1
            AND Upper(user_title.title) IN (
                    'ASSISTANT CUSTOMER VALUE DIRECTOR', 'CUSTOMER VALUE DIRECTOR', 'LIGHTING VALUE CREATION DIRECTOR',
                    'OFFICE SPACE VALUE CREATION DIRECTOR',
                    'ASSISTANT BRAND MANAGER',
                    'BRAND MANAGER',
                    'ENGINEERING MANAGER',
                    'OPERATIONS MANAGER', 
                    'MANAGING DIRECTOR') ";

$review_start_date = date("Y-m", strtotime("-3 month"));
$review_end_date = date("Y-m", strtotime("-1 month"));

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $username = $row['username'];
    $email = $row['email'];

    batch_performance_review_notify_mail($username, $email, $review_start_date, $review_end_date);
}
