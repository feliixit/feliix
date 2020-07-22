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
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $access = false;
            if($user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41)
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
            <a class="tag A focus">Attendance</a>
            <a class="tag B">Leaves</a>
        </div>
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
                <div class="title">
                    <b>Select Period</b>
                    <div class="function">
                        <input type="date" v-model="apply_start" />
                       <input type="date" v-model="apply_end" />
<!--
                        <b class="light green"></b>All
                        <b class="light blue"></b>Waiting for Approval
-->
                    </div>
                </div>
      
                <div class="btnbox">
                    <a class="btn" @click="apply" :disabled="submit">Export</a>
                
                </div>
            </div>
        </div>
        <div class="block B">
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
<script src="js/query_export.js"></script>
</html>
