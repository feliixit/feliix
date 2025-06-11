<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';
include_once 'config/conf.php';

include_once 'mail.php';

use \Firebase\JWT\JWT;
use Google\Cloud\Storage\StorageClient;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

switch ($method) {

    case 'POST':

        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();
        $conf = new Conf();

        $jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
        $pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
        $direct_access = (isset($_POST['direct_access']) ?  $_POST['direct_access'] : '');
        $manager_access = (isset($_POST['manager_access']) ?  $_POST['manager_access'] : '');
        $peer_access = (isset($_POST['peer_access']) ?  $_POST['peer_access'] : '');
        $other_access = (isset($_POST['other_access']) ?  $_POST['other_access'] : '');
        $outsider_name1 = (isset($_POST['outsider_name1']) ?  $_POST['outsider_name1'] : '');
        $outsider_email1 = (isset($_POST['outsider_email1']) ?  $_POST['outsider_email1'] : '');
        $outsider_name2 = (isset($_POST['outsider_name2']) ?  $_POST['outsider_name2'] : '');
        $outsider_email2 = (isset($_POST['outsider_email2']) ?  $_POST['outsider_email2'] : '');
      
        if ($pid == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        try {
            // now you can apply
            $query = "update leadership_assessment
                SET
                    `direct_access` = :direct_access,
                    `manager_access` = :manager_access,
                    `peer_access` = :peer_access,
                    `other_access` = :other_access,
                    `outsider_name1` = :outsider_name1,
                    `outsider_email1` = :outsider_email1,
                    `outsider_name2` = :outsider_name2,
                    `outsider_email2` = :outsider_email2,
                    `updated_at` = now(),
                    `updated_id` = :updated_id,
                    `status` = 1
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':direct_access', $direct_access);
            $stmt->bindParam(':manager_access', $manager_access);
            $stmt->bindParam(':peer_access', $peer_access);
            $stmt->bindParam(':other_access', $other_access);
            $stmt->bindParam(':outsider_name1', $outsider_name1);
            $stmt->bindParam(':outsider_email1', $outsider_email1);
            $stmt->bindParam(':outsider_name2', $outsider_name2);
            $stmt->bindParam(':outsider_email2', $outsider_email2);
            $stmt->bindParam(':updated_id', $user_id);

            $stmt->bindParam(':id', $pid);

            $last_id = $pid;
            // execute the query, also check if query was successful
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

            // json string to array
            $direct = json_decode($direct_access);
            $manager = json_decode($manager_access);
            $peer = json_decode($peer_access);
            $other = json_decode($other_access);

            $emails = array();
            foreach ($direct as $item) {
                $emails[] = $item;
            }
            foreach ($manager as $item) {
                $emails[] = $item;
            }
            foreach ($peer as $item) {
                $emails[] = $item;
            }
            foreach ($other as $item) {
                $emails[] = $item;
            }


            $_record = GetLeadershipAssessment($last_id, $db);
            EmailNotify($_record[0]['create_id'], $_record[0]['user_id'], $last_id);

            foreach ($emails as $email) {
                $email = trim($email);
                if ($email != '') {
                    EmailNotifyRegular($_record[0]['user_id'], $email, $last_id);
                }
            }

            if($outsider_email1 != '' && $outsider_name1 != '')
                EmailNotifyOther($outsider_email1, $outsider_name1, $_record[0]['user_id'], $last_id);
            if($outsider_email2 != '' && $outsider_name2 != '')
                EmailNotifyOther($outsider_email2, $outsider_name2, $_record[0]['user_id'], $last_id);

            http_response_code(200);
            echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
        } catch (Exception $e) {

            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }
        break;
}

function EmailNotify($creat_id, $user_id, $last_id){
    leadership_assessment_self_notify($user_id, $creat_id, $last_id);
}

function EmailNotifyRegular($user_id, $email, $last_id){
    leadership_assessment_respondent_notify($user_id, $email, $last_id);
}

function EmailNotifyOther($email, $name, $employee_id, $last_id){
    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify('+24 hours')->getTimestamp(); 

    $token = array(
           "name" => $name,
           "email" => $email,
           "id" => $last_id,
           
    );

    $token = passport_encrypt(json_encode($token));

    leadership_assessment_respondent_other_notify($email, $name, $employee_id, $last_id, $token);
}


function GetLeadershipAssessment($id, $db){
    
    $query = "SELECT pr.id, 
                pr.review_month, 
                pr.period, 
                pr.template_id,
                ud.department,  
                ut.title, 
                pr.create_id,
                pr.user_id,
                pr.direct_access,
                pr.manager_access,
                pr.peer_access,
                pr.other_access,
                pr.outsider_name1,
                pr.outsider_email1,
                pr.outsider_name2,
                pr.outsider_email2,
                u.username manager,
                u1.username employee, 
                COALESCE(pr.user_complete_at, '') user_complete_at, 
                COALESCE(pr.manager_complete_at, '') manager_complete_at,
                pr.created_at
                FROM leadership_assessment pr
                LEFT JOIN user u ON u.id = pr.create_id
                LEFT JOIN user u1 ON u1.id = pr.user_id
                LEFT JOIN user_title ut ON ut.id = u1.title_id
                LEFT JOIN user_department ud ON ud.id = u1.apartment_id
              WHERE pr.status <> -1  " . ($id != 0 ? " and pr.id=$id" : ' ');

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $merged_results = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $id = $row['id'];
        $review_month = $row['review_month'];
        $period = $row['period'];

        $department = $row['department'];
        $title = $row['title'];
        $template_id = $row['template_id'];
        $employee = $row['employee'];
        $manager = $row['manager'];
        $user_complete_at = $row['user_complete_at'];
        $manager_complete_at = $row['manager_complete_at'];

        $create_id = $row['create_id'];
        $user_id = $row['user_id'];

        $manager_access = $row['manager_access'];
        $peer_access = $row['peer_access'];
        $direct_access = $row['direct_access'];
        $other_access = $row['other_access'];

        $outsider_name1 = $row['outsider_name1'];
        $outsider_email1 = $row['outsider_email1'];
        $outsider_name2 = $row['outsider_name2'];
        $outsider_email2 = $row['outsider_email2'];

        $created_at = $row['created_at'];

        if($row['status'] == 0)
            $status = "Choose respondent for leadership assessment";
        if($row['status'] == 1)
            $status = "Assessed employee and respondents fill out survey";
        if($row['status'] == 2)
            $status = "Done";

        $merged_results[] = array(
            "id" => $id,
            "period" => $period,
            "review_month" => $review_month,
            "review_next_month" => "",
            "department" => $department,
            "template_id" => $template_id,
            "title" => $title,
            "employee" => $employee,
            "manager" => $manager,
            "create_id" => $create_id,
            "user_id" => $user_id,
            "direct_access" => $direct_access,
            "manager_access" => $manager_access,
            "peer_access" => $peer_access,
            "other_access" => $other_access,
            "outsider_name1" => $outsider_name1,
            "outsider_email1" => $outsider_email1,
            "outsider_name2" => $outsider_name2,
            "outsider_email2" => $outsider_email2,
            "user_complete_at" => $user_complete_at,
            "manager_complete_at" => $manager_complete_at,
            "status" => $status,

            "created_at" => $created_at,
        );
    }

    return $merged_results;
}