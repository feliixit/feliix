<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');
$action = (isset($_POST['action']) ?  $_POST['action'] : 1);

$payess1 = (isset($_POST['payess1']) ?  $_POST['payess1'] : '');
$payess2 = (isset($_POST['payess2']) ?  $_POST['payess2'] : '');
$payess3 = (isset($_POST['payess3']) ?  $_POST['payess3'] : '');
$payess4 = (isset($_POST['payess4']) ?  $_POST['payess4'] : '');

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

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    if ($action == 1) {
        //select all
        try {
            $query = "SELECT payess1, payess2, payess3, salary payess4 from access_control where id = 1";

            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $merged_results[] = $row;
            }
            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(401);

            echo json_encode(array("message" => ".$e."));
        }
    } else if ($action == 3) {
        //update
        try {
            $query = "UPDATE access_control
                        set payess1 = :payess1, 
                            payess2 = :payess2, 
                            payess3 = :payess3,
                            salary = :salary
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $id = 1;

            $payess1 = htmlspecialchars(strip_tags($payess1));
            $payess2 = htmlspecialchars(strip_tags($payess2));
            $payess3 = htmlspecialchars(strip_tags($payess3));
            $payess4 = htmlspecialchars(strip_tags($payess4));


            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':payess1', $payess1);
            $stmt->bindParam(':payess2', $payess2);
            $stmt->bindParam(':payess3', $payess3);
            $stmt->bindParam(':salary', $payess4);


            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    return true;
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    return false;
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                return false;
            }

            http_response_code(200);
            echo json_encode(array($arr));
            echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } // if decode fails, it means jwt is invalid
        catch (Exception $e) {

            http_response_code(401);

            echo json_encode(array("message" => "Access denied."));
        }
    }
}
