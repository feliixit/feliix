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
    <title>Order – Taiwan</title>
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
          href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
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

        body {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
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

        body.gray select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
            font-size: 16px;
            border: 1px solid #707070;
            margin: 5px 0;
        }

        body.gray input.alone[type=checkbox]::before, input[type=checkbox] + Label::before {
            color: #707070;
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

        body.gray .mainContent > .block {
            display: block;
            width: 100%;
            border: none;
            margin: 0 0 15px;
            border-top: 2px solid #94BABB;
            overflow-x: auto;
        }

        body.gray .mainContent > .block > .box-content {
            padding: 10px 15px 30px;
        }

        body.gray .dialog .formbox .half {
            width: 48.5%;
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
            width: 400px;
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
            padding: 3px 6px 6px 6px;
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

        .list_function.main {
            border-color: #707070;
            margin: 10px 0 15px;
        }

        .list_function.main > .block {
            margin-left: 5px;
            margin-bottom: 10px;
        }

        .list_function.main .tag {
            padding: 3px 10px;
            font-weight: 700;
        }

        .list_function.main .btn_block {
            display: inline-block;
        }

        .list_function.main .btn_block a.btn {
            color: white;
            width: 170px;
        }

        .list_function.main .btn_block input[type="text"] {
            float: right;
            margin-left: 10px;
            border-radius: 10px;
            border: 1px solid #333;
            padding: 2px 8px;
            width: 500px;
        }

        .list_function.main .popupblock a {
            background-color: #707070;
            width: 30px;
            height: 30px;
            text-align: center;
        }

        .list_function.main .popupblock a i {
            font-size: 20px;
            color: white;
            line-height: 1.45;
        }

        .list_function .pagenation a {
            color: #707071;
            border-color: #707071;
        }

        .list_function .pagenation a:hover {
            color: #FFF;
            background-color: #707071;
        }

        .tb_order {
            font-size: 16px;
            border: 1px solid black;
        }

        .tb_order thead tr th {
            border-top: 2px solid #C3C3C3;
            border-right: 2px solid #C3C3C3;
            text-align: center;
            font-weight: 800;
            background-color: #D7E7E8;
            color: #000000;
            padding: 12px 5px;
        }

        .tb_order tbody tr td {
            padding: 8px 20px;
            border-right: 2px solid #C3C3C3;
            border-bottom: 2px solid #C3C3C3;
        }

        .tb_order thead tr th:first-of-type {
            border-left: 2px solid #C3C3C3;
        }

        .tb_order tbody tr td:first-of-type {
            border-left: 2px solid #C3C3C3;
        }

        .tb_order tbody tr td.pic .read_block, .tb_order tbody tr td.pic .write_block {
            display: flex;
        }

        .tb_order tbody tr td:last-of-type .btnbox {
            padding: 5px 0;
        }

        .tb_order tbody tr td:last-of-type .btnbox button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px;
            margin: 0 3px;
        }

        .tb_order tbody tr td:last-of-type .btnbox a.btn {
            color: white;
            font-weight: 800;
            width: 98px;
            margin: 0 4px;
        }

        .tb_order tbody tr td:last-of-type .btnbox > i {
            font-size: 24px;
            color: #206766;
            margin: 0 4px;
            cursor: pointer;
        }

        .block.C .tb_order thead tr th:nth-of-type(2), .block.C .tb_order tbody tr td:nth-of-type(2) {
            min-width: 90px;
            text-align: center;
        }

        .block.C .tb_order thead tr th:nth-of-type(3), .block.C .tb_order tbody tr td:nth-of-type(3) {
            min-width: 230px;
            text-align: center;
        }

        .block.C .tb_order thead tr th:nth-of-type(4), .block.C .tb_order tbody tr td:nth-of-type(4) {
            min-width: 210px;
        }

        .block.C .tb_order thead tr th:nth-of-type(5) {
            min-width: 930px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(5) {
            width: 530px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(6) {
            min-width: 400px;
        }

        .block.C .tb_order thead tr th:nth-of-type(6), .block.C .tb_order tbody tr td:nth-of-type(7) {
            min-width: 130px;
        }

        .block.C .tb_order thead tr th:nth-of-type(7), .block.C .tb_order tbody tr td:nth-of-type(8) {
            min-width: 130px;
        }

        .block.C .tb_order thead tr th:nth-of-type(8), .block.C .tb_order tbody tr td:nth-of-type(9) {
            min-width: 180px;
        }

        .block.C .tb_order thead tr th:nth-of-type(9), .block.C .tb_order tbody tr td:nth-of-type(10) {
            min-width: 200px;
        }

        .block.C .tb_order thead tr th:nth-of-type(10), .block.C .tb_order tbody tr td:nth-of-type(11) {
            min-width: 450px;
        }

        .block.C .tb_order thead tr th:last-of-type, .block.C .tb_order tbody tr td:last-of-type {
            min-width: 120px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(12) {
            min-width: 450px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(13) {
            min-width: 220px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(14),
        .block.C .tb_order tbody tr td:nth-of-type(15) {
            min-width: 180px;
        }

        .block.C .tb_order tbody tr td:nth-of-type(16),
        .block.C .tb_order tbody tr td:nth-of-type(17),
        .block.C .tb_order tbody tr td:nth-of-type(18),
        .block.C .tb_order tbody tr td:nth-of-type(19) {
            min-width: 400px;
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

        .read_block img {
            height: 150px;
            width: 150px;
            object-fit: contain;
            margin: 5px;
        }

        .write_block {
            text-align: center;
        }

        .read_block select, .write_block select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 16px;
            height: 30px;
            margin: 5px 0;
            width: 100%;
        }

        .read_block select:disabled{
            opacity: 1;
            color: black;
        }

        .read_block input, .write_block input {
            height: 30px;
            border: 1px solid #707070;
            font-size: 16px;
            margin: 5px 0;
            width: 100%;
        }

        .read_block input[type="checkbox"], .write_block input[type="checkbox"] {
            border: none;
            vertical-align: 0.5px;
        }

        .read_block input[type="checkbox"]:disabled {
            opacity: 0.8;
        }

        .read_block textarea, .write_block textarea {
            border: 1px solid #707070;
            font-size: 16px;
            resize: none;
            margin: 5px 0;
            width: 100%;
        }

        .read_block .photobox{
            display: flex;
            justify-content: center;
        }

        .write_block .itembox {
            display: inline-block;
            margin: 5px;
            display: flex;
            justify-content: center;
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

        .write_block .photobox{
            height: 210px;
            display: flex;
            justify-content: center;
        }

        .msg_block .msgbox {
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            margin-bottom: 25px;
        }

        .msg_block .msgbox a.attch {
            display: block;
            color: #25a2b8;
            transition: .3s;
            margin: 2px 0 2px 5px;
        }

        .msg_block .msgbox i.t {
            color: var(--fth01);
            font-style: normal;
            display: block;
            margin-bottom: 5px;
        }

        .msg_block .msgbox .already_read {
            font-size: 10px;
            color: #1e6ba8;
        }

        .msg_block .msgbox .btnbox {
            text-align: right;
            padding: 0 5px 5px 0;
        }

        .msg_block .msgbox .btnbox a.btn.green {
            color: white;
        }

        .msg_block .msgbox.deleted {
            text-decoration: line-through;
            text-decoration-color: red;
        }

        .msg_block .msgbox.deleted a.attch {
            text-decoration-color: red;
        }

        .msg_block .msgbox.deleted .btnbox {
            display: none;
        }

        .block.C .tb_order tbody tr td:nth-of-type(9) .msg_block .msgbox .btnbox {
            display: none;
        }

        .write_msg_block {
            border: 1px solid #333;
            border-radius: 10px;
            padding: 7px;
        }

        .write_msg_block textarea {
            width: 100%;
            font-size: 14px;
            margin-bottom: 5px;
            border: none;
            resize: none;
        }

        .write_msg_block .file_block {
            display: flex;
        }

        .write_msg_block .file_block > span {
            color: green;
            font-size: 14px;
            font-weight: 500;
            padding-bottom: 5px;
            margin-right: 10px;
        }

        .write_msg_block .btnbox {
            text-align: right;
            padding: 0;
            color: white;
        }

        .btnbox i.fa-times-circle {
            color: indianred !important;
        }

        button.quick_move{
            position: fixed;
            top: calc(50vh + 30px);
            width: 50px;
            height: 50px;
            border-radius: 25px;
            border: 1px solid rgb(153,153,153);
            font-size: 23px;
            font-weight: 500;
            background-color: rgba(7, 220, 237, 0.5);
            z-index: 999;
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
            width: 250px;
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

<body class="gray">

<div class="bodybox" id="app">

    <!-- move-left/right shortcut -->
    <button class="quick_move" style="left: 5px;" onclick="move_left();">←</button>
    <button class="quick_move" style="right: 5px;" onclick="move_right();">→</button>

    <div class="mask" :ref="'mask'"></div>

    <!-- header -->
    <header class="noPrint"><img src="images/header1.png" style="width: 100%;"></header>
    <!-- header end -->

    <div class="mainContent">

        <div class="list_function main" style="border: none; margin-bottom: 20px; padding-left: 15px;">

            <div class="block" style="display: flex; justify-content: space-between;">
                <!-- tag -->
                <div>
                    <b class="tag focus">ORDER – TAIWAN</b>
                    <b class="tag" style="margin-right: 30px;">{{ serial_name + ' ' + od_name }}</b>
                    <b class="tag focus">PROJECT</b>
                    <a :href="'project03_other?sid=' + stage_id"><b class="tag">{{ project_name }}</b></a>
                </div>


                <a title="Export into Excel" style="color: #4882C3; font-size: 25px; float: right;">
                    <i class="fas fa-file-export" @click="export_petty()"></i>
                </a>

            </div>

        </div>


        <div class="tags">
            <a class="tag A" @click="p1()">Preliminary</a>
            <a class="tag B" @click="p2()">For Approval</a>
            <a class="tag C focus">Approved</a>
            <a class="tag D" @click="p4()">Overview</a>
        </div>
        <!-- Blocks -->
        <div class="block C" style="display: block;">

            <div class="box-content">

                <div class="list_function main">

                    <!-- buttons to add product -->
                    <div class="block">

                        <div class="popupblock" v-if="info_type == '' && access2 == true">
                            <a title="Add Item by Manual Encoding">
                                <i class="fas fa-plus" @click="addItem()"></i>
                            </a>
                        </div>

                        <div class="popupblock" v-if="info_type == '' && access2 == true">
                            <a title="Add Item from Product Database">
                                <i class="fas fa-list-alt"  @click="product_catalog()"></i>
                            </a>
                        </div>

                        <div class="popupblock" v-if="info_type == '' && access2 == true"> 
                            <a title="Export Supplier's Document">
                                <i class="fas fa-file-excel" @click="export_excel()"></i>
                            </a>
                        </div>

                        <div class="btn_block">
                            <a class="btn small green" @click="approve()" v-if="MarkasApproved()">Mark as Approved</a>
                            <a class="btn small green" @click="order()" v-if="MarkasOrdered()">Mark as Ordered</a>
                            <a class="btn small green" @click="cancel()" v-if="MarkasCanceled()">Mark as Canceled</a>

                            <a class="btn small green" @click="edit_shipping_info('ship_info')" v-if="EditShippingInfo() && no_privlege() != true">Edit Shipping Info</a>
                            <a class="btn small green" @click="edit_shipping_info('ware_info')" v-if="EditWarehouseInfo() && no_privlege() != true">Edit Warehouse Info</a>
                            <a class="btn small green" @click="edit_shipping_info('assing_test')" v-if="AssignTesting() && no_privlege() != true && 1==0">Assign Testing</a>
                            <a class="btn small green" @click="edit_shipping_info('edit_test')" v-if="EditTestingInfo() && no_privlege() != true">Edit Testing Info</a>
                            <a class="btn small green" @click="edit_shipping_info('assign_delivery')" v-if="AssignDelivery() && no_privlege() != true && 1==0">Assign Delivery</a>
                            <a class="btn small green" @click="edit_shipping_info('edit_delivery')" v-if="EditDeliveryInfo() && no_privlege() != true">Edit Delivery Info</a>
                            <a class="btn small green" @click="edit_shipping_info('edit_final')" v-if="EditFinalInfo() && no_privlege() != true">Edit Final Info</a>
                            <a class="btn small" @click="cancel_shipping_info()" v-if="Cancel()">Cancel</a>
                            <a class="btn small green" @click="save_shipping_info()" v-if="Save()">Save</a>
                           
                            <input type="text" placeholder="Comment" v-model="comment" v-if="(access2 == true || access4 == true || access5 == true || access6 == true) && no_privlege() != true">
                        </div>

                    </div>


                </div>


                <table class="tb_order">
                    <thead>
                    <tr>
                        <th><i class="micons">view_list</i></th>
                        <th>#</th>
                        <th>Status</th>
                        <th>Brand</th>
                        <th colspan="2">Description</th>
                        <th>Qty Needed</th>
                        <th>Backup Qty</th>
                        <th>SRP</th>
                        <th>Date Needed by Client</th>
                        <th>Notes</th>
                        <th>Notes (Only for Approved Stage)</th>
                        <th>Shipping Way</th>
                        <th>ETA</th>
                        <th>Arrival Date</th>
                        <th>Warehouse In Charge</th>
                        <th>Testing</th>
                        <th>Delivery</th>
                        <th>Final</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(item, index) in items" :class="['print_area_' + item.id]">
                        <td><input type="checkbox" class="alone" :value="item.index" :true-value="1" v-model:checked="item.is_checked"></td>
                        <td> {{item.serial_number}} </td>
                        <td>
                            <div class="read_block" v-if="!item.is_edit">
                            {{ item.confirm_text }}<br>
                            
                            </div>

                            <div class="write_block" v-if="item.is_edit">
                                <select v-model="item.confirm">
                                    <option value="A">Approved</option>
                                    <option value="O">Ordered</option>
                                    <option value="E">Canceled</option>
                                </select>
                            </div>
                        </td>

                        <td>
                            <div class="read_block" v-if="!item.is_edit">
                                {{ item.brand == 'OTHER' ? item.brand_other : item.brand }}
                            </div>
                            <div class="write_block" v-if="item.is_edit">
                                <select v-model="item.brand">
                                    <option v-for="it in brands" :value="it.brand" :key="it.brand">{{ it.brand }}</option>
                                    <option value="OTHER">OTHER</option>
                                </select>
                                <input type="text" v-model="item.brand_other" v-if="item.brand === 'OTHER'">
                            </div>
                        </td>

                        <td class="pic">
                            <div class="read_block" v-if="!item.is_edit">
                                <img v-if="item.photo1" :src="item.photo1">
                                <img v-if="item.photo2" :src="item.photo2">
                                <img v-if="item.photo3" :src="item.photo3">
                            </div>
            
            <div class="write_block" v-if="item.is_edit">
                <div :class="['itembox', (item.photo1 !== null ? 'chosen' : '')]">
                    <div class="photo">
                        <input type="file" :id="'photo_' + item.id + '_1'"  @change="onFileChangeImage($event, item, 1)">
                        <img v-if="item.photo1" :src="item.photo1"/>
                        <div @click="clear_photo(item, 1)">x</div>
                    </div>
                </div>
                <div :class="['itembox', (item.photo2 !== null ? 'chosen' : '')]">
                    <div class="photo">
                        <input type="file" :id="'photo_' + item.id + '_2'"  @change="onFileChangeImage($event, item, 2)">
                        <img v-if="item.photo2" :src="item.photo2"/>
                        <div @click="clear_photo(item, 2)">x</div>
                    </div>
                </div>
                <div :class="['itembox', (item.photo3 !== null ? 'chosen' : '')]">
                    <div class="photo">
                        <input type="file" :id="'photo_' + item.id + '_3'"  @change="onFileChangeImage($event, item, 3)">
                        <img v-if="item.photo3" :src="item.photo3"/>
                        <div @click="clear_photo(item, 3)">x</div>
                    </div>
                </div>
            </div>
            </td>

            <td>
                <div class="read_block" v-if="!item.is_edit">
                    <div class="id">ID: {{ item.pid != 0 ? item.pid : ''}}</div>
                    <div class="code">{{ item.code }}</div>
                    <div class="brief">{{ item.brief }}</div>
                    <div class="listing">{{ item.listing }}
                    </div>
                </div>
                <div class="write_block" v-if="item.is_edit">
                    <input type="text" placeholder="Code" v-model="item.code"><br>
                    <textarea rows="2" placeholder="Brief" v-model="item.brief"></textarea><br>
                    <textarea rows="4" placeholder="Listing" v-model="item.listing"></textarea>
                </div>
            </td>

            <td>
                <div class="read_block" v-if="!item.is_edit">
                {{ item.qty }}
                </div>
                <div class="write_block" v-if="item.is_edit">
                    <input type="text" v-model="item.qty">
                </div>
            </td>

            <td>
                <div class="read_block" v-if="!item.is_edit">
                {{ item.backup_qty }}
                </div>
                <div class="write_block" v-if="item.is_edit">
                    <input type="text" v-model="item.backup_qty">
                </div>
            </td>

            <td>
                <div class="read_block" v-if="!item.is_edit">
                    {{ item.srp != '' ? '₱ ' + item.srp : '' }}
                </div>
                <div class="write_block" v-if="item.is_edit">
                    <input type="text" v-model="item.srp">
                </div>
            </td>

            <td>
                <div class="read_block" v-if="!item.is_edit">
                    {{ item.date_needed }}
                </div>
                <div class="write_block" v-if="item.is_edit">
                    <input type="text" v-model="item.date_needed">
                </div>
            </td>

            <td>
                <div class="msg_block">
                    <div :class="['msgbox', (note.status == -1 ? 'deleted' : '')]" v-for="note in item.notes">
                        • {{ note.message }}

                        <template v-for="file in note.attachs">
                            <a class="attch" :href="img_url + file.gcp_name" target="_blank">{{file.filename}}</a>
                        </template>
                        
                        <i class="t">({{ note.username }} at {{ note.created_at }})</i>
                        <div class="already_read"><template v-if="note.got_it != undefined" v-for="(got, index) in note.got_it">{{ got.username }}<span v-if="index + 1 < note.got_it.length">, </span></template></div>
                        <div class="btnbox">
                          
                        </div>
                    </div>

                </div>

            </td>

            <td>
                <div class="msg_block">
                    <div :class="['msgbox', (note.status == -1 ? 'deleted' : '')]" v-for="note in item.notes_a">
                        • {{ note.message }}

                        <template v-for="file in note.attachs">
                            <a class="attch" :href="img_url + file.gcp_name" target="_blank">{{file.filename}}</a>
                        </template>
                        
                        <i class="t">({{ note.username }} at {{ note.created_at }})</i>
                        <div class="already_read"><template v-if="note.got_it != undefined" v-for="(got, index) in note.got_it">{{ got.username }}<span v-if="index + 1 < note.got_it.length">, </span></template></div>
                        <div class="btnbox">
                            <a class="btn small green"  @click="got_it_message_a(note.id, item.id)" v-if="note.i_got_it == false">Got it</a>
                            
                            <a class="btn small yellow" v-if="note.create_id == uid" @click="msg_delete_a(note.id, item.id)">Delete</a>
                        </div>
                    </div>

                </div>

                <div class="write_msg_block" v-if="!item.is_info">

                    <textarea rows="3" :ref="'comment_task_' + item.id" placeholder="Leave notes here"></textarea>

                    <div class="file_block">
                        <span>Files: </span>
                        <div class="pub-con">
                            <div class="input-zone">
                                <span class="upload-des">choose file</span>

                                    
                                <input class="input" type="file" :ref="'file_task_' + item.id" placeholder="choose file" @change="changeTaskFile(item.id)" multiple />
                                    </div>
                        </div>
                    </div>
                    <div class="file-list">
                        <a class="attch" v-for="(fs,index) in taskItems(item.id)" :key="index" @click="deleteTaskFile(item.id, index)">{{fs.name}}</a>
                    </div>

                    <div class="btnbox"> 
                        <a class="btn small green" @click="comment_create_a(item.id)">Create</a>
                    </div>
                </div>

            </td>

            <td>
                <div class="read_block" v-if="ShipwayRead(item)">
                    <select disabled v-model="item.shipping_way">
                        <option value=""></option>
                        <option value="sea">Sea</option>
                        <option value="air">Air</option>
                    </select>
                    <input type="text" placeholder="Container No."  v-model="item.shipping_number" v-if="item.shipping_way == 'sea'" readonly>
                    <input type="text" placeholder="Air Record No."  v-model="item.shipping_number" v-if="item.shipping_way == 'air'" readonly>

                    <select disabled v-model="item.shipping_vendor">
                        <option value=""></option>
                        <option value="ssit">盛盛</option>
                        <option value="cfs">卡菲斯</option>
                        <option value="dy">東渝</option>
                    </select>
                </div>
                <div class="write_block" v-if="ShipwayWrite(item)">
                    <select v-model="item.shipping_way">
                        <option value=""></option>
                        <option value="sea">Sea</option>
                        <option value="air">Air</option>
                    </select>
                    
                    <input type="text" placeholder="Container No."  v-model="item.shipping_number" v-if="item.shipping_way == 'sea'" >
                    <input type="text" placeholder="Air Record No."  v-model="item.shipping_number" v-if="item.shipping_way == 'air'">

                    <select v-model="item.shipping_vendor">
                        <option value=""></option>
                        <option value="ssit">盛盛</option>
                        <option value="cfs">卡菲斯</option>
                        <option value="dy">東渝</option>
                    </select>
                </div>
            </td>

            <td>
                <div class="read_block" v-if="EtaRead(item)">
                    <input type="text" v-model="item.eta" readonly>
                </div>
                <div class="write_block" v-if="EtaWrite(item)">
                    <input type="text" v-model="item.eta">
                </div>
            </td>

            <td>
                <div class="read_block" v-if="ArriveRead(item)">
                    <input type="text" v-model="item.arrive" readonly>
                </div>
                <div class="write_block" v-if="ArriveWrite(item)">
                    <input type="text" v-model="item.arrive">
                </div>
            </td>

            <td>
                <div class="read_block" v-if="ArriveRemarkRead(item)">
                    Confirm Arrival:  <input type="checkbox" :value="item.charge" :true-value="1" v-model:checked="item.charge" class="alone" disabled>

                    <div class="photobox">
	                    <img v-if="item.photo4" :src="item.photo4">
	                    <img v-if="item.photo5" :src="item.photo5">
                    </div>

                    <textarea rows="3" readonly v-model="item.remark"></textarea>
                </div>



                <div class="write_block" v-if="ArriveRemarkWrite(item)">
                    Confirm Arrival:  <input type="checkbox" class="alone" :value="item.charge" :true-value="1" v-model:checked="item.charge" >

                    <div class="photobox">

                        <div :class="['itembox', (item.photo4 !== null ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_4'"  @change="onFileChangeImage($event, item, 4)">
                                <img v-if="item.photo4" :src="item.photo4"/>
                                <div @click="clear_photo(item, 4)">x</div>
                            </div>
                        </div>

                        <div :class="['itembox', (item.photo5 !== null ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_5'"  @change="onFileChangeImage($event, item, 5)">
                                <img v-if="item.photo5" :src="item.photo5"/>
                                <div @click="clear_photo(item, 5)">x</div>
                            </div>
                        </div>

                    </div>




                    <textarea rows="3" placeholder="Remarks" v-model="item.remark"></textarea>
            </td>

            <td>
                <div class="read_block" v-if="TestRead(item)">
                    <select v-model="item.test" disabled v-if="1==0">
                        <option>Choose Assignee for Testing...</option>
                        <option v-for="item in charge" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                    Testing Result is Normal:  <input type="checkbox" :value="item.check_t" :true-value="1" v-model:checked="item.check_t" class="alone" disabled>
                    <textarea rows="3" v-model="item.remark_t" readonly></textarea>
                    <!-- <i>(更新者的名字 at 儲存日期和時間，範例如下)</i> -->
                    <i v-if="item.test_updated_name != ''">({{ item.test_updated_name }} at {{ item.test_updated_at }})</i>

                </div>

                <div class="write_block" v-if="TestWrite(item)">
                    <select v-model="item.test" class="assign_testing" v-if="info_type == 'assing_test' && 1==0">
                        <option>Choose Assignee for Testing...</option>
                        <option v-for="item in charge" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                    <div class="edit_testing_info" v-if="info_type == 'edit_test'">
                        Testing Result is Normal: <input type="checkbox" :value="item.check_t" :true-value="1" v-model:checked="item.check_t" class="alone">
                        <textarea rows="3" v-model="item.remark_t" placeholder="Remarks"></textarea>
                    </div>
                </div>
            </td>

            <td>
                <div class="read_block" v-if="DeliveryRead(item)">
                    <select v-model="item.delivery" disabled v-if="1==0">
                        <option>Choose Assignee for Delivery...</option>
                        <option v-for="item in charge" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                    Delivery is OK: <input type="checkbox" :value="item.check_d" :true-value="1" v-model:checked="item.check_d" class="alone" disabled>
                    <textarea rows="3" v-model="item.remark_d" readonly></textarea>
                    <!-- <i>(更新者的名字 at 儲存日期和時間，範例如下)</i> -->
                    <i v-if="item.delivery_updated_name != ''">({{ item.delivery_updated_name }} at {{ item.delivery_updated_at }})</i>
                </div>

                <div class="write_block" v-if="DeliveryWrite(item)">
                    <select v-model="item.delivery" class="assign_delivery" v-if="info_type == 'assign_delivery' && 1==0">
                        <option>Choose Assignee for Delivery...</option>
                        <option v-for="item in charge" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>

                    <div class="edit_delivery_info" v-if="info_type == 'edit_delivery'">
                        Delivery is OK: <input type="checkbox" :value="item.check_d" :true-value="1" v-model:checked="item.check_d" class="alone">
                        <textarea rows="3" v-model="item.remark_d" placeholder="Remarks"></textarea>
                    </div>
                    
                </div>
            </td>

            <td>
                <div class="read_block" v-if="FinalRead(item)">
                    <textarea rows="3" v-model="item.final" readonly></textarea>
                </div>
                <div class="write_block" v-if="FinalWrite(item)">
                    <textarea rows="3" v-model="item.final" placeholder="Remarks"></textarea>
                </div>

            </td>

            <td>
                <div class="btnbox">
                <i class="fas fa-arrow-alt-circle-up" @click="page_up(index, item.id)" v-if="item.is_edit !== true && info_type == ''"></i>
                    <i class="fas fa-arrow-alt-circle-down" @click="page_down(index, item.id)" v-if="item.is_edit !== true && info_type == ''"></i>
                    <i class="fas fa-edit" @click="editItem(item)" v-if="item.is_edit !== true  && access2 == true && info_type == '' && no_privlege() != true"></i>
                    <i class="fas fa-trash" @click="item_delete(item)" v-if="item.is_edit !== true  && access2 == true && info_type == '' && no_privlege() != true"></i>
                    <i class="fas fa-camera" @click="print_me(item)" v-if="item.is_edit !== true && info_type == ''"></i>
                    <i class="fas fa-times-circle" v-if="item.is_edit == true" @click="cancelItem(item)"></i>
                    <i class="fas fa-check-circle" v-if="item.is_edit == true" @click="confirmItem(item)"></i>
                </div>
            </td>
            </tr>
            </tbody>
            </table>


        </div>

    </div>


    <div class="block B" style="display: none;">


        <div class="box-content">

            <div class="list_function main">

                <!-- buttons to add product -->
                <div class="block">

                    <div class="btn_block">
                        <a class="btn small green" @click="">Approve</a>
                        <a class="btn small" @click="">Reject</a>

                        <input type="text" placeholder="Comment">
                    </div>

                </div>

            </div>


            <table class="tb_order">
                <thead>
                <tr>
                    <th><i class="micons">view_list</i></th>
                    <th>Status</th>
                    <th>Brand</th>
                    <th colspan="2">Description</th>
                    <th>Qty Needed</th>
                    <th>SRP</th>
                    <th>Date Needed by Client</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td><input type="checkbox" class="alone"></td>
                    <td>
                        <div class="read_block">
                            For Approval
                        </div>
                    </td>

                    <td>
                        <div class="read_block">
                            SEEDDESIGN
                        </div>
                    </td>

                    <td class="pic">
                        <div class="read_block">
                            <img src="images/qn_example1.png">
                            <img src="images/qn_example1.png">
                            <img src="images/qn_example1.png">
                        </div>
                    </td>

                    <td>
                        <div class="read_block">
                            <div class="code">FELIIX XL Xcellent Moment Mirage 1P</div>
                            <div class="brief"></div>
                            <div class="listing">Material: Acrylic, Glass, Steel
                                Color: Rose Gold
                                Dimensions: 22.2 H x 20 cm dia.
                                Weight: 0.9kg
                                Wire: 200 cm
                                Light Source: LED 3.2W, 2000K, CRI80
                                Warranty: 5 years
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="read_block">
                            1
                        </div>
                    </td>

                    <td>
                        <div class="read_block">
                            ₱ 11,900.00
                        </div>
                    </td>

                    <td>
                        <div class="read_block">
                            2022/01/01
                        </div>
                    </td>

                    <td>
                        <div class="msg_block">
                            <div class="msgbox">
                                • Workstations (cross shaped cluster) - remove pedestal, will go for laminated
                                option
                                1 ( show available swatches ) , change the size to 1.5x 1.5 ,kindly include cable
                                management ( cable outlets) and vertical pole ( same color with the table legs grey
                                color.
                                <a href="https://storage.cloud.google.com/feliiximg/1648525715_additional table.jpg"
                                   target="_blank" class="attch">additional table.jpg</a>
                                <i class="t">(Stan Fernandez at 2022-03-29 11:48:35)</i>
                                <div class="already_read">Dennis Lin</div>
                                <div class="btnbox">
                                    <a class="btn small green">Got it</a>
                                    <a class="btn small yellow">Delete</a>
                                </div>
                            </div>

                            <div class="msgbox deleted">
                                • Workstations (cross shaped cluster) - remove pedestal, will go for laminated
                                option
                                1 ( show available swatches ) , change the size to 1.5x 1.5 ,kindly include cable
                                management ( cable outlets) and vertical pole ( same color with the table legs grey
                                color.
                                <a href="https://storage.cloud.google.com/feliiximg/1648525715_additional table.jpg"
                                   target="_blank" class="attch">additional table.jpg</a>
                                <i class="t">(Stan
                                    Fernandez at 2022-03-29 11:48:35)</i>
                                <div class="already_read">Dennis Lin</div>
                                <div class="btnbox">
                                    <a class="btn small green">Got it</a>
                                    <a class="btn small yellow">Delete</a>
                                </div>
                            </div>
                        </div>

                        <div class="write_msg_block">

                            <textarea rows="3" name="" id="" placeholder="Leave notes here"></textarea>

                            <div class="file_block">
                                <span>Files: </span>
                                <div class="pub-con">
                                    <div class="input-zone">
                                        <span class="upload-des">choose file</span>
                                        <input type="file" name="file_r" value="" placeholder="choose file"
                                               multiple="multiple" class="input"></div>
                                </div>
                            </div>
                            <div class="file-list"></div>

                            <div class="btnbox">
                                <a class="btn small green">Create</a>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="btnbox">
                            <i class="fas fa-arrow-alt-circle-up"></i>
                            <i class="fas fa-arrow-alt-circle-down"></i>
                            <i class="fas fa-camera"></i>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>


        </div>

    </div>


    <div class="block C" style="display: none;">

    </div>

    <div class="block D" style="display: none;">

    </div>

</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_product_catalog">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

        <div class="modal-content" style="height: calc( 100vh - 3.75rem); overflow-y: auto;">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel">Product Catalog</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="modal_function" style="width: 100%; display: flex; align-items: center;">

                    <div class="left_function" style="width: 90%;">

                        <input type="text" placeholder="ID" v-model="fil_id">

                        <input type="text" placeholder="Code" v-model="fil_code">

                        <select v-model="fil_brand">
                            <option value="">Choose Brand...</option>
                            <option v-for="(item, index) in brands">{{ item.brand }}</option>
                        </select>

                        <br>

                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                data-width="555px" title="Choose Tag(s)..." id="tag01" v-model="fil_tag">

                                <optgroup label="BY INSTALL LOCATION">
                                    <option value="BLDG. FAÇADE">BLDG. FAÇADE</option>
                                    <option value="CABINET">CABINET</option>
                                    <option value="CEILING">CEILING</option>
                                    <option value="FLOOR">FLOOR</option>
                                    <option value="INDOOR">INDOOR</option>
                                    <option value="INGROUND">INGROUND</option>
                                    <option value="OTHER FURNITURES ">OTHER FURNITURES</option>
                                    <option value="OUTDOOR">OUTDOOR</option>
                                    <option value="STREET">STREET</option>
                                    <option value="TABLE">TABLE</option>
                                    <option value="UNDERWATER">UNDERWATER</option>
                                    <option value="WALL">WALL</option>
                                </optgroup>

                                <optgroup label="INSTALL METHOD">
                                    <option value="POLE-MOUNTED">POLE-MOUNTED</option>
                                    <option value="RECESSED">RECESSED</option>
                                    <option value="STAND-ALONE">STAND-ALONE</option>
                                    <option value="SURFACE-MOUNTED">SURFACE-MOUNTED</option>
                                    <option value="SUSPENDED">SUSPENDED</option>
                                </optgroup>

                                <optgroup label="BY TYPE / FUNCTION">
    <option value="ALUMINUM PROFILE">ALUMINUM PROFILE</option>
    <option value="ASSEMBLED">ASSEMBLED</option>
    <option value="AUDIO EQUIPMENT">AUDIO EQUIPMENT</option>
    <option value="BATTEN LIGHT">BATTEN LIGHT</option>
    <option value="BOLLARD">BOLLARD</option>
    <option value="CONTROLLER">CONTROLLER</option>
    <option value="CUSTOMIZED">CUSTOMIZED</option>
    <option value="DIMMABLE">DIMMABLE</option>
    <option value="DIMMER">DIMMER</option>
    <option value="DIRECTIONAL">DIRECTIONAL</option>
    <option value="DISPLAY SPOTLIGHT">DISPLAY SPOTLIGHT</option>
    <option value="DMX">DMX</option>
    <option value="EMERGENCY LIGHT">EMERGENCY LIGHT</option>
    <option value="FLOOD LIGHT">FLOOD LIGHT</option>
    <option value="HIGHBAY LIGHT">HIGHBAY LIGHT</option>
    <option value="LED BULB">LED BULB</option>
    <option value="LED DRIVER">LED DRIVER</option>
    <option value="LED STRIP">LED STRIP</option>
    <option value="LED TUBE">LED TUBE</option>
    <option value="LIGHTBOX">LIGHTBOX</option>
    <option value="LINEAR LIGHT">LINEAR LIGHT</option>
    <option value="MAGNETIC TRACK BAR">MAGNETIC TRACK BAR</option>
    <option value="MAGNETIC TRACK LIGHT">MAGNETIC TRACK LIGHT</option>
    <option value="PANEL LIGHT">PANEL LIGHT</option>
    <option value="PROJECTION LIGHT">PROJECTION LIGHT</option>
    <option value="RECEIVER">RECEIVER</option>
    <option value="SPECIALTY LIGHT">SPECIALTY LIGHT</option>
    <option value="STAGE LIGHT">STAGE LIGHT</option>
    <option value="SWITCH">SWITCH</option>
    <option value="TRACK BAR">TRACK BAR</option>
    <option value="TRACK LIGHT">TRACK LIGHT</option>
    <option value="TROFFER LIGHT">TROFFER LIGHT</option>
    <option value="UV LED">UV LED</option>
    <option value="WALL WASHER">WALL WASHER</option>
</optgroup>

                                <optgroup label="ACCESSORY">
                                    <option value="FUNCTIONAL ACCESSORY">FUNCTIONAL ACCESSORY</option>
                                    <option value="INSTALL ACCESSORY">INSTALL ACCESSORY</option>
                                    <option value="REPLACEMENT PART">REPLACEMENT PART</option>
                                </optgroup>

                        </select>
                        <input type="text" placeholder="Keyword" v-model="fil_k" style="margin-left: 20px;">
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
                                <span v-show="show_ntd === true">CP: {{ item.price_ntd }} <br v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }}<br></span>
                                <span>SRP: {{ item.price }}<br v-if="item.str_price_change"> {{ item.str_price_change ?  item.str_price_change : '' }}<br></span>
                                <span>QP: {{ item.quoted_price }} <br v-if="item.str_quoted_price_change"> {{ item.str_quoted_price_change ? item.str_quoted_price_change : '' }}<br></span>
                            </td>
                            <td>
                                <button id="edit01" @click="btnEditClick(item)"><i aria-hidden="true" 
                                                                                   class="fas fa-caret-right"></i>
                                </button>
                            </td>
                        </tr>


                        </tbody>
                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_product_display">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

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
                        <div class="basic_info"><span class="phasedout" v-if="phased == 1">Phased Out</span><h3 style="word-break: break-all;">{{product.code}}</h3> <h6>
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
                                    <button class="btn btn-info" @click="add_with_image()" :disabled="phased==1">Add with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image()" :disabled="phased==1">Add without Image
                                    </button>
                                </li>
                            </ul>

                            <!--
                            <ul v-if="product.variation_mode == 1">
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image('all')">Add all spec. with
                                        Image
                                    </button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image('all')">Add all spec.
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
                        <div class="basic_info"><span class="phasedout" v-if="phased == 1">Phased Out</span>
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
                                    <button class="btn btn-info" @click="add_with_image()" :disabled="phased==1">Add with Image</button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image()" :disabled="phased==1">Add without Image
                                    </button>
                                </li>
                            </ul>

                            <!--
                            <ul>
                                <li v-if="toggle_type == 'A'">
                                    <button class="btn btn-info" @click="add_with_image('all')">Add all spec. with
                                        Image
                                    </button>
                                </li>
                                <li>
                                    <button class="btn btn-info" @click="add_without_image('all')">Add all spec.
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


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modal_quotation_list">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1200px;">

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
                        <a class="prev" :disabled="product_page_quo == 1" @click="pre_page_quo(); ">Prev
                            10</a>
                        <a class="page" v-for="pg_quo in product_pages_10_quo" @click="product_page_quo=pg_quo; "
                           v-bind:style="[pg_quo == product_page_quo ? { 'background':'#707071', 'color': 'white'} : { }]">{{
                            pg_quo
                            }}</a>
                        <a class="next" :disabled="product_page_quo == product_pages_quo.length"
                           @click="nex_page_quo(); ">Next
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
                                <a :href="'quotation?id=' + item.id" target="_blank">{{
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


</div>


</body>

<script>

    function move_left() {
        document.getElementsByClassName('block C')[0].scrollLeft -= 400;
    };

    function move_right() {
        document.getElementsByClassName('block C')[0].scrollLeft += 400;
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
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/order_taiwan_p3.js"></script>
<script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</html>
