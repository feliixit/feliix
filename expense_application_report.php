<?php include 'check.php';?>
<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

    <!-- SEO -->
    <title>FELIIX template</title>
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
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    
    

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>



    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');

            dialogshow($('.list_function .popupblock a.filtering'),$('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .popupblock a.sorting'),$('.list_function .dialog.d-sort'));
          
        })
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">
        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.fifth header nav a, body.fifth header nav a:link {
            color: #000;
        }

        body.fifth header nav a:hover {
            color: #333;
        }

        body.fifth header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.fifth header nav ul.info {
            margin-bottom: 0;
        }

        body.fifth header nav ul.info b {
            font-weight: bold;
        }
    
        body.fifth .mainContent>.tags a.focus {
            background-color: #EA0029;
        }

        body.fifth .mainContent>.block {
            border: none;
            border-top: 2px solid #EA0029;
        }

        body.fifth .list_function .popupblock {
            position: relative;
            float: left;
            margin-right: 10px;
        }

        body.fifth .list_function .popupblock dl div.half:nth-of-type(odd){
            width: 48.5%;
        }

        .list_function a.filtering, .list_function a.sorting, .list_function a.exporting {
            width: 30px;
            height: 30px;
            background-color: #EA0029;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url(images/ui/btn_filter_white.svg);
        }

        .list_function a.sorting {
            background-image: url(images/ui/btn_sort_white.svg);
        }

        .list_function a.exporting {
            background-image: url(images/ui/btn_export_white.svg);
        }

        .list_function .dialog {
            border-color: #EA0029;
        }

        .list_function .dialog::before {
            border-color: transparent transparent transparent #EA0029;
        }

        .list_function .dialog h6, .list_function .dialog dt{
            color: #000000;
        }

        body.fifth .list_function .pagenation{
            padding-top: 5px;
        }

        body.fifth .list_function .pagenation a {
            color: #EA0029;
            border-color: #EA0029;
        }

        body.fifth .block .tablebox {
            border-top: 2px solid #EA0029;
            border-left: 2px solid #EA0029;
            width: 120%;
        }

        body.fifth .block .details .tablebox {
            width: 100%;
        }

        body.fifth .block .tablebox ul.head {
            background-color: rgba(255, 89, 90, 0.4);
        }

        body.fifth .block .tablebox>ul>li {
            border-bottom: 2px solid #EA0029;
            border-right: 2px solid #EA0029;
            font-size: 12px;
        }

        body.fifth .block .tablebox>ul>li:nth-of-type(6) {
            max-width: 260px;
        }

        body.fifth .block .tablebox>ul>li i {
            color: #EA0029;
        }

        .block .function input[type="month"] {
            border: 2px solid var(--sec03);
        }

        div.details {
            margin-top: 15px;
        }

        .block .tablebox li>a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 0;
            display: block;
        }

        div.tablebox.listing {
            margin-top: 10px;
        }

        div.box-content form {
            border: 3px solid #EA0029;
            margin-top: 40px;
            padding: 15px 15px 0;
            box-sizing: border-box;
        }

        div.details>span {
            font-size: x-large;
            font-weight: 700;
            color: #EA0029;
            display: block;
            margin-top: 20px;
        }

        .tableframe {
            overflow: auto;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(4),
        .tableframe .tablebox.lv1 li:nth-of-type(10) {
            color: #000000;
            min-width: auto;
        }

        #modal_Details {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
            display: block;
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

        #modal_Details>.modal-content {
            width: 90%;
            max-height: 95vh;
            margin: auto;
            border: 3px solid #EA0029;
            padding: 20px 25px;
            background-color: white;
            overflow-y: auto;
        }

        #modal_Details .modal-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #modal_Details .modal-heading h6 {
            color: #EA0029;
            border-bottom: none;
        }

        #modal_Details .modal-heading a {
            font-size: 20px;
        }

        header .headerbox {
            background-color: #EA0029;
        }

        body.fifth .details .tablebox>ul>li {
            font-size: 14px;
        }

        body.fifth .list_function .pagenation a:hover {
            background-color: #EA0029;
            color: #FFF;
        }

        ul.dropdown-menu.inner li {
            display: block;
            border-right: none;
            padding: 5px;
        }

        .dropdown-menu > .bs-searchbox > input[type='search'] {
            border: 1px solid #ced4da;
        }

        .bootstrap-select.show-tick .dropdown-menu .selected span.check-mark {
            top: 15px;
        }

    </style>


</head>

<body class="fifth">

    <div id="app" class="bodybox" style="min-height: 150vh;">
        <div class="mask" :ref="'mask'" style="display:none"></div>
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag A focus">Expense Application Tracker</a>
            </div>
            <!-- Blocks -->
            <div class="block A focus">

                <div class="list_function" style="margin-top: 10px;">

                    <!-- 篩選 -->
                    <div class="popupblock">
                        <a class="filtering" id="btn_filter"></a>
                        <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt style="margin-bottom:-15px;">Request No.</dt>
                                    <div class="half">
                                        <dt>From</dt>
                                        <dd><input type="number" min="1" step="1" v-model="fil_request_no_lower"></dd>
                                    </div>
                                    <div class="half">
                                        <dt>To</dt>
                                        <dd><input type="number" min="1" step="1" v-model="fil_request_no_upper"></dd>
                                    </div>

                                    <dt style="margin-top: 2px;">Requestor</dt>
                                    <dd>
                                        <select v-model="fil_creator">
                                            <option value="">
                                            <option v-for="item in creators" :value="item.username"
                                                    :key="item.username">
                                                {{ item.username }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt>Type</dt>
                                    <dd>
                                        <select v-model="fil_type">
                                            <option value=""></option>
                                            <option value="1">New</option>
                                            <option value="2">Reimbursement</option>
                                            <option value="3">Petty Cash Replenishment</option>
                                        </select>
                                    </dd>

                                    <dt>Status</dt>
                                    <dd>
                                        <!-- <select v-model="fil_status">
                                            <option value=""></option>
                                            <option value="1">For Check</option>
                                            <option value="2">For Approve</option>
                                            <option value="3">For Release</option>
                                            <option value="4">For Liquidate</option>
                                            <option value="5">For Verify</option>
                                            <option value="6">Completed</option>
                                            <option value="7">Rejected</option>
                                            <option value="8">Withdrawn</option>
                                        </select> -->

                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="No status selected" id="tag01" v-model="fil_status">

                                            <option value=""></option>
                                            <option value="1">For Check</option>
                                            <option value="2">For Approve</option>
                                            <option value="3">For Release</option>
                                            <option value="4">For Liquidate</option>
                                            <option value="5">For Verify</option>
                                            <option value="6">Completed</option>
                                            <option value="7">Rejected</option>
                                            <option value="8">Withdrawn</option>

                                    </select>
                                    </dd>

                                    <dt>Project Name</dt>
                                    <dd>
                                        <select v-model="fil_project_name">
                                            <option value=""></option>
                                            <option v-for="(item, index) in projects" :value="item.project_name" :key="item.project_name">
                                                {{ item.project_name }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 15px;">Type of Amount</dt>
                                    <dd style="margin-bottom: -5px;">
                                        <select v-model="fil_amount_type">
                                            <option value=""></option>
                                            <option value="1">Requested Amount</option>
                                            <option value="2">Actual Amount</option>
                                        </select>
                                    </dd>
                                    <div class="half">
                                        <dt>Lower bound</dt>
                                        <dd><input type="number" v-model="fil_amount_lower"></dd>
                                    </div>
                                    <div class="half">
                                        <dt>Upper bound</dt>
                                        <dd><input type="number" v-model="fil_amount_upper"></dd>
                                    </div>

                                    <dt style="margin-top: 5px;">Type of Date</dt>
                                    <dd style="margin-bottom: -5px;">
                                        <select v-model="fil_type_date">
                                            <option value=""></option>
                                            <option value="8">Application Time</option>
                                            <option value="1">Date Needed</option>
                                            <option value="2">Date Checked</option>
                                            <option value="3">Date Approved (1st)</option>
                                            <option value="4">Date Approved (2nd)</option>
                                            <option value="5">Date Released</option>
                                            <option value="6">Date Liquidated</option>
                                            <option value="7">Date Verified</option>
                                        </select>
                                    </dd>
                                    <div class="half">
                                        <dt>Start date</dt>
                                        <dd><input type="date" v-model="fil_date_start"></dd>
                                    </div>
                                    <div class="half">
                                        <dt>End date</dt>
                                        <dd><input type="date" v-model="fil_date_end"></dd>
                                    </div>
                                </dl>

                                <div class="btnbox"><a class="btn small" @click="filter_clear()">Cancel</a><a
                                        class="btn small" @click="filter_remove()">Clear</a> <a class="btn small green"
                                                                                                @click="filter_apply(1)">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 排序 -->
                    <div class="popupblock">
                        <a class="sorting" id="btn_sort"></a>
                        <div id="sort_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <div class="half">
                                        <dt>1st Criterion</dt>
                                        <dd>
                                            <select v-model="od_factor1">
                                                <option value="0"></option>
                                                <option value="1">
                                                    Request No.
                                                </option>
                                                <option value="12">
                                                    Project Name
                                                </option>
                                                <option value="2">
                                                    Application Time
                                                </option>
                                                <option value="3">
                                                    Requested Amount
                                                </option>
                                                <option value="4">
                                                    Actual Amount
                                                </option>
                                                <option value="5">
                                                    Date Needed
                                                </option>
                                                <option value="6">
                                                    Date Checked
                                                </option>
                                                <option value="7">
                                                    Date Approved (OP)
                                                </option>
                                                <option value="8">
                                                    Date Approved (MD)
                                                </option>
                                                <option value="9">
                                                    Date Released
                                                </option>
                                                <option value="10">
                                                    Date Liquidated
                                                </option>
                                                <option value="11">
                                                    Date Verified
                                                </option>
                                            </select>
                                        </dd>
                                    </div>

                                    <div class="half">
                                        <dt></dt>
                                        <dd>
                                            <select v-model="od_factor1_order">
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
                                            <select v-model="od_factor2">
                                                <option value="0"></option>
                                                <option value="1">
                                                    Request No.
                                                </option>
                                                <option value="12">
                                                    Project Name
                                                </option>
                                                <option value="2">
                                                    Application Time
                                                </option>
                                                <option value="3">
                                                    Requested Amount
                                                </option>
                                                <option value="4">
                                                    Actual Amount
                                                </option>
                                                <option value="5">
                                                    Date Needed
                                                </option>
                                                <option value="6">
                                                    Date Checked
                                                </option>
                                                <option value="7">
                                                    Date Approved (OP)
                                                </option>
                                                <option value="8">
                                                    Date Approved (MD)
                                                </option>
                                                <option value="9">
                                                    Date Released
                                                </option>
                                                <option value="10">
                                                    Date Liquidated
                                                </option>
                                                <option value="11">
                                                    Date Verified
                                                </option>
                                            </select>
                                        </dd>
                                    </div>

                                    <div class="half">
                                        <dt></dt>
                                        <dd>
                                            <select v-model="od_factor2_order">
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
                                <div class="btnbox"><a class="btn small" @click="order_clear">Cancel</a><a
                                        class="btn small" @click="order_remove">Clear</a><a class="btn small green"
                                                                                            @click="filter_apply(1)">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- 匯出 -->
                    <div class="popupblock">
                        <a class="exporting" id="btn_export" @click="export_petty_list()"></a>
                    </div>


                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply();">Prev 10</a>

                        <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[pg == page ? { 'background':'red', 'color': 'white'} : { }]">{{ pg }}</a>

                        <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                    </div>
                </div>

                <div class="tableframe" style="margin-top: 5px;">
                    <div class="tablebox lv1">
                        <ul class="head">
                            <li>Request No.</li>
                            <li>Requestor</li>
                            <li>Application Time</li>
                            <li>Type</li>
                            <li>Status</li>
                            <li>Project Name</li>
                            <li>Requested Amount</li>
                            <li>Actual Amount</li>

                            <li>Date Needed</li>
                            <li>Date Checked</li>
                            <li>Date Approved</li>
                            <li>Date Released</li>
                            <li>Date Liquidated</li>
                            <li>Date Verified</li>
                            <li>Details</li>
                        </ul>

                        <ul v-for='(record, index) in displayedRecord' :key="index">
                            <li>{{ record.request_no }}</li>
                            <li>{{ record.requestor }}</li>
                            <li>{{ record.created_at }}</li>
                            <li>{{ record.request_type }}</li>
                            <li>{{ record.desc }}</li>
                            <li>{{ record.project_name1 }}</li>
                            <li>{{ !(record.total) ? '' : Number(record.total).toLocaleString() }}</li>
                            <li>{{ !(record.amount_verified) ? '' : Number(record.amount_verified).toLocaleString() }}</li>
                            <li>{{ record.date_requested }}</li>
                            <li>{{ record.checked_date }}</li>
                            <li v-if="record.approve1_date == '' && record.approve2_date == ''"></li>
                            <li v-if="record.approve1_date != '' || record.approve2_date != ''">{{ record.approve1_date == "" ? "---" : record.approve1_date }}<br>{{ record.approve2_date == "" ? "---" : record.approve2_date }}</li>
                            <li>{{ record.release_date }}</li>
                            <li>{{ record.liquidate_date }}</li>
                            <li>{{ record.verified_date }}</li>
                            <li>
                                <a @click="show_detail(record.id)"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></a>
                            </li>
                        </ul>


                    </div>
                </div>



                <div id="modal_Details" class="modal" v-if="view_detail == true">

                    <!-- Modal content -->
                    <div class="modal-content">
                        <div class="modal-heading">
                            <h6>Details</h6>
                            <a @click="show_detail(0)"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a>
                        </div>

                        <div class="details">
                            <div class="tablebox">
                                <ul class="head">
                                    <li class="head">Request No.</li>
                                    <li>{{record.request_no}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Application Time</li>
                                    <li>{{ record.created_at }}</li>
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
                                            {{ item.action }} <a v-if="item.reason != ''">: {{ item.reason }}</a> ({{ item.actor }} at {{ item.created_at }})
                                        </p>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="head">Date Needed</li>
                                    <li>{{record.date_requested}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Type</li>
                                    <li>{{record.request_type}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Project Name</li>
                                    <li>{{ record.project_name1}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Reason</li>
                                    <li v-if="record.rtype==''">{{ record.project_name}}</li>
                                    <li v-if="record.rtype=='team'">{{ 'Team Building (' + record.department + ') — ' + record.project_name }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Requested Amount
                                    </li>
                                    <li>{{ isNaN(record.total) ? "" : Number(record.total).toLocaleString() }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Attachments</li>
                                    <li><a v-for='(item, index) in record.items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="head">Payable to
                                    </li>
                                    <li>{{ (record.payable_other == "") ? "Requestor" : (( typeof record.payable_other == "undefined" ) ? "":  "Other:" + record.payable_other) }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Remarks or Payment Instructions
                                    </li>
                                    <li>{{ record.remark }}</li>
                                </ul>
                            </div>

                            <div class="tablebox listing">
                                <ul class="head">
                                    <li>Payee</li>
                                    <li>Particulars</li>
                                    <li>Price</li>
                                    <li>Qty</li>
                                    <li>Amount</li>
                                </ul>
                                <ul v-for='(item, index) in record.list' :key="index">
                                    <li>{{ item.payee }}</li>
                                    <li>{{ item.particulars }}</li>
                                    <li>{{ Number(item.price).toLocaleString() }}</li>
                                    <li>{{ Number(item.qty).toLocaleString() }}</li>
                                    <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                </ul>

                            </div>


                            <span>Additional Info</span>
                            <div class="tablebox">
                                <ul>
                                    <li class="head">Account</li>
                                    <li>{{ record.info_account }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Category</li>
                                    <li>{{ record.info_category }} {{ (record.sub_category != "" ? ' >> ' + record.sub_category : "") }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Remarks or Payment Instructions</li>
                                    <li>{{ record.info_remark }}{{ (record.info_remark_other != "" ? ' : ' + record.info_remark_other : "") }}</li>
                                </ul>
                            </div>


                            <div class="tablebox" style="margin-top: 60px; margin-bottom: 20px;">
                                <ul class="head">
                                    <li class="head">Request No.</li>
                                    <li>{{record.request_no}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Total Amount Requested
                                    </li>
                                    <li>{{ isNaN(record.total) ? "" : Number(record.total).toLocaleString() }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Date Released</li>
                                    <li>{{record.release_date}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Proof of Release</li>
                                    <li><a v-for='(item, index) in record.release_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="head">Date Liquidated</li>
                                    <li>{{(record.request_type == "New") ? record.liquidate_date : "---"}}</li>
                                </ul>
                                <ul>
                                <li class="head">Total Amount in Liquidation Listing</li>
                                    <li>{{ (record.request_type == "New") ?  (!record.total_amount_liquidate ? "" : Number(record.total_amount_liquidate).toLocaleString()) : "---" }}</li>
                                </ul>
                                
                                <ul>
                                    <li class="head">Amount Liquidated</li>
                                    <li>{{ (record.request_type == "New") ?  (!record.amount_liquidated ? "" : Number(record.amount_liquidated).toLocaleString()) : "---" }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Amount of Return Money</li>
                                    <li>{{ (record.request_type == "New") ?  (!record.amount_of_return ? "" : Number(record.amount_of_return).toLocaleString()) : "---" }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Method to Return Money</li>
                                    <li>{{ record.method_of_return }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Liquidation Files</li>
                                    <li><a v-if="record.request_type == 'New'" v-for='(item, index) in record.liquidate_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                        <div v-if="record.request_type == 'Reimbursement' || record.request_type == 'Petty Cash Replenishment'">---</div>
                                    </li>
                                </ul>
                                <ul>
                                    <li class="head">Remarks</li>
                                    <li>{{ (record.request_type == "New") ? record.remark_liquidated : "---" }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Date Verified</li>
                                    <li>{{(record.request_type == "New") ? record.verified_date : "---"}}</li>
                                </ul>
                                <ul>
                                    <li class="head">Actual Amount After Verification</li>
                                    <li>{{ (record.request_type == "New") ? (!record.amount_verified  ? "" : Number(record.amount_verified).toLocaleString()) : "---" }}</li>
                                </ul>
                                <ul>
                                    <li class="head">Proof of Return or Release</li>
                                    <li><a v-if="record.request_type == 'New'" v-for='(item, index) in record.verified_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                        <div v-if="record.request_type == 'Reimbursement' || record.request_type == 'Petty Cash Replenishment'">---</div>
                                    </li>
                                </ul>
                            </div>
                            
                            <span>Liquidation Listing</span>
                            <div class="tablebox listing">
                                <ul class="head">
                                    <li>Vendor</li>
                                    <li>Particulars</li>
                                    <li>Price</li>
                                    <li>Qty</li>
                                    <li>Amount</li>
                                </ul>
                                <ul v-for='(item, index) in record.apply_for_petty_liquidate' :key="index">
                                    <li>{{ item.payee }}</li>
                                    <li>{{ item.particulars }}</li>
                                    <li>{{ Number(item.price).toLocaleString() }}</li>
                                    <li>{{ Number(item.qty).toLocaleString() }}</li>
                                    <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                </ul>

                            </div>



                            <div class="btnbox"><a class="btn" @click="export_petty">Export Voucher</a></div>

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
<script defer src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/expense_application_report.js"></script>

</html>