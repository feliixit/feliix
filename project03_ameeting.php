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
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $GLOBALS['username'] = $decoded->data->username;
    $GLOBALS['position'] = $decoded->data->position;
    $GLOBALS['department'] = $decoded->data->department;

    $test_manager = $decoded->data->test_manager;
    $user_id = $decoded->data->id;

    if($decoded->data->limited_access == true)
                header( 'location:index' );

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    $sid = (isset($_GET['sid']) ?  $_GET['sid'] : 0);
    if ($sid < 1 || !is_numeric($sid)) {
        header('location:project01');
    }

    $is_creator = IsCreator($sid, $user_id);

    if ($test_manager[2] == "0" && $is_creator == "1")
        $test_manager[2] = "1";
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header('location:index');
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
    <title>Project Management</title>
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
    <link rel="stylesheet" type="text/css" href="js/fancyBox/jquery.fancybox.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" type="text/css" href="css/vue-select.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">


    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.1/main.min.css'>
    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.0/main.min.css'>
    <script src='js/@fullcalendar/core@4.3.1/main.min.js'></script>
    <script src='js/@fullcalendar/daygrid@4.3.0/main.min.js'></script>

    <script src="js/moment.js"></script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <script type="text/javascript" src="js/fancyBox/jquery.fancybox.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <script>
        $(function () {
            $('header').load('include/header.php');
            //
        <?php
            if ($test_manager[2] == "1") {
        ?>
                dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
                dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
        <?php
            }
        ?>
            dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
            dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
            dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
            dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
            dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function () {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function () {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>

    <style>
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

        .tablebox .dialog {
            top: -20px;
        }


        .tablebox .dialog::before,
        .tablebox .dialog::after {
            top: 15px;
        }

        body.fourth .mainContent > .block .tablebox.lv3c {
            margin: 15px auto;
        }


        div.tablebox.lv3a a.attch {
            color: var(--fth05);
            transition: .3s;
            margin: 0 15px 0 0;
            font-size: 13px;
        }

        div.tablebox.lv3a a.attch:hover {
            color: var(--fth01);
        }

        div.tablebox.lv3a a.attch::before {
            content: '';
            width: 8px;
            height: 8px;
            display: inline-block;
            margin-right: 3px;
            background-color: var(--fth05);
            border-radius: 8px;
            vertical-align: baseline;
            transition: .3s;
        }

        div.tablebox.lv3a a.attch:hover::before {
            background-color: var(--fth01);
        }

        .mainContent {
            min-height: 150vh;
        }

        .list_function.main a.calendar.red {
            background-image: url(images/ui/btn_calendar_red.svg);
        }

        #tasks {
            border: 5px solid #00811e;
            padding: 10px 20px 20px;
            width: 1000px;
            margin: auto;
            position: absolute;
            top: 30px;
            left: 0;
            right: 0;
            z-index: 100;
            background-color: #fff;
        }

        .list_function.main {
            padding-top: 7px;
        }

        div.popupblock, div.namebox {
            margin-top: 3px;
        }

        div.namebox > span {
            display: inline-block;
            font-size: 18px;
            padding: 3px 20px;
            vertical-align: middle;
            border-radius: 20px;
            font-weight: 600;
            background-color: #E2E2E2;
        }

        .section {
            margin: 50px 10px 10px;
            padding: 20px 15px 15px;
            border: 2px solid #14456c;
            border-radius: 10px;
            position: relative;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .section:nth-of-type(1) {
            margin-top: 35px;
        }

        .section span.heading {
            display: inline-block;
            position: absolute;
            top: -17px;
            background-color: white;
            padding: 0 10px;
            font-size: 20px;
            font-weight: 500;
        }

        .section ul {
            margin: 0;
            border-bottom: 1px solid #D2D2D2;
            background-color: rgba(228, 241, 246, 0.7);
            display: flex;
            align-items: center;
            min-height: 58px;
        }

        .section ul li {
            display: table-cell;
            text-decoration: none;
            padding: 10px;
        }

        .section ul li:first-of-type {
            width: 20vw;
            min-width: 220px;
            text-align: center;
            flex-grow: 0;
            flex-shrink: 0;
            font-weight: 500;
        }

        .section.checkbox_ahead ul li:first-of-type {
            text-align: left;
            padding-left: 30px;
        }

        .section ul li:nth-of-type(2) {
            flex-grow: 1;
            flex-shrink: 1;
        }

        .section .form-control:not([type='checkbox']) {
            background-color: #fff;
            border: 1px solid #ced4da;
        }

        .section .form-control:disabled, .form-control[readonly] {
            background-color: #e9ecef;
        }

        .section input.alone[type=checkbox]::before {
            color: rgb(118, 118, 118);
            font-size: 25px;
        }

        .section input.alone[type=checkbox]:checked::before {
            color: rgb(0, 117, 255);
        }

        .section select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
        }

        .section .checkbox_label {
            display: flex;
            align-items: center;
        }

        .section .checkbox_label > :first-child {
            margin-right: 5px;
        }

        .section .input-zone {
            border-width: 1px;
            padding: 2px;
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

        .file-selector {
            margin: 8px 5px 10px 0;
            background-size: 2.13rem;
            border-radius: 0.38rem;
            border: 1px solid rgba(112, 112, 112, 1);
            position: relative;
            color: var(--fth04);
            font-size: 0.88rem;
            box-sizing: border-box;
            padding: 2px;
            width: 73px;
        }

        .file-selector > input[type="file"] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
            cursor: pointer;
        }

        .section .file-element {
            margin-left: 0;
        }
    </style>

    <style>
        .meetingform-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .meetingform-buttons a {
            margin: 0 20px;
            width: 80px;
            text-align: center;
        }

        .meetingform-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .meetingform-item label {
            color: #00811e;
            font-size: 14px;
            font-weight: 500;
            width: 100px;
        }

        .meetingform-item input,
        .meetingform-item select,
        .meetingform-item textarea {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
        }

        .meetingform-item input:disabled,
        .meetingform-item select:disabled,
        .meetingform-item textarea:disabled {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
            opacity: 1;
        }

        #meeting input {
            width: 160px;
            margin-right: 10px;
            height: 35px;
        }

        #addmeeting-form input[type="text"],
        #editmeeting-form input[type="text"],
        #addmeeting-form input[type="file"],
        #editmeeting-form input[type="file"] {
            width: 500px;
        }

        #meeting fieldset {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px 30px;
        }

        #meeting legend {
            margin-left: 10px;
            font-size: 24px;
            padding: 0 5px;
        }

        #meeting {
            border: 5px solid #00811e;
            padding: 10px 20px 20px;
            width: 1000px;
            margin: auto;
            position: absolute;
            top: 30px;
            left: 0;
            right: 0;
            z-index: 100;
            background-color: #fff;
        }

        .file-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .file-element {
            margin-bottom: 5px;
            margin-left: 105px;
        }

        .file-element input[type="checkbox"] + label::before {
            color: #007bff;
            font-size: 20px;
        }

        .file-element input[type="checkbox"]:disabled + label::before {
            color: rgba(127, 189, 255, 0.8);
        }

        .file-element a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .file-element a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .fc-daygrid-event {
            white-space: initial !important;
        }

        .fc-event-title {
            display: inline !important;
        }

        .fc-day-grid-event .fc-content {
            white-space: inherit;
        }

        .fc-button-group > .fc-button {
            font-size: 14px;
        }
    </style>

    <style scoped>
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
            display: inline-block;
        }

        .input-zone {
            margin: 0 5px 5px 0;
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
            margin-top: -10px;
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

        #vs1__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs1__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs1__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        #vs2__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs2__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs2__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        #vs3__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs3__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs3__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        #vs4__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs4__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs4__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        #vs5__combobox,
        #vs6__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs5__listbox,
        #vs6__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs5__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        #vs6__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        .swal2-popup.swal2-toast {
            flex-direction: row;
            align-items: center;
            width: auto;
            padding: .625em;
            overflow-y: hidden;
            background: #fff;
            box-shadow: 0 0 .625em #d9d9d9
        }

        .swal2-popup.swal2-toast .swal2-header {
            flex-direction: row;
            padding: 0
        }

        .swal2-popup.swal2-toast .swal2-title {
            flex-grow: 1;
            justify-content: flex-start;
            margin: 0 .6em;
            font-size: 1em
        }

        .swal2-popup.swal2-toast .swal2-footer {
            margin: .5em 0 0;
            padding: .5em 0 0;
            font-size: .8em
        }

        .swal2-popup.swal2-toast .swal2-close {
            position: static;
            width: .8em;
            height: .8em;
            line-height: .8
        }

        .swal2-popup.swal2-toast .swal2-content {
            justify-content: flex-start;
            padding: 0;
            font-size: 1em
        }

        .swal2-popup.swal2-toast .swal2-icon {
            width: 2em;
            min-width: 2em;
            height: 2em;
            margin: 0
        }

        .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content {
            display: flex;
            align-items: center;
            font-size: 1.8em;
            font-weight: 700
        }

        @media all and (-ms-high-contrast: none),
        (-ms-high-contrast: active) {
            .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content {
                font-size: .25em
            }
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-success .swal2-success-ring {
            width: 2em;
            height: 2em
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
            top: .875em;
            width: 1.375em
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=left] {
            left: .3125em
        }

        .swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=right] {
            right: .3125em
        }

        .swal2-popup.swal2-toast .swal2-actions {
            flex-basis: auto !important;
            width: auto;
            height: auto;
            margin: 0 .3125em
        }

        .swal2-popup.swal2-toast .swal2-styled {
            margin: 0 .3125em;
            padding: .3125em .625em;
            font-size: 1em
        }

        .swal2-popup.swal2-toast .swal2-styled:focus {
            box-shadow: 0 0 0 1px #fff, 0 0 0 3px rgba(50, 100, 150, .4)
        }

        .swal2-popup.swal2-toast .swal2-success {
            border-color: #a5dc86
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line] {
            position: absolute;
            width: 1.6em;
            height: 3em;
            transform: rotate(45deg);
            border-radius: 50%
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=left] {
            top: -.8em;
            left: -.5em;
            transform: rotate(-45deg);
            transform-origin: 2em 2em;
            border-radius: 4em 0 0 4em
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=right] {
            top: -.25em;
            left: .9375em;
            transform-origin: 0 1.5em;
            border-radius: 0 4em 4em 0
        }

        .swal2-popup.swal2-toast .swal2-success .swal2-success-ring {
            width: 2em;
            height: 2em
        }

        .swal2-popup.swal2-toast .swal2-success .swal2-success-fix {
            top: 0;
            left: .4375em;
            width: .4375em;
            height: 2.6875em
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line] {
            height: .3125em
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=tip] {
            top: 1.125em;
            left: .1875em;
            width: .75em
        }

        .swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=long] {
            top: .9375em;
            right: .1875em;
            width: 1.375em
        }

        .swal2-popup.swal2-toast .swal2-success.swal2-icon-show .swal2-success-line-tip {
            -webkit-animation: swal2-toast-animate-success-line-tip .75s;
            animation: swal2-toast-animate-success-line-tip .75s
        }

        .swal2-popup.swal2-toast .swal2-success.swal2-icon-show .swal2-success-line-long {
            -webkit-animation: swal2-toast-animate-success-line-long .75s;
            animation: swal2-toast-animate-success-line-long .75s
        }

        .swal2-popup.swal2-toast.swal2-show {
            -webkit-animation: swal2-toast-show .5s;
            animation: swal2-toast-show .5s
        }

        .swal2-popup.swal2-toast.swal2-hide {
            -webkit-animation: swal2-toast-hide .1s forwards;
            animation: swal2-toast-hide .1s forwards
        }

        .swal2-container {
            display: flex;
            position: fixed;
            z-index: 1060;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: .625em;
            overflow-x: hidden;
            transition: background-color .1s;
            -webkit-overflow-scrolling: touch
        }

        .swal2-container.swal2-backdrop-show,
        .swal2-container.swal2-noanimation {
            background: rgba(0, 0, 0, .4)
        }

        .swal2-container.swal2-backdrop-hide {
            background: 0 0 !important
        }

        .swal2-container.swal2-top {
            align-items: flex-start
        }

        .swal2-container.swal2-top-left,
        .swal2-container.swal2-top-start {
            align-items: flex-start;
            justify-content: flex-start
        }

        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top-right {
            align-items: flex-start;
            justify-content: flex-end
        }

        .swal2-container.swal2-center {
            align-items: center
        }

        .swal2-container.swal2-center-left,
        .swal2-container.swal2-center-start {
            align-items: center;
            justify-content: flex-start
        }

        .swal2-container.swal2-center-end,
        .swal2-container.swal2-center-right {
            align-items: center;
            justify-content: flex-end
        }

        .swal2-container.swal2-bottom {
            align-items: flex-end
        }

        .swal2-container.swal2-bottom-left,
        .swal2-container.swal2-bottom-start {
            align-items: flex-end;
            justify-content: flex-start
        }

        .swal2-container.swal2-bottom-end,
        .swal2-container.swal2-bottom-right {
            align-items: flex-end;
            justify-content: flex-end
        }

        .swal2-container.swal2-bottom-end > :first-child,
        .swal2-container.swal2-bottom-left > :first-child,
        .swal2-container.swal2-bottom-right > :first-child,
        .swal2-container.swal2-bottom-start > :first-child,
        .swal2-container.swal2-bottom > :first-child {
            margin-top: auto
        }

        .swal2-container.swal2-grow-fullscreen > .swal2-modal {
            display: flex !important;
            flex: 1;
            align-self: stretch;
            justify-content: center
        }

        .swal2-container.swal2-grow-row > .swal2-modal {
            display: flex !important;
            flex: 1;
            align-content: center;
            justify-content: center
        }

        .swal2-container.swal2-grow-column {
            flex: 1;
            flex-direction: column
        }

        .swal2-container.swal2-grow-column.swal2-bottom,
        .swal2-container.swal2-grow-column.swal2-center,
        .swal2-container.swal2-grow-column.swal2-top {
            align-items: center
        }

        .swal2-container.swal2-grow-column.swal2-bottom-left,
        .swal2-container.swal2-grow-column.swal2-bottom-start,
        .swal2-container.swal2-grow-column.swal2-center-left,
        .swal2-container.swal2-grow-column.swal2-center-start,
        .swal2-container.swal2-grow-column.swal2-top-left,
        .swal2-container.swal2-grow-column.swal2-top-start {
            align-items: flex-start
        }

        .swal2-container.swal2-grow-column.swal2-bottom-end,
        .swal2-container.swal2-grow-column.swal2-bottom-right,
        .swal2-container.swal2-grow-column.swal2-center-end,
        .swal2-container.swal2-grow-column.swal2-center-right,
        .swal2-container.swal2-grow-column.swal2-top-end,
        .swal2-container.swal2-grow-column.swal2-top-right {
            align-items: flex-end
        }

        .swal2-container.swal2-grow-column > .swal2-modal {
            display: flex !important;
            flex: 1;
            align-content: center;
            justify-content: center
        }

        .swal2-container.swal2-no-transition {
            transition: none !important
        }

        .swal2-container:not(.swal2-top):not(.swal2-top-start):not(.swal2-top-end):not(.swal2-top-left):not(.swal2-top-right):not(.swal2-center-start):not(.swal2-center-end):not(.swal2-center-left):not(.swal2-center-right):not(.swal2-bottom):not(.swal2-bottom-start):not(.swal2-bottom-end):not(.swal2-bottom-left):not(.swal2-bottom-right):not(.swal2-grow-fullscreen) > .swal2-modal {
            margin: auto
        }

        @media all and (-ms-high-contrast: none),
        (-ms-high-contrast: active) {
            .swal2-container .swal2-modal {
                margin: 0 !important
            }
        }

        .swal2-popup {
            display: none;
            position: relative;
            box-sizing: border-box;
            flex-direction: column;
            justify-content: center;
            width: 32em;
            max-width: 100%;
            padding: 1.25em;
            border: none;
            border-radius: .3125em;
            background: #fff;
            font-family: inherit;
            font-size: 1rem
        }

        .swal2-popup:focus {
            outline: 0
        }

        .swal2-popup.swal2-loading {
            overflow-y: hidden
        }

        .swal2-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 1.8em
        }

        .swal2-title {
            position: relative;
            max-width: 100%;
            margin: 0 0 .4em;
            padding: 0;
            color: #595959;
            font-size: 1.875em;
            font-weight: 600;
            text-align: center;
            text-transform: none;
            word-wrap: break-word
        }

        .swal2-actions {
            display: flex;
            z-index: 1;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 1.25em auto 0
        }

        .swal2-actions:not(.swal2-loading) .swal2-styled[disabled] {
            opacity: .4
        }

        .swal2-actions:not(.swal2-loading) .swal2-styled:hover {
            background-image: linear-gradient(rgba(0, 0, 0, .1), rgba(0, 0, 0, .1))
        }

        .swal2-actions:not(.swal2-loading) .swal2-styled:active {
            background-image: linear-gradient(rgba(0, 0, 0, .2), rgba(0, 0, 0, .2))
        }

        .swal2-actions.swal2-loading .swal2-styled.swal2-confirm {
            box-sizing: border-box;
            width: 2.5em;
            height: 2.5em;
            margin: .46875em;
            padding: 0;
            -webkit-animation: swal2-rotate-loading 1.5s linear 0s infinite normal;
            animation: swal2-rotate-loading 1.5s linear 0s infinite normal;
            border: .25em solid transparent;
            border-radius: 100%;
            border-color: transparent;
            background-color: transparent !important;
            color: transparent !important;
            cursor: default;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none
        }

        .swal2-actions.swal2-loading .swal2-styled.swal2-cancel {
            margin-right: 30px;
            margin-left: 30px
        }

        .swal2-actions.swal2-loading :not(.swal2-styled).swal2-confirm::after {
            content: "";
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-left: 5px;
            -webkit-animation: swal2-rotate-loading 1.5s linear 0s infinite normal;
            animation: swal2-rotate-loading 1.5s linear 0s infinite normal;
            border: 3px solid #999;
            border-radius: 50%;
            border-right-color: transparent;
            box-shadow: 1px 1px 1px #fff
        }

        .swal2-styled {
            margin: .3125em;
            padding: .625em 2em;
            box-shadow: none;
            font-weight: 500
        }

        .swal2-styled:not([disabled]) {
            cursor: pointer
        }

        .swal2-styled.swal2-confirm {
            border: 0;
            border-radius: .25em;
            background: initial;
            background-color: #3085d6;
            color: #fff;
            font-size: 1.0625em
        }

        .swal2-styled.swal2-cancel {
            border: 0;
            border-radius: .25em;
            background: initial;
            background-color: #aaa;
            color: #fff;
            font-size: 1.0625em
        }

        .swal2-styled:focus {
            outline: 0;
            box-shadow: 0 0 0 1px #fff, 0 0 0 3px rgba(50, 100, 150, .4)
        }

        .swal2-styled::-moz-focus-inner {
            border: 0
        }

        .swal2-footer {
            justify-content: center;
            margin: 1.25em 0 0;
            padding: 1em 0 0;
            border-top: 1px solid #eee;
            color: #545454;
            font-size: 1em
        }

        .swal2-timer-progress-bar-container {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: .25em;
            overflow: hidden;
            border-bottom-right-radius: .3125em;
            border-bottom-left-radius: .3125em
        }

        .swal2-timer-progress-bar {
            width: 100%;
            height: .25em;
            background: rgba(0, 0, 0, .2)
        }

        .swal2-image {
            max-width: 100%;
            margin: 1.25em auto
        }

        .swal2-close {
            position: absolute;
            z-index: 2;
            top: 0;
            right: 0;
            align-items: center;
            justify-content: center;
            width: 1.2em;
            height: 1.2em;
            padding: 0;
            overflow: hidden;
            transition: color .1s ease-out;
            border: none;
            border-radius: 0;
            background: 0 0;
            color: #ccc;
            font-family: serif;
            font-size: 2.5em;
            line-height: 1.2;
            cursor: pointer
        }

        .swal2-close:hover {
            transform: none;
            background: 0 0;
            color: #f27474
        }

        .swal2-close::-moz-focus-inner {
            border: 0
        }

        .swal2-content {
            z-index: 1;
            justify-content: center;
            margin: 0;
            padding: 0 1.6em;
            color: #545454;
            font-size: 1.125em;
            font-weight: 400;
            line-height: normal;
            text-align: center;
            word-wrap: break-word
        }

        .swal2-checkbox,
        .swal2-file,
        .swal2-input,
        .swal2-radio,
        .swal2-select,
        .swal2-textarea {
            margin: 1em auto
        }

        .swal2-file,
        .swal2-input,
        .swal2-textarea {
            box-sizing: border-box;
            width: 100%;
            transition: border-color .3s, box-shadow .3s;
            border: 1px solid #d9d9d9;
            border-radius: .1875em;
            background: inherit;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .06);
            color: inherit;
            font-size: 1.125em
        }

        .swal2-file.swal2-inputerror,
        .swal2-input.swal2-inputerror,
        .swal2-textarea.swal2-inputerror {
            border-color: #f27474 !important;
            box-shadow: 0 0 2px #f27474 !important
        }

        .swal2-file:focus,
        .swal2-input:focus,
        .swal2-textarea:focus {
            border: 1px solid #b4dbed;
            outline: 0;
            box-shadow: 0 0 3px #c4e6f5
        }

        .swal2-file::-moz-placeholder,
        .swal2-input::-moz-placeholder,
        .swal2-textarea::-moz-placeholder {
            color: #ccc
        }

        .swal2-file:-ms-input-placeholder,
        .swal2-input:-ms-input-placeholder,
        .swal2-textarea:-ms-input-placeholder {
            color: #ccc
        }

        .swal2-file::-ms-input-placeholder,
        .swal2-input::-ms-input-placeholder,
        .swal2-textarea::-ms-input-placeholder {
            color: #ccc
        }

        .swal2-file::placeholder,
        .swal2-input::placeholder,
        .swal2-textarea::placeholder {
            color: #ccc
        }

        .swal2-range {
            margin: 1em auto;
            background: #fff
        }

        .swal2-range input {
            width: 80%
        }

        .swal2-range output {
            width: 20%;
            color: inherit;
            font-weight: 600;
            text-align: center
        }

        .swal2-range input,
        .swal2-range output {
            height: 2.625em;
            padding: 0;
            font-size: 1.125em;
            line-height: 2.625em
        }

        .swal2-input {
            height: 2.625em;
            padding: 0 .75em
        }

        .swal2-input[type=number] {
            max-width: 10em
        }

        .swal2-file {
            background: inherit;
            font-size: 1.125em
        }

        .swal2-textarea {
            height: 6.75em;
            padding: .75em
        }

        .swal2-select {
            min-width: 50%;
            max-width: 100%;
            padding: .375em .625em;
            background: inherit;
            color: inherit;
            font-size: 1.125em
        }

        .swal2-checkbox,
        .swal2-radio {
            align-items: center;
            justify-content: center;
            background: #fff;
            color: inherit
        }

        .swal2-checkbox label,
        .swal2-radio label {
            margin: 0 .6em;
            font-size: 1.125em
        }

        .swal2-checkbox input,
        .swal2-radio input {
            margin: 0 .4em
        }

        .swal2-validation-message {
            display: none;
            align-items: center;
            justify-content: center;
            padding: .625em;
            overflow: hidden;
            background: #f0f0f0;
            color: #666;
            font-size: 1em;
            font-weight: 300
        }

        .swal2-validation-message::before {
            content: "!";
            display: inline-block;
            width: 1.5em;
            min-width: 1.5em;
            height: 1.5em;
            margin: 0 .625em;
            border-radius: 50%;
            background-color: #f27474;
            color: #fff;
            font-weight: 600;
            line-height: 1.5em;
            text-align: center
        }

        .swal2-icon {
            position: relative;
            box-sizing: content-box;
            justify-content: center;
            width: 5em;
            height: 5em;
            margin: 1.25em auto 1.875em;
            border: .25em solid transparent;
            border-radius: 50%;
            font-family: inherit;
            line-height: 5em;
            cursor: default;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none
        }

        .swal2-icon .swal2-icon-content {
            display: flex;
            align-items: center;
            font-size: 3.75em
        }

        .swal2-icon.swal2-error {
            border-color: #f27474;
            color: #f27474
        }

        .swal2-icon.swal2-error .swal2-x-mark {
            position: relative;
            flex-grow: 1
        }

        .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
            display: block;
            position: absolute;
            top: 2.3125em;
            width: 2.9375em;
            height: .3125em;
            border-radius: .125em;
            background-color: #f27474
        }

        .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=left] {
            left: 1.0625em;
            transform: rotate(45deg)
        }

        .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=right] {
            right: 1em;
            transform: rotate(-45deg)
        }

        .swal2-icon.swal2-error.swal2-icon-show {
            -webkit-animation: swal2-animate-error-icon .5s;
            animation: swal2-animate-error-icon .5s
        }

        .swal2-icon.swal2-error.swal2-icon-show .swal2-x-mark {
            -webkit-animation: swal2-animate-error-x-mark .5s;
            animation: swal2-animate-error-x-mark .5s
        }

        .swal2-icon.swal2-warning {
            border-color: #facea8;
            color: #f8bb86
        }

        .swal2-icon.swal2-info {
            border-color: #9de0f6;
            color: #3fc3ee
        }

        .swal2-icon.swal2-question {
            border-color: #c9dae1;
            color: #87adbd
        }

        .swal2-icon.swal2-success {
            border-color: #a5dc86;
            color: #a5dc86
        }

        .swal2-icon.swal2-success [class^=swal2-success-circular-line] {
            position: absolute;
            width: 3.75em;
            height: 7.5em;
            transform: rotate(45deg);
            border-radius: 50%
        }

        .swal2-icon.swal2-success [class^=swal2-success-circular-line][class$=left] {
            top: -.4375em;
            left: -2.0635em;
            transform: rotate(-45deg);
            transform-origin: 3.75em 3.75em;
            border-radius: 7.5em 0 0 7.5em
        }

        .swal2-icon.swal2-success [class^=swal2-success-circular-line][class$=right] {
            top: -.6875em;
            left: 1.875em;
            transform: rotate(-45deg);
            transform-origin: 0 3.75em;
            border-radius: 0 7.5em 7.5em 0
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            position: absolute;
            z-index: 2;
            top: -.25em;
            left: -.25em;
            box-sizing: content-box;
            width: 100%;
            height: 100%;
            border: .25em solid rgba(165, 220, 134, .3);
            border-radius: 50%
        }

        .swal2-icon.swal2-success .swal2-success-fix {
            position: absolute;
            z-index: 1;
            top: .5em;
            left: 1.625em;
            width: .4375em;
            height: 5.625em;
            transform: rotate(-45deg)
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            display: block;
            position: absolute;
            z-index: 2;
            height: .3125em;
            border-radius: .125em;
            background-color: #a5dc86
        }

        .swal2-icon.swal2-success [class^=swal2-success-line][class$=tip] {
            top: 2.875em;
            left: .8125em;
            width: 1.5625em;
            transform: rotate(45deg)
        }

        .swal2-icon.swal2-success [class^=swal2-success-line][class$=long] {
            top: 2.375em;
            right: .5em;
            width: 2.9375em;
            transform: rotate(-45deg)
        }

        .swal2-icon.swal2-success.swal2-icon-show .swal2-success-line-tip {
            -webkit-animation: swal2-animate-success-line-tip .75s;
            animation: swal2-animate-success-line-tip .75s
        }

        .swal2-icon.swal2-success.swal2-icon-show .swal2-success-line-long {
            -webkit-animation: swal2-animate-success-line-long .75s;
            animation: swal2-animate-success-line-long .75s
        }

        .swal2-icon.swal2-success.swal2-icon-show .swal2-success-circular-line-right {
            -webkit-animation: swal2-rotate-success-circular-line 4.25s ease-in;
            animation: swal2-rotate-success-circular-line 4.25s ease-in
        }

        .swal2-progress-steps {
            align-items: center;
            margin: 0 0 1.25em;
            padding: 0;
            background: inherit;
            font-weight: 600
        }

        .swal2-progress-steps li {
            display: inline-block;
            position: relative
        }

        .swal2-progress-steps .swal2-progress-step {
            z-index: 20;
            width: 2em;
            height: 2em;
            border-radius: 2em;
            background: #3085d6;
            color: #fff;
            line-height: 2em;
            text-align: center
        }

        .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step {
            background: #3085d6
        }

        .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step ~ .swal2-progress-step {
            background: #add8e6;
            color: #fff
        }

        .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step ~ .swal2-progress-step-line {
            background: #add8e6
        }

        .swal2-progress-steps .swal2-progress-step-line {
            z-index: 10;
            width: 2.5em;
            height: .4em;
            margin: 0 -1px;
            background: #3085d6
        }

        [class^=swal2] {
            -webkit-tap-highlight-color: transparent
        }

        .swal2-show {
            -webkit-animation: swal2-show .3s;
            animation: swal2-show .3s
        }

        .swal2-hide {
            -webkit-animation: swal2-hide .15s forwards;
            animation: swal2-hide .15s forwards
        }

        .swal2-noanimation {
            transition: none
        }

        .swal2-scrollbar-measure {
            position: absolute;
            top: -9999px;
            width: 50px;
            height: 50px;
            overflow: scroll
        }

        .swal2-rtl .swal2-close {
            right: auto;
            left: 0
        }

        .swal2-rtl .swal2-timer-progress-bar {
            right: 0;
            left: auto
        }

        @supports (-ms-accelerator:true) {
            .swal2-range input {
                width: 100% !important
            }

            .swal2-range output {
                display: none
            }
        }

        @media all and (-ms-high-contrast: none),
        (-ms-high-contrast: active) {
            .swal2-range input {
                width: 100% !important
            }

            .swal2-range output {
                display: none
            }
        }

        @-moz-document url-prefix() {
            .swal2-close:focus {
                outline: 2px solid rgba(50, 100, 150, .4)
            }
        }

        @-webkit-keyframes swal2-toast-show {
            0% {
                transform: translateY(-.625em) rotateZ(2deg)
            }

            33% {
                transform: translateY(0) rotateZ(-2deg)
            }

            66% {
                transform: translateY(.3125em) rotateZ(2deg)
            }

            100% {
                transform: translateY(0) rotateZ(0)
            }
        }

        @keyframes swal2-toast-show {
            0% {
                transform: translateY(-.625em) rotateZ(2deg)
            }

            33% {
                transform: translateY(0) rotateZ(-2deg)
            }

            66% {
                transform: translateY(.3125em) rotateZ(2deg)
            }

            100% {
                transform: translateY(0) rotateZ(0)
            }
        }

        @-webkit-keyframes swal2-toast-hide {
            100% {
                transform: rotateZ(1deg);
                opacity: 0
            }
        }

        @keyframes swal2-toast-hide {
            100% {
                transform: rotateZ(1deg);
                opacity: 0
            }
        }

        @-webkit-keyframes swal2-toast-animate-success-line-tip {
            0% {
                top: .5625em;
                left: .0625em;
                width: 0
            }

            54% {
                top: .125em;
                left: .125em;
                width: 0
            }

            70% {
                top: .625em;
                left: -.25em;
                width: 1.625em
            }

            84% {
                top: 1.0625em;
                left: .75em;
                width: .5em
            }

            100% {
                top: 1.125em;
                left: .1875em;
                width: .75em
            }
        }

        @keyframes swal2-toast-animate-success-line-tip {
            0% {
                top: .5625em;
                left: .0625em;
                width: 0
            }

            54% {
                top: .125em;
                left: .125em;
                width: 0
            }

            70% {
                top: .625em;
                left: -.25em;
                width: 1.625em
            }

            84% {
                top: 1.0625em;
                left: .75em;
                width: .5em
            }

            100% {
                top: 1.125em;
                left: .1875em;
                width: .75em
            }
        }

        @-webkit-keyframes swal2-toast-animate-success-line-long {
            0% {
                top: 1.625em;
                right: 1.375em;
                width: 0
            }

            65% {
                top: 1.25em;
                right: .9375em;
                width: 0
            }

            84% {
                top: .9375em;
                right: 0;
                width: 1.125em
            }

            100% {
                top: .9375em;
                right: .1875em;
                width: 1.375em
            }
        }

        @keyframes swal2-toast-animate-success-line-long {
            0% {
                top: 1.625em;
                right: 1.375em;
                width: 0
            }

            65% {
                top: 1.25em;
                right: .9375em;
                width: 0
            }

            84% {
                top: .9375em;
                right: 0;
                width: 1.125em
            }

            100% {
                top: .9375em;
                right: .1875em;
                width: 1.375em
            }
        }

        @-webkit-keyframes swal2-show {
            0% {
                transform: scale(.7)
            }

            45% {
                transform: scale(1.05)
            }

            80% {
                transform: scale(.95)
            }

            100% {
                transform: scale(1)
            }
        }

        @keyframes swal2-show {
            0% {
                transform: scale(.7)
            }

            45% {
                transform: scale(1.05)
            }

            80% {
                transform: scale(.95)
            }

            100% {
                transform: scale(1)
            }
        }

        @-webkit-keyframes swal2-hide {
            0% {
                transform: scale(1);
                opacity: 1
            }

            100% {
                transform: scale(.5);
                opacity: 0
            }
        }

        @keyframes swal2-hide {
            0% {
                transform: scale(1);
                opacity: 1
            }

            100% {
                transform: scale(.5);
                opacity: 0
            }
        }

        @-webkit-keyframes swal2-animate-success-line-tip {
            0% {
                top: 1.1875em;
                left: .0625em;
                width: 0
            }

            54% {
                top: 1.0625em;
                left: .125em;
                width: 0
            }

            70% {
                top: 2.1875em;
                left: -.375em;
                width: 3.125em
            }

            84% {
                top: 3em;
                left: 1.3125em;
                width: 1.0625em
            }

            100% {
                top: 2.8125em;
                left: .8125em;
                width: 1.5625em
            }
        }

        @keyframes swal2-animate-success-line-tip {
            0% {
                top: 1.1875em;
                left: .0625em;
                width: 0
            }

            54% {
                top: 1.0625em;
                left: .125em;
                width: 0
            }

            70% {
                top: 2.1875em;
                left: -.375em;
                width: 3.125em
            }

            84% {
                top: 3em;
                left: 1.3125em;
                width: 1.0625em
            }

            100% {
                top: 2.8125em;
                left: .8125em;
                width: 1.5625em
            }
        }

        @-webkit-keyframes swal2-animate-success-line-long {
            0% {
                top: 3.375em;
                right: 2.875em;
                width: 0
            }

            65% {
                top: 3.375em;
                right: 2.875em;
                width: 0
            }

            84% {
                top: 2.1875em;
                right: 0;
                width: 3.4375em
            }

            100% {
                top: 2.375em;
                right: .5em;
                width: 2.9375em
            }
        }

        @keyframes swal2-animate-success-line-long {
            0% {
                top: 3.375em;
                right: 2.875em;
                width: 0
            }

            65% {
                top: 3.375em;
                right: 2.875em;
                width: 0
            }

            84% {
                top: 2.1875em;
                right: 0;
                width: 3.4375em
            }

            100% {
                top: 2.375em;
                right: .5em;
                width: 2.9375em
            }
        }

        @-webkit-keyframes swal2-rotate-success-circular-line {
            0% {
                transform: rotate(-45deg)
            }

            5% {
                transform: rotate(-45deg)
            }

            12% {
                transform: rotate(-405deg)
            }

            100% {
                transform: rotate(-405deg)
            }
        }

        @keyframes swal2-rotate-success-circular-line {
            0% {
                transform: rotate(-45deg)
            }

            5% {
                transform: rotate(-45deg)
            }

            12% {
                transform: rotate(-405deg)
            }

            100% {
                transform: rotate(-405deg)
            }
        }

        @-webkit-keyframes swal2-animate-error-x-mark {
            0% {
                margin-top: 1.625em;
                transform: scale(.4);
                opacity: 0
            }

            50% {
                margin-top: 1.625em;
                transform: scale(.4);
                opacity: 0
            }

            80% {
                margin-top: -.375em;
                transform: scale(1.15)
            }

            100% {
                margin-top: 0;
                transform: scale(1);
                opacity: 1
            }
        }

        @keyframes swal2-animate-error-x-mark {
            0% {
                margin-top: 1.625em;
                transform: scale(.4);
                opacity: 0
            }

            50% {
                margin-top: 1.625em;
                transform: scale(.4);
                opacity: 0
            }

            80% {
                margin-top: -.375em;
                transform: scale(1.15)
            }

            100% {
                margin-top: 0;
                transform: scale(1);
                opacity: 1
            }
        }

        @-webkit-keyframes swal2-animate-error-icon {
            0% {
                transform: rotateX(100deg);
                opacity: 0
            }

            100% {
                transform: rotateX(0);
                opacity: 1
            }
        }

        @keyframes swal2-animate-error-icon {
            0% {
                transform: rotateX(100deg);
                opacity: 0
            }

            100% {
                transform: rotateX(0);
                opacity: 1
            }
        }

        @-webkit-keyframes swal2-rotate-loading {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        @keyframes swal2-rotate-loading {
            0% {
                transform: rotate(0)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
            overflow: hidden
        }

        body.swal2-height-auto {
            height: auto !important
        }

        body.swal2-no-backdrop .swal2-container {
            top: auto;
            right: auto;
            bottom: auto;
            left: auto;
            max-width: calc(100% - .625em * 2);
            background-color: transparent !important
        }

        body.swal2-no-backdrop .swal2-container > .swal2-modal {
            box-shadow: 0 0 10px rgba(0, 0, 0, .4)
        }

        body.swal2-no-backdrop .swal2-container.swal2-top {
            top: 0;
            left: 50%;
            transform: translateX(-50%)
        }

        body.swal2-no-backdrop .swal2-container.swal2-top-left,
        body.swal2-no-backdrop .swal2-container.swal2-top-start {
            top: 0;
            left: 0
        }

        body.swal2-no-backdrop .swal2-container.swal2-top-end,
        body.swal2-no-backdrop .swal2-container.swal2-top-right {
            top: 0;
            right: 0
        }

        body.swal2-no-backdrop .swal2-container.swal2-center {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%)
        }

        body.swal2-no-backdrop .swal2-container.swal2-center-left,
        body.swal2-no-backdrop .swal2-container.swal2-center-start {
            top: 50%;
            left: 0;
            transform: translateY(-50%)
        }

        body.swal2-no-backdrop .swal2-container.swal2-center-end,
        body.swal2-no-backdrop .swal2-container.swal2-center-right {
            top: 50%;
            right: 0;
            transform: translateY(-50%)
        }

        body.swal2-no-backdrop .swal2-container.swal2-bottom {
            bottom: 0;
            left: 50%;
            transform: translateX(-50%)
        }

        body.swal2-no-backdrop .swal2-container.swal2-bottom-left,
        body.swal2-no-backdrop .swal2-container.swal2-bottom-start {
            bottom: 0;
            left: 0
        }

        body.swal2-no-backdrop .swal2-container.swal2-bottom-end,
        body.swal2-no-backdrop .swal2-container.swal2-bottom-right {
            right: 0;
            bottom: 0
        }

        @media print {
            body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) {
                overflow-y: scroll !important
            }

            body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) > [aria-hidden=true] {
                display: none
            }

            body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) .swal2-container {
                position: static !important
            }
        }

        body.swal2-toast-shown .swal2-container {
            background-color: transparent
        }

        body.swal2-toast-shown .swal2-container.swal2-top {
            top: 0;
            right: auto;
            bottom: auto;
            left: 50%;
            transform: translateX(-50%)
        }

        body.swal2-toast-shown .swal2-container.swal2-top-end,
        body.swal2-toast-shown .swal2-container.swal2-top-right {
            top: 0;
            right: 0;
            bottom: auto;
            left: auto
        }

        body.swal2-toast-shown .swal2-container.swal2-top-left,
        body.swal2-toast-shown .swal2-container.swal2-top-start {
            top: 0;
            right: auto;
            bottom: auto;
            left: 0
        }

        body.swal2-toast-shown .swal2-container.swal2-center-left,
        body.swal2-toast-shown .swal2-container.swal2-center-start {
            top: 50%;
            right: auto;
            bottom: auto;
            left: 0;
            transform: translateY(-50%)
        }

        body.swal2-toast-shown .swal2-container.swal2-center {
            top: 50%;
            right: auto;
            bottom: auto;
            left: 50%;
            transform: translate(-50%, -50%)
        }

        body.swal2-toast-shown .swal2-container.swal2-center-end,
        body.swal2-toast-shown .swal2-container.swal2-center-right {
            top: 50%;
            right: 0;
            bottom: auto;
            left: auto;
            transform: translateY(-50%)
        }

        body.swal2-toast-shown .swal2-container.swal2-bottom-left,
        body.swal2-toast-shown .swal2-container.swal2-bottom-start {
            top: auto;
            right: auto;
            bottom: 0;
            left: 0
        }

        body.swal2-toast-shown .swal2-container.swal2-bottom {
            top: auto;
            right: auto;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%)
        }

        body.swal2-toast-shown .swal2-container.swal2-bottom-end,
        body.swal2-toast-shown .swal2-container.swal2-bottom-right {
            top: auto;
            right: 0;
            bottom: 0;
            left: auto
        }

        body.swal2-toast-column .swal2-toast {
            flex-direction: column;
            align-items: stretch
        }

        body.swal2-toast-column .swal2-toast .swal2-actions {
            flex: 1;
            align-self: stretch;
            height: 2.2em;
            margin-top: .3125em
        }

        body.swal2-toast-column .swal2-toast .swal2-loading {
            justify-content: center
        }

        body.swal2-toast-column .swal2-toast .swal2-input {
            height: 2em;
            margin: .3125em auto;
            font-size: 1em
        }

        body.swal2-toast-column .swal2-toast .swal2-validation-message {
            font-size: 1em
        }

        .select_disabled {
            pointer-events: none;
            color: #bfcbd9;
            cursor: not-allowed;
            background-image: none;
            background-color: #eef1f6;
            border-color: #d1dbe5;

        }

        .other .tablebox a.attch_pic {
            margin: 3px 13px 3px 0;
        }

        .other .tablebox a.attch_pic > img {
            width: 200px;
            vertical-align: bottom;
        }

    </style>

</head>

<body class="fourth other">

<div class="bodybox" id='app'>
    <!-- header -->
    <header class="dialogclear">header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="list_function main">
            <div class="block" style="display: flex; justify-content: space-between;">
                <div>
                    <!-- Task calendar -->
                    <div class="popupblock">
                        <a class="calendar red" id="btn_view"></a>
                    </div>
                    <!-- Meeting calendar -->
                    <div class="popupblock">
                        <a class="calendar" id="btn_arrange"></a>
                    </div>
                    <!-- Export -->
                    <div class="popupblock">
                        <a @click="export_word()"><i aria-hidden="true" class="fas fa-file-export" style="font-size: 26px;"></i></a>
                    </div>
                </div>

                <div class="namebox">
                    <!-- Project Name -->
                    <b class="tag focus">{{ special == 's' ? 'SPECIAL PROJECT' : 'PROJECT' }}</b>
                    <b><a style="font-size:20px; padding-left:20px;" :href="'project02?p=' + project_id">{{project_name
                        }}</a></b>
                </div>

                <div class="namebox">
                    <!-- Stage Name -->
                    <span class="stage_name">A Meeting / Close Deal Stage</span>
                </div>
            </div>
        </div>


        <div class="block" style="font-size: 16px; font-weight: 400;">

            <div class="section">
                <span class="heading">0. General Information</span>

                <ul>
                    <li>
                        Project Name
                    </li>
                    <li>
                        {{project_name}}
                    </li>
                </ul>

                <ul>
                    <li>
                        Project Category
                    </li>
                    <li>
                        {{category}}
                    </li>
                </ul>

                <ul>
                    <li>
                        Date of Down payment
                    </li>
                    <li>
                        <input type="date" class="form-control one_whole" v-model="down_payment_date" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        Customer Value Supervisor
                    </li>
                    <li>
                        <select class="form-control one_whole" v-model="account_executive" :disabled="!is_Editing">
                            <option value=""></option>
                            <option v-for="(item, index) in users" :value="item.username" :key="item.username">
                                {{ item.username }}
                            </option>
                        </select>
                    </li>
                </ul>

                <ul>
                    <li>
                        PIC
                    </li>
                    <li>
                        <select class="form-control one_whole" v-model="pic" :disabled="!is_Editing">
                            <option value=""></option>
                            <option v-for="(item, index) in users" :value="item.username" :key="item.username">
                                {{ item.username }}
                            </option>
                        </select>
                    </li>
                </ul>

                <ul>
                    <li>
                        Last Updated
                    </li>
                    <li>
                        {{ updator }} {{ updator !== '' ? 'at' : '' }} {{ updated_at }}
                    </li>
                </ul>

            </div>


            <div class="section">
                <span class="heading">1. Project Details</span>

                <ul>
                    <li>
                        Quotation #
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="quotation" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        a. Client Name
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="client_name" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        b. Contact Person
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="contact_person" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        c. Contact Number
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="contact_number" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        d. Delivery Address
                    </li>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="delivery_address_within" :disabled="!is_Editing">
                            <span>Within Metro Manila</span>
                        </div>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone"  v-model="delivery_address_outside" :disabled="!is_Editing">
                            <span>Outside Metro Manila</span>
                        </div>
                    </li>
                </ul>

                <ul>
                    <li>
                        e. Exact Delivery Address
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="2" v-model="exact_delivery_address" :disabled="!is_Editing"></textarea>
                    </li>
                </ul>

                <ul>
                    <li>
                        f. Detailed Delivery and Installation location ( Area / Floor / Department / Room Number )
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="5" v-model="detail_delivery_address" :disabled="!is_Editing"></textarea>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="attached_layout" :disabled="!is_Editing">
                            <span>See attached approved furniture layout / lighting layout</span>
                        </div>

                        <div id="" class="file-selector" v-show="attached_layout === true || attached_layout === '1' || attached_layout === 't'">
                            <span>choose file</span>
                            <input type="file" name="" multiple @change="change_attached_layout_file()" ref="attached_layout_file"  :disabled="!is_Editing"/>
                        </div>

                        <div class="file-container"  v-show="attached_layout === true || attached_layout === '1' || attached_layout === 't'">

                            <div class="file-element" v-for="(item,index) in attached_layout_file">
                                <input type="checkbox" :id="'attached_layout' + item.name" v-model="item.checked" :disabled="!is_Editing">
                                <label :for="'attached_layout' + item.name"><a v-if="item.gcp_name !== ''" :href="baseURL + item.gcp_name" target="_blank">{{ item.name }}</a><a v-if="item.gcp_name === ''">{{ item.name }}</a></label>
                            </div>


                        </div>

                        <input id="" style="display: none;" value="">

                    </li>
                </ul>

                <ul>
                    <li>
                        g. Permit Processing
                    </li>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="delivery_permit" :disabled="!is_Editing">
                            <span>Delivery Permit</span>
                        </div>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="work_permit" :disabled="!is_Editing">
                            <span>Work Permit (for Delivery and Install Projects)</span>
                        </div>

                        <div style="margin-top: 5px;">
                            <span style="margin-left: 3px;">Notes:</span>
                            <textarea class="form-control one_whole" rows="3" v-model="permit_processing_note" :disabled="!is_Editing"></textarea>
                        </div>
                    </li>
                </ul>

                <ul>
                    <li>
                        h. Other Client Concern / Request
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="5" v-model="other_request" :disabled="!is_Editing"></textarea>
                    </li>
                </ul>

            </div>


            <div class="section">
                <span class="heading">2. Delivery Schedule</span>

                <ul>
                    <li>
                        a. Date of Delivery / Site Timeline
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="date_of_delivery" :disabled="!is_Editing">
                    </li>
                </ul>

                <ul>
                    <li>
                        b. Deadline with the Client
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="client_deadline" :disabled="!is_Editing">
                    </li>
                </ul>
            </div>


            <div class="section">
                <span class="heading">3. Scope</span>

                <ul>
                    <li>
                        a. Delivery
                    </li>
                    <li>
                        
                        <textarea class="form-control one_whole" rows="20" style="margin-top: 5px;" v-model="delivery_1st_items" :disabled="!is_Editing"></textarea>

                    </li>
                </ul>

                <ul>
                    <li>
                        b. Installation
                    </li>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="os_delivery_only" :disabled="!is_Editing">
                            <span>Office Systems Furniture: Delivery Only</span>
                        </div>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="os_delivery_install" :disabled="!is_Editing">
                            <span>Office Systems Furniture: Delivery and Install</span>
                        </div>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="lt_delivery_only" :disabled="!is_Editing">
                            <span>Lighting: Delivery Only</span>
                        </div>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="lt_delivery_install" :disabled="!is_Editing">
                            <span>Lighting: Delivery and Install</span>
                        </div>

                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="delivery_install" :disabled="!is_Editing">
                            <span>Decorative Lighting: Delivery and Install</span>
                        </div>
                    </li>
                </ul>

                <ul>
                    <li>
                        c. Tagging of Products
                    </li>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="scope_attached_layout" :disabled="!is_Editing">
                            <span>See attached approved furniture layout / lighting layout</span>
                        </div>

                        <div id="" class="file-selector" v-show="scope_attached_layout === true || scope_attached_layout === '1' || scope_attached_layout === 't'">
                            <span>choose file</span>
                            <input type="file" name="" multiple @change="change_scope_attached_layout_file()" ref="scope_attached_layout_file" :disabled="!is_Editing"/>
                        </div>

                        <div class="file-container" v-show="scope_attached_layout === true || scope_attached_layout === '1' || scope_attached_layout === 't'">

                            <div class="file-element" v-for="(item,index) in scope_attached_layout_file">
                                <input type="checkbox" :id="'scope_attached_layout' + item.name" v-model="item.checked" :disabled="!is_Editing">
                                <label :for="'scope_attached_layout' + item.name"><a v-if="item.gcp_name !== ''" :href="baseURL + item.gcp_name" target="_blank">{{ item.name }}</a><a v-if="item.gcp_name === ''">{{ item.name }}</a></label>
                            </div>
                        </div>

                        <input id="" style="display: none;" value="">

                    </li>
                </ul>

            </div>


            <div class="section checkbox_ahead">
                <span class="heading">4. 3rd Party Contractor</span>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="timeline_check" :disabled="!is_Editing">
                            <span>Timeline</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(timeline_check !== true && timeline_check !== '1' && timeline_check !== 't') || !is_Editing"  v-model="timeline">
                    </li>
                </ul>

                <ul v-show="category_id == 1">
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="data_check" :disabled="!is_Editing">
                            <span>Data</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(data_check !== true && data_check !== '1' && data_check !== 't') || !is_Editing" v-model="data">
                    </li>
                </ul>

                <ul v-show="category_id == 1">
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="electrical_check" :disabled="!is_Editing">
                            <span>Electrical</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(electrical_check !== true && electrical_check !== '1' && electrical_check !== 't') || !is_Editing" v-model="electrical" >
                    </li>
                </ul>

                <ul v-show="category_id == 1">
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="flooring_check" :disabled="!is_Editing">
                            <span>Flooring</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(flooring_check !== true && flooring_check !== '1' && flooring_check !== 't') || !is_Editing" v-model="flooring" >
                    </li>
                </ul>

                <ul v-show="category_id == 2">
                    <li>
                        Type and Ceiling Height
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" v-model="type_and_ceiling"  :disabled="!is_Editing">
                    </li>
                </ul>

                <ul v-show="category_id == 2">
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="painting_check" :disabled="!is_Editing">
                            <span>Painting</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(painting_check !== true && painting_check !== '1' && painting_check !== 't') || !is_Editing" v-model="painting" >
                    </li>
                </ul>

                <ul v-show="category_id == 2">
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="ceiling_electrical_check" :disabled="!is_Editing">
                            <span>Electrical</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(ceiling_electrical_check !== true && ceiling_electrical_check !== '1' && ceiling_electrical_check !== 't') || !is_Editing" v-model="ceiling_electrical" >
                    </li>
                </ul>
            </div>


            <div class="section checkbox_ahead">
                <span class="heading">5. Outsourcing c/o Admin (if needed)</span>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="manpower_check" :disabled="!is_Editing">
                            <span>Manpower</span>
                        </div>
                    </li>
                    <li>
                        <input type="text" class="form-control one_whole" :disabled="(manpower_check !== true && manpower_check !== '1' && manpower_check !== 't') || !is_Editing" v-model="manpower" >
                    </li>
                </ul>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="materials_check" :disabled="!is_Editing">
                            <span>Materials</span>
                        </div>
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="5" :disabled="(materials_check !== true && materials_check !== '1' && materials_check !== 't') || !is_Editing"  v-model="materials"></textarea>
                    </li>
                </ul>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="trucking_services" :disabled="!is_Editing">
                            <span>Trucking Services</span>
                        </div>
                    </li>
                    <li>

                    </li>
                </ul>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="purchasing_of_special_products_check" :disabled="!is_Editing">
                            <span>Purchasing of Special Products</span>
                        </div>
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="5" :disabled="(purchasing_of_special_products_check !== true && purchasing_of_special_products_check !== '1' && purchasing_of_special_products_check !== 't') || !is_Editing" v-model="purchasing_of_special_products"></textarea>
                    </li>
                </ul>

                <ul>
                    <li>
                        <div class="checkbox_label">
                            <input type="checkbox" class="alone" v-model="tools_check" :disabled="!is_Editing">
                            <span>Tools</span>
                        </div>
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="5" :disabled="(tools_check !== true && tools_check !== '1' && tools_check !== 't') || !is_Editing" v-model="tools"></textarea>
                    </li>
                </ul>

            </div>


            <div class="section">
                <span class="heading">Others</span>

                <ul>
                    <li>
                        Attachment
                    </li>
                    <li>

                        <div id="" class="file-selector">
                            <span>choose file</span>
                            <input type="file" name="" multiple @change="change_other_attached_layout_file()" ref="other_attached_layout_file" :disabled="!is_Editing"/>
                        </div>

                        <div class="file-container" id="">

                            <div class="file-element" v-for="(item,index) in other_attached_layout_file">
                                <input type="checkbox" :id="'other_attached_layout' + item.name" v-model="item.checked" :disabled="!is_Editing">
                                <label :for="'other_attached_layout' + item.name"><a v-if="item.gcp_name !== ''" :href="baseURL + item.gcp_name" target="_blank">{{ item.name }}</a><a v-if="item.gcp_name == ''" >{{ item.name }}</a></label>
                            </div>

                        </div>

                        <input id="" style="display: none;" value="">

                    </li>
                </ul>

            </div>
<?php
if ($test_manager[2]  == "1") {
?>
            <div class="btnbox">
                <button type="button" class="btn btn-secondary btn-lg" style="width: 100px; font-weight: 600;" @click="cancel()" v-if="is_Editing">Cancel
                </button>
                <button type="button" class="btn btn-success btn-lg" style="width: 100px; font-weight: 600;" @click="a_meeting_edit_create()" v-if="is_Editing">Save
                </button>
                <button type="button" class="btn btn-success btn-lg"
                        style="width: 100px; font-weight: 600;" @click="edit()" v-if="!is_Editing">Edit
                </button>
            </div>
<?php
}
?>
        </div>

    </div>
</div>

<div id="tasks" style="visibility: hidden;">

    <div style="text-align: right;">
        <button style="border: none;" onclick="hideWindow('#tasks')"><i class="fa fa-times fa-lg"></i></button>
    </div>

    <div id="task_calendar" style="margin-bottom: 15px;"></div>

</div>

<div id="meeting" style="visibility: hidden;">

    <div style="text-align: right;">
        <button style="border: none;" onclick="hideWindow('#meeting')"><i class="fa fa-times fa-lg"></i></button>
    </div>

    <div id="calendar" style="margin-bottom: 15px;"></div>

    <form id="addmeeting-form" style="display: none;">
        <fieldset>
            <legend> Meeting Information</legend>

            <div class="meetingform-item">
                <label>Subject:</label>
                <input type="text" id="newSubject">
            </div>

            <div class="meetingform-item">
                <label>Project:</label>
                <input type="text" id="newProject" placeholder="">
            </div>

            <div class="meetingform-item">
                <label>Attendee:</label>
                <v-select id="newAttendee" :options="users" attach chips label="username" v-model=attendee
                          multiple></v-select>
            </div>

            <div class="meetingform-item">
                <label>Time:</label>
                <input type="date" id="newDate">
                <input type="time" id="newStartTime">
                <input type="time" id="newEndTime">
            </div>

            <div class="meetingform-item">
                <label>Location:</label>
                <input type="text" id="newLocation">
            </div>

            <div class="meetingform-item">
                <label>Content:</label>
                <textarea style="flex-grow: 1; resize: none;" rows="2" id="newContent"></textarea>

            </div>

            <div class="meetingform-item" id="upload_input">
                <label>File:</label>
                <input type="file" ref="file" id="fileload" name="file[]" onChange="onChangeFileUpload(event)" multiple>
            </div>

            <div class="file-container" id="sc_product_files">


            </div>

            <input id="sc_product_files_hide" style="display: none;" value="">

            <div class="meetingform-buttons">
                <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addmeeting-form')">Close</a>

                <a class="btn small green" id="btn_add">Add</a>
            </div>

        </fieldset>
    </form>

    <form id="editmeeting-form" style="display: none;">
        <fieldset disabled>
            <legend> Meeting Information</legend>

            <div class="meetingform-item">
                <label>Subject:</label>
                <input type="text" id="oldSubject">
            </div>

            <div class="meetingform-item">
                <label>Project:</label>
                <input type="text" id="oldProject" placeholder="">
            </div>

            <div class="meetingform-item">
                <label>Creator:</label>
                <input type="text" id="oldCreator">
            </div>

            <div class="meetingform-item">
                <label>Attendee:</label>
                <v-select id="oldAttendee" :options="users" attach chips label="username" v-model=old_attendee
                          multiple></v-select>
            </div>

            <div class="meetingform-item">
                <label>Time:</label>
                <input type="date" id="oldDate">
                <input type="time" id="oldStartTime">
                <input type="time" id="oldEndTime">
            </div>

            <div class="meetingform-item">
                <label>Location:</label>
                <input type="text" id="oldLocation">
            </div>

            <div class="meetingform-item">
                <label>Content:</label>
                <textarea style="flex-grow: 1; resize: none;" rows="2" id="oldContent"></textarea>

            </div>

            <div class="meetingform-item" id="upload_input_old">
                <label>File:</label>
                <input type="file" ref="file_old" id="fileload_old" name="file_old[]"
                       onChange="onChangeFileUploadOld(event)" multiple>
            </div>

            <div class="file-container" id="sc_product_files_old">


            </div>

            <input id="sc_product_files_hide" style="display: none;" value="">

            <div class="meetingform-buttons">
                <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')"
                   id="btn_close">Close</a>
                <a class="btn small" id="btn_delete">Delete</a>
                <a class="btn small green" id="btn_edit">Edit</a>
                <a class="btn small" id="btn_cancel">Cancel</a>
                <a class="btn small green" id="btn_save">Save</a>
            </div>

        </fieldset>
    </form>

</div>

</body>


<script>
    function CanClose(uid, creator_id, level, creator_level, department) {
        let can_close = false;

        if (department === 'Lighting') {
            if (level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR") {
                if (creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR") {
                    can_close = false;
                } else
                    can_close = true;
            }


            if (level === "LIGHTING VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER") {
                if (creator_level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" || creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR" || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }

            if (level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR") {
                if (creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR" || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }

            if (level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR") {
                if (creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }
        }

        if (department === 'Office Systems') {
            if (level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR") {
                if (creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR") {
                    can_close = false;
                } else
                    can_close = true;
            }

            if (level === "OFFICE SPACE VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER") {
                if (creator_level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" || creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR" || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }

            if (level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR") {
                if (creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR" || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }

            if (level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR") {
                if (creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR") {
                    can_close = true;
                }
            }
        }

        if (department === 'AD') {
            if (level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR") {
                if (creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR") {
                    can_close = false;
                } else
                    can_close = true;
            }

            if (level === "OPERATIONS MANAGER") {
                if (creator_level === "ASSISTANT OPERATIONS MANAGER") {
                    can_close = true;
                }
            }
        }

        if (department === 'DS') {
            if (level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR") {
                if (creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR") {
                    can_close = false;
                } else
                    can_close = true;
            }

            if (level === "BRAND MANAGER") {
                if (creator_level === "ASSISTANT Brand Manager") {
                    can_close = true;
                }
            }
        }

        if(department === 'LT_T')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }
                

            if(level === "LIGHTING VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" || creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'OS_T')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "OFFICE SPACE VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" || creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'SLS')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "CUSTOMER VALUE DIRECTOR" || level === "STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && creator_level != "CHIEF ADVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT CUSTOMER VALUE DIRECTOR" || level === "ASSISTANT STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "STORE MANAGER")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR CUSTOMER VALUE SUPERVISOR" || level === "CUSTOMER VALUE SUPERVISOR" || level === "JR. ACCOUNT EXECUTIVE" || level === "SR. STORE SALES EXECUTIVE" || level === "STORE SALES EXECUTIVE")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "STORE MANAGER" && 
                   creator_level != "ASSISTANT CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "ASSISTANT STORE MANAGER" && 
                   creator_level != "SENIOR CUSTOMER VALUE SUPERVISOR" && 
                   creator_level != "CUSTOMER VALUE SUPERVISOR" && 
                   creator_level != "JR. ACCOUNT EXECUTIVE" && 
                   creator_level != "SR. STORE SALES EXECUTIVE" && 
                   creator_level != "STORE SALES EXECUTIVE")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'ENG')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "ENGINEERING MANAGER")
            {
                if(creator_level === "ASSISTANT ENGINEERING MANAGER")
                {
                    can_close = true;
                }
            }
        }

        if (creator_id === uid)
            can_close = true;

        return can_close;
    }


    var calendarT1 = document.getElementById('task_calendar');
    var calendar_task;

    $(document).on("click", "#btn_arrange", function () {

        $('#meeting').show();
        $('#tasks').hide();
    });

    $(document).on("click", "#btn_view", function () {

        //刪除當前在日曆上的所有任務資訊
        calendar_task.removeAllEvents();

        //從資料庫中取出符合當前條件的任務

        let temp = [];
        //將符合條件的任務加入到日曆中
        // task status = Pending，則該任務顏色為 gray
        // task status = Close，則該任務顏色為 green
        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
        var token = localStorage.getItem('token');

        localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('uid', 1);

        $.ajax({
            url: "api/project03_other_task_calendar_dep",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                //console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        //console.log(obj[i]);

                        var obj_meeting = {
                            id: obj[i].id,
                            title: obj[i].title,
                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                            backgroundColor: obj[i].color,
                            borderColor: obj[i].color,
                            extendedProps: {
                                create_id: obj[i].create_id,
                                category: obj[i].category,
                                level: obj[i].level,
                                task_status: obj[i].task_status,
                                stage_id: obj[i].stage_id,
                            },
                        };

                        temp.push(obj_meeting);
                    }

                    if (arrayLength > 0) {
                        my_level = obj[0].my_l;
                        my_id = obj[0].my_i;
                        my_department = obj[0].my_d;
                    }
                }


                calendar_task.addEventSource(temp);

            }
        });

        $('#meeting').hide();
        $('#tasks').show();
    });

    document.addEventListener('DOMContentLoaded', function () {

        let clickCnt = 0;
        let my_id = 0;
        let my_level = 0;
        let my_department = "";

        let event_array_task = [];
        //將Task從資料庫中加入array
        //需要讀出task的 (1)專案名稱 (2)task名稱 (3) task 的due date (4) task 所在的 project03_other頁面網址 (5) task 在日曆中的顏色 (6) task 的 creator
        //event的對應格式請參考下方的events範例

        /* 會議加入array的格式如下： */
        var token = localStorage.getItem('token');

        localStorage.getItem('token');
        var form_Data = new FormData();
        form_Data.append('jwt', token);
        form_Data.append('uid', 1);

        $.ajax({
            url: "api/project03_other_task_calendar_dep",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                //console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        //console.log(obj[i]);

                        var obj_meeting = {
                            id: obj[i].id,
                            title: obj[i].title,
                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                            backgroundColor: obj[i].color,
                            borderColor: obj[i].color,
                            extendedProps: {
                                create_id: obj[i].create_id,
                                category: obj[i].category,
                                level: obj[i].level,
                                task_status: obj[i].task_status,
                                stage_id: obj[i].stage_id,
                            },
                        };

                        event_array_task.push(obj_meeting);
                    }

                    if (arrayLength > 0) {
                        my_level = obj[0].my_l;
                        my_id = obj[0].my_i;
                        my_department = obj[0].my_d;
                    }
                }

                calendar_task = new FullCalendar.Calendar(calendarT1, {

                    plugins: ['dayGrid'],
                    timeZone: 'UTC',
                    defaultView: 'dayGridMonth',

                    contentHeight: 'auto',

                    titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        month: '2-digit',
                        year: 'numeric',
                        day: '2-digit'
                    },

                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'individual,admin,design,lighting,furniture,sls,eng,tw,overall',
                    },

                    //Individual按鈕：只顯示出Creator、Assignee或Collaborator是當前使用者的task在日曆上
                    //Lighting按鈕：只顯示出專案的category為Lighting的task在日曆上
                    //Office Systems按鈕：只顯示出專案的category為Office Systems的task在日曆上
                    //all按鈕：顯示出所有的task在日曆上
                    customButtons: {
                        individual: {
                            text: 'Individual',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('uid', 1);

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);
                                            }

                                            if (arrayLength > 0) {
                                                my_level = obj[0].my_l;
                                                my_id = obj[0].my_i;
                                                my_department = obj[0].my_d;
                                            }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        admin: {
                            text: 'AD',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'ad');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/task_management_AD?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);

                                                if (arrayLength > 0) {
                                                    my_level = obj[0].my_l;
                                                    my_id = obj[0].my_i;
                                                    my_department = obj[0].my_d;
                                                }
                                            }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        design: {
                            text: 'DS',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'ds');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/task_management_DS?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);

                                                if (arrayLength > 0) {
                                                    my_level = obj[0].my_l;
                                                    my_id = obj[0].my_i;
                                                    my_department = obj[0].my_d;
                                                }
                                            }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        lighting: {
                            text: 'LT',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'lt');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };

                                                event_array_task = temp;
                                                temp.push(obj_meeting);
                                            }

                                            if (arrayLength > 0) {
                                                    my_level = obj[0].my_l;
                                                    my_id = obj[0].my_i;
                                                    my_department = obj[0].my_d;
                                                }
                                        }


                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        furniture: {
                            text: 'OS',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'os');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);
                                            }

                                            if (arrayLength > 0) {
                                                    my_level = obj[0].my_l;
                                                    my_id = obj[0].my_i;
                                                    my_department = obj[0].my_d;
                                                }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        sls: {
                            text: 'SLS',
                            click: function() {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'sls');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function(result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id:obj[i].create_id,
                                                        category:obj[i].category,
                                                        level:obj[i].level,
                                                        task_status:obj[i].task_status,
                                                        stage_id:obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);
                                            }

                                            if(arrayLength > 0)
                                            {
                                                my_level = obj[0].my_l;
                                                my_id = obj[0].my_i;
                                                my_department = obj[0].my_d;
                                            }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        eng: {
                            text: 'ENG',
                            click: function() {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'eng');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function(result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id:obj[i].create_id,
                                                        category:obj[i].category,
                                                        level:obj[i].level,
                                                        task_status:obj[i].task_status,
                                                        stage_id:obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);
                                            }

                                            if(arrayLength > 0)
                                            {
                                                my_level = obj[0].my_l;
                                                my_id = obj[0].my_i;
                                                my_department = obj[0].my_d;
                                            }
                                        }

                                        

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        tw: {
                            text: 'TW',
                            click: function() {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('category', 'tw');

                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function(result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id:obj[i].create_id,
                                                        category:obj[i].category,
                                                        level:obj[i].level,
                                                        task_status:obj[i].task_status,
                                                        stage_id:obj[i].stage_id,
                                                    },
                                                };

                                                temp.push(obj_meeting);
                                            }

                                            if(arrayLength > 0)
                                            {
                                                my_level = obj[0].my_l;
                                                my_id = obj[0].my_i;
                                                my_department = obj[0].my_d;
                                            }
                                        }

                                        

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        overall: {
                            text: 'All',
                            click: function () {

                                //刪除當前在日曆上的所有任務資訊
                                calendar_task.removeAllEvents();

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                                //將符合條件的任務加入到日曆中
                                // task status = Pending，則該任務顏色為 gray
                                // task status = Close，則該任務顏色為 green
                                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();


                                $.ajax({
                                    url: "api/project03_other_task_calendar_dep",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                //console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                    extendedProps: {
                                                        create_id: obj[i].create_id,
                                                        category: obj[i].category,
                                                        level: obj[i].level,
                                                        task_status: obj[i].task_status,
                                                        stage_id: obj[i].stage_id,
                                                    },
                                                };


                                                temp.push(obj_meeting);
                                            }

                                            if (arrayLength > 0) {
                                                    my_level = obj[0].my_l;
                                                    my_id = obj[0].my_i;
                                                    my_department = obj[0].my_d;
                                                }
                                        }

                                        event_array_task = temp;
                                        calendar_task.addEventSource(temp);

                                    }
                                });
                            }
                        }
                    },


                    events: event_array_task,

                    eventRender: function (info) {

                        //wired listener to handle click counts instead of event type
                        info.el.addEventListener('click', function () {
                            clickCnt++;
                            if (clickCnt === 1) {
                                oneClickTimer = setTimeout(function() {
                                            clickCnt = 0;
                                            if(info.event.extendedProps.category == 'AD')
                                                window.open('https://feliix.myvnc.com/task_management_AD?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'DS')
                                                window.open('https://feliix.myvnc.com/task_management_DS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'LT_T')
                                                window.open('https://feliix.myvnc.com/task_management_LT?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'OS_T')
                                                window.open('https://feliix.myvnc.com/task_management_OS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'SLS')
                                                window.open('https://feliix.myvnc.com/task_management_SLS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'ENG')
                                                window.open('https://feliix.myvnc.com/task_management_SVC?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'C')
                                                window.open('https://feliix.myvnc.com/project03_client_v2?sid=' + info.event.extendedProps.stage_id, "_blank");
                                            else
                                                window.open('https://feliix.myvnc.com/project03_other?sid=' + info.event.extendedProps.stage_id, "_blank");
                                        }, 400);

                            } else if (clickCnt === 2) {
                                clearTimeout(oneClickTimer);
                                clickCnt = 0;

                                if (info.event.extendedProps.task_status != 2) {
                                    if (CanClose(my_id, info.event.extendedProps.create_id, my_level, info.event.extendedProps.level, info.event.extendedProps.category)) {
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();
                                        form_Data.append('id', info.event.id);
                                        form_Data.append('category', info.event.extendedProps.category);

                                        let _info = info;

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_close_task",
                                            type: "POST",
                                            contentType: 'multipart/form-data',
                                            processData: false,
                                            contentType: false,
                                            data: form_Data,

                                            success: function (result) {
                                                //console.log(result);

                                                for (let i = 0; i < event_array_task.length; i++) {
                                                    if (event_array_task[i].id === _info.event.id && event_array_task[i].extendedProps.category === _info.event.extendedProps.category) {
                                                        event_array_task[i].backgroundColor = 'green';
                                                        event_array_task[i].borderColor = 'green';
                                                        event_array_task[i].extendedProps.task_status = 2;

                                                        calendar_task.removeAllEvents();
                                                        calendar_task.addEventSource(event_array_task);
                                                    }
                                                }

                                            }
                                        });
                                    } else
                                        console.log(info.event.extendedProps);
                                }

                            }
                        });
                    },


                    editable: false,
                });

                calendar_task.render();
            },
        });


        let calendarEl = document.getElementById('calendar');

        //##從資料庫中撈出已存在的會議，以便後續初始化到日曆中
        //SELECT ID, meeting_data FROM table_name;

        /* 會議加入array的格式如下：
        var event = {
          id: 資料庫中的會議 id,
          title: 資料庫中的 meeting_data.title,
          start: 資料庫中的 meeting_data.start,
          end: 資料庫中的 meeting_data.end,
          description: 資料庫中的 meeting_data
        };
        event_array.push(event);
       */

        /*
        var event = 
            {
      title: '昨天的活動',
      start: moment().subtract(1, 'days').format('YYYY-MM-DD'),
      end: moment().add(14, 'days').format('YYYY-MM-DD'),
      color: 'lightBlue'
    }; */

        let _app1 = app1;

        let event_array = [];
        /* 會議加入array的格式如下： */
        var token = localStorage.getItem('token');

        localStorage.getItem('token');
        var form_Data = new FormData();
        form_Data.append('jwt', token);
        form_Data.append('action', 1);

        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if (obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for (var j = 0; j < obj[i].attach.length; j++) {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if (attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach: attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: obj[i].id,
                            title: title,
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        event_array.push(obj_meeting);
                    }
                }

                //初始化 fullcalendar 物件
                calendar = new FullCalendar.Calendar(calendarEl, {

                    plugins: ['dayGrid'],
                    timeZone: 'UTC',
                    defaultView: 'dayGridMonth',

                    contentHeight: 'auto',

                    titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        month: '2-digit',
                        year: 'numeric',
                        day: '2-digit'
                    },

                    header: {
                        left: 'prev,next addEventButton',
                        center: 'title',
                        right: 'individual,overall dayGridMonth,timeGridWeek'
                    },

                    //Add Meeting被點擊的方法
                    customButtons: {
                        individual: {
                            text: 'Individual',
                            click: function () {
                                calendar.removeAllEvents();
                                hideWindow('#editmeeting-form');

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];

                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('jwt', token);
                                form_Data.append('action', 11);


                                $.ajax({
                                    url: "api/work_calender_meetings",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                console.log(obj[i]);

                                                var title = "";
                                                if (obj[i].project_name.trim() === '')
                                                    title = obj[i].subject.trim();
                                                else
                                                    title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                                                var attach = "";
                                                for (var j = 0; j < obj[i].attach.length; j++) {
                                                    attach += obj[i].attach[j].filename + ",";
                                                }

                                                if (attach !== "")
                                                    attach = attach.slice(0, -1);

                                                var obj_description = {
                                                    title: obj[i].subject.trim(),
                                                    project_name: obj[i].project_name.trim(),
                                                    attendee: obj[i].attendee.trim(),
                                                    items: obj[i].items,
                                                    attach: attach,
                                                    location: obj[i].location.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    content: obj[i].message.trim(),
                                                    creator: obj[i].created_by.trim(),
                                                };

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: title,
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    description: obj_description,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }

                                        event_array = temp;
                                        calendar.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        overall: {
                            text: 'All',
                            click: function () {
                                calendar.removeAllEvents();
                                hideWindow('#editmeeting-form');

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];

                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('jwt', token);
                                form_Data.append('action', 1);

                                $.ajax({
                                    url: "api/work_calender_meetings",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function (result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                console.log(obj[i]);

                                                var title = "";
                                                if (obj[i].project_name.trim() === '')
                                                    title = obj[i].subject.trim();
                                                else
                                                    title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                                                var attach = "";
                                                for (var j = 0; j < obj[i].attach.length; j++) {
                                                    attach += obj[i].attach[j].filename + ",";
                                                }

                                                if (attach !== "")
                                                    attach = attach.slice(0, -1);

                                                var obj_description = {
                                                    title: obj[i].subject.trim(),
                                                    project_name: obj[i].project_name.trim(),
                                                    attendee: obj[i].attendee.trim(),
                                                    items: obj[i].items,
                                                    attach: attach,
                                                    location: obj[i].location.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    content: obj[i].message.trim(),
                                                    creator: obj[i].created_by.trim(),
                                                };

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: title,
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    description: obj_description,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }

                                        event_array = temp;
                                        calendar.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        addEventButton: {
                            text: 'Add Meeting',
                            click: function () {
                                $('#addmeeting-form').trigger("reset");
                                $('#editmeeting-form').hide();
                                $('#addmeeting-form').show();

                                _app1.old_attendee = [];
                                _app1.attendee = [];
                                _app1.attachments = [];

                                $('#newProject').val(app.project_name);
                                $('#fileload').val('');
                                $('#sc_product_files').empty();
                                $('#newProject').attr("placeholder", app.project_name);

                            }
                        },

                    },


                    //日曆上meeting被點擊的方法
                    eventClick: function (info) {
                        $('#editmeeting-form').trigger("reset");
                        $('#addmeeting-form').hide();
                        $('#editmeeting-form > fieldset').prop('disabled', true);
                        $("#oldAttendee").addClass("select_disabled");
                        $('#sc_product_files').empty();
                        _app1.attachments = [];

                        //取得點擊的meeting資訊並載入表單
                        eventObj = info.event;
                        var obj_meeting = eventObj.extendedProps.description;

                        if (obj_meeting === undefined)
                            return;

                        $("#oldSubject").val(obj_meeting.title);
                        $("#oldProject").val(obj_meeting.project_name);
                        $('#oldProject').attr("placeholder", obj_meeting.project_name);
                        $("#oldCreator").val(info.event.extendedProps.description.creator);
                        $("#oldAttendee").val(info.event.extendedProps.description.items);
                        $("#oldLocation").val(info.event.extendedProps.description.location);
                        _app1.old_attendee = info.event.extendedProps.description.items;
                        $("#oldDate").val(obj_meeting.start.split("T")[0]);
                        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
                        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
                        $("#oldContent").val(obj_meeting.content);

                        var container = $("#sc_product_files_old");
                        container.empty();

                        if (obj_meeting.attach !== "") {
                            var files = obj_meeting.attach.split(",");
                            files.forEach((element) => {
                                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                                    '<label for="' + element + '">' +
                                    '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' +
                                    '</label>' +
                                    '</div>';

                                $(elm).appendTo(container);
                            });
                        }

                        //設定出現和隱藏按鈕，和出現視窗
                        $("#btn_close").show();
                        $("#btn_delete").show();
                        $("#btn_edit").show();
                        $("#btn_cancel").hide();
                        $("#btn_save").hide();
                        $("#editmeeting-form").show();

                    },

                    editable: false,
                    events: event_array

                });

                calendar.render();
                $("#meeting").hide();
                $("#meeting").css({"visibility": "visible"});
                $("#tasks").hide();
                $("#tasks").css({"visibility": "visible"});

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });

    });


    $(document).on("click", "#btn_edit", function () {

        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editmeeting-form > fieldset').prop('disabled', false);
        $("#oldCreator").prop('disabled', true);

        $("#oldAttendee").removeClass("select_disabled");

        //$("oldAttendee").prop('disabled', false);
        var file_elements = document.getElementsByName("file_elements_old");

        var item = 0;
        for (let i = 0; i < file_elements.length; i++) {
            file_elements[i].disabled = false;

        }


        //按鈕也會改變
        $("#btn_close").hide();
        $("#btn_delete").hide();
        $("#btn_edit").hide();
        $("#btn_cancel").show();
        $("#btn_save").show();

    });


    $(document).on("click", "#btn_cancel", function () {

        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject").val(obj_meeting.title);
        $("#oldCreator").val(obj_meeting.creator);
        $("#oldProject").val(obj_meeting.project_name);
        $("#oldAttendee").val(obj_meeting.attendee);
        $("#oldLocation").val(obj_meeting.location);
        $("#oldDate").val(obj_meeting.start.split("T")[0]);
        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
        $("#oldContent").val(obj_meeting.content);
        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_save", function () {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate").val() + " " + $("#oldStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate").val() + " " + $("#oldEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        if ($("#oldDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#oldEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#oldStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#oldSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.old_attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#oldContent").val().trim() === '') {
            app1.warning('Please enter content!');
            return;
        }


        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        //$("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");


        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;
        var id = eventObj.id;

        var file_elements = document.getElementsByName("file_elements_old");

        var attach = "";
        var remove = "";

        token = localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject").val().trim());
        form_Data.append('project_name', app.project_name);
        form_Data.append('message', $("#oldContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('location', $("#oldLocation").val());
        form_Data.append('start_time', $("#oldDate").val() + "T" + $("#oldStartTime").val());
        form_Data.append('end_time', $("#oldDate").val() + "T" + $("#oldEndTime").val());
        form_Data.append('is_enabled', true);

        var item = 0;
        for (let i = 0; i < file_elements.length; i++) {
            if (file_elements[i].checked) {
                attach += file_elements[i].value + ",";
                for (var j = 0; j < app1.attachments.length; j++) {
                    let file = app1.attachments[j];
                    if (file.name === file_elements[i].value) {
                        form_Data.append('files[' + item++ + ']', file);
                        break;
                    }
                }
            } else {
                remove += "'" + file_elements[i].value + "',";
            }
        }

        if (attach !== "")
            attach = attach.slice(0, -1);

        if (remove !== "")
            remove = remove.slice(0, -1);

        form_Data.append('remove', remove);

        var _app1 = app1;
        var _app = app;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);

                //##寄送通知信件給會議參與者,告知修改後訊息
                _app1.notify_mail(id, 2);

                //##修改後的內容 update到資料庫
                var obj_meeting = {
                    title: $("#oldSubject").val().trim(),
                    project_name: _app.project_name,
                    attendee: names.toString().trim(),
                    items: _app1.old_attendee,
                    start: $("#oldDate").val() + "T" + $("#oldStartTime").val(),
                    end: $("#oldDate").val() + "T" + $("#oldEndTime").val(),
                    content: $("#oldContent").val(),
                    attach: attach,
                    //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
                    creator: "<?php echo $GLOBALS['username'] ?>",
                    location: $("#oldLocation").val(),
                };
                $("#oldCreator").val(obj_meeting.creator);

                var title = $("#oldSubject").val().trim();
                if (_app.project_name !== "")
                    title = '[ ' + _app.project_name + ' ] ' + $("#oldSubject").val().trim();

                //把修改後的會議資訊 update 到日曆上
                eventObj.setStart(obj_meeting.start);
                eventObj.setEnd(obj_meeting.end);
                eventObj.setProp("title", title);
                eventObj.setExtendedProp("description", obj_meeting);

                refreshFileList(attach);
            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });

        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_delete", function () {
        var _app1 = app1;
        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        Swal.fire({
            title: "Delete",
            text: "Are you sure to delete?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {

                $("#editmeeting-form").hide();

                //##從資料庫中刪除該會議
                var id = eventObj.id;

                token = localStorage.getItem('token');
                var form_Data = new FormData();
                form_Data.append('jwt', token);
                form_Data.append('action', 7);

                form_Data.append('id', id);

                //DELETE table_name WHERE ID=id;
                $.ajax({
                    url: "api/work_calender_meetings",
                    type: "POST",
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: form_Data,

                    success: function (result) {
                        console.log(result);


                        //從日曆中刪除該會議
                        eventObj.remove();

                        _app1.notify_mail(id, 3);
                    },

                    // show error message to user
                    error: function (xhr, resp, text) {

                    }
                });


            } else {

            }
        });


    });


    $(document).on("click", "#btn_add", function () {
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        //##任一欄位如果為空則提示欄位不得為空
        if ($("#newDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#newEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#newStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#newSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#newContent").val().trim() === '') {
            app1.warning('Please enter content!');
            return;
        }

        var file_elements = document.getElementsByName("file_elements");

        var attach = "";
        for (let i = 0; i < file_elements.length; i++) {
            if (file_elements[i].checked) {
                attach += file_elements[i].value + ",";
            }
        }

        if (attach !== "")
            attach = attach.slice(0, -1);

        //##obj_meeting 內容寫入資料庫
        //資料庫欄位 (ID, meeting_data)  其中ID為自動計數
        //INSERT table_name (meeting_data) VALUES (obj_meeting)
        //##將該obj_meeting在資料庫給的id返回回來，並設定到前端的id變數
        //##寄送通知信件給會議參與者
        token = localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('action', 2);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#newSubject").val().trim());
        form_Data.append('project_name', app.project_name);
        form_Data.append('message', $("#newContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('location', $("#newLocation").val().trim());
        form_Data.append('start_time', $("#newDate").val() + "T" + $("#newStartTime").val());
        form_Data.append('end_time', $("#newDate").val() + "T" + $("#newEndTime").val());
        form_Data.append('is_enabled', true);
        form_Data.append('created_by', "<?php echo $GLOBALS['username'] ?>");

        var file_elements = document.getElementsByName("file_elements");
        var item = 0;
        for (let i = 0; i < file_elements.length; i++) {
            if (file_elements[i].checked) {
                for (var j = 0; j < app1.attachments.length; j++) {
                    let file = app1.attachments[j];
                    if (file.name === file_elements[i].value) {
                        form_Data.append('files[' + item++ + ']', file);
                        break;
                    }
                }
            }

        }

        var _app1 = app1;
        var _app = app;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (response) {
                var obj = JSON.parse(response);

                //##寄送通知信件給會議參與者,告知修改後訊息
                _app1.notify_mail(obj.id, 1);

                var title = '[ ' + _app.project_name + ' ] ' + $("#newSubject").val().trim();

                //把新增會議 呈現於日曆上
                if (obj.id != 0) {
                    var obj_meeting = {
                        id: obj.id,
                        title: $("#newSubject").val().trim(),
                        project_name: _app.project_name,
                        attendee: names.toString().trim(),
                        items: _app1.attendee,
                        start: $("#newDate").val() + "T" + $("#newStartTime").val(),
                        end: $("#newDate").val() + "T" + $("#newEndTime").val(),
                        location: $("#newLocation").val(),
                        content: $("#newContent").val(),
                        attach: attach,
                        //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
                        creator: "<?php echo $GLOBALS['username'] ?>"
                    };

                    calendar.addEvent({
                        id: obj.id,
                        title: title,
                        start: obj_meeting.start,
                        end: obj_meeting.end,
                        description: obj_meeting
                    });
                }

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });

        $("#addmeeting-form").hide();

    });


    function hideWindow(target) {

        $(target).hide();

        if (target == "#meeting") {
            $("#addmeeting-form").hide();
            $("#editmeeting-form").hide();
        }

    }

    function onChangeFileUpload(target) {

        var fileTarget = $("#fileload");
        var container = $("#sc_product_files");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements" value="' + fileTarget[0].files[i].name + '" checked>' +
                    '<label for="' + fileTarget[0].files[i].name + '">' +
                    '<a>' + fileTarget[0].files[i].name + '</a>' +
                    '</label>' +
                    '</div>';

                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            } else {
                fileTarget[0].value = "";
            }
        }
    }

    function refreshFileList(attach) {
        $('#sc_product_files_old').empty();

        var container = $("#sc_product_files_old");

        if (attach !== "") {
            var files = attach.split(",");
            files.forEach((element) => {
                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                    '<label for="' + element + '">' +
                    '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' +
                    '</label>' +
                    '</div>';

                $(elm).appendTo(container);
            });
        }
    }

    function onChangeFileUploadOld(target) {

        var fileTarget = $("#fileload_old");
        var container = $("#sc_product_files_old");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements_old" value="' + fileTarget[0].files[i].name + '" checked>' +
                    '<label for="' + fileTarget[0].files[i].name + '">' +
                    '<a>' + fileTarget[0].files[i].name + '</a>' +
                    '</label>' +
                    '</div>';

                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            } else {
                fileTarget[0].value = "";
            }
        }
    }
</script>

<script defer src="js/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/exif-js.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script type="text/javascript" src="js/project03_ameeting.js" defer></script>
<script defer src="js/a076d05399.js"></script>


</html>