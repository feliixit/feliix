<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="Bookmark" href="images/favicon.ico" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="images/iosicon.png"/>

<!-- SEO -->
<title>FELIIX template</title>
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
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

</head>

<body class="second">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Attendance 
                
                <div class="function">
                    <!--
                   <input type="radio" value="0" class="green" checked=""><label for="B">All</label>
                   <input type="radio" value="1" class="blue"><label for="B">Same Dpt.</label>
                    -->
<!--
                    <b class="light green"></b>All
                    <b class="light blue"></b>Same Dpt.
-->
                </div>
            </h6>
            <div class="box-content">
                <!-- 表單樣式 -->
                <div class="tablebox2">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecord'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                    
                </div>
                <!-- 表單樣式 -->
            </div>
        </div>
        <div class="block B focus">
            <h6>On Leave 
                  <div class="function">
                      <!--
                   <input name="Foodb" type="radio" value="A" id="A" class="green"><label for="A">All</label>
                   <input name="Foodb2" type="radio" value="A2" id="A2" class="blue"><label for="A2">Same Dpt.</label>
                    -->
<!--
                    <b class="light green"></b>All
                    <b class="light blue"></b>Same Dpt.
-->
                </div>
            </h6>
            <div class="box-content">
                <!-- 表單樣式 -->
                <!--
                <div class="tablebox2">
                    <ul class="head">
                        <li>Name</li>
                        <li>Reason</li>
                        <li>Start</li>
                        <li>End</li>
                    </ul>
                    <ul>
                        <li>Jaycee Villareal</li>
                        <li>Absent</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                    <ul>
                        <li>Kristel Tan</li>
                        <li>Absent</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                    <ul>
                        <li>Wren Benzon</li>
                        <li>Sick Leave</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                    <ul>
                        <li>Argel Argana</li>
                        <li>Sick Leave</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                    <ul>
                        <li>Kuan Lu</li>
                        <li>Vacation Leave</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                    <ul>
                        <li>Juan Dela Cruz</li>
                        <li>AWOL</li>
                        <li>1/01/2020 8:30AM</li>
                        <li>1/01/2020 8:30AM</li>
                    </ul>
                </div>
                -->
                <!-- 表單樣式 -->
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/attendance.js"></script>
</html>
