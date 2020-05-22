<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once '../config/core.php';
include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
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
          if(!$decoded->data->is_admin)
          {
            http_response_code(401);
     
            echo json_encode(array("message" => "Access denied."));
            die();
          }
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      $department = isset($_GET['department']) ?  $_GET['department'] : "";
      header('Access-Control-Allow-Origin: *');  

      include_once '../config/database.php';
      include_once '../objects/user.php';

      $method = $_SERVER['REQUEST_METHOD'];

      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $sql = "SELECT id, title FROM user_title where department_id = :department_id";

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->bindParam(':department_id', $department);
            $stmt->execute();


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;
        }


?>
