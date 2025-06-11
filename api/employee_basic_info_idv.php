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
          $username = $decoded->data->username;

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
            
            $id = $user_id;
            $apartment_id = '';
            //$apartment_id = (isset($_GET['apartment_id']) ? $_GET['apartment_id'] : "");

            $sql = "SELECT 0 as is_checked, user.id, user.id user_id, user.username, user.email, user.status,  COALESCE(department, '') department, apartment_id, title_id, COALESCE(title, '') title, 
                        COALESCE(eds.id, 0) data_id,

                        COALESCE(es.first_name , '') first_name,
                        COALESCE(es.middle_name , '') middle_name,
                        COALESCE(es.surname , '') surname,

                        COALESCE(eds.emp_number , '') emp_number,
                        COALESCE(eds.date_hired , '') date_hired,
                        COALESCE(eds.regular_hired , '') regular_hired,
                        COALESCE(eds.emp_status , '') emp_status,
                        COALESCE(eds.company , '') company,
                        COALESCE(eds.emp_category , '') emp_category,
                        COALESCE(eds.superior , '') superior,

                        COALESCE(eds.updated_at , '') updated_at,

                        eds.status as eds_status,

                        (select count(*) from employee_basic_info where employee_basic_info.user_id = user.id and employee_basic_info.status = 1) as need_review,
                        
                        '' updated_str
                    FROM user 
                    LEFT JOIN user_department ON user.apartment_id = user_department.id 
                    LEFT JOIN user_title ON user.title_id = user_title.id 
                    LEFT JOIN employee_basic_info eds ON user.id = eds.user_id and eds.status <> -1
                    LEFT JOIN employee_data_sheet es on user.id = es.user_id and es.status = 0
                    where user.status <> -1 ".($id ? " and user.id=$id" : '');

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
                if($row['need_review'] == 1 && $row['eds_status'] == 1)
                {
                    if($merged_results[count($merged_results) - 1]['username'] == $row['username'])
                        array_push($merged_results[count($merged_results) - 1]['review'], $row);
                    else
                    {
                        // add dummy row
                        $dummy['is_checked'] = 0;
                        $dummy['id'] = $row['id'];
                        $dummy['user_id'] = $row['user_id'];
                        $dummy['username'] = $row['username'];
                        $dummy['email'] = $row['email'];
                        $dummy['status'] = $row['status'];
                        $dummy['department'] = $row['department'];
                        $dummy['apartment_id'] = $row['apartment_id'];
                        $dummy['title_id'] = $row['title_id'];
                        $dummy['title'] = $row['title'];
                        $dummy['data_id'] = $row['data_id'];
                        $dummy['first_name'] = $row['first_name'];
                        $dummy['middle_name'] = $row['middle_name'];
                        $dummy['surname'] = $row['surname'];
                        $dummy['emp_number'] = '';
                        $dummy['date_hired'] = '';
                        $dummy['regular_hired'] = '';
                        $dummy['emp_status'] = '';
                        $dummy['company'] = '';
                        $dummy['emp_category'] = '';
                        $dummy['superior'] = '';
                        $dummy['updated_at'] = '';
                        $dummy['eds_status'] = 0;
                        $dummy['need_review'] = 1;
                        $dummy['updated_str'] = '';

                        $dummy['review'] = array();

                        array_push($dummy['review'], $row);

                        $merged_results[] = $dummy;

                    }
                }
                else
                {
                    $row['review'] = array();
                    $merged_results[] = $row;
                }
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

            break;

    }

?>
