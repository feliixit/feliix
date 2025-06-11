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

        $type_id = (isset($_POST['type_id']) ? $_POST['type_id'] : 0);
        $block = (isset($_POST['block']) ? $_POST['block'] : []);

        $pageless = (isset($_POST['pageless']) ? $_POST['pageless'] : '');

        $block_array = json_decode($block,true);

        

        if ($id == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        $last_id = $id;

        // delete previous -1
        $query = "delete from soa_quotation_page_type_block 
                    WHERE
                    `quotation_id` = :quotation_id
                    AND `status` = -1 and
                    `type_id` = :type_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':quotation_id', $last_id);
        $stmt->bindParam(':type_id', $type_id);

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
    
        // quotation_page
        $query = "UPDATE soa_quotation_page_type_block set `status` = -1
                    WHERE
                    `quotation_id` = :quotation_id
                    AND 
                    `type_id` = :type_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':quotation_id', $last_id);
        $stmt->bindParam(':type_id', $type_id);

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

        try {
            for($i=0 ; $i < count($block_array) ; $i++)
            {
                // insert quotation_page_type_block
                $query = "INSERT INTO soa_quotation_page_type_block
                SET
                    `quotation_id` = :quotation_id,
                    `type_id` = :type_id,
                    `code` = :code,
                    `type` = :type,
                    `qty` = :qty,
                    `ratio` = :ratio,
                    `price` = :price,
                    `discount` = :discount,
                    `amount` = :amount,
                    `description` = :description,
                    `v1` = :v1,
                    `v2` = :v2,
                    `v3` = :v3,
                    `v4` = :v4,
                    `ps_var` = :ps_var,
                    `listing` = :listing,
                    `num` = :num,
                    `pid` = :pid,
                    `notes` = :notes,
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                if($block_array[$i]['url'] !== '')
                {
                    $query .= ", `photo` = :photo";
                }
                else
                {
                    $query .= ", `photo` = ''";
                }

                if($block_array[$i]['url2'] !== '')
                {
                    $query .= ", `photo2` = :photo2";
                }
                else
                {
                    $query .= ", `photo2` = ''";
                }

                if($block_array[$i]['url3'] !== '')
                {
                    $query .= ", `photo3` = :photo3";
                }
                else
                {
                    $query .= ", `photo3` = ''";
                }

                // prepare the query
                $stmt = $db->prepare($query);

                $qty = isset($block_array[$i]['qty']) ? $block_array[$i]['qty'] : 0;
                $ratio = isset($block_array[$i]['ratio']) ? $block_array[$i]['ratio'] : 1.0;
                $price = isset($block_array[$i]['price']) ? $block_array[$i]['price'] : 0;
                $discount = isset($block_array[$i]['discount']) ? $block_array[$i]['discount'] : 0;
                $amount = isset($block_array[$i]['amount']) ? $block_array[$i]['amount'] : 0;
                $description = isset($block_array[$i]['desc']) ? $block_array[$i]['desc'] : '';

                $v1 = isset($block_array[$i]['v1']) ? $block_array[$i]['v1'] : '';
                $v2 = isset($block_array[$i]['v2']) ? $block_array[$i]['v2'] : '';
                $v3 = isset($block_array[$i]['v3']) ? $block_array[$i]['v3'] : '';
                $v4 = isset($block_array[$i]['v4']) ? $block_array[$i]['v4'] : '';

                $ps_var = isset($block_array[$i]['ps_var']) ? $block_array[$i]['ps_var'] : [];
                $json_ps_var = json_encode($ps_var);

                $listing = isset($block_array[$i]['list']) ? $block_array[$i]['list'] : '';
                $num = isset($block_array[$i]['num']) ? $block_array[$i]['num'] : '';
                $pid = isset($block_array[$i]['pid']) ? $block_array[$i]['pid'] : 0;
                $notes = isset($block_array[$i]['notes']) ? $block_array[$i]['notes'] : '';

                $qty == '' ? $qty = 0 : $qty = $qty;
                $ratio == '' ? $ratio = 1.0 : $ratio = $ratio;
                $price == '' ? $price = 0 : $price = $price;
                $discount == '' ? $discount = 0 : $discount = $discount;
                $amount == '' ? $amount = 0 : $amount = $amount;

                // bind the values
                $stmt->bindParam(':quotation_id', $last_id);
                $stmt->bindParam(':type_id', $type_id);
                $stmt->bindParam(':code', $block_array[$i]['code']);
                $stmt->bindParam(':type', $block_array[$i]['type']);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':ratio', $ratio);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':discount', $discount);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':v1', $v1);
                $stmt->bindParam(':v2', $v2);
                $stmt->bindParam(':v3', $v3);
                $stmt->bindParam(':v4', $v4);
                $stmt->bindParam(':ps_var', $json_ps_var);
                $stmt->bindParam(':listing', $listing);
                $stmt->bindParam(':num', $num);
                $stmt->bindParam(':pid', $pid);
                $stmt->bindParam(':notes', $notes);
                
                $stmt->bindParam(':create_id', $user_id);
                
                if($block_array[$i]['url'] !== '')
                {
                    $stmt->bindParam(':photo', $block_array[$i]['photo']);
                }
                if($block_array[$i]['url2'] !== '')
                {
                    $stmt->bindParam(':photo2', $block_array[$i]['photo2']);
                }
                if($block_array[$i]['url3'] !== '')
                {
                    $stmt->bindParam(':photo3', $block_array[$i]['photo3']);
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

                $_id = $block_array[$i]['id'];

                $batch_id = $block_id;
                $batch_type = "soa_block_image";

                $key = "block_image_" . $_id . "_1";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation($update_name, $batch_id, $db);
                }

                $key = "block_image_" . $_id . "_2";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation2($update_name, $batch_id, $db);
                }

                $key = "block_image_" . $_id . "_3";
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation3($update_name, $batch_id, $db);
                }

            }

            // update quotation_page_type.real_amount
            $query = "UPDATE soa_quotation_page_type p,( SELECT type_id, sum(amount)  as mysum FROM soa_quotation_page_type_block where `status` <> -1 GROUP BY type_id ) as s
                    SET p.real_amount = s.mysum
                    WHERE p.id = s.type_id
                    and p.quotation_id = :id
                    and p.id = :type_id";

            // prepare the query
            $stmt = $db->prepare($query);

            $stmt->bindParam(':id', $last_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_id', $type_id, PDO::PARAM_INT);

            // execute the query, also check if query was successful
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

            $query = "UPDATE soa_quotation SET `pageless` = :pageless WHERE `id` = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pageless', $pageless);
            $stmt->bindParam(':id', $last_id);
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
            
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
    
    $query = "update soa_quotation_page_type_block
    SET photo = :gcp_name where id=:id";

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

function UpdateImageNameVariation2($upload_name, $batch_id, $db){
    
    $query = "update soa_quotation_page_type_block
    SET photo2 = :gcp_name where id=:id";

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

function UpdateImageNameVariation3($upload_name, $batch_id, $db){
    
    $query = "update soa_quotation_page_type_block
    SET photo3 = :gcp_name where id=:id";

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
