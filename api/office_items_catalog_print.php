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

$parent = (isset($_POST['parent']) ?  $_POST['parent'] : '');

// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$lv1 = "";
$lv2 = "";
$lv3 = "";
$lv4 = "";

// seperate parent into 4 levels
if($parent != "")
{
    $lv1 = substr($parent, 0, 2);
    $lv2 = substr($parent, 2, 2);
    $lv3 = substr($parent, 4, 2);
    $lv4 = substr($parent, 6, 2);
}

// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        http_response_code(200);

        $query = "SELECT m.code code1, m.category cat1, s.code code2, s.category cat2, b.code code3, b.category cat3, d.code code4, d.category cat4, IFNULL(q.qty, 0) qty, IFNULL(q.reserve_qty, 0) reserve_qty
            FROM office_items_main_category m
            left join office_items_sub_category s on m.code = s.parent_code ";
        if($lv1 != "")
        {
            $query = $query . " and m.code = '" . $lv1 . "' ";
        }
        $query = $query . " left join office_items_brand b on CONCAT(m.code, s.code) = b.parent_code ";
        if($lv2 != "")
        {
            $query = $query . " and s.code = '" . $lv2 . "' ";
        }
        $query = $query . " left join office_items_description d on CONCAT(m.code,s.code,b.code) = d.parent_code ";
        if($lv3 != "")
        {
            $query = $query . " and b.code = '" . $lv3 . "' ";
        }
        $query = $query . " LEFT JOIN office_items_stock q ON q.code = concat(d.parent_code, d.code) ";
        $query = $query . "   where m.status <> -1 and s.status <> -1 and b.status <> -1 and d.status <> -1 ";

        if($lv4 != "")
        {
            $query = $query . " and d.code = '" . $lv4 . "' ";
        }
        
        $query = $query . " order by m.sn, s.sn, b.sn, d.sn ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }
        
            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),

                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
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


            $sheet->setCellValue('A1', 'MAIN CATEGORY');
            $sheet->mergeCells('A1:B1');
            $sheet->setCellValue('C1', 'SUB CATEGORY');
            $sheet->mergeCells('C1:D1');
            $sheet->setCellValue('E1', 'BRAND');
            $sheet->mergeCells('E1:F1');
            $sheet->setCellValue('G1', 'DESCRIPTION');
            $sheet->mergeCells('G1:H1');

            $sheet->setCellValue('I1', 'CODE');
            $sheet->setCellValue('J1', 'QTY');
        
            $i = 2;

            foreach ($merged_results as $measure)
            {
                    $sheet->setCellValue('A' . $i, $measure["code1"]);
                    $sheet->setCellValue('B' . $i, $measure["cat1"]);
                    $sheet->setCellValue('C' . $i, $measure["code2"]);
                    $sheet->setCellValue('D' . $i, $measure["cat2"]);
                    $sheet->setCellValue('E' . $i, $measure["code3"]);
                    $sheet->setCellValue('F' . $i, $measure["cat3"]);
                    $sheet->setCellValue('G' . $i, $measure["code4"]);
                    $sheet->setCellValue('H' . $i, $measure["cat4"]);
                    $sheet->setCellValue('I' . $i, $measure["code1"] . $measure["code2"] . $measure["code3"] . $measure["code4"]);
                    $sheet->setCellValue('J' . $i, "");

                    $sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);
                    $i++;
            
            }
            
            $sheet->getStyle('A1:' . 'J1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'J' . --$i)->applyFromArray($styleArray);
            
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




?>