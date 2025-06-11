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
    
    $sd = (isset($_GET['sd']) ?  $_GET['sd'] : 0);
    $d = (isset($_GET['d']) ?  $_GET['d'] : '');
    
    
    $database = new Database();
    $db = $database->getConnection();
    
    if($d != '' && $sd == 0)
    {
        $sd = $d;
        $d = "";
    }

    if($sd == "")
        $sd = 0;
    
    switch ($method) {
        case 'GET':
            $merged_results = array();
            
            $sql = "select id, product_id, p_id, code, photo1, photo2, photo3, photo4, photo5, photo6, description, variation, related_product, reserved, legend, `option`, category, indoor, type, grade from product_spec_sheet where product_id = " . $sd . " and p_id = '" . $d . "' and status <> -1";
            $stmt = $db->prepare( $sql );
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $product_id = $row['product_id'];
                $p_id = $row['p_id'];
                $code = $row['code'];

                $photo1 = ($row['photo1'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo1'] : '';
                $photo2 = ($row['photo2'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo2'] : '';
                $photo3 = ($row['photo3'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo3'] : '';
                $photo4 = ($row['photo4'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo4'] : '';
                $photo5 = ($row['photo5'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo5'] : '';
                $photo6 = ($row['photo6'] != '') ? 'https://storage.googleapis.com/feliiximg/' . $row['photo6'] : '';
                $description = $row['description'];
                $variation = $row['variation'];
                $related_product = json_decode($row['related_product'], true);
                $reserved = json_decode($row['reserved'], true);
                $legend = $row['legend'];
                $option = $row['option'];
                $category = $row['category'];
                $indoor = $row['indoor'];
                $type = $row['type'];
                $grade = $row['grade'];

                $variation_array = ParseTextAsVariant($row['variation']);

                $attribute_list_by_two = [];
                $two_array = [];
                for($i=0; $i<count($variation_array); $i++)
                {
                    if($i % 2 == 0)
                    {
                        $two_array = [];
                        $two_array[] = $variation_array[$i];
                    }
                    else
                    {
                        $two_array[] = $variation_array[$i];
                        $attribute_list_by_two[] = $two_array;
                    }
                }
                if(count($two_array) == 1)
                {
                    $attribute_list_by_two[] = $two_array;
                }
                
                $merged_results[] = array( "id" => $id,
                "product_id" => $product_id,
                "p_id" => $p_id,
                "code" => $code,
                "legend" => $legend,
                "option" => $option,
                "photo1" => $photo1,
                "photo2" => $photo2,
                "photo3" => $photo3,
                "photo4" => $photo4,
                "photo5" => $photo5,
                "photo6" => $photo6,
                "description" => $description,
                "variation" => $variation,
                "variation_array" => $attribute_list_by_two,
                "related_product" => $related_product,
                "reserved" => $reserved,
                "category" => $category,
                "indoor" => $indoor,
                "type" => $type,
                "grade" => $grade
            );
        }
        
        if(count($merged_results) > 0)
        {
            echo json_encode($merged_results, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            // product main
            $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  p.STATUS <> -1";
            
            if($sd != "")
            {
                $sql = $sql . " and p.id = " . $sd . " ";
            }
            
            $stmt = $db->prepare( $sql );
            $stmt->execute();
            
            $variation1 = "";
            $variation1_custom = "";
            $variation2 = "";
            $variation2_custom = "";
            $variation3 = "";
            $variation3_custom = "";
            $variation4 = "";
            $variation4_custom = "";
            $cat = "";
            $related_product = [];
            $attribute_list = [];
            $product_id = 0;
            $id = 0;
            $code = "";
            $photo1 = "";
            $photo2 = "";
            $photo3 = "";
            $photo4 = "";
            $photo5 = "";
            $photo6 = "";
            $description = "";
            $reserved = [];
            $indoor = "";
            $type = "";
            $grade = "";
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $id = 0;
                $product_id = $sd;
                $p_id = $d;
                $code = $row['code'];
                $photo1 = $row['photo1'];
                $photo2 = $row['photo2'];
                if($row['description'] != '' && $row['notes'] != '')
                    $description = "Description:" . PHP_EOL . $row['description'] . PHP_EOL . PHP_EOL . "Notes:" . PHP_EOL .$row['notes'];
                else if($row['description'] != '')
                    $description = "Description:" . PHP_EOL . $row['description'];
                else if($row['notes'] != '')
                    $description = "Notes:" . PHP_EOL . $row['notes'];
                else
                    $description = '';
                
                $accessory_mode = '';
                $attributes = '';
                $variation_mode = '';
                $variation = '';
                $status = '';
                $create_id = '';
                $created_at = '';
                $product = [];
                $accessory = [];
                
                $variation1_text = "1st Variation";
                $variation2_text = "2nd Variation";
                $variation3_text = "3rd Variation";
                $variation4_text = "4th Variation";
                
                $special_infomation = [];
                $accessory_information = [];
                $related_product = [];
                
                $sub_cateory_item = [];
                $cat = $row['category'];
                
                $category = GetCategory($row['category'], $db);
                $sub_category = $row['sub_category'];
                $tags = $row['tags'];
                $sub_category_name = GetCategory($row['sub_category'], $db);
                
                $brand = $row['brand'];
                
                $price_ntd = $row['price_ntd'];
                $price_org = $row['price'];
                $price_ntd_org = $row['price_ntd'];
                $price = $row['price'];

                $out = $row['out'];
                $notes = $row['notes'];
                
                $photo3 = $row['photo3'];
                $accessory_mode = $row['accessory_mode'];
                $attributes = $row['attributes'];
                $variation_mode = $row['variation_mode'];
                $variation = $row['variation'];
                $status = $row['status'];
                $create_id = $row['create_id'];
                $created_at = $row['created_at'];
                $updated_id = $row['updated_id'];
                $updated_at = $row['updated_at'];
                
                $created_name = $row['created_name'];
                $updated_name = $row['updated_name'];
                
                if($d != '')
                    $product = GetProductWithId($sd, $d, $db);
                else
                    $product = GetProduct($sd, $db);
                
                $related_product = GetRelatedProductCode($sd, $db);
                
                
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
                
                $accessory = GetAccessory($sd, $db);
                $sub_category_item = GetSubCategoryItem($category, $db);
                
                $special_info_json = json_decode($attributes);
                
                $special_information = GetSpecialInfomation($sub_category, $db, $special_info_json);
                $accessory_information = GetAccessoryInfomation($sub_category, $db, $sd);
                
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
                            $attribute_list[] = array("category" => $special_info_json[$i]->category, "value" => $value,);
                        }
                    }
                }
            }
            
            if($variation1 == "custom" && $variation1_custom != "1st Variation")
            {
                $attribute_list[] = array("category" => $variation1_text, "value" => $variation1_value,);
            }
            
            if($variation2 == "custom" && $variation2_custom != "2nd Variation")
            {
                $attribute_list[] = array("category" => $variation2_text, "value" => $variation2_value,);
            }
            
            if($variation3 == "custom" && $variation3_custom != "3rd Variation")
            {
                $attribute_list[] = array("category" => $variation3_text,"value" => $variation3_value,);
            }

            if($variation4 == "custom" && $variation4_custom != "4th Variation")
            {
                $attribute_list[] = array("category" => $variation4_text,"value" => $variation4_value,);
            }

            $cat_text = GetCategoryText($cat);
            $this_year = date("Y");
            
            $reserved = array("Tel" => "(+63) 2 8525-6288", "Email" => "info@feliix.com", "Website" => "www.feliix.com", "Copyright" => $this_year, "Feliix" => $cat_text, "Note" => "Specification are subject to change at any time without notice");
            
            $legend = "";
            $option = "";
            $type = "";
            $grade = "";
            $indoor = "";

            $photo3 = "";
            $photo4 = "";
            $photo5 = "";
            $photo6 = "";

            // only tak 4 of related_product
            for($i=0; $i < count($related_product); $i++)
            {
                if($i > 3)
                    break;
                $product = $related_product[$i];
                
                if($i == 0)
                    $photo3 = $product['photo1'];
                if($i == 1)
                    $photo4 = $product['photo1'];
                if($i == 2)
                    $photo5 = $product['photo1'];
                if($i == 3)
                    $photo6 = $product['photo1'];
                

            }

            $attribute_list_by_two = [];
            $two_array = [];
            for($i=0; $i<count($attribute_list); $i++)
            {
                if($i % 2 == 0)
                {
                    $two_array = [];
                    $two_array[] = $attribute_list[$i];
                }
                else
                {
                    $two_array[] = $attribute_list[$i];
                    $attribute_list_by_two[] = $two_array;
                }
            }
            if(count($two_array) == 1)
            {
                $attribute_list_by_two[] = $two_array;
            }
            
            $merged_results[] = array( 
            "id" => $id,
            "legend" => $legend,
            "option" => $option,
            "product_id" => $product_id,
            "p_id" => $p_id,
            "code" => $code,
            "photo1" => ($photo1 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo1: '',
            "photo2" => ($photo2 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo2: '',
            "photo3" => ($photo3 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo3: '',
            "photo4" => ($photo4 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo4: '',
            "photo5" => ($photo5 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo5: '',
            "photo6" => ($photo6 != '') ? 'https://storage.googleapis.com/feliiximg/' . $photo6: '',
            "description" => $description,
            "variation" => GetVariantAsText($attribute_list),
            "variation_array" => $attribute_list_by_two,
            "reserved" => $reserved,

            "related_product" => $related_product,

            "category" => GetCategoryText($cat),
            "indoor" => $indoor,
            "type" => $type,
            "grade" => $grade
        );

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
    }

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
        $price_change = $row['price_change'] != '' ? substr($row['price_change'], 0, 10) : '';
        $price_ntd_change = $row['price_ntd_change'] != '' ? substr($row['price_ntd_change'], 0, 10) : '';
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        $enabled = $row['enabled'];
        if($photo != '')
        $url = $row['url'];
        else
        $url = '';
        
        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';
        
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


function GetProductWithId($id, $d, $db){
    $sql = "SELECT *, CONCAT('https://storage.googleapis.com/feliiximg/' , photo) url FROM product WHERE product_id = ". $id . " and id = " . $d . " and STATUS <> -1";
    
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
        $price_change = $row['price_change'] != '' ? substr($row['price_change'], 0, 10) : '';
        $price_ntd_change = $row['price_ntd_change'] != '' ? substr($row['price_ntd_change'], 0, 10) : '';
        $status = $row['enabled'];
        $photo = trim($row['photo']);
        $enabled = $row['enabled'];
        if($photo != '')
        $url = $row['url'];
        else
        $url = '';
        
        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';
        
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

function GetCategory($cat_id, $db){
    $sql = "SELECT category FROM product_category_attribute WHERE cat_id = '". $cat_id . "' and STATUS <> -1";
    
    $merged_results = "";
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        $merged_results = $row['category'];
        
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

function GetCategoryText($category){
    $cat = "";
    if($category == "10000000")
        $cat = "Lighting";
    else if($category == "20000000")
        $cat = "Systems Furniture";
    
    return $cat;
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

function GetRelatedProductCode($id, $db){
    $sql = "SELECT * FROM product_category where code in (SELECT code FROM product_related WHERE product_id = '". $id . "' and STATUS <> -1) and status <> -1";
    
    $sql = $sql . " ORDER BY created_at desc ";
    
    $merged_results = [];
    
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
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

function phased_out_info($id, $db){
    $sql = "SELECT * FROM product WHERE product_id = ". $id . " and enabled = 0";
    
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

function GetVariantAsText($variants)
{
    $strVariant = "";

    if(count($variants) == 0)
    return $strVariant;

    // category as key : values as value
    foreach($variants as $variant)
    {
        $str_value = "";
        foreach($variant['value'] as $value)
        {
            $str_value .= $value . ",";
        }
        // remove last comma
        $str_value = substr($str_value, 0, -1);
        $strVariant .= $variant['category'] . ":" . $str_value . PHP_EOL;
    }

    // remove last /r/n ifhas newline
    if(substr($strVariant, -2) == PHP_EOL)
        $strVariant = substr($strVariant, 0, -2);

    return $strVariant;
}

function ParseTextAsVariant($variant_text)
{
    $lines = preg_split('/\r\n|\r|\n/', $variant_text);
    $variant = array();
    // each line as an record split by comma
    foreach($lines as $line)
    {
        // if line is  not contain comma, skip
        if(strpos($line, ":") == false)
            continue;

        if(trim($line) == '')
            continue;
        
        $key_value = explode(":", $line);
        $key = $key_value[0];

        if(strtolower($key) == "life hours")
        {
            $values = [];
            $values[] = $key_value[1];
        }
        else
            $values = explode(",", $key_value[1]);

        //$values = explode(",", $key_value[1]);
        
        $variant[] = array("category" => $key, "value" => $values);
    }
    return $variant;
}


?>
