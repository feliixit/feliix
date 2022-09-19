<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
include_once 'mail.php';
use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = 0;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $GLOBALS["user_id"] = $decoded->data->id;

        $user_name = $decoded->data->username;
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
    case "GET":
        $task_id = (isset($_GET['id']) ?  $_GET['id'] : 0);
        $notes = [];
        $notes = GetNotes($task_id, $db);
        $jsonEncodedReturnArray = json_encode($notes, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
        break;
    
    case 'POST':
        // get database connection
        $uid = $user_id;
        $task_id = (isset($_POST['task_id']) ?  $_POST['task_id'] : 0);
        $message = (isset($_POST['message']) ?  $_POST['message'] : '');

        $item = (isset($_POST['item']) ?  $_POST['item'] : []);
        $items = json_decode($item, true);

        $od_id = (isset($_POST['od_id']) ?  $_POST['od_id'] : 0);
        $od_name = (isset($_POST['od_name']) ? $_POST['od_name'] : '');
        $serial_name = (isset($_POST['serial_name']) ?  $_POST['serial_name'] : '');
        $project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');

        $page = (isset($_POST['page']) ?  $_POST['page'] : 1);

        $access1 = (isset($_POST['access1']) ?  $_POST['access1'] : false);
        $access2 = (isset($_POST['access2']) ?  $_POST['access2'] : false);
        $access3 = (isset($_POST['access3']) ?  $_POST['access3'] : false);
        $access4 = (isset($_POST['access4']) ?  $_POST['access4'] : false);
        $access5 = (isset($_POST['access5']) ?  $_POST['access5'] : false);
        $access6 = (isset($_POST['access6']) ?  $_POST['access6'] : false);
        $access7 = (isset($_POST['access7']) ?  $_POST['access7'] : false);
    
        $query = "INSERT INTO od_message
        SET
            `item_id` = :task_id,
            `message` = :message,
          
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':message', $message);
       
        $stmt->bindParam(':create_id', $uid);

        $last_id = 0;
        // execute the query, also check if query was successful
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
        }

        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

        $send17 = false;

        if($page == 1) {
            if($items['status'] != 1)
            {
                if($access2 == true && ($items['confirm'] != 'D' || $items['confirm'] != 'C' || $items['confirm'] != 'N'))
                    $send17 = true;
            }
    
            if($items['status'] == 1)
            {
                if($access1 == true && $items['confirm'] != 'D' && $items['confirm'] != 'C' && $items['confirm'] != 'N')
                    $send17 = true;
            }
    
            if($items['status'] == 1)
            {
                if($access7 == true && $items['confirm'] != 'D' && $items['confirm'] != 'C' && $items['confirm'] != 'N')
                    $send17 = true;
            }
    
            if($access3 == true && $access4 == true && $access5 == true && $access6 == true)
                $send17 = true;
    
            if($send17==true)
                order_notification03($user_name, 'access1,access2,access7', '', $project_name, $serial_name, $od_name, 'Order - Taiwan', $message, 'new_message_17', $items, $od_id);
        }

        if($page == 2)
        order_notification03($user_name, 'access1,access2,access3', '', $project_name, $serial_name, $od_name, 'Order - Taiwan', $message, 'new_message_19', $items, $od_id);
        
        echo $jsonEncodedReturnArray;

        break;
}


function GetNotes($id, $db){

    $query = "
            SELECT n.id,
                n.status,
                n.message,
                n.create_id,
                u.username,
                n.created_at
            FROM   od_message n
            left join user u on n.create_id = u.id
            WHERE  n.item_id = " . $id . "
            ORDER BY n.id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $status = $row['status'];
        $message = $row['message'];
        $create_id = $row['create_id'];
        $username = $row['username'];

        $created_at = $row['created_at'];
      
        $attachs = [];
        $got_it = [];

        $attachs = GetAttach($id, $db);
        $got_it = GetGotIt($id, $db);
        $i_got_it = false;

        foreach ($got_it as $g) {
            if ($g['uid'] == $GLOBALS["user_id"]) {
                $i_got_it = true;
                break;
            }
        }

        if($GLOBALS["user_id"] == $row["create_id"])
            $i_got_it = true;
    
        $merged_results[] = array(
            "id" => $id,
            "status" => $status,
            "message" => $message,
            "create_id" => $create_id,
            "username" => $username,
            "created_at" => $created_at,
            "attachs" => $attachs,
            "got_it" => $got_it,
            "i_got_it" => $i_got_it,
        );
    }

    return $merged_results;
}



function GetAttach($id, $db)
{
    $sql = "select COALESCE(filename, '') filename, COALESCE(gcp_name, '') gcp_name
            from gcp_storage_file where batch_id = " . $id . " and batch_type = 'od_message' 
            order by created_at ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = array(
            "filename" => $row['filename'],
            "gcp_name" => $row['gcp_name'],
        );
    }

    return $result;
}


function GetGotIt($msg_id, $db)
{
    $sql = "select  u.id uid, u.username username
            from od_got_it g
            LEFT JOIN user u ON u.id = g.create_id
            where g.message_id = " . $msg_id . " order by g.created_at";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $got_it = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $got_it[] = array(
            "uid" => $row['uid'],
            "username" => $row['username'],
        );
    }

    return $got_it;
}