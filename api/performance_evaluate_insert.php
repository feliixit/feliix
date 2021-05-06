<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$pid = (isset($_POST['pid']) ?  $_POST['pid'] : 0);
$answers = (isset($_POST['answers']) ?  $_POST['answers'] : '[]');
$answers = preg_replace('/(\w+):/i', '"\1":', $answers);
$answers_array = json_decode($answers, true);

$comment1 = (isset($_POST['commet1']) ?  $_POST['commet1'] : '');
$comment2 = (isset($_POST['commet2']) ?  $_POST['commet2'] : '');
$comment3 = (isset($_POST['commet3']) ?  $_POST['commet3'] : '');
$comment4 = (isset($_POST['commet4']) ?  $_POST['commet4'] : '');
$comment5 = (isset($_POST['commet5']) ?  $_POST['commet5'] : '');

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

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $uid = $decoded->data->id;

        // check if you can update the
        $query = "
            SELECT pm.id,
                pm.`template_id`,
                pm.user_id,
                pm.create_id
            FROM   performance_review pm
            WHERE  id = " . $pid . "
    
        ";

        // prepare the query
        $stmt = $db->prepare($query);
        $stmt->execute();

        $id = 0;
        $template_id = "";
        $user_id = "";
        $create_id = "";

        $anser_type = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $template_id = $row['template_id'];
            $user_id = $row['user_id'];
            $create_id = $row['create_id'];
        }

        if ($user_id != $uid && $create_id != $uid) {
            http_response_code(501);
            echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Permission denied.");
            die();
        }

        // now you can apply
        if ($user_id == $uid) {
            $anser_type = 1;

            $query = "update performance_review
                    SET
                        `emp_comment_1` = :emp_comment1,
                        `emp_comment_2` = :emp_comment2,
                        `emp_comment_3` = :emp_comment3,
                        `emp_comment_4` = :emp_comment4,
                        `emp_comment_5` = :emp_comment5,
                        `user_complete_at` = now() 
                        WHERE  id = " . $pid . "
                    ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':emp_comment1', $comment1);
            $stmt->bindParam(':emp_comment2', $comment2);
            $stmt->bindParam(':emp_comment3', $comment3);
            $stmt->bindParam(':emp_comment4', $comment4);
            $stmt->bindParam(':emp_comment5', $comment5);

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
        }

        if ($create_id == $uid) {
            $anser_type = 2;
            $query = "update performance_review
                    SET
                        `mag_comment_1` = :mag_comment1,
                        `mag_comment_2` = :mag_comment2,
                        `mag_comment_3` = :mag_comment3,
                        `mag_comment_4` = :mag_comment4,
                        `mag_comment_5` = :mag_comment5,
                        `manager_complete_at` = now() 
                        WHERE  id = " . $pid . "
                    ";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':mag_comment1', $comment1);
            $stmt->bindParam(':mag_comment2', $comment2);
            $stmt->bindParam(':mag_comment3', $comment3);
            $stmt->bindParam(':mag_comment4', $comment4);
            $stmt->bindParam(':mag_comment5', $comment5);

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
        }


        // answers
        for ($i = 0; $i < count($answers_array); $i++) {
            $query = "INSERT INTO performance_review_detail
            SET
                `review_id` = :review_id,
                `review_type` = :review_type,
                `review_question_id` = :review_question_id,
                `score` = :score,
                `option` = :option,
               
                `status` = 0,
                `create_id` = :create_id,
                `created_at` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':review_id', $pid);
            $stmt->bindParam(':review_type', $anser_type);
            $stmt->bindParam(':review_question_id', $answers_array[$i]['id']);
            $stmt->bindParam(':score', $answers_array[$i]['grade']);
            $stmt->bindParam(':option', $answers_array[$i]['opt']);
            $stmt->bindParam(':create_id', $uid);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
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
        }

        $db->commit();


        http_response_code(200);
        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    } catch (Exception $e) {

        error_log($e->getMessage());
        $db->rollback();
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . $e->getMessage()));
        die();
    }
}
