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

          $user_id = $decoded->data->id;
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
      include_once '../config/conf.php';

      $database = new Database();
      $db = $database->getConnection();

      $conf = new Conf();

      switch ($method) {
          case 'GET':
            $id = (isset($_GET['id']) ?  $_GET['id'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $apartment_id = (isset($_GET['apartment_id']) ? $_GET['apartment_id'] : "");

            $sql = "SELECT 0 as is_checked, id, username, COALESCE(pic_url, '') pic_url, tel, date_start_company, seniority, date_end_company, `status` FROM user where hide_user_profile <> '1' ".($id ? " and id=$id" : '');

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY username ";

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
            $database = new Database();
            $db = $database->getConnection();
             
            $tel = $_POST["tel"] ? $_POST["tel"] : "";
            $date_start_company = $_POST["date_start_company"] ? $_POST["date_start_company"] : "";
            $date_end_company = $_POST["date_end_company"] ? $_POST["date_end_company"] : "";
            $pic_url = $_POST["pic_url"] ? $_POST["pic_url"] : "";

            $crud = $_POST["crud"];
            $id = $_POST["id"];

            switch ($crud) 
            {
        
            case "update":
                $image = '';

                if(isset($_FILES['photo']['name']))
                {
                    $image_name = $_FILES['photo']['name'];
                    $valid_extensions = array("jpg","jpeg","png");
                    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                    if(in_array($extension, $valid_extensions))
                    {
                        $image = time() . '.' . $extension;
                        $upload_path = $conf::$photo_path . $image;
                        if(move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path))
                        {
                            $message = "";
                        }
                        else
                        {
                            $message = 'There is an error while uploading image';
                        }
                    }
                    else
                    {
                        $message = 'Only .jpg, .jpeg and .png Image allowed to upload';
                    }
                }

                $query = "UPDATE user SET ";

                if($image)
                {
                    $query .= "
                    pic_url = :pic_url, ";
                }

                $query .= "
                    tel = :tel,
                    `date_start_company` = :date_start_company,
                    `date_end_company` = :date_end_company
                WHERE id = :id";
    
                // prepare the query
                $stmt = $db->prepare($query);
            
                // bind the values from the form
                if($image)
                {
                    $stmt->bindParam(':pic_url', $image);
                }
                $stmt->bindParam(':tel', $tel);
                $stmt->bindParam(':date_start_company', $date_start_company);
                $stmt->bindParam(':date_end_company', $date_end_company);
              
                $stmt->bindParam(':id', $id);
            
                // execute the query
                if($stmt->execute()){
                    return true;
                }
                else
                {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                }

                break;

            case 'del':

                $query = "UPDATE user SET pic_url = '' WHERE id = :id";
               
                // prepare the query
                $stmt = $db->prepare($query);
            
                // bind the values from the form
                
             
                $stmt->bindParam(':id', $id);
            
                // execute the query
                if($stmt->execute()){

                    if($pic_url)
                    {
                        unlink($conf::$photo_path . $pic_url);
                    }

                    return true;
                }
                else
                {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                }

                
                
                break;
               
            }

            break;
      }



?>
