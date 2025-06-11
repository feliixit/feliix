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


$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

    $gneral_requirement = GetGeneralRequirement($id, $db);     
    $installation = GetInstallation($id, $db);

    $skip_group = array();
    // count for row span
    foreach($installation['block'] as $key => $value)
    {
        if($installation['block'][$key]['group'] == '')
        {
            $installation['block'][$key]['gp_cnt'] = 1;
            $installation['block'][$key]['gp_cost'] = $installation['block'][$key]['labor_price'];
            $installation['block'][$key]['gp_total'] = $installation['block'][$key]['total'];

            if($installation['block'][$key]['total'] == '' || $installation['block'][$key]['total'] == 0)
                $installation['block'][$key]['gp_total'] = '';

        }
        else if(array_key_exists($installation['block'][$key]['group'], $skip_group))
        {
            $installation['block'][$key]['gp_cnt'] = 0;
        }
        else
        {
            $group_ary = GetSameGroupOfItem($installation['block'], $installation['block'][$key]['group']);
            $skip_group[$installation['block'][$key]['group']] = 1;

            $installation['block'][$key]['gp_cnt'] = count($group_ary);
            $gp_cost = 0;
            $gp_total = 0;
            for($i = 0; $i < count($group_ary); $i++)
            {
                if(is_numeric($group_ary[$i]['labor_price']))
                    $gp_cost += $group_ary[$i]['labor_price'];
                if(is_numeric($group_ary[$i]['total']))
                    $gp_total += $group_ary[$i]['total'];
            }
            $installation['block'][$key]['gp_cost'] = $gp_cost;
            $installation['block'][$key]['gp_total'] = $gp_total;
        }
    }

    
    $consumable = GetConsumable($id, $db);
    
          
            // response in json format
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

            $numeric_style = array(
                'numberFormat' => array(
                    'formatCode' => '###,###,###,##0.00'
                )
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

            $sheet->getColumnDimension('C')->setWidth(60);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);

            $i = 1;

            // general requirement
            $sheet->setCellValue('A'. $i, $gneral_requirement["title"]);
            $sheet->mergeCells('A'. $i . ':G'. $i);

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

            $i = $i + 1;
            
            $sheet->setCellValue('A'. $i, "#");
            $sheet->setCellValue('B'. $i, "No");
            $sheet->setCellValue('C'. $i, "Description");
            $sheet->setCellValue('D'. $i, "Qty");
            $sheet->setCellValue('E'. $i, "Unit");
            $sheet->setCellValue('F'. $i, "Unit Labor Cost");
            $sheet->setCellValue('G'. $i, "Total Labor Cost");

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

            $i = $i + 1;

            $item = 1;
            foreach($gneral_requirement['block'] as $block)
            {
                if($block['not_show'] != "")
                    continue;

                $sheet->setCellValue('A'. $i, $item);
                $sheet->setCellValue('B'. $i, $block['no']);
                $sheet->setCellValue('C'. $i, $block['desc']);
                $sheet->setCellValue('D'. $i, number_format($block['qty']));
                $sheet->setCellValue('E'. $i, $block['unit']);
                $sheet->setCellValue('F'. $i, number_format($block['unit_cost'] * (100 - ($block['discount'] != '' ? $block['discount'] : 0)) / 100, 2, '.', ''));

                if($block['unit_cost'] * (100 - ($block['discount'] != '' ? $block['discount'] : 0)) / 100 * $block['qty'] != 0 && ($block['total'] == 0 || $block['total'] == ''))
                    $sheet->setCellValue('G'. $i, 'FREE AS PACKAGE!');
                else
                    $sheet->setCellValue('G'. $i, number_format($block['total'], 2, '.', ''));
                
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

                $sheet->getStyle('F'. $i. ':' . 'G' . $i)->applyFromArray($numeric_style);

                $i = $i + 1;
                $item += 1;
            }

            $i = $i + 1;
        
            // installation
            $sheet->setCellValue('A'. $i, $installation["title"]);
            $sheet->mergeCells('A'. $i . ':G'. $i);

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

            $i = $i + 1;
            
            $sheet->setCellValue('A'. $i, "#");
            $sheet->setCellValue('B'. $i, "No");
            $sheet->setCellValue('C'. $i, "Description");
            $sheet->setCellValue('D'. $i, "Qty");
            $sheet->setCellValue('E'. $i, "Unit");
            $sheet->setCellValue('F'. $i, "Unit Labor Cost");
            $sheet->setCellValue('G'. $i, "Total Labor Cost");

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

            $i = $i + 1;

            $starter = $i;
            $a = 0;

            $item = 1;
            foreach($installation['block'] as $block)
            {
                $sheet->setCellValue('A'. $i, $item);
                $sheet->setCellValue('B'. $i, $block['no']);
                $sheet->setCellValue('C'. $i, $block['desc']);
                $sheet->setCellValue('D'. $i, number_format($block['qty']));
                $sheet->setCellValue('E'. $i, $block['unit']);
                if($block["gp_cnt"] != 0)
                {
                    if($i > $starter)
                    {
                        $sheet->mergeCells('F' . $a . ':F' . ($i -1));
                        $sheet->getStyle('F' . $a . ':F' . ($i -1))->applyFromArray($center_style);

                        $sheet->mergeCells('G' . $a . ':G' . ($i -1));
                        $sheet->getStyle('G' . $a . ':G' . ($i -1))->applyFromArray($center_style);
                    }
                    $a = $i;
                    
                }
                
                $sheet->setCellValue('F'. $i, number_format((float)$block['gp_cost'], 2, '.', ''));
                if($block['gp_total'] == '' || $block['gp_total'] == 0)
                    $sheet->setCellValue('G'. $i, 'FREE AS PACKAGE!');
                else
                    $sheet->setCellValue('G'. $i, number_format((float)$block['gp_total'], 2, '.', ''));
                
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

                $sheet->getStyle('F'. $i. ':' . 'G' . $i)->applyFromArray($numeric_style);

                $i = $i + 1;
                $item += 1;
            }

            if($a > 2)
            {
                if($i > $starter && $a < $i-1)
                {
                    $sheet->mergeCells('F' . $a . ':F' . ($i -1));
                    $sheet->getStyle('F' . $a . ':F' . ($i -1))->applyFromArray($center_style);

                    $sheet->mergeCells('G' . $a . ':G' . ($i -1));
                    $sheet->getStyle('G' . $a . ':G' . ($i -1))->applyFromArray($center_style);

                    $sheet->getStyle('F'. $i. ':' . 'G' . $i)->applyFromArray($numeric_style);
                }
            }

            $i = $i + 1;

            // consumable
            $sheet->setCellValue('A'. $i, $consumable["title"]);
            $sheet->mergeCells('A'. $i . ':G'. $i);

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);
            

            $i = $i + 1;
            
            $sheet->setCellValue('A'. $i, "#");
            $sheet->setCellValue('B'. $i, "No");
            $sheet->setCellValue('C'. $i, "Description");
            $sheet->setCellValue('D'. $i, "Qty");
            $sheet->setCellValue('E'. $i, "Unit");
            $sheet->setCellValue('F'. $i, "Unit Labor Cost");
            $sheet->setCellValue('G'. $i, "Total Labor Cost");

            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
            $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);
            

            $i = $i + 1;

            $item = 1;
            foreach($consumable['block'] as $block)
            {
                if($block['not_show'] != "")
                    continue;

                $sheet->setCellValue('A'. $i, $item);
                $sheet->setCellValue('B'. $i, $block['no']);
                $sheet->setCellValue('C'. $i, $block['desc']);
                $sheet->setCellValue('D'. $i, number_format($block['qty']));
                $sheet->setCellValue('E'. $i, $block['unit']);
                $sheet->setCellValue('F'. $i, number_format($block['unit_cost'] * ($block['ratio'] != '' ? $block['ratio'] : 1) * (100 - $block['discount']) / 100, 2, '.', ''));

                if($block['unit_cost'] * ($block['ratio'] != '' ? $block['ratio'] : 1) * (100 - ($block['discount'] != '' ? $block['discount'] : 0)) / 100 * $block['qty'] != 0 && ($block['total'] == 0 || $block['total'] == ''))
                    $sheet->setCellValue('G'. $i, 'FREE AS PACKAGE!');
                else
                    $sheet->setCellValue('G'. $i, number_format($block['total'], 2, '.', ''));
                
                
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($styleArray);
                $sheet->getStyle('A'. $i. ':' . 'G' . $i)->applyFromArray($center_style);

                $sheet->getStyle('F'. $i. ':' . 'G' . $i)->applyFromArray($numeric_style);

                $i = $i + 1;
                $item += 1;
            }

            $i = $i + 1;

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


function GetGeneralRequirement($qid, $db)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_general_requirement
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            $row['block'] = $item;
            
            $row["show_r"] = 0;
            $row["pixa_r"] = 0;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $block[] = array(
            "id" => 1,
            "no" => "",
            "desc" => "Mobilization/Demobilization",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 2,
            "no" => "",
            "desc" => "Project Supervision",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 3,
            "no" => "",
            "desc" => "Tools & Equipment",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $block[] = array(
            "id" => 4,
            "no" => "",
            "desc" => "Functionality Test",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "General Requirements",
            "show_r" => 0,
            "pixa_r" => 0,
            "block" => $block,
        );
    }

    return $merged_results;
}


function GetConsumable($qid, $db)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_consumable
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            $row['block'] = $item;

            $row["show_c"] = 0;
            $row["pixa_c"] = 0;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $block[] = array(
            "id" => 1,
            "no" => "",
            "desc" => "Consumables (Base on BOM)",
            "qty" => 1,
            "unit" => "",
            "unit_cost" => 0,
            "discount" => 0,
            "ratio" => "1.00",
            "total" => "0.00",
            "not_show" => "",
            "details" => [],
        );

        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "Consumables",
            "show_c" => 0,
            "pixa_c" => 0,
            "block" => $block,
        );
    }

    return $merged_results;
}


function GetInstallation($qid, $db)
{
    $query = "
        SELECT 
        *
        FROM quotation_eng_installation
        WHERE quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];
    $merged_results = [];
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $list = $row['block'];

        if($list != '')
        {
            $item = json_decode($list, TRUE);
            // add gp_cnt field to each item
            foreach($item as $key => $value)
            {
                $item[$key]['gp_cnt'] = 1;
                $item[$key]['gp_cost'] = 0;
                $item[$key]['gp_total'] = 0;
            }
             
            $row['block'] = $item;

            $row["show_i"] = 0;
            $row["pixa_i"] = 0;
        }

        $merged_results = $row;
    }

    if($merged_results == [])
    {
        $merged_results = array(
            "id" => 0,
            "quotation_id" => $qid,
            "title" => "Lighting Fixtures Installation",
            "show_i" => 0,
            "pixa_i" => 0,
            "block" => [],
        );
    }

    return $merged_results;
}

function GetSameGroupOfItem($ary, $group) {
    $result = array();
    foreach($ary as $item)
    {
        if($item['group'] == $group)
            $result[] = $item;
    }
    return $result;
}
?>