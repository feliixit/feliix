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

         
    $merged_results = array();
    

    $query = "SELECT id, 
                    first_line, 
                    second_line, 
                    project_category, 
                    quotation_no, 
                    quotation_date, 
                    prepare_for_first_line, 
                    prepare_for_second_line, 
                    prepare_for_third_line,
                    prepare_by_first_line,
                    prepare_by_second_line,
                    footer_first_line,
                    footer_second_line,
                    (SELECT COUNT(*) FROM quotation_page WHERE quotation_id = quotation.id and quotation_page.status <> -1) page_count
                    FROM quotation
                    WHERE status <> -1 and id=$id";



        $stmt = $db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         
            $pages = GetPages($row['id'], $db);
    
            $merged_results[] = array(
    
                "pages" => $pages,
     
            );
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

            $center_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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


function GetPages($qid, $db){
    $query = "
        SELECT id,
            page
        FROM   quotation_page
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = GetTypes($id, $db);
       
  
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
     
        );
    }

    return $merged_results;
}



function GetTypes($qid, $db){
    $query = "
        SELECT id,
        block_type,
        block_name,
        not_show,
        real_amount
        FROM   quotation_page_type
        WHERE  page_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $block_type = $row['block_type'];
        $block_name = $row['block_name'];

        $not_show = $row['not_show'];
        $real_amount = $row['real_amount'];

        $blocks = [];

        $blocks = GetBlocks($id, $db);
  

        $merged_results[] = array(
            "id" => $id,
            "org_id" => $id,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "blocks" => $blocks,
  
        );
    }

    return $merged_results;
}

function GetBlocks($qid, $db){
    $query = "
        SELECT id,
        type_id,
        `type`,
        code,
        photo,
        photo2,
        photo3,
        qty,
        ratio,
        price,
        discount,
        amount,
        description,
        v1,
        v2,
        v3,
        listing,
        num,
        notes,
        pid
        FROM   quotation_page_type_block
        WHERE  type_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $type_id = $row['type_id'];
        $type = $row['type'];
        $code = $row['code'];
        $photo = $row['photo'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $qty = $row['qty'];
        $notes = $row['notes'];
        $ratio = $row['ratio'];
        $price = $row['price'];
        $num = $row['num'];
        $pid = $row['pid'];
        $discount = $row['discount'];
        $amount = $row['amount'];
        $description = $row['description'];
        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $listing = $row['listing'];

        $type == "" ? "" : "image";
        $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
        $url2 = $photo2 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo2;
        $url3 = $photo3 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo3;
  
        $merged_results[] = array(
            "id" => $id,
            "type_id" => $type_id,
            "code" => $code,
            "type" => $type,
            "photo" => $photo,
            "photo2" => $photo2,
            "photo3" => $photo3,
            "type" => $type,
            "url" => $url,
            "url2" => $url2,
            "url3" => $url3,
            "qty" => $qty,
            "notes" => $notes,
            "ratio" => $ratio,
            "num" => $num,
            "pid" => $pid,
            "price" => $price,
            "discount" => $discount,
            "amount" => $amount,
            "desc" => $description,
            "v1" => $v1,
            "v2" => $v2,
            "v3" => $v3,
            "list" => $listing,
            "srp" => $srp,
        );
    }

    return $merged_results;
}

?>