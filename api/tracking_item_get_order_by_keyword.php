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
            $pid = (isset($_GET['key']) ?  $_GET['key'] : '');
         
            // $sql = "SELECT distinct pm.id, pm.project_name FROM order_tracking_item oti 
            //         LEFT JOIN order_receive_item rec ON oti.item_id = rec.id 
            //         left join project_main pm on rec.project_id = pm.id
            //         AND oti.status <> -1 and pm.project_name like '%" . $pid . "%' and pm.status <> -1 order by project_name ";
            $sql = "SELECT distinct
                    rec.od_id id,
                    od.od_name
                    FROM order_receive_item rec 
                    LEFT JOIN od_main od ON rec.od_id = od.id order by od_name ";

            $merged_results = [];

            $stmt = $db->prepare( $sql );
            $stmt->execute();


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // remove empty values from the array
                $row = array_filter($row, function($value) {
                    return !is_null($value) && $value !== '';
                });
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;


      }



?>
