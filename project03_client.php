<?php 

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$sid = (isset($_GET['sid']) ?  $_GET['sid'] : 0);
if (  $sid < 1 || !is_numeric($sid)) {
  header( 'location:project02' );
}

?>
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

    dialogshow($('.tablebox a.add.a1'),$('.tablebox .dialog.a1'));   
    dialogshow($('.tablebox a.add.a2'),$('.tablebox .dialog.a2'));
    dialogshow($('.tablebox a.add.a3'),$('.tablebox .dialog.a3'));
    dialogshow($('.tablebox a.add.a4'),$('.tablebox .dialog.a4'));
    dialogshow($('.tablebox a.add.a5'),$('.tablebox .dialog.a5'));
    dialogshow($('.tablebox a.add.a6'),$('.tablebox .dialog.a6'));
    dialogshow($('.tablebox a.add.a7'),$('.tablebox .dialog.a7'));
    dialogshow($('.tablebox a.add.a8'),$('.tablebox .dialog.a8'));
    
    $('header').click(function(){dialogclear()});
    $('.block.right').click(function(){dialogclear()});
    
    
})

</script>

</head>

<body class="fourth">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block left">
            <div class="tablebox lv2a b-4">
                <ul class="head">
                    <li>Client</li>
                    <li>Client Type</li>
                    <li>Project Creator</li>
                    <li>Project Category</li>
                </ul>
                <ul>
                    <li>Office Systems</li>
                    <li>A - Architect/Designer</li>
                    <li>Stan</li>
                    <li>Office Systems</li>
                </ul>
            </div>
            <div class="tablebox lv3a">
                <!-- 一組qa -->
                <ul class="head">
                    <li>Venue</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li>
                        <div v-for='(receive_record, index) in stage_client_venue'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a1" class="add a1"></a>
                        <div id="dialog_a1" class="dialog a1">
                            <div class="formbox">
                                <dl>
                                    <dd><textarea placeholder="" v-model="venue"></textarea></dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="venue_clear">Cancel</a>
                                            <a class="btn small green" @click="venue_create">Create</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- 一組qa end -->
                <ul class="head">
                    <li>Sales Assigned</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_sales'>{{ receive_record.salesname }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a2" class="add a2"></a>
                        <div id="dialog_a2" class="dialog a2">
                            <div class="formbox">
                                <dl>
                                    <dd>
                                        <select v-model="uid">
                                          <option v-for="(item, index) in users" :value="item.id" :key="item.username">
                                              {{ item.username }}
                                          </option>
                                        </select>
                                    </dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="sales_clear">Cancel</a>
                                            <a class="btn small green" @click="sales_create">Create</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Target Date of Project</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_date'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a3" class="add a3"></a>
                        <div id="dialog_a3" class="dialog a3">
                            <div class="formbox">
                                <dl>
                                    <dd><input type="date" v-model="dt" /></dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="date_clear">Cancel</a>
                                            <a class="btn small green" @click="date_create">Create</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Project Status</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_status'>{{ receive_record.status }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a4" class="add a4"></a>
                        <div id="dialog_a4" class="dialog a4">
                            <div class="formbox">
                                <h6>Select Status</h6>
                                <dl>
                                    <dd>
                                        <select name="" id="" v-model="status">
                                            <option value="1">Planning</option>
                                            <option value="2">Pending Review</option>
                                            <option value="3">Pending Approval</option>
                                            <option value="4">For Revision</option>
                                            <option value="5">On Hold</option>
                                            <option value="6">Disapproved</option>
                                            <option value="7">Approved</option>
                                            <option value="8">On Progress</option>
                                            <option value="9">Completed</option>
                                            <option value="10">Special</option>
                                        </select>
                                    </dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="status_clear">Cancel</a>
                                            <a class="btn small green" @click="status_create">Save</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Project Priority</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_priority'>{{ receive_record.priority }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a5" class="add a5"></a>
                        <div id="dialog_a5" class="dialog a5">
                            <div class="formbox">
                                <h6>Select Priority</h6>
                                <dl>
                                    <dd>
                                        <select name="" id="" v-model="priority">
                                            <option value="1">No Priority</option>
                                            <option value="2">Low</option>
                                            <option value="3">Normal</option>
                                            <option value="4">High</option>
                                            <option value="5">Urgent</option>
                                        </select>
                                    </dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="priority_clear">Cancel</a>
                                            <a class="btn small green" @click="priority_create">Save</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Amount</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_amount'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a6" class="add a6"></a>
                        <div id="dialog_a6" class="dialog a6">
                            <div class="formbox">
                                <dl>
                                    <dd><input type="number" v-model="amount" /></dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="amount_clear">Cancel</a>
                                            <a class="btn small green" @click="amount_create">Save</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Competitors</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_competitor'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a7" class="add a7"></a>
                        <div id="dialog_a7" class="dialog a7">
                            <div class="formbox">
                                <dl>
                                    <dd><textarea placeholder="" v-model="competitor"></textarea></dd>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="competitor_clear">Cancel</a>
                                            <a class="btn small green" @click="competitor_create">Save</a>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="head">
                    <li>Additional Information</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li>The site is an existing office, and they just needed to renovate it quickly because there was an accident  that happened. (Stan at 2020/05/22 xx:xx) <br>
                    • sketch01.jpg
                    • sketch02.jpg
                    • requirement.pdf
                    • basic_idea.pdf
                    </li>
                    <li>
                        <a class="add a8"></a>
                        <div class="dialog a8">
                            <div class="formbox">
                                <dl>
                                    <dd><textarea placeholder="Update message here"></textarea></dd>
                                    <dd>
                                        <div class="browser_group"><span>Pictures:</span><input type="text" placeholder="No photo chosen"><button >Choose</button></div>
                                    </dd>
                                    <dd>
                                        <div class="browser_group"><span>Files:</span><input type="text" placeholder="No file chosen"><button >Choose</button></div>
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
                    </li>
                </ul>
            </div>
        </div>
        <div class="block right">
            <div class="list_function">
                <a class="btn small green">Arrange Meeting</a>
            </div>
            <div class="teskbox">
                <h5>Project Task Tracker</h5>
                <!-- list -->
                <div class="tablebox lv3b">
                    <!-- 一筆Tesk -->
                   <ul>
                       <li><b>1</b></li>
                       <li class="cmt">
                            <p>Go to the ??? to check Go to the ??? to check Go to the ??? to check Go to the ??? <br>
                            to check Go to the  ??? to check <i class="t">Stan at 2020/05/18 09:30</i></p>
                            
                            <p>Comment … <i class="t">Nestor Rosales at 2020/05/18 13:13</i></p>
                            <p>Comment … <i class="t">Kuan at 2020/05/18 16:11</i></p>
                       </li>
                       <li><a class="btn small orange cmt">Comment</a></li>
                   </ul>
                   <!-- 一筆Tesk end -->
                   <ul>
                       <li><b>2</b></li>
                       <li class="cmt">
                            <p>Go to the ??? to check Go to the ??? to check Go to the ??? to check Go to the ??? <br>
                            to check Go to the  ??? to check <i class="t">Stan at 2020/05/18 09:30</i></p>
                            
                            <p>Comment … <i class="t">Nestor Rosales at 2020/05/18 13:13</i></p>
                            <p>Comment … <i class="t">Kuan at 2020/05/18 16:11</i></p>
                       </li>
                       <li><a class="btn small orange cmt">Comment</a></li>
                   </ul>
                   <ul>
                       <li><b>3</b></li>
                       <li class="cmt">
                            <p>Go to the ??? to check Go to the ??? to check Go to the ??? to check Go to the ??? <br>
                            to check Go to the  ??? to check <i class="t">Stan at 2020/05/18 09:30</i></p>
                            
                            <p>Comment … <i class="t">Nestor Rosales at 2020/05/18 13:13</i></p>
                            <p>Comment … <i class="t">Kuan at 2020/05/18 16:11</i></p>
                       </li>
                       <li><a class="btn small orange cmt">Comment</a></li>
                   </ul>
               </div>
                <!-- list end -->
                <div class="tablebox lv3c">
                    <ul>
                        <li><textarea name="" id="" placeholder="Create a task here" ></textarea></li>
                        <li><a class="btn small green">Create</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript" src="js/project03_client.js" defer></script>
</html>
