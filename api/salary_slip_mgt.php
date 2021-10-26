<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_GET['jwt']) ?  $_GET['jwt'] : '');
$kw = (isset($_GET['kw']) ?  $_GET['kw'] : '');
$sdate = (isset($_GET['sdate']) ?  $_GET['sdate'] : '');
$edate = (isset($_GET['edate']) ?  $_GET['edate'] : '');
$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$kw = urldecode($kw);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    $merged_results = array();
    $return_result = array();

    $query = "SELECT pt.id, pt.uid, su.username, pt.start_date, pt.end_date, pt.remark, ud.department, pt.status `status`, ut.title, u.username created_name, COALESCE(pt.created_at, '') created_at, u1.username updated_name, COALESCE(pt.updated_at, '') updated_at, user_complete_at, manager_complete_at, pt.salary salary_then, pt.title title_then, pt.department department_then
                    FROM salary_slip_mgt pt
                    LEFT JOIN user su ON su.id = pt.uid
                    LEFT JOIN user_title ut ON ut.id = su.title_id
                    LEFT JOIN user_department ud ON ud.id = su.apartment_id
                    LEFT JOIN user u ON u.id = pt.create_id
                    LEFT JOIN user u1 ON u1.id = pt.updated_id
                    WHERE pt.status <> -1 " . ($id != 0 ? " and pt.title_id=$id" : ' ');

    if($sdate != '') {
        $query = $query . " and pt.start_date > '" . date("Y-m-d",strtotime($sdate . "-01 last day of -1 month")) . "' ";
    }

    if($edate != '') {
        $query = $query . " and pt.end_date < '" . date("Y-m-d",strtotime($edate . "-01 first day of 1 month")) . "' ";
    }

    $query = $query . " order by pt.created_at desc ";

    if (!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (false === $page) {
            $page = 1;
        }
    }

    if (!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if (false === $size) {
            $size = 10;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $uid = $row['uid'];
        $username = $row['username'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $detail_plus = GetDetail($row['id'], 1, $db);
        $detail_minus = GetDetail($row['id'], 2, $db);
        $other = GetOther($row['id'], 1, $db);
        $remark = $row['remark'];
        $status = $row['status'];
        $department = $row['department'];
        $title = $row['title'];
        $created_name = $row['created_name'];
        $created_at = $row['created_at'];
        $updated_name = $row['updated_name'];
        $updated_at = $row['updated_at'];
        $user_complete_at = $row['user_complete_at'];
        $manager_complete_at = $row['manager_complete_at'];

        $salary_then = $row['salary_then'];
        $title_then = $row['title_then'];
        $department_then = $row['department_then'];

        $status_remark = GetStatus($status);
       
        $merged_results[] = array(
            "id" => $id,
            "uid" => $uid,
            "username" => $username,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "detail_plus" => $detail_plus,
            "detail_minus" => $detail_minus,
            "other" => $other,
            "remark" => $remark,
            "status" => $status,
            "status_remark" => $status_remark,
            "department" => $department,
            "title" => $title,
            "created_name" => $created_name,
            "created_at" => $created_at,
            "updated_name" => $updated_name,
            "updated_at" => $updated_at,
            "user_complete_at" => $user_complete_at,
            "manager_complete_at" => $manager_complete_at,
            "salary_then" => $salary_then,
            "title_then" => $title_then,
            "department_then" => $department_then,
        );
    }

    if ($kw != "") {
        foreach ($merged_results as &$value) {
            if (
                preg_match("/{$kw}/i", $value['status_remark']) ||
                preg_match("/{$kw}/i", $value['username']) ||
                preg_match("/{$kw}/i", $value['title_then']) ||
                preg_match("/{$kw}/i", $value['department_then']) ||
                preg_match("/{$kw}/i", $value['start_date']) ||
                preg_match("/{$kw}/i", $value['end_date']) ||
                $kw == ($value['start_date'] != "" ? substr($value['start_date'], 0, 10) : "") ||
                $kw == ($value['updated_at'] != "" ? substr($value['updated_at'], 0, 10) : "")
            ) {
                $return_result[] = $value;
            }
        }
    } else
        $return_result = $merged_results;


    echo json_encode($return_result, JSON_UNESCAPED_SLASHES);
}


function GetDetail($tid, $type, $db){
    $query = "
        SELECT pm.id,
            pm.`order`,
            pm.`cust`,
            pm.category,
            pm.remark,
            pm.amount,
            pm.status          
        FROM   salary_slip_mgt_detail pm
        WHERE  salary_slip_id = " . $tid . "
            AND pm.`type` = " . $type . "
            AND pm.`status` <> -1 
        ORDER BY `order`
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $order = $row['order'];
        $cust = $row['cust'];
        $category = $row['category'];
        $remark = $row['remark'];
        $amount = $row['amount'];
        $status = $row['status'];

        $type = 0;
        if($cust == 0)
            $type = 1;

        $merged_results[] = array(
            "id" => $id,
            "order" => $order,
            "type" => $type,
            "category" => $category,
            "remark" => $remark,
            "amount" => $amount,
            "status" => $status,
          
        );
    }

    return $merged_results;
}

function GetStatus($status) {
    $remark = "";
    switch($status)
    {
        case 0: 
            $remark = "For Confirm";
            break;
        case 1: 
            $remark = "Confirmed";
            break;
        case 2: 
            $remark = "Rejected";
            break;
        case 3: 
            $remark = "Withdrawn";
            break;
        case -1: 
            $remark = "Deleted";
            break;
    }

    return $remark;
}

function GetOther($tid, $type, $db){
    $query = "
        SELECT pm.id,
            pm.`type`,
            pm.`order`,
            pm.category,
            pm.remark,
            pm.previous,
            pm.payment,
            pm.status          
        FROM   salary_slip_mgt_other pm
        WHERE  salary_slip_id = " . $tid . "
            AND pm.`status` <> -1 
        ORDER BY `order`
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $order = $row['order'];
        $category = $row['category'];
     
        $payment = $row['payment'];
        $previous = $row['previous'];

        $remark = number_format($previous - $payment, 2);

        $status = $row['status'];

        $merged_results[] = array(
            "id" => $id,
            "order" => $order,
            "category" => $category,
            "remark" => $remark,
            "payment" => $payment,
            "previous" => $previous,
            "status" => $status,
          
        );
    }

    return $merged_results;
}