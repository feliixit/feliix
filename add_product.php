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
    <title>FELIIX template</title>
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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css">
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
    $(function() {
        $('header').load('include/header.php');
    })
    </script>

    <style>
    body.gray header>.headerbox {
        background-color: #707071;
    }

    body.gray select {
        background-image: url(images/ui/icon_form_select_arrow_gray.svg);
    }

    a,
    a:link,
    a:visited,
    a:active,
    a:hover,
    area {
        text-decoration: none;
        cursor: pointer;
    }

    body.gray header nav a,
    body.gray header nav a:link {
        color: #000;
    }

    body.gray header nav a:hover {
        color: #333;
    }

    body.gray header nav {
        font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
    }

    body.gray li>input:not([type='checkbox']),
    body.gray td>input:not([type='checkbox']) {
        background-color: #fff;
        border: 1px solid #ced4da;
    }

    body.gray li>input.form-control:disabled,
    body.gray li>input.form-control[readonly] {
        background-color: #e9ecef;
    }

    body.gray input.alone[type=checkbox]::before {
        color: rgb(118, 118, 118);
        font-size: 20px;
    }

    body.gray input.alone[type=checkbox]:checked::before {
        color: rgb(0, 117, 255);
    }

    body.gray header nav ul.info {
        margin-bottom: 0;
    }

    body.gray header nav ul.info b {
        font-weight: bold;
    }

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

    .region>ul,
    .heading-and-btn>ul {
        margin: 0;
        border-bottom: 1px solid #E2E2E2;
        background-color: #F7F7F7;
        display: flex;
        align-items: center;
    }

    .region>ul.variation_list {
        align-items: flex-start;
        border-bottom: none;
        background-color: #F0F0F0;
        margin-bottom: 20px;
    }

    .region>ul.variation_list h6 {
        text-align: center;
    }

    .region>ul.variation_list li:first-of-type {
        min-width: 170px;
    }

    .region>ul.variation_list select {
        margin-bottom: 10px;
    }

    .region>ul>li,
    .heading-and-btn>ul>li {
        display: table-cell;
        text-decoration: none;
        padding: 10px;
    }

    .region>ul>li:first-of-type,
    .heading-and-btn ul li:nth-of-type(1) {
        width: 20vw;
        min-width: 150px;
        text-align: center;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .region>ul>li:nth-of-type(2),
    .heading-and-btn ul li:nth-of-type(2) {
        flex-grow: 1;
        flex-shrink: 1;
    }

    .region>ul>li>input[type='text']+i {
        margin-left: 5px;
    }

    .region>ul>li button.btn-light,
    .region>ul>li button.btn-light:not(:disabled).active,
    .region>ul>li .show>.btn-light.dropdown-toggle {
        background-color: #fff;
        border: 1px solid #ced4da;
    }

    .region>ul>li>input[type='text']:first-of-type~input {
        margin-left: 10px;
    }

    .region .bootstrap-select .dropdown-toggle:focus,
    .region .bootstrap-select>select.mobile-device:focus+.dropdown-toggle {
        outline: 0;
        box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
        background-color: #fff;
    }

    .region .bootstrap-select .btn:focus {
        outline: none !important;
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

    input.updated_date {
        margin-top: 10px;
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

    .itembox .photo>input[type='file'] {
        opacity: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
    }

    .itembox .photo>img {
        max-width: 100%;
        max-height: 100%;
    }

    .itembox.chosen .photo::before {
        content: none;
    }

    .itembox>div>span {
        display: none;
    }

    .itembox.chosen>div>span {
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
        border: 1px dashed #3FA4F4;
    }

    .itembox>div>i.fa-trash-alt {
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

    .additem>span {
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

    #tb_product_variants {
        width: 100%;
    }

    #tb_product_variants thead tr th {
        min-width: 100px;
    }

    #tb_product_variants thead tr th:first-of-type {
        min-width: 50px;
    }

    #tb_product_variants thead tr th:last-of-type {
        min-width: 130px;
    }

    #tb_product_variants tbody .itembox {
        margin: auto;
    }

    #tb_bulk_apply,
    #tb_edit_name,
    #tb_quick_assign,
    #tb_quick_assign2 {
        width: 100%;
        margin-bottom: 20px;
    }

    #tb_bulk_apply tbody tr,
    #tb_edit_name tbody tr {
        background-color: #FFFFFF;
    }

    #tb_bulk_apply tbody tr td .itembox {
        margin: auto;
    }

    .btnbox {
        text-align: center;
    }

    .btnbox>button,
    .heading-and-btn button {
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

    #modal_bulk_apply,
    #modal_edit_name,
    #modal_quick_assign,
    #modal_quick_assign2_1 {
        position: fixed;
        top: 30px;
        left: 0;
        right: 0;
        margin: auto;
        z-index: 2;
    }

    #modal_bulk_apply>.modal-content,
    #modal_edit_name>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_1>.modal-content {
        width: 700px;
        max-height: calc(100vh - 60px);
        margin: auto;
        border: 3px solid #E2E2E2;
        padding: 25px 20px 20px;
        background-color: white;
        overflow-y: auto;
    }

    #modal_bulk_apply>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_1>.modal-content {
        width: 600px;
    }

    #modal_bulk_apply,
    #modal_edit_name,
    #modal_quick_assign,
    #modal_quick_assign2_2 {
        position: fixed;
        top: 30px;
        left: 0;
        right: 0;
        margin: auto;
        z-index: 2;
    }

    #modal_bulk_apply>.modal-content,
    #modal_edit_name>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_2>.modal-content {
        width: 700px;
        max-height: calc(100vh - 60px);
        margin: auto;
        border: 3px solid #E2E2E2;
        padding: 25px 20px 20px;
        background-color: white;
        overflow-y: auto;
    }

    #modal_bulk_apply>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_2>.modal-content {
        width: 600px;
    }

    #modal_bulk_apply,
    #modal_edit_name,
    #modal_quick_assign,
    #modal_quick_assign2_3 {
        position: fixed;
        top: 30px;
        left: 0;
        right: 0;
        margin: auto;
        z-index: 2;
    }

    #modal_bulk_apply>.modal-content,
    #modal_edit_name>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_3>.modal-content {
        width: 700px;
        max-height: calc(100vh - 60px);
        margin: auto;
        border: 3px solid #E2E2E2;
        padding: 25px 20px 20px;
        background-color: white;
        overflow-y: auto;
    }

    #modal_bulk_apply>.modal-content,
    #modal_quick_assign>.modal-content,
    #modal_quick_assign2_3>.modal-content {
        width: 600px;
    }

    .custom-modal-header {
        color: #808080;
        border-bottom: 2px solid #E2E2E2;
        padding-bottom: 5px;
        margin-bottom: 10px;
        font-size: 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-header i {
        font-size: 20px;
    }

    #tb_edit_name thead tr th {
        min-width: 200px;
    }

    #tb_edit_name thead tr th:first-of-type {
        min-width: 150px;
    }

    #tb_edit_name tbody tr select+input {
        margin-top: 5px;
    }

    .heading-and-btn {
        border-bottom: 2px solid #E2E2E2;
        padding: 0 20px 10px;
    }

    .heading-and-btn ul {
        display: flex;
        justify-content: space-between;
        border-bottom: none;
        background-color: #FFFFFF;
    }

    .heading-and-btn ul li:nth-of-type(1) {
        text-align: left;
        flex-grow: 1;
    }

    .heading-and-btn ul li:nth-of-type(2) {
        width: 240px;
        text-align: center;
        flex-grow: 0;
        flex-shrink: 0;
    }

    .heading-and-btn ul li select {
        display: inline-block;
        width: 300px;
        margin-left: 10px;
    }

    .NTD_price {}
    </style>

</head>

<body class="gray">

    <div class="bodybox">
        <div class="mask" style="display:none"></div>
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div id="app" class="mainContent">

            <div class="heading-and-btn">

                <ul>
                    <li>
                        <h4>Add New Product</h4>
                    </li>

                    <li v-if="edit_mode == true">
                        <button class="btn btn-secondary" @click="cancel()">Cancel</button>
                        <button class="btn btn-primary" @click="save()">Save</button>
                    </li>
                </ul>

                <ul>

                    <li v-if="edit_mode == false">
                        Choose Category

                        <select class="form-control" v-model="category">
                            <option value="" selected>Select one value</option>
                            <option value="10000000">Lighting</option>
                            <option value="20000000">Systems Furniture</option>
                        </select>

                        <select v-if="category == '20000000'" class="form-control" v-model="sub_category">
                            <option v-for="(item, index) in sub_cateory_item" :value="item.cat_id" :key="item.category">
                                {{ item.category }}</option>
                        </select>

                        <select v-if="category == '10000000'" class="form-control">

                        </select>
                    </li>

                    <li v-if="edit_mode == false">
                        <button class="btn btn-primary" :disabled="sub_category == '' || category == ''"
                            @click="edit_category()">Start</button>
                    </li>


                </ul>

            </div>


            <div class="region" v-show="edit_mode == true">
                <span class="heading">Basic Information</span>

                <ul>
                    <li>
                        Category
                    </li>
                    <li>
                        <select class="form-control one_third" v-model="category" :disabled="edit_mode == true">
                            <option value="" selected>Select one value</option>
                            <option value="10000000">Lighting</option>
                            <option value="20000000">Systems Furniture</option>
                        </select>

                        <select v-if="category == '20000000'" class="form-control one_third" v-model="sub_category"
                            :disabled="edit_mode == true">
                            <option v-for="(item, index) in sub_cateory_item" :value="item.cat_id" :key="item.category">
                                {{ item.category }}</option>
                        </select>

                        <select v-if="category == '10000000'" class="form-control one_third"
                            :disabled="edit_mode == true">

                        </select>
                    </li>
                </ul>

                <ul>
                    <li>
                        Tag
                    </li>
                    <li>
                        <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="96%"
                            title="No tag selected" id="tag01">

                            <optgroup v-for="(group, index) in tag_group" :label="group.group">

                                <option v-for="(it, index2) in group.items" :value="it.item_name">{{ it.item_name }}
                                </option>

                            </optgroup>



                        </select>
                    </li>
                    <!-- <li v-show="category == '20000000'">
                    <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="96%" title="No tag selected">
                        
                    </select>
                </li> -->
                </ul>

                <ul>
                    <li>
                        Brand
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model="brand">
                    </li>
                </ul>

                <ul>
                    <li>
                        Code
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model="code">
                    </li>
                </ul>

                <ul class="NTD_price" v-show="show_ntd === true">
                    <li>
                        Cost Price (NTD)
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model.lazy="price_ntd">
                        <input type="text" class="form-control one_third" v-model="price_ntd_change">
                    </li>
                </ul>

                <ul>
                    <li>
                        Suggested Retail Price
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model.lazy="price">
                        <input type="text" class="form-control one_third" v-model="price_change">
                    </li>
                </ul>

                <ul>
                    <li>
                        Quoted Price
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model.lazy="quoted_price">
                        <input type="text" class="form-control one_third" v-model="quoted_price_change">
                    </li>
                </ul>

                <ul>
                    <li>
                        MOQ
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" v-model="moq">
                    </li>
                </ul>

                <ul>
                    <li>
                        Description
                    </li>
                    <li>
                        <textarea class="form-control one_whole" rows="10" v-model="description">

                    </textarea>
                    </li>
                </ul>

                <ul>
                    <li>Notes</li>
                    <li><input type="text" class="form-control one_half" v-model="notes"></li>
                </ul>

                <ul>
                    <li>Related Products</li>
                    <li><input type="text" value="" data-role="tagsinput" id="related_product"></li>
                </ul>

                <ul>
                    <li>
                        Images
                    </li>
                    <li style="display: flex; flex-wrap: wrap;">

                        <div :class="['itembox', (url1 !== null ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" id="photo1" name="photo1" @change="onFileChange($event, 1)">
                                <img v-if="url1" :src="url1" />
                            </div>
                            Cover Image
                            <div>
                                <span @click="clear_photo(1)">x</span>
                            </div>
                        </div>

                        <div :class="['itembox', (url2 !== null ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" id="photo2" name="photo2" @change="onFileChange($event, 2)">
                                <img v-if="url2" :src="url2" />
                            </div>
                            Image 1
                            <div>
                                <span @click="clear_photo(2)">x</span>
                            </div>
                        </div>

                        <div :class="['itembox', (url3 !== null ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" id="photo3" name="photo3" @change="onFileChange($event, 3)">
                                <img v-if="url3" :src="url3" />
                            </div>
                            Image 2
                            <div>
                                <span @click="clear_photo(3)">x</span>
                            </div>
                        </div>

                    </li>
                </ul>

            </div>


            <div class="region" v-if="edit_mode == true">
                <span class="heading">Specification Information</span>

                <ul v-for="(item, index) in special_infomation">
                    <li>
                        {{ item.category }}
                    </li>
                    <li>
                        <input type="text" class="form-control one_half" :ref="item.cat_id">
                        <i class="fas fa-hand-pointer" @click="get_special_infomation_detail(item.cat_id)"></i>
                    </li>
                </ul>


            </div>


            <div class="toggle-switch" v-show="edit_mode == true && accessory_infomation.length > 0">
                <label for="accessory_mode" class="description">Has Accessory?</label>
                <input type="checkbox" data-toggle="toggle" data-width="100px" data-onstyle="primary"
                    data-offstyle="secondary" data-on="Yes" data-off="No" id="accessory_mode">
            </div>


            <div class="region" v-show="edit_mode == true && accessory_mode == true">
                <span class="heading">Accessory Information</span>

                <ul v-for="(item, index) in accessory_infomation">
                    <li>
                        {{ item.category }}
                    </li>
                    <li style="display: flex; flex-wrap: wrap;">

                        <div :class="['itembox', (detail.url !== '' ? 'chosen' : '')]"
                            v-for="(detail, index) in item.detail[0]">
                            <div class="photo">
                                <input type="file" @change="onFileChangeAccessory($event, detail.cat_id, detail.id)"
                                    :id="'accessory_' + detail.cat_id + '_' + detail.id">
                                <img v-if="detail.url" :src="detail.url">
                            </div>
                            <input type="text" class="form-control" placeholder="Code" v-model="detail.code">
                            <input type="text" class="form-control" placeholder="Name" v-model="detail.name">
                            <input class="NTD_price form-control" type="text" class="form-control"
                                placeholder="Additional Price (NTD)" v-model="detail.price_ntd"
                                v-show="show_ntd === true">
                            <input type="text" class="form-control" placeholder="Additional Price"
                                v-model="detail.price">
                            <div>
                                <span @click="clear_accessory_item(detail.cat_id, detail.id)">x</span>
                                <i class="fas fa-trash-alt"
                                    @click="remove_accessory_item(detail.cat_id, detail.id)"></i>
                            </div>
                        </div>

                        <div class="additem">
                            <span @click="add_accessory_item(item.cat_id)">+</span>
                        </div>
                    </li>
                </ul>

            </div>


            <div class="toggle-switch" v-show="edit_mode == true">
                <label for="variation_mode" class="description">Variation Mode</label>
                <input type="checkbox" data-toggle="toggle" data-width="100px" data-onstyle="primary"
                    data-offstyle="secondary" data-on="Yes" data-off="No" id="variation_mode" v-model="variation_mode"
                    v-on:click="console.log('1');">
            </div>


            <div class="region" v-show="edit_mode == true && variation_mode == true">
                <span class="heading">Variations</span>

                <ul class="variation_list">
                    <li>
                        <h6>1st Variation</h6>
                        <select class="form-control" v-model="variation1">
                            <option value="">Select a value</option>
                            <option v-for="(item, index) in special_infomation" :value="item.category"
                                :key="item.category">{{ item.category }}</option>

                            <option value="custom">Custom</option>
                        </select>

                        <input type="text" class="form-control" :disabled="variation1 !== 'custom'"
                            v-model="variation1_custom">
                    </li>

                    <li>
                        <h6>Options</h6>
                        <input type="text" value="" data-role="tagsinput" id="variation1_value">
                        <i class="fas fa-hand-pointer" @click="get_special_infomation_detail_variantion1()"></i>
                    </li>
                </ul>

                <ul class="variation_list">
                    <li>
                        <h6>2nd Variation</h6>
                        <select class="form-control" v-model="variation2" :disabled="variation1 == ''">
                            <option value="">Select a value</option>
                            <option v-for="(item, index) in special_infomation" :value="item.category"
                                :key="item.category">{{ item.category }}</option>

                            <option value="custom">Custom</option>
                        </select>

                        <input type="text" class="form-control" :disabled="variation2 !== 'custom'"
                            v-model="variation2_custom">
                    </li>

                    <li>
                        <h6 style="text-align: center;">Options</h6>
                        <input type="text" value="" data-role="tagsinput" id="variation2_value"
                            :disabled="variation1 == ''">
                        <i class="fas fa-hand-pointer" @click="get_special_infomation_detail_variantion2()"></i>

                    </li>
                </ul>

                <ul class="variation_list">
                    <li>
                        <h6>3rd Variation</h6>
                        <select class="form-control" v-model="variation3"
                            :disabled="variation1 == '' || variation2 == ''">
                            <option value="">Select a value</option>
                            <option v-for="(item, index) in special_infomation" :value="item.category"
                                :key="item.category">{{ item.category }}</option>

                            <option value="custom">Custom</option>
                        </select>

                        <input type="text" class="form-control" :disabled="variation3 !== 'custom'"
                            v-model="variation3_custom">
                    </li>

                    <li>
                        <h6 style="text-align: center;">Options</h6>
                        <input type="text" data-role="tagsinput" id="variation3_value"
                            :disabled="variation1 == '' || variation2 == ''">
                        <i class="fas fa-hand-pointer" @click="get_special_infomation_detail_variantion3()"></i>
                    </li>
                </ul>

                <button class="btn btn-success" @click="generate_product_variants">Generate Product Variants</button>
            </div>


            <div class="region" v-show="edit_mode == true && variation_mode == true">
                <span class="heading">Product Variants</span>

                <button class="btn btn-info"
                    onclick=" (function(){ $('.mask').toggle(); $('#modal_bulk_apply').toggle(); return false;})();return false;">Bulk
                    Apply</button>
                <button style="display: none;" class="btn btn-info"
                    onclick=" (function(){ $('.mask').toggle(); $('#modal_edit_name').toggle(); return false;})();return false;">Edit
                    Variation/Option Name</button>

                <div style="width: 100%; overflow-x: auto; margin-top: 10px;">
                    <table id="tb_product_variants" class="table_template">
                        <thead>
                            <tr>
                                <th><input class="alone" type="checkbox" value="1" @click="toggle_product()"
                                        id="select_all_product"></th>
                                <th>{{ variation1_text }}</th>
                                <th>{{ variation2_text }}</th>
                                <th>{{ variation3_text }}</th>
                                <th>Code</th>
                                <th class="NTD_price" v-show="show_ntd === true">Cost Price (NTD)</th>
                                <th>Suggested Retail Price</th>
                                <th>Quoted Price</th>
                                <th>Image</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item, index) in variation_product">
                                <td><input class="alone" type="checkbox" value="1" v-model="item.checked"></td>
                                <td>{{ item.v1 }}</td>
                                <td>{{ item.v2 }}</td>
                                <td>{{ item.v3 }}</td>
                                <td><input type="text" class="form-control" v-model="item.code"></td>
                                <td class="NTD_price" v-show="show_ntd === true"><input type="number"
                                        class="form-control" v-model="item.price_ntd"
                                        @change="product_price_ntd_changed(item.id)"><input type="text"
                                        class="form-control updated_date" v-model="item.price_ntd_change"></td>
                                <td><input type="number" class="form-control" v-model="item.price"
                                        @change="product_price_changed(item.id)"><input type="text"
                                        class="form-control updated_date" v-model="item.price_change"></td>
                                <td><input type="number" class="form-control" v-model="item.quoted_price"
                                        @change="product_quoted_price_changed(item.id)"><input type="text"
                                        class="form-control updated_date" v-model="item.quoted_price_change"></td>
                                <td>
                                    <div :class="['itembox', (item.url !== '' ? 'chosen' : '')]">
                                        <div class="photo">
                                            <input type="file" @change="onFileChangeVariation($event, item.id)"
                                                :id="'variation_' + item.id">
                                            <img v-if="item.url" :src="item.url">
                                        </div>
                                        <div>
                                            <span @click="clear_variation_item(item.id)">x</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control" v-model="item.status">
                                        <option value="0">Disabled</option>
                                        <option value="1">Enabled</option>
                                    </select>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>

            </div>


            <div id="modal_quick_assign" class="modal">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Quickly Assign Attribute's Value</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign').toggle(); return false;})();return false;"></i>
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
                                <tr v-for="(item, index) in special_infomation_detail">
                                    <td>{{ item.option }}</td>
                                    <td><button class="btn btn-primary"
                                            @click="apply_special_infomation_detail(item.cat_id, item.option)">Apply</button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="btnbox">
                            <button class="btn btn-secondary"
                                onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign').toggle(); return false;})();return false;">Cancel</button>
                        </div>

                    </div>
                </div>
            </div>

            <div id="modal_quick_assign2_1" class="modal">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Quickly Assign Attribute's Value</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_1').toggle(); return false;})();return false;"></i>
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
                                <tr v-for="(item, index) in special_infomation_detail">
                                    <td>{{ item.option }}</td>
                                    <td><input class="alone" type="checkbox" name="apply_special_infomation_1"
                                            :value="item.option"></td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="btnbox">
                            <button class="btn btn-secondary"
                                onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_1').toggle(); return false;})();return false;">Cancel</button>
                            <button class="btn btn-primary"
                                @click="apply_special_infomation_detail_variantion1">Apply</button>
                        </div>

                    </div>
                </div>
            </div>

            <div id="modal_quick_assign2_2" class="modal">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Quickly Assign Attribute's Value</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_2').toggle(); return false;})();return false;"></i>
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
                                <tr v-for="(item, index) in special_infomation_detail">
                                    <td>{{ item.option }}</td>
                                    <td><input class="alone" type="checkbox" name="apply_special_infomation_2"
                                            :value="item.option"></td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="btnbox">
                            <button class="btn btn-secondary"
                                onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_2').toggle(); return false;})();return false;">Cancel</button>
                            <button class="btn btn-primary"
                                @click="apply_special_infomation_detail_variantion2">Apply</button>
                        </div>

                    </div>
                </div>
            </div>

            <div id="modal_quick_assign2_3" class="modal">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Quickly Assign Attribute's Value</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_3').toggle(); return false;})();return false;"></i>
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
                                <tr v-for="(item, index) in special_infomation_detail">
                                    <td>{{ item.option }}</td>
                                    <td><input class="alone" type="checkbox" name="apply_special_infomation_3"
                                            :value="item.option"></td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="btnbox">
                            <button class="btn btn-secondary"
                                onclick=" (function(){ $('.mask').toggle(); $('#modal_quick_assign2_3').toggle(); return false;})();return false;">Cancel</button>
                            <button class="btn btn-primary"
                                @click="apply_special_infomation_detail_variantion3">Apply</button>
                        </div>

                    </div>
                </div>
            </div>





            <div id="modal_bulk_apply" class="modal" style="display: none;">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Bulk Apply</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick="(function(){ $('.mask').toggle(); $('#modal_bulk_apply').toggle(); return false;})();return false;"></i>
                    </div>

                    <div>
                        <table id="tb_bulk_apply" class="table_template">
                            <thead>
                                <tr>
                                    <th><input class="alone" type="checkbox" @click="bulk_toggle_product()"
                                            id="bulk_select_all_product"></th>
                                    <th>Column</th>
                                    <th>Parameter</th>
                                    <th>Operator</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td><input class="alone" type="checkbox" value="1" v-model="code_checked"></td>
                                    <td>Code</td>
                                    <td><input type="text" class="form-control" v-model='bulk_code'></td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                                <tr class="NTD_price" v-show="show_ntd === true">
                                    <td><input class="alone" type="checkbox" value="1" v-model="price_ntd_checked"></td>
                                    <td>Cost Price (NTD)</td>
                                    <td><input type="text" class="form-control" v-model='bulk_price_ntd'></td>
                                    <td>
                                        <select class="form-control" v-model="price_ntd_action">
                                            <option value="assign">Assign To</option>
                                            <option value="add">Add To</option>
                                            <option value="multiply">Multiply To</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr class="NTD_price" v-show="show_ntd === true">
                                    <td><input class="alone" type="checkbox" value="1"
                                            v-model="price_ntd_last_change_checked"></td>
                                    <td>Last Updated</td>
                                    <td><input type="date" class="form-control" v-model='bulk_price_ntd_last_change'>
                                    </td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1" v-model="price_checked"></td>
                                    <td>Suggested Retail Price</td>
                                    <td><input type="text" class="form-control" v-model='bulk_price'></td>
                                    <td>
                                        <select class="form-control" v-model="price_action">
                                            <option value="assign">Assign To</option>
                                            <option value="add">Add To</option>
                                            <option value="multiply">Multiply To</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1"
                                            v-model="price_last_change_checked"></td>
                                    <td>Last Updated</td>
                                    <td><input type="date" class="form-control" v-model='bulk_price_last_change'></td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1" v-model="quoted_price_checked">
                                    </td>
                                    <td>Quoted Price</td>
                                    <td><input type="text" class="form-control" v-model='bulk_quoted_price'></td>
                                    <td>
                                        <select class="form-control" v-model="quoted_price_action">
                                            <option value="assign">Assign To</option>
                                            <option value="add">Add To</option>
                                            <option value="multiply">Multiply To</option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1"
                                            v-model="quoted_price_last_change_checked"></td>
                                    <td>Last Updated</td>
                                    <td><input type="date" class="form-control" v-model='bulk_quoted_price_last_change'>
                                    </td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1" v-model="image_checked"></td>
                                    <td>Image</td>
                                    <td>
                                        <div :class="['itembox', (bulk_url !== '' ? 'chosen' : '')]">
                                            <div class="photo">
                                                <input type="file" id="bulk_image" name="bulk_image"
                                                    @change="onFileChangeBulkImage($event)">
                                                <img v-if="bulk_url" :src="bulk_url">
                                            </div>

                                            <div>
                                                <span @click="clear_bulk_image()">x</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                                <tr>
                                    <td><input class="alone" type="checkbox" value="1" v-model="status_checked"></td>
                                    <td>Status</td>
                                    <td>
                                        <select class="form-control" v-model="bulk_status">
                                            <option value="1">Enabled</option>
                                            <option value="0">Disabled</option>
                                        </select>
                                    </td>
                                    <td>
                                        Assign To
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        <div class="btnbox">
                            <button class="btn btn-secondary"
                                onclick="(function(){ $('.mask').toggle(); $('#modal_bulk_apply').toggle(); return false;})();return false;">Cancel</button>
                            <button class="btn btn-primary" @click="bulk_apply()">Apply</button>
                        </div>

                    </div>
                </div>
            </div>


            <div id="modal_edit_name" class="modal" style="display: none;">
                <!-- Modal content -->
                <div class="modal-content">

                    <div class="custom-modal-header">
                        <h5>Edit Variation/Option Name</h5>
                        <i class="fa fa-times fa-lg" aria-hidden="true"
                            onclick="(function(){ $('.mask').toggle(); $('#modal_edit_name').toggle(); return false;})();return false;"></i>
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
                            <button class="btn btn-secondary"
                                onclick="(function(){ $('.mask').toggle(); $('#modal_edit_name').toggle(); return false;})();return false;">Cancel</button>
                            <button class="btn btn-primary">Apply</button>
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

<script src="js/add_product.js"></script>

<script>
$(function() {
    $('#accessory_mode').change(function() {
        app.accessory_mode = $(this).prop('checked');
    })

})

$(function() {

    $('#variation_mode').change(function() {
        app.variation_mode = $(this).prop('checked');
    })
})
</script>

</html>