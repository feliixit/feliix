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

    $query = "INSERT INTO product_category
    (category, sub_category, brand, `code`, price_ntd, price, `description`, 
    photo1, photo2, photo3, accessory_mode, attributes, variation_mode, variation, notes, price_ntd_change, 
    price_change, quoted_price, quoted_price_change, moq, `tags`, related_product, `OUT`, currency, srp_max, 
    srp_min, qp_max, qp_min, max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, 
    p1_code, p2_code, p3_code, p1_id, p2_id, p3_id, p1_qty, p2_qty, p3_qty, 
    max_quoted_price_change, min_quoted_price_change, phased_out_cnt, print_option, create_id)
    SELECT category, sub_category, brand, `code`, price_ntd, price, `description`, 
    photo1, photo2, photo3, accessory_mode, attributes, variation_mode, variation, notes, price_ntd_change, 
    price_change, quoted_price, quoted_price_change, moq, `tags`, related_product, `OUT`, currency, srp_max, 
    srp_min, qp_max, qp_min, max_price_change, min_price_change, max_price_ntd_change, min_price_ntd_change, 
    p1_code, p2_code, p3_code, p1_id, p2_id, p3_id, p1_qty, p2_qty, p3_qty,
    max_quoted_price_change, min_quoted_price_change, phased_out_cnt, print_option, :updated_id FROM 
    product_category WHERE id = :id";

    // prepare the query
    $stmt = $db->prepare($query);

    // bind the values
    $stmt->bindParam(':updated_id', $uid);
    $stmt->bindParam(':id', $id);

    $last_id = 0;

    try {
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            $last_id = $db->lastInsertId();

            $query = "INSERT INTO product (category_id, 1st_variation, 2rd_variation, 3th_variation, 4th_variation,
            `code`, photo, price_ntd, price, price_ntd_change, price_change, enabled, 
            quoted_price, quoted_price_change, `status`, create_id, product_id)
            SELECT category_id, 1st_variation, 2rd_variation, 3th_variation, 4th_variation,
            `code`, photo, price_ntd, price, price_ntd_change, price_change, enabled, 
            quoted_price, quoted_price_change, `status`, create_id, " . $last_id . " FROM product
            where product_id = :id";

            // prepare the query
            $stmt1 = $db->prepare($query);
            $stmt1->bindParam(':id', $id);

            if($stmt1->execute()) {
                $query = "INSERT INTO product_related (product_id, code)
                SELECT " . $last_id . ", code FROM product_related
                where product_id = :id";
                $stmt2 = $db->prepare($query);
                $stmt2->bindParam(':id', $id);
                if($stmt2->execute()) {
                    //$db->commit();
                } else {
                    $arr = $stmt2->errorInfo();
                    error_log($arr[2]);
                    //$db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } else {
                $arr = $stmt1->errorInfo();
                error_log($arr[2]);
                //$db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }

            update_product_category_tags_index($last_id, $db);
       
        } else {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            //$db->rollback();
            http_response_code(501);
            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
            die();
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        //$db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }

    http_response_code(200);
    echo json_encode(array("message" => "Duplicate at " . date("Y-m-d") . " " . date("h:i:sa")));


    
function update_product_category_tags_index($id, $db) {

    $sql = "SELECT id, tags, attributes, variation_mode FROM product_category where `status` <> -1";

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $tags = explode(',', $row['tags']);
        $attributes = json_decode($row['attributes'], true);

        $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`) values (:product_category_id, 0, :tag, '')";
        $stmt2 = $db->prepare( $sql );

        foreach ($tags as $tag) {
            $stmt2->bindParam(':product_category_id', $id);
            $stmt2->bindParam(':tag', $tag);
            $stmt2->execute();

            if($stmt2->errorInfo()[0] != "00000") {
                echo $stmt2->errorInfo()[2];
            }
        }

        foreach ($attributes as $att) {
            $key = $att['category'];
            $value = $att['value'];
            $watt = 0;
            if($value != "") {
                if($key == 'Wattage')
                {
                    if (preg_match_all('/\b(\d+(\.\d+)?)\b/i', $value, $matches)) {
                        $watt = max($matches[1]); // 取最大數值，確保獲取主要的功率數值
                    }
                }

                $sql = "insert into product_category_tags_index (pid, `type`, `key`, `value`, `watt`) values (:product_category_id, 1, :key, :value, :watt)";
                $stmt2 = $db->prepare( $sql );
                $stmt2->bindParam(':product_category_id', $id);
                $stmt2->bindParam(':key', $key);
                $stmt2->bindParam(':value', $value);
                $stmt2->bindParam(':watt', $watt);
                $stmt2->execute();

                if($stmt2->errorInfo()[0] != "00000") {
                    echo $stmt2->errorInfo()[2];
                }
            }
        }
    }
}
?>
