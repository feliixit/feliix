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
        $record_id = (isset($_POST['record_id']) ?  $_POST['record_id'] : 0);
        $period = (isset($_POST['period']) ?  $_POST['period'] : 0);
        $answer = (isset($_POST['answer']) ?  $_POST['answer'] : '[]');
        $answer_array = json_decode($answer, true);
        $access_type = (isset($_POST['access_type']) ?  $_POST['access_type'] : '');
      
        if ($pid == 0) {
            http_response_code(401);
            echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
            die();
        }

        try {
            // now you can apply
            $query = "update leadership_assessment_review
                SET
                    `period` = :period,
                    `answer` = :answer,
                    `updated_id` = :updated_id,
                    `updated_at` = now(),
                    `status` = 1,
                    `user_complete_at` = now()
                    where id = :id";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':period', $period);
            $stmt->bindParam(':answer', $answer);
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

            // questionss
            $query = "select id, category, css_class, is_development from leadership_assessment_questions where status <> -1";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $questions = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $questions[] = $row;
            }

            // pre_answer
            $query = "select id, question_id, score, type from leadership_assessment_answers where pid = :id and type = :type and status <> -1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $record_id);
            $stmt->bindParam(':type', $access_type);
            $stmt->execute();

            $pre_answer = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pre_answer[] = $row;
            }
            
            foreach ($answer_array as $key => $value) {
                if(substr($key, 0, 6) != "answer")
                    continue;

                $question_id = substr($key, 6);

                $score = $value;
                $category = "";
                $css_class = "";
                $is_development = "";
                
                foreach ($questions as $key => $value) {
                    if ($value['id'] == $question_id) {
                        $category = $value['category'];
                        $css_class = $value['css_class'];
                        $is_development = $value['is_development'];
                        break;
                    }
                }

                $pre_score = "";
                $pre_id = 0;
                foreach ($pre_answer as $key => $value) {
                    if ($value['question_id'] == $question_id) {
                        $pre_score = $value['score'];
                        $pre_id = $value['id'];
                        break;
                    }
                }

                // if there is no pre_answer, insert
                if ($pre_score == "") {
                    $query = "insert into leadership_assessment_answers(pid, question_id, score, score1, type, category, css_class, is_development, create_id, created_at) values(:pid, :question_id, :score, :score1, :type, :category, :css_class, :is_development, :create_id, now())";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':pid', $record_id);
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->bindParam(':score', $score);
                    $stmt->bindParam(':score1', $score);
                    $stmt->bindParam(':type', $access_type);
                    $stmt->bindParam(':category', $category);
                    $stmt->bindParam(':css_class', $css_class);
                    $stmt->bindParam(':is_development', $is_development);
                    $stmt->bindParam(':create_id', $user_id);

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
                } else {
                    // if there is pre_answer, update
                    $query = "update leadership_assessment_answers
                    SET
                        `score` = :score,
                        `score2` = :score2,
                        `updated_id` = :updated_id,
                        `updated_at` = now()
                        where pid = :pid and question_id = :question_id and type = :type";

                    
                    $average = ($score + ($pre_score == 0 ? $score : $pre_score)) / ($score == 0 ? 1 : 2);

                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':score', $average);
                    $stmt->bindParam(':score2', $score);
                    $stmt->bindParam(':updated_id', $user_id);
                    $stmt->bindParam(':pid', $record_id);
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->bindParam(':type', $access_type);

                    try {
                        // execute the query, also check

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
                }

            }


            // if there is 9 leadership_assessment_review, update leadership_assessment status to 1
            $query = "select count(*) as cnt from leadership_assessment_review where status = 1 and pid = :pid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pid', $record_id);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['cnt'] == 9) {
                $query = "update leadership_assessment
                SET
                    `status` = 2,
                    `user_complete_at` = now()
                    where id = :id";

                // prepare the query
                $stmt = $db->prepare($query);

                // bind the values
                $stmt->bindParam(':id', $record_id);

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
        break;
}
