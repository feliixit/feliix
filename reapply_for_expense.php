<?php include 'check.php';?>
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
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


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
            <a class="tag A focus">Apply</a>
            <a class="tag F">Liquidate</a>
            <a class="tag B">Records</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus" style="position: relative;">
            <h6>Expense Application Form</h6>
            <div class="box-content">

                <form>

                    <ul>
                        <li><b>Request No.</b></li>
                        <li><input type="text" required style="width:100%" readonly
                                                                placeholder="Auto Given"></li>

                        <li><b>Date Needed</b></li>
                        <li><input type="date" style="width:100%"></li>

                        <li><b>Type</b></li>
                        <li>
                            <select style="width:100%">
                                <option>New</option>
                                <option>Reimbursement</option>
                            </select>
                        </li>



                        <li><b>Project Name / Reason</b></li>
                        <li><input type="text" style="width:100%"></li>

                        <li><b>Listing</b>
                            <a style="background-image: url('btn_edit_green.svg'); width: 16px; height: 16px; display: inline-block; margin-left: 10px;"
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
                                <ul>
                                    <li>John Raymund Casero</li>
                                    <li>Light Texture</li>
                                    <li>350</li>
                                    <li>100</li>
                                    <li>35,000</li>
                                </ul>
                                <ul>
                                    <li>Pika</li>
                                    <li>Light Bulb</li>
                                    <li>135</li>
                                    <li>2,500</li>
                                    <li>337,500</li>
                                </ul>
                            </div>

                        </li>

                        <li><b>Total Amount Requested</b></li>
                        <li><input type="text" style="width:100%" readonly
                                                                placeholder="Auto Calculation"></li>

                        <li><b>Attachments</b></li>
                        <li>
                            <input type="file" multiple style="width:100%">
                            <a class="attch" href="" target="_blank">test.doc</a>
                            <a class="attch" href="" target="_blank">test1.doc</a>
                        </li>

                        <li><b>Payable to</b></li>
                        <li>
                            <select onchange="action_forOther(this);" style="width:100%">
                                <option value="0">Requestor</option>
                                <option value="1">Other</option>
                            </select>

                            <input type="text" id="specific_payableto" style="display: none; width:100%; margin-top: 5px;" placeholder="Please Specify ...">
                        </li>

                        <li><b>Remarks or Payment Instructions</b></li>
                        <li><textarea style="width:100%"></textarea></li>

                    </ul>

                    <div class="btnbox">
                        <a class="btn">Reset</a>
                        <a class="btn">Submit</a>
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
                            <li><input type="text" required style="width:100%"></li>

                            <li><b>Particulars</b></li>
                            <li><input type="text" required style="width:100%"></li>

                            <li><b>Price</b></li>
                            <li><input type="text" required style="width:100%"></li>

                            <li><b>Qty</b></li>
                            <li><input type="text" required style="width:100%"></li>

                            <li><b>Amount</b></li>
                            <li><input type="text" required style="width:100%" readonly
                                                                    placeholder="Auto calculation"></li>

                        </ul>

                        <div class="btnbox">
                            <a class="btn">Add</a>
                            <a class="btn">Delete</a>
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

                            <ul>
                                <li><input type="checkbox"></li>
                                <li>John Raymund Casero</li>
                                <li>Light Texture</li>
                                <li>350</li>
                                <li>100</li>
                                <li>35,000</li>
                            </ul>
                            <ul>
                                <li><input type="checkbox"></li>
                                <li>Pika</li>
                                <li>Light Bulb</li>
                                <li>135</li>
                                <li>2,500</li>
                                <li>337,500</li>
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

        if(selector.value == 0){
            document.getElementById("specific_payableto").style.display = "none";
        }else{
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
<script src="js/apply_for_leave.js"></script>

<!-- Awesome Font for current webpage -->
<script defer src="js/a076d05399.js"></script>

</html>