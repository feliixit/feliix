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
            background-color: #EFEFEF!important;
        }

        div.box-content form{
            border: 3px solid var(--black01);
            margin-top: 40px;
            padding: 15px 15px 0;
            box-sizing: border-box;
        }

        div.details>span {
            font-size: x-large;
            font-weight: 700;
            color: var(--black01);
            display: block;
            margin-top: 20px;
        }
        
        div.btnbox a.btn.red{
            background-color: var(--pri01a);
        }

        .block.G div.details form li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        body input.alone.black[type=radio]::before{
            font-size: 25px; color: var(--black01);}

    </style>



</head>

<body class="black">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A">Check</a>
            <a class="tag B">Review</a>
            <a class="tag C">Release</a>
            <a class="tag D focus">Verify</a>
        </div>
        <!-- Blocks -->
        <div class="block D focus">
            <h6>Liquidation Verification</h6>
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
                        <li>Date Requested</li>
                        <li>Total Amount Requested</li>
                    </ul>

                    <ul>
                        <li>
                            <input type="radio" class="alone black">
                        </li>
                        <li>00017</li>
                        <li>Dennis Lin</li>
                        <li>2020/12/07</li>
                        <li>372,500</li>
                    </ul>

                    <ul>
                        <li>
                            <input type="radio" class="alone black">
                        </li>
                        <li>00014</li>
                        <li>Dennis Lin</li>
                        <li>2021/01/25</li>
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
                            <li class="head">Application Time</li>
                            <li>2020/11/20 10:15</li>
                        </ul>
                        <ul>
                            <li class="head">Status</li>
                            <li>For Verify</li>
                        </ul>
                        <ul>
                            <li class="head">Processing History
                            </li>
                            <li>Submitted (Dennis Lin at 2020/11/20 15:30)<br>
                                Checker Rejected: document is not complete. (Mary Jude Jeng Articulo at 2020/12/03 09:43)<br>
                                Submitted (Dennis Lin at 2020/12/04 11:30)<br>
                                Checker Checked (Mary Jude Jeng Articulo at 2020/12/04 13:55)<br>
                                OP Approved (Thalassa Wren Benzon at 2020/12/04 16:23)<br>
                                MD Approved (Kristel Tan at 2020/12/04 17:05)<br>
                                Releaser Released (Mary Jude Jeng Articulo at 2020/12/07 10:03)<br>
                                Liquidated (Dennis Lin at 2020/12/14 14:04)
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
                        <ul>
                            <li><b>Actual Amount After Verification</b></li>
                            <li><input type="number" style="width:100%"></li>

                            <li style="margin-top: 15px;"><b>Proof of Return or Release of Payment Balance</b></li>
                            <li>
                                <input type="file" style="width:100%" multiple>
                            </li>

                        <div class="btnbox">
                            <a class="btn">Finish Verifying</a>
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
<script defer src="js/a076d05399.js"></script>

<script>
  ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/leave_record.js"></script>
</html>