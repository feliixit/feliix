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

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales'){
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
    <title>Statement of Account</title>
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
          href="css/bootstrap-select.min.css">
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

        #tb_product_list tbody tr td:nth-of-type(5) button {
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
            z-index: 1;
            display: none;
        }


        .qn_page {
            width: 1200px;
            background-color: white;
            overflow-x: auto;
        }

        .qn_page .qn_header {
            width: 100%;
            height: 275px;
            background-size: 100% auto;
        }

        .qn_header .left_block {
            width: 65%;
            float: left;
            padding-left: 30px;
        }

        .qn_header .left_block img.logo {
            display: block;
            width: 260px;
            margin-top: 35px;
        }

        .qn_header .left_block .soa_bill_title {
            font-size: 12px;
            margin-top: 55px;
            font-weight: 600;
            line-height: 1.2;
            height: 33.6px;
        }

        .qn_header .left_block .soa_bill_title > div {
            padding: 8px 0 0 2px;
        }

        .qn_header .left_block .soa_bill_title  .soa_bill_1stline {
            font-size: 20px;
            font-weight: 600;
        }

        .qn_header .left_block .soa_bill_title  .soa_bill_2ndline,
        .qn_header .left_block .soa_bill_title  .soa_bill_3rdline {
            font-size: 15px;
            font-weight: 400;
        }

        .qn_header .right_block {
            width: 35%;
            float: right;
            padding-right: 3px;
            margin-top: 35px;
        }

        .qn_header .right_block .soa_description {
            font-size: 30px;
            font-weight: 600;
            margin-top: 8px;
        }

        .qn_header .right_block .soa_info {
            margin-top: 72px;
        }

        .qn_header .right_block .soa_info ul {
            font-size: 13px;
            display: flex;
            margin: 8px 0 0;
        }

        .qn_header .right_block .soa_info ul:first-of-type {
            margin: 0;
        }

        .qn_header .right_block .soa_info ul > li:first-of-type {
            margin-right: 7px;
        }

        .qn_header .right_block .soa_account .soa_account_summary {
            font-size: 18px;
            background-color: rgb(78, 181, 187);
            display: flex;
            color: white;
            padding: 1px 0 4px 4px;
            font-weight: 500;
            margin: 20px 27px 2px 0;
        }

        .qn_header .right_block .soa_account .soa_account_summary li:first-of-type {
            width: 50%;
        }

        .qn_header .right_block .soa_account .soa_account_summary li:last-of-type {
            width: 50%;
            text-align: right;
            padding-right: 7px;
        }

        .qn_header .right_block .soa_account .soa_account_1stline,
        .qn_header .right_block .soa_account .soa_account_2ndline,
        .qn_header .right_block .soa_account .soa_account_3rdtline {
            display: flex;
            font-size: 13px;
            width: calc( 100% - 45px);
            margin-left: 6px;
            margin-bottom: 2px;
        }

        .qn_header .right_block .soa_account .soa_account_2ndline {
            margin-bottom: 20px;
        }

        .qn_header .right_block .soa_account .soa_account_1stline li:first-of-type,
        .qn_header .right_block .soa_account .soa_account_2ndline li:first-of-type,
        .qn_header .right_block .soa_account .soa_account_3rdtline li:first-of-type {
            width: 50%;
        }

        .qn_header .right_block .soa_account .soa_account_1stline li:last-of-type,
        .qn_header .right_block .soa_account .soa_account_2ndline li:last-of-type,
        .qn_header .right_block .soa_account .soa_account_3rdtline li:last-of-type {
            width: 50%;
            text-align: right;
        }

        .qn_page .qn_body {
            padding: 0 30px;
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
            border-bottom: none;
            font-style: italic;
            vertical-align: bottom;
            padding-left: 10px;
        }

        .tb_total tbody tr:nth-of-type(1) td {
            border-top: 2px solid #A0A0A0;
        }

        .tb_total tbody tr:last-of-type td {
            color: red;
        }

        .tb_total tbody tr td:nth-last-of-type(1) span.numbers.deleted {
            text-decoration: line-through;
        }

        .tb_total tbody tr td span.numbers, .tb_total tfoot tr td span.numbers {
            font-weight: 800;
        }

        .tb_total tfoot tr td:nth-of-type(1) {
            border-left: 2px solid #A0A0A0;
            border-bottom: none;
        }

        .tb_total tbody tr td:nth-last-of-type(2), .tb_total tfoot tr td:nth-last-of-type(2) {
            font-weight: 800;
            text-align: center;
            width: 285px;
        }

        .tb_total tbody tr td:nth-last-of-type(1), .tb_total tfoot tr td:nth-last-of-type(1) {
            width: 210px;
            padding: 5px 15px;
            text-align: right;
        }

        .tb_total tfoot tr:last-of-type td:nth-of-type(1) {
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_total tfoot tr:last-of-type td:nth-last-of-type(2), .tb_total tfoot tr:last-of-type td:nth-last-of-type(1) {
            background-color: rgb(78, 181, 187);
            color: white;
        }

        .area_payment {
            width: calc(100% - 20px);
            margin: 10px;
        }

        .area_payment b {
            font-size: 20px;
            font-weight: 600;
        }

        .area_payment .acount_info {
            font-size: 15px;
            font-weight: 400;
            margin-top: 5px;
            margin-bottom: 30px;
        }

        .area_payment > div:last-of-type {
            margin-bottom: 0;
        }

        .area_payment .acount_info .account_name {

        }

        .area_payment .acount_info .first_line {

        }

        .area_payment .acount_info .second_line {

        }

        .area_payment .acount_info .third_line {

        }

        .area_terms {
            width: calc(100% - 20px);
            margin: 10px;
            margin-bottom: 40px;
        }

        .area_terms b {
            font-size: 20px;
            font-weight: 600;
        }

        .area_terms .terms {
            font-size: 15px;
            font-weight: 400;
            white-space: pre-line;
            margin-top: 5px;
        }

        .qn_body .copyright {
            width: 100%;
            padding: 0 5px;
            font-size: 14px;
            font-weight: 500;
            text-align: right;
            margin-bottom: 30px;
        }

        .qn_body .area_subtotal {
            width: 100%;
        }

        .area_subtotal .tb_format1 {
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_format1 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format1 tbody tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format1 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format1 tbody tr td.pic {
            width: 125px;
            text-align: center;
        }

        .tb_format1 tbody tr td img {
            max-height: 90px;
            max-width: 90px;
        }

        .tb_format1 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format1 tbody tr td div.pid, .tb_format1 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
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

        .tb_format1 tbody tr.thead1 td.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: white;
            padding: 5px 20px;
            background: #C9C9C9;
        }

        .tb_format1 tbody tr.thead2 td {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            padding: 7px 20px;
        }

        .tb_format1 tbody tr.tfoot1 td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format1 tbody tr.tfoot1 td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
            text-align: right;
            padding: 5px 30px 5px 15px;
        }

        .tb_format1 tbody tr.tfoot1 td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
            padding: 5px 15px;
        }

        .tb_format1 tbody tr.thead2 td:nth-of-type(1), .tb_format1 tbody tr.desc1 td:nth-of-type(1) {
            width: 90px;
            text-align: center;
        }

        .tb_format1 tbody tr.thead2 td:nth-last-of-type(3) {
            width: 75px;
        }

        .tb_format1 tbody tr.desc1 td:nth-last-of-type(3) {
            width: 75px;
            text-align: center;
            height: 50px;
        }

        .tb_format1 tbody tr.thead2 td:nth-last-of-type(2) {
            width: 210px;
        }

        .tb_format1 tbody tr.desc1 td:nth-last-of-type(2) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1 tbody tr.thead2 td:nth-last-of-type(1) {
            width: 210px;
        }

        .tb_format1 tbody tr.desc1 td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1.vat tbody tr.thead2 td:nth-last-of-type(4) {
            width: 75px;
        }

        .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(4) {
            width: 75px;
            text-align: center;
            height: 50px;
        }

        .tb_format1.vat tbody tr.thead2 td:nth-last-of-type(3) {
            width: 210px;
        }

        .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(3) {
            width: 210px;
            text-align: right;
            height: 50px;
        }

        .tb_format1.vat tbody tr.thead2 td:nth-last-of-type(2) {
            width: 135px;
        }

        .tb_format1.vat tbody tr.desc1 td:nth-last-of-type(2) {
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
            max-height: 120px;
            max-width: 220px;
            margin: 0 5px;
        }

        .area_subtotal .tb_format2 {
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_format2 td {
            text-align: left;
            padding: 5px 20px;
            border-right: 2px solid #A0A0A0;
            border-bottom: 2px solid #A0A0A0;
        }

        .tb_format2 tbody tr td:first-of-type {
            border-left: 2px solid #A0A0A0;
        }

        .tb_format2 tbody tr td {
            font-size: 14px;
            vertical-align: top;
            padding: 15px;
        }

        .tb_format2 tbody tr td:nth-of-type(1) {
            font-weight: 600;
        }

        .tb_format2 tbody tr td div.pid, .tb_format2 tbody tr td div.code {
            font-size: 16px;
            font-weight: 800;
            word-break: break-all;
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
            width: 90px;
            text-align: center;
        }

        .tb_format2 tbody tr td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
        }

        .tb_format2 tbody tr.thead1 td.title {
            border-top: 2px solid #A0A0A0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: white;
            padding: 5px 20px;
            background: #C9C9C9;
        }

        .tb_format2 tbody tr.thead2 td {
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            padding: 5px 20px;
        }

        .tb_format2 tbody tr.tfoot1 td:nth-of-type(1) {
            border-right: none;
        }

        .tb_format2 tbody tr.tfoot1 td:nth-of-type(2) {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr.tfoot1 td:nth-of-type(3) {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: rgb(0, 117, 58);
        }

        .tb_format2 tbody tr.tfoot1 td:nth-last-of-type(1) {
            width: 210px;
            text-align: right;
            padding: 5px 15px;
        }

        .tb_format2 tbody tr.tfoot1 td:nth-last-of-type(2) {
            width: 135px;
            text-align: right;
            padding: 5px 30px 5px 15px;
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

        .pagebox .content_box ul li:nth-of-type(1) > input:nth-of-type(4) {
            width: 150px;
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

        #header_dialog, #total_dialog{
            zoom: 80%;
        }

        #page_dialog, #subtotal_dialog, #contact_dialog, #payment_dialog, #balance_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 80%;
        }

        #total_dialog {
            pointer-events: auto;
        }

        #total_dialog div.formbox dt > input[type='number'] {
            height: 30px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 100px;
            margin: 5px 0;
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

        #page_dialog .page_form, #subtotal_dialog .subtotalbox, #contact_dialog .termsbox, #balance_dialog .termsbox {
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

        #contact_dialog .formbox dl {
            margin-bottom: 0px;
            border-bottom: 1px solid black;
        }

        #contact_dialog .formbox dl dd select {
            width: 370px;
        }

        #contact_dialog div.formbox dt > input[type='number'] {
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

        #balance_dialog .termsbox .content_box ul li:nth-of-type(1) span {
            width: 90px;
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

        .list_function.main a.print, .list_function.main a.specification, .list_function.main a.approvalform, .list_function.main a.export_excel {
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


        .list_function.main a.print:hover, .list_function.main a.specification:hover, .list_function.main a.approvalform:hover, .list_function.main a.export_excel:hover {
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
                    <a id="" class="print" title="Export Statement of Account into PDF" @click="print_page()"></a>
                </div>

            </div>

            <div class="block fn">
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
                                <dt class="head">Bill To:</dt>
                                <dd>
                                    <input type="text" placeholder="First Line with Bold Font"
                                           v-model="temp_first_line">
                                    <input type="text" placeholder="Second Line" v-model="temp_second_line">
                                    <input type="text" placeholder="Third Line" v-model="temp_third_line">
                                </dd>

                                <dt>Statement Date:</dt>
                                <dd>
                                    <input type="date" v-model="temp_statement_date">
                                </dd>

                                <dt>Quotation Number:</dt>
                                <dd>
                                    <input type="text" v-model="temp_quotation_no">
                                </dd>

                                <dt>P.O. Number:</dt>
                                <dd>
                                    <input type="text" v-model="temp_po">
                                </dd>

                                <dt>Mode of Payment / Terms of Payment:</dt>
                                <dd>
                                    <select v-model="temp_mode">
                                        <option value="mode" selected>Mode of Payment</option>
                                        <option value="term">Terms of Payment</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input type="text" placeholder="Caption for Other" v-model="temp_caption" v-if="temp_mode == 'other'">
                                    <input type="text" placeholder="Content" v-model="temp_mode_content">
                                </dd>

                                <dt>Account Summary:</dt>
                                <dd>
                                    <input type="text" placeholder="Amount of Account Summary"
                                           v-model="temp_account_summary">
                                    <input type="text" placeholder="Caption of First Line"
                                           v-model="temp_caption_first_line">
                                    <input type="text" placeholder="Content of First Line"
                                           v-model="temp_content_first_line">
                                    <input type="text" placeholder="Caption of Second Line"
                                           v-model="temp_caption_second_line">
                                    <input type="text" placeholder="Content of Second Line"
                                           v-model="temp_content_second_line">
                                </dd>

                                <div class="btnbox">
                                    <a class="btn small" @click="cancel_header()">Close</a>
                                    <a class="btn small green" @click="save_header()">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock" v-if="soa">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_page = !show_page">Subtotal Block</a>
                    <?php
                    } else {
                    ?>
                    <a>Subtotal Block</a>
                    <?php
                    }
                    ?>
                    <div id="page_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_page">
                        <h6>Subtotal Block</h6>

                        <div class="page_form">

                            <!-- 在第五版的報價單中，因為會採用列印時瀏覽器自動分頁，所以原本需要使用者自行分頁的功能將移除掉，因此在第五版中當使用者建立Subtotal區塊時，Subtotal區塊的所在頁碼都直接設定為第一頁即可 -->
                            <div class="pagebox" v-for="(page, page_index) in temp_pages">

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
                                            Show "Subtotal Amount"<br>
                                            Distance from Previous Block: <input type="number" v-model="block.pixa">
                                            pixel
                                        </li>
                                        <li>
                                            <i class="fas fa-arrow-alt-circle-up"
                                               @click="set_up(page.id, block_index, block.id)"></i>

                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="set_down(page.id, block_index, block.id)"></i>

                                            <i class="fas fa-copy" @click="page_copy(page.id, block.id)"></i>

                                            <i class="fas fa-trash-alt" @click="del_block(page.id, block.id)"></i>
                                        </li>
                                    </ul>


                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="reset_page()">Close</a>
                                <a class="btn small green" @click="page_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="soa">

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
                                                    <input type="file" :id="'block_image_' + block.id + '_1'"
                                                           :name="'block_image_' + block.id + '_1'"
                                                           @change="onFileChangeImage($event, block.id, 1)">
                                                    <img v-if="block.url" :src="block.url"/>
                                                    <div @click="clear_photo(block.id, 1)">x</div>
                                                </div>
                                            </div>

                                            <div :class="['itembox', (block.url2 !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :id="'block_image_' + block.id + '_2'"
                                                           :name="'block_image_' + block.id + '_2'"
                                                           @change="onFileChangeImage($event, block.id, 2)">
                                                    <img v-if="block.url2" :src="block.url2"/>
                                                    <div @click="clear_photo(block.id, 2)">x</div>
                                                </div>
                                            </div>

                                            <div :class="['itembox', (block.url3 !== '' ? 'chosen' : '')]">
                                                <div class="photo">
                                                    <input type="file" :id="'block_image_' + block.id + '_3'"
                                                           :name="'block_image_' + block.id + '_3'"
                                                           @change="onFileChangeImage($event, block.id, 3)">
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
                                        <span>Discount: </span> <input type="number" v-model="block.discount" min="0"
                                                                       max="100"
                                                                       @change="chang_amount(block)"
                                                                       oninput="this.value|=0"> Amount:
                                        <input type="number" v-model="block.amount"
                                               @change="chang_my_amount(block)"><br>
                                        <span>Description:</span> <textarea rows="2"
                                                                            v-model="block.desc"></textarea><br>
                                        <span>Listing:</span> <textarea rows="4" v-model="block.list"></textarea><br>
                                        <!-- 只有subtotal box Type-A 而且是 Item with Image，才需要顯示下面這個 Notes 欄位出來 -->
                                        <span v-if="block.type == 'image'">Notes:</span> <textarea rows="2"
                                                                                                   v-model="block.notes"
                                                                                                   v-if="block.type == 'image'"></textarea>
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
                                                                                                      v-model="block.amount"
                                                                                                      @change="chang_my_amount(block)"><br>
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
                                <a class="btn small" @click="subtotal_close()">Close</a>
                                <a class="btn small green" @click="subtotal_save()" v-if="is_load">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock" v-if="soa">

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
                                    <select v-model="total.show">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="total.show == ''">Distance from Previous Block: <input type="number"
                                                                                                        v-model="total.pixa">
                                    pixel
                                </dt>
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
                                <a class="btn small" @click="close_total()">Close</a>
                                <a class="btn small green" @click="save_total()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_balance = !show_balance">Balance
                        Description</a>
                    <?php
                    } else {
                    ?>
                    <a>Balance Description</a>
                    <?php
                    }
                    ?>
                    <div id="balance_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_balance">
                        <h6>Balance Description</h6>

                        <div class="termsbox">
                            <div class="function_box">
                                <a class="btn small green" @click="add_term_item()">Add Description</a>
                            </div>

                            <div class="content_box">
                                <ul v-for="(item, index) in term.item">
                                    <li>
                                        <span>Description:</span> <input type="text" v-model="item.title"><br>
                                        <span>Amount:</span> <input type="text" v-model="item.brief"><br>
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
                                <a class="btn small" @click="close_balance()">Close</a>
                                <a class="btn small green" @click="balance_save()">Save</a>
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
                        Details</a>
                    <?php
                    } else {
                    ?>
                    <a>Payment Details</a>
                    <?php
                    }
                    ?>
                    <div id="payment_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_payment_term">
                        <h6>Payment Details</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose whether to show the block of payment terms in this document:
                                </dt>
                                <dd>
                                    <select v-model="show_p">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="show_p == ''">Distance from Previous Block: <input type="number"
                                                                                                          v-model="pixa_p">
                                    pixel
                                </dt>
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
                                <a class="btn small" @click="close_payment_term()">Close</a>
                                <a class="btn small green" @click="payment_term_save()">Save</a>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_contact = !show_contact">Contact</a>
                    <?php
                    } else {
                    ?>
                    <a>Contact</a>
                    <?php
                    }
                    ?>
                    <div id="contact_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_contact">
                        <h6>Contact</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">Choose whether to show the block of Contact in this document:</dt>
                                <dd>
                                    <select v-model="temp_show_t">
                                        <option value="N">No</option>
                                        <option value="">Yes</option>
                                    </select>
                                </dd>

                                <dt class="head" v-if="temp_show_t == ''">Distance from Previous Block: <input type="number"
                                                                                                          v-model="temp_pixa_t">
                                    pixel
                                </dt>
                            </dl>
                        </div>


                        <div class="termsbox">

                            <div class="content_box">

                                 <textarea rows="8" style="border: 1px solid black; width: 100%; height: 100%;" v-model="temp_contact"></textarea>

                            </div>
                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_contact()">Close</a>
                                <a class="btn small green" @click="contact_save()">Save</a>
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
                </td>
            </tr>
            </thead>


            <tbody>
            <tr>
                <td>

                    <div class="qn_header" v-if="show_title">

                        <div class="left_block">

                            <img class="logo" src="images/Feliix-Logo-Black.png">

                            <div class="soa_bill_title">
                                Bill to:
                                <div class="soa_bill_1stline">{{ first_line }}</div>
                                <div class="soa_bill_2ndline">{{ second_line }}</div>
                                <div class="soa_bill_3rdline">{{ third_line }}</div>
                            </div>

                        </div>

                        <div class="right_block">

                            <div class="soa_description">Statement of Account</div>

                            <div class="soa_info">

                                <ul class="soa_info_1stline">
                                    <li>Statement Date: </li>
                                    <li>{{ statement_date }}</li>
                                </ul>

                                <ul class="soa_info_2ndline" v-if="quotation_no != ''">
                                    <li>Quotation Number: </li>
                                    <li>{{ quotation_no }}</li>
                                </ul>

                                <ul class="soa_info_3rdline" v-if="po != ''">
                                    <li>P.O. Number: </li>
                                    <li>{{ po }}</li>
                                </ul>

                                <ul class="soa_info_4thline"  v-if="mode_content != ''">
                                    <li>{{ (mode == 'mode' ? 'Mode of Payment' : (mode == 'term' ? 'Terms of Payment' : caption)) }}:</li>
                                    <li>{{ mode_content }}</li>
                                </ul>

                            </div>

                            <div class="soa_account">

                                <ul class="soa_account_summary">
                                    <li>Account Summary:</li>
                                    <li>{{ account_summary }}</li>
                                </ul>

                                <ul class="soa_account_1stline">
                                    <li>{{ caption_first_line }}</li>
                                    <li>{{ content_first_line }}</li>
                                </ul>

                                <ul class="soa_account_2ndline">
                                    <li>{{ caption_second_line }}</li>
                                    <li>{{ content_second_line }}</li>
                                </ul>

                            </div>

                        </div>

                    </div>


                    <div class="qn_body" style="margin-top: 65px;">

                        <div class="area_subtotal">

                            <!-- 預設 margin-top = 30px; 因為最短距離就是30px -->
                            <template v-for="(pa, index) in pages">
                                <table :class="[tp.type == 'A' ? 'tb_format1' : 'tb_format2', product_vat == 'P' ? 'vat' : '']"
                                    v-bind:style="{ 'margin-top': (tp.pixa == '' ? 0 : tp.pixa) + 'px' }"
                                    v-for="(tp, index) in pa.types">

                                    <tbody>

                                    <!-- 表格標題列 -->

                                    <tr class="thead1" v-if="tp.type == 'A'">
                                        <td class="title" :colspan="product_vat == 'P' ? 7 : 6">{{ tp.name }}</td>
                                    </tr>

                                    <tr class="thead2" v-if="tp.type == 'A'">
                                        <td>#</td>
                                        <td colspan="2">DESCRIPTION</td>
                                        <td>QTY</td>
                                        <td>PRICE</td>
                                        <td v-if="product_vat == 'P'">12% VAT</td>
                                        <td>AMOUNT</td>
                                    </tr>

                                    <tr class="thead1" v-if="tp.type == 'B'">
                                        <td class="title" :colspan="product_vat == 'P' ? 4 : 4">{{ tp.name }}</td>
                                    </tr>

                                    <tr class="thead2" v-if="tp.type == 'B'">
                                        <td>#</td>
                                        <td colspan="2">DESCRIPTION</td>
                                        <td>AMOUNT</td>
                                    </tr>


                                    <template v-for="(bk, index) in tp.blocks" v-if="tp.type == 'A'">
                                        <!-- 表格內容物 -->

                                        <tr class="desc1">

                                            <td v-if="bk.type == 'image' || bk.type == '' || bk.type== 'noimage'">{{ bk.num }}</td>

                                            <td v-if="bk.type == 'image' || bk.type == '' || bk.type== 'noimage'" colspan="2">
                                                <!--
                                                <div class="pid noPrint" v-if="bk.pid != 0">{{ "ID: " + bk.pid }}</div>
                                                -->
                                                <div class="code">{{ bk.code }}</div>
                                            </td>
                                            <td>
                                                <span class="numbers">{{ bk.qty !== undefined ? Math.floor(bk.qty).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}</span>
                                            </td>
                                            <td>
                                                <span class="numbers" v-if="bk.discount == 0">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                                <span class="numbers deleted"
                                                    v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">₱ {{ (bk.price * bk.ratio  !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00') }}<span
                                                    v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">{{ bk.discount !== undefined ? Math.floor(bk.discount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "" }}% OFF</span></span><br
                                                    v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">
                                                <span class="numbers"
                                                    v-if="bk.discount != 0 && (bk.discount != 100 && bk.amount != '0.00')">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio - (bk.price * bk.ratio * (bk.discount / 100))).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                                <span class="numbers"
                                                    v-if="bk.discount != 0 && (bk.discount == 100 || bk.amount == '0.00')">₱ {{ bk.price * bk.ratio !== undefined ? Number(bk.price * bk.ratio).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
                                            </td>
                                            <td v-if="product_vat == 'P'">
                                                <span class="numbers" v-if="bk.discount == 0">₱ {{ bk.price * bk.ratio !== undefined ? (Number(bk.price * bk.ratio) * 0.12).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00' }}</span>
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

                                    </template>


                                    <template v-if="tp.type == 'B'">
                                        <tr v-for="(bk, index) in tp.blocks">
                                            <td>{{ bk.num }}</td>
                                            <td colspan="2">
                                                <!--
                                                <div class="pid noPrint" v-if="bk.pid != 0">{{ "ID: " + bk.pid }}</div>
                                                -->
                                                <div class="code">{{ bk.code }}</div>
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

                                    </template>

                                    <!-- 表格尾端，每一個 subtotal 小計金額的部分 -->
                                    <template v-if="tp.type == 'A' && tp.not_show != '1'">
                                        <tr class="tfoot1">
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

                                    </template>

                                    <template v-if="tp.type == 'B' && tp.not_show != '1'">
                                        <tr class="tfoot1">
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

                                    </template>

                                    </tbody>

                                </table>
                            </template>
                        </div>

                        <!-- 預設 margin-top = 30px; 因為最短距離就是30px -->
                        <div class="area_total" v-bind:style="{ 'margin-top': (show == '' ? pixa : 0) + 'px' }"
                            v-if="show == ''">
                            <table class="tb_total" v-for="(tt, index) in pag.total">
                                <tbody>
                                <tr>
                                    <td :rowspan="(tt.vat == 'Y' && tt.discount !== '0' ? 4 : 3)">
                                        <!--
                                        <div>Remarks: Quotation valid for <span class="valid_for">{{ tt.valid }}</span></div>
                                        <div></div>
                                        -->
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

                                <tr>
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
                                </tbody>

                                <tfoot>
                                <tr  v-for="(te, index) in pag.term">
                                    <td>
                                    </td>
                                    <td>{{ te.title }}</td>
                                    <td><span class="numbers">{{ te.brief }}</span>
                                    </td>
                                </tr>
                    

                                </tfoot>
                            </table>
                        </div>


                        <!-- 預設 margin-top = 30px; 因為最短距離就是30px -->
                        <div class="area_payment" v-bind:style="{ 'margin-top': (org_show_p == '' ? org_pixa_p : 0) + 'px' }"
                            v-if="pag.payment_term !== undefined && org_show_p == ''">

                            <b>Payment Details:</b>

                            <div class="acount_info" v-for="(tt, index) in pag.payment_term.list">
                                <div class="account_name">{{ tt.bank_name }}</div>
                                <div class="first_line">{{ tt.first_line }}</div>
                                <div class="second_line">{{ tt.second_line }}</div>
                                <div class="third_line">{{ tt.third_line }}</div>
                            </div>

                        </div>


                        <!-- 預設 margin-top = 30px; 因為最短距離就是30px -->
                        <div class="area_terms" v-bind:style="{ 'margin-top': (show_t == '' ? pixa_t : 0) + 'px' }"
                            v-if="show_t == ''">

                            <b>Contact:</b>

                            <div class="terms"> {{ contact }}
                            </div>
                        </div>


                        <!-- 預設 margin-top = 30px; 因為最短距離就是30px -->
                        <div class="copyright">© Feliix Inc.</div>

                    </div>


                </td>
            </tr>
            </tbody>

        </table>


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

                                <input type="text" placeholder="ID" v-model="fil_id"
                                       style="width: 240px; margin-right: 0;">

                                <input type="text" placeholder="Code" v-model="fil_code"
                                       style="width: 240px; margin-right: 0;">

                                <select v-model="fil_brand" style="width: 240px;">
                                    <option value="">Choose Brand...</option>
                                    <option v-for="(item, index) in brands">{{ item.brand }}</option>
                                </select>

                                <br>

                                <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                        data-width="585px" title="Choose Tag(s)..." id="tag01" v-model="fil_tag">

                                    <optgroup v-for="(group, index) in tag_group" :label="group.group">
                                        <option v-for="(it, index2) in group.items" :value="it.item_name">{{
                                            it.item_name }}
                                        </option>
                                    </optgroup>


                                </select>

                                <input type="text" placeholder="Keyword" v-model="fil_keyword"
                                       style="margin-left: 20px; width: 300px;">
                            </div>

                            <a class="btn small green" @click="filter_apply_new()">Search</a>

                        </div>

                        <div class="list_function" style="margin: 7px 0;">

                            <div class="sort_block">
                                <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(1)">SRP
                                    (Low → High)</a>
                                <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(2)">SRP
                                    (High → Low)</a>
                                <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(3)">QP
                                    (Low → High)</a>
                                <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(4)">QP
                                    (High → Low)</a>
                                <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(0)">Clear</a>
                            </div>

                            <div class="pagenation">
                                <a class="prev" :disabled="product_page == 1" @click="pre_page(); filter_apply();">Prev
                                    10</a>
                                <a class="page" v-for="pag in product_pages_10"
                                   @click="product_page=pag; filter_apply(pag);"
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
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in displayedPosts">

                                    <td><img
                                            :src="img_url + item.photo1" v-if="item.photo1 !== ''">
                                    </td>
                                    <td>
                                        <ul v-if="item.out == 'Y'">
                                            <li>
                                                <span class="phasedout">Phased Out</span>
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
                                        <!-- 如果停產的子規格數目大於 0，才需要顯示下面的<div class="phasedout_variant"> 結構 -->
                                        <div class="phasedout_variant" v-if="item.phased_out_cnt > 0">

                                            <!-- 如果停產的子規格數目大於或等於2，則顯示下面這一行 -->
                                            ※ {{ item.phased_out_cnt }} variant{{ item.phased_out_cnt > 1 ? 's' : '' }}
                                            are phased out.

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
                                                <template v-for="(att_value, index) in att.value">{{att_value}}
                                                </template>
                                            </li>

                                        </ul>
                                    </td>
                                    <td>
                                        <span v-show="show_ntd === true">CP: {{ item.price_ntd }} <br
                                                v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }}<br></span>
                                        <span>SRP: {{ item.price }}<br v-if="item.str_price_change"> {{ item.str_price_change ?  item.str_price_change : '' }}<br></span>
                                        <span>QP: {{ item.quoted_price }} <br v-if="item.str_quoted_price_change"> {{ item.str_quoted_price_change ? item.str_quoted_price_change : '' }}<br></span>
                                    </td>
                                    <td>
                                        <button id="edit01" @click="btnEditClick(item)" v-if="item.status != -1"><i aria-hidden="true"
                                                                                           class="fas fa-caret-right"></i>
                                        </button>
                                    </td>
                                </tr>


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
        <!-- Modal End -->


        <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true" id="modal_product_display">

            <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1300px;">

                <div class="modal-content"
                     style="height: calc( 100vh - 3.75rem); overflow-y: auto; border: none; padding-bottom: 20px;">

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
                                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="out_cnt == 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="out_cnt > 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">{{ out_cnt }} variants are phased out</span>
                                    <h3 style="word-break: break-all;">{{product.code}}</h3> <h6>
                                    {{product.brand}}</h6>
                                    <h6 v-if="category == 'Lighting'">{{ product.category}}</h6>
                                    <h6 v-if="category != 'Lighting'">{{ product.category}} >> {{
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
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add
                                                with Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul v-if="product.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')"
                                                    v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')"
                                                    v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                Add all spec.
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
                                                    <span class="phasedout2"
                                                          v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1"
                                                          @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1"
                                                          @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
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
                                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="out_cnt == 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="out_cnt > 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">{{ out_cnt }} variants are phased out</span>

                                    <h3>{{product.code}}</h3> <h6>{{product.brand}}</h6>
                                    <h6 v-if="category == 'Lighting'">{{ product.category}}</h6>
                                    <h6 v-if="category != 'Lighting'">{{ product.category}} >> {{
                                        product.sub_category_name}}</h6>
                                    <div class="tags"
                                         v-if="product.tags !== undefined ? product.tags[0] !== '' : false">
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
                                        {{ product.variation2 !== 'custom' ? product.variation2 :
                                        product.variation2_custom
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
                                        {{ product.variation3 !== 'custom' ? product.variation3 :
                                        product.variation3_custom
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
                                        {{ product.variation4 !== 'custom' ? product.variation4 :
                                        product.variation4_custom
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

                                    <template v-for="(item, index) in product.accessory_infomation"
                                              v-if="show_accessory">
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
                                            <button class="btn btn-info" @click="add_with_image()" v-if="out==''">Add
                                                with Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image()" v-if="out==''">Add
                                                without Image
                                            </button>
                                        </li>
                                    </ul>

                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')"
                                                    v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                Add all spec. with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')"
                                                    v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                Add all spec.
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
                                                    <span class="phasedout2"
                                                          v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1"
                                                          @click="PhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1"
                                                          @click="PhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
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

                </div>

            </div>

        </div>
        <!-- Modal End -->



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
                                    <td> {{ item.name }}</td>
                                    <td> {{ item.position }}</td>
                                    <td> {{ item.phone }}</td>
                                    <td> {{ item.email }}</td>
                                    <td><img v-if="item.url != ''" :src="item.url"></td>
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
        <!-- Modal End -->

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
<script defer src="js/soa.js"></script>
</html>