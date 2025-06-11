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
            <h6>{{ username }}</h6>
            <div class="box-content">
                <!-- 表單樣式2 -->
                <div class="formbox2">
                    <ul v-if="displayedRecord.length > 0 || displayedLeaveRecord.length > 0">
                        <li class="head">Department</li>
                        <li>{{ department }}</li>
                    </ul>
                    <!-- <div style="border-style: dotted; border-color: #fdb72f; padding:5px;" v-for='(record, index) in displayedRecord'> -->
                    <div v-for='(record, index) in displayedRecord'>
                        <ul v-if="record.duty_type === 'A'">
                            <li class="head">On-Duty Time</li>
                            <li>{{ record.duty_date }}  {{ record.duty_time }}</li>
                        </ul>
                        <ul v-else>
                            <li class="head">Off-Duty Time</li>
                            <li>{{ record.duty_date }}  {{ record.duty_time }}</li>
                        </ul>
                        <ul>
                            <li class="head">Punch-In Location</li>
                            <li>{{ record.location_detail }}</li>
                        </ul>
                        <ul v-if="record.location == 'A' || record.location == 'M' || record.location == 'B' || record.location == 'C' || record.location == 'D' || record.location == 'E' || record.location == 'F' ">
                            <li class="head" v-if="record.location == 'D' || record.location == 'E' || record.location == 'F' ">Further Explanation</li>
                            <li v-if="record.location == 'D' || record.location == 'E' || record.location == 'F' ">{{ record.duty_explain }}</li>
                            <li class="head"><span>Photo</span>
                            <img :src="'img/' + record.pic_url"></li>
                        </ul>
                        <ul v-if="record.remark !== ''">
                            <li class="head">Remark</li>
                            <li>{{ record.remark }}</li>
                        </ul>
                        <br />
                        <br />
                    </div>
                    <!-- <div style="border-style: dotted; border-color: gray; padding:5px;" v-for='(record, index) in displayedLeaveRecord'> -->
                    <div v-for='(record, index) in displayedLeaveRecord'>
                        <ul>
                            <li class="head">Off-Duty Time</li>
                            <li>{{ record.duty_date }} {{ record.duty_time }}</li>
                        </ul>
                        <ul>
                            <li class="head">Punch-Out Location</li>
                            <li>{{ record.location_detail }}</li>
                        </ul>
                        <ul v-if="record.location == 'A' || record.location == 'M' || record.location == 'B' || record.location == 'C' || record.location == 'D' || record.location == 'E' || record.location == 'F' ">
                            <li class="head" v-if="record.location == 'D' || record.location == 'E' || record.location == 'F' ">Further Explanation</li>
                            <li v-if="record.location == 'D' || record.location == 'E' || record.location == 'F' ">{{ record.duty_explain }}</li>
                            <li class="head"><span>Photo</span>
                            <img :src="'img/' + record.pic_url"></li>
                        </ul>
                        <ul v-if="record.remark != ''">
                            <li class="head">Remarks</li>
                            <li>{{ record.remark }}</li>
                        </ul>
                        <br />
                        <br />
                    </div>
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
<script defer src="js/attendance_detail.js"></script>
</html>
