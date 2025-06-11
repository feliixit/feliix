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
    
            $conf = new Conf();
            
            $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
            
            $item = (isset($_POST['item']) ?  $_POST['item'] : []);
            
            $item_array = json_decode($item, true);
            
            // is exist in product_spec_sheet update it else insert it
            $id = isset($item_array['id']) ? $item_array['id'] : 0;
            
            if($id == 0)
            {
                $query = "INSERT INTO product_spec_sheet
                SET
                `product_id` = :product_id,
                `p_id` = :p_id,
                `code` = :code,
                `photo1` = :photo1,
                `photo2` = :photo2,
                `photo3` = :photo3,
                `photo4` = :photo4,
                `photo5` = :photo5,
                `photo6` = :photo6,
                `legend` = :legend,
                `option` = :option,
                `category` = :category,
                `indoor` = :indoor,
                `type` = :type,
                `grade` = :grade,
                `description` = :description,
                `variation` = :variation,
                `related_product` = :related_product,
                `reserved` = :reserved,
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";
                
                
                // prepare the query
                $stmt = $db->prepare($query);
                
                //$sn = isset($block_array[$i]['sn']) ? $block_array[$i]['sn'] : 0;
                $product_id = isset($item_array['product_id']) ? $item_array['product_id'] : 0;
                $p_id = isset($item_array['p_id']) ? $item_array['p_id'] : '';
                $code = isset($item_array['code']) ? $item_array['code'] : '';
                $photo1 = isset($item_array['photo1']) ? $item_array['photo1'] : '';
                $photo2 = isset($item_array['photo2']) ? $item_array['photo2'] : '';
                $photo3 = isset($item_array['photo3']) ? $item_array['photo3'] : '';
                $photo4 = isset($item_array['photo4']) ? $item_array['photo4'] : '';
                $photo5 = isset($item_array['photo5']) ? $item_array['photo5'] : '';
                $photo6 = isset($item_array['photo6']) ? $item_array['photo6'] : '';
                $legend = isset($item_array['legend']) ? $item_array['legend'] : '';
                $option = isset($item_array['option']) ? $item_array['option'] : '';
                $category = isset($item_array['category']) ? $item_array['category'] : '';
                $indoor = isset($item_array['indoor']) ? $item_array['indoor'] : '';
                $type = isset($item_array['type']) ? $item_array['type'] : '';
                $grade = isset($item_array['grade']) ? $item_array['grade'] : '';
                $description = isset($item_array['description']) ? $item_array['description'] : '';
                $variation = isset($item_array['variation']) ? $item_array['variation'] : '';
                $related_product = isset($item_array['related_product']) ? $item_array['related_product'] : '';
                $reserved = isset($item_array['reserved']) ? $item_array['reserved'] : '';
                
                // trim leading https://storage.googleapis.com/ultrax_bucket/
                $photo1 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo1);
                $photo2 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo2);
                $photo3 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo3);
                $photo4 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo4);
                $photo5 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo5);
                $photo6 = str_replace('https://storage.googleapis.com/feliiximg/', '', $photo6);

                $related_product_json = json_encode($related_product);
                $reserved_json = json_encode($reserved);
                
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':p_id', $p_id);
                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':photo1', $photo1);
                $stmt->bindParam(':photo2', $photo2);
                $stmt->bindParam(':photo3', $photo3);
                $stmt->bindParam(':photo4', $photo4);
                $stmt->bindParam(':photo5', $photo5);
                $stmt->bindParam(':photo6', $photo6);
                $stmt->bindParam(':legend', $legend);
                $stmt->bindParam(':option', $option);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':indoor', $indoor);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':grade', $grade);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':variation', $variation);
                $stmt->bindParam(':related_product', $related_product_json);
                $stmt->bindParam(':reserved', $reserved_json);
                
                $stmt->bindParam(':create_id', $user_id);
                
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $block_id = $db->lastInsertId();
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                     
                        http_response_code(501);
                        echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                 
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
                
                
                $_id = $block_id;
                
                $batch_type = "spec_item";
                $batch_id = $_id;
                
                $key = "photo_1";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("1", $update_name, $batch_id, $db);
                }
                
                $key = "photo_2";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("2", $update_name, $batch_id, $db);
                }
                
                $key = "photo_3";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("3", $update_name, $batch_id, $db);
                }
                
                $key = "photo_4";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("4", $update_name, $batch_id, $db);
                }

                $key = "photo_5";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("5", $update_name, $batch_id, $db);
                }

                $key = "photo_6";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("6", $update_name, $batch_id, $db);
                }

                
            }
            else
            {
                $query = "UPDATE product_spec_sheet
                SET
                `code` = :code,  ";
                
                if($item_array['photo1'] == '')
                {
                    $query .= " `photo1` = '', ";
                }
                
                if($item_array['photo2'] == '')
                {
                    $query .= " `photo2` = '', ";
                }
                
                if($item_array['photo3'] == '')
                {
                    $query .= " `photo3` = '', ";
                }

                if($item_array['photo4'] == '')
                {
                    $query .= " `photo4` = '', ";
                }

                if($item_array['photo5'] == '')
                {
                    $query .= " `photo5` = '', ";
                }

                if($item_array['photo6'] == '')
                {
                    $query .= " `photo6` = '', ";
                }
                
                
                $query .= "         
                `legend` = :legend,
                `option` = :option,
                `category` = :category,
                `indoor` = :indoor,
                `type` = :type,
                `grade` = :grade,
                `description` = :description,
                `variation` = :variation,
                `related_product` = :related_product,
                `reserved` = :reserved,
                updated_id = :updated_id,
                updated_at = now()
                where id = :id
                ";
                
                
                // prepare the query
                $stmt = $db->prepare($query);
                
                $code = isset($item_array['code']) ? $item_array['code'] : '';
                $legend = isset($item_array['legend']) ? $item_array['legend'] : '';
                $option = isset($item_array['option']) ? $item_array['option'] : '';
                $category = isset($item_array['category']) ? $item_array['category'] : '';
                $indoor = isset($item_array['indoor']) ? $item_array['indoor'] : '';
                $type = isset($item_array['type']) ? $item_array['type'] : '';
                $grade = isset($item_array['grade']) ? $item_array['grade'] : '';
                $description = isset($item_array['description']) ? $item_array['description'] : '';
                $variation = isset($item_array['variation']) ? $item_array['variation'] : '';
                $related_product = isset($item_array['related_product']) ? $item_array['related_product'] : '';
                $reserved = isset($item_array['reserved']) ? $item_array['reserved'] : '';

                $related_product_json = json_encode($related_product);
                $reserved_json = json_encode($reserved);
                
                $stmt->bindParam(':code', $code);

                $stmt->bindParam(':legend', $legend);
                $stmt->bindParam(':option', $option);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':indoor', $indoor);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':grade', $grade);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':variation', $variation);
                $stmt->bindParam(':related_product', $related_product_json);
                $stmt->bindParam(':reserved', $reserved_json);
                
                $stmt->bindParam(':updated_id', $user_id);
                $stmt->bindParam(':id', $id);
                
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        //$block_id = $db->lastInsertId();
                    } else {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                        http_response_code(501);
                        echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
                
                $_id = $id;
                
                $batch_type = "spec_item";
                $batch_id = $_id;
                
                $key = "photo_1";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("1", $update_name, $batch_id, $db);
                }
                
                $key = "photo_2";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("2", $update_name, $batch_id, $db);
                }
                
                $key = "photo_3";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("3", $update_name, $batch_id, $db);
                }

                $key = "photo_4";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("4", $update_name, $batch_id, $db);
                }

                $key = "photo_5";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("5", $update_name, $batch_id, $db);
                }

                $key = "photo_6";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                    UpdateImageNameVariation("6", $update_name, $batch_id, $db);
                }
            
                
            }
            
            
            
            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
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
                               
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                                die();
                            }
                            
                            return $upload_name;
                        }
                        else
                        {
                            $message = 'There is an error while uploading file';
                 
                            http_response_code(501);
                            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                            die();
                            
                        }
                    }
                    else
                    {
                        $message = 'Only Images or Office files allowed to upload';
                 
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                        die();
                    }
                }
                
                
            } catch (Exception $e) {
         
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                die();
            }
        }
        
        
        function UpdateImageNameVariation($sn, $upload_name, $batch_id, $db){
            
            $query = "update product_spec_sheet
            SET photo" . $sn . " = :gcp_name where id=:id";
            
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
          
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }
        