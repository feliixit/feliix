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
  
        $fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');

        $fap = (isset($_GET['fap']) ?  $_GET['fap'] : '');
        $fap = urldecode($fap);

        $fp = (isset($_GET['fp']) ? $_GET['fp'] : '');
        $fp = urldecode($fp);

        $fk = (isset($_GET['fk']) ?  $_GET['fk'] : '');
        $fk = urldecode($fk);

        $fds = (isset($_GET['fds']) ?  $_GET['fds'] : '');
        $fds = str_replace('-', '/', $fds);
        $fde = (isset($_GET['fde']) ?  $_GET['fde'] : '');
        $fde = str_replace('-', '/', $fde);

        $of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
        $ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
        $of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
        $ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

        $page = (isset($_GET['page']) ?  $_GET['page'] : 1);
        $size = (isset($_GET['size']) ?  $_GET['size'] : 20);

        $lv1 = (isset($_GET['lv1']) ?  $_GET['lv1'] : '');
        $lv2 = (isset($_GET['lv2']) ?  $_GET['lv2'] : '');
        $lv3 = (isset($_GET['lv3']) ?  $_GET['lv3'] : '');
        $lv4 = (isset($_GET['lv4']) ?  $_GET['lv4'] : '');

        // check if can see petty expense list (Record only for himself)
        /*
        $sql = "select * from expense_flow where uid = " . $user_id . " where status <> -1";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $arry_apartment_id = [];
        $array_flow = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $apartment_id = $row['apartment_id'];
            $flow = $row['flow'];
            
            array_push($arry_apartment_id, $apartment_id);
            array_push($array_flow, $flow);
        }
        */

        $query_cnt = "SELECT  count(*) cnt
                from office_stock_history pm 
                LEFT JOIN user p ON p.id = pm.create_id 
                where pm.`status` <> -1   ";

        $sql = "SELECT pm.id,
                        pm.request_id, 
                        pm.code,
                        pm.qty,
                        pm.`action`,
                        pm.act_1,
                        pm.act_2,
                        pm.qty_before,
                        pm.qty_after,
                        pm.`status`,
                        p.username created_by,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %H:%i') created_at,
                        m.code code1, m.category cat1, 
                		s.code code2, s.category cat2, 
                		b.code code3, b.category cat3, 
                		d.code code4, d.category cat4, 
                		d.photo
                from office_stock_history pm 
                LEFT JOIN user p ON p.id = pm.create_id 
                left join office_items_main_category m on m.code = SUBSTRING(pm.code, 1, 2) and m.status <> -1
        		left join office_items_sub_category s on s.parent_code = SUBSTRING(pm.code, 1, 2) and s.code = SUBSTRING(pm.code, 3, 2) and s.status <> -1
        		left join office_items_brand b on b.parent_code =  SUBSTRING(pm.code, 1, 4) and b.code = SUBSTRING(pm.code, 5, 2) and b.status <> -1
        		left join office_items_description d on d.parent_code = SUBSTRING(pm.code, 1, 6) and d.code = SUBSTRING(pm.code, 7, 2) and d.status <> -1
                where pm.`status` <> -1  ";


if($lv1 != "" || $lv2 != "" || $lv3 != "" || $lv4 != "")
{
    $sql = $sql . " and pm.code like '" . $lv1 . $lv2 .$lv3 . $lv4 . "%' ";
    $query_cnt = $query_cnt . " and pm.code like '"  . $lv1 . $lv2 .$lv3 . $lv4 .  "%' ";
}

if($fk != "")
{
    $sql = $sql . " and pm.code like '" . $fk . "%' ";
    $query_cnt = $query_cnt . " and pm.code like '" . $fk . "%' ";
}

if($fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
}

if($fap != "")
{
    $sql = $sql . " and p.username = '" . $fap . "' ";
    $query_cnt = $query_cnt . " and p.username = '" . $fap . "' ";
}

// nothing selected
if($lv1 == "" && $lv2 == "" && $lv3 == "" && $lv4 == ""  && $fk == "" && $fds == "" && $fde == "" && $fap == "" && $fs == "")
{
    $sql = $sql . " and pm.code like '______' ";
    $query_cnt = $query_cnt . " and pm.code like '______' ";
}

$status_array = [];

if($fs != "" && $fs != "0")
{
    if(strpos($fs,"1") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'OIA-%'"]);
    if(strpos($fs,"2") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IC-%'"]);
    if(strpos($fs,"3") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IR-%'"]);
    if(strpos($fs,"4") > -1)
        $status_array = array_merge($status_array, ["pm.act_1 like 'IM-%'"]);
}

if(count($status_array) > 0)
{
    $sql = $sql . " and (" . implode(" or ", $status_array) . ") ";
    $query_cnt = $query_cnt . " and (" . implode(" or ", $status_array) . ") ";
}



$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {
        
        case 1:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "pm.act_1 desc";
            else
                $sOrder = "pm.act_1 ";
            break;  

        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder != "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", pm.act_1 desc";
            else
                $sOrder .= ", pm.act_1 ";
            break;  
      
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder == "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "pm.act_1 desc";
            else
                $sOrder = "pm.act_1 ";
            break;  
       
        default:
    }
}


if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " order by pm.created_at ";


if (!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if (false === $page) {
        $page = 1;
    }
}


if($page == 0)
    $page = 1;

if (!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if (false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $sql = $sql . " LIMIT " . $offset . "," . $size;
}


$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();

while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}


        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();
   

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $request_id = $row['request_id'];
            $code = $row['code'];
            $qty = $row['qty'];
            $action = $row['action'];
            $act_1 = $row['act_1'];
            $act_2 = $row['act_2'];
            $qty_before = $row['qty_before'];
            $qty_after = $row['qty_after'];
            $status = $row['status'];
            $created_by = $row['created_by'];
            $created_at = $row['created_at'];
            $code1 = $row['code1'];
            $code2 = $row['code2'];
            $code3 = $row['code3'];
            $code4 = $row['code4'];
            $cat1 = $row['cat1'];
            $cat2 = $row['cat2'];
            $cat3 = $row['cat3'];
            $cat4 = $row['cat4'];
            $photo = $row['photo'];
            $url = GetUrl($row['act_1'], $row['request_id']);

            $merged_results[] = array(
                "id" => $id,
                "request_id" => $request_id,
                "code" => $code,
                "qty" => $qty,
                "action" => $action,
                "act_1" => $act_1,
                "act_2" => $act_2,
                "qty_before" => $qty_before,
                "qty_after" => $qty_after,
                "status" => $status,
                "created_by" => $created_by,
                "created_at" => $created_at,
                "code1" => $code1,
                "code2" => $code2,
                "code3" => $code3,
                "code4" => $code4,
                "cat1" => $cat1,
                "cat2" => $cat2,
                "cat3" => $cat3,
                "cat4" => $cat4,
                "photo" => $photo,
                "url" => $url,
                "cnt" => $cnt,
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

function GetReleaseAttachment($_id, $db)
{
    $sql = "select COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = 'office_item_release'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetUrl($act_1, $request_id)
{
    $location = "";
    // get "ORA' from "ORA-0101" as $loc
    $loc = substr($act_1, 0, strpos($act_1, "-"));
    switch ($loc) {
        case "OIA":
            $location = "office_item_application_report" . "?id=" . $request_id;
            break;
        case "IR":
            $location = "office_item_inventory_replenish" . "?id=" . $request_id;
            break;
        case "IC":
            $location = "office_item_inventory_check" . "?id=" . $request_id;
            break;
        case "IM":
            $location = "office_item_inventory_modify" . "?id=" . $request_id;
            break;
    }

    return $location;
}

function GetStatus($loc)
{
    $location = "";
    switch ($loc) {
        case 1:
            $location = "PHASE 1: Create Checking List by Checker";
            break;
        case 2:
            $location = "PHASE 2: Inventory Count by Checker";
            break;
        case 3:
            $location = "PHASE 3: Review by Approver";
            break;
        case 4:
            $location = "PHASE 4: Inventory Check Completed";
            break;
    }

    return $location;
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

function GetHistoryDesc($_id, $db)
{
    $sql = "select pm.id, `actor`, `action`, reason, `status`, DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at from office_item_apply_history pm 
            where `status` <> -1 and request_id = " . $_id . " order by created_at desc ";

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

function GetApprove1History($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') dt, `status` from petty_history pm 
            where  `status` <> -1 and petty_id = " . $_id . " and `action` = 'OP Approved' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($row['status'] == -1)
            $merged_results = "";
        else
            $merged_results = $row['dt'];
    }

    return $merged_results;
}

function GetApprove2History($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'MD Approved' order by created_at desc limit 1";

    $merged_results = "";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row['created_at'];
    }

    return $merged_results;
}

function GetCheckedHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Checker Checked' order by created_at desc limit 1";

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

function GetVerifiedHistory($_id, $db)
{
    $sql = "select DATE_FORMAT(pm.created_at, '%Y/%m/%d') created_at from petty_history pm 
            where `status` <> -1 and petty_id = " . $_id . " and `action` = 'Verifier Verified' order by created_at desc limit 1";

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
