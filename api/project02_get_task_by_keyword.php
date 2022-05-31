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
            $kind = (isset($_GET['kind']) ?  $_GET['kind'] : '');

            $table = "";
            switch ($kind) {
                case 'a':
                    $table = "project_other_task_a";
                    break;
                case 'd':
                    $table = "project_other_task_d";
                    break;
                case 'l':
                    $table = "project_other_task_l";
                    break;
                case 'o':
                    $table = "project_other_task_o";
                    break;
                case 'sv':
                    $table = "project_other_task_sv";
                    break;
                case 'sl':
                    $table = "project_other_task_sl";
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(array("message" => "Bad request."));
                    die();
                    break;
            }
         
            $sql = "SELECT id, title project_name FROM " . $table . " pm where pm.title like '%" . $pid . "%' and pm.status <> -1 order by title ";

            $merged_results = [];

            $stmt = $db->prepare( $sql );
            $stmt->execute();


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;


      }



?>
