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
    <title>FELIIX template</title>
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
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
    </script>


    <!-- JS for current webpage -->
    <script>
        function EditListing() {
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

        body.third .mainContent > .block .tablebox > ul.head > li {
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

        #modal_EditListing > .modal-content {
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
    </style>


</head>

<body class="third">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Apply</a>
            <a class="tag B">Records</a>
            <a class="tag C">Check</a>
            <a class="tag D">Review</a>
            <a class="tag E">Release</a>
            <a class="tag F">Liquidate</a>
            <a class="tag G">Verify</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus" style="position: relative;">
            <h6>Expense Application Form</h6>
            <div class="box-content">

                <form>

                    <ul>
                        <li><b>Request No.</b></li>
                        <li><input type="text" required style="width:100%" readonly v-model="request_no"
                                                                placeholder="Auto Given"></li>

                        <li><b>Date Requested</b></li>
                        <li><input type="date" style="width:100%" v-model="date_requested"></li>

                        <li><b>Type</b></li>
                        <li>
                            <select style="width:100%" v-model="request_type">
                                <option value="1">New</option>
                                <option value="2">Reimbursement</option>
                            </select>
                        </li>

                        <li><b>Project Name / Reason</b></li>
                        <li><input type="text" style="width:100%" v-model="project_name"></li>

                        <li><b>Listing</b>
                            <a style="background-image: url('images/ui/btn_edit_blue.svg'); width: 16px; height: 16px; display: inline-block; margin-left: 10px;"
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
                                </ul>
                                <ul v-for="(item,index) in petty_list" :key="index">
                                    <li>{{ item.payee }}</li>
                                    <li>{{ item.particulars }}</li>
                                    <li>{{ Number(item.price).toLocaleString() }}</li>
                                    <li>{{ Number(item.qty).toLocaleString() }}</li>
                                    <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
                                </ul>
                            </div>

                        </li>

                        <li><b>Total Amount Requested</b></li>
                        <li><input type="text" style="width:100%" readonly v-model="Number(sum_amonut).toLocaleString()"
                                                                placeholder="Auto Calculation"></li>

                        <li><b>Attachments</b></li>
                        <li><input type="file" ref="file" name="file[]" multiple style="width:100%" ></li>

                        <li><b>Payable to</b></li>
                        <li>
                            <select onchange="action_forOther(this);" style="width:100%" v-model="payable_to">
                                <option value="1">Requestor</option>
                                <option value="2">Other</option>
                            </select>

                            <input type="text" id="specific_payableto" ref="payable_other" v-model="payable_other" style="display: none; width:100%; margin-top: 5px;" placeholder="Please Specify ...">
                        </li>

                        <li><b>Remarks or Payment Instructions</b></li>
                        <li><textarea style="width:100%" v-model="remark" ></textarea></li>

                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="reset">Reset</a>
                        <a class="btn" @click="apply">Submit</a>
                    </div>

                </form>

            </div>


            <div id="modal_EditListing" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-heading">
                        <h6>Edit Listing</h6>
                        <a href="javascript: void(0)" onclick="EditListing()"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a>
                    </div>


                    <div class="box-content">

                        <ul>
                            <li><b>Payee</b></li>
                            <li><input type="text" required style="width:100%" v-model="list_payee"></li>

                            <li><b>Particulars</b></li>
                            <li><input type="text" required style="width:100%" v-model="list_particulars"></li>

                            <li><b>Price</b></li>
                            <li><input type="text" required style="width:100%" v-model="list_price" onClick="this.setSelectionRange(0, this.value.length)"></li>

                            <li><b>Qty</b></li>
                            <li><input type="text" required style="width:100%" v-model="list_qty" onClick="this.setSelectionRange(0, this.value.length)"></li>

                            <li><b>Amount</b></li>
                            <li><input type="text" required style="width:100%" readonly v-model="Number(list_amonut).toLocaleString()"
                                                                    placeholder="Auto calculation"></li>

                        </ul>

                        <div class="btnbox">
                            <a class="btn" @click="add_list">Add</a>
                            <a class="btn" @click="remove_list">Delete</a>
                        </div>

                        <div class="tablebox">
                            <ul class="head">
                                <li><i class="micons">view_list</i></li>
                                <li>Payee</li>
                                <li>Particulars</li>
                                <li>Price</li>
                                <li>Qty</li>
                                <li>Amount</li>
                            </ul>
                            <ul v-for="(item,index) in petty_list" :key="index">
                                <li><input type="checkbox"  class="alone black" v-model="item.is_checked"></li>
                                <li>{{ item.payee }}</li>
                                <li>{{ item.particulars }}</li>
                                <li>{{ Number(item.price).toLocaleString() }}</li>
                                <li>{{ Number(item.qty).toLocaleString() }}</li>
                                <li>{{ Number(item.price * item.qty).toLocaleString() }}</li>
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

    function action_forOther(selector){

        if(selector.value == 1){
            document.getElementById("specific_payableto").style.display = "none";
        }else{
            document.getElementById("specific_payableto").value = "";
            document.getElementById("specific_payableto").style.display = "";
        }
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/apply_for_petty.js"></script>

<!-- Awesome Font for current webpage -->
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script>

</html>