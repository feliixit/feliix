<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $GLOBALS['username'] = $decoded->data->username;
$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($decoded->data->limited_access == true)
header( 'location:index' );

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
    <title>Registry and Tracking Code for Old Inventory</title>
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

            dialogshow($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'));

            $('.qn_page').click(function () {
                app.close_all();
            })

        })


    </script>

    <style>

        body {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        input.alone[type=checkbox]::before, input[type=checkbox] + Label::before {
            color: #707070;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.fourth header nav a, body.fourth header nav a:link {
            color: #000;
        }

        body.fourth header nav a:hover {
            color: #333;
        }

        body.fourth header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.fourth header nav ul.info {
            margin-bottom: 0;
        }

        body.fourth header nav ul.info b {
            font-weight: bold;
        }

        body.fourth .mainContent > .block > h6 {
            color: var(--fth01);
            padding-left: 10px;
            padding-bottom: 6px;
            border-bottom-color: var(--fth01);
        }

        .list_function {
            border: none;
            margin: 10px 0 15px;
            padding: 10px 10px 0;
        }

        .list_function .new_project a.catalog {
            color: white;
            background-color: var(--fth04);
            font-size: 24px;
            width: 36px;
            height: 36px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.filter {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 36px;
            height: 36px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.sort {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 36px;
            height: 36px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .pagenation {
            padding-top: 6px;
        }

        .list_function .pagenation a {
            border-color: var(--fth01);
        }

        .list_function .pagenation a:hover {
            color: #FFF;
            background-color: var(--fth01);
        }

        #filter_dialog, #order_dialog {
            top: 50px;
        }

        #filter_dialog::before, #order_dialog::before {
            left: -4.5px;
        }

        table.registry_list {
            width: 100%;
            font-size: 16px;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        }

        table.registry_list thead th, table.registry_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        table.registry_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        table.registry_list tbody td {
            border: 1px solid #dee2e6;
        }

        table.registry_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        table.registry_list tbody tr td:nth-of-type(1) {
            width: 180px;
        }

        table.registry_list tbody tr td:nth-of-type(2) {
            width: 400px;
            padding-right: 20px;
        }

        table.registry_list tbody tr td:nth-of-type(2) .id,
        table.registry_list tbody tr td:nth-of-type(2) .brand,
        table.registry_list tbody tr td:nth-of-type(2) .code {
            font-size: 16px;
            font-weight: 800;
            text-align: left;
        }

        table.registry_list tbody tr td:nth-of-type(2) .brief {
            font-size: 16px;
            font-weight: 400;
            white-space: pre-line;
            text-align: left;
        }

        table.registry_list tbody tr td:nth-of-type(2) .read_block .listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
            white-space: pre-line;
            text-align: left;
        }

        table.registry_list tbody tr td:nth-of-type(3) {
            width: 140px;
        }

        table.registry_list tbody tr td:nth-of-type(4) {
            width: 245px;
        }

        table.registry_list tbody tr td:nth-of-type(5) {
            width: 130px;
        }

        table.registry_list tbody tr td:nth-of-type(6) {
            width: 80px;
        }

        table.registry_list tbody tr td:nth-of-type(7),
        table.registry_list tbody tr td:nth-of-type(8) {
            width: 200px;
        }

        table.registry_list tbody tr td:nth-of-type(9) {
            width: 110px;
        }

        table.registry_list tbody tr td:nth-of-type(9) a.btn {
            display: block;
            margin: 10px 0;
            color: white;
            width: 80px;
        }

        table.registry_list a.hyperlink,
        table.barcode_list a.hyperlink {
            color: #0056b3;
        }

        table.registry_list a.hyperlink.block,
        table.barcode_list a.hyperlink.block {
            display: block;
        }

        .read_block .photo > div {
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 24px;
            cursor: pointer;
            text-align: center;
        }

        .registry_list .read_block .photo > div {
            border: 1px dashed red;
            color: red;
            margin-left: calc(50% - 18px);
        }

        #modal_barcode_printing table.barcode_list {
            width: 100%;
        }

        #modal_barcode_printing .modal-body {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        #modal_barcode_printing .list_function {
            margin: 8px 0;
        }

        table.barcode_list thead th, table.barcode_list tbody td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        table.barcode_list thead th {
            background-color: #E0E0E0;
            border: 1px solid #C9C9C9;
        }

        table.barcode_list tbody td {
            border: 1px solid #dee2e6;
        }

        table.barcode_list tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }

        table.barcode_list tbody tr td:nth-of-type(1) {
            width: 60px;
        }

        table.barcode_list tbody tr td:nth-of-type(2) {
            width: 140px;
        }

        table.barcode_list tbody tr td:nth-of-type(3) {
            width: 180px;
        }

        table.barcode_list tbody tr td:nth-of-type(4) {
            width: 400px;
            padding-right: 20px;
        }

        table.barcode_list tbody tr td:nth-of-type(4) .id,
        table.barcode_list tbody tr td:nth-of-type(4) .brand,
        table.barcode_list tbody tr td:nth-of-type(4) .code {
            font-size: 16px;
            font-weight: 800;
            text-align: left;
        }

        table.barcode_list tbody tr td:nth-of-type(4) .brief {
            font-size: 16px;
            font-weight: 400;
            white-space: pre-line;
            text-align: left;
        }

        table.barcode_list tbody tr td:nth-of-type(4) .read_block .listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
            white-space: pre-line;
            text-align: left;
        }

        table.barcode_list tbody tr td:nth-of-type(5) {
            width: 245px;
        }

        table.barcode_list tbody tr td:nth-of-type(6) {
            width: 130px;
        }

        table.barcode_list tbody tr td:nth-of-type(7) {
            width: 80px;
        }

        #modal_barcode_printing .modal-footer .btnbox {
            padding: 0;
        }

        #modal_barcode_printing .modal-footer .btnbox a.btn {
            margin: 5px 15px;
            color: white;
            width: 130px;
        }

        .mainContent > .tags a {
            color: #000;
            background-color: #D7E7E8;
            border-color: #94BABB;
            width: 160px;
            text-align: center;
            padding: 5px 16px;
        }

        .mainContent > .tags a.focus {
            color: #FFF;
            background-color: #94BABB;
            border-color: #94BABB;
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
            padding: 3px 6px 6px 6px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul:last-of-type {
            border-bottom: none;
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

        .bodybox .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        .hide {
            display: none;
        }

        .read_block {
            text-align: center;
        }

        .read_block .id {
            font-size: 16px;
            font-weight: 800;
            text-align: left;
        }

        .read_block .code {
            font-size: 16px;
            font-weight: 800;
            text-align: left;
        }

        .read_block .brief {
            font-size: 16px;
            font-weight: 400;
            white-space: pre-line;
            text-align: left;
        }

        .read_block .listing {
            font-size: 14px;
            font-weight: 400;
            margin-top: 3px;
            white-space: pre-line;
            text-align: left;
        }

        .read_block input {
            border: 1px solid #707070;
            color: #707070;
        }

        .read_block img {
            height: 150px;
            width: 150px;
            object-fit: contain;
            margin: 5px;
        }

        .write_block {
            text-align: center;
        }

        .write_block select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 16px;
            height: 30px;
            margin: 5px 0;
            width: 100%;
        }

        .write_block input {
            height: 30px;
            border: 1px solid #707070;
            font-size: 16px;
            margin: 5px 0;
            width: 100%;
        }

        .write_block input[type="checkbox"] {
            border: none;
        }

        .write_block input[type="checkbox"]:disabled {
            opacity: 1;
        }

        .write_block textarea {
            border: 1px solid #707070;
            font-size: 16px;
            resize: none;
            margin: 5px 0;
            width: 100%;
        }

        .write_block .itembox {
            display: inline-block;
            margin: 5px;
        }

        .write_block .itembox .photo {
            border: 1px dashed #707070;
            width: 150px;
            height: 150px;
            padding: 3px;
            position: relative;
        }

        .write_block .itembox .photo::before {
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
            padding: 2px 0 0;
        }

        .write_block .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .write_block .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .write_block .itembox.chosen .photo::before {
            content: none;
        }

        .write_block .itembox .photo > div {
            display: none;
        }

        .write_block .itembox.chosen .photo > div {
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
            bottom: -50px;
            left: 57px;
        }

        .btnbox i.fa-times-circle {
            color: indianred !important;
        }



        .modal{
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
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
            padding: 0 10px 20px 20px;
        }

        .imagebox .image_list img {
            max-width: 100px;
            max-height: 100px;
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
            padding: 10px 20px 15px;
            border-top: 2px solid rgb(225,225,225);
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
            max-height: 200px;
            max-width: 100%;
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

        .extendex-top {
            background: none;
            box-shadow: none;
        }

        .bg-whi {
            min-height: 100vh;
            box-sizing: border-box;
        }

        .top-box {

            background-size: 100%;
        }

        .pub-con {
            box-sizing: border-box;
            background-size: 100%;
            text-align: center;
            position: relative;
        }

        .input-zone {
            width: 5rem;
            background-size: 2.13rem;
            border-radius: 0.38rem;
            border: 0.06rem solid rgba(112, 112, 112, 1);
            position: relative;
            color: var(--fth04);
            font-size: 0.88rem;
            box-sizing: border-box;
        }

        .input {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
        }

        .pad {
            padding: 0.5rem 1.7rem 0 0rem;
            font-size: 0.88rem;
        }

        .btn-container {
            margin: 0.69rem auto;
            text-align: center;
        }

        .btn-container .btn {
            width: 10.56rem;
            height: 2.5rem;
            border-radius: 1.25rem;
            border: none;
            color: #ffffff;
        }

        .btn-container .btn.btn-gray {
            background: rgba(201, 201, 201, 1);
        }

        .btn-container .btn.btn-blue {
            background: linear-gradient(180deg,
            rgba(128, 137, 229, 1) 0%,
            rgba(87, 84, 196, 1) 100%);
            font-size: 1rem;
        }

        .tips {
            margin-top: 1.69rem;
        }

        .file-list {
            font-size: 0.88rem;
            color: #5a5cc6;
        }

        .file-list .file-item {
            margin-top: 0.63rem;
        }

        .file-list .file-item p {
            line-height: 1.25rem;
            position: relative;
        }

        .file-list img {
            width: 1.25rem;
            cursor: pointer;
        }

        .file-list img.upload-delete {
            position: absolute;
            bottom: 0;
            margin: 0 auto;
            margin-left: 1rem;
        }

        .progress-wrapper {
            position: relative;
            height: 0.5rem;
            border: 0.06rem solid rgba(92, 91, 200, 1);
            border-radius: 1px;
            box-sizing: border-box;
            width: 87%;
        }

        .progress-wrapper .progress-progress {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0%;
            border-radius: 1px;
            background-color: #5c5bc8;
            z-index: 1;
        }

        .progress-rate {
            font-size: 14px;
            height: 100%;
            z-index: 2;
            width: 12%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-rate span {
            display: inline-block;
            width: 100%;
            text-align: right;
        }

        .progress-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-list img.upload-success {
            margin-left: 0;
        }

        .list_function .sort_block {
            float: left;
        }

        .list_function .sort_block a.btn.green {
            color: white;
            margin: 0 5px;
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

        #modal_product_display select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
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
                padding: 0;
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

<body class="fourth">

<div class="bodybox" id="app">
    <div class="mask" :ref="'mask'"></div>

    <!-- header -->
    <header class="noPrint"><img src="images/header1.png" style="width: 100%;"></header>
    <!-- header end -->

    <div class="mainContent">

        <!-- Title -->
        <div class="block">
            <h6>Registry and Tracking Code for Old Inventory</h6>


            <div class="list_function">
                <!-- 從產品資料庫中選產品加入下方的表格 -->
                <div class="new_project">
                    <a class="catalog" title="Add Item from Product Database">
                        <i class="fas fa-list-alt" @click="product_catalog_warehouse()"></i>
                    </a>
                </div>

                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Product ID</dt>
                                <dd>
                                    <input type="text" v-model="fil_prod_id" id="prod_id">
                                </dd>

                                <dt>Product Code</dt>
                                <dd>
                                    <input type="text" v-model="fil_prod_code">
                                </dd>

                                <dt>Inventory Pool</dt>
                                <dd>
                                    <select v-model="fil_pool">
                                        <option value=""></option>
                                        <option value="Project Pool">Project Pool</option>
                                        <option value="Stock Pool">Stock Pool</option>
                                    </select>
                                </dd>

                                <dt>Related Project</dt>
                                <dd>
                                    <select v-model="fil_project_related">
                                        <option v-for="(item, index) in projects" :value="item.id">{{
                                            item.project_name}}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Location</dt>
                                <dd>
                                    <select v-model="fil_location">
                                        <option value=""></option>
                                        <option value="Caloocan">Caloocan</option>
                                        <option value="Makati">Makati</option>
                                    </select>
                                </dd>

                                <dt>Used as Sample?</dt>
                                <dd>
                                    <select v-model="fil_sample">
                                        <option value=""></option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </dd>

                                <dt style="margin-bottom:-18px;">Received Date of Item by Warehouse</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd><input type="date" v-model="fil_date_from"></dd>
                                </div>

                                <div class="half" style="margin-left: 0.5%;">
                                    <dt>to</dt>
                                    <dd><input type="date" v-model="fil_date_to"></dd>
                                </div>

                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="cancel_filters()">Cancel</a>
                                <a class="btn small" @click="clear_filters()">Clear</a>
                                <a class="btn small green" @click="apply_filters(1)">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 排序 -->
                <div class="new_project">
                    <a class="sort"></a>
                    <div id="order_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt1">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                            <option value="3">
                                                Product ID
                                            </option>
                                            <option value="4">
                                                Received Date of Item by Warehouse
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord1">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt>2nd Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt2">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                            <option value="3">
                                                Product ID
                                            </option>
                                            <option value="4">
                                                Received Date of Item by Warehouse
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord2">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>
                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="cancel_orders()">Cancel</a>
                                <a class="btn small" @click="clear_orders()">Clear</a>
                                <a class="btn small green" @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 10 筆資料  -->
                <div class="pagenation">
                    <a class="prev" :disabled="item_page == 1" @click="item_pre_page(); apply_filters(pg);">Prev 10</a>

                    <a class="page" v-for="pg in item_pages_10" @click="item_page=pg; apply_filters(pg);"
                       v-bind:style="[pg == item_page ? { 'background':'#1E6BA8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="item_page == item_pages.length" @click="item_nex_page(); apply_filters(pg);">Next 10</a>
                </div>
            </div>

            <div>
                <table class="registry_list">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Received Date<br>Qty</th>
                        <th>Inventory Pool</th>
                        <th>Location</th>
                        <th>Sample</th>
                        <th>Created Time</th>
                        <th>Last Updated Time</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(item, index) in received_items" :key="index">
                        <td class="pic">
                            <div class="read_block" v-if="item.photo1 != '' && item.status == 1">
                                <img :src="item.photo1">
                            </div>

                            <div class="read_block" v-if="item.photo1 != '' && item.status == 0">
                                <img :src="item.photo1">
                                <div class="photo">
                                    <div @click="clear_photo(item, 1)">x</div>
                                </div>
                            </div>

                            <div class="write_block" v-show="item.photo1 == '' && item.status == 0">
                                <div class="itembox">
                                    <div class="photo">
                                        <input type="file" @change="onFileChangeImage($event, item, 1)" :id="'photo_' + item.id + '_1'">
                                        <img v-if="item.photo1 != ''" :src="item.photo1"/>
                                        <div @click="clear_photo(item, 1)" v-if="item.photo1 != ''">x</div>
                                    </div>
                                </div>
                            </div>

                            <div class="read_block" v-if="item.photo2 != '' && item.status == 1 && 1==0">
                                <img :src="item.photo2">
                            </div>

                            <div class="read_block" v-if="item.photo2 != '' && item.status == 0 && 1==0">
                                <img :src="item.photo2">
                                <div class="photo">
                                    <div @click="clear_photo(item, 2)">x</div>
                                </div>
                            </div>

                            <div class="write_block" v-show="item.photo2 == '' && item.status == 0 && 1==0">
                                <div class="itembox">
                                    <div class="photo">
                                        <input type="file" @change="onFileChangeImage($event, item, 2)" :id="'photo_' + item.id + '_2'">
                                        <img v-if="item.photo2 != ''" :src="item.photo2"/>
                                        <div @click="clear_photo(item, 2)" v-if="item.photo2 != ''">x</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="id">ID: <a class="hyperlink" :href="'product_display_code?id=' + item.product_id + '&v1=' + item.v1 + '&v2=' + item.v2 + '&v3=' + item.v3 + '&v4=' + item.v4" target="_blank">{{ item.product_id }}</a></div>
                            <div class="brand">{{ item.brand }}</div>
                            <div class="code">{{ item.code }}</div>
                            <div class="brief">{{ item.brief }}</div>
                            <div class="read_block">
                                <div class="listing">{{ item.listing }}</div>
                            </div>
                            <div class="read_block" v-if="item.status == 1">
                                <div class="listing">{{ item.desc }}</div>
                            </div>
                            <div class="write_block">
                                <textarea rows="4" placeholder="Describe item's details if needed" v-model="item.desc" v-if="item.status == 0" ></textarea>
                            </div>
                        </td>
                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.receive_date }}
                                <br>
                                {{ item.qty }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <input type="date" v-model="item.receive_date">
                                <input type="number" min="0" v-model="item.qty" placeholder="Qty">
                            </div>
                        </td>
                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.which_pool }}
                                <a :href="'project02?p=' + item.project_id" class="hyperlink block" target="_blank" v-if="item.which_pool == 'Project Pool'"><div>{{ item.project_name }}</div></a>
                            </div>
                            <div class="write_block" v-if="item.status == 0" @change="changePool()">
                                <select v-model="item.which_pool">
                                    <option value="Project Pool">Project Pool</option>
                                    <option value="Stock Pool">Stock Pool</option>
                                </select>

                                <select v-model="item.project_id" v-show="item.which_pool == 'Project Pool'" @change="changeProjectName(item, $event)" >
                                    <option v-for="(prj, idx) in projects" :value="prj.id">{{ prj.project_name }}</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.location }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <select v-model="item.location">
                                    <option value="Caloocan">Caloocan</option>
                                    <option value="Makati">Makati</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.as_sample }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <select v-model="item.as_sample">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            {{ item.created_at != undefined ? item.created_at : ''}} <br> {{ item.created_by != undefined ? '(' + item.created_by + ')' : '' }}
                        </td>
                        <td>
                            {{ item.updated_at != undefined ? item.updated_at : '' }} <br> {{ item.updated_by != undefined ? '(' + item.updated_by + ')' : '' }}
                        </td>
                        <td>
                            <a class="btn small" @click="remove_item(item.id)" v-if="item.status == 0">Delete</a>
                            <a class="btn small green" @click="register(item)" v-if="item.status == 0">Register</a>
                            <a class="btn small green" @click="barcode_printing(item)" v-if="item.status == 1">Label</a>
                        </td>
                    </tr>
                    </tbody>

                    <!--
                    <tbody>
                    <tr v-for="(item, index) in received_items.items" :key="index">
                        <td class="pic">
                            <div class="read_block" v-if="item.photo1 != '' && item.status == 1">
                                <img :src="item.photo1">
                            </div>

                            <div class="read_block" v-if="item.photo1 != '' && item.status == 0">
                                <img :src="item.photo1">
                                <div class="photo">
                                    <div @click="clear_photo(item, 1)">x</div>
                                </div>
                            </div>

                            <div class="write_block" v-show="item.photo1 == '' && item.status == 0">
                                <div class="itembox">
                                    <div class="photo">
                                        <input type="file" @change="onFileChangeImage($event, item, 1)" :id="'photo_' + item.id + '_1'">
                                        <img v-if="item.photo1 != ''" :src="item.photo1"/>
                                        <div @click="clear_photo(item, 1)" v-if="item.photo1 != ''">x</div>
                                    </div>
                                </div>
                            </div>

                            <div class="read_block" v-if="item.photo2 != '' && item.status == 1 && 1==0">
                                <img :src="item.photo2">
                            </div>

                            <div class="read_block" v-if="item.photo2 != '' && item.status == 0 && 1==0">
                                <img :src="item.photo2">
                                <div class="photo">
                                    <div @click="clear_photo(item, 2)">x</div>
                                </div>
                            </div>

                            <div class="write_block" v-show="item.photo2 == '' && item.status == 0 && 1==0">
                                <div class="itembox">
                                    <div class="photo">
                                        <input type="file" @change="onFileChangeImage($event, item, 2)" :id="'photo_' + item.id + '_2'">
                                        <img v-if="item.photo2 != ''" :src="item.photo2"/>
                                        <div @click="clear_photo(item, 2)" v-if="item.photo2 != ''">x</div>
                                    </div>
                                </div>
                            </div>

                        </td>

                        <td>
                            <div class="id">ID: <a class="hyperlink" :href="'product_display_code?id=' + item.pid + '&v1=' + item.v1 + '&v2=' + item.v2 + '&v3=' + item.v3 + '&v4=' + item.v4" target="_blank">{{ item.pid }}</a></div>
                            <div class="brand">{{ item.brand }}</div>
                            <div class="code">{{ item.code }}</div>
                            <div class="brief">{{ item.brief }}</div>
                            <div class="read_block">
                                <div class="listing">{{ item.listing }}</div>
                            </div>
                            <div class="read_block" v-if="item.status == 1">
                                <div class="listing">{{ item.desc }}</div>
                            </div>
                            <div class="write_block">
                                <textarea rows="4" placeholder="Describe item's details if needed" v-model="item.desc" v-if="item.status == 0" ></textarea>
                            </div>
                        </td>

                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.receive_date }}
                                <br>
                                {{ item.qty }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <input type="date" v-model="item.receive_date">
                                <input type="number" min="0" v-model="item.qty" placeholder="Qty">
                            </div>
                        </td>

                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.which_pool }}
                                <a class="hyperlink" :href="'project02?p=' + item.project_id" target="_blank" v-if="item.which_pool == 'Project Pool'"><div>{{ item.project_name }}</div></a>
                            </div>
                            <div class="write_block" v-if="item.status == 0" @change="changePool()">
                                <select v-model="item.which_pool">
                                    <option value="Project Pool">Project Pool</option>
                                    <option value="Stock Pool">Stock Pool</option>
                                </select>

                                <select v-model="item.project_id" v-show="item.which_pool == 'Project Pool'" @change="changeProjectName(item, $event)" >
                                    <option v-for="(prj, idx) in projects" :value="prj.id">{{ prj.project_name }}</option>
                                </select>
                            </div>
                        </td>

                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.location }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <select v-model="item.location">
                                    <option value="Caloocan">Caloocan</option>
                                    <option value="Makati">Makati</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="read_block" v-if="item.status == 1">
                                {{ item.as_sample }}
                            </div>
                            <div class="write_block" v-if="item.status == 0">
                                <select v-model="item.as_sample">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </td>
                        <td>2025/05/28 09:55:11<br>Dennis Lin</td>
                        <td>2025/05/28 09:55:11<br>Dennis Lin</td>
                        <td>
                            <a class="btn small" @click="remove_item(item.id)" v-if="item.status == 0">Delete</a>
                            <a class="btn small green" @click="register(item)" v-if="item.status == 0">Register</a>
                            <a class="btn small green" @click="barcode_printing(received_items.id, item.id)" v-if="item.status == 1">Label</a>
                        </td>
                    </tr>

                    </tbody>
                    -->

                </table>
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
                                    <option v-for="(it, index2) in group.items" :value="it.item_name">{{ it.item_name}}</option>
                                </optgroup>

                            </select>

                            <input type="text" placeholder="Keyword" v-model="fil_keyword" style="margin-left: 20px; width: 300px;">
                        </div>

                        <a class="btn small green" @click="filter_apply_new()">Search</a>
                    </div>


                    <div class="list_function" style="margin: 7px 0;">
                        <div class="sort_block">
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(1)">Amount
                                (Low → High)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(2)">Amount
                                (High → Low)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(3)">QP (Low
                                → High)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;" @click="sort_me(4)">QP (High
                                → Low)</a>
                            <a class="btn small green" style="color: white; margin: 0 5px;"
                               @click="sort_me(0)">Clear</a>
                        </div>
                        <div class="pagenation">
                            <a class="prev" :disabled="product_page == 1" @click="pre_page(); filter_apply();">Prev
                                10</a>
                            <a class="page" v-for="pg in product_pages_10" @click="product_page=pg; filter_apply(pg);"
                               v-bind:style="[pg == product_page ? { 'background':'var(--fth01)', 'color': 'white'} : { }]">{{
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
                                                {{ item.created_at }} {{ item.created_name !== null ? '(' +
                                                item.created_name + ')' : '' }}
                                            </li>
                                        </ul>

                                        <ul>
                                            <li>
                                                Updated:
                                            </li>
                                            <li>
                                                {{ item.updated_name !== null ? item.updated_at : '' }} {{
                                                item.updated_name !== null ? '(' + item.updated_name + ')' : '' }}
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
                                        <button id="edit01" @click="btnEditClick(item)" v-if="item.status != -1"><i
                                                aria-hidden="true" class="fas fa-caret-right"></i></button>
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
                                                <span class="phasedout_replacement"
                                                      v-if="set.status != -1 && set.replacement_product.length > 0"
                                                      @click="replacement_info(set.replacement_text)">Phased Out</span>
                                                <span class="phasedout_replacement"
                                                      v-if="set.status == -1 && set.replacement_product.length > 0"
                                                      @click="replacement_info(set.replacement_text)">Deleted</span>
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
                                                {{ set.created_at }} {{ set.created_name !== null ? '(' +
                                                set.created_name + ')' : '' }}
                                            </li>
                                        </ul>

                                        <ul>
                                            <li>
                                                Updated:
                                            </li>
                                            <li>
                                                {{ set.updated_name !== null ? set.updated_at : '' }} {{
                                                set.updated_name !== null ? '(' + set.updated_name + ')' : '' }}
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
                                            ※ {{ set.phased_out_cnt }} variant{{ set.phased_out_cnt > 1 ? 's' : '' }}
                                            are phased out.

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
                                                <template v-for="(att_value, index) in att.value">{{att_value}}
                                                </template>
                                            </li>

                                        </ul>

                                    </td>

                                    <td>
                                        <span v-show="((cost_lighting == true && set.category == 'Lighting') || (cost_furniture == true && set.category == 'Systems Furniture')) && toggle == true">CP: {{ set.price_ntd }} <br
                                                v-if="set.str_price_ntd_change"> {{ set.str_price_ntd_change ?  set.str_price_ntd_change : '' }} <br></span>
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
                                    </td>
                                </tr>


                                <!-- 非 Product Set 子類別的產品，套用以下格式輸出到頁面上 -->
                                <tr v-for="(item, index) in displayedPosts">
                                <tr v-if="item.sub_category != '10020000'">
                                    <td>
                                        <img :src="img_url + item.photo1" v-if="item.photo1 !== ''">
                                    </td>
                                    <td>
                                        <ul v-if="item.out == 'Y' || (item.out == 'Y' && item.replacement_product.length > 0) || (item.status == -1 && item.replacement_product.length > 0)">
                                            <li>
                                                <!-- 依據這個停產的產品是否有 Replacement Product 的資料，沒有資料則用第一個 <span>，有資料則用二個 <span> -->
                                                <span class="phasedout" v-if="item.replacement_product.length == 0">Phased Out</span>
                                                <span class="phasedout_replacement"
                                                      v-if="item.status != -1 && item.replacement_product.length > 0"
                                                      @click="replacement_info(item.replacement_text)">Phased Out</span>
                                                <span class="phasedout_replacement"
                                                      v-if="item.status == -1 && item.replacement_product.length > 0"
                                                      @click="replacement_info(item.replacement_text)">Deleted</span>
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
                                        <ul class="last_order_history" v-if="item.is_last_order != ''">
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
                                        <span v-show="((cost_lighting == true && item.category == 'Lighting') || (cost_furniture == true && item.category == 'Systems Furniture')) === true">CP: {{ item.price_ntd }} <br
                                                v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }}<br></span>
                                        <span>Amount: {{ item.price }}<br v-if="item.str_price_change"> {{ item.str_price_change ?  item.str_price_change : '' }}<br></span>
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
                                    </td>
                                    <td>
                                        <button id="edit01" @click="btnEditClick(item)" v-if="item.status != -1"><i
                                                aria-hidden="true"
                                                class="fas fa-caret-right"></i>
                                        </button>
                                    </td>
                                </tr>

                            </template>
                            </tbody>
                        </table>

                    </div>
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
                                    <button class="btn btn-info" @click="add_with_image_set_select_warehouse()" v-if="out==''">Add
                                        with Image 
                                    </button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image_set_select_warehouse()" v-if="out==''">
                                        Add without Image 
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
                                        <button @click="last_order_info(set.is_last_order)" v-if="set.last_have_spec">
                                            Last Order History
                                        </button>
                                        <span v-if="set.last_order_name != ''">Last Ordered: {{ set.last_order_at }} at <a
                                                :href="set.last_order_url">{{ set.last_order_name }}</a></span>
                                    </div>

                                    <span class="phasedout" v-if="set.out == 'Y' && set.out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="set.out_cnt == 1"
                                          @click="PhaseOutAlert_set(set.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="set.out_cnt > 1"
                                          @click="PhaseOutAlert_set(set.phased_out_text1)">{{ set.out_cnt }} variants are phased out</span>
                                    <h3 style="word-break: break-all;">{{set.code}}</h3>
                                    <h6>{{set.brand}}</h6>
                                    <h6>{{ set.category}} >> {{set.sub_category_name}}</h6>
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
                                        {{ set.variation1 !== 'custom' ? set.variation1 + ': ' : set.variation1_custom +
                                        ': '}}
                                        <template v-for="(item, index) in set.variation1_value">{{ (index + 1 !==
                                            set.variation1_value.length) ? item + ', ' : item}}
                                        </template>
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
                                        {{ set.variation2 !== 'custom' ? set.variation2 + ': ' : set.variation2_custom +
                                        ': ' }}
                                        <template v-for="(item, index) in set.variation2_value">{{ (index + 1 !==
                                            set.variation2_value.length) ? item + ', ' : item}}
                                        </template>
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
                                        {{ set.variation3 !== 'custom' ? set.variation3 + ': ' : set.variation3_custom +
                                        ': ' }}
                                        <template v-for="(item, index) in set.variation3_value">{{ (index + 1 !==
                                            set.variation3_value.length) ? item + ', ' : item}}
                                        </template>
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
                                        {{ set.variation4 !== 'custom' ? set.variation4 + ': ' : set.variation4_custom +
                                        ': ' }}
                                        <template v-for="(item, index) in set.variation4_value">{{ (index + 1 !==
                                            set.variation4_value.length) ? item + ', ' : item}}
                                        </template>
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
                                            <select class="selectpicker" data-width="100%" :id="set.id + 'tag'+index">
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
                                            <button class="btn btn-info" @click="add_with_image_set_warehouse(set)"
                                                    v-if="set.out==''">Add with Image 
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set_warehouse(set)"
                                                    v-if="set.out==''">Add without Image 
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

                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement"
                                   role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement"
                                   role="button" data-slide="next">
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
                                        <button @click="last_order_info(set.is_last_order)" v-if="set.last_have_spec">
                                            Last Order History
                                        </button>
                                        <span v-if="set.last_order_name != ''">Last Ordered: {{ set.last_order_at }} at <a
                                                :href="set.last_order_url">{{ set.last_order_name }}</a></span>
                                    </div>

                                    <span class="phasedout" v-if="set.out == 'Y' && set.out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="set.out_cnt == 1"
                                          @click="PhaseOutAlert_set(set.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="set.out_cnt > 1"
                                          @click="PhaseOutAlert_set(set.phased_out_text1)">{{ set.out_cnt }} variants are phased out</span>

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
                                            <button class="btn btn-info" @click="add_with_image_set_warehouse(set)"
                                                    v-if="set.out==''">Add with Image 
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_set_warehouse(set)"
                                                    v-if="set.out==''">Add without Image 
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
                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement"
                                   role="button"
                                   data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement"
                                   role="button"
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
                                    <div class="last_order_history" v-if="product.is_last_order != ''">

                                        <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                        <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <button @click="last_order_info(product.is_last_order)"
                                                v-if="product.last_have_spec">Last Order History
                                        </button>
                                        <span v-if="product.last_order_url != ''">Last Ordered: {{ product.last_order_at }} at <a
                                                :href="product.last_order_url">{{ product.last_order_name }}</a></span>
                                    </div>

                                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                                    <span class="phasedout1" v-if="out_cnt == 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">1 variant is phased out</span>
                                    <span class="phasedout1" v-if="out_cnt > 1"
                                          @click="PhaseOutAlert(product.phased_out_text1)">{{ out_cnt }} variants are phased out</span>
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
                                            <button class="btn btn-info" @click="add_with_image_warehouse()" v-if="out==''">Add
                                                with Image 
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_warehouse()" v-if="out==''">Add
                                                without Image 
                                            </button>
                                        </li>
                                    </ul>

                                    <!--
                                    <ul v-if="product.variation_mode == 1">
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')"  v-if="product.out == '' && (out=='' || product.variation_mode == 1)">> with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                without Image
                                            </button>
                                        </li>
                                    </ul>
                                    -->

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
                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement"
                                   role="button"
                                   data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement"
                                   role="button"
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
                                    <div class="last_order_history" v-if="product.is_last_order != ''">

                                        <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                                        <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                                        <button @click="last_order_info(product.is_last_order)"
                                                v-if="product.last_have_spec">Last Order History
                                        </button>
                                        <span v-if="product.last_order_url != ''">Last Ordered: {{ product.last_order_at }} at <a
                                                :href="product.last_order_url">{{ product.last_order_name }}</a></span>
                                    </div>
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
                                            <button class="btn btn-info" @click="add_with_image_warehouse()" v-if="out==''">Add
                                                with Image 
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image_warehouse()" v-if="out==''">Add
                                                without Image 
                                            </button>
                                        </li>
                                    </ul>

                                    <!--
                                    <ul>
                                        <li v-if="toggle_type == 'A'">
                                            <button class="btn btn-info" @click="add_with_image('all')"  v-if="product.out == '' && (out=='' || product.variation_mode == 1)">> with
                                                Image
                                            </button>
                                        </li>
                                        <li>
                                            <button class="btn btn-info" @click="add_without_image('all')" v-if="product.out == '' && (out=='' || product.variation_mode == 1)">
                                                without Image
                                            </button>
                                        </li>
                                    </ul>
                                    -->

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
                                <a class="carousel-control-prev" href="#carouselExampleControls_replacement"
                                   role="button"
                                   data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls_replacement"
                                   role="button"
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


    <!-- Warehouse in Charge 人員用來 列印或註銷 Barcode 的彈出視窗 -->
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modal_barcode_printing">

        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

            <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

                <div class="modal-header">

                    <h4 class="modal-title" id="myLargeModalLabel">Label Printing</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close"
                            @click="close_barcode_printing()">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <div class="block">
                        <div class="list_function">
                            <!-- 分頁功能 -->
                            <!--
                            <div class="pagenation">
                                <a class="prev" :disabled="barcode_page == 1" @click="pre_page_barcode(); get_barcode_records(received_items.id, item_id);">Prev 10</a>

                                <a class="page" v-for="pg in barcode_pages_10" @click="barcode_page=pg; get_barcode_records(received_items.id, item_id);" v-bind:style="[pg == barcode_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="barcode_page == barcode_pages.length" @click="nex_page_barcode(); get_barcode_records(received_items.id, item_id);">Next 10</a>
                            </div>
                            -->
                        </div>
                    </div>

                    <table class="barcode_list">
                        <thead>
                        <tr>
                            <th><i class="micons">view_list</i></th>
                            <th>Barcode</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Inventory Pool</th>
                            <th>Location</th>
                            <th>Sample</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(item, index) in displayBarcodeItems">
                            <td><input type="checkbox" class="alone" :value="item.id" :true-value="1"
                                       v-model:checked="item.is_checked"></td>
                            <td>
                                {{ item.barcode }}
                            </td>
                            <td class="pic">
                                <div class="read_block">
                                    <img :src="item.pic" v-if="item.pic != ''">
                                </div>
                            </td>

                            <td>
                                <div class="id">ID: <a class="hyperlink"
                                                       :href="'product_display_code?id=' + item.product_id + '&v1=' + item.v1 + '&v2=' + item.v2 + '&v3=' + item.v3 + '&v4=' + item.v4"
                                                       target="_blank">{{ item.product_id }}</a></div>
                                <div class="brand">{{ item.brand }}</div>
                                <div class="code">{{ item.code }}</div>
                                <div class="brief">{{ item.brief }}</div>
                                <div class="read_block">
                                    <div class="listing">{{ item.listing }} {{ item.desc }}</div>
                                </div>
                            </td>

                            <td>
                                <div class="read_block">
                                    {{ item.which_pool }}
                                    <a class="hyperlink block" :href="'project02?p=' + item.project_id" target="_blank"
                                       v-if="item.which_pool == 'Project Pool'">{{ item.project_name }}</a>
                                </div>
                            </td>

                            <td>
                                <div class="read_block">
                                    {{ item.location }}
                                </div>
                            </td>
                            <td>
                                <div class="read_block">
                                    {{ item.as_sample }}
                                </div>
                            </td>
                        </tr>
                        </tbody>

                    </table>

                    <div class="block">
                        <div class="list_function">
                            <!-- 分頁功能 -->
                            <!--
                            <div class="pagenation">
                                <a class="prev" :disabled="barcode_page == 1" @click="pre_page_barcode(); get_barcode_records(received_items.id, item_id);">Prev 10</a>

                                <a class="page" v-for="pg in barcode_pages_10" @click="barcode_page=pg; get_barcode_records(received_items.id, item_id);" v-bind:style="[pg == barcode_page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                                <a class="next" :disabled="barcode_page == barcode_pages.length" @click="nex_page_barcode(); get_barcode_records(received_items.id, item_id);">Next 10</a>
                            </div>
                            -->
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="btnbox">
                        <a class="btn small" @click="close_barcode_printing()">Cancel</a>
                        <a class="btn small" @click="void_barcode_selected()">Void Barcode</a>
                        <a class="btn small" @click="deselect_all()">Deselect All</a>
                        <a class="btn small green" @click="select_all()">Select All</a>
                        <a class="btn small green" @click="print_barcode()">Print Label</a>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>


</body>

<script>

    function move_left() {
        document.getElementsByClassName('block A')[0].scrollLeft -= 400;
    };

    function move_right() {
        document.getElementsByClassName('block A')[0].scrollLeft += 400;
    };

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
<script defer src="js/old_inventory_register.js"></script>
<script src="js/canvas2image/canvas2image.js"></script>
<script defer src="js/html2canvas/html2canvas.min.js"></script>
</html>
