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

        if(trim(strtoupper($department)) == 'SALES')
        {

            $access7 = true;
        }
       
        if($username == "Kristel Tan" || $username == "Kuan" || $username == "Dennis Lin" || $username == "Marie Kayla Patricia Dequina" || $username == "Gina Donato" || $username == "dereck" || $username == "Aiza Eisma" || $username == "Johmar Maximo" || $username == "Stephanie De dios" || $username == "Jack Beringuela")
        {
            $access7 = true;
        }

        if(!$access7)
        {
         http_response_code(401);

         echo json_encode(array("message" => "Access denied."));
         die();
        }
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

$total_amount = 0;
$total_ar = 0;
$total_d = 0;
$total_p = 0;
$total_net_amount = 0;
$total_tax_withheld = 0;

switch ($method) {
    case 'GET':
        $strDate = (isset($_GET['d']) ?  $_GET['d'] : "");
        $strEDate = (isset($_GET['e']) ?  $_GET['e'] : "");
        $sale_person = (isset($_GET['p']) ?  $_GET['p'] : "");
        $sale_person = urldecode($sale_person);
        $category = (isset($_GET['c']) ?  $_GET['c'] : "");
    
        if($strDate == '')
        {
            $strDate = date('Y-m-d');

            for($i = 0; $i < 2; $i++)
            {
                $strDate = date("Y-m-d",strtotime($strDate . "first day of " . $i . " month"));
                $merged_results[] =  GetOneMonth($strDate, $sale_person, $category, $db);
            }
        }

        if($strDate != '' && $strEDate != "")
        {
            $interval = DateInterval::createFromDateString('1 month');
            $period = new DatePeriod(new DateTime($strDate), $interval, new DateTime(date("Y-m-d",strtotime($strEDate . "first day of 1 month"))));

            foreach ($period as $dt) {
                $strDate = $dt->format("Y-m-d");
                $merged_results[] =  GetCurrentMonth($strDate, $sale_person, $category, $db);

            }
        }

        // reverse the results
        $merged_results = array_reverse($merged_results);

        echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);

        break;

}

function GetCurrentMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);

    $sum = 0;
    foreach ($report1 as &$value) {
        $sum = $sum + $value['subtotal'];
    }

    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "report" => $report1, "sum" => $sum);
}

function GetOneMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -2 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 0 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);

    $sum = 0;
    foreach ($report1 as &$value) {
        $sum = $sum + $value['subtotal'];
    }


    return array("date" => date("Y/m",strtotime($strDate . "first day of -1 month")), "report" => $report1, "sum" => $sum);
}

function GetSalesMember($person, $db)
{
    $sql = "SELECT id, username FROM user WHERE apartment_id = 1 AND status = 1";

    if($person != "")
    {
        $sql = $sql . " and username = '" . $person . "' ";
    }
    
    $sql = $sql . " ORDER BY username";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $num = $stmt->rowCount();

    if($num > 0)
    {
        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $results[] = array("id" => $id, "username" => $username);
        }

        return $results;
    }
    else
    {
        return array();
    }
}

function GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db){
    $sql = "SELECT user.username,
                    pm.project_name,
                    CASE pm.catagory_id  
                            WHEN 1 THEN 'Office System'
                            WHEN 2 THEN 'Lighting'
                            ELSE ''  
                        END   catagory,
                    sum(pp.amount) amount
                FROM   project_proof pp
                LEFT JOIN project_main pm
                        ON pp.project_id = pm.id
                LEFT JOIN user
                        ON pm.create_id = user.id
                WHERE pp.status = 1
                AND pp.kind in(0, 1) AND user.apartment_id = 1
                AND pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "' ";

        if($sale_person != "")
        {
            $sql = $sql . " and user.username = '" . $sale_person . "' ";
        }

        if($category != "")
        {
            $sql = $sql . " and pm.catagory_id = " . $category . " ";
        }
                
            $sql = $sql . " group by user.username, pm.project_name, catagory
                    ORDER BY username, catagory
                    ";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // iterate through the results to find the count of catagories count
        $l_catagory = [];
        $o_catagory = [];

        $username = "";

        $subtotal = 0;

        $merged_results = [];

        foreach ($result as &$value) {
            if ($username != $value['username'] && $username != "") {

                $merged_results[] = array(
                    "username" => $username,
                    "l_catagory" => $l_catagory,
                    "o_catagory" => $o_catagory,

                    "subtotal" => $subtotal,
                
                );

                $l_catagory = [];
                $o_catagory = [];

                $$username = $value['username'];
                $subtotal = 0;
                
            }

            if($value['catagory'] == "Office System")
            {
                $o_catagory[] = array("catagory" => $value['catagory'], "project_name" => $value['project_name'], "amount" => $value['amount']);
            }
            else
            {
                $l_catagory[] = array("catagory" => $value['catagory'], "project_name" => $value['project_name'], "amount" => $value['amount']);
            }

            $subtotal = $subtotal + $value['amount'];
            $username = $value['username'];
        }

        if($username != "")
        {
            $merged_results[] = array(
                "username" => $username,
                "l_catagory" => $l_catagory,
                "o_catagory" => $o_catagory,

                "subtotal" => $subtotal,
            
            );
        }

        $sales = GetSalesMember($sale_person, $db);

        // if $merged_results not match with $sale_person, add the empty result
        foreach ($sales as &$value) {
            $bFound = false;
            foreach ($merged_results as &$value2) {
                if($value['username'] == $value2['username'])
                {
                    $bFound = true;
                    break;
                }
            }

            if($bFound == false)
            {
                $dummy_catagory = [];
                $dummy_catagory[] = array("catagory" => "", "project_name" => "", "amount" => 0);
                $merged_results[] = array(
                    "username" => $value['username'],
                    "l_catagory" => $dummy_catagory,
                    "o_catagory" => [],

                    "subtotal" => 0,
                
                );
            }
        }

        // sort by name again
        usort($merged_results, function($a, $b) {
            return strtoupper($a['username']) <=> strtoupper($b['username']);
        });

        return $merged_results;
}
