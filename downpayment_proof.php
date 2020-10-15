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
            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT * FROM leave_flow WHERE uid = " . $user_id;
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $access = true;
            }

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

<style type="text/css">
    body.black .list_function .pagenation a{
        color: var(--black01);
    }

    body.black .list_function .pagenation a:hover{
        color: white;
    }

</style>

</head>

<body class="black">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A">Attendance</a>
            <a class="tag B" href="ammend">Leaves</a>
            <a class="tag C focus">Project</a>
        </div>
        <!-- Blocks -->
        <div class="block A">
            <h6></h6>
            <div class="box-content">
             

                
            </div>
            
        </div>
        <div class="block B">
            <h6></h6>
            <div class="box-content">
             

                
            </div>
            
        </div>
        <div class="block C focus">
            <h6>Downpayment Proof</h6>
            <div class="box-content">
             
                <div class="list_function" style="margin-bottom:5px;">
                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>
                      
                        <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>
                      
                        <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Submission Time</li>
                        <li>Submitter</li>
                        <li>Project Name</li>
                        <li>Status</li>
                    </ul>
                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone black" :value="record.id" v-model="proof_id">
                        </li>
                       
                        <li>{{ record.created_at }}</li>
                        <li>{{ record.username }}</li>
                        <li>{{ record.project_name }}</li>
                        <li>{{ (record.status == 0) ? "Under Checking" : ((record.status == 1) ? "Checked: True" : ((record.status == -1) ? "Checked: False" : 'Under Checking')) }}</li>
                    </ul>
                </div>

                

                <div class="btnbox">
                    <a class="btn" @click="detail">Detail</a>
                </div>

                <div class="tablebox" v-if="view_detail">
                    <ul class="head">
                        <li class="head">Project Name</li>
                        <li>{{ record.project_name }}</li>
                    </ul>
                    <ul>
                        <li class="head">Submission Time</li>
                        <li>{{ record.created_at.replace(/-/g,"/").substring(0, 16) }}</li>
                    </ul>
                    <ul>
                        <li class="head">Submitter</li>
                        <li>{{ record.username }}</li>
                    </ul>
                    <ul>
                        <li class="head">Status</li>
                        <li>{{ (record.status == 0) ? "Under Checking" : ((record.status == 1) ? "Checked: True" : ((record.status == -1) ? "Checked: False" : 'Under Checking')) }}</li>
                    </ul>
                    <ul>
                        <li class="head">Downpayment Proof</li>
                        <li>
                            <span v-for="item in record.items">
                                <a :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>&nbsp&nbsp
                            </span>
                        </li>
                    </ul>
                    <ul>
                        <li class="head">Remark</li>
                        <li>{{ record.remark }}</li>
                    </ul>
                </div>

                <div class="btnbox" v-if="record.status == 0 && is_approval">
                    <a class="btn" @click="approve" :disabled="submit">Checked: True</a>
                    <a class="btn" @click="reject" :disabled="submit">Checked: False</a>
                </div>

                <textarea placeholder="Additional Remarks (Optional)" style=" width: 100%; margin-top:5px;" rows="5" v-if="record.status == 0 && is_approval" v-model="proof_remark"></textarea>

                <textarea style="color:#000; width: 100%; margin-top:5px;" rows="5" v-if="record.proof_remark !== '' && view_detail" :value="record.proof_remark"></textarea>
                
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
<script src="js/downpayment_proof.js"></script>
</html>
