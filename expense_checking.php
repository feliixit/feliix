<?php include 'check.php';?>
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

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- import CSS -->
<link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})

function EditListing() {
    $(".mask").toggle();
    $("#modal_EditListing").toggle();
}

</script>


     <!-- CSS for current webpage -->
    <style type="text/css">

        .block .function input[type="month"] {
            border: 2px solid var(--sec03);
        }

        div.details{
            margin-top: 40px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 0;
            display: block;
        }

        div.tablebox.listing {
            margin-top: 3px;
        }

        div.tablebox.listing ul.head li{
            background-color: #EFEFEF!important;
        }

        div.box-content form{
            border: 3px solid var(--black01);
            margin-top: 40px;
            padding: 15px 30px 0;
            box-sizing: border-box;
        }

        div.box-content form > span {
            font-size: x-large;
            font-weight: 700;
            color: var(--black01);
            display: block;
            margin-bottom: 5px;
        }

        div.btnbox a.btn.red {
            background-color: var(--pri01a);
        }

        .block.C div.details form li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        body input.alone.black[type=radio]::before{
            font-size: 25px; color: var(--black01);}

        #modal_EditListing {
            display: none;
            position: fixed;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        #modal_EditListing > .modal-content {
            width: 90%;
            margin: auto;
            border: 2px solid var(--black01);
            padding: 20px 25px;
            background-color: white;
            max-height: calc( 100vh - 40px);
            overflow-y: auto;
        }

        #modal_EditListing .modal-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #modal_EditListing .modal-heading h6 {
            color: var(--black01);
            border-bottom: none;
        }

        #modal_EditListing .modal-heading a {
            font-size: 20px;
        }

        #modal_EditListing .box-content {
            padding: 20px 20px 30px;
        }

        #modal_EditListing .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        .style_Remarks {
            max-width: 300px!important;
            text-align: left!important;
        }

        .style_Icons {
            font-size: 25px!important;
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

    </style>



</head>

<body class="black">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Check</a>
            <a class="tag B" href="expense_reviewing">Review</a>
            <a class="tag C" href="expense_releasing">Release</a>
            <a class="tag D" href="expense_verifying">Verify</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Expense Application Checking</h6>
            <div class="box-content">
                <div class="title">

                    <div class="function">

                    </div>

                </div>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Request No.</li>
                        <li>Requestor</li>
                        <li>Date Needed</li>
                        <li>Total Amount Requested</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone black" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.request_no }}</li>
                        <li>{{ record.requestor }}</li>
                        <li>{{ record.date_requested }}</li>
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
                                    {{ item.action }} <a v-if="item.reason != ''">: {{ item.reason }}</a> ({{ item.actor }} at {{ item.created_at }})
                                </p>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Needed</li>
                            <li>{{ record.date_requested }}</li>
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
                            <li>{{ (record.payable_other == "") ? "Requestor": (( typeof record.payable_other == "undefined" ) ? "":  "Other:" + record.payable_other) }}</li>
                        </ul>
                        <ul>
                            <li class="head">Remarks or Payment Instructions
                            </li>
                            <li>{{ record.remark }}</li>
                        </ul>
                    </div>
                    <div style="margin-top: 20px;">
                        <b>Listing</b> <a href="javascript: void(0)" onclick="EditListing()" style="background-image: url('images/ui/btn_edit_black.svg'); width: 16px; height: 16px; display: inline-block; margin-left: 10px;"></a>
                    </div>

                    <div id="modal_EditListing" class="modal" style="display: none;">
                        <div class="modal-content">
                            <div class="modal-heading"><h6>Edit Listing</h6> <a href="javascript: void(0)"
                                                                                onclick="EditListing()"><i aria-hidden="true"
                                                                                                        class="fa fa-times fa-lg"></i></a>
                            </div>
                            <div class="box-content" :ref="'porto'">
                                <ul>
                                    <li><b>Payee</b></li>
                                    <li><input type="text" required="required" style="width: 100%;" v-model="e_payee"></li>
                                    <li><b>Particulars</b></li>
                                    <li><input type="text" required="required" style="width: 100%;" v-model="e_particulars"></li>
                                    <li><b>Price</b></li>
                                    <li><input type="text" required="required"
                                            onclick="this.setSelectionRange(0, this.value.length)" style="width: 100%;" v-model="e_price"></li>
                                    <li><b>Qty</b></li>
                                    <li><input type="text" required="required"
                                            onclick="this.setSelectionRange(0, this.value.length)" style="width: 100%;" v-model="e_qty"></li>
                                    <li><b>Amount</b></li>
                                    <li><input type="text" required="required" readonly="readonly"
                                            placeholder="Auto calculation" style="width: 100%;" :value="Number(e_qty * e_price).toLocaleString()"></li>
                                    <li><b>Remarks</b></li>
                                    <li><input type="text" style="width: 100%;" v-model="e_check_remark"></li>
                                </ul>
                                <div class="btnbox">
                                    <a class="btn green" @click="e_add_criterion" v-if="!e_editing">Add</a>
                                    <a class="btn" v-if="e_editing" @click="e_cancel_criterion" >Cancel</a>
                                    <a class="btn green" v-if="e_editing" @click="e_update_criterion">Update</a>
                                </div>
                                <div class="tablebox">
                                    <ul class="head">
                                        <li>Payee</li>
                                        <li>Particulars</li>
                                        <li>Price</li>
                                        <li>Qty</li>
                                        <li>Amount</li>
                                        <li>Remarks</li>
                                        <li>Actions</li>
                                    </ul>
                                    <ul v-for='(item, index) in record.list' :key="index">
                                        <li>{{ item.payee }}</li>
                                        <li>{{ item.particulars }}</li>
                                        <li>{{ Number(item.price).toLocaleString() }}</li>
                                        <li>{{ Number(item.qty).toLocaleString() }}</li>
                                        <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                        <li class="style_Remarks">{{ item.check_remark }}</li>
                                        <li class="style_Icons">
                                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-up" @click="e_set_up(index, item.id)"></i>
                                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-down" @click="e_set_down(index, item.id)"></i>
                                            <br>
                                            <i aria-hidden="true" class="fas fa-edit" @click="e_edit(item.id)"></i>
                                            <i aria-hidden="true" class="fas fa-trash-alt" @click="e_del(item.id)"></i>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tablebox listing">
                        <ul class="head">
                            <li>Payee</li>
                            <li>Particulars</li>
                            <li>Price</li>
                            <li>Qty</li>
                            <li>Amount</li>
                            <li>Remarks</li>
                        </ul>
                        <ul v-for='(item, index) in record.list' :key="index" >
                            <li>{{ item.payee }}</li>
                            <li>{{ item.particulars }}</li>
                            <li>{{ Number(item.price).toLocaleString() }}</li>
                            <li>{{ Number(item.qty).toLocaleString() }}</li>
                            <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                            <li class="style_Remarks">{{ item.check_remark }}</li>
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
                        <span>Additional Info</span>
                        <ul>
                            <li><b>Account</b></li>
                            <li>
                                <select style="width:100%" v-model="record.info_account" v-if="record.request_type !== 'Petty Cash Replenishment'">
                                    <option value=""></option>
                                    <option value="Office Petty Cash">Office Petty Cash</option>
                                    <option value="Online Transactions" disabled>Online Transactions</option>
                                    <option value="Security Bank">Security Bank</option>
                                </select>

                                <select style="width:100%" v-model="record.info_account" v-if="record.request_type == 'Petty Cash Replenishment'" disabled="disabled">
                                    <option value="Security Bank => Office Petty Cash">Security Bank => Office Petty Cash</option>
                                </select>
                            </li>

                            <li><b>Category</b></li>
                            <li>
                                <select style="width:100%" v-model="record.info_category">
                                    <option value=""></option>
                                    <option value="Accounting and govt payments">Accounting and govt payments</option>
                                    <option value="Bills">Bills</option>
                                    <option value="Client Refunds">Client Refunds</option>
                                    <option value="Consignment">Consignment</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Incentives">Incentives</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Misc">Misc</option>
                                    <option value="Office Needs">Office Needs</option>
                                    <option value="Others">Others</option>
                                    <option value="Projects">Projects</option>
                                    <option value="Rental">Rental</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Sales Petty Cash">Sales Petty Cash</option>
                                    <option value="Store">Store</option>
                                    <option value="Transportation Petty Cash">Transportation Petty Cash</option>
                                </select>
                            </li>

                            <li v-if="record.info_category == 'Marketing' || record.info_category == 'Office Needs' || record.info_category == 'Others' || record.info_category == 'Projects' || record.info_category == 'Store'"  ><b>Sub Category</b></li>
                            <li v-if="record.info_category == 'Marketing' || record.info_category == 'Office Needs' || record.info_category == 'Others' || record.info_category == 'Projects' || record.info_category == 'Store'" >
                                <select style="width:100%" v-model="record.sub_category">
                                    <option value=""></option>
                                    <option value="Accommodation">Accommodation</option>
                                    <option value="Allowance">Allowance</option>
                                    <option value="Commission">Commission</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="Delivery and Installation">Delivery and Installation</option>
                                    <option value="Installation">Installation</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Meals">Meals</option>
                                    <option value="Misc">Misc</option>
                                    <option value="Mock-up">Mock-up</option>
                                    <option value="Others">Others</option>
                                    <option value="Outsource">Outsource</option>
                                    <option value="Payroll/Salary">Payroll/Salary</option>
                                    <option value="Petty cash">Petty cash</option>
                                    <option value="Products">Products</option>
                                    <option value="Site Visit">Site Visit</option>
                                    <option value="Supplies">Supplies</option>
                                    <option value="Team Building">Team Building</option>
                                    <option value="Tools and Materials">Tools and Materials</option>
                                    <option value="Transportation">Transportation</option>
                                </select>
                            </li>

                            <li><b>Remarks or Payment Instructions</b></li>
                            <li>
                                <select style="width:100%" v-model="record.info_remark">
                                    <option value=""></option>
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Check">Check</option>
                                    <option value="DigiBanker">DigiBanker</option>
                                    <option value="Other">Other</option>
                                </select>

                                <input v-if="record.info_remark == 'Other'" type="text" id="specific_payableto" ref="specific_payableto" v-model="record.info_remark_other" style="width:100%; margin-top: 5px;" placeholder="Please Specify ...">
                            </li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn" @click="approve_op_only">Send to OP</a>
                            <a class="btn" @click="approve_md" style="display: none">Send to MD</a>
                            <a class="btn" @click="approve_op" style="display: none">Send to OP & MD</a>
                        </div>

                    </form>

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
<script src="js/expense_checking.js"></script>
</html>