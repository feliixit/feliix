<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];

date_default_timezone_set('Asia/Taipei');

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $access7 = false;
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $position = $decoded->data->position;
        $department = $decoded->data->department;
        $username = $decoded->data->username;

    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';


$database = new Database();
$db = $database->getConnection();


switch ($method) {
    case 'GET':
        $strDate = (isset($_GET['d']) ?  $_GET['d'] : "");
        $strEDate = (isset($_GET['e']) ?  $_GET['e'] : "");
        $sale_person = (isset($_GET['p']) ?  $_GET['p'] : "");
        $sale_person = urldecode($sale_person);
        $category = (isset($_GET['c']) ?  $_GET['c'] : "");
        $archive = (isset($_GET['a']) ? $_GET['a'] : "");
    
        if($strDate == '')
        {
            $strEDate = date('Y-m-d');
            $strSDate = date("Y",strtotime($strEDate)) . "-01-01";

            $strEDate = date("Y-m-d",strtotime($strEDate . "last day of this month"));

            $merged_results =  GetMonthSaleReport($strSDate, $strEDate, $sale_person, $category, $archive, $db);
        }

        if($strDate != '' && $strEDate != "")
        {
            $strSDate = date("Y-m",strtotime($strDate)) . "-01";
            $strEDate = date("Y-m-d",strtotime($strEDate . "last day of this month"));
            $merged_results =  GetMonthSaleReport($strSDate, $strEDate, $sale_person, $category, $archive, $db);

        }

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}



function GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $archive, $db){
    $sql = "SELECT pm.id pid, COALESCE(user.username, ' ') username,
                pm.project_name,
                pm.catagory_id,
                CASE pm.catagory_id  
                        WHEN 1 THEN 'Office System'
                        WHEN 2 THEN 'Lighting'
                        ELSE ''  
                    END   catagory,
                    COALESCE(ps.project_status, '') project_status, 
                pm.final_amount,
                (SELECT count(*) cnt FROM project_proof pp  where pp.status <> -1 AND pp.`status` > 0 and pp.project_id = pm.id) proof_count,
                pm.created_at , pm.estimate_close_prob , pm.archive 
                from project_main pm
            LEFT JOIN user
                    ON pm.create_id = user.id
                    LEFT JOIN project_status ps ON pm.project_status_id = ps.id 
                    WHERE pm.created_at <= '" . $PeriodEnd . " 23:59:59' 
                    and pm.created_at >= '" . $PeriodStart . " 00:00:00'  ";

        if($sale_person != "")
        {
            $sql = $sql . " and user.username = '" . $sale_person . "' ";
        }

        if($category != "")
        {
            $sql = $sql . " and pm.catagory_id = " . $category . " ";
        }

        if($archive == "1")
        {
            $sql = $sql . " and pm.archive = " . $archive . " ";
        }
                
        if($archive == "0")
        {
            $sql = $sql . " and pm.archive = " . $archive . " ";
        }

        if($archive == "")
        {
            $sql = $sql . " and pm.archive = 0 ";
        }
                
    
        $sql = $sql . " 
                ORDER BY username, catagory, project_name
                ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $username = "";

        $yet_lighting_array = array();
        $yet_office_array = array();

        $close_lighting_array = array();
        $close_office_array = array();

        $disapprove_lighting_array = array();
        $disapprove_office_array = array();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // first row
            if ($username == "") {
                $username = $row['username'];
            }

            if ($username == $row['username']) {
                // yet
                if($row['project_status'] != 'Disapproved' && $row['proof_count'] == 0)
                {
                    if($row['catagory'] == 'Lighting')
                    {
                        array_push($yet_lighting_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    else if($row['catagory'] == 'Office System')
                    {
                        array_push($yet_office_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    
                }

                // close
                if($row['project_status'] != 'Disapproved' && $row['proof_count'] > 0)
                {
                    if($row['catagory'] == 'Lighting')
                    {
                        array_push($close_lighting_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    else if($row['catagory'] == 'Office System')
                    {
                        array_push($close_office_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    
                }

                // disapprove
                if($row['project_status'] == 'Disapproved')
                {
                    if($row['catagory'] == 'Lighting')
                    {
                        array_push($disapprove_lighting_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    else if($row['catagory'] == 'Office System')
                    {
                        array_push($disapprove_office_array, array(
                            "username" => $username,
                            "project_name" =>  $row['project_name'],
                            "final_amount" => $row['final_amount'],
                            "estimate_close_prob" => $row['estimate_close_prob'],
                            "archive" =>  $row['archive'],
                            "created_at" =>  $row['created_at'],
                            "pid" => $row['pid'],
                        ));
                    }
                    
                }

                $username = $row['username'];

            }
            else
            {

                $merged_results[] = array(
                    "username" => $username,
    
                    "yet_lighting_array" => $yet_lighting_array,
                    "yet_office_array" => $yet_office_array,
    
                    "close_lighting_array" => $close_lighting_array,
                    "close_office_array" => $close_office_array,
    
                    "disapprove_lighting_array" => $disapprove_lighting_array,
                    "disapprove_office_array" => $disapprove_office_array,

                    "date" => substr($PeriodStart, 0, 7) . " - " . substr($PeriodEnd, 0, 7),
                );
    
                $username = $row['username'];
                $yet_lighting_array = array();
                $yet_office_array = array();
                $close_lighting_array = array();
                $close_office_array = array();
                $disapprove_lighting_array = array();
                $disapprove_office_array = array();
            }
        }

        if ($username != "") {
            $merged_results[] = array(
                "username" => $username,

                "yet_lighting_array" => $yet_lighting_array,
                "yet_office_array" => $yet_office_array,

                "close_lighting_array" => $close_lighting_array,
                "close_office_array" => $close_office_array,

                "disapprove_lighting_array" => $disapprove_lighting_array,
                "disapprove_office_array" => $disapprove_office_array,

                "date" => substr($PeriodStart, 0, 7) . " - " . substr($PeriodEnd, 0, 7),
            );

        }


        return $merged_results;
}
