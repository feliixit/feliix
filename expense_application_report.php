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
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- import CSS -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">



<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>


    <!-- CSS for current webpage -->
    <style type="text/css">

        body.fifth .mainContent > .tags a.focus {
            background-color: #EA0029;
        }

        body.fifth .mainContent > .block {
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

        body.fifth .block .tablebox > ul > li {
            border-bottom: 2px solid #EA0029;
            border-right: 2px solid #EA0029;
            font-size: 12px;
        }

        body.fifth .block .tablebox > ul > li i {
            color: #EA0029;
        }

        .block .function input[type="month"] {
            border: 2px solid var(--sec03);
        }

        div.details {
            margin-top: 15px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #25a2b8;
            cursor: pointer;
            margin: 3px 6px 3px 0;
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

        div.details > span {
            font-size: x-large;
            font-weight: 700;
            color: #EA0029;
            display: block;
            margin-top: 20px;
        }

        .tableframe {
            overflow: auto;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(4), .tableframe .tablebox.lv1 li:nth-of-type(10) {
            color: #000000;
            min-width: auto;
        }

        #modal_Details {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
        }

        #modal_Details > .modal-content {
            width: 90%;
            max-height: 95vh;
            margin: auto;
            border: 2px solid #EA0029;
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

    </style>


</head>

<body class="fifth">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Expense Application Tracker</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">

            <div class="list_function" style="margin-top: 10px;"><div class="pagenation"><a class="prev" disabled="disabled">Previous</a> <a class="page">1</a> <a disabled="disabled" class="next">Next</a></div></div>

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

                    <ul>
                        <li>00017</li>
                        <li>Dennis Lin</li>
                        <li>2020/11/20 10:15</li>
                        <li>New</li>
                        <li>Completed</li>
                        <li>372,500</li>
                        <li>370,000</li>
                        <li>2020/12/07</li>
                        <li>2020/12/04</li>
                        <li>2020/12/04<br>2020/12/04</li>
                        <li>2020/12/07</li>
                        <li>2020/12/14</li>
                        <li>2020/12/14</li>
                        <li>
                            <a href="javascript: void(0)" onclick="ShowDetails()"><i
                                    class="fas fa-info-circle fa-lg" aria-hidden="true"></i></a></li>
                    </ul>

                    <ul>
                        <li>00014</li>
                        <li>Dennis Lin</li>
                        <li>2020/11/03 14:47</li>
                        <li>Reimbursement</li>
                        <li>For Approve</li>
                        <li>10,750.5</li>
                        <li></li>
                        <li>2021/01/25</li>
                        <li>2020/11/30</li>
                        <li>2020/12/11</li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li>
                            <a href="javascript: void(0)" onclick="ShowDetails()"><i
                                    class="fas fa-info-circle fa-lg" aria-hidden="true"></i></a></li>
                    </ul>

                </div>
            </div>


            <div id="modal_Details" class="modal">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="modal-heading">
                        <h6>Details</h6>
                        <a href="javascript: void(0)" onclick="ShowDetails()"><i class="fa fa-times fa-lg"
                                                                                 aria-hidden="true"></i></a>
                    </div>

                    <div class="details">
                        <div class="tablebox">
                            <ul class="head">
                                <li class="head">Request No.</li>
                                <li>00017</li>
                            </ul>
                            <ul>
                                <li class="head">Application Time</li>
                                <li>2020/11/20 10:15</li>
                            </ul>
                            <ul>
                                <li class="head">Status</li>
                                <li>Completed</li>
                            </ul>
                            <ul>
                                <li class="head">Processing History
                                </li>
                                <li>Submitted (Dennis Lin at 2020/11/20 15:30)<br>
                                    Checker Rejected: document is not complete. (Mary Jude Jeng Articulo at 2020/12/03
                                    09:43)<br>
                                    Submitted (Dennis Lin at 2020/12/04 11:30)<br>
                                    Checker Checked (Mary Jude Jeng Articulo at 2020/12/04 13:55)<br>
                                    OP Approved (Thalassa Wren Benzon at 2020/12/04 16:23)<br>
                                    MD Approved (Kristel Tan at 2020/12/04 17:05)<br>
                                    Releaser Released (Mary Jude Jeng Articulo at 2020/12/07 10:03)<br>
                                    Liquidated (Dennis Lin at 2020/12/14 14:04)<br>
                                    Verifier Verified (Mary Jude Jeng Articulo at 2020/12/14 16:25)
                                </li>
                            </ul>
                             <ul>
                                <li class="head">Date Requested</li>
                                <li>2020/12/07</li>
                            </ul>
                            <ul>
                                <li class="head">Type</li>
                                <li>New</li>
                            </ul>
                            <ul>
                                <li class="head">Project Name / Reason</li>
                                <li>UDNP Ranee</li>
                            </ul>
                            <ul>
                                <li class="head">Requested Amount
                                </li>
                                <li>372,500</li>
                            </ul>
                            <ul>
                                <li class="head">Attachments</li>
                                <li><a>Requirement.doc</a>
                                    <a>Requirement.xlsx</a>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Payable to
                                </li>
                                <li>Other: xxxxxxx</li>
                            </ul>
                            <ul>
                                <li class="head">Remarks or Payment Instructions
                                </li>
                                <li></li>
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


                        <span>Additional Info</span>
                    <div class="tablebox">
                        <ul>
                            <li class="head">Account</li>
                            <li>Office Petty Cash</li>
                        </ul>
                        <ul>
                            <li class="head">Category</li>
                            <li>Office Needs>>Tools and Materials</li>
                        </ul>
                        <ul>
                            <li class="head">Remarks or Payment Instructions</li>
                            <li>Check</li>
                        </ul>
                    </div>


                    <div class="tablebox" style="margin-top: 60px;">
                        <ul class="head">
                            <li class="head">Request No.</li>
                            <li>00017</li>
                        </ul>
                        <ul>
                            <li class="head">Total Amount Requested
                            </li>
                            <li>372,500</li>
                        </ul>
                        <ul>
                            <li class="head">Date Released</li>
                            <li>2020/12/07</li>
                        </ul>
                        <ul>
                            <li class="head">Proof of Release</li>
                            <li><a>Signature_01.pdf</a>
                                <a>Signature_02.pdf</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Liquidated</li>
                            <li>2020/12/14</li>
                        </ul>
                        <ul>
                            <li class="head">Amount Liquidated</li>
                            <li>372,500</li>
                        </ul>

                        <ul>
                            <li class="head">Liquidation Files</li>
                            <li><a>Receipt.jpg</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="head">Date Verified</li>
                            <li>2020/12/14</li>
                        </ul>
                        <ul>
                            <li class="head">Actual Amount After Verification</li>
                            <li>370,000</li>
                        </ul>
                        <ul>
                            <li class="head">Proof of Return or Release</li>
                            <li><a>Signature_03.pdf</a>
                            </li>
                        </ul>
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

<script>
        function ShowDetails() {
            $("#modal_Details").toggle();
        }
    </script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/leave_record.js"></script>
</html>