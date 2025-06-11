<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 4);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$subject = (isset($_POST['subject']) ?  $_POST['subject'] : '');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');
$remove = (isset($_POST['remove']) ?  $_POST['remove'] : '');
$message = (isset($_POST['message']) ?  $_POST['message'] : '');
$attendee = (isset($_POST['attendee']) ?  $_POST['attendee'] : '');
$location = (isset($_POST['location']) ?  $_POST['location'] : '');
$start_time = (isset($_POST['start_time']) ?  $_POST['start_time'] : '');
$end_time = (isset($_POST['end_time']) ?  $_POST['end_time'] : '');
$is_enabled = (isset($_POST['is_enabled']) && $_POST['is_enabled'] === "true"? 1 : 0);
$created_by = (isset($_POST['created_by']) ?  $_POST['created_by'] : '');
$updated_by = (isset($_POST['updated_by']) ?  $_POST['updated_by'] : '');
$deleted_by = (isset($_POST['deleted_by']) ?  $_POST['deleted_by'] : '');

$color = (isset($_POST['color']) ?  $_POST['color'] : '');
$color_other = (isset($_POST['color_other']) ?  $_POST['color_other'] : '');
$text_color = (isset($_POST['text_color']) ?  $_POST['text_color'] : '');

$merged_results = array();
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'objects/work_calender_notes.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

$workCalenderNotes = new WorkCalenderNotes($db);
$user_name = "";
//$le = new Leave($db);

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);
    

    echo json_encode(array("message" => "Access denied1."));
    die();
}
else
{
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_name = $decoded->data->username;
    
    if($action == 1){
        //select all
        try{
            $query = "SELECT * from work_calendar_notes where is_enabled = true";

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $items = [];

            $id = 0;
            $subject = "";
            $project_name = "";
            $message = "";
            $attendee = "";
            $location = "";
            $start_time = "";
            $end_time = "";
            $is_enabled = 0;
            $created_at = '';
            $updated_at = '';
            $deleted_at = "";
            $created_by = "";
            $updated_by = "";
            $deleted_by = "";

            $color = "";
            $color_other = "";
            $text_color = "";

            $attach = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($id != $row['id'] && $id != 0) {
                    $merged_results[] = array(
                        "id" => $id,
                        "subject" => $subject,
                        "project_name" => $project_name,
                        "message" => $message,
                        "attendee" => $attendee,
                        "location" => $location,
                        "start_time" => $start_time,
                        "end_time" => $end_time,
                        "is_enabled" => $is_enabled,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "deleted_at" => $deleted_at,
                        "created_by" => $created_by,
                        "updated_by" => $updated_by,
                        "deleted_by" => $deleted_by,
                        "items" => $items,
                        "attach" => $attach,

                        "color" => $color,
                        "color_other" => $color_other,
                        "text_color" => $text_color,
                        
                    );
    
                    $items = [];
                }
    
                $id = $row['id'];
                $subject = $row['subject'];
                $project_name = $row['project_name'];
                $message = $row['message'];
                $attendee = $row['attendee'];
                $location = $row['location'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $is_enabled = $row['is_enabled'];

                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];
                $deleted_at = $row['deleted_at'];

                $created_by = $row['created_by'];
                $updated_by = $row['updated_by'];
                $deleted_by = $row['deleted_by'];

                $color = $row['color'];
                $color_other = $row['color_other'];
                $text_color = $row['text_color'];

                $attach = GetItem($row['id'], $db, 'meeting_notes');

                if(!empty($attendee ))
                    $items = GetUserInfo($row['attendee'], $db);
            }

            if ($id != 0) {
                $merged_results[] = array(
                    "id" => $id,
                    "subject" => $subject,
                    "project_name" => $project_name,
                    "message" => $message,
                    "attendee" => $attendee,
                    "location" => $location,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "is_enabled" => $is_enabled,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    "deleted_at" => $deleted_at,
                    "created_by" => $created_by,
                    "updated_by" => $updated_by,
                    "deleted_by" => $deleted_by,
                    "items" => $items,
                    "attach" => $attach,

                    "color" => $color,
                    "color_other" => $color_other,
                    "text_color" => $text_color,
                );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
    else if($action == 11){
        //select my meeting_notes
        try{
            $query = "SELECT * from work_calendar_notes where is_enabled = true and (created_by = '" . $user_name . "' ) ";

            if($sdate != ""){
                $query .= " and main.start_time >= '" . $sdate . "-01 00:00:00' ";
            }

            if($edate != ""){
                // edate be the last day of the month
                $edate = date("Y-m-t", strtotime($edate . "-01"));

                $query .= " and main.start_time < '" . $edate . " 23:59:59' ";
                
            }

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $items = [];

            $id = 0;
            $subject = "";
            $project_name = "";
            $message = "";
            $attendee = "";
            $location = "";
            $start_time = "";
            $end_time = "";
            $is_enabled = 0;
            $created_at = '';
            $updated_at = '';
            $deleted_at = "";
            $created_by = "";
            $updated_by = "";
            $deleted_by = "";

            $color = "";
            $color_other = "";
            $text_color = "";


            $attach = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($id != $row['id'] && $id != 0) {
                    $merged_results[] = array(
                        "id" => $id,
                        "subject" => $subject,
                        "project_name" => $project_name,
                        "message" => $message,
                        "attendee" => $attendee,
                        "location" => $location,
                        "start_time" => $start_time,
                        "end_time" => $end_time,
                        "is_enabled" => $is_enabled,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "deleted_at" => $deleted_at,
                        "created_by" => $created_by,
                        "updated_by" => $updated_by,
                        "deleted_by" => $deleted_by,
                        "items" => $items,
                        "attach" => $attach,

                        "color" => $color,
                        "color_other" => $color_other,
                        "text_color" => $text_color,
                        
                    );
    
                    $items = [];
                }
    
                $id = $row['id'];
                $subject = $row['subject'];
                $project_name = $row['project_name'];
                $message = $row['message'];
                $attendee = $row['attendee'];
                $location = $row['location'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $is_enabled = $row['is_enabled'];

                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];
                $deleted_at = $row['deleted_at'];

                $created_by = $row['created_by'];
                $updated_by = $row['updated_by'];
                $deleted_by = $row['deleted_by'];

                $color = $row['color'];
                $color_other = $row['color_other'];
                $text_color = $row['text_color'];

                $attach = GetItem($row['id'], $db, 'meeting_notes');

                if(!empty($attendee ))
                    $items = GetUserInfo($row['attendee'], $db);
            }

            if ($id != 0) {
                $merged_results[] = array(
                    "id" => $id,
                    "subject" => $subject,
                    "project_name" => $project_name,
                    "message" => $message,
                    "attendee" => $attendee,
                    "location" => $location,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "is_enabled" => $is_enabled,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    "deleted_at" => $deleted_at,
                    "created_by" => $created_by,
                    "updated_by" => $updated_by,
                    "deleted_by" => $deleted_by,
                    "items" => $items,
                    "attach" => $attach,

                    "color" => $color,
                    "color_other" => $color_other,
                    "text_color" => $text_color,
                );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
    else if($action == 12){
        //select my meeting_notes
        $uid = (isset($_POST['uid']) ?  $_POST['uid'] : '');
        $sdate = (isset($_POST['sdate']) ?  $_POST['sdate'] : '');
        $edate = (isset($_POST['edate']) ?  $_POST['edate'] : '');

        $query = "select username from user where id = " . $uid;
        $stmt = $db->prepare( $query );
        $stmt->execute();

        $user_name = "";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_name = $row['username'];
        }

        try{

            $query = "SELECT * from work_calendar_notes where is_enabled = true and (created_by like '%" . $user_name ."%') ";

            if($sdate != ""){
                $query .= " and start_time >= '" . $sdate . "-01 00:00:00' ";
            }
            
            if($edate != ""){
                // edate be the last day of the month
                $edate = date("Y-m-t", strtotime($edate . "-01"));
            
                $query .= " and end_time <= '" . $edate . " 23:59:59' ";
                
            }

            $stmt = $db->prepare( $query );
            $stmt->execute();

            $items = [];

            $id = 0;
            $subject = "";
            $project_name = "";
            $message = "";
            $attendee = "";
            $location = "";
            $start_time = "";
            $end_time = "";
            $is_enabled = 0;
            $created_at = '';
            $updated_at = '';
            $deleted_at = "";
            $created_by = "";
            $updated_by = "";
            $deleted_by = "";

            $color = "";
            $color_other = "";
            $text_color = "";


            $attach = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($id != $row['id'] && $id != 0) {
                    $merged_results[] = array(
                        "id" => $id,
                        "subject" => $subject,
                        "project_name" => $project_name,
                        "message" => $message,
                        "attendee" => $attendee,
                        "location" => $location,
                        "start_time" => $start_time,
                        "end_time" => $end_time,
                        "is_enabled" => $is_enabled,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "deleted_at" => $deleted_at,
                        "created_by" => $created_by,
                        "updated_by" => $updated_by,
                        "deleted_by" => $deleted_by,
                        "items" => $items,
                        "attach" => $attach,

                        "color" => $color,
                        "color_other" => $color_other,
                        "text_color" => $text_color,
                        
                    );
    
                    $items = [];
                }
    
                $id = $row['id'];
                $subject = $row['subject'];
                $project_name = $row['project_name'];
                $message = $row['message'];
                $attendee = $row['attendee'];
                $location = $row['location'];
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];
                $is_enabled = $row['is_enabled'];

                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];
                $deleted_at = $row['deleted_at'];

                $created_by = $row['created_by'];
                $updated_by = $row['updated_by'];
                $deleted_by = $row['deleted_by'];

                $color = $row['color'];
                $color_other = $row['color_other'];
                $text_color = $row['text_color'];

                $attach = GetItem($row['id'], $db, 'meeting_notes');

                if(!empty($attendee ))
                    $items = GetUserInfo($row['attendee'], $db);
            }

            if ($id != 0) {
                $merged_results[] = array(
                    "id" => $id,
                    "subject" => $subject,
                    "project_name" => $project_name,
                    "message" => $message,
                    "attendee" => $attendee,
                    "location" => $location,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "is_enabled" => $is_enabled,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    "deleted_at" => $deleted_at,
                    "created_by" => $created_by,
                    "updated_by" => $updated_by,
                    "deleted_by" => $deleted_by,
                    "items" => $items,
                    "attach" => $attach,

                    "color" => $color,
                    "color_other" => $color_other,
                    "text_color" => $text_color,
                );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }
    else if($action == 2) {
        //add
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));

            $workCalenderNotes->subject = $subject;
            $workCalenderNotes->project_name = $project_name;
            $workCalenderNotes->message = $message;
            $workCalenderNotes->attendee = $attendee;
            $workCalenderNotes->location = $location;
            $workCalenderNotes->start_time = $start_time;
            $workCalenderNotes->end_time = $end_time;
            $workCalenderNotes->is_enabled = $is_enabled;
            $workCalenderNotes->created_by = $created_by;

            $workCalenderNotes->color = $color;
            $workCalenderNotes->color_other = $color_other;
            $workCalenderNotes->text_color = $text_color;

            $arr = $workCalenderNotes->create();


            $batch_id = $arr;
            $batch_type = 'meeting_notes';

            $_pic_url = "";
            $_real_url = "";

            if(isset($_FILES['files']['name']))
            {
                try {
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

                                $bucket = $storage->bucket('feliiximg');

                                $upload_name = pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                                $file_size = filesize($_FILES['files']['tmp_name'][$i]);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                    ['name' => $upload_name]);

                                $info = $obj->info();
                                $size = $info['size'];

                                if($size == $file_size && $file_size != 0 && $size != 0)
                                {
                                    $query = "INSERT INTO gcp_storage_file
                                    SET
                                        batch_id = :batch_id,
                                        batch_type = :batch_type,
                                        filename = :filename,
                                        gcp_name = :gcp_name,

                                        create_id = :create_id,
                                        created_at = now()";

                                    // prepare the query
                                    $stmt = $db->prepare($query);

                                    // bind the values
                                    $stmt->bindParam(':batch_id', $batch_id);
                                    $stmt->bindParam(':batch_type', $batch_type);
                                    $stmt->bindParam(':filename', $image_name);
                                    $stmt->bindParam(':gcp_name', $upload_name);

                                    $stmt->bindParam(':create_id', $user_id);

                                    try {
                                        // execute the query, also check if query was successful
                                        if ($stmt->execute()) {
                                            $last_id = $db->lastInsertId();
                                        } else {
                                            $arr = $stmt->errorInfo();
                                            error_log($arr[2]);
                                        }
                                    } catch (Exception $e) {
                                        error_log($e->getMessage());
     
                                        http_response_code(501);
                                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                                        die();
                                    }


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;

                                } else {
                                    $message = 'There is an error while uploading file';
              
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                    die();
                                }
                            } else {
                                $message = 'Only Images or Office files allowed to upload';
                       
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                die();
                            }
                        }
                    }
                } catch (Exception $e) {
             
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Error uploading, Please use laptop to upload again."));
                    die();
                }
            }

            http_response_code(200);
            echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa"), "id" => $arr));
            //echo json_encode(array("message" => " Add success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));

        }
    }else if($action == 3){
        //update
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderNotes->id = $id;
            $workCalenderNotes->subject = $subject;
            $workCalenderNotes->project_name = $project_name;
            $workCalenderNotes->message = $message;
            $workCalenderNotes->attendee = $attendee;
            $workCalenderNotes->location = $location;
            $workCalenderNotes->start_time = $start_time;
            $workCalenderNotes->end_time = $end_time;
            $workCalenderNotes->is_enabled = $is_enabled;
            $workCalenderNotes->updated_by = $updated_by;

            $workCalenderNotes->color = $color;
            $workCalenderNotes->color_other = $color_other;
            $workCalenderNotes->text_color = $text_color;

            $arr = $workCalenderNotes->update();

            if($remove != "")
            {
                // items to delete
                $query = "DELETE FROM gcp_storage_file
                    WHERE `batch_id` = " . $id ." AND filename in (" . $remove . ")";

                // prepare the query
                $stmt = $db->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
            
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
            
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
            }

            $batch_id = $id;
            $batch_type = 'meeting_notes';

            $_pic_url = "";
            $_real_url = "";

            if(isset($_FILES['files']['name']))
            {
                try {
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

                                $bucket = $storage->bucket('feliiximg');

                                $upload_name = pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                                $file_size = filesize($_FILES['files']['tmp_name'][$i]);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($_FILES['files']['tmp_name'][$i], 'r'),
                                    ['name' => $upload_name]);

                                $info = $obj->info();
                                $size = $info['size'];

                                if($size == $file_size && $file_size != 0 && $size != 0)
                                {
                                    $query = "INSERT INTO gcp_storage_file
                                    SET
                                        batch_id = :batch_id,
                                        batch_type = :batch_type,
                                        filename = :filename,
                                        gcp_name = :gcp_name,

                                        create_id = :create_id,
                                        created_at = now()";

                                    // prepare the query
                                    $stmt = $db->prepare($query);

                                    // bind the values
                                    $stmt->bindParam(':batch_id', $batch_id);
                                    $stmt->bindParam(':batch_type', $batch_type);
                                    $stmt->bindParam(':filename', $image_name);
                                    $stmt->bindParam(':gcp_name', $upload_name);

                                    $stmt->bindParam(':create_id', $user_id);

                                    try {
                                        // execute the query, also check if query was successful
                                        if ($stmt->execute()) {
                                            $last_id = $db->lastInsertId();
                                        } else {
                                            $arr = $stmt->errorInfo();
                                            error_log($arr[2]);
                                        }
                                    } catch (Exception $e) {
                                        error_log($e->getMessage());
     
                                        http_response_code(501);
                                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                                        die();
                                    }


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;

                                } else {
                                    $message = 'There is an error while uploading file';
              
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                    die();
                                }
                            } else {
                                $message = 'Only Images or Office files allowed to upload';
                       
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                die();
                            }
                        }
                    }
                } catch (Exception $e) {
             
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Error uploading, Please use laptop to upload again."));
                    die();
                }
            }


            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }else if($action == 4) {//未處理
        //select by date
        try{
            $query = "SELECT * from work_calendar_notes where is_enabled = true ";
            /*
            if($start_date!='') {
                $query = $query . " and paid_date >= '$start_date' ";
            }

            if($end_date!='') {
                $query = $query . " and paid_date <= '$end_date' ";
            }
            
            if($category!='') {
                $query = $query . " and category <= '$category' ";
            }
            
            if($sub_category!='') {
                $query = $query . " and sub_category <= '$sub_category' ";
            }
            */
            $query = $query . " order by created_at desc ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }else if($action == 5) {
        //get members
        try{
            $query = "SELECT * from user";
            $stmt = $db->prepare( $query );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }else if($action == 6){
        //select by id
        try{
            $query = "SELECT * from work_calendar_notes where id = ".$id;
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        }
        catch(Exception $e){
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    }else if($action == 7){
        //delete
        try {
            // decode jwt
            //$key = 'myKey';
            //$decoded = JWT::decode($jwt, $key, array('HS256'));
            $workCalenderNotes->id = $id;
            $workCalenderNotes->deleted_by = $deleted_by;
            $arr = $workCalenderNotes->delete();

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));

        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }
}



function GetUserInfo($users, $db)
{
    $psq = "";
    $a_users = explode(",", $users);

    foreach ($a_users as $value) {
        $psq .= "'" . $value . "',";
    }

    $psq = rtrim($psq, ",");

    $sql = "SELECT id, username FROM user WHERE username in (" . $psq . ")";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetItem($batch_id, $db, $type){
    $query = "
        
        SELECT f.id,
            coalesce(f.filename, '')   filename,
            coalesce(f.bucketname, '') bucket,
            coalesce(f.gcp_name, '')   gcp_name,
            u.username,
            f.created_at
        FROM   gcp_storage_file f

            LEFT JOIN user u
                ON u.id = f.create_id
        WHERE batch_id = " . $batch_id . "
        AND f.batch_type = '" . $type . "'
            AND f.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}