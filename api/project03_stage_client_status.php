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
            $stage_id = (isset($_GET['stage_id']) ?  $_GET['stage_id'] : 0);
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $type = (isset($_GET['type']) ?  $_GET['type'] : "");

            $sql = "SELECT pm.id, pm.type, pm.option, u.username, pm.created_at FROM project_stage_client pm left join user u on u.id = pm.create_id where stage_id = " . $stage_id . " and type = '" . $type . "' and pm.status <> -1 ";

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


            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $type = $row['type'];
                $option = $row['option'];
                $username = $row['username'];
                $created_at = $row['created_at'];

                $status = GetStatus($row['option']);

                $merged_results[] = array( "id" => $id,
                                            "type" => $type,
                                            "option" => $option,
                                            "username" => $username,
                                            "created_at" => $created_at,
                                            "status" => $status,
                        );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

      }

      function GetStatus($loc)
      {
          $location = "";
          switch ($loc) {
              case "1":
                  $location = "Planning";
                  break;
              case "2":
                  $location = "Pending Review";
                  break;
              case "3":
                  $location = "Pending Approval";
                  break;
              case "4":
                  $location = "For Revision";
                  break;
              case "5":
                  $location = "On Hold";
                  break;
              case "6":
                  $location = "Disapproved";
                  break;
              case "7":
                  $location = "Approved";
                  break;
              case "8":
                  $location = "On Progress";
                  break;
            case "9":
                $location = "Completed";
                break;
            case "10":
                $location = "Special";
                break;
          }
      
          return $location;
      }
