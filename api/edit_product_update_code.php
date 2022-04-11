<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ? $_POST['id'] : 0);
$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : '');
$code = (isset($_POST['code']) ?  $_POST['code'] : '');
$tags = (isset($_POST['tags']) ?  $_POST['tags'] : '');
$moq = (isset($_POST['moq']) ?  $_POST['moq'] : '');
$price_ntd = (isset($_POST['price_ntd']) ?  $_POST['price_ntd'] : '');
$price_org = (isset($_POST['price_org']) ?  $_POST['price_org'] : '');
$price_ntd_change = (isset($_POST['price_ntd_change']) ?  $_POST['price_ntd_change'] : '');
$price_ntd_org = (isset($_POST['price_ntd_org']) ?  $_POST['price_ntd_org'] : '');
$price = (isset($_POST['price']) ?  $_POST['price'] : '');
$price_change = (isset($_POST['price_change']) ?  $_POST['price_change'] : '');
$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$related_product = (isset($_POST['related_product']) ? $_POST['related_product'] : '');
$notes = (isset($_POST['notes']) ? $_POST['notes'] : '');

$quoted_price = (isset($_POST['quoted_price']) ?  $_POST['quoted_price'] : '');
$quoted_price_change = (isset($_POST['quoted_price_change']) ?  $_POST['quoted_price_change'] : '');
$quoted_price_org = (isset($_POST['quoted_price_org']) ?  $_POST['quoted_price_org'] : '');

$accessory_mode = (isset($_POST['accessory_mode']) ? $_POST['accessory_mode'] : 0);
$variation_mode = (isset($_POST['variation_mode']) ? $_POST['variation_mode'] : 0);

$attributes = (isset($_POST['attributes']) ?  $_POST['attributes'] : '[]');
$accessory = (isset($_POST['accessory']) ?  $_POST['accessory'] : '[]');
$accessory_array = json_decode($accessory, true);
$variation = (isset($_POST['variation']) ?  $_POST['variation'] : '[]');
$variation_array = json_decode($variation, true);

$url1 = (isset($_POST['url1']) ? $_POST['url1'] : '');
$url2 = (isset($_POST['url2']) ? $_POST['url2'] : '');
$url3 = (isset($_POST['url3']) ? $_POST['url3'] : '');


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

include_once 'config/conf.php';
require_once '../vendor/autoload.php';


$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        // preserve previous data
        $original_relative_product = get_related_product($id, $db);
        
        // now you can apply
        $uid = $user_id;
    
        $query = "update product_category
        SET
            `brand` = :brand,
            `code` = :code, ";

            if($price_ntd != '' && !is_null($price_ntd) && $price_ntd != 'null')
            {
                $query .= "`price_ntd` = :price_ntd, ";
            }
            else
            {
                $query .= "`price_ntd` = NULL, ";
            }

            if($price != '' && !is_null($price) && $price != 'null')
            {
                $query .= "`price` = :price, ";
            }
            else
            {
                $query .= "`price` = NULL, ";
            }

            if($quoted_price != '' && !is_null($quoted_price) && $quoted_price != 'null')
            {
                $query .= "`quoted_price` = :quoted_price, ";
            }
            else
            {
                $query .= "`quoted_price` = NULL, ";
            }

            if($price_ntd != $price_ntd_org)
            {
                if($price_ntd_change != '')
                {
                    if($price_ntd == '')
                        $query .= "`price_ntd_change` = null, ";
                    else
                        $query .= "`price_ntd_change` = STR_TO_DATE('" . $price_ntd_change . "', '%Y-%m-%d'), ";
                }
                else
                    $query .= "`price_ntd_change` = now(), ";
            }
            
            if($price != $price_org)
            {
                if($price_change != '')
                {
                    if($price_ntd == '')
                        $query .= "`price_change` = null, ";
                    else
                        $query .= "`price_change` = STR_TO_DATE('" . $price_change . "', '%Y-%m-%d'), ";
                }
                else
                    $query .= "`price_change` = now(), ";
            }

            if($quoted_price != $quoted_price_org)
            {
                if($quoted_price_change != '')
                {
                    if($price_ntd == '')
                        $query .= "`quoted_price_change` = null, ";
                    else
                        $query .= "`quoted_price_change` = STR_TO_DATE('" . $quoted_price_change . "', '%Y-%m-%d'), ";
                }
                else
                    $query .= "`quoted_price_change` = now(), ";
            }

            if($url1 == '')
                $query .= "`photo1` = '', ";
            if($url2 == '')
                $query .= "`photo2` = '', ";
            if($url3 == '')
                $query .= "`photo3` = '', ";

            $query .= "`description` = :description,
  
            `notes` = :notes,
            `tags` = :tags,
            `moq` = :moq,
            `accessory_mode` = :accessory_mode,
            `variation_mode` = :variation_mode,
            `attributes` = :attributes,
            `status` = 1,
            `updated_id` = :updated_id,
            `updated_at` = now() 
            where id = :id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':code', $code);

        if($price_ntd != '' && !is_null($price_ntd) && $price_ntd != 'null')
        {
            $stmt->bindParam(':price_ntd', $price_ntd);
        }

        if($price != '' && !is_null($price) && $price != 'null')
        {
            $stmt->bindParam(':price', $price);
        }

        if($quoted_price != '' && !is_null($quoted_price) && $quoted_price != 'null')
        {
            $stmt->bindParam(':quoted_price', $quoted_price);
        }

        $related_product = valid_id($related_product, $db);
        
        $stmt->bindParam(':description', $description);
        // $stmt->bindParam(':related_product', $related_product);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':moq', $moq);
        $stmt->bindParam(':accessory_mode', $accessory_mode);
        $stmt->bindParam(':variation_mode', $variation_mode);
        $stmt->bindParam(':attributes', $attributes);
        $stmt->bindParam(':updated_id', $user_id);
        $stmt->bindParam(':id', $id);

        $last_id = $id;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {

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

        // update other related_product
        update_relative_ids($related_product, $original_relative_product, $id, $code, $db);

        $batch_id = $last_id;
        $batch_type = "product_photo";

        if (array_key_exists('photo1', $_FILES))
        {
            $update_name = SaveImage('photo1', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo1', $batch_id, $db);
        }
        if (array_key_exists('photo2', $_FILES))
        {
            $update_name = SaveImage('photo2', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo2', $batch_id, $db);
        }
        if (array_key_exists('photo3', $_FILES))
        {
            $update_name = SaveImage('photo3', $batch_id, $batch_type, $user_id, $db, $conf);
            if($update_name != "")
                UpdateImageName($update_name, 'photo3', $batch_id, $db);
        }

        $product_id = $last_id;

        // delete accessory
        $query = "DELETE FROM accessory
        WHERE
        `product_id` = :product_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':product_id', $product_id);

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

        // accessory
        if($accessory_mode == 1)
        {
            for ($i = 0; $i < count($accessory_array); $i++) {
                $category = $accessory_array[$i]['category'];
                $cat_id = $accessory_array[$i]['cat_id'];
                $detail = $accessory_array[$i]['detail'];

                for($j=0; $j < count($detail); $j++)
                {
                    $query = "INSERT INTO accessory
                    SET
                        `category_id` = :category_id,
                        `product_id` = :product_id,
                        `accessory_type` = :accessory_type,
                        `code` = :code,
                        `accessory_name` = :accessory_name, ";
                    if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']) && $detail[$j]['price_ntd'] != 'null')
                    {
                        $query .= "`price_ntd` = :price_ntd, ";
                    }
                    if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']) && $detail[$j]['price'] != 'null')
                    {
                        $query .= "`price` = :price, ";
                    }
                    $query .= "
                        `enabled` = :enabled,
                    
                        `status` = 0,
                        `create_id` = :create_id,
                        `created_at` = now()";
        
                    // prepare the query
                    $stmt = $db->prepare($query);
        
                    // bind the values
                    $stmt->bindParam(':category_id', $cat_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':accessory_type', $category);
                    $stmt->bindParam(':code', $detail[$j]['code']);
                    $stmt->bindParam(':accessory_name', $detail[$j]['name']);
                    if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']) && $detail[$j]['price_ntd'] != 'null')
                    {
                        $stmt->bindParam(':price_ntd', $detail[$j]['price_ntd']);
                    }
                    if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']) && $detail[$j]['price'] != 'null')
                    {
                        $stmt->bindParam(':price', $detail[$j]['price']);
                    }
                    $stmt->bindParam(':enabled', $detail[$j]['enabled']);
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

                    $batch_id = $last_id;
                    $batch_type = "accessory_photo";

                    $key = "accessory_" . $cat_id . "_" . $detail[$j]['id'];
                    if (array_key_exists($key, $_FILES))
                    {
                        $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                        if($update_name != "")
                            UpdateImageNameAccessory($update_name, $batch_id, $db);
                    }
                    elseif($detail[$j]['photo'] != "")
                    {
                        UpdateImageNameAccessory($detail[$j]['photo'], $batch_id, $db);
                    }
                }
                
            }
        }

        // delete accessory
        $query = "DELETE FROM product
        WHERE
        `product_id` = :product_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':product_id', $product_id);

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


        // variation
        if($variation_mode == 1)
        {
            for ($i = 0; $i < count($variation_array); $i++) {
                $id = $variation_array[$i]['id'];
                $code = $variation_array[$i]['code'];
                $k1 = $variation_array[$i]['k1'];
                $k2 = $variation_array[$i]['k2'];
                $k3 = $variation_array[$i]['k3'];
                $v1 = $variation_array[$i]['v1'];
                $v2 = $variation_array[$i]['v2'];
                $v3 = $variation_array[$i]['v3'];
                $price = $variation_array[$i]['price'];
                $price_change = $variation_array[$i]['price_change'];
                $quoted_price = $variation_array[$i]['quoted_price'];
                $quoted_price_change = $variation_array[$i]['quoted_price_change'];
                $quoted_price_org = $variation_array[$i]['quoted_price_org'];
                $price_ntd = $variation_array[$i]['price_ntd'];
                $price_ntd_change = $variation_array[$i]['price_ntd_change'];
                $price_org = $variation_array[$i]['price_org'];
                $price_ntd_org = $variation_array[$i]['price_ntd_org'];
                $photo = $variation_array[$i]['photo'];
                $enabled = $variation_array[$i]['status'];
                $category_id = '';

                $st_variation = $k1 . '=>' . $v1;
                $rd_variation = $k2 . '=>' . $v2;
                $th_variation = $k3 . '=>' . $v3;
        
                $query = "INSERT INTO product
                SET
                    `category_id` = :category_id,
                    `product_id` = :product_id,
                    `1st_variation` = :1st_variation,
                    `2rd_variation` = :2rd_variation,
                    `3th_variation` = :3th_variation,
                    `code` = :code, ";
                    if($price_ntd != '' && !is_null($price_ntd) && $price_ntd != 'null')
                    {
                        $query .= "`price_ntd` = :price_ntd, ";
                    }

                    if($price != '' && !is_null($price) && $price != 'null')
                    {
                        $query .= "`price` = :price, ";
                    }
                    
                    if($quoted_price != '' && !is_null($quoted_price) && $quoted_price != 'null')
                    {
                        $query .= "`quoted_price` = :quoted_price, ";
                    }
                    
                    if(($price_ntd != $price_ntd_org) || $price_ntd_change != '')
                    {
                        if($price_ntd_change != '')
                        {
                            $query .= "`price_ntd_change` = STR_TO_DATE('" . $price_ntd_change . "', '%Y-%m-%d'), ";
                        }
                        else
                            $query .= "`price_ntd_change` = now(), ";
                    }

                    if(($price != $price_org) || $price_change != '')
                    {
                        if($price_change != '')
                        {
                            $query .= "`price_change` = STR_TO_DATE('" . $price_change . "', '%Y-%m-%d'), ";
                        }
                        else
                            $query .= "`price_change` = now(), ";
                    }
                    
                    if(($quoted_price != $quoted_price_org) || $quoted_price_change != '')
                    {
                        if($quoted_price_change != '')
                        {
                            $query .= "`quoted_price_change` = STR_TO_DATE('" . $quoted_price_change . "', '%Y-%m-%d'), ";
                        }
                        else
                            $query .= "`quoted_price_change` = now(), ";
                    }

                    $query .= "`enabled` = :enabled,
                    
                    `status` = 0,
                    `create_id` = :create_id,
                    `created_at` = now()";

                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':1st_variation', $st_variation);
                $stmt->bindParam(':2rd_variation', $rd_variation);
                $stmt->bindParam(':3th_variation', $th_variation);
                $stmt->bindParam(':code', $code);
        
                if($price_ntd != '' && !is_null($price_ntd) && $price_ntd != 'null')
                {
                    $stmt->bindParam(':price_ntd', $price_ntd);
                }
                if($price != '' && !is_null($price) && $price != 'null')
                {
                    $stmt->bindParam(':price', $price);
                }
                if($quoted_price != '' && !is_null($quoted_price) && $quoted_price != 'null')
                {
                    $stmt->bindParam(':quoted_price', $quoted_price);
                }
                $stmt->bindParam(':enabled', $enabled);
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

                $batch_id = $last_id;
                $batch_type = "variation_photo";

                $key = "variation_" . $id;
                if (array_key_exists($key, $_FILES))
                {
                    $update_name = SaveImage($key, $batch_id, $batch_type, $user_id, $db, $conf);
                    if($update_name != "")
                        UpdateImageNameVariation($update_name, $batch_id, $db);
                }
                elseif($photo != "")
                {
                    UpdateImageNameVariation($photo, $batch_id, $db);
                }
            }
        }
            
        $db->commit();
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

function UpdateImageName($upload_name, $type, $batch_id, $db){
    
    $query = "update product_category
    SET " . $type . " = :gcp_name where id=:id";

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

function UpdateImageNameAccessory($upload_name, $batch_id, $db){
    
    $query = "update accessory
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

function UpdateImageNameVariation($upload_name, $batch_id, $db){
    
    $query = "update product
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

function SaveImage($type, $batch_id, $batch_type, $user_id, $db, $conf)
{
    try {
        if($_FILES[$type]['name'] == null)
            return "";
        // Loop through each file

        if(isset($_FILES[$type]['name']))
        {
            $image_name = $_FILES[$type]['name'];
            $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
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


function valid_id($ids, $db) {
    $id_array = explode(',', $ids);
    $code_array = [];

    for($i = 0; $i < count($id_array); $i++)
    {
        $query = "SELECT code FROM product_category WHERE code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $id_array[$i]);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $code_array[] = $row['code'];
        }
    }
    
    return $code_array;
}

// update_relative_ids 
function update_relative_ids($id_array, $org_ids_array, $me_id, $me_code, $db) {

    // get array difference
    $diff = array_diff($org_ids_array, $id_array);
    foreach ($diff as &$value) 
    {
        if (in_array($value, $org_ids_array)) {
            remove_related_product_by_code($value, $me_code, $db);
        }
    }


    for($i = 0; $i < count($id_array); $i++)
    {
        $query = "SELECT id, code FROM product_category WHERE code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $id_array[$i]);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_array = get_relatived_id_from_other_product($row['id'], $db);
            array_push($_array, $me_code);
            // remove duplicate from array
            $related_product = array_unique($_array);
            // remove empty from array
            $related_product = array_filter($related_product, "not_empty");
            // order array
            sort($related_product);
    
            update_relative_ids_in_product_category($row['id'], $related_product, $db);

        }
    }

    update_relative_ids_in_product_category($me_id, $id_array, $db);

}

function get_relatived_id_from_other_product($id, $db) {
    $query = "SELECT code FROM product_related WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $new_ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $new_ids[] = $row['code'];
    }

    return $new_ids;
}

function update_relative_ids_in_product_category($id, $related_product, $db) {
    $query = "DELETE FROM product_related WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // loop to insert 
    for($i = 0; $i < count($related_product); $i++)
    {
        $query = "INSERT INTO product_related SET product_id = :id, code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':code', $related_product[$i]);
        $stmt->execute();
    }
}

function not_empty($array) 
{ 
    // returns if the input integer is even 
    if($array!="") 
       return TRUE; 
    else 
       return FALSE;  
} 

function get_related_product($id, $db) {
    $query = "SELECT code FROM product_related WHERE product_id = :id order by code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $new_ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $new_ids[] = $row['code'];
    }

    return $new_ids;
}

function remove_related_product_by_code($code, $org, $db) {
    $query = "select id, code from product_category where code = :code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_id = $row['id'];
        $_code = $row['code'];
        
        $query = "DELETE FROM product_related WHERE product_id = :id and code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_id);
        $stmt->bindParam(':code', $org);
        $stmt->execute();
    }

}
