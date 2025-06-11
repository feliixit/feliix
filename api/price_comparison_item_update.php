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
        $id = (isset($_POST['id']) ?  $_POST['id'] : 0);

        $block = (isset($_POST['block']) ? $_POST['block'] : []);

        $block_array = json_decode($block,true);

      
        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        // update main table
        $query = "UPDATE price_comparison SET `updated_id` = :updated_id,  `updated_at` = now() WHERE id = :id";
        $stmt = $db->prepare($query);
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
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }


        $legend_id = $block_array['id'];
        // delete 
        for($i=0 ; $i < count($block_array['options']) ; $i++)
        {
            $option = $block_array['options'][$i];

            // delete previous item
            $query = "DELETE FROM price_comparison_item
            WHERE od_id = :od_id 
            AND legend_id = :legend_id
            and option_id = :option_id
            and `status` = -1";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':od_id', $id);
            $stmt->bindParam(':option_id', $option['id']);
            $stmt->bindParam(':legend_id', $legend_id);
    
        
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

            // update previous item to -1
            $query = "update price_comparison_item set status = -1
            WHERE od_id = :od_id 
            AND legend_id = :legend_id
            and option_id = :option_id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':od_id', $id);
            $stmt->bindParam(':option_id', $option['id']);
            $stmt->bindParam(':legend_id', $legend_id);
    
        
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
        

        $last_id = $id;

        $sn = 0;

        for($i=0 ; $i < count($block_array['options']) ; $i++)
        {
            $option = $block_array['options'][$i];

            for($j=0 ; $j < count($option['temp_block_a']) ; $j++)
            {
                $sn++;

                $temp_block_a = $option['temp_block_a'][$j];

                $_id = $temp_block_a['id'];

                // insert quotation_page_type_block
                $query = "INSERT INTO price_comparison_item
                SET
                    `od_id` = :od_id,
                    `option_id` = :option_id,
                    `legend_id` = :legend_id,
                    `sn` = :sn,
                    `code` = :code,
                    `brief` = :brief,
                    `list` = :list,
                    `qty` = :qty,
                    `price` = :price,
                    `ratio` = :ratio,
                    `notes` = :notes,
                    `discount` = :discount,
                    `amount` = :amount,
                    `desc` = :desc,
                    `v1` = :v1,
                    `v2` = :v2,
                    `v3` = :v3,
                    `v4` = :v4,
                    `ps_var` = :ps_var,
                    `pid` = :pid,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                if($temp_block_a['photo1'] !== '')
                {
                    $query .= ", `photo1` = :photo1";
                }

                if($temp_block_a['photo2'] !== '')
                {
                    $query .= ", `photo2` = :photo2";
                }

                if($temp_block_a['photo3'] !== '')
                {
                    $query .= ", `photo3` = :photo3";
                }

                // prepare the query
                $stmt = $db->prepare($query);

                $legend_id = $temp_block_a['legend_id'];
                $code = $temp_block_a['code'];
                $brief = $temp_block_a['brief'];
                $list = $temp_block_a['list'];
                $qty = isset($temp_block_a['qty']) ? $temp_block_a['qty'] : 0;
                $price = isset($temp_block_a['price']) ? $temp_block_a['price'] : 0;
            
                $amount = isset($temp_block_a['amount']) ? $temp_block_a['amount'] : 0;
                $ratio = isset($temp_block_a['ratio']) ? $temp_block_a['ratio'] : 1.0;
                $notes = isset($temp_block_a['notes']) ? $temp_block_a['notes'] : '';
                $discount = isset($temp_block_a['discount']) ? $temp_block_a['discount'] : 0;
                $desc = $temp_block_a['desc'];
                $v1 = $temp_block_a['v1'];
                $v2 = $temp_block_a['v2'];
                $v3 = $temp_block_a['v3'];
                $v4 = $temp_block_a['v4'];

                $ps_var = isset($temp_block_a['ps_var']) ? $temp_block_a['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                $pid = $temp_block_a['pid'];

                $qty == '' ? $qty = 0 : $qty = $qty;
                $ratio == '' ? $ratio = 1.0 : $ratio = $ratio;

                $discount == '' ? $discount = 0 : $discount = $discount;

                $price == '' ? $price = 0 : $price = $price;
            
                $amount == '' ? $amount = 0 : $amount = $amount;

                // bind the values
                $stmt->bindParam(':od_id', $last_id);
                $stmt->bindParam(':option_id', $option['id']);
                $stmt->bindParam(':legend_id', $temp_block_a['legend_id']);
                $stmt->bindParam(':sn', $sn);

                $stmt->bindParam(':code', $code);
                $stmt->bindParam(':brief', $brief);
                $stmt->bindParam(':list', $list);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':ratio', $ratio);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':discount', $discount);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':desc', $desc);
                $stmt->bindParam(':v1', $v1);
                $stmt->bindParam(':v2', $v2);
                $stmt->bindParam(':v3', $v3);
                $stmt->bindParam(':v4', $v4);
                $stmt->bindParam(':ps_var', $json_ps_var);
                $stmt->bindParam(':pid', $pid);

                
                $stmt->bindParam(':create_id', $user_id);
                
                if($temp_block_a['photo1'] !== '')
                {
                    $stmt->bindParam(':photo1', $temp_block_a['photo1']);
                }
                if($temp_block_a['photo2'] !== '')
                {
                    $stmt->bindParam(':photo2', $temp_block_a['photo2']);
                }
                if($temp_block_a['photo3'] !== '')
                {
                    $stmt->bindParam(':photo3', $temp_block_a['photo3']);
                }
            
                $block_id = 0;
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

                

                $batch_id = $block_id;
                $batch_type = "price_image";

                $key = "photo_" . $_id . "_1";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("1", $update_name, $batch_id, $db);
                }

                $key = "photo_" . $_id . "_2";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("2", $update_name, $batch_id, $db);
                }

                $key = "photo_" . $_id . "_3";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation("3", $update_name, $batch_id, $db);
                }
            } 
        }

        $db->commit();
        

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


function UpdateImageNameVariation($sn, $upload_name, $batch_id, $db){
    
    $query = "update price_comparison_item
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
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}
