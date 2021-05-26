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

$sql = "SELECT 
            user_id, 
            create_id, 
            review_month, 
            DATE(NOW()),
            DATE_FORMAT(DATE_ADD(CONCAT(review_month, '-01'), INTERVAL 1 MONTH), '%Y-%m') end_month,
            DATE_ADD(CONCAT(review_month, '-10'), INTERVAL 2 month) dead_date,
            COALESCE(user_complete_at, '') user_complete_at, 
            COALESCE(manager_complete_at, '') manager_complete_at 
        FROM  performance_review  
            WHERE (user_complete_at IS null OR manager_complete_at IS NULL)
            AND `status` <> -1
            AND DATE_ADD(CONCAT(review_month, '-10'), INTERVAL 2 MONTH) < DATE(NOW())";



$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $s_date = $row['review_month'];
    $e_date = $row['end_month'];
    $dead_date = $row['dead_date'];
    $emp_id = $row['user_id'];
    $adm_id = $row['create_id'];
    $ucp = $row['user_complete_at'];
    $acp = $row['manager_complete_at'];

    if($ucp == '')
        batch_performance_evaluate_emp_notify_mail($s_date, $e_date, $dead_date, $adm_id, $emp_id);
   
    if($acp == '')
        batch_performance_evaluate_adm_notify_mail($s_date, $e_date, $dead_date, $adm_id, $emp_id);
}
