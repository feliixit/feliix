<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$status = (isset($_GET['status']) ?  $_GET['status'] : "0");
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
            $id = (isset($_GET['id']) ?  $_GET['id'] : "");
            $page = (isset($_GET['page']) ?  $_GET['page'] : "");
            $size = (isset($_GET['size']) ?  $_GET['size'] : "");
            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");

            $apartment_id = (isset($_GET['apartment_id']) ? $_GET['apartment_id'] : "");

            $sql = "SELECT 0 as is_checked, user.id, user.id user_id, user.username,  user.status,  COALESCE(department, '') department, apartment_id, title_id, COALESCE(title, '') title, 
                        COALESCE(eds.id, 0) data_id,
                        COALESCE(eds.first_name , '') first_name,
                        COALESCE(eds.middle_name , '') middle_name,
                        COALESCE(eds.surname , '') surname,
                        COALESCE(eds.gender , '') gender,
                        COALESCE(eds.present_address , '') present_address,
                        COALESCE(eds.permanent_address , '') permanent_address,
                        COALESCE(eds.telephone , '') telephone,
                        COALESCE(eds.cellphone , '') cellphone,
                        COALESCE(eds.email , '') email,
                        COALESCE(eds.birthday , '') birthday,
                        COALESCE(eds.birthplace , '') birthplace,
                        COALESCE(eds.civil_status , '') civil_status,
                        COALESCE(eds.citizenship , '') citizenship,
                        COALESCE(eds.height , '') height,
                        COALESCE(eds.weight , '') weight,
                        COALESCE(eds.religion , '') religion,
                        COALESCE(eds.language , '') language,
                        COALESCE(eds.medical , '') medical,
                        COALESCE(eds.spouse , '') spouse,
                        COALESCE(eds.spouse_ocupation , '') spouse_ocupation,
                        COALESCE(eds.children , '') children,
                        COALESCE(eds.father , '') father,
                        COALESCE(eds.father_ocupation , '') father_ocupation,
                        COALESCE(eds.mother , '') mother,
                        COALESCE(eds.mother_ocupation , '') mother_ocupation,
                        COALESCE(eds.siblings , '') siblings,
                        COALESCE(eds.tin , '') tin,
                        COALESCE(eds.sss , '') sss,
                        COALESCE(eds.philhealth , '') philhealth,
                        COALESCE(eds.pagibig , '') pagibig,
                        COALESCE(eds.emergency_name , '') emergency_name,
                        COALESCE(eds.emergency_address , '') emergency_address,
                        COALESCE(eds.emergency_contact , '') emergency_contact,
                        COALESCE(eds.emergency_relationship , '') emergency_relationship,
                        COALESCE(eds.education_elementary , '') education_elementary,
                        COALESCE(eds.education_elementary_year , '') education_elementary_year,
                        COALESCE(eds.education_highschool , '') education_highschool,
                        COALESCE(eds.education_highschool_year , '') education_highschool_year,
                        COALESCE(eds.education_college , '') education_college,
                        COALESCE(eds.education_college_year , '') education_college_year,
                        COALESCE(eds.employment_company1 , '') employment_company1,
                        COALESCE(eds.employment_position1 , '') employment_position1,
                        COALESCE(eds.employment_period1 , '') employment_period1,
                        COALESCE(eds.employment_company2 , '') employment_company2,
                        COALESCE(eds.employment_position2 , '') employment_position2,
                        COALESCE(eds.employment_period2 , '') employment_period2,

                        COALESCE(user.auth_date , '') auth_date,
                        COALESCE(user.sig_name , '') sig_name,
                        COALESCE(user.sig_date , '') sig_date,

                        COALESCE(eds.updated_at , '') updated_at,
                        '' updated_str
                    FROM user 
                    LEFT JOIN user_department ON user.apartment_id = user_department.id 
                    LEFT JOIN user_title ON user.title_id = user_title.id 
                    LEFT JOIN employee_data_sheet eds ON user.id = eds.user_id and eds.status = " . ($status ? $status : "0") . "
                    where user.status <> -1 ".($user_id ? " and user.id=$user_id" : '') . ($apartment_id ? " and user.apartment_id=$apartment_id" : '');

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

      }



?>
