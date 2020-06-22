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
<title>Feliix</title>
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

<body class="third">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A">Attendance</a>
            <a class="tag B focus">Leaves</a>
        </div>
        <!-- Blocks -->
        <div class="block A">
            <h6>Attendance</h6>
            <div class="box-content">
                <div class="title">
                    <b>Employee Name</b>
                    <div class="function">
                        <input name="Foodb" type="radio" value="A" id="A" class="green"><label for="A">All</label>
                       <input name="Foodb2" type="radio" value="A2" id="A2" class="blue"><label for="A2">Waiting for Approval</label>
<!--
                        <b class="light green"></b>All
                        <b class="light blue"></b>Waiting for Approval
-->
                        <select name="" id="">
                            <option value="">MAR / 2020</option>
                            <option value="">FEB / 2020</option>
                            <option value="">JAN / 2020</option>
                        </select>
                    </div>
                </div>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Attendance Date</li>
                        <li>On-Duty Time</li>
                        <li>Off-Duty Time</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>Waiting for Approval</li>
                        <li>2020/01/07</li>
                        <li>9:00 AM</li>
                        <li>5:30 PM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>Approval</li>
                        <li>2020/01/07</li>
                        <li>9:00 AM</li>
                        <li>5:30 PM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>Approval</li>
                        <li>2020/01/07</li>
                        <li>9:00 AM</li>
                        <li>5:30 PM</li>
                    </ul>
                </div>
                <div class="btnbox">
                    <a class="btn">Withdraw</a>
                    <a class="btn">Detail</a>
                </div>
            </div>
        </div>
        <div class="block B focus">
            <h6>Leave Applications</h6>
            <div class="box-content">
                <div class="title">
                    <b>Employee Name</b>
                    <div class="function">
                        <input name="Foodb" type="radio" value="A" id="A" class="green"><label for="A">All</label>
                       <input name="Foodb2" type="radio" value="A2" id="A2" class="blue"><label for="A2">Waiting for Approval</label>
<!--
                        <b class="light green"></b>All
                        <b class="light blue"></b>Waiting for Approval
-->
                        <select name="" id="">
                            <option value="">MAR / 2020</option>
                            <option value="">FEB / 2020</option>
                            <option value="">JAN / 2020</option>
                        </select>
                    </div>
                </div>
                <i class="hit"><b>WD:</b>Working Day, <b>RD:</b>Resting Day, <b>RH:</b>Regular Day, <b>SH:</b>Special Holiday</i>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Date</li>
                        <li>Shift</li>
                        <li>On-Duty Time</li>
                        <li>Off-Duty Time</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>RH</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>RD</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>WD</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>RH</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>SH</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>RD</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>
                    <ul>
                        <li><input name="Foodb2" type="radio" value="A2" id="B2" class="alone blue"></li>
                        <li>2020/01/07</li>
                        <li>WD</li>
                        <li>2020/01/07 9:00 AM</li>
                        <li>2020/01/07 9:00 AM</li>
                    </ul>

                </div>
            </div>
            
        </div>
    </div>
</div>
</body>
</html>
