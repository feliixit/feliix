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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">



    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');
        })
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">
        body.fifth .mainContent>.tags a.focus {
            background-color: #EA0029;
        }

        body.fifth .mainContent>.block {
            border: none;
            border-top: 2px solid #EA0029;
        }

        body.fifth .list_function .pagenation a {
            color: #EA0029;
            border-color: #EA0029;
        }

        body.fifth .block .tablebox {
            border-top: 2px solid #EA0029;
            border-left: 2px solid #EA0029;
        }

        body.fifth .block .tablebox ul.head {
            background-color: rgba(255, 89, 90, 0.4);
        }

        body.fifth .block .tablebox>ul>li {
            border-bottom: 2px solid #EA0029;
            border-right: 2px solid #EA0029;
            font-size: 12px;
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
    </style>


</head>

<body class="fifth">

    <div id="app" class="bodybox">
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
                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>

                        <a class="page" v-for="pg in pages" @click="page=pg" v-bind:style="[pg == page ? { 'background':'red', 'color': 'white'} : { }]">{{ pg }}</a>

                        <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
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
                            <li>Requested Amount</li>
                            <li>Actual Amount</li>

                            <li>Date Requested</li>
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
                                    <li class="head">Date Requested</li>
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
                                    <li>{{ record.project_name}}</li>
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


                            <div class="tablebox" style="margin-top: 60px;">
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
                                    <li class="head">Amount Liquidated</li>
                                    <li>{{ (record.request_type == "New") ?  (!record.amount_liquidated ? "" : Number(record.amount_liquidated).toLocaleString()) : "---" }}</li>
                                </ul>

                                <ul>
                                    <li class="head">Liquidation Files</li>
                                    <li><a v-if="record.request_type == 'New'" v-for='(item, index) in record.liquidate_items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                        <div v-if="record.request_type == 'Reimbursement'">---</div>
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
                                        <div v-if="record.request_type == 'Reimbursement'">---</div>
                                    </li>
                                </ul>
                            </div>

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

<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/expense_application_report.js"></script>

</html>