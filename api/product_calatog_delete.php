<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

include_once 'mail.php';

$method = $_SERVER['REQUEST_METHOD'];


if ( !isset( $jwt ) ) {
    http_response_code(401);
 
    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
  try {
          // decode jwt
          $decoded = JWT::decode($jwt, $key, array('HS256'));
          $user_id = $decoded->data->id;
          // now you can apply
          $uid = $user_id;
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      header('Access-Control-Allow-Origin: *');  

      include_once 'config/database.php';
      $id = (isset($_POST['id']) ? $_POST['id'] : 0);

      $database = new Database();
      $db = $database->getConnection();

    $query = "update product_category 
            set status = -1,
            `deleted_id` = :updated_id,
            `deleted_time` = now() 
            where id = :id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':updated_id', $uid);
    $stmt->bindParam(':id', $id);

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

   if($user_id != 1 && $user_id != 2)
        EmailNotify($id, $db);

    update_product_category_tags_index($id, $db);

    http_response_code(200);
    echo json_encode(array("message" => "Deleted at " . date("Y-m-d") . " " . date("h:i:sa")));

    

function EmailNotify($id, $db){
    $_record = GetProductCategory($id, $db);
    if(count($_record) > 0 && $_record[0]['category'] != "20000000")
        product_notify("delete", $_record[0]);
}

function GetProductCategory($id, $db){
    $query = "SELECT p.id, p.category, p.sub_category, p.brand, p.code, p.photo1, p.created_at, p.create_id, p.updated_at, p.updated_id, p.deleted_id, p.deleted_time, p.attributes, c.username creator, u.username updator, d.username deletor  FROM product_category  p left join user c on p.create_id = c.id left join user u on p.updated_id = u.id  left join user d on p.deleted_id = d.id WHERE p.id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $merged_results = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $product = GetProduct($row["id"], $db);

        $variation1_value = [];
        $variation2_value = [];
        $variation3_value = [];
        $variation4_value = [];

        $variation1_text = "";
        $variation2_text = "";
        $variation3_text = "";
        $variation4_text = "";

        if(count($product) > 0)
        {
            $variation1_text = $product[0]['k1'];
            $variation2_text = $product[0]['k2'];
            $variation3_text = $product[0]['k3'];
            $variation4_text = $product[0]['k4'];

            $variation1_value = [];
            $variation2_value = [];
            $variation3_value = [];
            $variation4_value = [];

            for($i = 0; $i < count($product); $i++)
            {
                if (!in_array($product[$i]['v1'],$variation1_value))
                {
                    array_push($variation1_value,$product[$i]['v1']);
                }
                if (!in_array($product[$i]['v2'],$variation2_value))
                {
                    array_push($variation2_value,$product[$i]['v2']);
                }
                if (!in_array($product[$i]['v3'],$variation3_value))
                {
                    array_push($variation3_value,$product[$i]['v3']);
                }
                if (!in_array($product[$i]['v4'],$variation4_value))
                {
                    array_push($variation4_value,$product[$i]['v4']);
                }
            }
        }

        $special_info_json = json_decode($row["attributes"]);

        $sub_category = $row["sub_category"];

        $special_information = GetSpecialInfomation($sub_category, $db, $special_info_json);
        $accessory_information = GetAccessoryInfomation($sub_category, $db, $id);

        $variation1 = 'custom';
        $variation1_custom = $variation1_text;
        $variation2 = 'custom';
        $variation2_custom = $variation2_text;
        $variation3 = 'custom';
        $variation3_custom = $variation3_text;
        $variation4 = 'custom';
        $variation4_custom = $variation4_text;

        for($i = 0; $i < count($special_information); $i++)
        {
            if ($special_information[$i]['cat_id'] == $sub_category)
            {
                $lv3 = $special_information[$i]['lv3'][0];
                for($j = 0; $j < count($lv3); $j++)
                {
                    if($lv3[$j]['category'] == $variation1_text)
                    {
                        $variation1 = $variation1_text;
                        $variation1_custom = "";
                    }

                    if($lv3[$j]['category'] == $variation2_text)
                    {
                        $variation2 = $variation2_text;
                        $variation2_custom = "";
                    }

                    if($lv3[$j]['category'] == $variation3_text)
                    {
                        $variation3 = $variation3_text;
                        $variation3_custom = "";
                    }

                    if($lv3[$j]['category'] == $variation4_text)
                    {
                        $variation4 = $variation4_text;
                        $variation4_custom = "";
                    }
                }
            }
            
        }

        if($variation1_text == "")
        {
            $variation1 = "";
            $variation1_custom = "";
        }

        if($variation2_text == "")
        {
            $variation2 = "";
            $variation2_custom = "";
        }

        if($variation3_text == "")
        {
            $variation3 = "";
            $variation3_custom = "";
        }

        if($variation4_text == "")
        {
            $variation4 = "";
            $variation4_custom = "";
        }

        $attribute_list = [];
        if($special_info_json != null)
        {
            for($i=0; $i<count($special_info_json); $i++)
            {
                $value = [];
                $_category = $special_info_json[$i]->category;

                if($special_info_json[$i]->value != "")
                {
                    array_push($value, $special_info_json[$i]->value);
                    
                }
                
                if($variation1_text == $special_info_json[$i]->category)
                {
                    $value = $variation1_value;
                }
                if($variation2_text == $special_info_json[$i]->category)
                {
                    $value = $variation2_value;
                }
                if($variation3_text == $special_info_json[$i]->category)
                {
                    $value = $variation3_value;
                }
                if($variation4_text == $special_info_json[$i]->category)
                {
                    $value = $variation4_value;
                }

                if(count($value) > 0)
                {
                    $attribute_list[] = array("category" => $special_info_json[$i]->category,
                                    "value" => $value,
                                );
                }
            }
        }

        
        if($variation1 == "custom" && $variation1_custom != "1st Variation")
        {
            $attribute_list[] = array("category" => $variation1_text,
                                   "value" => $variation1_value,
                                );
        }

        if($variation2 == "custom" && $variation2_custom != "2nd Variation")
        {
            $attribute_list[] = array("category" => $variation2_text,
                                   "value" => $variation2_value,
                                );
        }

        if($variation3 == "custom" && $variation3_custom != "3rd Variation")
        {
            $attribute_list[] = array("category" => $variation3_text,
                                   "value" => $variation3_value,
                                );
        }

        if($variation4 == "custom" && $variation4_custom != "4th Variation")
        {
            $attribute_list[] = array("category" => $variation4_text,
                                   "value" => $variation4_value,
                                );
        }

        $merged_results[] = array( "id" => $row["id"],
                            "category" => $row["category"],
                            "tags" => explode(',', $row["tags"]),
                            "brand" => $row["brand"],
                            "code" => $row["code"],
                        
                            "photo1" => $row["photo1"],
                
                            "created_at" => $row["created_at"],
                            "create_id" => $row["create_id"],
                            "updated_at" => $row["updated_at"],
                            "updated_id" => $row["updated_id"],

                            "deleted_id" => $row["deleted_id"],
                            "deleted_time" => $row["deleted_time"],

                            "creator" => $row["creator"],
                            "updator" => $row["updator"],
                            "deletor" => $row["deletor"],
                           
                            "attribute_list" => $attribute_list,
                           

        );
    }

    return $merged_results;
}

function GetKey($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[0]) ? $obj[0] : "";
}

function GetValue($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[1]) ? $obj[1] : "";
}

function GetProduct($id, $db){
    $sql = "SELECT *, CONCAT('https://storage.googleapis.com/feliiximg/' , photo) url FROM product WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $k1 = GetKey($row['1st_variation']);
        $k2 = GetKey($row['2rd_variation']);
        $k3 = GetKey($row['3th_variation']);
        $k4 = GetKey($row['4th_variation']);
        $v1 = GetValue($row['1st_variation']);
        $v2 = GetValue($row['2rd_variation']);
        $v3 = GetValue($row['3th_variation']);
        $v4 = GetValue($row['4th_variation']);
        $checked = '';
        $code = $row['code'];
        $price = $row['price'];
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price_change = $row['price_change'];
        $price_ntd_change = $row['price_ntd_change'];
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        $enabled = $row['enabled'];
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'];

        $merged_results[] = array(  "id" => $id, 
                                    "k1" => $k1, 
                                    "k2" => $k2, 
                                    "k3" => $k3, 
                                    "k4" => $k4,
                                    "v1" => $v1, 
                                    "v2" => $v2, 
                                    "v3" => $v3, 
                                    "v4" => $v4,
                                    "checked" => $checked, 
                                    "code" => $code, 
                                    "price" => $price, 
                                    "price_ntd" => $price_ntd, 
                                    "price_org" => $price_org, 
                                    "price_ntd_org" => $price_ntd_org, 
                                    "price_change" => $price_change, 
                                    "price_ntd_change" => $price_ntd_change, 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 
                                    "enabled" => $enabled,

                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                   
                                    "file" => array( "value" => ''),
                                   
            );
    }
    
    return $merged_results;
}

function GetSpecialInfomation($cat_id, $db, $special_info_json){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 2 AND left(cat_id, 1) = '". substr($cat_id, 0, 1) . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY cat_id ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $cat_id = "";
    $category = "";

    $lv3 = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "lv3" => $lv3,
            );

            $lv3 = [];

        }

        $cat_id = $row['cat_id'];
        $category = $row['category'];

        $lv3[] = GetLevel3_value($cat_id, $db, $special_info_json);
    }

    if($cat_id != "")
    {
        $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "lv3" => $lv3,
            );
    }

    return $merged_results;

}


function GetLevel3_value($cat_id, $db, $special_info_json){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY cat_id ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $cat_id = "";
    $category = "";

    $lv2 = [];

    $value = '';

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "detail" => $lv2,
                                    "value" => $value,
            );

            $lv2 = [];

        }

        $cat_id = $row['cat_id'];
        $category = $row['category'];

        $value = '';
        if($special_info_json != null)
        {
            for($i=0; $i<count($special_info_json); $i++)
            {
                if($special_info_json[$i]->cat_id == $cat_id)
                {
                    $value = $special_info_json[$i]->value;
                    break;
                }
            }
        }

        $lv2[] = GetDetail($cat_id, $db);
    }

    if($cat_id != "")
    {
        $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "detail" => $lv2,
                                    "value" => $value,
            );
    }

    return $merged_results;

}

function GetDetail($cat_id, $db){
    $sql = "SELECT cat_id, sn, `option` FROM product_category_attribute_detail WHERE cat_id = '". $cat_id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY sn ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;

}

function GetAccessoryInfomation($cat_id, $db, $product_id){
    $sql = "SELECT * FROM accessory_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY cat_id ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $cat_id = "";
    $category = "";

    $lv2 = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if($cat_id != $row['cat_id'] && $cat_id != "")
        {
            $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "detail" => $lv2,
            );

            $lv2 = [];

        }

        $cat_id = $row['cat_id'];
        $category = $row['category'];

        $lv2[] = GetAccessoryInfomationDetail($cat_id, $product_id, $db);
    }

    if($cat_id != "")
    {
        $merged_results[] = array( "cat_id" => $cat_id,
                                    "category" => $category,
                                    "detail" => $lv2,
            );
    }

    return $merged_results;

}

function GetAccessoryInfomationDetail($cat_id, $product_id, $db){

    $sql = "SELECT id, code, accessory_name `name`, price, price_ntd, category_id cat_id, photo, CONCAT('https://storage.googleapis.com/feliiximg/', photo) url FROM accessory WHERE product_id = ". $product_id . " and category_id = '" . $cat_id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY id ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $photo = trim($row['photo']);
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

        $merged_results[] = array(  "id" => $row['id'], 
                                    "code" => $row['code'],
                                    "name" => $row['name'],
                                    "price" => $row['price'],
                                    "price_ntd" => $row['price_ntd'],
                                    "cat_id" => $row['cat_id'],
                                    "url" => $url,
                                    "photo" => $photo,
                                    "file" => array( "value" => ''),
                                   
            );
    }

    return $merged_results;

}

function update_product_category_tags_index($id, $db) {
    // clear all data
    $query = "DELETE FROM product_category_tags_index WHERE pid = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

?>
