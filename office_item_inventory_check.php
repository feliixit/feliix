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
    <title>Office Item Inventory Check</title>
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
    body.green .mainContent>.block,
    body.green .mainContent>.block h6,
    body.green .mainContent>.block .tablebox,
    body.green .mainContent>.block .tablebox>ul>li,
    body.green .mainContent>.block .tablebox2,
    body.green .mainContent>.block .formbox,
    body.green .mainContent>.block .formbox dd,
    body.green .mainContent>.tags a {
        border-color: #2F9A57;
    }

    body.green .mainContent>.block h6 {
        color: #2F9A57;
    }

    body.green .mainContent>.block .tablebox>ul.head>li,
    body.green .mainContent>.tags a {
        background-color: #E5F7EB;
    }

    body.green .mainContent>.tags a.focus {
        background-color: #2F9A57;
    }

    body.green .mainContent>.block .tablebox {
        border-top: 2px solid #2F9A57;
        border-left: 2px solid #2F9A57;
        width: 100%;
    }

    body.green .mainContent>.block .tablebox>ul>li {
        text-align: center;
        padding: 10px;
        border-bottom: 2px solid #2F9A57;
        border-right: 2px solid #2F9A57;
        font-weight: 500;
        font-size: 16px;
        vertical-align: middle;
    }

    body.green .mainContent>.block .tablebox ul.head,
    body.green .mainContent>.block .formbox li.head {
        background-color: #2F9A57;
        font-weight: 800;
    }

    body.green .mainContent>.block .tablebox ul.head li {
        font-weight: 800;
    }

    body.green input.alone[type=radio]::before,
    body.green input.alone[type=checkbox]::before,
    body.green input[type=checkbox]+Label::before,
    body.green input[type=radio]+Label::before {
        color: var(--green01);
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
        background-image: url(../images/ui/icon_form_select_arrow_green.svg);
    }

    body.green a.btn {
        background-color: #2F9A57;
    }

    body.green a.btn:hover {
        background-color: #A9E5BF;
    }

    body.green a.btn.red {
        background-color: var(--pri01a);
    }

    body.green a.btn.red:hover {
        background-color: var(--pri01b);
    }

    body.green input[type=date] {
        border: 2px solid #2F9A57;
        padding: 5px;
        background-color: transparent;
    }

    body.green .box-content form .tablebox ul > li:nth-of-type(2) img {
        max-width: 100px;
        max-height: 100px;
    }

    body.green .box-content form .tablebox ul > li:nth-of-type(4) input {
        width: 100px;
    }

    body.green .box-content form .tablebox ul > li:last-of-type i {
        font-size: 24px;
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
        border: 2px solid #2F9A57;
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
        color: #2F9A57;
        border-bottom: none;
    }

    #modal_EditListing .modal-heading a {
        color: #2F9A57;
        font-size: 20px;
    }

    #modal_EditListing .filter_function {
        margin: 3px 20px 0 0;
        padding: 0 10px;
        display: flex;
        align-items: center;
    }

    #modal_EditListing .filter_function > select {
        font-size: 14px;
        font-weight: 500;
        padding: 3px 25px 3px 10px;
        border-radius: 3px;
        width: 200px;
        height: 29px;
        border: 1px solid #999;
        background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        margin-left: 10px;
    }

    #modal_EditListing .filter_function > select:nth-of-type(1) {
        margin-left: 0;
    }

    #modal_EditListing .filter_function > select:nth-of-type(4) {
        width: 320px;
    }

    #modal_EditListing .filter_function > button {
        height: 29px;
        width: 29px;
        padding: 2px;
        margin: 0 10px;
        cursor: pointer;
        vertical-align: middle;
        border: 1px solid #999;
        border-radius: 5px;
        background-color: #fff;
    }

    #modal_EditListing .list_function {
        width: 100%;
        padding: 10px 10px 2px;
    }

    .list_function .pagenation a {
        border-color: #2F9A57;
        color: #2F9A57;
    }

    .list_function .pagenation a:hover {
        border-color: #2F9A57;
        color: #FFF;
    }

    #modal_EditListing .tablebox {
        width: 100%;
        padding: 5px 10px 10px;
        border: none;
    }

    #modal_EditListing .tablebox table {
        width: 100%;
    }

    #modal_EditListing .tablebox thead tr th {
        background-color: #E5F7EB;
        padding: 10px;
        text-align: center;
        vertical-align: middle;
        font-size: 15px;
        font-weight: 700;
        color: #333;
        min-width: 50px;
        border: 1px solid #2F9A57;
        border-left: none;
    }

    #modal_EditListing .tablebox thead tr th:first-of-type {
        border-left: 1px solid #2F9A57;
    }

    #modal_EditListing .tablebox tbody tr:nth-of-type(2n) td {
        background-color: #DDD;
    }

    #modal_EditListing .tablebox tbody tr:hover:nth-of-type(2n) td {
        background-color: var(--orange01);
    }

    #modal_EditListing .tablebox tbody tr td {
        padding: 8px;
        text-align: center;
        vertical-align: middle;
        font-size: 16px;
        font-weight: 300;
        color: #333333;
        min-width: 50px;
        border-right: 1px solid #2F9A57;
        border-bottom: 1px solid #2F9A57;
        height: 100px;
    }

    #modal_EditListing .tablebox tbody tr td:first-of-type {
        border-left: 1px solid #2F9A57;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(12) button {
        border: 2px solid black;
        width: 34px;
        height: 34px;
        box-sizing: border-box;
        padding: 6px;
        line-height: 1.0;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(1),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(3),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(5),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(7) {
        width: 70px;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(2),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(4),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(6),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(9) {
        width: 200px;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(8) {
       width: 350px;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(10) {
       width: 240px;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(10) img {
        max-width: 100px;
        max-height: 100px;
    }

    #modal_EditListing .tablebox tbody tr td:nth-of-type(11),
    #modal_EditListing .tablebox tbody tr td:nth-of-type(12) {
       width: 100px;
    }

    #modal_EditListing .box-content {
        padding: 20px 20px 30px;
    }

    #modal_EditListing .box-content ul:first-of-type li:nth-of-type(even) {
        padding-bottom: 10px;
    }


    .block.A .box-content ul:first-of-type li:nth-of-type(even) {
        padding-bottom: 10px;
    }

    .list_attch {
        display: flex;
        align-items: center;
        margin-top: 5px;
    }

    .list_attch a.attch {
        color: #25a2b8;
        transition: .3s;
        margin: 0 0 0 5px;
        font-weight: 500;
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
        color: #2F9A57;
        background-color: #E5F7EB;
        padding-left: 20px;
        margin-left: -10px;
    }

    li.row_list {
        display: flex;
        margin: 12px 0 7px;
    }

    li.row_list div.label_btn {
        width: 180px;
        display: flex;
        align-items: center;
    }

    li.row_list div.label_btn a.btn_catalog {
        background-image: url('images/ui/btn_edit_green.svg');
        width: 16px;
        height: 16px;
        display: inline-block;
        margin-left: 10px;
    }

    li.row_list div.label_btn a.btn_export {
        width: 16px;
        height: 16px;
        display: inline-block;
        margin-left: 10px;
        color: #2F9A57;
        padding-bottom: 25px;
    }

    div.block > h6 > span {
        color: #2F9A57;
        background-color: #E5F7EB;
        font-size: 26px;
        font-weight: 700;
        border-radius: 50px;
        padding: 3px 20px 5px;
        margin-left: 50px;
        display: inline-block;
        vertical-align: text-bottom;
    }

    div.tablebox.notes {
        margin: 25px 0 50px!important;;
    }

    div.tablebox.notes ul.head > li:nth-of-type(1) {
        width: 35%!important;
        font-weight: 700!important;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    }

    div.tablebox.notes ul.head > li:nth-of-type(2) {
        font-size: 14px!important;
        font-weight: 500!important;
        background-color: white!important;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    }

    #phase2 .tablebox ul > li:nth-of-type(5) input, #phase3 .tablebox ul > li:nth-of-type(6) input {
        width: 120px;
        margin-bottom: 20px;
    }

    #phase2 .tablebox ul > li:nth-of-type(5) textarea, #phase3 .tablebox ul > li:nth-of-type(6) textarea {
        width: 90%;
    }

    #phase3 .tablebox ul > li.checker_result > span.green,
    #phase4 .tablebox ul > li.checker_result > span.green,
    #phase4 .tablebox ul > li.approver_result > span.green {
        color: green;
        margin-left: 10px;
    }

    #phase3 .tablebox ul > li.checker_result > span.red,
    #phase4 .tablebox ul > li.checker_result > span.red,
    #phase4 .tablebox ul > li.approver_result > span.red {
        color: red;
        margin-left: 10px;
    }

    #phase3 .tablebox ul > li.checker_result > div,
    #phase4 .tablebox ul > li.checker_result > div,
    #phase4 .tablebox ul > li.approver_result > div {
        margin-top: 5px;
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


    </style>


</head>

<body class="green">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">

        <!-- Blocks -->
        <div class="block A focus" style="position: relative;">
            <h6>Office Item Inventory Check<span>{{ record.request_no }} {{ record.check_name }}</span></h6>

            <div class="box-content" id="phase1" v-if="record.status == 1">

                <div class="headings">
                    <b class="tag focus">PHASE 1</b>
                    <b class="tag">Create Checking List by Checker</b>
                </div>

                <form>
                    <ul>
                        <li><b>Notes</b></li>
                        <li><textarea style="width:100%" v-model="notes"></textarea></li>


                        <li class="row_list">
                            <div class="label_btn">
                                <b>Checking List</b>
                                <a class="btn_catalog" href="javascript: void(0)" onclick="EditListing()"></a>
                            </div>

                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 20 筆資料  -->
                            <div class="list_function">
                                <div class="pagenation">
                                    <a class="prev" :disabled="it_page == 1" @click="it_pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in it_pages_10" @click="it_page=pg;" v-bind:style="[pg == it_page ? { 'background':'#2F9A57', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="it_page == it_pages.length" @click="it_nex_page();">Next 10</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="tablebox">
                                <ul class="head">
                                    <li>Code</li>
                                    <li>Image</li>
                                    <li>Particulars</li>
                                    <li>Qty in Stock</li>
                                    <li>Action</li>
                                </ul>

                                <ul v-for="(item,index) in phase" :key="index" :class="[is_toIndex === (it_page - 1 ) * it_perPage + index ? 'shake' : '' ]" :data-attr="index">
                                    <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                                    <li>
                                        <a :href="item.url" target="_blank" v-if="item.url">
                                            <img :src="item.url" v-if="item.url">
                                        </a>
                                    </li>
                                    <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>
                                    <li>
                                        {{ item.qty }}
                                    </li>
                                    <li>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-up"
                                           @click="_set_up((it_page - 1 ) * it_perPage + index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-down"
                                           @click="_set_down((it_page - 1 ) * it_perPage + index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-trash-alt" @click="_del(item.id)"></i>
                                    </li>
                                </ul>

                            </div>

                        </li>


                    </ul>

                    <div class="btnbox">
                        <a class="btn red" @click="reset" title="Clear All Encoded Content">Reset</a>
                        <a class="btn" @click="save(1, notes)" title="Temporarily Save Encoded Content">Save</a>
                        <a class="btn" @click="goto_phase2" title="Go to Next Phase: Inventory Count by Checker">Next Phase</a>
                    </div>

                </form>

            </div>



            <div class="box-content" id="phase2" v-if="record.status == 2">

                <div class="headings">
                    <b class="tag focus">PHASE 2</b>
                    <b class="tag">Inventory Count by Checker</b>
                </div>


                <div class="tablebox notes">
                    <ul class="head">
                        <li>PHASE 1: Create Checking List by Checker<br>Notes</li>
                        <li>{{ notes }}</li>
                    </ul>
                    <ul class="head">
                        <li>PHASE 3: Review by Approver<br>Notes</li>
                        <li>{{ notes3 }}</li>
                    </ul>
                </div>


                <form>
                    <ul>
                        <li><b>Notes</b></li>
                        <li><textarea style="width:100%" v-model="notes2"></textarea></li>


                        <li class="row_list">
                            <div class="label_btn">
                                <b>Checking List</b>

                                <!-- 匯出清單，可以套用利用 office_items_catalog.php 的匯出功能去修改 -->
                                <a class="btn_export" href="javascript: void(0)" @click="export_list()">
                                    <i aria-hidden="true" class="fas fa-file-export"></i>
                                </a>
                            </div>

                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 20 筆資料  -->
                            <div class="list_function">
                                <div class="pagenation">
                                    <a class="prev" :disabled="it_page == 1" @click="it_pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in it_pages_10" @click="it_page=pg;" v-bind:style="[pg == it_page ? { 'background':'#2F9A57', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="it_page == it_pages.length" @click="it_nex_page();">Next 10</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="tablebox">
                                <ul class="head">
                                    <li>Code</li>
                                    <li>Image</li>
                                    <li>Particulars</li>
                                    <li>Qty in Stock</li>
                                    <li>Qty Counted</li>
                                </ul>

                                <ul v-for="(item,index) in phase" :key="index" :class="[is_toIndex === (it_page - 1 ) * it_perPage + index ? 'shake' : '' ]" :data-attr="index">
                                    <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                                    <li>
                                        <a :href="item.url" target="_blank" v-if="item.url">
                                            <img :src="item.url" v-if="item.url">
                                        </a>
                                    </li>
                                    <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>

                                    <li>{{item.qty}}</li>

                                    <li>
                                        <input type="number" min=0 v-model="item.qty1" placeholder="Qty">
                                        <br>
                                        <textarea v-model="item.note" placeholder="Remarks"></textarea>
                                    </li>

                                </ul>

                            </div>

                        </li>


                    </ul>

                    <div class="btnbox">
                        <a class="btn red" @click="reset2" title="Clear All Encoded Content">Reset</a>
                        <a class="btn" @click="save(2, notes2)" title="Temporarily Save Encoded Content">Save</a>
                        <a class="btn red" @click="goto_phase1" title="Go to Previous Phase: Create Checking List by Checker">Previous Phase</a>
                        <a class="btn" @click="goto_phase3" title="Go to Next Phase: Review by Approver">Next Phase</a>
                    </div>

                </form>

            </div>



            <div class="box-content" id="phase3" v-if="record.status == 3">

                <div class="headings">
                    <b class="tag focus">PHASE 3</b>
                    <b class="tag">Review by Approver</b>
                </div>


                <div class="tablebox notes">
                    <ul class="head">
                        <li>PHASE 1: Create Checking List by Checker<br>Notes</li>
                        <li>{{ notes }}</li>
                    </ul>
                    <ul class="head">
                        <li>PHASE 2: Inventory Count by Checker<br>Notes</li>
                        <li>{{ notes2 }}</li>
                    </ul>
                </div>


                <form>
                    <ul>
                        <li><b>Notes</b></li>
                        <li><textarea style="width:100%" v-model="notes3"></textarea></li>


                        <li class="row_list">
                            <div class="label_btn">
                                <b>Checking List</b>

                                <!-- 匯出清單，可以套用利用 office_items_catalog.php 的匯出功能去修改，和 Phase 2 的匯出結果欄位內容不相同 -->
                                <a class="btn_export" href="javascript: void(0)" @click="export_list3()">
                                    <i aria-hidden="true" class="fas fa-file-export"></i>
                                </a>
                            </div>

                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 20 筆資料  -->
                            <div class="list_function">
                                <div class="pagenation">
                                    <a class="prev" :disabled="it_page == 1" @click="it_pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in it_pages_10" @click="it_page=pg;" v-bind:style="[pg == it_page ? { 'background':'#2F9A57', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="it_page == it_pages.length" @click="it_nex_page();">Next 10</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="tablebox">
                                <ul class="head">
                                    <li>Code</li>
                                    <li>Image</li>
                                    <li>Particulars</li>
                                    <li>Qty in Stock</li>
                                    <li>Qty Counted</li>
                                    <li>Comment</li>
                                </ul>

                                <ul v-for="(item,index) in phase" :key="index">
                                    <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                                    <li>
                                        <a :href="item.url" target="_blank" v-if="item.url">
                                            <img :src="item.url" v-if="item.url">
                                        </a>
                                    </li>
                                    <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>

                                    <!-- 庫存的數量 -->
                                    <li>{{item.qty}}</li>

                                    <!-- Checker 輸入的數量 和 Checker 輸入的remarks -->
                                    <li class="checker_result">

                                        {{item.qty1}}

                                        <!-- 如果 盤點數量 大於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="green" v-if="parseInt(item.qty1) > parseInt(item.qty)">
                                            ( ↑ {{item.qty1 - item.qty}} )
                                        </span>

                                        <!-- 如果 盤點數量 小於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="red" v-if="parseInt(item.qty1) < parseInt(item.qty)">
                                            ( ↓ {{item.qty - item.qty1}} )
                                        </span>

                                        <div>{{ item.note }}</div>
                                    </li>

                                    <li>
                                        <input placeholder="Qty" type="number" min=1 v-model="item.qty2">
                                        <textarea v-model="item.comment" placeholder="Comment"></textarea>
                                    </li>

                                </ul>

                            </div>

                        </li>


                    </ul>

                    <div class="btnbox">
                        <a class="btn red" @click="reset3" title="Clear All Encoded Content">Reset</a>
                        <a class="btn" @click="save(3, notes3)" title="Temporarily Save Encoded Content">Save</a>
                        <a class="btn red" @click="backto_phase2" title="Go to Previous Phase: Inventory Count by Checker">Reject</a>
                        <a class="btn" @click="goto_phase4" title="Go to Next Phase: Inventory Check Completed">Approve</a>
                    </div>

                </form>



            </div>



            <div class="box-content" id="phase4" v-if="record.status == 4">

                <div class="headings">
                    <b class="tag focus">PHASE 4</b>
                    <b class="tag">Inventory Check Completed</b>
                </div>


                <div class="tablebox notes">
                    <ul class="head">
                        <li>PHASE 1: Create Checking List by Checker<br>Notes</li>
                        <li>{{ notes }}</li>
                    </ul>
                    <ul class="head">
                        <li>PHASE 2: Inventory Count by Checker<br>Notes</li>
                        <li>{{ notes2 }}</li>
                    </ul>
                    <ul class="head">
                        <li>PHASE 3: Review by Approver<br>Notes</li>
                        <li>{{ notes3 }}</li>
                    </ul>
                </div>


                <form>
                    <ul>
                        <!-- <li><b>Notes</b></li>
                        <li><textarea style="width:100%" v-model="notes4"></textarea></li> -->


                        <li class="row_list">
                            <div class="label_btn">
                                <b>Checking List</b>
                                <!-- 匯出清單，可以套用利用 office_items_catalog.php 的匯出功能去修改，和 Phase 2 的匯出結果欄位內容不相同 -->
                                <a class="btn_export" href="javascript: void(0)" @click="export_list4()">
                                    <i aria-hidden="true" class="fas fa-file-export"></i>
                                </a>
                            </div>

                            <!-- 分頁功能，下方的 tablebox 的內容要做分頁，每一頁 20 筆資料  -->
                            <div class="list_function">
                            <div class="pagenation">
                                    <a class="prev" :disabled="it_page == 1" @click="it_pre_page();">Prev 10</a>

                                    <a class="page" v-for="pg in it_pages_10" @click="it_page=pg;" v-bind:style="[pg == it_page ? { 'background':'#2F9A57', 'color': 'white'} : { }]">{{ pg }}</a>

                                    <a class="next" :disabled="it_page == it_pages.length" @click="it_nex_page();">Next 10</a>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="tablebox">
                                <ul class="head">
                                    <li>Code</li>
                                    <li>Image</li>
                                    <li>Particulars</li>
                                    <li>Qty in Stock</li>
                                    <li>Qty Counted</li>
                                    <li>Comment</li>
                                </ul>

                                <ul v-for="(item,index) in phase" :key="index">
                                    <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                                    <li>
                                        <a :href="item.url" target="_blank" v-if="item.url">
                                            <img :src="item.url" v-if="item.url">
                                        </a>
                                    </li>
                                    <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>

                                    <!-- 庫存的數量 -->
                                    <li>{{item.qty}}</li>

                                    <!-- Checker 輸入的數量 和 Checker 輸入的remarks -->
                                    <li class="checker_result">

                                        {{ item.qty1 }}

                                        <!-- 如果 盤點數量 大於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="green" v-if="parseInt(item.qty1) > parseInt(item.qty)">
                                            ( ↑ {{item.qty1 - item.qty}} )
                                        </span>

                                        <!-- 如果 盤點數量 小於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="red" v-if="parseInt(item.qty1) < parseInt(item.qty)">
                                            ( ↓ {{item.qty - item.qty1}} )
                                        </span>

                                        <div>{{item.note}}</div>
                                    </li>

                                    <!-- Approver 輸入的數量 和 Approver 輸入的comment -->
                                    <li class="approver_result">

                                        {{ item.qty2 }}

                                        <!-- 如果 Approver的數量 大於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="green" v-if="item.qty2 != '' && parseInt(item.qty2) > parseInt(item.qty)">
                                            ( ↑ {{item.qty2 - item.qty}} )
                                        </span>

                                        <!-- 如果 Approver的數量 小於 庫存數量，則下方的 <span> 結構需要創造出來 -->
                                        <span class="red" v-if="item.qty2 != '' && parseInt(item.qty2) < parseInt(item.qty)">
                                            ( ↓ {{item.qty - item.qty2}} )
                                        </span>

                                        <div>{{item.comment}}</div>
                                    </li>


                                </ul>

                            </div>

                        </li>


                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="go_to_check_mgt()">Go Back To Inventory Check Mgt.</a>
                    </div>

                </form>

            </div>



            <div id="modal_EditListing" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-heading">
                        <h6>Office Items Catalog</h6>
                        <a href="javascript: void(0)" onclick="EditListing()"><i class="fa fa-times fa-lg"
                                                                                 aria-hidden="true"></i></a>
                    </div>


                    <div class="filter_function">
                            <select v-model='lv1' v-on:change="getLevel2()">
                                <option value="">----- Main Category -----</option>
                                <!-- Main Category 的選項內容格式為：Main Category(Code)，例如像是：OFFICE SUPPLIES(01) -->
                                <option :value="item.code" v-for="(item, index) in level1">{{ item.category }}({{
                                    item.code }})
                                </option>
                            </select>

                            <select v-model='lv2' v-on:change="getLevel3()">
                                <option value="">----- Sub Category -----</option>
                                <!-- Sub Category 的選項內容格式為：Sub Category(Code)，例如像是：BALLPEN(01)。當使用者選擇不同的 Main Category 時，Sub Category 的 select 只會載入特定 Main Category 底下的 Sub Category 到 select 裡面 -->
                                <option :value="item.code" v-for="(item, index) in level2">{{ item.category }}({{
                                    item.code }})
                                </option>
                            </select>

                            <select v-model='lv3' v-on:change="getLevel4()">
                                <option value="">----- Brand -----</option>
                                <!-- Brand 的選項內容格式為：Brand(Code)，例如像是：HP(01)。當使用者在某一層的的 select 選擇了某一個值之後，下一層的 select 只會載入階層架構下該節點的子節點 到 select 裡面 -->
                                <option :value="item.code" v-for="(item, index) in level3">{{ item.category }}({{
                                    item.code }})
                                </option>
                            </select>

                            <select v-model='lv4'>
                                <option value="">----- Description -----</option>
                                <option :value="item.code" v-for="(item, index) in level4">{{ item.category }}({{
                                    item.code }})
                                </option>
                            </select>

                            <button style="margin-left: 20px;" @click="filter_apply_new()"><i aria-hidden="true"
                                                                                              class="fas fa-filter"></i>
                            </button>

                            <button @click="add_filtered()" style="width: 160px;">Add All Filtered</button>
                            <button @click="clear()" style="width: 60px;">Clear</button>

                        </div>


                    <div class="list_function">



                        <!-- 分頁功能 -->
                        <!-- 這個頁面需要做分頁，每一頁 20 筆資料  -->
                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#2F9A57', 'color': 'white'} : { }]">{{ pg }}</a>

                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
                        </div>

                    </div>


                    <div class="tablebox">
                        <table id="showUser1">
                            <thead>
                            <tr>
                                <th colspan="2">
                                    MAIN CATEGORY
                                </th>
                                <th colspan="2">
                                    SUB CATEGORY
                                </th>
                                <th colspan="2">
                                    BRAND
                                </th>
                                <th colspan="2">
                                    DESCRIPTION
                                </th>
                                <th>
                                    CODE
                                </th>
                                <th>
                                    IMAGE
                                </th>
                                <th>
                                    QTY IN STOCK
                                </th>
                                <th>
                                    ACTION
                                </th>
                            </tr>
                            </thaed>

                            <tbody>

                            <tr v-for="(item, index) in items">
                                <td>{{ item.code1 }}</td>
                                <td>{{ item.cat1 }}</td>
                                <td>{{ item.code2 }}</td>
                                <td>{{ item.cat2 }}</td>
                                <td>{{ item.code3 }}</td>
                                <td>{{ item.cat3 }}</td>
                                <td>{{ item.code4 }}</td>
                                <td>{{ item.cat4 }}</td>
                                <td>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</td>
                                <td>
                                    <a href="item.url" target="_blank" v-if="item.url">
                                        <img :src="item.url" v-if="item.url">
                                    </a>
                                </td>
                                <td>{{ item.qty }}</td>
                                <td>
                                    <button id="edit01" @click="_add_criterion(item)"><i aria-hidden="true" class="fas fa-caret-right"></i></button>
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
<script src="js/office_item_inventory_check.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

</html>