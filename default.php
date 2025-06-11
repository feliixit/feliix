<?php

 date_default_timezone_set('Asia/Taipei');
 $date = date('d');
 //$show0 = false;

// if($date % 2 == 0)
//     $show0 = true;
// ?>

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
    <title>FELIIX</title>
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
    <link rel="stylesheet" href="css/bootstrap/4.5.0/bootstrap.min.css">
    <link href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css"
          rel="stylesheet">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script defer src="js/bootstrap/4.5.0/bootstrap.min.js"></script>
    <script defer
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
            
    <style>

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.primary header nav a, body.primary header nav a:link {
            color: #000;
        }

        body.primary header nav a:hover {
            color: #333;
        }

        body.primary header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.primary header nav ul.info {
            margin-bottom: 0;
        }

        body.primary header nav ul.info b {
            font-weight: bold;
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

        .modal{
            top: 100px;
        }

        .modal-header{
            align-items: center;
        }

        .modal-body{
            padding: 30px 20px 30px 20px;
        }

        #voting_topic_table{
            text-align: center;
            width: 100%;
        }

        #voting_topic_table th{
            background-color: lightseagreen;
            color: white;
        }


        .table__item{
            padding: 3pt;
            border: 2px solid rgb(222,225,230);
        }

        .modal-footer{
            justify-content: center;
        }

        .modal-footer > a{
            color: white;
            font-weight: 600;
            background-color: lightseagreen!important;
        }

d
    </style>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
$(function(){
    $('header').load('include/header.php');
})

    </script>

</head>

<body class="primary">
    <div id="app">
        <div class="bodybox">
            <div class="mask" style="display:none"></div>
            <!-- header -->
            <header>header</header>
            <!-- header end -->
            <div class="mainContent" style="text-align: center;">
                <!-- mainContent為動態內容包覆的內容區塊 -->

                <div class="banner1" <?php if($date % 4 != 0) echo('style="display:none;"'); ?>>
                    <img src="images/action_and_word.png" height="auto" width="90%"/>
                    <!--
                        <div style="font-size:5vw; font-weight: bold;">Wear Mask</div>
                        <div style="font-size:5vw;; font-weight: bold;">Wash Hands</div>
                        <div style="font-size:5vw;; font-weight: bold;">Don't Touch Face</div>
                        <img src="images/wear_mask.png" height="auto" width="33%"/>
                        <img src="images/wash_hands.png" height="auto" width="33%"/>
                        <img src="images/dont_touch_face.png" height="auto" width="33%"/>
                        <div></div>
                        <div style="font-size:5vw;; font-weight: bold;">COVID-19 Hotlines</div>
                        <div style="font-size:5vw;; font-weight: bold;">1555 and 02-894-COVID (26843)</div>
                    -->
                </div>

                <div class="banner2" <?php if($date % 4 != 1) echo('style="display:none;"'); ?>>
                    <img src="images/communication.jpg" height="auto" width="96%"/>
                </div>

                <div class="banner3" <?php if($date % 4 != 2) echo('style="display:none;"'); ?>>
                    <img src="images/S__7733280.jpg" height="auto" width="85%"/>
                </div>

                <div class="banner4" <?php if($date % 4 != 3) echo('style="display:none;"'); ?>>
                    <img src="images/S__3907658.jpg" height="auto" width="85%"/>
                </div>
            </div>
        </div>


        <div class="modal" tabindex="-1" role="dialog" id="vote_modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Voting Topic for You</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick=" (function(){ $('.mask').toggle(); $('#vote_modal').toggle(); return false;})();return false;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <table id="voting_topic_table">

                            <tr>
                                <th class="table__item">Topic</th>
                                <th class="table__item">Voting Time</th>
                                <th class="table__item">Status</th>
                            </tr>


                            <tr v-for="(item, index) in receive_records">
                                <td class="table__item">{{ item.topic }}</td>
                                <td class="table__item">{{ item.start_date }} ~ {{ item.end_date }}</td>
                                <td class="table__item">{{ item.vote_status }}</td>
                            </tr>

                        </table>
                    </div>

                    <div class="modal-footer">
                        <a class="btn green small" href="voting_system">GO</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="js/npm/vue/dist/vue.js"></script>
    <script src="js/axios.min.js"></script> 
    <script src="js/default.js"></script>
</body>
</html>
