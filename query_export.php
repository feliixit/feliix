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
            $username = $decoded->data->username;
            
            if($decoded->data->limited_access == true)
                header( 'location:index' );
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            // 改從 access control
            $access = false;
            $access_leave = false;
            $access_attendance = false;

            $focus = 0;

            $database = new Database();
            $db = $database->getConnection();

            //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
            //    $access2 = true;
            $query = "SELECT * FROM access_control WHERE payess2 LIKE '%" . $username . "%' ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $access_attendance = true;
            }

            $query = "SELECT * FROM access_control WHERE  payess3 LIKE '%" . $username . "%' ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $access_leave = true;
            }

            $access = $access_leave || $access_attendance;

            if($access_attendance != true )
                $focus = 2;
            else
                $focus = 1;

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

<body class="black">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <?php if($access_attendance) { ?>
                <a class="tag A <?php if($focus == 1) echo "focus"; ?>">Attendance</a>
            <?php } ?>
            <?php if($access_leave) { ?>
                <a class="tag B <?php if($focus == 2) echo "focus"; ?>">Leaves</a>
            <?php } ?>
        </div>
        <!-- Blocks -->
        <div class="block A <?php if($focus == 1) echo "focus"; ?>" <?php if($access_attendance != true) echo 'style="display:none;"'; ?>>
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
                <div class="title"><b>Select:</b></div>
                <div class="title"><b>Period</b> <div class="function"><input type="date" style="width: 200px; height: 40px;" v-model="apply_start"> <b>~</b> <input type="date" style="width: 200px; height: 40px;" v-model="apply_end"></div></div>
            <div class="title"><b>Department</b> <div class="function"><select style="width: 200px; height: 40px;" v-model="department"><option value="" selected="">All</option><option v-for="(item, index) in position" :value="item.did" :key="item.department">{{ item.department }}</option></select></div></div> 
            <div class="btnbox"><a @click="apply" :disabled="submit" class="btn">Export</a></div>
            </div>
        </div>
        <div class="block B <?php if($focus == 2) echo "focus"; ?>" <?php if($access_leave != true) echo 'style="display:none;"'; ?>>
            <h6>Leave Application
                
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
                <div class="title">
                    <b>Select Period</b>
                    <div class="function">
                        <input type="date" v-model="leave_start" />
                       <input type="date" v-model="leave_end" />
<!--
                        <b class="light green"></b>All
                        <b class="light blue"></b>Waiting for Approval
-->
                    </div>
                </div>
      
                <div class="btnbox">
                    <a class="btn" @click="leave" :disabled="submit">Export</a>
                
                </div>
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
<script src="js/query_export.js"></script>
</html>
