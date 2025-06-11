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

// include_once 'mail.php';

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
            
            $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
            
            $petty_list = (isset($_POST['level1']) ?  $_POST['level1'] : '[]');
            $petty_array = json_decode($petty_list, true);
            $code = isset($_POST['code']) ? $_POST['code'] : '';
            
            try {
                //$pre_array = GetPreTags($db);

                //$diff = show_diff($pre_array, $petty_array);

                // delete previous data
                $query = "delete from office_items_description where status = -1 and parent_code = '$code'";
                $stmt = $db->prepare($query);
                $stmt->execute();

                // petty_list
                $query = "update office_items_description
                set status = -1 where parent_code = '$code'";
                
                
                // prepare the query
                $stmt = $db->prepare($query);
                
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

                $batch_type = "office_items";

                for ($i = 0; $i < count($petty_array); $i++) {

                    $upload_name = $petty_array[$i]['photo'];
                    
                    $sn = $i+1;
                    $batch_id = $sn;

                    $file_name = $petty_array[$i]['photo'];
                    if($file_name != '')    
                    {
                        $file_name = str_replace(".", "_", $file_name);
                        $file_name = str_replace(" ", "_", $file_name);

                        try {

                            if (isset($_FILES[$file_name]['name'])) {
                                $image_name = $_FILES[$file_name]['name'];

                                if($image_name != $petty_array[$i]['photo'])
                                {
                                    continue;
                                }

                                $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
                                $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                                if (in_array(strtolower($extension), $valid_extensions)) {
                                    //$upload_path = 'img/' . time() . '.' . $extension;

                                    $storage = new StorageClient([
                                        'projectId' => 'predictive-fx-284008',
                                        'keyFilePath' => $conf::$gcp_key
                                    ]);

                                    $bucket = $storage->bucket('feliiximg');

                                    $upload_name = time() . '_' .  pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                                    $file_size = filesize($_FILES[$file_name]['tmp_name']);
                                    $size = 0;

                                    $obj = $bucket->upload(
                                        fopen($_FILES[$file_name]['tmp_name'], 'r'),
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
                        
                        } catch (Exception $e) {
                    
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Error uploading, Please use laptop to upload again."));
                            die();
                        }
                        
                    }
                    
                    $query = "INSERT INTO office_items_description
                    SET
                    `sn` = :sn,
                    `code` = :code,
                    `category` = :category,
                    `parent_code` = '$code',
                    `photo` = :photo,
                    `create_id` = :create_id,
                    `created_at` = now()";
                    
                    // prepare the query
                    $stmt = $db->prepare($query);
                    
                    // bind the values
                    $stmt->bindParam(':create_id', $user_id);
                    $stmt->bindParam(':code', $petty_array[$i]['code']);
                    $stmt->bindParam(':category', $petty_array[$i]['category']);
                    $stmt->bindParam(':photo', $upload_name);

                    $stmt->bindParam(':sn', $sn);
                    
                    
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
                
                $db->commit();

                
                
                // // 有不同才寄信
                // if(count($diff) > 0)
                //     tag_group_notification($user_name, $user_id, $diff);

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
        

        function GetPreTags($db) {
            $query = "SELECT id, sn, group_name from tag_group where status <> -1 order by sn";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }

            return $result;
        }

        function show_diff($pre_item, $item)
        {
            $diff = [];

            // 1. check if the item is new
            foreach ($item as $it) {
                if($it['id'] == 0)
                    $diff[] = "," . $it['group_name'];
            }
            // 2. check if the value is different
            foreach($pre_item as $it) {
                foreach($item as $i) {
                    if($it['id'] == $i['id'] && $it['group_name'] != $i['group_name'])
                        $diff[] =  $it['group_name'] . "," . $i['group_name'];
                }
            }
            // 3. check if the key is deleted
            foreach($pre_item as $it) {
                $found = false;
                foreach($item as $i) {
                    if($it['id'] == $i['id'])
                        $found = true;
                }
                if(!$found)
                    $diff[] = $it['group_name'] . ",";
            }

            return $diff;
        }