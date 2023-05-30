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
$payess5 = (isset($_POST['payess5']) ?  $_POST['payess5'] : '');
$payess6 = (isset($_POST['payess6']) ?  $_POST['payess6'] : '');
$payess7 = (isset($_POST['payess7']) ?  $_POST['payess7'] : '');
$payess8 = (isset($_POST['payess8']) ?  $_POST['payess8'] : '');

$access1 = (isset($_POST['access1']) ?  $_POST['access1'] : '');
$access2 = (isset($_POST['access2']) ?  $_POST['access2'] : '');
$access3 = (isset($_POST['access3']) ?  $_POST['access3'] : '');
$access4 = (isset($_POST['access4']) ?  $_POST['access4'] : '');
$access5 = (isset($_POST['access5']) ?  $_POST['access5'] : '');
$access6 = (isset($_POST['access6']) ?  $_POST['access6'] : '');
$access7 = (isset($_POST['access7']) ?  $_POST['access7'] : '');
$knowledge = (isset($_POST['knowledge']) ?  $_POST['knowledge'] : '');
$vote1 = (isset($_POST['vote1']) ?  $_POST['vote1'] : '');
$vote2 = (isset($_POST['vote2']) ?  $_POST['vote2'] : '');
$schedule_confirm = (isset($_POST['schedule_confirm']) ?  $_POST['schedule_confirm'] : '');

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
            $query = "SELECT payess1, payess2, payess3, salary payess4, salary_mgt payess5, salary_slip_mgt payess6, payess7, payess8, access1, access2, access3, access4, access5, access6, access7, knowledge, vote1, vote2, schedule_confirm from access_control where id = 1";

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
                            salary = :salary,
                            salary_mgt = :salary_mgt,
                            salary_slip_mgt = :salary_slip_mgt,
                            payess7 = :payess7,
                            payess8 = :payess8,
                            access1 = :access1,
                            access2 = :access2,
                            access3 = :access3,
                            access4 = :access4,
                            access5 = :access5,
                            access6 = :access6,
                            access7 = :access7,
                            knowledge = :knowledge,
                            vote1 = :vote1,
                            vote2 = :vote2,
                            schedule_confirm = :schedule_confirm
                        where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $id = 1;

            $payess1 = htmlspecialchars(strip_tags($payess1));
            $payess2 = htmlspecialchars(strip_tags($payess2));
            $payess3 = htmlspecialchars(strip_tags($payess3));
            $payess4 = htmlspecialchars(strip_tags($payess4));
            $payess5 = htmlspecialchars(strip_tags($payess5));
            $payess6 = htmlspecialchars(strip_tags($payess6));
            $payess7 = htmlspecialchars(strip_tags($payess7));
            $payess8 = htmlspecialchars(strip_tags($payess8));
            $access1 = htmlspecialchars(strip_tags($access1));
            $access2 = htmlspecialchars(strip_tags($access2));
            $access3 = htmlspecialchars(strip_tags($access3));
            $access4 = htmlspecialchars(strip_tags($access4));
            $access5 = htmlspecialchars(strip_tags($access5));
            $access6 = htmlspecialchars(strip_tags($access6));
            $access7 = htmlspecialchars(strip_tags($access7));
            $knowledge = htmlspecialchars(strip_tags($knowledge));
            $vote1 = htmlspecialchars(strip_tags($vote1));
            $vote2 = htmlspecialchars(strip_tags($vote2));
            $schedule_confirm = htmlspecialchars(strip_tags($schedule_confirm));


            // bind the values
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':payess1', $payess1);
            $stmt->bindParam(':payess2', $payess2);
            $stmt->bindParam(':payess3', $payess3);
            $stmt->bindParam(':salary', $payess4);
            $stmt->bindParam(':salary_mgt', $payess5);
            $stmt->bindParam(':salary_slip_mgt', $payess6);
            $stmt->bindParam(':payess7', $payess7);
            $stmt->bindParam(':payess8', $payess8);
            $stmt->bindParam(':access1', $access1);
            $stmt->bindParam(':access2', $access2);
            $stmt->bindParam(':access3', $access3);
            $stmt->bindParam(':access4', $access4);
            $stmt->bindParam(':access5', $access5);
            $stmt->bindParam(':access6', $access6);
            $stmt->bindParam(':access7', $access7);
            $stmt->bindParam(':knowledge', $knowledge);
            $stmt->bindParam(':vote1', $vote1);
            $stmt->bindParam(':vote2', $vote2);
            $stmt->bindParam(':schedule_confirm', $schedule_confirm);

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
