<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $username = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';


$database = new Database();
$db = $database->getConnection();

const APPROVE = 4;
const RELEASE = 5;

switch ($method) {
    case 'GET':
 
        $page = (isset($_GET['page']) ?  $_GET['page'] : "");
        $size = (isset($_GET['size']) ?  $_GET['size'] : "");

        // check if can see petty expense list (Record only for himself)
        $sql = "SELECT * FROM access_control WHERE office_item_release LIKE '%" . $username . "%' ";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $arry_apartment_id = [];
        $array_flow = [];

        $merged_results = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $flow = RELEASE;
            array_push($array_flow, $flow);
        }
        
        if(sizeof($array_flow) == 0)
        {
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            die();
        }

        //$apartment_id_str = implode (", ", $arry_apartment_id);

        $sql = "SELECT  pm.id,
                        pm.uid,
                        request_no, 
                        DATE_FORMAT(pm.date_requested, '%Y/%m/%d') date_requested,
                        reason,
                        listing,
                        remarks,
                        pm.`status`,
                        p.username,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at
                     
                from apply_for_office_item pm 
                LEFT JOIN user p ON p.id = pm.uid ";
        
        $status_str = "";
        foreach($array_flow as &$list)
        {
            $status_str .= $list . ",";
        }

        $sql = $sql . "
                where pm.`status` in (" . rtrim($status_str, ",") . ")";

 
        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        $sql = $sql . " ORDER BY pm.date_requested ";

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $sql = $sql . " LIMIT " . $offset . "," . $size;
        }

        

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $id = 0;
        $request_no = "";
        $date_requested = "";
        $reason = "";
        $listing = "";
        $remarks = "";
        $status = "";
        $requestor = "";
        $created_at = "";
        
        $list = [];
        $attachment = [];
    

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $request_no = $row['request_no'];
            $date_requested = $row['date_requested'];
            $reason = $row['reason'];
            $listing = $row['listing'];
            $remarks = $row['remarks'];
            $status = $row['status'];
            $requestor = $row['username'];
            $created_at = $row['created_at'];
            
            $desc = GetStatus($row['status']);
            $attachment = GetAttachment($id, $db);
            $history = GetHistory($id, $db);

            $list = JSON_decode($row['listing'], true);
            $list = UpdateQty($list, $db);

            $merged_results[] = array(
                "id" => $id,
                "request_no" => $request_no,
                "date_requested" => $date_requested,
                "reason" => $reason,
                "listing" => $listing,
                "remarks" => $remarks,
                "status" => $status,
                "desc" => $desc,
                "attachment" => $attachment,
                "history" => $history,
                "requestor" => $requestor,
                "created_at" => $created_at,
                "list" => $list
            );

        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function GetAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'apply_office_item'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function UpdateQty($list, $db)
{
    foreach($list as &$item)
    {
        $code = $item['code1'] . $item['code2'] . $item['code3'] . $item['code4'];

        $sql = "select qty, reserve_qty from office_items_stock where code = '" . $code . "'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $qty = $row['qty'];
            $reserve_qty = $row['reserve_qty'];

            $item['qty'] = $qty;
            $item['reserve_qty'] = $reserve_qty;
        }
    }

    return $list;
}

function GetUserInfo($users, $db)
{
    $sql = "SELECT id, username, pic_url FROM user WHERE id IN (" . $users . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetPriority($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "No Priority";
            break;
        case "2":
            $location = "Low";
            break;
        case "3":
            $location = "Normal";
            break;
        case "4":
            $location = "High";
            break;
        case "5":
            $location = "Urgent";
            break;
        
    }

    return $location;
}

function GetPettyType($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "New";
            break;
        case "2":
            $location = "Reimbursement";
            break;
        case "3":
            $location = "Petty Cash Replenishment";
            break;
        default:
            $location = "";
            break;
    }

    return $location;
}

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case 3:
            $location = "Void";
            break;
        case -1:
            $location = "Withdrawn";
            break;
        case 2:
            $location = "Rejected";
            break;
        case 4:
            $location = "For Approve";
            break;
        case 5:
            $location = "For Release";
            break;
        case 6:
            $location = "Completed";
            break;
    }

    return $location;
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

function GetHistory($_id, $db)
{
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from office_item_apply_history pm 
            where `status` <> -1 and request_id = " . $_id . " order by created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetDepartment($dept_name)
{
    $department = "";

    if($dept_name == 'admin')
        $department = 'Admin Department';

    if($dept_name == 'design')
        $department = 'Design Department';

    if($dept_name == 'engineering')
        $department = 'Engineering Department';

    if($dept_name == 'lighting')
        $department = 'Lighting Department';
    
    if($dept_name == 'office')
        $department = 'Office Department';
    
    if($dept_name == 'sales')
        $department = 'Sales Department';

    return $department;
}