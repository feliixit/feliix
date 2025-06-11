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

$username = $decoded->data->username;
$position = $decoded->data->position;
$department = $decoded->data->department;

// 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
$test_manager = $decoded->data->test_manager;

}
catch (Exception $e){

header( 'location:index' );
}

$access6 = false;

// QOUTE AND PAYMENT Management
if(trim(strtoupper($department)) == 'SALES')
{
if(trim(strtoupper($position)) == 'JR. ACCOUNT EXECUTIVE'
|| trim(strtoupper($position)) == 'CUSTOMER VALUE SUPERVISOR'
|| trim(strtoupper($position)) == 'SENIOR CUSTOMER VALUE SUPERVISOR'
|| trim(strtoupper($position)) == 'ASSISTANT CUSTOMER VALUE DIRECTOR'
|| trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'LIGHTING')
{
if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'OFFICE')
{
if(trim(strtoupper($position)) == 'ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'DESIGN')
{
if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'ENGINEERING')
{
if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'ADMIN')
{
if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'TW')
{
if(trim(strtoupper($position)) == 'SUPPLY CHAIN MANAGER')
{
$access6 = true;
}
}

if(trim($department) == '')
{
if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
{
$access6 = true;
}
}

if($user_id == 1 || $user_id == 99 || $user_id == 41 )
$access6 = true;

if($access6 == false)
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
    <title>Project Grouping</title>
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

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
            //toggleme($('.list_function .new_function a.filter'),$('.list_function .dialog.A'),'show');
            //toggleme($('.list_function .new_function a.sort'),$('.list_function .dialog.B'),'show');

            dialogshow($('.list_function .new_function a.filter'), $('.list_function .dialog.A'));
            dialogshow($('.list_function .new_function a.sort'), $('.list_function .dialog.B'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })
    </script>

    <style>
        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.fourth header nav a, body.fourth header nav a:link{
            color: #000;
        }

        body.fourth header nav a:hover{
            color: #333;
        }

        body.fourth header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.fourth header nav ul.info{
            margin-bottom: 0;
        }

        body.fourth header nav ul.info b{
            font-weight: bold;
        }

        .tableframe .tablebox.lv1 a, a:link {
            color: #1e6ba8;
            display: inline-block;
        }

        .tableframe .tablebox.lv1 li {
            min-width: auto;
            color: #14456c;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(2) {
            min-width: 150px;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
        }

        .heading-and-btn {
            border-bottom: 2px solid #C2C2C2;
            padding: 0 20px 10px;
        }

        .heading-and-btn ul {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: none;
            background-color: #FFFFFF;
        }

        .heading-and-btn ul li:nth-of-type(1) {
            text-align: left;
            flex-grow: 1;
        }

        .heading-and-btn ul li:nth-of-type(2) {
            width: 300px;
            text-align: center;
            flex-grow: 0;
            flex-shrink: 0;
        }

        .heading-and-btn ul li input {
            display: inline-block;
            width: 90%;
            margin-left: 10px;
            border: 2px solid #C2C2C2;
        }

        .region {
            margin: 20px 10px 40px;
            padding: 20px 15px 15px;
            border: 2px solid #C2C2C2;
            border-radius: 10px;
            position: relative;
        }

        .region span.heading {
            display: inline-block;
            position: absolute;
            top: -17px;
            background-color: white;
            padding: 0 10px;
            font-size: 20px;
            font-weight: 500;
        }

        .region .btnbox{
            padding: 10px;
        }

        a.btn.small {
            color: #FFF !important;
            border: none !important;
            font-weight: 500;
            padding: 5px 20px;
            margin: 0 0 0 10px !important;
            min-width: 82px !important;
            text-align: center;
            font-size: 16px;
        }

    </style>

</head>

<body class="fourth">

<div id="app" class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent">

        <div class="heading-and-btn">

            <ul>
                <li>
                    <h4>Add/Edit Project Group</h4>
                </li>
            </ul>

            <ul>
                <li>
                    Group Name:
                    <input type="text" class="form-control" v-model="project_group">
                </li>

                <li>
                    <a class="btn small green" v-if="!is_editing" @click="add_group">Add</a>
                    <a class="btn small" v-if="is_editing" @click="cancel">Cancel</a>
                    <a class="btn small green" v-if="is_editing" @click="save_group">Save</a>
                </li>
            </ul>

        </div>

        <div class="block">
            <div class="region" v-for="(item, index) in receive_records">
                <span class="heading">{{ item.project_group }}</span>

                <div class="tableframe">
                    <div class="tablebox lv1">
                        <ul class="head">
                            <li>Project Category</li>
                            <li>Project Name</li>
                            <li>Status</li>
                            <li>Project Creator</li>
                            <li>Execution Period</li>
                        </ul>
                        <ul v-for='(it, index) in item.detail'>
                            <li>{{ it.category }}</li>
                            <li><a :href="'project02?p=' + it.id" target="_blank">{{
                                it.project_name
                                }}</a></li>
                            <li>{{ it.project_status }}</li>
                            <li>{{ it.username }}</li>
                            <li>{{ it.created_at }} ~ {{ it.updated_at }}</li>
                        </ul>
                    </div>
                </div>

                <div class="btnbox">
                    <a class="btn small" @click="delete_me(item.id)">Delete</a>
                    <a class="btn small green" @click="rename(item.id)">Rename</a>
                </div>

            </div>

        </div>


    </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/project_grouping.js"></script>
<script src="js/a076d05399.js"></script>


</html>