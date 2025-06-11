<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$record = (isset($_POST['record']) ?  $_POST['record'] : '{}');
$record1 = (isset($_POST['record1']) ?  $_POST['record1'] : '{}');
$rs = json_decode($record,true);
$rs1 = json_decode($record1,true);

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

include_once 'mail.php';

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

use \Firebase\JWT\JWT;


if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $username = $decoded->data->username;
  
        // now you can apply
        $uid = $user_id;

        // // delete previous data
        // $query = "delete from employee_data_sheet where user_id = :user_id and status = -1";
        // // prepare the query
        // $stmt = $db->prepare($query);
        // // bind the values
        // $stmt->bindParam(':user_id', $uid);
        // $stmt->execute();

        // 當前使用者在 employee_data_sheet 資料表中 有 status=0 的記錄存在，而且 也有 status=1 的記錄存在
        // 
        if($rs1 != null && count($rs1) > 0)
        {
          
            $query = "update employee_data_sheet set status = -1, 
            updated_id = :updated_id, 
            updated_at = now() where id = :id";
            // prepare the query
            $stmt = $db->prepare($query);
            // bind the values

            $stmt->bindParam(':updated_id', $uid);
            $stmt->bindParam(':id', $rs1['data_id']);
            $stmt->execute();
        
        }
        

        if($rs["data_id"] == 0)
        {
            $query = "insert into employee_data_sheet set 
                        user_id = :user_id,
                        first_name = :first_name,
                        middle_name = :middle_name,
                        surname = :surname,
                        gender = :gender,
                        present_address = :present_address,
                        permanent_address = :permanent_address, 
                        telephone = :telephone,
                        cellphone = :cellphone,
                        email = :email,
                        birthday = :birthday, 
                        birthplace = :birthplace,
                        civil_status = :civil_status,
                        citizenship = :citizenship,
                        height = :height, 
                        weight = :weight,
                        religion = :religion,
                        language = :language,
                        medical = :medical, 
                        spouse = :spouse,
                        spouse_ocupation = :spouse_ocupation,
                        children = :children,
                        father = :father, 
                        father_ocupation = :father_ocupation,
                        mother = :mother,
                        mother_ocupation = :mother_ocupation,
                        siblings = :siblings, 
                        tin = :tin,
                        sss = :sss,
                        philhealth = :philhealth,
                        pagibig = :pagibig, 
                        emergency_name = :emergency_name,
                        emergency_address = :emergency_address, 
                        emergency_contact = :emergency_contact,
                        emergency_relationship = :emergency_relationship,
                        education_elementary = :education_elementary,
                        education_elementary_year = :education_elementary_year, 
                        education_highschool = :education_highschool,
                        education_highschool_year = :education_highschool_year,
                        education_college = :education_college,
                        education_college_year = :education_college_year, 
                        employment_company1 = :employment_company1,
                        employment_position1 = :employment_position1,
                        employment_period1 = :employment_period1,
                        employment_company2 = :employment_company2, 
                        employment_position2 = :employment_position2,
                        employment_period2 = :employment_period2,
                        create_id = :create_id,
                        created_at = now(),
                        updated_id = :updated_id, 
                        updated_at = now()
            ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':user_id', $rs['id']);
            $stmt->bindParam(':first_name', $rs['first_name']);
            $stmt->bindParam(':middle_name', $rs['middle_name']);
            $stmt->bindParam(':surname', $rs['surname']);
            $stmt->bindParam(':gender', $rs['gender']);
            $stmt->bindParam(':present_address', $rs['present_address']);
            $stmt->bindParam(':permanent_address', $rs['permanent_address']);
            $stmt->bindParam(':telephone', $rs['telephone']);
            $stmt->bindParam(':cellphone', $rs['cellphone']);
            $stmt->bindParam(':email', $rs['email']);
            $stmt->bindParam(':birthday', $rs['birthday']);
            $stmt->bindParam(':birthplace', $rs['birthplace']);
            $stmt->bindParam(':civil_status', $rs['civil_status']);
            $stmt->bindParam(':citizenship', $rs['citizenship']);
            $stmt->bindParam(':height', $rs['height']);
            $stmt->bindParam(':weight', $rs['weight']);
            $stmt->bindParam(':religion', $rs['religion']);
            $stmt->bindParam(':language', $rs['language']);
            $stmt->bindParam(':medical', $rs['medical']);
            $stmt->bindParam(':spouse', $rs['spouse']);
            $stmt->bindParam(':spouse_ocupation', $rs['spouse_ocupation']);
            $stmt->bindParam(':children', $rs['children']);
            $stmt->bindParam(':father', $rs['father']);
            $stmt->bindParam(':father_ocupation', $rs['father_ocupation']);
            $stmt->bindParam(':mother', $rs['mother']);
            $stmt->bindParam(':mother_ocupation', $rs['mother_ocupation']);
            $stmt->bindParam(':siblings', $rs['siblings']);
            $stmt->bindParam(':tin', $rs['tin']);
            $stmt->bindParam(':sss', $rs['sss']);
            $stmt->bindParam(':philhealth', $rs['philhealth']);
            $stmt->bindParam(':pagibig', $rs['pagibig']);
            $stmt->bindParam(':emergency_name', $rs['emergency_name']);
            $stmt->bindParam(':emergency_address', $rs['emergency_address']);
            $stmt->bindParam(':emergency_contact', $rs['emergency_contact']);
            $stmt->bindParam(':emergency_relationship', $rs['emergency_relationship']);
            $stmt->bindParam(':education_elementary', $rs['education_elementary']);
            $stmt->bindParam(':education_elementary_year', $rs['education_elementary_year']);
            $stmt->bindParam(':education_highschool', $rs['education_highschool']);
            $stmt->bindParam(':education_highschool_year', $rs['education_highschool_year']);
            $stmt->bindParam(':education_college', $rs['education_college']);
            $stmt->bindParam(':education_college_year', $rs['education_college_year']);
            $stmt->bindParam(':employment_company1', $rs['employment_company1']);
            $stmt->bindParam(':employment_position1', $rs['employment_position1']);
            $stmt->bindParam(':employment_period1', $rs['employment_period1']);
            $stmt->bindParam(':employment_company2', $rs['employment_company2']);
            $stmt->bindParam(':employment_position2', $rs['employment_position2']);
            $stmt->bindParam(':employment_period2', $rs['employment_period2']);
            $stmt->bindParam(':create_id', $user_id);
            $stmt->bindParam(':updated_id', $user_id);
            
            $stmt->execute();

            // // update user table for first_name, middle_name, surname
            // $query = "update user set 
            //             first_name = :first_name,
            //             middle_name = :middle_name,
            //             surname = :surname,
            //             updated_id = :updated_id, 
            //             updated_at = now()

            //             where id = :id
            // ";

            // // prepare the query
            // $stmt = $db->prepare($query);

            // // bind the values
            // $stmt->bindParam(':first_name', $rs['first_name']);
            // $stmt->bindParam(':middle_name', $rs['middle_name']);
            // $stmt->bindParam(':surname', $rs['surname']);
            // $stmt->bindParam(':updated_id', $user_id);

            // $stmt->bindParam(':id', $rs['id']);

            // $stmt->execute();

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
            
        }
        else
        {
            $query = "insert into  employee_data_sheet set 
                        user_id = :user_id,
                        first_name = :first_name,
                        middle_name = :middle_name,
                        surname = :surname,
                        gender = :gender,
                        present_address = :present_address,
                        permanent_address = :permanent_address, 
                        telephone = :telephone,
                        cellphone = :cellphone,
                        email = :email,
                        birthday = :birthday, 
                        birthplace = :birthplace,
                        civil_status = :civil_status,
                        citizenship = :citizenship,
                        height = :height, 
                        weight = :weight,
                        religion = :religion,
                        language = :language,
                        medical = :medical, 
                        spouse = :spouse,
                        spouse_ocupation = :spouse_ocupation,
                        children = :children,
                        father = :father, 
                        father_ocupation = :father_ocupation,
                        mother = :mother,
                        mother_ocupation = :mother_ocupation,
                        siblings = :siblings, 
                        tin = :tin,
                        sss = :sss,
                        philhealth = :philhealth,
                        pagibig = :pagibig, 
                        emergency_name = :emergency_name,
                        emergency_address = :emergency_address, 
                        emergency_contact = :emergency_contact,
                        emergency_relationship = :emergency_relationship,
                        education_elementary = :education_elementary,
                        education_elementary_year = :education_elementary_year, 
                        education_highschool = :education_highschool,
                        education_highschool_year = :education_highschool_year,
                        education_college = :education_college,
                        education_college_year = :education_college_year, 
                        employment_company1 = :employment_company1,
                        employment_position1 = :employment_position1,
                        employment_period1 = :employment_period1,
                        employment_company2 = :employment_company2, 
                        employment_position2 = :employment_position2,
                        employment_period2 = :employment_period2,
                        `status` = 1,
                        create_id = :create_id,
                        created_at = now(),
                        updated_id = :updated_id, 
                        updated_at = now()

            ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':user_id', $rs['user_id']);
            $stmt->bindParam(':first_name', $rs['first_name']);
            $stmt->bindParam(':middle_name', $rs['middle_name']);
            $stmt->bindParam(':surname', $rs['surname']);
            $stmt->bindParam(':gender', $rs['gender']);
            $stmt->bindParam(':present_address', $rs['present_address']);
            $stmt->bindParam(':permanent_address', $rs['permanent_address']);
            $stmt->bindParam(':telephone', $rs['telephone']);
            $stmt->bindParam(':cellphone', $rs['cellphone']);
            $stmt->bindParam(':email', $rs['email']);
            $stmt->bindParam(':birthday', $rs['birthday']);
            $stmt->bindParam(':birthplace', $rs['birthplace']);
            $stmt->bindParam(':civil_status', $rs['civil_status']);
            $stmt->bindParam(':citizenship', $rs['citizenship']);
            $stmt->bindParam(':height', $rs['height']);
            $stmt->bindParam(':weight', $rs['weight']);
            $stmt->bindParam(':religion', $rs['religion']);
            $stmt->bindParam(':language', $rs['language']);
            $stmt->bindParam(':medical', $rs['medical']);
            $stmt->bindParam(':spouse', $rs['spouse']);
            $stmt->bindParam(':spouse_ocupation', $rs['spouse_ocupation']);
            $stmt->bindParam(':children', $rs['children']);
            $stmt->bindParam(':father', $rs['father']);
            $stmt->bindParam(':father_ocupation', $rs['father_ocupation']);
            $stmt->bindParam(':mother', $rs['mother']);
            $stmt->bindParam(':mother_ocupation', $rs['mother_ocupation']);
            $stmt->bindParam(':siblings', $rs['siblings']);
            $stmt->bindParam(':tin', $rs['tin']);
            $stmt->bindParam(':sss', $rs['sss']);
            $stmt->bindParam(':philhealth', $rs['philhealth']);
            $stmt->bindParam(':pagibig', $rs['pagibig']);
            $stmt->bindParam(':emergency_name', $rs['emergency_name']);
            $stmt->bindParam(':emergency_address', $rs['emergency_address']);
            $stmt->bindParam(':emergency_contact', $rs['emergency_contact']);
            $stmt->bindParam(':emergency_relationship', $rs['emergency_relationship']);
            $stmt->bindParam(':education_elementary', $rs['education_elementary']);
            $stmt->bindParam(':education_elementary_year', $rs['education_elementary_year']);
            $stmt->bindParam(':education_highschool', $rs['education_highschool']);
            $stmt->bindParam(':education_highschool_year', $rs['education_highschool_year']);
            $stmt->bindParam(':education_college', $rs['education_college']);
            $stmt->bindParam(':education_college_year', $rs['education_college_year']);
            $stmt->bindParam(':employment_company1', $rs['employment_company1']);
            $stmt->bindParam(':employment_position1', $rs['employment_position1']);
            $stmt->bindParam(':employment_period1', $rs['employment_period1']);
            $stmt->bindParam(':employment_company2', $rs['employment_company2']);
            $stmt->bindParam(':employment_position2', $rs['employment_position2']);
            $stmt->bindParam(':employment_period2', $rs['employment_period2']);
            $stmt->bindParam(':create_id', $user_id);
            $stmt->bindParam(':updated_id', $user_id);

            
            $stmt->execute();

            employee_data_sheet_notification($username);

            // // update user table for first_name, middle_name, surname
            // $query = "update user set 
            //             first_name = :first_name,
            //             middle_name = :middle_name,
            //             surname = :surname,
            //             updated_id = :updated_id, 
            //             updated_at = now()

            //             where id = :id
            // ";

            // // prepare the query
            // $stmt = $db->prepare($query);

            // // bind the values
            // $stmt->bindParam(':first_name', $rs['first_name']);
            // $stmt->bindParam(':middle_name', $rs['middle_name']);
            // $stmt->bindParam(':surname', $rs['surname']);
            // $stmt->bindParam(':updated_id', $user_id);

            // $stmt->bindParam(':id', $rs['id']);

            // $stmt->execute();

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        }
    }
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
        die();
    }
}