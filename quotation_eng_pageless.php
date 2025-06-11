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

            if($decoded->data->limited_access == true)
                header( 'location:index' );

$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales' || $GLOBALS['department'] == 'Engineering'){
$test_manager = "1";
}

//  ('Kuan', 'Dennis Lin', 'dereck', 'Ariel Lin', 'Kristel Tan');
if($user_id == 48 || $user_id == 2 || $user_id == 11 || $user_id == 6 ||  $user_id == 1 || $user_id == 3 || $user_id == 89 || $user_id == 129 || $user_id == 137 || $user_id == 138 || $user_id == 148 || $user_id == 191 || $user_id == 195)
$test_manager = "1";
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

        body.gray {
            counter-reset: PageNumber;
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
            z-index: 1;
            display: none;
        }

        .qn_page {
            width: 1200px;
            background-color: white;
            position: relative;
            overflow-x: auto;
        }

        .qn_page .qn_header_space {
            height: 305px;
        }

        .qn_page .qn_header {
            width: 100%;
            height: 275px;
            background: url('images/Feliix-QuoteBG-03.png');
            background-size: 100% auto;
            position: absolute;
            top: 0;
            left: 0;
        }

        .qn_header .left_block {
            width: 71%;
            float: left;
            padding-left: 30px;
        }

        .qn_header .left_block img.logo {
            display: block;
            width: 166px;
            margin-top: 35px;
        }

        .qn_header .left_block .qn_title {
            margin-top: 50px;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            height: 76.8px;
        }

        .qn_header .left_block .qn_title > div {
            height: 38.4px;
        }

        .qn_header .left_block .project_category {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            height: 33.6px;
        }

        .qn_header .left_block .project_category > .line1 {
            height: 16.8px;
        }

        .qn_header .right_block {
            width: 29%;
            float: right;
            padding-right: 3px;
        }

        .qn_header .right_block .qn_number_date {
            margin-top: 35px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
        }

        .qn_header .right_block .qn_number_date .qn_number,
        .qn_header .right_block .qn_number_date .qn_date,
        .qn_header .right_block .qn_for div,
        .qn_header .right_block .qn_by div {
            font-weight: 700;
        }

        .qn_header .right_block .qn_for {
            margin-top: 45px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            height: 67.2px;
        }

        .qn_header .right_block .qn_for > div {
            height: 16.8px;
            width: 100%;
            overflow: hidden;
        }

        .qn_header .right_block .qn_by {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            height: 50.4px;
        }

        .qn_header .right_block .qn_by > div {
            height: 16.8px;
        }

        .qn_page .qn_body {
            padding: 0 30px;
        }

        .qn_page .qn_footer_space {
            height: 107px;
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

        .area_total .amount_in_words {
            font-weight: 600;
            text-align: center;
            font-style: italic;
            margin: 7px;
        }

        .qn_body .area_subtotal {
            width: 100%;
        }

        .area_subtotal .tb_format3 {
            width: 100%;
        }

        .tb_format3 tbody tr td {
            text-align: left;
            vertical-align: top;
            font-size: 14px;
            padding: 15px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format3 tbody tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format3 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format3 tbody tr td div.pid, .tb_format3 tbody tr td div.description {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
        }

        .tb_format3 tbody tr td div.unit {
            font-size: 14px;
            font-weight: 800;
            word-break: break-all;
        }

        .tb_format3 tbody tr td span.numbers {
            font-weight: 800;
            font-size: 14px;
        }

        .tb_format3 tbody tr td span.numbers.red {
            color: red;
        }

        .tb_format3 tbody tr td span.numbers.deleted {
            position: relative;
        }

        .tb_format3 tbody tr td span.numbers.deleted::before {
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

        .tb_format3 tbody tr td span.numbers.deleted span {
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

        .tb_format3 tbody tr.thead1 td.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
            padding: 5px 20px;
        }

        .tb_format3 tbody tr.thead2 td {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            padding: 5px 20px;
        }

        .tb_format3 tbody tr.tfoot1 td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format3 tbody tr.tfoot1 td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
            text-align: right;
            padding: 5px 30px 5px 15px;
            width: 210px;
        }

        .tb_format3 tbody tr.tfoot1 td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
            padding: 5px 15px;
            width: 210px;
        }

        .tb_format3 tbody tr.thead2 td:nth-of-type(1), .tb_format3 tbody tr.desc1 td:nth-of-type(1) {
            width: 70px;
            text-align: center;
        }

        .tb_format3 tbody tr.thead2 td:nth-of-type(3), .tb_format3 tbody tr.thead2 td:nth-of-type(4) {
            width: 75px;
        }

        .tb_format3 tbody tr.desc1 td:nth-of-type(3), .tb_format3 tbody tr.desc1 td:nth-of-type(4) {
            width: 75px;
            text-align: center;
            height: 50px;
        }

        .tb_format3 tbody tr.thead2 td:nth-of-type(5) {
            width: 210px;
        }

        .tb_format3 tbody tr.desc1 td:nth-of-type(5) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format3 tbody tr.thead2 td:nth-of-type(6) {
            width: 210px;
        }

        .tb_format3 tbody tr.desc1 td:nth-of-type(6) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        #header_dialog, #footer_dialog {
            zoom: 80%;
        }

        #requirements_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #requirements_dialog h6 {
            margin-bottom: 15px;
        }

        #requirements_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #requirements_dialog .formbox dl dd select {
            width: 310px;
        }

        #requirements_dialog div.formbox dt > input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 400px;
            margin: 5px 0;
        }

        #requirements_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
        }

        #requirements_dialog .item_form {
            max-height: 400px;
            overflow-y: auto;
        }

        .item_block {
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .item_block .function_box {
            padding: 10px 10px 15px 10px;
        }

        .item_block .content_box {
            padding: 0 10px 10px 10px;
        }

        .item_block .content_box ul {
            width: 100%;
            border-bottom: none;
            display: flex;
            margin-bottom: 0;
            align-items: center;
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .item_block .content_box ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .item_block .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            line-height: 2.5;
            border-right: 1px solid black;
        }

        .item_block .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 10px 5px 0;
        }

        .item_block .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 105px;
            margin: 5px 10px 5px 0;
        }

        .item_block .content_box ul li:nth-of-type(1) input[type='checkbox'] {
            display: inline-block;
            margin-left: 20px;
        }

        .item_block .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2.5;
        }

        .item_block .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #detail_requirements_dialog {
            min-width: 1040px;
            pointer-events: auto;
            zoom: 80%;
        }

        #detail_requirements_dialog .detailbox {
            max-height: 400px;
            overflow-y: auto;
        }

        .detailbox {
            margin-bottom: 5px;
        }

        .detailbox .title_box {
            border: 1px solid black;
            padding: 7px;
            font-weight: 700;
        }

        .detailbox .function_box {
            padding: 10px 10px 15px 10px;
        }

        .detailbox .function_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px 1px 10px;
            font-size: 14px;
            height: 30px;
            width: 250px;
        }

        .detailbox .content_box {
            padding: 0 10px 10px 10px;
        }

        .detailbox .content_box > ul {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-top: 1px solid black;
            width: 100%;
            display: flex;
            margin-bottom: 0;
            align-items: center;
        }

        .detailbox .content_box > ul:last-of-type {
            border-bottom: 1px solid black;
        }

        .detailbox .content_box ul li:nth-of-type(1) {
            width: 85%;
            padding: 3px 3px 10px 10px;
            border-right: 1px solid black;
        }

        .detailbox .content_box ul li:nth-of-type(1) span {
            display: inline-block;
            width: 137px;
            padding-right: 5px;
            text-align: right;
        }

        .detailbox .content_box ul li:nth-of-type(1) input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            margin: 5px 10px 5px 0;
        }

        .detailbox .content_box ul li:nth-of-type(1) input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 105px;
            margin: 5px 10px 5px 0;
        }

        .detailbox .content_box ul li:nth-of-type(1) textarea {
            border: 1px solid #707070;
            font-size: 14px;
            width: calc(100% - 160px);
            resize: none;
            margin: 5px 0;
        }

        .detailbox .content_box ul li:nth-of-type(1) select {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 105px;
            margin: 5px 10px 5px 0;
            padding: 0 10px;
        }

        .detailbox .content_box ul li:nth-of-type(2) {
            width: 15%;
            padding: 3px;
            text-align: center;
            line-height: 2;
        }

        .detailbox .content_box ul li:nth-of-type(2) i {
            font-size: 20px;
            margin: 0 5px;
            width: 20px;
            cursor: pointer;
        }

        #installation_dialog {
            min-width: 1040px;
            pointer-events: auto;
            zoom: 80%;
        }

        #installation_dialog .detailbox {
            max-height: 400px;
            overflow-y: auto;
        }

        #installation_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #installation_dialog .formbox dl dd select {
            width: 310px;
        }

        #installation_dialog div.formbox dt > input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 400px;
            margin: 5px 0;
        }

        #installation_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
        }

        #consumables_dialog  {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #consumables_dialog h6 {
            margin-bottom: 15px;
        }

        #consumables_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #consumables_dialog .formbox dl dd select {
            width: 310px;
        }

        #consumables_dialog div.formbox dt > input[type='text'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 400px;
            margin: 5px 0;
        }

        #consumables_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
        }

        #consumables_dialog .item_form {
            max-height: 400px;
            overflow-y: auto;
        }

        #detail_consumables_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #detail_consumables_dialog .detailbox {
            max-height: 400px;
            overflow-y: auto;
        }

        #total_dialog {
            zoom: 80%;
            pointer-events: auto;
        }

        #total_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
        }

        #terms_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #terms_dialog .termsbox {
            max-height: 400px;
            overflow-y: auto;
        }

        #terms_dialog .formbox dl {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #terms_dialog .formbox dl dd select {
            width: 370px;
        }

        #terms_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
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

        #payment_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #payment_dialog .termsbox {
            max-height: 300px;
            overflow-y: auto;
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

        #payment_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
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


        #signature_dialog {
            min-width: 700px;
            pointer-events: auto;
            zoom: 80%;
        }

        #signature_dialog .formbox dl {
            margin-bottom: 15px;
            border-bottom: 1px solid black;
        }

        #signature_dialog .formbox dl dd select {
            width: 310px;
        }

        #signature_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
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

         #work_schedule_dialog h6 a.add_work_schedule {
            background-image: url(images/ui/file-plus.svg);
            width: 30px;
            height: 30px;
            float: right;
            text-decoration: none;
            border-bottom: none;
            zoom: 0.9;
            margin-right: 0;
        }

        .work_schedule_box {
            max-height: 500px;
            overflow-y: auto;
        }

        .work_schedule_box  .work_schedule_entity {
            padding: 7px 7px 7px 10px;
        }

        .work_schedule_entity a.work_schedule_link {
            color: var(--fth05);
            transition: .3s;
            margin: 0 15px 0 0;
            font-size: 13px;
            border-bottom: none!important;
        }

        .work_schedule_entity a.work_schedule_link:before {
            content: "•";
            color: black;
            margin-right: 5px;
            font-size: 13px;
        }

        .work_schedule_entity table {
            width: 100%;
        }

        .work_schedule_entity thead tr th {
            font-size: 13px;
            text-align: center;
            border: 1px solid black;
        }

        .work_schedule_entity tbody tr td {
            font-size: 13px;
            text-align: center;
            border: 1px solid black;
        }

        .work_schedule_entity tbody tr td:nth-of-type(1),
        .work_schedule_entity tbody tr td:nth-of-type(3),
        .work_schedule_entity tbody tr td:nth-of-type(5) {
            width: 40px;
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

        .list_function.main a.print, .list_function.main a.specification, .list_function.main a.approvalform, .list_function.main a.export_excel, .list_function.main a.export_soa {
            width: 30px;
            height: 30px;
            background-color: #00811e;
            position: relative;
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

        .list_function.main a.print:hover, .list_function.main a.specification:hover, .list_function.main a.approvalform:hover, .list_function.main a.export_excel:hover, .list_function.main a.export_soa:hover {
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

        #modal_quotation_list.modal .modal_function select {
            width: 322px;
            font-size: 14px;
        }

        #modal_quotation_list.modal .modal_function input[type='text'] {
            width: 650px;
            font-size: 14px;
        }

        #tb_quotation_list {
            width: 100%;
        }

        #tb_quotation_list thead th, #tb_quotation_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        #tb_quotation_list tbody td {
            font-size: 13px;
        }

        #tb_quotation_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_quotation_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_quotation_list tbody tr td:nth-of-type(1) {
            width: 350px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(2) {
            width: 350px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(3) {
            width: 100px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(4) {
            width: 200px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(5) {
            width: 200px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(6) {
            width: 110px;
        }

        #tb_quotation_list tbody tr td:nth-of-type(1) a, #tb_quotation_list tbody tr td:nth-of-type(2) a {
            color: #0056b3;
        }

        #modal_electrical_catalog.modal .modal_function input[type='text'] {
            width: 90%;
            font-size: 14px;
        }

        #tb_electrical_catalog {
            width: 100%;
        }

        #tb_electrical_catalog thead th, #tb_electrical_catalog tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        #tb_electrical_catalog tbody td {
            font-size: 13px;
        }

        #tb_electrical_catalog thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_electrical_catalog tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_electrical_catalog tbody tr td:nth-of-type(1) {
            width: 550px;
        }

        #tb_electrical_catalog tbody tr td:nth-of-type(2) {
            width: 100px;
        }

        #tb_electrical_catalog tbody tr td:nth-of-type(3) {
            width: 200px;
        }

        #tb_electrical_catalog tbody tr td:nth-of-type(4) {
            width: 350px;
        }

        #tb_electrical_catalog tbody tr td:nth-of-type(5) {
            width: 110px;
        }

        #modal_electrical_tool_catalog.modal .modal_function input[type='text'] {
            width: 90%;
            font-size: 14px;
        }

        #tb_electrical_tool_catalog {
            width: 100%;
        }

        #tb_electrical_tool_catalog thead th, #tb_electrical_tool_catalog tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        #tb_electrical_tool_catalog tbody td {
            font-size: 13px;
        }

        #tb_electrical_tool_catalog thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        #tb_electrical_tool_catalog tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        #tb_electrical_tool_catalog tbody tr td:nth-of-type(1) {
            width: 550px;
        }

        #tb_electrical_tool_catalog tbody tr td:nth-of-type(2) {
            width: 100px;
        }

        #tb_electrical_tool_catalog tbody tr td:nth-of-type(3) {
            width: 200px;
        }

        #tb_electrical_tool_catalog tbody tr td:nth-of-type(4) {
            width: 350px;
        }

        #tb_electrical_tool_catalog tbody tr td:nth-of-type(5) {
            width: 110px;
        }

        .dialog.reverse {
            left: unset;
        }

        .dialog.reverse::before {
            border-color: transparent var(--fth04) transparent transparent;
            right: -5px;
            left: unset;
        }

        .dialog.reverse::after {
            border-color: transparent #fff transparent transparent;
            right: 0;
            left: unset;
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
                zoom: 93%;
                margin: 1px 0px 0px 7px;
                page-break-after: always;
                overflow-y: hidden;
            }

            .qn_page .qn_header, .qn_page .qn_footer {
                position: fixed;
            }

            .noPrint {
                display: none;
            }
        }

        @page {
            size: A3 portrait;
            margin: 0;
        }

    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <div class="mask" :ref="'mask'"></div>

    <!-- header -->
    <header class="noPrint">header</header>
    <!-- header end -->

    <!-- Function Bar start-->
    <div class="functionbar noPrint">
        <div class="list_function main noPrint">

            <div class="block">
                <!-- print -->
                <div class="popupblock">
                    <a id="" class="print" title="Export Whole Quotation into PDF" @click="print_page()"></a>
                </div>

            <!-- 以下三個功能需要提供，但因為這個報價單的格式改變很多，所以下面三個功能需要大修改 -->
                <div class="popupblock">
                    <a id="" class="export_excel" title="Export Simple Item List into Excel" @click="export_excel()"></a>
                </div>
<!--
                <div class="popupblock">
                    <a id="" class="approvalform" title="Generate Corresponding Approval Form" @click="approval_form_post()"></a>
                </div>

                <div class="popupblock">
                    <a id="" class="export_soa" title="Generate Corresponding Statement of Account" @click="soa_post()"></a>
                </div>
            -->

            </div>

            <div class="block fn" id="menu">
                <div class="popupblock">
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
                                <!-- <dt>Project Category:</dt>
                                <dd>
                                    <select v-model="temp_project_category">
                                        <option value="Lighting">Lighting</option>
                                        <option value="Office Systems">Office Systems</option>
                                    </select>
                                </dd> -->
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
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_header()" v-if="submit == false">Close</a>
                                    <a class="btn small green" @click="save_header()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock">
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


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_requirements = !show_requirements">General Requirements</a>
                    <?php
                    } else {
                    ?>
                    <a>General Requirements</a>
                    <?php
                    }
                    ?>
                    <div id="requirements_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_requirements">
                        <h6>General Requirements</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Name of this sub block: <input type="text" v-model="temp_general_requirement.title"></dt>

                                <dt class="head">Choose whether to show the block of general requirements in this document:</dt>
                                <dd>
                                    <select v-model="temp_general_requirement.show_r">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="temp_general_requirement.show_r == ''">Distance from Next Block: <input type="number" v-model="temp_general_requirement.pixa_r"> pixel</dt>
                            </dl>
                        </div>


                        <div class="item_form">

                            <div class="item_block">

                                <div class="function_box">
                                    <a class="btn small green" @click="add_item(temp_general_requirement.block)">Add Blank Item</a>
                                </div>

                                <div class="content_box">

                                    <ul v-for="(block, block_index) in temp_general_requirement.block">

                                        <li>
                                            <span>No.:</span> <input style="width: 105px;" type="text" v-model="block.no"><br>
                                            <span>Description:</span> <input type="text" v-model="block.desc"><br>

                                            <span>Qty:</span> <input type="number" min="1" step="1" v-model="block.qty" @change="chang_block_amount(block)">
                                            <span>Unit:</span> <input style="width: 105px;" type="text" v-model="block.unit">
                                            <span>Unit Labor Cost:</span> <input type="number" v-model="block.unit_cost" disabled><br>

                                            <span>Discount:</span> <input type="number" v-model="block.discount" min="0" max="100" @change="chang_block_amount(block)" oninput="this.value|=0">
                                            <span>Total Labor Cost:</span> <input type="number" :value="block.total" @change="check_block_amount(block,$event)"><br>

                                            <input type="checkbox" class="alone" value="1" v-model="block.not_show" style="margin-left: 0;"> Not show this item
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="set_up_requirement(block_index, block.id)"></i>

                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="set_down_requirement(block_index, block.id)"></i>

                                            <i class="fas fa-trash-alt" @click="del_block_requirement(block.id)"></i>
                                        </li>
                                    </ul>

                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="show_requirements = false" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="requirements_save_pre()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_detail_requirements = !show_detail_requirements">Detail of General Requirements</a>
                    <?php
                    } else {
                    ?>
                    <a>Detail of General Requirements</a>
                    <?php
                    }
                    ?>
                    <div id="detail_requirements_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_detail_requirements">
                        <h6>Detail of General Requirements</h6>

                        <div class="tablebox s2 edit"
                             style="padding-bottom: 3px; border-bottom: 1px solid black; margin-bottom: 12px;">
                            <ul>
                                <li class="head" style="width: 160px;">Choose Item:</li>
                                <li class="mix">
                                    <select v-model="block_value">
                                        <option v-for="(block, index) in temp_general_requirement_detail.block" :value="block">{{ block.desc }}
                                        </option>
                                    </select>

                                    <a class="btn small green" @click="load_block()">Load</a>
                                </li>
                            </ul>
                        </div>


                        <div class="detailbox">

                            <div class="title_box" v-if="requirement_id != 0">
                                {{temp_detail_block.desc}}
                            </div>

                            <div class="function_box" v-if="requirement_id != 0">
                                <select id="cost_type">
                                    <option value="material">Unit Material Cost</option>
                                    <option value="labor">Unit Labor Cost</option>
                                </select>
                                <a class="btn small green" @click="add_block_a()">Add Blank Detail</a>
                                <a class="btn small green" @click="electrical_tool_catalog()">Electrical Tools and Equipments Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(detail, index) in temp_detail_block.details">
                                    <li>
                                        <span>Legend:</span> <input style="width: 105px;" type="text" v-model="detail.legend"><br>
                                        <span>Description:</span> <input type="text" v-model="detail.desc"><br>


                                        <span>Quantity:</span> <input type="number" min="1" step="1" v-model="detail.qty" @change="chang_detail_amount(detail)" oninput="this.value|=0">
                                        <span>Unit:</span> <input style="width: 105px;" type="text" v-model="detail.unit">
                                        <span>Duration (Days):</span> <input type="number" v-model="detail.duration"><br>

                                        <span v-if="detail.cost_type == 'material'">Unit Material Cost</span><span v-if="detail.cost_type == 'labor'">Labor Cost:</span> <input type="number" v-model="detail.price" @change="chang_detail_amount(detail)">
                                        <span>Multiplier(%):</span> <input type="number" v-model="detail.ratio" @change="chang_detail_amount(detail)" max="100">
                                        <span>Total:</span> <input type="number" v-model="detail.total" >
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_a_up(index, detail.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="block_a_down(index, detail.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_a_del(detail.id)"></i>
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



                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_installation = !show_installation">Installation</a>
                    <?php
                    } else {
                    ?>
                    <a>Installation</a>
                    <?php
                    }
                    ?>
                    <div id="installation_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_installation">
                        <h6>Installation</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Name of this sub block: <input type="text" v-model="temp_installation.title"></dt>

                                <dt class="head">Choose whether to show the block of lighting fixtures installation in this document:</dt>
                                <dd>
                                    <select v-model="temp_installation.show_i">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="temp_installation.show_i == ''">Distance from Next Block: <input type="number" v-model="temp_installation.pixa_i"> pixel</dt>
                            </dl>

                        </div>


                        <div class="detailbox">

                            <div class="function_box">
                                <a class="btn small green" @click="add_block_a_installation()">Add Blank Detail</a>
                                <a class="btn small green" @click="product_catalog_a()">Product Catalog</a>
                                <a class="btn small green" @click="quotation_mgt()">Import from Quotation</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(block, index) in temp_installation.block">

                                    <li>
                                        <span>No:</span> <input style="width: 105px;" type="text" v-model="block.no"><br>
                                        <span>Description:</span> <input style="width: calc(100% - 157px);" type="text" v-model="block.desc"><br>

                                        <span>Quantity:</span> <input type="number" min="1" step="1" v-model="block.qty" @change="chang_amount_installation(block)" oninput="this.value|=0">
                                        <span>Unit:</span> <input style="width: 105px;" type="text" v-model="block.unit">
                                        <span>Duration (Days):</span> <input type="number" v-model="block.duration"><br>

                                        <span>Unit Material Cost:</span> <input type="number" v-model="block.material_price" @change="chang_amount_installation_material(block)">
                                        <span>Multiplier(%):</span> <input type="number" v-model="block.ratio" @change="chang_amount_installation_material(block)">
                                        <span>Unit Labor Cost:</span> <input type="number" v-model="block.labor_price" @change="chang_amount_installation(block)"><br>

                                        <span>Total:</span> <input type="number" v-model="block.total">
                                        <span>Grouping:</span>
                                        <select v-model="block.group">
                                            <option value=""></option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                        </select>
                                    </li>

                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_a_up_installation(index, block.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="block_a_down_installation(index, block.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_a_del_installation(block.id)"></i>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="show_installation = false" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="subtotal_save_installation()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_consumables = !show_consumables">Consumables</a>
                    <?php
                    } else {
                    ?>
                    <a>Consumables</a>
                    <?php
                    }
                    ?><div id="consumables_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_consumables">
                        <h6>Consumables</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Name of this sub block: <input type="text" v-model="temp_consumable.title"></dt>

                                <dt class="head">Choose whether to show the block of consumables in this document:</dt>
                                <dd>
                                    <select v-model="temp_consumable.show_c">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="temp_consumable.show_c == ''">Distance from Next Block: <input type="number" v-model="temp_consumable.pixa_c"> pixel</dt>
                            </dl>

                        </div>


                        <div class="item_form">


                            <div class="item_block">

                                <div class="function_box">
                                    <a class="btn small green" @click="add_item_consumable(temp_consumable.block)">Add Blank Item</a>
                                </div>

                                <div class="content_box">

                                    <ul v-for="(block, block_index) in temp_consumable.block">

                                        <li>
                                            <span>No.:</span> <input style="width: 105px;" type="text" v-model="block.no"><br>
                                            <span>Description:</span> <input type="text" v-model="block.desc"><br>

                                            <span>Qty:</span> <input type="number" min="1" step="1" v-model="block.qty" @change="chang_amount_consumable(block)">
                                            <span>Unit:</span> <input style="width: 105px;" type="text" v-model="block.unit"><br>

                                            <span>Unit Labor Cost:</span> <input type="number" v-model="block.unit_cost" disabled>
                                            <span>Multiplier(%):</span> <input type="number" v-model="block.ratio" @change="chang_amount_consumable(block)">
                                            <br>

                                            <span>Discount:</span> <input type="number" v-model="block.discount" min="0" max="100" @change="chang_amount_consumable(block)" oninput="this.value|=0">
                                            <span>Total Labor Cost:</span> <input type="number" :value="block.total" @change="check_block_amount_consumable(block,$event)"><br>

                                            <input type="checkbox" class="alone" value="1" v-model="block.not_show" style="margin-left: 0;"> Not show this item
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up" @click="set_up_consumable(block_index, block.id)"></i>

                                            <i class="fas fa-arrow-alt-circle-down" @click="set_down_consumable(block_index, block.id)"></i>

                                            <i class="fas fa-trash-alt" @click="del_block_consumable(block.id)"></i>
                                        </li>
                                    </ul>

                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="show_consumables = false" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="consumalbe_save_pre()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_detail_consumables = !show_detail_consumables">Detail of Consumables</a>
                    <?php
                    } else {
                    ?>
                    <a>Detail of Consumables</a>
                    <?php
                    }
                    ?>
                    <div id="detail_consumables_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_detail_consumables">
                        <h6>Detail of Consumables</h6>

                        <div class="tablebox s2 edit"
                             style="padding-bottom: 3px; border-bottom: 1px solid black; margin-bottom: 12px;">
                            <ul>
                                <li class="head" style="width: 160px;">Choose Item:</li>
                                <li class="mix">
                                    <select v-model="block_value_consumable">
                                        <option v-for="(block, index) in temp_consumable_detail.block" :value="block">{{ block.desc }}
                                        </option>
                                    </select>

                                    <a class="btn small green" @click="load_block_consumable()">Load</a>
                                </li>
                            </ul>
                        </div>


                        <div class="detailbox"> 

                            <div class="title_box" v-if="requirement_id_consumable != 0">
                                {{block_value_consumable.desc}}
                            </div>

                            <div class="function_box" v-if="requirement_id_consumable != 0">
                                <a class="btn small green" @click="add_block_a_consumable()">Add Blank Detail</a>
                                <a class="btn small green" @click="electrical_catalog()">Electrical Materials Catalog</a>
                            </div>

                            <div class="content_box">

                                <ul v-for="(detail, index) in temp_detail_block_consumable.details">
                                    <li>
                                        <span>Quantity:</span> <input type="number" min="1" step="1" v-model="detail.qty" @change="chang_detail_amount_consumable(detail)" oninput="this.value|=0">
                                        <span>Unit:</span> <input type="text" style="width: 105px;" v-model="detail.unit"><br>

                                        <span>Particulars:</span> <input type="text" style="width: calc(100% - 160px);" v-model="detail.particulars"><br>

                                        <span>Price:</span> <input type="number" v-model="detail.price" @change="chang_detail_amount_consumable(detail)">
                                        <span>Total:</span> <input type="number" v-model="detail.total" ><br>

                                        <span>Remarks:</span> <textarea rows="2" v-model="detail.remark"></textarea>
                                    </li>
                                    <li>
                                        <i class="fas fa-arrow-alt-circle-up" @click="block_a_up_consumable(index, detail.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="block_a_down_consumable(index, detail.id)"></i>
                                        <i class="fas fa-trash-alt" @click="block_a_del_consumable(detail.id)"></i>
                                    </li>
                                </ul>

                            </div>
                        </div>


                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_detail_consumables()" v-if="submit == false">Close</a>
                                <a class="btn small green" @click="subtotal_save_consumable()" v-if="is_load_consumable">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>



                <div class="popupblock">

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
                            <dl style="margin-bottom: 0px; border-bottom: 1px solid black;">
                                <dt class="head">Choose whether to show the block of grand total in this document:</dt>
                                <dd>
                                    <select v-model="show">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="show == ''">Distance from Next Block: <input type="number" v-model="pixa"> pixel</dt>
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
                                        <option value="Y">Yes (12% VAT is shown in the block of Total)</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Show "*price inclusive of VAT" in the Quotation:</dt>
                                <dd>
                                    <select v-model="total.show_vat">
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Quotation Valid for:</dt>
                                <dd>
                                    <input type="text" v-model="total.valid"
                                           placeholder="Input like 1 month, 45 days, ...">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Show "Amount in Words" in the Quotation:</dt>
                                <dd>
                                    <select v-model="total.show_word">
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
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


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_term = !show_term">Terms and
                        Condition</a>
                    <?php
                    } else {
                    ?>
                    <a>Terms and Condition</a>
                    <?php
                    }
                    ?>
                    <div id="terms_dialog" class="dialog fn2 show reverse" :ref="'dlg_fn2'" v-show="show_term" style="right: 160px;">
                        <h6>Terms and Condition</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose whether to show the block of terms and condition in this document:</dt>
                                <dd>
                                    <select v-model="show_t">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="show_t == ''">Distance from Next Block: <input type="number" v-model="pixa_t"> pixel</dt>
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


                <div class="popupblock">

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
                    <div id="payment_dialog" class="dialog fn2 show reverse" :ref="'dlg_fn2'" v-show="show_payment_term" style="right: 120px;">
                        <h6>Payment Terms</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose whether to show the block of payment terms in this document:</dt>
                                <dd>
                                    <select v-model="show_p">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="show_p == ''">Distance from Next Block: <input type="number" v-model="pixa_p"> pixel</dt>
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


                <div class="popupblock">

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
                    <div id="signature_dialog" class="dialog fn2 show reverse" :ref="'dlg_fn2'" v-show="show_signature" style="right: 80px;">
                        <h6>Signature</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose whether to show the block of signature in this document:</dt>
                                <dd>
                                    <select v-model="show_s">
                                    <option value="N">No</option>
                                    <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="show_s == ''">Distance from Next Block: <input type="number" v-model="pixa_s"> pixel</dt>
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


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'"
                       @click="show_work_schedule = !show_work_schedule">Work Schedule</a>
                    <?php
                    } else {
                    ?>
                    <a>Work Schedule</a>
                    <?php
                    }
                    ?>
                    <div id="work_schedule_dialog" class="dialog fn2 show reverse" :ref="'dlg_fn2'" v-show="show_work_schedule" style="right: 95px;">
                        <h6>Work Schedule
                            <a class="add_work_schedule" @click="add_work_schedule()"></a>
                        </h6>

                        <div class="work_schedule_box">

                            <div class="work_schedule_entity" v-for="(item, index) in temp_work_schedule">

                                <a class="work_schedule_link" :href="'work_schedule_eng?id='+item.id ">Work Schedule #{{ item.id }}</a>

                                <table>
                                    <thead>
                                    <tr>
                                        <th colspan="6">Total Man Power Fee</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>x1</td>
                                        <td>{{ Math.floor(item.week1).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                        <td>x2</td>
                                        <td>{{ Math.floor(item.week2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                        <td>x3</td>
                                        <td>{{ Math.floor(item.week3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_work_schedule()" v-if="submit == false">Close</a>
                            </div>
                        </div>

                    </div>
                </div>



            </div>
        </div>

    </div>

    <!-- Function Bar end-->


    <div class="mainContent" style="background-color: rgb(230,230,230)">

        <table class="qn_page">

            <thead>
            <tr>
                <td>
                    <div class="qn_header_space">&nbsp;</div>
                    <div class="qn_header" v-if="show_title">

                        <div class="left_block">

                            <img class="logo" src="images/Feliix-Logo-Black.png">

                            <div class="qn_title">
                                <div class="line1">{{ first_line }}</div>
                                <div class="line2">{{ second_line }}</div>
                            </div>

                            <div class="project_category">
                                <div class="line1"></div>
                                <div class="line2">Installation Quotation</div>
                            </div>

                        </div>

                        <div class="right_block">

                            <div class="qn_number_date">
                                Quotation No.: <span class="qn_number">{{ quotation_no }}</span><br>
                                Date: <span class="qn_date">{{ quotation_date }}</span>
                            </div>

                            <div class="qn_for">
                                Prepare for:<br>
                                <div class="line1">{{ prepare_for_first_line }}</div>
                                <div class="line2">{{ prepare_for_second_line }}</div>
                                <div class="line3">{{ prepare_for_third_line }}</div>
                            </div>

                            <div class="qn_by">
                            Prepare by:<br>
                                <div class="line1">{{ prepare_by_first_line }}</div>
                                <div class="line2">{{ prepare_by_second_line }}</div>
                            </div>

                        </div>

                    </div>
                </td>
            </tr>
            </thead>


            <tbody>
            <tr>
                <td style="vertical-align: top;">

                    <div class="qn_body">

                        <div class="area_subtotal">

                            <!-- General Requirement 區塊的內容 -->
                            <table class="tb_format3" v-bind:style="{ 'margin-bottom': pixa_r + 'px' }" v-if="show_r == ''">

                                <tbody>

                                <!-- 表格標題列 -->
                                <tr class="thead1">
                                    <td class="title" colspan="6">{{ general_requirement.title }}</td>
                                </tr>

                                <tr class="thead2">
                                    <td>No</td>
                                    <td>Description</td>
                                    <td>Qty</td>
                                    <td>Unit</td>
                                    <td>Unit Labor Cost</td>
                                    <td>Total Labor Cost</td>
                                </tr>

                                <template v-for="(bk, index) in general_requirement.block">
                                <!-- 表格內容物 -->

                                <tr class="desc1" v-if="bk.not_show == ''">

                                    <td>{{ bk.no }}</td>

                                    <td>
                                        <div class="description">{{ bk.desc }}</div>
                                    </td>

                                    <td>
                                        <span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                                    </td>

                                    <td>
                                        <div class="unit">{{ bk.unit }}</div>
                                    </td>

                                    <!-- Unit Labor Cost -->
                                    <td>
                                        <span class="numbers" v-if="bk.discount == 0 || bk.discount == 100">₱ {{ bk.unit_cost !== undefined ? Number(bk.unit_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <span class="numbers deleted" v-if="bk.discount > 0 && bk.discount < 100">₱ {{ (bk.unit_cost  !== undefined ? Number(bk.unit_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}
                                        <span>{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br v-if="bk.discount > 0 && bk.discount < 100">
                                        <span class="numbers" v-if="bk.discount > 0 && bk.discount < 100">₱ {{ bk.unit_cost !== undefined ? Number(bk.unit_cost - (bk.unit_cost * (bk.discount / 100))).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <!-- <span class="numbers" v-if="bk.discount != 0 && (bk.discount == 100 || bk.total == '0.00')">₱ {{ bk.unit_cost !== undefined ? Number(bk.unit_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span> -->
                                    </td>

                                    <!-- Total Labor Cost -->
                                    <td v-if="bk.unit_cost == '0' || bk.unit_cost == ''">
                                        <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                    </td>

                                    <template v-if="bk.unit_cost != '0' && bk.unit_cost != '' && bk.qty * bk.unit_cost * (100 - bk.discount) / 100 == Number(bk.total)">
                                        <td v-if="Number(bk.total) != 0 && bk.total != '' && product_vat !== 'P'">
                                            <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                        </td>

                                        <td v-if="(Number(bk.total) == 0 || bk.total == '') && product_vat !== 'P'">
                                            <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost !== undefined ? Number(bk.qty * bk.unit_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers red">FREE AS PACKAGE!</span>
                                        </td>
                                    </template>

                                    <template v-if="bk.unit_cost != '0' && bk.unit_cost != '' && bk.qty * bk.unit_cost * (100 - bk.discount) / 100 != Number(bk.total)">
                                        <td v-if="Number(bk.total) != 0 && bk.total != '' && product_vat !== 'P'">
                                        <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost * (100 - bk.discount) / 100  !== undefined ? Number(bk.qty * bk.unit_cost * (100 - bk.discount) / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                        </td>

                                        <td v-if="(Number(bk.total) == 0 || bk.total == '') && product_vat !== 'P'">
                                            <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost * (100 - bk.discount) / 100  !== undefined ? Number(bk.qty * bk.unit_cost * (100 - bk.discount) / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers red">FREE AS PACKAGE!</span>
                                        </td>
                                    </template>
                                </tr>

                                </template>


                                <!-- 表格尾端，每一個 subtotal 小計金額的部分 -->
                                <tr class="tfoot1">
                                    <td colspan="4"></td>
                                    <td>SUBTOTAL</td>
                                    <td v-if="general_requirement.general_requirement_total == 0">₱ {{ general_requirement.general_requirement_total !== undefined ?
                                        Number(general_requirement.general_requirement_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                    <td v-if="general_requirement.general_requirement_total != 0">₱ {{ general_requirement.general_requirement_total !== undefined ?
                                        Number(general_requirement.general_requirement_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>



                            <!-- Lighting Fixtures Installation 區塊的內容 -->
                            <table class="tb_format3" v-bind:style="{ 'margin-bottom': pixa_i + 'px' }" v-if="show_i == ''">

                                <tbody>

                                <!-- 表格標題列 -->
                                <tr class="thead1">
                                    <td class="title" colspan="6">{{ installation.title }}</td>
                                </tr>

                                <tr class="thead2">
                                    <td>No</td>
                                    <td>Description</td>
                                    <td>Qty</td>
                                    <td>Unit</td>
                                    <td>Unit Labor Cost</td>
                                    <td>Total Labor Cost</td>
                                </tr>

                                <template v-for="(bk, index) in installation.block">
                                <!-- 表格內容物 -->

                                <tr class="desc1">

                                    <td>{{ bk.no }}</td>

                                    <td>
                                        <div class="pid noPrint" v-if="bk.pid != 0 && bk.pid != undefined">{{ "ID: " + bk.pid }}</div>
                                        <div class="description">{{ bk.desc }}</div>
                                    </td>

                                    <td>
                                        <span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                                    </td>

                                    <td>
                                        <div class="unit">{{ bk.unit }}</div>
                                    </td>

                                    <!-- Unit Labor Cost -->
                                    <!-- 這邊需要按照 Grouping，把同一群的商品的 Unit Labor Cost 加總起來，然後變成一欄 -->
                                    <td :rowspan="bk.gp_cnt" v-if="bk.gp_cnt != 0">
                                        <span class="numbers">₱ {{ bk.gp_cost  !== undefined ? Number(bk.gp_cost ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <!-- <span class="numbers" v-if="(bk.gp_total != '0.00')">₱ {{ bk.gp_cost  !== undefined ? Number(bk.gp_cost ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <span class="numbers" v-if="(bk.gp_total == '0.00')">₱ {{ bk.gp_cost  !== undefined ? Number(bk.gp_cost ).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span> -->
                                    </td>

                                    <!-- Total Labor Cost -->
                                    <!-- 這邊需要按照 Grouping，把同一群的商品的 Total Labor Cost 加總起來，然後變成一欄 -->
                                    <td :rowspan="bk.gp_cnt" v-if="bk.gp_cnt != 0 && (bk.gp_total != 0 && bk.gp_total != '') && product_vat !== 'P'">
                                        <span class="numbers">₱ {{ bk.gp_total !== undefined ? Number(bk.gp_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                    </td>

                                    <td :rowspan="bk.gp_cnt" v-if="bk.gp_cnt != 0 && (bk.gp_total == '0' || bk.gp_total == '') && product_vat !== 'P'">
                                        <span class="numbers deleted">₱ {{ (bk.qty  * bk.gp_cost  !== undefined ? Number(bk.qty  * bk.gp_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                        <span class="numbers red">FREE AS PACKAGE!</span>
                                    </td>
                                </tr>

                                </template>


                                <!-- 表格尾端，每一個 subtotal 小計金額的部分 -->
                                <tr class="tfoot1">
                                    <td colspan="4"></td>
                                    <td>SUBTOTAL</td>
                                    <td v-if="installation.installation_total == 0">₱ {{ installation.installation_total !== undefined ?
                                        Number(installation.installation_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                    <td v-if="installation.installation_total != 0">₱ {{ installation.installation_total !== undefined ?
                                        Number(installation.installation_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>



                            <!-- Consumables 區塊的內容 -->
                            <table class="tb_format3" v-bind:style="{ 'margin-bottom': pixa_c + 'px' }" v-if="show_c == ''">

                                <tbody>

                                <!-- 表格標題列 -->
                                <tr class="thead1">
                                    <td class="title" colspan="6">{{ consumable.title }}</td>
                                </tr>

                                <tr class="thead2">
                                    <td>No</td>
                                    <td>Description</td>
                                    <td>Qty</td>
                                    <td>Unit</td>
                                    <td>Unit Labor Cost</td>
                                    <td>Total Labor Cost</td>
                                </tr>

                                <template v-for="(bk, index) in consumable.block">
                                <!-- 表格內容物 -->

                                <tr class="desc1"  v-if="bk.not_show == ''">

                                    <td>{{ bk.no }}</td>

                                    <td>
                                        <div class="description">{{ bk.desc }}</div>
                                    </td>

                                    <td>
                                        <span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                                    </td>

                                    <td>
                                        <div class="unit">{{ bk.unit }}</div>
                                    </td>

                                    <!-- Unit Labor Cost 這裡的 Unit Price 需要放的是 unit price 乘上 multiplier 的結果-->
                                    <td>
                                        <span class="numbers" v-if="bk.discount == 0 || bk.discount == 100">₱ {{ bk.unit_cost !== undefined ? Number(bk.unit_cost*( bk.ratio !== undefined ? (bk.ratio != '' ? bk.ratio : 1 ) : 1 )).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <span class="numbers deleted" v-if="bk.discount > 0 && bk.discount < 100">₱ {{ (bk.unit_cost  !== undefined ? Number(bk.unit_cost*( bk.ratio !== undefined ? (bk.ratio != '' ? bk.ratio : 1 ) : 1 )).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}
                                        <span>{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br v-if="bk.discount > 0 && bk.discount < 100">
                                        <span class="numbers" v-if="bk.discount > 0 && bk.discount < 100">₱ {{ bk.unit_cost !== undefined ? Number((bk.unit_cost - (bk.unit_cost * (bk.discount / 100)))*( bk.ratio !== undefined ? (bk.ratio != '' ? bk.ratio : 1 ) : 1 )).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                        <!-- <span class="numbers" v-if="bk.discount != 0 && (bk.discount == 100 || bk.total == '0.00')">₱ {{ bk.unit_cost !== undefined ? Number(bk.unit_cost).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span> -->
                                    </td>

                                    <!-- Total Labor Cost -->
                                    <td v-if="bk.unit_cost == '0' || bk.unit_cost == '' || bk.ratio == '0'">
                                        <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                    </td>

                                    <template v-if="bk.unit_cost != '0' && bk.unit_cost != '' && bk.ratio != '0' && bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio) == Number(bk.total)">
                                        <td v-if="Number(bk.total) != 0 && bk.total != '' && product_vat !== 'P'">
                                            <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                        </td>

                                        <td v-if="(Number(bk.total) == 0 || bk.total == '') && product_vat !== 'P'">
                                            <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost * (bk.ratio == '' ? 1 : bk.ratio)  !== undefined ? Number(bk.qty * bk.unit_cost * (bk.ratio == '' ? 1 : bk.ratio)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers red">FREE AS PACKAGE!</span>
                                        </td>
                                    </template>

                                    <template v-if="bk.unit_cost != '0' && bk.unit_cost != '' && bk.ratio != '0' && bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio) != Number(bk.total)">
                                        <td v-if="Number(bk.total) != 0 && bk.total != '' && product_vat !== 'P'">
                                        <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio)  !== undefined ? Number(bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers">₱ {{ bk.total !== undefined ? Number(bk.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }} </span>
                                        </td>

                                        <td v-if="(Number(bk.total) == 0 || bk.total == '') && product_vat !== 'P'">
                                            <span class="numbers deleted">₱ {{ (bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio)  !== undefined ? Number(bk.qty * bk.unit_cost * (100 - bk.discount) / 100  * (bk.ratio == '' ? 1 : bk.ratio)).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}</span><br>
                                            <span class="numbers red">FREE AS PACKAGE!</span>
                                        </td>
                                    </template>
                                </tr>

                                </template>


                                <!-- 表格尾端，每一個 subtotal 小計金額的部分 -->
                                <tr class="tfoot1">
                                    <td colspan="4"></td>
                                    <td>SUBTOTAL</td>
                                    <td v-if="consumable.consumable_total == 0">₱ {{ consumable.consumable_total !== undefined ?
                                        Number(consumable.consumable_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                    <td v-if="consumable.consumable_total != 0">₱ {{ consumable.consumable_total !== undefined ?
                                        Number(consumable.consumable_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g,
                                        "$1,") : '0.00' }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>


                        <div class="area_total" v-bind:style="{ 'margin-bottom': pixa + 'px' }" v-if="show == ''">
                            <table class="tb_total">
                                <tbody>
                                <tr>
                                    <td :rowspan="(temp_total.vat == 'Y' && temp_total.discount !== '0' ? 3 :  2)">
                                        <div>Remarks: Quotation valid for <span class="valid_for">{{ temp_total.valid }}</span></div>
                                        <div></div>
                                    </td>
                                    <td>SUBTOTAL</td>
                                    <td><span class="numbers">₱ {{ temp_total.back_total !== undefined ? Number(temp_total.back_total).toFixed(2).toLocaleString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") : '0.00' }}</span>
                                    </td>
                                </tr>

                                <tr class="total_discount" v-if="temp_total.discount != '0'">
                                    <td>{{ temp_total.discount !== undefined ?
                                        Math.floor(temp_total.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}%
                                        DISCOUNT
                                    </td>
                                    <td><span class="numbers">₱ {{ temp_total.back_total !== undefined ? (temp_total.back_total * (temp_total.discount) / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                                    </td>
                                </tr>

                                <tr class="total_vat" v-if="temp_total.vat == 'Y'">
                                    <td>(12% VAT)</td>
                                    <td><span class="numbers">₱ {{ (((subtotal_info_not_show_a + subtotal_info_not_show_b + subtotal_info_not_show_c) * (100 - temp_total.discount) / 100) * 12 / 100) !== undefined ? (((subtotal_info_not_show_a + subtotal_info_not_show_b + subtotal_info_not_show_c) * (100 - temp_total.discount) / 100) * 12 / 100).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                                    </td>
                                </tr>
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>
                                        <span class="total_discount" v-if="temp_total.show_vat == 'Y'">*price inclusive of VAT</span>
                                    </td>
                                    <td>GRAND TOTAL</td>
                                    <td v-if="temp_total.total != '' && temp_total.total != '0.00'">
                                        <span class="numbers deleted" v-if="temp_total.total != temp_total.real_total">₱ {{ temp_total.real_total !== "" ? Number(temp_total.real_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span><br
                                            v-if="temp_total.total != temp_total.real_total">
                                        <span class="numbers">₱ {{ temp_total.total !== "" ? Number(temp_total.total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                                    </td>
                                    <td v-if="temp_total.total == '' || temp_total.total == '0.00'">
                                        <span class="numbers">₱ {{ temp_total.real_total !== "" ? Number(temp_total.real_total).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "0.00" }}</span>
                                    </td>
                                </tr>
                                </tfoot>

                            </table>

                            <div class="amount_in_words" v-if="temp_total.show_word == 'Y'">Amount in Words : {{ temp_total.total_text }}</div>
                        </div>



                        <div class="area_terms" v-bind:style="{ 'margin-bottom': pixa_t + 'px' }" v-if="show_t == ''">
                            <div class="terms" v-for="(tt, index) in temp_term.item">
                                <div class="title">{{ tt.title }}</div>
                                <div class="brief" :style="tt.brief == '' ? 'white-space: pre-line; display: none;' : 'white-space: pre-line;'">{{ tt.brief }}</div>
                                <div class="listing" style="white-space: pre-line;">{{ tt.list }}</div>
                            </div>
                        </div>



                        <div class="area_payment" v-bind:style="{ 'margin-bottom': pixa_p + 'px' }" v-if="payment_term !== undefined && show_p == ''">
                            <table class="tb_payment">
                                <tbody>
                                <tr>
                                    <td colspan="2">Payment Terms:</td>
                                    <td>
                                        <div>
                                            <span v-for="(tt, index) in payment_term.payment_method_list">{{ tt }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        {{ payment_term.brief }}
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

                                        <div class="acount_info" v-for="(tt, index) in payment_term.item">
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


                        <div class="area_conforme" v-bind:style="{ 'margin-bottom': pag.pixa_s + 'px' }" v-if="pag.show_s == ''">
                            <div class="conforme"
                                v-if="(pag.sig != undefined ? pag.sig.item_client.length : 0)  + (pag.sig != undefined ?  pag.sig.item_company.length : 0) > 0">
                                CONFORME
                            </div>

                            <div class="client_signature" v-if="(pag.sig != undefined ? pag.sig.item_client.length : 0) > 0">

                                <div class="signature" v-for="(tt, index) in pag.sig.item_client">
                                    <div class="pic"></div>
                                    <div class="name">{{ tt.name }}</div>
                                    <div class="line1">{{ tt.position }}</div>
                                    <div class="line2">{{ tt.phone }}</div>
                                    <div class="line3">{{ tt.email }}</div>
                                </div>

                            </div>

                            <div class="company_signature"
                                 v-if="(pag.sig != undefined ? pag.sig.item_company.length : 0) > 0 && (pag.sig != undefined ? pag.sig.item_company.length : 0) <= 4">

                                <div class="signature" v-for="(tt, index) in pag.sig.item_company">
                                    <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                                    <div class="name">{{ tt.name }}</div>
                                    <div class="line1">{{ tt.position }}</div>
                                    <div class="line2">{{ tt.phone }}</div>
                                    <div class="line3">{{ tt.email }}</div>
                                </div>
                            </div>

                            <div class="company_signature"
                                 v-if="(pag.sig != undefined ? pag.sig.item_company.length : 0) > 4">

                                <div class="signature" v-for="(tt, index) in pag.sig.item_company" v-if="index < 3">
                                    <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                                    <div class="name">{{ tt.name }}</div>
                                    <div class="line1">{{ tt.position }}</div>
                                    <div class="line2">{{ tt.phone }}</div>
                                    <div class="line3">{{ tt.email }}</div>
                                </div>
                            </div>
                            <div class="company_signature" style="margin-top: -18px;" v-if="(pag.sig != undefined ? pag.sig.item_company.length : 0) > 4">

                                <div class="signature" v-for="(tt, index) in pag.sig.item_company" v-if="index >= 3">
                                    <div class="pic"><img :src="img_url + tt.photo" v-if="tt.photo != ''"></div>
                                    <div class="name">{{ tt.name }}</div>
                                    <div class="line1">{{ tt.position }}</div>
                                    <div class="line2">{{ tt.phone }}</div>
                                    <div class="line3">{{ tt.email }}</div>
                                </div>
                            </div>

                        </div>

                    </div>

                </td>
            </tr>
            </tbody>


            <tfoot>
            <tr>
                <th>
                    <div class="qn_footer_space">&nbsp;</div>

                    <div class="qn_footer">
                        <div class="foot_divider"></div>
                        <div class="line1">{{ footer_first_line }}</div>
                        <div class="line2">{{ footer_second_line }}</div>
                        <div class="qn_page_number"></div>
                    </div>
                </th>
            </tr>
            </tfoot>

        </table>
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
                            <a class="page" v-for="pag in product_pages_10" @click="product_page=pag; filter_apply(pag);"
                               v-bind:style="[pag == product_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                                pag
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
                                    <button class="btn btn-info" @click="add_with_image_set_select()" v-if="out==''">Add</button>
                                </li>
                                <!-- <li>
                                    <button class="btn btn-info" @click="add_without_image_set_select()" v-if="out==''">Add without Image</button>
                                </li>
                            </ul>

                            <ul> -->
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
                                            <button class="btn btn-info" @click="add_with_image_set(set)" v-if="set.out==''">Add</button>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>
                                        <!-- <li>
                                            這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_without_image_set(set)" v-if="set.out==''">Add without Image
                                            </button>
                                        </li> -->
                                    </ul>
<!-- 
                                    <ul v-if="set.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                             這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_with_image_set(set,'all')"  v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)"> Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_without_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)" > Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul> -->

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
                                            <button class="btn btn-info" @click="add_with_image_set(set)" v-if="set.out==''">Add</button>
                                        </li>
                                        <li>
                                            <!-- 這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_without_image_set(set)" v-if="set.out==''">Add without Image
                                            </button> -->
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>
                                    </ul>
<!-- 
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_with_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)">Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_without_image_set(set, 'all')" v-if="set.out == '' && (set.out=='' || set.variation_mode == 1)">Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul> -->

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
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add</button>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>
                                        <!-- <li>
                                            這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add without Image
                                            </button>
                                        </li> -->
                                    </ul>
<!-- 
                                    <ul v-if="product.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                            這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_with_image('all')"  v-if="product.out == '' && (out=='' || product.variation_mode == 1)"> Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            這個按鈕需要被移除掉
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)" > Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul> -->
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
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add</button>
                                            <button class="btn btn-warning" @click="close_single()">Cancel</button>
                                        </li>
                                        <!-- <li>
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add without Image
                                            </button>
                                        </li> -->
                                    </ul>
<!-- 
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_with_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            這個按鈕需要被移除掉 
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">Add all spec.
                                                without Image
                                            </button>
                                        </li>
                                    </ul> -->
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
                            <a class="page" v-for="pag in product_pages_10" @click="product_page=pag; filter_apply(pag);"
                               v-bind:style="[pag == product_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                                pag
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
                                    <div class="pid noPrint" v-if="item.pid != 0">{{ "ID: " +  item.pid}}</div>
                                    <div class="code">{{ item.code }}</div>
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


    <!-- Modal for Quotation Import -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_quotation_list">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

        <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel">List of Existing Quotations</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                    <div class="left_function" style="width: 90%;">

                        <select v-model="fil_project_category">
                            <option value="">Choose Project Category...</option>
                            <option value="2">Lighting</option>
                            <option value="1">Office Systems</option>
                        </select>

                        <select v-model="fil_project_creator">
                            <option value="">Choose Project Creator...</option>
                            <option v-for="item in users" :value="item.id" :key="item.id">
                                {{ item.username }}
                            </option>
                        </select>

                        <select v-model="fil_kind">
                            <option value="">Which Department's Task Management</option>
                            <option value="a">Admin Department</option>
                            <option value="d">Design Department</option>
                            <option value="l">Lighting Department</option>
                            <option value="o">Office Systems Department</option>
                            <option value="sl">Sales Department</option>
                            <option value="sv">Engineering Department</option>
                        </select>

                        <br>

                        <select v-model="fil_creator">
                            <option value="">Choose Quotation Creator...</option>
                            <option v-for="item in creators" :value="item.username" :key="item.username">
                                {{ item.username }}
                            </option>
                        </select>

                        <input type="text" v-model="fil_keyword"
                               placeholder="Input Keyword Here (only for quotation name, project name or quotation no.)">
                    </div>

                    <a class="btn small green" @click="filter_apply_new_quo()">Search</a>

                </div>

                <div class="list_function" style="margin: 7px 0;">
                    <div class="pagenation">
                        <a class="prev" :disabled="product_page_quo == 1" @click="pre_page_quo(); getQuoMasterRecords();">Prev
                            10</a>
                        <a class="page" v-for="pg_quo in product_pages_10_quo" @click="product_page_quo=pg_quo; getQuoMasterRecords();"
                           v-bind:style="[pg_quo == product_page_quo ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                            pg_quo
                            }}</a>
                        <a class="next" :disabled="product_page_quo == product_pages_quo.length"
                           @click="nex_page_quo(); getQuoMasterRecords();">Next
                            10</a>
                    </div>
                </div>


                <div>
                    <table id="tb_quotation_list" class="table  table-sm table-bordered">
                        <thead>
                        <tr>
                            <th>Quotation Name</th>
                            <th>Related Project / Related Task Mgt.</th>
                            <th>Quotation Number</th>
                            <th>Created Time</th>
                            <th>Last Updated Time</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in displayedQuoMasterPosts">

                            <td>
                            <a :href="'quotation?id=' + item.id" target="_blank" v-if="item.pageless == ''">{{
                                item.title }}</a>
                                <a :href="'quotation_pageless?id=' + item.id" target="_blank" v-if="item.pageless == 'Y'">{{
                                item.title }}</a>
                            </td>

                            <td>
                                <a v-show="item.kind == ''"
                                v-bind:href="'project02?p='+ item.project_id">Project: {{ item.project_name }}
                                </a>
                                <a v-show="item.kind == 'a'"
                                v-bind:href="'task_management_AD?sid='+ item.project_id">Admin Department Task Management: {{ item.project_name_a }}
                                </a>
                                <a v-show="item.kind == 'd'"
                                v-bind:href="'task_management_DS?sid='+ item.project_id">Design Department Task Management: {{ item.project_name_d }}
                                </a>
                                <a v-show="item.kind == 'l'"
                                v-bind:href="'task_management_LT?sid='+ item.project_id">Lighting Department Task Management: {{ item.project_name_l }}
                                </a>
                                <a v-show="item.kind == 'o'"
                                v-bind:href="'task_management_OS?sid='+ item.project_id">Office Systems Department Task Management: {{ item.project_name_o }}
                                </a>
                                <a v-show="item.kind == 'sl'"
                                v-bind:href="'task_management_SLS?sid='+ item.project_id">Sales Task Management: {{ item.project_name_sl }}
                                </a>
                                <a v-show="item.kind == 'sv'"
                                v-bind:href="'task_management_SVC?sid='+ item.project_id">Engineering Task Management: {{ item.project_name_sv }}
                                </a>
                            </td>

                            <td>
                                {{ item.quotation_no }}
                            </td>

                            <td>
                            {{item.created_at}}<br>{{item.created_by}}
                            </td>

                            <td>
                            {{item.post[0].updated_at}}<br>{{item.post[0].username}}
                            </td>

                            <td>
                                <a class="btn small yellow" @click="quotation_import(item)">Import</a>
                            </td>
                        </tr>


                        <tr v-for='(receive_record, index) in displayedQuoDetailPosts'>
                            <td>
                                <a v-show="receive_record.is_edited == 1"
                                   v-bind:href="'quotation?id=' + receive_record.id">{{
                                    receive_record.title }}</a>
                            <td>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == ''"
                                   v-bind:href="'project02?p='+ receive_record.project_id">Project: {{
                                    receive_record.project_name }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'a'"
                                   v-bind:href="'task_management_AD?sid='+ receive_record.project_id">Admin
                                    Department Task Management: {{ receive_record.project_name_a }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'd'"
                                   v-bind:href="'task_management_DS?sid='+ receive_record.project_id">Design
                                    Department Task Management: {{ receive_record.project_name_d }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'l'"
                                   v-bind:href="'task_management_LT?sid='+ receive_record.project_id">tdghting
                                    Department Task Management: {{ receive_record.project_name_l }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'o'"
                                   v-bind:href="'task_management_OS?sid='+ receive_record.project_id">Office Systems
                                    Department Task Management: {{ receive_record.project_name_o }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'sl'"
                                   v-bind:href="'task_management_SLS?sid='+ receive_record.project_id">Sales Task
                                    Management: {{ receive_record.project_name_sl }}
                                </a>
                                <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'sv'"
                                   v-bind:href="'task_management_SVC?sid='+ receive_record.project_id">Engineering Task
                                    Management: {{ receive_record.project_name_sv }}
                                </a>
                            </td>
                            <td>{{ receive_record.quotation_no }}</td>
                            <td>{{receive_record.created_at}}<br>{{receive_record.created_by}}</td>
                            <td>{{receive_record.post[0].updated_at}}<br>{{receive_record.post[0].username}}</td>
                            <td>
                                <a class="btn small yellow" @click="">Import</a>
                            </td>
                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    </div>



    <!-- Modal for Electrical Materials Catalog -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_electrical_catalog">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

        <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel">Electrical Materials Catalog</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                    <div class="left_function" style="width: 81%;">
                        <input type="text" v-model="fil_keyword" placeholder="Keyword">
                    </div>

                    <a class="btn small green" @click="filter_apply_new_bom()">Search</a>

                </div>

                <div class="list_function" style="margin: 7px 0;">
                    <div class="pagenation">
                        <a class="prev" :disabled="product_page_bom == 1" @click="pre_page_bom(); getBomRecords();">Prev
                            10</a>
                        <a class="page" v-for="pg_bom in product_pages_10_bom" @click="product_page_bom=pg_bom; getBomRecords();"
                           v-bind:style="[pg_bom == product_page_bom ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                            pg_bom
                            }}</a>
                        <a class="next" :disabled="product_page_bom == product_pages_bom.length"
                           @click="nex_page_bom(); getBomRecords();">Next
                            10</a>
                    </div>
                </div>


                <div>
                    <table id="tb_electrical_catalog" class="table  table-sm table-bordered">
                        <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in displayedBomMasterPosts">

                            <td>
                                {{ item.particulars }}
                            </td>

                            <td>
                                {{ item.unit }}
                            </td>

                            <td>
                            ₱ {{ item.price % 1 !== 0 ? Number(item.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : Number(item.price).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </td>

                            <td>
                                {{ item.remarks }}
                            </td>

                            <td>
                                <a class="btn small yellow" @click="add_consumable(item)">Add</a>
                            </td>
                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    </div>



    <!-- Modal for Electrical Tools and Equipments Catalog -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_electrical_tool_catalog">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

        <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel">Electrical Tools and Equipments Catalog</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                    <div class="left_function" style="width: 81%;">
                        <input type="text" v-model="fil_keyword" placeholder="Keyword">
                    </div>

                    <a class="btn small green" @click="filter_apply_new_tools()">Search</a>

                </div>

                <div class="list_function" style="margin: 7px 0;">
                    <div class="pagenation">
                        <a class="prev" :disabled="product_page_tools == 1" @click="pre_page_tools(); gettoolsRecords();">Prev
                            10</a>
                        <a class="page" v-for="pg_tools in product_pages_10_tools" @click="product_page_tools=pg_tools; gettoolsRecords();"
                           v-bind:style="[pg_tools == product_page_tools ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                            pg_tools
                            }}</a>
                        <a class="next" :disabled="product_page_tools == product_pages_tools.length"
                           @click="nex_page_tools(); gettoolsRecords();">Next
                            10</a>
                    </div>
                </div>


                <div>
                    <table id="tb_electrical_tool_catalog" class="table  table-sm table-bordered">
                        <thead>
                        <tr>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in displayedtoolsMasterPosts">

                            <td>
                                {{ item.particulars }}
                            </td>

                            <td>
                                {{ item.unit }}
                            </td>

                            <td>
                            ₱ {{ item.price % 1 !== 0 ? Number(item.price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : Number(item.price).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </td>

                            <td>
                                {{ item.remarks }}
                            </td>

                            <td>
                                <a class="btn small yellow" @click="add_tools(item)">Add</a>
                            </td>
                        </tr>

                        </tbody>

                    </table>

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


</script>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/quotation_eng_pageless.js"></script>
</html>