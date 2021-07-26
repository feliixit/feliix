<?php
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
$work_calendar_main_id = (isset($_POST['work_calendar_main_id']) ?  $_POST['work_calendar_main_id'] : 0);
$location = (isset($_POST['location']) ?  $_POST['location'] : '');
$agenda = (isset($_POST['agenda']) ?  $_POST['agenda'] : '');
$appoint_time = (isset($_POST['appoint_time']) ?  $_POST['appoint_time'] : '');
$message = (isset($_POST['message']) ?  $_POST['message'] : '');
$is_enabled = (isset($_POST['is_enabled']) && $_POST['is_enabled'] === "true" ? 1 : 0);
$created_by = (isset($_POST['created_by']) ?  $_POST['created_by'] : '');
$updated_by = (isset($_POST['updated_by']) ?  $_POST['updated_by'] : '');
$deleted_by = (isset($_POST['deleted_by']) ?  $_POST['deleted_by'] : '');

$detail_list = (isset($_POST['detail_list']) ?  $_POST['detail_list'] : '');
$detail_array = json_decode($detail_list, true);

$today = (isset($_POST['today']) ?  $_POST['today'] : '');

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
    if ($action == 1) {
        //select all
        try {
            $query = "SELECT * from work_calendar_main where is_enabled = true";

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
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 7) {
        //delete
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderMain->id = $id;
            $workCalenderMain->deleted_by = $deleted_by;
            $arr = $workCalenderMain->delete();

            SendDelMail($id, $deleted_by);

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
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
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
                        $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
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

            SendEditMail($id, $project, $created_by, substr($start_time, 0, 10), $all_day, $start_time, $end_time, $sales_executive, $project_in_charge, $project_relevant, $updated_by);

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

    send_schedule_notify_mail($last_id, $project, $creator, $_date, $_time, $sales_executive, $project_in_charge, $relevants);
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

    send_schedule_edit_mail($last_id, $project, $_record[0]["created_by"], $_date, $_time, $sales_executive, $project_in_charge, $relevants, $updated_by);
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

    send_schedule_del_mail($last_id, $_record[0]["project"], $_record[0]["created_by"], $_date, $_time, $_record[0]["sales_executive"], $_record[0]["project_in_charge"], $_record[0]["project_relevant"], $updated_by);
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
