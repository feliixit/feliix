<?php
ob_start();
//error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', true);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 4);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$title = (isset($_POST['title']) ?  $_POST['title'] : '');
$all_day = (isset($_POST['all_day']) && ($_POST['all_day'] === 'true') ? 1 : 0);
$start_time = (isset($_POST['start_time']) ?  $_POST['start_time'] : '');
$end_time = (isset($_POST['end_time']) ?  $_POST['end_time'] : '');
$color = (isset($_POST['color']) ?  $_POST['color'] : '');
$color_other = (isset($_POST['color_other']) ?  $_POST['color_other'] : '');
$text_color = (isset($_POST['text_color']) ?  $_POST['text_color'] : '');
$project = (isset($_POST['project']) ?  $_POST['project'] : '');
$sales_executive = (isset($_POST['sales_executive']) ?  $_POST['sales_executive'] : '');
$project_in_charge = (isset($_POST['project_in_charge']) ?  $_POST['project_in_charge'] : '');
$project_relevant = (isset($_POST['project_relevant']) ?  $_POST['project_relevant'] : '');
$installer_needed = (isset($_POST['installer_needed']) ?  $_POST['installer_needed'] : '');
$installer_needed_other = (isset($_POST['installer_needed_other']) ?  $_POST['installer_needed_other'] : '');
$installer_needed_location = (isset($_POST['installer_needed_location']) ?  $_POST['installer_needed_location'] : '');
$things_to_bring = (isset($_POST['things_to_bring']) ?  $_POST['things_to_bring'] : '');
$things_to_bring_location = (isset($_POST['things_to_bring_location']) ?  $_POST['things_to_bring_location'] : '');
$products_to_bring = (isset($_POST['products_to_bring']) ?  $_POST['products_to_bring'] : '');
$products_to_bring_files = (isset($_POST['products_to_bring_files']) ?  $_POST['products_to_bring_files'] : '');
$service = (isset($_POST['service']) ?  $_POST['service'] : '');
$driver = (isset($_POST['driver']) ?  $_POST['driver'] : '');
$driver_other = (isset($_POST['driver_other']) ?  $_POST['driver_other'] : '');
$back_up_driver = (isset($_POST['back_up_driver']) ?  $_POST['back_up_driver'] : '');
$back_up_driver_other = (isset($_POST['back_up_driver_other']) ?  $_POST['back_up_driver_other'] : '');
$photoshoot_request = (isset($_POST['photoshoot_request']) && $_POST['photoshoot_request'] === "Yes" ? 1 : 0);
$notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
$lock = (isset($_POST['lock']) ?  $_POST['lock'] : '');
$confirm = (isset($_POST['confirm']) ?  $_POST['confirm'] : '');
$work_calendar_main_id = (isset($_POST['work_calendar_main_id']) ?  $_POST['work_calendar_main_id'] : 0);
$location = (isset($_POST['location']) ?  $_POST['location'] : '');
$agenda = (isset($_POST['agenda']) ?  $_POST['agenda'] : '');
$appoint_time = (isset($_POST['appoint_time']) ?  $_POST['appoint_time'] : '');
$message = (isset($_POST['message']) ?  $_POST['message'] : '');
$is_enabled = (isset($_POST['is_enabled']) && $_POST['is_enabled'] === "true" ? 1 : 0);
$created_by = (isset($_POST['created_by']) ?  $_POST['created_by'] : '');
$updated_by = (isset($_POST['updated_by']) ?  $_POST['updated_by'] : '');
$deleted_by = (isset($_POST['deleted_by']) ?  $_POST['deleted_by'] : '');

$related_project_id = (isset($_POST['related_project_id']) ?  $_POST['related_project_id'] : 0);
$related_stage_id = (isset($_POST['related_stage_id']) ?  $_POST['related_stage_id'] : 0);

if($related_project_id == null || $related_project_id == "" || $related_project_id == "null")
    $related_project_id = 0;

if($related_stage_id == null || $related_stage_id == "" || $related_stage_id == "null")
    $related_stage_id = 0;

$detail_list = (isset($_POST['detail_list']) ?  $_POST['detail_list'] : '');
$detail_array = json_decode($detail_list, true);

$today = (isset($_POST['today']) ?  $_POST['today'] : '');

$sdate = (isset($_POST['sdate']) ?  $_POST['sdate'] : '');
$edate = (isset($_POST['edate']) ?  $_POST['edate'] : '');

$status = (isset($_POST['status']) ?  $_POST['status'] : 0);

$check_info = (isset($_POST['check_info']) ?  $_POST['check_info'] : '[]');
$check_info_ary = json_decode($check_info, true);

$user_id = 0;
$user_name = "";

$merged_results = array();
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/database.php';
include_once 'objects/work_calender.php';
include_once 'config/conf.php';

include_once 'mail.php';

use Google\Cloud\Storage\StorageClient;
use PhpOffice\PhpWord\IOFactory;

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

$workCalenderMain = new WorkCalenderMain($db);
$workCalenderDetails = new WorkCalenderDetails($db);
$workCalenderMessages = new WorkCalenderMessages($db);
//$le = new Leave($db);

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = $decoded->data->id;
    $user_name = $decoded->data->username;

    if($action == 1){
        //select all
        try {
            $query = "SELECT * from work_calendar_main main 
                        where main.is_enabled = true ";

            $stmt = $db->prepare( $query );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            $merged_results = RefactorInstallerNeeded($merged_results, $db);

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
    else if ($action == 10) {
        //select all

        $database_sea = new Database_Sea();
        $db_sea = $database_sea->getConnection();

        try {
            $query = "SELECT main.id, main.title, main.start_time, main.end_time, 
                            main.color, main.color_other, main.text_color, main.all_day, main.photoshoot_request, 
                            main.project, main.sales_executive, main.project_in_charge, main.project_relevant,
                            main.installer_needed, main.installer_needed_other, main.things_to_bring_location,
                            main.things_to_bring, main.installer_needed_location,
                            main.products_to_bring, main.products_to_bring_files,
                            main.service, main.driver, main.driver_other, main.back_up_driver, main.back_up_driver_other,
                            main.notes, main.`lock`, main.related_project_id, main.related_stage_id,
                            main.created_by, main.created_at, main.updated_by, main.updated_at, main.confirm,
                            detail.main_id, detail.agenda, detail.appoint_time, detail.end_time d_end_time, detail.sort, detail.location, main.status
                        from work_calendar_main main 
                        left join work_calendar_details detail on detail.main_id = main.id and detail.is_enabled = true
                        where main.is_enabled = true ";

            if($sdate != ""){
                $query .= " and main.start_time >= '" . $sdate . "-01 00:00:00' ";
            }

            if($edate != ""){
                // edate be the last day of the month
                $edate = date("Y-m-t", strtotime($edate . "-01"));

                $query .= " and main.start_time < '" . $edate . " 23:59:59' ";
                
            }

            $query .= " order by main.id, detail.sort ";

            $stmt = $db->prepare($query);
            $stmt->execute();

            $merged_results = array();
            $detail_array = array();

            // master
            $id = 0;
            $title = "";
            $start_time = "";
            $end_time = "";
            $color = "";
            $color_other = "";
            $text_color = "";
            $all_day = "";
            $photoshoot_request = "";
            $project = "";
            $sales_executive = "";
            $project_in_charge = "";
            $project_relevant = "";
            $installer_needed = "";
            $installer_needed_other = "";
            $things_to_bring_location = "";
            $things_to_bring = "";
            $installer_needed_location = "";
            $products_to_bring = "";
            $products_to_bring_files = "";
            $service = "";
            $driver = "";
            $driver_other = "";
            $driver_text = "";
            $back_up_driver = "";
            $back_up_driver_other = "";
            $notes = "";
            $lock = "";
            $related_project_id = "";
            $related_stage_id = "";
            $created_by = "";
            $created_at = "";
            $updated_by = "";
            $updated_at = "";
            $status = 0;

            $check1 = array();
            $check2 = array();

            $confirm = "";

            // detail
            $main_id = 0;
            $agenda = "";
            $appoint_time = "";
            $d_end_time = "";
            $sort = "";
            $location = "";

            $old_id = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if($old_id != $row['id'] && $old_id != 0)
                {
                    // remove item from array where main_id = ''
                    $detail_array = array_filter($detail_array, function($item) {
                        return $item['main_id'] != '';
                    });

                    $merged_results[] = array(
                        "id" => $id,
                        "title" => $title,
                        "start_time" => $start_time,
                        "end_time" => $end_time,
                        "color" => $color,
                        "color_other" => $color_other,
                        "text_color" => $text_color,
                        "all_day" => $all_day,
                        "confirm" => $confirm,
                        "photoshoot_request" => $photoshoot_request,
                        "project" => $project,
                        "sales_executive" => $sales_executive,
                        "project_in_charge" => $project_in_charge,
                        "project_relevant" => $project_relevant,
                        "installer_needed" => $installer_needed,
                        "installer_needed_other" => $installer_needed_other,
                        "things_to_bring_location" => $things_to_bring_location,
                        "things_to_bring" => $things_to_bring,
                        "installer_needed_location" => $installer_needed_location,
                        "products_to_bring" => $products_to_bring,
                        "products_to_bring_files" => $products_to_bring_files,
                        "service" => $service,
                        "driver" => $driver,
                        "driver_text" => $driver_text,
                        "driver_other" => $driver_other,
                        "back_up_driver" => $back_up_driver,
                        "back_up_driver_other" => $back_up_driver_other,
                        "notes" => $notes,
                        "lock" => $lock,
                        "related_project_id" => $related_project_id,
                        "related_stage_id" => $related_stage_id,
                        "created_by" => $created_by,
                        "created_at" => $created_at,
                        "updated_by" => $updated_by,
                        "updated_at" => $updated_at,
                        "detail" => $detail_array,
                        "status" => $status,

                        "check1" => $check1,
                        "check2" => $check2,
                    );

                    $detail_array = array();

                }

                $id = $row['id'];
                $title = $row['title'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $color = $row['color'];
                $color_other = $row['color_other'];
                $text_color = $row['text_color'];
                $all_day = $row['all_day'];
                $confirm = $row['confirm'];
                $photoshoot_request = $row['photoshoot_request'];
                $project = $row['project'];
                $sales_executive = $row['sales_executive'];
                $project_in_charge = $row['project_in_charge'];
                $project_relevant = $row['project_relevant'];
                $installer_needed = $row['installer_needed'];
                $installer_needed_other = $row['installer_needed_other'];
                $things_to_bring_location = $row['things_to_bring_location'];
                $things_to_bring = $row['things_to_bring'];
                $installer_needed_location = $row['installer_needed_location'];
                $products_to_bring = $row['products_to_bring'];
                $products_to_bring_files = $row['products_to_bring_files'];
                $service = $row['service'];
                $driver = $row['driver'];

                $driver_text = getDriver($driver);

                $driver_other = $row['driver_other'];
                $back_up_driver = $row['back_up_driver'];
                $back_up_driver_other = $row['back_up_driver_other'];
                $notes = $row['notes'];
                $lock = $row['lock'];
                $related_project_id = $row['related_project_id'];
                $related_stage_id = $row['related_stage_id'];
                $created_by = $row['created_by'];
                $created_at = $row['created_at'];
                $updated_by = $row['updated_by'];
                $updated_at = $row['updated_at'];

                $check1 = GetCheck($db_sea, $row['id'], "1", "1");
                $check2 = GetCheck($db_sea, $row['id'], "2", "1");

                $status = $row['status'];

                $main_id = $row['main_id'];
                $agenda = $row['agenda'];
                $appoint_time = $row['appoint_time'];
                $d_end_time = $row['d_end_time'];
                $sort = $row['sort'];
                $location = $row['location'];

                $old_id = $id;

                $detail_array[] = array(
                    "main_id" => $main_id,
                    "agenda" => $agenda,
                    "appoint_time" => $appoint_time,
                    "end_time" => $d_end_time,
                    "sort" => $sort,
                    "location" => $location
                );

            }
        

            if($old_id != 0)
            {
                // remove item from array where main_id = ''
                $detail_array = array_filter($detail_array, function($item) {
                    return $item['main_id'] != '';
                });

                $merged_results[] = array(
                    "id" => $id,
                    "title" => $title,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "color" => $color,
                    "color_other" => $color_other,
                    "text_color" => $text_color,
                    "all_day" => $all_day,
                    "confirm" => $confirm,
                    "photoshoot_request" => $photoshoot_request,
                    "project" => $project,
                    "sales_executive" => $sales_executive,
                    "project_in_charge" => $project_in_charge,
                    "project_relevant" => $project_relevant,
                    "installer_needed" => $installer_needed,
                    "installer_needed_other" => $installer_needed_other,
                    "things_to_bring_location" => $things_to_bring_location,
                    "things_to_bring" => $things_to_bring,
                    "installer_needed_location" => $installer_needed_location,
                    "products_to_bring" => $products_to_bring,
                    "products_to_bring_files" => $products_to_bring_files,
                    "service" => $service,
                    "driver" => $driver,
                    "driver_other" => $driver_other,
                    "driver_text" => $driver_text,
                    "back_up_driver" => $back_up_driver,
                    "back_up_driver_other" => $back_up_driver_other,
                    "notes" => $notes,
                    "lock" => $lock,
                    "related_project_id" => $related_project_id,
                    "related_stage_id" => $related_stage_id,
                    "created_by" => $created_by,
                    "created_at" => $created_at,
                    "updated_by" => $updated_by,
                    "updated_at" => $updated_at,
                    "detail" => $detail_array,
                    "status" => $status,
                    
                    "check1" => $check1,
                    "check2" => $check2,
                );
            }
            

            $merged_results = RefactorInstallerNeeded($merged_results, $db);
    

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 2) {
        //add
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));

            $workCalenderMain->title = $title;
            $workCalenderMain->all_day = $all_day;
            $workCalenderMain->start_time = $start_time;
            $workCalenderMain->end_time = $end_time;
            $workCalenderMain->color = $color;
            $workCalenderMain->color_other = $color_other;
            $workCalenderMain->text_color = $text_color;
            $workCalenderMain->project = $project;
            $workCalenderMain->sales_executive = $sales_executive;
            $workCalenderMain->project_in_charge = $project_in_charge;
            $workCalenderMain->project_relevant = $project_relevant;
            $workCalenderMain->installer_needed = $installer_needed;
            $workCalenderMain->installer_needed_other = $installer_needed_other;
            $workCalenderMain->installer_needed_location = $installer_needed_location;
            $workCalenderMain->things_to_bring = $things_to_bring;
            $workCalenderMain->things_to_bring_location = $things_to_bring_location;

            $workCalenderMain->related_project_id = $related_project_id;
            $workCalenderMain->related_stage_id = $related_stage_id;

            $workCalenderMain->products_to_bring = $products_to_bring;
            $workCalenderMain->products_to_bring_files = $products_to_bring_files;
            $workCalenderMain->service = $service;
            $workCalenderMain->driver = $driver;
            $workCalenderMain->driver_other = $driver_other;
            $workCalenderMain->back_up_driver = $back_up_driver;
            $workCalenderMain->back_up_driver_other = $back_up_driver_other;
            $workCalenderMain->photoshoot_request = $photoshoot_request;
            $workCalenderMain->notes = $notes;
            $workCalenderMain->is_enabled = $is_enabled;
            $workCalenderMain->created_by = $created_by;
            $arr = $workCalenderMain->create();

            http_response_code(200);
            echo json_encode(array($arr));
            //echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    } else if ($action == 3) {
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->title = $title;
            $workCalenderMain->all_day = $all_day;
            $workCalenderMain->start_time = $start_time;
            $workCalenderMain->end_time = $end_time;
            $workCalenderMain->color = $color;
            $workCalenderMain->color_other = $color_other;
            $workCalenderMain->text_color = $text_color;
            $workCalenderMain->project = $project;
            $workCalenderMain->sales_executive = $sales_executive;
            $workCalenderMain->project_in_charge = $project_in_charge;
            $workCalenderMain->project_relevant = $project_relevant;
            $workCalenderMain->installer_needed = $installer_needed;
            $workCalenderMain->installer_needed_other = $installer_needed_other;
            $workCalenderMain->installer_needed_location = $installer_needed_location;
            $workCalenderMain->things_to_bring = $things_to_bring;
            $workCalenderMain->things_to_bring_location = $things_to_bring_location;

            $workCalenderMain->products_to_bring = $products_to_bring;
            $workCalenderMain->products_to_bring_files = $products_to_bring_files;
            $workCalenderMain->service = $service;
            $workCalenderMain->driver = $driver;
            $workCalenderMain->driver_other = $driver_other;
            $workCalenderMain->back_up_driver = $back_up_driver;
            $workCalenderMain->back_up_driver_other = $back_up_driver_other;
            $workCalenderMain->photoshoot_request = $photoshoot_request;
            $workCalenderMain->notes = $notes;
            $workCalenderMain->is_enabled = $is_enabled;
            $workCalenderMain->updated_by = $updated_by;
            $arr = $workCalenderMain->update();

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }
    //else if($action == 4) {//未處理
    //    //select by date
    //    try{
    //        $query = "SELECT * from work_calendar_main where is_enabled = true ";
    //        if($start_date!='') {
    //            $query = $query . " and paid_date >= '$start_date' ";
    //        }
    //
    //        if($end_date!='') {
    //            $query = $query . " and paid_date <= '$end_date' ";
    //        }
    //        
    //        if($category!='') {
    //            $query = $query . " and category <= '$category' ";
    //        }
    //        
    //        if($sub_category!='') {
    //            $query = $query . " and sub_category <= '$sub_category' ";
    //        }
    //        $query = $query . " order by created_at desc ";
    //        $stmt = $db->prepare( $query );
    //        $stmt->execute();
    //        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //            $merged_results[] = $row;
    //        }
    //        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    //    }
    //    catch(Exception $e){
    //        http_response_code(401);
    //
    //        echo json_encode(array("message" => ".$e."));
    //    }
    //}
    else if ($action == 5) {
        //get members
        try {
            $query = "SELECT * from user where status <> -1 order by username";
            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 6) {
        //select by id
        try {
            $query = "SELECT * from work_calendar_main where id = " . $id;
            $stmt = $db->prepare($query);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            $merged_results = RefactorInstallerNeeded($merged_results, $db);
            
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 7) {
        //delete
        try {
            $database_1 = new Database();
            $db_1 = $database_1->getConnection();

            // for requestor
            $project = "";
            $all_day = "";
            $start_time = "";
            $end_time = "";
            $created_by = "";
            $project_relevant = "";
            $service = "";
            $requestor = "";
            $driver = "";
            $_status = 0;

            $sql = "select project, all_day, start_time, end_time, created_by, project_relevant, service, requestor, status, driver from work_calendar_main where id = :id";

            $stmt = $db_1->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // read old and append into array
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project = $row['project'];
                $all_day = $row['all_day'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $created_by = $row['created_by'];
                $project_relevant = $row['project_relevant'];
                $service = $row['service'];
                $requestor = $row['requestor'];
                $_status = $row['status'];
                $driver = $row['driver'];
            }


            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->deleted_by = $deleted_by;
            $arr = $workCalenderMain->delete();

            SendDelMail($id, $deleted_by);


            // for mail
            $database_sea = new Database_Sea();
            $db_sea = $database_sea->getConnection();

            $check1 = GetCheck($db_sea, $id, "1", "1");
            $check2 = GetCheck($db_sea, $id, "2", "1");

            $date_check = date("Y-m-d", strtotime($start_time));
            $time_check = date("h:i A", strtotime($start_time)) . " to " . date("h:i A", strtotime($end_time));
            $service_check = $service;
            $driver_check = $driver;
            if(count($check1) > 0)
            {
                $date_check = date("Y-m-d", strtotime($check1[0]["date_use"]));
                $time_check = date("h:i A", strtotime($check1[0]["time_out"])) . " to " . date("h:i A", strtotime($check1[0]["time_in"]));
                $service_check = $check1[0]["car_use"];
                $driver_check = $check1[0]["driver"];
            }
            if(count($check2) > 0)
            {
                $driver_check = $check2[0]["driver"];
            }

            
            
            $cc = $project_relevant;
            $creator = $created_by;


            $date = date("Y-m-d", strtotime($start_time));
            $time = date("h:i A", strtotime($start_time)) . " to " . date("h:i A", strtotime($end_time));
            $service = $service;

            if($_status == 2)
            {
                $to = $created_by . "," . $requestor . "," . $project_relevant . "," . $user_name;
                $att = get_schedule_file_full($id);
                delete_car_approval_mail_5($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $user_name);
            }

            if($_status == 1)
            {
                $to = $created_by . "," . $requestor  . "," . $user_name;
                $att = get_schedule_file($id);
                delete_car_request_mail_6($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $user_name);
            }

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    } else if ($action == 8) {
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->lock = $lock;

            $arr = $workCalenderMain->updateLockStatus();

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " lock success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    } else if ($action == 87) {
        //update
        try {
            $auto_pass = true;
            $database_sea = new Database_Sea();
            $db_sea = $database_sea->getConnection();

            if($status == '1')
            {
                $sql = "select ck.sid, ck.feliix
                            from car_calendar_check ck
                        where 1 = 1
                        and ck.car_use = :car_use 
                        and ck.date_use = :date_use 
                        and ck.status <> -1 ";

                $stmt = $db_sea->prepare($sql);

                $stmt->bindParam(':car_use', $check_info_ary['Service']);
                $stmt->bindParam(':date_use', $check_info_ary['Date']);

                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // $sql = "select * from work_calendar_main where id = " . $row['sid'] . " and status = 2 and is_enabled = 1";
                    // $stmt1 = $db->prepare($sql);

                    // $stmt1->execute();

                    // while ($row_feliix = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    if($row['feliix'] == 0)
                        $auto_pass = false;
                    // }
                }

                if($auto_pass)
                    $status = 2;
            }


            $database_1 = new Database();
            $db_1 = $database_1->getConnection();

                // for requestor
                $project = "";
                $all_day = "";
                $start_time = "";
                $end_time = "";
                $created_by = "";
                $project_relevant = "";
                $service = "";
                $requestor = "";

                $sql = "select project, all_day, start_time, end_time, created_by, project_relevant, service, requestor from work_calendar_main where id = :id";

                $stmt = $db_1->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // read old and append into array
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $project = $row['project'];
                    $all_day = $row['all_day'];
                    $start_time = $row['start_time'];
                    $end_time = $row['end_time'];
                    $created_by = $row['created_by'];
                    $project_relevant = $row['project_relevant'];
                    $service = $row['service'];
                    $requestor = $row['requestor'];
                }
                
                if($requestor == "")
                    $requestor = $user_name;
                else
                    $requestor = $requestor . "," . $user_name;

                // update requestor
                try {
                    $sql = "update 
                    work_calendar_main
                            set 
                                requestor = :requestor
                            where id = :id";

                    $stmt = $db_1->prepare($sql);

                    $stmt->bindParam(':requestor', $requestor);
                    $stmt->bindParam(':id', $id);

                    $stmt->execute();

                } catch (Exception $e) {
                    http_response_code(501);
                    echo json_encode(array("insertion error" => $e->getMessage()));
                    die();
                }


            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->status = $status;

            $tout = $check_info_ary['Date'] . " " . $check_info_ary['Starttime'];
            $tin = $check_info_ary['Date'] . " " . $check_info_ary['Endtime'];

            if($check_info_ary['Allday'] == true)
            {
                $tout = $check_info_ary['Date'] . " 00:00:00";
                $tin = $check_info_ary['Date'] . " 23:59:59";
            }

            $arr = $workCalenderMain->updateRequestStatus();

            if($auto_pass)
            {
                try {
                    $sql = "insert into car_calendar_check
                                (sid, kind, date_use, car_use, driver, time_out, time_in,  created_by, created_at, feliix)
                            values
                                (:sid, '1', :date_use, :car_use, :driver, :time_out, :time_in, :created_by, now(), '1')";

                    $stmt = $db_sea->prepare($sql);

                    $stmt->bindParam(':sid', $id);
                    $stmt->bindParam(':date_use', $check_info_ary['Date']);
                    $stmt->bindParam(':car_use', $check_info_ary['Service']);
                    $stmt->bindParam(':driver', $check_info_ary['Driver_Text']);
                    $stmt->bindParam(':time_out',  $tout);
                    $stmt->bindParam(':time_in',  $tin);
                    $stmt->bindParam(':created_by', $check_info_ary['created_by']);


                    $stmt->execute();
                } catch (Exception $e) {
                    http_response_code(501);
                    echo json_encode(array("insertion error" => $e->getMessage()));
                    die();
                }

            }

            $to = $created_by . "," . $user_name;
            $cc = $project_relevant;
            $creator = $created_by;
            $date_check = $check_info_ary['Date'];
            $time_check = date("h:i A", strtotime($tout)) . " to " . date("h:i A", strtotime($tin));
            $service_check = $check_info_ary['Service'];
            $driver_check = $check_info_ary['Driver_Text'];

            $date = date("Y-m-d", strtotime($start_time));
            $time = date("h:i A", strtotime($start_time)) . " to " . date("h:i A", strtotime($end_time));
            $service = $service;

            if($status == 2)
            {
                $att = get_schedule_file_full($id);
                send_car_approval_mail_1($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att);
            }

            if($status == 1)
            {
                $to = $created_by . "," . $user_name;
                $att = get_schedule_file($id);
                send_car_request_mail_2($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att);
            }

            http_response_code(200);
            //echo json_encode(array($arr));
            echo json_encode(array("status" => $status));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }  else if ($action == 88) {
        //update
        try {

            $database_1 = new Database();
            $db_1 = $database_1->getConnection();

            // for requestor
            $project = "";
            $all_day = "";
            $start_time = "";
            $end_time = "";
            $created_by = "";
            $project_relevant = "";
            $service = "";
            $requestor = "";
            $driver = "";
            $_status = 0;

            $sql = "select project, all_day, start_time, end_time, created_by, project_relevant, service, requestor, status, driver from work_calendar_main where id = :id";

            $stmt = $db_1->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // read old and append into array
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project = $row['project'];
                $all_day = $row['all_day'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $created_by = $row['created_by'];
                $project_relevant = $row['project_relevant'];
                $service = $row['service'];
                $requestor = $row['requestor'];
                $_status = $row['status'];
                $driver = $row['driver'];
            }


            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->status = $status;

            $arr = $workCalenderMain->updateRequestStatus();

            // for mail
            $database_sea = new Database_Sea();
            $db_sea = $database_sea->getConnection();

            $check1 = GetCheck($db_sea, $id, "1", "1");
            $check2 = GetCheck($db_sea, $id, "2", "1");

            $date_check = date("Y-m-d", strtotime($start_time));
            $time_check = date("h:i A", strtotime($start_time)) . " to " . date("h:i A", strtotime($end_time));
            $service_check = $service;
            $driver_check = $driver;
            if(count($check1) > 0)
            {
                $date_check = date("Y-m-d", strtotime($check1[0]["date_use"]));
                $time_check = date("h:i A", strtotime($check1[0]["time_out"])) . " to " . date("h:i A", strtotime($check1[0]["time_in"]));
                $service_check = $check1[0]["car_use"];
                $driver_check = $check1[0]["driver"];
            }
            if(count($check2) > 0)
            {
                $driver_check = $check2[0]["driver"];
            }

            $cc = "";
            $creator = $created_by;

            $date = date("Y-m-d", strtotime($start_time));
            $time = date("h:i A", strtotime($start_time)) . " to " . date("h:i A", strtotime($end_time));
            $service = $service;

            if($_status == 2)
            {
                $to = $created_by . "," . $requestor . "," . $project_relevant . "," . $user_name;
                $att = get_schedule_file_full($id);
                withdraw_car_approval_mail_3($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $user_name);
            }

            if($_status == 1)
            {
                $to = $created_by . "," . $requestor . "," . $user_name;
                $att = get_schedule_file($id);
                withdraw_car_request_mail_4($to, $cc, $project, $creator, $date_check, $time_check, $service_check, $driver_check, $date, $time, $service, $att, $user_name);
            }

            // remove db_sea car_calendar_check's data
            $sql = "update car_calendar_check set `status` = -1, deleted_at = now(), deleted_by = :deleted_by where sid = :sid and date_use = :date_use and car_use = :car_use and feliix = '1' and `status` <> -1";
            $stmts = $db_sea->prepare($sql);
            $stmts->bindParam(':deleted_by', $user_name);
            $stmts->bindParam(':sid', $id);
            $stmts->bindParam(':date_use', $date);
            $stmts->bindParam(':car_use', $service);
            $stmts->execute();


            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " request success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }  else if ($action == 9) {
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->confirm = $confirm;

            $arr = $workCalenderMain->updateConfirmStatus();

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " confirm success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    } else if ($action == 22) {
        //add
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $database = new Database();
            $db = $database->getConnection();
            $db->beginTransaction();

            $workCalenderMain = new WorkCalenderMain($db);
            $workCalenderDetails = new WorkCalenderDetails($db);

            $workCalenderMain->title = $title;
            $workCalenderMain->all_day = $all_day;
            $workCalenderMain->start_time = $start_time;
            $workCalenderMain->end_time = $end_time;
            $workCalenderMain->color = $color;
            $workCalenderMain->color_other = $color_other;
            $workCalenderMain->text_color = $text_color;
            $workCalenderMain->project = $project;
            $workCalenderMain->sales_executive = $sales_executive;
            $workCalenderMain->project_in_charge = $project_in_charge;
            $workCalenderMain->project_relevant = $project_relevant;
            $workCalenderMain->installer_needed = $installer_needed;
            $workCalenderMain->installer_needed_other = $installer_needed_other;
            $workCalenderMain->installer_needed_location = $installer_needed_location;
            $workCalenderMain->things_to_bring = $things_to_bring;
            $workCalenderMain->things_to_bring_location = $things_to_bring_location;

            $workCalenderMain->related_project_id = $related_project_id;
            $workCalenderMain->related_stage_id = $related_stage_id;

            $workCalenderMain->products_to_bring = $products_to_bring;
            $workCalenderMain->products_to_bring_files = $products_to_bring_files;
            $workCalenderMain->service = $service;
            $workCalenderMain->driver = $driver;
            $workCalenderMain->driver_other = $driver_other;
            $workCalenderMain->back_up_driver = $back_up_driver;
            $workCalenderMain->back_up_driver_other = $back_up_driver_other;
            $workCalenderMain->photoshoot_request = $photoshoot_request;
            $workCalenderMain->notes = $notes;
            $workCalenderMain->is_enabled = $is_enabled;
            $workCalenderMain->created_by = $created_by;
            $arr = $workCalenderMain->create();

            // detail
            for ($i = 0; $i < count($detail_array); $i++) {
                try {
                    // decode jwt
                    //$key = 'myKey';
                    //$decoded = JWT::decode($jwt, $key, array('HS256'));

                    $workCalenderDetails->main_id = $arr;
                    $workCalenderDetails->location = $detail_array[$i]['location'];
                    $workCalenderDetails->agenda = $detail_array[$i]['agenda'];
                    $workCalenderDetails->appoint_time = $detail_array[$i]['appointtime'];
                    $workCalenderDetails->end_time = $detail_array[$i]['endtime'];
                    $workCalenderDetails->sort = $detail_array[$i]['sort'];
                    $workCalenderDetails->is_enabled = 1;
                    $workCalenderDetails->created_by = $created_by;
                    $workCalenderDetails->create();

                    //echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa")));

                } // if decode fails, it means jwt is invalid
                catch (Exception $e) {
                    $db->rollback();

                    http_response_code(501);

                    echo json_encode(array("Detail insertion error"));

                    die();
                }
            }

            try {
                $total = 0;
                if (isset($_FILES['files']))
                    $total = count($_FILES['files']['name']);
                // Loop through each file
                for ($i = 0; $i < $total; $i++) {

                    if (isset($_FILES['files']['name'][$i])) {
                        $image_name = $_FILES['files']['name'][$i];
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo","dwf");
                        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $valid_extensions)) {
                            //$upload_path = 'img/' . time() . '.' . $extension;

                            $storage = new StorageClient([
                                'projectId' => 'predictive-fx-284008',
                                'keyFilePath' => $conf::$gcp_key
                            ]);

                            $bucket = $storage->bucket('calendarfile');

                            $upload_name = pathinfo($today . '_' . $image_name, PATHINFO_FILENAME) . '.' . $extension;

                            if (!file_exists($_FILES['files']['tmp_name'][$i])) {
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                                die();
                            }

                            if ($bucket->upload(
                                fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                ['name' => $upload_name]
                            )) {
                                $message = 'Uploaded';
                                $code = 0;
                                $image = $image_name;
                            } else {
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                                die();
                            }
                        } else {
                            $message = 'Only Images or Office files allowed to upload';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array($message));
                            die();
                        }
                    }
                }
            } catch (Exception $e) {
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                die();
            }

            $db->commit();

            // send notify mail
            SendCreateMail($arr, $project, $created_by, substr($start_time,0,10), $all_day, $start_time, $end_time, $sales_executive, $project_in_charge, $project_relevant);


            http_response_code(200);
            echo json_encode(array($arr));
            die();
            //echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            $db->rollback();
            http_response_code(501);

            echo json_encode(array("Error create schedule"));

            die();
        }
    } else if ($action == 33) {
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $database = new Database();
            $db = $database->getConnection();

            $_record = GetTaskDetail($id, $db);

            $db->beginTransaction();

            $workCalenderMain = new WorkCalenderMain($db);
            $workCalenderDetails = new WorkCalenderDetails($db);

            $workCalenderMain->id = $id;
            $workCalenderMain->title = $title;
            $workCalenderMain->all_day = $all_day;
            $workCalenderMain->start_time = $start_time;
            $workCalenderMain->end_time = $end_time;
            $workCalenderMain->color = $color;
            $workCalenderMain->color_other = $color_other;
            $workCalenderMain->text_color = $text_color;
            $workCalenderMain->project = $project;
            $workCalenderMain->sales_executive = $sales_executive;
            $workCalenderMain->project_in_charge = $project_in_charge;
            $workCalenderMain->project_relevant = $project_relevant;
            $workCalenderMain->installer_needed = $installer_needed;
            $workCalenderMain->installer_needed_other = $installer_needed_other;
            $workCalenderMain->installer_needed_location = $installer_needed_location;
            $workCalenderMain->things_to_bring = $things_to_bring;
            $workCalenderMain->things_to_bring_location = $things_to_bring_location;

            $workCalenderMain->related_project_id = $related_project_id;
            $workCalenderMain->related_stage_id = $related_stage_id;

            $workCalenderMain->products_to_bring = $products_to_bring;
            $workCalenderMain->products_to_bring_files = $products_to_bring_files;
            $workCalenderMain->service = $service;
            $workCalenderMain->driver = $driver;
            $workCalenderMain->driver_other = $driver_other;
            $workCalenderMain->back_up_driver = $back_up_driver;
            $workCalenderMain->back_up_driver_other = $back_up_driver_other;
            $workCalenderMain->photoshoot_request = $photoshoot_request;
            $workCalenderMain->notes = $notes;
            $workCalenderMain->is_enabled = $is_enabled;
            $workCalenderMain->updated_by = $updated_by;
            $arr = $workCalenderMain->update();

            // delete detail first
            try {
                // decode jwt
                //$key = 'myKey';
                //$decoded = JWT::decode($jwt, $key, array('HS256'));
                $workCalenderDetails->main_id = $id;
                $workCalenderDetails->deleted_by = $updated_by;
                $arr = $workCalenderDetails->delete();

            } // if decode fails, it means jwt is invalid
            catch (Exception $e) {
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Detail delete error"));
                die();
            }

            // detail
            for ($i = 0; $i < count($detail_array); $i++) {
                try {

                    $workCalenderDetails->main_id = $id;
                    $workCalenderDetails->location = $detail_array[$i]['location'];
                    $workCalenderDetails->agenda = $detail_array[$i]['agenda'];
                    $workCalenderDetails->appoint_time = $detail_array[$i]['appointtime'];
                    $workCalenderDetails->end_time = $detail_array[$i]['endtime'];
                    $workCalenderDetails->sort = $detail_array[$i]['sort'];
                    $workCalenderDetails->is_enabled = 1;
                    $workCalenderDetails->created_by = $created_by;
                    $workCalenderDetails->create();


                } 
                catch (Exception $e) {
                    $db->rollback();

                    http_response_code(501);

                    echo json_encode(array("Detail insertion error"));

                    die();
                }
            }

            try {
                $total = 0;
                if (isset($_FILES['files']))
                    $total = count($_FILES['files']['name']);
                // Loop through each file
                for ($i = 0; $i < $total; $i++) {

                    if (isset($_FILES['files']['name'][$i])) {
                        $image_name = $_FILES['files']['name'][$i];
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo","dwf");
                        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                        if (in_array(strtolower($extension), $valid_extensions)) {
                            //$upload_path = 'img/' . time() . '.' . $extension;

                            $storage = new StorageClient([
                                'projectId' => 'predictive-fx-284008',
                                'keyFilePath' => $conf::$gcp_key
                            ]);

                            $bucket = $storage->bucket('calendarfile');

                            $upload_name = pathinfo($today . '_' . $image_name, PATHINFO_FILENAME) . '.' . $extension;

                            if (!file_exists($_FILES['files']['tmp_name'][$i])) {
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                                die();
                            }

                            if ($bucket->upload(
                                fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                ['name' => $upload_name]
                            )) {
                                $message = 'Uploaded';
                                $code = 0;
                                $image = $image_name;
                            } else {
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                                die();
                            }
                        } else {
                            $message = 'Only Images or Office files allowed to upload';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array($message));
                            die();
                        }
                    }
                }
            } catch (Exception $e) {
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Error uploading, Please use laptop to upload again."));
                die();
            }

            $db->commit();

            $myArray1 = explode(',', $_record[0]['project_relevant']);
            $myArray2 = explode(',', $project_relevant);

            $result = array_diff($myArray1, $myArray2);
            $goodby = implode ( ",", $result );

            SendEditMail($id, $project, $created_by, substr($start_time, 0, 10), $all_day, $start_time, $end_time, $sales_executive, $project_in_charge, $project_relevant, $updated_by);
            if(count($result) > 0)
                SendEditGoodbyMail($id, $project, $created_by, substr($start_time, 0, 10), $all_day, $start_time, $end_time, $sales_executive, $project_in_charge, $goodby, $updated_by);

            http_response_code(200);
            echo json_encode(array("message" => "Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
            die();
            
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {
            $db->rollback();
            http_response_code(501);

            echo json_encode(array("Error create schedule"));

            die();
        }
    }
}


function SendCreateMail($last_id, $project, $creator, $_date, $all_day, $_stime, $_etime, $sales_executive, $project_in_charge, $relevants)
{    
    if($all_day == 1)
        $_time = "all day";
    else
        $_time = substr($_stime, 11, 5) . " to " . substr($_etime, 11, 5);

    $att = get_schedule_file($last_id);
    send_schedule_notify_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants, $att);
}

function SendEditMail($last_id, $project, $creator, $_date, $all_day, $_stime, $_etime, $sales_executive, $project_in_charge, $relevants, $updated_by)
{
    $_record = array();
    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);

    if($all_day == 1)
        $_time = "all day";
    else
        $_time = substr($_stime, 11, 5) . " to " . substr($_etime, 11, 5);

    $att = get_schedule_file($last_id);
    send_schedule_edit_mail($last_id, $project, $_record[0]["created_by"], $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by, $att);
}

function SendEditGoodbyMail($last_id, $project, $creator, $_date, $all_day, $_stime, $_etime, $sales_executive, $project_in_charge, $relevants, $updated_by)
{
    $_record = array();
    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);

    if($all_day == 1)
        $_time = "all day";
    else
        $_time = substr($_stime, 11, 5) . " to " . substr($_etime, 11, 5);

    $att = get_schedule_file($last_id);
    send_schedule_edit_goodby_mail($last_id, $project, $_record[0]["created_by"], $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by, $att);
}

function SendDelMail($last_id, $updated_by)
{
    $_record = array();
    $database = new Database();
    $db = $database->getConnection();

    $_record = GetTaskDetail($last_id, $db);

    $_date = substr($_record[0]["start_time"], 0, 10);
    
    if($_record[0]["all_day"] == 1)
        $_time = "all day";
    else
        $_time = substr($_record[0]["start_time"], 11, 5) . " to " . substr($_record[0]["end_time"], 11, 5);


    $att = get_schedule_file($last_id);
    send_schedule_del_mail($last_id, $_record[0]["project"], $_record[0]["created_by"], $_date, $_time, $_record[0]["sales_executive"], $_record[0]["project_in_charge"], $_record[0]["project_relevant"], $updated_by, $att);
}

function GetTaskDetail($id, $db)
{
    $sql = "SELECT *
            FROM work_calendar_main pt
            WHERE pt.id  = :id";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id',  $id);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function get_schedule_file($id)
{
    $database = new Database();
    $db = $database->getConnection();
 
    $sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
        project_in_charge, project_relevant, installer_needed, installer_needed_other, things_to_bring, installer_needed_location, things_to_bring_location, 
        products_to_bring, service, driver, driver_other,
        back_up_driver, back_up_driver_other, photoshoot_request, notes, products_to_bring_files,
        coalesce(pm.project_name, '') project_name, coalesce(pst.stage, '') stage_name, coalesce(`sequence`, '') sequence, main.status
        from work_calendar_main main 
        left join project_main pm on pm.id = main.related_project_id
        left join project_stages ps on ps.id = main.related_stage_id
        LEFT JOIN project_stage pst ON ps.stage_id = pst.id
        where  main.id = " . $id;

        $stmt = $db->prepare( $sql );
        $stmt->execute();
    
        $weekday = '';
        $start_time = '';
        $title = '';
        $sales_executive = '';
        $project_in_charge = '';
        $project_relevant = '';
        $installer_needed = '';
        $installer_needed_other = '';
        $things_to_bring = '';
        $installer_needed_location = '';
        $things_to_bring_location = '';
        $products_to_bring = '';
        $service = '';
        $driver = '';
        $driver_other = '';
        $back_up_driver = '';
        $back_up_driver_other = '';
        $photoshoot_request = '';
        $notes = '';
    
        $products_to_bring_files = '';

        $details = array();
    
        $location = '';
        $agenda = '';
        $appoint_time = '';
        $end_time = '';
    
        $onrecord = 0;
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        $onrecord = $onrecord + 1;
    
        $weekday = $row['weekday'];
        $start_time = $row['start_time'];
        $title = $row['title'];
        $sales_executive = $row['sales_executive'];
        $project_in_charge = $row['project_in_charge'];
        $project_relevant = $row['project_relevant'];
        $installer_needed = $row['installer_needed'];
        $installer_needed_other = $row['installer_needed_other'];
        $things_to_bring = $row['things_to_bring'];
        $installer_needed_location = $row['installer_needed_location'];
        $things_to_bring_location = $row['things_to_bring_location'];
        $products_to_bring = $row['products_to_bring'];
        $service = $row['service'];
        $driver = $row['driver'];
        $driver_other = $row['driver_other'];
        $back_up_driver = $row['back_up_driver'];
        $back_up_driver_other = $row['back_up_driver_other'];
        $photoshoot_request = $row['photoshoot_request'];
        $notes = $row['notes'];
    
        $products_to_bring_files = $row['products_to_bring_files'];
    
        $details = GetDetails($id, $db);
    
        // $location = $row['location'];
        // $agenda = $row['agenda'];
        // $appoint_time = $row['appoint_time'];
        // $end_time = $row['end_time'];
    
        break;
    }
    
    // Creating the new document...
    $phpWord = new PhpOffice\PhpWord\PhpWord();
    
    /* Note: any element you append to a document must reside inside of a Section. */
    
    // Adding an empty Section to the document...
    $section = $phpWord->addSection();
    // Adding Text element to the Section having font styled by default...
    $section->addText($weekday . ", " . $start_time . " Schedule");
    
    $section->addText("");
    
    $table = $section->addTable('table', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0
    ]);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($start_time);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Project:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($title);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Sales Executive:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($sales_executive);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Project_in_charge:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($project_in_charge);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Relevant Persons:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($project_relevant);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Technician needed:", array('bold' => true));

    // CONCAT installer_needed and installer_needed_other and remove duplicate
    //$installer_needed_other = str_replace(" ", "", $installer_needed_other);
    //$installer_needed = str_replace(" ", "", $installer_needed);
    $installer_needed_other_array = explode(",", $installer_needed_other);
    $installer_needed_array = explode(",", $installer_needed);
    $installer_needed_array = array_merge($installer_needed_array, $installer_needed_other_array);
    $installer_needed_array = array_unique($installer_needed_array);
   
    $merged_installer = trim(implode(", ", $installer_needed_array), ", ");

    $merged_installer = str_replace("  ", " ", $merged_installer);

    $table->addCell(8500, ['borderSize' => 6])->addText($merged_installer);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Things to Bring:", array('bold' => true));
    if($things_to_bring_location != "")
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, "From " . $things_to_bring_location . "\n" . $things_to_bring);
    }
    else
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, $things_to_bring);
    }
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Products to Bring:", array('bold' => true));
    if($installer_needed_location != "")
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, "From " . $installer_needed_location . "\n" . $products_to_bring);
    }
    else
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, $products_to_bring);
    }
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Service:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($service);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getDriver($driver) . ' ' . $driver_other);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Back-up Driver:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getDriver($back_up_driver) . ' ' . $back_up_driver_other);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Photoshoot Request:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getRequest($photoshoot_request));
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Note/s:", array('bold' => true));
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $notes);
    
    $section->addText("");
    
    $table1 = $section->addTable('table1', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0,
        'textAlign' => 'center'
    ]);
    
    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText("Location", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("Agenda",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("Appoint Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("End Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    
    // $table1->addRow();
    // $table1->addCell(2600, ['borderSize' => 6])->addText($location,  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    foreach ($details as &$value)
    {
        $location = $value['location'];
        $agenda = $value['agenda'];
        $appoint_time = $value['appoint_time'];
        $end_time = $value['end_time'];
    
        $table1->addRow();
        $table1->addCell(2600, ['borderSize' => 6])->addText($location, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    }
    
    // Adding Text element with font customized using explicitly created font style object...
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(true);
    $fontStyle->setName('Tahoma');
    $fontStyle->setSize(13);
    // $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
    //$myTextElement->setFontStyle($fontStyle);
    
    ob_end_clean();
    // Saving the document as OOXML file...
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);
    
    $conf = new Conf();

    $path = $conf::$upload_path . "tmp/";

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    else
    {
        $files = glob($path . "*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    $objWriter->save($path . "schedule" . $id . ".docx");

    return $path . "schedule" . $id . ".docx";

}




function get_schedule_file_full($id)
{
    $database = new Database();
    $db = $database->getConnection();
 
    $sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
        project_in_charge, project_relevant, installer_needed, installer_needed_other, things_to_bring, installer_needed_location, things_to_bring_location, 
        products_to_bring, service, driver, driver_other,
        back_up_driver, back_up_driver_other, photoshoot_request, notes, products_to_bring_files,
        coalesce(pm.project_name, '') project_name, coalesce(pst.stage, '') stage_name, coalesce(`sequence`, '') sequence, main.status
        from work_calendar_main main 
        left join project_main pm on pm.id = main.related_project_id
        left join project_stages ps on ps.id = main.related_stage_id
        LEFT JOIN project_stage pst ON ps.stage_id = pst.id
        where  main.id = " . $id;
    
        $stmt = $db->prepare( $sql );
        $stmt->execute();
    
        $weekday = '';
        $start_time = '';
        $title = '';
        $sales_executive = '';
        $project_in_charge = '';
        $project_relevant = '';
        $installer_needed = '';
        $installer_needed_other = '';
        $things_to_bring = '';
        $installer_needed_location = '';
        $things_to_bring_location = '';
        $products_to_bring = '';
        $service = '';
        $driver = '';
        $driver_other = '';
        $back_up_driver = '';
        $back_up_driver_other = '';
        $photoshoot_request = '';
        $notes = '';
    
        $products_to_bring_files = '';
    
        $location = '';
        $agenda = '';
        $appoint_time = '';
        $end_time = '';

        $details = array();
    
        $onrecord = 0;
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        $onrecord = $onrecord + 1;
    
        $weekday = $row['weekday'];
        $start_time = $row['start_time'];
        $title = $row['title'];
        $sales_executive = $row['sales_executive'];
        $project_in_charge = $row['project_in_charge'];
        $project_relevant = $row['project_relevant'];
        $installer_needed = $row['installer_needed'];
        $installer_needed_other = $row['installer_needed_other'];
        $things_to_bring = $row['things_to_bring'];
        $installer_needed_location = $row['installer_needed_location'];
        $things_to_bring_location = $row['things_to_bring_location'];
        $products_to_bring = $row['products_to_bring'];
        $service = $row['service'];
        $driver = $row['driver'];
        $driver_other = $row['driver_other'];
        $back_up_driver = $row['back_up_driver'];
        $back_up_driver_other = $row['back_up_driver_other'];
        $photoshoot_request = $row['photoshoot_request'];
        $notes = $row['notes'];
    
        $products_to_bring_files = $row['products_to_bring_files'];
    
        $details = GetDetails($id, $db);

        // $location = $row['location'];
        // $agenda = $row['agenda'];
        // $appoint_time = $row['appoint_time'];
        // $end_time = $row['end_time'];
    
        break;
    }
    
    
    // Creating the new document...
    $phpWord = new PhpOffice\PhpWord\PhpWord();
    
    /* Note: any element you append to a document must reside inside of a Section. */
    
    // Adding an empty Section to the document...
    $section = $phpWord->addSection();
    // Adding Text element to the Section having font styled by default...



    $database_sea = new Database_Sea();
    $db_sea = $database_sea->getConnection();

    $check_date_use = "";
    $check_car_use = "";
    $check_driver = "";
    $check_time_out = "";
    $check_time_in = "";

    $sql = "select date_use, car_use, driver, time_out, time_in from car_calendar_check where feliix = 1 and sid = " . $id . " order by id desc limit 1";

    $stmt = $db_sea->prepare( $sql );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        $check_date_use = $row['date_use'];
        $check_car_use = $row['car_use'];
        $check_driver = $row['driver'];
        $check_time_out = $row['time_out'];
        $check_time_in = $row['time_in'];

        break;
    }

    $check_dateString = date('Y-m-d', strtotime( $check_date_use));

    $check_tout = "";
    if($check_date_use != "" && $check_time_out != "")
    {
        //$check_dateString = new DateTime($check_date_use);
        $check_tout = date('h:i A', strtotime( $check_time_out));
    }

    $check_tin = "";
    if($check_date_use != "" && $check_time_in != "")
    {
        //$check_dateString = new DateTime($check_date_use);
        $check_tin = date('h:i A', strtotime($check_time_in));
    }

    $table2 = $section->addTable('table2', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0
    ]);


    $table2->addRow();
    $cell = $table2->addCell(10500, ['borderSize' => 6]);
    $cell->getStyle()->setGridSpan(2);
    $cell->addText("Request Review", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_dateString);

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Time:", array('bold' => true));
    $TextRun = $table2->addCell(8500, ['borderSize' => 6])->addTextRun();
    $TextRun->addText($check_tout);
    $TextRun->addText(" to ");
    $TextRun->addText($check_tin);

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Assigned Car:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_car_use);
    
    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Assigned Driver:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_driver);

    $section->addText("");
    $section->addText("");




    
    $table = $section->addTable('table', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0
    ]);

    $table->addRow();
    $cell = $table->addCell(10500, ['borderSize' => 6]);
    $cell->getStyle()->setGridSpan(2);
    $cell->addText("Content of Request", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($start_time);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Project:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($title);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Sales Executive:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($sales_executive);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Project_in_charge:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($project_in_charge);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Relevant Persons:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($project_relevant);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Technician needed:", array('bold' => true));

    // CONCAT installer_needed and installer_needed_other and remove duplicate
    //$installer_needed_other = str_replace(" ", "", $installer_needed_other);
    //$installer_needed = str_replace(" ", "", $installer_needed);
    $installer_needed_other_array = explode(",", $installer_needed_other);
    $installer_needed_array = explode(",", $installer_needed);
    $installer_needed_array = array_merge($installer_needed_array, $installer_needed_other_array);
    $installer_needed_array = array_unique($installer_needed_array);
   
    $merged_installer = trim(implode(", ", $installer_needed_array), ", ");

    $merged_installer = str_replace("  ", " ", $merged_installer);

    $table->addCell(8500, ['borderSize' => 6])->addText($merged_installer);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Things to Bring:", array('bold' => true));
    if($things_to_bring_location != "")
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, "From " . $things_to_bring_location . "\n" . $things_to_bring);
    }
    else
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, $things_to_bring);
    }
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Products to Bring:", array('bold' => true));
    if($installer_needed_location != "")
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, "From " . $installer_needed_location . "\n" . $products_to_bring);
    }
    else
    {
        $cell = $table->addCell(8500, ['borderSize' => 6]);
        addMultiLineText($cell, $products_to_bring);
    }
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Service:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText($service);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getDriver($driver) . ' ' . $driver_other);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Back-up Driver:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getDriver($back_up_driver) . ' ' . $back_up_driver_other);
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Photoshoot Request:", array('bold' => true));
    $table->addCell(8500, ['borderSize' => 6])->addText(getRequest($photoshoot_request));
    
    $table->addRow();
    $table->addCell(2000, ['borderSize' => 6])->addText("Note/s:", array('bold' => true));
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $notes);
    
    $section->addText("");
    
    $table1 = $section->addTable('table1', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0,
        'textAlign' => 'center'
    ]);
    
    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText("Location", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("Agenda",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("Appoint Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText("End Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    
    // $table1->addRow();
    // $table1->addCell(2600, ['borderSize' => 6])->addText($location,  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    foreach ($details as &$value)
    {
        $location = $value['location'];
        $agenda = $value['agenda'];
        $appoint_time = $value['appoint_time'];
        $end_time = $value['end_time'];
    
        $table1->addRow();
        $table1->addCell(2600, ['borderSize' => 6])->addText($location, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
        $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    
    }
    
    // Adding Text element with font customized using explicitly created font style object...
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setBold(true);
    $fontStyle->setName('Tahoma');
    $fontStyle->setSize(13);
    // $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
    //$myTextElement->setFontStyle($fontStyle);
    
    ob_end_clean();
    // Saving the document as OOXML file...
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);
    
    $conf = new Conf();

    $path = $conf::$upload_path . "tmp/";

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    else
    {
        $files = glob($path . "*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    $objWriter->save($path . "schedule" . $id . ".docx");

    return $path . "schedule" . $id . ".docx";

}


function getService($type){
    $leave_type = '';

    if($type =="1")
        $leave_type = "innova";
    if($type =="2")
        $leave_type = "avanza gold";
    if($type =="3")
        $leave_type = "avanza gray";
    if($type =="4")
        $leave_type = "L3001";
    if($type =="5")
        $leave_type = "L3002";
    if($type =="6")
        $leave_type = "Grab";
    
    return $leave_type;
}


function getDriver($type){
    $leave_type = '';

    if($type =="1")
        $leave_type = "MG";
    if($type =="2")
        $leave_type = "AY";
    if($type =="3")
        $leave_type = "EV";
    if($type =="4")
        $leave_type = "JB";
    if($type =="5")
        $leave_type = "MA";
    if($type =="6")
        $leave_type = "Other";

    return $leave_type;
}

function getRequest($type){
    $leave_type = '';

    if($type =="0")
        $leave_type = "No";
    if($type =="1")
        $leave_type = "Yes";
    
    return $leave_type;
}

function addMultiLineText($cell, $text)
{
    // break from line breaks
    $strArr = explode("\n", $text);

    // add text line together
    foreach ($strArr as $v) {
        $cell->addText($v);
    }
    
}

function GetCheck($db, $sid, $kind, $feliix)
{
    $result = array();

    $query = "SELECT * from car_calendar_check 
              where `feliix` = " . $feliix . " and `status` <> -1 and kind = '" . $kind . "' and sid = " . $sid . " order by id desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }

    return $result;
}

function grab_image($image_url,$image_file){

    /*
    $storage = new StorageClient([
        'projectId' => 'predictive-fx-284008',
        'keyFilePath' => $key
    ]);

    $bucket = $storage->bucket('feliiximg');

    $object = $bucket->object($image_url);
    $object->downloadToFile($image_file);
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://storage.googleapis.com/calendarfile/' . $image_url);
    //Create a new file where you want to save
    $fp = fopen($image_file, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec ($ch);
    curl_close ($ch);
    fclose($fp);
}

function RefactorInstallerNeeded($merged_results, $db)
{
    $tech = [];
    $query = "SELECT username  FROM `user` where status = 1 and title_id in (21, 22, 56, 57, 66)";
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tech[] = $row['username'];
    }

    // iterate over each row and filter installer_needed to installer_needed_other
    foreach ($merged_results as $key => $value) {
        // convert installer_needed into array
        //$value['installer_needed'] = str_replace(" ", "", $value['installer_needed']);
        $installer_needed_array = explode(",", $value['installer_needed']);
        $installer_needed_other_array = explode(",", $value['installer_needed_other']);

        // trim space of array
        $installer_needed_array = array_map('trim', $installer_needed_array);
        $installer_needed_other_array = array_map('trim', $installer_needed_other_array);

        $installer = array();
        $installer_other = array();

        foreach ($installer_needed_other_array as $people) {
            if (in_array($people, $tech)) 
                $installer[] = $people;
            else
                $installer_other[] = $people;
        }

        foreach ($installer_needed_array as $people) {
            if (in_array($people, $tech)) 
                $installer[] = $people;
            else
                $installer_other[] = $people;
           
        }

        // installer_needed_array to string concate by comma
        $merged_results[$key]['installer_needed'] = trim(implode(",", $installer), ",");

        $merged_results[$key]['installer_needed_other'] = trim(implode(", ", array_unique($installer_other)), ", ");
        $merged_results[$key]['installer_needed_other'] = str_replace("  ", " ", $merged_results[$key]['installer_needed_other']);
    }

    return $merged_results;
}

function GetDetails($id, $db)
    {
        $merged_details = array();

        $sql = "select detail.location, agenda, DATE_FORMAT(appoint_time, '%I:%i %p') appoint_time, 
                DATE_FORMAT(detail.end_time, '%I:%i %p') end_time
                from work_calendar_details detail
                where coalesce(detail.is_enabled, 1) = 1  and main_id = " . $id . " order by sort " ;

        $stmt = $db->prepare( $sql );
        $stmt->execute();

        $location = '';
        $agenda = '';
        $appoint_time = '';
        $end_time = '';

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            $location = $row['location'];
            $agenda = $row['agenda'];
            $appoint_time = $row['appoint_time'];
            $end_time = $row['end_time'];

            $merged_details[] = array(
                'location' => $location,
                'agenda' => $agenda,
                'appoint_time' => $appoint_time,
                'end_time' => $end_time
            );
        }

        return $merged_details;

    }

