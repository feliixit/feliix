<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : '');

$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$new_info_account = (isset($_POST['new_info_account']) ?  $_POST['new_info_account'] : '');
$org_info_account = (isset($_POST['org_info_account']) ?  $_POST['org_info_account'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/apply_for_leave.php';
include_once 'objects/leave.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;

        $query = "update apply_for_petty
                SET
      
                `updated_at` = now(),
                `info_account` =  :info_account
              
                where id = :id ";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':info_account', $new_info_account);
        
        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        $db->commit();

        $crud = "MD Send To Releaser";
        // Send Mail
        SendNotifyMail($id, $crud);

        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}

function SendNotifyMail($id, $action)
{
    $requestor = "";
    $requestor_email = "";
    $request_no = "";
    $applicant = "";
    $department = "";
    $application_Time = "";
    $project_name = "";
    $project_name1 = "";
    $date_request = "";
    $total_amount = "";
    $reject_reason = "";

    $date_release = "";
    $date_liquidate = "";
    $liquidate_amount = "";
    $remarks = "";

    $info_account = "";

    $notifior = array();

    $database = new Database();
    $db = $database->getConnection();

    $_record = GetPettyDetail($id, $db);
    $requestor = $_record[0]["username"];
    $requestor_email = $_record[0]["email"];
    $request_no = $_record[0]["request_no"];
    $applicant = $_record[0]["username"];
    $department = $_record[0]["department"];
    $application_Time = str_replace("-", "/", $_record[0]["created_at"]);
    $project_name = $_record[0]["project_name"];
    $project_name1 = $_record[0]["project_name1"];
    $date_request = $_record[0]["date_requested"];
    $total_amount = $_record[0]["total"];

    $liquidate_amount = $_record[0]["amount_liquidated"];
    $remarks = $_record[0]["remark_liquidated"];

    $info_account = $_record[0]["info_account"];

    switch ($action) {
        case "Checking Reject":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "Send To OP":
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Send To OP ONLY":
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Send To MD":
            $notifior = GetNotifyer(3, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "OP Send To MD":
            $notifior = GetNotifyer(3, $db);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "OP Review Reject To User":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 

            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "OP Review Reject To Checker":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                reject_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            }
            break;
        case "MD Review Reject To User":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db);
            reject_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        case "MD Review Reject To Checker":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 
            $notifior = GetNotifyer(2, $db);
            foreach($notifior as &$list)
            {
                reject_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            }
            break;
        case "MD Send To Releaser":
            $notifior = GetReleasers($db, $info_account);
            foreach($notifior as &$list)
            {
                send_expense_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action);
            }
            break;
        case "Releasing":
            $location = 6;
            break;
        case "Liquidated":
            $date_release = GetReleaseHistory($id, $db);
            $date_liquidate = GetLiquidateHistory($id, $db);

            $notifior = GetNotifyer(7, $db);
            foreach($notifior as &$list)
            {
                send_liquidate_mail($request_no,  $applicant, $list["username"], $list["email"], $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $action, $date_release, $date_liquidate, $liquidate_amount, $remarks);
            }
        case "Finish Releasing":
            $location = 9;
            break;
        case "Verifier Rejected":
            $location = 7;
            break;
        case "Verifier Verified":
            $location = 9;
            break;
        case "Void":
            $reject_reason = GetRejectReason($id, GetDesc($action), $db); 

            void_expense_mail($request_no, $applicant, $requestor, $requestor_email, $department, $application_Time, $project_name1, $project_name, $date_request, $total_amount, $reject_reason, $action);
            break;
        default:
            return;
            break;
        }
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

function GetLiquidateHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Liquidated' order by created_at desc limit 1";

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
    $sql = "SELECT request_no, project_name1, project_name, u.username, u.email, ud.department, ap.created_at, ap.date_requested, 
            (SELECT SUM(price * qty) FROM petty_list WHERE petty_id = :id1) total, ap.amount_liquidated, ap.remark_liquidated, ap.info_account
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

function GetRejectReason($_id, $action, $db)
{
    $sql = "select reason from petty_history pm 
            where `status` <> -1 and petty_id = :id and `action` = :action order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $_id);
    $stmt->bindParam(':action',  $action);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['reason'];
    }

    return $merged_results;
}

function GetNotifyer($action, $db)
{
    $sql = "SELECT username, email FROM expense_flow ap
    LEFT JOIN user u ON ap.uid = u.id 
    WHERE flow in (:action)";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':action',  $action);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetReleasers($db, $info_account)
{
    $flow = 0;

    if($info_account == 'Office Petty Cash')
        $flow = 4;
    if($info_account == 'Online Transactions')
        $flow = 5;
    if($info_account == 'Security Bank')
        $flow = 6;
    $sql = "SELECT username, email FROM expense_flow ap
    LEFT JOIN user u ON ap.uid = u.id 
    WHERE flow = " . $flow;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function &GetAction($loc)
{
    $location = "";
    switch ($loc) {
        case "Revise":
            $location = 1;
            break;
        case "Withdraw":
            $location = -1;
            break;
        case "Checking Reject":
            $location = 0;
            break;
        case "Send To OP":
            $location = 3;
            break;
        case "Send To OP ONLY":
            $location = -3;
            break;
        case "OP Send To MD":
            $location = 4;
            break;
        case "Send To MD":
            $location = 4;
            break;
        case "OP Review Reject To User":
            $location = 0;
            break;
        case "OP Review Reject To Checker":
            $location = 2;
            break;
        case "MD Review Reject To User":
            $location = 0;
            break;
        case "MD Review Reject To Checker":
            $location = 2;
            break;
        case "MD Send To Releaser":
            $location = 5;
            break;
        case "Releasing":
            $location = 6;
            break;
        case "Liquidated":
            $location = 8;
            break;
        case "Finish Releasing":
            $location = 9;
            break;
        case "Verifier Rejected":
            $location = 7;
            break;
        case "Verifier Verified":
            $location = 9;
            break;
        case "Void":
            $location = -2;
            break;
    }

    return $location;
}

function &GetDesc($loc)
{
    $location = $loc;
    switch ($loc) {
        case "Withdraw":
            $location = "Withdrew";
            break;
        case "Checking Reject":
            $location = "Checker Rejected";
            break;
        case "Send To OP":
            $location = "Checker Checked";
            break;
        case "Send To OP ONLY":
            $location = "Checker Checked";
            break;
        case "Send To MD":
            $location = "Checker Checked";
            break;
        case "OP Send To MD":
            $location = "OP Approved";
            break;
        case "OP Review Reject To User":
            $location = "OP Rejected";
            break;
        case "OP Review Reject To Checker":
            $location = "OP Rejected";
            break;
        case "MD Review Reject To User":
            $location = "MD Rejected";
            break;
        case "MD Review Reject To Checker":
            $location = "MD Rejected";
            break;
        case "MD Send To Releaser":
            $location = "MD Approved";
            break;
        case "Releasing":
            $location = "Releaser Released";
            break;
        case "Void":
            $location = "Releaser Voided";
            break;
        case "Finish Releasing":
            $location = "Releaser Released";
            break;
    }

    return $location;
}

function GetRecordDetail($_id, $db)
{
    $sql = "SELECT ap.id, request_no, project_name1, project_name, info_account, info_category, info_sub_category, u.username, amount_verified
            FROM apply_for_petty ap 
            LEFT JOIN user u ON ap.uid = u.id 
            where ap.id = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $_id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetReleaseAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Releaser Released'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetLiquidateAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'Liquidated'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetAttachments($_id, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'petty'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetList($_id, $db)
{
    $sql = "select pm.id, sn, payee, particulars, price, qty, `status`
    from petty_list pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}
