<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : '');
$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);
$tax_withheld = (isset($_POST['tax_withheld']) ?  $_POST['tax_withheld'] : 0);
$billing_name = (isset($_POST['billing_name']) ?  $_POST['billing_name'] : "");

$amount = ($amount == '' ? 0 : $amount);
$tax_withheld = ($tax_withheld == '' ? 0 : $tax_withheld);


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
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $user_id = $decoded->data->id;
        $apartment_id = $decoded->data->apartment_id;

        $user_name = $decoded->data->username;
        $user_department = $decoded->data->department;

        $uid = $user_id;

        $query = "update project_main
                    set final_amount = :amount,
                    tax_withheld = :tax_withheld,
                    billing_name = :billing_name
                  where id = :project_id ";

        $stmt = $db->prepare( $query );
        // bind the values
        $stmt->bindParam(':project_id', $pid);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':tax_withheld', $tax_withheld);
        $stmt->bindParam(':billing_name', $billing_name);

        try {
            // execute the query, also check if query was successful
            if (!$stmt->execute()) {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                die();
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        $db->commit();

        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
    }
}
