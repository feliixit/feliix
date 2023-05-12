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

$strDate = (isset($_POST['d']) ?  $_POST['d'] : '');
$strEDate = (isset($_POST['e']) ?  $_POST['e'] : '');
$sale_person = (isset($_POST['p']) ?  $_POST['p'] : '');
$sale_person = urldecode($sale_person);
$category = (isset($_POST['c']) ?  $_POST['c'] : '');
$archive = (isset($_GET['a']) ? $_GET['a'] : "");

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
            // date
            //$sheet->setCellValue('A'. $i, $row["date"]);
            // title 
            $i = $i + 1;
            $sheet->setCellValue('A'. $i, 'Sales Person');
            $sheet->setCellValue('B'. $i, 'Classification');
            $sheet->setCellValue('C'. $i, 'Category');
            $sheet->setCellValue('D'. $i, 'Project Name');
            $sheet->setCellValue('E'. $i, 'Created Time');
            $sheet->setCellValue('F'. $i, 'Est. Closing Prob.');
            $sheet->setCellValue('G'. $i, 'Amount');

            $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
            
            foreach($merged_results as $row)
            {
                foreach($row['yet_lighting_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Yet Close-Deal');
                    $sheet->setCellValue('C' . $i, 'Lighting');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }
                foreach($row['yet_office_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Yet Close-Deal');
                    $sheet->setCellValue('C' . $i, 'Office');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }

                foreach($row['close_lighting_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Close-Deal');
                    $sheet->setCellValue('C' . $i, 'Lighting');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }
                foreach($row['close_office_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Close-Deal');
                    $sheet->setCellValue('C' . $i, 'Office');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }

                foreach($row['disapprove_lighting_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Disapproved');
                    $sheet->setCellValue('C' . $i, 'Lighting');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }
                foreach($row['disapprove_office_array'] as $rp)
                {
                    $i = $i + 1;
                    $sheet->setCellValue('A' . $i, $rp['username']);
                    $sheet->setCellValue('B' . $i, 'Disapproved');
                    $sheet->setCellValue('C' . $i, 'Office');
                    $sheet->setCellValue('D' . $i, $rp['project_name']);
                    $sheet->setCellValue('E' . $i, $rp['created_at']);
                    $sheet->setCellValue('F' . $i, $rp['estimate_close_prob']);
                    $sheet->setCellValue('G' . $i, number_format((float)$rp['final_amount'], 2, '.', ''));
                    $sheet->getStyle('A' . $i . ':' . 'G' . $i)->getFont()->setBold(true);
                }

                $i = $i + 1;

                $sheet->getStyle('A' . $i . ':' . 'J' . $i)->getFont()->setBold(true);
                //$sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);
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