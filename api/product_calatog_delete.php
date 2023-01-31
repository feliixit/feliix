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

    EmailNotify($id, $db);

    http_response_code(200);
    echo json_encode(array("message" => "Deleted at " . date("Y-m-d") . " " . date("h:i:sa")));

    

function EmailNotify($id, $db){
    $_record = GetProductCategory($id, $db);
    if(count($_record) > 0)
        product_notify("delete", $_record[0]);
}

function GetProductCategory($id, $db){
    $query = "SELECT p.id, p.category, p.brand, p.code, p.photo1, p.created_at, p.create_id, p.deleted_time updated_at, p.deleted_id updated_id, p.attributes, c.username creator, u.username updator  FROM product_category  p left join user c on p.create_id = c.id left join user u on p.deleted_id = u.id  WHERE p.id = :id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $merged_results = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        
        $special_info_json = json_decode($row["attributes"]);

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

                if(count($value) > 0)
                {
                    $attribute_list[] = array("category" => $special_info_json[$i]->category,
                                    "value" => $value,
                                );
                }
            }
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
                            "creator" => $row["creator"],
                            "updator" => $row["updator"],
                           
                            "attribute_list" => $attribute_list,
                           

        );
    }

    return $merged_results;
}

?>
