<?php

 date_default_timezone_set('Asia/Taipei');
 $date = date('d');
 $show0 = false;

if($date % 2 == 0)
    $show0 = true;
?>

<?php include 'check.php';?>
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
    <title>Knowledge Management</title>
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
    </script>

    <style>
        body.primary header > .headerbox {
            background-color: #006BA6;
        }

        .container{
            width: 92vw;
            min-height: calc( 100vh - 150px);
            margin: 30px auto 0;
            background-color: #EBEBEB;
        }

        .container ul{
            width: 100%;
            display: flex;
        }

        .container ul li{
            width: 25%;
            padding: 15px 10px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container ul.head li{
            background-color: #7ACCC8;
            font-weight: 500;
        }

        .container ul li{
            border-bottom: 2px solid black;
            border-right: 2px solid black;
        }

        .container ul:nth-of-type(1) li{
            border-top: 1px solid black;
        }

        .container ul li:first-of-type{
            border-left: 1px solid black;
        }

        .container ul li:last-of-type{
            border-right: 1px solid black;
        }

        .container ul:last-of-type li{
            border-bottom: 1px solid black;
        }

        .container ul li:last-of-type i {
            font-size: 24px;
            margin: 0 4px;
            cursor: pointer;
        }

        .container ul li:last-of-type a {
            font-size: 24px;
            margin: 0 4px;
            cursor: pointer;
        }

    </style>

</head>

<body class="primary">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent" style="text-align: center;" id="app" >
        <!-- mainContent為動態內容包覆的內容區塊 -->

        <div class="container">

            <ul class="head">
                <li>TITLE</li>
                <li>CREATED ON</li>
                <li>LAST UPDATE</li>
                <li>ACTIONS</li>
            </ul>

            <ul v-for='(receive_record, index) in displayedPosts'>
                <li>{{ receive_record.title }}</li>
                <li>{{ receive_record.created_at }} {{ receive_record.created_by }}</li>
                <li>{{ receive_record.updated_at }} {{ receive_record.updated_by }}</li>
                <li>
                    <a class="fas fa-edit" :href="'knowledge_add?id=' + receive_record.id" target="_blank"></a>
                    <i class="fas fa-trash" @click="deleteRow(receive_record)"></i>
                </li>
            </ul>

        

        </div>


    </div>


</div>

</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/knowledge_mgt.js"></script>
</html>
