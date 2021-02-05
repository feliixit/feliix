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
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">



<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
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
            margin: 3px 6px 3px 0;
        }

        div.tablebox.listing {
            margin-top: 15px;
        }

        div.tablebox.listing ul.head li{
            background-color: #CCDCEE!important;
        }

        div.box-content form{
            border: 3px solid var(--sec03);
            margin-top: 40px;
            padding: 15px 30px 0;
            box-sizing: border-box;
        }

        div.box-content form > span {
            font-size: x-large;
            font-weight: 700;
            color: var(--sec03);
            display: block;
            margin-bottom: 5px;
        }

        div.btnbox a.btn.red {
            background-color: var(--pri01a);
        }

        .block.C div.details form li:nth-of-type(even) {
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
            <a class="tag A" href="apply_for_petty_cash">Apply</a>
            <a class="tag B" href="petty_cash_record">Records</a>
            <a class="tag C focus">Check</a>
            <a class="tag D">Review</a>
            <a class="tag E">Release</a>
            <a class="tag F">Liquidate</a>
            <a class="tag G" href="petty_cash_verify">Verify</a>
        </div>
        <!-- Blocks -->
        <div class="block C focus">
            <h6>Expense Application Checking</h6>
            <div class="box-content">
                <div class="title">

                    <div class="function">
                        <input type="month">
                    </div>

                </div>
                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Request No.</li>
                        <li>Requestor</li>
                        <li>Date Requested</li>
                        <li>Total Amount Requested</li>
                    </ul>

                    <ul>
                        <li>
                            <input type="radio" class="alone blue">
                        </li>
                        <li>00017</li>
                        <li>Dennis Lin</li>
                        <li>2020/11/20</li>
                        <li>372,500</li>
                    </ul>

                    <ul>
                        <li>
                            <input type="radio" class="alone blue">
                        </li>
                        <li>00014</li>
                        <li>Dennis Lin</li>
                        <li>2020/11/03</li>
                        <li>10,750.5</li>
                    </ul>
                </div>


                <div class="details">
                    <div class="tablebox">
                        <ul class="head">
                            <li class="head">Request No.</li>
                            <li>00017</li>
                        </ul>
                        <ul>
                            <li class="head">Date Requested</li>
                            <li>2020/11/20</li>
                        </ul>
                        <ul>
                            <li class="head">Requestor</li>
                            <li>Dennis Lin</li>
                        </ul>
                        <ul>
                            <li class="head">Status</li>
                            <li>For Check</li>
                        </ul>
                        <ul>
                            <li class="head">Processing History
                            </li>
                            <li>Submitted (Dennis Lin at 2020/11/20 15:30)<br>
                                Checker Rejected: document is not complete. (Mary Jude Jeng Articulo at 2020/12/03 09:43)<br>
                                Submitted (Dennis Lin at 2020/12/04 11:30)
                            </li>
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
                            <li class="head">Total Amount Requested
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
                            <li>Kristel Tan</li>
                            <li>Light Bulb</li>
                            <li>135</li>
                            <li>2,500</li>
                            <li>337,500</li>
                        </ul>
                    </div>


                    <form>
                        <ul>
                            <li><b>Rejection Reason</b></li>
                            <li><textarea style="width:100%"></textarea></li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn red">Reject</a>
                        </div>
                    </form>


                    <form>
                        <span>Additional Info</span>
                        <ul>
                            <li><b>Account</b></li>
                            <li>
                                <select style="width:100%">
                                    <option>Office Petty Cash</option>
                                    <option>Online Transactions</option>
                                    <option>Security Bank</option>
                                </select>
                            </li>

                            <li><b>Category</b></li>
                            <li>
                                <select style="width:100%">
                                    <option>Accounting and govt payments</option>
                                    <option>Bills</option>
                                    <option>Client Refunds</option>
                                    <option>Consignment</option>
                                    <option>Credit Card</option>
                                    <option>Marketing</option>
                                    <option>Misc</option>
                                    <option>Office Needs</option>
                                    <option>Others</option>
                                    <option>Projects</option>
                                    <option>Rental</option>
                                    <option>Salary</option>
                                    <option>Sales Petty Cash</option>
                                    <option>Store</option>
                                    <option>Transportation Petty Cash</option>
                                </select>
                            </li>

                            <li><b>Sub Category</b></li>
                            <li>
                                <select style="width:100%">
                                    <option>Allowance</option>
                                    <option>Commission</option>
                                    <option>Delivery</option>
                                    <option>Maintenance</option>
                                    <option>Meals</option>
                                    <option>Misc</option>
                                    <option>Others</option>
                                    <option>Outsource</option>
                                    <option>Petty cash</option>
                                    <option>Products</option>
                                    <option>Supplies</option>
                                    <option>Tools and Materials</option>
                                    <option>Transportation</option>
                                </select>
                            </li>

                            <li><b>Remarks or Payment Instructions</b></li>
                            <li>
                                <select style="width:100%">
                                    <option>Cash</option>
                                    <option>Check</option>
                                    <option>Other</option>
                                </select>
                            </li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn">Send to OP</a>
                            <a class="btn">Send to MD</a>
                        </div>

                    </form>

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
<script src="js/leave_record.js"></script>
</html>