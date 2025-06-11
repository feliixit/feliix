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

            $position = $decoded->data->position;
            $department = $decoded->data->department;
            
if($decoded->data->limited_access == true)
header( 'location:index' );
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;
            
            $access6 = false;

            // QOUTE AND PAYMENT Management
            if(trim(strtoupper($department)) == 'SALES')
            {
                if(trim(strtoupper($position)) == 'ASSISTANT CUSTOMER VALUE DIRECTOR'
                || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'LIGHTING')
            {
                if(trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }

                if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'OFFICE')
            {
                if(trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'DESIGN')
            {
                if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ENGINEERING')
            {
                if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ADMIN')
            {
                if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
                {
                    $access6 = true;
                }
            }


            if(trim($department) == '')
            {
                if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER')
            {
                $access6 = true;
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
    <title>Performance Review</title>
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
    <script src="js/moment.js"></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })

        function ToggleModal(target) {
            $(".mask").toggle();

            if (target == 1) {
                $("#Modal_1").toggle();
            } else if (target == 2) {
                $("#Modal_2").toggle();
            } else if (target == 3) {
                $("#Modal_3").toggle();
            } else if (target == 4) {
                $("#Modal_4").toggle();
            }
        }
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

        body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
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
            background-image: url(images/ui/icon_form_select_arrow_green.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
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
            margin-bottom: 5px;
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
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: calc(100vh - 40px);
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

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=month]{
            border: 2px solid #2F9A57;
            width: 100%;
            padding: 8px 35px 8px 15px;
        }

        .modal .modal-content .box-content textarea{
            border: 1px solid black;
            width: 100%;
            resize: none;
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
        }

        .modal .box-content ul li.content_disabled {
            padding: 8px 8px 30px 30px;
            font-size: 17px;
            font-weight: 500;
            font-family: Lato, Arial, Helvetica, 'Noto Sans TC', 'LiHei Pro', '微軟正黑體', '新細明體', 'Microsoft JhengHei', sans-serif;
        }

        .rating_table{
           width:80%;
        }

        .rating_table th{
            font-weight: 700;
            text-align: left;
        }

        .rating_table tr td:nth-of-type(odd) {
        font-weight: 700;
            width: 10%;
            text-align: center;
        }

        .rating_table tr td:nth-of-type(even) {
            font-weight: 500;
            width: 40%;
        }

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table thead th, .list_table tbody td{
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tbody tr td:first-of-type {
            font-weight: 600;
            min-width: 240px;
            background-color: #E5F7EB;
        }

        .list_table tbody tr td:nth-of-type(2) {
            text-align: left;
            min-width: 550px;
        }

        .list_table tbody tr td:nth-of-type(3) {
            min-width: 110px;
            color: #999;
        }

        .list_table tbody tr td:nth-of-type(4) {
            text-align: left;
            min-width: 300px;
            color: #999;
        }

        .list_table tbody tr.supervisor td:nth-of-type(1) {
            font-weight: 400;
            min-width: 110px;
            background-color: white;
        }

        .list_table tbody tr.supervisor td:nth-of-type(2) {
            text-align: left;
            min-width: 300px;
        }

        .modal .modal-content .box-content .list_table tbody select{
            padding: 10px;
            width: 75px;
        }

        .modal .modal-content .box-content .list_table tbody input[type="text"]{
            width: 100%;
            font-size: 16px;
        }

        .list_table tfoot tr th{
            border-right: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tfoot tr th:first-of-type{
            text-align: right;
        }

        .list_table tfoot tr th:nth-of-type(2){
            border-right: none;
        }

        .list_table tfoot tr th:nth-of-type(2) span:first-of-type{
            color: #999;
        }

        ul.summary {
            margin: 30px 0 20px;
        }

        ul.summary li:nth-of-type(2){
            font-weight: 700;
        }

        ul.summary li:nth-of-type(2) span:first-of-type{
            color: #999;
        }

        ul.summary li:nth-of-type(3){
            margin-top: 40px;
        }

        ul.summary li.content_disabled span.subordinate{
            display: block;
            color: #999;
            margin-bottom: 8px;
        }

        ul.summary li.content_disabled span.supervisor{
            display: block;
            color: #000;
            margin-bottom: 8px;
        }

    </style>

</head>

<body class="green">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag C" href="performance_dashboard">Performance Dashboard</a>
            <a class="tag A focus">Performance Review</a>
            <a class="tag D" href="template_library">Template Library</a>
            <?php if($access6 == true) echo('<a class="tag B" href="template_management?kw=&pg=1">Template Management</a>'); ?>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Performance Review</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create"  @click="open_review"></a>

                            <div class="searching">
                                <input type="month" v-model="sdate">
                                <input type="month" v-model="edate">
                                <input type="text" placeholder="Searching Keyword Here" v-model="keyword">
                                <button style="border: none;" @click="search(1)"><i class="fas fa-search-plus"></i></button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"  @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]" >{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Employee</li>
                        <li>Position</li>
                        <li>Review Period</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.status }}</li>
                        <li>{{ record.employee }}</li>
                        <li>{{ record.title }} ({{ record.department }})</li>
                        <li v-if="record.period == 0 || record.period == 3">{{ record.review_month }} ~ {{ record.review_next_month }}</li>
                        <li v-if="record.period == 1">{{ record.review_month }} </li>
                    </ul>

                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">
                    <a class="btn green" @click="evalua()">Evaluate</a>
                    <a class="btn green" @click="view()">View</a>
                    <a class="btn"  @click="remove()">Delete</a>
                </div>


                <div id="Modal_1" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Add Performance Review</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(1)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>

                        <!-- Choose employee, review period and template version for new evaluation -->
                        <div class="box-content" style="border-bottom: none;">
                            <ul>
                                <li><b>Employee Name</b></li>
                                <li>
                                    <select v-model="employee">
                                        <option v-for="(item, index) in employees" :value="item" :key="item.id">{{ item.username }}</option>
                                    </select>
                                </li>

                                <li><b>Review Period</b></li>

                                <li>
                                    <select style="margin-bottom: 10px;" v-model="month_type">
                                        <option value="0"></option>
                                        <option value="1">1 Month</option>
                                        <!-- <option value="2">2 Months</option> -->
                                        <option value="3">3 Months</option>
                                    </select>

                                    <input type="month" min="2021-04" step="1" v-if="month_type == 1" v-model="review_month_1" style="width: 49%; margin-right: 1.5%;">
                            
                                    <input type="month" min="2021-04" step="2" v-if="month_type == 2" v-model="review_month" style="width: 49%; margin-right: 1.5%;">
                                    <input type="month" readonly="readonly" v-if="month_type == 2" v-model="review_next_month" style="width: 49%;">

                                    <input type="month" min="2024-06" step="3" v-if="month_type == 3" v-model="review_month" style="width: 49%; margin-right: 1.5%;">
                                    <input type="month" readonly="readonly" v-if="month_type == 3" v-model="review_next_month3" style="width: 49%;">
                                </li>

                                <li><b>Version of Template</b></li>
                                <li>
                                    <select v-model="template">
                                        <option v-for="(item, index) in templates" :value="item" :key="item.id">{{ item.version }}</option>
                                    </select>
                                </li>
                            </ul>

                        </div>

                        <div class="btnbox">
                            <a class="btn green" :disabled="submit == true" @click="add_review">Add</a>
                        </div>

                    </div>

                </div>

            </div>


                <div id="Modal_2" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Performance Review</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(2)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- General description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Employee Name:</b></li>
                                <li class="content">{{ evals.employee }}</li>

                                <li><b>Employee Position:</b></li>
                                <li class="content">{{ evals.title }}</li>

                                <li><b>Employee Department:</b></li>
                                <li class="content">{{ evals.department }}</li>

                                <li><b>Supervisor:</b></li>
                                <li class="content">{{ evals.manager }}</li>

                                <li><b>Review Period:</b></li>
                                <li class="content" v-if="evals.period == 0 || evals.period == 3 ">{{ evals.review_month }} ~ {{ evals.review_next_month }}</li>
                                <li class="content" v-if="evals.period == 1">{{ evals.review_month }}</li>

                                <li><b>Version of Template:</b></li>
                                <li class="content">{{ evals.version }}</li>
                            </ul>

                        </div>


                        <!-- Evaluation form start -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="rating_table">
                                <thead>
                                <tr>
                                    <th colspan="4">Rating Scale</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td>10</td>
                                    <td>Meets Expectation</td>
                                    <td>5</td>
                                    <td>Below Average</td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Very good</td>
                                    <td>4</td>
                                    <td>Needs Improvement</td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Good</td>
                                    <td>3</td>
                                    <td>Poor</td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Above Average</td>
                                    <td>2</td>
                                    <td>Unacceptable</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>OK</td>
                                    <td>1</td>
                                    <td>Very Unacceptable</td>
                                </tr>
                                </tbody>
                            </table>

                             <table class="list_table" style="margin-top:30px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for='(record, index) in evals.agenda'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <select name="grade" @change="on_grade_change($event)" ref="grade">
                                            <option value="10">10</option>
                                            <option value="9">9</option>
                                            <option value="8">8</option>
                                            <option value="7">7</option>
                                            <option value="6">6</option>
                                            <option value="5">5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                            <option value="-1">N/A</option>
                                        </select>
                                    </td>
                                    <td><input hidden name="qid" ref="qid" :value="record.id" v-show="false"><input name="opt" ref="opt" type="text"></td>
                                </tr>


                                </tbody>

                                 <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>{{ avg == 0? "N/A" : (avg / 1).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (60%)</th>
                                     <th>{{ avg == 0 ? "N/A" : ( avg * 0.6 ).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>


                            <table class="list_table" style="margin-top: 40px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for='(record, index) in evals.agenda1'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <select name="grade1" @change="on_grade1_change($event)" ref="grade1">
                                            <option value="10">10</option>
                                            <option value="9">9</option>
                                            <option value="8">8</option>
                                            <option value="7">7</option>
                                            <option value="6">6</option>
                                            <option value="5">5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                            <option value="-1">N/A</option>
                                        </select>
                                    </td>
                                    <td><input hidden name="qid1" ref="qid1" :value="record.id" v-show="false"><input name="opt1" ref="opt1" type="text"></td>
                                </tr>


                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>{{ avg1 == 0 ? "N/A" : (avg1 / 1).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (40%)</th>
                                     <th>{{ avg1 == 0 ? "N/A" : ( avg1 * 0.4 ).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>


                            <table class="list_table" style="margin-top: 40px;" v-if="evals.agenda2 === undefined ? false : evals.agenda2.length > 0" >
                                <thead>
                                <tr>
                                    <th colspan="2">PART III: BONUS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for='(record, index) in evals.agenda2'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <select name="grade2" @change="on_grade2_change($event)" ref="grade2">
                                            <option value="10">10</option>
                                            <option value="9">9</option>
                                            <option value="8">8</option>
                                            <option value="7">7</option>
                                            <option value="6">6</option>
                                            <option value="5">5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                            <option value="-1">N/A</option>
                                        </select>
                                    </td>
                                    <td><input hidden name="qid2" ref="qid2" :value="record.id" v-show="false"><input name="opt2" ref="opt2" type="text"></td>
                                </tr>


                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>{{ avg2 == 0 ? "N/A" : (avg2 / 1).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (10%)</th>
                                     <th>{{ avg2 == 0 ? "N/A" : ( avg2 * 0.1 ).toFixed(1) }}</th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>


                            <ul style="margin-top: 30px;">
                                <li><b>TOTAL:</b></li>
                                <li class="content" style="font-weight: 700;">{{ (avg == 0 && avg1 == 0) ? "N/A" : ( avg * 0.6 + avg1 * 0.4 + (evals.agenda2 !== undefined ? (evals.agenda2.length > 0 ? parseFloat((avg2 * 0.1).toFixed(1)) : 0.0 ) : 0.0 ) ).toFixed(1) }}</li>

                                <li style="margin-top: 40px;"><b><template v-if="evals.user_id == user_id">Noteworthy accomplishment</template><template v-if="evals.create_id == user_id">Noteworthy accomplishment</template></b><span> ({{comment1.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li><textarea rows="5" v-model="comment1" :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>
                       

                                <li style="margin-top: 40px;"><b><template v-if="evals.user_id == user_id">What is your opinion about the progress of your objective in the past two months? What ability, attitude, or method makes you deliver this progress? Anything else can be done or changed to make you execute better?</template><template v-if="evals.create_id == user_id">What is your opinion about the progress of subordinate's objective in the past two months? What ability, attitude, or method makes subordinate deliver this progress? Anything else can be done or changed to make subordinate execute better?</template></b><span> ({{comment2.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li><textarea rows="5" v-model="comment2" :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>
                   

                                <li style="margin-top: 40px;"><b><template v-if="evals.user_id == user_id">What is your planning objective for the next two months? What is your role and responsibility in the objective? How do you define your success and failure in the objective?</template><template v-if="evals.create_id == user_id">What is your expectation of subordinate's objective for the next two months? What is subordinate's role and responsibility in the objective? How do you define subordinate's success and failure in the objective?</template></b><span> ({{comment3.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li><textarea rows="5" v-model="comment3"  :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>
                  

                                <li v-if="evals.user_id == user_id" style="margin-top: 40px;"><b><template v-if="evals.user_id == user_id">What are your career goals? Did the current job arrangement fit your career goals? If not, any suggestions?</template></b><span v-if="evals.user_id == user_id"> ({{comment4.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li v-if="evals.user_id == user_id"><textarea rows="5" v-model="comment4" :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>
                          

                                <li style="margin-top: 40px;"><b><template v-if="evals.user_id == user_id">Other comments</template><template v-if="evals.create_id == user_id">Other comments</template></b><span> ({{comment5.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li><textarea rows="5" v-model="comment5" :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>
                              
                            </ul>


                            <div class="btnbox">
                                <a class="btn green" @click="review_submit">Submit</a>
                            </div>

                        </div>

                    </div>
                    <!-- Evaluation form end -->

                </div>


            <div id="Modal_3" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Performance Review</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(3)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- General description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Employee Name:</b></li>
                                <li class="content">{{ views.employee }}</li>

                                <li><b>Employee Position:</b></li>
                                <li class="content">{{ views.title }}</li>

                                <li><b>Employee Department:</b></li>
                                <li class="content">{{ views.department }}</li>

                                <li><b>Supervisor:</b></li>
                                <li class="content">{{ views.manager }}</li>

                                <li><b>Review Period:</b></li>
                                <li class="content" v-if="views.period == 0 || views.period == 3">{{ views.review_month }} ~ {{ views.review_next_month }}</li>
                                <li class="content" v-if="views.period == 1">{{ views.review_month }}</li>

                                <li><b>Version of Template:</b></li>
                                <li class="content">{{ views.version }}</li>

                                <li><b>Date of Evaluation:</b></li>
                                <li class="content" style="color:#999; height:27px;"><template v-if="views.user_complete_at != ''">{{ views.user_complete_at }} ({{ views.employee }})</template><b>&ensp;</b></li>
                                <li class="content" style="height:27px;"><template v-if="views.manager_complete_at != ''">{{ views.manager_complete_at }} ({{ views.manager }})</template><b>&ensp;</b></li>
                            </ul>

                        </div>


                        <!-- Evaluation form start -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="rating_table">
                                <thead>
                                <tr>
                                    <th colspan="4">Rating Scale</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td>10</td>
                                    <td>Meets Expectation</td>
                                    <td>5</td>
                                    <td>Below Average</td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Very good</td>
                                    <td>4</td>
                                    <td>Needs Improvement</td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Good</td>
                                    <td>3</td>
                                    <td>Poor</td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Above Average</td>
                                    <td>2</td>
                                    <td>Unacceptable</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>OK</td>
                                    <td>1</td>
                                    <td>Very Unacceptable</td>
                                </tr>
                                </tbody>
                            </table>

                             <table class="list_table" style="margin-top:30px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                 <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg == 0 ? "N/A" : (emp_avg / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg == 0 ? "N/A" : (mag_avg / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (60%)</th>
                                     <th>
                                         <span>{{ emp_avg == 0 ? "N/A" : ( emp_avg * 0.6 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg == 0 ? "N/A" : ( mag_avg * 0.6 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <table class="list_table" style="margin-top: 40px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda1'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg1 == 0 ? "N/A" : (emp_avg1 / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg1 == 0 ? "N/A" : (mag_avg1 / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (40%)</th>
                                     <th>
                                         <span>{{ emp_avg1 == 0 ? "N/A" : ( emp_avg1 * 0.4 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg1 == 0 ? "N/A" : ( mag_avg1 * 0.4 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <table class="list_table" style="margin-top: 40px;" v-if="views.agenda2 === undefined ? false : views.agenda2.length > 0">
                                <thead>
                                <tr>
                                    <th colspan="2">PART III: BONUS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda2'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg2 == 0 ? "N/A" : (emp_avg2 / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg2 == 0 ? "N/A" : (mag_avg2 / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (10%)</th>
                                     <th>
                                         <span>{{ emp_avg2 == 0 ? "N/A" : ( emp_avg2 * 0.1 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg2 == 0 ? "N/A" : ( mag_avg2 * 0.1 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <ul class="summary">
                                <li><b>TOTAL:</b></li>
                                <li class="content">
                                    <span>{{ (emp_avg == 0 && emp_avg1 == 0) ? "N/A" : ( emp_avg * 0.6 + emp_avg1 * 0.4 + (views.agenda2 !== undefined ? parseFloat((emp_avg2 * 0.1).toFixed(1)) : 0.0 ) ).toFixed(1) }}</span>
                                    <br>
                                    <span>{{ (mag_avg == 0 && mag_avg1 == 0) ? "N/A" : ( mag_avg * 0.6 + mag_avg1 * 0.4 + (views.agenda2 !== undefined ? parseFloat((mag_avg2 * 0.1).toFixed(1)) : 0.0 ) ).toFixed(1) }}</span>
                                </li>

                                <li><b>Noteworthy accomplishment</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_1" v-html="views.emp_comment_1.split('\n').join('<br />')" class="subordinate"></span>
                                    <span v-if="views.mag_comment_1" v-html="views.mag_comment_1.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What is your opinion about the progress of your objective in the past two months? What ability, attitude, or
                                    method makes you deliver this progress? Anything else can be done or changed to make you execute better?</b>
                                </li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_2" v-html="views.emp_comment_2.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>What is your opinion about the progress of subordinate's objective in the past two months? What ability,
                                    attitude, or method makes subordinate deliver this progress? Anything else can be done or changed to make
                                    subordinate execute better?</b>
                                </li>
                                <li class="content_disabled">
                                    <span v-if="views.mag_comment_2" v-html="views.mag_comment_2.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What is your planning objective for the next two months? What is your role and responsibility in the
                                    objective? How do you define your success and failure in the objective?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_3" v-html="views.emp_comment_3.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>What is your expectation of subordinate's objective for the next two months? What is subordinate's role and
                                    responsibility in the objective? How do you define subordinate's success and failure in the objective?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.mag_comment_3" v-html="views.mag_comment_3.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What are your career goals? Did the current job arrangement fit your career goals? If not, any
                                    suggestions?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_4" v-html="views.emp_comment_4.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>Other comments</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_5" v-html="views.emp_comment_5.split('\n').join('<br />')" class="subordinate"></span>
                                    <span v-if="views.mag_comment_5" v-html="views.mag_comment_5.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>Comments after communication</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.mag_comment_6" v-html="views.mag_comment_6.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                            </ul>


                        </div>

                    </div>
                    <!-- Evaluation form end -->

                </div>


            </div>


            <div id="Modal_4" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Performance Review</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(4)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- General description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Employee Name:</b></li>
                                <li class="content">{{ views.employee }}</li>

                                <li><b>Employee Position:</b></li>
                                <li class="content">{{ views.title }}</li>

                                <li><b>Employee Department:</b></li>
                                <li class="content">{{ views.department }}</li>

                                <li><b>Supervisor:</b></li>
                                <li class="content">{{ views.manager }}</li>

                                <li><b>Review Period:</b></li>
                                <li class="content" v-if="views.period == 0 || views.period == 3">{{ views.review_month }} ~ {{ views.review_next_month }}</li>
                                <li class="content" v-if="views.period == 1">{{ views.review_month }}</li>

                                <li><b>Version of Template:</b></li>
                                <li class="content">{{ views.version }}</li>

                                <li><b>Date of Evaluation:</b></li>
                                <li class="content" style="color:#999; height:27px;"><template v-if="views.user_complete_at != ''">{{ views.user_complete_at }} ({{ views.employee }})</template><b>&ensp;</b></li>
                                <li class="content" style="height:27px;"><template v-if="views.manager_complete_at != ''">{{ views.manager_complete_at }} ({{ views.manager }})</template><b>&ensp;</b></li>
                            </ul>

                        </div>


                        <!-- Evaluation form start -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="rating_table">
                                <thead>
                                <tr>
                                    <th colspan="4">Rating Scale</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td>10</td>
                                    <td>Meets Expectation</td>
                                    <td>5</td>
                                    <td>Below Average</td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Very good</td>
                                    <td>4</td>
                                    <td>Needs Improvement</td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Good</td>
                                    <td>3</td>
                                    <td>Poor</td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Above Average</td>
                                    <td>2</td>
                                    <td>Unacceptable</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>OK</td>
                                    <td>1</td>
                                    <td>Very Unacceptable</td>
                                </tr>
                                </tbody>
                            </table>

                             <table class="list_table" style="margin-top:30px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                 <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg == 0 ? "N/A" : (emp_avg / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg == 0 ? "N/A" : (mag_avg / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (60%)</th>
                                     <th>
                                         <span>{{ emp_avg == 0 ? "N/A" : ( emp_avg * 0.6 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg == 0 ? "N/A" : ( mag_avg * 0.6 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <table class="list_table" style="margin-top: 40px;">
                                <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda1'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg1 == 0 ? "N/A" : (emp_avg1 / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg1 == 0 ? "N/A" : (mag_avg1 / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (40%)</th>
                                     <th>
                                         <span>{{ emp_avg1 == 0 ? "N/A" : ( emp_avg1 * 0.4 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg1 == 0 ? "N/A" : ( mag_avg1 * 0.4 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <table class="list_table" style="margin-top: 40px;" v-if="views.agenda2 === undefined ? false : views.agenda2.length > 0">
                                <thead>
                                <tr>
                                    <th colspan="2">PART III: BONUS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                                </thead>

                                <tbody>
                                <template v-for='(record, index) in views.agenda2'>
                                    <tr style="height: 45px;">
                                        <td rowspan="2">
                                            {{ record.category }}
                                        </td>
                                        <td rowspan="2">
                                            {{ record.criterion }}
                                        </td>
                                        <td>
                                            {{ record.emp_score == -1 ? "N/A" : record.emp_score }}
                                        </td>
                                        <td>
                                            {{ record.emp_opt }}
                                        </td>
                                    </tr>
                                    <tr class="supervisor" style="height: 45px;">
                                        <td>
                                            {{ record.mag_score == -1 ? "N/A" : record.mag_score }}
                                        </td>
                                        <td>
                                            {{ record.mag_opt }}
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot>
                                 <tr>
                                     <th colspan="2">AVERAGE</th>
                                     <th>
                                         <span>{{ emp_avg2 == 0 ? "N/A" : (emp_avg2 / 1).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg2 == 0 ? "N/A" : (mag_avg2 / 1).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 <tr>
                                     <th colspan="2">SUBTOTAL (10%)</th>
                                     <th>
                                         <span>{{ emp_avg2 == 0 ? "N/A" : ( emp_avg2 * 0.1 ).toFixed(1) }}</span>
                                         <br>
                                         <span>{{ mag_avg2 == 0 ? "N/A" : ( mag_avg2 * 0.1 ).toFixed(1) }}</span>
                                     </th>
                                     <th></th>
                                 </tr>
                                 </tfoot>
                            </table>

                            <ul class="summary">
                                <li><b>TOTAL:</b></li>
                                <li class="content">
                                    <span>{{ (emp_avg == 0 && emp_avg1 == 0) ? "N/A" : ( emp_avg * 0.6 + emp_avg1 * 0.4 + (views.agenda2 !== undefined ? parseFloat((emp_avg2 * 0.1).toFixed(1)) : 0.0 ) ).toFixed(1) }}</span>
                                    <br>
                                    <span>{{ (mag_avg == 0 && mag_avg1 == 0) ? "N/A" : ( mag_avg * 0.6 + mag_avg1 * 0.4 + (views.agenda2 !== undefined ? parseFloat((mag_avg2 * 0.1).toFixed(1)) : 0.0 ) ).toFixed(1) }}</span>
                                </li>

                                <li><b>Noteworthy accomplishment</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_1" v-html="views.emp_comment_1.split('\n').join('<br />')" class="subordinate"></span>
                                    <span v-if="views.mag_comment_1" v-html="views.mag_comment_1.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What is your opinion about the progress of your objective in the past two months? What ability, attitude, or
                                    method makes you deliver this progress? Anything else can be done or changed to make you execute better?</b>
                                </li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_2" v-html="views.emp_comment_2.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>What is your opinion about the progress of subordinate's objective in the past two months? What ability,
                                    attitude, or method makes subordinate deliver this progress? Anything else can be done or changed to make
                                    subordinate execute better?</b>
                                </li>
                                <li class="content_disabled">
                                    <span v-if="views.mag_comment_2" v-html="views.mag_comment_2.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What is your planning objective for the next two months? What is your role and responsibility in the
                                    objective? How do you define your success and failure in the objective?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_3" v-html="views.emp_comment_3.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>What is your expectation of subordinate's objective for the next two months? What is subordinate's role and
                                    responsibility in the objective? How do you define subordinate's success and failure in the objective?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.mag_comment_3" v-html="views.mag_comment_3.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>What are your career goals? Did the current job arrangement fit your career goals? If not, any
                                    suggestions?</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_4" v-html="views.emp_comment_4.split('\n').join('<br />')" class="subordinate"></span>
                                </li>

                                <li><b>Other comments</b></li>
                                <li class="content_disabled">
                                    <span v-if="views.emp_comment_5" v-html="views.emp_comment_5.split('\n').join('<br />')" class="subordinate"></span>
                                    <span v-if="views.mag_comment_5" v-html="views.mag_comment_5.split('\n').join('<br />')" class="supervisor"></span>
                                </li>

                                <li><b>Comments after communication</b><span> ({{comment6.length}}/{{ (evals.user_id == user_id) ? 512 : 2048 }})</span></li>
                                <li><textarea rows="5" v-model="comment6" :maxlength="(evals.user_id == user_id) ? 512 : 2048" show-word-limit></textarea></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green" @click="review_comment_submit">Submit</a>
                            </div>

                        </div>

                    </div>
                    <!-- Evaluation form end -->

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
<script src="js/performance_review.js"></script>
</html>