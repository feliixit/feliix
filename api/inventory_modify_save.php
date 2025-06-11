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
            
            $items = (isset($_POST['items']) ?  $_POST['items'] : '[]');
            $reason = (isset($_POST['reason']) ?  $_POST['reason'] : '');
            $which_pool = (isset($_POST['which_pool']) ?  $_POST['which_pool'] : '');
            $related_project = (isset($_POST['related_project']) ?  $_POST['related_project'] : 0);
            $as_sample = (isset($_POST['as_sample']) ?  $_POST['as_sample'] : '');
            $location = (isset($_POST['location']) ?  $_POST['location'] : 1);
            $stage = (isset($_POST['stage']) ?  $_POST['stage'] : 1);
            $id = (isset($_POST['id']) ?  $_POST['id'] : '0');
            $notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
            $receiver = (isset($_POST['receiver']) ?  $_POST['receiver'] : 0);
  
            if($related_project == "")
            {
                $related_project = 0;
            }

            if($receiver == "")
            {
                $receiver = 0;
            }

            try {
                $query = "update inventory_modify
                set    

                reason = :reason,
                note_1 = :notes,
                receive_id = :receiver,
                which_pool = :which_pool,
                as_sample = :as_sample,
                location = :location,
                project_id = :related_project,
                listing = :items,
                updated_id = :updated_id,
                status = :stage,
                updated_at = now()
                where id = :id";
            
                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':reason', $reason);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':receiver', $receiver);
                $stmt->bindParam(':which_pool', $which_pool);
                $stmt->bindParam(':as_sample', $as_sample);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':related_project', $related_project);
                $stmt->bindParam(':items', $items);
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':stage', $stage);
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
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }

                $batch_id = $id;
                $batch_type = "inventory_modify";

                try {
                    // count if there is any file
                    if(isset($_FILES['transmittal_file']['name']))
                    {
                        $total = count($_FILES['transmittal_file']['name']);
                    }
                    else
                    {
                        $total = 0;
                    }
                    // Loop through each file
                    for( $i=0 ; $i < $total ; $i++ ) {

                        if(isset($_FILES['transmittal_file']['name'][$i]))
                        {
                            $image_name = $_FILES['transmittal_file']['name'][$i];
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

                                $file_size = filesize($_FILES['transmittal_file']['tmp_name'][$i]);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($_FILES['transmittal_file']['tmp_name'][$i], 'r'),
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


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;
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

                    }
                } catch (Exception $e) {
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                    die();
                }

                // // items to delete
                // for ($i = 0; $i < count($items_array); $i++) {
                //     $query = "DELETE FROM gcp_storage_file
                //         WHERE
                //             `id` = :_id";

                //     // prepare the query
                //     $stmt = $db->prepare($query);

                //     // bind the values
                //     $stmt->bindParam(':_id', $items_array[$i]);

                //     try {
                //         // execute the query, also check if query was successful
                //         if (!$stmt->execute()) {
                //             $arr = $stmt->errorInfo();
                //             error_log($arr[2]);
                //             $db->rollback();
                //             http_response_code(501);
                //             echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                //             die();
                //         }
                //     } catch (Exception $e) {
                //         error_log($e->getMessage());
                //         $db->rollback();
                //         http_response_code(501);
                //         echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                //         die();
                //     }
                // }
            
                
                $db->commit();
                

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
        
        ?>