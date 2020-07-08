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
    <div id="app" class="mainContent">
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
             
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Application Time</li>
                        <li>Applicant</li>
                        <li>Type</li>
                        <li>Leave Time</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="checkbox" name="record_id" class="alone blue" :value="record.index" :true-value="1" v-model:checked="record.is_checked">
                        </li>
                       
                        <li>{{ record.created_at }}</li>
                        <li>{{ record.username }}</li>
                        <li>{{ (record.leave_type == 'A') ? "Vacation Leave" : ((record.leave_type == 'B') ? "Emerency/Sick Leave" : ((record.leave_type == 'C') ? "Unpaid Leave" : 'Absence')) }}</li>
                        <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }} {{ record.start_time }} - {{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }} {{ record.end_time }}</li>
                    </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="detail">Detail</a>
                    <a class="btn" @click="approve" :disabled="submit">Approve</a>
                    <a class="btn" @click="reject" :disabled="submit">Reject</a>
                </div>

                <div class="tablebox" v-if="view_detail">
                    <ul class="head">
                        <li class="head">Leave Type</li>
                        <li>{{ (record.leave_type == 'A') ? "Vacation Leave" : ((record.leave_type == 'B') ? "Emerency/Sick Leave" : ((record.leave_type == 'C') ? "Unpaid Leave" : 'Absence')) }}</li>
                    </ul>
                    <ul>
                        <li class="head">Application Time</li>
                        <li>{{ record.created_at.replace(/-/g,"/").substring(0, 16) }}</li>
                    </ul>
                    <ul>
                        <li class="head">Start Time</li>
                        <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }} {{ record.start_time }} </li>
                    </ul>
                    <ul>
                        <li class="head">End Time</li>
                        <li>{{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }} {{ record.end_time }}</li>
                    </ul>
                    <ul>
                        <li class="head">Leave Length</li>
                        <li>{{ record.le }} Days</li>
                    </ul>
                    <ul>
                        <li class="head">Reason</li>
                        <li>{{ record.reason }}</li>
                    </ul>
                    <ul v-if="record.pic_url != ''">
                        <li class="head">Certificate of Diagnosis</li>
                        <li><i class="fas fa-image"  @click="showPic(record.pic_url)"></i></li>
                    </ul>
                </div>
                
            </div>
            
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="js/axios.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 
<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/ammend.js"></script>
</html>
