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
            $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $type = (isset($_GET['type']) ?  $_GET['type'] : "");

            $sql = "SELECT pm.id, pm.type, pm.message, pm.option, u.username, pm.created_at, COALESCE(f.filename, '') filename, COALESCE(f.gcp_name, '') gcp_name, u.username, pm.created_at FROM project_stage_client pm left join user u on u.id = pm.create_id LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'additional' where stage_id = " . $stage_id . " and type = '" . $type . "' and pm.status <> -1 ";

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY pm.id ";

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }

            $merged_results = array();

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            $id = 0;
            $message = "";
            $filename = "";
            $gcp_name = "";
            $username = "";
            $created_at = "";
            $items = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if($id != $row['id'] && $id != 0)
                {
                    $merged_results[] = array( "message" => $message,
                                            "items" => $items,
                                            "username" => $username,
                                            "created_at" => $created_at
                    );

                    $items = [];

                }

                $id = $row['id'];
                $created_at = $row['created_at'];
                $username = $row['username'];
                $gcp_name = $row['gcp_name'];
                $filename = $row['filename'];
                $message = $row['message'];

                if($filename != "")
                  $items[] = array('filename' => $filename,
                                 'gcp_name' => $gcp_name );
            }

            if($id != 0)
            {
                $merged_results[] = array( "message" => $message,
                                                "items" => $items,
                                                "username" => $username,
                                                "created_at" => $created_at
                        );
            }


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }



?>
