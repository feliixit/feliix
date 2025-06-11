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

      $page = (isset($_GET['page']) ?  $_GET['page'] : "");
      $size = (isset($_GET['size']) ?  $_GET['size'] : "");

      $sd = (isset($_GET['sd']) ?  $_GET['sd'] : "");
      $d = (isset($_GET['d']) ?  $_GET['d'] : "");
      $d1 = (isset($_GET['d1']) ?  $_GET['d1'] : "");
      $g = (isset($_GET['g']) ?  $_GET['g'] : "");
      $c = (isset($_GET['c']) ?  $_GET['c'] : "");
      $c = urldecode($c);
      $t = (isset($_GET['t']) ?  $_GET['t'] : "");
      $t = urldecode($t);
      $k = (isset($_GET['k']) ?  $_GET['k'] : "");
      $k = urldecode($k);
      $tag_array = json_decode($t, true);
      $b = (isset($_GET['b']) ?  $_GET['b'] : "");
      $b = urldecode($b);

      $of1 = (isset($_GET['of1']) ?  $_GET['of1'] : '');
      $ofd1 = (isset($_GET['ofd1']) ?  $_GET['ofd1'] : '');
      $of2 = (isset($_GET['of2']) ?  $_GET['of2'] : '');
      $ofd2 = (isset($_GET['ofd2']) ?  $_GET['ofd2'] : '');

      $database = new Database();
      $db = $database->getConnection();

      if($d != "" && $d1 == "" && $sd == "")
      {
          $sd = $d;
          $d = "";
      }

      switch ($method) {
          case 'GET':
            $merged_results = array();

            $query_cnt = "SELECT count(*) cnt FROM product_spec_sheet p  WHERE  p.STATUS <> -1 ";

            // product main
            $sql = "SELECT p.*, pc.category, pc.sub_category, cu.username created_name, uu.username updated_name FROM product_spec_sheet p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id left join product_category pc on pc.id = p.product_id  WHERE  p.STATUS <> -1";

            if (!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if (false === $page) {
                    $page = 1;
                }
            }

            if($sd != "" && $sd != "0")
            {
                $sql = $sql . " and p.product_id = " . $sd . " ";
                $query_cnt = $query_cnt . " and p.product_id = " . $sd . " ";
            }

            if($d != "" && $d != "0")
            {
                $sql = $sql . " and p.product_id >= " . $d . " ";
                $query_cnt = $query_cnt . " and p.product_id >= " . $d . " ";
            }

            if($d1 != "" && $d1 != "0")
            {
                $sql = $sql . " and p.product_id <= " . $d1 . " ";
                $query_cnt = $query_cnt . " and p.product_id <= " . $d1 . " ";
            }

            if($g != "")
            {
                $sql = $sql . " and (pc.category = '" . $g . "' or pc.sub_category = '" . $g . "') ";
                $query_cnt = $query_cnt . " and (pc.category = '" . $g . "' or pc.sub_category = '" . $g . "') ";
            }

            if($c != "")
            {
                $sql = $sql . " and p.code like '%" . $c . "%' ";
                $query_cnt = $query_cnt . " and p.code like '%" . $c . "%' ";
            }

            $tag_sql = "";
            if($tag_array != null)
            {
                for ($i = 0; $i < count($tag_array); $i++) {
                    $tag_sql = $tag_sql . " pc.tags like '%" . $tag_array[$i] . "%' and ";
                }
            }

            if($tag_sql != "")
            {
                $tag_sql = substr($tag_sql, 0, -4);

                $sql = $sql . " and (" . $tag_sql . ") ";
                $query_cnt = $query_cnt . " and (" . $tag_sql . ") ";
            }

            if($b != "")
            {
                $sql = $sql . " and pc.brand = '" . $b . "' ";
                $query_cnt = $query_cnt . " and pc.brand = '" . $b . "' ";
            }

            if($k != "")
            {
                $sql = $sql . " and (p.description like '%" . $k . "%' or p.notes like '%" . $k . "%') ";
                $query_cnt = $query_cnt . " and (p.description like '%" . $k . "%' or p.notes like '%" . $k . "%') ";
            }
    
            
$sOrder = "";
if($of1 != "" && $of1 != "0")
{
    switch ($of1)
    {   
        case 1:
            if($ofd1 == 2)
                $sOrder = "p.product_id desc";
            else
                $sOrder = "p.product_id ";
            break;  
        case 2:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.srp_min, 99999999.99) ";
            break;  

        case 5:
            if($ofd1 == 2)
                $sOrder = "Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.qp_min, 99999999.99) ";
            break;  
        
        default:
    }
}

if($of2 != "" && $of2 != "0" && $sOrder != "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder .= ", p.product_id desc";
            else
                $sOrder .= ", p.product_id";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder .= ", Coalesce(p.srp_min, 99999999.99) ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder .= ", Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder .= ", Coalesce(p.qp_min, 99999999.99) ";
            break;  
     
        default:
    }
}


if($of2 != "" && $of2 != "0" && $sOrder == "")
{
    switch ($of2)
    {
        case 1:
            if($ofd2 == 2)
                $sOrder = "p.product_id desc";
            else
                $sOrder = "p.product_id";
            break;  
        case 2:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.created_at, '9999-99-99') ";
            break;  
        case 3:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(p.updated_at, '9999-99-99') ";
            break;  
        case 4:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.srp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.srp_min, 99999999.99) ";
            break;  
        case 5:
            if($ofd2 == 2)
                $sOrder = "Coalesce(p.qp_max, 0.00) desc";
            else
                $sOrder = "Coalesce(p.qp_min, 99999999.99) ";
            break;  
     
        default:
    }
}

if($sOrder != "")
    $sql = $sql . " order by  " . $sOrder;
else
    $sql = $sql . " ORDER BY p.created_at desc ";
    
            if (!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if (false === $size) {
                    $size = 10;
                }
    
                $offset = ($page - 1) * $size;
    
                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }


            $cnt = 0;
            $stmt_cnt = $db->prepare( $query_cnt );
            $stmt_cnt->execute();
            while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
                $cnt = $row['cnt'];
            }

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $id = '';
                $category = '';
                $tags = '';
                $sub_category = '';
        
                $brand = '';
                $code = '';

                $description = '';

                $notes = '';
                $photo1 = '';
                $photo2 = '';
                $photo3 = '';

                $variation = '';
                $status = '';
                $create_id = '';
                $created_at = '';


                // extra information
                $info = GetProductInfo($row['product_id'], $db);


                $id = $row['id'];
                $category = GetCategory($info[0]['category'], $db);
                $sub_category = $info[0]['sub_category'];
                $tags = $info[0]['tags'];
                $sub_category_name = GetCategory($info[0]['sub_category'], $db);
                $out = $info[0]['out'];
        
                $brand = $info[0]['brand'];
                $code = $row['code'];

                $description = $row['description'];

                $photo1 = $row['photo1'];
                $photo2 = $row['photo2'];
                $photo3 = $row['photo3'];

                $variation = $row['variation'];
                $variation_array = ParseTextAsVariant($row['variation']);

                $status = $row['status'];
                $create_id = $row['create_id'];
                $created_at = $row['created_at'];
                $updated_id = $row['updated_id'];
                $updated_at = $row['updated_at'];

                $created_name = $row['created_name'];
                $updated_name = $row['updated_name'];

                $product_id = $row['product_id'];
                $p_id = $row['p_id'];

                $merged_results[] = array( "id" => $id,
                                    "product_id" => $product_id,
                                    "p_id" => $p_id,
                                    "category" => $category,
                                    "sub_category" => $sub_category,
                                    "sub_category_name" => $sub_category_name,
                                    "tags" => explode(',', $tags),
                                    "brand" => $brand,
                                    "code" => $code,
                                    "out" => $out,
                                    "description" => $description,
                                    "photo1" => $photo1,
                                    "photo2" => $photo2,
                                    "photo3" => $photo3,

                                    "variation" => $variation,
                                    "variation_array" => $variation_array,
                                    "status" => $status,
                                    "created_at" => $created_at,
                                    "create_id" => $create_id,
                                    "updated_at" => $updated_at,
                                    "updated_id" => $updated_id,
                                    "created_name" => $created_name,
                                    "updated_name" => $updated_name,
                                    "cnt" => $cnt,
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

function GetProductInfo($product_id, $db)
{
    $sql = "SELECT * FROM product_category WHERE id = '". $product_id . "' and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $merged_results[] = $row;
      
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

    $sql = $sql . " ORDER BY code ";

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
        
        $variant[] = array("category" => $key, "value" => $values);
    }
    return $variant;
}



?>
