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

            if($decoded->exp < time())
            {
                header( 'location:index' );
            }

            if($decoded->data->limited_access == true)
                header( 'location:index' );
            
            $user_id = $decoded->data->id;

$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales'){
$test_manager = "1";
}

//  ('Kuan', 'Dennis Lin', 'dereck', 'Ariel Lin', 'Kristel Tan');
if($user_id == 48 || $user_id == 2 || $user_id == 1 || $user_id == 6 || $user_id == 3 || $user_id == 89 || $user_id == 129 || $user_id == 137 || $user_id == 138 || $user_id == 148 || $user_id == 191 || $user_id == 195)
$test_manager = "1";
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
    <title>Transmittal Management</title>
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
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

          <link rel="stylesheet" href="css/chosen/chosen.min.css">

    <!-- jQuery和js載入 -->
    <!-- <script defer src="//code.jquery.com/jquery-1.11.3.min.js"></script> -->

    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js" defer></script>
    <script defer src="js/chosen/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
            // toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
            // toggleme($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'),'show');
            // toggleme($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'),'show');

            dialogshow($('.list_function .new_project a.add'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function .new_project a.filter'), $('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'), $('.list_function .dialog.d-sort'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })
    </script>
    <style>
        .list_function .new_project {
            margin-top: -15px;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
        }

        body.fourth .bodybox {
            min-height: 120vh;
        }

        .list_function .new_project a.filter {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.sort {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .tablebox.lv1 i {
            padding: 0;
            color: #000;
        }

        .tablebox.lv1 ul li:nth-of-type(1), .tablebox.lv1 ul li:nth-of-type(2) {
            width: 400px;
        }

        .tablebox.lv1 ul li:nth-of-type(3), .tablebox.lv1 ul li:nth-of-type(4) {
            min-width: 60px;
        }

        .tablebox.lv1 ul li:nth-of-type(5), .tablebox.lv1 ul li:nth-of-type(6) {
            color: #000;
            min-width: 200px;
        }

        .tablebox.lv1 ul li:nth-of-type(7) {
            min-width: 200px;
        }

        .tableframe .tablebox ul li:nth-of-type(1) a.red {
            color: red;
        }

        .tableframe .tablebox ul li:nth-of-type(1) input[type='text'] {
            margin-top: 10px;
            width: 90%;
            border-color: #1e6ba8;
            background-color: white;
        }

        .tableframe .tablebox ul li:nth-of-type(1) div.need_followup {
            margin-top: 10px;
        }

        .tableframe .tablebox ul li:nth-of-type(1) div.need_followup span {
            font-size: 18px;
            margin-right: 10px;
        }

        .tableframe .tablebox ul li:nth-of-type(1) div.need_followup select {
            border-color: #1e6ba8;
            background-color: white;
            background-image: url(../images/ui/icon_form_select_arrow_blue.svg);
            transform: translateY(-3px);
        }

        .tableframe .tablebox ul li:nth-of-type(2) select {
            width: 90%;
            border-color: #1e6ba8;
            background-color: white;
            background-image: url(../images/ui/icon_form_select_arrow_blue.svg);
        }

        .tableframe .tablebox ul li:nth-of-type(4) {
            padding-left: 12px!important;
        }

        .tableframe .tablebox ul li:nth-of-type(4) > div {
            display: flex;
            justify-content: center;
        }

        .tableframe .tablebox ul li:nth-of-type(4) > div > a {
            font-size: 18px;
            display: block;
            margin: 5px 0;
        }

        .tableframe .tablebox ul li:nth-of-type(4) > div > a > i.fa-file {
            color: var(--fth01);
        }

        .tableframe .tablebox ul li:nth-of-type(4) > div > span {
            display: block;
            width: 24px;
            height: 24px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 23px;
            text-align: center;
            color: #EA0029;
            font-size: 20px;
            font-weight: 400;
            cursor: pointer;
            margin: 5px 0 5px 10px;
        }

        .tableframe .tablebox ul li:nth-of-type(7) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px;
            margin: 0 3px;
        }

        .tableframe .tablebox ul li:nth-of-type(7) button.file_uploader {
            position: relative;
        }

        .tableframe .tablebox ul li:nth-of-type(7) button.file_uploader input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
            cursor: pointer;
        }

        dd.relate_to > input[type="radio"] {
            border: none;
            margin-right: 0;
            vertical-align: 1px;
        }

        dd.relate_to > input[type="radio"]::before {
            color: #00811e;
        }

        dd.relate_to > span {
            font-size: 14px;
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 500;
        }

        dd.relate_to > span:first-of-type {
            margin-right: 20px;
        }

        dd.relate_to > select {
            margin-top: 5px;
        }

    </style>
</head>

<body class="fourth">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block">
            <div class="list_function">
                <div class="new_project">
                    <a class="add"></a>


                    <div id="insert_dialog" class="dialog d-add">
                        <h6>Create New Transmittal:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Transmittal Name</dt>
                                <dd><input type="text" placeholder="" v-model="ins_title" style="margin-bottom: 15px;"></dd>

                                <dt>Need System to Generate Transmittal Number?</dt>
                                <dd style="margin-bottom: 15px;">
                                    <select v-model='generate'>
                                        <option value="">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </dd>

                                <dt>Need Follow-up?</dt>
                                <dd style="margin-bottom: 15px;">
                                    <select v-model='followup'>
                                        <option value="Y">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                </dd>

                                <dt>Relate To</dt>
                                <dd class="relate_to" style="margin-top: -5px;">
                                    <input type="radio" class="alone" value="project" v-model="type"> <span>Project</span>
                                    <input type="radio" class="alone" value="task" v-model="type"> <span>Task Management</span>

                                    <!-- if choose Project -->
                                    <select v-model="ins_project_id" v-show="type == 'project'">
                                        <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>

                                    <!-- if choose Task Management -->
                                    <select v-show="type == 'task'" v-model='kind'>
                                        <option value="a">Admin Department</option>
                                        <option value="d">Design Department</option>
                                        <option value="l">Lighting Department</option>
                                        <option value="o">Office Systems Department</option>
                                        <option value="sl">Sales Department</option>
                                        <option value="sv">Engineering Department</option>
                                    </select>

                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'a' ">
                                        <option v-for="(item, index) in task_a" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'd' ">
                                        <option v-for="(item, index) in task_d" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'l' ">
                                        <option v-for="(item, index) in task_l" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'o' ">
                                        <option v-for="(item, index) in task_o" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'sl' ">
                                        <option v-for="(item, index) in task_sl" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                    <select v-model="ins_project_id" v-show="type == 'task' && kind == 'sv' ">
                                        <option v-for="(item, index) in task_sv" :value="item.id">{{ item.project_name
                                            }}
                                        </option>
                                    </select>
                                </dd>

                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="clear()">Cancel</a>
                                <a class="btn small green" @click="approve()">Create</a>
                            </div>
                        </div>
                    </div>


                </div>


                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Category</dt>
                                <dd>
                                    <select v-model="fil_project_category">
                                        <option value=""></option>
                                        <option value="2">Lighting</option>
                                        <option value="1">Office Systems</option>
                                    </select>
                                </dd>

                                <dt>Project Creator</dt>
                                <dd>
                                    <select v-model="fil_project_creator">
                                        <option value=""></option>
                                        <option v-for="item in users" :value="item.id" :key="item.id">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Which Department's Task Management</dt>
                                <dd>
                                    <select v-model="fil_kind">
                                        <option value=""></option>
                                        <option value="a">Admin Department</option>
                                        <option value="d">Design Department</option>
                                        <option value="l">Lighting Department</option>
                                        <option value="o">Office Systems Department</option>
                                        <option value="sl">Sales Department</option>
                                        <option value="sv">Engineering Department</option>
                                    </select>
                                </dd>

                                <dt>Transmittal Creator</dt>
                                <dd>
                                    <select v-model="fil_creator">
                                        <option value=""></option>
                                        <option v-for="item in creators" :value="item.username" :key="item.username">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Has Scanned Copy?</dt>
                                <dd>
                                    <select v-model="fil_approval">
                                        <option value=""></option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </dd>

                                <dt>Need Follow-up?</dt>
                                <dd>
                                    <select v-model="fil_followup">
                                        <option value=""></option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </dd>

                                <dt>To Whom</dt>
                                <dd><input type="text" v-model="fil_towhom"></dd>

                                <dt style="margin-bottom:-18px;">Date</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd><input type="date" v-model="fil_date_from"></dd>
                                </div>

                                <div class="half">
                                    <dt>to</dt>
                                    <dd><input type="date" v-model="fil_date_to"></dd>
                                </div>

                                <dt>Keyword (for transmittal name, project name or transmittal number)</dt>
                                <dd><input type="text" v-model="fil_keyword"></dd>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a
                                    class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green"
                                                                                            @click="apply_filters(1)">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 排序 -->
                <div class="new_project">
                    <a class="sort"></a>
                    <div id="order_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt1">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                            <option value="3">
                                                Transmittal Number
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord1">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt>2nd Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt2">
                                            <option value=""></option>
                                            <option value="1">
                                                Created Time
                                            </option>
                                            <option value="2">
                                                Last Updated Time
                                            </option>
                                            <option value="3">
                                                Transmittal Number
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord2">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_orders()">Cancel</a><a
                                    class="btn small" @click="clear_orders()">Clear</a> <a class="btn small green"
                                                                                           @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Filter 
                <div class="filter">
                    <select name="" id="" v-model="fil_project_category">
                        <option value="">Project Category</option>
                        <option v-for="item in categorys" :value="item.id" :key="item.category">
                            {{ item.category }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_client_type">
                        <option value="">Client Type</option>
                        <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                            {{ item.client_type }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_priority">
                        <option value="">Priority</option>
                        <option v-for="item in priorities" :value="item.id" :key="item.priority">
                            {{ item.priority }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_status">
                        <option value="">Status</option>
                        <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                            {{ item.project_status }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_stage">
                        <option value="">Current Stage</option>
                        <option value="Empty">Empty</option>
                        <option v-for="item in stages" :value="item.stage" :key="item.stage">
                            {{ item.stage }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_creator">
                        <option value="">Project Creator</option>
                        <option v-for="item in creators" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                </div>

                    -->


                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
            <!-- list -->
            <div class="tableframe">
                <div class="tablebox lv1">
                    <ul class="head">
                        <li>Transmittal Name</li>
                        <li>Related Project / Related Task Mgt.</li>
                        <li>Transmittal Number</li>
                        <li>Scanned Copy</li>
                        <li>Created Time</li>
                        <li>Last Updated Time</li>
                        <li>Action</li>
                    </ul>
                    <ul v-for='(receive_record, index) in displayedPosts'>
                        <li>
                            <a v-show="receive_record.is_edited == 1" :class="receive_record.followup == 'Y' ? 'red' : ''" v-bind:href="'transmittal?id=' + receive_record.id">{{
                                receive_record.title }}</a>
                            <input name="title" type="text"
                                   v-show="receive_record.is_edited == 0"
                                   v-model="title" maxlength="1024">

                            <div class="need_followup" v-show="receive_record.is_edited == 0">
                                <span>Need Follow-up?</span>
                                <select v-model="followup">
                                    <option value="Y">Yes</option>
                                    <option value="">No</option>
                                </select>
                            </div>
                        </li>

                        <li>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == ''"
                               v-bind:href="'project02?p='+ receive_record.project_id">Project: {{ receive_record.project_name }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'a'"
                               v-bind:href="'task_management_AD?sid='+ receive_record.project_id">Admin Department Task Management: {{ receive_record.project_name_a }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'd'"
                               v-bind:href="'task_management_DS?sid='+ receive_record.project_id">Design Department Task Management: {{ receive_record.project_name_d }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'l'"
                               v-bind:href="'task_management_LT?sid='+ receive_record.project_id">Lighting Department Task Management: {{ receive_record.project_name_l }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'o'"
                               v-bind:href="'task_management_OS?sid='+ receive_record.project_id">Office Systems Department Task Management: {{ receive_record.project_name_o }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'sl'"
                               v-bind:href="'task_management_SLS?sid='+ receive_record.project_id">Sales Task Management: {{ receive_record.project_name_sl }}
                            </a>
                            <a v-show="receive_record.is_edited == 1 && receive_record.kind == 'sv'"
                               v-bind:href="'task_management_SVC?sid='+ receive_record.project_id">Engineering Task Management: {{ receive_record.project_name_sv }}
                            </a>
                            <!--
                            <select name="project_name" v-show="receive_record.is_edited == 0"
                                    class="limitedNumbChosen"
                                    v-model="receive_record.project_id">
                                <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name }}
                                </option>
                            </select>
                            -->

                            <dd class="relate_to" v-show="receive_record.is_edited == 0">
                                <input type="radio" class="alone" value="project" v-model="type"> <span>Project</span>
                                <input type="radio" class="alone" value="task" v-model="type"> <span>Task Management</span>

                                <!-- if choose Project -->
                                <select v-model="project_id" v-if="type == 'project'">
                                    <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>

                                <!-- if choose Task Management -->
                                <select v-model="kind" v-if="type == 'task'">
                                    <option :value="'a'">Admin Department</option>
                                    <option :value="'d'">Design Department</option>
                                    <option :value="'l'">Lighting Department</option>
                                    <option :value="'o'">Office Systems Department</option>
                                    <option :value="'sl'">Sales Department</option>
                                    <option :value="'sv'">Engineering Department</option>
                                </select>

                                <select v-model="task_id" v-if="kind == 'a' && type == 'task'">
                                    <option v-for="(item, index) in task_a" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'd' && type == 'task'">
                                    <option v-for="(item, index) in task_d" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'l' && type == 'task'">
                                    <option v-for="(item, index) in task_l" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'o' && type == 'task'">
                                    <option v-for="(item, index) in task_o" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'sl' && type == 'task'">
                                    <option v-for="(item, index) in task_sl" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                                <select v-model="task_id" v-if="kind == 'sv' && type == 'task'">
                                    <option v-for="(item, index) in task_sv" :value="item.id">{{ item.project_name }}
                                    </option>
                                </select>
                            </dd>

                        </li>


                        <li><a :href="'transmittal?id=' + receive_record.id"  target="_blank">{{ receive_record.serial_name }}</a></li>

                        <!-- 如果使用者已經有上傳檔案到這個客戶同意書記錄中，則需要把每一個上傳的檔案用 a 標籤列出來；如果沒有任何已上傳的檔案，則 li 標籤裡面留空白即可 -->
                        <li>
                            <template v-for="(file, index) in receive_record.files">
                                <div>
                                    <a :href="img_url + file.gcp_name"  target="_blank"><i aria-hidden="true" class="fas fa-file fa-lg"></i></a>
                                    <span @click="delete_me(file)">X</span>
                                </div>
                            </template>
                        </li>

                        <li>{{receive_record.created_at}}<br>{{receive_record.created_by}}</li>
                        <li>{{receive_record.updated_at}}<br>{{receive_record.updated_by}}</li>
                        <li>
                            <button v-show="receive_record.is_edited == 1" @click="editRow(receive_record)"><i
                                    class="fas fa-edit"></i></button>
                            <button v-show="receive_record.is_edited == 1" @click="duplicateRow(receive_record)"><i
                                    class="fas fa-copy"></i></button>
                            <button v-show="receive_record.is_edited == 1" @click="deleteRow(receive_record)"><i
                                    class="fas fa-trash"></i></button>

                            <!-- 新增一個按鈕，當按鈕點擊時，讓使用者可以用檔案總管 選擇 裝置裡面的檔案來上傳 -->
                            <button class="file_uploader" v-show="receive_record.is_edited == 1">
                                <i class="fa fa-upload"></i>
                                <input type='file' multiple :id="'comm_file_' + receive_record.id" @change="comm_changeFile(receive_record.id)"/>
                            </button>

                            <button v-show="receive_record.is_edited == 0" @click="confirmRow(receive_record)"><i
                                    class="fas fa-check"></i></button>
                            <button v-show="receive_record.is_edited == 0" @click="cancelRow(receive_record)"><i
                                    class="fas fa-times"></i></button>
                        </li>
                    </ul>

                </div>
            </div>
            <!-- list end -->
            <div class="list_function">
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>
                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/transmittal_mgt.js"></script>

</html>

<script>

</script>