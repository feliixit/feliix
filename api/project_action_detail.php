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
            $pid = (isset($_GET['pid']) ?  $_GET['pid'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $sql = "SELECT pm.id, pm.detail_type, pm.detail_desc, COALESCE(f.filename, '') filename, COALESCE(f.gcp_name, '') gcp_name, u.username, pm.created_at FROM project_action_detail pm left join user u on u.id = pm.create_id LEFT JOIN gcp_storage_file f ON f.batch_id = pm.id AND f.batch_type = 'action_detail' where project_id = " . $pid . " and pm.status <> -1 ";

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
            $detail_type = "";
            $detail_desc = "";
            $filename = "";
            $gcp_name = "";
            $username = "";
            $created_at = "";
            $items = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if($id != $row['id'] && $id != 0)
                {
                    $merged_results[] = array( "detail_type" => $detail_type,
                                            "detail_desc" => $detail_desc,
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
                $detail_desc = $row['detail_desc'];
                $detail_type = GetDetailType($row['detail_type']);

                if($filename != "")
                $items[] = array('filename' => $filename,
                                 'gcp_name' => $gcp_name );
            }

            if($id != 0)
            {
                $merged_results[] = array( "detail_type" => $detail_type,
                                                "detail_desc" => $detail_desc,
                                                "items" => $items,
                                                "username" => $username,
                                                "created_at" => $created_at
                        );
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

          case 'POST':
              // get database connection
            $uid = $user_id;
            $pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
            $detail_type = (isset($_POST['detail_type']) ?  $_POST['detail_type'] : '');
            $detail_desc = (isset($_POST['detail_desc']) ?  $_POST['detail_desc'] : '');

             
            $query = "INSERT INTO project_action_detail
                SET
                    project_id = :project_id,
                    detail_type = :detail_type,
                    detail_desc = :detail_desc,
                  
                    create_id = :create_id,
                    created_at = now()";
    
                // prepare the query
                $stmt = $db->prepare($query);
            
                // bind the values
                $stmt->bindParam(':project_id', $pid);
                $stmt->bindParam(':detail_type', $detail_type);
                $stmt->bindParam(':detail_desc', $detail_desc);
      
                $stmt->bindParam(':create_id', $user_id);

                $last_id = 0;
                // execute the query, also check if query was successful
                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = $db->lastInsertId();

                    }
                    else
                    {
                        $arr = $stmt->errorInfo();
                        error_log($arr[2]);
                    }
                }
                catch (Exception $e)
                {
                    error_log($e->getMessage());
                }

                $returnArray = array('batch_id' => $last_id);
                $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
                
                echo $jsonEncodedReturnArray;
                
                break;

      }

function GetDetailType($loc)
{
    $location = "";
    switch ($loc) {
        case "1":
            $location = "Requirements";
            break;
        case "2":
            $location = "Submittals";
            break;
        case "3":
            $location = "Discount";
            break;
        case "4":
            $location = "Client Details";
            break;
        case "5":
            $location = "Competitors";
            break;
        case "6":
            $location = "Lead Time";
            break;
        case "7":
            $location = "Warranty";
            break;
        case "8":
            $location = "Other";
            break;
    }

    return $location;
}

?>
