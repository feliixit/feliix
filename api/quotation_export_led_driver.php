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

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$led_driver = (isset($_POST['led_driver']) ?  $_POST['led_driver'] : '[]');
$project_name = (isset($_POST['project_name']) ?  $_POST['project_name'] : '');
$led_array = json_decode($led_driver, true);

$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            // response in json format
            $bold_border_style = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),
            );

            $center_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );

            $left_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );

            $title_style = array(
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );

            $title_gray_background = array(
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ),
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => array(
                        'rgb' => 'ffff00'
                    )
                ),
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

            $sheet->setCellValue('A'. $i, "PROJECT NAME: " . $project_name);
            $sheet->getStyle('A'. $i. ':' . 'A' . $i)->applyFromArray($title_style);

            // merge cells
            $sheet->mergeCells('A'. $i. ':' . 'I' . $i);
            
            $i = $i + 1;

            $sheet->setCellValue('A'. $i, "ITEM NO.");
            $sheet->setCellValue('B'. $i, "LOCATION (AREA)");
            $sheet->setCellValue('C'. $i, "QUANTITY");
            $sheet->setCellValue('D'. $i, "LED STRIP (WATTAGE)");
            $sheet->setCellValue('E'. $i, "TOTAL\n(LENGTH IN METERS)");

            $sheet->getStyle('E'. $i)->getAlignment()->setWrapText(true);
            
            $sheet->setCellValue('F'. $i, "TOTAL (WATTAGE)");
            $sheet->setCellValue('G'. $i, "TOTAL LED DRIVER WATTAGE ");
            $sheet->setCellValue('H'. $i, "LED DRIVER WATTAGE AVAILABLE\n(ITEM CODE)");

            $sheet->getStyle('H'. $i)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('I'. $i, "LED DRIVER QTY");
            $sheet->getStyle('A'. $i. ':' . 'I' . $i)->applyFromArray($title_gray_background);
            $i = $i + 1;

            $sheet->getColumnDimensionByColumn(2)->setWidth(20);
            $sheet->getColumnDimensionByColumn(8)->setWidth(30);

            foreach($led_array as $data)
            {
                $product_code = '';
                $wattage = '';
                foreach($data['products'] as $product)
                {
                    if($product['id'] == $data['field'])
                    {
                        $product_code = $product['code'];
                        $wattage = $product['wattage'];
                        break;
                    }
                }

                $sheet->setCellValue('A'. $i, $data['no']);
                $sheet->setCellValue('B'. $i, $data['area']);
                $sheet->setCellValue('C'. $i, $data['qty']);
                $sheet->setCellValue('D'. $i, " " . number_format($data['watt'], 2, '.', ','));
                $sheet->setCellValue('E'. $i, " " . number_format($data['length'], 2, '.', ','));
                $sheet->setCellValue('F'. $i, " " . number_format($data['qty'] * $data['watt'] * $data['length'], 2, '.', ','));
                $sheet->setCellValue('G'. $i, " " . number_format($data['qty'] * $data['watt'] * $data['length'] * 1.2, 2, '.', ','));
                $sheet->setCellValue('H'. $i, $data['tag'] . "\n" . $wattage != "" ? $wattage . ' - ' . $product_code : $product_code);
                $sheet->getStyle('H'. $i)->getAlignment()->setWrapText(true);
                $sheet->setCellValue('I'. $i, $data['driver']);
                

                $sheet->getStyle('A'. $i. ':' . 'I' . $i)->applyFromArray($styleArray);
                $sheet->getStyle('A'. $i. ':' . 'A' . $i)->applyFromArray($center_style);
                $sheet->getStyle('B'. $i. ':' . 'B' . $i)->applyFromArray($left_style);
                $sheet->getStyle('C'. $i. ':' . 'I' . $i)->applyFromArray($center_style);

                $i = $i + 1;
            }

            $i = $i - 1;

            $sheet->getStyle('A2'. ':' . 'I' . $i)->applyFromArray($bold_border_style);


            $i = $i + 3;
            $sheet->setCellValue('F'. $i, "WATTAGE");
            $sheet->setCellValue('G'. $i, "ID");
            $sheet->setCellValue('H'. $i, "CODE");
            $sheet->setCellValue('I'. $i, "QTY");
            $sheet->getStyle('F'. $i. ':' . 'I' . $i)->applyFromArray($title_gray_background);
            $i = $i + 1;
            foreach($led_array as $data)
            {
                $product_code = '';
                $wattage = '';
                $id = '';
                foreach($data['products'] as $product)
                {
                    if($product['id'] == $data['field'])
                    {
                        $product_code = $product['code'];
                        $wattage = $product['wattage'];
                        $id = $product['id'];
                        break;
                    }
                }

                $sheet->setCellValue('F'. $i, $wattage);
                $sheet->setCellValue('G'. $i, $id);
                $sheet->setCellValue('H'. $i, $product_code);
                $sheet->setCellValue('I'. $i, $data['driver']);
                
                $i = $i + 1;
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



?>