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

            $total = 0.0;

            $i = 1;
            foreach($merged_results as $row)
            {
                // date
                $sheet->setCellValue('A'. $i, $row["date"]);
                // title 
                $i = $i + 1;
                $sheet->setCellValue('A'. $i, 'Customer Value Supervisor');
                $sheet->setCellValue('B'. $i, 'Monthly Quota');
                $sheet->setCellValue('C'. $i, 'Category');
                $sheet->setCellValue('D'. $i, 'Project Name');
                $sheet->setCellValue('E'. $i, 'Collected Payments');
                $sheet->setCellValue('F'. $i, 'Remaining Amount to Quota');
                $sheet->setCellValue('G'. $i, 'Remark');

                $sheet->getColumnDimension('A')->setWidth(32);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(22);
                $sheet->getColumnDimension('D')->setWidth(56);
                $sheet->getColumnDimension('E')->setWidth(24);
                $sheet->getColumnDimension('F')->setWidth(28);
                $sheet->getColumnDimension('G')->setWidth(32);
    
                $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);

                $total = 0;

                foreach($row['report'] as $rp)
                {
                    foreach ($rp['l_catagory'] as $low)
                    {
                        $i = $i + 1;
                        $sheet->setCellValue('A' . $i, $rp['username']);
                        $sheet->setCellValue('B' . $i, $row['date'] > '2025/01' ? '6,600,000.00' : '2,200,000.00');
                        $sheet->setCellValue('C' . $i, $low['catagory']);
                        $sheet->setCellValue('D' . $i, $low['project_name']);
                        $sheet->setCellValue('E' . $i, number_format((float)$low['amount'], 2, '.', ''));
                        //$sheet->setCellValue('F' . $i, number_format($quota - (float)$rp['subtotal'], 2, '.', ''));
                        //$sheet->setCellValue('G' . $i, $quota - (float)$rp['subtotal'] <= 0 ? 'Achieved Monthly Quota' : '');
                        $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
                    }

                    foreach ($rp['o_catagory'] as $oow)
                    {
                        $i = $i + 1;
                        $sheet->setCellValue('A' . $i, $rp['username']);
                        $sheet->setCellValue('B' . $i, $row['date'] > '2025/01' ? '6,600,000.00' : '2,200,000.00');
                        $sheet->setCellValue('C' . $i, 'Office Systems');
                        $sheet->setCellValue('D' . $i, $oow['project_name']);
                        $sheet->setCellValue('E' . $i, number_format((float)$oow['amount'], 2, '.', ''));
                        //$sheet->setCellValue('F' . $i, number_format($quota - (float)$rp['subtotal'], 2, '.', ''));
                        //$sheet->setCellValue('G' . $i, $quota - (float)$rp['subtotal'] <= 0 ? 'Achieved Monthly Quota' : '');
                        $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
                    }

                    $i = $i + 1;
                    $sheet->setCellValue('D' . $i, "Sub Total:");
                    $sheet->setCellValue('E' . $i, number_format((float)$rp['subtotal'], 2, '.', ''));
                    $sheet->setCellValue('F' . $i, number_format(($row['date'] > '2025/01' ? 6600000 : 2200000) - (float)$rp['subtotal'], 2, '.', ''));
                    $sheet->setCellValue('G' . $i, ($row['date'] > '2025/01' ? 6600000 : 2200000) - (float)$rp['subtotal'] <= 0 ? 'Achieved Monthly Quota' : '');

                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                    $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);

                    $total = $total + (float)$rp['subtotal'];
                }

                $i = $i + 1;
                $sheet->setCellValue('D' . $i, "Total:");
                $sheet->setCellValue('E' . $i, number_format($total, 2, '.', ''));
              
                $sheet->getStyle('A' . $i . ':' . 'J' . $i)->getFont()->setBold(true);
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);

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
   
    return array("date" => date("Y/m",strtotime($strDate . "first day of 0 month")), "report" => $report1);
}

function GetOneMonth($strDate, $sale_person, $category, $db)
{
    $PeriodStart = date("Y-m-d",strtotime($strDate . "last day of -2 month"));
    $PeriodEnd = date("Y-m-d",strtotime($strDate . "first day of 0 month"));

    $report1 = GetMonthSaleReport($PeriodStart, $PeriodEnd, $sale_person, $category, $db);
   

    return array("date" => date("Y/m",strtotime($strDate . "first day of -1 month")), "report" => $report1);
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

?>