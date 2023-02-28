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

$uid = (isset($_GET['uid']) ?  urldecode($_GET['uid']) : '');

$fpt = (isset($_GET['fpt']) ?  $_GET['fpt'] : '');

$fc = (isset($_GET['fc']) ?  $_GET['fc'] : '');

$fpt = urldecode($fpt);

$fg = (isset($_GET['fg']) ?  $_GET['fg'] : '');

$fpc = (isset($_GET['fpc']) ?  $_GET['fpc'] : '');
$fpc = urldecode($fpc);

$kw = (isset($_GET['kw']) ?  $_GET['kw'] : '');
$kw = urldecode($kw);

$st = (isset($_GET['st']) ?  $_GET['st'] : '');


$op1 = (isset($_GET['op1']) ?  urldecode($_GET['op1']) : '');
$od1 = (isset($_GET['od1']) ?  urldecode($_GET['od1']) : '');

$op2 = (isset($_GET['op2']) ?  urldecode($_GET['op2']) : '');
$od2 = (isset($_GET['od2']) ?  urldecode($_GET['od2']) : '');

$page = (isset($_GET['page']) ?  urldecode($_GET['page']) : "");
$size = (isset($_GET['size']) ?  urldecode($_GET['size']) : "");

$kind = (isset($_GET['kind']) ?  urldecode($_GET['kind']) : "");

$tp = (isset($_GET['tp']) ?  urldecode($_GET['tp']) : '');


$id = (isset($_GET['id']) ?  $_GET['id'] : 0);

$merged_results = array();

$query = "SELECT pm.id, 
                pm.topic,
                pm.access, 
                pm.start_date,
                pm.end_date,
                pm.rule,
                pm.display,
                pm.sort,
                pm.status,
                c_user.username AS created_by, 
                u_user.username AS updated_by,
                DATE_FORMAT(pm.created_at, '%Y-%m-%d %H:%i:%s') created_at, 
                DATE_FORMAT(pm.updated_at, '%Y-%m-%d %H:%i:%s') updated_at
          FROM voting_template pm 
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                where pm.status <> -1 ";

// for record size
$query_cnt = "SELECT count(*) cnt 
FROM voting_template pm 
                LEFT JOIN user c_user ON pm.create_id = c_user.id 
                LEFT JOIN user u_user ON pm.updated_id = u_user.id 
                where pm.status <> -1"; 

if($id != 0){
    $query .= " and pm.id = $id ";
    $query_cnt .= " and pm.id = $id ";
}


if($kw != "")
{
    $query = $query . " and (pm.topic like '%" . $kw . "%' or pm.access like '%" . $kw . "%')";
    $query_cnt = $query_cnt . " and (pm.topic like '%" . $kw . "%' or pm.access like '%" . $kw . "%')";
}

if($st == 'not_yet')
{
    $query = $query . " and pm.start_date > DATE_FORMAT(NOW(), '%Y-%m-%d') ";
    $query_cnt = $query_cnt . " and pm.start_date > DATE_FORMAT(NOW(), '%Y-%m-%d') ";
}

if($st == 'ongoing')
{
    $query = $query . " and pm.start_date <= DATE_FORMAT(NOW(), '%Y-%m-%d') and pm.end_date >= DATE_FORMAT(NOW(), '%Y-%m-%d') ";
    $query_cnt = $query_cnt . " and pm.start_date <= DATE_FORMAT(NOW(), '%Y-%m-%d') and pm.end_date >= DATE_FORMAT(NOW(), '%Y-%m-%d') ";
}

if($st == 'finished')
{
    $query = $query . " and pm.end_date < DATE_FORMAT(NOW(), '%Y-%m-%d') ";
    $query_cnt = $query_cnt . " and pm.end_date < DATE_FORMAT(NOW(), '%Y-%m-%d') ";
}

$sOrder = "";
if($op1 != "" && $op1 != "0")
{
    switch ($op1)
    {
        case 1:
            if($od1 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od1 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
       
        
        default:
    }
}

if($op2 != "" && $op2 != "0" && $sOrder != "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder .= ", Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder .= ", Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder .= ", Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
      
        
        default:
    }
}


if($op2 != "" && $op2 != "0" && $sOrder == "")
{
    switch ($op2)
    {
        case 1:
            if($od2 == 2)
                $sOrder = "Coalesce(pm.created_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.created_at, '9999-99-99') ";
            break;  
        case 2:
            if($od2 == 2)
                $sOrder = "Coalesce(pm.updated_at, '0000-00-00') desc";
            else
                $sOrder = "Coalesce(pm.updated_at, '9999-99-99') ";
            break;  
       
        
        default:
    }
}

if($sOrder != "")
    $query = $query . " order by  " . $sOrder;
else
    $query = $query . " order by pm.created_at desc ";


if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();

$cnt = 0;
$stmt_cnt = $db->prepare( $query_cnt );
$stmt_cnt->execute();
while($row = $stmt_cnt->fetch(PDO::FETCH_ASSOC)) {
    $cnt = $row['cnt'];
}

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $topic = $row['topic'];
    $access = $row['access'];
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];
    $rule = $row['rule'];
    $display = $row['display'];
    $sort = $row['sort'];
    $status = $row['status'];
    $created_by = $row['created_by'];
    $updated_by = $row['updated_by'];
    $created_at = $row['created_at'];
    $updated_at = $row['updated_at'];

    $details = GetDetail($id, $db);
    if($access != "") {
        $access_array = json_decode($access,true);
        $access_text = GetAccessText($access_array);
    }
    else {
        $access_array = [];
        $access_text = "";
    }

    $rule_text = GetRuleText($rule);
    $display_text = GetDisplayText($display);
    $sort_text = GetSortText($sort);

 
    $merged_results[] = array(
        'id' => $id,
        'topic' => $topic,
        'access' => $access,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'rule' => $rule,
        'display' => $display,
        'sort' => $sort,
        'status' => $status,
        'created_by' => $created_by,
        'updated_by' => $updated_by,
        'created_at' => $created_at,
        'updated_at' => $updated_at,

        'access_text' => $access_text,
        'access_array' => $access_array,
        'rule_text' => $rule_text,
        'display_text' => $display_text,
        'sort_text' => $sort_text,

        'details' => $details,
        "vote_status" => GetVotingStatus($start_date, $end_date),
        "votes" => [],

        "cnt" => $cnt,

    );
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


function GetDetail($template_id, $db)
{
    $query = "select id, template_id, sn, title, pic, description, link from voting_template_detail where template_id = " . $template_id . " order by sn";
    $stmt = $db->prepare( $query );
    $stmt->execute();

    $merged_results = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $template_id = $row['template_id'];
        $sn = $row['sn'];
        $title = $row['title'];
        $pic = $row['pic'];
        $description = $row['description'];
        $link = $row['link'];

        $merged_results[] = array(
            'id' => $id,
            'template_id' => $template_id,
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

function GetVotingStatus($start_date, $end_date)
{
    $status = "";
    $now = date("Y-m-d");
    if($now < $start_date)
        $status = "Not Yet Start";
    else if($now >= $start_date && $now <= $end_date)
        $status = "Ongoing";
    else if($now > $end_date)
        $status = "Finished";

    return $status;
}

function GetRuleText($rule)
{
    $rule_text = "";

    if($rule == "1")
        $rule_text = "one person - one vote";
    else if($rule == "2")
        $rule_text = "one person - two votes";
    else if($rule == "3")
        $rule_text = "one person - three votes";

    return $rule_text;
}

function GetDisplayText($display)
{
    $display_text = "";

    if($display == "1")
        $display_text = "Top 1";
    else if($display == "2")
        $display_text = "Top 3";
    else if($display == "3")
        $display_text = "Top 5";
    else if($display == "4")
        $display_text = "Top 10";
    else if($display == "5")
        $display_text = "All";

    return $display_text;
}

function GetSortText($sort)
{
    $sort_text = "";

    if($sort == "1")
        $sort_text = "Descending";
    else if($sort == "2")
        $sort_text = "Ascending";
   
    return $sort_text;
}

function GetAccessText($access_array)
{
    $access_text = "";

    foreach($access_array as $access)
    {
        if($access == "all")
            $access_text .= "All, ";
        else if($access == "sales")
            $access_text .= "Sales Department, ";
        else if($access == "lighting")
            $access_text .= "Lighting Department, ";
        else if($access == "office")
            $access_text .= "Office Department, ";
        else if($access == "design")
            $access_text .= "Design Department, ";
        else if($access == "engineering")
            $access_text .= "Engineering Department, ";
        else if($access == "admin")
            $access_text .= "Admin Department, ";
        else if($access == "store")
            $access_text .= "Store Department, ";
        else
            $access_text .= $access . ", ";
    }

    $access_text = rtrim($access_text, ", ");

    return $access_text;
}

?>