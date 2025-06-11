<?php
ob_start();

error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';

include_once 'api/config/database.php';
include_once 'api/config/conf.php';
require_once 'vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {

    if($id == 0)
    {
        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }

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
                    pageless,
                    (SELECT COUNT(*) FROM quotation_page WHERE quotation_id = quotation.id and quotation_page.status <> -1) page_count
                    FROM quotation
                    WHERE status <> -1 and id=$id";


    $query = $query . " order by quotation.created_at desc ";

    if (!empty($_GET['page'])) {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        if (false === $page) {
            $page = 1;
        }
    }

    if (!empty($_GET['size'])) {
        $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
        if (false === $size) {
            $size = 10;
        }

        $offset = ($page - 1) * $size;

        $query = $query . " LIMIT " . $offset . "," . $size;
    }

    $stmt = $db->prepare($query);
    $stmt->execute();

    $vat = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        $pages = GetPages($row['id'], $db, $row['pageless']);
        // print
        $product_array = GetProductItems($pages, $row['id'], $db);

        $vat = $pages[0]['total'][0]['vat'];

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
                $sheet->setTitle("Overview");
    
    
                $sheet->getStyle('A1:P300')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1:P300')->getAlignment()->setVertical('center');    
    
                $sheet->setCellValue('A1', 'NO');
                $sheet->setCellValue('B1', 'ID');
                $sheet->setCellValue('C1', 'Qty');
                $sheet->setCellValue('D1', 'Product Price');

                if($vat == 'P')
                {
                    $sheet->setCellValue('E1', 'Vat');
                    $sheet->setCellValue('F1', 'Amount');
                    $sheet->setCellValue('G1', '');
                    $sheet->setCellValue('H1', '');
                    $sheet->setCellValue('I1', 'CP價格幣種');
                    $sheet->setCellValue('J1', 'CP價格日期');
                    $sheet->setCellValue('K1', 'CP價格');
                    $sheet->setCellValue('L1', 'SRP價格日期');
                    $sheet->setCellValue('M1', 'SRP價格');
                    $sheet->setCellValue('N1', '');
                    $sheet->setCellValue('O1', '');
                    $sheet->setCellValue('P1', 'SRP比例');
                    $sheet->setCellValue('Q1', 'QP比例');
                }
                else
                {
                    $sheet->setCellValue('E1', 'Amount');
                    $sheet->setCellValue('F1', '');
                    $sheet->setCellValue('G1', '');
                    $sheet->setCellValue('H1', 'CP價格幣種');
                    $sheet->setCellValue('I1', 'CP價格日期');
                    $sheet->setCellValue('J1', 'CP價格');
                    $sheet->setCellValue('K1', 'SRP價格日期');
                    $sheet->setCellValue('L1', 'SRP價格');
                    $sheet->setCellValue('M1', '');
                    $sheet->setCellValue('N1', '');
                    $sheet->setCellValue('O1', 'SRP比例');
                    $sheet->setCellValue('P1', 'QP比例');
                }

    
                $sheet->getColumnDimension('A')->setWidth(13.82);
                $sheet->getColumnDimension('B')->setWidth(13.82);
                $sheet->getColumnDimension('C')->setWidth(13.82);
                $sheet->getColumnDimension('D')->setWidth(18.82);
                $sheet->getColumnDimension('E')->setWidth(18.82);
                $sheet->getColumnDimension('F')->setWidth(18.82);
                $sheet->getColumnDimension('G')->setWidth(18.82);
                $sheet->getColumnDimension('H')->setWidth(18.82);
                $sheet->getColumnDimension('I')->setWidth(18.82);
                $sheet->getColumnDimension('J')->setWidth(18.82);
                $sheet->getColumnDimension('K')->setWidth(18.82);
                $sheet->getColumnDimension('L')->setWidth(18.82);
                $sheet->getColumnDimension('M')->setWidth(18.82);
                $sheet->getColumnDimension('N')->setWidth(18.82);
                $sheet->getColumnDimension('O')->setWidth(18.82);
                $sheet->getColumnDimension('P')->setWidth(18.82);
                $sheet->getColumnDimension('Q')->setWidth(18.82);
    
                $i = 2;
                foreach($product_array as $row)
                {
                    if($row['is_selected'] == 'xxx')
                    {
                        $i++;
                        continue;
                    }

                    $sheet->setCellValue('A' . $i, $row['num']);
                    if($row['pid'] != '0')
                        $sheet->setCellValue('B' . $i, $row['pid']);
                    $sheet->setCellValue('C' . $i, $row['qty']);

                    $discount = $row['discount'];
                    $ratio = $row['ratio'];
                    if($ratio == '')
                        $ratio = 1;

                    if($discount != "0")
                    {
                        if($discount == 100)
                            $price = round($row['price'] * $ratio, 2);
                        else
                            $price = round($row['price'] * $ratio * (100 - $discount) / 100, 2);
                    }
                    else
                        $price = round($row['price'] * $ratio, 2);

                    $sheet->setCellValue('D' . $i, $price);

                    if($vat == 'P')
                    {
                        if($discount == 100)
                            $sheet->setCellValue('E' . $i, round($row['price']  * 0.12, 2));
                        else
                            $sheet->setCellValue('E' . $i, round($price  * 0.12, 2));
                    }
                    elseif($vat == 'Y')
                    {
                        if($discount == 100)
                            $sheet->setCellValue('E' . $i, round($row['price'] * $ratio * 0.12, 2));
                        else
                            $sheet->setCellValue('E' . $i, round($price * $ratio * 0.12, 2));
                    }


                    if($vat == 'P')
                    {
                        $sheet->setCellValue('F' . $i, $row['amount']);
    
                        if($row['pid'] != '0')
                        {
                            if(count($row['ps_var']) > 0)
                            {
                                $products = GetProductSet($row['ps_var'], $db);
    
                                $price = "";
                                $price_change = "";
                                $price_ntd = "";
                                $price_ntd_change = "";
                                $currency = "";
                                $price_changek = "";
                                $pricel = "";
                                
                                foreach($products as $product)
                                {
                                    if($product['currency'] == "")
                                    {
                                        $price .= $product['price'] . " + ";
                                        $price_change .= ($product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '') . " + ";
                                    }
                                    else
                                    {
                                        $price .= $product['price_ntd'] . " + ";
                                        $price_change .= ($product['price_ntd_change'] != '' ? substr($product['price_ntd_change'], 0, 10) : '') . " + ";
                                    }
    
                                    if($product['currency'] != "")
                                        $currency .= $product['currency'] . " + ";
    
                                    $price_changek .= ($product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '') . " + ";
                                    $pricel .= $product['price'] . " + ";
                                }
                                
                                $price = substr($price, 0, -2);
                                $pricel = substr($pricel, 0, -2);
                                $price_change = substr($price_change, 0, -2);
                                $price_changek = substr($price_changek, 0, -2);
                                $price_ntd = substr($price_ntd, 0, -2);
                                $price_ntd_change = substr($price_ntd_change, 0, -2);
                                $currency = substr($currency, 0, -2);
    
                                $sheet->setCellValue('K' . $i, $price);
                                $sheet->setCellValue('J' . $i, $price_change);
                                $sheet->setCellValue('I' . $i, $currency);
                                $sheet->setCellValue('L' . $i, $price_changek);
                                $sheet->setCellValue('M' . $i, $pricel);
                            }
                            else
                            {
                                $product = $row['product'];
        
                                if($product['currency'] == "")
                                {
                                    $sheet->setCellValue('K' . $i, $product['price']);
                                    $sheet->setCellValue('J' . $i, $product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '');
                                }
                                else
                                {
                                    $sheet->setCellValue('K' . $i, $product['price_ntd']);
                                    $sheet->setCellValue('J' . $i, $product['price_ntd_change'] != '' ? substr($product['price_ntd_change'], 0, 10) : '');
                                }
        
        
                                $sheet->setCellValue('I' . $i, $product['currency']);
                                
                                $sheet->setCellValue('L' . $i, $product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '');
                                $sheet->setCellValue('M' . $i, $product['price']);
        
                                if($product['price'] != 0 && $product['currency'] == "")
                                {
                                    $sheet->setCellValue('P' . $i, round($product['price'] / $product['price'], 2));
                                    $sheet->setCellValue('Q' . $i, round($price / $product['price'], 2));
                                }
                                
                                if($product['price_ntd'] != 0 && $product['currency'] != "")
                                {
                                    $sheet->setCellValue('P' . $i, round($product['price'] / $product['price_ntd'], 2));
                                    $sheet->setCellValue('Q' . $i, round($price / $product['price_ntd'], 2));
                                }
                            }
                        }
                    }
                    else
                    {
                        $sheet->setCellValue('E' . $i, $row['amount']);

                        // if($vat == 'Y')
                        //     $sheet->setCellValue('E' . $i, round($row['amount'] * 1.12, 2));
                    
    
                        if($row['pid'] != '0')
                        {
                            if(count($row['ps_var']) > 0)
                            {
                                $products = GetProductSet($row['ps_var'], $db);
    
                                $price = "";
                                $price_change = "";
                                $price_ntd = "";
                                $price_ntd_change = "";
                                $currency = "";
                                $price_changek = "";
                                $pricel = "";
                                
                                foreach($products as $product)
                                {
                                    if($product['currency'] == "")
                                    {
                                        $price .= $product['price'] . " + ";
                                        $price_change .= ($product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '') . " + ";
                                    }
                                    else
                                    {
                                        $price .= $product['price_ntd'] . " + ";
                                        $price_change .= ($product['price_ntd_change'] != '' ? substr($product['price_ntd_change'], 0, 10) : '') . " + ";
                                    }
    
                                    if($product['currency'] != "")
                                        $currency .= $product['currency'] . " + ";
    
                                    $price_changek .= ($product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '') . " + ";
                                    $pricel .= $product['price'] . " + ";
                                }
                                
                                $price = substr($price, 0, -2);
                                $pricel = substr($pricel, 0, -2);
                                $price_change = substr($price_change, 0, -2);
                                $price_changek = substr($price_changek, 0, -2);
                                $price_ntd = substr($price_ntd, 0, -2);
                                $price_ntd_change = substr($price_ntd_change, 0, -2);
                                $currency = substr($currency, 0, -2);
    
                                $sheet->setCellValue('K' . $i, $price);
                                $sheet->setCellValue('J' . $i, $price_change);
                                $sheet->setCellValue('I' . $i, $currency);
                                $sheet->setCellValue('L' . $i, $price_changek);
                                $sheet->setCellValue('M' . $i, $pricel);
                            }
                            else
                            {
                                $product = $row['product'];
        
                                if($product['currency'] == "")
                                {
                                    $sheet->setCellValue('J' . $i, $product['price']);
                                    $sheet->setCellValue('I' . $i, $product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '');
                                }
                                else
                                {
                                    $sheet->setCellValue('J' . $i, $product['price_ntd']);
                                    $sheet->setCellValue('I' . $i, $product['price_ntd_change'] != '' ? substr($product['price_ntd_change'], 0, 10) : '');
                                }
        
        
                                $sheet->setCellValue('H' . $i, $product['currency']);
                                
                                $sheet->setCellValue('K' . $i, $product['price_change'] != '' ? substr($product['price_change'], 0, 10) : '');
                                $sheet->setCellValue('L' . $i, $product['price']);
        
                                if($product['price'] != 0 && $product['currency'] == "")
                                {
                                    $sheet->setCellValue('O' . $i, round($product['price'] / $product['price'], 2));
                                    $sheet->setCellValue('P' . $i, round($price / $product['price'], 2));
                                }
                                
                                if($product['price_ntd'] != 0 && $product['currency'] != "")
                                {
                                    $sheet->setCellValue('O' . $i, round($product['price'] / $product['price_ntd'], 2));
                                    $sheet->setCellValue('P' . $i, round($price / $product['price_ntd'], 2));
                                }
                            }
                        }
                    }

                    // $sheet->getStyle('A' . $i . ':' . 'Q' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            
                $sheet->getStyle('A1:' . 'Q1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . 'Q' . --$i)->applyFromArray($styleArray);
    
                ob_end_clean();
    
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="cost_analysis_quotation.xlsx"');
    
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


function GetSubtotal($ary)
{
    $total = 0;
    foreach ($ary as $item) {
        $total += $item['amount'];
    }

    return $total;
}

function GetPages($qid, $db, $pageless){
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
        $total = GetTotal($qid, $page, $db, $pageless);
        $term = GetTerm($qid, $page, $db);
        $payment_term = GetPaymentTerm($qid, $page, $db);
        $sig = GetSig($qid, $page, $db);
  
        $merged_results[] = array(
            "id" => $id,
            "page" => $page,
            "types" => $type,
            "total" => $total,
            "term" => $term,
            "payment_term" => $payment_term,
            "sig" => $sig,
        );
    }

    return $merged_results;
}

function GetTotal($qid, $page, $db, $pageless){
    $query = "
        SELECT 
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM   quotation_total
        WHERE  quotation_id = " . $qid;
    if($pageless != 'Y')
        $query .= " AND  page = " . $page;
    $query .= "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    
    $discount = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];

        $merged_results[] = array(
            "page" => $page,
            "discount" => $discount,
            "vat" => $vat,
            "show_vat" => $show_vat,
            "valid" => $valid,
            "total" => $total,
            
            
        );
    }

    return $merged_results;
}


function GetTerm($qid, $page, $db){
    $query = "
        SELECT 
        `page`,
        title,
        brief,
        list 
        FROM   quotation_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
    

        $merged_results[] = array(
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
    }

    return $merged_results;
}


function GetPaymentTerm($qid, $page, $db){
    $query = "
        SELECT 
        `page`,
        payment_method,
        brief,
        list 
        FROM   quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $payment = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
    
        $payment_method = explode (";", $payment);
        $payment_method= array_filter($payment_method);
        $payment_method = array_map('trim', $payment_method);
        $item = json_decode($list, TRUE); 

        $merged_results = array(
            "page" => $page,
            "payment_method" => $payment_method,
            "brief" => $brief,
            "list" => $item,
          
        );
    }

    return $merged_results;
}


function GetSigInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   quotation_signature
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(
        "page" => $page,
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}


function GetSig($qid, $page, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   quotation_signature
        WHERE  quotation_id = " . $qid . "
        AND  page = " . $page . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item_client = [];
    $item_company = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $type = '';
    $name = '';
    $photo = '';
    $url = '';
    $position = '';
    $phone = '';
    $email = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];
       
        if($type == 'C'){
            $item_client[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => '',
                "url" => '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
        if($type ==  'F'){
            $item_company[] = array(
                "id" => $id,
                "type" => $type,
                "photo" => $photo,
                "url" =>  $photo != '' ? 'https://storage.googleapis.com/feliiximg/' . $photo : '',
                "name" => $name,
                "position" => $position,
                "phone" => $phone,
                "email" => $email,
            );
        }
        
    }

    $merged_results = array(
        "page" => $page,
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}

function GetTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        title,
        brief,
        list 
        FROM   quotation_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $title = '';
    $brief = '';
    $list = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item[] = array(
            "id" => $id,
            "page" => $page,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
        
    }

    $merged_results = array(
        "page" => $page,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetPaymentTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        payment_method,
        brief,
        list 
        FROM   quotation_payment_term
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $item = [];

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
    $brief = '50% Downpayment & another 50% balance a day before the delivery';
    $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":"Address: 512 Edsa near Corner Urbano Plata St., Caloocan City"}]';

    $item = json_decode($list, TRUE); 
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $page = $row['page'];
        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item = json_decode($list, TRUE); 
        
    }

    $merged_results = array(
        "page" => $page,
        "payment_method" => $payment_method,
        "brief" => $brief,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetTotalInfo($qid, $db){
    $query = "
        SELECT 
        id,
        `page`,
        discount,
        vat,
        show_vat,
        valid,
        total
        FROM   quotation_total
        WHERE  quotation_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    
    $id = 0;
    $page = 0;
    $discount = 0;
    $vat = '';
    $show_vat = '';
    $valid = '';
    $total = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $valid = $row['valid'];
        $total = $row['total'];
        
        
    }

    $merged_results = array(
        "id" => $id,
        "page" => $page,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "valid" => $valid,
        "total" => $total,
        
        "real_total" => 0,

        "back_total" => 0,
    );

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
        $subtotal = GetSubtotal($blocks);

        $merged_results[] = array(
            "id" => $id,
            "org_id" => $id,
            "type" => $block_type,
            "name" => $block_name,
            "not_show" => $not_show,
            "real_amount" => $real_amount,
            "blocks" => $blocks,
            "subtotal" => $subtotal,
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
        v4,
        ps_var,
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
        $v4 = $row['v4'];
        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);

        $listing = $row['listing'];

        $srp = array(
            "price" => "",
            "price_change" => "",
            "quoted_price" => "",
            "quoted_price_change" => "",
        );

        if($pid != 0)
            $srp = GetProductPrice($row['pid'], $row['v1'], $row['v2'], $row['v3'], $row['v4'], $db);
    
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
            "v4" => $v4,
            "ps_var" => $ps_var,
            "list" => $listing,
            "srp" => $srp,
        );
    }

    return $merged_results;
}

function GetProducts($pid, $v1, $v2, $v3, $v4, $db)  {

    $price = 0;
    $price_change = "";
    $price_ntd = 0;
    $price_ntd_change = "";
    $quoted_price = 0;
    $quoted_price_change = "";
    $currency = "";

    $query = "
            SELECT currency, price_ntd, price_ntd_change, price, price_change, quoted_price, quoted_price_change
            FROM   product_category
            WHERE  id = " . $pid . "
            AND `status` <> -1 
            ORDER BY id
        ";

        // prepare the query
        $stmt = $db->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
            $currency = $row['currency'];
            $price = $row['price'];
            $price_ntd = $row['price_ntd'];
            $price_ntd_change = $row['price_ntd_change'];
            $price_change = $row['price_change'];
            $quoted_price = $row['quoted_price'];
            $quoted_price_change = $row['quoted_price_change'];
        }

        $ret = array(
            "currency" => $currency,
            "price" => $price,
            "price_ntd" => $price_ntd,
            "price_ntd_change" => $price_ntd_change,
            "price_change" => $price_change,
            "quoted_price" => $quoted_price,
            "quoted_price_change" => $quoted_price_change,
        );

        if($v1 != '' || $v2 != '' || $v3 != '' || $v4 != '')
        {
            $query = "SELECT price, price_ntd, price_ntd_change, price_change, quoted_price, quoted_price_change,
                    1st_variation,
                    2rd_variation,
                    3th_variation,
                    4th_variation
                FROM   product
                WHERE  product_id = " . $pid . "
                AND `status` <> -1 
                ORDER BY id";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->execute();

            $price = 0;
            $price_ntd = 0;
            $price_ntd_change = "";
            $price_change = "";
            $quoted_price = 0;
            $quoted_price_change = "";
            $val1 = "";
            $val2 = "";
            $val3 = "";
            $val4 = "";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $price = $row['price'];
                $price_ntd = $row['price_ntd'];
                $price_change = $row['price_change'];
                $price_ntd_change = $row['price_ntd_change'];
                $quoted_price = $row['quoted_price'];
                $quoted_price_change = $row['quoted_price_change'];
                $val1 = GetValue($row['1st_variation']);
                $val2 = GetValue($row['2rd_variation']);
                $val3 = GetValue($row['3th_variation']);
                $val4 = GetValue($row['4th_variation']);

                if($val1 == $v1 && $val2 == $v2 && $val3 == $v3 && $val4 == $v4)
                    break;
            }

            $ret['price'] = $price;
            $ret['price_ntd'] = $price_ntd;
            $ret['price_ntd_change'] = $price_ntd_change;
            $ret['price_change'] = $price_change;
            $ret['quoted_price'] = $quoted_price;
            $ret['quoted_price_change'] = $quoted_price_change;
        }

    return $ret;

}

function GetValue($str)
{
    if(trim($str) == '')
        return "";
    
    $obj = explode('=>', $str);

    return isset($obj[1]) ? $obj[1] : "";
}

function GetProductPrice($pid, $v1, $v2, $v3, $v4, $db)
{
    $price = 0;
    $price_change = "";
    $quoted_price = 0;
    $quoted_price_change = "";

    $ret = array(
        "price" => 0,
        "price_change" => "",
        "quoted_price" => 0,
        "quoted_price_change" => "",
    );


    if($v1 != '' || $v2 != '' || $v3 != '' || $v4 != '')
        $ret = GetProducts($pid, $v1, $v2, $v3, $v4, $db);
    else
    {
        
        $query = "
            SELECT price, price_change, quoted_price, quoted_price_change
            FROM   product_category
            WHERE  id = " . $pid . "
            AND `status` <> -1 
            ORDER BY id
        ";

        // prepare the query
        $stmt = $db->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row !== false)
        {
            $price = $row['price'];
            $price_change = $row['price_change'];
            $quoted_price = $row['quoted_price'];
            $quoted_price_change = $row['quoted_price_change'];
        }

        $ret = array(
            "price" => $price,
            "price_change" => $price_change,
            "quoted_price" => $quoted_price,
            "quoted_price_change" => $quoted_price_change,
        );
    }

    return $ret;
}


function GetQuotationExport($q_id, $db)
{
    $items = "[]";

    $query = "
        SELECT items
        FROM   quotation_export
        WHERE  quotation_id = " . $q_id . "
        AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row !== false)
    {
        $items = $row['items'];
    }

    // json to php array
    $items = json_decode($items, true);

    return $items;

}

function GetProductItems($pages, $q_id, $db)
{
    $merged_results = [];

    $cache_results = GetQuotationExport($q_id, $db);

    foreach($pages as $page)
    {
        foreach($page['types'] as $type)
        {
            foreach($type['blocks'] as $row)
            {

                $id = $row['id'];
                $type_id = $row['type_id'];
                $type = $row['type'];
                $code = $row['code'];
                $photo = $row['photo'];
                $qty = $row['qty'];
                $notes = $row['notes'];
                $price = $row['price'];
                $num = $row['num'];
                $pid = $row['pid'];

                
                $discount = $row['discount'];
                $amount = $row['amount'];
                $description = $row['desc'];
                $ratio = $row['ratio'];
                $v1 = $row['v1'];
                $v2 = $row['v2'];
                $v3 = $row['v3'];
                $v4 = $row['v4'];
                $ps_var = $row['ps_var'];

                $listing = $row['list'];

                $product = [];

                if($pid != 0)
                {
                    $product = GetProducts($row['pid'], $v1, $v2, $v3, $v4, $db);
                }
            
                $type == "" ? "" : "image";
                $url = $photo == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo;
            
                $merged_results[] = array(
                    "id" => $id,
                    "is_selected" => "",
                    "type_id" => $type_id,
                    "code" => $code,
                    "type" => $type,
                    "photo" => $photo,
                    "type" => $type,
                    "url" => $url,
                    "qty" => $qty,
                    "notes" => $notes,
                    "num" => $num,
                    "pid" => $pid,
                    "price" => $price,
                    "discount" => $discount,
                    "ratio" => $ratio,
                    "amount" => $amount,
                    "desc" => $description,
                    "v1" => $v1,
                    "v2" => $v2,
                    "v3" => $v3,
                    "v4" => $v4,
                    "list" => $listing,

                    "ps_var" => $ps_var,

                    "product" => $product,
                );
                
            }

            $merged_results[] = array(
                "id" => $id,
                "is_selected" => "xxx",
                "type_id" => $type_id,
                "code" => $code,
                "type" => $type,
                "photo" => $photo,
                "type" => $type,
                "url" => $url,
                "qty" => $qty,
                "notes" => $notes,
                "num" => $num,
                "pid" => $pid,
                "price" => $price,
                "discount" => $discount,
                "ratio" => $ratio,
                "amount" => $amount,
                "desc" => $description,
                "v1" => $v1,
                "v2" => $v2,
                "v3" => $v3,
                "v4" => $v4,
                "ps_var" => $ps_var,
                "list" => $listing,

                "ps_var" => $ps_var,

                "product" => $product,
            );
        }
    }

    $return_result = array();
    foreach ($cache_results as $result)
    {
        // if result[id] is in merged_results, then remove it from merged_results
        $index = array_search($result['id'], array_column($merged_results, 'id'));
        if($index !== false)
        {
            $return_result[] = $merged_results[$index];
        }
    }

    foreach ($merged_results as $result)
    {
        $index = array_search($result['id'], array_column($cache_results, 'id'));
        if($index === false)
        {
            $return_result[] = $result;
        }
     
    }

    return $return_result;
}

function GetProduct($id, $v1, $v2, $v3, $v4, $db)
{
    $pid = 0;

    $query = "
        SELECT *
        FROM product_category
        WHERE  id = '" . $id . "'
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $product_category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $products = [];
    $query = "
        SELECT *
        FROM product
        WHERE  product_id = '" . $id . "'
        AND `status` <> -1 
        ORDER BY id";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    
    

    return $pid;
}



function GetProductSet($ps_var, $db)
{
    $merged_results = [];

    foreach($ps_var as $ps_lines)
    {
        $jsonstr = "";

        $lines = explode("\n", $ps_lines);
        foreach($lines as $line)
        {
            if(trim($line) != "")
            {
                // split key and value by :
                $line = explode(":", $line);
                $key = $line[0];
                $value = $line[1];
                $jsonstr .= '"' . trim($key) . '":"' . trim($value) . '",';
            }
        }

        $jsonstr = rtrim($jsonstr, ",");
        $jsonstr = "{" . $jsonstr . "}";

        $var_json = json_decode($jsonstr, true);
        
        $v1 = "";
        $v2 = "";
        $v3 = "";
        $v4 = "";
        // iterate through json
        foreach ($var_json as $key => $value) {
            if($key != 'id')
            {
                if($v1 == "")
                    $v1 = $value;
                else if($v2 == "")
                    $v2 = $value;
                else if($v3 == "")
                    $v3 = $value;
                else if($v4 == "")
                    $v4 = $value;
            }
            else if($key == 'id')
            {
                $pid = $value;
            }
        }

        if($v4 != "")
            $query = "select * from product where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "' and 4th_variation like '%" . $v4 . "'";
        else
            $query = "select * from product where product_id = :pid and 1st_variation like '%" . $v1 . "' and 2rd_variation like '%" . $v2 . "' and 3th_variation like '%" . $v3 . "'";
        // prepare the query
        $stmt = $db->prepare($query);

        $stmt->bindParam(':pid', $pid);
        
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $merged_results[] = $row;

        $query = "select * from product_category where id = :pid";
        // prepare the query
        $stmt = $db->prepare($query);
        $stmt->bindParam(':pid', $pid);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $currency = $row['currency'];

        // add currency to the result
        $merged_results[count($merged_results) - 1]['currency'] = $currency;

    }

    return $merged_results;

}