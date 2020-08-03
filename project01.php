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
<title>FELIIX template pc</title>
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

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
    toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
    $('.tablebox').click(function(){
        $('.list_function .dialog').removeClass('show');
    })
    
})
</script>

</head>

<body class="fourth">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block">
            <div class="list_function">
                <div class="new_project">
                    <a class="add"></a>
                    <div class="dialog">
                        <h6>Create New Project:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Name / Client Name</dt>
                                <dd><input type="text" placeholder=""></dd>
                                <dt>Project Category</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">Office Systems</option>
                                        <option value="">Lighting</option>
                                    </select>
                                </dd>
                                <div class="half">
                                    <dt>Client Type</dt>
                                    <dd>
                                        <select name="" id="">
                                            <option value="">A - Architect/Designer</option>
                                            <option value="">B - Architect/Designer</option>
                                            <option value="">C - Architect/Designer</option>
                                            <option value="">A - End User</option>
                                            <option value="">B - End User</option>
                                            <option value="">C - End User</option>
                                            <option value="">A - Contractor</option>
                                            <option value="">B - Contractor</option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Priority</dt>
                                    <dd>
                                        <select name="" id="">
                                            <option value="">No Priority</option>
                                            <option value="">Low</option>
                                            <option value="">Normal</option>
                                            <option value="">High</option>
                                            <option value="">Urgent</option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Project Status</dt>
                                    <dd>
                                        <select name="" id="">
                                            <option value="">Planning</option>
                                            <option value="">Pending Review</option>
                                            <option value="">Pending Approval</option>
                                            <option value="">For Revision</option>
                                            <option value="">On Hold</option>
                                            <option value="">Disapproved</option>
                                            <option value="">Approved</option>
                                            <option value="">On Progress</option>
                                            <option value="">Completed</option>
                                            <option value="">Special</option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt></dt>
                                    <dd><input type="text" placeholder="" disabled></dd>
                                </div>
                                <dt>Reason for Estimated Closing Probability</dt>
                                <dd><textarea placeholder=""></textarea></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filter -->
                <div class="filter">
                    <select name="" id="">
                        <option value="">Project Category</option>
                        <option value="">Office Systems</option>
                        <option value="">Lighting</option>
                    </select>
                    <select name="" id="">
                        <option value="">Client Type</option>
                        <option value="">A - Architect/Designer</option>
                        <option value="">B - Architect/Designer</option>
                        <option value="">C - Architect/Designer</option>
                        <option value="">A - End User</option>
                        <option value="">B - End User</option>
                        <option value="">C - End User</option>
                        <option value="">A - Contractor</option>
                        <option value="">B - Contractor</option>
                    </select>
                    <select name="" id="">
                        <option value="">Priority</option>
                        <option value="">No Priority</option>
                        <option value="">Low</option>
                        <option value="">Normal</option>
                        <option value="">High</option>
                        <option value="">Urgent</option>
                    </select>
                    <select name="" id="">
                        <option value="">Status</option>
                        <option value="">Planning</option>
                        <option value="">Pending Review</option>
                        <option value="">Pending Approval</option>
                        <option value="">For Revision</option>
                        <option value="">On Hold</option>
                        <option value="">Disapproved</option>
                        <option value="">Approved</option>
                        <option value="">On Progress</option>
                        <option value="">Completed</option>
                        <option value="">Special</option>
                    </select>
                    <select name="" id="">
                        <option value="">Current Stage</option>
                        <option value="">Client</option>
                        <option value="">Proposal</option>
                        <option value="">A Meeting / Close Deal</option>
                        <option value="">Order</option>
                        <option value="">Execution Plan</option>
                        <option value="">Delivery</option>
                        <option value="">Installation</option>
                        <option value="">Client Feedback / After Service</option>
                    </select>
                </div>
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev">Previous</a>
                    <a class="page">1</a>
                    <a class="page">2</a>
                    <a class="page">3</a>
                    <b>...</b>
                    <a class="page">12</a>
                    <a class="next">Next</a>
                </div>
            </div>
            <!-- list -->
            <div class="tablebox lv1">
               <ul class="head">
                   <li>Project Category</li>
                   <li>Client Type</li>
                   <li>Priority</li>
                   <li>Project Name</li>
                   <li>Status</li>
                   <li>Estimated Closing Prob.</li>
                   <li>Project Creator</li>
                   <li>Execution Period</li>
                   <li>Current Stage</li>
                   <li>Recent Message</li>
               </ul>
               <ul>
                   <li>Office Systems</li>
                   <li><i class="ct01">A - Architect/Designer</i></li>
                   <li><i class="pt01">No Priority</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct02">B - Architect/Designer</i></li>
                   <li><i class="pt02">Low</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct03">C - Architect/Designer</i></li>
                   <li><i class="pt03">Normal</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct04">A - End User</i></li>
                   <li><i class="pt04">High</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct05">B - End User</i></li>
                   <li><i class="pt05">Urgent</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct06">C - End User</i></li>
                   <li><i class="pt01">No Priority</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct07">A - Contractor</i></li>
                   <li><i class="pt01">No Priority</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
               <ul>
                   <li>Lighting</li>
                   <li><i class="ct08">B - Contractor</i></li>
                   <li><i class="pt01">No Priority</i></li>
                   <li>Feliix Antel Office</li>
                   <li>Pending Approval</li>
                   <li>80</li>
                   <li>Marion</li>
                   <li>2019/12/17 ~ 2020/01/17</li>
                   <li>Client Feedback / After Service</li>
                   <li>2020/03/31 17:21 Wren</li>
               </ul>
           </div>
           <!-- list end -->
           <div class="list_function">
               <!-- 分頁 -->
               <div class="pagenation">
                   <a class="prev">Previous</a>
                   <a class="page">1</a>
                   <a class="page">2</a>
                   <a class="page">3</a>
                   <b>...</b>
                   <a class="page">12</a>
                   <a class="next">Next</a>
               </div>
           </div>
        </div>
    </div>
</div>
</body>
</html>
