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
    <title>Work Schedule Eng.</title>
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

        .pagebox .title_box ul li:nth-of-type(1) > input[type="text"]:nth-of-type(1) {
            margin-left: 5px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 65px;
            height: 30px;
        }

        .pagebox .title_box ul li:nth-of-type(1) > input[type="text"]:nth-of-type(2) {
            margin-left: 10px;
            border: 1px solid #707070;
            font-size: 14px;
            width: 350px;
            height: 30px;
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

        .pagebox .content_box ul li:nth-of-type(1) span {
            width: 60px;
            display: inline-block;
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

        #page_dialog {
            min-width: 1000px;
            pointer-events: auto;
            zoom: 85%;
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

        #page_dialog .page_form {
            max-height: 400px;
            overflow-y: auto;
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

        .list_function.main a.print {
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

        .list_function.main a.print:hover {
            background-color: #707071;
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

        div.box_timeline_manpower {
            width: 100%;
            padding: 5px 0 20px;
            overflow-x: auto;
        }

        #tb_timeline {
            margin-bottom: 20px;
        }

        #tb_timeline thead tr th {
            border: 1px solid black;
            background: white;
            text-align: center;
            padding: 5px 15px;
            min-width: 95px;
            max-width: 95px;
        }

        #tb_timeline thead tr th:nth-of-type(1) {
            min-width: 540px;
            max-width: 540px;
        }

        #tb_timeline thead tr th:nth-of-type(2) {
            min-width: 120px;
            max-width: 120px;
        }

        #tb_timeline tbody tr td {
            border: 1px solid black;
            background: white;
            text-align: center;
        }

        #tb_timeline tbody tr.item_row td:nth-of-type(1) {
            text-align: right;
            min-width: 100px;
            max-width: 100px;
            font-weight: 700;
            padding: 5px;
        }

        #tb_timeline tbody tr.item_row td:nth-of-type(2) {
            text-align: left;
            padding: 5px;
        }

        #tb_timeline tbody tr.sub_item_row td:nth-of-type(2) {
            min-width: 40px;
            max-width: 40px;
            padding: 5px;
        }

        #tb_timeline tbody tr.sub_item_row td:nth-of-type(3) {
            min-width: 400px;
            max-width: 400px;
            text-align: left;
            padding: 5px;
        }

        #tb_timeline td select {
            border: none;
            width: 100%;
        }

        #tb_timeline td select option {
            background: white;
            color: white;
        }

        #tb_timeline td select:has(option[value="1"]:checked), #tb_timeline td select option.color1 {
            background: #FFD965;
            color: white;
        }

        #tb_timeline td select:has(option[value="2"]:checked), #tb_timeline td select option.color2 {
            background: #ED7D31;
            color: white;
        }

        #tb_timeline td select:has(option[value="3"]:checked), #tb_timeline td select option.color3 {
            background: #2E75B5;
            color: white;
        }

        #tb_timeline td select:has(option[value="4"]:checked), #tb_timeline td select option.color4 {
            background: #00B050;
            color: white;
        }

        #tb_timeline td select:has(option[value="5"]:checked), #tb_timeline td select option.color5 {
            background: #FF0000;
            color: white;
        }

        #tb_manpower {
            margin-left: 100px;
        }

        #tb_manpower tbody tr td {
            border: 1px solid black;
            background: white;
            text-align: center;
            min-width: 95px;
            max-width: 95px;
        }

        #tb_manpower tbody tr td:nth-of-type(1) {
            min-width: 40px;
            max-width: 40px;
            padding: 5px;
        }

        #tb_manpower tbody tr td:nth-of-type(2) {
            min-width: 520px;
            max-width: 520px;
            padding: 5px;
        }

        #tb_manpower tbody tr td input[type="number"] {
            border: none;
            width: 100%;
            text-align: center;
            font-weight: 400;
        }

        #tb_manpower tfoot tr.first_line td {
            border: none;
            background: white;
            height: 35px;
        }

        #tb_manpower tfoot tr.second_line td {
            border: 1px solid black;
            background: white;
            text-align: center;
            min-width: 95px;
            max-width: 95px;
        }

        #tb_manpower tfoot tr.second_line td:nth-of-type(1) {
            min-width: 560px;
            max-width: 560px;
            font-weight: 700;
            padding: 5px;
        }

        div.box_weekly_cost {
            width: 100%;
        }

        table.tb_weekly_cost {
            margin-top: 30px;
        }

        table.tb_weekly_cost tbody tr td {
            border: 1px solid black;
            background: white;
            text-align: center;
            width: 95px;
        }

        table.tb_weekly_cost tbody tr.title td {
            font-weight: 700;
        }

        table.tb_weekly_cost tbody tr td:nth-of-type(1) {
            width: 150px;
        }

        table.tb_total_cost {
            margin-top: 30px;
        }

        table.tb_total_cost tbody tr td {
            border: 1px solid black;
            background: white;
            text-align: center;
            width: 120px;
        }

        table.tb_total_cost tbody tr.title td {
            font-weight: 700;
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
                padding: 5px 0 0 15px !important;
                background-color: #FFF !important;
            }

            div.box_timeline_manpower {
            }

            .noPrint {
                display: none;
            }

            #tb_timeline tbody tr.sub_item_row td select {
                background-image: none;
            }
        }

        @page {
            size: A4 landscape;
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
                    <a id="" class="print" title="Export Work Schedule into Picture" @click="print_me()"></a>
                </div>

            </div>

            <div class="block fn" id="menu">

                <div class="popupblock">

                    <?php
                    if ($test_manager[0]  == "1")
                    {
                    ?>
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_day_and_rate = !show_day_and_rate">Days and Rates</a>
                    <?php
                    } else {
                    ?>
                    <a>Days and Rates</a>
                    <?php
                    }
                    ?>
                    <div id="total_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_day_and_rate">
                        <h6>Days and Rates</h6>

                        <div class="formbox">
                            <dl>
                                <dt class="head">How many days will appear on the timeline?</dt>
                                <dd>
                                    <input type="number" min="0" max="84" step="1" v-model="temp_period">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Rate of Leadman:</dt>
                                <dd>
                                    <input type="number" min="0" step="1" v-model="temp_rate_leadman">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Rate of Sr. Technician:</dt>
                                <dd>
                                    <input type="number" min="0" step="1" v-model="temp_rate_sr_technician">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Rate of Technician:</dt>
                                <dd>
                                    <input type="number" min="0" step="1" v-model="temp_rate_technician">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Rate of Oursource (Electrician):</dt>
                                <dd>
                                    <input type="number" min="0" step="1" v-model="temp_rate_electrician">
                                </dd>
                            </dl>

                            <dl>
                                <dt class="head">Rate of Oursource (Helper):</dt>
                                <dd>
                                    <input type="number" min="0" step="1" v-model="temp_rate_helper">
                                </dd>
                            </dl>

                            <div class="btnbox">
                                <a class="btn small" @click="close_all()" v-if="submit == false">Close</a>
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
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'" @click="show_item_list = !show_item_list">Item List</a>
                    <?php
                    } else {
                    ?>
                    <a>Item List</a>
                    <?php
                    }
                    ?>
                    <div id="page_dialog" class="dialog fn2 show" :ref="'dlg_fn2'" v-show="show_item_list">
                        <h6>Item List
                            <a class="add_page" @click="add_page()" title="Add New Item"></a>
                        </h6>

                        <div class="page_form">

                            <div class="pagebox" v-for="(page, page_index) in temp_item_list">

                                <div class="title_box">
                                    <ul>
                                        <li>Item <input type="text" placeholder="Legend" v-model="page.legend"> <input type="text" placeholder="Name of Item" v-model="page.name"></li>
                                        <li><i class="fas fa-arrow-alt-circle-up"
                                               @click="page_up(page_index, page.id)"></i>
                                            <i class="fas fa-arrow-alt-circle-down"
                                               @click="page_down(page_index, page.id)"></i>
                                            <i class="fas fa-trash-alt" @click="page_del(page.id)"></i>
                                        </li>
                                    </ul>
                                </div>
                                <div class="function_box">
                                    <a class="btn small green" @click="add_item(page.id)">Add Sub Item</a>
                                </div>
                                <div class="content_box">

                                    <ul v-for="(block, block_index) in page.types">
                                        <li>
                                            Sub Item<br>
                                            <span>Legend:</span> <input type="text" v-model="block.legend"><br>
                                            <span>Name:</span> <input type="text" v-model="block.name">
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
                                            <i class="fas fa-copy" @click="page_copy(page.id, block.id)"></i>
                                            <i class="fas fa-trash-alt" @click="del_block(page.id, block.id)"></i>
                                        </li>
                                    </ul>


                                </div>

                            </div>

                        </div>

                        <div class="formbox">
                            <div class="btnbox">
                                <a class="btn small" @click="close_all()"  v-if="submit == false">Close</a>
                                <a class="btn small green" @click="page_save_pre()">{{ submit == false ? 'Save' : 'Checking and Saving...' }}</a>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>


    <!-- Function Bar end-->

    <div class="mainContent" style="overflow-x: auto;">

        <div class="box_timeline_manpower" v-show="show_gantt">
            <h5>Work Schedule</h5>

            <table id="tb_timeline">
                <thead>
                <tr>
                    <th colspan="3"></th>
                    <th>Duration in days</th>
                    <th v-for="it in period">Day {{ it }}</th>
                </tr>
                </thead>


                <tbody>
                    <template v-for="(item, index) in item_list">
                        <tr class="item_row">
                            <td>{{item.legend}}</td>
                            <td colspan="2">{{item.name}}</td>
                            <td :colspan="period+1"></td>
                        </tr>

                        <tr class="sub_item_row" v-for="(it, sub_index) in item.types" v-if="item.types.length > 0">
                            <td></td>
                            <td>{{it.legend}}</td>
                            <td>{{it.name}}</td>
                            <td>{{ it.days.filter(function(it){ return it != ''; }).length}}</td>
                            <td v-for="(day, day_index) in it.days" v-if="it.days.length > 0">
                                <select v-model="it.days[day_index]">
                                    <option></option>
                                    <option class="color1" value="1"></option>
                                    <option class="color2" value="2"></option>
                                    <option class="color3" value="3"></option>
                                    <option class="color4" value="4"></option>
                                    <option class="color5" value="5"></option>
                                </select>
                            </td>
                        
                        </tr>
                    </template>
                </tbody>

            </table>


            <h5>Manpower</h5>

            <table id="tb_manpower">
                <tbody>
                <tr>
                    <td>a</td>
                    <td>Project-in-Charge</td>
                    <td v-for="(it, index) in man_power1"><input type="number" min="0" v-model="man_power1[index]" @change="sum_man_power()"></td>
                </tr>

                <tr>
                    <td>b</td>
                    <td>Leadman</td>
                    <td v-for="(it, index) in man_power2"><input type="number" min="0" v-model="man_power2[index]" @change="sum_man_power()"></td>
                </tr>

                <tr>
                    <td>c</td>
                    <td>Sr. Technician</td>
                    <td v-for="(it, index) in man_power3"><input type="number" min="0" v-model="man_power3[index]" @change="sum_man_power()"></td>
                </tr>

                <tr>
                    <td>d</td>
                    <td>Technician</td>
                    <td v-for="(it, index) in man_power4"><input type="number" min="0" v-model="man_power4[index]" @change="sum_man_power()"></td>
                </tr>

                <tr>
                    <td>e</td>
                    <td>Oursource (Electrician)</td>
                    <td v-for="(it, index) in man_power5"><input type="number" min="0" v-model="man_power5[index]" @change="sum_man_power()"></td>
                </tr>

                <tr>
                    <td>f</td>
                    <td>Oursource (Helper)</td>
                    <td v-for="(it, index) in man_power6"><input type="number" min="0" v-model="man_power6[index]" @change="sum_man_power()"></td>
                </tr>
                </tbody>


                <tfoot>
                <tr class="first_line">
                    <td colspan="2"></td>
                    <td v-for="it in period"></td>
                </tr>

                <tr class="second_line">
                    <td colspan="2">Total Manpower Per Day</td>
                    <td v-for="(it, index) in man_power_sum">{{man_power_sum[index]}}</td>
                </tr>
                </tfoot>

            </table>
        </div>



        <div class="btnbox noPrint" style="text-align: left; padding: 20px 0 0;" v-show="show_gantt">
            <a class="btn" style="color: white;" @click="reset()">Reset</a>
            <a class="btn green" style="color: white;" @click="apply()">Save</a>
        </div>
        

        <div class="box_weekly_cost" v-show="man_power_weekly.length > 0">

            <table class="tb_weekly_cost" v-for="(item, index) in man_power_weekly">
                <tbody>
                <tr class="title">
                    <td colspan="6">Week {{index + 1}}</td>
                </tr>

                <tr>
                    <td></td>
                    <td>b</td>
                    <td>c</td>
                    <td>d</td>
                    <td>e</td>
                    <td>f</td>
                </tr>

                <tr>
                    <td>No. of ppl.</td>
                    <td>{{ item.man_power2 }}</td>
                    <td>{{ item.man_power3 }}</td>
                    <td>{{ item.man_power4 }}</td>
                    <td>{{ item.man_power5 }}</td>
                    <td>{{ item.man_power6 }}</td>
                </tr>

                <tr>
                    <td>Rate</td>
                    <td>{{ rate_leadman }}</td>
                    <td>{{ rate_sr_technician }}</td>
                    <td>{{ rate_technician }}</td>
                    <td>{{ rate_electrician }}</td>
                    <td>{{ rate_helper }}</td>
                </tr>

                <tr>
                    <td>Subtotal</td>
                    <td>{{ item.man_power2 * rate_leadman }}</td>
                    <td>{{ item.man_power3 * rate_sr_technician }}</td>
                    <td>{{ item.man_power4 * rate_technician }}</td>
                    <td>{{ item.man_power5 * rate_electrician }}</td>
                    <td>{{ item.man_power6 * rate_helper }}</td>
                </tr>

                <tr>
                    <td>Total per Week</td>
                    <td colspan="5">{{ item.man_power2 * rate_leadman + item.man_power3 * rate_sr_technician + item.man_power4 * rate_technician + item.man_power5 * rate_electrician + item.man_power6 * rate_helper}}</td>
                </tr>
                </tbody>
            </table>

            <table class="tb_total_cost">
                <tbody>
                <tr class="title">
                    <td colspan="3">Total Man Power Fee</td>
                </tr>

                <tr>
                    <td>Total</td>
                    <td>x2</td>
                    <td>x3</td>
                </tr>

                <tr>
                    <td>{{ Math.floor(sum_man_power_weekly).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    <td>{{ Math.floor(sum_man_power_weekly * 2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    <td>{{ Math.floor(sum_man_power_weekly * 3).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                </tr>
                </tbody>
            </table>

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
<script defer src="js/work_schedule_eng.js"></script>
<script src="js/canvas2image/canvas2image.js"></script>
<script defer src="js/html2canvas/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</html>