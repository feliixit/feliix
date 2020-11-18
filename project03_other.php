<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if (!isset($jwt)) {
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
catch (Exception $e) {

    header('location:index');
}

?>

<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

    <!-- SEO -->
    <title>FELIIX template pc</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all" />
    <meta name="author" content="FELIIX" />

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX" />
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website" />
    <meta property="og:description" content="FELIIX" />
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="js/fancyBox/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />
    <link rel="stylesheet" href="css/vue-select.css" type="text/css">

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/fullcalendar@5.1.0/main.min.css">
    <script type="text/javascript" src="https://unpkg.com/fullcalendar@5.1.0/main.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <script type="text/javascript" src="js/fancyBox/jquery.fancybox.min.js"></script>

    <script>
        $(function() {
            $('header').load('include/header.php');
            //
            dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
            dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
            dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
            dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
            dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
            dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
            dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function() {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function() {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>


    <style>
        .meetingform-buttons {
            display: flex;
            justify-content: center;

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

        @media all and (-ms-high-contrast:none),
        (-ms-high-contrast:active) {
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

        .swal2-container.swal2-bottom-end>:first-child,
        .swal2-container.swal2-bottom-left>:first-child,
        .swal2-container.swal2-bottom-right>:first-child,
        .swal2-container.swal2-bottom-start>:first-child,
        .swal2-container.swal2-bottom>:first-child {
            margin-top: auto
        }

        .swal2-container.swal2-grow-fullscreen>.swal2-modal {
            display: flex !important;
            flex: 1;
            align-self: stretch;
            justify-content: center
        }

        .swal2-container.swal2-grow-row>.swal2-modal {
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

        .swal2-container.swal2-grow-column>.swal2-modal {
            display: flex !important;
            flex: 1;
            align-content: center;
            justify-content: center
        }

        .swal2-container.swal2-no-transition {
            transition: none !important
        }

        .swal2-container:not(.swal2-top):not(.swal2-top-start):not(.swal2-top-end):not(.swal2-top-left):not(.swal2-top-right):not(.swal2-center-start):not(.swal2-center-end):not(.swal2-center-left):not(.swal2-center-right):not(.swal2-bottom):not(.swal2-bottom-start):not(.swal2-bottom-end):not(.swal2-bottom-left):not(.swal2-bottom-right):not(.swal2-grow-fullscreen)>.swal2-modal {
            margin: auto
        }

        @media all and (-ms-high-contrast:none),
        (-ms-high-contrast:active) {
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

        .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step {
            background: #add8e6;
            color: #fff
        }

        .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step-line {
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

        @media all and (-ms-high-contrast:none),
        (-ms-high-contrast:active) {
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

        body.swal2-no-backdrop .swal2-container>.swal2-modal {
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

            body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown)>[aria-hidden=true] {
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
            pointer-events:none;
            color: #bfcbd9;
            cursor: not-allowed;
            background-image: none;
            background-color: #eef1f6;
            border-color: #d1dbe5;   
            
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
                <div class="block">
                    <!-- add red -->
                    <div class="popupblock">
                        <a id="dialog_a1" class="add red"></a>
                        <!-- dialog -->
                        <div id="add_a1" class="dialog r-add add">
                            <h6>Add/Duplicate Task</h6>
                            <div class="tablebox s1">
                                <ul>
                                    <li class="head">Operation Type:</li>
                                    <li style="padding-right: 0;">
                                        <select name="" id="opType3">
                                            <option value="add">Add New Task</option>
                                            <option value="dup">Duplicate Existing Task</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 add">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" v-model="title"></dd>
                                </dl>
                                <dl>
                                    <dt>Priority:</dt>
                                    <dd>
                                        <select v-model="priority">
                                            <option value="1">No Priority</option>
                                            <option value="2">Low</option>
                                            <option value="3">Normal</option>
                                            <option value="4">High</option>
                                            <option value="5">Urgent</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="assignee" :id="assignee" :options="users" attach chips label="username" multiple></v-select>

                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Collaborator:</dt>
                                    <dd>
                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="collaborator" :id="collaborator" :options="users" attach chips label="username" multiple></v-select>

                                        </div>

                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Due Date:</dt>
                                    <dd>
                                        <div class="browser_group"><input type="date" style="width: 100%!important;" v-model="due_date"></div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Task Detail:</dt>
                                    <dd><textarea placeholder="" v-model="detail"></textarea></dd>
                                </dl>
                                <dl>
                                    <dd style="display: flex; justify-content: flex_start;">
                                        <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" name="file" value placeholder="choose file" ref="file" v-show="canSub" @change="changeFile()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>

                                    <dd>
                                        <div class="browser_group">
                                            <div class="pad">
                                                <div class="file-list">
                                                    <div class="file-item" v-for="(item,index) in fileArray" :key="index">
                                                        <p>
                                                            {{item.name}}
                                                            <span @click="deleteFile(index)" v-show="item.progress==0" class="upload-delete"><i class="fas fa-backspace"></i>
                                                            </span>
                                                        </p>
                                                        <div class="progress-container" v-show="item.progress!=0">
                                                            <div class="progress-wrapper">
                                                                <div class="progress-progress" :style="'width:'+item.progress*100+'%'"></div>
                                                            </div>
                                                            <div class="progress-rate">
                                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                    </dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="task_clear">Cancel</a>
                                    <a class="btn small green" id="btn_arrange">Calendar</a>
                                    <a class="btn small green" @click="task_create">Create</a>
                                </div>
                            </div>
                            <div class="tablebox s2 dup">
                                <ul>
                                    <li class="head">Target Task:</li>
                                    <li class="mix">
                                        <select v-model="task_id_to_dup">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_dup">Duplicate</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- edit red -->
                    <div class="popupblock">
                        <a id="edit_red" class="edit red"></a>
                        <!-- dialog -->
                        <div id="dialog_red_edit" class="dialog r-edit edit">
                            <h6>Edit/Delete Task:</h6>
                            <div class="tablebox s1">
                                <ul>
                                    <li class="head">Operation Type:</li>
                                    <li style="padding-right: 0;">
                                        <select name="" id="opType">
                                            <option value="edit">Edit Existing Task</option>
                                            <option value="del">Delete Existing Task</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 del">
                                <ul>
                                    <li class="head">Target Task:</li>
                                    <li class="mix">
                                        <select v-model="task_id_to_del">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small" @click="task_del">Delete</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 edit">
                                <ul>
                                    <li class="head">Target Sequence:</li>
                                    <li class="mix">
                                        <select v-model="task_id_to_load">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_load">Load</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 edit">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" v-model="record.title"></dd>
                                </dl>
                                <dl>
                                    <dt>Priority:</dt>
                                    <dd>
                                        <select name="" id="" v-model="record.priority_id">
                                            <option value="1">No Priority</option>
                                            <option value="2">Low</option>
                                            <option value="3">Normal</option>
                                            <option value="4">High</option>
                                            <option value="5">Urgent</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Status:</dt>
                                    <dd>
                                        <select name="" id="" v-model="record.task_status">
                                            <option value="0">Ongoing</option>
                                            <option value="1">Pending</option>
                                            <option value="2">Close</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="record.assignee_id" :id="record.assignee" :options="users" attach chips label="username" v-if="record.assignee_id" multiple></v-select>

                                        </div>

                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Collaborator:</dt>
                                    <dd>

                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="record.collaborator_id" :id="record.collaborator_id" :options="users" attach chips label="username" v-if="record.collaborator_id" multiple></v-select>

                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Due Date:</dt>
                                    <dd>
                                        <div class="browser_group"><input type="date" style="width: 100%!important;" v-model="record.due_date"></div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="record.detail"></textarea></dd>
                                </dl>

                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="filebox">
                                            <a class="attch" v-for="(it,index) in editfileArray" :key="index" @click="deleteEditFile(index)">{{it.name}}</a>
                                        </div>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" :ref="'editfile'" placeholder="choose file" @change="changeEditFile()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>

                                <div class="btnbox">
                                    <a class="btn small" @click="task_edit_clear">Cancel</a>
                                    <a class="btn small green" id="btn_arrange">Calendar</a>
                                    <a class="btn small green" @click="task_edit_create">Save</a>
                                </div>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- add -->
                    <div class="popupblock">
                        <a id="dialog_a1_r" class="add blue"></a>
                        <!-- dialog -->
                        <div id="add_a1_r" class="dialog d-add">
                            <h6>Add Message:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" placeholder="" v-model="title_r"></dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="assignee_r" :id="assignee_r" :options="users" attach chips label="username" multiple></v-select>

                                        </div>

                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="detail_r"></textarea></dd>
                                </dl>
                                <dl>
                                    <dd style="display: flex; justify-content: flex_start;">
                                        <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" name="file_r" value placeholder="choose file" ref="file_r" v-show="canSub_r" @change="changeFile_r()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dd>
                                        <div class="browser_group">

                                            <div class="pad">
                                                <div class="file-list">
                                                    <div class="file-item" v-for="(item,index) in fileArray_r" :key="index">
                                                        <p>
                                                            {{item.name}}
                                                            <span @click="deleteFile_r(index)" v-show="item.progress==0" class="upload-delete"><i class="fas fa-backspace"></i>
                                                            </span>
                                                        </p>
                                                        <div class="progress-container" v-show="item.progress!=0">
                                                            <div class="progress-wrapper">
                                                                <div class="progress-progress" :style="'width:'+item.progress*100+'%'"></div>
                                                            </div>
                                                            <div class="progress-rate">
                                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                    </dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="task_clear_r">Cancel</a>
                                    <a class="btn small green" @click="task_create_r">Create</a>
                                </div>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- edit -->
                    <div class="popupblock">
                        <a id="edit_blue" class="edit blue"></a>
                        <!-- dialog -->
                        <div id="dialog_blue_edit" class="dialog d-edit edit">
                            <h6>Edit/Delete Message:</h6>
                            <div class="tablebox s1">
                                <ul>
                                    <li class="head">Operation Type:</li>
                                    <li style="padding-right: 0;">
                                        <select name="" id="opType2">
                                            <option value="edit">Edit Existing Message</option>
                                            <option value="del">Delete Existing Message</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 del">
                                <ul>
                                    <li class="head">Target Message:</li>
                                    <li class="mix">
                                        <select v-model="task_id_to_del_r">
                                            <option v-for="(it, index) in project03_other_task_r" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small" @click="task_del_r">Delete</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 edit">
                                <ul>
                                    <li class="head">Target Message:</li>
                                    <li class="mix">
                                        <select v-model="task_id_to_load_r">
                                            <option v-for="(it, index) in project03_other_task_r" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_load_r">Load</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 edit">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" placeholder="" v-model="record_r.title"></dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div style="text-align: left;font-size: 12px;">
                                            <v-select v-model="record_r.assignee_r" :id="record_r.assignee_r" :options="users" attach chips label="username" v-if="record_r.assignee_id" multiple></v-select>

                                        </div>

                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="record_r.detail"></textarea></dd>
                                </dl>
                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="filebox">
                                            <a class="attch" v-for="(it,index) in editfileArray_r" :key="index" @click="deleteEditFile_r(index)">{{it.name}}</a>
                                        </div>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" :ref="'editfile_r'" placeholder="choose file" @change="changeEditFile_r()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>

                                <div class="btnbox">
                                    <a class="btn small" @click="task_edit_clear_r">Cancel</a>
                                    <a class="btn small green" @click="task_edit_create_r">Save</a>
                                </div>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- calendar -->
                    <div class="popupblock">
                        <a class="calendar" id="btn_arrange"></a>
                    </div>
                    <!-- tag -->
                    <b class="tag focus">PROJECT</b>
                    <b class="tag">UNDP / Ranee</b>
                </div>
            </div>
            <div class="block left">
                <div class="list_function dialogclear">
                    <!-- Filter -->
                    <div class="filter">
                        <b>Filter:</b>

                        <select name="" id="" v-model="fil_priority">
                            <option value="">Priority</option>
                            <option value="1">No Priority</option>
                            <option value="2">Low</option>
                            <option value="3">Normal</option>
                            <option value="4">High</option>
                            <option value="5">Urgent</option>
                        </select>
                        <select name="" id="" v-model="fil_status">
                            <option value="">Status</option>
                            <option value="0">Ongoing</option>
                            <option value="1">Pending</option>
                            <option value="2">Close</option>

                        </select>
                        <select v-model="fil_due_date">
                            <option v-for="(it, index) in opt_due_date" :value="it.due_date" :key="it.due_date">
                                {{ it.due_date }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-for='(receive_record, index) in project03_other_task' v-if="receive_record.task_status != '-1'">

                    <div class="teskbox dialogclear">
                        <a class="btn small red">{{ receive_record.priority }}</a>
                        <a class="btn small yellow" v-if="receive_record.task_status == '0'">Ongoing</a>
                        <a class="btn small yellow" v-if="receive_record.task_status == '1'">Pending</a>
                        <a class="btn small green" v-if="receive_record.task_status == '2'">Close</a>
                        <b>[Task] {{ receive_record.title }}</b>
                        <!-- <a class="btn small blue right" id="btn_arrange">Arrange Meeting</a> -->
                    </div>
                    <div class="teskbox dialogclear" style="margin-top:-2px !important">
                        <div class="tablebox m01">
                            <ul>
                                <li><b>Creator</b></li>
                                <li><a class="man" :style="'background-image: url(images/man/' +  receive_record.creator_pic  + ');'" :title="receive_record.creator"></a></li>
                            </ul>
                            <ul>
                                <li><b>Assignee</b></li>
                                <li>
                                    <i v-for="item in receive_record.assignee">
                                        <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'" :title="item.username"></a>
                                    </i>

                                </li>
                            </ul>
                            <ul>
                                <li><b>Collaborator</b></li>
                                <li>
                                    <i v-for="item in receive_record.collaborator">
                                        <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'" :title="item.username"></a>
                                    </i>

                                </li>
                            </ul>
                            <ul>
                                <li><b>Due Date</b></li>
                                <li>{{ receive_record.due_date }}</li>
                            </ul>
                            <ul>
                                <li><b>Description</b></li>
                                <li>{{ receive_record.detail }}</li>
                            </ul>
                            <ul>
                                <li><b>Attachments</b></li>
                                <li>
                                    <i v-for="item in receive_record.items">
                                        <a class="attch" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                    </i>
                                </li>

                            </ul>
                        </div>
                    </div>

                    <div class="teskbox scroll">
                        <div class="tableframe">
                            <div class="tablebox m02">
                                <!-- 1 message -->
                                <ul v-for="item in receive_record.message" :class="{ deleted : item.message_status == -1, dialogclear : item.message_status == -1 }">
                                    <li class="dialogclear">
                                        <a class="man" :style="'background-image: url(images/man/' + item.messager_pic + ');'" :title="item.messager"></a>
                                        <i class="info">
                                            <b>{{item.messager}}</b><br>
                                            {{ item.message_time }}<br>
                                            {{ item.message_date }}

                                        </i>
                                    </li>
                                    <li v-if="item.message_status == 0">
                                        <div class="msg">
                                            <div class="msgbox dialogclear">
                                                <p v-if="item.ref_id != 0"><a href="" class="tag_name">@{{ item.ref_name}}</a> {{ item.ref_msg}}</p>
                                                <p>{{ item.message }}</p>
                                                <i v-for="file in item.items">
                                                    <a class="attch" :href="baseURL + file.gcp_name" target="_blank">{{file.filename}}</a>
                                                </i>

                                            </div>
                                            <div class="btnbox">
                                                <a class="btn small green reply r1" :id="'task_reply_btn_' + item.message_id + '_' + item.ref_id" @click="openTaskMsgDlg(item.message_id + '_' + item.ref_id)">Reply</a>
                                                <!-- dialog -->
                                                <div class="dialog reply r1" :id="'task_reply_dlg_' + item.message_id + '_' + item.ref_id">
                                                    <div class="formbox">
                                                        <dl>
                                                            <dd><textarea name="" :ref="'task_reply_msg_' + item.message_id + '_' + item.ref_id" :id="'task_reply_msg_' + item.message_id + '_' + item.ref_id"></textarea></dd>

                                                            <dd>
                                                                <div class="pub-con" ref="bg">
                                                                    <div class="input-zone">
                                                                        <span class="upload-des">choose file</span>
                                                                        <input class="input" type="file" :ref="'file_msg_' + item.message_id + '_' + item.ref_id" placeholder="choose file" @change="changeMsgFile(item.message_id + '_' + item.ref_id)" multiple />
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                            <dd>
                                                                <div class="filebox">
                                                                    <a class="attch" v-for="(it,index) in msgItems(item.message_id + '_' + item.ref_id)" :key="index" @click="deleteMsgFile(item.message_id + '_' + item.ref_id, index)">{{it.name}}</a>
                                                                </div>
                                                            </dd>
                                                            <dd>
                                                                <div class="btnbox">
                                                                    <a class="btn small orange" @click="msg_clear(item.message_id + '_' + item.ref_id)">Cancel</a>
                                                                    <a class="btn small green" @click="msg_create(item.message_id + '_' + item.ref_id, item.message_id)">Save</a>
                                                                </div>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                                <!-- dialog end -->
                                                <a class="btn small yellow" @click="msg_delete(item.message_id, item.ref_id)">Delete</a>
                                            </div>

                                            <div class="msgbox dialogclear" v-for="reply in item.reply">
                                                <p><a href="" class="tag_name">@{{ item.messager}}</a> {{ reply.reply}}</p>
                                                <i v-for="file in reply.items">
                                                    <a class="attch" :href="baseURL + reply.gcp_name" target="_blank">{{reply.filename}}</a>
                                                </i>

                                            </div>
                                        </div>
                                    </li>

                                    <li v-if="item.message_status == -1">
                                        <div class="msg">
                                            <div class="msgbox">
                                                <p>Deleted by <a href="" class="tag_name">@{{ item.updator }}</a> at {{ item.update_date }}</p>
                                            </div>
                                        </div>
                                    </li>

                                </ul>


                            </div>
                        </div>
                        <div class="tablebox lv3c m03 dialogclear">
                            <ul>
                                <li>
                                    <textarea name="" id="" placeholder="Write your comment here" :ref="'comment_task_' + receive_record.task_id"></textarea>
                                    <div class="filebox">
                                        <a class="attch" v-for="(item,index) in taskItems(receive_record.task_id)" :key="index" @click="deleteTaskFile(receive_record.task_id, index)">{{item.name}}</a>

                                    </div>
                                </li>
                                <li>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input class="input" type="file" :ref="'file_task_' + receive_record.task_id" placeholder="choose file" @change="changeTaskFile(receive_record.task_id)" multiple />
                                        </div>
                                        <a class="btn small green" @click="comment_create(receive_record.task_id)">Comment</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>




            </div>


            <div class="block right ">
                <div class="list_function dialogclear">
                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>

                        <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>

                        <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
                    </div>
                </div>
                <div class="teskbox" v-for='(receive_record, index) in displayedStagePosts' :class="{ red : receive_record.task_status == -1, dialogclear : receive_record.task_status == -1 }">
                    <h5>[MESSAGE] {{ receive_record.title }}</h5>
                    <div class="tablebox2">
                        <ul>
                            <li class="teskblock dialogclear">
                                <div class="tablebox m01">
                                    <ul>
                                        <li><b>Creator</b></li>
                                        <li><a class="man" :style="'background-image: url(images/man/' +  receive_record.creator_pic  + ');'" :title="receive_record.creator"></a></li>
                                    </ul>
                                    <ul>
                                        <li><b>Date</b></li>
                                        <li>{{ receive_record.task_date }}</li>
                                    </ul>
                                    <ul>
                                        <li><b>Assignee</b></li>
                                        <li>
                                            <i v-for="item in receive_record.assignee">
                                                <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'" :title="item.username"></a>
                                            </i>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li><b>Description</b></li>
                                        <li>
                                            {{ receive_record.detail }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li><b>Attachments</b></li>
                                        <li>
                                            <i v-for="item in receive_record.items">
                                                <a class="attch" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                            </i>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="teskblock">
                                <div class="tableframe">
                                    <div class="tablebox m02">
                                        <!-- 1 message -->
                                        <ul v-for="item in receive_record.message" :class="{ deleted : item.message_status == -1, dialogclear : item.message_status == -1 }">
                                            <li class="dialogclear">
                                                <a class="man" :style="'background-image: url(images/man/' + item.messager_pic + ');'" :title="item.messager"></a>
                                                <i class="info">
                                                    <b>{{item.messager}}</b><br>
                                                    {{ item.message_time }}<br>
                                                    {{ item.message_date }}
                                                </i>
                                            </li>
                                            <li v-if="item.message_status == 0">
                                                <div class="msg">
                                                    <div class="msgbox dialogclear">
                                                        <p v-if="item.ref_id != 0"><a href="" class="tag_name">@{{ item.ref_name}}</a> {{ item.ref_msg}}</p>
                                                        <p>{{ item.message }}</p>
                                                        <i v-for="file in item.items">
                                                            <a class="attch" :href="baseURL + file.gcp_name" target="_blank">{{file.filename}}</a>
                                                        </i>
                                                    </div>
                                                    <div class="btnbox">
                                                        <a class="btn small green reply r3" :id="'task_reply_btn_r_' + item.message_id + '_' + item.ref_id" @click="openTaskMsgDlg_r(item.message_id + '_' + item.ref_id)">Reply</a>
                                                        <!-- dialog -->
                                                        <div class="dialog reply r3" :id="'task_reply_dlg_r_' + item.message_id + '_' + item.ref_id">
                                                            <div class="formbox">
                                                                <dl>
                                                                    <dd><textarea name="" :ref="'task_reply_msg_r_' + item.message_id + '_' + item.ref_id" :id="'task_reply_msg_r_' + item.message_id + '_' + item.ref_id"></textarea></dd>

                                                                    <dd>
                                                                        <div class="pub-con" ref="bg">
                                                                            <div class="input-zone">
                                                                                <span class="upload-des">choose file</span>
                                                                                <input class="input" type="file" :ref="'file_msg_r_' + item.message_id + '_' + item.ref_id" placeholder="choose file" @change="changeMsgFile_r(item.message_id + '_' + item.ref_id)" multiple />
                                                                            </div>
                                                                        </div>
                                                                    </dd>
                                                                    <dd>
                                                                        <div class="filebox">
                                                                            <a class="attch" v-for="(it,index) in msgItems_r(item.message_id + '_' + item.ref_id)" :key="index" @click="deleteMsgFile_r(item.message_id + '_' + item.ref_id, index)">{{it.name}}</a>
                                                                        </div>
                                                                    </dd>
                                                                    <dd>
                                                                        <div class="btnbox">
                                                                            <a class="btn small orange" @click="msg_clear_r(item.message_id + '_' + item.ref_id)">Cancel</a>
                                                                            <a class="btn small green" @click="msg_create_r(item.message_id + '_' + item.ref_id, item.message_id)">Save</a>
                                                                        </div>
                                                                    </dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                        <!-- dialog end -->
                                                        <a class="btn small yellow" @click="msg_delete_r(item.message_id, item.ref_id)">Delete</a>
                                                    </div>

                                                    <div class="msgbox dialogclear" v-for="reply in item.reply">
                                                        <p><a href="" class="tag_name">@{{ item.messager}}</a> {{ reply.reply}}</p>
                                                        <i v-for="file in reply.items">
                                                            <a class="attch" :href="baseURL + reply.gcp_name" target="_blank">{{reply.filename}}</a>
                                                        </i>

                                                    </div>

                                                </div>
                                            </li>

                                            <li v-if="item.message_status == -1">
                                                <div class="msg">
                                                    <div class="msgbox">
                                                        <p>Deleted by <a href="" class="tag_name">@{{ item.updator }}</a> at {{ item.update_date }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>


                                    </div>
                                </div>
                                <div class="tablebox lv3c m03 dialogclear">
                                    <ul>
                                        <li>
                                            <textarea name="" id="" placeholder="Write your comment here" :ref="'comment_task_r_' + receive_record.task_id"></textarea>
                                            <div class="filebox">
                                                <a class="attch" v-for="(item,index) in taskItems_r(receive_record.task_id)" :key="index" @click="deleteTaskFile_r(receive_record.task_id, index)">{{item.name}}</a>

                                            </div>
                                        </li>
                                        <li>
                                            <div class="pub-con" ref="bg">
                                                <div class="input-zone">
                                                    <span class="upload-des">choose file</span>
                                                    <input class="input" type="file" :ref="'file_task_r_' + receive_record.task_id" placeholder="choose file" @change="changeTaskFile_r(receive_record.task_id)" multiple />
                                                </div>
                                                <a class="btn small green" @click="comment_create_r(receive_record.task_id)">Comment</a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <div id="meeting">

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
                    <label>Attendee:</label>
                    <v-select id="newAttendee" :options="users" attach chips label="username" v-model=attendee multiple></v-select>
                </div>

                <div class="meetingform-item">
                    <label>Time:</label>
                    <input type="date" id="newDate">
                    <input type="time" id="newStartTime">
                    <input type="time" id="newEndTime">
                </div>

                <div class="meetingform-item">
                    <label>Content:</label>
                    <textarea style="flex-grow: 1; resize: none;" rows="2" id="newContent"></textarea>

                </div>

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
                    <label>Creator:</label>
                    <input type="text" style="width: 330px" value="Joyza Jane Julao Semilla at 2020/10/18 15:09" id="oldCreator">
                </div>

                <div class="meetingform-item">
                    <label>Attendee:</label>
                    <v-select id="oldAttendee" :options="users" attach chips label="username" v-model=old_attendee multiple ></v-select>
                </div>

                <div class="meetingform-item">
                    <label>Time:</label>
                    <input type="date" id="oldDate">
                    <input type="time" id="oldStartTime">
                    <input type="time" id="oldEndTime">
                </div>

                <div class="meetingform-item">
                    <label>Content:</label>
                    <textarea style="flex-grow: 1; resize: none;" rows="2" id="oldContent"></textarea>

                </div>

                <div class="meetingform-buttons">
                    <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')" id="btn_close">Close</a>
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
    var eventObj;

    $(document).on("click", "#btn_arrange", function() {


        $('#meeting').show();
    });


    document.addEventListener('DOMContentLoaded', function() {

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

            success: function(result) {
                console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            attendee: obj[i].attendee.trim(),
                            items:obj[i].items,
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),	
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: obj[i].id,
                            title: obj[i].subject.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        event_array.push(obj_meeting);
                    }
                }

                //初始化 fullcalendar 物件
                calendar = new FullCalendar.Calendar(calendarEl, {

                    contentHeight: 'auto',

                    titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        month: '2-digit',
                        year: 'numeric',
                        day: '2-digit'
                    },

                    headerToolbar: {
                        left: 'prev,next addEventButton',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },

                    //Add Meeting被點擊的方法
                    customButtons: {
                        addEventButton: {
                            text: 'Add Meeting',
                            click: function() {
                                $('#addmeeting-form').trigger("reset");
                                $('#editmeeting-form').hide();
                                $('#addmeeting-form').show();
                                
                                _app1.old_attendee = [];
                                _app1.attendee = [];

                            }
                        }
                    },


                    //日曆上meeting被點擊的方法
                    eventClick: function(info) {
                        $('#editmeeting-form').trigger("reset");
                        $('#addmeeting-form').hide();
                        $('#editmeeting-form > fieldset').prop('disabled', true);
                        $("#oldAttendee").addClass("select_disabled");
                        

                        //取得點擊的meeting資訊並載入表單
                        eventObj = info.event;
                        var obj_meeting = eventObj.extendedProps.description;

                        if (obj_meeting === undefined)
                            return;

                        $("#oldSubject").val(obj_meeting.title);
                        $("#oldCreator").val(info.event.extendedProps.description.creator);
                        $("#oldAttendee").val(info.event.extendedProps.description.items);
                        _app1.old_attendee = info.event.extendedProps.description.items;
                        $("#oldDate").val(obj_meeting.start.split("T")[0]);
                        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
                        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
                        $("#oldContent").val(obj_meeting.content);

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

            },

            // show error message to user
            error: function(xhr, resp, text) {

            }
        });


        


    });


    $(document).on("click", "#btn_edit", function() {

        if($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>")
        {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editmeeting-form > fieldset').prop('disabled', false);
        $("#oldCreator").prop('disabled', true);

        $("#oldAttendee").removeClass("select_disabled");
  
        //$("oldAttendee").prop('disabled', false);


        //按鈕也會改變
        $("#btn_close").hide();
        $("#btn_delete").hide();
        $("#btn_edit").hide();
        $("#btn_cancel").show();
        $("#btn_save").show();

    });


    $(document).on("click", "#btn_cancel", function() {

        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject").val(obj_meeting.title);
        $("#oldCreator").val(obj_meeting.creator);
        $("#oldAttendee").val(obj_meeting.attendee);
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

    $(document).on("click", "#btn_save", function() {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate").val() + " " + $("#oldStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate").val() + " " + $("#oldEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

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

        var names = app1.old_attendee.map(function(item) {
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

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        //$("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //##修改後的內容 update到資料庫
        var id = eventObj.id;
        var obj_meeting = {
            title: $("#oldSubject").val().trim(),
            attendee: names.toString().trim(),
            items: app1.old_attendee,
            start: $("#oldDate").val() + "T" + $("#oldStartTime").val(),
            end: $("#oldDate").val() + "T" + $("#oldEndTime").val(),
            content: $("#oldContent").val(),
            //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
            creator: "<?php echo $GLOBALS['username'] ?>"
        };
        $("#oldCreator").val(obj_meeting.creator);

        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;

        token = localStorage.getItem('token');
        var form_Data = new FormData();
      
        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject").val().trim());
        form_Data.append('message', $("#oldContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('start_time', $("#oldDate").val() + "T" + $("#oldStartTime").val());
        form_Data.append('end_time', $("#oldDate").val() + "T" + $("#oldEndTime").val());
        form_Data.append('is_enabled', true);
   
        var _func = app1;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function(result) {
                console.log(result);
           
                _func.notify_mail(id, 2);
            },

            // show error message to user
            error: function(xhr, resp, text) {

            }
        });

        //##寄送通知信件給會議參與者,告知修改後訊息


        //把修改後的會議資訊 update 到日曆上
        eventObj.setStart(obj_meeting.start);
        eventObj.setEnd(obj_meeting.end);
        eventObj.setProp("title", obj_meeting.title);
        eventObj.setExtendedProp("description", obj_meeting);

        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_delete", function() {

        if($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>")
        {
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

                    success: function(result) {
                        console.log(result);
                        
                        app1.notify_mail(id, 3);
                    },

                    // show error message to user
                    error: function(xhr, resp, text) {

                    }
                });


                //從日曆中刪除該會議
                eventObj.remove();
              
            } else {
          
            }
          });

        

    });


    $(document).on("click", "#btn_add", function() {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

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

        var names = app1.attendee.map(function(item) {
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


        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        var obj_meeting = {
            title: $("#newSubject").val().trim(),
            attendee: names.toString().trim(),
            items: app1.attendee,
            start: $("#newDate").val() + "T" + $("#newStartTime").val(),
            end: $("#newDate").val() + "T" + $("#newEndTime").val(),
            content: $("#newContent").val(),
            items:app1.attendee,
            //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
            creator: "<?php echo $GLOBALS['username'] ?>"
        };

        var id = app1.addMeetings($("#newSubject").val().trim(),
            $("#newContent").val(),
            names.toString(),
            $("#newDate").val() + "T" + $("#newStartTime").val(),
            $("#newDate").val() + "T" + $("#newEndTime").val(),
            "<?php echo $GLOBALS['username'] ?>"
        );

        //##obj_meeting 內容寫入資料庫
        //資料庫欄位 (ID, meeting_data)  其中ID為自動計數
        //INSERT table_name (meeting_data) VALUES (obj_meeting)


        //##將該obj_meeting在資料庫給的id返回回來，並設定到前端的id變數



        //##寄送通知信件給會議參與者
        //???


        //把新增會議 呈現於日曆上
        calendar.addEvent({
            id: id,
            title: obj_meeting.title,
            start: obj_meeting.start,
            end: obj_meeting.end,
            description: obj_meeting
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
</script>

<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript" src="js/project03_other.js" defer></script>
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script>


</html>