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
      include_once 'mail.php';


      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'POST':
            $bid = (isset($_POST['bid']) ?  $_POST['bid'] : "");

            $sql = "SELECT p.project_name, pm.remark, u.username, u.email, pm.created_at FROM project_proof pm left join user u on u.id = pm.create_id LEFT JOIN project_main p ON p.id = pm.project_id  WHERE pm.id = " . $bid . " and pm.status <> -1 ";

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $project_name = "";
            $remark = "";
            $leaver = "";
            $subtime = "";
            $email1 = "";

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $project_name = $row['project_name'];
                $remark = $row['remark'];
                $leaver = $row['username'];
                $subtime = $row['created_at'];
                $email1 = $row['email'];
            }

            send_pay_notify_mail($leaver, $email1, $leaver, $project_name, $remark, $subtime);

            break;


      }



?>
