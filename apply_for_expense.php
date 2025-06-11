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


    body.third input[type=date] {
        border: 2px solid var(--sec03);
        padding: 5px;
        background-color: transparent;
    }

    body.third .mainContent>.block .tablebox>ul.head>li {
        background-color: #CCDCEE;
    }

    #modal_EditListing {
        display: none;
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        margin: auto;
    }

    #modal_EditListing>.modal-content {
        width: 90%;
        margin: auto;
        border: 2px solid var(--sec03);
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
        color: var(--sec03);
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

    .block.A .box-content ul:first-of-type li:nth-of-type(even) {
        padding-bottom: 10px;
    }

    body.green select {
        background-image: url(./images/ui/icon_form_select_arrow_green.svg) !important;
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
        z-index: 2;
    }

    #modal_EditListing>.modal-content {
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
                <a class="tag A focus">Apply</a>
                <a class="tag F" href="expense_liquidating">Liquidate</a>
                <a class="tag B" href="expense_application_records">Records</a>
            </div>
            <!-- Blocks -->
            <div class="block A focus" style="position: relative;">
                <h6>Expense Application Form</h6>
                <div class="box-content">

                    <form>

                        <ul>
                            <li v-if="pid != 0"><b>Request No.</b></li>
                            <li v-if="pid != 0"><input type="text" required style="width:100%" readonly
                                    v-model="request_no" placeholder="Auto Given"></li>

                            <li><b>Date Needed</b></li>
                            <li><input type="date" style="width:100%" v-model="date_requested"></li>

                            <li><b>Type</b></li>
                            <li>
                                <select style="width:100%" v-model="request_type">
                                    <option value="1">New</option>
                                    <option value="2">Reimbursement</option>
                                    <option value="3">Petty Cash Replenishment</option>
                                </select>
                            </li>

                            <li><b>Project Name</b></li>
                            <li>
                                <select style="width:100%" v-model="project_name1">
                                    <option v-for="(item, index) in projects" :value="item.project_name"
                                        :key="item.project_name">
                                        {{ item.project_name }}
                                    </option>
                                </select>
                            </li>

                            <li><b>Reason</b></li>

                            <li>
                                <select style="width: 100%;" v-model="rtype" @change="clear_projectname()">
                                    <option value="">Other</option>
                                    <option value="team">Team Building</option>
                                </select>
                            </li>

                            <!-- 如果上面選項是選 Other, 則下面的 <li> 需要顯示出來 -->
                            <li style="margin-bottom: 10px;" v-if="rtype == '' "><input type="text" style="width:100%"
                                    v-model="project_name"></li>

                            <!-- 如果上面選項是選 Team Building, 則下面的 <li> 需要顯示出來, li 的 display 要切換的是 none 和 flex -->
                            <li style="display: flex; justify-content: space-between; margin-bottom: 10px;"
                                v-if="rtype == 'team' ">
                                <select style="width: 47%;" v-model="dept_name">
                                    <option value="admin">Admin Department</option>
                                    <option value="design">Design Department</option>
                                    <option value="engineering">Engineering Department</option>
                                    <option value="lighting">Lighting Department</option>
                                    <option value="office">Office Department</option>
                                    <option value="sales">Sales Department</option>
                                </select>

                                <input type="text" style="width: 50%;"
                                    placeholder="Which month(s) of fund will be used? / Remarks" v-model="project_name"
                                    v-if="rtype == 'team' ">
                            </li>

                            <li><b>Listing</b>
                                <a style="background-image: url('images/ui/btn_edit_green.svg'); width: 16px; height: 16px; display: inline-block; margin-left: 10px;"
                                    href="javascript: void(0)" onclick="EditListing()"></a>
                            </li>
                            <li>
                                <div class="tablebox">
                                    <ul class="head">
                                        <li>Payee</li>
                                        <li>Particulars</li>
                                        <li>Price</li>
                                        <li>Qty</li>
                                        <li>Amount</li>
                                        <li v-if="pid != 0">Remarks</li>
                                    </ul>
                                    <ul v-for="(item,index) in petty_list" :key="index">
                                        <li>{{ item.payee }}</li>
                                        <li>{{ item.particulars }}</li>
                                        <li>{{ Number(item.price).toLocaleString() }}</li>
                                        <li>{{ Number(item.qty).toLocaleString() }}</li>
                                        <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                        <li v-if="pid != 0" class="style_Remarks">{{ item.check_remark }}</li>
                                    </ul>
                                </div>

                            </li>

                            <li><b>Total Amount Requested</b></li>
                            <li><input type="text" style="width:100%" readonly
                                    v-model="Number(sum_amonut).toLocaleString()" placeholder="Auto Calculation"></li>

                            <li><b>Attachments</b></li>
                            <li>
                                <input type="file" ref="file" name="file[]" multiple style="width:100%">

                                <div class="list_attch" v-for="(item,index) in item_list" :key="index">
                                    <input type="checkbox" :id="'file' + item.id" v-model="item.is_checked">
                                    <label :for="'file' + item.id"><a class="attch" :href="baseURL + item.gcp_name"
                                            target="_blank">{{item.filename}}</a></label>
                                </div>

                            </li>

                            <li><b>Payable to</b></li>
                            <li>
                                <select onchange="action_forOther(this);" style="width:100%" v-model="payable_to">
                                    <option value="1">Requestor</option>
                                    <option value="2">Other</option>
                                </select>

                                <input type="text" id="specific_payableto" ref="payable_other" v-model="payable_other"
                                    style="display: none; width:100%; margin-top: 5px;"
                                    placeholder="Please Specify ...">
                            </li>

                            <li><b>Remarks or Payment Instructions</b></li>
                            <li><textarea style="width:100%" v-model="remark"></textarea></li>

                        </ul>

                        <div class="btnbox">
                            <a class="btn" @click="reset">Reset</a>
                            <a class="btn" :disabled="submit == true" v-if="pid == 0" @click="apply">Submit</a>
                            <a class="btn" :disabled="submit == true" v-if="pid != 0" @click="apply_edit">Submit</a>
                        </div>

                    </form>

                </div>


                <div id="modal_EditListing" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-heading">
                            <h6>Edit Listing</h6>
                            <a href="javascript: void(0)" onclick="EditListing()"><i class="fa fa-times fa-lg"
                                    aria-hidden="true"></i></a>
                        </div>


                        <div class="box-content" :ref="'porto'">

                            <ul>
                                <li><b>Payee</b></li>
                                <li><input type="text" required style="width:100%" v-model="list_payee"></li>

                                <li><b>Particulars</b></li>
                                <li><input type="text" required style="width:100%" v-model="list_particulars"></li>

                                <li><b>Price</b></li>
                                <li><input type="text" required style="width:100%" v-model="list_price"
                                        onClick="this.setSelectionRange(0, this.value.length)"></li>

                                <li><b>Qty</b></li>
                                <li><input type="text" required style="width:100%" v-model="list_qty"
                                        onClick="this.setSelectionRange(0, this.value.length)"></li>

                                <li><b>Amount</b></li>
                                <li><input type="text" required style="width:100%" readonly
                                        v-model="Number(list_amonut).toLocaleString()" placeholder="Auto calculation">
                                </li>

                            </ul>

                            <div class="btnbox" v-if="pid != 0">
                                <a class="btn green" @click="e_add_criterion" v-if="!e_editing">Add</a>
                                <a class="btn" v-if="e_editing" @click="e_cancel_criterion">Cancel</a>
                                <a class="btn green" v-if="e_editing" @click="e_update_criterion">Update</a>
                            </div>

                            <div class="btnbox" v-if="pid == 0">
                                <a class="btn green" @click="_add_criterion" v-if="!e_editing">Add</a>
                                <a class="btn" v-if="e_editing" @click="_cancel_criterion">Cancel</a>
                                <a class="btn green" v-if="e_editing" @click="_update_criterion">Update</a>
                            </div>

                            <div class="tablebox">
                                <ul class="head">
                                    <li>Payee</li>
                                    <li>Particulars</li>
                                    <li>Price</li>
                                    <li>Qty</li>
                                    <li>Amount</li>
                                    <li v-if="pid != 0">Remarks</li>
                                    <li v-if="pid != 0">Actions</li>
                                    <li v-if="pid == 0">Actions</li>
                                </ul>
                                <ul v-for="(item,index) in petty_list" :key="index">
                                    <li>{{ item.payee }}</li>
                                    <li>{{ item.particulars }}</li>
                                    <li>{{ Number(item.price).toLocaleString() }}</li>
                                    <li>{{ Number(item.qty).toLocaleString() }}</li>
                                    <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                    <li v-if="pid != 0">{{ item.check_remark }}</li>
                                    <li class="style_Icons" v-if="pid != 0">
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-up"
                                            @click="e_set_up(index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-down"
                                            @click="e_set_down(index, item.id)"></i>
                                        <br>
                                        <i aria-hidden="true" class="fas fa-edit" @click="e_edit(item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-trash-alt" @click="e_del(item.id)"></i>
                                    </li>

                                    <li class="style_Icons" v-if="pid == 0">
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-up"
                                            @click="_set_up(index, item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-arrow-alt-circle-down"
                                            @click="_set_down(index, item.id)"></i>
                                        <br>
                                        <i aria-hidden="true" class="fas fa-edit" @click="_edit(item.id)"></i>
                                        <i aria-hidden="true" class="fas fa-trash-alt" @click="_del(item.id)"></i>
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
<script src="js/apply_for_petty.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

</html>