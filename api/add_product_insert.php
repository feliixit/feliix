<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');
$tags = (isset($_POST['tags']) ?  $_POST['tags'] : '');
$brand = (isset($_POST['brand']) ?  $_POST['brand'] : '');
$code = (isset($_POST['code']) ?  $_POST['code'] : '');
$price_ntd = (isset($_POST['price_ntd']) ?  $_POST['price_ntd'] : '');
$price_ntd_change = (isset($_POST['price_ntd_change']) ?  $_POST['price_ntd_change'] : '');
$price = (isset($_POST['price']) ?  $_POST['price'] : '');
$price_change = (isset($_POST['price_change']) ?  $_POST['price_change'] : '');
$quoted_price = (isset($_POST['quoted_price']) ?  $_POST['quoted_price'] : '');
$quoted_price_change = (isset($_POST['quoted_price_change']) ?  $_POST['quoted_price_change'] : '');
$moq = (isset($_POST['moq']) ?  $_POST['moq'] : '');
$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$notes = (isset($_POST['notes']) ? $_POST['notes'] : '');
$related_product = (isset($_POST['related_product']) ? $_POST['related_product'] : '');

$accessory_mode = (isset($_POST['accessory_mode']) ? $_POST['accessory_mode'] : 0);
$variation_mode = (isset($_POST['variation_mode']) ? $_POST['variation_mode'] : 0);

$attributes = (isset($_POST['attributes']) ?  $_POST['attributes'] : '[]');
$accessory = (isset($_POST['accessory']) ?  $_POST['accessory'] : '[]');
$accessory_array = json_decode($accessory, true);
$variation = (isset($_POST['variation']) ?  $_POST['variation'] : '[]');
$variation_array = json_decode($variation, true);


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

        // 去除非商品資料
        $related_product = valid_id($related_product, $db);

        $srp_max = "";
        $srp_min = "";
        $qp_max = "";
        $qp_min = "";
        
        // now you can apply
        $uid = $user_id;
    
        $query = "INSERT INTO product_category
        SET
            `category` = :category,
            `sub_category` = :sub_category,
            `tags` = :tags,
            `brand` = :brand,
            `code` = :code, ";
            if($price_ntd != ''  && !is_null($price_ntd))
            {
                $query .= "`price_ntd` = :price_ntd, ";
            }

            if($price != ''  && !is_null($price))
            {
                $query .= "`price` = :price, ";

                $srp_max = $price;
                $srp_min = $price;
            }

            if($quoted_price != ''  && !is_null($quoted_price))
            {
                $query .= "`quoted_price` = :quoted_price, ";

                $qp_max = $quoted_price;
                $qp_min = $quoted_price;
            }


    $query .= "`price_ntd_change` = :price_ntd_change, ";

    $query .= "`price_change` = :price_change, ";

    $query .= "`quoted_price_change` = :quoted_price_change, ";

        $query .= "
            `moq` = :moq,
            `description` = :description,
            `related_product` = :related_product,
            `notes` = :notes,
            `accessory_mode` = :accessory_mode,
            `variation_mode` = :variation_mode,
            `attributes` = :attributes,
            `status` = 1,
            `create_id` = :create_id,
            `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':sub_category', $sub_category);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':code', $code);
        if($price_ntd != '' && !is_null($price_ntd))
        {
            $stmt->bindParam(':price_ntd', $price_ntd);
        }

        if($price != '' && !is_null($price))
        {
            $stmt->bindParam(':price', $price);
        }

        if($quoted_price != '' && !is_null($quoted_price))
        {
            $stmt->bindParam(':quoted_price', $quoted_price);
        }

        $price_ntd_change = formate_date($price_ntd_change);
        $price_change = formate_date($price_change);
        $quoted_price_change = formate_date($quoted_price_change);

        $stmt->bindParam(':price_ntd_change', $price_ntd_change);
        $stmt->bindParam(':price_change', $price_change);
        $stmt->bindParam(':quoted_price_change', $quoted_price_change);

        $stmt->bindParam(':moq', $moq);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':related_product', $related_product);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':accessory_mode', $accessory_mode);
        $stmt->bindParam(':variation_mode', $variation_mode);
        $stmt->bindParam(':attributes', $attributes);
        $stmt->bindParam(':create_id', $user_id);

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

        // update other related_product
        update_relative_ids($related_product, $last_id, $db);

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

        // accessory
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

                    if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']))
                    {
                        $query .= "`price_ntd` = :price_ntd, ";
                    }
                    if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']))
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
                if($detail[$j]['price_ntd'] != '' && !is_null($detail[$j]['price_ntd']))
                {
                    $stmt->bindParam(':price_ntd', $detail[$j]['price_ntd']);
                }
                if($detail[$j]['price'] != '' && !is_null($detail[$j]['price']))
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
            }
            
        }

        // variation
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
            $price_ntd = $variation_array[$i]['price_ntd'];
            $price_ntd_change = $variation_array[$i]['price_ntd_change'];
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
                if($price_ntd != '' && !is_null($price_ntd))
                {
                    $query .= "`price_ntd` = :price_ntd, ";
                }

                if($price != '' && !is_null($price))
                {
                    $query .= "`price` = :price, ";

                    if(parseFloat($price) > parseFloat($srp_max))
                        $srp_max = $price;
                    
                    if(parseFloat($price) < parseFloat($srp_min))
                        $srp_min = $price;
                    
                    if(parseFloat($price) != 0 && $srp_min == '')
                        $srp_min = $price;
                }

                if($quoted_price != '' && !is_null($quoted_price))
                {
                    $query .= "`quoted_price` = :quoted_price, ";

                    if(parseFloat($quoted_price) > parseFloat($qp_max))
                        $qp_max = $quoted_price;
                    
                    if(parseFloat($quoted_price) < parseFloat($qp_min))
                        $qp_min = $quoted_price;

                    if(parseFloat($quoted_price) != 0 && $qp_min == '')
                        $qp_min = $quoted_price;
                }

            $query .= "`price_ntd_change` = :price_ntd_change, ";
            $query .= "`price_change` = :price_change, ";
            $query .= "`quoted_price_change` = :quoted_price_change, ";

            $query .= "
                `enabled` = :enabled,    
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
            if($price_ntd != '' && !is_null($price_ntd))
            {
                $stmt->bindParam(':price_ntd', $price_ntd);
            }
            if($price != '' && !is_null($price))
            {
                $stmt->bindParam(':price', $price);
            }
            if($quoted_price != '' && !is_null($quoted_price))
            {
                $stmt->bindParam(':quoted_price', $quoted_price);
            }

            $price_ntd_change = formate_date($price_ntd_change);
            $price_change = formate_date($price_change);
            $quoted_price_change = formate_date($quoted_price_change);

            $stmt->bindParam(':price_ntd_change', $price_ntd_change);
            $stmt->bindParam(':price_change', $price_change);
            $stmt->bindParam(':quoted_price_change', $quoted_price_change);

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
        }

        // update srp_max, srp_min, qp_max, qp_min to product_category
        $query = "UPDATE product_category SET "; 

        if($srp_max != '')
        {
            $query .= "`srp_max` = :srp_max, ";
        }
        else
        {
            $query .= "`srp_max` = null, ";
        }
        
        if($srp_min != '')
        {
            $query .= "`srp_min` = :srp_min, ";
        }
        else
        {
            $query .= "`srp_min` = null, ";
        }

        if($qp_max != '')
        {
            $query .= "`qp_max` = :qp_max, ";
        }
        else
        {
            $query .= "`qp_max` = null, ";
        }

        if($qp_min != '')
        {
            $query .= "`qp_min` = :qp_min, ";
        }
        else
        {
            $query .= "`qp_min` = null, ";
        }

        $query .= "`updated_at` = now() WHERE `id` = :id";
        
           
        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        if($srp_max != '' && !is_null($srp_max))
        {
            $stmt->bindParam(':srp_max', $srp_max);
        }

        if($srp_min != '' && !is_null($srp_min))
        {
            $stmt->bindParam(':srp_min', $srp_min);
        }

        if($qp_max != '' && !is_null($qp_max))
        {
            $stmt->bindParam(':qp_max', $qp_max);
        }

        if($qp_min != '' && !is_null($qp_min))
        {
            $stmt->bindParam(':qp_min', $qp_min);
        }

        $stmt->bindParam(':id', $product_id);

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

function parseFloat($value) {
    $ret_value = 0.0;

    // parse $value as float
    if (is_numeric($value)) {
        $ret_value = floatval($value);
    } else {
        // parse $value as string
        $value = str_replace(',', '.', $value);
        $value = str_replace(' ', '', $value);
        $value = str_replace(' ', '', $value); // this is a non-breaking space (0xC2A0 hex)
        $value = preg_replace('/[^0-9\.]/', '', $value);

        if (is_numeric($value)) {
            $ret_value = floatval($value);
        }
    }
    
    return $ret_value;
    //return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,3}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value));
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

function formate_date($date)
{
    $v_date = trim($date);
    
    if(valid_date($v_date) == 1)
        return $v_date;
    else
        return null;
}

function valid_date($date) {
    return (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date));
}

function valid_id($ids, $db) {
    $id_array = explode(',', $ids);
    $new_ids = "";

    for($i = 0; $i < count($id_array); $i++)
    {
        if (is_numeric($id_array[$i])) {
            $new_ids .= $id_array[$i] . ",";
        }
    }

    if($new_ids != "")
        $new_ids = substr($new_ids, 0, -1);
    else
        return "";

    $query = "SELECT id FROM product_category WHERE id IN ($new_ids) order by id";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $new_ids = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $new_ids .= $row['id'] . ",";
    }

    if($new_ids != "")
        $new_ids = substr($new_ids, 0, -1);
    
    return $new_ids;
}

// update_relative_ids 
function update_relative_ids($ids, $me_id, $db) {
    $id_array = explode(',', $ids);
    $new_ids = "";

    for($i = 0; $i < count($id_array); $i++)
    {
        if (is_numeric($id_array[$i])) {
            $new_ids .= $id_array[$i] . ",";
        }
    }

    if($new_ids != "")
        $new_ids = substr($new_ids, 0, -1);
    else
        return "";

    $query = "SELECT id FROM product_category WHERE id IN ($new_ids)";
    $stmt = $db->prepare($query);
    $stmt->execute();

    $new_ids = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $related_product = get_relatived_id_from_other_product($row['id'], $db);
        $related_product .= "," . $me_id;

        $id_array = explode(',', $related_product);

        // remove duplicate from array
        $related_product = array_unique($id_array);
        // remove empty from array
        $related_product = array_filter($related_product, "not_empty");
        // order array
        sort($related_product);
        // array to string separated by comma
        $related_product = implode(',', $related_product);

        update_relative_ids_in_product_category($row['id'], $related_product, $db);

    }

}

function update_relative_ids_in_product_category($id, $related_product, $db) {
    $query = "UPDATE product_category SET related_product = :related_product WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':related_product', $related_product);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

function not_empty($array) 
{ 
    // returns if the input integer is even 
    if($array!="") 
       return TRUE; 
    else 
       return FALSE;  
} 

function get_relatived_id_from_other_product($id, $db) {
    $query = "SELECT related_product FROM product_category WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $new_ids = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $new_ids = $row['related_product'];
    }

    return $new_ids;
}
