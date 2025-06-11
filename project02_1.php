<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/config/database.php';
include_once 'api/project02_is_creator.php';


use \Firebase\JWT\JWT;


try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            $GLOBALS['position'] = $decoded->data->position;
            $GLOBALS['department'] = $decoded->data->department;
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

        }
        catch (Exception $e){

            header( 'location:index' );
        }

        $p = (isset($_GET['p']) ?  $_GET['p'] : 0);
        if (  $p < 1 || !is_numeric($p)) {
          header( 'location:project01' );
        }
        
        $is_creator = IsCreator($p, $user_id);
        
        if($test_manager[1] == "0" && $is_creator == "1")
            $test_manager[1] = "1";

        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        //    header( 'location:index.php' );
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        header( 'location:index' );
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
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<script>
$(function(){
    $('header').load('include/header.php');
    //
<?php 
  if ($test_manager[1]  == "1")
  {
?>
    dialogshow($('.list_function a.add'),$('.list_function .dialog.d-add'));
    dialogshow($('.list_function a.edit'),$('.list_function .dialog.d-edit'));
<?php
  }
?>
    dialogshow($('.list_function a.file'),$('.list_function .dialog.d-file'));
<?php 
  if ($test_manager[1]  == "1")
  {
?>
    dialogshow($('.list_function a.fn1'),$('.list_function .dialog.fn1'));
    dialogshow($('.list_function a.fn2'),$('.list_function .dialog.fn2'));
<?php
  }
?>
    dialogshow($('.list_function a.fn3'),$('.list_function .dialog.fn3'));
    dialogshow($('.list_function a.fn4'),$('.list_function .dialog.fn4'));
    dialogshow($('.list_function a.fn5'),$('.list_function .dialog.fn5'));
    dialogshow($('.list_function a.fn6'),$('.list_function .dialog.fn6'));
    dialogshow($('.list_function a.fn7'),$('.list_function .dialog.fn7'));
    
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

<style>
    .mainContent {    
        min-height: 110vh;
    }

    .tablebox.s2.edit>div{
        position: relative;
        height: 35px;
    }

    .tablebox.s2.edit>div div.btnbox{
        position: absolute;
        min-width: 470px;
        top: -5px;
        left: 0;
        right: 0;
        margin: auto;
    }
</style>

</head>

<body class="fourth">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="list_function main">
            <div class="block">
                <!-- add -->
                <div class="popupblock">
                    <a id="stage_fn1" class="add" :ref="'a_add'" ></a>
                    <div id="stage_dialog" class="dialog d-add" :ref="'dlg_add'">
                        <h6>Add New Stage:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Sequence:</dt>
                                <dd><input type="text" placeholder="" v-model="stage_sequence"></dd>
                                <dt>Stage:</dt>
                                <dd>
                                    <select v-model="project_stage">
                                      <option v-for="(item, index) in stages" :value="item.id" :key="item.stage">
                                          {{ item.stage }}
                                      </option>
                                    </select>
                                </dd>
                                <dt>Stage Status:</dt>
                                <dd>
                                    <select  v-model="stage_status">
                                        <option value="1">Ongoing</option>
                                        <option value="2">Pending</option>
                                        <option value="3">Close</option>
                                    </select>
                                </dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="stage_clear">Cancel</a>
                                <a class="btn small green" @click="stage_add">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- edit -->
                <div class="popupblock">
                    <a id="edit_stage_fn1" class="edit" :ref="'a_edit'"></a>
                    <div id="edit_stage_dialog" class="dialog d-edit edit" :ref="'dlg_edit'">
                        <h6>Edit/Delete Stage:</h6>
                        <div class="tablebox s1">
                            <ul>
                                <li class="head">Operation Type:</li>
                                <li>
                                    <select  id="opType">
                                        <option value="edit">Edit Existing Stage</option>
                                        <option value="del">Delete Existing Stage</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 del">
                            <ul>
                                <li class="head">Target Sequence:</li>
                                <li class="mix">
                                    <select  v-model="stage_id_to_edit">
                                        <option v-for="(item, index) in receive_stage_records" :value="item.id" >
                                          {{ item.sequence }}
                                      </option>
                                    </select>
                                    <a class="btn small" @click="stage_delete">Delete</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tablebox s2 edit">
                            <ul>
                                <li class="head">Target Sequence:</li>
                                <li class="mix">
                                    <select  v-model="stage_id_to_edit">
                                        <option v-for="(item, index) in receive_stage_records" :value="item.id" >
                                          {{ item.sequence }}
                                      </option>
                                    </select>
                                    <a class="btn small green" @click="stage_load">Load</a>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Sequence:</li>
                                <li><input type="text" placeholder="" v-model="record.sequence"></li>
                            </ul>
                            <ul>
                                <li class="head">Stage:</li>
                                <li>
                                    <select v-model="record.project_stage_id">
                                      <option v-for="(item, index) in stages" :value="item.id" :key="item.stage">
                                          {{ item.stage }}
                                      </option>
                                    </select>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Stage Status:</li>
                                <li>
                                    <select v-model="record.stages_status_id">
                                      <option value="1">Ongoing</option>
                                        <option value="2">Pending</option>
                                        <option value="3">Close</option>
                                    </select>
                                </li>
                            </ul>
                            <ul>
                                <li class="head">Reason for Editing:</li>
                                <li><textarea placeholder="" v-model="stage_edit_reason"></textarea></li>
                            </ul>
                            <div>
                                <div class="btnbox">
                                    <a class="btn small" @click="edit_stage_clear">Cancel</a>
                                    <a class="btn small green" @click="save_edit_stage">Save</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="popupblock"><a id="f_stage_fn1" class="file" :ref="'a_file'"></a>
                    <div id="file_stage_dialog" class="dialog d-file" :ref="'dlg_file'"><h6>File Folder:</h6>
                        <div class="formbox">
                            <dl>
                                <dt> </dt>
                                <dd><input type="text" v-model="keyword" placeholder="Search for..."></dd>

                                <div class="file_div">
                                    <table id="showFile" class="file_table">
                                        <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Uploaded by</th>
                                            <th>Time</th>
                                            <th>Webpage</th>
                                        </tr>
                                        </thead>

                                        <tbody id="contact">
                                        <tr v-for="(item, index) in file_management">
                                            <td><a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank">{{ item.filename }}</a></td>
                                            <td>{{ item.messager }}</td>
                                            <td>{{ item.message_date }} {{ item.message_time }}</td>
                                            <td><a :href="pageURL + item.url">{{ item.stage}}</a></td>
                                        </tr>


                                        </tbody>
                                    </table>

                                </div>

                            <div class="btnbox"><a class="btn small" @click="file_dialog_clear()">Close</a>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- tag -->
                <b class="tag focus">PROJECT</b>
                <b class="tag">{{ projectname }}</b>
                <b class="tag" v-if="verified_downpayment">(Verified Downpayment)</b>
            </div>
            <div class="block fn">
                <div class="popupblock">
                    <a id="status_fn1" class="fn1" :ref="'a_fn1'">Change Project Status</a>
                    <div id="status_dialog" class="dialog fn1" :ref="'dlg_fn1'">
                        <h6>Change Project Status:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Current Status:</dt>
                                <dd><input type="text" v-model="project_status" readonly="true"></dd>
                                <dt>Change to:</dt>
                                <dd>
                                    <select v-model="project_status_edit">
                                      <option v-for="(item, index) in statuses" :value="item.id" :key="item.project_status">
                                          {{ item.project_status }}
                                      </option>
                                    </select>
                                </dd>
                                <dt>Reason:</dt>
                                <dd><textarea name="" id="" v-model="project_status_reason"></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="status_clear">Cancel</a>
                                    <a class="btn small green" @click="status_create">Create</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a id="project_fn2" class="fn2" :ref="'a_fn2'">Edit Project Info</a>
                    <div id="project_dialog" class="dialog fn2" :ref="'dlg_fn2'">
                        <h6>Edit Project Info:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Project Category:</dt>
                                <dd>
                                    <select v-model="edit_category">
                                      <option v-for="(item, index) in categorys" :value="item.id" :key="item.category">
                                          {{ item.category }}
                                      </option>
                                    </select>
                                </dd>
                                <div class="half">
                                    <dt>Client Type:</dt>
                                    <dd>
                                        <select v-model="edit_client_type">
                                          <option v-for="(item, index) in client_types" :value="item.id" :key="item.client_type">
                                              {{ item.client_type }}
                                          </option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Priority</dt>
                                    <dd>
                                        <select v-model="edit_priority">
                                          <option v-for="(item, index) in priorities" :value="item.id" :key="item.priority">
                                              {{ item.priority }}
                                          </option>
                                        </select>
                                    </dd>
                                </div>
                                <dt>Project Creator:</dt>
                                <dd>
                                    <div class="browser_group">
                                        <select v-model="uid">
                                          <option v-for="(item, index) in users" :value="item.id" :key="item.username">
                                              {{ item.username }}
                                          </option>
                                        </select>
                                       <!-- <button @click="change_project_creator">Change</button> --></div>
                                </dd>

                                <div class="half">
                                    <dt>Project Location:</dt>
                                    <dd><input type="text" v-model="edit_location"></dd>
                                </div>

                                <div class="half">
                                    <dt>Contact Person:</dt>
                                    <dd><input type="text" v-model="edit_contactor"></dd>
                                </div>

                                <div class="half">
                                    <dt>Contact Number:</dt>
                                    <dd><input type="text" v-model="edit_contact_number"></dd>
                                </div>

                                <div class="half">
                                    <dt>Client:</dt>
                                    <dd><input type="text" v-model="edit_client"></dd>
                                </div>

                                <dt>Reason for Editing Project Info:</dt>
                                <dd><textarea name="" id="" cols="30" rows="10" v-model="edit_project_reason"></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="project_clear">Cancel</a>
                                    <a class="btn small green" @click="project_create">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a id="project_fn3" class="fn3" :ref="'a_fn3'">Action to Comments</a>
                    <div id="comment_dialog" class="dialog fn3" :ref="'dlg_fn3'">
                        <h6>Action to Comments:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Comment:</dt>
                                <dd><textarea name="" id="" v-model="comment"></textarea></dd>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                          <span class="upload-des">choose file</span>
                                          <input
                                            class="input"
                                            type="file"
                                            name="comm_file"
                                            value
                                            placeholder="choose file"
                                            ref="comm_file"
                                            v-show="comm_canSub"
                                            @change="comm_changeFile()"
                                            multiple
                                          />
                                    </div>
                                  </div>
                                </dd>

                                <div class="file-list">
                                  <div class="file-item" v-for="(item,index) in comm_fileArray" :key="index">
                                    <p>
                                      {{item.name}}
                                      <span
                                        @click="comm_deleteFile(index)"
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

                                <div class="btnbox">
                                    <a class="btn small" @click="comment_clear">Cancel</a>
                                    <a class="btn small green" @click="comment_create">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="popupblock">
                    <a id="status_fn4" class="fn4" :ref="'a_fn4'">Action to Est. Closing Prob.</a>
                    <div id="prob_dialog" class="dialog fn4" :ref="'dlg_fn4'">
                        <h6>Action to Est. Closing Prob.:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Estimated Closing Probability:</dt>
                                <dd>
                                    <select v-model="probability">
                                      <option value="0">0</option>
                                      <option value="10">10</option>
                                      <option value="20">20</option>
                                      <option value="30">30</option>
                                      <option value="40">40</option>
                                      <option value="50">50</option>
                                      <option value="60">60</option>
                                      <option value="70">70</option>
                                      <option value="80">80</option>
                                      <option value="90">90</option>
                                      <option value="100">100</option>
                                    </select>
                                </dd>
                                <dt class="head">Reason for Estimated Closing Probability:</dt>
                                <dd><textarea name="" id="" v-model="prob_reason" ></textarea></dd>
                                <div class="btnbox">
                                    <a class="btn small" @click="prob_clear">Cancel</a>
                                    <a class="btn small green" @click="prob_create">Save</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="popupblock">
                    <a id="status_fn5" class="fn5" :ref="'a_fn5'">Action to Project Details</a>
                    <div id="detail_dialog" class="dialog fn5" :ref="'dlg_fn5'">
                        <h6>Action to Project Details:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Detail Type:</dt>
                                <dd>
                                    <select  v-model="detail_type">
                                        <option value="4">Client Details</option>
                                        <option value="5">Competitors</option>
                                        <option value="3">Discount</option>
                                        <option value="6">Lead Time</option>
                                        <option value="9">Quotation Deadline</option>
                                        <option value="1">Requirements</option>
                                        <option value="2">Submittals</option>
                                        <option value="7">Warranty</option>
                                        <option value="8">Other</option>
                                    </select>
                                </dd>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="detail_desc"></textarea></dd>
                               
                                <dt></dt> 
                                    <dd style="display: flex; justify-content: flex_start;">
                                        <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                              <span class="upload-des">choose file</span>
                                              <input
                                                class="input"
                                                type="file"
                                                name="file"
                                                value
                                                placeholder="choose file"
                                                ref="file"
                                                v-show="canSub"
                                                @change="changeFile()"
                                                multiple
                                              />
                                        </div>
                                      </div>
                                    </dd>
                                <dd>
                                    <div class="browser_group">

                                        <div class="pad">
                                            <div class="file-list">
                                              <div class="file-item" v-for="(item,index) in fileArray" :key="index">
                                                <p>
                                                  {{item.name}}
                                                  <span
                                                    @click="deleteFile(index)"
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
                                                <div class="btnbox">
                                                    <a class="btn small" @click="detail_clear">Cancel</a>
                                                    <a class="btn small green"  @click="detail_create">Save</a>
                                                  
                                                </div>
                                          </div>
                                    </div>
                                </dd>

                                
                            </dl>
                        </div>
                    </div>
                </div>


                <div class="popupblock">
                    <a id="a_fn7" class="fn7" :ref="'a_fn7'">Upload Quotation</a>
                    <div id="dlg_fn7" class="dialog fn7" :ref="'dlg_fn7'">
                        <h6>Upload Quotation:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="quote_remark"></textarea></dd>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                          <span class="upload-des">choose file</span>
                                          <input
                                            class="input"
                                            type="file"
                                            name="quote_file"
                                            value
                                            placeholder="choose file"
                                            ref="quote_file"
                                            v-show="quote_canSub"
                                            @change="quote_changeFile()"
                                            multiple
                                          />
                                    </div>
                                  </div>
                                </dd>

                                <div class="file-list">
                                  <div class="file-item" v-for="(item,index) in quote_fileArray" :key="index">
                                    <p>
                                      {{item.name}}
                                      <span
                                        @click="quote_deleteFile(index)"
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

                                <div class="btnbox">
                                    <a class="btn small" @click="quote_clear">Cancel</a>
                                    <a class="btn small green" @click="quote_create">Upload</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                
                <div class="popupblock">
                    <a id="status_fn6" class="fn6" :ref="'a_fn6'">Submit Downpayment Proof</a>
                    <div id="prof_dialog" class="dialog fn6" :ref="'dlg_fn6'">
                        <h6>Submit Downpayment Proof:</h6>
                        <div class="formbox">
                            <dl>
                               
                                
                                <dt class="head">Remarks:</dt>
                                <dd><textarea name="" id="" v-model="prof_remark"></textarea></dd>

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
                                    <p>
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

                                <div class="btnbox">
                                    <a class="btn small" @click="prof_clear">Cancel</a>
                                    <a class="btn small green" @click="prof_create">Submit</a>
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
                    <li>{{ category }}</li>
                    <li>{{ client_type }}</li>
                    <li>{{ priority }}</li>
                    <li>{{ username }}</li>
                </ul>
            </div>
            <div class="tablebox lv2a b-3">
                <ul class="head">
                    <li>Project Status</li>
                    <li>Current Stage</li>
                    <li>Project Execution Period</li>
                </ul>
                <ul>
                    <li>{{ project_status }}</li>
                    <li>{{ stage }}</li>
                    <li>{{ created_at }} ~  {{ end_at }}</li>
                </ul>
            </div>
            <div class="tablebox lv2a b-4">
                <ul class="head">
                    <li>Project Location</li>
                    <li>Contact Person</li>
                    <li>Contact Number</li>
                    <li>Client</li>
                </ul>
                <ul>
                    <li>{{ location }}</li>
                    <li>{{ contactor }}</li>
                    <li>{{ contact_number }}</li>
                    <li>{{ client }}</li>
                </ul>
            </div>
            <div class="tablebox lv2a">
                <ul class="head">
                    <li style="text-align: center !important;">Quotation Files</li>
                </ul>
                <ul>
                    <li class="morespace">
                        <div v-for='(receive_record, index) in project_quotes'>• {{ receive_record.comment }} <br v-if="receive_record.items.length > 0">
                            <span v-for="item in receive_record.items">
                                <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                            </span>
                            <br>({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                </ul>
            </div>
            <div class="tablebox lv2a b-2">
                <ul class="head">
                    <li style="text-align: center !important;">Comments</li>
                    <li style="text-align: center !important;">Estimated Closing Probability</li>
                </ul>
                <ul>
                    <li class="morespace">
                        <div v-for='(receive_record, index) in project_comments'>• {{ receive_record.comment }} <br v-if="receive_record.items.length > 0">
                        <span v-for="item in receive_record.items">
                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                        </span>
                         <br>({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                    <li class="morespace">
                        <div v-for='(receive_record, index) in project_probs'>• {{ receive_record.prob }} - {{ receive_record.comment }} <br>

                         ({{ receive_record.username }} at {{ receive_record.created_at }})
                        </div>
                    </li>
                </ul>
            </div>
            <div class="tablebox lv2a">
                <ul class="head">
                    <li style="text-align: center !important;">Project Details</li>
                </ul>
                <ul>
                    <li class="morespace">
                        <div v-for='(receive_record, index) in project_action_detials'>• {{ receive_record.detail_type }} : {{ receive_record.detail_desc }}  <br v-if="receive_record.items.length > 0">
                        <span v-for="item in receive_record.items">
                            <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                        </span>
                        <br>
                         ({{ receive_record.username }} at {{ receive_record.created_at }})
                         <br>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="block right">
            <div class="list_function">
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>
                  
                    <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>
                  
                    <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
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
                   <!-- <li>Post/Reply</li> -->
                   <li>Recent Message</li>
               </ul>
               <ul v-for='(receive_record, index) in displayedStagePosts'>
                   <li>{{ receive_record.sequence }}</li>
                   <li v-if="receive_record.project_stage_id == 1"><a v-bind:href="'project03_client?sid='+ receive_record.id">{{ receive_record.stage }}</a></li>
                   <li v-if="receive_record.project_stage_id != 1"><a v-bind:href="'project03_other?sid='+ receive_record.id">{{ receive_record.stage }}</a></li>
                   <li>{{ receive_record.stages_status }}</li>
                   <li>{{ receive_record.start }} ~  </li>
                   <li>{{ receive_record.created_at }} {{ receive_record.username }}</li>
                   <!-- <li>{{ receive_record.replies }}/{{ receive_record.post }}</li> -->
                   <li v-if="receive_record.recent != ''">{{ receive_record.recent }}</li>
                   <li v-else>{{ receive_record.created_at }} {{ receive_record.username }}</li>
               </ul>
           </div>
           <!-- list end -->
        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/project02.js"></script>
<script defer src="js/a076d05399.js"></script> 
<style scoped>
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

.list_function.main a.file {
        width: 30px; 
        height: 30px; 
        background-image: url(images/ui/btn_file_blue.svg);
        background-position: center center;
        background-size: cover;
    }

    .list_function.main a.file:hover,
    .list_function.main a.file.focus {
        background-image: url(images/ui/btn_file_green.svg);
        background-position: center center;
        background-size: cover;
    }

    .dialog.d-file{
        min-width: 600px;
    }

    .file_div {
        overflow-y: auto;
        max-height: 450px;
    }

    .file_table {
        border: 1px solid black;
    }

    .file_table th, .file_table td {
        border: 1px solid black;
        text-align: center;
        color: black;
        font-size: 13px;
        padding: 5px;
        width: 25%;
    }

    div.block.left a.attch{
        color: var(--fth05);
        transition: .3s;
        margin: 0 15px 0 0;    
        font-size: 13px;
    }

    div.block.left a.attch:hover{
        color: var(--fth01);
    }

    li.morespace>div + div{
    margin-top: 10px;
    }

</style>
</html>
