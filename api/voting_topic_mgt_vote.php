<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);

$merged_results = array();

$votes = GetVotes($id, $db);

echo json_encode($votes, JSON_UNESCAPED_SLASHES);


function GetVotes($template_id, $db)
{
    $query = "select voting_review.id, template_id, user_id, u.username, voting_review.created_at from voting_review left join user u on u.id = voting_review.create_id where voting_review.`status` <> -1 and template_id = " . $template_id;
    $stmt = $db->prepare( $query );
    $stmt->execute();

    $merged_results = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $template_id = $row['template_id'];
        $user_id = $row['user_id'];
        $username = $row['username'];
        $answers = GetAnswers($id, $db);
        $created_at = $row['created_at'];

        $merged_results[] = array(
            'id' => $id,
            'template_id' => $template_id,
            'user_id' => $user_id,
            'username' => $username,
            'answers' => $answers,
            'created_at' => $created_at,
        );
    }

    return $merged_results;
}


function GetAnswers($review_id, $db)
{
    $query = "select id, review_question_id, answer, sn, title, pic, description, link from voting_review_detail a left join voting_template_detail q on q.id = a. review_question_id where voting_review_detail.`status` <> -1 and review_id = " . $review_id;
    $stmt = $db->prepare( $query );
    $stmt->execute();

    $merged_results = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $review_question_id = $row['review_question_id'];
        $answer = $row['answer'];
        $sn = $row['sn'];
        $title = $row['title'];
        $pic = $row['pic'];
        $description = $row['description'];
        $link = $row['link'];

        $merged_results[] = array(
            'id' => $id,
            'review_question_id' => $review_question_id,
            'answer' => $answer,
            'sn' => $sn,
            'title' => $title,
            'pic' => $pic,
            'description' => $description,
            'link' => $link,
            'url' => ($pic != '' ? "https://storage.googleapis.com/feliiximg/" . $pic : ''),
            
        );
    }

    return $merged_results;
}

?>