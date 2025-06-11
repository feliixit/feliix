<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if (!isset($jwt)) {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
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

    $access5 = false;
    $access6 = false;

    // decode jwt
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $username = $decoded->data->username;

        $GLOBALS['position'] = $decoded->data->position;
        $GLOBALS['department'] = $decoded->data->department;

        if($decoded->data->limited_access == true)
                header( 'location:index' );

        $database = new Database();
        $db = $database->getConnection();

        $special_agreement = false;

        $query = "SELECT * FROM access_control WHERE special_agreement LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $special_agreement = true;
        }


$position = $decoded->data->position;
$department = $decoded->data->department;

// 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
$test_manager = $decoded->data->test_manager;

// 5. 針對 Reporting Section的內容，只有 Kristel Tan 和Thalassa Wren Benzon 和 Dennis Lin有權限可以進入和看到 幫Mary Jude Jeng Articulo(9) 和 Glendon Wendell Co(41)
if($user_id == 1 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41 || $user_id == 190 || $user_id == 153 || $user_id == 198)
$access5 = true;

// QOUTE AND PAYMENT Management
if(trim(strtoupper($department)) == 'SALES')
{
if(trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
{
$access5 = true;
}
}

if(trim(strtoupper($department)) == 'LIGHTING')
{
if(trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'OFFICE')
{
if(trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'DESIGN')
{
if(trim(strtoupper($position)) == 'BRAND MANAGER')
{
$access6 = true;
}
}

if(trim(strtoupper($department)) == 'ENGINEERING')
{
if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
{
$access5 = true;
}
}

if(trim(strtoupper($department)) == 'ADMIN')
{
if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
{
$access5 = true;
}
}

if(trim(strtoupper($department)) == 'TW')
{
if(trim(strtoupper($position)) == 'SUPPLY CHAIN MANAGER')
{
$access6 = true;
}
}

if($access5 == true)
$access6 = false;


} catch (Exception $e) {

header('location:index');
}

$p = (isset($_GET['p']) ?  $_GET['p'] : 0);
if ($p < 1 || !is_numeric($p)) {
header('location:project01');
}

$is_creator = IsCreator($p, $user_id);

if ($test_manager[1] == "0" && $is_creator == "1")
$test_manager[1] = "1";

//if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
//    header( 'location:index.php' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

header('location:index');
}

?>

<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Project Management</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
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
        $(function () {
            $('header').load('include/header.php');
            //
        <?php
            if ($test_manager[1] == "1") {
                    ?>
                dialogshow($('.list_function a.add'), $('.list_function .dialog.d-add'));
                dialogshow($('.list_function a.edit'), $('.list_function .dialog.d-edit'));
            <?php
            }
                ?>
            dialogshow($('.list_function a.file'), $('.list_function .dialog.d-file'));
        <?php
            if ($test_manager[1] == "1") {
                    ?>
                dialogshow($('.list_function a.fn1'), $('.list_function .dialog.fn1'));
                dialogshow($('.list_function a.fn2'), $('.list_function .dialog.fn2'));
            <?php
            }
                ?>
            dialogshow($('.list_function a.fn3'), $('.list_function .dialog.fn3'));

            // dialogshow($('.list_function a.fn4'), $('.list_function .dialog.fn4'));
            dialogshow($('.list_function a.fn5'), $('.list_function .dialog.fn5'));
            dialogshow($('.list_function a.fn6'), $('.list_function .dialog.fn6'));
            dialogshow($('.list_function a.fn7'), $('.list_function .dialog.fn7'));
            dialogshow($('.list_function a.fn8'), $('.list_function .dialog.fn8'));
            dialogshow($('.list_function a.fn10'), $('.list_function .dialog.fn10'));

            $('header').click(function () {
                dialogclear()
            });
            $('.block.left').click(function () {
                dialogclear()
            });
            $('.block.right').click(function () {
                dialogclear()
            });

            //
            $('#opType').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })

        })
    </script>

    <style>
        .mainContent {
            min-height: 160vh;
        }

        .tablebox.s2.edit > div {
            position: relative;
            height: 35px;
        }

        .tablebox.s2.edit > div div.btnbox {
            position: absolute;
            min-width: 470px;
            top: -5px;
            left: 0;
            right: 0;
            margin: auto;
        }

        .tablebox.lv2b > ul > li > select{
            font-size: 14px;
            font-weight: 500;
            border-color: var(--fth01);
            background-image: url(../images/ui/icon_form_select_arrow_blue.svg);
        }

        .list_function .info_btnbox {
            display: inline-block;
            width: 40px;
            height: 20px;
            margin-bottom: 5px;
            border-radius: 100px;
            background-color: var(--fth01);
        }

        .list_function .info_btnbox .info_btn {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: white;
            border: 3px solid var(--fth01);
        }

        .info_checkbox:checked + .info_btnbox .info_btn {
            margin-left: 20px;
        }

        .project_info_side_button {
            margin-top: 0 !important;
            margin-bottom: 5px !important;
            text-align: right !important;
        }

        .project_info_side_button a {
            display: inline-block !important;
            width: 60px !important;
            background: green !important;
            color: white !important;
            text-align: center !important;
            border-radius: 20px !important;
            line-height: 20px !important;
            height: 20px !important;
            border: none !important;
            font-weight: 700 !important;
        }

        .project_info_items {
            border: 1px solid #707070 !important;
            border-radius: 0 !important;
            width: 99% !important;
        }

        .project_info_items ul {
            border: none !important;
            margin-top: 0 !important;
        }

        .project_info_items li {
            font-size: 12px !important;
            border: none !important;
        }

        .project_info_items li a {
            display: inline-block !important;
            width: 20px !important;
            background: gray !important;
            color: white !important;
            text-align: center !important;
            border-radius: 20px !important;
            line-height: 20px !important;
            height: 20px !important;
            border: none !important;
            font-weight: 700 !important;
        }

        #project_dialog input[type=checkbox] + Label::before {
            color: var(--fth04);
            font-size: 22px;
        }

        div.subtitle {
            font-size: 12px;
            margin-top: 1px;
        }

        div.links {
            max-width: 300px;
            text-align: left;
            padding-left: 5px;
        }

        div.links > a {
            font-size: 12px;
            margin-top: 1px;
            color: rgb(243, 112, 88);
            display: block;
            min-width: 180px;
        }

        div.red {
            color: red;
        }

        div.red a {
            color: red!important;
        }

        .dialog.left {
            left: unset;
            right: 12px;
        }

        .dialog.left::before {
            border-color: transparent var(--fth04) transparent transparent;
            left: unset;
            right: -5px;
        }

        .dialog.left::after {
            border-color: transparent #fff transparent transparent;
            left: unset;
            right: 0;
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
                    <a id="stage_fn1" class="add" :ref="'a_add'"></a>
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
                                    <select v-model="stage_status">
                                        <option value="1">Ongoing</option>
                                        <option value="2">Pending</option>
                                        <option value="3">Close</option>
                                    </select>
                                </dd>
                                <dt>Sub Title:</dt>
                                <dd>
                                    <input type="text" placeholder="" v-model="stage_title">
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
                                    <select id="opType">
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
                                    <select v-model="stage_id_to_edit">
                                        <option v-for="(item, index) in receive_stage_records" :value="item.id">
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
                                    <select v-model="stage_id_to_edit">
                                        <option v-for="(item, index) in receive_stage_records" :value="item.id">
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
                                <li class="head">Sub Title:</li>
                                <li>
                                    <input type="text" placeholder="" v-model="stage_edit_title">
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
                    <div id="file_stage_dialog" class="dialog d-file" :ref="'dlg_file'">
                        <h6>File Folder:</h6>
                        <div class="formbox">
                            <dl>
                                <dt></dt>
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
                                            <td><a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank">{{
                                                item.filename }}</a></td>
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
                <b class="tag focus">{{ special == 's' ? 'SPECIAL PROJECT: X-DEAL' : ( special == 'sn' ? 'SPECIAL PROJECT: NO DP' : 'PROJECT') }}</b>
                <a v-if="project_status == 'Disapproved' " href="project01_disapproved"><b class="tag">{{ project_name
                    }}</b></a>
                <a v-if="project_status != 'Disapproved' " href="project01"><b class="tag">{{ project_name }}</b></a>
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
                                        <option v-for="(item, index) in statuses" :value="item.id"
                                                :key="item.project_status">
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
                                <div class="Info_A">
                                    <dt class="head">Project Name:</dt>
                                    <dd>
                                        <input type="text" v-model="edit_project_name">
                                    </dd>
                                    <dt class="head">Project Group:</dt>
                                    <dd>
                                        <select v-model="edit_group">
                                            <option value="0">Not Belong to Any Group</option>
                                            <option v-for="(item, index) in project_groups" :value="item.id"
                                                    :key="item.project_group">
                                                {{ item.project_group }}
                                            </option>
                                        </select>
                                    </dd>
                                    <dt class="head">Project Category:</dt>
                                    <dd>
                                        <select v-model="edit_category">
                                            <option v-for="(item, index) in categorys" :value="item.id"
                                                    :key="item.category">
                                                {{ item.category }}
                                            </option>
                                        </select>
                                    </dd>
                                    <dt class="head">Project Type:</dt>
                                    <dd style="margin-bottom: 0;">
                                        <select v-model="edit_special">
                                            <option value="">Normal</option>
                                            <option value="s">Special --- X-Deal</option>
                                            <option value="sn">Special --- No DP</option>
                                        </select>
                                    </dd>
                                    <div class="half">
                                        <dt>Client Type:</dt>
                                        <dd>
                                            <select v-model="edit_client_type">
                                                <option v-for="(item, index) in client_types" :value="item.id"
                                                        :key="item.client_type">
                                                    {{ item.client_type }}
                                                </option>
                                            </select>
                                        </dd>
                                    </div>
                                    <div class="half">
                                        <dt>Priority</dt>
                                        <dd>
                                            <select v-model="edit_priority">
                                                <option v-for="(item, index) in priorities" :value="item.id"
                                                        :key="item.priority">
                                                    {{ item.priority }}
                                                </option>
                                            </select>
                                        </dd>
                                    </div>
                                    <dt class="head">Client Name (Firm/Company Name):</dt>
                                    <dd>
                                        <input type="text" v-model="edit_client">
                                    </dd>
                                    <dt class="head">Architect/Designer:</dt>
                                    <dd>
                                        <input type="text" v-model="edit_designer">
                                    </dd>
                                    <dt>Project Creator:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="uid">
                                                <option v-for="(item, index) in users_del" :value="item.id"
                                                        :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <!-- <button @click="change_project_creator">Change</button> -->
                                        </div>
                                    </dd>
                                    <dt>PIC 1:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="uid_pic1">
                                                <option v-for="(item, index) in users_del" :value="item.id"
                                                        :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <!-- <button @click="change_project_creator">Change</button> -->
                                        </div>
                                    </dd>

                                    <dt>PIC 2:</dt>
                                    <dd>
                                        <div class="browser_group">
                                            <select v-model="uid_pic2">
                                                <option v-for="(item, index) in users_del" :value="item.id"
                                                        :key="item.username">
                                                    {{ item.username }}
                                                </option>
                                            </select>
                                            <!-- <button @click="change_project_creator">Change</button> -->
                                        </div>
                                    </dd>

                                    
                                    <dt>Client Target Date:</dt>
                                    <dd>
                                        <input type="date" v-model="edit_target_date">
                                    </dd>

                                    <dt>Actual Delivery/Installation Date:</dt>
                                    <dd>
                                        <input type="date" v-model="edit_real_date">
                                    </dd>


                                    <dt>Type:</dt>
                                    <dd><select v-model="edit_type">
                                        <option value="Major">Major</option>
                                        <option value="Minor">Minor</option>
                                    </select></dd>

                                    <div class="half">
                                        <dt style="margin-top: 3px;">Scope of Works:</dt>
                                        <dd><select v-model="edit_scope">
                                            <option value="Installation">Installation</option>
                                            <option value="Delivery">Delivery</option>
                                            <option value="Other">Other</option>
                                        </select></dd>
                                    </div>
                                    <dd><input type="text" v-model="edit_scope_other" v-if="edit_scope === 'Other'">
                                    </dd>

                                    <dt style="display: none;">
                                        <input type="checkbox" id="send_emai" v-model="edit_send_mail">
                                        <label for="send_emai" style="font-size: 14px; margin-left: -3px;">Send E-mail
                                            to engineering manager when full payment is verified</label>
                                    </dt>

                                    <dt>Reason for Editing Project Info:</dt>
                                    <dd><textarea name="" id="" cols="30" rows="10"
                                                  v-model="edit_edit_reason"></textarea></dd>

                                </div>

                                <div class="Info_B" style="display: none;">

                                    <dt>Contractor:</dt>
                                    <dd><input type="text" v-model="edit_contractor"></dd>

                                    <div class="half">
                                        <dt>3rd Party Contractor:</dt>
                                        <dd>
                                            <select :ref="'party_type'" @change="onChange_party_type()">
                                                <option value="Electrical">Electrical</option>
                                                <option value="Data">Data</option>
                                                <option value="Furniture">Furniture</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </dd>
                                        <dd><input type="text" :ref="'party_name'" placeholder="Name"></dd>
                                    </div>

                                    <div class="half">
                                        <dt></dt>
                                        <dd><input type="text" :ref="'party_type_other'" style="display: none;"></dd>
                                        <!-- 如果選擇Other，則可以在此Input中輸入Contractor特定的類型名稱 -->
                                        <dd><input type="text" :ref="'party_number'" placeholder="Contact Number"></dd>
                                    </div>

                                    <dt class="project_info_side_button">
                                        <a @click="add_party_contactor">Add</a>
                                    </dt>
                                    <dd class="project_info_items">
                                        <ul v-for="(item, index) in edit_party_contactor" :key="index">
                                            <li>
                                                <a @click="remove_party_contactor(index)">x</a>
                                            </li>
                                            <li>{{ item.type}}: {{ item.name }} - {{ item.number }}
                                            </li>
                                        </ul>
                                    </dd>

                                    <div class="half">
                                        <dt>Project Location:</dt>
                                        <dd><input type="text" v-model="edit_location"></dd>
                                    </div>

                                    <div class="half">
                                        <dt>Office Location:</dt>
                                        <dd><input type="text" v-model="edit_office_location"></dd>
                                    </div>

                                    <div class="half">
                                        <dt>Key Person:</dt>
                                        <dd>
                                            <select :ref="'key_type'" @change="onChange_key_type()">
                                                <option value="Purchasing contact">Purchasing contact</option>
                                                <option value="Accounting contact">Accounting contact</option>
                                                <option value="Admin contact">Admin contact</option>
                                                <option value="Person in charge of site">Person in charge of site
                                                </option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </dd>
                                        <dd><input type="text" :ref="'key_name'" placeholder="Name"></dd>
                                    </div>
                                    <div class="half">
                                        <dt></dt>
                                        <dd><input type="text" :ref="'key_type_other'" style="display:none"></dd>
                                        <!-- 如果選擇Other，則可以在此Input中輸入Key Person特定的角色名稱 -->
                                        <dd><input type="text" :ref="'key_number'" placeholder="Contact Number"></dd>
                                    </div>

                                    <dt class="project_info_side_button">
                                        <a @click="add_key_person">Add</a>
                                    </dt>
                                    <dd class="project_info_items">
                                        <ul v-for="(item, index) in edit_key_person" :key="index">
                                            <li>
                                                <a @click="remove_key_person(index)">x</a>
                                            </li>
                                            <li>{{ item.type}}: {{ item.name }} - {{ item.number }}
                                            </li>
                                        </ul>

                                    </dd>

                                    <dt>Background of Client:</dt>
                                    <dd><textarea name="" id="" cols="30" rows="10"
                                                  v-model="edit_background_client"></textarea></dd>

                                    <dt>Brief Background Story Regarding Project:</dt>
                                    <dd><textarea name="" id="" cols="30" rows="10"
                                                  v-model="edit_background_project"></textarea></dd>
                                </div>

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
                                            <input class="input" type="file" name="comm_file" value
                                                   placeholder="choose file" ref="comm_file" v-show="comm_canSub"
                                                   @change="comm_changeFile()" multiple/>
                                        </div>
                                    </div>
                                </dd>

                                <div class="file-list">
                                    <div class="file-item" v-for="(item,index) in comm_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span @click="comm_deleteFile(index)" v-show="item.progress==0"
                                                  class="upload-delete"><i class="fas fa-backspace"></i>
                                                </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
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
                    <a id="status_fn4" class="fn4" :ref="'a_fn4'" @click="open_prob()">Action to Est. Closing Prob.</a>
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
                                <dd><textarea name="" id="" v-model="prob_reason"></textarea></dd>
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
                                    <select v-model="detail_type">
                                        <option value="10">Confirmation - Specific Products (like key spec attribute, price of specific item)</option>
                                        <option value="11">Confirmation - Installation</option>
                                        <option value="12">Confirmation - Warranty</option>
                                        <option value="13">Confirmation - Lead-time</option>
                                        <option value="14">Confirmation - Budget</option>
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
                                            <input class="input" type="file" name="file" value placeholder="choose file"
                                                   ref="file" v-show="canSub" @change="changeFile()" multiple/>
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
                                                        <span @click="deleteFile(index)" v-show="item.progress==0"
                                                              class="upload-delete"><i class="fas fa-backspace"></i>
                                                            </span>
                                                    </p>
                                                    <div class="progress-container" v-show="item.progress!=0">
                                                        <div class="progress-wrapper">
                                                            <div class="progress-progress"
                                                                 :style="'width:'+item.progress*100+'%'"></div>
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
                                                <a class="btn small green" @click="detail_create">Save</a>

                                            </div>
                                        </div>
                                    </div>
                                </dd>


                            </dl>
                        </div>
                    </div>
                </div>

                <div class="popupblock">
                    <a id="a_fn7" class="fn7" :ref="'a_fn7'">New Quotation</a>
                    <div id="dlg_fn7" class="dialog fn7" :ref="'dlg_fn7'">
                        <h6>New Quotation:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Operation Type:</dt>
                                <dd>
                                    <select v-model="quotation_type">
                                        <option value="n" selected>Create New Quotation</option>
                                        <option value="u">Upload New Quotation</option>
                                    </select>
                                </dd>
                            </dl>
                        </div>

                        <!-- 建立報價單 -->
                        <div class="formbox" v-if="quotation_type == 'n'">
                            <dl>
                                <dt class="head">Quotation Name:</dt>
                                <dd><input type="text" v-model="quotation_name"></dd>
                                <dt class="head">Project Name:</dt>
                                <dd>
                                    <select readonly>
                                        <option>{{ project_name }}</option>
                                    </select>
                                </dd>

                                <div class="btnbox">
                                    <a class="btn small" @click="quotation_clear">Cancel</a>
                                    <a class="btn small green" @click="quotation_add">Create</a>
                                </div>
                            </dl>
                        </div>

                        <!-- 上傳報價單 -->
                        <div class="formbox" v-if="quotation_type == 'u'">
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
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
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


                <!--
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
-->
                <div class="popupblock">
                    <a id="status_fn8" class="fn8" :ref="'a_fn8'">Upload Approved Plan</a>
                    <div id="approve_dialog" class="dialog fn8" :ref="'dlg_fn8'">
                        <h6>Upload Approved Plan:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="approve_remark"></textarea></dd>

                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input class="input" type="file" name="approve_file" value
                                                   placeholder="choose file" ref="approve_file" v-show="approve_canSub"
                                                   @change="approve_changeFile()" multiple/>
                                        </div>
                                    </div>
                                </dd>

                                <div class="file-list">
                                    <div class="file-item" v-for="(item,index) in approve_fileArray" :key="index">
                                        <p>
                                            {{item.name}}
                                            <span @click="approve_deleteFile(index)" v-show="item.progress==0"
                                                  class="upload-delete"><i class="fas fa-backspace"></i>
                                                </span>
                                        </p>
                                        <div class="progress-container" v-show="item.progress!=0">
                                            <div class="progress-wrapper">
                                                <div class="progress-progress"
                                                     :style="'width:'+item.progress*100+'%'"></div>
                                            </div>
                                            <div class="progress-rate">
                                                <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                <span v-else><i class="fas fa-check-circle"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btnbox">
                                    <a class="btn small" @click="approve_clear">Cancel</a>
                                    <a class="btn small green" @click="approve_create">Submit</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="popupblock">
                    <a id="status_fn9" class="fn9" @click="apply_for_expense()">Apply Expense</a>
                </div>
<?php if ($special_agreement == true) { ?>
                <div class="popupblock">
                    <a id="status_fn10" class="fn10" :ref="'a_fn10'">Special Agreement</a>
                    <div id="special_dialog" class="dialog left fn10" :ref="'dlg_fn10'">
                        <h6>Special Agreement:</h6>
                        <div class="formbox">
                            <dl>
                                <dt class="head">Description:</dt>
                                <dd><textarea name="" id="" v-model="special_remark"></textarea></dd>

                                <dt></dt>
                                <dd style="display: flex; justify-content: flex_start;">
                                    <span style="color: green; font-size: 14px; font-weight: 500; padding-bottom: 5px; margin-right:10px;">Files: </span>
                                    <div class="pub-con" ref="bg">
                                        <div class="input-zone">
                                            <span class="upload-des">choose file</span>
                                            <input class="input" type="file" name="special_file" value placeholder="choose file"
                                                   ref="special_file" v-show="special_canSub" @change="special_changeFile()" multiple/>
                                        </div>
                                    </div>
                                </dd>
                                <dd>
                                    <div class="browser_group">

                                        <div class="pad">
                                            <div class="file-list">
                                                <div class="file-item" v-for="(item,index) in special_fileArray" :key="index">
                                                    <p>
                                                        {{item.name}}
                                                        <span @click="special_deleteFile(index)" v-show="item.progress==0"
                                                              class="upload-delete"><i class="fas fa-backspace"></i>
                                                            </span>
                                                    </p>
                                                    <div class="progress-container" v-show="item.progress!=0">
                                                        <div class="progress-wrapper">
                                                            <div class="progress-progress"
                                                                 :style="'width:'+item.progress*100+'%'"></div>
                                                        </div>
                                                        <div class="progress-rate">
                                                            <span v-if="item.progress!=1">{{(item.progress*100).toFixed(0)}}%</span>
                                                            <span v-else><i class="fas fa-check-circle"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btnbox">
                                                <a class="btn small" @click="special_clear">Cancel</a>
                                                <a class="btn small green" @click="special_create">Save</a>

                                            </div>
                                        </div>
                                    </div>
                                </dd>


                            </dl>
                        </div>
                    </div>
                </div>
<?php } ?>
            </div>
        </div>
        <div class="block left">

            <div class="list_function">
                <label style="font-size: 14px">
                    <input type="checkbox" class="info_checkbox" name id onchange="change_Project_Info(this)">
                    <span class="info_btnbox">
                            <span class="info_btn"></span>
                        </span>
                </label>
            </div>

            <div class="Info_A">

                <div class="tablebox lv2a b-4">
                    <ul class="head">
                        <li>Category</li>
                        <li>Client Type</li>
                        <li>Client Name</li>
                        <li>Architect / Designer</li>
                    </ul>
                    <ul>
                        <li>{{ category }}</li>
                        <li>{{ client_type }}</li>
                        <li>{{ client }}</li>
                        <li>{{ designer }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-3">
                    <ul class="head">
                        <li>Priority</li>
                        <li>Type</li>
                        <li>Scope of Works</li>
                    </ul>
                    <ul>
                        <li>{{ priority }}</li>
                        <li>{{ type }}</li>
                        <li>{{ (scope == 'Other' ? 'Other : ' + scope_other : scope) }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-3">
                    <ul class="head">
                        <li>Project Status</li>
                        <li>Current Stage</li>
                        <li>Project Creator</li>
                    </ul>
                    <ul>
                        <li>{{ project_status }}</li>
                        <li>{{ stage }}</li>
                        <li>{{ username }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-3">
                    <ul class="head">
                        <li>PIC 1</li>
                        <li>PIC 2</li>
                        <li>Execution Period</li>
                    </ul>
                    <ul>
                        <li>{{ pic1 }}</li>
                        <li>{{ pic2 }}</li>
                        <li>{{ created_at }} ~ {{ end_at }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-2">
                    <ul class="head">
                        <li style="text-align: center !important;">Client Target Date</li>
                        <li style="text-align: center !important;">Actual Delivery/Installation Date</li>
                    </ul>
                    <ul>
                        <li style="text-align: center !important;">{{ target_date }}</li>
                        <li style="text-align: center !important;">{{ real_date }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-2">
                    <ul class="head">
                        <li style="text-align: center !important;">Comments</li>
                        <li style="text-align: center !important;">Estimated Closing Probability</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_comments'>• {{ receive_record.comment }} <br
                                    v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch">{{item.filename}}</a>
                                    </span>
                                <br>({{ receive_record.username }} at {{ receive_record.created_at }})
                            </div>
                        </li>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_probs'>• {{ receive_record.prob }} - {{
                                receive_record.comment }} <br>

                                ({{ receive_record.username }} at {{ receive_record.created_at }})
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a" v-if="title != 'technician'">
                    <ul class="head">
                        <li style="text-align: center !important;">Related Projects</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_relative'>
                                <a :href="'project02?p=' + receive_record.id" target="_blank" class="attch">• {{
                                    receive_record.project_name }} </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
if ($access5 == true) {
    ?>
                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Expense Records</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(exp, index) in expense_record'>
                                • Request No.:<a :href="'expense_application_report?id=' + exp.id" target="_blank"
                                                 class="attch">{{ exp.request_no }}</a>and Amount:<template v-if="exp.status == 9"> {{ exp.request_type
                                == 1 ? Number(exp.amount_verified).toLocaleString() :
                                Number(exp.amount_applied).toLocaleString() }}</template><br/>({{exp.username}} at {{
                                exp.created_at }})
                            </div>

                            <!-- 如果這個專案沒有任何的費用記錄，則下面這行<div>也不用顯示。如果這個專案有費用記錄，則下面這行<div>才需要顯示 -->
                            <div style="color: rgb(243, 112, 88);" v-if="price_record_total >= 0">Total Expense Amount in this Project is {{ price_record_total.toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</div>
                        </li>

                    </ul>
                </div>
                <?php
}
?>

                <?php
if ($access6 == true) {
    ?>
                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Expense Records</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(exp, index) in expense_record'>
                                • Request No.:<a @click="export_petty(exp.id)" class="attch">{{ exp.request_no }}</a>and
                                Amount: {{ exp.request_type == 1 ? Number(exp.amount_verified).toLocaleString() :
                                Number(exp.amount_applied).toLocaleString() }}<br/>({{exp.username}} at {{
                                exp.created_at }})
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
}
?>
                <div class="tablebox lv2a" v-if="title != 'technician'">
                    <ul class="head">
                        <li style="text-align: center !important;">Quotation Files</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_quotes'
                                 v-bind:style="[receive_record.final_quotation == 1 ? { 'color':'#F37058', '': ''} : { }]">
                                • {{ receive_record.comment }} <br v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items" v-if="receive_record.type == 'f'">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch">{{item.filename}}</a>
                                    </span>

                                <span v-if="receive_record.type == 'p' && receive_record.pageless == '' ">
                                        <a :href="'quotation?id=' + receive_record.id" target="_blank" class="attch">https://feliix.myvnc.com/quotation?id={{receive_record.id}}</a>
                                    </span>
                                <span v-if="receive_record.type == 'p' && receive_record.pageless == 'Y' ">
                                        <a :href="'quotation_pageless?id=' + receive_record.id" target="_blank" class="attch">https://feliix.myvnc.com/quotation_pageless?id={{receive_record.id}}</a>
                                    </span>
                                <br>({{ receive_record.username }} at {{ receive_record.created_at }})
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a" v-if="title != 'technician'">
                    <ul class="head">
                        <li style="text-align: center !important;">Orders</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_orders'>
                                • {{ receive_record.order_type == 'taiwan' ? 'Order – Close Deal' : receive_record.order_type == 'mockup' ? 'Order – Mockup' : receive_record.order_type == 'sample' ? 'Order – Sample' : receive_record.order_type == 'stock' ? 'Order – Stock' : '' }} <br>
                                <span>
                                        <a v-if="receive_record.order_type == 'taiwan'" :href="'order_taiwan_p4?id=' + receive_record.id" target="_blank" class="attch">{{ receive_record.serial_name + ' ' + receive_record.od_name }}</a>
                                        <a v-if="receive_record.order_type == 'mockup'" :href="'order_taiwan_mockup_p4?id=' + receive_record.id" target="_blank" class="attch">{{ receive_record.serial_name + ' ' + receive_record.od_name }}</a>
                                        <a v-if="receive_record.order_type == 'sample'" :href="'order_taiwan_sample_p4?id=' + receive_record.id" target="_blank" class="attch">{{ receive_record.serial_name + ' ' + receive_record.od_name }}</a>
                                        <a v-if="receive_record.order_type == 'stock'" :href="'order_taiwan_stock_p4?id=' + receive_record.id" target="_blank" class="attch">{{ receive_record.serial_name + ' ' + receive_record.od_name }}</a>
                                </span>
                                <br>({{ receive_record.username  }} at {{ receive_record.created_at  }})
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Approved Plan</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_approves'>• {{ receive_record.comment }} <br
                                    v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch">{{item.filename}}</a>
                                    </span>
                                <br>({{ receive_record.username }} at {{ receive_record.created_at }})
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a" v-if="title != 'technician'">
                    <ul class="head">
                        <li style="text-align: center !important;">Transmittal</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in transmittal' :class="receive_record.followup == 'Y' ? 'red' : ''">
                                <a :href="'transmittal?id=' + receive_record.id">• {{ receive_record.comment }}</a> <br v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch" style="color: var(--fth05);">{{item.filename}}</a>
                                    </span>
                                <br>({{ receive_record.username }} at {{ receive_record.created_at }})
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
                            <div v-for='(receive_record, index) in project_action_detials'>• {{
                                receive_record.detail_type
                                }} : {{ receive_record.detail_desc }} <br v-if="receive_record.items.length > 0">
                                <span v-for="item in receive_record.items">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch">{{item.filename}}</a>
                                    </span>
                                <br>
                                ({{ receive_record.username }} at {{ receive_record.created_at }})
                                <br>
                            </div>
                        </li>
                    </ul>
                </div>
<?php if ($special_agreement == true) { ?>
                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Special Agreement</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for='(receive_record, index) in project_specials'>
                                <div style="white-space: break-spaces;">• {{ receive_record.comment }}</div>
                                <span v-for="item in receive_record.items">
                                        <a :href="baseURL + item.bucket + '\\' + item.gcp_name" target="_blank"
                                           class="attch">{{item.filename}}</a>
                                    </span>
                                <br>
                                ({{ receive_record.username }} at {{ receive_record.created_at }})
                                <br>
                            </div>
                        </li>
                    </ul>
                </div>
<?php } ?>
            </div>

            <div class="Info_B" style="display: none;">

                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Contractor</li>
                    </ul>
                    <ul>
                        <li style="text-align: center !important;">
                            {{ contractor }}
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">3rd Party Contractor</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for="(item, index) in party_contactor" :key="index">• {{ item.type }}: {{ item.name
                                }} - {{ item.number }}<br>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="tablebox lv2a b-2">
                    <ul class="head">
                        <li style="text-align: center !important;">Project Location</li>
                        <li style="text-align: center !important;">Office Location</li>
                    </ul>
                    <ul>
                        <li style="text-align: center !important;">{{ location }}</li>
                        <li style="text-align: center !important;">{{ office_location }}</li>
                    </ul>
                </div>

                <div class="tablebox lv2a">
                    <ul class="head">
                        <li style="text-align: center !important;">Key Person</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <div v-for="(item, index) in key_person" :key="index">• {{ item.type }}: {{ item.name }} -
                                {{ item.number }}<br>
                            </div>
                        </li>
                    </ul>

                    <ul class="head">
                        <li style="text-align: center !important;">Background of Client</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <pre>{{ background_client }}</pre>
                        </li>
                    </ul>

                    <ul class="head">
                        <li style="text-align: center !important;">Brief Background Story Regarding Project</li>
                    </ul>
                    <ul>
                        <li class="morespace">
                            <pre>{{ background_project }}</pre>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
        <div class="block right">
            <div class="list_function">
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>

                    <a class="page" v-for="pg in pages" @click="page=pg"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

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
                    <!-- <li>Created by</li> -->
                    <!-- <li>Post/Reply</li> -->
                    <li>Inquiry / Order / Schedule</li>
                    <li>Recent Message</li>
                </ul>
                <ul v-for='(receive_record, index) in displayedStagePosts'>
                    <li>{{ receive_record.sequence }}</li>
                    <li v-if="receive_record.project_stage_id == 1 && receive_record.id <= 2570">
                        <a v-bind:href="'project03_client?sid='+ receive_record.id">{{ receive_record.stage }}</a>
                        <div class="subtitle">{{receive_record.title}}</div>
                        <!-- <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'" target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                        </div> -->
                    </li>
                    <li v-if="receive_record.project_stage_id == 1 && receive_record.id > 2570"><a
                            v-bind:href="'project03_client_v2?sid='+ receive_record.id">{{ receive_record.stage }}</a>
                            <div class="subtitle">{{receive_record.title}}</div>
                            <!-- <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                        </div> -->
                    </li>

                    <li v-if="receive_record.project_stage_id == 3 && receive_record.id > 1810"><a
                            v-bind:href="'project03_ameeting?sid='+ receive_record.id">{{ receive_record.stage }}</a>
                            <div class="subtitle">{{receive_record.title}}</div>
                            <!-- <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                        </div> -->
                    </li>
                    <li v-if="receive_record.project_stage_id == 3 && receive_record.id <= 1810"><a
                            v-bind:href="'project03_other?sid='+ receive_record.id">{{ receive_record.stage }}</a>
                            <div class="subtitle">{{receive_record.title}}</div>
                            <!-- <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                        </div> -->
                    </li>
                    <li v-if="receive_record.project_stage_id != 1 && receive_record.project_stage_id != 3"><a
                            v-bind:href="'project03_other?sid='+ receive_record.id">{{ receive_record.stage }}</a>
                            <div class="subtitle">{{receive_record.title}}</div>
                            <!-- <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank"> {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                        </div> -->
                    </li>

                <?php
                    if ($test_manager[1] == "1") {
                ?>
                    <li><select v-model="receive_record.stages_status_id" @change="update_status(receive_record)">
                            <option value="1">Ongoing</option>
                            <option value="2">Pending</option>
                            <option value="3">Close</option>
                        </select>
                    </li>
                <?php
                    } else {    
                ?>
                    <li>{{ receive_record.stages_status }}</li>
                <?php
                }
                ?>

                    
                    <li>{{ receive_record.start }} ~</li>
                    <li>
                        <div class="links">
                            <template v-for="(od, idx) in receive_record.order" >
                                <a :href="'order_taiwan_p4?id=' + od.id" v-if="od.order_type == 'taiwan'"  target="_blank">• {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_mockup_p4?id=' + od.id"  v-if="od.order_type == 'mockup'"  target="_blank">• {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_sample_p4?id=' + od.id"  v-if="od.order_type == 'sample'"  target="_blank">• {{ od.serial_name }} {{ od.od_name }} </a>
                                <a :href="'order_taiwan_stock_p4?id=' + od.id"  v-if="od.order_type == 'stock'"  target="_blank">• {{ od.serial_name }} {{ od.od_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.inquiry" >
                                <a :href="'inquiry_taiwan?id=' + od.id"  target="_blank">• {{ od.serial_name }} {{ od.iq_name }} </a>
                            </template>
                            <template v-for="(od, idx) in receive_record.schedule" >
                                <a :href="'schedule_calendar?id=' + od.id"  target="_blank">• Schedule -  {{ od.title }} on {{ od.start_time }}</a>
                            </template>
                        </div>
                    </li>
                    <!-- <li>{{ receive_record.created_at }} {{ receive_record.username }}</li> -->
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
<script src="js/a076d05399.js"></script>
<script>
    function change_Project_Info(obj_checkbox) {
        if (obj_checkbox.checked) {
            document.getElementsByClassName("Info_A")[0].style.display = "none";
            document.getElementsByClassName("Info_A")[1].style.display = "none";
            document.getElementsByClassName("Info_B")[0].style.display = "";
            document.getElementsByClassName("Info_B")[1].style.display = "";
        } else {
            document.getElementsByClassName("Info_A")[0].style.display = "";
            document.getElementsByClassName("Info_A")[1].style.display = "";
            document.getElementsByClassName("Info_B")[0].style.display = "none";
            document.getElementsByClassName("Info_B")[1].style.display = "none";
        }
    }
</script>

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

    .dialog.d-file {
        min-width: 600px;
    }

    .file_div {
        overflow-y: auto;
        max-height: 450px;
    }

    .file_table {
        border: 1px solid black;
    }

    .file_table th,
    .file_table td {
        border: 1px solid black;
        text-align: center;
        color: black;
        font-size: 13px;
        padding: 5px;
        width: 25%;
    }

    div.block.left a.attch {
        color: var(--fth05);
        transition: .3s;
        margin: 0 15px 0 0;
        font-size: 13px;
    }

    div.block.left a.attch:hover {
        color: var(--fth01);
    }

    li.morespace > div + div {
        margin-top: 10px;
    }
</style>

</html>