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
include_once 'mail.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);
    
    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
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
            
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }
    }
    
    header('Access-Control-Allow-Origin: *');
    
    include_once 'config/database.php';
    
    switch ($method) {
        
        case 'POST':
            
            $database = new Database();
            $db = $database->getConnection();
            $db->beginTransaction();
            $conf = new Conf();
            
            $id = (isset($_POST['id']) ? $_POST['id'] : '');
            
            $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
            
            $topic = (isset($_POST['topic']) ? $_POST['topic'] : '');
            $start_date = (isset($_POST['start_date']) ? $_POST['start_date'] : '');
            $end_date = (isset($_POST['end_date']) ? $_POST['end_date'] : '');
            $access = (isset($_POST['access']) ? $_POST['access'] : "");
            
            $rule = (isset($_POST['rule']) ? $_POST['rule'] : '');
            $display = (isset($_POST['display']) ? $_POST['display'] : '');
            $sort = (isset($_POST['sort']) ? $_POST['sort'] : '');
            
            $blocks = (isset($_POST['blocks']) ? $_POST['blocks'] : []);
            
            $block_array = json_decode($blocks,true);
            
            
            $last_id = $id;
            
            // insert into voting_template
            $query = "update voting_template
            SET
            `topic` = :topic,
            `access` = :access,
            `start_date` = :start_date,
            `end_date` = :end_date,
            `rule` = :rule,
            `display` = :display,
            `sort` = :sort,
            `status` = 0,
            `updated_id` = :updated_id,
            `updated_at` = now()
            WHERE id = :id";
            
            // prepare the query
            $stmt = $db->prepare($query);
            
            // bind the values
            $stmt->bindParam(':topic', $topic);
            $stmt->bindParam(':access', $access);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':rule', $rule);
            $stmt->bindParam(':display', $display);
            $stmt->bindParam(':sort', $sort);
            $stmt->bindParam(':updated_id', $user_id);
            $stmt->bindParam(':id', $id);
            
            
            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
                $last_id = $db->lastInsertId();
                
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
            
            try{
                $sn = 0;
                // select old voting_template_detail and compare with new voting_template_detail
                $query = "select * from voting_template_detail where template_id = :template_id and status <> -1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':template_id', $id);
                $stmt->execute();
                $old_voting_template_detail = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // if new voting_template_detail is not in old voting_template_detail, insert it
                for($i=0 ; $i < count($block_array) ; $i++)
                {
                    $sn = $sn + 1;
                    
                    $is_exist = false;
                    for($j=0 ; $j < count($old_voting_template_detail) ; $j++)
                    {
                        if($block_array[$i]['id'] == $old_voting_template_detail[$j]['id'])
                        {
                            $is_exist = true;
                            break;
                        }
                    }
                    
                    
                    if(!$is_exist)
                    {
                        $block_id = 0;

                        $query = "insert into voting_template_detail
                        SET
                        `template_id` = :template_id,
                        `sn` = :sn, 
                        `title` = :title,
                        `description` = :description,
                        `link` = :link,
                        `status` = 0,
                        `create_id` = :create_id,
                        `created_at` = now()";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        // bind the values
                        $stmt->bindParam(':template_id', $id);
                        $stmt->bindParam(':sn', $sn);
                        $stmt->bindParam(':title', $block_array[$i]['title']);
                        $stmt->bindParam(':description', $block_array[$i]['description']);
                        $stmt->bindParam(':link', $block_array[$i]['link']);
                        $stmt->bindParam(':create_id', $user_id);

                        $url = isset($block_array[$i]['url']) ? $block_array[$i]['url'] : '';
                        
                        try {
                            // execute the query, also check if query was successful
                            if ($stmt->execute()) {
                                $block_id = $db->lastInsertId();
                            } else {
                                $arr = $stmt->errorInfo();
                                error_log($arr[2]);
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                                die();
                            }
                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                            die();
                        }

                        $_id = $block_array[$i]['id'];

                        $batch_id = $block_id;
                        $batch_type = "voting_image";

                        $key = "file" . $_id;
                        if (array_key_exists($key, $_FILES) && $url != '')
                        {
                            $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                            if($update_name != "")
                                UpdateImageNameVariation($update_name, $batch_id, $db);
                        }
                    }
                }
                // if old voting_template_detail is not in new voting_template_detail, delete it
                for($i=0 ; $i < count($old_voting_template_detail) ; $i++)
                {
                    $is_exist = false;
                    for($j=0 ; $j < count($block_array) ; $j++)
                    {
                        if($old_voting_template_detail[$i]['id'] == $block_array[$j]['id'])
                        {
                            $is_exist = true;
                            break;
                        }
                    }
                    
                    if(!$is_exist)
                    {
                        $query = "delete from voting_template_detail where id = :id";
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        
                        // bind the values
                        $stmt->bindParam(':id', $old_voting_template_detail[$i]['id']);
                        
                        try {
                            // execute the query, also check if query was successful
                            if (!$stmt->execute()) {
                                $arr = $stmt->errorInfo();
                                error_log($arr[2]);
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                                die();
                            }
                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                            die();
                        }
                    }
                }
                
                $sn = 0;
                // if old voting_template_detail is in new voting_template_detail, update it
                for($i=0 ; $i < count($block_array) ; $i++)
                {
                    $sn = $sn + 1;
                    
                    $is_exist = false;
                    for($j=0 ; $j < count($old_voting_template_detail) ; $j++)
                    {
                        if($block_array[$i]['id'] == $old_voting_template_detail[$j]['id'])
                        {
                            $is_exist = true;
                            break;
                        }
                    }
                    
                    if($is_exist)
                    {
                        $query = "update voting_template_detail
                        SET
                        `sn` = :sn, ";

                        if($block_array[$i]['url'] == '')
                        {
                            $query = $query . "`pic` = '', ";
                        }

                        $query = $query . "
                        `title` = :title,
                        `description` = :description,
                        `link` = :link,
                        `status` = 0,
                        `updated_id` = :updated_id,
                        `updated_at` = now()
                        where id = :id";
                        
                        $title = isset($block_array[$i]['title']) ? $block_array[$i]['title'] : '';
                        $description = isset($block_array[$i]['description']) ? $block_array[$i]['description'] : '';
                        $link = isset($block_array[$i]['link']) ? $block_array[$i]['link'] : '';
                        
                        $url = isset($block_array[$i]['url']) ? $block_array[$i]['url'] : '';
                        
                        // prepare the query
                        $stmt = $db->prepare($query);
                        // bind the values
                        $stmt->bindParam(':sn', $sn);
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':link', $link);
                        $stmt->bindParam(':updated_id', $user_id);
                        $stmt->bindParam(':id', $block_array[$i]['id']);

                        try {
                            // execute the query, also check if query was successful
                            if (!$stmt->execute()) {
                                $arr = $stmt->errorInfo();
                                error_log($arr[2]);
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                                die();
                            }
                        } catch (Exception $e) {
                            error_log($e->getMessage());
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                            die();
                        }
                        
                        $_id = $block_array[$i]['id'];

                        $batch_id = $_id;
                        $batch_type = "voting_image";

                        $key = "file" . $_id;
                        if (array_key_exists($key, $_FILES) && $url != '')
                        {
                            $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                            if($update_name != "")
                                UpdateImageNameVariation($update_name, $batch_id, $db);
                        }
                    }
                }
                
                $db->commit();

                send_mail($id, $db);
                
                http_response_code(200);
                echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
            } catch (Exception $e) {
                
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
            break;
        }
        
        
        
        function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
        {
            try {
                if($_FILES[$type]['name'] == null)
                return "";
                // Loop through each file
                
                if(isset($_FILES[$type]['name']))
                {
                    $image_name = $_FILES[$type]['name'];
                    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo","dwf","bmp");
                    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), $valid_extensions)) 
                    {
                        //$upload_path = 'img/' . time() . '.' . $extension;
                        
                        $storage = new StorageClient([
                            'projectId' => 'predictive-fx-284008',
                            'keyFilePath' => $conf::$gcp_key
                        ]);
                        
                        $bucket = $storage->bucket('feliiximg');
                        
                        $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;
                        
                        $file_size = filesize($_FILES[$type]['tmp_name']);
                        $size = 0;
                        
                        $obj = $bucket->upload(
                            fopen($_FILES[$type]['tmp_name'], 'r'),
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
                                    $db->rollback();
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                                    die();
                                }
                                
                                return $upload_name;
                            }
                            else
                            {
                                $message = 'There is an error while uploading file';
                                $db->rollback();
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                die();
                                
                            }
                        }
                        else
                        {
                            $message = 'Only Images or Office files allowed to upload';
                            $db->rollback();
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                            die();
                        }
                    }
                    
                    
                } catch (Exception $e) {
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                    die();
                }
            }
            
            
            function UpdateImageNameVariation($upload_name, $batch_id, $db){
                
                $query = "update voting_template_detail
                SET pic = :gcp_name where id=:id";
                
                // prepare the query
                $stmt = $db->prepare($query);
                
                // bind the values
                $stmt->bindParam(':id', $batch_id);
                
                $stmt->bindParam(':gcp_name', $upload_name);
                
                
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
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
            }
            
            
function send_mail($_id, $db)
{
    // get today by "Y-m-d"
    $today = date("Y-m-d");

    $sql = "select pm.id,
                pm.topic, 
                pm.access, 
                pm.start_date, 
                pm.end_date, 
                pm.rule, 
                pm.`status`,
                pm.create_id,
                c_user.username AS created_by, 
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at 
            from voting_template pm 
            LEFT JOIN user c_user ON pm.create_id = c_user.id where pm.status <> -1 and pm.id = " . $_id;

    $stmt = $db->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $username = $row['created_by'];
        $created_at = $row['created_at'];
        $topic = $row['topic'];
        $review_start_date = $row['start_date'];
        $review_end_date = $row['end_date'];
        $od_id = $row['id'];
        $cc_id = $row['create_id'];
        $rule = $row['rule'];

        $status_text = GetVotingStatus($review_start_date, $review_end_date);

        // if today is review_start_date, send email
        if ("Ongoing" == $status_text)
        {
            // json to array
            $access = json_decode($row['access'], true);
            // convert to string
            $access_str = implode(",", $access);
            $receiver = knowledge_access_get($access_str, $db);

            batch_voting_system_notify_mail($receiver, $cc_id, $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "start");
        }

        // if today is review_start_date, send email
        if ("Finished" == $status_text)
        {
            // json to array
            $access = json_decode($row['access'], true);
            // convert to string
            $access_str = implode(",", $access);
            $receiver = knowledge_access_get($access_str, $db);

            batch_voting_system_notify_mail($receiver, "",  $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "end");
            batch_voting_system_notify_mail($cc_id, "",  $topic, $username, $created_at, $review_start_date, $review_end_date,  GetRuleText($rule), $od_id, "mgt");
        }
    }
}

function GetVotingStatus($start_date, $end_date)
{
    $status = "";
    $now = date("Y-m-d");
    if($now < $start_date)
        $status = "Not Yet Start";
    else if($now >= $start_date && $now <= $end_date)
        $status = "Ongoing";
    else if($now > $end_date)
        $status = "Finished";

    return $status;
}

function knowledge_access_get($access, $db)
{
    $users = array();

    $query = "select `user`.id,
                username , email, department from `user` 
            left join `user_department` on `user`.apartment_id = `user_department`.id
            where `user`.status = 1";

    $username = "";
    $email = "";
    $department = "";

    $access_up = strtoupper($access); 

    $stmt_cnt = $db->prepare( $query );
    $stmt_cnt->execute();
    while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
        $uid = $row['id'];
        $username = $row['username'];
        $email = $row['email'];
        $department = $row['department'];

        // if username or department part of access then add to uses
        if(strpos($access_up, strtoupper($username)) !== false || strpos($access_up, strtoupper($department)) !== false || strpos($access_up, "ALL") !== false)
        {
            $users[] = $uid;
        }
    }

    return implode(",", $users);
}

function GetRuleText($rule)
{
    $rule_text = "";

    if($rule == "1")
        $rule_text = "one person - one vote";
    else if($rule == "2")
        $rule_text = "one person - two votes";
    else if($rule == "3")
        $rule_text = "one person - three votes";

    return $rule_text;
}