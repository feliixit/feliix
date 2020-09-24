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

    //dialogshow($('.tablebox a.add.a1'),$('.tablebox .dialog.a1'));   
    //dialogshow($('.tablebox a.add.a2'),$('.tablebox .dialog.a2'));
    //dialogshow($('.tablebox a.add.a3'),$('.tablebox .dialog.a3'));
    // dialogshow($('.tablebox a.add.a4'),$('.tablebox .dialog.a4'));
    // dialogshow($('.tablebox a.add.a5'),$('.tablebox .dialog.a5'));
    //dialogshow($('.tablebox a.add.a6'),$('.tablebox .dialog.a6'));
    //dialogshow($('.tablebox a.add.a7'),$('.tablebox .dialog.a7'));
    //dialogshow($('.tablebox a.add.a8'),$('.tablebox .dialog.a8'));
    
    $('header').click(function(){dialogclear()});
    $('.block.right').click(function(){dialogclear()});
    $('.block.left ul li:first-child').click(function(){dialogclear()});
    
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
                    <li>{{ contactor }}</li>
                    <li>{{ client_type }}</li>
                    <li>{{ username }}</li>
                    <li>{{ category }}</li>
                </ul>
            </div>
            <div class="tablebox lv3a">
                <!-- 一組qa -->
                <ul class="head">
                    <li style="text-align: center !important;">Venue</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li>
                        <div v-for='(receive_record, index) in stage_client_venue'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a1" class="add a1" @click="dialogshow1"></a>
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
                    <li style="text-align: center !important;">Sales Assigned</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_sales'>{{ receive_record.salesname }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a2" class="add a2" @click="dialogshow2"></a>
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
                    <li style="text-align: center !important;">Target Date of Project</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_date'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a3" class="add a3" @click="dialogshow3"></a>
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
                    <li style="text-align: center !important;">Project Status</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_status'>{{ receive_record.status }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a4" class="add a4" @click="dialogshow4"></a>
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
                    <li style="text-align: center !important;">Project Priority</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_priority'>{{ receive_record.priority }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li>
                        <a id="add_a5" class="add a5" @click="dialogshow5"></a>
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
                    <li style="text-align: center !important;">Amount</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_amount'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a6" class="add a6" @click="dialogshow6"></a>
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
                    <li style="text-align: center !important;">Competitors</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                    <li><div v-for='(receive_record, index) in stage_client_competitor'>{{ receive_record.message }}  ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div></li>
                    <li>
                        <a id="add_a7" class="add a7" @click="dialogshow7"></a>
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
                    <li style="text-align: center !important;">Additional Information</li>
                    <li><!--請留空--></li>
                </ul>
                <ul>
                <li>
                        <div v-for='(receive_record, index) in stage_client_infomation'>{{ receive_record.message }} ({{ receive_record.username }} at {{ receive_record.created_at }})
                            <br v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items">
                                • <a :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>&nbsp&nbsp
                                </span>
                         
                        </div>
                    </li>
                    <li>
                        <a id="add_a8" class="add a8" @click="dialogshow8"></a>
                        <div id="dialog_a8" class="dialog a8">
                            <div class="formbox">
                                <dl>
                                    <dd><textarea placeholder="" v-model="prof_remark"></textarea></dd>
                                    <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                          <span class="upload-des">choose file</span>
                                          <input
                                            class="input"
                                            type="file"
                                            name="prof_file"
                                            value
                                            placeholder="choose file"
                                            ref="prof_file"
                                            v-show="prof_canSub"
                                            @change="prof_changeFile()"
                                            multiple
                                          />
                                    </div>
                                  </div>
                                </dd>

                                <div class="file-list">
                                  <div class="file-item" v-for="(item,index) in prof_fileArray" :key="index">
                                    <p style="text-align: left;">
                                      {{item.name}}
                                      <span
                                        @click="prof_deleteFile(index)"
                                        v-show="item.progress==0"
                                        class="upload-delete"
                                      ><i class="fas fa-backspace"></i>
                                        </span>
                                    </p>
                                    <div class="progress-container" v-show="item.progress!=0">
                                      <div class="progress-wrapper">
                                        <div class="progress-progress" :style="'width:'+item.progress*100+'%'"></div>
                                      </div>
                                      <div class="progress-rate">
                                        <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                        <span v-else><i class="fas fa-check-circle"></i></span>  
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                    <dd>
                                        <div class="btnbox">
                                            <a class="btn small orange" @click="prof_clear">Cancel</a>
                                            <a class="btn small green" @click="prof_create">Save</a>
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
                   <ul v-for='(receive_record, index) in project03_client_stage_task'>
                       <li><b>{{ receive_record.num }}</b></li>
                       <li class="cmt">
                            <p>{{ receive_record.message }} <i class="t">{{ receive_record.username }} at {{ receive_record.created_at }}</i></p>
                                <p v-for="item in receive_record.items">
                                    {{ item.f_message }}
                                    <i class="t"> {{ item.f_username }} at {{ item.f_created_at }}</i>
                                </p>
                       </li>
                       <li style="position:relative;">

                        <a  class="btn small orange cmt" @click="comment_show(receive_record.id)">Comment</a>

                        <div :id="'btn' + receive_record.id" :ref="'dialog' + receive_record.num" class="commentbox">
                            <div class="arrow"></div>
                            <div class="arrowcover"></div>

                            <div class="commentbody" >
                                <textarea :id="'comment' + receive_record.id" :ref="'comment' + receive_record.id"></textarea>

                                <div class="commentfooter">
                                    <a class="btn small orange btncancel" style="cursor: pointer;" @click="comment_clear(receive_record.id)">Cancel</a>
                                    <a class="btn small green btncreate" @click="comment_create(receive_record.id)">Create</a>
                                </div>
                            </div>
                        </div>

                    </li>
                   </ul>
                   <!-- 一筆Tesk end -->
                 
               </div>
                <!-- list end -->
                <div class="tablebox lv3c">
                    <ul>
                        <li><textarea name="" id="" placeholder="Create a task here" v-model="stage_task"></textarea></li>
                        <li><a class="btn small green" @click="task_create">Create</a></li>
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
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 
<style scoped>

input:focus::-webkit-input-placeholder{
    color: transparent!important;
}
input:focus::-moz-placeholder{
    color: transparent!important;
}
input:focus:-moz-placeholder{
    color: transparent!important;
}

.extendex-top {
  background: none;
  box-shadow: none;
}
.bg-whi {
  min-height: 100vh;
  box-sizing: border-box;
}
.top-box {

  background-size: 100%;
}
.pub-con {
  box-sizing: border-box;
  background-size: 100%;
  text-align: center;
  position: relative;
}
.input-zone {
  width: 5rem;
  background-size: 2.13rem;
  border-radius: 0.38rem;
  border: 0.06rem solid rgba(112, 112, 112, 1);
  position: relative;
  color: var(--fth04);
  font-size: 0.88rem;
  box-sizing: border-box;
}
.input {
  opacity: 0;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  z-index: 2;
}
.pad {
  padding: 0.5rem 1.7rem 0 0rem;
  font-size: 0.88rem;
}
.btn-container {
  margin: 0.69rem auto;
  text-align: center;
}
.btn-container .btn {
  width: 10.56rem;
  height: 2.5rem;
  border-radius: 1.25rem;
  border: none;
  color: #ffffff;
}
.btn-container .btn.btn-gray {
  background: rgba(201, 201, 201, 1);
}
.btn-container .btn.btn-blue {
  background: linear-gradient(
    180deg,
    rgba(128, 137, 229, 1) 0%,
    rgba(87, 84, 196, 1) 100%
  );
  font-size: 1rem;
}
.tips {
  margin-top: 1.69rem;
}
.file-list {
  font-size: 0.88rem;
  color: #5a5cc6;
}
.file-list .file-item {
  margin-top: 0.63rem;
}
.file-list .file-item p {
  line-height: 1.25rem;
  position: relative;
}
.file-list img {
  width: 1.25rem;
  cursor: pointer;
}
.file-list img.upload-delete {
  position: absolute;
  bottom: 0;
  margin: 0 auto;
  margin-left: 1rem;
}
.progress-wrapper {
  position: relative;
  height: 0.5rem;
  border: 0.06rem solid rgba(92, 91, 200, 1);
  border-radius: 1px;
  box-sizing: border-box;
  width: 87%;
}
.progress-wrapper .progress-progress {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 0%;
  border-radius: 1px;
  background-color: #5c5bc8;
  z-index: 1;
}
.progress-rate {
  font-size: 14px;
  height: 100%;
  z-index: 2;
  width: 12%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.progress-rate span {
  display: inline-block;
  width: 100%;
  text-align: right;
}
.progress-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.file-list img.upload-success {
  margin-left: 0;
}
</style>

<style>
        
        .commentbox {
            position: absolute;
            border: 5px solid #00811e;
            background-color: #FFF;
            min-width: 500px;
            padding: 15px 25px 10px 15px;
            left: calc(50% - 575px);
            top: -40px;
            z-index: 1000;
            
            opacity: 0;
            pointer-events: none;
        }

        .diashow {
            
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        
        .arrow {
            border-color: transparent transparent transparent #00811e;
            border-style: solid solid solid solid;
            border-width: 14px 28px;
            right: -56px;
            top: 55px;
            position: absolute;
            width: 0px;
            height: 0px;
            z-index: 1;
        }

        .arrowcover{
            border-color: transparent transparent transparent #fff;
            border-style: solid solid solid solid;
            border-width: 14px 28px;
            position: absolute;
            top: 55px;
            right: -46px;
            width: 0px;
            height: 0px;
            z-index: 1;
        }

        .commentbody {
            border: none;
            padding: 0;
            width: 100%;
        }

        .commentbody textarea {
            border: 1px solid #707070;
            padding: 1px 3px;
            font-size: 14px;
            border-radius: 6px;
            width: 100%;
            height: 70px;
            margin-bottom: 10px;
        }

        .commentfooter {
            padding: 0 0 5px;
            text-align: right;
            width: 100%;
            display: inline-block;
        }

        .btncancel {
            text-align: center;
            margin: 0 10px;
        }
        
        .btncreate {
            text-align: center;
            margin: 0;
            margin-left: 10px;
        }
        
        a.btncancel:hover {
            background-color: #F37058;
            cursor: pointer;
        }

        a.btncreate:hover {
            background-color: #F37058;
            cursor: pointer;
        }
        
  </style>
</html>
