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

    $time_start = microtime(true); 

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

            $query_cnt = "SELECT count(*) cnt FROM product_category p  left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  (p.STATUS <> -1 or (p.status = -1 and (select count(*) from product_replacement pr where pr.product_id = p.id) > 0))  ";

            // product main join product_replacement if p.Status -1 and product_replacement has data
            $sql = "SELECT p.*, cu.username created_name, uu.username updated_name, (select count(*) from product_replacement pr where pr.product_id = p.id) replacement_cnt FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  (p.STATUS <> -1 or (p.status = -1 and (select count(*) from product_replacement pr where pr.product_id = p.id) > 0)) ";
            // $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  p.STATUS <> -1";

            if (!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if (false === $page) {
                    $page = 1;
                }
            }

            if($sd != "" && $sd != "0")
            {
                $sql = $sql . " and p.id = " . $sd . " ";
                $query_cnt = $query_cnt . " and p.id = " . $sd . " ";
            }

            if($d != "" && $d != "0")
            {
                $sql = $sql . " and p.id >= " . $d . " ";
                $query_cnt = $query_cnt . " and p.id >= " . $d . " ";
            }

            if($d1 != "" && $d1 != "0")
            {
                $sql = $sql . " and p.id <= " . $d1 . " ";
                $query_cnt = $query_cnt . " and p.id <= " . $d1 . " ";
            }

            if($g != "")
            {
                $sql = $sql . " and (p.category = '" . $g . "' or p.sub_category = '" . $g . "') ";
                $query_cnt = $query_cnt . " and (p.category = '" . $g . "' or p.sub_category = '" . $g . "') ";
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
                    $tag_sql = $tag_sql . " p.tags like '%" . $tag_array[$i] . "%' and ";
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
                $sql = $sql . " and p.brand = '" . $b . "' ";
                $query_cnt = $query_cnt . " and p.brand = '" . $b . "' ";
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
                $sOrder = "p.id desc";
            else
                $sOrder = "p.id ";
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
                $sOrder .= ", p.id desc";
            else
                $sOrder .= ", p.id";
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
                $sOrder = "p.id desc";
            else
                $sOrder = "p.id";
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
                $price_ntd = '';
                $price_ntd_org = '';
                $price_ntd_change = '';
                $price_quoted = '';
                $price = '';
                $price_org = '';
                $price_change = '';
                $description = '';
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

                $p1_id = '';
                $p2_id = '';
                $p3_id = '';

                $quoted_price = '';
                $quoted_price_change = '';

                $variation1_text = "1st Variation";
                $variation2_text = "2nd Variation";
                $variation3_text = "3rd Variation";
                $variation4_text = "4th Variation";

                $special_infomation = [];
                $accessory_information = [];
                $related_product = [];
                $replacement_product = [];
                $replacement_cnt = $row['replacement_cnt'];

                $sub_cateory_item = [];

                $out = "";
                $phased_out_cnt = 0;

                $id = $row['id'];
                $category = GetCategory($row['category'], $db);
                $sub_category = $row['sub_category'];
                $tags = $row['tags'];
                $sub_category_name = GetCategory($row['sub_category'], $db);
        
                $brand = $row['brand'];
                $code = $row['code'];
                $price_ntd = $row['price_ntd'];
                $price_org = $row['price'];
                $price_ntd_org = $row['price_ntd'];
                $price = $row['price'];
                $description = $row['description'];
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
                $updated_id = $row['updated_id'];
                $updated_at = $row['updated_at'];

                $created_name = $row['created_name'];
                $updated_name = $row['updated_name'];

                $p1_id = $row['p1_id'];
                $p2_id = $row['p2_id'];
                $p3_id = $row['p3_id'];

                $qp_max = $row['qp_max'];
                $qp_min = $row['qp_min'];

                $srp_max = $row['srp_max'];
                $srp_min = $row['srp_min'];

                $product = GetProduct($id, $db);

                $product_ics = GetAttachment($id, 'product_ics', $db);
                $product_skp = GetAttachment($id, 'product_skp', $db);
                $product_manual = GetAttachment($id, 'product_manual', $db);

                if($replacement_cnt > 0)
                    $replacement_product = GetReplacementProduct($id, $db);

                $is_replacement_product = IsReplacementProduct($id, $db);

                $product_set_1 = [];
                $product_set_2 = [];
                $product_set_3 = [];

                $product_set = [];

                $product_set_cnt = 0;

                $last_order = $row['last_order'];
                $last_order_name = $row['last_order_name'];
                $last_order_at = $row['last_order_at'];
                $last_order_url = "";

                if($sub_category == '10020000')
                {
                    if($p1_id != '')
                    {
                        $product_set_cnt++;
                        $product_set_1 = GetProductSet($p1_id, $db);
                        array_push($product_set, $product_set_1);

                    }
                    if($p2_id != '')
                    {
                        $product_set_cnt++;
                        $product_set_2 = GetProductSet($p2_id, $db);
                        array_push($product_set, $product_set_2);
                    }
                    if($p3_id != '')
                    {
                        $product_set_cnt++;
                        $product_set_3 = GetProductSet($p3_id, $db);
                        array_push($product_set, $product_set_3);
                    }
                }

                $out = $row['out'];
                $phased_out_cnt = 0;
                $phased_out_text1 = [];

                // for last order
                $is_last_order_product = "";
                $order_sn = 1;
                
                for($i = 0; $i < count($product); $i++)
                {
                    if($product[$i]['enabled'] != 1)
                    {
                        $key_value_text = "";

                        $phased_out_cnt++;
                        if($product[$i]['v1'] != "")
                            $key_value_text .= $product[$i]['k1'] . " = " . $product[$i]['v1'] . ", ";
                        if($product[$i]['v2'] != "")
                            $key_value_text .= $product[$i]['k2'] . " = " . $product[$i]['v2'] . ", ";
                        if($product[$i]['v3'] != "")
                            $key_value_text .= $product[$i]['k3'] . " = " . $product[$i]['v3'] . ", ";
                        if($product[$i]['v4'] != "")
                            $key_value_text .= $product[$i]['k4'] . " = " . $product[$i]['v4'] . ", ";

                        $key_value_text = substr($key_value_text, 0, -2);
                        
                        array_push($phased_out_text1, $key_value_text);

                        
                    }

                    if($product[$i]['last_order_name'] != '')
                    {
                        $order_info = getOrderInfo($product[$i]['last_order'], $db);
                        $url = "";

                        if(count($order_info) != 0)
                        {
                            if($order_info["order_type"] == "taiwan")
                                $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "mockup")
                                $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "sample")
                                $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $product[$i]['last_order'];
                            
                            if($order_info["order_type"] == "stock")
                                $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $product[$i]['last_order'];
                        }

                        $params = str_replace("=>", " = ", $product[$i]['1st_variation']);
                        if($product[$i]['2rd_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['2rd_variation']);
                        if($product[$i]['3th_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['3th_variation']);
                        if($product[$i]['4th_variation'] != "=>")
                            $params .= ", " . str_replace("=>", " = ", $product[$i]['4th_variation']);

                        $is_last_order_product .= "(" . $order_sn++ . ") " . $params . ": <br>" . substr($product[$i]['last_order_at'], 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $product[$i]['last_order_name'] . "</a><br><br>";
                    }
                }
                $phased_out_cnt = $phased_out_cnt;

                $related_product = GetRelatedProductCode($id, $db);

                $quoted_price = $row['quoted_price'];
                $quoted_price_org = $row['quoted_price'];
                $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';
                $price_change = $row['price_change'] != '' ? substr($row['price_change'], 0, 10) : '';
                $price_ntd_change = $row['price_ntd_change'] != '' ? substr($row['price_ntd_change'], 0, 10) : '';

                $currency = $row['currency'];

                // max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, max_quoted_price_change, min_quoted_price_change
                $max_price_change = $row['max_price_change'] ? substr($row['max_price_change'], 0, 10) : $price_change;
                $min_price_change = $row['min_price_change'] ? substr($row['min_price_change'], 0, 10) : $price_change;
                $max_price_ntd_change = $row['max_price_ntd_change'] ? substr($row['max_price_ntd_change'], 0, 10) : $price_ntd_change;
                $min_price_ntd_change = $row['min_price_ntd_change'] ? substr($row['min_price_ntd_change'], 0, 10) : $price_ntd_change;
                $max_quoted_price_change = $row['max_quoted_price_change'] ? substr($row['max_quoted_price_change'], 0, 10) : $quoted_price_change;
                $min_quoted_price_change = $row['min_quoted_price_change'] ? substr($row['min_quoted_price_change'], 0, 10) : $quoted_price_change;

                $phased_out_cnt = $row['phased_out_cnt'];

                $phased_out_info = [];
                $phased_out_text = "";
                if($phased_out_cnt > 0)
                {
                    $phased_out_info = phased_out_info($id, $db);
                    for($i = 0; $i < count($phased_out_info); $i++)
                    {
                        $cn = $i + 1;
                        $phased_out_text .= "(" . $cn . ") " . ($phased_out_info[$i]["1st_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["1st_variation"]) . ", " : "");
                        $phased_out_text .= ($phased_out_info[$i]["2rd_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["2rd_variation"]) . ", " : "");
                        $phased_out_text .= ($phased_out_info[$i]["3th_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["3th_variation"]) . ", " : "");
                        $phased_out_text .= ($phased_out_info[$i]["4th_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["4th_variation"]) . ", " : "");

                        $phased_out_text = rtrim($phased_out_text, ", ");

                        if($i < count($phased_out_info) - 1)
                        {
                            $phased_out_text .= "<br/>";
                        }
                    }
                }

                $replacement_text = "";
                if($replacement_cnt > 0)
                {
                    for($i = 0; $i < count($replacement_product); $i++)
                    {
                        $cn = $i + 1;
                        $replacement_text .= "(" . $cn . ") " . $replacement_product[$i]["code"] . " (ID: " . $replacement_product[$i]["replacement_id"] . ")<br>";
                    }
                }
                

                $srp = 0;
                $srp_quoted = 0;

                // for price
                $pro_price_ntd = [];
                $pro_price_quoted = [];
                $pro_price = [];


                if(count($product) > 0)
                {
                    for($i = 0; $i < count($product); $i++)
                    {
                        if (!in_array($product[$i]['price'],$pro_price) && $product[$i]['price'] != '')
                        {
                            array_push($pro_price,$product[$i]['price']);
                        }

                        if (!in_array($product[$i]['price_ntd'],$pro_price_ntd) && $product[$i]['price_ntd'] != '')
                        {
                            array_push($pro_price_ntd,$product[$i]['price_ntd']);
                        }

                        // price_quoted
                        if (!in_array($product[$i]['quoted_price'],$pro_price_quoted) && $product[$i]['quoted_price'] != '')
                        {
                            array_push($pro_price_quoted,$product[$i]['quoted_price']);
                        }

                        if($max_price_change == '' && $product[$i]['price_change'] != '')
                        {
                            $max_price_change = $product[$i]['price_change'];
                        }

                        if($min_price_change == '' && $product[$i]['price_change'] != '')
                        {
                            $min_price_change = $product[$i]['price_change'];
                        }

                        if($max_price_ntd_change == '' && $product[$i]['price_ntd_change'] != '')
                        {
                            $max_price_ntd_change = $product[$i]['price_ntd_change'];
                        }

                        if($min_price_ntd_change == '' && $product[$i]['price_ntd_change'] != '')
                        {
                            $min_price_ntd_change = $product[$i]['price_ntd_change'];
                        }

                        if($max_quoted_price_change == '' && $product[$i]['quoted_price_change'] != '')
                        {
                            $max_quoted_price_change = $product[$i]['quoted_price_change'];
                        }

                        if($min_quoted_price_change == '' && $product[$i]['quoted_price_change'] != '')
                        {
                            $min_quoted_price_change = $product[$i]['quoted_price_change'];
                        }

                        if($product[$i]['price'] > $srp)
                        {
                            $srp = $product[$i]['price'];
                        }

                        if($product[$i]['price_change'] > $max_price_change && $product[$i]['price_change'] != '')
                        {
                            $max_price_change = $product[$i]['price_change'];
                        }

                        if($product[$i]['price_change'] < $min_price_change && $product[$i]['price_change'] != '')
                        {
                            $min_price_change = $product[$i]['price_change'];
                        }

                        if($product[$i]['price_ntd_change'] > $max_price_ntd_change && $product[$i]['price_ntd_change'] != '')
                        {
                            $max_price_ntd_change = $product[$i]['price_ntd_change'];
                        }

                        if($product[$i]['price_ntd_change'] < $min_price_ntd_change && $product[$i]['price_ntd_change'] != '')
                        {
                            $min_price_ntd_change = $product[$i]['price_ntd_change'];
                        }

                        if($product[$i]['quoted_price_change'] > $max_quoted_price_change && $product[$i]['quoted_price_change'] != '')
                        {
                            $max_quoted_price_change = $product[$i]['quoted_price_change'];
                        }

                        if($product[$i]['quoted_price_change'] < $min_quoted_price_change && $product[$i]['quoted_price_change'] != '')
                        {
                            $min_quoted_price_change = $product[$i]['quoted_price_change'];
                        }

                    }
                }

                // for last order
                $is_last_order_main = "";
                if($last_order_name != '')
                {
                    $order_info = getOrderInfo($last_order, $db);
                    $url = "";

                    if(count($order_info) != 0)
                    {
                        if($order_info["order_type"] == "taiwan")
                            $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
                        
                        if($order_info["order_type"] == "mockup")
                            $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                        
                        if($order_info["order_type"] == "sample")
                            $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                        
                        if($order_info["order_type"] == "stock")
                            $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
                    }

                    $last_order_url = $url;


                    $is_last_order_main = "Main Product: <br>" . substr($last_order_at, 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $last_order_name . "</a><br>";
                }

                $is_last_order = $is_last_order_main . $is_last_order_product;

                // $smax = 0;
                // $qmax = 0;
                // $nmax = 0;

                // $smin = 0;
                // $qmin = 0;
                // $nmin = 0;
                
                // if(count($product_set) > 0)
                // {
                //     for($i = 0; $i < count($product_set); $i++)
                //     {
                //         if (!in_array(tofloat($product_set[$i]->price),$pro_price) && tofloat($product_set[$i]->price) != '')
                //         {
                //             array_push($pro_price,tofloat($product_set[$i]->price));
                //             $smax += tofloat($product_set[$i]->price);
                //         }

                //         if (!in_array(tofloat($product_set[$i]->price_ntd),$pro_price_ntd) && tofloat($product_set[$i]->price_ntd) != '')
                //         {
                //             array_push($pro_price_ntd,tofloat($product_set[$i]->price_ntd));
                //             $nmax += tofloat($product_set[$i]->price_ntd);
                //         }

                //         // price_quoted
                //         if (!in_array(tofloat($product_set[$i]->quoted_price),$pro_price_quoted) && tofloat($product_set[$i]->quoted_price) != '')
                //         {
                //             array_push($pro_price_quoted,tofloat($product_set[$i]->quoted_price));
                //             $qmax += tofloat($product_set[$i]->quoted_price);
                //         }

                //         if($max_price_change == '' && $product_set[$i]->max_price_change != '')
                //         {
                //             $max_price_change = $product_set[$i]->max_price_change;
                //         }

                //         if($min_price_change == '' && $product_set[$i]->min_price_change != '')
                //         {
                //             $min_price_change = $product_set[$i]->min_price_change;
                //         }

                //         if($max_price_ntd_change == '' && $product_set[$i]->max_price_ntd_change != '')
                //         {
                //             $max_price_ntd_change = $product_set[$i]->max_price_ntd_change;
                //         }

                //         if($min_price_ntd_change == '' && $product_set[$i]->min_price_ntd_change != '')
                //         {
                //             $min_price_ntd_change = $product_set[$i]->min_price_ntd_change;
                //         }

                //         if($max_quoted_price_change == '' && $product_set[$i]->max_quoted_price_change != '')
                //         {
                //             $max_quoted_price_change = $product_set[$i]->max_quoted_price_change;
                //         }

                //         if($min_quoted_price_change == '' && $product_set[$i]->min_quoted_price_change != '')
                //         {
                //             $min_quoted_price_change = $product_set[$i]->min_quoted_price_change;
                //         }

                //         if(tofloat($product_set[$i]->price) > $srp)
                //         {
                //             $srp = tofloat($product_set[$i]->price);
                //         }

                //         if($product_set[$i]->max_price_change > $max_price_change && $product_set[$i]->max_price_change != '')
                //         {
                //             $max_price_change = $product_set[$i]->max_price_change;
                //         }

                //         if($product_set[$i]->min_price_change < $min_price_change && $product_set[$i]->min_price_change != '')
                //         {
                //             $min_price_change = $product_set[$i]->min_price_change;
                //         }

                //         if($product_set[$i]->max_price_ntd_change > $max_price_ntd_change && $product_set[$i]->max_price_ntd_change != '')
                //         {
                //             $max_price_ntd_change = $product_set[$i]->max_price_ntd_change;
                //         }

                //         if($product_set[$i]->min_price_ntd_change < $min_price_ntd_change && $product_set[$i]->min_price_ntd_change != '')
                //         {
                //             $min_price_ntd_change = $product_set[$i]->min_price_ntd_change;
                //         }

                //         if($product_set[$i]->max_quoted_price_change > $max_quoted_price_change && $product_set[$i]->max_quoted_price_change != '')
                //         {
                //             $max_quoted_price_change = $product_set[$i]->max_quoted_price_change;
                //         }

                //         if($product_set[$i]->min_quoted_price_change < $min_quoted_price_change && $product_set[$i]->min_quoted_price_change != '')
                //         {
                //             $min_quoted_price_change = $product_set[$i]->min_quoted_price_change;
                //         }
                //     }
                // }

                sort($pro_price);
                sort($pro_price_ntd);
                sort($pro_price_quoted);

                $s_price = "";
                if(count($pro_price) == 1)
                {
                    $pro_price[0] = $pro_price[0] + 0;

                    $s_price = "PHP " . number_format($pro_price[0]);
                    $srp = $pro_price[0];
                }
                if(count($pro_price) > 1)
                {
                    $b = "";
                    $e = "";
                    for($i=0; $i<count($pro_price); $i++)
                    {
                        if($b == "")
                            $b = $pro_price[$i];

                        $e = $pro_price[$i];
                    }
                    $b = $b + 0;
                    $e = $e + 0;
                    $s_price = "PHP " . number_format($b) . " ~ " . "PHP " . number_format($e);

                    $srp = $e;
                }

                $s_price_ntd = "";

                if(count($pro_price_ntd) == 1)
                {
                    $pro_price_ntd[0] = $pro_price_ntd[0] + 0;
                    $s_price_ntd = $currency . " " . formatPrice($pro_price_ntd[0]);
                }
                if(count($pro_price_ntd) > 1)
                {
                    $b = "";
                    $e = "";
                    for($i=0; $i<count($pro_price_ntd); $i++)
                    {
                        if($b == "")
                            $b = $pro_price_ntd[$i];

                        $e = $pro_price_ntd[$i];
                    }
                    $b = $b + 0;
                    $e = $e + 0;
                    $s_price_ntd = $currency . " " . formatPrice($b) . " ~ " . $currency . " " . formatPrice($e);
                }

                $s_price_quoted = "";
                if(count($pro_price_quoted) == 1)
                {
                    $pro_price_quoted[0] = $pro_price_quoted[0] + 0;
                    $s_price_quoted = "PHP " . number_format($pro_price_quoted[0]);
                    $srp_quoted = $pro_price_quoted[0];
                }
                if(count($pro_price_quoted) > 1)
                {
                    $b = "";
                    $e = "";
                    for($i=0; $i<count($pro_price_quoted); $i++)
                    {
                        if($b == "")
                            $b = $pro_price_quoted[$i];

                        $e = $pro_price_quoted[$i];
                    }
                    $b = $b + 0;
                    $e = $e + 0;
                    $s_price_quoted = "PHP " . number_format($b) . " ~ " . "PHP " . number_format($e);

                    $srp_quoted = $e;
                }

                $price = $price + 0;
                $price_ntd = $price_ntd + 0;
                $quoted_price = $quoted_price + 0;

                if($s_price == "")
                    $price = "PHP " .  number_format($price);
                else
                    $price = $s_price;

                if($s_price_ntd == "")
                    $price_ntd = $currency . " " .  formatPrice($price_ntd);
                else
                    $price_ntd = $s_price_ntd; 

                if($s_price_quoted == "")
                    $price_quoted = "PHP " .  number_format($quoted_price);
                else
                    $price_quoted = $s_price_quoted; 


                if($sub_category == '10020000')
                {
                    if($srp_max == $srp_min)
                    {
                        $_srp = number_format($srp_max);
                    }
                    else
                    {
                        $_srp = number_format($srp_min) . " ~ PHP " . number_format($srp_max);
                    }

                    $price = "PHP " .  $_srp;
                    
                    if($qp_max == $qp_min)
                    {
                        $_qp = number_format($qp_max);
                    }
                    else
                    {
                        $_qp = number_format($qp_min) . " ~ PHP " . number_format($qp_max);
                    }

                    $price_quoted = "PHP " .  $_qp;
                }

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
                        $custom = "";
                        $value = [];
                        $_category = $special_info_json[$i]->category;

                        if($special_info_json[$i]->value != "")
                        {
                            array_push($value, $special_info_json[$i]->value);
                           
                        }
                        
                        if($variation1_text == $special_info_json[$i]->category)
                        {
                            $value = $variation1_value;
                            $custom = "custom";
                        }
                        if($variation2_text == $special_info_json[$i]->category)
                        {
                            $value = $variation2_value;
                            $custom = "custom";
                        }
                        if($variation3_text == $special_info_json[$i]->category)
                        {
                            $value = $variation3_value;
                            $custom = "custom";
                        }
                        if($variation4_text == $special_info_json[$i]->category)
                        {
                            $value = $variation4_value;
                            $custom = "custom";
                        }

                        if(count($value) > 0)
                        {
                            $attribute_list[] = array("category" => $special_info_json[$i]->category,
                                           "value" => $value,
                                           "type" => $custom,
                                        );
                        }
                    }
                }

                if($variation1 == "custom" && $variation1_custom != "1st Variation")
                {
                    $attribute_list[] = array("category" => $variation1_text,
                                           "value" => $variation1_value,
                                           "type" => "custom",
                                        );
                }

                if($variation2 == "custom" && $variation2_custom != "2nd Variation")
                {
                    $attribute_list[] = array("category" => $variation2_text,
                                           "value" => $variation2_value,
                                           "type" => "custom",
                                        );
                }

                if($variation3 == "custom" && $variation3_custom != "3rd Variation")
                {
                    $attribute_list[] = array("category" => $variation3_text,
                                           "value" => $variation3_value,
                                           "type" => "custom",
                                        );
                }

                if($variation4 == "custom" && $variation4_custom != "4th Variation")
                {
                    $attribute_list[] = array("category" => $variation4_text,
                                           "value" => $variation4_value,
                                           "type" => "custom",
                                        );
                }

                $moq = $row['moq'];


                // 20241218 for incoming qty
                $incoming_qty = $row['incoming_qty'];
                $incoming_element = $row['incoming_element'];

                // 20250526 for project_qty, project_s_qty, stock_qty, stock_s_qty
                $project_qty = $row['project_qty'];
                $project_s_qty = $row['project_s_qty'];
                $stock_qty = $row['stock_qty'];
                $stock_s_qty = $row['stock_s_qty'];

                $incoming_html = "";
                $order_sn = 1;

                if($incoming_element != "")
                {
                    $incoming_element_json = json_decode($incoming_element, true);

                    // order by order_date asc
                    usort($incoming_element_json, function($a, $b) {
                        return strtotime($a['order_date']) - strtotime($b['order_date']);
                    });

                    for($i = 0; $i < count($incoming_element_json); $i++)
                    {
                        $key_value_text = "";
                        for($j = 0; $j < count($product); $j++)
                        {
                            if($product[$j]['v1'] == $incoming_element_json[$i]['v1'] && $product[$j]['v2'] == $incoming_element_json[$i]['v2'] && $product[$j]['v3'] == $incoming_element_json[$i]['v3'] && $product[$j]['v4'] == $incoming_element_json[$i]['v4'])
                            {
                                if($product[$j]['v1'] != "")
                                    $key_value_text .= $product[$j]['k1'] . " = " . $product[$j]['v1'] . ", ";
                                if($product[$j]['v2'] != "")
                                    $key_value_text .= $product[$j]['k2'] . " = " . $product[$j]['v2'] . ", ";
                                if($product[$j]['v3'] != "")
                                    $key_value_text .= $product[$j]['k3'] . " = " . $product[$j]['v3'] . ", ";
                                if($product[$j]['v4'] != "")
                                    $key_value_text .= $product[$j]['k4'] . " = " . $product[$j]['v4'] . ", ";

                                $key_value_text = substr($key_value_text, 0, -2);
                                
                                break;
                            }

                        }

                        // if($key_value_text == "")
                        // {
                        //     // $key_value_text = $attribute_list to string
                        //     for($j = 0; $j < count($attribute_list); $j++)
                        //     {
                        //         $key_value_text .= $attribute_list[$j]['category'] . " = " . implode(', ', $attribute_list[$j]['value']) . ", ";
                        //     }
                        // }

                        $order_info = getOrderInfo($incoming_element_json[$i]['od_id'], $db);

                        if(count($order_info) != 0)
                        {
                            if($order_info["order_type"] == "taiwan")
                                $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $incoming_element_json[$i]['od_id'];
                            
                            if($order_info["order_type"] == "mockup")
                                $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $incoming_element_json[$i]['od_id'];
                            
                            if($order_info["order_type"] == "sample")
                                $url = "https://feliix.myvnc.com/order_taiwan_sample_p3?id=" . $incoming_element_json[$i]['od_id'];
                            
                            if($order_info["order_type"] == "stock")
                                $url = "https://feliix.myvnc.com/order_taiwan_stock_p3?id=" . $incoming_element_json[$i]['od_id'];
                        }

                        $total_qty = is_numeric($incoming_element_json[$i]['qty']) ? $incoming_element_json[$i]['qty'] : 0;
                        $total_qty += is_numeric($incoming_element_json[$i]['backup_qty']) ? $incoming_element_json[$i]['backup_qty'] : 0;
        
                        $is_last_order_main = "<a href='" . $url . "' target='_blank'>" . $order_info["serial_name"] . " " . $order_info["od_name"] . "</a><br>";
                        $incoming_html .= "(" . $order_sn++ . ") Ordered Date: " . substr($incoming_element_json[$i]['order_date'], 0, 10) . ", Qty: " . $total_qty  . " <br> " . ($key_value_text != "" ? $key_value_text . " <br> " : "") . $is_last_order_main .  "<br>";
                    }
                }


                $str_price_change = "";
                if($max_price_change != "" || $min_price_change != "")
                {
                    if($min_price_change != "" && $max_price_change != "")
                    {
                        if($min_price_change == $max_price_change)
                        {
                            $str_price_change = "(" . $min_price_change . ")";
                        }else
                        {
                            $str_price_change = "(" . $min_price_change . " ~ " . $max_price_change . ")";
                        }
                    }
                    else
                    {
                        $str_price_change = "(" . $min_price_change . " ~ " . $max_price_change . ")";
                    }
                }

                $str_price_ntd_change = "";
                if($max_price_ntd_change != "" || $min_price_ntd_change != "")
                {
                    if($min_price_ntd_change != "" && $max_price_ntd_change != "")
                    {
                        if($min_price_ntd_change == $max_price_ntd_change)
                        {
                            $str_price_ntd_change = "(" . $min_price_ntd_change . ")";
                        }else
                        {
                            $str_price_ntd_change = "(" . $min_price_ntd_change . " ~ " . $max_price_ntd_change . ")";
                        }
                    }
                    else
                    {
                        $str_price_ntd_change = "(" . $min_price_ntd_change . " ~ " . $max_price_ntd_change . ")";
                    }
                }

                $str_quoted_price_change = "";
                if($max_quoted_price_change != "" || $min_quoted_price_change != "")
                {
                    if($max_quoted_price_change != "" && $min_quoted_price_change != "")
                    {
                        if($max_quoted_price_change == $min_quoted_price_change)
                        {
                            $str_quoted_price_change = "(" . $min_quoted_price_change . ")";
                        }else
                        {
                            $str_quoted_price_change = "(" . $min_quoted_price_change . " ~ " . $max_quoted_price_change . ")";
                        }
                    }
                    else
                    {
                        $str_quoted_price_change = "(" . $min_quoted_price_change . " ~ " . $max_quoted_price_change . ")";
                    }
                }

                $merged_results[] = array( "id" => $id,
                                    "category" => $category,
                                    "sub_category" => $sub_category,
                                    "sub_category_name" => $sub_category_name,
                                    "tags" => explode(',', $tags),
                                    "brand" => $brand,
                                    "code" => $code,
                                    "price_ntd" => $price_ntd,
                                    "price" => $price,
                                    "quoted_price" => $price_quoted,
                                    "price_ntd_org" => $price_ntd_org,
                                    "price_org" => $price_org,
                                    "quoted_price_org" => $quoted_price_org,
                                    "description" => $description,
                                    "photo1" => $photo1,
                                    "photo2" => $photo2,
                                    "photo3" => $photo3,
                                    "accessory_mode" => $accessory_mode,
                                    "variation_mode" => $variation_mode,
                                    "variation" => $variation,
                                    "status" => $status,
                                    "created_at" => $created_at,
                                    "create_id" => $create_id,
                                    "updated_at" => $updated_at,
                                    "updated_id" => $updated_id,
                                    "created_name" => $created_name,
                                    "updated_name" => $updated_name,
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
                                    "attribute_list" => $attribute_list,
                                    "sub_category_item" => $sub_category_item,
                                    "special_information" => $special_information,
                                    "moq" => $moq,
                                    "out" => $out,
                                    "notes" => $notes,
                                    "related_product" => $related_product,
                                    "cnt" => $cnt,
                                    "srp" => $srp,
                                    "srp_quoted" => $srp_quoted,
                                    "max_price_change" => $max_price_change,
                                    "min_price_change" => $min_price_change,
                                    "max_price_ntd_change" => $max_price_ntd_change,
                                    "min_price_ntd_change" => $min_price_ntd_change,
                                    "max_quoted_price_change" => $max_quoted_price_change,
                                    "min_quoted_price_change" => $min_quoted_price_change,

                                    "str_price_change" => $str_price_change,
                                    "str_price_ntd_change" => $str_price_ntd_change,
                                    "str_quoted_price_change" => $str_quoted_price_change,

                                    "phased_out_cnt" => $phased_out_cnt,
                                    "phased_out_info" => $phased_out_info,
                                    "phased_out_text" => $phased_out_text,

                                    "replacement_cnt" => $replacement_cnt,
                                    "replacement_product" => $replacement_product,
                                    "replacement_text" => $replacement_text,

                                    "phased_out_text1" => $phased_out_text1,

                                    "product_set" => $product_set,

                                    "product_set_cnt" => $product_set_cnt,

                                    "last_order" => $last_order,
                                    "last_order_name" => $last_order_name,
                                    "is_last_order" => $is_last_order,
                                    "last_order_at" => substr($last_order_at,0, 10),
                                    "last_order_url" => "",
                                    "last_have_spec" => true,

                                    "incoming_qty" => $incoming_qty,
                                    "incoming_element" => $incoming_element,
                                    "incoming_html" => $incoming_html,

                                    "project_qty" => $project_qty,
                                    "project_s_qty" => $project_s_qty,
                                    "stock_qty" => $stock_qty,
                                    "stock_s_qty" => $stock_s_qty,

                                    "product_ics" => $product_ics,
                                    "product_skp" => $product_skp,
                                    "product_manual" => $product_manual,

                                    "is_replacement_product" => $is_replacement_product,
                );
            }

            
            $time_end = microtime(true);

            $execution_time = ($time_end - $time_start);
            // echo '<b>Total</b> '.$execution_time.'<br/>';

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

    $time_start = microtime(true); 

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

        $fir = $row['1st_variation'];
        $sec = $row['2rd_variation'];
        $thi = $row['3th_variation'];
        $fth = $row['4th_variation'];

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

        $replacement_product = GetReplacementProduct($id, $db);

        $replacement_text = "";
        if(count($replacement_product) > 0)
        {
            for($i = 0; $i < count($replacement_product); $i++)
            {
                $cn = $i + 1;
                $replacement_text .= "(" . $cn . ") " . $replacement_product[$i]["code"] . " (ID: " . $replacement_product[$i]["replacement_id"] . ")<br>";
            }
        }

        $quoted_price = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];

        if($last_order != "")
            {
                $order_info = getOrderInfo($last_order, $db);
                $last_order_url = "";
    
                if(count($order_info) != 0)
                {
                    if($order_info["order_type"] == "taiwan")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "mockup")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "sample")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                    
                    if($order_info["order_type"] == "stock")
                        $last_order_url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
                }
            }
            else
            {
                $last_order_url = "";
            }

        $merged_results[] = array(  "id" => $id, 
                                    "k1" => $k1, 
                                    "k2" => $k2, 
                                    "k3" => $k3, 
                                    "k4" => $k4,
                                    "v1" => $v1, 
                                    "v2" => $v2, 
                                    "v3" => $v3, 
                                    "v4" => $v4,
                                    "1st_variation" => $fir,
                                    "2rd_variation" => $sec,
                                    "3th_variation" => $thi,
                                    "4th_variation" => $fth,

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
                                   "last_order" => $last_order,
                                    "last_order_name" => $last_order_name,
                                    "last_order_at" => substr($last_order_at,0, 10),
                                    "last_order_url" => $last_order_url,
                                    "last_have_spec" => true,

                                    "replacement_product" => $replacement_product,
                                    "replacement_text" => $replacement_text,

            );
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetProduct</b> '.$execution_time.'<br/>';
    
    return $merged_results;
}

function GetCategory($cat_id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT category FROM product_category_attribute WHERE cat_id = '". $cat_id . "' and STATUS <> -1";

    $merged_results = "";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $merged_results = $row['category'];
      
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetProduct</b> '.$execution_time.'<br/>';

    return $merged_results;
}

function GetSubCategoryItem($cat_id, $db){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetSubCategoryItem</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetAccessory($id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT * FROM accessory WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();


    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetAccessory</b> '.$execution_time.'<br/>';
    
    return $merged_results;
}

function GetSpecialInfomation($cat_id, $db, $special_info_json){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetSpecialInfomation</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetLevel3_value($cat_id, $db, $special_info_json){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetLevel3_value</b> '.$execution_time.'<br/>';

    return $merged_results;

}


function GetLevel3($cat_id, $db){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetLevel3</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function IsReplacementProduct($id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT * FROM product_replacement WHERE product_id = ". $id . " and STATUS <> -1";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>IsReplacementProduct</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetRelatedProductCode($id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT * FROM product_category where code in (SELECT code FROM product_related WHERE product_id = '". $id . "' and STATUS <> -1) and status <> -1";

    $sql = $sql . " ORDER BY code ";

    $merged_results = [];

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_id = $row['id'];
        $currency = $row['currency'];

        $product = GetProduct($_id, $db, $currency);
        $phased_out_cnt = 0;
        $phased_out_text = [];
        for($i = 0; $i < count($product); $i++)
        {
            if($product[$i]['enabled'] != 1)
            {
                $key_value_text = "";

                $phased_out_cnt++;
                if($product[$i]['v1'] != "")
                    $key_value_text .= $product[$i]['k1'] . " = " . $product[$i]['v1'] . ", ";
                if($product[$i]['v2'] != "")
                    $key_value_text .= $product[$i]['k2'] . " = " . $product[$i]['v2'] . ", ";
                if($product[$i]['v3'] != "")
                    $key_value_text .= $product[$i]['k3'] . " = " . $product[$i]['v3'] . ", ";
                if($product[$i]['v4'] != "")
                    $key_value_text .= $product[$i]['k4'] . " = " . $product[$i]['v4'] . ", ";

                $key_value_text = substr($key_value_text, 0, -2);

                array_push($phased_out_text, $key_value_text);
            }
                
        }

        $row['phased_out_cnt'] = $phased_out_cnt;
        $row['phased_out_text'] = $phased_out_text;
        
        $merged_results[] = $row;
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetRelatedProductCode</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetDetail($cat_id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT cat_id, sn, `option` FROM product_category_attribute_detail WHERE cat_id = '". $cat_id . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY sn ";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetDetail</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function phased_out_info($id, $db){

    $time_start = microtime(true); 

    $sql = "SELECT * FROM product WHERE product_id = ". $id . " and enabled = 0";

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>phased_out_info</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetAccessoryInfomation($cat_id, $db, $product_id){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetAccessoryInfomation</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetAccessoryInfomationDetail($cat_id, $product_id, $db){

    $time_start = microtime(true); 

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

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetAccessoryInfomationDetail</b> '.$execution_time.'<br/>';

    return $merged_results;

}

function GetProductSetContent($id, $db){

    $time_start = microtime(true); 

    $merged_results = array();

    // product main
    $sql = "SELECT p.*, cu.username created_name, uu.username updated_name FROM product_category p left join `user` cu on cu.id = p.create_id left join `user` uu on uu.id = p.updated_id WHERE  (p.STATUS <> -1 or (p.status = -1 and (select count(*) from product_replacement pr where pr.product_id = p.id) > 0)) ";



        $sql = $sql . " and p.id = " . $id . " ";


    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = '';
        $category = '';
        $tags = '';
        $sub_category = '';

        $brand = '';
        $code = '';
        $price_ntd = '';
        $price_ntd_org = '';
        $price_ntd_change = '';
        $price_quoted = '';
        $price = '';
        $price_org = '';
        $price_change = '';
        $description = '';
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

        $quoted_price = '';
        $quoted_price_change = '';

        $variation1_text = "1st Variation";
        $variation2_text = "2nd Variation";
        $variation3_text = "3rd Variation";
        $variation4_text = "4th Variation";

        $special_infomation = [];
        $accessory_information = [];
        $related_product = [];

        $sub_cateory_item = [];

        $out = "";
        $phased_out_cnt = 0;

        $id = $row['id'];
        $category = GetCategory($row['category'], $db);
        $sub_category = $row['sub_category'];
        $tags = $row['tags'];
        $sub_category_name = GetCategory($row['sub_category'], $db);

        $brand = $row['brand'];

        $pid = $row['id'];
        
        $code = $row['code'];
        $price_ntd = $row['price_ntd'];
        $price_org = $row['price'];
        $price_ntd_org = $row['price_ntd'];
        $price = $row['price'];
        $description = $row['description'];
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
        $updated_id = $row['updated_id'];
        $updated_at = $row['updated_at'];

        $created_name = $row['created_name'];
        $updated_name = $row['updated_name'];

        $last_order = $row['last_order'];
        $last_order_name = $row['last_order_name'];
        $last_order_at = $row['last_order_at'];

        $product = GetProduct($id, $db);

        $product_ics = GetAttachment($id, 'product_ics', $db);
        $product_skp = GetAttachment($id, 'product_skp', $db);
        $product_manual = GetAttachment($id, 'product_manual', $db);

        $out = $row['out'];
        $phased_out_cnt = 0;
        $phased_out_text1 = [];

        // for last order
        $is_last_order_product = "";
        $order_sn = 1;

        for($i = 0; $i < count($product); $i++)
        {
            if($product[$i]['enabled'] != 1)
            {
                $key_value_text = "";

                $phased_out_cnt++;
                if($product[$i]['v1'] != "")
                    $key_value_text .= $product[$i]['k1'] . " = " . $product[$i]['v1'] . ", ";
                if($product[$i]['v2'] != "")
                    $key_value_text .= $product[$i]['k2'] . " = " . $product[$i]['v2'] . ", ";
                if($product[$i]['v3'] != "")
                    $key_value_text .= $product[$i]['k3'] . " = " . $product[$i]['v3'] . ", ";
                if($product[$i]['v4'] != "")
                    $key_value_text .= $product[$i]['k4'] . " = " . $product[$i]['v4'] . ", ";

                $key_value_text = substr($key_value_text, 0, -2);
                
                array_push($phased_out_text1, $key_value_text);
            }

            if($product[$i]['last_order_name'] != '')
            {
                $order_info = getOrderInfo($product[$i]['last_order'], $db);
                $url = "";

                if(count($order_info) != 0)
                {
                    if($order_info["order_type"] == "taiwan")
                        $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $product[$i]['last_order'];
                    
                    if($order_info["order_type"] == "mockup")
                        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $product[$i]['last_order'];
                    
                    if($order_info["order_type"] == "sample")
                        $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $product[$i]['last_order'];
                    
                    if($order_info["order_type"] == "stock")
                        $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $product[$i]['last_order'];
                }

                $params = str_replace("=>", " = ", $product[$i]['1st_variation']);
                if($product[$i]['2rd_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['2rd_variation']);
                if($product[$i]['3th_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['3th_variation']);
                if($product[$i]['4th_variation'] != "=>")
                    $params .= ", " . str_replace("=>", " = ", $product[$i]['4th_variation']);

                $is_last_order_product .= "(" . $order_sn++ . ") " . $params . ": <br>" . substr($product[$i]['last_order_at'], 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $product[$i]['last_order_name'] . "</a><br><br>";
            }
            
        }
        $phased_out_cnt = $phased_out_cnt;

        $related_product = GetRelatedProductCode($id, $db);

        $is_replacement_product = IsReplacementProduct($id, $db);

        $quoted_price = $row['quoted_price'];
        $quoted_price_org = $row['quoted_price'];
        $quoted_price_change = $row['quoted_price_change'] != '' ? substr($row['quoted_price_change'], 0, 10) : '';
        $price_change = $row['price_change'] != '' ? substr($row['price_change'], 0, 10) : '';
        $price_ntd_change = $row['price_ntd_change'] != '' ? substr($row['price_ntd_change'], 0, 10) : '';

        $currency = $row['currency'];

        // max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, max_quoted_price_change, min_quoted_price_change
        $max_price_change = $row['max_price_change'] ? substr($row['max_price_change'], 0, 10) : $price_change;
        $min_price_change = $row['min_price_change'] ? substr($row['min_price_change'], 0, 10) : $price_change;
        $max_price_ntd_change = $row['max_price_ntd_change'] ? substr($row['max_price_ntd_change'], 0, 10) : $price_ntd_change;
        $min_price_ntd_change = $row['min_price_ntd_change'] ? substr($row['min_price_ntd_change'], 0, 10) : $price_ntd_change;
        $max_quoted_price_change = $row['max_quoted_price_change'] ? substr($row['max_quoted_price_change'], 0, 10) : $quoted_price_change;
        $min_quoted_price_change = $row['min_quoted_price_change'] ? substr($row['min_quoted_price_change'], 0, 10) : $quoted_price_change;

        $phased_out_cnt = $row['phased_out_cnt'];

        $replacement_product = GetReplacementProduct($id, $db);

        $replacement_text = "";
        if(count($replacement_product) > 0)
        {
            for($i = 0; $i < count($replacement_product); $i++)
            {
                $cn = $i + 1;
                $replacement_text .= "(" . $cn . ") " . $replacement_product[$i]["code"] . " (ID: " . $replacement_product[$i]["replacement_id"] . ")<br>";
            }
        }

        $phased_out_info = [];
        $phased_out_text = "";
        if($phased_out_cnt > 0)
        {
            $phased_out_info = phased_out_info($id, $db);
            for($i = 0; $i < count($phased_out_info); $i++)
            {
                $cn = $i + 1;
                $phased_out_text .= "(" . $cn . ") " . ($phased_out_info[$i]["1st_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["1st_variation"]) . ", " : "");
                $phased_out_text .= ($phased_out_info[$i]["2rd_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["2rd_variation"]) . ", " : "");
                $phased_out_text .= ($phased_out_info[$i]["3th_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["3th_variation"]) . ", " : "");
                $phased_out_text .= ($phased_out_info[$i]["4th_variation"] != "=>" ? str_replace('=>', ' = ', $phased_out_info[$i]["4th_variation"]) . ", " : "");

                $phased_out_text = rtrim($phased_out_text, ", ");

                if($i < count($phased_out_info) - 1)
                {
                    $phased_out_text .= "<br/>";
                }
            }
        }
        

        $srp = 0;
        $srp_quoted = 0;

        // for price
        $pro_price_ntd = [];
        $pro_price_quoted = [];
        $pro_price = [];

        // for last order
        $is_last_order = 0;

        if($row['price'] != null)
            array_push($pro_price,$row['price']);
        if($row['price_ntd'] != null)
            array_push($pro_price_ntd,$row['price_ntd']);
        if($row['quoted_price'] != null)
            array_push($pro_price_quoted,$row['quoted_price']);

        if(count($product) > 0)
        {
            for($i = 0; $i < count($product); $i++)
            {
                if (!in_array($product[$i]['price'],$pro_price) && $product[$i]['price'] != '')
                {
                    array_push($pro_price,$product[$i]['price']);
                }

                if (!in_array($product[$i]['price_ntd'],$pro_price_ntd) && $product[$i]['price_ntd'] != '')
                {
                    array_push($pro_price_ntd,$product[$i]['price_ntd']);
                }

                // price_quoted
                if (!in_array($product[$i]['quoted_price'],$pro_price_quoted) && $product[$i]['quoted_price'] != '')
                {
                    array_push($pro_price_quoted,$product[$i]['quoted_price']);
                }

                if($max_price_change == '' && $product[$i]['price_change'] != '')
                {
                    $max_price_change = $product[$i]['price_change'];
                }

                if($min_price_change == '' && $product[$i]['price_change'] != '')
                {
                    $min_price_change = $product[$i]['price_change'];
                }

                if($max_price_ntd_change == '' && $product[$i]['price_ntd_change'] != '')
                {
                    $max_price_ntd_change = $product[$i]['price_ntd_change'];
                }

                if($min_price_ntd_change == '' && $product[$i]['price_ntd_change'] != '')
                {
                    $min_price_ntd_change = $product[$i]['price_ntd_change'];
                }

                if($max_quoted_price_change == '' && $product[$i]['quoted_price_change'] != '')
                {
                    $max_quoted_price_change = $product[$i]['quoted_price_change'];
                }

                if($min_quoted_price_change == '' && $product[$i]['quoted_price_change'] != '')
                {
                    $min_quoted_price_change = $product[$i]['quoted_price_change'];
                }

                if($product[$i]['price'] > $srp)
                {
                    $srp = $product[$i]['price'];
                }

                if($product[$i]['price_change'] > $max_price_change && $product[$i]['price_change'] != '')
                {
                    $max_price_change = $product[$i]['price_change'];
                }

                if($product[$i]['price_change'] < $min_price_change && $product[$i]['price_change'] != '')
                {
                    $min_price_change = $product[$i]['price_change'];
                }

                if($product[$i]['price_ntd_change'] > $max_price_ntd_change && $product[$i]['price_ntd_change'] != '')
                {
                    $max_price_ntd_change = $product[$i]['price_ntd_change'];
                }

                if($product[$i]['price_ntd_change'] < $min_price_ntd_change && $product[$i]['price_ntd_change'] != '')
                {
                    $min_price_ntd_change = $product[$i]['price_ntd_change'];
                }

                if($product[$i]['quoted_price_change'] > $max_quoted_price_change && $product[$i]['quoted_price_change'] != '')
                {
                    $max_quoted_price_change = $product[$i]['quoted_price_change'];
                }

                if($product[$i]['quoted_price_change'] < $min_quoted_price_change && $product[$i]['quoted_price_change'] != '')
                {
                    $min_quoted_price_change = $product[$i]['quoted_price_change'];
                }

            }
        }

        // for last order
        $is_last_order_main = "";
        if($last_order_name != '')
        {
            $order_info = getOrderInfo($last_order, $db);
            $url = "";
            if(count($order_info) != 0)
            {
                if($order_info["order_type"] == "taiwan")
                    $url = "https://feliix.myvnc.com/order_taiwan_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "mockup")
                    $url = "https://feliix.myvnc.com/order_taiwan_mockup_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "sample")
                    $url = "https://feliix.myvnc.com/order_taiwan_sample_p4?id=" . $last_order;
                
                if($order_info["order_type"] == "stock")
                    $url = "https://feliix.myvnc.com/order_taiwan_stock_p4?id=" . $last_order;
            }

            $is_last_order_main = "Main Product: <br>" . substr($last_order_at, 0, 10) . " at <a href='" . $url . "' target='_blank'>" .  $last_order_name . "</a><br>";
        }

        $is_last_order = $is_last_order_main . $is_last_order_product;


        $pro_price = array_unique($pro_price);
        $pro_price_ntd = array_unique($pro_price_ntd);
        $pro_price_quoted = array_unique($pro_price_quoted);

        sort($pro_price);
        sort($pro_price_ntd);
        sort($pro_price_quoted);

        $s_price = "";
        if(count($pro_price) == 1)
        {
            $pro_price[0] = $pro_price[0] + 0;

            $s_price = "PHP " . number_format($pro_price[0]);
            $srp = $pro_price[0];
        }
        if(count($pro_price) > 1)
        {
            $b = "";
            $e = "";
            for($i=0; $i<count($pro_price); $i++)
            {
                if($b == "")
                    $b = $pro_price[$i];

                $e = $pro_price[$i];
            }
            $b = $b + 0;
            $e = $e + 0;
            $s_price = "PHP " . number_format($b) . " ~ " . "PHP " . number_format($e);

            $srp = $e;
        }

        $s_price_ntd = "";

        if(count($pro_price_ntd) == 1)
        {
            $pro_price_ntd[0] = $pro_price_ntd[0] + 0;
            $s_price_ntd = $currency . " " . formatPrice($pro_price_ntd[0]);
        }
        if(count($pro_price_ntd) > 1)
        {
            $b = "";
            $e = "";
            for($i=0; $i<count($pro_price_ntd); $i++)
            {
                if($b == "")
                    $b = $pro_price_ntd[$i];

                $e = $pro_price_ntd[$i];
            }
            $b = $b + 0;
            $e = $e + 0;
            $s_price_ntd = $currency . " " . formatPrice($b) . " ~ " . $currency . " " . formatPrice($e);
        }

        $s_price_quoted = "";
        if(count($pro_price_quoted) == 1)
        {
            $pro_price_quoted[0] = $pro_price_quoted[0] + 0;
            $s_price_quoted = "PHP " . number_format($pro_price_quoted[0]);
            $srp_quoted = $pro_price_quoted[0];
        }
        if(count($pro_price_quoted) > 1)
        {
            $b = "";
            $e = "";
            for($i=0; $i<count($pro_price_quoted); $i++)
            {
                if($b == "")
                    $b = $pro_price_quoted[$i];

                $e = $pro_price_quoted[$i];
            }
            $b = $b + 0;
            $e = $e + 0;
            $s_price_quoted = "PHP " . number_format($b) . " ~ " . "PHP " . number_format($e);

            $srp_quoted = $e;
        }

        $price = $price + 0;
        $price_ntd = $price_ntd + 0;
        $quoted_price = $quoted_price + 0;

        if($s_price == "")
            $price = "PHP " .  number_format($price);
        else
            $price = $s_price;

        if($s_price_ntd == "")
            $price_ntd = $currency . " " .  formatPrice($price_ntd);
        else
            $price_ntd = $s_price_ntd; 

        if($s_price_quoted == "")
            $price_quoted = "PHP " .  number_format($quoted_price);
        else
            $price_quoted = $s_price_quoted; 

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

        $moq = $row['moq'];

        // 20241218 for incoming qty
        $incoming_qty = $row['incoming_qty'];
        $incoming_element = $row['incoming_element'];
        $incoming_html = "";
        $order_sn = 1;

        // 20250526 for project_qty, project_s_qty, stock_qty, stock_s_qty
        $project_qty = $row['project_qty'];
        $project_s_qty = $row['project_s_qty'];
        $stock_qty = $row['stock_qty'];
        $stock_s_qty = $row['stock_s_qty'];

        if($incoming_element != "")
        {
            $incoming_element_json = json_decode($incoming_element, true);

            // order by order_date asc
            usort($incoming_element_json, function($a, $b) {
                return strtotime($a['order_date']) - strtotime($b['order_date']);
            });

            for($i = 0; $i < count($incoming_element_json); $i++)
            {
                $key_value_text = "";
                for($j = 0; $j < count($product); $j++)
                {
                    if($product[$j]['v1'] == $incoming_element_json[$i]['v1'] && $product[$j]['v2'] == $incoming_element_json[$i]['v2'] && $product[$j]['v3'] == $incoming_element_json[$i]['v3'] && $product[$j]['v4'] == $incoming_element_json[$i]['v4'])
                    {
                        if($product[$j]['v1'] != "")
                            $key_value_text .= $product[$j]['k1'] . " = " . $product[$j]['v1'] . ", ";
                        if($product[$j]['v2'] != "")
                            $key_value_text .= $product[$j]['k2'] . " = " . $product[$j]['v2'] . ", ";
                        if($product[$j]['v3'] != "")
                            $key_value_text .= $product[$j]['k3'] . " = " . $product[$j]['v3'] . ", ";
                        if($product[$j]['v4'] != "")
                            $key_value_text .= $product[$j]['k4'] . " = " . $product[$j]['v4'] . ", ";

                        $key_value_text = substr($key_value_text, 0, -2);
                        
                        break;
                    }

                }

                // if($key_value_text == "")
                // {
                //     // $key_value_text = $attribute_list to string
                //     for($j = 0; $j < count($attribute_list); $j++)
                //     {
                //         $key_value_text .= $attribute_list[$j]['category'] . " = " . implode(', ', $attribute_list[$j]['value']) . ", ";
                //     }
                // }

                $order_info = getOrderInfo($incoming_element_json[$i]['od_id'], $db);

                if(count($order_info) != 0)
                {
                    if($order_info["order_type"] == "taiwan")
                        $url = "https://feliix.myvnc.com/order_taiwan_p3?id=" . $incoming_element_json[$i]['od_id'];
                    
                    if($order_info["order_type"] == "mockup")
                        $url = "https://feliix.myvnc.com/order_taiwan_mockup_p3?id=" . $incoming_element_json[$i]['od_id'];
                    
                    if($order_info["order_type"] == "sample")
                        $url = "https://feliix.myvnc.com/order_taiwan_sample_p3?id=" . $incoming_element_json[$i]['od_id'];
                    
                    if($order_info["order_type"] == "stock")
                        $url = "https://feliix.myvnc.com/order_taiwan_stock_p3?id=" . $incoming_element_json[$i]['od_id'];
                }

                $total_qty = is_numeric($incoming_element_json[$i]['qty']) ? $incoming_element_json[$i]['qty'] : 0;
                $total_qty += is_numeric($incoming_element_json[$i]['backup_qty']) ? $incoming_element_json[$i]['backup_qty'] : 0;

                $is_last_order_main = "<a href='" . $url . "' target='_blank'>" . $order_info["serial_name"] . " " . $order_info["od_name"] . "</a><br>";
                $incoming_html .= "(" . $order_sn++ . ") Ordered Date: " . substr($incoming_element_json[$i]['order_date'], 0, 10) . ", Qty: " . $total_qty  . " <br> " . ($key_value_text != "" ? $key_value_text . " <br> " : "") . $is_last_order_main .  "<br>";
            }
        }

        $str_price_change = "";
        if($max_price_change != "" || $min_price_change != "")
        {
            if($min_price_change != "" && $max_price_change != "")
            {
                if($min_price_change == $max_price_change)
                {
                    $str_price_change = "(" . $min_price_change . ")";
                }else
                {
                    $str_price_change = "(" . $min_price_change . " ~ " . $max_price_change . ")";
                }
            }
            else
            {
                $str_price_change = "(" . $min_price_change . " ~ " . $max_price_change . ")";
            }
        }

        $str_price_ntd_change = "";
        if($max_price_ntd_change != "" || $min_price_ntd_change != "")
        {
            if($min_price_ntd_change != "" && $max_price_ntd_change != "")
            {
                if($min_price_ntd_change == $max_price_ntd_change)
                {
                    $str_price_ntd_change = "(" . $min_price_ntd_change . ")";
                }else
                {
                    $str_price_ntd_change = "(" . $min_price_ntd_change . " ~ " . $max_price_ntd_change . ")";
                }
            }
            else
            {
                $str_price_ntd_change = "(" . $min_price_ntd_change . " ~ " . $max_price_ntd_change . ")";
            }
        }

        $str_quoted_price_change = "";
        if($max_quoted_price_change != "" || $min_quoted_price_change != "")
        {
            if($max_quoted_price_change != "" && $min_quoted_price_change != "")
            {
                if($max_quoted_price_change == $min_quoted_price_change)
                {
                    $str_quoted_price_change = "(" . $min_quoted_price_change . ")";
                }else
                {
                    $str_quoted_price_change = "(" . $min_quoted_price_change . " ~ " . $max_quoted_price_change . ")";
                }
            }
            else
            {
                $str_quoted_price_change = "(" . $min_quoted_price_change . " ~ " . $max_quoted_price_change . ")";
            }
        }

        $merged_results = array( "id" => $id,
                            "category" => $category,
                            "sub_category" => $sub_category,
                            "sub_category_name" => $sub_category_name,
                            "tags" => explode(',', $tags),
                            "brand" => $brand,
                            "code" => $code,
                            "price_ntd" => $price_ntd,
                            "price" => $price,
                            "quoted_price" => $price_quoted,
                            "price_ntd_org" => $price_ntd_org,
                            "price_org" => $price_org,
                            "quoted_price_org" => $quoted_price_org,
                            "description" => $description,
                            "photo1" => $photo1,
                            "photo2" => $photo2,
                            "photo3" => $photo3,
                            "accessory_mode" => $accessory_mode,
                            "variation_mode" => $variation_mode,
                            "variation" => $variation,
                            "status" => $status,
                            "created_at" => $created_at,
                            "create_id" => $create_id,
                            "updated_at" => $updated_at,
                            "updated_id" => $updated_id,
                            "created_name" => $created_name,
                            "updated_name" => $updated_name,
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
                            "attribute_list" => $attribute_list,
                            "sub_category_item" => $sub_category_item,
                            "special_information" => $special_information,
                            "moq" => $moq,
                            "out" => $out,
                            "notes" => $notes,
                            "related_product" => $related_product,
                            "srp" => $srp,
                            "srp_quoted" => $srp_quoted,
                            "max_price_change" => $max_price_change,
                            "min_price_change" => $min_price_change,
                            "max_price_ntd_change" => $max_price_ntd_change,
                            "min_price_ntd_change" => $min_price_ntd_change,
                            "max_quoted_price_change" => $max_quoted_price_change,
                            "min_quoted_price_change" => $min_quoted_price_change,

                            "str_price_change" => $str_price_change,
                            "str_price_ntd_change" => $str_price_ntd_change,
                            "str_quoted_price_change" => $str_quoted_price_change,

                            "phased_out_cnt" => $phased_out_cnt,
                            "phased_out_info" => $phased_out_info,
                            "phased_out_text" => $phased_out_text,

                            "replacement_product" => $replacement_product,
                            "replacement_text" => $replacement_text,

                            "out" => $out,
                            "phased_out_cnt" => $phased_out_cnt,

                            "phased_out_text1" => $phased_out_text1,

                            "last_order" => $last_order,
                            "last_order_name" => $last_order_name,
                            "is_last_order" => $is_last_order,

                            "incoming_qty" => $incoming_qty,
                            "incoming_element" => $incoming_element,
                            "incoming_html" => $incoming_html,

                            "project_qty" => $project_qty,
                            "project_s_qty" => $project_s_qty,
                            "stock_qty" => $stock_qty,
                            "stock_s_qty" => $stock_s_qty,

                            "product_ics" => $product_ics,
                            "product_skp" => $product_skp,
                            "product_manual" => $product_manual,

                            "is_replacement_product" => $is_replacement_product,

        );
    }

    file_put_contents('set_cache' . $id . '.txt', serialize(json_encode($merged_results)));

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetProductSetContent</b> '.$execution_time.'<br/>';

    return $merged_results;
}

function GetProductSet($id, $db){

    $time_start = microtime(true); 

    $merged_results = array();

    if(file_exists('set_cache' . $id . '.txt'))
    {
        if( filemtime('set_cache' . $id . '.txt') > time()-1*1800)
        {
            $merged_results = json_decode(unserialize(file_get_contents('set_cache' . $id . '.txt')));
            
        }
        else
        {
            $merged_results = GetProductSetContent($id, $db);
        }
    }
    else
    {
        $merged_results = GetProductSetContent($id, $db);
    }

    $time_end = microtime(true);

    $execution_time = ($time_end - $time_start);
    // echo '<b>GetProductSet</b> '.$execution_time.'<br/>';

    return $merged_results;
}


function tofloat($numberString) {
    return floatval(preg_replace("/[^0-9.]/", '', $numberString));
}

function getOrderInfo($od_id, $db)
{
    $sql = "select order_type, serial_name, status, od_name from od_main WHERE id = ". $od_id;

    $merged_results = array();

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = $row;
    }

    return $merged_results;
}

function GetReplacementProduct($id, $db){
    $sql = "SELECT id, id replacement_id, code, photo1 FROM product_category where id in (SELECT replacement_id FROM product_replacement WHERE product_id = ". $id . " and STATUS <> -1)";

    $sql = $sql . " ORDER BY code ";

    $merged_results = [];

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = array(  "id" => $row['id'],
                                    "replacement_id" => $row['replacement_id'], 
                                    "code" => $row['code'],
                                    "photo1" => $row['photo1'],
                                   
            );
    }

    return $merged_results;

}

function formatPrice($price) {
    if (floor($price) == $price) {
        return number_format($price, 0); // No decimal places for whole numbers
    } else {
        return number_format($price, 2); // Two decimal places for float values
    }
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
