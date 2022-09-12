<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/conf.php';
include_once 'config/database.php';


use Google\Cloud\Storage\StorageClient;


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
$conf = new Conf();

switch ($method) {
 
    case 'POST':
        // get database connection

        $message = "snapshot";
        $batch_type = "od_message";

        $uid = $user_id;

        $data = json_decode(file_get_contents('php://input'), true);
        
        $item_id = $data['item_id'];
        $img = $data['image'];
        $text = $data['text'];

        if($text != "")
            $message = $text;

        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        if($img != "")
            $fileData = base64_decode($img);
    
        $query = "INSERT INTO od_message
        SET
            `item_id` = :task_id,
            `message` = :message,
          
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':task_id', $item_id);
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

        try {
            if (isset($fileData)) {

                $key = "myKey";
                $time = time();
                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                $ext = "jpg";
                $filename = $time . $hash . "." . $ext;

                $storage = new StorageClient([
                    'projectId' => 'predictive-fx-284008',
                    'keyFilePath' => $conf::$gcp_key
                ]);
        
                $bucket = $storage->bucket('feliiximg');
        
                $upload_name = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;

                $image_name = "snapshot.jpg";


                $obj = $bucket->upload(
                    $fileData,
                    ['name' => $upload_name]);

                $info = $obj->info();
                $size = $info['size'];

                if($size)
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
                    $stmt->bindParam(':batch_id', $last_id);
                    $stmt->bindParam(':batch_type', $batch_type);
                    $stmt->bindParam(':filename', $image_name);
                    $stmt->bindParam(':gcp_name', $upload_name);
        
                    $stmt->bindParam(':create_id', $user_id);

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = $db->lastInsertId();
                        }
                        else
                        {
                            $arr = $stmt->errorInfo();
                            error_log($arr[2]);
                        }
                    }
                    catch (Exception $e)
                    {
                        error_log($e->getMessage());
                    }

                }
                else
                {
                    $code = 502;
                    $message = 'There is an error while uploading file';
                    $image = $image_name;
                }
            }
        }catch (Exception $e){

            //http_response_code(401);

            //echo json_encode(array("message" => "Access denied."));
            //die();
        }

        $returnArray = array('batch_id' => $last_id);
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);

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