<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="Images/favicon.ico" />
<link rel="Bookmark" href="Images/favicon.ico" />
<link rel="icon" href="Images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="Images/iosicon.png"/>

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
<link rel="stylesheet" type="text/css" href="Css/default.css"/>
<link rel="stylesheet" type="text/css" href="Css/ui.css"/>
<link rel="stylesheet" type="text/css" href="Css/case.css"/>
<link rel="stylesheet" type="text/css" href="Css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="Js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="Js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="Js/main.js" defer></script>

<script>
$(function(){
    $('header').load('Include/header.htm');
    //

    dialogshow($('.list_function a.add'),$('.list_function .dialog.d-add'));
    dialogshow($('.list_function a.edit'),$('.list_function .dialog.d-edit'));
    dialogshow($('.list_function a.fn1'),$('.list_function .dialog.fn1'));
    dialogshow($('.list_function a.fn2'),$('.list_function .dialog.fn2'));
    dialogshow($('.list_function a.fn3'),$('.list_function .dialog.fn3'));
    dialogshow($('.list_function a.fn4'),$('.list_function .dialog.fn4'));
    dialogshow($('.list_function a.fn5'),$('.list_function .dialog.fn5'));
    dialogshow($('.list_function a.fn6'),$('.list_function .dialog.fn6'));
    
    $('header').click(function(){dialogclear()});
    $('.block.left').click(function(){dialogclear()});
    $('.block.right').click(function(){dialogclear()});
    
    //
    $('#opType').change(function(){
        //console.log('Operation Type:'+$("#opType").val());
        var f = $("#opType").val();
        $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
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
        <div class="list_function main">
            <div class="block">
                <!-- add -->
                <div class="popupblock">
                    <a class="add"></a>
                    <div class="dialog d-add">
                        <h6>Add New Stage:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Sequence:</dt>
                                <dd><input type="text" placeholder=""></dd>
                                <dt>Stage:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">Client</option>
                                        <option value="">Proposal</option>
                                        <option value="">A Meeting / Close Deal</option>
                                        <option value="">Order</option>
                                        <option value="">Execution Plan</option>
                                        <option value="">Delivery</option>
                                        <option value="">Installation</option>
                                        <option value="">Client Feedback / After Service</option>
                                    </select>
                                </dd>
                                <dt>Stage Status:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">Ongoing</option>
                                        <option value="">Pending</option>
                                        <option value="">Close</option>
                                    </select>
                                </dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- edit -->
                <div class="popupblock">
                    <a class="edit"></a>
                    <div class="dialog d-edit">
                        <h6>Edit/Delete Stage:</h6>
                        <div class="tablebox s1">
                            <ul>
                                <li class="head">Operation Type:</li>
                                <li>
                                    <select name="" id="opType">
                                        <option value="edit">Edit Existing Stage</option>
                                        <option value="del">Delete Existing Stage</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 del">
                            <ul>
                                <li class="head">Target Sequence:</li>
                                <li>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                    <a class="btn small">Delete</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 edit">
                            <ul>
                                <li class="head">Target Sequence:</li>
                                <li>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                    <a class="btn small green">Load</a>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Sequence:</li>
                                <li><input type="text" placeholder=""></li>
                            </ul>
                            <ul>
                                <li class="head">Stage:</li>
                                <li>
                                    <select name="" id="">
                                        <option value="">Client</option>
                                        <option value="">Proposal</option>
                                        <option value="">A Meeting / Close Deal</option>
                                        <option value="">Order</option>
                                        <option value="">Execution Plan</option>
                                        <option value="">Delivery</option>
                                        <option value="">Installation</option>
                                        <option value="">Client Feedback / After Service</option>
                                    </select>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Stage Status:</li>
                                <li>
                                    <select name="" id="">
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Reason for Editing:</li>
                                <li><textarea placeholder=""></textarea></li>
                            </ul>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Save</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- tag -->
                <b class="tag focus">PROJECT</b>
                <b class="tag">UNDP / Ranee</b>
            </div>
            <div class="block fn">
                <div class="popupblock">
                    <a class="fn1">Change Project Status</a>
                    <div class="dialog fn1">
                        <h6>Change Project Status:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Current Status:</dt>
                                <dd><input type="text"></dd>
                                <dt>Change to:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                                <dt>Reason:</dt>
                                <dd><textarea name="" id="" ></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Create</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a class="fn2">Edit Project Info</a>
                    <div class="dialog fn2">
                        <h6>Edit Project Info:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Project Category:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                                <div class="half">
                                    <dt>Client Type:</dt>
                                    <dd>
                                        <select name="" id="">
                                            <option value="">1</option>
                                            <option value="">2</option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Priority</dt>
                                    <dd>
                                        <select name="" id="">
                                            <option value="">1</option>
                                            <option value="">2</option>
                                        </select>
                                    </dd>
                                </div>
                                <dt>Project Creator:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button disabled>Browser</button></div>
                                </dd>
                                <dt>Contact Person:</dt>
                                <dd><input type="text"></dd>
                                <div class="half">
                                    <dt>Project Location:</dt>
                                    <dd><input type="text"></dd>
                                </div>
                                <div class="half">
                                    <dt>Contact Number:</dt>
                                    <dd><input type="text"></dd>
                                </div>
                                <dt>Reason for Editing Project Info:</dt>
                                <dd><textarea name="" id="" cols="30" rows="10"></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a class="fn3">Action to Comments</a>
                    <div class="dialog fn3">
                        <h6>Action to Comments:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Comment:</dt>
                                <dd><textarea name="" id="" ></textarea></dd>
                                <dt>Pictures:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <dt>Files:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="popupblock">
                    <a class="fn4">Acton to Est. Closing Prob.</a>
                    <div class="dialog fn4">
                        <h6>Acton to Est. Closing Prob.:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Estimated Closing Probability:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                                <dt class="head">Reason for Estimated Closing Probability:</dt>
                                <dd><textarea name="" id="" ></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a class="fn5">Acton to Project Details</a>
                    <div class="dialog fn5">
                        <h6>Acton to Project Details:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Detail Type:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" ></textarea></dd>
                                <dt>Pictures:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <dt>Files:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a class="fn6">Submit Downpayment Proof</a>
                    <div class="dialog fn6">
                        <h6>Submit Downpayment Proof:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Pictures:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <dt>Files:</dt>
                                <dd>
                                    <div class="browser_group"><input type="text"><button >Choose</button></div>
                                </dd>
                                <dt class="head">Remarks:</dt>
                                <dd><textarea name="" id="" ></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small">Cancel</a>
                                    <a class="btn small green">Submit</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block left">
            <div class="tablebox lv2a b-4">
                <ul class="head">
                    <li>Category</li>
                    <li>Client Type</li>
                    <li>Priority</li>
                    <li>Project Creator</li>
                </ul>
                <ul>
                    <li>Office Systems</li>
                    <li>A - Architect/Designer</li>
                    <li>Urgent</li>
                    <li>Stan</li>
                </ul>
            </div>
            <div class="tablebox lv2a b-3">
                <ul class="head">
                    <li>Project Status</li>
                    <li>Current Stage</li>
                    <li>Project Execution Period</li>
                </ul>
                <ul>
                    <li>Pending Approval</li>
                    <li>A Meeting / Close Deal</li>
                    <li>2020/03/11 ~ / /</li>
                </ul>
                <ul class="head">
                    <li>Project Location</li>
                    <li>Contact Person</li>
                    <li>Contact Number</li>
                </ul>
                <ul>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
            <div class="tablebox lv2a b-2">
                <ul class="head">
                    <li>Comments</li>
                    <li>Estimated Closing Probability</li>
                </ul>
                <ul>
                    <li>
                        • We didn’t get the deal at the first time A-Meeting because xxxxxxx (Kristel at 2020/03/24 17:15) <br>
                        • New feedbacks from client are <br>
                         1. xxxxxxxxxxxxxx, <br>
                         2. xxxxxxxxxxxxxxx, <br>
                         3. xxxxxxxxxxxxxxxxxx, <br>
                         4. xxxxxxxxxxxxxxxxxxxxxxx. <br>
                         (Kristel at 2020/03/25 11:21)
                    </li>
                    <li>
                        • 50: My reason is xxxxxxxxxxxxxx <br>
                        (Stan at 2020/03/11 11:03) <br>
                        • 70: Client gave feedbacks xxxxxxxxxxxxxx <br>
                        (Kristel at 2020/03/15 15:49)
                    </li>
                </ul>
            </div>
            <div class="tablebox lv2a">
                <ul class="head">
                    <li>Project Details</li>
                </ul>
                <ul>
                    <li>
                        • Requirements: xxxxxxxxxxxxxxxxxx; requirement.jpg; spec.jpg  quotation.pdf; spec.pdf <br>
                        (Stan at 2020/03/11 11:03) <br>
                        • Discount: 20% (Kristel at 2020/03/15 15:49)
                    </li>
                </ul>
            </div>
        </div>
        <div class="block right">
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
            <!-- list -->
            <div class="tablebox lv2b">
               <ul class="head">
                   <li>Sequence</li>
                   <li>Stage</li>
                   <li>Status</li>
                   <li>Execution Period</li>
                   <li>Created by</li>
                   <li>Post/Reply</li>
                   <li>Recent Message</li>
               </ul>
               <ul>
                   <li>1</li>
                   <li>A Meeting / Close Deal</li>
                   <li>Close</li>
                   <li>2020/03/11 ~ 2020/03/13</li>
                   <li>2020/03/11 11:03 STAN</li>
                   <li>2/29</li>
                   <li>2020/03/12 17:21 Wren</li>
               </ul>
               <ul>
                   <li>2</li>
                   <li>Proposal</li>
                   <li>Ongoing</li>
                   <li>2020/03/11 ~ 2020/03/13</li>
                   <li>2020/03/11 11:03 STAN</li>
                   <li>2/29</li>
                   <li>2020/03/12 17:21 Wren</li>
               </ul>
               <ul>
                   <li>3</li>
                   <li>Client</li>
                   <li>Ongoing</li>
                   <li>2020/03/11 ~ 2020/03/13</li>
                   <li>2020/03/11 11:03 STAN</li>
                   <li>2/29</li>
                   <li>2020/03/12 17:21 Wren</li>
               </ul>
               <ul>
                   <li>4</li>
                   <li>A Meeting / Close Deal</li>
                   <li>Ongoing</li>
                   <li>2020/03/11 ~ 2020/03/13</li>
                   <li>2020/03/11 11:03 STAN</li>
                   <li>2/29</li>
                   <li>2020/03/12 17:21 Wren</li>
               </ul>
              
           </div>
           <!-- list end -->
        </div>
    </div>
</div>
</body>
</html>
