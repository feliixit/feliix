<?php
// required headers
 error_reporting(0);
 
 require '../vendor/autoload.php';
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
 include_once 'config/conf.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');

$start_date = str_replace('-', '/', $start_date);
$end_date = str_replace('-', '/', $end_date);

$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');

// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $sql = "SELECT * from price_record where is_enabled = true";

            if($start_date != '') {
                $sql = $sql . " and paid_date >= '$start_date' ";
            }

            if($end_date != '') {
                $sql = $sql . " and paid_date <= '$end_date' ";
            }
            
            if($category != '') {
                $sql = $sql . " and category = '$category' ";
            }
            
            if($sub_category != '') {
                $sql = $sql . " and sub_category = '$sub_category' ";
            }

            $sql = $sql . " order by account , created_at  ";

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;
          
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


            $sheet->setCellValue('A1', 'Account');
            $sheet->setCellValue('B1', 'Date');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Related Account');
            $sheet->setCellValue('E1', 'Details');
            $sheet->setCellValue('F1', 'Photos');
            $sheet->setCellValue('G1', 'Payee');
            $sheet->setCellValue('H1', 'Paid/Received Date');
            $sheet->setCellValue('I1', 'Cash in');
            $sheet->setCellValue('J1', 'Cash out');
            $sheet->setCellValue('K1', 'Remarks');


            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, getAccount($row['account']));
                $sheet->setCellValue('B' . $i, getFormatDate($row['created_at']));
                if($row['sub_category'] != ''){
                    $sheet->setCellValue('C' . $i, $row['category'] .">>". $row['sub_category']);
                }else{
                    $sheet->setCellValue('C' . $i, $row['category']);
                }
                $sheet->setCellValue('D' . $i, $row['related_account']);
                $sheet->setCellValue('E' . $i, $row['details']);
                if($row['pic_url'] != '')
                {
                    $conf = new Conf();
                    $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
                    $sheet->setCellValue('F' . $i, 'Photo');
                    $sheet->getCellByColumnAndRow(5,$i)->getHyperlink()->setUrl($link);
                }
                else
                    $sheet->setCellValue('F' . $i, '');

                
                $sheet->setCellValue('G' . $i, $row['payee']);
                $sheet->setCellValue('H' . $i, $row['paid_date']);
                $sheet->setCellValue('I' . $i, $row['cash_in']);
                $sheet->setCellValue('J' . $i, $row['cash_out']);
                $sheet->setCellValue('K' . $i, $row['remarks']);

                $sheet->getStyle('A'. $i. ':' . 'K' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'K1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'K' . --$i)->applyFromArray($styleArray);

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

function getAccount($loc)
{
    $account = "";
    switch ($loc) {
        case 1:
            $account = "Office Petty Cash";
            break;
        case 2:
            $account = "Security Bank";
            break;
   
    }

    return $account;
}

function getFormatDate($date){
    return $date.substring(0,10);
}
?>