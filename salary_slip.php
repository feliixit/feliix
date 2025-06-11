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

$test_manager = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;
            $username = $decoded->data->username;

            
if($decoded->data->limited_access == true)
header( 'location:index' );

            $database = new Database();
            $db = $database->getConnection();

            $access_attendance = false;

            $show_salary_mgt = false;
            $show_salary_slip_mgt = false;

            //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
            //    $access2 = true;
            $query = "SELECT * FROM access_control WHERE salary_slip_mgt LIKE '%" . $username . "%' ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $show_salary_slip_mgt = true;
            }

            $query = "SELECT * FROM access_control WHERE salary_mgt LIKE '%" . $username . "%' ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $show_salary_mgt = true;
            }

        }
        catch (Exception $e){

            header( 'location:index' );
        }


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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Salary Slip</title>
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

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
/*
        function ToggleModal(target) {
            $(".mask").toggle();

            if (target == 1) {
                $("#Modal_1").toggle();
            } else if (target == 2) {
                $("#Modal_2").toggle();
            } else if (target == 3) {
                $("#Modal_3").toggle();
            }
        }
        */
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">

        /* -------------------------- */
        /* body.green Style (Yellow) */
        /* -------------------------- */
        body.green .mainContent > .block,
        body.green .mainContent > .block h6,
        body.green .mainContent > .block .tablebox,
        body.green .mainContent > .block .tablebox > ul > li,
        body.green .mainContent > .block .tablebox2,
        body.green .mainContent > .block .formbox,
        body.green .mainContent > .block .formbox dd,
        body.green .mainContent > .tags a {
            border-color: #2F9A57;
        }

        body.green .mainContent > .block h6 {
            color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox > ul.head > li,
        body.green .mainContent > .tags a {
            background-color: #E5F7EB;
        }

        body.green .mainContent > .tags a.focus {
            background-color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox {
            border-top: 2px solid #2F9A57;
            border-left: 2px solid #2F9A57;
            width: 100%;
        }

        body.green .mainContent > .block .tablebox > ul > li {
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #2F9A57;
            border-right: 2px solid #2F9A57;
            font-weight: 500;
            font-size: 16px;
            vertical-align: middle;
        }

        body.green .mainContent > .block .tablebox.salary > ul > li:nth-of-type(3) {
            text-align: left;
        }

        body.green .mainContent > .block .tablebox.salary > ul.head > li:nth-of-type(3) {
            text-align: center;
        }

        body.green .mainContent > .block .tablebox.loan > ul > li:nth-of-type(3) {
            text-align: center;
        }

        body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
            font-weight: 800;
        }

        body.green .mainContent > .block .tablebox ul.footer li {
            background-color: #F4F4F4;
            font-weight: 800;
        }

        body.green .mainContent > .block .tablebox ul.head li {
            font-weight: 800;
        }

        body.green input.alone[type=radio]::before,
        body.green input.alone[type=checkbox]::before,
        body.green input[type=checkbox] + Label::before,
        body.green input[type=radio] + Label::before {
            color: #2F9A57;
        }

        body.green input[type=range],
        body.green input[type=text],
        body.green input[type=password],
        body.green input[type=file],
        body.green input[type=number],
        body.green input[type=url],
        body.green input[type=email],
        body.green input[type=tel],
        body.green input[list],
        body.green input[type=button],
        body.green input[type=submit],
        body.green button,
        body.green textarea,
        body.green select,
        body.green output {
            border-color: #2F9A57;
        }

        body.green select {
            background-image: url(images/ui/icon_form_select_arrow_black.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
        }


        .block.A .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #2F9A57;
            cursor: pointer;
            margin: 3px 6px 3px 0;
        }

        .list_function {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list_function::after {
            display: none;
        }

        .list_function .front {
            display: flex;
            align-items: center;
        }

        .list_function .front a.create {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_add_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 35px;
            height: 35px;
            line-height: 35px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            margin-right: 25px;
            flex-grow: 0;
            flex-shrink: 0;
            margin-top: 5px;
        }

        .list_function .searching input {
            font-size: 15px;
            padding: 4px 7px;
            height: 34px;
            width: 201px;
            margin-top: 5px;
        }

        .list_function .searching input[type=month] {
            border: 2px solid #2F9A57;
            background-color: transparent;
            vertical-align: middle;
        }

        .list_function .searching i {
            color: #2F9A57;
            font-size: 22px;
        }

        .list_function .pagenation {
            float: none;
        }

        .list_function .pagenation a {
            color: #2F9A57;
            border-color: #2F9A57;
        }

        .list_function .pagenation a:hover {
            background-color: #2F9A57;
            color: #FFF;
        }

        body input.alone.green[type=radio]::before {
            font-size: 25px;
            color: #2F9A57;
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

        .modal {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        .modal .modal-content {
            width: 90%;
            height: calc(100vh - 40px);
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: 850px;
            overflow-y: auto;
        }

        .modal .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px 15px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .modal-content .modal-header h6 {
            color: #2F9A57;
            border-bottom: none;
            padding: 0;
        }

        .modal .modal-content .modal-header a {
            color: #2F9A57;
            font-size: 20px;
        }

        .modal .modal-content .box-content {
            padding: 20px 25px 25px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

        .modal .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
            height: 38px;
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=text], .modal .modal-content .box-content input[type=number], .modal .modal-content .box-content input[type=date] {
            border: 1px solid black;
            width: 100%;
            padding: 8px 35px 8px 15px;
            height: 39px;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(1) {
            width: 35%;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(2) {
            width: 25%;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(3) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(1) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(n+2) {
            width: 20%;
            min-width: 100px;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(1) {
            width: 40%;
        }

        .modal .modal-content .box-content .tablebox ul > li input {
            border-color: #D0D0D0;
            text-align: center;
        }

        .modal .modal-content .box-content .tablebox ul > li:nth-of-type(3) input {
            text-align: left;
        }

        .modal .modal-content .box-content .tablebox.loan ul > li:nth-of-type(3) input {
            text-align: center;
        }

        .modal .modal-content .box-content ul > li.datebox {
            display: flex;
            align-items: center;
        }

        .modal .modal-content .box-content ul > li.datebox > input {
            flex-grow: 1
        }

        .modal .modal-content .box-content ul > li.datebox > span {
            margin: 0 10px;
            flex-grow: 0;
            flex-shrink: 0;
            font-weight: 600;
        }

        .modal .modal-content .box-content .tablebox.tb_salary{
            border-top: 1px solid black;
            border-left: 1px solid black;
        }

        .modal .modal-content .box-content .tablebox.tb_salary ul> li{
            width: 33.3%;
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .modal .modal-content .box-content .heading {
            color: #2F9A57;
            font-size: 30px;
            font-weight: 600;
        }
    </style>


</head>

<body class="green">

<div class="bodybox">
    
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <div class="mask" :ref="'mask'" style="display:none"></div>
        <!-- tags js在 main.js -->
        <div class="tags">
            <?php if($show_salary_mgt == true){ ?> <a class="tag A" href="salary_mgt">Salary Management</a> <?php } ?>
            <?php if($show_salary_slip_mgt == true){ ?> <a class="tag B" href="salary_slip_mgt">Salary Slip Management</a> <?php } ?>
            <a class="tag C focus">Salary Slip</a>
        </div>
        <!-- Blocks -->
        <div class="block C focus">
            <h6>Salary Slip</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <div class="searching">
                                <input type="month" v-model="sdate">
                                <input type="month" v-model="edate">
                                <button style="border: none;" @click="search()"><i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"
                               @click="page < 1 ? page = 1 : page--; filter_apply();">Previous</a>
                            <a class="page" v-for="pg in pages" @click="page=pg"
                               v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]"
                               v-on:click="filter_apply();">{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="page++; filter_apply();">Next</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Employee</li>
                        <li>Position</li>
                        <li>Salary for</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.id" @click="reload_detail()"
                                   v-model="proof_id">
                        </li>
                        <li style="white-space: pre;">{{ record.status_remark }}</li>
                        <li>{{ record.username }}</li>
                        <li>{{ record.title_then }} ({{ record.department_then }})</li>
                        <li>{{ record.start_date }} ~ {{ record.end_date }}</li>
                    </ul>

                </div>



                <div id="Modal_1" class="modal" :ref="'Modal_1'" >

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Salary Slip</h6>
                            <a href="javascript: void(0)" @click="ToggleModal(2, 'c')"><i class="fa fa-times fa-lg"
                                                                                      aria-hidden="true"></i></a>
                        </div>

                        <!-- Salary slip general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Employee Name</b></li>
                                <li class="content">{{ record.username }}</li>

                                <li><b>Position</b></li>
                                <li class="content">{{ record.title_then }} ({{ record.department_then }})</li>

                                <li><b>Salary for</b></li>
                                <li class="content">{{ record.start_date }} ~ {{ record.end_date }}</li>

                                <li>
                                    <div class="tablebox tb_salary">
                                        <ul>
                                            <li><b>Salary per Month</b></li>
                                            <li><b>Salary per Day</b></li>
                                            <li><b>Salary per Minute</b></li>
                                        </ul>

                                        <ul>
                                            <li>{{salary_per_month}}</li>
                                            <li>{{salary_per_day}}</li>
                                            <li>{{salary_per_minute}}</li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>

                        </div>

                        <!-- Earning and Deduction -->
                        <div class="box-content">
                            <div class="heading">Salary Detail</div>

                            <div class="tablebox salary" style="margin-top: 10px;">
                                <ul class="head">
                                    <li>Earnings</li>
                                    <li>Amount</li>
                                    <li>Remarks</li>
                                </ul>

                                <ul v-for='(item, index) in detail_plus' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.amount == 0) ? "" : item.amount }}</li>
                                    <li style="white-space: pre;">{{ item.remark }}</li>
                                </ul>


                                <ul class="footer">
                                    <li>Total Earnings</li>
                                    <li>{{ detail_plus_sum }}</li>
                                </ul>
                            </div>


                            <div class="tablebox salary" style="margin-top: 40px;">
                                <ul class="head">
                                    <li>Deductions</li>
                                    <li>Amount</li>
                                    <li>Remarks</li>
                                </ul>

                                <ul v-for='(item, index) in detail_minus' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.amount == 0) ? "" : item.amount }}</li>
                                    <li>{{ item.remark }}</li>
                                </ul>

                                <ul class="footer">
                                    <li>Total Deductions</li>
                                    <li>{{ detail_minus_sum }}</li>
                                </ul>
                            </div>

                            <ul>
                                <li style="margin-top: 40px;"><b>Total Pay:</b></li>
                                <li class="content" style="font-weight: 700;">{{ detail_sum }}</li>
                            </ul>

                        </div>


                        <!-- Other Information -->
                        <div class="box-content">
                            <div class="heading">Other Information</div>

                            <div class="tablebox loan" style="margin-top: 10px;">
                                <ul class="head">
                                    <li></li>
                                    <li>Previous</li>
                                    <li>Payment</li>
                                    <li>Balance</li>
                                </ul>

                                <ul v-for='(item, index) in other' :key="index">
                                    <li>{{ item.category }}</li>
                                    <li>{{ (item.previous == 0) ? "" : item.previous }}</li>
                                    <li>{{ (item.payment == 0) ? "" : item.payment }}</li>
                                    <li>{{ item.remark }}</li>
                                </ul>


                            </div>

                        </div>

                        <!-- Action Buttons -->
                        <div class="modal-footer">

                            <ul style="padding: 25px 25px 5px;">
                                <li><b>Remarks</b></li>
                                <li>
                                    <textarea rows="3" style="width: 100%;" :readonly="record.status != 0" v-model="remarks">{{ record.remark }}</textarea>
                                </li>
                            </ul>

                            <div class="btnbox">
                                <a class="btn" v-if="record.status == 0" @click="remove(2)">Reject</a>
                                <a class="btn green" v-if="record.status == 0" @click="remove(1)">Confirm</a>
                            </div>
                        </div>

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

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/salary_slip.js"></script>
</html>