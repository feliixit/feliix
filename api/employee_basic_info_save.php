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
$rs = json_decode($record,true);


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';

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
  
        // now you can apply
        $uid = $user_id;

        if($rs != null && count($rs) > 0)
        {
          
            $query = "update employee_basic_info set status = -1, 
            updated_id = :updated_id, 
            updated_at = now() where id = :id";
            // prepare the query
            $stmt = $db->prepare($query);
            // bind the values

            $stmt->bindParam(':updated_id', $uid);
            $stmt->bindParam(':id', $rs['data_id']);
            $stmt->execute();
        
        }

        // if($rs["data_id"] == 0)
        // {
            $query = "insert into employee_basic_info set 
                        user_id = :user_id,
                        emp_number = :emp_number,
                        date_hired = :date_hired,
                        regular_hired = :regular_hired, 
                        emp_status = :emp_status,
                        company = :company,
                        emp_category = :emp_category,
                        superior = :superior, 

                        create_id = :create_id,
                        created_at = now(),
                        updated_id = :updated_id, 
                        updated_at = now()
            ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':user_id', $rs['id']);
            $stmt->bindParam(':emp_number', $rs['emp_number']);
            $stmt->bindParam(':date_hired', $rs['date_hired']);
            $stmt->bindParam(':regular_hired', $rs['regular_hired']);
            $stmt->bindParam(':emp_status', $rs['emp_status']);
            $stmt->bindParam(':company', $rs['company']);
            $stmt->bindParam(':emp_category', $rs['emp_category']);
            $stmt->bindParam(':superior', $rs['superior']);

            $stmt->bindParam(':create_id', $user_id);
            $stmt->bindParam(':updated_id', $user_id);
            
            $stmt->execute();

            // // update user table for first_name, middle_name, surname
            // $query = "update employee_data_sheet set 
            //             first_name = :first_name,
            //             middle_name = :middle_name,
            //             surname = :surname,
            //             updated_id = :updated_id, 
            //             updated_at = now()

            //             where user_id = :id and status = 0
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
            
        // }
        // else
        // {
        //     $query = "update employee_basic_info set 
        //                 user_id = :user_id,
        //                 emp_number = :emp_number,
        //                 date_hired = :date_hired,
        //                 regular_hired = :regular_hired, 
        //                 emp_status = :emp_status,
        //                 company = :company,
        //                 emp_category = :emp_category,
        //                 superior = :superior, 

        //                 updated_id = :updated_id, 
        //                 updated_at = now()

        //                 where id = :id
        //     ";

        //     // prepare the query
        //     $stmt = $db->prepare($query);

        //     // bind the values
        //     $stmt->bindParam(':user_id', $rs['user_id']);
        //     $stmt->bindParam(':emp_number', $rs['emp_number']);
        //     $stmt->bindParam(':date_hired', $rs['date_hired']);
        //     $stmt->bindParam(':regular_hired', $rs['regular_hired']);
        //     $stmt->bindParam(':emp_status', $rs['emp_status']);
        //     $stmt->bindParam(':company', $rs['company']);
        //     $stmt->bindParam(':emp_category', $rs['emp_category']);
        //     $stmt->bindParam(':superior', $rs['superior']);
        //     $stmt->bindParam(':updated_id', $user_id);

        //     $stmt->bindParam(':id', $rs['data_id']);
            
        //     $stmt->execute();

        //     // update user table for first_name, middle_name, surname
        //     $query = "update employee_data_sheet set 
        //                 first_name = :first_name,
        //                 middle_name = :middle_name,
        //                 surname = :surname,
        //                 updated_id = :updated_id, 
        //                 updated_at = now()

        //                 where user_id = :id and status = 0
        //     ";

        //     // prepare the query
        //     $stmt = $db->prepare($query);

        //     // bind the values
        //     $stmt->bindParam(':first_name', $rs['first_name']);
        //     $stmt->bindParam(':middle_name', $rs['middle_name']);
        //     $stmt->bindParam(':surname', $rs['surname']);
        //     $stmt->bindParam(':updated_id', $user_id);

        //     $stmt->bindParam(':id', $rs['id']);

        //     $stmt->execute();

            // http_response_code(200);
            // echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        //}
    }
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
        die();
    }
}