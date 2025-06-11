<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$employee_id = (isset($_POST['user_id']) ?  $_POST['user_id'] : 0);
$review_month = (isset($_POST['review_month']) ?  $_POST['review_month'] : '');
$period = (isset($_POST['period']) ?  $_POST['period'] : 0);
$template_id = (isset($_POST['template_id']) ?  $_POST['template_id'] : 0);


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
$db->beginTransaction();
$conf = new Conf();

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

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
    
        $query = "INSERT INTO performance_review
        SET
            `template_id` = :template_id,
            `user_id` = :employee_id,
            `review_month` = :review_month,
            `period` = :period,
            `create_id` = :create_id,
            `created_at` =  now() ";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':template_id', $template_id);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':review_month', $review_month);
        $stmt->bindParam(':period', $period);
        $stmt->bindParam(':create_id', $user_id);


        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
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

        if($period == 1)
        {
            $s_date = $review_month;
  
            $dead_date = GetDeadMonthSingle($review_month);

            send_review_mail_adm_single($s_date, $user_id, $employee_id, $dead_date);
            send_review_mail_single($s_date, $user_id, $employee_id, $dead_date);
        }
        
        if($period == 0)
        {
            $s_date = $review_month;
            $e_date = GetNextMonth($review_month);
            $dead_date = GetDeadMonth($review_month);

            send_review_mail_adm($s_date, $e_date, $user_id, $employee_id, $dead_date);
            send_review_mail($s_date, $e_date, $user_id, $employee_id, $dead_date);
        }

        if($period == 3)
        {
            $s_date = $review_month;
            $e_date = GetNextMonth3($review_month);
            $dead_date = GetDeadMonth3($review_month);

            send_review_mail_adm($s_date, $e_date, $user_id, $employee_id, $dead_date);
            send_review_mail($s_date, $e_date, $user_id, $employee_id, $dead_date);
        }
        
        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa") ));
        
    }
    catch (Exception $e){

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();

    }
}

function GetNextMonth($d)
{
    $date = date('Y-m', strtotime('+1 month', strtotime($d . '-01')));
    return $date;
}

function GetNextMonth3($d)
{
    $date = date('Y-m', strtotime('+2 month', strtotime($d . '-01')));
    return $date;
}

function GetDeadMonth($d)
{
    $date = date('Y-m-d', strtotime('+2 month', strtotime($d . '-10')));
    return $date;
}

function GetDeadMonth3($d)
{
    $date = date('Y-m-d', strtotime('+3 month', strtotime($d . '-10')));
    return $date;
}

function GetDeadMonthSingle($d)
{
    $date = date('Y-m-d', strtotime('+1 month', strtotime($d . '-10')));
    return $date;
}