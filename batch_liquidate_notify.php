<?php

include_once 'api/config/core.php';
include_once 'api/config/database.php';
include_once 'api/config/conf.php';
require_once 'vendor/autoload.php';

include_once 'api/mail.php';

$database = new Database();
$db = $database->getConnection();

$id = 0;

$sql = "SELECT pm.id, 
DATE_FORMAT(ph.created_at, '%Y/%m/%d') created_at, 
DATE_FORMAT(NOW(), '%Y/%m/%d')
from apply_for_petty pm 
left join petty_history ph ON petty_id = pm.id and `action` = 'Releaser Released'
where pm.`status` in (6, 7) 
AND DATE_FORMAT(ph.created_at, '%Y/%m/%d') <> DATE_FORMAT(NOW(), '%Y/%m/%d') 
AND MOD(DATEDIFF(DATE_FORMAT(NOW(), '%Y/%m/%d'), DATE_FORMAT(ph.created_at, '%Y/%m/%d')), 2) = 0";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            SendNotifyMail($id, $db);
        }

function SendNotifyMail($id, $db)
{
    $requestor = "";
    $requestor_email = "";
    $request_no = "";
    $applicant = "";
    $department = "";
    $application_Time = "";
    $project_name = "";
    $date_request = "";
    $total_amount = "";
    $reject_reason = "";

    $date_release = "";
 

    $_record = GetPettyDetail($id, $db);
    $requestor = $_record[0]["username"];
    $requestor_email = $_record[0]["email"];
    $request_no = $_record[0]["request_no"];
    $applicant = $_record[0]["username"];
    $department = $_record[0]["department"];
    $application_Time = str_replace("-", "/", $_record[0]["created_at"]);
    $project_name = $_record[0]["project_name"];
    $date_request = $_record[0]["date_requested"];
    $total_amount = $_record[0]["total"];


    $date_release = GetReleaseHistory($id, $db);

    batch_liquidate_notify_mail($request_no, $requestor, $requestor_email, $department, $application_Time, $project_name, $date_request, $total_amount, $reject_reason, $date_release);
      
}


function GetReleaseHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Releaser Released' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetPettyDetail($id, $db)
{
    $sql = "SELECT request_no, project_name, u.username, u.email, ud.department, ap.created_at, ap.date_requested, 
            (SELECT SUM(price * qty) FROM petty_list WHERE petty_id = :id1) total, ap.amount_liquidated,
                        ap.remark_liquidated
            FROM apply_for_petty ap 
            LEFT JOIN user u ON ap.uid = u.id 
            left JOIN user_department ud ON ud.id = u.apartment_id
            where ap.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id1',  $id);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

