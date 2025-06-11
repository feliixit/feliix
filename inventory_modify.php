<?php include 'check.php'; ?>

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
    <title>Inventory Modification</title>
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
    $(function() {
        $('header').load('include/header.php');
    })

    </script>


    <!-- JS for current webpage -->
    <script>
    function EditListing() {
        app.clearListing();
        $(".mask").toggle();
        $("#modal_EditListing").toggle();
    }

    </script>

    <!-- CSS for current webpage -->
    <style type="text/css">
    .box-content table {
        border-top: 2px solid var(--pri01a);
        border-left: 2px solid var(--pri01a);
        width: 100%;
    }

    .box-content table tr th {
        background-color: var(--pri01c);
        font-weight: 800;
        border-bottom: 2px solid var(--pri01a);
        border-right: 2px solid var(--pri01a);
        text-align: center;
        padding: 10px;
    }

    .box-content table tr td {
        font-weight: 800;
        border-bottom: 2px solid var(--pri01a);
        border-right: 2px solid var(--pri01a);
        text-align: center;
        padding: 10px;
    }

    .block.A .box-content ul:first-of-type li:nth-of-type(even) {
        padding-bottom: 10px;
    }

    body.green select {
        background-image: url(./images/ui/icon_form_select_arrow_green.svg);
    }

    .style_Remarks {
        max-width: 300px !important;
        text-align: left !important;
    }

    .style_Icons {
        font-size: 25px !important;
    }

    </style>

    <style type="text/css">
    /* -------------------------- */
    /* body.green Style (Yellow) */
    /* -------------------------- */
    body.fourth .mainContent>.block {
        border: 2px solid var(--fth01);
    }

    body.fourth .mainContent>.block h6 {
        color: var(--fth01);
        border-bottom: 2px solid var(--fth01);
    }

    .block.A .box-content ul:first-of-type li:nth-of-type(even) {
        padding-bottom: 10px;
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

    .headings {
        margin-bottom: 15px;
    }

    .headings .tag {
        font-size: 24px;
        font-weight: 700;
        color: black;
        border-radius: 50px;
        padding: 3px 20px 3px 10px;
        margin-right: 0;
        pointer-events: none;
        vertical-align: middle;
    }

    .headings .tag.focus {
        color: var(--fth01);
        background-color: var(--fth02);
        padding-left: 20px;
        margin-left: -10px;
    }

    form ul li b {
        display: block;
        font-size: 20px;
        font-weight: 500;
        width: 100px;
    }

    form ul li select {
        border-color: var(--fth01)!important;
        background-image: url(../images/ui/icon_form_select_arrow_blue.svg);
    }

    form ul li textarea {
        border-color: var(--fth01)!important;
    }

    li.further_input div.deliver_client,
    li.further_input div.change_pool_project,
    li.further_input div.change_location,
    li.further_input div.change_sample {
        margin-top: 15px;
    }

    li.further_input b {
        width: 150px;
    }

    li.further_input select {
        width: 100%;
        margin-bottom: 8px;
    }

    li.further_input div.compoundbox {
        display: flex;
        align-items: center;
    }

    li.further_input div.compoundbox span {
        display: block;
        width: 430px;
        font-size: 18px;
        color: var(--fth01);
        font-weight: 500;
        margin-left: 10px;
    }

    li.further_input div.compoundbox input[type="file"] {
        width: 100%;
        border: 2px solid var(--fth01);
    }

    li.further_input div.compoundbox .list_attch {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }

    li.further_input div.compoundbox .list_attch a.attch {
        color: #25a2b8;
        transition: .3s;
        margin: 3px 0 3px 5px;
        font-weight: 500;
        font-size: 15px;
    }

    li.further_input div.compoundbox .list_attch a.attch::before {
        content: '';
        width: 20px;
        height: 20px;
        display: inline-block;
        margin-right: 5px;
        background-color: var(--fth05);
        border-radius: 50px;
        vertical-align: text-bottom;
        transition: .3s;
    }

    li.row_list {
        display: flex;
        margin: 50px 0 7px;
    }

    li.row_list div.label_btn {
        display: flex;
        align-items: center;
    }

    li.row_list div.label_btn a.btn_quickquery {
        background-color: var(--fth01);
        width: 30px;
        height: 30px;
        text-align: center;
    }

    li.row_list div.label_btn a.btn_quickquery i {
        font-size: 20px;
        color: white;
        line-height: 1.45;
    }

    li.row_list div.label_btn input[type="text"] {
        font-size: 16px;
        font-weight: 500;
        padding: 3px 25px 3px 10px;
        border-radius: 3px;
        width: 500px;
        height: 29px;
        border: 1px solid #999;
        margin: 0 5px 0 15px;
    }

    li.row_list div.label_btn a.btn.small {
        background-color: var(--fth01);
    }

    li.row_list div.label_btn a.btn_export {
        width: 16px;
        height: 16px;
        display: inline-block;
        margin-left: 10px;
        color: #2F9A57;
        padding-bottom: 25px;
    }

    li.row_list div.label_btn div.list_function {
        margin-top: 5px;
    }

    #video_area {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    #video_area #video {
        border: 1px solid gray;
        width: 600px;
    }

    #video_area > div {
        margin-top: 12px;
    }

    #video_area > div > a.btn {
        width: 130px!important;
        margin: 0 20px 0 20px!important;
        text-align: center;
    }



    div.block > h6 > span {
        color: var(--fth01);
        background-color: var(--fth02);
        font-size: 28px;
        font-weight: 700;
        border-radius: 50px;
        padding: 3px 20px 5px;
        margin-left: 100px;
        display: inline-block;
        vertical-align: text-bottom;
    }

    #modal_EditListing {
        display: none;
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        margin: auto;
        z-index: 2;
    }

    #modal_EditListing > .modal-content {
        width: 90%;
        margin: auto;
        border: 2px solid var(--fth01);
        padding: 20px 25px;
        background-color: white;
        max-height: 850px;
        overflow-y: auto;
    }

    #modal_EditListing .modal-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    #modal_EditListing .modal-heading h6 {
        color: var(--fth01);
        border-bottom: none;
        font-size: 36px;
        font-weight: 700;
        padding: 10px 20px;
    }

    #modal_EditListing .modal-heading a {
        color: var(--fth01);
        font-size: 20px;
    }

    #modal_EditListing .filter_function {
        margin: 3px 20px 0 0;
        padding: 0 10px;
        display: flex;
        align-items: center;
    }

    #modal_EditListing .filter_function > input[type="text"] {
        font-size: 14px;
        font-weight: 500;
        padding: 3px 25px 3px 10px;
        border-radius: 3px;
        width: 250px;
        height: 29px;
        border: 1px solid #999;
    }

    #modal_EditListing .filter_function > select {
        font-size: 14px;
        font-weight: 500;
        padding: 3px 25px 3px 10px;
        border-radius: 3px;
        width: 250px;
        height: 29px;
        border: 1px solid #999;
        background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        margin-left: 20px;
    }



    #modal_EditListing .filter_function > button {
        height: 29px;
        width: 29px;
        padding: 2px;
        margin: 0 7px;
        cursor: pointer;
        vertical-align: middle;
        border: 1px solid #999;
        border-radius: 5px;
        background-color: #fff;
    }

    #modal_EditListing .list_function {
        width: 100%;
        padding: 20px 10px 2px;
    }

    .list_function .pagenation a {
        border-color: var(--fth01);
        color: var(--fth01);
    }

    .list_function .pagenation a:hover {
        border-color: var(--fth01);
        color: #FFF;
    }

    #modal_EditListing .tablebox {
        width: 100%;
        padding: 0 10px 10px;
        border: none;
        overflow-x: auto;
    }

    #tb_tracking_codes1, #tb_tracking_codes2, #tb_tracking_codes3 {
        width: 100%;
        margin: 5px 0 10px 0;
        border-top: none;
        border-left: none;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }

    #tb_tracking_codes1 thead th,
    #tb_tracking_codes2 thead th,
    #tb_tracking_codes3 thead th {
        background-color: var(--fth01);
        border: 1px solid var(--fth03);
        font-size: 14px;
        color: #FFF;
        text-align: center;
        vertical-align: middle;
        padding: 10px;
    }

    #tb_tracking_codes1 tbody td,
    #tb_tracking_codes2 tbody td,
    #tb_tracking_codes3 tbody td {
        border: 1px solid var(--fth03);
        vertical-align: top;
        padding: 10px;
        font-weight: 300;
    }

    #tb_tracking_codes1 tbody tr:nth-of-type(even),
    #tb_tracking_codes2 tbody tr:nth-of-type(even),
    #tb_tracking_codes3 tbody tr:nth-of-type(even) {
        background-color: #F6F6F6;
    }

    #tb_tracking_codes1 thead tr th:nth-of-type(1),
    #tb_tracking_codes2 thead tr th:nth-of-type(1),
    #tb_tracking_codes3 thead tr th:nth-of-type(1) {
        width: 30%;
    }

    #tb_tracking_codes1 thead tr th:nth-of-type(2),
    #tb_tracking_codes2 thead tr th:nth-of-type(2),
    #tb_tracking_codes3 thead tr th:nth-of-type(2) {
        width: 30%;
    }

    #tb_tracking_codes1 thead tr th:nth-of-type(3),
    #tb_tracking_codes2 thead tr th:nth-of-type(3),
    #tb_tracking_codes3 thead tr th:nth-of-type(3) {
        width: 30%;
    }

    #tb_tracking_codes1 thead tr th:nth-of-type(4),
    #tb_tracking_codes2 thead tr th:nth-of-type(4),
    #tb_tracking_codes3 thead tr th:nth-of-type(4) {
        width: 10%;
    }

    #tb_tracking_codes1 tbody tr td:nth-of-type(4),
    #tb_tracking_codes2 tbody tr td:nth-of-type(4),
    #tb_tracking_codes3 tbody tr td:nth-of-type(4) {
        vertical-align: middle;
        text-align: center;
        font-size: 25px;
    }

    #tb_tracking_codes1 tbody tr td ul,
    #tb_tracking_codes2 tbody tr td ul,
    #tb_tracking_codes3 tbody tr td ul {
        margin-bottom: 5px;
    }

    #tb_tracking_codes1 ul li,
    #tb_tracking_codes2 ul li,
    #tb_tracking_codes3 ul li {
        display: table-cell;
        text-decoration: none;
        text-align: left;
        font-size: 16px;
    }

    #tb_tracking_codes1 ul li:first-of-type,
    #tb_tracking_codes2 ul li:first-of-type,
    #tb_tracking_codes3 ul li:first-of-type {
        font-weight: 600;
        padding: 1px 7px 1px 5px;
        max-width: 230px;
        vertical-align: top;
    }

    #tb_tracking_codes1 ul li:nth-of-type(2) span.after_change,
    #tb_tracking_codes2 ul li:nth-of-type(2) span.after_change,
    #tb_tracking_codes3 ul li:nth-of-type(2) span.after_change {
        color: red;
        font-size: 16px;
        font-weight: 500;
        margin-left: 5px;
    }

    #tb_tracking_codes1 tbody td ul li:nth-of-type(2) a,
    #tb_tracking_codes2 tbody td ul li:nth-of-type(2) a,
    #tb_tracking_codes3 tbody td ul li:nth-of-type(2) a {
        color: #007bff;
    }

    #tb_tracking_codes1 ul li:nth-of-type(2) span.after_change a,
    #tb_tracking_codes2 ul li:nth-of-type(2) span.after_change a,
    #tb_tracking_codes3 ul li:nth-of-type(2) span.after_change a {
        color: red;
    }

    #tb_tracking_codes1 tbody tr td:nth-of-type(3) img,
    #tb_tracking_codes2 tbody tr td:nth-of-type(3) img,
    #tb_tracking_codes3 tbody tr td:nth-of-type(3) img {
        max-width: 100px;
        max-height: 100px;
    }

    #tb_tracking_codes1 tbody tr td:nth-of-type(4) button,
    #tb_tracking_codes2 tbody tr td:nth-of-type(4) button,
    #tb_tracking_codes3 tbody tr td:nth-of-type(4) button {
        border: 2px solid black;
        width: 34px;
        height: 34px;
        box-sizing: border-box;
        padding: 6px;
        line-height: 1.0;
    }



    .shake {
        animation: shake 0.82s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        transform: translate3d(0, 0, 0);
    }

    @keyframes shake {
        10%,
        90% {
            transform: translate3d(-1px, 0, 0);
        }

        20%,
        80% {
            transform: translate3d(2px, 0, 0);
        }

        30%,
        50%,
        70% {
            transform: translate3d(-4px, 0, 0);
        }

        40%,
        60% {
            transform: translate3d(4px, 0, 0);
        }
    }

    @media screen and (max-width: 1024px) {

        #modal_EditListing .filter_function > select:nth-of-type(2),
        #modal_EditListing .filter_function > select:nth-of-type(3) {
            width: 400px;
        }

        #modal_EditListing .modal-heading h6 {
            font-size: 28px;
        }

        #modal_EditListing .filter_function {
            align-items: flex-start;
            flex-direction: column;
        }

        #modal_EditListing .filter_function > * {
            margin: 5px 0 5px 0!important;
        }

    }

    @media screen and (max-width: 640px) {

        div.block > h6 > span {
            margin-left: 10px;
        }

        li.row_list div.label_btn input[type="text"] {
            width: 260px;
        }

        #tb_tracking_codes1, #tb_tracking_codes2 {
            min-width: 1750px;
        }

        #tb_tracking_codes3 {
            min-width: 1600px;
        }

        #modal_EditListing .filter_function > select:nth-of-type(2),
        #modal_EditListing .filter_function > select:nth-of-type(3) {
            width: 400px;
        }

        #modal_EditListing .modal-heading h6 {
            font-size: 28px;
        }

        #modal_EditListing .filter_function {
            align-items: flex-start;
            flex-direction: column;
        }

        #modal_EditListing .filter_function > * {
            margin: 5px 0 5px 0!important;
        }

    }


    </style>


</head>

<body class="fourth">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">

        <!-- Blocks -->
        <div class="block A focus" style="position: relative;">
            <h6>Inventory Modification<span>{{ record.request_no }}</span></h6>

            <div class="box-content" id="phase1" v-show="record.status == 1">

                <div class="headings">
                    <b class="tag focus">PHASE 1</b>
                    <b class="tag">User Chooses Reason and Creates List of Affected Item(s)</b>
                </div>

                <form>
                    <ul>
                        <li><b>Reason</b></li>
                        <li>
                            <select style="width: 100%; margin-bottom: 8px;" v-model="reason" @change="reset_codition()">
                                <option value=""></option>
                                <option value="Deliver Item(s) to Client">Deliver Item(s) to Client</option>
                                <option value="Return Item(s) from Client to Inventory System">Return Item(s) from Client to Inventory System</option>
                                <option value="Void Tracking Code of Item(s)">Void Tracking Code of Item(s)</option>
                                <option value="Item(s) Lost">Item(s) Lost</option>
                                <option value="Item(s) Scrapped">Item(s) Scrapped</option>
                                <option value="Change Inventory Pool or Related Project of Item(s)">Change Inventory Pool or Related Project of Item(s)</option>
                                <option value="Change Location of Item(s)">Change Location of Item(s)</option>
                                <option value="Change Sample Status of Item(s)">Change Sample Status of Item(s)</option>
                            </select>

                            <textarea style="width:100%" placeholder="Notes" v-model="notes"></textarea>
                        </li>


                        <li class="further_input">
                            <div class="deliver_client" v-show="reason == 'Deliver Item(s) to Client'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>Which Intermal Member Receives?</span>
                                    <select v-model="receiver">
                                        <option value="0">Which internal member receives the item(s)?</option>
                                        <option v-for="(item, index) in users" :value="item.id">{{ item.username }}</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Transmittal File</span>
                                    <input type="file" multiple id="transmittal_file" name="transmittal_file" :ref="'transmittal_file'">
                                </div>
                            </div>


                            <div class="change_pool_project" v-show="reason == 'Change Inventory Pool or Related Project of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Inventory Pool of Item(s)</span>
                                    <select v-model="which_pool">
                                        <option value="Project Pool">Project Pool</option>
                                        <option value="Stock Pool">Stock Pool</option>
                                    </select>
                                </div>

                                <div class="compoundbox" v-show="which_pool == 'Project Pool'">
                                    <span>Related Project?</span>
                                    <select v-model="project_id">
                                         <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name}}</option>
                                    </select>
                                </div>
                                
                                <div class="compoundbox" v-show="which_pool == 'Stock Pool'" disabled>
                                    <span>Related Project?</span>
                                    <select disabled>
                                         <option value=""></option>
                                    </select>
                                </div>
                            </div>


                            <div class="change_location" v-show="reason == 'Change Location of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Location of Item(s)</span>
                                    <select v-model="location">
                                        <option value="Caloocan">Caloocan</option>
                                        <option value="Makati">Makati</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Which Intermal Member Receives?</span>
                                    <select v-model="receiver">
                                        <option value="0">Which internal member receives the item(s)?</option>
                                        <option v-for="(item, index) in users" :value="item.id">{{ item.username }}</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Transmittal File</span>
                                    <input type="file" multiple id="transmittal_file_1" name="transmittal_file_1" :ref="'transmittal_file_1'">
                                </div>

                            </div>

                            <div class="change_sample" v-show="reason == 'Change Sample Status of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Sample Status of Item(s)</span>

                                    <select v-model="as_sample">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>

                            </div>

                        </li>

                        <li class="row_list">
                            <div class="label_btn">
                                <b>Item List</b>
                                <a class="btn_quickquery" title="Add Item from Quick Query" href="javascript: void(0)" onclick="EditListing()"><i class="fas fa-list-alt"></i></a>
                                <input type="text" placeholder="Input Tracking Code(s) Here and Separate by Semicolon." id="tracking_code">
                                <a class="btn small" id="startButton">Scan</a>
                                <a class="btn small" style="margin-left: 10px;" @click="add_scan_tracking_code()">Add</a>
                            </div>
                        </li>

                        <li>
                            <div id="video_area" style="display: none;">
                                <video id="video"></video>

                                <div>
                                    <a class="btn small orange" id="resetButton">Stop</a>
                                    <a id="switchCameraButton" class="btn small blue">Switch Camera</a>
                                </div>
                            </div>
                        </li>

                        <li style="padding-bottom: 0;">
                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 10 筆資料  -->
                            <div class="list_function">
                                <div class="pagenation">
                                    <a class="prev" :disabled="page == 1" @click="pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in pages_10" @click="page=pg;" v-bind:style="[pg == page ? { 'background':'#1E6BA8', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="page == pages.length" @click="nex_page();">Next 10</a>
                                </div>
                            </div>

                        </li>

                        <li style="overflow-x: auto;">
                            <table id="tb_tracking_codes1" class="table  table-sm table-bordered" >

                                <thead>
                                <tr>
                                    <th>Tracking Code Info</th>
                                    <th>Inventory Info</th>
                                    <th>Product Info</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(item, index) in phase" :key="index" :class="[is_toIndex === (page - 1 ) * perPage + index ? 'shake' : '' ]" :data-attr="index">
                                    <td>
                                        <ul>
                                            <li>Tracking Code:</li>
                                            <li>{{ item.format_bar }}</li>
                                        </ul>

                                        <ul>
                                            <li>Status:</li>
                                            <li>{{ item.status_text }}</li>
                                        </ul>

                                        <ul>
                                            <li style="max-width: 135px;">Purchased thru Which Order:</li>
                                            <li><a :href="item.order_url" target="_blank">{{item.order_name}}</a></li>
                                        </ul>

                                        <ul>
                                            <li>Created:</li>
                                            <li>{{ item.created_at }} ({{ item.created_by }})</li>
                                        </ul>

                                        <ul>
                                            <li>Updated:</li>
                                            <li v-show="item.updated_by != null">{{ item.updated_at }} ({{ item.updated_by }})</li>
                                        </ul>
                                    </td>

                                    <td>
                                        <ul>
                                            <li>Inventory Pool:</li>
                                            <li>{{ item.which_pool }}</li>
                                        </ul>

                                        <ul>
                                            <li style="min-width: 130px;">Related Project:</li>
                                            <li><a :href="'project02?p=' + item.project_id" target="_blank">{{ item.project_name }}</a></li>
                                        </ul>

                                        <ul>
                                            <li>Location:</li>
                                            <li>{{ item.location }}</li>
                                        </ul>

                                        <ul>
                                            <li>Sample:</li>
                                            <li>{{ item.as_sample }}</li>
                                        </ul>
                                    </td>

                                    <td>
                                        <img>

                                        <ul>
                                            <li>Product ID:</li>
                                            <li>{{ item.product_id }}</li>
                                        </ul>

                                        <ul>
                                            <li style="min-width: 120px;">Product Code:</li>
                                            <li><a :href="'product_display?id=' + item.product_id" target="_blank">{{ item.code }}</a></li>
                                        </ul>

                                        <ul>
                                            <li>Brand:</li>
                                            <li>{{ item.brand }}</li>
                                        </ul>

                                        <!-- 列出 brief -->
                                        <ul>
                                            <li style="padding: 1px 3px;"></li>
                                            <li style="white-space: break-spaces; font-weight: 300;">{{ item.listing }}</li>
                                        </ul>

                                        <!-- 列出 listing -->
                                        <ul>
                                            <li style="padding: 1px 3px;"></li>
                                            <li style="white-space: break-spaces; font-weight: 500;">{{ item.remark }}</li>
                                        </ul>
                                     
                                    </td>

                                    <td>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-up"
                                           @click="set_up((page - 1 ) * perPage + index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-down"
                                           @click="set_down((page - 1 ) * perPage + index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-trash-alt" @click="del(item.id)"></i>
                                    </td>
                                </tr>
                                </tbody>

                            </table>

                        </li>

                    </ul>

                    <div class="btnbox">
                        <a class="btn red" @click="reset1" title="Clear All Encoded Content">Reset</a>
                        <a class="btn green" @click="save()" title="Temporarily Save Encoded Content">Save</a>
                        <a class="btn green" @click="goto_phase2">Execute</a>
                    </div>

                </form>

            </div>



            <div class="box-content" id="phase2" v-show="record.status == 2">

                <div class="headings">
                    <b class="tag focus">PHASE 2</b>
                    <b class="tag">Inventory Modification Completed</b>
                </div>

                <form>
                    <ul>
                        <li><b>Reason</b></li>
                        <li>
                            <select style="width: 100%; margin-bottom: 8px;" v-model="reason" disabled>
                                <option value=""></option>
                                <option value="Deliver Item(s) to Client">Deliver Item(s) to Client</option>
                                <option value="Return Item(s) from Client to Inventory System">Return Item(s) from Client to Inventory System</option>
                                <option value="Void Tracking Code of Item(s)">Void Tracking Code of Item(s)</option>
                                <option value="Item(s) Lost">Item(s) Lost</option>
                                <option value="Item(s) Scrapped">Item(s) Scrapped</option>
                                <option value="Change Inventory Pool or Related Project of Item(s)">Change Inventory Pool or Related Project of Item(s)</option>
                                <option value="Change Location of Item(s)">Change Location of Item(s)</option>
                                <option value="Change Sample Status of Item(s)">Change Sample Status of Item(s)</option>
                            </select>

                            <textarea style="width:100%" placeholder="Notes" v-model="notes" disabled></textarea>
                        </li>


                        <li class="further_input">
                            <div class="deliver_client" v-show="reason == 'Deliver Item(s) to Client'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>Which Intermal Member Receives?</span>
                                    <select v-model="receiver" disabled>
                                        <option value="0">Which internal member receives the item(s)?</option>
                                        <option v-for="(item, index) in users" :value="item.id">{{ item.username }}</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Transmittal File</span>
                                    <div class="list_attch">
                                        <a v-for='(item, index) in item_list' :key="index" :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                                    </div>
                                </div>
                            </div>


                            <div class="change_pool_project" v-show="reason == 'Change Inventory Pool or Related Project of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Inventory Pool of Item(s)</span>
                                    <select v-model="which_pool" disabled>
                                        <option value="Project Pool">Project Pool</option>
                                        <option value="Stock Pool">Stock Pool</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Related Project?</span>
                                    <select v-model="project_id" disabled>
                                        <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="change_location" v-show="reason == 'Change Location of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Location of Item(s)</span>
                                    <select v-model="location" disabled>
                                        <option value="Caloocan">Caloocan</option>
                                        <option value="Makati">Makati</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Which Intermal Member Receives?</span>
                                    <select v-model="receiver" disabled>
                                        <option value="0">Which internal member receives the item(s)?</option>
                                        <option v-for="(item, index) in users" :value="item.id">{{ item.username }}</option>
                                    </select>
                                </div>

                                <div class="compoundbox">
                                    <span>Transmittal File</span>
                                    <div class="list_attch">
                                        <a v-for='(item, index) in item_list' :key="index" :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                                    </div>
                                </div>

                            </div>

                            <div class="change_sample" v-show="reason == 'Change Sample Status of Item(s)'">
                                <b>Further Input</b>

                                <div class="compoundbox">
                                    <span>New Sample Status of Item(s)</span>

                                    <select v-model="as_sample" disabled>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>

                            </div>

                        </li>


                        <li class="row_list">
                            <div class="label_btn">
                                <b>Item List</b>
                            </div>

                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 10 筆資料  -->
                            <div class="list_function">
                                <div class="pagenation">
                                    <a class="prev" :disabled="page == 1" @click="pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in pages_10" @click="page=pg;" v-bind:style="[pg == page ? { 'background':'#1E6BA8', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="page == pages.length" @click="nex_page();">Next 10</a>
                                </div>
                            </div>
                        </li>


                        <li style="overflow-x: auto;">
                            <table id="tb_tracking_codes2" class="table  table-sm table-bordered" >

                                <thead>
                                <tr>
                                    <th>Tracking Code Info</th>
                                    <th>Inventory Info</th>
                                    <th>Product Info</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(item, index) in phase" :key="index">
                                    <td>
                                        <ul>
                                            <li>Tracking Code:</li>
                                            <li>{{ item.format_bar }}</li>
                                        </ul>

                                        <ul>
                                            <li>Status:</li>
                                            <li>{{ item.status_text }}<span class="after_change" v-if="item.status_text_new">{{ item.status_text_new }}</span></li>
                                        </ul>

                                        <ul>
                                            <li style="max-width: 135px;">Purchased thru Which Order:</li>
                                            <li><a :href="item.order_url" target="_blank">{{item.order_name}}</a></li>
                                        </ul>

                                        <ul>
                                            <li>Created:</li>
                                            <li>{{ item.created_at }} ({{ item.created_by }})</li>
                                        </ul>

                                        <ul>
                                            <li>Updated:</li>
                                            <li v-show="item.updated_by_new != null">{{ item.updated_at_new }} ({{ item.updated_by_new }})</li>
                                        </ul>
                                    </td>

                                    <td>
                                        <ul>
                                            <li>Inventory Pool:</li>
                                            <li>{{ item.which_pool }}<span class="after_change" v-if="item.which_pool_new">{{ item.which_pool_new }}</span></li>
                                        </ul>

                                        <ul>
                                            <li style="min-width: 130px;">Related Project:</li>
                                            <li><a :href="'project02?p=' + item.project_id" target="_blank">{{ item.project_name }}</a> <span class="after_change" v-if="item.project_name_new"> => <a :href="'project02?p=' + item.project_id_new">{{ item.project_name_new }}</a><span></li>
                                        </ul>

                                        <ul>
                                            <li>Location:</li>
                                            <li>{{ item.location }} <span class="after_change" v-if="item.location_new">{{ item.location_new }}</span></li>
                                        </ul>

                                        <ul>
                                            <li>Sample:</li>
                                            <li>{{ item.as_sample }}<span class="after_change" v-if="item.as_sample_new">{{ item.as_sample_new }}</span></li>
                                        </ul>
                                    </td>

                                    <td>
                                        <img>

                                        <ul>
                                            <li>Product ID:</li>
                                            <li>{{ item.product_id }}</li>
                                        </ul>

                                        <ul>
                                            <li>Product Code:</li>
                                            <li><a :href="'product_display?id=' + item.product_id" target="_blank">{{ item.code }}</a></li>
                                        </ul>

                                        <ul>
                                            <li>Brand:</li>
                                            <li>{{ item.brand }}</li>
                                        </ul>

                                        <!-- 列出 brief -->
                                        <ul>
                                            <li style="padding: 1px 3px;"></li>
                                            <li style="white-space: break-spaces; font-weight: 300;">{{ item.listing }}</li>
                                        </ul>

                                        <!-- 列出 listing -->
                                        <ul>
                                            <li style="padding: 1px 3px;"></li>
                                            <li style="white-space: break-spaces; font-weight: 500;">{{ item.remark }}</li>
                                        </ul>
                                    </td>

                                </tr>
                                </tbody>

                            </table>

                        </li>

                    </ul>

                </form>

            </div>

        </div>



        <div id="modal_EditListing" class="modal">

            <!-- Modal content -->
            <div class="modal-content">

                <div class="modal-heading">
                    <h6>Quick Query for Tracking Code</h6>
                    <a href="javascript: void(0)" onclick="EditListing()"><i class="fa fa-times fa-lg"
                                                                             aria-hidden="true"></i></a>
                </div>


                <div class="filter_function">
                    <input type="text" v-model="fil_tracking" placeholder="Input Product ID or Code">

                    <select v-model="fil_status">
                        <option value="">----- Status of Tracking Code -----</option>
                        <option value="2">Delivered to Client</option>
                        <option value="1">Lost</option>
                        <option value="0">On Hand</option>
                        <option value="3">Scrapped</option>
                        <option value="-1">Voided</option>
                    </select>

                    <select v-model="fil_project_related">
                        <option value="">----- Related Project -----</option>
                        <!-- 載入系統上所有的 Project 名字 -->
                        <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name}}</option>
                    </select>

                    <select v-model="fil_order">
                        <option value="">----- Which Order -----</option>
                        <!-- 載入系統上所有的 Order 編號和名字，例如： LPO-TW-0284 Laureen Uy-Cruz House - General Lights -->
                        <option v-for="(item, index) in orders" :value="item.id">{{ item.od_name}}</option>
                    </select>


                    <button style="margin-left: 20px;" @click="filter_apply_new()">
                        <i aria-hidden="true" class="fas fa-filter"></i>
                    </button>

                    <button @click="add_filtered()" style="width: 160px; font-size: 14px;">Add All Filtered</button>
                    <button @click="clearListing()" style="width: 60px; font-size: 14px;">Clear</button>

                </div>


                <div class="list_function">
                    <!-- 分頁功能 -->
                    <!-- 這個頁面需要做分頁，每一頁 10 筆資料  -->
                    <div class="pagenation">
                        <a class="prev" :disabled="it_page == 1" @click="it_pre_page(); filter_apply_new();">Prev 10</a>

                        <a class="page" v-for="pg in it_pages_10" @click="it_page=pg; filter_apply_new();" v-bind:style="[pg == it_page ? { 'background':'var(--fth01)', 'color': 'white'} : { }]">{{ pg }}</a>

                        <a class="next" :disabled="it_page == it_pages.length" @click="it_nex_page(); filter_apply_new();">Next 10</a>
                    </div>

                </div>


                <div class="tablebox">

                    <table id="tb_tracking_codes3" class="table  table-sm table-bordered" >

                        <thead>
                        <tr>
                            <th>Tracking Code Info</th>
                            <th>Inventory Info</th>
                            <th>Product Info</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="(item, index) in it_records" :key="index">
                            <td>
                                <ul>
                                    <li>Tracking Code:</li>
                                    <li>{{ item.format_bar }}</li>
                                </ul>

                                <ul>
                                    <li>Status:</li>
                                    <li>{{ item.status_text }}</li>
                                </ul>

                                <ul>
                                    <li style="max-width: 125px;">Purchased thru Which Order:</li>
                                    <li><a :href="item.order_url" target="_blank">{{item.order_name}}</a></li>
                                </ul>

                                <ul>
                                    <li>Created:</li>
                                    <li>{{ item.created_at }} ({{ item.created_by }})</li>
                                </ul>

                                <ul>
                                    <li>Updated:</li>
                                    <li v-show="item.updated_by != null">{{ item.updated_at }} ({{ item.updated_by }})</li>
                                </ul>
                            </td>

                            <td>
                                 <ul>
                                    <li>Inventory Pool:</li>
                                    <li>{{ item.which_pool }}</li>
                                 </ul>

                                 <ul>
                                    <li style="min-width: 130px;">Related Project:</li>
                                    <li><a :href="'project02?p=' + item.project_id" target="_blank">{{ item.project_name }}</a></li>
                                 </ul>

                                 <ul>
                                    <li>Location:</li>
                                    <li>{{ item.location }}</li>
                                 </ul>

                                 <ul>
                                    <li>Sample:</li>
                                    <li>{{ item.as_sample }}</li>
                                 </ul>
                             </td>

                             <td>
                                <img>

                                <ul>
                                    <li>Product ID:</li>
                                    <li>{{ item.product_id }}</li>
                                </ul>

                                <ul>
                                    <li>Product Code:</li>
                                    <li><a :href="'product_display?id=' + item.product_id" target="_blank">{{ item.code }}</a></li>
                                </ul>

                                <ul>
                                    <li>Brand:</li>
                                    <li>{{ item.brand }}</li>
                                </ul>

                                <!-- 列出 brief -->
                                <ul>
                                    <li style="padding: 1px 3px;"></li>
                                    <li style="white-space: break-spaces; font-weight: 300;">{{ item.listing }}</li>
                                </ul>

                                <!-- 列出 listing -->
                                <ul>
                                    <li style="padding: 1px 3px;"></li>
                                    <li style="white-space: break-spaces; font-weight: 500;">{{ item.remark }}</li>
                                </ul>
                            </td>

                            <td>
                                <button id="edit01" @click="addItem(item)"><i aria-hidden="true" class="fas fa-caret-right"></i></button>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>

    </div>
</div>


</body>

<script>
function action_forOther(selector) {

    if (selector.value == 1) {
        document.getElementById("specific_payableto").style.display = "none";
    } else {
        document.getElementById("specific_payableto").value = "";
        document.getElementById("specific_payableto").style.display = "";
    }
}

</script>


<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<script>
ELEMENT.locale(ELEMENT.lang.en)

</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/inventory_modify.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>
<script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
</html>

<script type="text/javascript">
    window.addEventListener('load', function () {
      let selectedDeviceId;
      let videoInputDevices = [];
      let currentDeviceIndex = 0;

      const hints = new Map();
      const formats = [ZXing.BarcodeFormat.CODE_128];
      const CHARSET = 'utf-8';
      hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, formats);
      hints.set(ZXing.DecodeHintType.CHARACTER_SET, CHARSET);
      hints.set(ZXing.DecodeHintType.TRY_HARDER, true);
      hints.set(ZXing.DecodeHintType.PURE_BARCODE, false);

      const codeReader = new ZXing.BrowserMultiFormatReader();
      console.log('ZXing code reader initialized');

      codeReader.listVideoInputDevices()
        .then((devices) => {
          videoInputDevices = devices;
          selectedDeviceId = videoInputDevices[0].deviceId;
          
          document.getElementById('startButton').addEventListener('click', () => {
            document.getElementById('video_area').style.display = 'flex';
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                //console.log(result);
                //alert(result.text);

                if(result.text.length == 16){
                    codeReader.reset();
                    document.getElementById('video_area').style.display = 'none';
                    app.fil_tracking += ";" + result.text;
                }
                
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
                document.getElementById('result').textContent = err;
              }
            });
            console.log(`Started continuous decode from camera with id ${selectedDeviceId}`);
          });

          document.getElementById('resetButton').addEventListener('click', () => {
            codeReader.reset();
            document.getElementById('video_area').style.display = 'none';

            console.log('Reset.');
          });

          document.getElementById('switchCameraButton').addEventListener('click', () => {
            codeReader.reset();
            currentDeviceIndex = (currentDeviceIndex + 1) % videoInputDevices.length;
            selectedDeviceId = videoInputDevices[currentDeviceIndex].deviceId;
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                if(result.text.length == 16){
                    codeReader.reset();
                    document.getElementById('video_area').style.display = 'none';
                    document.getElementById('tracking_code').value += ";" + result.text;
                    //app.fil_tracking += ";" + result.text;
                }
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
                document.getElementById('result').textContent = err;
              }
            });
            console.log(`Switched to camera with id ${selectedDeviceId}`);
          });
        })
        .catch((err) => {
          console.error(err);
        });
    });
  </script> 