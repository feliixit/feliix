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

<script>
$(function(){
    $('header').load('include/header.php');
    //
    dialogshow($('.list_function a.add.red'),$('.list_function .dialog.r-add'));
    dialogshow($('.list_function a.edit.red'),$('.list_function .dialog.r-edit'));
    dialogshow($('.list_function a.add.blue'),$('.list_function .dialog.d-add'));
    dialogshow($('.list_function a.edit.blue'),$('.list_function .dialog.d-edit'));
    // left block Reply
    dialogshow($('.btnbox a.reply.r1'),$('.btnbox .dialog.r1'));
    dialogshow($('.btnbox a.reply.r2'),$('.btnbox .dialog.r2'));
    dialogshow($('.btnbox a.reply.r3'),$('.btnbox .dialog.r3'));
    dialogshow($('.btnbox a.reply.r4'),$('.btnbox .dialog.r4'));
    // 套上 .dialogclear 關閉所有的跳出框
    $('.dialogclear').click(function(){dialogclear()});
    // 根據 select 分類
    $('#opType').change(function(){
        //console.log('Operation Type:'+$("#opType").val());
        var f = $("#opType").val();
        $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
    })
    $('#opType2').change(function(){
        //console.log('Operation Type:'+$("#opType").val());
        var f = $("#opType2").val();
        $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
    })
    $('#opType3').change(function(){
        //console.log('Operation Type:'+$("#opType").val());
        var f = $("#opType3").val();
        $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
    })
})

</script>

</head>

<body class="fourth other">
 	
<div class="bodybox">
    <!-- header -->
	<header class="dialogclear">header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="list_function main">
            <div class="block">
                <!-- add red -->
                <div class="popupblock">
                    <a class="add red"></a>
                    <!-- dialog -->
                    <div class="dialog r-add">
                        <h6>Add/Duplicate Task</h6>
                        <div class="tablebox s1">
                            <ul>
                                <li class="head">Operation Type:</li>
                                <li>
                                    <select name="" id="opType3">
                                        <option value="add">Add New Tesk</option>
                                        <option value="dup">Duplicate Existing Task</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="formbox s2 add">
                            <dl>
                                <dt>Title:</dt>
                                <dd><input type="text"></dd>
                            </dl>
                            <dl>
                                <dt>Priority:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                            </dl>
                            <dl>
                                <dt>Assignee:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Collaborator:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Due Date:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Date</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Task Detail:</dt>
                                <dd><textarea placeholder=""></textarea></dd>
                            </dl>
                            <dl>
                                <dt>Pictures:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Picture</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Files:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose File</button></div></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Calendar</a>
                                <a class="btn small green">Create</a>
                            </div>
                        </div>
                        <div class="tablebox s2 dup">
                            <ul>
                                <li class="head">Target Tesk:</li>
                                <li class="mix">
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                    <a class="btn small green">Duplicate</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- dialog end -->
               </div>
                <!-- edit red -->
                <div class="popupblock">
                    <a class="edit red"></a>
                    <!-- dialog -->
                    <div class="dialog r-edit">
                        <h6>Edit/Delete Task:</h6>
                        <div class="tablebox s1">
                            <ul>
                                <li class="head">Operation Type:</li>
                                <li>
                                    <select name="" id="opType">
                                        <option value="edit">Edit Existing Task</option>
                                        <option value="del">Delete Existing Task</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 del">
                            <ul>
                                <li class="head">Target Tesk:</li>
                                <li class="mix">
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
                                <li class="mix">
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                    <a class="btn small green">Load</a>
                                </li>
                            </ul>
                        </div>
                        <div class="formbox s2 edit">
                            <dl>
                                <dt>Title:</dt>
                                <dd><input type="text"></dd>
                            </dl>
                            <dl>
                                <dt>Priority:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                            </dl>
                            <dl>
                                <dt>Status:</dt>
                                <dd>
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                </dd>
                            </dl>
                            <dl>
                                <dt>Assignee:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Collaborator:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Due Date:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Date</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Description:</dt>
                                <dd><textarea placeholder=""></textarea></dd>
                            </dl>
                            <dl>
                                <dt>Pictures:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Picture</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Files:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose File</button></div></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Calendar</a>
                                <a class="btn small green">Save</a>
                            </div>
                        </div>
                    </div>
                    <!-- dialog end -->
               </div>
                <!-- add -->
                <div class="popupblock">
                    <a class="add blue"></a>
                    <!-- dialog -->
                    <div class="dialog d-add">
                        <h6>Add Message:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Title:</dt>
                                <dd><input type="text" placeholder=""></dd>
                            </dl>
                            <dl>
                                <dt>Assignee:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Description:</dt>
                                <dd><textarea placeholder=""></textarea></dd>
                            </dl>
                            <dl>
                                <dt>Pictures:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Picture</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Files:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose File</button></div></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Create</a>
                            </div>
                        </div>
                    </div>
                    <!-- dialog end -->
                </div>
                <!-- edit -->
                <div class="popupblock">
                    <a class="edit blue"></a>
                    <!-- dialog -->
                    <div class="dialog d-edit">
                        <h6>Edit/Delete Message:</h6>
                        <div class="tablebox s1">
                            <ul>
                                <li class="head">Operation Type:</li>
                                <li>
                                    <select name="" id="opType2">
                                        <option value="edit">Edit Existing Message</option>
                                        <option value="del">Delete Existing Message</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 del">
                            <ul>
                                <li class="head">Target Message:</li>
                                <li class="mix">
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
                                <li class="head">Target Message:</li>
                                <li class="mix">
                                    <select name="" id="">
                                        <option value="">1</option>
                                        <option value="">2</option>
                                    </select>
                                    <a class="btn small green">Load</a>
                                </li>
                            </ul>
                        </div>
                        <div class="formbox s2 edit">
                            <dl>
                                <dt>Title:</dt>
                                <dd><input type="text" placeholder=""></dd>
                            </dl>
                            <dl>
                                <dt>Assignee:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Browse</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Description:</dt>
                                <dd><textarea placeholder=""></textarea></dd>
                            </dl>
                            <dl>
                                <dt>Pictures:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose Picture</button></div></dd>
                            </dl>
                            <dl>
                                <dt>Files:</dt>
                                <dd><div class="browser_group"><input type="text"><button >Choose File</button></div></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small">Cancel</a>
                                <a class="btn small green">Save</a>
                            </div>
                        </div>
                    </div>
                    <!-- dialog end -->
                </div>
                <!-- calendar -->
                <div class="popupblock">
                   <a class="calendar"></a>
                </div>
                <!-- tag -->
                <b class="tag focus">PROJECT</b>
                <b class="tag">UNDP / Ranee</b>
            </div>
        </div>
        <div class="block left">
             <div class="list_function dialogclear">
                <!-- Filter -->
                <div class="filter">
                    <b>Filter:</b>
                    <select name="" id="">
                        <option value="">Info. Type</option>
                        <option value=""></option>
                        <option value=""></option>
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
                        <option value="">Due Date</option>
                        <option value=""></option>
                        <option value=""></option>
                        <option value=""></option>
                        <option value=""></option>
                        <option value=""></option>
                    </select>
                </div>
             </div>
             <div class="teskbox dialogclear">
                 <a class="btn small red">High</a>
                 <a class="btn small green">Done</a>
                 <b>[Task] Quote and Randering</b>
                 <a class="btn small blue right">Arrange Meeting</a>
             </div>
             <div class="teskbox dialogclear">
                 <div class="tablebox m01">
                     <ul>
                         <li><b>Creator</b></li>
                         <li><a class="man" style="background-image: url(images/man/man1.jpg);"></a></li>
                     </ul>
                     <ul>
                         <li><b>Assignee</b></li>
                         <li>
                             <a class="man" style="background-image: url(images/man/man2.jpg);"></a>
                             <a class="man" style="background-image: url(images/man/man3.jpg);"></a>
                         </li>
                     </ul>
                     <ul>
                         <li><b>Collaborator</b></li>
                         <li>
                             <a class="man" style="background-image: url(images/man/man4.jpg);"></a>
                             <a class="man" style="background-image: url(images/man/man5.jpg);"></a>
                             <a class="man" style="background-image: url(images/man/man6.jpg);"></a>
                         </li>
                     </ul>
                     <ul>
                         <li><b>Due Date</b></li>
                         <li>November 4, 2020</li>
                     </ul>
                     <ul>
                         <li><b>Description</b></li>
                         <li>Work-related musculoskeletal and cardiovascular disorders are still prevalent in today’s working population. Nowadays, risk assessments are usually performed via self-reports or observations, which have relatively low reliability.</li>
                     </ul>
                     <ul>
                         <li><b>Attachments</b></li>
                         <li>
                             <a class="attch">requirement.doc</a>
                             <a class="attch">requirement.doc</a>
                         </li>
                     </ul>
                 </div>
             </div>
             <div class="teskbox scroll">
                 <div class="tableframe">
                     <div class="tablebox m02">
                     <!-- 1 message -->
                     <ul>
                         <li class="dialogclear">
                             <a class="man" style="background-image: url(images/man/man7.jpg);"></a>
                             <i class="info">
                                 <b>Nestor Rosales</b><br>
                                 2:00 PM<br>
                                 May 3, 2020
                             </i>
                         </li>
                         <li>
                             <div class="msg">
                                <div class="msgbox dialogclear">
                                    <p>Hi Nestor. Here are the deliverables. Please check. Thank you.</p>
                                     <a class="attch">building1.jpg</a>
                                     <a class="attch">building.jpg</a>
                                     <a class="attch">quotation.pdf</a>
                                     <a class="attch">rendering.pdf</a>
                                </div>
                                 <div class="btnbox">
                                     <a class="btn small green reply r1">Reply</a>
                                     <!-- dialog -->
                                     <div class="dialog reply r1">
                                        <div class="formbox">
                                            <dl>
                                                <dd><textarea name="" id="" ></textarea></dd>
                                                <dd>
                                                    <div class="browser_group"><span>Photo:</span><input type="text"><button >Choose</button></div>
                                                </dd>

                                                <dd>
                                                    <div class="browser_group"><span>File:</span><input type="text"><button >Choose</button></div>
                                                </dd>
                                                <dd>
                                                    <div class="btnbox">
                                                        <a class="btn small orange">Cancel</a>
                                                        <a class="btn small green">Save</a>
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                     </div>
                                     <!-- dialog end -->
                                     <a class="btn small yellow">Delete</a>
                                 </div>
                             </div>
                         </li>
                     </ul>
                     <!-- 1 message end -->
                     <ul class="deleted dialogclear">
                         <li>
                             <a class="man" style="background-image: url(images/man/man8.jpg);"></a>
                             <i class="info">
                                 <b>Dennis Lin</b><br>
                                 1:00 PM<br>
                                 May 30, 2020
                             </i>
                         </li>
                         <li>
                             <div class="msg">
                                <div class="msgbox">
                                    <p>Deleted by <a href="" class="tag_name">@Nestor Rosales</a> at 2020/04/03 15:47</p>
                                </div>
                             </div>
                         </li>
                     </ul>
                     
                     <ul>
                         <li class="dialogclear">
                             <a class="man" style="background-image: url(images/man/man9.jpg);"></a>
                             <i class="info">
                                 <b>Kuan Lu</b><br>
                                 1:30 PM<br>
                                 May 30, 2020
                             </i>
                         </li>
                         <li>
                             <div class="msg">
                                <div class="msgbox dialogclear">
                                    <p><a href="" class="tag_name">@Dennis Lin</a> I think this task needs to be more careful.</p>
                                </div>
                                 <div class="btnbox">
                                     <a class="btn small green reply r2">Reply</a>
                                     <!-- dialog -->
                                     <div class="dialog reply r2">
                                        <div class="formbox">
                                            <dl>
                                                <dd><textarea name="" id="" ></textarea></dd>
                                                <dd>
                                                    <div class="browser_group"><span>Photo:</span><input type="text"><button >Choose</button></div>
                                                </dd>

                                                <dd>
                                                    <div class="browser_group"><span>File:</span><input type="text"><button >Choose</button></div>
                                                </dd>
                                                <dd>
                                                    <div class="btnbox">
                                                        <a class="btn small orange">Cancel</a>
                                                        <a class="btn small green">Save</a>
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                     </div>
                                     <!-- dialog end -->
                                     <a class="btn small yellow">Delete</a>
                                 </div>
                             </div>
                         </li>
                     </ul>
                 </div>
                 </div>
                 <div class="tablebox lv3c m03 dialogclear">
                    <ul>
                        <li><textarea name="" id="" placeholder="Write your comment here" ></textarea></li>
                        <li><a class="btn small green">Comment</a></li>
                    </ul>
                </div>
             </div>
        </div>
        <div class="block right ">
            <div class="list_function dialogclear">
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
            <div class="teskbox">
                <h5>[MESSAGE] Information from Customer</h5>
                <div class="tablebox2">
                    <ul>
                         <li class="teskblock dialogclear">
                            <div class="tablebox m01">
                                 <ul>
                                     <li><b>Creator</b></li>
                                     <li><a class="man" style="background-image: url(images/man/man10.jpg);"></a></li>
                                 </ul>
                                 <ul>
                                     <li><b>Date</b></li>
                                     <li>November 4, 2020 / 10:07 AM</li>
                                 </ul>
                                 <ul>
                                     <li><b>Assignee</b></li>
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man11.jpg);"></a>
                                         <a class="man" style="background-image: url(images/man/man12.jpg);"></a>
                                     </li>
                                 </ul>
                                 <ul>
                                     <li><b>Description</b></li>
                                     <li>Below are the information that the client provided: <br>
                                         • The site is an existing office, they just needed to renovate it quickly because there was an accident that happened. <br>
                                         • They need workstations, low partitions and suspended panel light. <br>
                                         • They already have existing layout or plan for the office.
                                     </li>
                                 </ul>
                                 <ul>
                                     <li><b>Attachments</b></li>
                                     <li>
                                         <a class="attch">requirement.doc</a>
                                     </li>
                                 </ul>
                             </div>
                         </li>
                         <li class="teskblock">
                             <div class="tableframe">
                                 <div class="tablebox m02">
                                 <!-- 1 message -->
                                 <ul>
                                     <li class="dialogclear">
                                         <a class="man" style="background-image: url(images/man/man7.jpg);"></a>
                                         <i class="info">
                                             <b>Nestor Rosales</b><br>
                                             2:00 PM<br>
                                             May 3, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox dialogclear">
                                                <p>Hi Nestor. Here are the deliverables. Please check. Thank you.</p>
                                                 <a class="attch">building1.jpg</a>
                                                 <a class="attch">building.jpg</a>
                                                 <a class="attch">quotation.pdf</a>
                                                 <a class="attch">rendering.pdf</a>
                                            </div>
                                             <div class="btnbox">
                                                 <a class="btn small green reply r3">Reply</a>
                                                 <!-- dialog -->
                                                 <div class="dialog reply r3">
                                                    <div class="formbox">
                                                        <dl>
                                                            <dd><textarea name="" id="" ></textarea></dd>
                                                            <dd>
                                                                <div class="browser_group"><span>Photo:</span><input type="text"><button >Choose</button></div>
                                                            </dd>

                                                            <dd>
                                                                <div class="browser_group"><span>File:</span><input type="text"><button >Choose</button></div>
                                                            </dd>
                                                            <dd>
                                                                <div class="btnbox">
                                                                    <a class="btn small orange">Cancel</a>
                                                                    <a class="btn small green">Save</a>
                                                                </div>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                 </div>
                                                 <!-- dialog end -->
                                                 <a class="btn small yellow">Delete</a>
                                             </div>
                                         </div>
                                     </li>
                                 </ul>
                                 <!-- 1 message end -->
                                 <ul class="deleted dialogclear">
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man8.jpg);"></a>
                                         <i class="info">
                                             <b>Dennis Lin</b><br>
                                             1:00 PM<br>
                                             May 30, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox">
                                                <p>Deleted by <a href="" class="tag_name">@Nestor Rosales</a> at 2020/04/03 15:47</p>
                                            </div>
                                         </div>
                                     </li>
                                 </ul>

                                 <ul>
                                     <li class="dialogclear">
                                         <a class="man" style="background-image: url(images/man/man9.jpg);"></a>
                                         <i class="info">
                                             <b>Kuan Lu</b><br>
                                             1:30 PM<br>
                                             May 30, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox dialogclear">
                                                <p><a href="" class="tag_name">@Dennis Lin</a> I think this task needs to be more careful.</p>
                                            </div>
                                             <div class="btnbox">
                                                 <a class="btn small green reply r4">Reply</a>
                                                 <!-- dialog -->
                                                 <div class="dialog reply r4">
                                                    <div class="formbox">
                                                        <dl>
                                                            <dd><textarea name="" id="" ></textarea></dd>
                                                            <dd>
                                                                <div class="browser_group"><span>Photo:</span><input type="text"><button >Choose</button></div>
                                                            </dd>

                                                            <dd>
                                                                <div class="browser_group"><span>File:</span><input type="text"><button >Choose</button></div>
                                                            </dd>
                                                            <dd>
                                                                <div class="btnbox">
                                                                    <a class="btn small orange">Cancel</a>
                                                                    <a class="btn small green">Save</a>
                                                                </div>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                 </div>
                                                 <!-- dialog end -->
                                                 <a class="btn small yellow">Delete</a>
                                             </div>
                                         </div>
                                     </li>
                                 </ul>
                             </div>
                             </div>
                             <div class="tablebox lv3c m03 dialogclear">
                                <ul>
                                    <li><textarea name="" id="" placeholder="Write your comment here" ></textarea></li>
                                    <li><a class="btn small green">Comment</a></li>
                                </ul>
                             </div>
                         </li>
                    </ul>
                </div>
            </div>
            <div class="teskbox red dialogclear">
                <h5>[MESSAGE] Information from Customer</h5>
                <div class="tablebox2">
                    <ul>
                        <li class="teskblock">
                            <div class="tablebox m01">
                                 <ul>
                                     <li><b>Creator</b></li>
                                     <li><a class="man" style="background-image: url(images/man/man1.jpg);"></a></li>
                                 </ul>
                                 <ul>
                                     <li><b>Date</b></li>
                                     <li>November 4, 2020 / 10:07 AM</li>
                                 </ul>
                                 <ul>
                                     <li><b>Assignee</b></li>
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man2.jpg);"></a>
                                         <a class="man" style="background-image: url(images/man/man3.jpg);"></a>
                                     </li>
                                 </ul>
                            </div>
                        </li>
                        <li class="teskblock">
                            <div class="tableframe">
                                 <div class="tablebox m02">
                                 <!-- 1 message -->
                                 <ul>
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man8.jpg);"></a>
                                         <i class="info">
                                             <b>Dennis Lin</b><br>
                                             1:00 PM<br>
                                             May 30, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox">
                                                <p>Edited 2020/04/03 15:47</p>
                                            </div>
                                         </div>
                                     </li>
                                 </ul>
                                 <!-- 1 message end -->
                                 <ul class="deleted">
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man9.jpg);"></a>
                                         <i class="info">
                                             <b>Dennis Lin</b><br>
                                             1:00 PM<br>
                                             May 30, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox">
                                                <p>Deleted at 2020/04/03 15:47</p>
                                            </div>
                                         </div>
                                     </li>
                                 </ul>
                                 <ul>
                                     <li>
                                         <a class="man" style="background-image: url(images/man/man10.jpg);"></a>
                                         <i class="info">
                                             <b>Dennis Lin</b><br>
                                             1:00 PM<br>
                                             May 30, 2020
                                         </i>
                                     </li>
                                     <li>
                                         <div class="msg">
                                            <div class="msgbox">
                                                <p>Edited 2020/04/03 15:47</p>
                                            </div>
                                         </div>
                                     </li>
                                 </ul>
                             </div>
                            </div>
                        </li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
</div>
</body>
</html>
