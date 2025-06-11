<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    
if($decoded->data->limited_access == true)
header( 'location:index' );

    $database = new Database();
    $db = $database->getConnection();

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    $access6 = false;

    $query = "SELECT * FROM access_control WHERE (office_item_approve LIKE '%" . $username . "%' or office_item_release LIKE '%" . $username . "%') ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $access6 = true;
    }


    if ($access6 == false)
        header('location:index');
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header('location:index');
}

?>
<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Releasing of Office Item Application</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>


    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
    $(function(){
        $('header').load('include/header.php');
    })

    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        a, a:link {
            color: #000;
        }

        a:hover {
            color: #333;
        }

        body.black header nav a, body.black header nav a:link {
            color: #000;
        }

        body.black header nav a:hover {
            color: #333;
        }

        body.black header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.black header nav ul.info {
            margin-bottom: 0;
        }

        body.black header nav ul.info b {
            font-weight: bold;
        }

        body {
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif!important;
        }

        .bodybox .mask {
            position: absolute;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 0;
            display: block;
        }

        div.details{
            margin-top: 40px;
        }

        div.tablebox.listing {
            margin-top: 15px;
        }

        div.tablebox.listing ul li{
            height: 100px;
        }

        div.tablebox.listing ul.head li {
            background-color: #EFEFEF!important;
            height: 45px;
        }

        div.tablebox.listing ul li:nth-of-type(2) a > img{
            max-width: 100px;
            max-height: 100px;
        }

        div.box-content form {
            border: 3px solid var(--black01);
            margin-top: 40px;
            padding: 15px 15px 0;
            box-sizing: border-box;
        }

        div.box-content form > span {
            font-size: x-large;
            font-weight: 700;
            color: var(--black01);
            display: block;
            margin-bottom: 5px;
        }

        div.details > span {
            font-size: x-large;
            font-weight: 700;
            color: var(--black01);
            display: block;
            margin-top: 20px;
        }

        div.btnbox a.btn{
            color: #FFF;
        }
        
        div.btnbox a.btn.red{
            background-color: var(--pri01a);
        }

        div.btnbox a.btn.blue{
            background-color: #0069d9;
        }

        body input.alone.black[type=radio]::before{
            font-size: 25px; color: var(--black01);
        }

        #Modal_signature {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        #Modal_signature > .modal-content {
            width: 90%;
            margin: auto;
            border: 3px solid black;
            padding: 20px 25px;
            background-color: white;
            overflow-y: auto;
        }

        #Modal_signature .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
            border-bottom: none;
        }

        #Modal_signature .modal-header h6 {
            border-bottom: none;
        }

        #Modal_signature .modal-header a {
            font-size: 20px;
        }


         #Modal_signature .modal-content .sign_dialog {
            margin-top: 30px;
        }

        #Modal_signature .modal-content .sign_dialog h5 {
            padding-left: 2px;
            margin-bottom: -20px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_name {
            position: relative;
            border-bottom: 1px solid black;
            margin: 30px 0 10px;
            padding: 10px 20px;
            width: 90%;
            min-width: 400px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_name.bg_gray {
            background-color: rgba(0,0,0,0.05);
        }

        #Modal_signature .modal-content .sign_dialog .releaser_name::after {
            content: attr(data-attr);
            position: absolute;
            bottom: -27px;
            left: 5px;
            font-size: 15px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_name #signature_name1 {
            width: 100%;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_name img {
            max-width: 80%;
            max-height: 80px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_date {
            position: relative;
            border-bottom: 1px solid black;
            margin: 40px 0 60px;
            padding: 10px 20px;
            width: 90%;
            min-width: 400px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_date.bg_gray {
            background-color: rgba(0,0,0,0.05);
        }

        #Modal_signature .modal-content .sign_dialog .releaser_date::after {
            content: "Date Released";
            position: absolute;
            bottom: -27px;
            left: 5px;
            font-size: 15px;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_date #signature_date1 {
            width: 100%;
        }

        #Modal_signature .modal-content .sign_dialog .releaser_date img {
            max-width: 80%;
            max-height: 80px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_name {
            position: relative;
            border-bottom: 1px solid black;
            margin: 30px 0 10px;
            padding: 10px 20px;
            width: 90%;
            min-width: 400px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_name.bg_gray {
            background-color: rgba(0,0,0,0.05);
        }

        #Modal_signature .modal-content .sign_dialog .receiver_name::after {
            content: attr(data-attr);
            position: absolute;
            bottom: -27px;
            left: 5px;
            font-size: 15px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_name #signature_name2 {
            width: 100%;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_name img {
            max-width: 80%;
            max-height: 80px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_date {
            position: relative;
            border-bottom: 1px solid black;
            margin: 40px 0 60px;
            padding: 10px 20px;
            width: 90%;
            min-width: 400px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_date.bg_gray {
            background-color: rgba(0,0,0,0.05);
        }

        #Modal_signature .modal-content .sign_dialog .receiver_date::after {
            content: "Date Received";
            position: absolute;
            bottom: -27px;
            left: 5px;
            font-size: 15px;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_date #signature_date2 {
            width: 100%;
        }

        #Modal_signature .modal-content .sign_dialog .receiver_date img {
            max-width: 80%;
            max-height: 80px;
        }

    </style>


</head>

<body class="black">

<div class="bodybox">
    <div class="mask" id="mask" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="office_item_reviewing">Review</a>
            <a class="tag B focus">Release</a>
        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Releasing of Office Item Application</h6>
            <div class="box-content">
                <div class="title">

                    <div class="function"></div>

                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Request No.</li>
                        <li>Requestor</li>
                        <li>Date Needed</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone black" :value="record.id"
                                   v-model="proof_id">
                        </li>
                        <li>{{ record.request_no }}</li>
                        <li>{{ record.requestor }}</li>
                        <li>{{ record.date_requested }}</li>
                    </ul>
                </div>


                <div class="details" v-if="proof_id != 0 && auth_date == ''">
                    <div class="tablebox">
                        <ul class="head">
                            <li class="head">Request No.</li>
                            <li>{{record.request_no}}</li>
                        </ul>
                        <ul>
                            <li class="head">Application Time</li>
                            <li>{{record.created_at}}</li>
                        </ul>
                        <ul>
                            <li class="head">Requestor</li>
                            <li>{{ record.requestor }}</li>
                        </ul>
                        <ul>
                            <li class="head">Status</li>
                            <li>{{ record.desc }}</li>
                        </ul>
                        <ul>
                            <li class="head">Processing History
                            </li>
                            <li>
                                <p v-for='(item, index) in record.history' :key="index">
                                    {{ item.action }} <a v-if="item.reason != '' && item.action != 'Submitted'">: {{ item.reason }}</a> ({{ item.actor }} at {{ item.created_at }})
                                </p>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Needed</li>
                            <li>{{ record.date_requested }}</li>
                        </ul>
                        <ul>
                            <li class="head">Reason</li>
                            <li>{{ record.reason}}</li>
                        </ul>
                        <ul>
                            <li class="head">Attachments</li>
                            <li>
                                <a v-for='(item, index) in record.attachment' :key="index" :href="baseURL + item.gcp_name"
                                   target="_blank">{{item.filename}}</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Remarks
                            </li>
                            <li>{{ record.remarks }}</li>
                        </ul>
                    </div>

                    <div class="tablebox listing">
                        <ul class="head">
                            <li>Code</li>
                            <li>Image</li>
                            <li>Particulars</li>
                            <li>Needed Qty</li>
                            <li>Stock Status</li>
                        </ul>
                        <ul v-for='(item, index) in record.list' :key="index">
                            <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                            <li>
                                <a href="item.url" target="_blank" v-if="item.url">
                                    <img :src="item.url" v-if="item.url">
                                </a>
                            </li>
                            <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>
                            <li>{{ item.amount }}</li>
                            <li>{{ item.qty }}<br>(Reserved: {{ item.reserve_qty }})</li>
                        </ul>

                    </div>

                    <form>
                        <ul>
                            <li><b>Rejection Reason</b></li>
                            <li><textarea style="width:100%" v-model="reject_reason"></textarea></li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn red" @click="reject">Reject</a>
                        </div>
                    </form>

                    <form>
                        <ul>
                            <li><b>Voiding Reason</b></li>
                            <li><textarea style="width:100%" v-model="void_reason"></textarea></li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn red" @click="void_me">Void Application</a>
                        </div>
                    </form>

                    <form>
                        <ul>
                            <li><b>Upload Proof of Release</b></li>
                            <li><input type="file" style="width:100%" ref="file" name="file[]" multiple></li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn" @click="export_office_item">Export Voucher</a>
                            <a class="btn" :disabled="submit == true" @click="finish();">Finish Releasing</a>
                            <a class="btn blue" @click="authRecord();">Online Voucher</a>
                        </div>
                    </form>

                </div>
            </div>


            <!-- Online Office Item Application Voucher Modal start -->
            <div id="Modal_signature" class="modal" >

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Office Item Application Voucher</h6>
                    </div>

                    <div class="tablebox" style="margin-top: 15px;">
                        <ul class="head">
                            <li class="head" style="width: 36%;">Request No.</li>
                            <li>{{record.request_no}}</li>
                        </ul>
                        <ul>
                            <li class="head">Application Time</li>
                            <li>{{record.created_at}}</li>
                        </ul>
                        <ul>
                            <li class="head">Requestor</li>
                            <li>{{ record.requestor }}</li>
                        </ul>
                        <ul>
                            <li class="head">Status</li>
                            <li>{{ record.desc }}</li>
                        </ul>
                        <ul>
                            <li class="head">Processing History
                            </li>
                            <li>
                                <p v-for='(item, index) in record.history' :key="index">
                                    {{ item.action }} <a v-if="item.reason != '' && item.action != 'Submitted'">: {{ item.reason }}</a> ({{ item.actor }} at {{ item.created_at }})
                                </p>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Needed</li>
                            <li>{{ record.date_requested }}</li>
                        </ul>
                        <ul>
                            <li class="head">Reason</li>
                            <li>{{ record.reason}}</li>
                        </ul>
                        <ul>
                            <li class="head">Attachments</li>
                            <li>
                                <a v-for='(item, index) in record.attachment' :key="index" :href="baseURL + item.gcp_name"
                                   target="_blank">{{item.filename}}</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Remarks
                            </li>
                            <li>{{ record.remarks }}</li>
                        </ul>
                    </div>

                    <div class="tablebox listing">
                        <ul class="head">
                            <li>Code</li>
                            <li>Image</li>
                            <li>Particulars</li>
                            <li>Needed Qty</li>
                            <li>Stock Status</li>
                        </ul>
                        <ul v-for='(item, index) in record.list' :key="index">
                            <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                            <li>
                                <a href="item.url" target="_blank" v-if="item.url">
                                    <img :src="item.url" v-if="item.url">
                                </a>
                            </li>
                            <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>
                            <li>{{ item.amount }}</li>
                            <li>{{ item.qty }}<br>(Reserved: {{ item.reserve_qty }})</li>
                        </ul>
                    </div>
<!--

                    <div class="sign_dialog" style="display: none;">

                        <h5>Releaser</h5>

                        <div :class="['releaser_name', (auth_date == '' ? 'bg_gray' : '')]">
                            <div id="signature_name1" v-if="auth_date == ''"></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.releaser_sig_name"
                                 v-if="record.releaser_sig_name">
                        </div>

                        <div :class="['releaser_date', (auth_date == '' ? 'bg_gray' : '')]">
                            <div id="signature_date1" v-if="auth_date == ''"></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.releaser_sig_date"
                                 v-if="record.releaser_sig_date">
                        </div>

                        <h5>Receiver</h5>

                        <div :class="['receiver_name', (auth_date == '' ? 'bg_gray' : '')]">
                            <div id="signature_name2" v-if="auth_date == ''"></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_name"
                                 v-if="record.sig_name">
                        </div>

                        <div :class="['receiver_date', (auth_date == '' ? 'bg_gray' : '')]">
                            <div id="signature_date2" v-if="auth_date == ''"></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_date"
                                 v-if="record.sig_date">
                        </div>

                    </div>
    -->
                    <div class="sign_dialog">

                        <h5>Releaser</h5>

                        <div class="releaser_name bg_gray" :data-attr="'Released by ' + name">
                            <div id="releaser_sig_name" style='height: 155.6px; overflow: hidden;'></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.releaser_sig_name"
                                 v-if="record.releaser_sig_name">
                        </div>

                        <div class="releaser_date bg_gray">
                            <div id="releaser_sig_date" style='height: 155.6px; overflow: hidden;'></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.releaser_sig_date"
                                 v-if="record.releaser_sig_date">
                        </div>

                        <h5>Receiver</h5>

                        <div class="receiver_name bg_gray" :data-attr="'Received by ' + record.requestor">
                            <div id="sig_name" style='height: 155.6px; overflow: hidden;'></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_name"
                                 v-if="record.sig_name">
                        </div>

                        <div class="receiver_date bg_gray">
                            <div id="sig_date" style='height: 155.6px; overflow: hidden;'></div>
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_date"
                                 v-if="record.sig_date">
                        </div>

                    </div>

                    <div class="btnbox" style="margin-bottom: -20px;">
                        <a class="btn red" @click="toggle_auth()">Close</a>
                        <a class="btn red" @click="reset_auth()">Reset</a>
                        <a class="btn" @click="submit_auth()">Finish Releasing</a>
                    </div>

                </div>

            </div>
            <!-- Online Office Item Application Voucher Modal end -->


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
<script src="js/office_item_releasing.js"></script>

<script src="js/jSignature/flashcanvas.js"></script>
<script src="js/jSignature/jSignature.min.js"></script>
<script src="js/canvas2image/canvas2image.js"></script>
<script defer src="js/html2canvas/html2canvas.min.js"></script>
</html>