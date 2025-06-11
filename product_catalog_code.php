
<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
	setcookie("userurl", $_SERVER['REQUEST_URI']);
  header( 'location:index' );
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

        if($decoded->data->limited_access == true 
        && !strpos($_SERVER['REQUEST_URI'], 'meeting_calendar')
        && !strpos($_SERVER['REQUEST_URI'], 'product_catalog_code') 
        && !strpos($_SERVER['REQUEST_URI'], 'product_display_code') 
        && !strpos($_SERVER['REQUEST_URI'], 'add_product_code') 
        && !strpos($_SERVER['REQUEST_URI'], 'edit_product_code') 
        && !strpos($_SERVER['REQUEST_URI'], 'product_spec_sheet') 
        && !strpos($_SERVER['REQUEST_URI'], 'tag_mgt')
        && !strpos($_SERVER['REQUEST_URI'], 'default'))
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
    <title>Product Catalog</title>
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

           

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

            $('.list_function .filter').click(function (e) {
                $("#tag01").selectpicker("refresh");
                $('#tag01').selectpicker('val', app.fil_tag);
            })

        })

    </script>

    <style>

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
        }

        body.gray .mainContent{
            padding-top: 100px;
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
            width: 50px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) {
            width: 130px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) {
            width: 380px;
        }

        #tb_product_list tbody tr td:nth-of-type(4) {
            width: 430px;
        }

        #tb_product_list tbody tr td:nth-of-type(5) {
            width: 210px;
        }

        #tb_product_list tbody tr td:nth-of-type(6) {
            width: 150px;
        }

        #tb_product_list tbody tr td:nth-of-type(7) {
            width: 100px;
        }

        #tb_product_list tbody tr td:nth-of-type(2) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr td:nth-of-type(4) ul {
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

        #tb_product_list tbody tr td:nth-of-type(7) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul li span.phasedout{
            background-color: red;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody tr td:nth-of-type(3) ul li span.phasedout_replacement {
            background-color: orange;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
            cursor: pointer;
        }

        #tb_product_list tbody tr td:nth-of-type(4) ul:last-of-type {
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
            vertical-align: top;
        }

        #tb_product_list ul li:nth-of-type(2) span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
        }

        #tb_product_list ul li:nth-of-type(2) span + span{
            margin-left: 5px;
        }

        #tb_product_list tbody td ul li:nth-of-type(2) a {
            color: #007bff;
        }

        #tb_product_list tbody td ul.supporting_attachment {
            margin-top: 2px;
        }

        #tb_product_list tbody td ul.supporting_attachment li:nth-of-type(1) {
            padding: 1px 0;
        }

        #tb_product_list tbody td ul.supporting_attachment span {
            background-color: orange;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            margin: 3px 0px;
            padding: 1px 15px 2px;
            cursor: pointer;
            border: none;
        }

        #tb_product_list tbody td ul.supporting_attachment span + span {
            margin-left: 5px;
        }

        #tb_product_list tbody td ul.supporting_attachment li ~ li {
            padding-left: 5px;
        }

        #tb_product_list tbody td ul.last_order_history button {
            font-size: 14px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-left: 3px;
            padding: 0 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody td span.phasedout_replacement {
            background-color: orange;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
            cursor: pointer;
        }

        #tb_product_list tbody td div.phasedout_variant {
            text-align: left;
            color: red;
            font-size: 16px;
            font-weight: 600;
            padding: 5px 0 0 3px;
        }

        #tb_product_list tbody td div.phasedout_variant button {
            font-size: 14px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-left: 3px;
            padding: 0 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody td div.phasedout_variant button:focus {
            outline-color: transparent;
        }

        #tb_product_list tbody tr.set_format1 td, #tb_product_list tbody tr.set_format2 td {
            background-color: rgba(255,255,0,0.1)!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) {
            width: 50px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) {
            width: 940px!important;
            columns: 2!important;
            padding: 10px 25px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) ul {
            margin-bottom: 0;
            break-inside: avoid-column;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) > div.product_set_desc {
            padding: 1px 7px 1px 5px;
            text-align: left;
            font-weight: 600;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) > div.product_set_desc > div {
            font-weight: 400;
            white-space: pre-line;
            padding-left: 10px;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(3) {
            width: 150px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(4) {
            width: 150px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(5) {
            width: 100px!important;
        }

        #tb_product_list tbody tr.set_format1 > td:nth-of-type(5) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(1) {
            width: 130px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(1) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) {
            width: 380px!important;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) ul {
            margin-bottom: 0;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) ul li span.phasedout {
            background-color: red;
            color: white;
            padding: 0px 5px 3px;
            border-radius: 10px;
        }

        #tb_product_list tbody tr.set_format2 > td:nth-of-type(3) {
            width: 430px!important;
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

        button.btn.dropdown-toggle {
            background-color: white;
            border: 1px solid #999;
            border-radius: 0;
        }

        button.btn_switch{
            position: fixed;
            right: 10px;
            top: 10px;
            width: 50px;
            height: 50px;
            border: 1px solid rgb(153,153,153);
            border-radius: 25px;
            font-size: 15px;
            font-weight: 700;
            background-color: rgba(7, 220, 237, 0.8);
            z-index: 999;
        }

        ul.dropdown-menu.inner li {
            display: block;
            border-right: none;
        }

        .dropdown-menu > .bs-searchbox > input[type='search'] {
            border: 1px solid #ced4da;
        }

        @media screen and (max-width: 640px) {
            .mainContent > div.block:nth-of-type(1) > .list_function  {
                padding-top: 110px;
            }

            #tb_product_list tbody tr td:nth-of-type(1) {
                min-width: 50px;
            }

            #tb_product_list tbody tr td:nth-of-type(2) {
                min-width: 130px;
            }

            #tb_product_list tbody tr td:nth-of-type(3) {
                min-width: 380px;
            }

            #tb_product_list tbody tr td:nth-of-type(4) {
                min-width: 430px;
            }

            #tb_product_list tbody tr td:nth-of-type(5) {
                min-width: 210px;
            }

            #tb_product_list tbody tr td:nth-of-type(6) {
                min-width: 150px;
            }

            #tb_product_list tbody tr td:nth-of-type(7) {
                min-width: 100px;
            }

            #tb_product_list tbody tr.set_format1 > td:nth-of-type(1) {
                min-width: 50px;
            }

            #tb_product_list tbody tr.set_format1 > td:nth-of-type(2) {
                min-width: 940px;
            }

            #tb_product_list tbody tr.set_format1 > td:nth-of-type(3) {
                min-width: 150px;
            }

            #tb_product_list tbody tr.set_format1 > td:nth-of-type(4) {
                min-width: 150px;
            }

            #tb_product_list tbody tr.set_format1 > td:nth-of-type(5) {
                min-width: 100px;
            }

            #tb_product_list tbody tr.set_format2 > td:nth-of-type(1) {
                min-width: 130px;
            }

            #tb_product_list tbody tr.set_format2 > td:nth-of-type(2) {
                min-width: 380px;
            }

            #tb_product_list tbody tr.set_format2 > td:nth-of-type(3) {
                min-width: 430px;
            }

            #tb_product_list tbody tr.set_format2 > td:nth-of-type(4) {
                min-width: 220px;
            }
        }


    </style>

</head>

<body class="gray">

<div id="app" class="bodybox">
    <div class="mask" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->

    <button @click="toggle_price()" class="btn_switch" v-show="show_ntd === true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="bi bi-toggles"><path d="M4.5 9a3.5 3.5 0 1 0 0 7h7a3.5 3.5 0 1 0 0-7h-7zm7 6a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm-7-14a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm2.45 0A3.49 3.49 0 0 1 8 3.5 3.49 3.49 0 0 1 6.95 6h4.55a2.5 2.5 0 0 0 0-5H6.95zM4.5 0h7a3.5 3.5 0 1 1 0 7h-7a3.5 3.5 0 1 1 0-7z"></path></svg></button>

    <div class="mainContent">

        <div class="block">
            <div class="list_function">

                <!-- 點擊後跳轉去建立產品頁面 -->
                <div class="new_function">
                    <a class="add" href="add_product_code"></a>
                </div>

                <!-- 篩選功能 -->
                <div class="new_function">
                    <a class="filter" id="btn_filter"></a>
                    <div id="filter_dialog" class="dialog A"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <!-- <dt>ID</dt>
                                <dd><input type="text" v-model="fil_id"></dd> -->

                                <dt style="margin-bottom: -15px;">ID</dt>
<div class="half"><dt>From</dt> <dd><input type="number" min="1" step="1" v-model="fil_id"></dd></div>
<div class="half"><dt>To</dt> <dd><input type="number" min="1" step="1" v-model="fil_id_1"></dd></div>

                                <dt>Code</dt>
                                <dd><input type="text" v-model="fil_code"></dd>

                                <dt>Category</dt>
                                <dd>
                                <select v-model="fil_category">
                                <option></option>
                                <option value="10000000">Lighting</option>
                                <option value="20000000">Systems Furniture</option>
                                <option value="20010000">Systems Furniture >> Cabinet</option>
                                <option value="20020000">Systems Furniture >> Chair</option>
                                <option value="20030000">Systems Furniture >> Table</option>
                                <option value="20040000">Systems Furniture >> Workstation</option>
                                <option value="20050000">Systems Furniture >> Partition</option>
                                </select>
                                </dd>

                                <dt>Tag</dt>
                                <dd>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="No tag selected" id="tag01" v-model="fil_tag">
                                            <optgroup v-for="(group, index) in tag_group" :label="group.group">
            <option v-for="(it, index2) in group.items" :value="it.item_name">{{ it.item_name }}</option>
        </optgroup>
                                    </select>
                                </dd>

                                <dt>Brand</dt>
                                <dd>
                                    <select v-model="fil_brand">
                                        <option value="">
                                        <option v-for="(item, index) in brands">{{ item.brand }}</option>
                                    </select>
                                </dd>

                                <dt>Keyword (only for description and notes)</dt>
                                <dd><input type="text" v-model="fil_keyword"></dd>
<!--
                                <div class="half">
                                    <dt></dt>
                                    <dd><input type="text"></dd>
                                </div>
    -->
                                <!--
                                <div class="half">
                                    <dt>Category</dt>
                                    <dd>
                                        <select>
                                            <option value="0">
                                            <option>Lighting</option>
                                            <option>Systems Furniture</option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select>
                                            <option value="0">
                                            <option>Indoor</option>
                                            <option>Outdoor</option>
                                            <option>Accessory</option>
                                            <option>Cabinet</option>
                                            <option>Chair</option>
                                            <option>Table</option>
                                            <option>Workstation</option>
                                            <option>Partition</option>
                                        </select>
                                    </dd>
                                </div>

                                <dt style="margin: 20px 0 -18px;">Price</dt>
                                <div class="half">
                                    <dt>min</dt>
                                    <dd><input type="number" v-model="fil_amount_lower"></dd>
                                </div>

                                <div class="half">
                                    <dt>max</dt>
                                    <dd><input type="number" v-model="fil_amount_upper"></dd>
                                </div>
                                -->

                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="filter_clear">Cancel</a>
                                <a class="btn small" @click="filter_remove">Clear</a>
                                <a class="btn small green" @click="filter_apply">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 排序功能 -->
                <div class="new_function">
                    <a class="sort" id="btn_sort"></a>
                    <div id="sort_dialog" class="dialog B"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_factor1">
                                            <option value="0"></option>
                                            <option value="1">
                                                ID
                                            </option>
                                            <option value="2">
                                                Created Time
                                            </option>
                                            <option value="3">
                                                Updated Time
                                            </option>
                                            <option value="4">
                                                SRP
                                            </option>
                                            <option value="5">
                                                QP
                                            </option>
                                          
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_factor1_order">
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
                                        <select v-model="od_factor2">
                                            <option value="0"></option>
                                            <option value="1">
                                                ID
                                            </option>
                                            <option value="2">
                                                Created Time
                                            </option>
                                            <option value="3">
                                                Updated Time
                                            </option>
                                            <option value="4">
                                                SRP
                                            </option>
                                            <option value="5">
                                                QP
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_factor2_order">
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
                            <div class="btnbox"><a class="btn small" @click="order_clear">Cancel</a><a class="btn small" @click="order_remove">Clear</a><a class="btn small green" @click="filter_apply">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 分頁功能 -->
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
                </div>
            </div>
        </div>


        <div style="overflow-x: auto;">
            <table id="tb_product_list" class="table  table-sm table-bordered" >

                <thead>
                <tr>

                    <th><input type="checkbox" class="alone"></th>

                    <th>Image</th>

                    <th>Information</th>

                    <th>Specification</th>

                    <th>Price</th>

                    <th>Inventory Status</th>

                    <th>Action</th>

                </tr>

                </thead>

                <tbody>

                



                <!-- 非 Product Set 子類別的產品，套用以下格式輸出到頁面上 -->
                <template v-for="(item, index) in displayedPosts">
                    <!-- Product Set 子類別的產品，套用以下格式輸出到頁面上 -->
                    <!-- set_format1 會套用在 Product Set 產品的主敘述，set_format2 會套用在 Product Set 產品的 Product1, Product 2, Product 3 -->
                    <tr class="set_format1" v-if="item.sub_category == '10020000'">
                        <!-- 如果這個 Product Set 產品有 Product1 和 Product 2，則 rowspan=3；如果這個 Product Set 產品有 Product1 和 Product 2 和 Product 3，則 rowspan=4 -->
                        <td :rowspan="item.product_set_cnt + 1">
                            <input type="checkbox" class="alone">
                        </td>
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
                                <a target="_blank" :href="'product_display_code?id='+item.id">{{ item.code }}</a>
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

                            <!-- 如果當前產品是 Lighting 產品，而且該產品有上傳 IES 檔案，則下面的結構中就要把 IES 的 span 建立出來，如果該產品有 上傳 Supporting File 檔案，則下面的結構中就要把 Supporting File 的 span 建立出來
                                 如果當前產品是 Office 產品，而且該產品有上傳 SketchUp 檔案，則下面的結構中就要把 SketchUp 的 span 建立出來，如果該產品有 上傳 Supporting File 檔案，則下面的結構中就要把 Supporting File 的 span 建立出來 -->
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
                            <span v-show="((cost_lighting == true && item.category == 'Lighting') || (cost_furniture == true && item.category == 'Systems Furniture')) && toggle == true">CP: {{ item.price_ntd }} <br v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }} <br></span>
                            <span>SRP: {{ item.price }}<br></span>
                            <span>QP: {{ item.quoted_price }}<br></span>
                        </td>

                        <td></td>

                        <!-- 如果這個 Product Set 產品有 Product1 和 Product 2，則 rowspan=3；如果這個 Product Set 產品有 Product1 和 Product 2 和 Product 3，則 rowspan=4 -->
                        <td :rowspan="item.product_set_cnt + 1">
                            <button id="edit01" @click="btnEditClick(item.id)" v-if="product_edit"><i class="fas fa-edit"></i>
                            </button>

                            <button id="copy01" @click="btnDuplicateClick(item.id)" v-if="product_duplicate"><i class="fas fa-copy"></i></button>

                            <button @click="btnDelClick(item.id)" v-if="product_delete"><i class="fas fa-times"></i></button>

                        </td>
                    </tr>

                    <!-- set_format1 會套用在 Product Set 產品的主敘述，set_format2 會套用在 Product Set 產品的 Product1, Product 2, Product 3 -->
                    <tr class="set_format2" v-for="(set, index) in item.product_set">
                        <td>
                            <a target="_blank" :href="'product_display_code?id='+set.id"><img :src="baseURL + set.photo1" v-if="set.photo1"></a>
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
                                <a target="_blank" :href="'product_display_code?id='+set.id">{{ set.code }}</a>
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

                            <!-- 如果當前產品是 Lighting 產品，而且該產品有上傳 IES 檔案，則下面的結構中就要把 IES 的 span 建立出來，如果該產品有 上傳 Supporting File 檔案，則下面的結構中就要把 Supporting File 的 span 建立出來
                                 如果當前產品是 Office 產品，而且該產品有上傳 SketchUp 檔案，則下面的結構中就要把 SketchUp 的 span 建立出來，如果該產品有 上傳 Supporting File 檔案，則下面的結構中就要把 Supporting File 的 span 建立出來 -->
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



                    <tr v-if="item.sub_category != '10020000'">
                        <td>
                            <input type="checkbox" class="alone">
                        </td>
                        <td>
                            <a target="_blank" :href="'product_display_code?id='+item.id"><img :src="baseURL + item.photo1" v-if="item.photo1"></a>
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
                                <li>
                                <a target="_blank" :href="'product_display_code?id='+item.id">{{ item.code }}</a>
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
                            <span v-show="((cost_lighting == true && item.category == 'Lighting') || (cost_furniture == true && item.category == 'Systems Furniture')) && toggle == true">CP: {{ item.price_ntd }} <br v-if="item.str_price_ntd_change"> {{ item.str_price_ntd_change ?  item.str_price_ntd_change : '' }} <br></span>
                            <span>SRP: {{ item.price }} <br v-if="item.str_price_change"> {{ item.str_price_change ?  item.str_price_change : '' }} <br></span>
                            <span>QP: {{ item.quoted_price }} <br v-if="item.str_quoted_price_change"> {{ item.str_quoted_price_change ? item.str_quoted_price_change : '' }} <br></span>
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
                            <button id="edit01" @click="btnEditClick(item.id)" v-if="product_edit && item.status != -1"><i class="fas fa-edit"></i>
                            </button>

                            <button id="copy01" @click="btnDuplicateClick(item.id)" v-if="product_duplicate && item.status != -1"><i class="fas fa-copy"></i></button>

                            <button @click="btnDelClick(item.id)" v-if="product_delete && item.status != -1"><i class="fas fa-times"></i></button>

                        </td>
                    </tr>
                </template>

                </tbody>

            </table>
        </div>


        <div class="block">
            <div class="list_function">
                <!-- 分頁功能 -->
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
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
</script>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script defer src="js/product_calatog_code.js"></script>

</html>
