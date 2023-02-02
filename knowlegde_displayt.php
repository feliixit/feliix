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

        .container {
            width: 92vw;
            margin: 4vh auto;
            background-color: #EBEBEB;
            display: flex;
            align-items: stretch;
            padding: 25px 2.5vw;
            justify-content: space-evenly;
            flex-wrap: wrap;
        }

        .itembox {
            width: 320px;
            height: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 25px 5px;
        }

        .itembox li:nth-of-type(1) {
            width: 220px;
            height: 290px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .itembox li:nth-of-type(1) img {
            width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .itembox li:nth-of-type(2) {
            color: #8B8B89;
            padding: 15px 0 0;
            font-size: 12px;
            font-weight: 400;
            margin-bottom: -3px;
        }

        .itembox li:nth-of-type(3) {
            font-weight: 500;
            font-size: 16px;
        }

        .itembox li:nth-of-type(4) {
            color: #8B8B89;
            font-size: 12px;
            font-weight: 400;
            padding: 7px 0 0;
        }


    </style>

</head>

<body class="primary">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent" style="text-align: center;" id="app">
        <!-- mainContent為動態內容包覆的內容區塊 -->

        <div class="container">

            <!-- 利用迴圈 套用 ul=itembox 這個結構，來建立出每一則知識的區塊 -->
            <ul class="itembox">
                <li style="background-color: #0000FF88;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    "10 Years with Hayo Miyazaki"
                </li>

                <li>
                    <span class="category">documentary, film, animation</span>
                    <span> // </span>
                    <span class="duration">1-hr watch</span>
                </li>
            </ul>


            <ul class="itembox">
                <li style="background-color: #00FFFF88;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    2022 iPhone Photogrphy Awards winner
                </li>

                <li>
                    <span class="category">photography, inspiration, travel</span>
                    <span> // </span>
                    <span class="duration">5-min read</span>
                </li>
            </ul>


            <ul class="itembox">
                <li style="background-color: #00FF0088;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    Instruction Manual to Quotation Creation and Management Interface (ver.2)
                </li>

                <li>
                    <span class="category">documentary, film, animation</span>
                    <span> // </span>
                    <span class="duration">1-hr watch</span>
                </li>
            </ul>

            <ul class="itembox">
                <li style="background-color: #FFFF0088;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    "10 Years with Hayo Miyazaki"
                </li>

                <li>
                    <span class="category">documentary, film, animation</span>
                    <span> // </span>
                    <span class="duration">1-hr watch</span>
                </li>
            </ul>

            <ul class="itembox">
                <li style="background-color: #FF7F0088;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    "10 Years with Hayo Miyazaki"
                </li>

                <li>
                    <span class="category">documentary, film, animation</span>
                    <span> // </span>
                    <span class="duration">1-hr watch</span>
                </li>
            </ul>

            <ul class="itembox">
                <li style="background-color: #FF000088;">
                    <img src="test.png">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    "10 Years with Hayo Miyazaki"
                </li>

                <li>
                    <span class="category">documentary, film, animation</span>
                    <span> // </span>
                    <span class="duration">1-hr watch</span>
                </li>
            </ul>

        </div>


    </div>


</div>

</body>

<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/knowledge_display.js"></script>

</html>
