<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

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
    <link rel="stylesheet" type="text/css" href="js/fancyBox/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/fancyBox/jquery.fancybox.min.js"></script>

    <script>
        $(function() {
            $('header').load('include/header.php');
            //
            dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
            dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
            dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
            dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
            dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
            dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
            dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function() {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function() {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>

</head>

<body class="fourth other">

    <div class="bodybox">
        <!-- header -->
        <header class="dialogclear">header</header>
        <!-- header end -->
        <div id='app' class="mainContent">
            <!-- mainContent為動態內容包覆的內容區塊 -->
            <div class="list_function main">
                <div class="block">
                    <!-- add red -->
                    <div class="popupblock">
                        <a id="dialog_a1" class="add red"></a>
                        <!-- dialog -->
                        <div id="add_a1" class="dialog r-add add">
                            <h6>Add/Duplicate Task</h6>
                            <div class="tablebox s1">
                                <ul>
                                    <li class="head">Operation Type:</li>
                                    <li>
                                        <select name="" id="opType3">
                                            <option value="add">Add New Task</option>
                                            <option value="dup">Duplicate Existing Task</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 add">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" v-model="title"></dd>
                                </dl>
                                <dl>
                                    <dt>Priority:</dt>
                                    <dd>
                                        <select v-model="priority">
                                            <option value="1">No Priority</option>
                                            <option value="2">Low</option>
                                            <option value="3">Normal</option>
                                            <option value="4">High</option>
                                            <option value="5">Urgent</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="assignee" id="assignee">
                                                <option v-for="(item, index) in users" :value="item.id" :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <button @click="OpenAssignee">Browse</button><button class="selectbox">Browse</button>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Collaborator:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select id="collaborator" v-model="collaborator">
                                                <option v-for="(item, index) in users" :value="item.id" :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <button @click="OpenCollaborator">Browse</button>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Due Date:</dt>
                                    <dd>
                                        <div class="browser_group"><input type="date" v-model="due_date"></div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Task Detail:</dt>
                                    <dd><textarea placeholder="" v-model="detail"></textarea></dd>
                                </dl>
                                <dl>
                                    <dd style="display: flex; justify-content: flex_start;">
                                        <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" name="file" value placeholder="choose file" ref="file" v-show="canSub" @change="changeFile()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="browser_group">

                                            <div class="pad">
                                                <div class="file-list">
                                                    <div class="file-item" v-for="(item,index) in fileArray" :key="index">
                                                        <p>
                                                            {{item.name}}
                                                            <span @click="deleteFile(index)" v-show="item.progress==0" class="upload-delete"><i class="fas fa-backspace"></i>
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
                                    </dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="task_clear">Cancel</a>
                                    <a class="btn small green">Calendar</a>
                                    <a class="btn small green" @click="task_create">Create</a>
                                </div>
                            </div>
                            <div class="tablebox s2 dup">
                                <ul>
                                <li class="head">Target Task:</li>
                                    <li class="mix">
                                        <select  v-model="task_id_to_dup">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id" >
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_dup">Duplicate</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- edit red -->
                    <div class="popupblock">
                        <a id="edit_red" class="edit red"></a>
                        <!-- dialog -->
                        <div id="dialog_red_edit" class="dialog r-edit edit">
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
                                    <li class="head">Target Task:</li>
                                    <li class="mix">
                                        <select  v-model="task_id_to_del">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small" @click="task_del">Delete</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 edit">
                                <ul>
                                    <li class="head">Target Sequence:</li>
                                    <li class="mix">
                                        <select  v-model="task_id_to_load">
                                            <option v-for="(it, index) in project03_other_task" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_load">Load</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 edit">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" v-model="record.title"></dd>
                                </dl>
                                <dl>
                                    <dt>Priority:</dt>
                                    <dd>
                                        <select name="" id="" v-model="record.priority_id">
                                            <option value="1">No Priority</option>
                                            <option value="2">Low</option>
                                            <option value="3">Normal</option>
                                            <option value="4">High</option>
                                            <option value="5">Urgent</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Status:</dt>
                                    <dd>
                                    <select name="" id="" v-model="record.task_status">
                                            <option value="0">Ongoing</option>
                                            <option value="1">Pending</option>
                                            <option value="2">Close</option>
                                        </select>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="record.assignee_id" multiple v-if="record.assignee_id">
                                                <option v-for="(it, index) in users" :value="it.id" :key="it.username">
                                                    {{ it.username }}
                                                </option>
                                            </select>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Collaborator:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="record.collaborator_id" multiple v-if="record.collaborator_id">
                                                <option v-for="(it, index) in users" :value="it.id" :key="it.username">
                                                    {{ it.username }}
                                                </option>
                                            </select>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Due Date:</dt>
                                    <dd>
                                        <div class="browser_group"><input type="date" v-model="record.due_date"></div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="record.detail"></textarea></dd>
                                </dl>

                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="filebox">
                                            <a class="attch" v-for="(it,index) in editfileArray" :key="index" @click="deleteEditFile(index)">{{it.name}}</a>
                                        </div>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" :ref="'editfile'" placeholder="choose file" @change="changeEditFile()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="task_edit_clear">Cancel</a>
                                    <a class="btn small green">Calendar</a>
                                    <a class="btn small green" @click="task_edit_create">Save</a>
                                </div>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- add -->
                    <div class="popupblock">
                        <a id="dialog_a1_r" class="add blue"></a>
                        <!-- dialog -->
                        <div id="add_a1_r" class="dialog d-add">
                            <h6>Add Message:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" placeholder="" v-model="title_r"></dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="assignee_r" id="assignee_r">
                                                <option v-for="(item, index) in users" :value="item.id" :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <button @click="OpenAssignee_r">Browse</button>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="detail_r"></textarea></dd>
                                </dl>
                                <dl>
                                    <dd style="display: flex; justify-content: flex_start;">
                                        <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" name="file_r" value placeholder="choose file" ref="file_r" v-show="canSub_r" @change="changeFile_r()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="browser_group">

                                            <div class="pad">
                                                <div class="file-list">
                                                    <div class="file-item" v-for="(item,index) in fileArray_r" :key="index">
                                                        <p>
                                                            {{item.name}}
                                                            <span @click="deleteFile_r(index)" v-show="item.progress==0" class="upload-delete"><i class="fas fa-backspace"></i>
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
                                    </dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="task_clear_r">Cancel</a>
                                    <a class="btn small green" @click="task_create_r">Create</a>
                                </div>
                            </div>
                        </div>
                        <!-- dialog end -->
                    </div>
                    <!-- edit -->
                    <div class="popupblock">
                        <a id="edit_blue" class="edit blue"></a>
                        <!-- dialog -->
                        <div id="dialog_blue_edit" class="dialog d-edit edit">
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
                                        <select  v-model="task_id_to_del_r">
                                            <option v-for="(it, index) in project03_other_task_r" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small" @click="task_del_r">Delete</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tablebox s2 edit">
                                <ul>
                                    <li class="head">Target Message:</li>
                                    <li class="mix">
                                        <select  v-model="task_id_to_load_r">
                                            <option v-for="(it, index) in project03_other_task_r" :value="it.task_id" v-if="it.task_status != '-1'">
                                                {{ it.title }}
                                            </option>
                                        </select>
                                        <a class="btn small green" @click="task_load_r">Load</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="formbox s2 edit">
                                <dl>
                                    <dt>Title:</dt>
                                    <dd><input type="text" placeholder="" v-model="record_r.title"></dd>
                                </dl>
                                <dl>
                                    <dt>Assignee:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="record_r.assignee_id" multiple v-if="record_r.assignee_id">
                                                <option v-for="(it, index) in users" :value="it.id" :key="it.username">
                                                    {{ it.username }}
                                                </option>
                                            </select>
                                        </div>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Description:</dt>
                                    <dd><textarea placeholder="" v-model="record_r.detail"></textarea></dd>
                                </dl>
                                <dl>
                                    <dt>Files:</dt>
                                    <dd>
                                        <div class="filebox">
                                            <a class="attch" v-for="(it,index) in editfileArray_r" :key="index" @click="deleteEditFile_r(index)">{{it.name}}</a>
                                        </div>
                                        <div class="pub-con" ref="bg">
                                            <div class="input-zone">
                                                <span class="upload-des">choose file</span>
                                                <input class="input" type="file" :ref="'editfile_r'" placeholder="choose file" @change="changeEditFile_r()" multiple />
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                
                                <div class="btnbox">
                                    <a class="btn small" @click="task_edit_clear_r">Cancel</a>
                                    <a class="btn small green" @click="task_edit_create_r">Save</a>
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
                     
                        <select name="" id="" v-model="fil_priority">
                            <option value="">Priority</option>
                            <option value="1">No Priority</option>
                            <option value="2">Low</option>
                            <option value="3">Normal</option>
                            <option value="4">High</option>
                            <option value="5">Urgent</option>
                        </select>
                        <select name="" id="" v-model="fil_status">
                            <option value="">Status</option>
                            <option value="0">Ongoing</option>
                            <option value="1">Pending</option>
                            <option value="2">Close</option>
                         
                        </select>
                        <select v-model="fil_due_date">
                            <option v-for="(it, index) in opt_due_date" :value="it.due_date" :key="it.due_date">
                                {{ it.due_date }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-for='(receive_record, index) in project03_other_task' v-if="receive_record.task_status != '-1'">

                    <div class="teskbox dialogclear">
                        <a class="btn small red">{{ receive_record.priority }}</a>
                        <a class="btn small yellow" v-if="receive_record.task_status == '0'">Ongoing</a>
                        <a class="btn small yellow" v-if="receive_record.task_status == '1'">Pending</a>
                        <a class="btn small green" v-if="receive_record.task_status == '2'">Close</a>
                        <b>[Task] {{ receive_record.title }}</b>
                        <a class="btn small blue right">Arrange Meeting</a>
                    </div>
                    <div class="teskbox dialogclear" style="margin-top:-2px !important">
                        <div class="tablebox m01">
                            <ul>
                                <li><b>Creator</b></li>
                                <li><a class="man" :style="'background-image: url(images/man/' +  receive_record.creator_pic  + ');'"></a></li>
                            </ul>
                            <ul>
                                <li><b>Assignee</b></li>
                                <li>
                                    <i v-for="item in receive_record.assignee">
                                        <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'"></a>
                                    </i>

                                </li>
                            </ul>
                            <ul>
                                <li><b>Collaborator</b></li>
                                <li>
                                    <i v-for="item in receive_record.collaborator">
                                        <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'"></a>
                                    </i>

                                </li>
                            </ul>
                            <ul>
                                <li><b>Due Date</b></li>
                                <li>{{ receive_record.due_date }}</li>
                            </ul>
                            <ul>
                                <li><b>Description</b></li>
                                <li>{{ receive_record.detail }}</li>
                            </ul>
                            <ul>
                                <li><b>Attachments</b></li>
                                <li>
                                    <i v-for="item in receive_record.items">
                                        <a class="attch" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                    </i>
                                </li>
                                
                            </ul>
                        </div>
                    </div>

                    <div class="teskbox scroll">
                        <div class="tableframe">
                            <div class="tablebox m02">
                                <!-- 1 message -->
                                <ul v-for="item in receive_record.message" :class="{ deleted : item.message_status == -1, dialogclear : item.message_status == -1 }" >
                                    <li class="dialogclear">
                                        <a class="man" :style="'background-image: url(images/man/' + item.messager_pic + ');'"></a>
                                        <i class="info">
                                            <b>{{item.messager}}</b><br>
                                            {{ item.message_time }}<br>
                                            {{ item.message_date }}
                                            
                                        </i>
                                    </li>
                                    <li v-if="item.message_status == 0">
                                        <div class="msg">
                                            <div class="msgbox dialogclear">
                                                <p v-if="item.ref_id != 0"><a href="" class="tag_name">@{{ item.ref_name}}</a> {{ item.ref_msg}}</p>
                                                <p>{{ item.message }}</p>
                                                <i v-for="file in item.items">
                                                    <a class="attch" :href="baseURL + file.gcp_name" target="_blank">{{file.filename}}</a>
                                                </i>
                                                
                                            </div>
                                            <div class="btnbox">
                                                <a class="btn small green reply r1" :id="'task_reply_btn_' + item.message_id + '_' + item.ref_id" @click="openTaskMsgDlg(item.message_id + '_' + item.ref_id)">Reply</a>
                                                <!-- dialog -->
                                                <div class="dialog reply r1" :id="'task_reply_dlg_' + item.message_id + '_' + item.ref_id">
                                                    <div class="formbox">
                                                        <dl>
                                                            <dd><textarea name="" :ref="'task_reply_msg_' + item.message_id + '_' + item.ref_id" :id="'task_reply_msg_' + item.message_id + '_' + item.ref_id"></textarea></dd>
                                                            
                                                            <dd>
                                                                <div class="pub-con" ref="bg">
                                                                    <div class="input-zone">
                                                                        <span class="upload-des">choose file</span>
                                                                        <input class="input" type="file" :ref="'file_msg_' + item.message_id + '_' + item.ref_id" placeholder="choose file" @change="changeMsgFile(item.message_id + '_' + item.ref_id)" multiple />
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                            <dd>
                                                                <div class="filebox">
                                                                        <a class="attch" v-for="(it,index) in msgItems(item.message_id + '_' + item.ref_id)" :key="index" @click="deleteMsgFile(item.message_id + '_' + item.ref_id, index)">{{it.name}}</a>
                                                                </div>
                                                            </dd>
                                                            <dd>
                                                                <div class="btnbox">
                                                                    <a class="btn small orange" @click="msg_clear(item.message_id + '_' + item.ref_id)">Cancel</a>
                                                                    <a class="btn small green" @click="msg_create(item.message_id + '_' + item.ref_id, item.message_id)">Save</a>
                                                                </div>
                                                            </dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                                <!-- dialog end -->
                                                <a class="btn small yellow" @click="msg_delete(item.message_id, item.ref_id)">Delete</a>
                                            </div>

                                            <div class="msgbox dialogclear" v-for="reply in item.reply">
                                                <p><a href="" class="tag_name">@{{ item.messager}}</a> {{ reply.reply}}</p>
                                                <i v-for="file in reply.items">
                                                    <a class="attch" :href="baseURL + reply.gcp_name" target="_blank">{{reply.filename}}</a>
                                                </i>

                                            </div>
                                        </div>
                                    </li>

                                    <li v-if="item.message_status == -1">
                                        <div class="msg">
                                            <div class="msgbox">
                                                <p>Deleted by <a href="" class="tag_name">@{{ item.updator }}</a> at {{ item.update_date }}</p>
                                            </div>
                                        </div>
                                    </li>

                                </ul>

                                
                            </div>
                        </div>
                        <div class="tablebox lv3c m03 dialogclear">
                            <ul>
                                <li>
                                    <textarea name="" id="" placeholder="Write your comment here" :ref="'comment_task_' + receive_record.task_id"></textarea>
                                    <div class="filebox">
                                        <a class="attch" v-for="(item,index) in taskItems(receive_record.task_id)" :key="index" @click="deleteTaskFile(receive_record.task_id, index)">{{item.name}}</a>

                                    </div>
                                </li>
                                <li>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input class="input" type="file" :ref="'file_task_' + receive_record.task_id" placeholder="choose file" @change="changeTaskFile(receive_record.task_id)" multiple />
                                        </div>
                                        <a class="btn small green" @click="comment_create(receive_record.task_id)">Comment</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>




            </div>


            <div class="block right ">
                <div class="list_function dialogclear">
                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>
                      
                        <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>
                      
                        <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
                    </div>
                </div>
                <div class="teskbox" v-for='(receive_record, index) in displayedStagePosts' :class="{ red : receive_record.task_status == -1, dialogclear : receive_record.task_status == -1 }">
                    <h5>[MESSAGE] {{ receive_record.title }}</h5>
                    <div class="tablebox2">
                        <ul>
                            <li class="teskblock dialogclear">
                                <div class="tablebox m01">
                                    <ul>
                                        <li><b>Creator</b></li>
                                        <li><a class="man" :style="'background-image: url(images/man/' +  receive_record.creator_pic  + ');'"></a></li>
                                    </ul>
                                    <ul>
                                        <li><b>Date</b></li>
                                        <li>{{ receive_record.task_date }}</li>
                                    </ul>
                                    <ul>
                                        <li><b>Assignee</b></li>
                                        <li>
                                            <i v-for="item in receive_record.assignee">
                                                <a class="man" :style="'background-image: url(images/man/' + item.pic_url + ');'"></a>
                                            </i>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li><b>Description</b></li>
                                        <li>
                                            {{ receive_record.detail }}
                                        </li>
                                    </ul>
                                    <ul>
                                        <li><b>Attachments</b></li>
                                        <li>
                                            <i v-for="item in receive_record.items">
                                                <a class="attch" :href="baseURL + item.gcp_name" target="_blank">{{item.filename}}</a>
                                            </i>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="teskblock">
                                <div class="tableframe">
                                    <div class="tablebox m02">
                                        <!-- 1 message -->
                                        <ul v-for="item in receive_record.message" :class="{ deleted : item.message_status == -1, dialogclear : item.message_status == -1 }">
                                            <li class="dialogclear">
                                                <a class="man" :style="'background-image: url(images/man/' + item.messager_pic + ');'"></a>
                                                <i class="info">
                                                    <b>{{item.messager}}</b><br>
                                                    {{ item.message_time }}<br>
                                                    {{ item.message_date }}
                                                </i>
                                            </li>
                                            <li v-if="item.message_status == 0">
                                                <div class="msg">
                                                    <div class="msgbox dialogclear">
                                                        <p v-if="item.ref_id != 0"><a href="" class="tag_name">@{{ item.ref_name}}</a> {{ item.ref_msg}}</p>
                                                        <p>{{ item.message }}</p>
                                                        <i v-for="file in item.items">
                                                            <a class="attch" :href="baseURL + file.gcp_name" target="_blank">{{file.filename}}</a>
                                                        </i>
                                                    </div>
                                                    <div class="btnbox">
                                                        <a class="btn small green reply r3" :id="'task_reply_btn_r_' + item.message_id + '_' + item.ref_id" @click="openTaskMsgDlg_r(item.message_id + '_' + item.ref_id)">Reply</a>
                                                        <!-- dialog -->
                                                        <div class="dialog reply r3" :id="'task_reply_dlg_r_' + item.message_id + '_' + item.ref_id">
                                                            <div class="formbox">
                                                                <dl>
                                                                    <dd><textarea name="" :ref="'task_reply_msg_r_' + item.message_id + '_' + item.ref_id" :id="'task_reply_msg_r_' + item.message_id + '_' + item.ref_id"></textarea></dd>
                                                                    
                                                                    <dd>
                                                                        <div class="pub-con" ref="bg">
                                                                            <div class="input-zone">
                                                                                <span class="upload-des">choose file</span>
                                                                                <input class="input" type="file" :ref="'file_msg_r_' + item.message_id + '_' + item.ref_id" placeholder="choose file" @change="changeMsgFile_r(item.message_id + '_' + item.ref_id)" multiple />
                                                                            </div>
                                                                        </div>
                                                                    </dd>
                                                                    <dd>
                                                                        <div class="filebox">
                                                                            <a class="attch" v-for="(it,index) in msgItems_r(item.message_id + '_' + item.ref_id)" :key="index" @click="deleteMsgFile_r(item.message_id + '_' + item.ref_id, index)">{{it.name}}</a>
                                                                        </div>
                                                                    </dd>
                                                                    <dd>
                                                                        <div class="btnbox">
                                                                            <a class="btn small orange" @click="msg_clear_r(item.message_id + '_' + item.ref_id)">Cancel</a>
                                                                            <a class="btn small green" @click="msg_create_r(item.message_id + '_' + item.ref_id, item.message_id)">Save</a>
                                                                        </div>
                                                                    </dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                        <!-- dialog end -->
                                                        <a class="btn small yellow" @click="msg_delete_r(item.message_id, item.ref_id)">Delete</a>
                                                    </div>

                                                    <div class="msgbox dialogclear" v-for="reply in item.reply">
                                                        <p><a href="" class="tag_name">@{{ item.messager}}</a> {{ reply.reply}}</p>
                                                        <i v-for="file in reply.items">
                                                            <a class="attch" :href="baseURL + reply.gcp_name" target="_blank">{{reply.filename}}</a>
                                                        </i>

                                                    </div>
                                                    
                                                </div>
                                            </li>

                                            <li v-if="item.message_status == -1">
                                                <div class="msg">
                                                    <div class="msgbox">
                                                        <p>Deleted by <a href="" class="tag_name">@{{ item.updator }}</a> at {{ item.update_date }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                     

                                    </div>
                                </div>
                                <div class="tablebox lv3c m03 dialogclear">
                                    <ul>
                                        <li>
                                            <textarea name="" id="" placeholder="Write your comment here" :ref="'comment_task_r_' + receive_record.task_id"></textarea>
                                            <div class="filebox">
                                                <a class="attch" v-for="(item,index) in taskItems_r(receive_record.task_id)" :key="index" @click="deleteTaskFile_r(receive_record.task_id, index)">{{item.name}}</a>

                                            </div>
                                        </li>
                                        <li>
                                            <div class="pub-con" ref="bg">
                                                <div class="input-zone">
                                                    <span class="upload-des">choose file</span>
                                                    <input class="input" type="file" :ref="'file_task_r_' + receive_record.task_id" placeholder="choose file" @change="changeTaskFile_r(receive_record.task_id)" multiple />
                                                </div>
                                                <a class="btn small green" @click="comment_create_r(receive_record.task_id)">Comment</a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
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
<script type="text/javascript" src="js/project03_other.js" defer></script>
<script defer src="https://kit.fontawesome.com/a076d05399.js"></script>
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
        background: linear-gradient(180deg,
                rgba(128, 137, 229, 1) 0%,
                rgba(87, 84, 196, 1) 100%);
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

</html>