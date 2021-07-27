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
    <title>FELIIX template</title>
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
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
    </script>

    <style>


        .region {
            margin: 50px 10px 10px;
            padding: 20px 15px 15px;
            border: 2px solid #E2E2E2;
            border-radius: 10px;
            position: relative;
        }

        .region span.heading {
            display: inline-block;
            position: absolute;
            top: -17px;
            background-color: white;
            padding: 0 10px;
            font-size: 20px;
            font-weight: 500;
        }

        ul {
            margin: 0;
            border-bottom: 1px solid #E2E2E2;
            background-color: #F7F7F7;
            display: flex;
            align-items: center;
        }

        ul.variation_list {
            align-items: flex-start;
            border-bottom: none;
            background-color: #F0F0F0;
            margin-bottom: 20px;
        }

        ul.variation_list h6 {
            text-align: center;
        }

        ul.variation_list li:first-of-type {
            min-width: 170px;
        }

        ul.variation_list select {
            margin-bottom: 10px;
        }

        ul li {
            display: table-cell;
            text-decoration: none;
            padding: 10px;
        }

        ul li:first-of-type {
            width: 20vw;
            min-width: 150px;
            text-align: center;
            flex-grow: 0;
            flex-shrink: 0;
        }

        ul li:nth-of-type(2) {
            flex-grow: 1;
            flex-shrink: 1;
        }

        ul li > input[type='text'] + i{
            margin-left: 5px;
        }

        .one_half {
            width: 48%;
            display: inline-block;
        }

        .one_third {
            width: 32%;
            display: inline-block;
        }

        .one_whole {
            width: 96%;
            display: inline-block;
        }

        .itembox {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 20px;
        }

        .itembox:last-of-type {
            margin-right: 0;
        }

        .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 200px;
            height: 200px;
            padding: 3px;
            position: relative;
        }

        .itembox .photo::before {
            content: "+";
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .itembox .photo >  input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .itembox .photo > img{
            max-width: 100%;
            max-height: 100%;
        }

        .itembox.chosen .photo::before {
            content: none;
        }

        .itembox > div > span {
            display: none;
        }

        .itembox.chosen > div > span {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 28px;
            text-align: center;
            color: #EA0029;
            font-size: 24px;
            font-weight: 400;
            cursor: pointer;
        }

        .itembox input[type="text"] {
            box-sizing: border-box;
            width: 200px;
            margin-top: 5px;
        }

        .itembox > div > i.fa-trash-alt {
            display: inline-block;
            margin: 8px auto 5px;
            width: 36px;
            height: 36px;
            border: 1px dashed #EA0029;
            border-radius: 18px;
            line-height: 34px;
            text-align: center;
            color: #EA0029;
            font-size: 18px;
            font-weight: 400;
            cursor: pointer;
        }

        .additem {
            width: 200px;
            height: 200px;
            padding: 3px;
            position: relative;
        }

        .additem > span {
            display: block;
            width: 36px;
            height: 36px;
            border: 1px dashed #3FA4F4;
            border-radius: 18px;
            line-height: 24px;
            text-align: center;
            color: #3FA4F4;
            font-size: 36px;
            font-weight: 300;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
            cursor: pointer;
        }

        span.badge.badge-info {
            background-color: #5bc0de;
            margin-right: 8px;
            font-size: 14px;
            height: 24px;
        }

        .bootstrap-tagsinput .badge [data-role="remove"]::after {
            background-color: #5bc0de !important;
            font-size: 14px;
        }

        .toggle-switch {
            margin: 50px 0 -30px;
            border-top: 2px solid #E2E2E2;
            padding: 20px;
        }

        .toggle-switch .description {
            margin-right: 20px;
            color: #0069d9;
            font-weight: 700;
            font-size: 20px;
        }


        .table_template {
            text-align: center;
        }

        .table_template thead th {
            background-color: #E0E0E0;
            padding: 10px;
            text-align: center;
        }

        .table_template tbody td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #E2E2E2;
        }

        .table_template tbody tr:nth-of-type(even) {
            background-color: #F7F7F7
        }

        .table_template .itembox .photo {
            width: 100px;
            height: 100px;
        }

        #tb_product_variants, #tb_frequently_used_options {
            width: 100%;
        }

        #tb_product_variants thead tr th, #tb_frequently_used_options thead tr th {
            min-width: 100px;
        }

        #tb_product_variants thead tr th:first-of-type, #tb_frequently_used_options thead tr th:first-of-type {
            min-width: 50px;
        }

        #tb_product_variants thead tr th:last-of-type, #tb_frequently_used_options thead tr th:last-of-type {
            min-width: 130px;
        }

        #tb_product_variants tbody .itembox{
            margin: auto;
        }

        #tb_frequently_used_options i{
            font-size: 24px;
        }

        #tb_bulk_apply, #tb_edit_name, #tb_quick_assign, #tb_quick_assign2 {
            width: 100%;
            margin-bottom: 20px;
        }

        #tb_bulk_apply tbody tr, #tb_edit_name tbody tr {
            background-color: #FFFFFF;
        }

        #tb_bulk_apply tbody tr td .itembox {
            margin: auto;
        }

        .btnbox {
            text-align: center;
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 80px;
        }

        .bodybox .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        #modal_bulk_apply, #modal_edit_name, #modal_quick_assign, #modal_quick_assign2 {
            position: fixed;
            top: 30px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        #modal_bulk_apply > .modal-content, #modal_edit_name > .modal-content, #modal_quick_assign > .modal-content, #modal_quick_assign2 > .modal-content {
            width: 700px;
            max-height: calc( 100vh - 60px);
            margin: auto;
            border: 3px solid #E2E2E2;
            padding: 25px 20px 20px;
            background-color: white;
            overflow-y: auto;
        }

        #modal_bulk_apply > .modal-content, #modal_quick_assign > .modal-content, #modal_quick_assign2 > .modal-content{
            width: 600px;
        }

        .custom-modal-header{
            color: #808080;
            border-bottom: 2px solid #E2E2E2;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-modal-header i{
            font-size: 20px;
        }

        #tb_edit_name thead tr th {
            min-width: 200px;
        }

        #tb_edit_name thead tr th:first-of-type {
            min-width: 150px;
        }

        #tb_edit_name tbody tr select + input {
            margin-top: 5px;
        }

        .heading-and-btn {
            border-bottom: 2px solid #E2E2E2;
            padding: 0 20px 10px;
        }

        .heading-and-btn ul{
            display: flex;
            justify-content: space-between;
            border-bottom: none;
            background-color: #FFFFFF;
        }

        .heading-and-btn ul li:nth-of-type(1){
            text-align: left;
            flex-grow: 1;
        }

        .heading-and-btn ul li:nth-of-type(2){
            width: 240px;
            text-align: center;
            flex-grow: 0;
            flex-shrink: 0;
        }

        .heading-and-btn ul li select{
            display: inline-block;
            width: 300px;
            margin-left: 10px;
        }

        .NTD_price{
            display: none;
        }




    </style>

</head>

<body class="primary">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent" id="app">

        <div class="heading-and-btn">

            <ul>
                <li>
                    <h4>Edit Attribute's Frequently Used Options</h4>
                </li>
            </ul>

            <ul>
                <li>
                    Choose Attribute:

                    <select class="form-control" v-model='lv1'>
                        <option value="0">Select Product Category</option>
                        <option v-for="(item, index) in level1" :value="item.cat_id" :key="item.category">{{ item.category }}</option>
                    </select>

                    <select class="form-control" v-model='lv2'>
                        <option value="0">Select Product Sub Category</option>
                        <option v-for="(item, index) in level2" :value="item.cat_id" :key="item.category">{{ item.category }}</option>
                    </select>

                    <select class="form-control" v-model='lv3'>
                        <option value="0">Select Attribute</option>
                        <option v-for="(item, index) in level3" :value="item.cat_id" :key="item.category">{{ item.category }}</option>
                    </select>
                </li>

                <li>
                    <button class="btn btn-primary" @click="detail()">Start</button>
                </li>
            </ul>

        </div>




        <div class="region" v-if="editing">
            <span class="heading">Attribute's Name</span>

            <div class="heading-and-btn" style="border-bottom: none;" :ref="'porto'">
                <ul>
                    <li>
                        <input type="text" class="form-control" style="border:none; border-bottom: 2px solid #C0C0C0; border-radius: 0;" v-model="option" placeholder="Input Option Here">
                    </li>
                    <li v-if="pid != 0">
                        <button class="btn btn-info" @click="e_add_criterion" v-if="!e_editing">Add</button>
                        <button class="btn btn-secondary" v-if="e_editing" @click="e_cancel_criterion">Cancel</button>
                        <button class="btn btn-info" v-if="e_editing" @click="e_update_criterion">Update</button>
                    </li>

                    <li v-if="pid == 0">
                        <button class="btn btn-info" @click="_add_criterion" v-if="!e_editing">Add</button>
                        <button class="btn btn-secondary" v-if="e_editing" @click="_cancel_criterion">Cancel</button>
                        <button class="btn btn-info" v-if="e_editing" @click="_update_criterion">Update</button>
                    </li>
                </ul>

            </div>


            <div style="width: 100%; overflow-x: auto; margin-top: 5px;">
                <table id="tb_frequently_used_options" class="table_template">
                    <thead>
                    <tr>
                        <th>Frequently Used Option</th>
                        <th v-if="pid != 0">Action</th>
                        <th v-if="pid == 0">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(item,index) in petty_list" :key="index">
                        <td>{{ item.option }}</td>
                        <td v-if="pid != 0">
                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-up" @click="e_set_up(index, item.id)"></i>
                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-down" @click="e_set_down(index, item.id)"></i>
                            <i aria-hidden="true" class="fas fa-edit" @click="e_edit(item.id)"></i>
                            <i aria-hidden="true" class="fas fa-trash-alt" @click="e_del(item.id)"></i>
                        </td>
                        <td v-if="pid == 0">
                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-up" @click="_set_up(index, item.id)"></i>
                            <i aria-hidden="true" class="fas fa-arrow-alt-circle-down" @click="_set_down(index, item.id)"></i>
                            <i aria-hidden="true" class="fas fa-edit" @click="_edit(item.id)"></i>
                            <i aria-hidden="true" class="fas fa-trash-alt" @click="_del(item.id)"></i>
                        </td>
                    </tr>


                    </tbody>

                </table>
            </div>

            <div class="btnbox">
                <a class="btn" @click="reset">Reset</a>
                <a class="btn" :disabled="submit == true" v-if="pid == 0" @click="apply">Submit</a>
                <a class="btn" :disabled="submit == true" v-if="pid != 0" @click="apply_edit">Submit</a>
            </div>

        </div>


        <div id="modal_quick_assign" class="modal">
            <!-- Modal content -->
            <div class="modal-content">

                <div class="custom-modal-header">
                    <h5>Quickly Assign Attribute's Value</h5>
                    <i class="fa fa-times fa-lg" aria-hidden="true" onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign').toggle(); return false;})();return false;"></i>
                </div>

                <div>
                    <table id="tb_quick_assign" class="table_template">
                        <thead>
                        <tr>
                            <th>Frequently Used Options</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><button class="btn btn-primary">Apply</button></td>
                        </tr>

                        </tbody>
                    </table>

                    <div class="btnbox">
                        <button class="btn btn-secondary" onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign').toggle(); return false;})();return false;">Cancel</button>
                    </div>

                </div>
            </div>
        </div>

        <div id="modal_quick_assign2" class="modal">
            <!-- Modal content -->
            <div class="modal-content">

                <div class="custom-modal-header">
                    <h5>Quickly Assign Attribute's Value</h5>
                    <i class="fa fa-times fa-lg" aria-hidden="true" onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2').toggle(); return false;})();return false;"></i>
                </div>

                <div>
                    <table id="tb_quick_assign2" class="table_template">
                        <thead>
                        <tr>
                            <th>Frequently Used Options</th>
                            <th>Check</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>CD Table with Acrylic/Laminate Partition</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Black</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Off White</td>
                            <td><input type="checkbox"></td>
                        </tr>

                        </tbody>
                    </table>

                    <div class="btnbox">
                        <button class="btn btn-secondary" onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2').toggle(); return false;})();return false;">Cancel</button>
                        <button class="btn btn-primary">Apply</button>
                    </div>

                </div>
            </div>
        </div>






        <div id="modal_bulk_apply" class="modal" style="display: none;">
            <!-- Modal content -->
            <div class="modal-content">

                <div class="custom-modal-header">
                    <h5>Bulk Apply</h5>
                    <i class="fa fa-times fa-lg" aria-hidden="true" onclick="(function(){ $('.mask').toggle(); $('#modal_bulk_apply').toggle(); return false;})();return false;"></i>
                </div>

                <div>
                    <table id="tb_bulk_apply" class="table_template">
                        <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Column</th>
                            <th>Parameter</th>
                            <th>Operator</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Code</td>
                            <td><input type="text" class="form-control"></td>
                            <td>
                                Assign To
                            </td>
                        </tr>

                        <tr class="NTD_price">
                            <td><input type="checkbox"></td>
                            <td>Price (NTD)</td>
                            <td><input type="text" class="form-control"></td>
                            <td>
                                <select class="form-control">
                                    <option>Assign To</option>
                                    <option>Add To</option>
                                    <option>Multiply To</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Price</td>
                            <td><input type="text" class="form-control"></td>
                            <td>
                                <select class="form-control">
                                    <option>Assign To</option>
                                    <option>Add To</option>
                                    <option>Multiply To</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Image</td>
                            <td>
                                <div class="itembox">
                                    <div class="photo">
                                        <input type="file">
                                        <img>
                                    </div>

                                    <div>
                                        <span>x</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                Assign To
                            </td>
                        </tr>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Status</td>
                            <td>
                                <select class="form-control">
                                    <option>Enabled</option>
                                    <option>Disabled</option>
                                </select>
                            </td>
                            <td>
                                Assign To
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <div class="btnbox">
                        <button class="btn btn-secondary" onclick="(function(){ $('.mask').toggle(); $('#modal_bulk_apply').toggle(); return false;})();return false;">Cancel</button>
                        <button class="btn btn-primary">Apply</button>
                    </div>

                </div>
            </div>
        </div>


        <div id="modal_edit_name" class="modal" style="display: none;">
            <!-- Modal content -->
            <div class="modal-content">

                <div class="custom-modal-header">
                    <h5>Edit Variation/Option Name</h5>
                    <i class="fa fa-times fa-lg" aria-hidden="true" onclick="(function(){ $('.mask').toggle(); $('#modal_edit_name').toggle(); return false;})();return false;"></i>
                </div>

                <div>
                    <table id="tb_edit_name" class="table_template">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Old Name</th>
                            <th>New Name</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td><b>1st Variation</b></td>
                            <td>
                                <select class="form-control" readonly="">
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <select class="form-control">
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 1</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 2</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td><b>2nd Variation</b></td>
                            <td>
                                <select class="form-control" disabled>
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <select class="form-control">
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 1</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 2</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td><b>3rd Variation</b></td>
                            <td>
                                <select class="form-control" readonly="">
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <select class="form-control">
                                    <option>Select a value</option>
                                    <option>Beam Angle</option>
                                    <option>Lumens</option>
                                    <option>CRI / RA</option>
                                    <option>CCT</option>
                                    <option>Wattage</option>
                                    <option>IP Rating</option>
                                    <option>Life Hours</option>
                                    <option>Color Finish</option>
                                    <option>Installation</option>
                                    <option>Size</option>
                                    <option>Body Color</option>
                                    <option>Trim Color</option>
                                    <option>Custom</option>
                                </select>

                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 1</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Option 2</td>
                            <td>
                                <input type="text" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <div class="btnbox">
                        <button class="btn btn-secondary" onclick="(function(){ $('.mask').toggle(); $('#modal_edit_name').toggle(); return false;})();return false;">Cancel</button>
                        <button class="btn btn-primary">Apply</button>
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script src="https://cdn.bootcss.com/moment.js/2.21.0/moment.js"></script>
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="js/a076d05399.js"></script> 
<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>
<script defer src="js/frequently_used_options.js"></script>

</html>
