<?php
 error_reporting(E_ERROR | E_PARSE);
//error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';

$check_name = (isset($_POST['check_name']) ?  $_POST['check_name'] : '');

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        $row_id = 0;
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;

        $database = new Database();
        $db = $database->getConnection();

        $merged_results = array();

        /* fetch data */
        $query = "SELECT id from office_item_inventory_check order by id desc limit 1";

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row_id = $row['id'];
        }

        $row_id += 1;

        $request_no = "IC-" . str_pad($row_id, 5, '0', STR_PAD_LEFT);

        $query = "INSERT INTO office_item_inventory_check SET 
                    request_no = :request_no, 
                    check_name = :check_name, 
                    create_id = :create_id, 
                    created_at = now(), 
                    updated_id = :create_id,
                    updated_at = now(),
                    `status` = 1";

        $stmt1 = $db->prepare( $query );

        $stmt1->bindParam(':request_no', $request_no);
        $stmt1->bindParam(':check_name', $check_name);
        $stmt1->bindParam(':create_id', $user_id);

        if (!$stmt1->execute())
        {
            $arr = $stmt1->errorInfo();
            error_log($arr[2]);
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }

        
        update_office_item_forzen($db);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));


    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

function update_office_item_forzen($db)
{
    $frozen = "";
    
    $query = "select count(*) cnt from office_item_inventory_check where status not in (-1, 4)";
    $stmt = $db->prepare($query);
    
    if($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['cnt'] > 0) {
            $frozen = "Y";
        } else {
            $frozen = "";
        }
    } else {
        echo json_encode(array("message" => "Failed"));
        die();
    }
    
    $query = "UPDATE access_control SET frozen_office = :frozen";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':frozen', $frozen);
    
    if(!$stmt->execute()) {
        echo json_encode(array("message" => "Failed"));
        die();
    }
}