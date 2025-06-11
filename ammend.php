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

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            if($decoded->data->limited_access == true)
                header( 'location:index' );
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $access = false;
            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT * FROM leave_flow WHERE uid = " . $user_id;
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $access = true;
            }

            // Glendon Wendell Co and Kuan
            if($user_id == 3 || $user_id == 41)
                $access = true;

        }
        catch (Exception $e){

            header( 'location:index' );
        }

        if(!$access)
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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
    $(function() {
        $('header').load('include/header.php');
    })
    </script>

</head>

<body class="black">

    <div class="bodybox">
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div id="app" class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <!-- <a class="tag A">Attendance</a> -->
                <a class="tag B focus">Leaves</a>
                <a class="tag D" href="leave_void">Leave Void</a>
                <a class="tag C" href="downpayment_proof">Project</a>
            </div>
            <!-- Blocks -->
            <div class="block A">
                <h6></h6>
                <div class="box-content">



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
                                <input type="checkbox" name="record_id" class="alone blue" :value="record.index"
                                    :true-value="1" v-model:checked="record.is_checked">
                            </li>

                            <li>{{ record.created_at }}</li>
                            <li>{{ record.username }}</li>
                            <li>{{ record.leave_type == 'A' ? "Service Incentive Leave" : ((record.leave_type == 'B' || record.leave_type == 'S') ? "Sick Leave" : ((record.leave_type == 'C' || record.leave_type == 'U') ? "Unpaid Leave" : (record.leave_type == 'N' ? 'Vaction Leave' : (record.leave_type == 'H' ? 'Vacation Leave --- Manager Halfday Planning' : 'Absence')))) }}
                            </li>
                            <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }}
                                {{ record.start_time }}
                                - {{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }}
                                {{ record.end_time }}</li>
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
                            <li>{{ (record.leave_type == 'A') ? "Service Incentive Leave" : ((record.leave_type == 'B' || record.leave_type == 'S') ? "Sick Leave" : ((record.leave_type == 'C' || record.leave_type == 'U') ? "Unpaid Leave" : (record.leave_type == 'N' ? 'Vaction Leave' : (record.leave_type == 'H' ? 'Vacation Leave --- Manager Halfday Planning' : 'Absence')))) }}
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Application Time</li>
                            <li>{{ record.created_at.replace(/-/g,"/").substring(0, 16) }}</li>
                        </ul>
                        <ul>
                            <li class="head">Start Time</li>
                            <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }}
                                {{ record.start_time }} </li>
                        </ul>
                        <ul>
                            <li class="head">End Time</li>
                            <li>{{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }}
                                {{ record.end_time }}</li>
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
                            <li><i class="fas fa-image" @click="showPic(record.pic_url)"></i></li>
                        </ul>
                        <ul>
                            <li class="head">Notes</li>
                            <li style="white-space: pre;">{{record.message}}</li>
                        </ul>
                    </div>

                </div>

            </div>

            <div class="block D">
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
                        <ul v-for='(record, index) in approvedRecord' :key="index">
                            <li>
                                <input type="checkbox" name="record_id" class="alone blue" :value="record.index"
                                    :true-value="1" v-model:checked="record.is_checked">
                            </li>

                            <li>{{ record.created_at }}</li>
                            <li>{{ record.username }}</li>
                            <li>{{ (record.leave_type == 'A') ? "Service Incentive Leave" : ((record.leave_type == 'B' || record.leave_type == 'S') ? "Sick Leave" : ((record.leave_type == 'C' || record.leave_type == 'U') ? "Unpaid Leave" : (record.leave_type == 'N' ? 'Vaction Leave' : (record.leave_type == 'H' ? 'Vacation Leave --- Manager Halfday Planning' : 'Absence')))) }}
                            </li>
                            <li>{{ record.start_date.substring(0, 4) }}/{{ record.start_date.substring(4, 6) }}/{{ record.start_date.substring(6, 8) }}
                                {{ record.start_time }}
                                - {{ record.end_date.substring(0, 4) }}/{{ record.end_date.substring(4, 6) }}/{{ record.end_date.substring(6, 8) }}
                                {{ record.end_time }}</li>
                        </ul>
                    </div>

                    <div class="btnbox">
                        <a class="btn" @click="void_detail">Detail</a>

                        <a class="btn" @click="void_click" :disabled="submit">Void</a>
                    </div>

                    <div class="tablebox" v-if="view_void_detail">
                        <ul class="head">
                            <li class="head">Leave Type</li>
                            <li>{{ (void_record.leave_type == 'A') ? "Service Incentive Leave" : ((void_record.leave_type == 'B' || void_record.leave_type == 'S') ? "Sick Leave" : ((void_record.leave_type == 'C' || void_record.leave_type == 'U') ? "Unpaid Leave" : (void_record.leave_type == 'N' ? 'Vaction Leave' : (record.leave_type == 'H' ? 'Vacation Leave --- Manager Halfday Planning' : 'Absence')))) }}
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Application Time</li>
                            <li>{{ void_record.created_at.replace(/-/g,"/").substring(0, 16) }}</li>
                        </ul>
                        <ul>
                            <li class="head">Start Time</li>
                            <li>{{ void_record.start_date.substring(0, 4) }}/{{ void_record.start_date.substring(4, 6) }}/{{ void_record.start_date.substring(6, 8) }}
                                {{ void_record.start_time }} </li>
                        </ul>
                        <ul>
                            <li class="head">End Time</li>
                            <li>{{ void_record.end_date.substring(0, 4) }}/{{ void_record.end_date.substring(4, 6) }}/{{ void_record.end_date.substring(6, 8) }}
                                {{ void_record.end_time }}</li>
                        </ul>
                        <ul>
                            <li class="head">Leave Length</li>
                            <li>{{ void_record.le }} Days</li>
                        </ul>
                        <ul>
                            <li class="head">Reason</li>
                            <li>{{ void_record.reason }}</li>
                        </ul>
                        <ul v-if="record.pic_url != ''">
                            <li class="head">Certificate of Diagnosis</li>
                            <li><i class="fas fa-image" @click="showPic(void_record.pic_url)"></i></li>
                        </ul>
                        <ul>
                            <li class="head">Notes</li>
                            <li style="white-space: pre;">{{void_record.message}}</li>
                        </ul>
                    </div>

                </div>

            </div>

            <div class="block C">
                <h6></h6>
                <div class="box-content">



                </div>

            </div>

        </div>
    </div>
</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/a076d05399.js"></script>
<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<script>
ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/ammend.js"></script>

</html>