<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : '');
$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
$section = (isset($_GET['section']) ?  $_GET['section'] : "");

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
$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $user_id = $decoded->data->id;

    $merged_results = array();

    // get category
    $filter = get_category($section);

    $query = "SELECT question_id, score, score1, score2, `type`, category
                FROM leadership_assessment_answers pr
              WHERE pr.status <> -1 and pr.pid = " . $id . "  " . $filter . " order by question_id";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $pivot = sort_and_group_by_type($merged_results);

    $questions = array();
    $query = "SELECT id, question, css_class, category FROM leadership_assessment_questions pr where pr.`status` <> -1 " . $filter . " order by pr.id";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $questions[] = $row;
    }

    $merged_results = array();

    foreach ($pivot as $key => $value) {
        $found = false;
        foreach ($questions as $key2 => $value2) {
            if ($value2['id'] == $value['question_id']) {
                $merged_results[] = array('id' => $value2['id'], 'question' => $value2['question'], 'css_class' => $value2['css_class'], 'category' => $value2['category'], 'direct' => $value['direct'], 'manager' => $value['manager'], 'peer' => $value['peer'], 'other' => $value['other'], 'self' => $value['self'], 'average' => $value['average']);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $merged_results[] = array('id' => 0, 'question' => "", 'css_class' => "", 'category' => "", 'direct' => $value['direct'], 'manager' => $value['manager'], 'peer' => $value['peer'], 'other' => $value['other'], 'self' => $value['self'], 'average' => $value['average']);
        }
    }

    echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
}

function sort_and_group_by_type($data)
{
    // pivot table into column average score(direct + manager + peer + other + self) / 5, type(direct), type(manager), type(peer), type(other), type(self)  by question_id
    $question_id = 0;
    $direct = 0;
    $manager = 0;
    $peer = 0;
    $other = 0;
    $self = 0;

    $direct1 = 0;
    $direct2 = 0;
    $manager1 = 0;
    $manager2 = 0;
    $peer1 = 0;
    $peer2 = 0;
    $other1 = 0;
    $other2 = 0;

    $type = "";
    $category = "";

    $result = array();

    foreach ($data as $item) {
        if ($question_id == 0) {
            $question_id = $item['question_id'];
            $type = $item['type'];
            $category = $item['category'];
        }

        if ($question_id != $item['question_id']) {
            $result[] = array('question_id' => $question_id, 'direct' => $direct, 'manager' => $manager, 'peer' => $peer, 'other' => $other, 'self' => $self, 'type' => $type, 'category' => $category, 'direct1' => $direct1, 'direct2' => $direct2, 'manager1' => $manager1, 'manager2' => $manager2, 'peer1' => $peer1, 'peer2' => $peer2, 'other1' => $other1, 'other2' => $other2);

            $question_id = $item['question_id'];
            $direct = 0;
            $manager = 0;
            $peer = 0;
            $other = 0;

            $direct1 = 0;
            $direct2 = 0;
            $manager1 = 0;
            $manager2 = 0;
            $peer1 = 0;
            $peer2 = 0;
            $other1 = 0;
            $other2 = 0;

            $self = 0;
            $type = $item['type'];
            $category = $item['category'];
        }

        if ($item['type'] == 'direct') {
            $direct = $item['score'];
            $direct1 = $item['score1'];
            $direct2 = $item['score2'];
        } else if ($item['type'] == 'manager') {
            $manager = $item['score'];
            $manager1 = $item['score1'];
            $manager2 = $item['score2'];
        } else if ($item['type'] == 'peer') {
            $peer = $item['score'];
            $peer1 = $item['score1'];
            $peer2 = $item['score2'];
        } else if ($item['type'] == 'other') {
            $other = $item['score'];
            $other1 = $item['score1'];
            $other2 = $item['score2'];
        } else if ($item['type'] == 'self') {
            $self = $item['score'];
        }

    }

    $result[] = array('question_id' => $question_id, 'direct' => $direct, 'manager' => $manager, 'peer' => $peer, 'other' => $other, 'self' => $self, 'type' => $type, 'category' => $category, 'direct1' => $direct1, 'direct2' => $direct2, 'manager1' => $manager1, 'manager2' => $manager2, 'peer1' => $peer1, 'peer2' => $peer2, 'other1' => $other1, 'other2' => $other2);

    // add average score
    foreach ($result as $key => $value) {
        $divisor = 0;
        $sum = 0;
        if ((int)$value['direct1'] != 0) 
        {
            $sum += $value['direct1'];
            $divisor++;
        }
        if ((int)$value['direct2'] != 0) 
        {
            $sum += $value['direct2'];
            $divisor++;
        }

        if ((int)$value['manager1'] != 0) 
        {
            $sum += $value['manager1'];
            $divisor++;
        }
        if ((int)$value['manager2'] != 0) 
        {
            $sum += $value['manager2'];
            $divisor++;
        }

        if ((int)$value['peer1'] != 0) 
        {
            $sum += $value['peer1'];
            $divisor++;
        }
        if ((int)$value['peer2'] != 0) 
        {
            $sum += $value['peer2'];
            $divisor++;
        }

        if ((int)$value['other1'] != 0) 
        {
            $sum += $value['other1'];
            $divisor++;
        }
        if ((int)$value['other2'] != 0) 
        {
            $sum += $value['other2'];
            $divisor++;
        }
        // if ($value['self'] != 0) 
        // {
        //     $sum += $value['self'];
        //     $divisor++;
        // }

        $result[$key]['average'] = number_format($sum / $divisor, 1, '.', '');
    }

    // order by highest average score
    usort($result, function ($a, $b) {
        return floatval($b['average']) > floatval($a['average']);
    });

    return $result;
}

function get_category($section)
{
    $filer = "";

    if($section == 'PRODUCTION')
        $filer = " and pr.category = 'Production' ";
    if($section == 'PERMISSION')
        $filer = " and pr.category = 'Permission' ";
    if($section == 'PINNACLE-SELF')
        $filer = " and pr.category = 'Pinnacle-S' ";
    if($section == 'PINNACLE-OTHERS')
        $filer = " and pr.category = 'Pinnacle-O' ";
    if($section == 'POSITION')
        $filer = " and pr.category = 'Position' ";
    if($section == 'PEOPLE DEVELOPMENT')
        $filer = " and pr.is_development = 'Y' ";
    if($section == 'APPENDIX')
        $filer = "  ";

    return $filer;
}