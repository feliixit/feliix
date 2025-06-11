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

<style type="text/css"> 

    li.a
    {
        overflow-x:hidden;
        white-space:nowrap;
        height: 1em;
        width: 100%;
        display:inline;
    }
    
    .block .tablebox2.group1,
    .block .tablebox2.group2,
    .block .tablebox2.group3,
    .block .tablebox2.group4,
    .block .tablebox2.group5,
    .block .tablebox2.group6
     {
        position: relative;
        padding-top: 30px;
        margin-bottom: 20px;
    }

    .block .tablebox2.group7
     {
        position: relative;
        padding-top: 30px;
    }

    div.tablebox2.group1::before {
        content: 'Business Relationship Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group2::before {
        content: 'Lighting Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group3::before {
        content: 'Office Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group4::before {
        content: 'Engineering Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group5::before {
        content: 'Design Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group6::before {
        content: 'Admin Team';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    div.tablebox2.group7::before {
        content: 'Other';
        width: 100%;
        position: absolute;
        top: 0px;
        font-size: 18px;
        color: white;
        font-weight: 800;
        background: rgba(253, 183, 47, 0.7);
        text-align: center;
        padding: 3px 0 4px;
    }

    .block .tablebox2.group1 ul li:nth-of-type(2),
    .block .tablebox2.group2 ul li:nth-of-type(2),
    .block .tablebox2.group3 ul li:nth-of-type(2),
    .block .tablebox2.group4 ul li:nth-of-type(2),
    .block .tablebox2.group5 ul li:nth-of-type(2),
    .block .tablebox2.group6 ul li:nth-of-type(2),
    .block .tablebox2.group7 ul li:nth-of-type(2)
     {
        min-width: 132.5px;
    }

</style>
    
<body class="second">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Holiday 
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
                <div class="tablebox2">
                    <ul class="head">
                        <li>Date</li>
                        <li>Location</li>
                        <li>Holiday Name</li>
                        
                   
                    </ul>
                    <ul v-for='(record, index) in holiday_records'>
                        
                        <li>{{ record.from_date.substring(0, 4) }}/{{ record.from_date.substring(4, 6) }}/{{ record.from_date.substring(6, 8) }}</li>
                        <li>{{ record.location }}</li>
                        <li>{{ record.holiday }}</li>
                     
                   
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
                <div class="tablebox2">
                    <ul class="head">
                        <li>Name</li>
                        <li>Leave Time</li>
                        <li>Type</li>
                        <li>Status</li>
                   
                    </ul>
                    <ul v-for='(record, index) in leave_records'>
                        <li>{{ record.username }}</li>
                        <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }} {{ record.start_time }} - {{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }} {{ record.end_time }}</li>
                        <li>{{ (record.leave_type == 'A') ? "Service Incentive Leave" : ((record.leave_type == 'B') ? "Sick Leave" : ((record.leave_type == 'C') ? "Unpaid Leave" : (record.leave_type == 'N' || record.leave_type == 'H') ? "Vacation Leave" : (record.leave_type == 'S') ? "Sick Leave" : (record.leave_type == 'U') ? "Unpaid Leave" : 'Absence')) }}</li>
                        <li>{{ (record.approval == 'P') ? "Waiting for Approval" : (record.approval == 'R') ? "Rejected" : (record.approval == 'D') ? "Archived" : (record.approval == 'W') ? "Withdrawn" : "Approved" }}</li>
                   
                    </ul>
                    
                </div>
                <!-- 表單樣式 -->
            </div>
        </div>
        <div class="block C focus">
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
                <!-- 放入部門為 Sales 的所有成員 -->
                <div class="tablebox2 group1">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordSales'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 放入部門為 Lighting 的所有成員 -->
                <div class="tablebox2 group2">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordLighting'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 放入部門為 Office 的所有成員 -->
                <div class="tablebox2 group3">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordOffice'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 放入部門為 Engineering 的所有成員 -->
                <div class="tablebox2 group4">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordEngineering'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 放入部門為 Design 的所有成員 -->
                <div class="tablebox2 group5">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordDesign'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 放入部門為 Admin 的所有成員 -->
                <div class="tablebox2 group6">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordAdmin'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>

                <!-- 不屬於上面六群的使用者，都放到這裡 -->
                <div class="tablebox2 group7">
                    <ul class="head">
                        <li>Status</li>
                        <li>Name</li>
                        <li>Time In</li>
                        <li>Location</li>
                        <li>Detail</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecordOthers'>
                        <li><b :class="record.is_checked == 1 && record.leave == 0 ? 'light green' : 'light ungreen'"></b></li>
                        <li>{{ record.username }}</li>
                        <li class="a"><p v-html="record.duty_date.split('<br>').join('<br />')"></li>
                        <li class="a"><p v-html="record.location.split('<br>').join('<br />')"></p></li>
                        <li><a v-bind:href="'attendance_detail?uid='+ record.id + '&date=' + record.date"><b class="light blue"></b></a></li>
                    </ul>
                </div>
                <!-- 表單樣式 -->
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/attendance.js"></script>
</html>
