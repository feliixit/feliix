<?php
ob_start();
// required headers
 error_reporting(0);
 
 require '../vendor/autoload.php';
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/conf.php';

use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$total_amount = 0;
$total_ar = 0;
$total_d = 0;
$total_p = 0;
$total_dsum = 0;
$total_psum = 0;
$total_net_amount = 0;
$total_tax_withheld = 0;

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$strDate = (isset($_POST['d']) ?  $_POST['d'] : '');
$strEDate = (isset($_POST['e']) ?  $_POST['e'] : '');
$sale_person = (isset($_POST['p']) ?  $_POST['p'] : '');
$sale_person = urldecode($sale_person);
$category = (isset($_POST['c']) ?  $_POST['c'] : '');


$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            if($strDate == '')
            {
                $strDate = date('Y-m-d');

                for($i = 0; $i < 2; $i++)
                {
                    $strDate = date("Y-m-d",strtotime($strDate . "first day of " . $i . " month"));
                    $merged_results[] =  GetOneMonth($strDate, $sale_person, $category, $db);

                    $total_amount = 0;
                    $total_ar = 0;
                    $total_d = 0;
                    $total_p = 0;
                    $total_dsum = 0;
                $total_psum = 0;
                    $total_net_amount = 0;
                    $total_tax_withheld = 0;
                }
            }

            if($strDate != '' && $strEDate != "")
            {
                $interval = DateInterval::createFromDateString('1 month');
                $period = new DatePeriod(new DateTime($strDate), $interval, new DateTime(date("Y-m-d",strtotime($strEDate . "first day of 1 month"))));

                foreach ($period as $dt) {
                    $strDate = $dt->format("Y-m-d");
                    $merged_results[] =  GetCurrentMonth($strDate, $sale_person, $category, $db);

                    $total_amount = 0;
                    $total_ar = 0;
                    $total_d = 0;
                    $total_p = 0;
                    $total_dsum = 0;
                $total_psum = 0;
                    $total_net_amount = 0;
                    $total_tax_withheld = 0;

                }
            }
          
            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('PhpOffice')
                    ->setLastModifiedBy('PhpOffice')
                    ->setTitle('Office 2007 XLSX Test Document')
                    ->setSubject('Office 2007 XLSX Test Document')
                    ->setDescription('PhpOffice')
                    ->setKeywords('PhpOffice')
                    ->setCategory('PhpOffice');

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Sheet 1");
            $sheet->getMergeCells();

            $i = 1;
            foreach($merged_results as $row)
            {
                // date
                $sheet->setCellValue('A'. $i, $row["date"]);
                // title 
                $i = $i + 1;
                $sheet->setCellValue('A'. $i, 'Sales Person');
                $sheet->setCellValue('B'. $i, 'Category');
                $sheet->setCellValue('C'. $i, 'Customer Name');
                $sheet->setCellValue('D'. $i, 'Project Name');
                $sheet->setCellValue('E'. $i, 'Created Time');
                $sheet->setCellValue('F'. $i, 'Amount');
                $sheet->setCellValue('G'. $i, 'A/R');
                $sheet->setCellValue('H'. $i, 'Down Payment');
                $sheet->setCellValue('I'. $i, 'Full Payment');
                $sheet->setCellValue('J'. $i, 'Total Down Payment');
                $sheet->setCellValue('K'. $i, 'Total Full Payment');
                $sheet->setCellValue('L'. $i, 'Net Amount');
                $sheet->setCellValue('M'. $i, 'Tax Withheld');
                $sheet->getStyle('A' . $i . ':' . 'M' . $i)->getFont()->setBold(true);

                foreach($row['report'] as $rp)
                {
                    foreach ($rp['l_catagory'] as $low)
                    {
                        $i = $i + 1;
                        $sheet->setCellValue('A' . $i, $low['username']);
                        $sheet->setCellValue('B' . $i, 'Ligthing');
                        $sheet->setCellValue('C' . $i, $low['client']);
                        $sheet->setCellValue('D' . $i, $low['project_name']);
                        $sheet->setCellValue('E' . $i, $low['created_at']);
                        $sheet->setCellValue('F' . $i, number_format((float)$low['final_amount'], 2, '.', ''));
                        $sheet->setCellValue('G' . $i, number_format((float)$low['ar'], 2, '.', ''));
                        $sheet->setCellValue('H' . $i, number_format((float)$low['dsum'], 2, '.', ''));
                        $sheet->setCellValue('I' . $i, number_format((float)$low['psum'], 2, '.', ''));
                        $sheet->setCellValue('J' . $i, number_format((float)$low['total_dsum'], 2, '.', ''));
                        $sheet->setCellValue('K' . $i, number_format((float)$low['total_psum'], 2, '.', ''));
                        $sheet->setCellValue('L' . $i, number_format((float)$low['net_amount'], 2, '.', ''));
                        $sheet->setCellValue('M' . $i, number_format((float)$low['tax_withheld'], 2, '.', ''));
                    }

                    foreach ($rp['o_catagory'] as $oow)
                    {
                        $i = $i + 1;
                        $sheet->setCellValue('A' . $i, $oow['username']);
                        $sheet->setCellValue('B' . $i, 'Office Systems');
                        $sheet->setCellValue('C' . $i, $oow['client']);
                        $sheet->setCellValue('D' . $i, $oow['project_name']);
                        $sheet->setCellValue('E' . $i, $low['created_at']);
                        $sheet->setCellValue('F' . $i, number_format((float)$oow['final_amount'], 2, '.', ''));
                        $sheet->setCellValue('G' . $i, number_format((float)$oow['ar'], 2, '.', ''));
                        $sheet->setCellValue('H' . $i, number_format((float)$oow['dsum'], 2, '.', ''));
                        $sheet->setCellValue('I' . $i, number_format((float)$oow['psum'], 2, '.', ''));
                        $sheet->setCellValue('J' . $i, number_format((float)$oow['total_dsum'], 2, '.', ''));
                        $sheet->setCellValue('K' . $i, number_format((float)$oow['total_psum'], 2, '.', ''));
                        $sheet->setCellValue('L' . $i, number_format((float)$oow['net_amount'], 2, '.', ''));
                        $sheet->setCellValue('M' . $i, number_format((float)$oow['tax_withheld'], 2, '.', ''));
                    }

                    $i = $i + 1;
                    $sheet->setCellValue('E' . $i, "Sub Total:");
                    $sheet->setCellValue('F' . $i, number_format((float)$rp['sub_amount'], 2, '.', ''));
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['sub_ar'], 2, '.', ''));
                    $sheet->setCellValue('H' . $i, number_format((float)$rp['sub_d'], 2, '.', ''));
                    $sheet->setCellValue('I' . $i, number_format((float)$rp['sub_p'], 2, '.', ''));
                    $sheet->setCellValue('J' . $i, number_format((float)$rp['total_d'], 2, '.', ''));
                    $sheet->setCellValue('K' . $i, number_format((float)$rp['total_p'], 2, '.', ''));
                    $sheet->setCellValue('L' . $i, number_format((float)$rp['sub_net_amount'], 2, '.', ''));
                    $sheet->setCellValue('M' . $i, number_format((float)$rp['sub_tax_withheld'], 2, '.', ''));

                    $sheet->getStyle('A' . $i . ':' . 'M' . $i)->getFont()->setBold(true);
                }

                $i = $i + 1;
                $sheet->setCellValue('E' . $i, "Total:");
                $sheet->setCellValue('F' . $i, number_format((float)$row['total']['total_amount'], 2, '.', ''));
                $sheet->setCellValue('G' . $i, number_format((float)$row['total']['total_ar'], 2, '.', ''));
                $sheet->setCellValue('H' . $i, number_format((float)$row['total']['total_d'], 2, '.', ''));
                $sheet->setCellValue('I' . $i, number_format((float)$row['total']['total_p'], 2, '.', ''));
                $sheet->setCellValue('J' . $i, number_format((float)$row['total']['total_dsum'], 2, '.', ''));
                $sheet->setCellValue('K' . $i, number_format((float)$row['total']['total_psum'], 2, '.', ''));
                $sheet->setCellValue('L' . $i, number_format((float)$row['total']['total_net_amount'], 2, '.', ''));
                $sheet->setCellValue('M' . $i, number_format((float)$row['total']['total_tax_withheld'], 2, '.', ''));

                $sheet->getStyle('A' . $i . ':' . 'M' . $i)->getFont()->setBold(true);
                //$sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);

                $i = $i + 2;
            }

            
            // $sheet->getStyle('A1:' . 'J' . --$i)->applyFromArray($styleArray);

            ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file.xlsx"');

            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');


            exit;
    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}

function GetCurrentMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -1 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 1 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $total1 = array(
        "total_amount" => $GLOBALS["total_amount"],
        "total_ar" => $GLOBALS["total_ar"],
        "total_d" => $GLOBALS["total_d"],
        "total_p" => $GLOBALS["total_p"],
        "total_dsum" => $GLOBALS["total_dsum"],
        "total_psum" => $GLOBALS["total_psum"],
        "total_net_amount" => $GLOBALS["total_net_amount"],
        "total_tax_withheld" => $GLOBALS["total_tax_withheld"],
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "report" => $report1, "total" => $total1,);
}

function GetOneMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -2 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 0 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
    $total1 = array(
        "total_amount" => $GLOBALS["total_amount"],
        "total_ar" => $GLOBALS["total_ar"],
        "total_d" => $GLOBALS["total_d"],
        "total_p" => $GLOBALS["total_p"],
        "total_dsum" => $GLOBALS["total_dsum"],
        "total_psum" => $GLOBALS["total_psum"],
        "total_net_amount" => $GLOBALS["total_net_amount"],
        "total_tax_withheld" => $GLOBALS["total_tax_withheld"],
    );

    return array("date" => date("Y/m",strtotime($strDate . "first day of -1 month")), "report" => $report1, "total" => $total1,);
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
                ifnull(pm.final_amount, 0) - IFNULL(pm.tax_withheld, 0) -(SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 1 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "')  - (SELECT IFNULL(SUM(amount), 0) FROM project_proof p WHERE p.project_id = pm.id AND p.status <> -1 AND p.kind = 0 AND p.received_date <> '' AND p.received_date < '" . $PeriodEnd . "') > 0";

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
                    $GLOBALS['total_dsum'] += $value['total_dsum'];
                    $GLOBALS['total_psum'] += $value['total_psum'];
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
                    $GLOBALS['total_dsum'] += $value['total_dsum'];
                    $GLOBALS['total_psum'] += $value['total_psum'];
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
                array_push($o_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));
            if($row['catagory_id'] == 2)
                array_push($l_catagory, GetDetail($row['pid'], $PeriodStart, $PeriodEnd, $sale_person, $category, $db));

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
                $GLOBALS['total_dsum'] += $value['total_dsum'];
                $GLOBALS['total_psum'] += $value['total_psum'];
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
                $GLOBALS['total_dsum'] += $value['total_dsum'];
                    $GLOBALS['total_psum'] += $value['total_psum'];
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
                return $item2['sub_amount'] <=> $item1['sub_amount'];
            });
        }

        return $merged_results;
}

function GetDetail($_pid, $sdate, $edate, $sale_person, $category, $db)
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


?>