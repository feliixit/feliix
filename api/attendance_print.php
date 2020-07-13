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
 
// get database connection
$database = new Database();
$db = $database->getConnection();

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$apply_start = (isset($_POST['apply_start']) ?  $_POST['apply_start'] : '');
$apply_end = (isset($_POST['apply_end']) ?  $_POST['apply_end'] : '');

$apply_start = str_replace('-', '/', $apply_start);
$apply_end = str_replace('-', '/', $apply_end);

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $sql = "SELECT username, duty_date, duty_type, location, duty_time, `explain` FROM on_duty LEFT JOIN user ON on_duty.uid = user.id WHERE 1=1 ";

            if(!empty($apply_start)) {
                $sql = $sql . " and duty_date >= '$apply_start' ";
            }

            if(!empty($apply_end)) {
                $sql = $sql . " and duty_date <= '$apply_end' ";
            }

            $sql = $sql . " ORDER BY duty_date, uid, duty_type  ";

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


            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1', 'Employee');
            $sheet->setCellValue('C1', 'Duty Type');
            $sheet->setCellValue('D1', 'Duty Time');
            $sheet->setCellValue('E1', 'Location');
            $sheet->setCellValue('F1', 'explain');


            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['duty_date']);
                $sheet->setCellValue('B' . $i, $row['username']);
                $sheet->setCellValue('C' . $i, GetDutyType($row['duty_type']));
                $sheet->setCellValue('D' . $i, $row['duty_time']);
                $sheet->setCellValue('E' . $i, GetLocation($row['location']));
                $sheet->setCellValue('F' . $i, $row['explain']);


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

function GetLocation($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "Antel Office";
            break;
        case "T":
            $location = "Taiwan Office";
            break;
        case "B":
            $location = "Shangri-La Store";
            break;
        case "C":
            $location = "Caloocan Warehouse";
            break;
        case "D":
            $location = "Installation";
            break;
        case "E":
            $location = "Client Meeting";
            break;
            case "F":
                $location = "Others";
                break;
    }

    return $location;
}

function GetDutyType($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "On Duty";
            break;
        case "B":
            $location = "Off Duty";
            break;
   
    }

    return $location;
}
?>