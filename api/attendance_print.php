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

$apply_start = (isset($_POST['apply_start']) ?  $_POST['apply_start'] : '');
$apply_end = (isset($_POST['apply_end']) ?  $_POST['apply_end'] : '');
$department = (isset($_POST['department']) ?  $_POST['department'] : '');

$apply_start = str_replace('-', '/', $apply_start);
$apply_end = str_replace('-', '/', $apply_end);

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

            $sql = "SELECT username, duty_date, duty_type, location, duty_time, `explain`, on_duty.pic_url, remark, pos_lat, pos_lng, ud.department, ut.title FROM on_duty 
            LEFT JOIN user ON on_duty.uid = user.id 
            LEFT JOIN user_department ud ON user.apartment_id = ud.id 
            LEFT JOIN user_title ut ON user.title_id = ut.id 
            WHERE 1=1 ";

            if(!empty($apply_start)) {
                $sql = $sql . " and duty_date >= '$apply_start' ";
            }

            if(!empty($apply_end)) {
                $sql = $sql . " and duty_date <= '$apply_end' ";
            }

            if(!empty($department)) {
                $sql = $sql . " and ud.id = $department ";
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
            $sheet->setCellValue('C1', 'Department');
            $sheet->setCellValue('D1', 'Position');
            $sheet->setCellValue('E1', 'Duty Type');
            $sheet->setCellValue('F1', 'Duty Time');
            $sheet->setCellValue('G1', 'Location');
            $sheet->setCellValue('H1', 'explain');
            $sheet->setCellValue('I1', 'Photo');
            $sheet->setCellValue('J1', 'GPS');
            $sheet->setCellValue('K1', 'Remark');

            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['duty_date']);
                $sheet->setCellValue('B' . $i, $row['username']);
                $sheet->setCellValue('C' . $i, $row['department']);
                $sheet->setCellValue('D' . $i, $row['title']);
                $sheet->setCellValue('E' . $i, GetDutyType($row['duty_type']));
                $sheet->setCellValue('F' . $i, $row['duty_time']);
                $sheet->setCellValue('G' . $i, GetLocation($row['location']));
                $sheet->setCellValue('H' . $i, $row['explain']);

                if($row['pic_url'] != '')
                {
                    $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
                    $sheet->setCellValue('I' . $i, 'Photo');
                    $sheet->getCellByColumnAndRow(7,$i)->getHyperlink()->setUrl($link);
                }
                else
                    $sheet->setCellValue('I' . $i, '');

                 
                $link = "http://www.google.com/maps/place/" . $row['pos_lat'] . ',' . $row['pos_lng'];
                $sheet->setCellValue('J' . $i, '(' . $row['pos_lat'] . ',' . $row['pos_lng'] . ')');
                $sheet->getCellByColumnAndRow(8,$i)->getHyperlink()->setUrl($link);
                    

                //$sheet->setCellValue('H' . $i, $row['pos_lat'] . " - " . $row['pos_lng']);
                
                $sheet->setCellValue('K' . $i, $row['remark']);

                $sheet->getStyle('A'. $i. ':' . 'K' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'K1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'K' . --$i)->applyFromArray($styleArray);


            // page 2
            $merged_results = array();

            $sql = "SELECT t4.username, t4.duty_date, t4.duty_type, t4.duty_time, t4.location, t4.explain, t4.remark, t4.pic_url, t4.pos_lat, t4.pos_lng, t4.department, t4.title
                    FROM 
                    (SELECT *, ROW_NUMBER() OVER (PARTITION BY username, t3.duty_date, t3.duty_type  ORDER BY t3.id) AS rank1
                    , ROW_NUMBER() OVER (PARTITION BY username, t3.duty_date, t3.duty_type  ORDER BY t3.id DESC) AS rank2
                        FROM
                    (SELECT t1.id, t2.username, t1.duty_date, t1.duty_type, t1.duty_time, t1.location, t1.explain, t1.remark, t1.pic_url, t1.pos_lat, t1.pos_lng, ud.department, ut.title 

             FROM feliix.on_duty AS t1 LEFT JOIN feliix.user AS t2 ON t1.uid = t2.id LEFT JOIN user_department ud ON t2.apartment_id = ud.id 
            LEFT JOIN user_title ut ON t2.title_id = ut.id  WHERE 1=1 "; 

            if(!empty($apply_start)) {
                $sql = $sql . " and t1.duty_date >= '$apply_start' ";
            }

            if(!empty($apply_end)) {
                $sql = $sql . " and t1.duty_date <= '$apply_end' ";
            }
             
             if(!empty($department)) {
                $sql = $sql . " AND t2.apartment_id = $department ) AS t3
                    ) AS t4
                    WHERE (t4.duty_type='A' and t4.rank1=1) OR (t4.duty_type='B' and t4.rank2=1) 
                    ORDER BY t4.username, t4.duty_date, t4.duty_type";
             }
             else
             {
                $sql = $sql . "
                       ) AS t3
                        ) AS t4
                        WHERE (t4.duty_type='A' and t4.rank1=1) OR (t4.duty_type='B' and t4.rank2=1) 
                        ORDER BY t4.username, t4.duty_date, t4.duty_type";
             }


            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;

            $spreadsheet->createSheet();

            $spreadsheet->setActiveSheetIndex(1);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Sheet 2");


            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1', 'Employee');
            $sheet->setCellValue('C1', 'Department');
            $sheet->setCellValue('D1', 'Position');
            $sheet->setCellValue('E1', 'Duty Type');
            $sheet->setCellValue('F1', 'Duty Time');
            $sheet->setCellValue('G1', 'Location');
            $sheet->setCellValue('H1', 'explain');
            $sheet->setCellValue('I1', 'Photo');
            $sheet->setCellValue('J1', 'GPS');
            $sheet->setCellValue('K1', 'Remark');




            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['duty_date']);
                $sheet->setCellValue('B' . $i, $row['username']);
                $sheet->setCellValue('C' . $i, $row['department']);
                $sheet->setCellValue('D' . $i, $row['title']);
                $sheet->setCellValue('E' . $i, GetDutyType($row['duty_type']));
                $sheet->setCellValue('F' . $i, $row['duty_time']);
                $sheet->setCellValue('G' . $i, GetLocation($row['location']));
                $sheet->setCellValue('H' . $i, $row['explain']);

                if($row['pic_url'] != '')
                {
                    $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
                    $sheet->setCellValue('I' . $i, 'Photo');
                    $sheet->getCellByColumnAndRow(7,$i)->getHyperlink()->setUrl($link);
                }
                else
                    $sheet->setCellValue('I' . $i, '');

                    $link = "http://www.google.com/maps/place/" . $row['pos_lat'] . ',' . $row['pos_lng'];
                    $sheet->setCellValue('J' . $i, '(' . $row['pos_lat'] . ',' . $row['pos_lng'] . ')');
                    $sheet->getCellByColumnAndRow(8,$i)->getHyperlink()->setUrl($link);
                    
                $sheet->setCellValue('K' . $i, $row['remark']);


                $sheet->getStyle('A'. $i. ':' . 'K' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'K1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'K' . --$i)->applyFromArray($styleArray);


            // page 3
            $merged_results = array();

            $sql = "SELECT t4.username, t4.duty_date, t4.duty_type, t4.duty_time, t4.location, t4.explain, t4.remark, t4.pic_url, t4.pos_lat, t4.pos_lng, t4.department, t4.title 
                FROM 
                    (SELECT *, ROW_NUMBER() OVER (PARTITION BY username, t3.duty_date ORDER BY t3.id) AS rank1
                             , ROW_NUMBER() OVER (PARTITION BY username, t3.duty_date ORDER BY t3.id DESC) AS rank2
                     FROM
                        (SELECT t1.id, t2.username, t1.duty_date, t1.duty_type, t1.duty_time, t1.location, t1.explain, t1.remark, t1.pic_url, t1.pos_lat, t1.pos_lng, ud.department, ut.title 
                         FROM feliix.on_duty AS t1 LEFT JOIN feliix.user AS t2 ON t1.uid = t2.id LEFT JOIN user_department ud ON user.apartment_id = ud.id 
                        LEFT JOIN user_title ut ON user.title_id = ut.id  WHERE 1=1 ";

            if(!empty($apply_start)) {
                $sql = $sql . " and t1.duty_date >= '$apply_start' ";
            }

            if(!empty($apply_end)) {
                $sql = $sql . " and t1.duty_date <= '$apply_end' ";
            }
                                       
            if(!empty($department)) {
            $sql = $sql . " AND t2.apartment_id = $department ) AS t3
                    ) AS t4
                WHERE (t4.rank1=1) OR (t4.rank2=1) 
                ORDER BY t4.username, t4.duty_date, t4.id";
            }
            else
            {
            $sql = $sql . "
                    ) AS t3
                    ) AS t4
                WHERE (t4.rank1=1) OR (t4.rank2=1) 
                ORDER BY t4.username, t4.duty_date, t4.id";
            }

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;

            $spreadsheet->createSheet();

            $spreadsheet->setActiveSheetIndex(2);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Sheet 3");


            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1', 'Employee');
            $sheet->setCellValue('C1', 'Department');
            $sheet->setCellValue('D1', 'Position');
            $sheet->setCellValue('E1', 'Duty Type');
            $sheet->setCellValue('F1', 'Duty Time');
            $sheet->setCellValue('G1', 'Location');
            $sheet->setCellValue('H1', 'explain');
            $sheet->setCellValue('I1', 'Photo');
            $sheet->setCellValue('J1', 'GPS');
            $sheet->setCellValue('K1', 'Remark');


            $i = 2;
            foreach($merged_results as $row)
            {
                $sheet->setCellValue('A' . $i, $row['duty_date']);
                $sheet->setCellValue('B' . $i, $row['username']);
                $sheet->setCellValue('C' . $i, $row['department']);
                $sheet->setCellValue('D' . $i, $row['title']);
                $sheet->setCellValue('E' . $i, GetDutyType($row['duty_type']));
                $sheet->setCellValue('F' . $i, $row['duty_time']);
                $sheet->setCellValue('G' . $i, GetLocation($row['location']));
                $sheet->setCellValue('H' . $i, $row['explain']);

                if($row['pic_url'] != '')
                {
                    $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
                    $sheet->setCellValue('I' . $i, 'Photo');
                    $sheet->getCellByColumnAndRow(7,$i)->getHyperlink()->setUrl($link);
                }
                else
                    $sheet->setCellValue('I' . $i, '');
                
                    $link = "http://www.google.com/maps/place/" . $row['pos_lat'] . ',' . $row['pos_lng'];
                    $sheet->setCellValue('J' . $i, '(' . $row['pos_lat'] . ',' . $row['pos_lng'] . ')');
                    $sheet->getCellByColumnAndRow(8,$i)->getHyperlink()->setUrl($link);

                $sheet->setCellValue('K' . $i, $row['remark']);


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