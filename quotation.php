<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/config/database.php';


use \Firebase\JWT\JWT;

$test_manager = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;
            $username = $decoded->data->username;

$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales' || $GLOBALS['department'] == 'Engineering'){
$test_manager = "1";
}

//  ('Kuan', 'Dennis Lin', 'dereck', 'Ariel Lin', 'Kristel Tan');
if($user_id == 48 || $user_id == 2 || $user_id == 11 || $user_id == 6 ||  $user_id == 1 || $user_id == 3 || $user_id == 89 || $user_id == 129 || $user_id == 137 || $user_id == 138 || $user_id == 148 || $user_id == 191 || $user_id == 195)
$test_manager = "1";

$database = new Database();
$db = $database->getConnection();

$quotation_control = false;
$query = "SELECT quotation_control FROM access_control WHERE quotation_control LIKE '%" . $username . "%' ";
$stmt = $db->prepare( $query );
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
$quotation_control = true;
}

if(!$quotation_control)
{
    $id = (isset($_GET['id']) ?  $_GET['id'] : 0);
    $query = "SELECT can_view FROM quotation WHERE id = " . $id;

    $stmt = $db->prepare( $query );
    $stmt->execute();
    $can_view = '';
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $can_view = $row['can_view'];
    }

    if($can_view != "")
        header( 'location:index' );

}

}



catch (Exception $e){

header( 'location:index' );
}


//if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
//    header( 'location:index.php' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e){

header( 'location:index' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Quotation Form</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');

            dialogshow($('.list_function .new_function a.filter'), $('.list_function .dialog.A'));
            dialogshow($('.list_function .new_function a.sort'), $('.list_function .dialog.B'));

            $('.qn_page').click(function () {
                app.close_all();
            })

        })

    </script>

    <style>

        @font-face {
            font-family: 'SFPRODISPLAYMEDIUM';
            src: url('/fonts/SFPRODISPLAYMEDIUM.OTF') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'SFPRODISPLAYBOLD';
            src: url('/fonts/SFPRODISPLAYBOLD.OTF') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body.gray header > .headerbox {
            background-color: #707071;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.gray header nav a, body.gray header nav a:link {
            color: #000;
        }

        body.gray header nav a:hover {
            color: #333;
        }

        body.gray header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.gray header nav ul.info {
            margin-bottom: 0;
        }

        body.gray header nav ul.info b {
            font-weight: bold;
        }

        body.gray header{
            position: fixed;
            z-index: 999;
        }

        body.gray select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        }

        body.gray .mainContent{
            padding: 185px 12px 30px;
        }

        body.gray .mainContent > .block {
            display: block;
            width: 100%;
            border: none;
            margin: 0 0 15px;
        }

        body.gray .list_function .new_function {
            float: left;
            display: inline-block;
            position: relative;
            vertical-align: bottom;
            margin-right: 20px;
            margin-top: -15px;
        }

        body.gray .list_function .new_function a.add {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_add_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .list_function .new_function a.filter {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .list_function .new_function a.sort {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            cursor: pointer;
        }

        body.gray .dialog .formbox .half {
            width: 48.5%;
        }

        body.gray .list_function .pagenation a {
            color: #707071;
            border-color: #707071;
        }

        body.gray .list_function .pagenation a:hover {
            background-color: #707071;
            color: #FFF;
        }

        body.gray input.alone[type=checkbox]::before, body.gray input[type=checkbox] + Label::before {
            color: #414042;
        }

        #tb_product_list {
            width: 100%;
        }

        #tb_product_list thead th, #tb_product_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        #tb_product_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_product_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_product_list tbody tr td:nth-of-type(1) {
            width: 115px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) {
            width: 420px;
            padding-right: 20px
        }

        #tb_product_list tbody tr td:nth-of-type(3) {
            width: 460px;
        }

        #tb_product_list tbody tr td:nth-of-type(4) {
            width: 220px;
        }

        #tb_product_list tbody tr td:nth-of-type(5) {
            width: 150px;
        }

        #tb_product_list tbody tr td:nth-of-type(6) {
            width: 80px;
        }

        #tb_product_list tbody tr td:nth-of-type(1) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul {
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }

        #tb_product_list tbody tr td span.stock_qty,
        #tb_product_list tbody tr td span.stock_qty_ware {
            background-color: yellowgreen;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            margin: 3px 0 13px;
            padding: 1px 15px 2px;
            cursor: pointer;
        }

        #tb_product_list tbody tr td span.stock_qty_ware {
            background-color: #5bc0de;
        }

        #tb_product_list tbody tr td:nth-of-type(6) button {
            border: 2px solid black;
            width: 34px;
            height: 34px;
            box-sizing: border-box;
            padding: 6px;
            line-height: 1.0;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul:last-of-type {
            border-bottom: none;
        }

        #tb_product_list ul li {
            display: table-cell;
            text-decoration: none;
            text-align: left;
        }

        #tb_product_list ul li:first-of-type {
            font-weight: 600;
            padding: 1px 7px 1px 5px;
            max-width: 230px;
        }

        #tb_product_list ul li:nth-of-type(1) span {
            background-color: red;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list ul li:nth-of-type(2) span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px 2px 6px;
            margin: 0 2px;
        }

        #tb_product_list ul li.code {
            word-break: break-all;
        }


        #tb_product_list tbody tr.set_format1 td, #tb_product_list tbody tr.set_format2 td {
            background-color: rgba(255,255,0,0.1)!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) {
            width: 995px!important;
            columns: 2!important;
            padding: 10px 25px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) ul {
            margin-bottom: 0;
            break-inside: avoid-column;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) > div.product_set_desc {
            padding: 1px 7px 1px 5px;
            text-align: left;
            font-weight: 600;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) > div.product_set_desc > div {
            font-weight: 400;
            white-space: pre-line;
            padding-left: 10px;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) {
            width: 220px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(3) {
            width: 150px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(4) {
            width: 80px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(4) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(1) {
            width: 115px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(1) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) {
            width: 420px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(3) {
            width: 460px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(3) ul {
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(3) ul:last-of-type {
            border-bottom: none;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(4) {
            width: 220px!important;
        }


        #tb_product_list tbody tr.set_format2 > td:nth-of-type(5) {
            width: 150px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(6) {
            width: 80px!important;
        }

        #tb_specification_list {
            width: 100%;
            margin-top: 15px;
        }

        #tb_specification_list thead th, #tb_specification_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        #tb_specification_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_specification_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_specification_list tbody tr td:nth-of-type(1) {
            width: 130px;
        }

        #tb_specification_list tbody tr td:nth-of-type(2) {
            width: 130px;
        }

        #tb_specification_list tbody tr td:nth-of-type(2) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_specification_list tbody tr td:nth-of-type(3) {
            width: 460px;
            text-align: left;
        }

         #tb_specification_list tbody tr td:nth-of-type(3) div.pid, #tb_specification_list tbody tr td:nth-of-type(3) div.code {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
        }

        #tb_specification_list tbody tr td:nth-of-type(3) div.brief {
            font-size: 16px;
            font-weight: 400;
            white-space: pre-line;
        }

        #tb_specification_list tbody tr td:nth-of-type(3) div.listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
            white-space: pre-line;
        }

        #tb_specification_list tbody tr td:nth-of-type(4) {
            width: 130px;
        }

        #tb_specification_list tbody tr td:nth-of-type(4) i {
            font-size: 22px;
            cursor: pointer;
            margin: 0 5px;
        }

        #tb_signature_codebook {
            width: 100%;
            margin-top: 0;
        }

        #tb_signature_codebook thead th, #tb_signature_codebook tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        #tb_signature_codebook thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_signature_codebook tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_signature_codebook tbody tr td:nth-of-type(5) img {
            max-width: 100px;
            max-height: 100px;
        }

        .NTD_price {

        }

        .one_half {
            width: 48%;
            display: inline-block;
        }

        .one_third {
            width: 32%;
            display: inline-block;
        }

        .one_whole {
            width: 96%;
            display: inline-block;
        }

        .table_template {
            text-align: center;
        }

        .table_template thead th {
            background-color: #E0E0E0;
            padding: 10px;
            text-align: center;
        }

        .table_template tbody td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #E2E2E2;
        }

        .table_template tbody tr:nth-of-type(even) {
            background-color: #F7F7F7
        }

        .table_template .itembox .photo {
            width: 100px;
            height: 100px;
        }

        .btnbox {
            text-align: center;
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 80px;
        }

        .bodybox .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 100;
            display: none;
        }

        .qn_page {
            width: 1200px;
            height: 1697px;
            background-color: white;
            position: relative;
            margin-bottom: 80px;
        }



        .qn_page .qn_header {
            width: 100%;
            height: 327.14px;
            background: url('images/Feliix-QuoteBG-04.png');
            background-size: 100% auto;
            position: absolute;
            top: 0;
            left: 0;
            color: white;
        }

        .qn_header .left_block {
            width: 69.30%;
            float: left;
            padding-left: 57.14px;
            padding-right: 57.14px;
        }
                
        .qn_header .left_block .qn_title {
            margin-top: 75.78px;
            font-size: 40px;
            font-weight: 700;
            line-height: 1.1;
            height: 114.79px;
        }

        .qn_header .left_block .qn_title > div {
            height: 57.39px;
        }

        .qn_header .left_block .qn_by_for {
            margin-top: 20px;
            width: 100%;
        }

        .qn_header .left_block .qn_for {
            font-size: 20px;
            font-weight: 700;
            height: 84.32x;
            width: 348.69px;
            float: left;
            line-height: 1.2;
        }

        .qn_header .left_block .qn_for > div {
            width: 100%;
            overflow: hidden;
            height: 21.42px;
        }

        .qn_header .left_block .qn_by {
            font-size: 20px;
            font-weight: 700;
            height: 84.32px;
            width: 348.69px;
            float: right;
            line-height: 1.2;
        }

        .qn_header .left_block .qn_by > div {
            width: 100%;
            overflow: hidden;
            height: 21.42px;
        }

        .qn_header .right_block {
            width: 30.70%;
            float: right;
            padding-right: 57.14px;
        }

        .qn_header .right_block img.logo {
            display: block;
            width: 245.48px;
            margin-top: 75.78px;
            height: 53.58px;
        }

        .qn_header .right_block .project_category {
            font-size: 30px;
            font-weight: 500;
            line-height: 1.8;
            height: 58.94px;
        }

        .qn_header .right_block .qn_number_date {
            margin-top: 10px;
            height: 84.32px;
            font-size: 20px;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }

        .qn_header .right_block .qn_number_date span.qn_number_date_title {
            display: inline-block;
            width: 145px;
            font-weight: 700;
        }

        .qn_header .right_block .qn_number_date .qn_number,
        .qn_header .right_block .qn_number_date .qn_date {
            font-weight: 700;
        }

        .qn_header .font_M {
            font-family: 'SFPRODISPLAYMEDIUM';
        }

        .qn_header .font_B {
            font-family: 'SFPRODISPLAYBOLD';
        }




        .qn_page .qn_body {
            padding: 357.14px 30px 30px;
        }

        .qn_page .qn_footer {
            width: 100%;
            height: 107px;
            padding: 30px;
            position: absolute;
            bottom: 0;
            left: 0;
            background-color: white;
        }

        .qn_footer .foot_divider {
            border-top: 2px solid black;
            width: 100%;
            margin-bottom: 6px;
        }

        .qn_footer .line1 {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.1;
            height: 16.5px;
        }

        .qn_footer .line2 {
            font-size: 15px;
            height: 22.5px;
        }

        .qn_footer .qn_page_number {
            position: absolute;
            font-weight: 700;
            right: 45px;
            top: 45px;
        }

        .qn_body .area_conforme {
            width: 100%;
        }

        .area_conforme .conforme {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: -35px;
        }

        .area_conforme .client_signature, .area_conforme .company_signature {
            display: flex;
            justify-content: space-around;
        }

        .area_conforme .signature {
            text-align: center;
            padding-top: 20px;
            width: 280px;
        }

        .area_conforme .signature .pic {
            width: 230px;
            height: 140px;
            padding-bottom: 5px;
            text-align: center;
            vertical-align: bottom;
            display: table-cell;
        }

        .area_conforme .signature .name {
            font-weight: 700;
            border-top: 2px solid black;
            padding-top: 5px;
            margin-bottom: -3px;
        }

        .area_conforme .signature .line1, .area_conforme .signature .line2, .area_conforme .signature .line3 {
            height: 24px;
            margin-bottom: -3px;
        }

        .area_terms {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .area_terms > div.terms {
            display: inline-block;
            width: 48.2%;
            border: 1px solid #A0A0A0;
            margin: 10px;
        }

        .area_terms > div.terms:last-of-type {
            width: 100%;
        }

        .area_terms > div.terms:nth-of-type(even) {
            width: 48.2%;
        }

        .area_terms .terms .title {
            text-align: center;
            padding: 3px;
            border-bottom: 1px solid #A0A0A0;
            font-size: 18px;
            font-weight: 700;
        }

        .area_terms .terms .brief {
            text-align: center;
            padding: 3px;
            border-bottom: 1px solid #A0A0A0;
            font-size: 16px;
        }

        .area_terms .terms .listing {
            font-size: 14px;
            padding: 7px 7px 7px 14px;
            margin-bottom: 0;
        }

        .area_payment .tb_payment {
            width: calc(100% - 20px);
            margin: 10px;
        }

        .tb_payment td {
            text-align: left;
            padding: 5px 10px;
            border-right: 1px solid #A0A0A0;
            border-bottom: 1px solid #A0A0A0;
            height: 37px;
        }

        .tb_payment tbody tr td:first-of-type {
            border-left: 1px solid #A0A0A0;
            font-style: italic;
            vertical-align: top;
        }

        .tb_payment tbody tr:nth-of-type(1) td {
            border-top: 1px solid #A0A0A0;
        }

        .tb_payment tbody tr:nth-of-type(1) td:first-of-type {
            border-right: none;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) {
            padding: 5px 100px 5px 30px;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div {
            display: flex;
            justify-content: space-between;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div > span {
            position: relative;
        }

        .tb_payment tbody tr:nth-of-type(1) td:nth-of-type(2) > div > span::before {
            content: "";
            width: 20px;
            height: 20px;
            border: 1px solid black;
            display: inline-block;
            position: absolute;
            top: 2.5px;
            left: -25px;
        }

        .tb_payment tbody tr:nth-of-type(2) td {
            text-align: center;
            font-weight: 700;

        }

        .tb_payment tbody tr:nth-of-type(3) td {
            vertical-align: top;
            line-height: 1.8;
        }

        .tb_payment tbody tr:nth-of-type(3) td:first-of-type {
            text-align: center;
            width: 90px;
        }

        .tb_payment tbody tr:nth-of-type(3) td:nth-of-type(2) {
            width: 220px;
        }

        .acount_info > span:first-of-type {
            font-weight: 700;
            text-decoration: underline;
        }

        .acount_info > .first_line {
            display: inline-block;
        }

        .area_total .tb_total {
            width: 100%;
        }

        .tb_total td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_total tbody tr:nth-of-type(1) td:first-of-type {
            border-left: 2px solid #A0A0A0;
            font-style: italic;
            vertical-align: bottom;
            padding-left: 10px;
        }

        .tb_total tbody tr:nth-of-type(1) td {
            border-top: 2px solid #A0A0A0;
        }

        .tb_total tbody tr td span.numbers, .tb_total tfoot tr td span.numbers {
            font-weight: 800;
        }

        .tb_total tfoot td {
            color: red;
        }

        .tb_total tfoot tr td:nth-of-type(1) {
            border-left: 2px solid #A0A0A0;
            border-right: none;
            font-style: italic;
            padding-left: 10px;
        }

        .tb_total tbody tr td:nth-last-of-type(2), .tb_total tfoot tr td:nth-last-of-type(2) {
            font-weight: 800;
            text-align: center;
            width: 210px;
        }

        .tb_total tbody tr td:nth-last-of-type(1), .tb_total tfoot tr td:nth-last-of-type(1) {
            width: 210px;
            padding: 5px 15px;
            text-align: right;
        }

        .tb_total tfoot tr td:nth-last-of-type(1) span.numbers.deleted {
            text-decoration: line-through;
        }

        .qn_body .area_slogan {
            width: 100%;
            border: 2px solid #A0A0A0;
            padding: 16.61px 45px;
            position: relative;
        }

        .qn_body .area_slogan.no_top_border {
            border-top: none;
        }

        .area_slogan hr {
            background: #A42A82;
            height: 1px;
        }

        .area_slogan .slogan_text {
            color: #A42A82;
            font-weight: 700;
            font-size: 20px;
            text-align: center;
            position: absolute;
            width: 600px;
            background: white;
            top: 18px;
            left: calc(50% - 300px);
            font-family: 'SFPRODISPLAYBOLD';
        }

        .qn_body .area_subtotal {
            width: 100%;
        }

        .area_subtotal .tb_format1 {
            width: 100%;
            margin-bottom: 30px;
        }


        .tb_format1 th, .tb_format1 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format1 thead tr th:first-of-type,
        .tb_format1 tbody tr td:first-of-type,
        .tb_format1 tfoot tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format1 thead tr th.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format1 thead tr:nth-of-type(2) th {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }


        .tb_format1 tbody tr td img {
            max-height: 90px;
            max-width: 90px;
        }

        .tb_format1 tbody tr td.pic {
            width: 125px;
            text-align: center;
        }

        .tb_format1 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format1 tfoot tr td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format1 tfoot tr td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
            text-align: right;
            padding-right: 30px;
        }

        .tb_format1 tfoot tr td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
            padding: 5px 15px;
        }

        .tb_format1 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format1 tbody tr td div.pid, .tb_format1 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
        }

        .tb_format1 tbody tr td div.pid.deleted, .tb_format1 tbody tr td div.code.deleted {
            text-decoration: line-through;
            text-decoration-color: red;
        }

        .tb_format1 tbody tr td div.pid button.last_order_history {
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .tb_format1 tbody tr td div.moq {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
            color: red;
        }

        .tb_format1 tbody tr td div.brief {
            font-size: 16px;
            font-weight: 400;
        }

        .tb_format1 tbody tr td div.listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
        }

        .tb_format1 tbody tr td span.numbers {
            font-weight: 800;
            font-size: 14px;
        }

        .tb_format1 tbody tr td span.numbers.red {
            color: red;
        }

        .tb_format1 tbody tr td span.numbers.deleted {
            position: relative;
        }

        .tb_format1 tbody tr td span.numbers.deleted::before {
            content: "";
            width: 100%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .tb_format1 tbody tr td span.numbers.deleted span {
            position: absolute;
            color: white;
            background-color: red;
            padding: 0px 5px 1px;
            border-radius: 7px;
            top: 10px;
            left: -73px;
            font-size: 14px;
            font-weight: 500;
        }


        .tb_format1 thead tr:nth-of-type(2) td:nth-of-type(1), .tb_format1 tbody tr.desc1 td:nth-of-type(1) {
            width: 70px;
            text-align: center;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(3), .tb_format1 tbody tr.desc1 td:nth-last-of-type(3) {
            width: 75px;
            text-align: center;
            height: 50px;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(2),
        .tb_format1 tbody tr.desc1 td:nth-last-of-type(2) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1 thead tr:nth-of-type(2) td:nth-last-of-type(1),
        .tb_format1 tbody tr.desc1 td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1.vat thead tr:nth-of-type(2) td:nth-last-of-type(4), .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(4) {
            width: 75px;
            text-align: right;
            height: 50px;
        }

        .tb_format1.vat thead tr:nth-of-type(2) td:nth-last-of-type(3), .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(3) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1.vat thead tr:nth-of-type(2) td:nth-last-of-type(2), .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(2) {
            width: 135px;
            text-align: right;
            height: 50px;
        }

        .tb_format1 tbody tr.desc2 td {
            max-width: 495px;
        }

        .tb_format1.vat tbody tr.desc2 td {
            max-width: 630px;
        }

        .tb_format1 tbody tr.desc2 td div.notes {
            text-align: left;
            color: red;
            margin: 0 5px 8px;
            white-space: pre-line;
        }

        .tb_format1 tbody tr.desc2 td div.picbox {
            text-align: center;
            vertical-align: middle;
            margin-top: 8px;
        }


        .tb_format1 tbody tr.desc2 td div.picbox img {
            max-height: 180px;
            max-width: 220px;
            margin: 0 5px;
        }

        .area_subtotal .tb_format2 {
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_format2 th, .tb_format2 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format2 thead tr th:first-of-type,
        .tb_format2 tbody tr td:first-of-type,
        .tb_format2 tfoot tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format2 thead tr th.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format2 tfoot tr td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format2 tfoot tr td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tfoot tr td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format2 tbody tr td div.pid, .tb_format2 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
        }

        .tb_format2 tbody tr td div.pid.deleted, .tb_format2 tbody tr td div.code.deleted {
            text-decoration: line-through;
            text-decoration-color: red;
        }

        .tb_format2 tbody tr td div.pid button.last_order_history {
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .tb_format2 tbody tr td div.moq {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
            color: red;
        }

        .tb_format2 tbody tr td div.brief {
            font-size: 16px;
            font-weight: 400;
        }

        .tb_format2 tbody tr td div.listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
        }

        .tb_format2 tbody tr td span.numbers {
            font-weight: 800;
            font-size: 14px;
        }

        .tb_format2 tbody tr td span.numbers.red {
            color: red;
        }

        .tb_format2 tbody tr td span.numbers.deleted {
            position: relative;
        }

        .tb_format2 tbody tr td span.numbers.deleted::before {
            content: "";
            width: 100%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .tb_format2 tbody tr td span.numbers.deleted span {
            position: absolute;
            color: white;
            background-color: red;
            padding: 0px 5px 1px;
            border-radius: 7px;
            top: 10px;
            left: -73px;
            font-size: 14px;
            font-weight: 500;
        }

        .tb_format2 tbody tr td:nth-of-type(1) {
            width: 70px;
            text-align: center;
        }

        .tb_format2 tbody tr td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
        }

        .tb_format2 tfoot tr td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
            padding: 5px 15px;
        }

        .tb_format2 tfoot tr td:nth-last-of-type(2) {
            width: 135px;
            text-align: right;
            padding-right: 30px;
        }

        .pagebox {
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .pagebox .title_box {
            border-bottom: 1px solid black;
        }

        .pagebox .title_box ul {
            width: 100%;
            margin-bottom: 0;
            display: flex;
        }

        .pagebox .title_box ul li:nth-of-type(1) {
            width: 80%;
            padding: 3px 3px 3px 10px;
            font-weight: 700;
        }

        .pagebox .title_box ul li:nth-of-type(2) {
            border-left: 1px solid black;
            width: 20%;
            padding: 3px;
            text-align: center;
        }

        .pagebox .title_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            cursor: pointer;
        }

        .pagebox .function_box {
            padding: 10px 10px 15px 10px;
        }

        .pagebox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .pagebox .content_box {
            padding: 0 10px 10px 10px;
        }

        .pagebox .content_box ul {
            width: 100%;
            border-bottom: none;
            display: flex;
            margin-bottom: 0;
            align-items: center;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .pagebox .content_box ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .pagebox .content_box ul li:nth-of-type(1) {
            width: 75%;
            padding: 3px 3px 10px 10px;
            line-height: 2.5;
            border-right: 1px solid black;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 272px;
        }

        .pagebox .content_box ul li:nth-of-type(1) input[type='checkbox'] {
            display: inline-block;
            margin-left: 20px;
        }

        .pagebox .content_box ul li:nth-of-type(2) {
            width: 25%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .pagebox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #header_access, #header_dialog, #footer_dialog, #total_dialog {
            zoom: 85%;
        }

        #page_dialog, #subtotal_dialog, #terms_dialog, #payment_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 85%;
        }

        #signature_dialog {
            min-width: 700px;
            pointer-events: auto;
            zoom: 85%;
        }

        #total_dialog {
            pointer-events: auto;
        }

        #page_dialog h6 {
            margin-bottom: 15px;
        }

        #page_dialog h6 a.add_page {
            background-image: url(images/ui/file-plus.svg);
            width: 30px;
            height: 30px;
            float: right;
            text-decoration: none;
            border-bottom: none;
        }

        #page_dialog .page_form, #subtotal_dialog .subtotalbox, #terms_dialog .termsbox {
            max-height: 400px;
            overflow-y: auto;
        }

        #payment_dialog .termsbox {
            max-height: 300px;
            overflow-y: auto;
        }

        .subtotalbox {
            margin-bottom: 5px;
        }

        .subtotalbox .title_box {
            border: 1px solid black;
            padding: 7px;
            font-weight: 700;
        }

        .subtotalbox .function_box {
            padding: 10px 10px 15px 10px;
        }

        .subtotalbox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .subtotalbox .content_box {
            padding: 0 10px 10px 10px;
        }

        .subtotalbox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .subtotalbox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 95px;
            padding-right: 5px;
            text-align: right;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 105px;
            margin: 5px 10px 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(1) textarea {
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            resize: none;
            margin: 5px 0;
        }

        .subtotalbox .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .subtotalbox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        .subtotalbox .content_box .itembox {
            display: inline-block;
            margin: 5px 90px 5px 0;
        }

        .subtotalbox .content_box .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
            padding: 3px;
            position: relative;
        }

        .subtotalbox .content_box .itembox .photo::before {
            content: "+";
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .subtotalbox .content_box .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .subtotalbox .content_box .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .subtotalbox .content_box .itembox.chosen .photo::before {
            content: none;
        }

        .subtotalbox .content_box .itembox .photo > div {
            display: none;
        }

        .subtotalbox .content_box .itembox.chosen .photo > div {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 28px;
            text-align: center;
            color: #EA0029;
            font-size: 24px;
            font-weight: 400;
            cursor: pointer;
            position: absolute;
            top: 18px;
            right: -50px;
        }

        .subtotalbox .subtotal_image{
            display: flex;
            align-items: center;
            margin: 3px 0;
        }

        .subtotalbox .subtotal_image > span{
            margin-right: 5px;
        }

        #terms_dialog .formbox dl {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #terms_dialog .formbox dl dd select {
            width: 370px;
        }

        .termsbox {
            margin: 10px 0 5px;

        }

        .termsbox .function_box {
            padding: 5px 10px 10px 10px;
        }

        .termsbox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .termsbox .content_box {
            padding: 0 10px 10px 10px;
        }

        .termsbox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .termsbox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .termsbox .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .termsbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 75px;
            padding-right: 5px;
            text-align: right;
        }

        .termsbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            margin: 5px 0;
        }

        .termsbox .content_box ul li:nth-of-type(1) textarea {
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 110px);
            resize: none;
            margin: 5px 0;
        }

        .termsbox .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .termsbox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #payment_dialog .formbox dl:first-of-type {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #payment_dialog .formbox dl:nth-of-type(2) {
            margin-bottom: 0px;
            padding: 0 10px;
        }

        #payment_dialog .formbox dl dd select {
            width: 370px;
        }

        #payment_dialog .termsbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 120px;
            padding-right: 5px;
            text-align: right;
        }

        #payment_dialog .termsbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 155px);
            margin: 5px 0;
        }


        .signaturebox {
            margin-bottom: 5px;
        }

        .signaturebox .title_box {
            border: 1px solid black;
            padding: 7px;
            font-weight: 700;
        }

        .signaturebox .function_box {
            padding: 8px 10px 8px 0px;
        }

        .signaturebox .content_box {
            padding: 0 10px 10px 10px;
        }

        .signaturebox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .signaturebox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .signaturebox .content_box ul li:nth-of-type(1) {
            width: 80%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .signaturebox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 95px;
            padding-right: 5px;
            text-align: right;
        }

        .signaturebox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 0;
        }

        .signaturebox .content_box ul li:nth-of-type(2) {
            width: 20%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .signaturebox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        .signaturebox .content_box .itembox {
            display: inline-block;
            margin: 5px 0;
        }

        .signaturebox .content_box .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
            padding: 3px;
            position: relative;
        }

        .signaturebox .content_box .itembox .photo::before {
            content: "+";
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .signaturebox .content_box .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .signaturebox .content_box .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .signaturebox .content_box .itembox.chosen .photo::before {
            content: none;
        }

        .signaturebox .content_box .itembox .photo > div {
            display: none;
        }

        .signaturebox .content_box .itembox.chosen .photo > div {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 28px;
            text-align: center;
            color: #EA0029;
            font-size: 24px;
            font-weight: 400;
            cursor: pointer;
            position: absolute;
            top: 18px;
            right: -50px;
        }

        #signature_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #signature_dialog .formbox dl dd select {
            width: 310px;
        }

        .functionbar{
            position: fixed;
            z-index: 998;
            width: 100%;
            background: rgb(230, 230, 230);
            padding: 80px 12px 0;
        }

        .list_function.main {
            border-color: #00811e;
        }

        .list_function.main .block.fn a {
            border-bottom-color: rgb(230, 230, 230);
        }

        .list_function.main a.print,
        .list_function.main a.specification,
        .list_function.main a.export_excel,
        .list_function.main a.approvalform,
        .list_function.main a.export_soa,
        .list_function.main a.driver_computation,
        .list_function.main a.cost_analysis {
            width: 30px;
            height: 30px;
            background-color: #00811e;
            position: relative;
        }

        .list_function.main a.print.purple {
            background-color: mediumpurple;
        }

        .list_function.main a.print::after {
            content: " ";
            background: url(images/ui/btn_print.svg);
            background-size: 45px 45px;
            width: 45px;
            height: 45px;
            position: absolute;
            top: -7px;
            left: -7px;
        }

        .list_function.main a.specification::after {
            content: " ";
            background: url(images/ui/btn_specification.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 3px;
            left: 4px;
        }

        .list_function.main a.export_excel::after {
            content: " ";
            background: url(images/ui/btn_export_excel.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 0.5px;
            left: 0.5px;
            zoom: 130%;
        }

        .list_function.main a.approvalform::after {
            content: " ";
            background: url(images/ui/btn_approvalform.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 3px;
            left: 4px;
        }

        .list_function.main a.export_soa::after {
            content: " ";
            background: url(images/ui/btn_soa.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 3px;
            left: 4px;
        }

        .list_function.main a.driver_computation::after {
            content: " ";
            background: url(images/ui/btn_driver_computation.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            zoom: 1.3;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 0.5px;
            left: 0;
        }

        .list_function.main a.cost_analysis::after {
            content: " ";
            background: url(images/ui/btn_calculator.svg);
            background-size: 22px 22px;
            background-repeat: no-repeat;
            width: 45px;
            height: 45px;
            position: absolute;
            top: 3px;
            left: 4px;
        }

        .list_function.main a.print:hover,
        .list_function.main a.specification:hover,
        .list_function.main a.export_excel:hover,
        .list_function.main a.approvalform:hover,
        .list_function.main a.export_soa:hover,
        .list_function.main a.driver_computation:hover,
        .list_function.main a.cost_analysis:hover {
            background-color: #707071;
        }

        .modal .modal_function .left_function {
            width: 90%;
            margin-right: 20px;
        }

        .modal .modal_function input[type='text'] {
            height: 38px;
            border: 1px solid #707070;
            font-size: 16px;
            width: 280px;
            margin: 5px 20px 5px 0;
        }

        .modal .modal_function select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 16px;
            height: 38px;
            width: 280px;
        }

        .modal .modal_function select:nth-of-type(2) {
            width: 350px;
        }

        .modal .modal_function .left_function > input[type='checkbox']{
            margin-left: 6px;
            margin-right: -3px;
        }

        .modal .modal_function > a.btn {
            margin-left: 10px;
            color: #FFF !important;
        }

        .modal .modal_function button.btn.dropdown-toggle {
            background-color: white;
            border: 1px solid #999;
            border-radius: 0;
        }

        .modal .modal_function ul.dropdown-menu.inner li {
            display: block;
            border-right: none;
        }

        .modal .modal_function .dropdown-menu > .bs-searchbox > input[type='search'] {
            border: 1px solid #ced4da;
            font-size: 14px;
        }

        .upper_section {
            margin: 20px 20px 0;
            border: 2px solid rgb(225, 225, 225);
            display: flex;
        }

        .imagebox {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .imagebox .selected_image {
            padding: 20px;
            text-align: center;
            width: 300px;
            height: 300px;
        }

        .imagebox .selected_image img {
            object-fit: contain;
            width: 100%;
            height: 100%;
        }

        .imagebox .image_list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 0 10px 20px 10px;
        }

        .imagebox .image_list img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin: 5px 10px;
            cursor: pointer;
            border: 2px solid #ced4da;
        }

        .imagebox .image_list img:hover {
            border-color: #F0502F;
        }

        .infobox {
            width: 50%;
        }

        .infobox .basic_info {
            border-bottom: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            padding: 30px 20px 5px;
        }

        .infobox .basic_info div.last_order_history button {
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .infobox .basic_info div.last_order_history span {
            font-size: 16px;
            font-weight: 500;
            color: red;
        }

        .infobox .basic_info div.last_order_history span a {
            color: blue;
        }

        .infobox .basic_info span.phasedout {
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .infobox .basic_info span.phasedout1{
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
            cursor: pointer;
        }

        .middle_section span.phasedout2{
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 2px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .phasedout2:hover{
            cursor: pointer;
        }

        .infobox .basic_info div.tags {
            margin-bottom: 0.5rem;
        }

        .infobox .basic_info div.tags span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
            margin: 0 3px;
        }

        .infobox .basic_info div.tags span:first-of-type {
            margin-left: 0;
        }

        .infobox .basic_info div.tags span:last-of-type {
            margin-right: 0;
        }

        .infobox .price_stock {
            border-bottom: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            padding: 15px 20px;
        }

        .infobox .price_stock > li {
            color: rgb(83, 132, 155);
            font-size: 18px;
            font-weight: 600;
            padding: 8px 0;
        }

        .infobox .price_stock > li span:nth-of-type(1) {
            font-size: 22px;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .price_stock > li span:nth-of-type(2) {
            font-size: 16px;
            font-weight: 400;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .variants {
            margin-left: 20px;
            padding: 0 20px;
        }

        .infobox .variants li {
            color: #212529;
            font-size: 16px;
            margin-left: 15px;
        }

        .infobox .variants li:nth-of-type(odd) {
            margin-bottom: 15px;
        }

        .infobox .variants li:nth-of-type(1) {
            font-weight: 700;
            margin-left: 0;
            margin-bottom: 5px;
        }

        .infobox .variants .dropdown-menu li {
            margin-left: 0;
        }

        .infobox .variants .btn-light {
            background-color: #fff;
            border: 1px solid #ced4da;
            outline: none;
        }

        .infobox .variants .btn-light:focus {
            outline: none !important;
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
        }

        .infobox .btnbox ul {
            width: 100%;
            display: flex;
            justify-content: space-evenly;
        }

        .infobox .btnbox ul li > button {
            width: 230px;

        }

        div.upper_section.product_set {
            background: rgba(255,255,0,0.1);
        }

        div.upper_section.product_set .infobox .basic_info {
            border-bottom: none;
        }

        div.upper_section.product_set .infobox .product_set_desc {
            font-size: 16px;
            margin-left: 22px;
            padding: 0 20px 15px;
        }

        div.upper_section.product_set .infobox .product_set_desc span {
            font-weight: 500;
        }

        .middle_section {
            margin: 0 20px;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
        }

        .middle_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .middle_section table {
            margin: 5px 20px;
            width: calc(100% - 40px);
        }

        .middle_section tbody tr:nth-of-type(n+2) {
            border-top: 1px solid rgb(225, 225, 225);
        }

        .middle_section tbody tr td:nth-of-type(odd) {
            color: #B3B3B3;
            padding: 10px;
            width: 20%;
        }

        .middle_section tbody tr td:nth-of-type(even) {
            width: 30%;
        }

        .lower_section {
            margin: 0 20px 20px;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
            text-align: left;
        }

        .lower_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .lower_section p {
            margin: 15px 20px;
        }

        .lower_section .desc_imgbox {
            width: 100%;
            padding: 0 15px;
            margin: 10px 0 20px;
        }

        .lower_section .desc_imgbox img {
            width: calc(50% - 8px);
            margin: 5px 0;
        }

        .lower_section .desc_imgbox img:nth-of-type(odd) {
            margin-right: 10px;
        }


        .row.custom {
            margin: 5px 0 0 0;
        }

        .col.custom {
            width: 24%;
            padding-left: 5px;
            padding-right: 5px;
            text-align: center;
        }

        .col.custom > img {
            height: 150px;
            width: 150px;
            object-fit: contain;
        }

        .col.custom > div > a {
            text-decoration: none;
            color: blue;
            cursor: pointer;
            font-size: 16px;
            padding: 5px 10px;
        }

        .carousel-control-next, .carousel-control-prev {
            opacity: 0.7;
            top: 35%;
            width: 4%;
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5L4.25 4l2.5-2.5L5.25 0z'/%3e%3c/svg%3e") !important;
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5L3.75 4l-2.5 2.5L2.75 8l4-4-4-4z'/%3e%3c/svg%3e") !important;
        }

        .list_function .sort_block {
            float: left;
        }

        .list_function .sort_block a.btn.green {
            color: white;
            margin: 0 5px;
        }

        #modal_product_catalog tbody td ul.supporting_attachment {
            margin-top: 2px;
        }

        #modal_product_catalog tbody td ul.supporting_attachment li:nth-of-type(1) {
            padding: 1px 0;
        }

        #modal_product_catalog tbody td ul.supporting_attachment span {
            background-color: orange;
            color: #fff;
            font-size: 13px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            margin: 3px 0px;
            padding: 1px 10px 2px;
            cursor: pointer;
            border: none;
        }

        #modal_product_catalog tbody td ul.supporting_attachment span + span {
            margin-left: 5px;
        }

        #modal_product_catalog tbody td ul.supporting_attachment li ~ li {
            padding-left: 5px;
        }

        #modal_product_catalog tbody td ul.last_order_history button {
            font-size: 14px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-left: 3px;
            padding: 0 5px 3px;
            border-radius: 10px;
        }

        #modal_product_catalog tbody td div.phasedout_variant {
            text-align: left;
            color: red;
            font-size: 16px;
            font-weight: 600;
            padding: 5px 0 0 3px;
        }

        #modal_product_catalog tbody td div.phasedout_variant button {
            font-size: 14px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-left: 3px;
            padding: 0 5px 3px;
            border-radius: 10px;
        }

        #modal_product_catalog tbody td div.phasedout_variant button:focus {
            outline-color: transparent!important;
        }

        #tb_product_list ul li:nth-of-type(1) span.phasedout_replacement {
            background-color: orange;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
            cursor: pointer;	
        }

        #progress-bar-container {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 30px;
            display: none;  /* Initially hidden */
        }
        #progress-bar {
            width: 0;
            height: 100%;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 30px;
            border-radius: 5px;
        }

        #export_pdf {
            display: none;
        }

        #modal_driver_computation .modal_function > a.btn {
            margin-left: 0;
        }

        #modal_driver_computation .modal-footer {
            padding: 0;
        }

        #modal_driver_computation .modal-footer a.btn {
            color: white;
            width: 110px;
            margin-left: 12px;
            margin-right: 12px;
        }

        #modal_driver_computation .modal-footer a.btn.gray {
            background-color: gray;
        }


        #tb_driver_computation {
            width: 100%;
            margin-top: 10px;
        }

        #tb_driver_computation tr th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_driver_computation tr th, #tb_driver_computation tr td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        #tb_driver_computation tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_driver_computation tr td input {
            border: 2px solid #ccc;
            text-align: center;
        }

        #tb_driver_computation tr td textarea {
            border: 2px solid #ccc;
            resize: none;
        }

        #tb_driver_computation tr td select {
            border: 2px solid #ccc;
            width: 200px;
        }

        #tb_driver_computation tr td select ~ select {
            margin-top: 5px;
        }

        #tb_driver_computation tr td:nth-of-type(1) input[type="text"] {
            width: 80px;
        }

        #tb_driver_computation tr td:nth-of-type(3) input[type="number"] {
            width: 90px;
        }

        #tb_driver_computation tr td:nth-of-type(4) input[type="number"] {
            width: 90px;
        }

        #tb_driver_computation tr td:nth-of-type(5) input[type="number"] {
            width: 100px;
        }

        #tb_driver_computation tr td:nth-of-type(6),
        #tb_driver_computation tr td:nth-of-type(7),
        #tb_driver_computation tr td:nth-of-type(8) {
            width: 120px;
        }

        #tb_driver_computation tr td:nth-of-type(10) input[type="number"] {
            width: 90px;
        }

        #tb_driver_computation tr td:nth-of-type(11) {
            width: 100px;
            font-size: 20px;
        }

        @media screen and (max-width: 1050px) {

            #export_pdf {
                display: inline-block;
            }
        }

        @media screen and (max-width: 640px) {
            .functionbar {
                padding-top: 180px;
            }

            body.gray .mainContent {
                padding-top: 325px;
            }
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
            }

            .mainContent {
                padding: 0 !important;
                background-color: #FFF !important;
            }

            .qn_page {
                zoom: 82%;
                margin: 1px 0px 0px 7px;
                page-break-after: always;
                overflow-y: hidden;
            }

            .noPrint {
                display: none;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <div class="mask" :ref="'mask'"></div>

    <div id="progress-bar-container">
        <div id="progress-bar">0%</div>
    </div>

    <!-- header -->
    <header class="noPrint">header</header>
    <!-- header end -->

    <!-- Function Bar start-->
    <div class="functionbar noPrint">
        <div class="list_function main">

            <div class="block">
                <!-- print -->
                <div class="popupblock">
                    <a id="" class="print" title="Export Whole Quotation into PDF" @click="print_page()"></a>
                </div>

                <div class="popupblock">
                    <a id="export_pdf" class="print purple" title="Export Whole Quotation into PDF" @click="export_pdf()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="specification" title="Export Specification Sheet into PDF" @click="specification_sheet()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="export_excel" title="Export Simple Item List into Excel" @click="export_excel()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="approvalform" title="Generate Corresponding Approval Form" @click="approval_form_post()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="export_soa" title="Generate Corresponding Statement of Account" @click="soa_post()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="driver_computation" title="LED Driver Computation" @click="driver_compute()"></a>
                </div>

                <!-- 只有當使用者是 Dennis Lin 或 Ariel Lin 或 testmanager 時，下方這個 <div> 才會被建立出來，不要使用 display: none; 的方式 -->
                <?php
                if($username == 'Dennis Lin' || $username == 'Ariel Lin' || $username == 'testmanager')
                {
                ?>
                <div class="popupblock">
                    <a id="" class="cost_analysis" @click="cost_analysis()"></a>
                </div>
                <?php
                }
                ?>

            </div>

            <div class="block fn" id="menu">
                <div class="popupblock" v-if="quotation_control && project_category == 'Office Systems' ">

                    <a id="status_fn1" class="fn1" :ref="'a_fn1'" @click="show_access = !show_access">Access</a>

                    <div id="access_dialog" class="dialog fn1 show" :ref="'dlg_fn1'" v-show="show_access">
                        <h6>Access</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Can be Viewed?</dt>
                                <dd>
                                    <select v-model="temp_can_view">
                                        <option value="">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </dd>
                                <!-- <dt>Can be Duplicated?</dt>
                                <dd>
                                    <select v-model="temp_can_duplicate">
                                        <option value="">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </dd> -->

                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_access()">Close</a>
                                    <a class="btn small green" @click="save_access()">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>



                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">
                    <?php
                if ($test_manager[0]  == "1")
                {
                ?>
                    <a id="status_fn1" class="fn1" :ref="'a_fn1'" @click="show_header = !show_header">Header</a>
                    <?php
                } else {
                ?>
                    <a>Header</a>
                    <?php
                }
                ?>
                    <div id="header_dialog" class="dialog fn1 show" :ref="'dlg_fn1'" v-show="show_header">
                        <h6>Header</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Quotation Title:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_second_line">
                                </dd>
                                <dt>Project Category:</dt>
                                <dd>
                                    <select v-model="temp_project_category">
                                        <option value="Lighting">Lighting</option>
                                        <option value="Office Systems">Office Systems</option>
                                    </select>
                                </dd>
                                <dt>Quotation Number:</dt>
                                <dd>
                                    <input type="text" v-model="temp_quotation_no">
                                </dd>
                                <dt>Quotation Date:</dt>
                                <dd>
                                    <input type="date" v-model="temp_quotation_date">
                                </dd>
                                <dt>Prepare for:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_prepare_for_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_prepare_for_second_line">
                                    <input type="text" placeholder="Third Line" v-model="temp_prepare_for_third_line">
                                </dd>
                                <dt>Prepare by:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line" v-model="temp_prepare_by_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_prepare_by_second_line">
                                    <input type="text" placeholder="Third Line" v-model="temp_prepare_by_third_line">
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_header()" v-if="submit == false">Close</a>
                                    <a class="btn small green" @click="save_header()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">
                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="status_fn1" class="fn1" :ref="'a_fn1'" @click="show_footer = !show_footer">Footer</a>
                    <?php
                    } else {
                    ?>
                    <a>Footer</a>
                    <?php
                    }
                    ?>
                    <div id="footer_dialog" class="dialog fn1 show" :ref="'dlg_fn1'" v-show="show_footer">
                        <h6>Footer</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">First Line (bold font):</dt>
                                <dd>
                                    <input type="text" v-model="temp_footer_first_line">
                                </dd>
                                <dt>Second Line:</dt>
                                <dd>
                                    <input type="text" v-model="temp_footer_second_line">
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_footer()" v-if="submit == false">Close</a>
                                    <a class="btn small green" @click="save_footer()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_page = !show_page">Page</a>
                    <?php
                    } else {
                    ?>
                    <a>Page</a>
                    <?php
                    }
                    ?>
                    <div id="page_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_page">
                        <h6>Page
                            <a class="add_page" @click="add_page()"></a>
                        </h6>

                        <div class="page_form">

                            <div class="pagebox" v-for="(page, page_index) in temp_pages">

                                <div class="title_box">
                                    <ul>
                                        <li>Page {{page_index + 1}}</li>
                                        <li><i class="fas fa-arrow-alt-circle-up"
                                               @click="page_up(page_index, page.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="page_down(page_index, page.id)"></i>
                                            <i class="fas fa-trash-alt" @click="page_del(page.id)"></i>
                                        </li>
                                    </ul>
                                </div>
                                <div class="function_box">
                                    <select :id="'block_type_' + page.id">
                                        <option value="A">Type-A Subtotal Block</option>
                                        <option value="B">Type-B Subtotal Block</option>
                                    </select>
                                    <a class="btn small green" @click="add_item(page.id)">Add</a>
                                </div>
                                <div class="content_box">

                                    <ul v-for="(block, block_index) in page.types">
                                        <li>
                                            Type-{{block.type}} Subtotal Block<br>
                                            Name: <input type="text" v-model="block.name"><br>
                                            Subtotal Amount: <input type="number" v-model="block.real_amount"> <input
                                                type="checkbox" class="alone" value="1" v-model="block.not_show"> Not
                                            Show "Subtotal Amount"
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="set_up(page.id, block_index, block.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="set_down(page.id, block_index, block.id)"></i>
                                            <i class="fas fa-file-upload"
                                               @click="set_up_page(page.id, page_index, block_index, block.id)"></i>
                                            <i class="fas fa-file-download"
                                               @click="set_down_page(page.id, page_index, block_index, block.id)"></i>
                                            <!--
                                            <i class="fas fa-save" @click="page_save()"></i>
                                            -->


                                            <i class="fas fa-copy" @click="page_copy(page.id, block.id)"></i>

                                            <i class="fas fa-trash-alt" @click="del_block(page.id, block.id)"></i>
                                        </li>
                                    </ul>


                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="show_page = false"  v-if="submit == false">Close</a>
                                <a class="btn small green" @click="page_save_pre()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_subtotal = !show_subtotal">Subtotal</a>
                    <?php
                    } else {
                    ?>
                    <a>Subtotal</a>
                    <?php
                    }
                    ?>
                    <div id="subtotal_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_subtotal">
                        <h6>Subtotal</h6>

                        <div class="tablebox s2 edit"
                             style="padding-bottom: 3px; border-bottom: 1px solid black; margin-bottom: 12px;">
                            <ul>
                                <li class="head" style="width: 160px;">Choose Subtotal Block:</li>
                                <li class="mix">
                                    <select v-model="block_value">
                                        <option v-for="(block, index) in block_names" :value="block">{{ block.name }}
                                        </option>

                                    </select>

                                    <a class="btn small green" @click="load_block()">Load</a>
                                </li>
                            </ul>
                        </div>


                        <div class="subtotalbox Type-A" v-if="edit_type_a">

                            <div class="title_box">
                                {{block_value.name}}
                            </div>

                            <div class="function_box">
                                <select id="with_image">
                                    <option value="image">Item with Image</option>
                                    <option value="noimage">Item without Image</option>
                                </select>
                                <a class="btn small green" @click="add_block_a()">Add</a>
                                <a class="btn small green" @click="product_catalog_a()">Product Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(block, index) in temp_block_a">
                                    <li>
                                        <span>No.:</span> <input style="width: 95px;" type="text" v-model="block.num">
                                        <input type="text" v-model="block.pid" hidden><br>
                                        <span>Code:</span> <input type="text" v-model="block.code"><br>

                                        <div class="subtotal_image" v-if="block.type == 'image' ">
                                            <span>Image:</span>
                                            <div :class="['itembox', (block.url !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :id="'block_image_' + block.id + '_1'" :name="'block_image_' + block.id + '_1'" @change="onFileChangeImage($event, block.id, 1)">
                                                    <img v-if="block.url" :src="block.url"/>
                                                    <div @click="clear_photo(block.id, 1)">x</div>
                                                </div>
                                            </div>

                                            <div :class="['itembox', (block.url2 !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :id="'block_image_' + block.id + '_2'" :name="'block_image_' + block.id + '_2'"  @change="onFileChangeImage($event, block.id, 2)">
                                                    <img v-if="block.url2" :src="block.url2"/>
                                                    <div @click="clear_photo(block.id, 2)">x</div>
                                                </div>
                                            </div>

                                            <div :class="['itembox', (block.url3 !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :id="'block_image_' + block.id + '_3'" :name="'block_image_' + block.id + '_3'"  @change="onFileChangeImage($event, block.id, 3)">
                                                    <img v-if="block.url3" :src="block.url3"/>
                                                    <div @click="clear_photo(block.id, 3)">x</div>
                                                </div>
                                            </div>
                                        </div>

                                        <br v-if="block.type == 'image' ">
                                        <span>Qty:</span> <input type="number" min="1" step="1" v-model="block.qty"
                                                                 @change="chang_amount(block)" oninput="this.value|=0">
                                        Product Price: <input type="number" v-model="block.price"
                                                              @change="chang_amount(block)">

                                        Ratio: <input type="number" v-model="block.ratio"
                                                              @change="chang_amount(block)">
                                        <br>
                                        <span>Discount: </span> <input type="number" v-model="block.discount" min="0" max="100"
                                                         @change="chang_amount(block)" oninput="this.value|=0"> Amount:
                                        <input type="number" v-model="block.amount" @change="chang_my_amount(block)"><br>
                                        <span>Description:</span> <textarea rows="2"
                                                                            v-model="block.desc"></textarea><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="block.list"></textarea><br>
                                        <!-- 只有subtotal box Type-A 而且是 Item with Image，才需要顯示下面這個 Notes 欄位出來 -->
                                        <span v-if="block.type == 'image'">Notes:</span> <textarea rows="2" v-model="block.notes" v-if="block.type == 'image'"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_a_up(index, block.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="block_a_down(index, block.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_a_del(block.id)"></i>
                                    </li>
                                </ul>


                            </div>
                        </div>


                        <div class="subtotalbox Type-B" v-if="edit_type_b">

                            <div class="title_box">
                                {{block_value.name}}
                            </div>

                            <div class="function_box">
                                <a class="btn small green" @click="add_block_b()">Add Item</a>
                                <a class="btn small green" @click="product_catalog_b()">Product Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(block, index) in temp_block_b">
                                    <li>
                                        <span>No.:</span> <input style="width: 95px;" type="text" v-model="block.num">
                                        <input type="text" v-model="block.pid" hidden><br>
                                        <span>Code:</span> <input type="text" v-model="block.code"><br>
                                        <span>Price:</span> <input type="number" v-model="block.price"
                                                                   @change="chang_discount(block)">
                                        Ratio: <input
                                            type="number" v-model="block.ratio" @change="chang_discount(block)">
                                        Discount: <input
                                            type="number" v-model="block.discount" @change="chang_discount(block)"
                                            min="0" max="100" oninput="this.value|=0"> Amount: <input type="number"
                                                                                                      v-model="block.amount" @change="chang_my_amount(block)"><br>
                                        <!-- <span>Discount:</span> <input type="number" v-model="block.discount" @change="chang_discount(block)" min="0" max="100" oninput="this.value|=0"> Amount: <input type="number" v-model="block.amount"><br> -->
                                        <span>Description:</span> <textarea rows="2"
                                                                            v-model="block.desc"></textarea><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="block.list"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_b_up(index, block.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="block_b_down(index, block.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_b_del(block.id)"></i>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="subtotal_close()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="subtotal_save()" v-if="is_load">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_total = !show_total">Total</a>
                    <?php
                    } else {
                    ?>
                    <a>Total</a>
                    <?php
                    }
                    ?>
                    <div id="total_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_total">
                        <h6>Total</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of total locates at:</dt>
                                <dd>
                                    <select v-model="total.page">
                                        <option value="0"></option>
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>

                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Discount:</dt>
                                <dd>
                                    <input type="number" v-model="total.discount" min="0" max="100" step="1"
                                           oninput="this.value|=0" @change="change_total_amount(total)">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">12% VAT:</dt>
                                <dd>
                                    <select v-model="total.vat" @change="change_total_amount(total)">
                                        <option value="P">Yes (12% VAT is shown in each individual product)</option>
                                        <option value="Y">Yes (12% VAT is shown in the block of Total)</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Show "*price inclusive of VAT" or "*price exclusive of VAT" or nothing in the Quotation:</dt>
                                <dd>
                                    <select v-model="total.show_vat">
                                        <option value="">Nothing</option>
                                        <option value="Y">*price inclusive of VAT</option>
                                        <option value="E">*price exclusive of VAT</option>
                                    </select>

                                    <!-- 現存的報價單，如果值是 Yes，則會對應成 *price inclusive of VAT；如果值是 No，則會對應成 Nothing
                                    <select v-model="total.show_vat">
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                    -->
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Quotation Valid for:</dt>
                                <dd>
                                    <input type="text" v-model="total.valid"
                                           placeholder="Input like 1 month, 45 days, ...">
                                </dd>
                            </dl>


                            <!-- 系統會先自動算出折扣後加稅後的總價，但使用者還是可以針對總價做後續修改(例如取整數等) -->
                            <dl>
                                <dt class="head">System Computed Grand Total:</dt>
                                <dd>
                                    <input type="number" v-model="total.real_total" style="opacity: 0.6;" disabled>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Manual Assigned Grand Total:</dt>
                                <dd>
                                    <input type="number" v-model="total.total">
                                </dd>
                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="close_total()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="save_total()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_slogan = !show_slogan">Slogan</a>
                    <?php
                    } else {
                    ?>
                    <a>Slogan</a>
                    <?php
                    }
                    ?>
                    <div id="slogan_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_slogan">
                        <h6>Slogan</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of slogan locates at:</dt>
                                <dd>
                                    <select v-model="slogan.page">
                                        <option value="0"></option>
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>

                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Need the top border for the block of slogan:</dt>
                                <dd>
                                    <select v-model="slogan.border">
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="close_slogan()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="save_slogan()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>
                

                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_term = !show_term">Terms and
                        Condition</a>
                    <?php
                    } else {
                    ?>
                    <a>Terms and Conditions</a>
                    <?php
                    }
                    ?>
                    <div id="terms_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_term">
                        <h6>Terms and Conditions</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of terms and conditions locates at:</dt>
                                <dd>
                                    <select v-model="term.page">
                                        <option value="0"></option>
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>
                        </div>


                        <div class="termsbox">
                            <div class="function_box">
                                <a class="btn small green" @click="add_term_item()">Add Item</a>
                            </div>

                            <div class="content_box">
                                <ul v-for="(item, index) in term.item">
                                    <li>
                                        <span>Title:</span> <input type="text" v-model="item.title"><br>
                                        <span>Brief:</span> <input type="text" v-model="item.brief"><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="item.list"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="term_item_up(index, item.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="term_item_down(index, item.id)"></i>
                                        <i class="fas fa-trash-alt" @click="term_item_del(index)"></i>
                                    </li>
                                </ul>


                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_term()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="term_save()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_payment_term = !show_payment_term">Payment
                        Terms</a>
                    <?php
                    } else {
                    ?>
                    <a>Payment Terms</a>
                    <?php
                    }
                    ?>
                    <div id="payment_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_payment_term">
                        <h6>Payment Terms</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of payment terms locates at:</dt>
                                <dd>
                                    <select v-model="payment_term.page">
                                        <option value="0"></option>
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Payment Method:</dt>
                                <dd>
                                    <!-- <input type="text" value="Cash; Cheque; Credit Card; Bank Wiring;"> -->

                                    <input type="text" v-model="payment_term.payment_method">
                                </dd>

                                <dt class="head">Brief:</dt>
                                <dd>
                                    <input type="text" v-model="payment_term.brief">
                                </dd>
                            </dl>
                        </div>

                        <div class="termsbox">
                            <div class="function_box">
                                <a class="btn small green" @click="add_payment_term_item()">Add Account</a>
                            </div>

                            <div class="content_box">
                                <ul v-for="(item, index) in payment_term.item">
                                    <li>
                                        <span>Bank Name:</span> <input type="text" v-model="item.bank_name"><br>
                                        <span>First Line:</span> <input type="text" v-model="item.first_line"><br>
                                        <span>Second Line:</span> <input type="text" v-model="item.second_line"><br>
                                        <span>Third Line:</span> <input type="text" v-model="item.third_line"><br>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up"
                                           @click="payment_term_item_up(index, item.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down"
                                           @click="payment_term_item_down(index, item.id)"></i>
                                        <i class="fas fa-trash-alt" @click="payment_term_item_del(index)"></i>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_payment_term()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="payment_term_save()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="(quotation_control && project_category == 'Office Systems') || project_category != 'Office Systems' ">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'"
                       @click="show_signature = !show_signature">Signature</a>
                    <?php
                    } else {
                    ?>
                    <a>Signature</a>
                    <?php
                    }
                    ?>
                    <div id="signature_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_signature">
                        <h6>Signature</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose where the block of signature locates at:</dt>
                                <dd>
                                    <select v-model="sig.page">
                                        <option value="0"></option>
                                        <option v-for="(page, page_index) in pages" :value="page.page">Page {{ page.page
                                            }}
                                        </option>
                                    </select>
                                </dd>
                            </dl>
                        </div>


                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="signaturebox client">

                                <div class="title_box">
                                    Client
                                </div>

                                <div class="function_box">
                                    <a class="btn small green" @click="add_sig_client_item()">Add</a>
                                </div>

                                <div class="content_box">
                                    <ul v-for="(item, index) in sig.item_client">
                                        <li>
                                            <span>Name:</span> <input type="text" v-model="item.name"><br>
                                            <span>Line 1:</span> <input type="text" placeholder="Position"
                                                                        v-model="item.position"><br>
                                            <span>Line 2:</span> <input type="text" placeholder="Phone Number"
                                                                        v-model="item.phone"><br>
                                            <span>Line 3:</span> <input type="text" placeholder="Email"
                                                                        v-model="item.email"><br>
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="sig_item_client_up(index, item.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="sig_item_client_down(index, item.id)"></i>
                                            <i class="fas fa-trash-alt"
                                               @click="sig_item_client_del(index, item.id)"></i>
                                        </li>
                                    </ul>


                                </div>
                            </div>


                            <div class="signaturebox company">

                                <div class="title_box">
                                    Feliix
                                </div>

                                <div class="function_box">
                                    <a class="btn small green" @click="add_sig_company_item()">Add</a>
                                    <a class="btn small green" @click="add_signature_codebook()">Signature Codebook</a>
                                </div>

                                <div class="content_box">
                                    <ul v-for="(item, index) in sig.item_company">
                                        <li>
                                            <span>Name:</span> <input type="text" v-model="item.name"><br>
                                            <span>Line 1:</span> <input type="text" placeholder="Position"
                                                                        v-model="item.position"><br>
                                            <span>Line 2:</span> <input type="text" placeholder="Phone Number"
                                                                        v-model="item.phone"><br>
                                            <span>Line 3:</span> <input type="text" placeholder="Email"
                                                                        v-model="item.email"><br>
                                            <span>Signature:</span>
                                            <div :class="['itembox', (item.url !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :name="'sig_image_' + item.id"
                                                           @change="onSigFileChangeImage($event, item.id)"
                                                           :id="'sig_image_' + item.id">
                                                    <img v-if="item.url" :src="item.url"/>
                                                    <div @click="clear_sig_photo(item.id)">x</div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="sig_item_company_up(index, item.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="sig_item_company_down(index, item.id)"></i>
                                            <i class="fas fa-trash-alt"
                                               @click="sig_item_company_del(index, item.id)"></i>
                                        </li>
                                    </ul>


                                </div>
                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_sig()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="sig_save()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>


    <!-- Function Bar end-->

    <div class="mainContent" style="overflow-x: auto; background-color: rgb(230,230,230)">

        <div class="qn_page" v-for="(pg, index) in pages" id="qn_page">

            <div class="qn_header" v-if="show_title">

                <div class="left_block">

                    <div class="qn_title">
                        <div class="line1 font_B">{{ first_line }}</div>
                        <div class="line2 font_B" style="margin-top: -15px;">{{ second_line }}</div>
                    </div>

                    <div class="qn_by_for">

                        <div class="qn_for font_B">
                            Prepared for:<br>
                            <div class="line1 font_B">{{ prepare_for_first_line }}</div>
                            <div class="line2 font_B">{{ prepare_for_second_line }}</div>
                            <div class="line3 font_B">{{ prepare_for_third_line }}</div>
                        </div>

                        <div class="qn_by font_B" v-if="prepare_by_first_line != '' || prepare_by_second_line != '' || prepare_by_third_line != ''">
                            Prepared by:<br>
                            <div class="line1 font_B">{{ prepare_by_first_line }}</div>
                            <div class="line2 font_B">{{ prepare_by_second_line }}</div>
                            <div class="line3 font_B">{{ prepare_by_third_line }}</div>
                        </div>

                    </div>

                </div>

                <div class="right_block">

                    <img class="logo" src="images/Feliix-Logo-White.png">

                    <div class="project_category font_M">
                        <!-- 下面的{{}}要顯示的值，如果是 project_category == 'Office Systems'，則{{ }}需要顯示 SystemsFurniture;如果是 project_category == 'Lighting'，則{{ }}需要顯示 Lighting -->
                       {{ project_category == 'Office Systems' ? 'SystemsFurniture' : project_category }}
                    </div>

                    <div class="qn_number_date">
                        <div><span class="qn_number_date_title font_B">Quotation #:</span> <span class="qn_number font_B">{{ quotation_no }}</span></div>
                        <div><span class="qn_number_date_title font_B">Date:</span> <span class="qn_date font_B">{{ quotation_date }}</span></div>
                    </div>

                </div>

            </div>

            <div class="qn_body">

                <div class="area_subtotal">

                    <table :class="[tp.type == 'A' ? 'tb_format1' : 'tb_format2', product_vat == 'P' ? 'vat' : '']"
                           v-for="(tp, index) in pg.types">
                        <thead v-if="tp.type == 'A'">

                        <tr>
                            <th class="title" :colspan="product_vat == 'P' ? 7 : 6">{{ tp.name }}</th>
                        </tr>

                        <tr>
                            <th>No</th>
                            <th colspan="2">Description</th>
                            <th>Qty.</th>
                            <th>Product Price</th>
                            <th v-if="product_vat == 'P'">12% VAT</th>
                            <th>Amount</th>
                        </tr>

                        </thead>

                        <thead v-if="tp.type == 'B'">

                        <tr>
                            <th class="title" :colspan="product_vat == 'P' ? 4 : 4">{{ tp.name }}</th>
                        </tr>

                        </thead>

                        <tbody v-if="tp.type == 'A'">

                        <template v-for="(bk, index) in tp.blocks">

                        <tr class="desc1">

                            <td rowspan="2" v-if="bk.type == 'image'">{{ bk.num }}</td>
                            <td v-if="bk.type == '' || bk.type== 'noimage' ">{{ bk.num }}</td>

                            <td rowspan="2" class="pic" v-if="bk.type == 'image'">
                                <img v-show="bk.photo !== ''" :src=" bk.photo !== '' ? img_url + bk.photo : ''">
                            </td>
                            <td rowspan="2" v-if="bk.type == 'image'">
                                <div :class="['pid', 'noPrint', (bk.status == -1 ? 'deleted' : '')]" v-if="bk.pid != 0">{{ "ID: " + bk.pid }} <button class="last_order_history" v-if="bk.is_last_order != ''" @click="last_order_info(bk.is_last_order)">Last Order History</button></div>
                                <div class="moq noPrint" v-if="bk.moq != ''">{{ "MOQ: " + bk.moq }}</div>
                                <div :class="['code', (bk.status == -1 ? 'deleted' : '')]">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>
                            <td v-if="bk.type == '' || bk.type== 'noimage'" colspan="2">
                                <div :class="['pid', 'noPrint', (bk.status == -1 ? 'deleted' : '')]" v-if="bk.pid != 0">{{ "ID: " + bk.pid }} <button class="last_order_history" v-if="bk.is_last_order != ''" @click="last_order_info(bk.is_last_order)">Last Order History</button></div>
                                <div class="moq noPrint" v-if="bk.moq != ''">{{ "MOQ: " + bk.moq }}</div>
                                <div :class="['code', (bk.status == -1 ? 'deleted' : '')]">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>
                            <td><span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                            </td>
                            <td><span class="numbers" v-if="bk.discount == 0">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                <span class="numbers deleted"
                                      v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">₱ {{ (bk.price * bk.ratio  !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                        v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">
                                <span class="numbers"
                                      v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio - (bk.price * bk.ratio * (bk.discount / 100))).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                <span class="numbers"
                                      v-if="bk.discount != 0 && (bk.discount == 100 || bk.amount == '0.00')">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                            </td>
                            <td v-if="product_vat == 'P'"><span class="numbers" v-if="bk.discount == 0">₱ {{ bk.price * bk.ratio !== undefined ? (Number(bk.price * bk.ratio) * 0.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                <span class="numbers" v-if="bk.discount != 0 && bk.amount != '0.00'">₱ {{ bk.price * bk.ratio !== undefined ? (Number(bk.price * bk.ratio - (bk.price * bk.ratio * (bk.discount / 100))) * 0.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                <span class="numbers" v-if="bk.discount != 0 && bk.amount == '0.00'">₱ {{ bk.price * bk.ratio !== undefined ? (Number(bk.price * bk.ratio) * 0.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                            </td>

                            <td v-if="bk.amount != '0.00' && product_vat == 'P'">

                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat == 'P'">
                                <span class="numbers deleted">₱ {{ (bk.qty * bk.ratio * bk.price  !== undefined ? Number(bk.qty * bk.ratio * bk.price * 1.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>


                            <td v-if="bk.amount != '0.00' && product_vat !== 'P'">

                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted">₱ {{ (bk.qty * bk.ratio * bk.price  !== undefined ? Number(bk.qty * bk.ratio * bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>
                        </tr>

                        <tr class="desc2" v-if="bk.type == 'image'">

                            <td class="pic" colspan="3" v-if="!(product_vat == 'P')">
                                <div class="notes">{{ bk.notes }}</div>
                                <div class="picbox">
                                    <img v-if="bk.photo2 != ''" :src="bk.url2">
                                    <img v-if="bk.photo3 != ''" :src="bk.url3">
                                </div>
                            </td>

                            <td class="pic" colspan="4" v-if="(product_vat == 'P')">
                                <div class="notes">{{ bk.notes }}</div>
                                <div class="picbox">
                                    <img v-if="bk.photo2 != ''" :src="bk.url2">
                                    <img v-if="bk.photo3 != ''" :src="bk.url3">
                                </div>
                            </td>

                        </tr>

                        </template>

                        </tbody>

                        <tbody v-if="tp.type == 'B'">
                        <tr v-for="(bk, index) in tp.blocks">
                            <td>{{ bk.num }}</td>
                            <td colspan="2">
                                <div :class="['pid', 'noPrint', (bk.status == -1 ? 'deleted' : '')]" v-if="bk.pid != 0">{{ "ID: " + bk.pid }} <button class="last_order_history" v-if="bk.is_last_order != ''" @click="last_order_info(bk.is_last_order)">Last Order History</button></div>
                                <div class="moq noPrint" v-if="bk.moq != ''">{{ "MOQ: " + bk.moq }}</div>
                                <div :class="['code', (bk.status == -1 ? 'deleted' : '')]">{{ bk.code }}</div>
                                <div class="brief" style="white-space: pre-line;">{{ bk.desc }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ bk.list }}</div>
                            </td>

                            <td v-if="bk.amount != '0.00' && product_vat == 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.ratio * bk.price  !== undefined ? (Number(bk.ratio * bk.price) * 1 ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? (Number(bk.amount)).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat == 'P'">
                                <span class="numbers deleted">₱ {{ (bk.ratio * bk.price  !== undefined ? (Number(bk.ratio * bk.price) * 1 ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>


                            <td v-if="bk.amount != '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted" v-if="bk.discount != 0">₱ {{ (bk.ratio * bk.price  !== undefined ? Number(bk.ratio * bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                        v-if="bk.discount != 0">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                    v-if="bk.discount != 0">
                                <span class="numbers">₱ {{ bk.amount !== undefined ? Number(bk.amount).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                            <td v-if="bk.amount == '0.00' && product_vat !== 'P'">
                                <span class="numbers deleted">₱ {{ (bk.ratio * bk.price  !== undefined ? Number(bk.ratio * bk.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                <span class="numbers red">FREE AS PACKAGE!</span>
                            </td>
                        </tr>

                        </tbody>

                        <tfoot v-if="tp.type == 'A' && tp.not_show != '1'">
                        <tr>
                            <td :colspan="product_vat == 'P' ? 5 : 4"></td>
                            <td>SUBTOTAL</td>
                            <td v-if="tp.real_amount == 0">₱ {{ tp.subtotal !== undefined ?
                                Number(tp.subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                            <td v-if="tp.real_amount != 0">₱ {{ tp.real_amount !== undefined ?
                                Number(tp.real_amount).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                        </tr>

                        </tfoot>

                        <tfoot v-if="tp.type == 'B' && tp.not_show != '1'">
                        <tr>
                            <td :colspan="product_vat == 'P' ? 2 : 2"></td>
                            <td>SUBTOTAL</td>
                            <td v-if="tp.real_amount == 0">₱ {{ tp.subtotal !== undefined ?
                                Number(tp.subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                            <td v-if="tp.real_amount != 0">₱ {{ tp.real_amount !== undefined ?
                                Number(tp.real_amount).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                "$1,") : '0.00' }}
                            </td>
                        </tr>

                        </tfoot>

                    </table>

                </div>

                <div class="area_total">
                    <table class="tb_total" v-for="(tt, index) in pg.total">
                        <tbody>
                        <tr>
                            <td :rowspan="(tt.vat == 'Y' && tt.discount !== '0' ? 3 :  2)">
                                <div>Remarks: Quotation valid for <span class="valid_for">{{ tt.valid }}</span></div>
                                <div></div>
                            </td>
                            <td>SUBTOTAL</td>
                            <td><span class="numbers">₱ {{ subtotal !== undefined ? Number(subtotal).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                            </td>
                        </tr>

                        <tr class="total_discount" v-if="tt.discount !== '0'">
                            <td>{{ tt.discount !== undefined ?
                                Math.floor(tt.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}%
                                DISCOUNT
                            </td>
                            <td><span class="numbers">₱ {{ (subtotal * tt.discount / 100) !== undefined ? (subtotal * tt.discount / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        <!--
                                                <tr class="total_vat" v-if="tt.vat == 'Y'">
                                                    <td>(12% VAT)</td>
                                                    <td><span class="numbers">₱ {{ (subtotal * 12 / 100) !== undefined ? (subtotal * 12 / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span></td>
                                                </tr>
                            -->
                        <tr class="total_vat" v-if="tt.vat == 'Y'">
                            <td>(12% VAT)</td>
                            <td><span class="numbers">₱ {{ ((subtotal_info_not_show_a * (100 - tt.discount) / 100) * 12 / 100) !== undefined ? ((subtotal_info_not_show_a * (100 - tt.discount) / 100) * 12 / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td><span class="total_discount" v-if="tt.show_vat == 'Y'">*price inclusive of VAT</span> <span class="total_discount" v-if="tt.show_vat == 'E'">*price exclusive of VAT</span>
                            </td>
                            <td>GRAND TOTAL</td>
                            <td v-if="tt.total != '0.00'">
                                <span class="numbers deleted" v-if="tt.total != total.back_total">₱ {{ total.back_total !== "" ? Number(total.back_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span><br
                                    v-if="tt.total != total.back_total">
                                <span class="numbers">₱ {{ tt.total !== "" ? Number(tt.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                            <td v-if="tt.total == '0.00'">
                                <span class="numbers">₱ {{ total.back_total !== "" ? Number(total.back_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 如果使用者在表單中選擇不要上框線，則在下面的 div 中需要加入 no_top_border 到 class 裡面 -->
                <div :class="[pg.slogan.border == 'Y' ? 'area_slogan' : 'area_slogan no_top_border']" v-if="pg.slogan.page !== undefined">
                
                    <hr>
                    <div class="slogan_text">DELIVERING THE RESULTS AT THE CHEAPEST PRICE</div>

                </div>


                <div class="area_terms">
                    <div class="terms" v-for="(tt, index) in pg.term">
                        <div class="title">{{ tt.title }}</div>
                        <div class="brief" :style="tt.brief == '' ? 'white-space: pre-line; display: none;' : 'white-space: pre-line;'">{{ tt.brief }}</div>
                        <div class="listing" style="white-space: pre-line;">{{ tt.list }}</div>
                    </div>
                </div>

                <div class="area_payment" v-if="pg.payment_term.page !== undefined">
                    <table class="tb_payment">
                        <tbody>
                        <tr>
                            <td colspan="2">Mode of Payment:</td>
                            <td>
                                <div>
                                    <span v-for="(tt, index) in pg.payment_term.payment_method">{{ tt }}</span>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                {{ pg.payment_term.brief }}
                            </td>
                        </tr>
                        <tr>
                            <td>Notes:</td>
                            <td>
                                <b>For Cheque</b><br>
                                Kindly Address to<br>
                                Feliix Inc.
                            </td>
                            <td>
                                <b>For Bank Details for Wiring</b>

                                <div class="acount_info" v-for="(tt, index) in pg.payment_term.list">
                                    <span class="account_name">{{ tt.bank_name }}</span>
                                    <span>: </span>
                                    <div class="first_line">
                                        {{ tt.first_line }}
                                    </div>
                                    <div class="second_line">{{ tt.second_line }}</div>
                                    <div class="third_line">{{ tt.third_line }}</div>
                                </div>


                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="area_conforme" style="margin-top: 60px;">
                    <div class="conforme"
                         v-if="(pg.sig != undefined ? pg.sig.item_client.length : 0)  + (pg.sig != undefined ?  pg.sig.item_company.length : 0) > 0">
                        CONFORME
                    </div>

                    <div class="client_signature" v-if="(pg.sig != undefined ? pg.sig.item_client.length : 0) > 0">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_client">
                            <div class="pic"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>

                    </div>

                    
                    <div class="company_signature"
                            v-if="(pg.sig != undefined ? pg.sig.item_company.length : 0) > 0 && (pg.sig != undefined ? pg.sig.item_company.length : 0) <= 4">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_company">
                            <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>
                    </div>

                    <div class="company_signature"
                            v-if="(pg.sig != undefined ? pg.sig.item_company.length : 0) > 4">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_company" v-if="index < 3">
                            <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>
                    </div>
                    <div class="company_signature" style="margin-top: -18px;"
                            v-if="(pg.sig != undefined ? pg.sig.item_company.length : 0) > 4">

                        <div class="signature" v-for="(tt, index) in pg.sig.item_company" v-if="index >= 3">
                            <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                            <div class="name">{{ tt.name }}</div>
                            <div class="line1">{{ tt.position }}</div>
                            <div class="line2">{{ tt.phone }}</div>
                            <div class="line3">{{ tt.email }}</div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="qn_footer">

                <div class="foot_divider"></div>
                <div class="line1">{{ footer_first_line }}</div>
                <div class="line2">{{ footer_second_line }}</div>
                <div class="qn_page_number">{{ index + 1 }}</div>

            </div>

        </div>


    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_product_catalog">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">Product Catalog</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                        <div class="left_function">

                            <select v-model="fil_category">
                                <option value="">Choose Category...</option>
                                <option value="10000000">Lighting</option>
                                <option value="20000000">Systems Furniture</option>
                                <option value="20010000">Systems Furniture >> Cabinet</option>
                                <option value="20020000">Systems Furniture >> Chair</option>
                                <option value="20030000">Systems Furniture >> Table</option>
                                <option value="20040000">Systems Furniture >> Workstation</option>
                                <option value="20050000">Systems Furniture >> Partition</option>
                            </select>

                            <input type="text" placeholder="ID" v-model="fil_id" style="width: 240px; margin-right: 0;">

                            <input type="text" placeholder="Code" v-model="fil_code" style="width: 240px; margin-right: 0;">

                            <select v-model="fil_brand" style="width: 240px;">
                                <option value="">Choose Brand...</option>
                                <option v-for="(item, index) in brands">{{ item.brand }}</option>
                            </select>

                            <br>

                            <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                    data-width="585px" title="Choose Tag(s)..." id="tag01" v-model="fil_tag">

                                    <optgroup v-for="(group, index) in tag_group" :label="group.group">
<option v-for="(it, index2) in group.items" :value="it.item_name">{{ it.item_name }}</option>
</optgroup>


                            </select>

                            <input type="text" placeholder="Keyword" v-model="fil_keyword" style="margin-left: 20px; width: 300px;">
                        </div>

                        <a class="btn small green" @click="filter_apply_new()">Search</a>

                    </div>

                    <div class="list_function" style="margin: 7px 0;">

                        <div class="sort_block">
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(1)">SRP (Low → High)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(2)">SRP (High → Low)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(3)">QP (Low → High)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(4)">QP (High → Low)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(0)">Clear</a>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="product_page == 1" @click="pre_page(); filter_apply();">Prev
                                10</a>
                            <a class="page" v-for="pg in product_pages_10" @click="product_page=pg; filter_apply(pg);"
                               v-bind:style="[pg == product_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                                pg
                                }}</a>
                            <a class="next" :disabled="product_page == product_pages.length"
                               @click="nex_page(); filter_apply();">Next
                                10</a>
                        </div>
                    </div>


                    <div>
                        <table id="tb_product_list" class="table  table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Information</th>
                                <th>Specification</th>
                                <th>Price</th>
                                <th>Inventory Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            <template v-for="(item, index) in displayedPosts">
                                <!-- Product Set 子類別的產品，套用以下格式輸出到頁面上 -->
                                <!-- set_format1 會套用在 Product Set 產品的主敘述，set_format2 會套用在 Product Set 產品的 Product1, Product 2, Product 3 -->
                                <tr class="set_format1" v-if="item.sub_category == '10020000'">
                                    <!-- 如果這個 Product Set 產品有 Product1 和 Product 2，則 rowspan=3；如果這個 Product Set 產品有 Product1 和 Product 2 和 Product 3，則 rowspan=4 -->
                                    <td colspan="3">
                                        <ul>
                                            <li>
                                                ID:
                                            </li>
                                            <li>
                                                {{ item.id }}
                                            </li>

                                        </ul>
                                        <ul>
                                            <li>
                                                Code:
                                            </li>
                                            <li>
                                                {{ item.code }}
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Category:
                                            </li>
                                            <li>
                                                {{ item.category}} >> {{ item.sub_category_name}}
                                            </li>

                                        </ul>
                                        <ul>
                                            <li>
                                                Tags:
                                            </li>
                                            <li>
                                                <span v-for="(it, index) in item.tags">{{ it }}</span>
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Created:
                                            </li>
                                            <li>
                                                {{ item.created_at }} {{ item.created_name !== null ? '(' + item.created_name + ')' : '' }}
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Updated:
                                            </li>
                                            <li>
                                                {{ item.updated_name !== null ? item.updated_at : '' }} {{ item.updated_name !== null ? '(' + item.updated_name + ')' : '' }}
                                            </li>

                                        </ul>

                                        <ul class="supporting_attachment">
                                            <li></li>
                                            <li>
                                                <span v-if="item.product_ics.length > 0">IES File</span>
                                                <span v-if="item.product_skp.length > 0">SketchUp File</span>
                                                <span v-if="item.product_manual.length > 0">Supporting File</span>
                                                <span v-if="item.is_replacement_product.length > 0">Replacement Product</span>
                                            </li>
                                        </ul>

                                        <div class="product_set_desc">
                                Description:
                                <div>{{ item.description }}</div>
                            </div>

                                    </td>

                                    <td>
                                        <span>SRP: {{ item.price }}<br></span>
                                        <span>QP: {{ item.quoted_price }}<br></span>
                                    </td>

                                    <td></td>

                                    <!-- 如果這個 Product Set 產品有 Product1 和 Product 2，則 rowspan=3；如果這個 Product Set 產品有 Product1 和 Product 2 和 Product 3，則 rowspan=4 -->
                                    <td :rowspan="item.product_set_cnt + 1">
                                        <button id="edit01" @click="btnEditClick(item)" v-if="item.status != -1"><i aria-hidden="true" class="fas fa-caret-right"></i></button>
                                    </td>
                                </tr>


                                <!-- set_format1 會套用在 Product Set 產品的主敘述，set_format2 會套用在 Product Set 產品的 Product1, Product 2, Product 3 -->
                                <tr class="set_format2" v-for="(set, index) in item.product_set">
                                    <td>
                                        <img :src="img_url + set.photo1" v-if="set.photo1"></a>
                                    </td>
                                    <td>
                                        <ul v-if="set.out == 'Y' || (set.out == 'Y' && set.replacement_product.length > 0) || (set.status == -1 && set.replacement_product.length > 0)">
                            <li>
                                    <!-- 依據這個停產的產品是否有 Replacement Product 的資料，沒有資料則用第一個 <span>，有資料則用二個 <span> -->
                                    <span class="phasedout" v-if="set.replacement_product.length == 0">Phased Out</span>
                                    <span class="phasedout_replacement" v-if="set.status != -1 && set.replacement_product.length > 0" @click="replacement_info(set.replacement_text)">Phased Out</span>
                                    <span class="phasedout_replacement" v-if="set.status == -1 && set.replacement_product.length > 0" @click="replacement_info(set.replacement_text)">Deleted</span>
                            </li>
                                            <li></li>
                                        </ul>
                                        <ul>
                                            <li>
                                                ID:
                                            </li>
                                            <li>
                                                {{ set.id }}
                                            </li>

                                        </ul>
                                        <ul>
                                            <li>
                                                Code:
                                            </li>
                                            <li>
                                                {{ set.code }}
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Category:
                                            </li>
                                            <li>
                                                {{ set.category}} >> {{ set.sub_category_name}}
                                            </li>

                                        </ul>
                                        <ul>
                                            <li>
                                                Tags:
                                            </li>
                                            <li>
                                                <span v-for="(it, index) in set.tags">{{ it }}</span>
                                            </li>

                                        </ul>
                                        <ul>
                                            <li>
                                                Brand:
                                            </li>
                                            <li>
                                                {{ set.brand }}
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Created:
                                            </li>
                                            <li>
                                                {{ set.created_at }} {{ set.created_name !== null ? '(' + set.created_name + ')' : '' }}
                                            </li>

                                        </ul>

                                        <ul>
                                            <li>
                                                Updated:
                                            </li>
                                            <li>
                                                {{ set.updated_name !== null ? set.updated_at : '' }} {{ set.updated_name !== null ? '(' + set.updated_name + ')' : '' }}
                                            </li>

                                        </ul>

                                        <ul class="supporting_attachment">
                                            <li></li>
                                            <li>
                                                <span v-if="set.product_ics.length > 0">IES File</span>
                                                <span v-if="set.product_skp.length > 0">SketchUp File</span>
                                                <span v-if="set.product_manual.length > 0">Supporting File</span>
                                                <span v-if="set.is_replacement_product.length > 0">Replacement Product</span>
                                            </li>
                                        </ul>

                                        <!-- 針對一個產品 ID， if (它的主產品在 product_category 資料表 last_order 欄位有值 or 它的任何一個子規格在 product 資料表 last_order 欄位有值)，就需要顯示下面的 <ul class="last_order_history"> 結構 -->
                                        <ul class="last_order_history" v-if="set.is_last_order != ''">
                                            <li>
                                                Last Order History:
                                            </li>
                                            <li>
                                                <button @click="last_order_info(set.is_last_order)">info</button>
                                            </li>

                                        </ul>

                                        <!-- 如果停產的子規格數目大於 0，才需要顯示下面的<div class="phasedout_variant"> 結構 -->
                                        <div class="phasedout_variant" v-if="set.phased_out_cnt > 0">

                                            <!-- 如果停產的子規格數目大於或等於2，則顯示下面這一行 -->
                                            ※ {{ set.phased_out_cnt }} variant{{ set.phased_out_cnt > 1 ? 's' : '' }} are phased out.

                                            <!-- 當使用者點擊下方的 info 按鈕，這時候系統才會向資料庫利用這個產品的id，去查詢product這張表裡這個商品是哪些子規格停產，之後則會利用 sweetalert2@9 跑出一個彈出訊息框，訊息框裡面會列出停產子規格的資訊 -->
                                            <!-- 並不需要網頁載入時，就把所有停產的子規格查詢出來 且 掛在網頁上。只有當使用者點擊下方的 info 按鈕，才需去額外查詢停產的子規格，以減少頁面負載量。 -->
                                            <button @click="phased_out_info(set.phased_out_text)">info</button>
                                        </div>

                                    </td>

                                    <td>
                                        <ul v-for="(att, index) in set.attribute_list">
                                            <li>
                                                {{ att.category }}:
                                            </li>
                                            <li v-if="att.value.length > 1">
                                                <span v-for="(att_value, index) in att.value">{{att_value}}</span>
                                            </li>
                                            <li v-if="att.value.length == 1">
                                                <template v-for="(att_value, index) in att.value">{{att_value}}</template>
                                            </li>

                                        </ul>

                                    </td>

                                    <td>
                                        <span v-show="((cost_lighting == true && set.category == 'Lighting') || (cost_furniture == true && set.category == 'Systems Furniture')) && toggle == true">CP: {{ set.price_ntd }} <br v-if="set.str_price_ntd_change"> {{ set.str_price_ntd_change ?  set.str_price_ntd_change : '' }} <br></span>
                                        <span>SRP: {{ set.price }} <br v-if="set.str_price_change"> {{ set.str_price_change ?  set.str_price_change : '' }} <br></span>
                                        <span>QP: {{ set.quoted_price }} <br v-if="set.str_quoted_price_change"> {{ set.str_quoted_price_change ? set.str_quoted_price_change : '' }} <br></span>
                                    </td>

                                    <td>
                                        Incoming<br>
                                        <span class="stock_qty" @click="incoming_qty_info(set.incoming_html)">{{ set.incoming_qty }}</span><br>
                                        Project Pool<br>
                                        <span class="stock_qty_ware">{{ set.project_qty }}</span>
                                        <span class="stock_qty_ware">{{ set.project_s_qty }}</span>
                                        <br>

                                        Stock Pool<br>
                                        <span class="stock_qty_ware">{{ set.stock_qty }}</span>
                                        <span class="stock_qty_ware">{{ set.stock_s_qty }}</span>
                                        <br>
                                        <!--
                                        Project-Locked<br>
                                        <span class="stock_qty_ware">10</span><br>
                                        Freely Usable<br>
                                        <span class="stock_qty_ware">5</span>
                                        -->
                                    </td>
                                </tr>


                                <!-- 非 Product Set 子類別的產品，套用以下格式輸出到頁面上 -->
                                <tr v-for="(item, index) in displayedPosts">
                                <tr v-if="item.sub_category != '10020000'">

                                <td><img
                                        :src="img_url + item.photo1" v-if="item.photo1 !== ''">
                                </td>
                                <td>
                                <ul v-if="item.out == 'Y' || (item.out == 'Y' && item.replacement_product.length > 0) || (item.status == -1 && item.replacement_product.length > 0)">
                            <li>
                                    <!-- 依據這個停產的產品是否有 Replacement Product 的資料，沒有資料則用第一個 <span>，有資料則用二個 <span> -->
                                    <span class="phasedout" v-if="item.replacement_product.length == 0">Phased Out</span>
                                    <span class="phasedout_replacement" v-if="item.status != -1 && item.replacement_product.length > 0" @click="replacement_info(item.replacement_text)">Phased Out</span>
                                    <span class="phasedout_replacement" v-if="item.status == -1 && item.replacement_product.length > 0" @click="replacement_info(item.replacement_text)">Deleted</span>
                            </li>
                                        <li></li>
                                    </ul>
                                    <ul>
                                        <li>
                                            ID:
                                        </li>
                                        <li>
                                            {{ item.id }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Code:
                                        </li>
                                        <li class="code">{{ item.code }}</li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Category:
                                        </li>
                                        <li v-if="item.category == 'Lighting'">
                                            {{ item.category}}
                                        </li>
                                        <li v-if="item.category != 'Lighting'">
                                            {{ item.category}} >> {{ item.sub_category_name}}
                                        </li> <!----></ul>
                                    <ul>
                                        <li>
                                            Tags:
                                        </li>
                                        <li><span v-for="(it, index) in item.tags"
                                                  v-if="item.tags !== undefined ? item.tags[0] !== '' : false">{{ it }}</span>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Brand:
                                        </li>
                                        <li>
                                            {{ item.brand }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Created:
                                        </li>
                                        <li>
                                            {{ item.created_at }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            Updated:
                                        </li>
                                        <li>
                                            {{ item.updated_at }}
                                        </li>
                                    </ul>

                                    <ul class="supporting_attachment">
                                            <li></li>
                                            <li>
                                                <span v-if="item.product_ics.length > 0">IES File</span>
                                                <span v-if="item.product_skp.length > 0">SketchUp File</span>
                                                <span v-if="item.product_manual.length > 0">Supporting File</span>
                                                <span v-if="item.is_replacement_product.length > 0">Replacement Product</span>
                                            </li>
                                        </ul>

                                <!-- 針對一個產品 ID， if (它的主產品在 product_category 資料表 last_order 欄位有值 or 它的任何一個子規格在 product 資料表 last_order 欄位有值)，就需要顯示下面的 <ul class="last_order_history"> 結構 -->
                                <ul class="last_order_history"  v-if="item.is_last_order != ''">
                                    <li>
                                        Last Order History:
                                    </li>
                                    <li>
                                        <button @click="last_order_info(item.is_last_order)">info</button>
                                    </li>

                                </ul>
                                    <!-- 如果停產的子規格數目大於 0，才需要顯示下面的<div class="phasedout_variant"> 結構 -->
                                    <div class="phasedout_variant" v-if="item.phased_out_cnt > 0">

                                        <!-- 如果停產的子規格數目大於或等於2，則顯示下面這一行 -->
                                        ※ {{ item.phased_out_cnt }} variant{{ item.phased_out_cnt > 1 ? 's' : '' }} are phased out.

                                        <!-- 當使用者點擊下方的 info 按鈕，這時候系統才會向資料庫利用這個產品的id，去查詢product這張表裡這個商品是哪些子規格停產，之後則會利用 sweetalert2@9 跑出一個彈出訊息框，訊息框裡面會列出停產子規格的資訊 -->
                                        <!-- 並不需要網頁載入時，就把所有停產的子規格查詢出來 且 掛在網頁上。只有當使用者點擊下方的 info 按鈕，才需去額外查詢停產的子規格，以減少頁面負載量。 -->
                                        <button @click="phased_out_info(item.phased_out_text)">info</button>
                                    </div>
                                </td>
                                <td>
                                    <ul v-for="(att, index) in item.attribute_list">
                                        <li>
                                            {{ att.category }}:
                                        </li>
                                        <li v-if="att.value.length > 1">
                                            <span v-for="(att_value, index) in att.value">{{att_value}}</span>
                                        </li>
                                        <li v-if="att.value.length == 1">
                                            <template v-for="(att_value, index) in att.value">{{att_value}}</template>
                                        </li>

                                    </ul>
                                </td>
                                <td>
                                    <span v-show="((cost_lighting == true && item.category == 'Lighting') || (cost_furniture == true && item.category == 'Systems Furniture'))">CP: {{ item.price_ntd }} <br v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }}<br></span>
                                    <span>SRP: {{ item.price }}<br v-if="item.str_price_change"> {{ item.str_price_change ?  item.str_price_change : '' }}<br></span>
                                    <span>QP: {{ item.quoted_price }} <br v-if="item.str_quoted_price_change"> {{ item.str_quoted_price_change ? item.str_quoted_price_change : '' }}<br></span>
                                </td>
                                <td>
                                    Incoming<br>
                                    <span class="stock_qty" @click="incoming_qty_info(item.incoming_html)">{{ item.incoming_qty }}</span><br>
                                    Project Pool<br>
                                        <span class="stock_qty_ware">{{ item.project_qty }}</span>
                                        <span class="stock_qty_ware">{{ item.project_s_qty }}</span>
                                        <br>

                                        Stock Pool<br>
                                        <span class="stock_qty_ware">{{ item.stock_qty }}</span>
                                        <span class="stock_qty_ware">{{ item.stock_s_qty }}</span>
                                        <br>
                                    <!--
                                    Project-Locked<br>
                                    <span class="stock_qty_ware">10</span><br>
                                    Freely Usable<br>
                                    <span class="stock_qty_ware">5</span>
                                    -->
                                </td>
                                <td>
                                    <button id="edit01" @click="btnEditClick(item)" v-if="item.status != -1"><i aria-hidden="true"
                                                                                       class="fas fa-caret-right"></i>
                                    </button>
                                </td>
                            </tr>

                            </template>

                            </tbody>
                        </table>

                    </div>

                    <!--
                                        <div>
                                            <table id="tb_product_list" class="table  table-sm table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Information</th>
                                                    <th>Specification</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(item, index) in displayedPosts">
                                                    <td>
                                                        <img :src="baseURL + item.photo1" v-if="item.photo1">
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            <li>
                                                                ID:
                                                            </li>
                                                            <li>
                                                                {{ item.id }}
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Code:
                                                            </li>
                                                            <li>
                                                                {{ item.code }}
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Category:
                                                            </li>
                                                            <li v-if="item.category == 'Lighting'">
                                                                {{ item.category}}
                                                            </li>
                                                            <li v-if="item.category != 'Lighting'">
                                                                {{ item.category}} >> {{ item.sub_category_name}}
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Tags:
                                                            </li>
                                                            <li>
                                                                <span v-for="(it, index) in item.tags">{{ it }}</span>
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Brand:
                                                            </li>
                                                            <li>
                                                                {{ item.brand }}
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Created:
                                                            </li>
                                                            <li>
                                                                {{ item.created_at }}
                                                            </li>
                                                        </ul>
                                                        <ul>
                                                            <li>
                                                                Updated:
                                                            </li>
                                                            <li>
                                                                {{ item.updated_at }}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <ul v-for="(att, index) in item.attribute_list">
                                                            <li>
                                                                {{ att.category }}:
                                                            </li>
                                                            <li v-if="att.value.length > 1">
                                                                <span v-for="(att_value, index) in att.value">{{att_value}}</span>
                                                            </li>
                                                            <li v-if="att.value.length == 1">
                                                                <template v-for="(att_value, index) in att.value">{{att_value}}</template>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <span>SRP: {{ item.price }}<br></span>
                                                        <span>QP: {{ item.quoted_price }}<br></span>
                                                    </td>
                                                    <td>
                                                        <button id="edit01"><i
                                                                class="fas fa-caret-right"></i></i>
                                                        </button>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                                -->

                </div>

            </div>


        </div>

    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_product_display">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

            <div class="modal-content"
                 style="height: calc( 100vh - 3.75rem); overflow-y: auto; border: none; padding-bottom: 20px;">

                <!-- 如果載入的產品為 Product Set 子類別的產品，則需要使用下面的結構來顯示 Product Set 產品的基本資訊 -->
                <div class="upper_section product_set noPrint" v-if="product.sub_category == '10020000'">

                    <div class="infobox">
                        <div class="basic_info">
                            <h3>ID: {{ product.pid }}</h3>
                            <h3 style="word-break: break-all;">{{product.code}}</h3>
                            <h6>{{ product.category}} >> {{ product.sub_category_name}}</h6>
                            <div class="tags">
                                <span v-for="(it, index) in product.tags">{{ it }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="infobox">
                        <ul class="price_stock">

                            <li id="print_srp">
                                Standard Retail Price: <span>{{product.price}}</span>
                            </li>

                            <li id="print_qp">
                                Quoted Price: <span>{{product.quoted_price}}</span>
                            </li>

                        </ul>

                        <!-- 如果這個 Product Set 的 Description 是空值，則整個 <div class="product_set_desc"> 都不用被建立出來 -->
                        <div class="product_set_desc">
                            <span>Description: </span> {{ product.description }}
                        </div>

                        <!-- 針對 Product Set 產品的新加入方法 -->
                        <div class="btnbox">
                            <ul>
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image_set_select()" v-if="out==''">Add with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image_set_select()" v-if="out==''">Add without Image</button>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                </li>

                            </ul>
                        </div>

                    </div>

                </div>



                <!-- 如果載入的產品為 Product Set 子類別的產品，則需要使用原本整個 product_display_code 的結構，來一個一個顯示其中的 Product 1、Product 2、Product 3 -->
                <template v-if="product.sub_category == '10020000'" v-for="(set, set_index) in product.product_set">
                    <template v-if="set.variation_mode != 1">
                        <div class="upper_section">
                            <div class="imagebox">
                                <div class="selected_image">
                                    <img :src="set.url" v-if="set.url !== ''">
                                </div>
                                <div class="image_list">
                                    <img v-if="set.photo1" :src="img_url + set.photo1"
                                        @click="change_url_set(set, 1)"/>
                                    <img v-if="set.photo2" :src="img_url + set.photo2"
                                        @click="change_url_set(set, 2)"/>
                                    <img v-if="set.photo3" :src="img_url + set.photo3"
                                        @click="change_url_set(set, 3)"/>
                                    <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                                </div>
                            </div>
                            <div class="infobox">
                                <div class="basic_info">

                                    <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                                    <div class="last_order_history" v-if="set.is_last_order != ''">
                                        <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                        <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <button @click="last_order_info(set.is_last_order)" v-if="set.last_have_spec">Last Order History</button>
                                        <span v-if="set.last_order_name != ''">Last Ordered: {{ set.last_order_at }} at <a :href="set.last_order_url">{{ set.last_order_name }}</a></span>
                                    </div>
                                
                                    <span class="phasedout" v-if="set.out == 'Y' && set.out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="set.out_cnt == 1" @click="PhaseOutAlert_set(set.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="set.out_cnt > 1" @click="PhaseOutAlert_set(set.phased_out_text1)">{{ set.out_cnt }} variants are phased out</span>
                                <h3 style="word-break: break-all;">{{set.code}}</h3> <h6>
                                    {{set.brand}}</h6>
                                    <h6>{{ set.category}} >> {{
                                        set.sub_category_name}}</h6>
                                    <!---->
                                    <div class="tags"><span v-for="(it, index) in set.tags">{{ it }}</span></div>
                                </div>
                                <ul class="price_stock">
                                    <li>
                                        Suggested Retail Price: <span>{{set.price}}</span><span></span></li>
                                    <li>
                                        Quoted Price: <span>{{set.quoted_price}}</span><span></span></li>
                                </ul>

                                <ul class="variants" v-if="set.variation1_value.length > 0">
                            <li>
                                Variants
                            </li>
                            <li v-if="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                                {{ set.variation1 !== 'custom' ? set.variation1 + ': ' : set.variation1_custom + ': '}} <template v-for="(item, index) in set.variation1_value">{{ (index + 1 !== set.variation1_value.length) ? item + ', ' : item}} </template>
                            </li>
                            <li v-show="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                                <select class="form-control" v-model="set.v1" @change="change_v_set(set)">
                                    <option value=""></option>
                                    <option v-for="(item, index) in set.variation1_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>
                            <li v-if="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                                {{ set.variation2 !== 'custom' ? set.variation2 + ': ' : set.variation2_custom + ': ' }} <template v-for="(item, index) in set.variation2_value">{{ (index + 1 !== set.variation2_value.length) ? item + ', ' : item}} </template>
                            </li>
                            <li v-show="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                                <select class="form-control" v-model="set.v2" @change="change_v_set(set)">
                                    <option value=""></option>
                                    <option v-for="(item, index) in set.variation2_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>
                            <li v-if="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                                {{ set.variation3 !== 'custom' ? set.variation3 + ': ' : set.variation3_custom + ': ' }} <template v-for="(item, index) in set.variation3_value">{{ (index + 1 !== set.variation3_value.length) ? item + ', ' : item}} </template>
                            </li>
                            <li v-show="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                                <select class="form-control" v-model="set.v3" @change="change_v_set(set)">
                                    <option value=""></option>
                                    <option v-for="(item, index) in set.variation3_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>
                            <li v-if="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                                {{ set.variation4 !== 'custom' ? set.variation4 + ': ' : set.variation4_custom + ': ' }} <template v-for="(item, index) in set.variation4_value">{{ (index + 1 !== set.variation4_value.length) ? item + ', ' : item}} </template>
                            </li>
                            <li v-show="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                                <select class="form-control" v-model="set.v4" @change="change_v_set(set)">
                                    <option value=""></option>
                                    <option v-for="(item, index) in set.variation4_value" :value="item" :key="item">{{item}}
                                    </option>
                                </select>
                            </li>

                            <template v-for="(item, index) in set.accessory_infomation" v-if="show_accessory">
                                <li>{{ item.category }}</li>
                                <li>
                                    <select class="selectpicker" data-width="100%" :id="set.id + 'tag'+index">
                                        <option :data-thumbnail="set.detail.url" v-for="(detail, index) in item.detail[0]">
                                            {{detail.code}}
                                        </option>
                                    </select>
                                </li>
                            </template>
                        </ul>


                                <div class="btnbox">
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image_set(set)" v-if="set.out==''">Add with Image</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set(set)" v-if="set.out==''">Add without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul v-if="set.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image_set(set,'all')"  v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)"> Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)" > Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="middle_section" v-if="set.specification.length > 0"><h5>Specification</h5>
                            <table>
                                <tbody>
                                <template v-for="(item, index) in set.specification">
                                    <tr>
                                        <td>
                                            {{item.k1}}
                                        </td>
                                        <td>
                                            {{item.v1}}
                                        </td>
                                        <td>
                                            {{item.k2}}
                                        </td>
                                        <td> {{item.v2}}</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="middle_section"
                            v-if="set.related_product !== undefined ? set.related_product.length !== 0 : false">
                            <h5>Related Products</h5>

                            <div id="carouselExampleControls" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in set.groupedItems'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="middle_section"
                            v-if="set.replacement_product !== undefined ? set.replacement_product.length !== 0 : false">
                            <h5>Replacement Product</h5>

                            <div id="carouselExampleControls_replacement" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in set.groupedItems_replacement'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="lower_section"
                            v-if="(set.notes != null && set.notes != '') || set.description != ''"><h5>
                            Description</h5>
                            <p>
                                {{ set.description }}
                            </p>
                            <p v-if="set.notes != null && set.notes != ''">
                                Notes: {{ set.notes }}
                            </p>
                        </div>
                    </template>

                    <template v-if="set.variation_mode == 1">
                        <div class="upper_section">

                            <div class="imagebox">
                                <div class="selected_image">
                                    <img :src="set.url" v-if="set.url !== ''">
                                </div>
                                <div class="image_list">
                                    <img v-if="set.photo1" :src="img_url + set.photo1"
                                        @click="change_url_set(set, 1)"/>
                                    <img v-if="set.photo2" :src="img_url + set.photo2"
                                        @click="change_url(set, 2)"/>
                                    <img v-if="set.photo3" :src="img_url + set.photo3"
                                        @click="change_url(set, 3)"/>
                                    <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                                </div>

                            </div>


                            <div class="infobox">
                                <div class="basic_info">

                                    <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                                    <div class="last_order_history" v-if="set.is_last_order != ''">
                                        <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                        <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <button @click="last_order_info(set.is_last_order)" v-if="set.last_have_spec">Last Order History</button>
                                        <span v-if="set.last_order_name != ''">Last Ordered: {{ set.last_order_at }} at <a :href="set.last_order_url">{{ set.last_order_name }}</a></span>
                                    </div>

                                    <span class="phasedout" v-if="set.out == 'Y' && set.out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="set.out_cnt == 1" @click="PhaseOutAlert_set(set.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="set.out_cnt > 1" @click="PhaseOutAlert_set(set.phased_out_text1)">{{ set.out_cnt }} variants are phased out</span>

                                    <h3>{{set.code}}</h3> <h6>{{set.brand}}</h6>
                                    <h6>{{ set.category}} >> {{
                                        set.sub_category_name}}</h6>
                                    <div class="tags" v-if="set.tags !== undefined ? set.tags[0] !== '' : false">
                                        <span v-for="(it, index) in set.tags">{{ it }}</span>
                                    </div>
                                </div>

                                <ul class="price_stock">

                                    <li>
                                        Suggested Retail Price: <span>{{set.price}}</span><span></span>
                                    </li>

                                    <li>
                                        Quoted Price: <span>{{set.quoted_price}}</span><span></span>
                                    </li>

                                </ul>

                                <ul class="variants">
                                    <li>
                                        Select:
                                    </li>
                                    <li v-if="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                                        {{ set.variation1 !== 'custom' ? set.variation1 :
                                        set.variation1_custom}}
                                    </li>
                                    <li v-show="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                                        <select class="form-control" v-model="set.v1" @change="change_v_set(set)">
                                            <option value=""></option>
                                            <option v-for="(item, index) in set.variation1_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                                        {{ set.variation2 !== 'custom' ? set.variation2 : set.variation2_custom
                                        }}
                                    </li>
                                    <li v-show="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                                        <select class="form-control" v-model="set.v2" @change="change_v_set(set)">
                                            <option value=""></option>
                                            <option v-for="(item, index) in set.variation2_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                                        {{ set.variation3 !== 'custom' ? set.variation3 : set.variation3_custom
                                        }}
                                    </li>
                                    <li v-show="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                                        <select class="form-control" v-model="set.v3" @change="change_v_set(set)">
                                            <option value=""></option>
                                            <option v-for="(item, index) in set.variation3_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                                        {{ set.variation4 !== 'custom' ? set.variation4 : set.variation4_custom
                                        }}
                                    </li>
                                    <li v-show="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                                        <select class="form-control" v-model="set.v4" @change="change_v_set(set)">
                                            <option value=""></option>
                                            <option v-for="(item, index) in set.variation4_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>

                                    <template v-for="(item, index) in set.accessory_infomation" v-if="show_accessory">
                                        <li>{{ item.category }}</li>
                                        <li>
                                            <select class="selectpicker" data-width="100%" :id="'tag'+index">
                                                <option :data-thumbnail="set.detail.url"
                                                        v-for="(detail, index) in item.detail[0]">
                                                    {{detail.code}}
                                                </option>
                                            </select>
                                        </li>
                                    </template>

                                </ul>

                                <div class="btnbox">
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image_set(set)" v-if="set.out==''">Add with Image</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set(set)" v-if="set.out==''">Add without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)">Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)">Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>

                                    </ul>
                                </div>

                            </div>

                        </div>


                        <div class="middle_section" v-if="set.specification.length > 0">
                            <h5>Specification</h5>

                            <table>
                                <tbody>
                                <template v-for="(item, index) in set.specification">
                                    <tr>
                                        <td>
                                            {{item.k1}}
                                        </td>
                                        <td>
                                            {{item.v1}}
                                        </td>
                                        <td>
                                            {{item.k2}}
                                        </td>
                                        <td> {{item.v2}}</td>
                                    </tr>
                                </template>

                                </tbody>

                            </table>

                        </div>

                        <div class="middle_section"
                            v-if="set.related_product !== undefined ? set.related_product.length !== 0 : false">
                            <h5>Related Products</h5>

                            <div id="carouselExampleControls" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in set.groupedItems'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="middle_section"
                            v-if="set.replacement_product !== undefined ? set.replacement_product.length !== 0 : false">
                            <h5>Replacement Product</h5>

                            <div id="carouselExampleControls_replacement" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in set.groupedItems_replacement'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>

                        <div class="lower_section"
                            v-if="(set.notes != null && set.notes != '') || set.description != ''">
                            <h5>Description</h5>
                            <p>
                                {{ set.description }}
                            </p>
                            <p v-if="set.notes != null && set.notes != ''">
                                Notes: {{ set.notes }}
                            </p>
                            <!--
                            <div class="desc_imgbox">
                                <img src="images/realwork.png">
                                <img src="images/realwork.png">
                                <img src="images/wash_hands.png">
                                <img src="images/realwork.png">
                            </div>
                            -->
                        </div>
                    </template>
                </template>

                <template v-if="product.sub_category != '10020000'">
                    <template v-if="product.variation_mode != 1">
                        <div class="upper_section">
                            <div class="imagebox">
                                <div class="selected_image">
                                    <img :src="url" v-if="url !== ''">
                                </div>
                                <div class="image_list">
                                    <img v-if="product.photo1" :src="img_url + product.photo1"
                                        @click="change_url(product.photo1)"/>
                                    <img v-if="product.photo2" :src="img_url + product.photo2"
                                        @click="change_url(product.photo2)"/>
                                    <img v-if="product.photo3" :src="img_url + product.photo3"
                                        @click="change_url(product.photo3)"/>
                                    <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                                </div>
                            </div>
                            <div class="infobox">
                                <div class="basic_info">

                                <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                                <div class="last_order_history"  v-if="product.is_last_order != ''">

                                <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                <button @click="last_order_info(product.is_last_order)" v-if="product.last_have_spec">Last Order History</button>
                                <span v-if="product.last_order_url != ''">Last Ordered: {{ product.last_order_at }} at <a :href="product.last_order_url">{{ product.last_order_name }}</a></span>
                                </div>

                                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="out_cnt == 1" @click="PhaseOutAlert(product.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="out_cnt > 1" @click="PhaseOutAlert(product.phased_out_text1)">{{ out_cnt }} variants are phased out</span>
                                <h3 style="word-break: break-all;">{{product.code}}</h3> <h6>
                                    {{product.brand}}</h6>
                                    <h6>{{ product.category}} >> {{
                                        product.sub_category_name}}</h6>
                                    <!---->
                                    <div class="tags"><span v-for="(it, index) in product.tags">{{ it }}</span></div>
                                </div>
                                <ul class="price_stock">
                                    <li>
                                        Suggested Retail Price: <span>{{price}}</span><span></span></li>
                                    <li>
                                        Quoted Price: <span>{{quoted_price}}</span><span></span></li>
                                </ul>

                                <ul class="variants" style="display: none;">
                                    <li>
                                        Select:
                                    </li>
                                    <li>Beam Angle</li><!---->
                                    <li><select class="form-control">
                                        <option value=""></option>
                                    </select></li>
                                    <li>CCT</li><!---->
                                    <li style="display: none;"><select class="form-control">
                                        <option value=""></option>
                                    </select></li> <!---->
                                    <li>Color Finish</li>
                                    <li style="display: none;"><select class="form-control">
                                        <option value=""></option>
                                    </select></li> <!----><!----><!----></ul>


                                <div class="btnbox">
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add with Image</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul v-if="product.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')"  v-if="product.out == '' && (out=='' || product.variation_mode == 1)"> Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)" > Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="middle_section" v-if="specification.length > 0"><h5>Specification</h5>
                            <table>
                                <tbody>
                                <template v-for="(item, index) in specification">
                                    <tr>
                                        <td>
                                            {{item.k1}}
                                        </td>
                                        <td>
                                            {{item.v1}}
                                        </td>
                                        <td>
                                            {{item.k2}}
                                        </td>
                                        <td> {{item.v2}}</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="middle_section"
                            v-if="product.related_product !== undefined ? product.related_product.length !== 0 : false">
                            <h5>Related Products</h5>

                            <div id="carouselExampleControls" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in groupedItems'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="middle_section"
                         v-if="product.replacement_product !== undefined ? product.replacement_product.length !== 0 : false">
                        <h5>Replacement Product</h5>

                        <div id="carouselExampleControls_replacement" class="carousel slide">

                            <div class="carousel-inner">

                                <div v-for='(g, groupIndex) in groupedItems_replacement'
                                     :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                    <div class="row custom">
                                        <div class="col custom" v-for='(item, index) in g'>
                                            <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                            <div>
                                                <a @click="getSingleProduct(item.id)">
                                                    {{ item.code }}
                                                </a>
                                            </div>
					    <div>
                                                <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button"
                               data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button"
                               data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                        <div class="lower_section"
                            v-if="(product.notes != null && product.notes != '') || product.description != ''"><h5>
                            Description</h5>
                            <p>
                                {{ product.description }}
                            </p>
                            <p v-if="product.notes != null && product.notes != ''">
                                Notes: {{ product.notes }}
                            </p>
                        </div>
                    </template>
                    <template v-if="product.variation_mode == 1">
                        <div class="upper_section">

                            <div class="imagebox">
                                <div class="selected_image">
                                    <img :src="url" v-if="url !== ''">
                                </div>
                                <div class="image_list">
                                    <img v-if="product.photo1" :src="img_url + product.photo1"
                                        @click="change_url(product.photo1)"/>
                                    <img v-if="product.photo2" :src="img_url + product.photo2"
                                        @click="change_url(product.photo2)"/>
                                    <img v-if="product.photo3" :src="img_url + product.photo3"
                                        @click="change_url(product.photo3)"/>
                                    <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                                </div>

                            </div>


                            <div class="infobox">
                                <div class="basic_info">
                                <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                                <div class="last_order_history"  v-if="product.is_last_order != ''">

                                <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                <button @click="last_order_info(product.is_last_order)" v-if="product.last_have_spec">Last Order History</button>
                                <span v-if="product.last_order_url != ''">Last Ordered: {{ product.last_order_at }} at <a :href="product.last_order_url">{{ product.last_order_name }}</a></span>
                                </div>
                                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="out_cnt == 1" @click="PhaseOutAlert(product.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="out_cnt > 1" @click="PhaseOutAlert(product.phased_out_text1)">{{ out_cnt }} variants are phased out</span>

                                    <h3>{{product.code}}</h3> <h6>{{product.brand}}</h6>
                                    <h6 v-if="category == 'Lighting'">{{ product.category}}</h6>
                                    <h6 v-if="category != 'Lighting'">{{ product.category}} >> {{
                                        product.sub_category_name}}</h6>
                                    <div class="tags" v-if="product.tags !== undefined ? product.tags[0] !== '' : false">
                                        <span v-for="(it, index) in product.tags">{{ it }}</span>
                                    </div>
                                </div>

                                <ul class="price_stock">

                                    <li>
                                        Suggested Retail Price: <span>{{price}}</span><span></span>
                                    </li>

                                    <li>
                                        Quoted Price: <span>{{quoted_price}}</span><span></span>
                                    </li>

                                </ul>

                                <ul class="variants">
                                    <li>
                                        Select:
                                    </li>
                                    <li v-if="product.variation1_value[0] !== '' && product.variation1_value[0] !== undefined">
                                        {{ product.variation1 !== 'custom' ? product.variation1 :
                                        product.variation1_custom}}
                                    </li>
                                    <li v-show="product.variation1_value[0] !== '' && product.variation1_value[0] !== undefined">
                                        <select class="form-control" v-model="v1" @change="change_v()">
                                            <option value=""></option>
                                            <option v-for="(item, index) in product.variation1_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="product.variation2_value[0] !== '' && product.variation2_value[0] !== undefined">
                                        {{ product.variation2 !== 'custom' ? product.variation2 : product.variation2_custom
                                        }}
                                    </li>
                                    <li v-show="product.variation2_value[0] !== '' && product.variation2_value[0] !== undefined">
                                        <select class="form-control" v-model="v2" @change="change_v()">
                                            <option value=""></option>
                                            <option v-for="(item, index) in product.variation2_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="product.variation3_value[0] !== '' && product.variation3_value[0] !== undefined">
                                        {{ product.variation3 !== 'custom' ? product.variation3 : product.variation3_custom
                                        }}
                                    </li>
                                    <li v-show="product.variation3_value[0] !== '' && product.variation3_value[0] !== undefined">
                                        <select class="form-control" v-model="v3" @change="change_v()">
                                            <option value=""></option>
                                            <option v-for="(item, index) in product.variation3_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>
                                    <li v-if="product.variation4_value[0] !== '' && product.variation4_value[0] !== undefined">
                                        {{ product.variation4 !== 'custom' ? product.variation4 : product.variation4_custom
                                        }}
                                    </li>
                                    <li v-show="product.variation4_value[0] !== '' && product.variation4_value[0] !== undefined">
                                        <select class="form-control" v-model="v4" @change="change_v()">
                                            <option value=""></option>
                                            <option v-for="(item, index) in product.variation4_value" :value="item"
                                                    :key="item">{{item}}
                                            </option>
                                        </select>
                                    </li>

                                    <template v-for="(item, index) in product.accessory_infomation" v-if="show_accessory">
                                        <li>{{ item.category }}</li>
                                        <li>
                                            <select class="selectpicker" data-width="100%" :id="'tag'+index">
                                                <option :data-thumbnail="detail.url"
                                                        v-for="(detail, index) in item.detail[0]">
                                                    {{detail.code}}
                                                </option>
                                            </select>
                                        </li>
                                    </template>

                                </ul>

                                <div class="btnbox">
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add with Image</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>

                                    </ul>
                                </div>

                            </div>

                        </div>


                        <div class="middle_section" v-if="specification.length > 0">
                            <h5>Specification</h5>

                            <table>
                                <tbody>
                                <template v-for="(item, index) in specification">
                                    <tr>
                                        <td>
                                            {{item.k1}}
                                        </td>
                                        <td>
                                            {{item.v1}}
                                        </td>
                                        <td>
                                            {{item.k2}}
                                        </td>
                                        <td> {{item.v2}}</td>
                                    </tr>
                                </template>

                                </tbody>

                            </table>

                        </div>

                        <div class="middle_section"
                            v-if="product.related_product !== undefined ? product.related_product.length !== 0 : false">
                            <h5>Related Products</h5>

                            <div id="carouselExampleControls" class="carousel slide">

                                <div class="carousel-inner">

                                    <div v-for='(g, groupIndex) in groupedItems'
                                        :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                        <div class="row custom">
                                            <div class="col custom" v-for='(item, index) in g'>
                                                <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                                <div>
                                                    <a @click="getSingleProduct(item.id)">
                                                        {{ item.code }}
                                                    </a>
                                                </div>
                                                <div>
                                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        <div class="middle_section"
                         v-if="product.replacement_product !== undefined ? product.replacement_product.length !== 0 : false">
                        <h5>Replacement Product</h5>

                        <div id="carouselExampleControls_replacement" class="carousel slide">

                            <div class="carousel-inner">

                                <div v-for='(g, groupIndex) in groupedItems_replacement'
                                     :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                                    <div class="row custom">
                                        <div class="col custom" v-for='(item, index) in g'>
                                            <img :src="img_url + item.photo1" :alt="'No Product Picture'">
                                            <div>
                                                <a @click="getSingleProduct(item.id)">
                                                    {{ item.code }}
                                                </a>
                                            </div>
					    <div>
                                                <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                                <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button"
                               data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button"
                               data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                        <div class="lower_section"
                            v-if="(product.notes != null && product.notes != '') || product.description != ''">
                            <h5>Description</h5>
                            <p>
                                {{ product.description }}
                            </p>
                            <p v-if="product.notes != null && product.notes != ''">
                                Notes: {{ product.notes }}
                            </p>
                            <!--
                            <div class="desc_imgbox">
                                <img src="images/realwork.png">
                                <img src="images/realwork.png">
                                <img src="images/wash_hands.png">
                                <img src="images/realwork.png">
                            </div>
                            -->
                        </div>
                    </template>
                </template>

            </div>

        </div>


    </div>


    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_specification_sheet">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">Export Specification Sheet</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                        <div class="left_function"> On Specification Sheet:
                            <input type="checkbox" class="alone" v-model="p_pid"> Show Product ID 
                            <input type="checkbox" class="alone" v-model="p_brand"> Show Brand Name
                            <input type="checkbox" class="alone" v-model="srp"> Show SRP on Specification Sheet
                            <input type="checkbox" class="alone" v-model="qp"> Show QP on Specification Sheet
                        </div>

                        <a class="btn small green" @click="prod_export()">Export</a>

                    </div>

                    <!--
                    <div class="list_function" style="margin: 7px 0;">
                        <div class="pagenation">
                            <a class="prev" :disabled="product_page == 1" @click="pre_page(); filter_apply();">Prev
                                10</a>
                            <a class="page" v-for="pg in product_pages_10" @click="product_page=pg; filter_apply(pg);"
                               v-bind:style="[pg == product_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                                pg
                                }}</a>
                            <a class="next" :disabled="product_page == product_pages.length"
                               @click="nex_page(); filter_apply();">Next
                                10</a>
                        </div>
                    </div>
                    -->


                    <div>
                        <table id="tb_specification_list" class="table  table-sm table-bordered">
                            <thead>
                            <tr>
                                <th><i class="micons" @click="selectall()">view_list</i></th>
                                <th>Image</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(item, index) in product_array">
                                <td v-if="item.pid != 0">
                                    <input type="checkbox" class="alone" :true-value="1" v-model:checked="item.is_selected" >
                                </td>
                                <td  v-if="item.pid == 0">
                                    No Match in Product Database
                                </td>

                                <td>
                                    <img v-if="item.url != ''" :src="item.url">
                                </td>

                                <td>
                                    <div :class="['pid', 'noPrint', (item.status == -1 ? 'deleted' : '')]" v-if="item.pid != 0">{{ "ID: " +  item.pid}}</div>
                                    <div :class="['code', (item.status == -1 ? 'deleted' : '')]">{{ item.code }}</div>
                                    <div class="brief">{{ item.brief }}</div>
                                    <div class="listing">{{ item.desc }}{{ item.list }}</div>
                                </td>

                                <td>
                                    <i class="fas fa-arrow-alt-circle-up" @click="item_up(index, item.id)"></i>
                                    <i class="fas fa-arrow-alt-circle-down" @click="item_down(index, item.id)"></i>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                    </div>


                </div>

            </div>


        </div>

    </div>



    <!-- Modal for Signature Codebook -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_signature_codebook">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">Signature Codebook</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div>
                        <table id="tb_signature_codebook" class="table  table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Signature</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(item, index) in signature_codebook">
                                <td> {{ item.name }} </td>
                                <td> {{ item.position }} </td>
                                <td> {{ item.phone }} </td>
                                <td> {{ item.email }} </td>
                                <td> <img v-if="item.url != ''" :src="item.url"> </td>
                                <td>
                                    <a class="btn small yellow" @click="signature_import(item)">Import</a>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                    </div>


                </div>

            </div>


        </div>

    </div>


    <!-- Modal for LED Driver Computation -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_driver_computation">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1500px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">LED Driver Computation</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="modal_function" style="width: 100%; display: flex; align-items: center;">
                        <a class="btn small green" @click="add_led()">Add Item</a>
                    </div>

                    <div>
                        <table id="tb_driver_computation" class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>ITEM NO.</th>
                                <th>LOCATION (AREA)</th>
                                <th>QUANTITY</th>
                                <th>LED STRIP (WATTAGE)</th>
                                <th>TOTAL (LENGTH IN METERS)</th>
                                <th>TOTAL (WATTAGE)</th>
                                <th>DRIVER LOSSES (20%)</th>
                                <th>TOTAL LED DRIVER WATTAGE</th>
                                <th>LED DRIVER WATTAGE AVAILABLE</th>
                                <th>LED DRIVER QTY</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>

                            <tr v-for="(item, index) in led_array">
                                <td><input type="text" v-model="item.no"></td>
                                <td><textarea rows="3" v-model="item.area"></textarea></td>
                                <td><input type="number" v-model="item.qty"></td>
                                <td><input type="number" v-model="item.watt"></td>
                                <td><input type="number" v-model="item.length"></td>
                                <td>{{ Number((parseFloat(item.qty) || 0) * (parseFloat(item.watt) || 0) * (parseFloat(item.length) || 0)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>{{ Number((parseFloat(item.qty) || 0) * (parseFloat(item.watt) || 0) * (parseFloat(item.length) || 0) * .2).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>{{ Number((parseFloat(item.qty) || 0) * (parseFloat(item.watt) || 0) * (parseFloat(item.length) || 0) * 1.2).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>
                                    <select v-model="item.tag" @change="led_driver_search(item)">
                                        <option value="NON-DIMMABLE">NON-DIMMABLE</option>
                                        <option value="TRIAC">TRIAC</option>
                                        <option value="0/1-10V">0/1-10V</option>
                                        <option value="DALI">DALI</option>
                                    </select>

                                    <select v-model="item.range" @change="led_driver_search(item)">
                                        <option value="">WATTAGE RANGE</option>
                                        <option value="100W">1W~100W</option>
                                        <option value="200W">101W ~ 200W</option>
                                        <option value="300W">201W ~ 300W</option>
                                        <option value="400W">301W ~ 400W</option>
                                        <option value="500W">401W ~ 500W</option>
                                        <option value="above">501W above</option>
                                    </select>

                                    <select v-model="item.field">
                                        <option :value="product.id" v-for="product in item.products">{{ product.wattage != '' ? product.wattage + ' - ' + product.code : product.code }}</option>
                                    </select>

                                </td>
                                <td><input type="number" v-model="item.driver"></td>
                                <td>
                                    <i class="fas fa-arrow-alt-circle-up" @click="led_item_up(index, item.id)"></i>
                                    <i class="fas fa-arrow-alt-circle-down"
                                      @click="led_item_down(index, item.id)"></i>
                                    <i class="fas fa-trash-alt" @click="led_item_del(index)"></i>
                                </td>

                            </tr>


                            </tbody>
                        </table>

                    </div>


                </div>

                <div class="modal-footer">
                    <div class="btnbox">
                        <a class="btn" @click="close_led_driver()">Close</a>
                        <a class="btn green" @click="save_led_driver()">Save</a>
                        <a class="btn gray" @click="export_led_driver()">Export</a>
                    </div>
                </div>

            </div>

        </div>

    </div>




</div>


</body>

<script>
    $(".btn").click(function () {

        if ($("#collapseme").hasClass("show")) {
            $("#collapseme").removeClass("show");
        } else {
            $("#collapseme").addClass("show");
        }
    });

    window.onafterprint = (event) => {
        app.show_title = true;
    };

    function generatePDF() {
        // Select all elements with the class 'pdf-section'
        const sections = document.querySelectorAll('.qn_page');

        // Create a new container to append sections for the PDF
        const container = document.createElement('div');

        // PDF page width in inches (assuming 'letter' size)
        const pdfWidthInches = 8.5; // For A4 use 8.27 inches
        const pixelPerInch = 96; // Standard for most screens

        // Append each section to the container
        sections.forEach(section => {
            container.appendChild(section.cloneNode(true)); // Clone and append the content
        });

        // Set up html2pdf options
        const opt = {
            margin: 0.5,                        // Adjust margins as needed
            filename: 'fitted-sections.pdf',     // Output file name
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,                       // Base scale to capture content
                onclone: (clonedDoc) => {
                    // Set the cloned document to auto width to ensure full capture
                    clonedDoc.body.style.width = 'auto';
                }
            },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        // Use html2pdf to generate the PDF and scale the canvas to fit PDF width
        html2pdf().from(container).toPdf().get('pdf').then((pdf) => {


            // Adjust the canvas scale factor to fit the PDF page width
            opt.html2canvas.scale = 2;

            // Render the PDF with the scaled canvas
            return html2pdf().from(container).set(opt).save();
        });
    }

        async function generate_pdf_test() {
            const { jsPDF } = window.jspdf;

            const items = document.querySelectorAll('.qn_page');

            const pdf = new jsPDF('', 'pt', 'a4');

            for (let i = 0; i < items.length; i++) {
                const item = items[i];

                const canvas = await html2canvas(item, { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true });

                const contentWidth = canvas.width;
                const contentHeight = canvas.height;

                const pageHeight = contentWidth / 592.28 * 841.89;
                let leftHeight = contentHeight;
                let position = 0;
                const imgWidth = 595.28;
                const imgHeight = 592.28 / contentWidth * contentHeight;

                while (leftHeight > 0) {
                    const pageData = canvas.toDataURL('image/jpeg', 1.0);

                    pdf.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight);

                    leftHeight -= pageHeight;
                    position -= 841.89;

                    
                    pdf.addPage();
                    
                }

                
            }

            // remove last page
            pdf.deletePage(pdf.internal.getNumberOfPages());

            pdf.save('quotation_' + app.quotation_no + '.pdf');
        }
    </script>


</script>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/quotation_v4.js"></script>
<script defer src="js/html2canvas/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</html>