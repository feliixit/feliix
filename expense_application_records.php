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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />

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


    <!-- CSS for current webpage -->
    <style type="text/css">
        .block .function input[type="month"] {
            border: 2px solid var(--sec03);
        }

        div.details {
            margin-top: 40px;
        }

        .block .tablebox li>a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 0;
            display: block;
        }

        div.tablebox.listing {
            margin-top: 15px;
        }

        /* div.tablebox.listing ul.head li {
            background-color: #CCDCEE !important;
        } */
    </style>

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
    color: var(--green01);
    font-size: 25px;
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

body.green input[type=date] {
    border: 2px solid #2F9A57;
    padding: 5px;
    background-color: transparent;
}

#modal_EditListing {
    display: none;
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    margin: auto;
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
}

#modal_EditListing .modal-heading h6 {
    color: #2F9A57;
    border-bottom: none;
}

#modal_EditListing .modal-heading a {
    color: #2F9A57;
    font-size: 20px;
}

#modal_EditListing .box-content {
    padding: 20px 20px 30px;
}

#modal_EditListing .box-content ul:first-of-type li:nth-of-type(even) {
    padding-bottom: 10px;
}

#modal_EditListing .tablebox li input[type="checkbox"] {
    -webkit-appearance: checkbox;
    -moz-appearance: checkbox;
    appearance: checkbox;
    display: inline-block;
}

.block.A .box-content ul:first-of-type li:nth-of-type(even) {
    padding-bottom: 10px;
}

.box-content li a.attch {
    color: #2F9A57;
    transition: .3s;
    margin: 0 15px 0 0;
    font-weight: 500;
}

.block .function input[type="month"] {
    border: 2px solid #2F9A57;
}

/* body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
            font-weight: 800;
} */
</style>

</head>

<body class="green">

    <div class="bodybox">
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div id="app" class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag A" href="apply_for_expense">Apply</a>
                <a class="tag F" href="expense_liquidating">Liquidate</a>
                <a class="tag B focus">Records</a>
            </div>
            <!-- Blocks -->
            <div class="block B focus">
                <h6>Expense Application Records</h6>
                <div class="box-content">
                    <div class="title">

                        <div class="function">
                            <input type="month" id="start" name="start" @change="getLeaveCredit()">
                        </div>

                    </div>
                    <div class="tablebox">
                        <ul class="head">
                            <li><i class="micons">view_list</i></li>
                            <li>Status</li>
                            <li>Request No.</li>
                            <li>Application Time</li>
                            <li>Total Amount Requested</li>
                        </ul>

                        <ul v-for='(record, index) in displayedRecord' :key="index">
                            <li>
                                <input type="radio" name="record_id" class="alone black" :value="record.id" v-model="proof_id">
                            </li>
                            <li>{{ record.desc }}</li>
                            <li>{{ record.request_no }}</li>
                            <li>{{ record.created_at }}</li>
                            <li>{{ isNaN(record.total) ? 0 : Number(record.total).toLocaleString() }}</li>

                        </ul>

                    </div>


                    <div class="details" v-if="proof_id != 0">
                        <div class="tablebox">
                            <ul class="head">
                                <li class="head">Request No.</li>
                                <li>{{record.request_no}}</li>
                            </ul>
                            <ul>
                                <li class="head">Application Time</li>
                                <li>{{record.created_at}}</li>
                                </li>
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
                                </li>
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
                                <li class="head">Total Amount Requested
                                </li>
                                <li>{{ isNaN(record.total) ? "" : Number(record.total).toLocaleString() }}</li>
                            </ul>
                            <ul>
                                <li class="head">Attachments</li>
                                <li>
                                    <a v-for='(item, index) in record.items' :key="index" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
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
                            <ul v-for='(item, index) in record.list' :key="index" >
                                <li>{{ item.payee }}</li>
                                <li>{{ item.particulars }}</li>
                                <li>{{ Number(item.price).toLocaleString() }}</li>
                                <li>{{ Number(item.qty).toLocaleString() }}</li>
                                <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
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
                                    <div v-if="record.request_type == 'Reimbursement' || record.request_type == 'Petty Cash Replenishment'">---</div>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Remarks</li>
                                <li>{{ (record.request_type == 'New') ? record.remark_liquidated  : "---"}}</li>
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

                        <div class="btnbox">
                            <a class="btn" v-if="record.status == 0 || record.status == -1  || record.status == -2" v-bind:href="'apply_for_expense?pid='+ record.id">&nbsp;&nbsp;Re-Submit&nbsp;&nbsp;</a>
                            <a class="btn" v-if="record.status == 1 || record.status == 2 || record.status == 3 || record.status == 4" @click="withdraw">Withdraw</a>
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
<script src="js/petty_cash_record.js"></script>

</html>