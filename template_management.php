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


use \Firebase\JWT\JWT;

$test_manager = "0";

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if($decoded->exp < time())
            {
                header( 'location:index' );
            }

            if($decoded->data->limited_access == true)
                header( 'location:index' );
            
            $user_id = $decoded->data->id;

            $position = $decoded->data->position;
            $department = $decoded->data->department;
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

            $access6 = false;

            // QOUTE AND PAYMENT Management
            if(trim(strtoupper($department)) == 'SALES')
            {
                if(trim(strtoupper($position)) == 'ASSISTANT CUSTOMER VALUE DIRECTOR'
                || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'LIGHTING')
            {
                if(trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }

                if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR')
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
                if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ENGINEERING')
            {
                if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ADMIN')
            {
                if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
                {
                    $access6 = true;
                }
            }


            if(trim($department) == '')
            {
                if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER')
            {
                $access6 = true;
            }

            if($access6 == false)
                header( 'location:index' );
        }
        catch (Exception $e){

            header( 'location:index' );
        }


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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Template Management</title>
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

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })

        function ToggleModal(target) {
            $(".mask").toggle();

            if (target == 1) {
                $("#Modal_1").toggle();
            }else if (target == 2) {
                $("#Modal_2").toggle();
            }else if (target == 3) {
                $("#Modal_3").toggle();
            }
        }
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">

        /* -------------------------- */
        /* body.green Style (Yellow) */
        /* -------------------------- */
        body.green .mainContent > .block,
        body.green .mainContent > .block h6,
        body.green .mainContent > .block .tablebox,
        body.green .mainContent > .block .tablebox > ul > li,
        body.green .mainContent > .block .tablebox2,
        body.green .mainContent > .block .formbox,
        body.green .mainContent > .block .formbox dd,
        body.green .mainContent > .tags a {
            border-color: #2F9A57;
        }

        body.green .mainContent > .block h6 {
            color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox > ul.head > li,
        body.green .mainContent > .tags a {
            background-color: #E5F7EB;
        }

        body.green .mainContent > .tags a.focus {
            background-color: #2F9A57;
        }

        body.green .mainContent > .block .tablebox {
            border-top: 2px solid #2F9A57;
            border-left: 2px solid #2F9A57;
            width: 100%;
        }

        body.green .mainContent > .block .tablebox > ul > li {
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #2F9A57;
            border-right: 2px solid #2F9A57;
            font-weight: 500;
            font-size: 16px;
            vertical-align: middle;
        }

        body.green .mainContent > .block .tablebox ul.head,
        body.green .mainContent > .block .formbox li.head {
            background-color: #2F9A57;
            font-weight: 800;
        }

        body.green .mainContent > .block .tablebox ul.head li {
            font-weight: 800;
        }

        body.green input.alone[type=radio]::before,
        body.green input.alone[type=checkbox]::before,
        body.green input[type=checkbox] + Label::before,
        body.green input[type=radio] + Label::before {
            color: #2F9A57;
        }

        body.green input[type=range],
        body.green input[type=text],
        body.green input[type=password],
        body.green input[type=file],
        body.green input[type=number],
        body.green input[type=url],
        body.green input[type=email],
        body.green input[type=tel],
        body.green input[list],
        body.green input[type=button],
        body.green input[type=submit],
        body.green button,
        body.green textarea,
        body.green select,
        body.green output {
            border-color: #2F9A57;
        }

        body.green select {
            background-image: url(images/ui/icon_form_select_arrow_black.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
        }

        .block.A .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
        }

        .block .tablebox li > a {
            text-decoration: none;
            color: #2F9A57;
            cursor: pointer;
            margin: 3px 6px 3px 0;
        }

        .list_function {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list_function::after {
            display: none;
        }

        .list_function .front {
            display: flex;
            align-items: center;
        }

        .list_function .front a.create {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_add_green.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 35px;
            height: 35px;
            line-height: 35px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            margin-right: 25px;
        }

        .list_function .searching input {
            font-size: 15px;
            padding: 4px 7px;
        }

        .list_function .searching i {
            color: #2F9A57;
            font-size: 22px;
        }

        .list_function .pagenation {
            float: none;
        }

        .list_function .pagenation a {
            color: #2F9A57;
            border-color: #2F9A57;
        }

        .list_function .pagenation a:hover {
            background-color: #2F9A57;
            color: #FFF;
        }


        body input.alone.green[type=radio]::before {
            font-size: 25px;
            color: #2F9A57;
        }

        .bodybox .mask {
            position: absolute;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }


        .modal {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        .modal .modal-content {
            width: 90%;
            height: calc(100vh - 40px);
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: 850px;
            overflow-y: auto;
        }

        .modal .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px 15px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .modal-content .modal-header h6 {
            color: #2F9A57;
            border-bottom: none;
            padding: 0;
        }

        .modal .modal-content .modal-header a {
            color: #2F9A57;
            font-size: 20px;
        }

        .modal .modal-content .box-content {
            padding: 20px 25px 25px;
            border-bottom: 2px solid #2F9A57;
        }

         .modal .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

         .modal .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=text] {
            border: 1px solid black;
            width: 100%;
            padding: 8px 35px 8px 15px;
        }

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table th, .list_table td{
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tr td:first-of-type {
            font-weight: 600;
            width: 25%;
            background-color: #E5F7EB;
        }

        .list_table tr td:nth-of-type(2) {
            text-align: left;
        }

        .list_table tr td:nth-of-type(3) {
            width: 110px;
            line-height: 2;
        }

        .list_table td i {
            font-size: 25px;
        }

        .list_table td i:nth-of-type(odd) {
            margin-right: 5px;
        }

    </style>




</head>

<body class="green">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag C" href="performance_dashboard">Performance Dashboard</a>
            <a class="tag A" href="performance_review">Performance Review</a>
            <a class="tag D" href="template_library">Template Library</a>
            <a class="tag B focus">Template Management</a>

        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Template Management of Performance Review</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create" href="javascript: void(0)" onclick="ToggleModal(1)"></a>

                            <div class="searching">
                                <input type="text" placeholder="Searching Keyword Here" v-model="keyword">
                                <button style="border: none;" @click="search()"><i class="fas fa-search-plus"></i></button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"  @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]" >{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Position</li>
                        <li>Version</li>
                        <li>Created Time</li>
                        <li>Updated Time</li>
                        <li>Times Cited</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                        <input type="radio" name="record_id" class="alone green" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.title }} ({{ record.department }})</li>
                        <li>{{ record.version }}</li>
                        <li>{{ record.created_at }}</li>
                        <li>{{ record.updated_at }}</li>
                        <li>{{ record.cited }}</li>
                    </ul>
                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">

                    <a class="btn green" @click="view_detai()">Detail</a>
                    <a class="btn green" @click="duplicate()">Duplicate</a>
                    <a class="btn green" @click="edit_detai()">Edit</a>
                    <a class="btn" @click="remove()">Delete</a>
                </div>


                <div id="Modal_1" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Create Template</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(1)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content" :ref="'addto'">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li>
                                    <select v-model="department">
                                        <option v-for="(item, index) in position" :value="item.did" :key="item.department">{{ item.department }}</option>
                                    </select>

                                    <select style="margin-top: 5px;" v-model="title_id">
                                        <option v-for="(item, index) in title" :value="item.tid" :key="item.title">{{ item.title }}</option>
                                    </select>
                                </li>

                                <li><b>Version Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="version"></li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content">

                            <ul>
                                <li><b>Category</b></li>
                                <li>
                                    <select v-model="type">
                                        <option value="1">PART I: SELF-IMPROVEMENT SKILLS</option>
                                        <option value="2">PART II: BASIC SKILLS</option>
                                        <option value="3">PART III: BONUS</option>
                                    </select>
                                </li>

                                <li><b>Sub Category</b></li>
                                <li><input type="text" required style="width:100%" v-model="category"></li>

                                <li><b>Criterion</b></li>
                                <li><input type="text" required style="width:100%" v-model="criterion"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green" @click="add_criterion" v-if="!editing">Add Criterion</a>
                                <a class="btn" v-if="editing" @click="cancel_criterion" >Cancel</a>
                                <a class="btn green" v-if="editing" @click="update_criterion">Update Criterion</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in agenda'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="set_up(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="set_down(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="edit(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="del(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in agenda1'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="set_up1(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="set_down1(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="edit1(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="del1(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART III: BONUS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in agenda2'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="set_up2(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="set_down2(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="edit2(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="del2(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>
                        
                        <!-- Button to save template -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green" :disabled="submit == true" @click="create_template()">Create Template</a>
                            </div>
                        </div>

                    </div>

                </div>



                <div id="Modal_2" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Template Detail</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(2)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li class="content">{{ record.title }} ({{ record.department }})</li>

                                <li><b>Version Name</b></li>
                                <li class="content">{{ record.version }}</li>

                                <li><b>Created Time</b></li>
                                <li class="content">{{ record.created_name }} at {{ record.created_at }}</li>

                                <li><b>Updated Time</b></li>
                                <li class="content">{{ ( record.updated_name == null ) ? "": record.updated_name + " at " + record.updated_at }}</li>

                                <li><b>Times Cited</b></li>
                                <li class="content">{{ record.cited }}</li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for='(item, index) in record.agenda' :key="index">
                                    <td>
                                        {{ item.category }}
                                    </td>
                                    <td>
                                        {{ item.criterion }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(item, index) in record.agenda1' :key="index">
                                    <td>
                                        {{ item.category }}
                                    </td>
                                    <td>
                                    {{ item.criterion }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="2">PART III: BONUS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(item, index) in record.agenda2' :key="index">
                                    <td>
                                        {{ item.category }}
                                    </td>
                                    <td>
                                    {{ item.criterion }}
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                <div id="Modal_3" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Edit Template</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(3)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content" :ref="'porto'">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li>
                                    <select v-model="e_department">
                                        <option v-for="(item, index) in position" :value="item.did" :key="item.department">{{ item.department }}</option>
                                    </select>

                                    <select style="margin-top: 5px;" v-model="e_tid">
                                        <option v-for="(item, index) in e_title" :value="item.tid" :key="item.title">{{ item.title }}</option>
                                    </select>
                                </li>

                                <li><b>Version Name</b></li>
                                <li><input type="text" required style="width:100%" v-model="record.version"></li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content">

                            <ul>
                                <li><b>Category</b></li>
                                <li>
                                    <select v-model="e_type">
                                        <option value="1">PART I: SELF-IMPROVEMENT SKILLS</option>
                                        <option value="2">PART II: BASIC SKILLS</option>
                                        <option value="3">PART III: BONUS</option>
                                    </select>
                                </li>

                                <li><b>Sub Category</b></li>
                                <li><input type="text" required style="width:100%" v-model="e_category"></li>

                                <li><b>Criterion</b></li>
                                <li><input type="text" required style="width:100%" v-model="e_criterion"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green" @click="e_add_criterion" v-if="!e_editing">Add Criterion</a>
                                <a class="btn" v-if="e_editing" @click="e_cancel_criterion" >Cancel</a>
                                <a class="btn green" v-if="e_editing" @click="e_update_criterion">Update Criterion</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in record.agenda'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="e_set_up(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="e_set_down(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="e_edit(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="e_del(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in record.agenda1'> 
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                    <i class="fas fa-arrow-alt-circle-up" @click="e_set_up1(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="e_set_down1(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="e_edit1(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="e_del1(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART III: BONUS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in record.agenda2'> 
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.criterion }}
                                    </td>
                                    <td>
                                    <i class="fas fa-arrow-alt-circle-up" @click="e_set_up2(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="e_set_down2(index, record.id)"></i>
                                        <i class="fas fa-edit" @click="e_edit2(record.id)"></i>
                                        <i class="fas fa-trash-alt" @click="e_del2(record.id)"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>


                        <!-- Button to save template -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green" @click="update_template()">Update Template</a>
                            </div>
                        </div>

                    </div>

                </div>


            </div>
        </div>
    </div>
</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/template_management.js"></script>
</html>