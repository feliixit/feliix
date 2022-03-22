<?php

error_reporting(E_ERROR | E_PARSE);
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

        if(trim(strtoupper($department)) == '')
        {
            if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
            {
                $access7 = true;
            }
        }

        if($username == "Glendon Wendell Co")
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

        $total1 = [];
    
        if($strDate == '')
        {
            $strDate = date('Y-m-d');

            $this_year = date("Y",strtotime($strDate . "first day of 0 year"));
            $last_year = date("Y",strtotime($strDate . "first day of -1 year"));

            $strDate    = $this_year;
                $strEDate      = $last_year;


        }

        if($strDate != '' && $strEDate != "")
        {
            if($strDate > $strEDate)
            {
                $tempDate = $strDate;
                $strDate = $strEDate;
                $strEDate = $tempDate;
            }

            $interval = DateInterval::createFromDateString('1 year');
            $period = new DatePeriod(new DateTime($strDate . "-01-01"), $interval, new DateTime(date("Y-m-d",strtotime($strEDate . "-12-31"))));
            foreach ($period as $dt) {
                $strDate = $dt->format("Y") . "-01-01";
                $strEDate = $dt->format("Y") . "-12-31";
                $merged_results =  GetYearCurrentMonth($strDate, $strEDate, $sale_person, $category, $db);

                $total1[] = array(
                    "data" => $merged_results,
                    "year" => $strDate,
                   
                );

            }

            
        }

        $reversed = array_reverse($total1);
        
        echo json_encode($reversed, JSON_UNESCAPED_SLASHES);

        break;

}

function GetYearCurrentMonth($strDate, $strEDate, $sale_person, $category, $db)
{
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod(new DateTime($strDate), $interval, new DateTime(date("Y-m-d",strtotime($strEDate . "first day of 1 month"))));

    foreach ($period as $dt) {
        $strDate = $dt->format("Y-m-d");
        $merged_results[] =  GetCurrentMonth($strDate, $sale_person, $category, $db);

    }

    $total_net_amount_l = 0;
        $total_net_amount_o = 0;
        $total_net_shagrila = 0;
        $total_cash_received = 0;
        $total_ar = 0;
        $total_down_payment = 0;
        $total_cash_expense = 0;
        $total_lai_expense = 0;

        foreach ($merged_results as $mo) {
            $amount = $mo['total'];
            $total_net_amount_l += $amount['net_amount_l'];
            $total_net_amount_o += $amount['net_amount_o'];
            $total_net_shagrila += $amount['net_shagrila'];
            $total_cash_received += $amount['cash_received'];
            $total_ar += $amount['ar'];
            $total_down_payment += $amount['down_payment'];
            $total_cash_expense += $amount['cash_expense'];
            $total_lai_expense += $amount['lai_expense'];
        }

        $total1 = array(
            "merged_results" => $merged_results,
            "total_net_amount_l" => $total_net_amount_l,
            "total_net_amount_o" => $total_net_amount_o,
            "total_net_shagrila" => $total_net_shagrila,
            "total_cash_received" => $total_cash_received,
            "total_ar" => $total_ar,
            "total_down_payment" => $total_down_payment,
            "total_cash_expense" => $total_cash_expense,
            "total_lai_expense" => $total_lai_expense,
        );

    return $total1;
}

function GetCurrentMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $shagrila = GetMonthShagri($PeriodStart, $PeriodEnd, $db);
    $cash_record = GetMonthCashReceived($PeriodStart, $PeriodEnd, $db);
    //$down_payment = GetMonthCashReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $expense_record = GetMonthExpense($PeriodStart, $PeriodEnd, $db);
    $Lai_record = GetLaiExpense($PeriodStart, $PeriodEnd, $db);

    $net_amount_o = 0;
    $net_amount_l = 0;

    

    foreach($report1 as $row)
    {
        $l_catagory = $row['l_catagory'];
        foreach($l_catagory as $r)
            $net_amount_l += $r['net_amount'];

        $o_catagory = $row['o_catagory'];
        foreach($o_catagory as $r)
            $net_amount_o += $r['net_amount'];
    }

    $net_shagrila = 0;
    foreach($shagrila as $row)
    {
        $net_shagrila += $row['total_amount'] - $row['discount'];
    }

    $cash_received = 0;
    foreach($cash_record as $row)
    {
        $cash_received += $row['amount'];
    }

    $down_payment_amount = 0;
    foreach($report1 as $row)
    {
        $l_downpayment = $row['l_downpayment'];
        foreach($l_downpayment as $r)
            $down_payment_amount += $r['amount'];

        $o_downpayment = $row['o_downpayment'];
        foreach($o_downpayment as $r)
            $down_payment_amount += $r['amount'];
    }

    $cash_expense = 0;
    foreach($expense_record as $row)
    {
        $cash_expense += $row['amount'];
    }

    $ar = 0;
    foreach($report1 as $row)
    {
        $l_cumulate = $row['l_cumulate'];
        foreach($l_cumulate as $r)
            $ar += $r['ar'];

        $o_cumulate = $row['o_cumulate'];
        foreach($o_cumulate as $r)
            $ar += $r['ar'];
    }

    $lai_expense = 0;
    foreach($Lai_record as $row)
    {
        $lai_expense += $row['amount'];
    }

    $total1 = array(
        "net_amount_o" => $net_amount_o,
        "net_amount_l" => $net_amount_l,
        "net_shagrila" => $net_shagrila,
        "cash_received" => $cash_received + $net_shagrila,
        "down_payment" => $down_payment_amount,
        "cash_expense" => $cash_expense,
        "lai_expense" => $lai_expense,
        "ar" => $ar,
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "total" => $total1,);
}

function GetMonthCashReceived($start_date, $end_date, $db)
{
    try {

        $query = "select sum(amount) amount from project_proof 
                                where status = 1 ";

        if($start_date!='') {
            $query = $query . " and received_date > '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and received_date < '$end_date' ";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $amount = $row['amount'];

            $merged_results[] = array( 
                "amount" => $amount,
              
            );
        }

        // response in json format
        return $merged_results;
      
    }
    catch (Exception $e){
    
        return [];
    }
}

function GetMonthExpense($start_date, $end_date, $db)
{
    try {

        $query = "select sum(cash_out) amount from price_record 
                                where is_enabled = 1 ";

        if($start_date!='') {
            $query = $query . " and paid_date > '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and paid_date < '$end_date' ";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $amount = $row['amount'];

            $merged_results[] = array( 
                "amount" => $amount,
              
            );
        }

        // response in json format
        return $merged_results;
      
    }
    catch (Exception $e){
    
        return [];
    }
}

function GetLaiExpense($start_date, $end_date, $db)
{
    try {

        $query = "select sum(total_amount) amount from store_sales_lai 
                                where status <> -1 ";

        if($start_date!='') {
            $query = $query . " and sales_date > '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and sales_date < '$end_date' ";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $amount = $row['amount'];

            $merged_results[] = array( 
                "amount" => $amount,
              
            );
        }

        // response in json format
        return $merged_results;
      
    }
    catch (Exception $e){
    
        return [];
    }
}

function GetMonthShagri($start_date, $end_date, $db)
{
    try {

        $query = "SELECT  0 as is_checked, 
                                ss.id, 
                                ss.sales_date, 
                                ss.sales_name, 
                                ss.customer_name,
                                ss.discount,
                                ss.invoice,
                                ss.remark,
                                ss.payment_method,
                                ss.teminal,
                                ss.`status`,
                                DATE_FORMAT(ss.crt_time ,'%Y-%m-%d') crt_time
                                from store_sales ss
                                left join store_sales_record sr 
                                on sr.sales_id = ss.id
                                where ss.status <> -1 ";

        if($start_date!='') {
            $query = $query . " and ss.sales_date > '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.sales_date < '$end_date' ";
        }

   
        $query .= "group by
        ss.id, 
        ss.sales_date, 
        ss.sales_name, 
        ss.customer_name,
        ss.discount,
        ss.invoice,
        ss.remark,
        ss.payment_method,
        ss.teminal,
        ss.`status`,
        ss.crt_time ";
        
        $query .= " order by ss.sales_date ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
            $id = $row['id'];
            $sales_date = $row['sales_date'];
            $sales_name = $row['sales_name'];
            $customer_name = $row['customer_name'];
            $discount = $row['discount'];
            $invoice = $row['invoice'];
            $remark = $row['remark'];
 
            $teminal = $row['teminal'];
            $status = $row['status'];
            $crt_time = $row['crt_time'];

            $items = GetSalesDetail($id, $db);
           
            $merged_results[] = array( 
                "is_checked" => 0,
                "id" => $id,
                "sales_date" => $sales_date,
                "sales_name" => $sales_name,
                "customer_name" => $customer_name,
                "discount" => $discount,
                "invoice" => $invoice,
                "remark" => $remark,

                "teminal" => $teminal,
                "status" => $status,
                "payment" => $items,
                "total_amount" => GetAmount($items),
                "crt_time"=> $crt_time,
            );
        }

        // response in json format
        return $merged_results;
      
    }
    catch (Exception $e){
    
        return [];
    }
}

function GetAmount($array)
{
    $amount = 0;

    foreach($array as $item) {
        if($item['free'] == '')
            $amount += ($item['qty'] == "" ? 0 : $item['qty']) * ($item['price'] == "" ? 0 : $item['price']);
    }

    return $amount;
}


function GetOneMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $shagrila = GetMonthShagri($PeriodStart, $PeriodEnd, $db);
    $cash_record = GetMonthCashReceived($PeriodStart, $PeriodEnd, $db);
    //$down_payment = GetMonthCashReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $expense_record = GetMonthExpense($PeriodStart, $PeriodEnd, $db);

    $Lai_record = GetLaiExpense($PeriodStart, $PeriodEnd, $db);
    
    $net_amount_o = 0;
    $net_amount_l = 0;
    foreach($report1 as $row)
    {
        $l_catagory = $row['l_catagory'];
        foreach($l_catagory as $r)
            $net_amount_l += $r['net_amount'];

        $o_catagory = $row['o_catagory'];
        foreach($o_catagory as $r)
            $net_amount_o += $r['net_amount'];
    }

    $net_shagrila = 0;
    foreach($shagrila as $row)
    {
        $net_shagrila += $row['total_amount'] - $row['discount'];
    }

    $cash_received = 0;
    foreach($cash_record as $row)
    {
        $cash_received += $row['amount'];
    }

    $down_payment_amount = 0;
    foreach($report1 as $row)
    {
        $l_downpayment = $row['l_downpayment'];
        foreach($l_downpayment as $r)
            $down_payment_amount += $r['amount'];

        $o_downpayment = $row['o_downpayment'];
        foreach($o_downpayment as $r)
            $down_payment_amount += $r['amount'];
    }

    $cash_expense = 0;
    foreach($expense_record as $row)
    {
        $cash_expense += $row['amount'];
    }

    $lai_expense = 0;
    foreach($Lai_record as $row)
    {
        $lai_expense += $row['amount'];
    }

    $ar = 0;
    foreach($report1 as $row)
    {
        $l_cumulate = $row['l_cumulate'];
        foreach($l_cumulate as $r)
            $ar += $r['ar'];

        $o_cumulate = $row['o_cumulate'];
        foreach($o_cumulate as $r)
            $ar += $r['ar'];
    }

    $total1 = array(
        "net_amount_o" => $net_amount_o,
        "net_amount_l" => $net_amount_l,
        "net_shagrila" => $net_shagrila,
        "cash_received" => $cash_received + $net_shagrila,
        "down_payment" => $down_payment_amount,
        "cash_expense" => $cash_expense,
        "lai_expense" => $lai_expense,
        "ar" => $ar,
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "total" => $total1,);
}


function GetSalesDetail($sales_id, $db){
    $query = "
            SELECT 0 as is_checked, id, product_name, qty, price, free, DATE_FORMAT(crt_time, '%Y/%m/%d') crt_time
                FROM store_sales_record
            WHERE  sales_id = " . $sales_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $qty = $row['qty'] == 0 ? "" : $row['qty'];
        $price = $row['price'] == 0 ? "" : $row['price'];
        $free = $row['free'] == "" ? "" : $row['free'];
        $product_name = $row['product_name'] == "" ? "" : $row['product_name'];
        $crt_time = $row['crt_time'] == "" ? "" : $row['crt_time'];
    
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "qty" => $qty,
            "price" => $price,
            "free" => $free,
            "product_name" => $product_name,
           "crt_time" => $crt_time,
        );
    }

    return $merged_results;
}

function GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db){
    $sql = "SELECT pm.id pid, user.username,
                        pm.project_name,
                        pm.catagory_id,
                        CASE pm.catagory_id  
                                WHEN 1 THEN 'Office System'
                                WHEN 2 THEN 'Lighting'
                                ELSE ''  
                            END   catagory,
                        pm.`client`,
                        pm.final_amount,
                        pm.tax_withheld,    
                        pp.received_date,
                        CASE pp.kind
                                WHEN 0 THEN 'Down Payment'
                                WHEN 1 THEN 'Payment'
                                ELSE ''  
                            END   pay_type,
                        pp.kind,
                        pp.amount
                    FROM   project_proof pp
                    LEFT JOIN project_main pm
                            ON pp.project_id = pm.id
                    LEFT JOIN user
                            ON pm.create_id = user.id
                    WHERE pp.status = 1
                    AND pp.kind = 0
                    and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "'
                    AND pp.id IN (SELECT * 
                                FROM (SELECT MIN(n.id)
                                        FROM project_proof n where STATUS = 1
                                    GROUP BY n.project_id , n.kind HAVING n.kind = 0) x) ";

        if($sale_person != "")
        {
            $sql = $sql . " and user.username = '" . $sale_person . "' ";
        }

        if($category != "")
        {
            $sql = $sql . " and pm.catagory_id = " . $category . " ";
        }
                
        
        $sql = $sql . " UNION 

                SELECT pm.id pid, user.username,
                        pm.project_name,
                        pm.catagory_id,
                        CASE pm.catagory_id  
                                WHEN 1 THEN 'Office System'
                                WHEN 2 THEN 'Lighting'
                                ELSE ''  
                            END   catagory,
                        pm.`client`,
                        pm.final_amount,
                        pm.tax_withheld,    
                        pp.received_date,
                        CASE pp.kind
                                WHEN 0 THEN 'Down Payment'
                                WHEN 1 THEN 'Payment'
                                ELSE ''  
                            END   pay_type,
                        pp.kind,
                        pp.amount
                    FROM   project_proof pp
                    LEFT JOIN project_main pm
                            ON pp.project_id = pm.id
                    LEFT JOIN user
                            ON pm.create_id = user.id
                    WHERE pp.status = 1
                    AND pp.kind = 1
                    and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "'
                    AND pp.id IN (SELECT id 
                                FROM (SELECT MIN(n.id) id, SUM(amount) amt, kind, (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = n.project_id AND p.kind = 0) down_sum
                                        FROM project_proof n where STATUS = 1
                                    GROUP BY n.project_id , n.kind
                    HAVING down_sum = 0) x) ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
                    ORDER BY username, catagory
                    ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $username = "";
        
        $sub_amount = 0;
        $sub_ar = 0;
        $sub_d = 0;
        $sub_p = 0;
        $sub_net_amount = 0;
        $sub_tax_withheld = 0;

        $l_catagory = [];
        $o_catagory = [];

        $l_cumulate = [];
        $o_cumulate = [];

        $l_downpayment = [];
        $o_downpayment = [];

        $subtotal  = 0;
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($username != $row['username'] && $username != "") {
                
                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
/*
                if($o_catagory == []){
                    array_push($o_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
    
                 if($l_catagory == []){
                    array_push($l_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
*/
                foreach ($o_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['dsum'] + $value['psum'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }
    
                foreach ($l_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['dsum'] + $value['psum'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }

                $merged_results[] = array(
                    "username" => $username,
                    "l_catagory" => $l_catagory,
                    "o_catagory" => $o_catagory,

                    "l_cumulate" => $l_cumulate,
                    "o_cumulate" => $o_cumulate,

                    "l_downpayment" => $l_downpayment,
                    "o_downpayment" =>  $o_downpayment,
             
                    "sub_amount" => $sub_amount,
                    "sub_ar" => $sub_ar,
                    "sub_d" => $sub_d,
                    "sub_p" => $sub_p,
                    "sub_net_amount" => $sub_net_amount,
                    "sub_tax_withheld" => $sub_tax_withheld,

                    "subtotal" => $subtotal,
                
                );

                $l_catagory = [];
                $o_catagory = [];

                $l_cumulate = [];
                $o_cumulate = [];

                $l_downpayment = [];
                $o_downpayment = [];

                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
            }

            $username = $row['username'];
       
            if($row['catagory_id'] == 1)
            {
                array_push($o_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));
                $ary = GetCumulateDetail($row['pid'], $sale_person, $category, $db);
                if($ary != null)
                    array_push($o_cumulate, $ary);

                $down_ary = GetDownPaymentDetail($row['pid'], $PeriodStart, $PeriodEnd, $db);
                if($down_ary != null)
                    array_push($o_downpayment, $down_ary);
            }
            if($row['catagory_id'] == 2)
            {
                array_push($l_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));
                $ary = GetCumulateDetail($row['pid'], $sale_person, $category, $db);
                if($ary != null)
                    array_push($l_cumulate, $ary);

                $down_ary = GetDownPaymentDetail($row['pid'], $PeriodStart, $PeriodEnd, $db);
                if($down_ary != null)
                    array_push($l_downpayment, $down_ary);
            }

        }

        if ($username != "") {
            $sub_amount = 0;
            $sub_ar = 0;
            $sub_d = 0;
            $sub_p = 0;
            $sub_net_amount = 0;
            $sub_tax_withheld = 0;

            $subtotal = 0;
/*
            if($o_catagory == []){
                array_push($o_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }

             if($l_catagory == []){
                array_push($l_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }
*/
            foreach ($o_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['dsum'] + $value['psum'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            foreach ($l_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['dsum'] + $value['psum'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            $merged_results[] = array(
                "username" => $username,
                "l_catagory" => $l_catagory,
                "o_catagory" => $o_catagory,

                "l_cumulate" => $l_cumulate,
                "o_cumulate" => $o_cumulate,

                "l_downpayment" => $l_downpayment,
                "o_downpayment" => $o_downpayment,
  
                "sub_amount" => $sub_amount,
                "sub_ar" => $sub_ar,
                "sub_d" => $sub_d,
                "sub_p" => $sub_p,
                "sub_net_amount" => $sub_net_amount,
                "sub_tax_withheld" => $sub_tax_withheld,

                "subtotal" => $subtotal,
            );

        }

        if(count($merged_results) > 0)
        {
            usort($merged_results, function ($item1, $item2) {
                return $item2['sub_amount'] <=> $item1['sub_amount'];
            });
        }

        return $merged_results;
}


function GetCashDetail($_pid, $sdate, $edate, $sale_person, $category, $db)
{
    $sql = "SELECT user.username,
                pm.project_name,
                pm.`client`,
                DATE(pm.created_at) created_at,
                (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' and p.received_date > '" . $sdate . " 23:59:59' AND p.received_date < '" . $edate . "') dsum,
                (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' and p.received_date > '" . $sdate . " 23:59:59' AND p.received_date < '" . $edate . "') psum,

                (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date < '" . $edate . "') total_dsum,
                (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date < '" . $edate . "') total_psum,

                COALESCE(pm.final_amount, 0) final_amount,
                COALESCE(pm.tax_withheld, 0) tax_withheld,
                ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) -(SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.received_date <> '' AND p.received_date < '" . $edate . "') ar,
                ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) net_amount
                FROM  project_main pm

            LEFT JOIN user
                    ON pm.create_id = user.id
            WHERE 1 = 1
            ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
            AND pm.id = " . $_pid;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = array(
            "username" => $row["username"],
            "project_name" => $row["project_name"],
            "client" => $row["client"],
            "dsum" => $row["dsum"],
            "psum" => $row["psum"],
            "total_dsum" => $row["total_dsum"],
            "total_psum" => $row["total_psum"],
            "final_amount" => $row["final_amount"],
            "tax_withheld" => $row["tax_withheld"],
            "ar" => $row["ar"],
            "created_at" => $row["created_at"],
            "net_amount" => $row["final_amount"] - $row["tax_withheld"],
        );
    }

    return $merged_results;
}


function GetDownPaymentDetail($_pid, $PeriodStart, $PeriodEnd, $db)
{
    $sql = "SELECT pp.amount 
            FROM   project_proof pp
            LEFT JOIN project_main pm
                    ON pp.project_id = pm.id
        
            WHERE pp.status = 1
            AND pp.received_date <> ''
            and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "'
            ";
            
            $sql = $sql . " 
            AND pm.id = " . $_pid . " order by pp.received_date, pp.id limit 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = array(
            "amount" => $row["amount"],
          
        );
    }

    return $merged_results;
}




function GetCumulateDetail($_pid, $sale_person, $category, $db)
{
    $sql = "SELECT user.username,
                pm.project_name,
                pm.`client`,
                sum(CASE WHEN pp.kind = 1  THEN amount ELSE 0 END) psum,
                sum(CASE WHEN pp.kind = 0  THEN amount ELSE 0 END) dsum,
                COALESCE(pm.final_amount, 0) final_amount,
                COALESCE(pm.tax_withheld, 0) tax_withheld
            FROM   project_proof pp
            LEFT JOIN project_main pm
                    ON pp.project_id = pm.id
            LEFT JOIN user
                    ON pm.create_id = user.id
            WHERE pp.status = 1
            AND pp.received_date <> ''
           
            ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
            AND pm.id = " . $_pid;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = array(
            "username" => $row["username"],
            "project_name" => $row["project_name"],
            "client" => $row["client"],
            "dsum" => $row["dsum"],
            "psum" => $row["psum"],
            "final_amount" => $row["final_amount"],
            "tax_withheld" => $row["tax_withheld"],
            "ar" => $row["final_amount"] - $row["dsum"] - $row["psum"] - $row["tax_withheld"],
            "net_amount" => $row["final_amount"] - $row["tax_withheld"],
        );
    }

    return $merged_results;
}


function GetDetail($_pid, $sdate, $edate, $sale_person, $category, $db)
{
    $sql = "SELECT user.username,
                pm.project_name,
                pm.`client`,
                sum(CASE WHEN pp.kind = 1  THEN amount ELSE 0 END) psum,
                sum(CASE WHEN pp.kind = 0  THEN amount ELSE 0 END) dsum,
                COALESCE(pm.final_amount, 0) final_amount,
                COALESCE(pm.tax_withheld, 0) tax_withheld
            FROM   project_proof pp
            LEFT JOIN project_main pm
                    ON pp.project_id = pm.id
            LEFT JOIN user
                    ON pm.create_id = user.id
            WHERE pp.status = 1
            and pp.received_date > '" . $sdate . " 23:59:59' AND pp.received_date < '" . $edate . "'
            ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
            AND pm.id = " . $_pid;

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results = array(
            "username" => $row["username"],
            "project_name" => $row["project_name"],
            "client" => $row["client"],
            "dsum" => $row["dsum"],
            "psum" => $row["psum"],
            "final_amount" => $row["final_amount"],
            "tax_withheld" => $row["tax_withheld"],
            "ar" => $row["final_amount"] - $row["dsum"] - $row["psum"] - $row["tax_withheld"],
            "net_amount" => $row["final_amount"] - $row["tax_withheld"],
        );
    }

    return $merged_results;
}



function GetMonthCashReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db){
    $sql = "SELECT pm.id pid, user.username,
                        pm.project_name,
                        pm.catagory_id,
                        CASE pm.catagory_id  
                                WHEN 1 THEN 'Office System'
                                WHEN 2 THEN 'Lighting'
                                ELSE ''  
                            END   catagory,
                        pm.`client`,
                        pm.final_amount,
                        ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) -(SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '')  - (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '') ar,
                        pm.tax_withheld,    
                        ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) net_amount,
                        
                        (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date > '" . $PeriodStart . " 23:59:59' and p.received_date < '" . $PeriodEnd . "') dsum,
                        (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date > '" . $PeriodStart . " 23:59:59' and p.received_date < '" . $PeriodEnd . "') psum,

                        (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') total_dsum,
                        (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') total_psum

                        from project_proof pp
                    LEFT JOIN project_main pm
                            ON pp.project_id = pm.id
                    LEFT JOIN user
                            ON pm.create_id = user.id
                    WHERE pp.status = 1
                    and pm.created_at < '" . $PeriodEnd . "' 
                    and pp.received_date > '" . $PeriodStart . " 23:59:59' AND pp.received_date < '" . $PeriodEnd . "' ";

        if($sale_person != "")
        {
            $sql = $sql . " and user.username = '" . $sale_person . "' ";
        }

        if($category != "")
        {
            $sql = $sql . " and pm.catagory_id = " . $category . " ";
        }
                
        
        $sql = $sql . " UNION 

                    SELECT pm.id pid, user.username,
                    pm.project_name,
                    pm.catagory_id,
                    CASE pm.catagory_id  
                            WHEN 1 THEN 'Office System'
                            WHEN 2 THEN 'Lighting'
                            ELSE ''  
                        END   catagory,
                    pm.`client`,
                    pm.final_amount,
                    ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) -(SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '')  - (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '') ar,
                    pm.tax_withheld,    
                    ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) net_amount,
                    
                    (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date > '" . $PeriodStart . " 23:59:59' and p.received_date < '" . $PeriodEnd . "') dsum,
                    (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date > '" . $PeriodStart . " 23:59:59' and p.received_date < '" . $PeriodEnd . "') psum,
                    
                    (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') total_dsum,
                    (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') total_psum
                    
                FROM   project_main pm
                LEFT JOIN user
                        ON pm.create_id = user.id
                WHERE 
                ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) -(SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "')  - (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') > 0
                and pm.created_at < '" . $PeriodEnd . "' ";

            if($sale_person != "")
            {
                $sql = $sql . " and user.username = '" . $sale_person . "' ";
            }
    
            if($category != "")
            {
                $sql = $sql . " and pm.catagory_id = " . $category . " ";
            }
                    
            
            $sql = $sql . " 
                    ORDER BY username, catagory
                    ";

        

        $merged_results = array();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        $username = "";
        
        $sub_amount = 0;
        $sub_ar = 0;
        $sub_d = 0;
        $sub_p = 0;
        $total_d = 0;
        $total_p = 0;
        $sub_net_amount = 0;
        $sub_tax_withheld = 0;


        $l_catagory = [];
        $o_catagory = [];

        $subtotal  = 0;
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($username != $row['username'] && $username != "") {
                
                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
          
                $total_d = 0;
                $total_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
/*
                if($o_catagory == []){
                    array_push($o_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
    
                 if($l_catagory == []){
                    array_push($l_catagory, array(
                                            "username" => "",
                                            "project_name" => "",
                                            "client" => "",
                                            "dsum" => 0,
                                            "psum" => 0,
                                            "final_amount" => 0,
                                            "tax_withheld" => 0,
                                            "ar" => 0,
                                            "net_amount" => 0,));
                 }
*/
                foreach ($o_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
               
                    $total_d += $value['total_dsum'];
                    $total_p += $value['total_psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['final_amount'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    //$GLOBALS['total_dsum'] += $value['total_dsum'];
                    //$GLOBALS['total_psum'] += $value['total_psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }
    
                foreach ($l_catagory as &$value) {
                    $sub_amount += $value['final_amount'];
                    $sub_ar += $value['ar'];
                    $sub_d += $value['dsum'];
                    $sub_p += $value['psum'];
                
                    $total_d += $value['total_dsum'];
                    $total_p += $value['total_psum'];
                    $sub_net_amount += $value['net_amount'];
                    $sub_tax_withheld += $value['tax_withheld'];

                    $subtotal += $value['final_amount'];

                    $GLOBALS['total_amount'] += $value['final_amount'];
                    $GLOBALS['total_ar'] += $value['ar'];
                    $GLOBALS['total_d'] += $value['dsum'];
                    $GLOBALS['total_p'] += $value['psum'];
                    //$GLOBALS['total_dsum'] += $value['total_dsum'];
                    //$GLOBALS['total_psum'] += $value['total_psum'];
                    $GLOBALS['total_net_amount'] += $value['net_amount'];
                    $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
                }

                $merged_results[] = array(
                    "username" => $username,

                    "l_catagory" => $l_catagory,
                    "o_catagory" => $o_catagory,
                                 
                    "sub_amount" => $sub_amount,
                    "sub_ar" => $sub_ar,
                    "sub_d" => $sub_d,
                    "sub_p" => $sub_p,
                    
                    "total_d" => $total_d,
                    "total_p" => $total_p,
                    "sub_net_amount" => $sub_net_amount,
                    "sub_tax_withheld" => $sub_tax_withheld,

                    "subtotal" => $subtotal,
                
                );

                $l_catagory = [];
                $o_catagory = [];

                $sub_amount = 0;
                $sub_ar = 0;
                $sub_d = 0;
                $sub_p = 0;
          
                $total_d = 0;
                $total_p = 0;
                $sub_net_amount = 0;
                $sub_tax_withheld = 0;

                $subtotal = 0;
            }

            $username = $row['username'];

            if($row['catagory_id'] == 1)
                array_push($o_catagory, GetCashDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));
            if($row['catagory_id'] == 2)
                array_push($l_catagory, GetCashDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));

        }

        if ($username != "") {
            $sub_amount = 0;
            $sub_ar = 0;
            $sub_d = 0;
            $sub_p = 0;
          
            $total_d = 0;
            $total_p = 0;
            $sub_net_amount = 0;
            $sub_tax_withheld = 0;

            $subtotal = 0;
/*
            if($o_catagory == []){
                array_push($o_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }

             if($l_catagory == []){
                array_push($l_catagory, array(
                                        "username" => "",
                                        "project_name" => "",
                                        "client" => "",
                                        "dsum" => 0,
                                        "psum" => 0,
                                        "final_amount" => 0,
                                        "tax_withheld" => 0,
                                        "ar" => 0,
                                        "net_amount" => 0,));
             }
*/
            foreach ($o_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
              
                $total_d += $value['total_dsum'];
                $total_p += $value['total_psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['final_amount'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                //$GLOBALS['total_dsum'] += $value['total_dsum'];
                //$GLOBALS['total_psum'] += $value['total_psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            foreach ($l_catagory as &$value) {
                $sub_amount += $value['final_amount'];
                $sub_ar += $value['ar'];
                $sub_d += $value['dsum'];
                $sub_p += $value['psum'];
          
                $total_d += $value['total_dsum'];
                    $total_p += $value['total_psum'];
                $sub_net_amount += $value['net_amount'];
                $sub_tax_withheld += $value['tax_withheld'];

                $subtotal += $value['final_amount'];

                $GLOBALS['total_amount'] += $value['final_amount'];
                $GLOBALS['total_ar'] += $value['ar'];
                $GLOBALS['total_d'] += $value['dsum'];
                $GLOBALS['total_p'] += $value['psum'];
                //$GLOBALS['total_dsum'] += $value['total_dsum'];
                //    $GLOBALS['total_psum'] += $value['total_psum'];
                $GLOBALS['total_net_amount'] += $value['net_amount'];
                $GLOBALS['total_tax_withheld'] += $value['tax_withheld'];
            }

            $merged_results[] = array(
                "username" => $username,
                "l_catagory" => $l_catagory,
                "o_catagory" => $o_catagory,
  
                "sub_amount" => $sub_amount,
                "sub_ar" => $sub_ar,
                "sub_d" => $sub_d,
                "sub_p" => $sub_p,
                
                "total_d" => $total_d,
                    "total_p" => $total_p,
                "sub_net_amount" => $sub_net_amount,
                "sub_tax_withheld" => $sub_tax_withheld,

                "subtotal" => $subtotal,
            );

        }

        if(count($merged_results) > 0)
        {
            usort($merged_results, function ($item1, $item2) {
                return $item2['subtotal'] <=> $item1['subtotal'];
            });
        }

        return $merged_results;
}