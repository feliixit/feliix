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

switch ($method) {
    case 'GET':
 
        $page = (isset($_GET['page']) ?  $_GET['page'] : "");
        $size = (isset($_GET['size']) ?  $_GET['size'] : "");

        /*
        // check if can see petty expense list (Record only for himself)
        $sql = "select * from expense_flow where uid = " . $user_id . " AND `status` <> -1 and flow in (7)";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $arry_apartment_id = [];
        $array_flow = [];
        */
        $merged_results = array();
        /*
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $apartment_id = $row['apartment_id'];
            $flow = $row['flow'];
            
            array_push($arry_apartment_id, $apartment_id);
            array_push($array_flow, $flow);
        }
        
        if(sizeof($array_flow) == 0)
        {
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            die();
        }
        

        $apartment_id_str = implode (", ", $arry_apartment_id);
        */

        $sql = "SELECT  pm.id,
                        request_no, 
                        DATE_FORMAT(pm.date_requested, '%Y/%m/%d') date_requested,
                        p.username requestor,
                        request_type,
                        project_name,
                        project_name1,
                        u.username payable_to,
                        payable_other,
                        remark,
                        pm.`status` ,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at,
                        info_account,
                        info_category,
                        info_sub_category,
                        info_remark,
                        info_remark_other,
                        amount_liquidated,
                        remark_liquidated,
                        total_amount_liquidate,
                        amount_of_return,
                        method_of_return,
                        pm.rtype,
                        pm.dept_name
                from apply_for_petty pm 
                LEFT JOIN user u ON u.id = pm.payable_to 
                LEFT JOIN user p ON p.id = pm.uid 
                where pm.uid = " . $user_id . " 
                AND pm.`status` in (6, 7)";

 
        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        $sql = $sql . " ORDER BY pm.id ";

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
        $request_type = "";
        $project_name = "";
        $project_name1 = "";
        $payable_to = "";
        $payable_other = "";
        $remark = "";
        $status = 0;
        $desc = "";

        $requestor = "";
        $created_at = "";

        $info_account = "";
        $info_category = "";
        $info_sub_category = "";
        $info_remark = "";
        $info_remark_other = "";
       
        $history = [];
        $list = [];
        $items = [];

        $release_date = "";
        $release_items = [];
        $liquidate_date = "";
        $liquidate_items = [];

        $total_amount_liquidate = "";
        $amount_of_return = "";
        $method_of_return = "";
        $apply_for_petty_liquidate = [];

        $amount_liquidated = 0;
        $remark_liquidated = "";

        $rtype = "";
        $dept_name = "";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $request_no = $row['request_no'];
            $date_requested = $row['date_requested'];
            $request_type = GetPettyType($row['request_type']);
            $requestor = $row['requestor'];
            $project_name = $row['project_name'];
            $project_name1 = $row['project_name1'];
            $payable_to = $row['payable_to'];
            $payable_other = $row['payable_other'];
            $remark = $row['remark'];
            $status = $row['status'];
            $desc = GetStatus($row['status']);
            $items = GetAttachment($row['id'], $db);

            $release_date = GetReleaseHistory($row['id'], $db);
            $release_items = GetReleaseAttachment($row['id'], $db);
            $liquidate_date = GetLiquidateHistory($row['id'], $db);
            $liquidate_items = GetLiquidateAttachment($row['id'], $db);

            $amount_liquidated = $row['amount_liquidated'];
            $remark_liquidated = $row['remark_liquidated'];

            $apply_for_petty_liquidate = GetAmountPettyLiquidate($row['id'], $db);

            $total_amount_liquidate = $row['total_amount_liquidate'];
            $amount_of_return = $row['amount_of_return'];
            $method_of_return = $row['method_of_return'];

            $history = GetHistory($row['id'], $db);
            $list = GetList($row['id'], $db);
            $created_at = $row['created_at'];

            $combine_liquidate = [];
            if($amount_liquidated == null)
            {
                //$total_amount_liquidate = 0;
                foreach ($list as &$value) {
                    $obj = array(
                        "id" => $value['id'],
                        "sn" => $value['sn'],
                        "vendor" => $value['payee'],
                        "particulars" => $value['particulars'],
                        "price" => $value['price'],
                        "qty" => $value['qty'],
                        "status" => $value['status']
                    );

                    //$total_amount_liquidate += $value['price'] * $value['qty'];
                    $combine_liquidate[] = $obj;
                }
            }
            else
            {
                $combine_liquidate = $apply_for_petty_liquidate;
            }

            $info_account = $row['info_account'];
            $info_category = $row['info_category'];
            $info_sub_category = $row['info_sub_category'];
            $info_remark = $row['info_remark'];
            $info_remark_other = $row['info_remark_other'];

            $total = 0;
            foreach ($list as &$value) {
                $total += $value['price'] * $value['qty'];
            }

            $rtype = $row['rtype'];
            $dept_name = $row['dept_name'];
            $department = GetDepartment($row['dept_name']);

            $merged_results[] = array(
                "id" => $id,
                "request_no" => $request_no,
                "date_requested" => $date_requested,
                "request_type" => $request_type,
                "requestor" => $requestor,
                "project_name" => $project_name,
                "project_name1" => $project_name1,
                "payable_to" => $payable_to,
                "payable_other" => $payable_other,
                "remark" => $remark,
                "status" => $status,
                "desc" => $desc,
                "items" => $items,
                "history" => $history,
                "list" => $list,
                "total" => $total,
                "created_at" => $created_at,

                "liquidate_date" => $liquidate_date,
                "liquidate_items" => $liquidate_items,
                "amount_liquidated" => $amount_liquidated,
                "remark_liquidated" => $remark_liquidated,

                "total_amount_liquidate" => $total_amount_liquidate,
                "amount_of_return" => $amount_of_return,
                "method_of_return" => $method_of_return,
                "apply_for_petty_liquidate" => $combine_liquidate,

                "release_date" => $release_date,
                "release_items" => $release_items,

                "info_account" => $info_account,
                "info_category" => $info_category,
                "sub_category" => $info_sub_category,
                "info_remark" => $info_remark,
                "info_remark_other" => $info_remark_other,

                "rtype" => $rtype,
                "dept_name" => $dept_name,
                "department" => $department,
            );

        }

        
        
        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function GetAttachment($_id, $db)
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

function GetReleaseAttachment($_id, $db)
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

function GetLiquidateAttachment($_id, $db)
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
        case -2:
            $location = "Void";
            break;
        case -1:
            $location = "Withdrawn";
            break;
        case 0:
            $location = "Rejected";
            break;
        case 1:
            $location = "For Check";
            break;
        case 2:
            $location = "For Check";
            break;
        case 3:
            $location = "For Approve";
            break;
        case 4:
            $location = "For Approve";
            break;
        case 5:
            $location = "For Release";
            break;
        case 6:
            $location = "For Liquidate";
            break;
        case 7:
            $location = "For Liquidate";
            break;
        case 8:
            $location = "For Verify";
            break;
        case 9:
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
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " order by created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
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

function GetAmountPettyLiquidate($_id, $db)
{
    $sql = "select pm.id, sn, vendor payee, particulars, price, qty, `status`
    from apply_for_petty_liquidate pm 
    where `status` <> -1 and petty_id = " . $_id . " order by sn ";

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
