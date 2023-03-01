<?php
error_reporting(E_ALL);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$answers = (isset($_POST['answers']) ?  $_POST['answers'] : '[]');
// $answers = preg_replace('/(\w+):/i', '"\1":', $answers);
$answers_array = json_decode($answers, true);


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

        // delete previous answers
        $query = "update voting_review set `status` = -1 where template_id = " . $id . " and create_id = " . $uid . " ";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $query = "update voting_review_detail set `status` = -1 where template_id = " . $id . " and create_id = " . $uid . " ";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $last_id = 0;

        // insert answers
        $query = "INSERT INTO voting_review
                SET
                    `template_id` = :template_id,
                    `user_id` = :user_id,
                    `create_id` = :create_id,
                    `created_at` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':template_id', $id);
        $stmt->bindParam(':user_id', $uid);
        $stmt->bindParam(':create_id', $uid);


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
            $last_id = $db->lastInsertId();

        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        foreach ($answers_array as $key => $value) {
            $query = "INSERT INTO voting_review_detail
                SET
                    `template_id` = :template_id,
                    `review_id` = :review_id,
                    `review_question_id` = :review_question_id,
                    `answer` = :answer,
                    `create_id` = :create_id,
                    `created_at` = now()";

            $stmt = $db->prepare($query);

            $stmt->bindParam(':template_id', $id);
            $stmt->bindParam(':review_id', $last_id);
            $stmt->bindParam(':review_question_id', $value['id']);
            $stmt->bindParam(':answer', $value['check']);
            $stmt->bindParam(':create_id', $uid);

            $stmt->execute();
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
