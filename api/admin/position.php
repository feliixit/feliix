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
        //   if(!$decoded->data->is_admin)
        //   {
        //     http_response_code(401);
     
        //     echo json_encode(array("message" => "Access denied."));
        //     die();
        //   }
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      header('Access-Control-Allow-Origin: *');  

      include_once '../config/database.php';
      include_once '../objects/user_title.php';



      $database = new Database();
      $db = $database->getConnection();

      switch ($method) {
          case 'GET':
            $id = (isset($_GET['id']) ?  $_GET['id'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");
            $department_id  = (isset($_GET['department_id']) ?  $_GET['department_id'] : "");

            $sql = "SELECT 0 as is_checked, id, department_id, title FROM user_title  where status <> -1 and department_id=$department_id ".($id ? " and id=$id" : '');

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY id ";

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
                $merged_results[] = $row;
            }

            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

        case 'POST':
            // get database connection
             
            // instantiate product object
            $user = new UseTitle($db);
            $department_id  = (isset($_POST['department_id']) ?  $_POST['department_id'] : "");
            $department_id = stripslashes($department_id);
            $title = stripslashes($_POST["title"]);
            

            $crud = $_POST["crud"];
            $id = $_POST["id"];

            switch ($crud) 
            {
              case 'insert':
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $user->department_id = $department_id;
                $user->title = $title;
               
                $user->create();

                break;

            case "update":
                $user->title = $title;
              
                    $user->id = $id;

                    $user->updateStatus();

                break;

            case 'del':
                $ids = explode(",", $id);
                foreach($ids as $item) {
                    $user->id = trim($item);
                    $user->delete();
                }

                if($query){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }
               
                break;
            }

            break;
      }



?>
