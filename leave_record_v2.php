<?php include 'check.php';?>
<?php
use \Firebase\JWT\JWT;
try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $can_use = false;

        $leave_level = $decoded->data->leave_level;

        $valid_date = new DateTime('2022-12-01');
        $all_valid_date = new DateTime('2023-01-01');
        $today = new DateTime();

        if($today < $valid_date)
            header( 'location:index' );
            
        if(($leave_level == 'B' || $leave_level == 'C') && $today >= $valid_date)
            $can_use = true;
        elseif($leave_level == 'A' && $today >= $all_valid_date)
            $can_use = true;
        else
            header( 'location:index' );
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

<!-- import CSS -->
<link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

</head>

<body class="primary">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="apply_for_leave_v2">Apply for Leave</a>
            <a class="tag B focus">Leave Record</a>
        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Leave Records</h6>
            <div class="box-content">
                <div class="title">
                    
                    <div class="function">
                       <input name="LeaveType" type="radio" value="A" id="A" class="green" checked v-model="picked"><label for="A">All</label>
                        <input name="LeaveType" type="radio" value="P" id="A2" class="blue"  v-model="picked"><label for="A2">Waiting for Approval</label>
            
                        <input type="month" id="start" name="start" @change="getLeaveCredit()">

                    </div>

                </div>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Type</li>
                        <li>Leave Time</li>
                        <li>Notes</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="checkbox" name="record_id" class="alone blue" :value="record.index" :true-value="1" v-model:checked="record.is_checked">
                        </li>
                       
                        <li>{{ (record.approval == 'P') ? "Waiting for Approval" : (record.approval == 'R') ? "Rejected" : (record.approval == 'D') ? "Archived" : (record.approval == 'W') ? "Withdrawn" : (record.approval == 'V') ? "Void" : "Approved" }}</li>
                        <li>{{ (record.leave_type == 'A') ? "Service Incentive Leave" : ((record.leave_type == 'B' || record.leave_type == 'S') ? "Sick Leave" : ((record.leave_type == 'C' || record.leave_type == 'U') ? "Unpaid Leave" : (record.leave_type == 'N') ? "Vaction Leave" : (record.leave_type == 'H') ? "Vacation Leave --- Manager Halfday Planning" : 'Absence')) }}</li>
                        <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }} {{ record.start_time }} - {{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }} {{ record.end_time }}</li>
                        <li style="white-space: pre;">{{ record.message }}</li>
                    </ul>
                </div>

                <div class="btnbox">
                    <a class="btn" @click="detail">Detail</a>
                    <a class="btn" @click="apply" :disabled="submit">Withdraw</a>
                </div>

                <div class="tablebox" v-if="view_detail">
                    <ul class="head">
                        <li class="head">Leave Type</li>
                        <li>{{ (record.leave_type == 'A') ? "Service Incentive Leave" : ((record.leave_type == 'B' || record.leave_type == 'S') ? "Sick Leave" : ((record.leave_type == 'C' || record.leave_type == 'U') ? "Unpaid Leave" : (record.leave_type == 'N') ? "Vaction Leave" : (record.leave_type == 'H') ? "Vaction Leave --- Manager Halfday Planning" : 'Absence')) }}</li>
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
                    <ul>
                        <li class="head">Notes</li>
                        <li style="white-space: pre;">{{ record.message }}</li>
                    </ul>
                </div>
            </div>

            
            
        </div>
    </div>
</div>
</body>
<script src="js/npm/vue/dist/vue.js"></script> 
<script src="js/axios.min.js"></script> 
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>
<script defer src="js/a076d05399.js"></script> 

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/leave_record_v2.js"></script>
</html>
