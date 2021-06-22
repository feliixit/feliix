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

    div.searching{
         float: left;
    }

    div.searching input{
        font-size: 12px;
        padding: 4px 7px;
    }

    div.searching i {
        font-size: 18px;
        margin-left: 5px;
    }


    div.box-content form, div.info form {
        border: 3px solid var(--black01);
        margin-top: 40px;
        padding: 15px 15px 0;
        box-sizing: border-box;
    }

    div.box-content form input, div.box-content form textarea {
        width: 100%;
    }

    div.box-content form li:nth-of-type(odd) {
        margin-top: 15px;
    }

    div.box-content form li:first-of-type {
        margin-top: 0;
    }

    div.info>span {
        font-size: x-large;
        font-weight: 700;
        color: var(--black01);
        display: block;
    }

    div.btnbox a.btn.red {
        background-color: var(--pri01a);
    }
    
    .tablebox li span {
        display: block;
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
            <!-- <a class="tag A">Attendance</a> -->
            <a class="tag B" href="ammend">Leaves</a>
            <a class="tag D" href="leave_void">Leave Void</a>
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
            <h6>Payment Proof</h6>
            <div class="box-content">
             
                <div class="list_function" style="margin-bottom:5px;">

                    <!-- 搜尋 -->
                    <div class="searching">
                        <input type="text" placeholder="Searching Keyword Here"  v-model="fil_keyowrd">
                        <i class="fas fa-search-plus" @click="getLeaveCredit()"></i>
                    </div>

                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="pre_page()">Prev 10</a>
                      
                        <a class="page" v-for="pg in pages_10" @click="page=pg" v-bind:style="[pg == page ? { 'background':'grey', 'color': 'white'} : { }]">{{ pg }}</a>
                      
                        <a class="next" :disabled="page == pages.length" @click="nex_page()">Next 10</a>
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
                            <input type="radio" name="record_id" class="alone black" :value="record.id" @click="detail(record.id)">
                        </li>
                       
                        <li>{{ record.created_at }}</li>
                        <li>{{ record.username }}</li>
                        <li>{{ record.project_name }}</li>
                        <li>{{ (record.status == 0) ? "Under Checking" : ((record.status == 1) ? "Checked: True" : ((record.status == -1) ? "Checked: False" : 'Under Checking')) }}</li>
                    </ul>
                </div>


                <div class="tablebox" v-if="view_detail" style="margin-top: 40px;">
                    <ul class="head">
                        <li class="head">Project Name</li>
                        <li><a :href="'quotation_and_payment_mgt?id=' + record.pid" target="_blank"  class="attch">{{ record.project_name }}</a></li>
                    </ul>
                    <ul class="head">
                        <li class="head">Amount</li>
                        <li>{{ isNaN(parseInt(record.final_amount)) ? "" : Number(record.final_amount).toLocaleString() }}</li>
                    </ul>
                    <ul class="head">
                        <li class="head">Final Quotation</li>
                        <li>
                            <span v-for="item in record.final_quotation">
                                <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                            </span>
                        </li>
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
                        <li class="head">Type</li>
                        <li>{{ (record.kind == 0) ? "Down Payment" : "Full Payment" }}</li>
                    </ul>
                    <ul>
                        <li class="head">Status</li>
                        <li>{{ (record.status == 0) ? "Under Checking" : ((record.status == 1) ? "Checked: True" : ((record.status == -1) ? "Checked: False" : 'Under Checking')) }}</li>
                    </ul>
                    <ul>
                        <li class="head">Proof Files</li>
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

                 <form  v-if="view_detail && record.status == 0">
                    <ul>
                        <li><b>Date of Receiving Payment</b></li>
                        <li><input type="date" id="receive_date"></li>
                        <li><b>Amount of Receiving Payment</b></li>
                        <li><input type="number" id="amount"></li>
                        <li><b>Invoice Number</b></li>
                        <li><input type="text" id="invoice"></li>
                        <li><b>Detail/WarrantyCard Number</b></li>
                        <li><input type="text" id="detail"></li>
                        <li><b>Remarks (Required if Checked: False)</b></li>
                        <li><textarea id="remark"></textarea></li>
                    </ul>

                    <div class="btnbox" v-if="record.status == 0 && is_approval">
                        <a class="btn" @click="approve" :disabled="submit">Checked: True</a>
                        <a class="btn" @click="reject" :disabled="submit">Checked: False</a>
                    </div>

                </form>
            
                <div class="info" style="margin-top: 40px;" v-if="view_detail && record.status != 0">
                    <span>Info After Check</span>
                    <div class="tablebox" v-if="view_detail">
                        <ul>
                            <li class="head">Date of Receiving Payment</li>
                            <li>{{ record.received_date }}</li>
                        </ul>
                        <ul>
                            <li class="head">Amount of Receiving Payment</li>
                            <li>{{ isNaN(parseInt(record.amount)) ? "" : Number(record.amount).toLocaleString() }}</li>
                        </ul>
                        <ul>
                            <li class="head">Invoice Number</li>
                            <li>{{ record.invoice }}</li>
                        </ul>
                        <ul>
                            <li class="head">Detail/WarrantyCard Number</li>
                            <li>{{ record.detail }}</li>
                        </ul>
                        <ul>
                            <li class="head">Remarks</li>
                            <li>{{ record.proof_remark }}
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
            
        </div>


    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script src="js/axios.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/a076d05399.js"></script> 
<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/downpayment_proof.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

</html>
