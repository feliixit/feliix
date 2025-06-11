<?php
ob_start();
// required headers
 error_reporting(0);
 
 require '../vendor/autoload.php';
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/conf.php';

use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = (isset($_POST['id']) ?  $_POST['id'] : '');
$fru = (isset($_POST['fru']) ?  $_POST['fru'] : '');
$frl = (isset($_POST['frl']) ?  $_POST['frl'] : '');
$fc = (isset($_POST['fc']) ?  $_POST['fc'] : '');

$fc = urldecode($fc);

$ft = (isset($_POST['ft']) ?  $_POST['ft'] : '');
$fs = (isset($_POST['fs']) ?  $_POST['fs'] : '');
$fat = (isset($_POST['fat']) ?  $_POST['fat'] : '');
$fau = (isset($_POST['fau']) ?  $_POST['fau'] : '');
$fal = (isset($_POST['fal']) ?  $_POST['fal'] : '');

$fp = (isset($_POST['fp']) ? $_POST['fp'] : '');
$fp = urldecode($fp);

$ftd = (isset($_POST['ftd']) ?  $_POST['ftd'] : '');
$fds = (isset($_POST['fds']) ?  $_POST['fds'] : '');
$fds = str_replace('-', '/', $fds);
$fde = (isset($_POST['fde']) ?  $_POST['fde'] : '');
$fde = str_replace('-', '/', $fde);

$of1 = (isset($_POST['of1']) ?  $_POST['of1'] : '');
$ofd1 = (isset($_POST['ofd1']) ?  $_POST['ofd1'] : '');
$of2 = (isset($_POST['of2']) ?  $_POST['of2'] : '');
$ofd2 = (isset($_POST['ofd2']) ?  $_POST['ofd2'] : '');

$page = (isset($_POST['page']) ?  $_POST['page'] : 1);
$size = (isset($_POST['size']) ?  $_POST['size'] : 10);

$conf = new Conf();
$apartment_id = 0;

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $apartment_id = $decoded->data->apartment_id;
        // response in json format
            http_response_code(200);

            $merged_results = array();

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
                        info_account,
                        info_category,
                        info_sub_category,
                        info_remark,
                        info_remark_other,
                        pm.`status` ,
                        DATE_FORMAT(pm.created_at, '%Y/%m/%d %T') created_at,
                        pm.amount_liquidated,
                        pm.remark_liquidated,
                        pm.amount_verified,
                        pm.rtype,
                        pm.dept_name
                from apply_for_petty pm 
                LEFT JOIN user u ON u.id = pm.payable_to 
                LEFT JOIN user p ON p.id = pm.uid 
                where 1=1 ";

// Sale = 1, Lighting = 2, Office = 3, Engineering = 5
if($apartment_id == 1 || $apartment_id == 2 || $apartment_id == 3 || $apartment_id == 5)
{
    $sql = $sql . " and p.apartment_id = " . $apartment_id . " ";
}

if($ft != "" && $ft != "0")
{
    $sql = $sql . " and pm.request_type = '" . $ft . "' ";
}

if($frl != "")
{
    $sql = $sql . " and pm.request_no >= '" . sprintf('%05d', $frl) . "' ";
}

if($fru != "")
{
    $sql = $sql . " and pm.request_no <= '" . sprintf('%05d', $fru) . "' ";
}

if($fc != "")
{
    $sql = $sql . " and p.username = '" . $fc . "' ";
}

if($fp != "")
{
    $sql = $sql . " and pm.project_name1 = '" . $fp . "' ";
    $query_cnt = $query_cnt . " and pm.project_name1 = '" . $fp . "' ";
}

$status_array = [];


if($fs != "" && $fs != "0")
{
    if(strpos($fs,"1") > -1)
        $status_array = array_merge($status_array, [1, 2]);
    if(strpos($fs,"2") > -1)
        $status_array = array_merge($status_array, [3, 4]);
    if(strpos($fs,"3") > -1)
        $status_array = array_merge($status_array, [5]);
    if(strpos($fs,"4") > -1)
        $status_array = array_merge($status_array, [6, 7]);
    if(strpos($fs,"5") > -1)
        $status_array = array_merge($status_array, [8]);
    if(strpos($fs,"6") > -1)
        $status_array = array_merge($status_array, [9]);
    if(strpos($fs,"7") > -1)
        $status_array = array_merge($status_array, [0]);
    if(strpos($fs,"8") > -1)
        $status_array = array_merge($status_array, [-1]);
}

if(count($status_array) > 0)
{
    $sql = $sql . " and pm.`status` in (" . implode(",", $status_array) . ") ";
}

if($fat == "1" && $fau != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) <= " . $fau . " ";
}

if($fat == "1" && $fal != "")
{
    $sql = $sql . " and (select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) >= " . $fal . " ";
}

if($fat == "2" && $fau != "")
{
    $sql = $sql . " and pm.amount_verified <= " . $fau . " ";
}

if($fat == "2" && $fal != "")
{
    $sql = $sql . " and pm.amount_verified >= " . $fal . " ";
}

if($ftd == "1" && $fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($ftd == "1" && $fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.date_requested, '%Y/%m/%d') <= '" . $fde . "' ";
}

if($ftd == "2" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "2" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "3" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "3" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "4" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "4" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "5" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "5" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "6" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "6" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "7" && $fds != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) >= '" . $fds . "' ";
}

if($ftd == "7" && $fde != "")
{
    $sql = $sql . " and (select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1) <= '" . $fde . "' ";
}

if($ftd == "8" && $fds != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') >= '" . $fds . "' ";
}

if($ftd == "8" && $fde != "")
{
    $sql = $sql . " and DATE_FORMAT(pm.created_at, '%Y/%m/%d') <= '" . $fde . "' ";
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
                $sOrder = "Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1), 0) desc";
            else
                $sOrder = "Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1) , 99999999)";
            break;  
        case 4:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.amount_verified, 0) desc";
            else
                $sOrder = "Coalesce(pm.amount_verified, 99999999)";
            break;
        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 6:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 7:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 8:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 10:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 11:
            if($ofd1 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
            case 12:
                if($ofd1 == 2)
                    $sOrder = "pm.project_name1 desc ";
                else
                $sOrder = "CONCAT(trim(pm.project_name1), 'ZZZZZZZZZZZZZZZZZZZZ') ";
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
                $sOrder .= ", Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1), 0) desc";
            else
                $sOrder .= ", Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1), 0) desc";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.amount_verified, 0) desc";
            else
                $sOrder .= ", Coalesce(pm.amount_verified, 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 8:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 10:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 11:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
            case 12:
                if($ofd2 == 2)
                    $sOrder .= ", pm.project_name1 desc ";
                else
                    $sOrder .= ", CONCAT(trim(pm.project_name1), 'ZZZZZZZZZZZZZZZZZZZZ') ";
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
                $sOrder = "Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1), 0) desc";
            else
                $sOrder = "Coalesce((select SUM(pl.price * pl.qty) from petty_list pl WHERE pl.petty_id = pm.id AND pl.`status` <> -1), 999999990) ";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.amount_verified, 0) desc";
            else
                $sOrder = "Coalesce(pm.amount_verified, 99999999)";
            break;
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce(pm.date_requested, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.date_requested, '9999-99-99')";
            break;
        case 6:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Checker Checked' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 7:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'OP Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 8:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'MD Approved' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 9:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Releaser Released' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 10:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Liquidated' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
        case 11:
            if($ofd2 == 2)
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '0000-00-00') desc";
            else
                $sOrder = "Coalesce((select DATE_FORMAT(ph.created_at, '%Y/%m/%d')  from petty_history ph where ph.`status` <> -1 and ph.petty_id = pm.id and ph.`action` = 'Verifier Verified' order by ph.created_at desc LIMIT 1), '9999-99-99') ";
            break;
            case 12:
                if($ofd2 == 2)
                    $sOrder = "pm.project_name1 desc";
                else
                    $sOrder = "CONCAT(trim(pm.project_name1), 'ZZZZZZZZZZZZZZZZZZZZ') ";
                break;
        default:
    }
}


if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " order by pm.created_at desc ";

            $stmt = $db->prepare( $sql );
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
        $info_account = "";
        $info_category = "";
        $info_sub_category = "";
        $info_remark = "";
        $info_remark_other = "";
        $status = 0;
        $desc = "";

        $requestor = "";
        $created_at = "";
       
        $history = [];
        $list = [];
        $items = [];

        $release_date = "";
        $release_items = [];
        $liquidate_date = "";
        $liquidate_items = [];

        $amount_liquidated = 0;
        $amount_verified = 0;
        $remark_liquidated = '';

        $verified_date = "";
        $verified_items = [];

        $checked_date = "";
        $approve1_date = "";
        $approve2_date = "";

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
            $info_account = $row['info_account'];
            $info_category = $row['info_category'];
            $info_sub_category = $row['info_sub_category'];
            $info_remark = $row['info_remark'];
            $info_remark_other = $row['info_remark_other'];
            $status = $row['status'];
            $desc = GetStatus($row['status']);

            $history = GetHistory($row['id'], $db);
            $list = GetList($row['id'], $db);
            $created_at = $row['created_at'];

            $release_date = GetReleaseHistory($row['id'], $db);
    
            $liquidate_date = GetLiquidateHistory($row['id'], $db);
       

            $verified_date = GetVerifiedHistory($row['id'], $db);
       

            $amount_liquidated = $row['amount_liquidated'];
            $remark_liquidated = $row['remark_liquidated'];
            $amount_verified = $row['amount_verified'];

            $checked_date = GetCheckedHistory($row['id'], $db);
            $approve1_date = GetApprove1History($row['id'], $db);
            $approve2_date = GetApprove2History($row['id'], $db);

            $rtype = $row['rtype'];
            $dept_name = $row['dept_name'];
            $department = GetDepartment($row['dept_name']);

            $total = 0;
            foreach ($list as &$value) {
                $total += $value['price'] * $value['qty'];
            }

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
                "info_account" => $info_account,
                "info_category" => $info_category,
                "sub_category" => $info_sub_category,
                "info_remark" => $info_remark,
                "info_remark_other" => $info_remark_other,
                "status" => $status,
                "desc" => $desc,
                "items" => $items,
                "history" => $history,
                "list" => $list,
                "total" => $total,
                "created_at" => $created_at,

                "liquidate_date" => $liquidate_date,
                "liquidate_items" => $liquidate_items,
                "release_date" => $release_date,
                "release_items" => $release_items,

                "verified_date" => $verified_date,
                "verified_items" => $verified_items,

                "amount_liquidated" => $amount_liquidated,
                "remark_liquidated" => $remark_liquidated,
                "amount_verified" => $amount_verified,

                "checked_date" => $checked_date,
                "approve1_date" => $approve1_date,
                "approve2_date" => $approve2_date,

                "rtype" => $rtype,
                "dept_name" => $dept_name,
                "department" => $department

            );

        }
          
            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Sheet 1");


            $sheet->setCellValue('A1', 'Request No.');
            $sheet->setCellValue('B1', 'Requestor');
            $sheet->setCellValue('C1', 'Application Time');
            $sheet->setCellValue('D1', 'Type');
            $sheet->setCellValue('E1', 'Status');
            $sheet->setCellValue('F1', 'Project Name');
            $sheet->setCellValue('G1', 'Requested Amount');
            $sheet->setCellValue('H1', 'Actual Amount');
            $sheet->setCellValue('I1', 'Date Needed');
            $sheet->setCellValue('J1', 'Date Checked');
            $sheet->setCellValue('K1', 'Date Approved');
            $sheet->setCellValue('L1', 'Date Released');
            $sheet->setCellValue('M1', 'Date Liquidated');
            $sheet->setCellValue('N1', 'Date Verified');

            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['request_no']);
                $sheet->setCellValue('B' . $i, $row['requestor']);
                $sheet->setCellValue('C' . $i, $row['created_at']);
                $sheet->setCellValue('D' . $i, $row['request_type']);
                $sheet->setCellValue('E' . $i, $row['desc']);
                $sheet->setCellValue('F' . $i, $row['project_name1']);
                $sheet->setCellValue('G' . $i, number_format($row['total']));
                $sheet->setCellValue('H' . $i, number_format($row['amount_verified']));
                $sheet->setCellValue('I' . $i, $row['date_requested']);
                $sheet->setCellValue('J' . $i, $row['checked_date']);

                $checked_date = '';
                if($row['approve1_date'] != '' || $row['approve2_date'] != '')
                {
                    $checked_date = ($row['approve1_date'] == "" ? "---" : $row['approve1_date']) . "\n" . ($row['approve2_date'] == "" ? "---" : $row['approve2_date']);
                }

                $sheet->getStyle('K' . $i)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension('K')->setWidth(20);

                $sheet->setCellValue('K' . $i, $checked_date);

                $sheet->setCellValue('L' . $i, $row['release_date']);
                $sheet->setCellValue('M' . $i, $row['liquidate_date']);
                $sheet->setCellValue('N' . $i, $row['verified_date']);
           

                $sheet->getStyle('A'. $i. ':' . 'N' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'N1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'N' . --$i)->applyFromArray($styleArray);

            ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file.xlsx"');

            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');


            exit;
    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
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


?>