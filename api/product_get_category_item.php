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


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':

            $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 1 AND STATUS <> -1";

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
                                            "lv2" => $lv2,
                    );

                    $lv2 = [];

                }

                $cat_id = $row['cat_id'];
                $category = $row['category'];

                $lv2[] = GetLevel2($cat_id, $db);
            }

            if($cat_id != "")
            {
                $merged_results[] = array( "cat_id" => $cat_id,
                                            "category" => $category,
                                            "lv2" => $lv2,
                    );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }

function GetLevel2($cat_id, $db){
    $sql = "SELECT * FROM product_category_attribute WHERE LEVEL = 2 AND left(cat_id, 1) = '". substr($cat_id, 0, 1) . "' and STATUS <> -1";

    $sql = $sql . " ORDER BY category ";

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


?>
