<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = (isset($_GET['id']) ?  $_GET['id'] : 0);
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
                    title, 
                    kind, 
                    amount, 
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
                    status, 
                    create_id,
                    created_at
                    FROM price_comparison
                    WHERE status <> -1 and id=$id";


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

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $kind = $row['kind'];
        $amount = $row['amount'];
        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];

        $first_line = $row['first_line'];
        $second_line = $row['second_line'];
        $project_category = $row['project_category'];
        $quotation_no = $row['quotation_no'];
        $quotation_date = $row['quotation_date'];
        $prepare_for_first_line = $row['prepare_for_first_line'];
        $prepare_for_second_line = $row['prepare_for_second_line'];
        $prepare_for_third_line = $row['prepare_for_third_line'];
        $prepare_by_first_line = $row['prepare_by_first_line'];
        $prepare_by_second_line = $row['prepare_by_second_line'];
        $footer_first_line = $row['footer_first_line'];
        $footer_second_line = $row['footer_second_line'];

        $options = GetOptions($id, $db);

        $groups = GetGroups($id, $db, $options);

        $ng = GetGroupsDis($id, $db, $options);

        $clone = json_decode( json_encode($groups), true);

        // $dis_groups = FloorGroups($clone);
        
        $legends = GetAllLegend($groups);

        $total_info = GetTotalInfo($row['id'], $db);
        $term_info = GetTermInfo($row['id'], $db);
        $payment_term_info = GetPaymentTermInfo($row['id'], $db);
        $payment_term = GetPaymentTerm($row['id'], $db);
        $sig_info = GetSigInfo($row['id'], $db);

        $subtotal_a = [];
        $subtotal_b = [];
        $subtotal_c = [];

        if(count($options) > 0)
            $subtotal_a = GetSubTotal($row['id'], $options[0]['id'], $db);
        if(count($options) > 1)
            $subtotal_b = GetSubTotal($row['id'], $options[1]['id'], $db);
        if(count($options) > 2)
            $subtotal_c = GetSubTotal($row['id'], $options[2]['id'], $db);


        // $total_info['back_total'] = ($subtotal_info_not_show_a + $subtotal_info_not_show_b) * (100 - $total_info['discount']) / 100;
        // if($total_info['vat'] == 'Y')
        //     $total_info['back_total'] += $subtotal_info_not_show_a * (100 - $total_info['discount']) / 100 * 0.12;

        // $total_info['back_total'] = number_format($total_info['back_total'], 2, '.', '');

        $merged_results = array(
            "id" => $id,
            "first_line" => $first_line,
            "second_line" => $second_line,
            "project_category" => $project_category,
            "quotation_no" => $quotation_no,
            "quotation_date" => $quotation_date,
            "prepare_for_first_line" => $prepare_for_first_line,
            "prepare_for_second_line" => $prepare_for_second_line,
            "prepare_for_third_line" => $prepare_for_third_line,
            "prepare_by_first_line" => $prepare_by_first_line,
            "prepare_by_second_line" => $prepare_by_second_line,
            "footer_first_line" => $footer_first_line,
            "footer_second_line" => $footer_second_line,
            "kind" => $kind,
            "amount" => $amount,
            "status" => $status,
            "create_id" => $create_id,
            "created_at" => $created_at,
            "options" => $options,
            "groups" => $groups,
            "dis_groups" => $ng,
            "legends" => $legends,
            "total_info" => $total_info,
            "term_info" => $term_info,
            "payment_term_info" => $payment_term_info,
            "payment_term" => $payment_term,
            "sig_info" => $sig_info,
            "subtotal_a" => $subtotal_a,
            "subtotal_b" => $subtotal_b,
            "subtotal_c" => $subtotal_c,
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
                $sheet = $spreadsheet->getActiveSheet();
    
                $spreadsheet->getProperties()->setCreator('PhpOffice')
                        ->setLastModifiedBy('PhpOffice')
                        ->setTitle('Office 2007 XLSX Test Document')
                        ->setSubject('Office 2007 XLSX Test Document')
                        ->setDescription('PhpOffice')
                        ->setKeywords('PhpOffice')
                        ->setCategory('PhpOffice');
    
                $total_page = count($options);

                $page = 0;
                foreach($options as $opt)
                {        
                    $sheet->getStyle('A1:P300')->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('A1:P300')->getAlignment()->setVertical('center');    
        
                    $sheet->setCellValue('A1', 'NO');
                    $sheet->setCellValue('B1', 'ID');
                    $sheet->setCellValue('C1', 'Qty');
                    $sheet->setCellValue('D1', 'Product Price');
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

                    // remove characters from title
                    $title = preg_replace('/[\/\*\[\]:?]/', '', $opt["title"]);
        
                    $sheet->setTitle($title);

                    $i = 2;
                    foreach($legends as $legend)
                    {   
                        $title = $legend['title'];
                        $sheet->setCellValue('A' . $i, $title);
    
                        foreach($legend['options'][$page]['temp_block_a'] as $row)
                        {
                            
                            if($row['pid'] != '0')
                                $sheet->setCellValue('B' . $i, $row['pid']);
                            $sheet->setCellValue('C' . $i, $row['qty']);
        
                            $discount = $row['discount'];
                            if($discount != "0")
                                $price = round($row['price'] * (100 - $discount) / 100, 2);
                            else
                                $price = $row['price'];
                            $sheet->setCellValue('D' . $i, $price);
                            $sheet->setCellValue('E' . $i, $row['amount']);
        
                            if($row['pid'] != '0')
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
        
                            // $sheet->getStyle('A' . $i . ':' . 'P' . $i)->applyFromArray($styleArray);
                            $i++;
                        }
    
                        $i++;
                    }

                    $sheet->getStyle('A1:' . 'P1')->getFont()->setBold(true);
                    $sheet->getStyle('A1:' . 'P' . --$i)->applyFromArray($styleArray);

                    $page++;
                
                    if($page < $total_page)
                    {
                        $spreadsheet->createSheet();
                        $spreadsheet->setActiveSheetIndex($page);
                        $sheet = $spreadsheet->getActiveSheet();
                    }

                    
                }

                $spreadsheet->setActiveSheetIndex(0);
                
                


                
                ob_end_clean();
    
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="cost_analysis_price_comparison.xlsx"');
    
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


function GetSubTotal($qid, $oid, $db)
{
    $total = 0;

    // $query = "
    //         select sum(amount) amt from price_comparison_item
    //         WHERE od_id  = " . $qid . " and `status` <> -1 and option_id = " . $oid . " 
    // ";

    $query = "SELECT sum(amount) amt FROM price_comparison_item where legend_id in 
                (select id from price_comparison_legend where group_id in 
                (select id from price_comparison_group where p_id = " . $qid . "  and status <> -1)  and status <> -1
                ) and status <> -1 and option_id = " . $oid;

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
        $total = $row['amt'];

    }

    return $total;
}


function GetAllLegend($groups)
{
    $legends = array();
    foreach ($groups as $group) {
        $legends = array_merge($legends, $group['legend']);
    }

    return $legends;
}

function GetGroups($qid, $db, $options){

    $merged_results = array();
    
    $query = "SELECT id, 
                    p_id, 
                    title, 
                    sn, 
                    color, 
                    create_id,
                    created_at
                    FROM price_comparison_group
                    WHERE status <> -1 and p_id=$qid";


    $query = $query . " order by sn ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $p_id = $row['p_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
   
        $legend = GetLegends($row['id'], $db, $options);

        $merged_results[] = array(
            "id" => $id,
            "p_id" => $p_id,
            "title" => $title,
            "sn" => $sn,
            "color" => $color,
            "create_id" => $create_id,
            "created_at" => $created_at,
            "legend" => $legend,
        );
    }

    return $merged_results;
}



function GetGroupsDis($qid, $db, $options){

    $merged_results = array();
    
    $query = "SELECT id, 
                    p_id, 
                    title, 
                    sn, 
                    color, 
                    create_id,
                    created_at
                    FROM price_comparison_group
                    WHERE status <> -1 and p_id=$qid";


    $query = $query . " order by sn ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $p_id = $row['p_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];
   
        $legend = GetLegendsDis($row['id'], $db, $options);

        $merged_results[] = array(
            "id" => $id,
            "p_id" => $p_id,
            "title" => $title,
            "sn" => $sn,
            "color" => $color,
            "create_id" => $create_id,
            "created_at" => $created_at,
            "legend" => $legend,
        );
    }

    return $merged_results;
}



function GetOptions($qid, $db){
    $query = "
            SELECT id,
                p_id,
                title,
                sn,
                color,
                status,
                create_id,
                created_at
            FROM   price_comparison_option 
            WHERE  p_id = " . $qid . "
            AND `status` <> -1 
            ORDER BY sn
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $p_id = $row['p_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];

        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];

        $merged_results[] = array(
            'id' => $id,
            'p_id' => $p_id,
            'title' => $title,
            'sn' => $sn,
            'color' => $color,
            'status' => $status,
            'create_id' => $create_id,
            'created_at' => $created_at
        );
       
    }

    return $merged_results;
}


function GetLegendsDis($qid, $db, $options){
    $query = "
            SELECT id,
                group_id,
                title,
                sn,
                color,
                status,
                create_id,
                created_at
            FROM   price_comparison_legend 
            WHERE  group_id = " . $qid . "
            AND `status` <> -1 
            ORDER BY sn
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $group_id = $row['group_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];

        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];

        $matrix = GetItemMaxtrix($row['id'], $db, $options);

        $merged_results[] = array(
            'id' => $id,
            'group_id' => $group_id,
            'title' => $title,
            'sn' => $sn,
            'color' => $color,
            'status' => $status,
            'create_id' => $create_id,
            'created_at' => $created_at,
            'matrix' => $matrix
        );
       
    }

    return $merged_results;
}



function GetLegends($qid, $db, $options){
    $query = "
            SELECT id,
                group_id,
                title,
                sn,
                color,
                status,
                create_id,
                created_at
            FROM   price_comparison_legend 
            WHERE  group_id = " . $qid . "
            AND `status` <> -1 
            ORDER BY sn
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $group_id = $row['group_id'];
        $title = $row['title'];
        $sn = $row['sn'];
        $color = $row['color'];

        $status = $row['status'];
        $create_id = $row['create_id'];
        $created_at = $row['created_at'];

        $options = GetOption($row['id'], $db, $options);

        $merged_results[] = array(
            'id' => $id,
            'group_id' => $group_id,
            'title' => $title,
            'sn' => $sn,
            'color' => $color,
            'status' => $status,
            'create_id' => $create_id,
            'created_at' => $created_at,
            'options' => $options
        );
       
    }

    return $merged_results;
}




function GetOptionDis($qid, $db, $options){

    $merged_results = [];

    foreach($options as $option){
        $items = GetItems($option['id'], $qid, $db);

        $merged_results[] = array(
            'id' => $option['id'],
            'p_id' => $option['p_id'],
            'title' => $option['title'],
            'sn' => $option['sn'],
            'color' => $option['color'],
            'status' => $option['status'],
            'create_id' => $option['create_id'],
            'created_at' => $option['created_at'],
            'temp_block_a' => $items,
        );
    }


    return $merged_results;
}



function GetOption($qid, $db, $options){

    $merged_results = [];

    foreach($options as $option){
        $items = GetItems($option['id'], $qid, $db);

        $merged_results[] = array(
            'id' => $option['id'],
            'p_id' => $option['p_id'],
            'title' => $option['title'],
            'sn' => $option['sn'],
            'color' => $option['color'],
            'status' => $option['status'],
            'create_id' => $option['create_id'],
            'created_at' => $option['created_at'],
            'temp_block_a' => $items,
        );
    }


    return $merged_results;
}

function GetItemMaxtrix($legend_id, $db, $options){
    $i = 0;
    $max_count = 0;

    $gp1_item = [];
    $gp2_item = [];
    $gp3_item = [];

    $row_item = [];

    foreach($options as $option){
        $items = GetItems($option['id'], $legend_id, $db);

        if($i == 0)
        {
            $gp1_item = $items;
            if(count($items) > $max_count)
                $max_count = count($items);
        }
        if($i == 1)
        {
            $gp2_item = $items;
            if(count($items) > $max_count)
                $max_count = count($items);
        }
        if($i == 2)
        {
            $gp3_item = $items;
            if(count($items) > $max_count)
                $max_count = count($items);
        }

        $i++;
    }

    $gp1 = [];
    $gp2 = [];
    $gp3 = [];
    for($i=0; $i<$max_count; $i++){
        if($i < count($gp1_item))
            $gp1 = $gp1_item[$i];
        else
            $gp1 =  array(
                'id' => 0,
                'od_id' => 0,
                'option_id' => 0,
                'legend_id' => 0,
                'sn' => 0,
                'photo1' => "",
                'photo2' => "",
                'photo3' => "",
                'code' => "",
                'brief' => "",
                'list' => "",
                'qty' => "",
                'price' => "",
                'ratio' => "",
                'notes' => "",
                'discount' => "",
                'amount' => "",
                'desc' => "",
                'pid' => "",
                'v1' => "",
                'v2' => "",
                'v3' => "",
                'v4' => "",
                'url1' => "",
                'url2' => "",
                'url3' => "",
                'srp' => '',
            );

        if($i < count($gp2_item))
            $gp2 = $gp2_item[$i];
        else
            $gp2 =  array(
                'id' => 0,
                'od_id' => 0,
                'option_id' => 0,
                'legend_id' => 0,
                'sn' => 0,
                'photo1' => "",
                'photo2' => "",
                'photo3' => "",
                'code' => "",
                'brief' => "",
                'list' => "",
                'qty' => "",
                'price' => "",
                'ratio' => "",
                'notes' => "",
                'discount' => "",
                'amount' => "",
                'desc' => "",
                'pid' => "",
                'v1' => "",
                'v2' => "",
                'v3' => "",
                'v4' => "",
                'url1' => "",
                'url2' => "",
                'url3' => "",
                'srp' => '',
            );

        if($i < count($gp3_item))
            $gp3 = $gp3_item[$i];
        else
            $gp3 =  array(
                'id' => 0,
                'od_id' => 0,
                'option_id' => 0,
                'legend_id' => 0,
                'sn' => 0,
                'photo1' => "",
                'photo2' => "",
                'photo3' => "",
                'code' => "",
                'brief' => "",
                'list' => "",
                'qty' => "",
                'price' => "",
                'ratio' => "",
                'notes' => "",
                'discount' => "",
                'amount' => "",
                'desc' => "",
                'pid' => "",
                'v1' => "",
                'v2' => "",
                'v3' => "",
                'v4' => "",
                'url1' => "",
                'url2' => "",
                'url3' => "",
                'srp' => '',
            );

        $row_item[] = array(
            'gp1' => $gp1,
            'gp2' => $gp2,
            'gp3' => $gp3,
        );
    }

    return $row_item;
}

function GetItems($option_id, $legend_id, $db){
    $query = "
        SELECT id,
        od_id,
        option_id,
        legend_id,
        sn,
        photo1,
        photo2,
        photo3,
        code,
        brief,
        list,
        qty,
        price,
        ratio,
        notes,
        discount,
        amount,
        `desc`,
        pid,
        v1,
        v2,
        v3,
        v4,
        ps_var
        FROM   price_comparison_item
        WHERE  option_id = " . $option_id . "
        AND  legend_id = " . $legend_id . "
        AND `status` <> -1 
        ORDER BY sn
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $od_id = $row['od_id'];
        $option_id = $row['option_id'];
        $legend_id = $row['legend_id'];
        $sn = $row['sn'];
     
        $photo1 = $row['photo1'];
        $photo2 = $row['photo2'];
        $photo3 = $row['photo3'];
        $code = $row['code'];
        $brief = $row['brief'];
        $list = $row['list'];
   
        $qty = $row['qty'];
      
        $price = $row['price'];
        $ratio = $row['ratio'];
        $notes = $row['notes'];

        $discount = $row['discount'];

        $amount = $row['amount'];
        $desc = $row['desc'];
        $pid = $row['pid'];

        $srp = GetProductPrice($row['pid'], $row['v1'], $row['v2'], $row['v3'], $row['v4'], $db);
      
        $v1 = $row['v1'];
        $v2 = $row['v2'];
        $v3 = $row['v3'];
        $v4 = $row['v4'];

        $ps_var = json_decode($row['ps_var'] == null ? "[]" : $row['ps_var'], true);
       
        $url1 = $photo1 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo1;
        $url2 = $photo2 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo2;
        $url3 = $photo3 == "" ? "" : "https://storage.googleapis.com/feliiximg/" . $photo3;

        $product = [];

        if($pid != 0)
        {
            $product = GetProductNew($row['pid'], $v1, $v2, $v3, $v4, $db);
        }

  
        $merged_results[] = array(
            'id' => $id,
            'od_id' => $od_id,
            'option_id' => $option_id,
            'legend_id' => $legend_id,
            'sn' => $sn,
            'photo1' => $photo1,
            'photo2' => $photo2,
            'photo3' => $photo3,
            'code' => $code,
            'brief' => $brief,
            'list' => $list,
            'qty' => $qty,
            'price' => $price,
            'ratio' => $ratio,
            'notes' => $notes,
            'discount' => $discount,
            'amount' => $amount,
            'desc' => $desc,
            'pid' => $pid,
            'v1' => $v1,
            'v2' => $v2,
            'v3' => $v3,
            'v4' => $v4,
            'ps_var' => $ps_var,
            'url1' => $url1,
            'url2' => $url2,
            'url3' => $url3,

            "srp" => $srp,

            "product" => $product,
        );
    }

    return $merged_results;
}


function GetProductNew($pid, $v1, $v2, $v3, $v4, $db)  {

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


function GetProducts($pid, $v1, $v2, $v3, $v4, $db)  {

    $query = "SELECT price,
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
    $val1 = "";
    $val2 = "";
    $val3 = "";
    $val4 = "";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price = $row['price'];
        $val1 = GetValue($row['1st_variation']);
        $val2 = GetValue($row['2rd_variation']);
        $val3 = GetValue($row['3th_variation']);
        $val4 = GetValue($row['4th_variation']);

        if($val1 == $v1 && $val2 == $v2 && $val3 == $v3 && $val4 == $v4)
            break;
    }

    return $price;

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
    $srp = 0;
    $p_srp = 0;

    if($v1 != '' || $v2 != '' || $v3 != '' || $v4 != '')
     $p_srp = GetProducts($pid, $v1, $v2, $v3, $v4, $db);

    if($p_srp > 0)
    {
        $srp = $p_srp;
    }
    else
    {
        
        $query = "
            SELECT price
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
            $srp = $row['price'];
        }
    }

    return $srp;
}

function FloorGroups($groups)
{
    $merged_results = [];


    foreach($groups as $group){

        foreach ($group['legend'] as &$leg) {
          
            $max_items = 0;

            foreach ($leg['options'] as &$option)
            {
                if(count($option['temp_block_a']) > $max_items){
                    $max_items = count($option['temp_block_a']);
                }
            } 

            $items = [];

            foreach ($leg['options'] as &$option)
            {
                

                $togo = $max_items - count($option['temp_block_a']);
            
                for($i = 0; $i < $togo; $i++){

                    $option['temp_block_a'][] = array(
                        'id' => 0,
                        'od_id' => 0,
                        'option_id' => 0,
                        'legend_id' => 0,
                        'sn' => 0,
                        'photo1' => "",
                        'photo2' => "",
                        'photo3' => "",
                        'code' => "",
                        'brief' => "",
                        'list' => "",
                        'qty' => 0,
                        'price' => 0,
                        'srp' => 0,
                        'ratio' => 0,
                        'notes' => "",
                        'amount' => 0,
                        'desc' => "",
                        'pid' => 0,
                        'v1' => "",
                        'v2' => "",
                        'v3' => "",
                        'v4' => "",
                        'url1' => "",
                        'url2' => "",
                        'url3' => "",
                    );
                }

                array_push($items, $option['temp_block_a']);

            }

            $leg['items'] = $items;

        }

        $merged_results[] = array(
            'id' => $group['id'],
            
            'title' => $group['title'],
            'sn' => $group['sn'],
            'color' => $group['color'],
            'create_id' => $group['create_id'],
            'created_at' => $group['created_at'],
            'legend' => $group['legend'],
          
        );
    }

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
        show_t,
        valid,
        coalesce(total1, '') total1,
        coalesce(total2, '') total2,
        coalesce(total3, '') total3
        FROM  price_comparison_total
        WHERE  price_id = " . $qid . "
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
    $show_t = '';
    $valid = '';
    $total1 = '';
    $total2 = '';
    $total3 = '';

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $show_vat = $row['show_vat'];
        $show_t = $row['show_t'];
        $valid = $row['valid'];
        $total1 = $row['total1'];
        $total2 = $row['total2'];
        $total3 = $row['total3'];
    }

    $merged_results = array(
        "id" => $id,
        "page" => $page,
        "discount" => $discount,
        "vat" => $vat,
        "show_vat" => $show_vat,
        "show_t" => $show_t,
        "valid" => $valid,
        "total1" => $total1,
        "total2" => $total2,
        "total3" => $total3,
        
        "real_total" => 0,

        "back_total" => 0,
    );

    return $merged_results;
}


function GetTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        pixel,
        title,
        brief,
        list 
        FROM   price_comparison_term
        WHERE  price_id = " . $qid . "
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
    $pixel = '0';
    $title = '';
    $brief = '';
    $list = '';
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $page = $row['page'];
        $pixel = $row['pixel'];
        $title = $row['title'];
        $brief = $row['brief'];
        $list = $row['list'];

        if($pixel == ''){
            $pixel = '0';
        }
       
        $item[] = array(
            "id" => $id,
            "page" => $page,
            "pixel" => $pixel,
            "title" => $title,
            "brief" => $brief,
            "list" => $list,
          
        );
        
    }

    $merged_results = array(
        "page" => $page,
        "pixel" => $pixel,
        "item" => $item,
               
    );

    return $merged_results;
}

function GetPaymentTerm($qid, $db){
    $query = "
        SELECT 
        `page`,
        pixel,
        payment_method,
        brief,
        list 
        FROM   price_comparison_payment_term
        WHERE  price_id = " . $qid . "
        AND `status` <> -1 
        ORDER BY id
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];
    

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $page = $row['page'];
        $pixel = $row['pixel'];
        $payment = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];

        if($pixel == ''){
            $pixel = '0';
        }
    
        $payment_method = explode (";", $payment);
        $payment_method= array_filter($payment_method);
        $payment_method = array_map('trim', $payment_method);
        $item = json_decode($list, TRUE); 

        $merged_results = array(
            "page" => $page,
            "pixel" => $pixel,
            "payment_method" => $payment_method,
            "brief" => $brief,
            "list" => $item,
          
        );
    }

    return $merged_results;
}


function GetPaymentTermInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        pixel,
        payment_method,
        brief,
        list 
        FROM   price_comparison_payment_term
        WHERE  price_id = " . $qid . "
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
    $pixel = "0";
    $payment_method = 'Cash; Cheque; Credit Card; Bank Wiring;';
    $brief = '50% Downpayment & another 50% balance a day before the delivery';
    $list = '[{"id":"0", "bank_name": "BDO", "first_line":"Acct. Name: Feliix Inc. Acct no: 006910116614", "second_line":"Branch: V.A Rufino", "third_line":""}, {"id":"1", "bank_name": "SECURITY BANK", "first_line":"Acct. Name: Feliix Inc. Acct no: 0000018155245", "second_line":"Swift code: SETCPHMM", "third_line":"Address: 512 Edsa near Corner Urbano Plata St., Caloocan City"}]';

    $item = json_decode($list, TRUE); 
  
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $page = $row['page'];
        $pixel = $row['pixel'];
        $payment_method = $row['payment_method'];
        $brief = $row['brief'];
        $list = $row['list'];
       
        $item = json_decode($list, TRUE); 

        if($pixel == ''){
            $pixel = '0';
        }
        
    }

    $merged_results = array(
        "page" => $page,
        "pixel" => $pixel,
        "payment_method" => $payment_method,
        "brief" => $brief,
        "item" => $item,
               
    );

    return $merged_results;
}


function GetSigInfo($qid, $db)
{
    $query = "
        SELECT 
        id,
        `page`,
        pixel,
        `type`,
        `name`,
        `photo`,
        position,
        phone,
        email 
        FROM   price_comparison_signature
        WHERE  price_id = " . $qid . "
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
    $pixel = '60';
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
        $pixel = $row['pixel'];
        $type = $row['type'];
        $name = $row['name'];
        $photo = $row['photo'];
        $position = $row['position'];
        $phone = $row['phone'];
        $email = $row['email'];

        if($pixel == ''){
            $pixel = '60';
        }
       
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
        "pixel" => $pixel,
        "item_client" => $item_client,
        "item_company" => $item_company,
               
    );

    return $merged_results;
}
