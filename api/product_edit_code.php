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
          //if(!$decoded->data->is_admin)
          //{
          //  http_response_code(401);
     
          //  echo json_encode(array("message" => "Access denied."));
          //  die();
          //}
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
      $id = (isset($_GET['id']) ?  $_GET['id'] : -1);


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $merged_results = array();

            // product main
            $sql = "SELECT p.*, pa.category sub_category_name FROM product_category p left join product_category_attribute pa on p.sub_category = pa.cat_id WHERE p.id = " . $id . " AND p.STATUS <> -1";

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $id = '';
            $category = '';
            $sub_category = '';
            $sub_category_name = '';
            $brand = '';
            $currency = '';
            $code = '';
            $price_ntd = '';
            $price_ntd_org = '';
            $price_ntd_change = '';
            $price = '';
            $price_org = '';
            $price_change = '';
            $description = '';
            $relative_product = '';
            $out = '';
            $notes = '';
            $photo1 = '';
            $photo2 = '';
            $photo3 = '';
            $accessory_mode = '';
            $attributes = '';
            $variation_mode = '';
            $variation = '';
            $status = '';
            $create_id = '';
            $created_at = '';
            $product = [];
            $accessory = [];

            $tags = '';
            $moq = '';
            $quoted_price = '';
            $quoted_price_change = '';

            $variation1_text = "1st Variation";
            $variation2_text = "2nd Variation";
            $variation3_text = "3rd Variation";
            $variation4_text = "4th Variation";

            $special_infomation = [];
            $accessory_information = [];

            $sub_cateory_item = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $id = $row['id'];
                $category = $row['category'];
                $sub_category = $row['sub_category'];
                $sub_category_name = $row['sub_category_name'];
                $brand = $row['brand'];
                $currency = $row['currency'];
                $code = $row['code'];
                $price_ntd = $row['price_ntd'];
                $price_org = $row['price'];
                $price_ntd_org = $row['price_ntd'];
                $price = $row['price'];
                $description = $row['description'];
                $related_product = GetRelatedProduct($id, $db);
                $replacement_product = GetReplacementProduct($id, $db);
                $out = $row['out'];
                $notes = $row['notes'];
                $photo1 = $row['photo1'];
                $photo2 = $row['photo2'];
                $photo3 = $row['photo3'];
                $accessory_mode = $row['accessory_mode'];
                $attributes = $row['attributes'];
                $variation_mode = $row['variation_mode'];
                $variation = $row['variation'];
                $status = $row['status'];
                $create_id = $row['create_id'];
                $created_at = $row['created_at'];

                $tags = $row['tags'];
                $moq = $row['moq'];
                $quoted_price = $row['quoted_price'];
                $quoted_price_org = $row['quoted_price'];
                $quoted_price_change = $row['quoted_price_change'];
                $price_change = $row['price_change'];
                $price_ntd_change = $row['price_ntd_change'];

                $p1_code = $row['p1_code'];
                $p1_id = $row['p1_id'];
                $p1_qty = $row['p1_qty'];

                $p2_code = $row['p2_code'];
                $p2_id = $row['p2_id'];
                $p2_qty = $row['p2_qty'];

                $p3_code = $row['p3_code'];
                $p3_id = $row['p3_id'];
                $p3_qty = $row['p3_qty'];

                $brand_handler = $row['brand_handler'];

                $product = GetProduct($id, $db);

                $product_ics = GetAttachment($id, 'product_ics', $db);
                $product_skp = GetAttachment($id, 'product_skp', $db);
                $product_manual = GetAttachment($id, 'product_manual', $db);

                $variation1_value = [];
                $variation2_value = [];
                $variation3_value = [];
                $variation4_value = [];

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

                $accessory = GetAccessory($id, $db);
                $sub_category_item = GetSubCategoryItem($category, $db);

                $special_info_json = json_decode($attributes);

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

                if($variation1_text == "1st Variation")
                {
                    $variation1 = "";
                    $variation1_custom = "";
                }

                if($variation2_text == "2nd Variation")
                {
                    $variation2 = "";
                    $variation2_custom = "";
                }

                if($variation3_text == "3rd Variation")
                {
                    $variation3 = "";
                    $variation3_custom = "";
                }

                if($variation4_text == "4th Variation")
                {
                    $variation4 = "";
                    $variation4_custom = "";
                }

                $merged_results[] = array( "id" => $id,
                                    "category" => $category,
                                    "sub_category" => $sub_category,
                                    "brand" => $brand,
                                    "currency" => $currency,
                                    "code" => $code,
                                    "price_ntd" => $price_ntd,
                                    "price" => $price,
                                    "price_ntd_org" => $price_ntd_org,
                                    "price_org" => $price_org,
                                    "description" => $description,
                                    "photo1" => $photo1,
                                    "photo2" => $photo2,
                                    "photo3" => $photo3,
                                    "accessory_mode" => $accessory_mode,
                                    "attributes" => $attributes,
                                    "variation_mode" => $variation_mode,
                                    "variation" => $variation,
                                    "status" => $status,
                                    "created_at" => $created_at,
                                    "create_id" => $create_id,
                                    "product" => $product,
                                    "variation1_text" => $variation1_text,
                                    "variation2_text" => $variation2_text,
                                    "variation3_text" => $variation3_text,
                                    "variation4_text" => $variation4_text,
                                    "variation1_value" => $variation1_value,
                                    "variation2_value" => $variation2_value,
                                    "variation3_value" => $variation3_value,
                                    "variation4_value" => $variation4_value,
                                    "variation1" => $variation1,
                                    "variation2" => $variation2,
                                    "variation3" => $variation3,
                                    "variation4" => $variation4,
                                    "variation1_custom" => $variation1_custom,
                                    "variation2_custom" => $variation2_custom,
                                    "variation3_custom" => $variation3_custom,
                                    "variation4_custom" => $variation4_custom,
                                    "accessory" => $accessory,
                                    "special_information" => $special_information,
                                    "accessory_information" => $accessory_information,
                                    "sub_category_item" => $sub_category_item,
                                    "related_product" => $related_product,
                                    "replacement_product" => $replacement_product,
                                    "out" => $out,
                                    "notes" => $notes,
                                    "moq" => $moq,
                                    "tags" => $tags,
                                    "quoted_price" => $quoted_price,
                                    "quoted_price_org" => $quoted_price_org,
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10),
                                    "price_change" => substr($price_change, 0, 10),
                                    "price_ntd_change" => substr($price_ntd_change, 0, 10),

                                    "p1_code" => $p1_code,
                                    "p1_qty" => $p1_qty,
                                    "p1_id" => $p1_id,

                                    "p2_code" => $p2_code,
                                    "p2_qty" => $p2_qty,
                                    "p2_id" => $p2_id,

                                    "p3_code" => $p3_code,
                                    "p3_qty" => $p3_qty,
                                    "p3_id" => $p3_id,

                                    "brand_handler" => $brand_handler,

                                    "product_ics" => $product_ics,
                                    "product_skp" => $product_skp,
                                    "product_manual" => $product_manual,

            );
            }



            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

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

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'];

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];
        $last_order_type = $row['last_order_type'];

        $status = $row['enabled'];
        $photo = trim($row['photo']);
        if($photo != '')
            $url = $row['url'];
        else
            $url = '';

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
                                    "price_change" => substr($price_change, 0, 10), 
                                    "price_ntd_change" => substr($price_ntd_change, 0, 10), 
                                    "quoted_price" => $quoted_price, 
                                    "quoted_price_org" => $quoted_price, 
                                    "quoted_price_change" => substr($quoted_price_change, 0, 10), 
                                    "status" => $status, 
                                    "url" => $url, 
                                    "photo" => $photo, 

                                    "last_order" => $last_order,
                                    "last_order_name" => $last_order_name,
                                    "last_order_at" => $last_order_at,
                                    "last_order_type" => $last_order_type,
                                   
                                    "file" => array( "value" => ''),
                                   
            );
    }
    
    return $merged_results;
}

function GetSubCategoryItem($cat_id, $db){
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

        $lv3[] = GetLevel3($cat_id, $db);
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

function GetAccessory($id, $db){
    $sql = "SELECT * FROM accessory WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();


    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }
    
    return $merged_results;
}

function GetSpecialInfomation($cat_id, $db, $special_info_json){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 2 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";

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


function GetLevel3($cat_id, $db){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 3 AND left(cat_id, 4) = '". substr($cat_id, 0, 4) . "' and STATUS <> -1";

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

        $lv2[] = GetDetail($cat_id, $db);
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

function GetRelatedProduct($id, $db){
    $sql = "SELECT code FROM product_related WHERE product_id = '". $id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY code ";

    $merged_results = "";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= $row['code'] . ",";
    }

    if($merged_results != "")
    {
        $merged_results = substr($merged_results, 0, strlen($merged_results) - 1);
    }

    return $merged_results;

}

function GetReplacementProduct($id, $db){
    $sql = "SELECT code FROM product_replacement WHERE product_id = '". $id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY code ";

    $merged_results = "";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results .= $row['code'] . ",";
    }

    if($merged_results != "")
    {
        $merged_results = substr($merged_results, 0, strlen($merged_results) - 1);
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

function GetAttachment($_id, $batch_type, $db)
{
    $sql = "select id, 1 is_checked, COALESCE(h.filename, '') filename, COALESCE(h.gcp_name, '') gcp_name
            from gcp_storage_file h where h.batch_id = " . $_id . " AND h.batch_type = '" . $batch_type . "'
            order by h.created_at ";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


?>
