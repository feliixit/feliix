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
  
        $id = (isset($_GET['id']) ?  $_GET['id'] : '');
        $fru = (isset($_GET['fru']) ?  $_GET['fru'] : '');
        $frl = (isset($_GET['frl']) ?  $_GET['frl'] : '');
        $fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');

        $fc = urldecode($fc);

        $fp = (isset($_GET['fp']) ? $_GET['fp'] : '');
        $fp = urldecode($fp);

        $ft = (isset($_GET['ft']) ?  $_GET['ft'] : '');
        $fs = (isset($_GET['fs']) ?  $_GET['fs'] : '');
        $fat = (isset($_GET['fat']) ?  $_GET['fat'] : '');
        $fau = (isset($_GET['fau']) ?  $_GET['fau'] : '');
        $fal = (isset($_GET['fal']) ?  $_GET['fal'] : '');

        $ftd = (isset($_GET['ftd']) ?  $_GET['ftd'] : '');
        $fds = (isset($_GET['fds']) ?  $_GET['fds'] : '');
        $fds = str_replace('-', '/', $fds);
        $fde = (isset($_GET['fde']) ?  $_GET['fde'] : '');
        $fde = str_replace('-', '/', $fde);

        $of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
        $ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
        $of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
        $ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

        $page = (isset($_GET['page']) ?  $_GET['page'] : 1);
        $size = (isset($_GET['size']) ?  $_GET['size'] : 10);

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
                from apply_for_office_item pm 
                LEFT JOIN user p ON p.id = pm.uid 
                where 1=1 ";

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
                LEFT JOIN user p ON p.id = pm.uid 
                where 1=1 ";

if($id != "" && $id != "0")
{
    $sql = $sql . " and pm.id = '" . $id . "' ";
    $query_cnt = $query_cnt . " and pm.id = '" . $id . "' ";
}

if($ft != "" && $ft != "0")
{
    $sql = $sql . " and pm.request_type = '" . $ft . "' ";
    $query_cnt = $query_cnt . " and pm.request_type = '" . $ft . "' ";
}

if($frl != "")
{
    $sql = $sql . " and pm.request_no >= '" . sprintf('%05d', $frl) . "' ";
    $query_cnt = $query_cnt . " and pm.request_no >= '" . sprintf('%05d', $frl) . "' ";
}

if($fru != "")
{
    $sql = $sql . " and pm.request_no <= '" . sprintf('%05d', $fru) . "' ";
    $query_cnt = $query_cnt . " and pm.request_no <= '" . sprintf('%05d', $fru) . "' ";
}

if($fc != "")
{
    $sql = $sql . " and p.username = '" . $fc . "' ";
    $query_cnt = $query_cnt . " and p.username = '" . $fc . "' ";
}

if($fp != "")
{
    $sql = $sql . " and pm.project_name1 = '" . $fp . "' ";
    $query_cnt = $query_cnt . " and pm.project_name1 = '" . $fp . "' ";
}

$status_array = [];


if($fs != "" && $fs != "0")
{
    if(strpos($fs,"-1") > -1)
        $status_array = array_merge($status_array, [-1]);
    if(strpos($fs,"2") > -1)
        $status_array = array_merge($status_array, [2]);
    if(strpos($fs,"3") > -1)
        $status_array = array_merge($status_array, [3]);
    if(strpos($fs,"4") > -1)
        $status_array = array_merge($status_array, [4]);
    if(strpos($fs,"5") > -1)
        $status_array = array_merge($status_array, [5]);
    if(strpos($fs,"6") > -1)
        $status_array = array_merge($status_array, [6]);
}

if(count($status_array) > 0)
{
    $sql = $sql . " and pm.`status` in (" . implode(",", $status_array) . ") ";
    $query_cnt = $query_cnt . " and pm.`status` in (" . implode(",", $status_array) . ") ";
}

// if($fs != "" && $fs != "0")
// {
//     if(strpos($fs,"1") > -1)
//         $sql = $sql . " and pm.`status` in (1, 2) ";
//     if(strpos($fs,"2") > -1)
//         $sql = $sql . " and pm.`status` in (3, 4) ";
//     if(strpos($fs,"3") > -1)
//         $sql = $sql . " and pm.`status` in (5) ";
//     if(strpos($fs,"4") > -1)
//         $sql = $sql . " and pm.`status` in (6, 7) ";
//     if(strpos($fs,"5") > -1)
//         $sql = $sql . " and pm.`status` in (8) ";
//     if(strpos($fs,"6") > -1)
//         $sql = $sql . " and pm.`status` in (9) ";
//     if(strpos($fs,"7") > -1)
//         $sql = $sql . " and pm.`status` in (0) ";
//     if(strpos($fs,"8") > -1)
//         $sql = $sql . " and pm.`status` in (-1) ";

//     if(strpos($fs,"1") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (1, 2) ";
//     if(strpos($fs,"2") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (3, 4) ";
//     if(strpos($fs,"3") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (5) ";
//     if(strpos($fs,"4") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (6, 7) ";
//     if(strpos($fs,"5") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (8) ";
//     if(strpos($fs,"6") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (9) ";
//     if(strpos($fs,"7") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (0) ";
//     if(strpos($fs,"8") > -1)
//         $query_cnt = $query_cnt . " and pm.`status` in (-1) ";
// }

if($fat == "1" && $fau != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) <= " . $fau . " ";
    $query_cnt = $query_cnt . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) <= " . $fau . " ";
}

if($fat == "1" && $fal != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) >= " . $fal . " ";
    $query_cnt = $query_cnt . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) >= " . $fal . " ";
}

if($fat == "2" && $fau != "")
{
    $sql = $sql . " and pm.amount_verified <= " . $fau . " ";
    $query_cnt = $query_cnt . " and pm.amount_verified <= " . $fau . " ";
}

if($fat == "2" && $fal != "")
{
    $sql = $sql . " and pm.amount_verified >= " . $fal . " ";
    $query_cnt = $query_cnt . " and pm.amount_verified >= " . $fal . " ";
}

if($ftd == "1" && $fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($ftd == "1" && $fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') <= '" . $fde . "' ";
}

if($ftd == "2" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "2" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "3" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "3" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "4" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "4" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "5" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "5" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "6" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "6" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "7" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "7" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "8" && $fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($ftd == "8" && $fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
    $query_cnt = $query_cnt . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
}


$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {
        case 1:
            if($ofd1 == 2)
                $sOrder = "pm.request_no desc";
            else
                $sOrder = "pm.request_no ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "p.username desc ";
            else
                $sOrder = "p.username ";
            break;  
        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
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
                $sOrder .= ", pm.request_no desc";
            else
                $sOrder .= ", pm.request_no ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = ", p.username desc ";
            else
                $sOrder = ", p.username ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
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
                $sOrder = "pm.request_no desc";
            else
                $sOrder = "pm.request_no ";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "p.username desc ";
            else
                $sOrder = "p.username ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Approver Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from office_item_apply_history ph where ph.`status` <> -1 and ph.request_id = pm.id and ph.`action` = 'Releaser released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        default:
    }
}


if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " order by pm.request_no desc ";


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
        $release_items = [];
    

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

            $history_desc = GetHistoryDesc($id, $db);

            $date_release = "";
            $date_approved = "";

            foreach($history_desc as $h)
            {
                if($h['action'] == 'Releaser released')
                {
                    $date_release = $h['created_at'];
                    break;
                }
            }

            foreach($history_desc as $h)
            {
                if($h['action'] == 'Approver Approved')
                {
                    $date_approved = $h['created_at'];
                    break;
                }
            }

            $release_items = GetReleaseAttachment($row['id'], $db);
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
                "date_approved" => $date_approved,
                "date_release" => $date_release,
                "release_items" => $release_items,
                "requestor" => $requestor,
                "created_at" => $created_at,
                "list" => $list,
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
