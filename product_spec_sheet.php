<?php include 'check.php';?>
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
    <title>Product Display</title>
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
          href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script src="js/bootstrap-select.js"></script>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');

            dialogshow($('.list_function .new_function a.filter'), $('.list_function .dialog.A'));
            dialogshow($('.list_function .new_function a.sort'), $('.list_function .dialog.B'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })

    </script>

    <style>

        body.gray header > .headerbox {
            background-color: #707071;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.gray header nav a, body.gray header nav a:link {
            color: #000;
        }

        body.gray header nav a:hover {
            color: #333;
        }

        body.gray header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.gray header nav ul.info {
            margin-bottom: 0;
        }

        body.gray header nav ul.info b {
            font-weight: bold;
        }

        body.gray select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 130px;
        }

        div.commandbox {
            position: fixed;
            right: 10px;
            z-index: 999;
            display: flex;
            flex-direction: column;
        }

        div.commandbox button{
            width: 160px;
            margin: 5px 0;
            border: 2px solid white;
        }

        .carousel-control-next, .carousel-control-prev {
            opacity: 0.7;
            top: 35%;
            width: 4%;
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5L4.25 4l2.5-2.5L5.25 0z'/%3e%3c/svg%3e") !important;
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5L3.75 4l-2.5 2.5L2.75 8l4-4-4-4z'/%3e%3c/svg%3e") !important;
        }

        .header_section {
            margin: 0 150px;
            width: 1240px;
        }

        .header_section table.upper_part {
            width: 100%;
        }

        .header_section table.upper_part td:nth-of-type(1) {
            width: 50%;
            height: 120px;
            vertical-align: bottom;
        }

        .header_section table.upper_part td:nth-of-type(1) img {
            margin-left: -10px;
        }

        .header_section table.upper_part td:nth-of-type(2) {
            width: 50%;
            vertical-align: top;
        }

        .header_section table.upper_part td:nth-of-type(2) div {
            color: #8B8B8B;
            font-size: 24px;
            border-left: 2px solid #8B8B8B;
            padding: 22px 0 10px 20px;
        }

        .header_section table.upper_part td:nth-of-type(2) div span {
            font-weight: 600;
        }

        .header_section div.middle_part {
            display: flex;
            justify-content: end;
            height: 100px;
            padding: 15px 0;
        }

        .header_section div.middle_part table tr:nth-of-type(1) td {
            background-color: #4473C5;
            font-weight: 700;
            color: white;
            text-align: center;
            padding: 0 25px;
            height: 35px;
        }

        .header_section div.middle_part table tr:nth-of-type(2) td {
            background-color: #C00002;
            padding: 0 25px;
            color: white;
            text-align: center;
            font-weight: 600;
            height: 35px;
        }

        .header_section div.middle_part table td input[type='text'] {
            border: none;
            background-color: white;
            padding: 0 3px;
            width: 100px;
            text-align: center;
        }

        .header_section div.lower_part {
            margin-top: 15px;
        }

        .header_section div.lower_part table {
            width: 100%;
            height: 120px;
        }

        .header_section div.lower_part table tr td:nth-of-type(1) {
            font-size: 42px;
            font-weight: 500;
            width: 61.93%;
            vertical-align: top;
        }

        .header_section div.lower_part table tr td:nth-of-type(2) {
            width: 25.23%;
            border-left: 2px solid #8B8B8B;
            vertical-align: top;
            padding-left: 17px;
        }

        .header_section div.lower_part table tr td:nth-of-type(3) {
            width: 12.84%;
            border-left: 2px solid #8B8B8B;
            vertical-align: top;
            padding-left: 17px;
        }

        .header_section div.lower_part table tr td span {
            width: 120px;
            text-align: center;
            color: white;
            display: inline-block;
            padding: 0 0 2px;
        }

        .header_section div.lower_part table tr td div {
            font-weight: 500;
            margin-top: 2px;
        }

        .header_section div.lower_part table tr td:nth-of-type(1) input[type='text'],
        .header_section div.lower_part table tr td div input[type='text'] {
            border: 1px dashed #8B8B8B;
            background-color: white;
            padding: 0 3px;
            width: 95%;
        }

        .header_section div.lower_part table tr td span input[type='text'] {
            border: none;
            background-color: white;
            padding: 0 3px;
            width: 100px;
            text-align: center;
            font-size: 14px;
        }

        .photo_section {
            margin: 20px 150px 0;
            width: 1240px;
        }

        .photo_section table{
            text-align: center;
            vertical-align:middle;
            width: 100%;
        }

        .photo_section table tr td:nth-of-type(1) {
            width: 49.2%;
            padding: 3px 0 7px;
        }

        .photo_section table tr td:nth-of-type(3){
            width: 49.2%;
            padding: 3px 0 7px;
            border-bottom: 2px solid #8B8B8B;
        }

        .photo_section table tr td > img {
            max-width: 100%;
            max-height: 100%;
            min-width: 50%;
            min-height: 50%;
        }

        .itembox {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }

        .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 100%;
            height: 100%;
            padding: 3px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
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
            min-width: 50%;
            min-height: 50%;
        }

        .itembox.chosen .photo::before {
            content: none;
        }

        .itembox > .photo > span {
            display: none;
        }

        .itembox.chosen > .photo > span {
            display: inline-block;
            position: absolute;
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
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }

        .photo_section.read td img {
            max-height: 385px;
        }

        .photo_section.write div.itembox {
            height: 385px;
        }

        .feature_section {
            margin: 30px 150px 20px;
            border-bottom: 1px solid black;
            padding: 10px 0 30px;
            width: 1240px;
        }

        .feature_section div.description {
            white-space: pre-wrap;
            color: #8B8B8B;
            font-size: large;
        }

        .feature_section textarea {
            border: 1px dashed #8B8B8B;
            width: 100%;
            color: #8B8B8B;
            font-size: large;
        }

        .spec_section {
            margin: 0 150px;
            width: 1240px;
        }

        .spec_section div {
            margin-bottom: 20px;
            color: #8B8B8B;
        }

        .spec_section table {
            width: 100%;
        }

        .spec_section table tr > td:nth-of-type(3n+1) {
            border: 1px solid #8B8B8B;
            width: 18.81%;
            padding: 10px;
            text-align: center;
            color: #8B8B8B;
        }

        .spec_section table tr > td:nth-of-type(3n+2) {
            border: 1px solid #8B8B8B;
            border-left: none;
            width: 30.37%;
            padding: 10px;
            text-align: left;
            color: #8B8B8B
        }

        .spec_section textarea {
            border: 1px dashed #8B8B8B;
            width: 100%;
            color: #8B8B8B;
        }

        .related_product_section {
            margin: 30px 150px 20px;
            width: 1240px;
            border-top: 1px solid #8B8B8B;
        }

        .related_product_section > div {
            margin: 20px 0;
            color: #8B8B8B;
        }

        .related_product_section > table {
            width: 100%;
        }

        .related_product_section > table tr td:nth-of-type(odd) {
            width: 23.85%;
            background-color: #8B8B8B;
            height: 155px;
            text-align: center;
            vertical-align: middle;
        }

        .related_product_section > table tr td:nth-of-type(odd) > img {
            max-height: 100%;
            min-height: 50%;
            max-width: 100%;
            min-width: 50%;
        }

        .related_product_section > table tr td:nth-of-type(even) {
            width: 1.53%;
        }

        .related_product_section.read td img {
            max-height: 155px;
        }

        .related_product_section.write .itembox {
            height: 155px;
        }

        .footer_section {
            margin: 30px 150px;
            width: 1240px;
        }

        .footer_section div {
            color: #8B8B8B;
        }

        .footer_section div > span {
            font-weight: 600;
        }

        .footer_section div:nth-of-type(2) > span:nth-of-type(1),
        .footer_section div:nth-of-type(2) > span:nth-of-type(2) {
            font-weight: 400;
        }

        .footer_section div > input[type='text'] {
            font-weight: 600;
            border: 1px dashed #8B8B8B;
            background-color: white;
            padding: 0 3px;
            width: 150px;
            font-size: 14px;
        }

        .footer_section.write div:nth-of-type(2) {
            margin-top: 5px;
        }


        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
            }

            .mainContent {
                padding: 0;
                background-color: #FFF !important;
                zoom: 64%;
                margin: 30px 0px 0px 10px;
                overflow-y: hidden;
            }

            .noPrint {
                display: none!important;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

     
    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <!-- header -->
    <header class="noPrint">header</header>
    <!-- header end -->

    <div class="commandbox noPrint">
        <button class="btn btn-primary" v-if="mode == 'write'" @click="preview()">Preview</button>
        <button class="btn btn-primary" v-if="mode == 'write' && fil_id != ''" @click="confirmItem()">Save and Preview</button>
        <button class="btn btn-primary" v-if="mode != 'write' && show_title == true" @click="edit()">Edit</button>
        <button class="btn btn-primary" v-if="mode != 'write' && show_title == true" @click="print_page()">Export</button>
    </div>

    <div class="mainContent">

        <div class="header_section" style="margin: 0 150px; width: 1240px;">
            <table class="upper_part">
                <tr>
                    <td>
                        <img src="images/ui/logo_dark.svg"  width="200" height="50">
                    </td>
                    <td>
                        <div>
                            <span>FELIIX</span>
                            PRODUCTS SPECIFICATIONS SHEET
                        </div>
                    </td>

                </tr>
            </table>


            <div class="middle_part read" v-if="mode != 'write'">
                <table>
                    <tr>
                        <td :style="[item.legend == '' ?  { 'visibility' : 'hidden' } : { }]">{{ item.legend }}</td>
                    </tr>
                    <tr>
                        <td :style="[item.option == '' ?  { 'visibility' : 'hidden' } : { }]">{{ item.option }}</td>
                    </tr>
                </table>
            </div>

            <div class="middle_part write" v-if="mode == 'write'">
                <table>
                    <tr>
                        <td>
                            <input type="text" placeholder="Legend" v-model="item.legend">
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <input type="text" placeholder="Option" v-model="item.option">
                        </td>
                    </tr>
                </table>
            </div>


            <div class="lower_part read" v-if="mode != 'write'">
                <table>
                    <tr>
                        <td>{{ item.code }}</td>
                        <td>
                            <span :style="[item.indoor != '' ? { 'background-color':'#00AEEF'} : { 'visibility' : 'hidden' }]">{{ item.indoor }}</span>
                            <div>{{ item.type }}</div>
                        </td>
                        <td>
                            <span :style="[item.grade != '' ? { 'background-color':'#EC008C'} : { 'visibility' : 'hidden' }]">{{ item.grade }}</span>
                            <div></div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="lower_part write" v-if="mode == 'write'">
                <table>
                    <tr>
                        <td>
                            <input type="text" placeholder="Product Code" v-model="item.code">
                        </td>
                        <td>
                            <span style="background-color: #00AEEF;">
                                <input type="text" placeholder="INDOOR/OUTDOOR" v-model="item.indoor">
                            </span>
                            <div>
                                <input type="text" placeholder="Product Type" v-model="item.type">
                            </div>
                        </td>
                        <td>
                            <span style="background-color: #EC008C;">
                                <input type="text" placeholder="HIGH-GRADE" v-model="item.grade">
                            </span>
                            <div>
                                <input type="text" placeholder="???" v-if="1==0">

                            </div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>


        <div class="photo_section read" v-if="mode != 'write'">

            <table>

                <tr>
                    <td>
                        <img :src="item.photo1"  v-if="item.photo1 != ''">
                    </td>

                    <td></td>

                    <td>
                        <img :src="item.photo2" v-if="item.photo2 != ''">
                    </td>
                </tr>
            </table>

        </div>

        <div class="photo_section write" v-if="mode == 'write'">

            <table>

                <tr>
                    <td>

                        <div :class="['itembox', (item.photo1 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_1'"  @change="onFileChangeImage($event, item, 1)" v-show="item.photo1 == ''">
                                <img v-if="item.photo1" :src="item.photo1"/>
                                <span @click="clear_photo(item, 1)">x</span>
                            </div>
                        </div>
                    </td>

                    <td></td>

                    <td>
                        <div :class="['itembox', (item.photo2 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_2'"  @change="onFileChangeImage($event, item, 2)"  v-show="item.photo2 == ''">
                                <img v-if="item.photo2" :src="item.photo2"/>
                                <span @click="clear_photo(item, 2)">x</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>


        <div class="feature_section read" v-if="mode != 'write'">
            <div class="description">{{ item.description }}</div>
        </div>

        <div class="feature_section write" v-if="mode == 'write'">
            <textarea rows="10" v-model="item.description"></textarea>
        </div>


        <div class="spec_section read" v-if="mode != 'write'">

            <div>SPECIFICATIONS:</div>

            <table>
                <template v-for="(specs, idx) in item.variation_array">
                    <tr>
                        <template v-for="(spec, index) in specs">
                            <td>{{ spec.category }}</td>
                            <td>{{ spec.value.join(',') }}</td>
                            <td v-if="index+1 < specs.length && index % 2 == 0"></td>
                        </template>
                    </tr>
                </template>
            </table>

        </div>

        <div class="spec_section write" v-if="mode == 'write'">

            <div>SPECIFICATIONS:</div>

            <textarea rows="10" v-model="item.variation"></textarea>

        </div>


        <div class="related_product_section read" v-if="mode != 'write'">

            <div>ACCESSORIES:</div>


            <table>
                <tr>
                    <td :style="[item.photo3 == '' ? { 'visibility' : 'hidden' } : {}]">
                        <img :src="item.photo3" v-if="item.photo3 != ''">
                    </td>
                    <td></td>
                    <td :style="[item.photo4 == '' ? { 'visibility' : 'hidden' } : {}]">
                        <img :src="item.photo4" v-if="item.photo4 != ''">
                    </td>
                    <td></td>
                    <td :style="[item.photo5 == '' ? { 'visibility' : 'hidden' } : {}]">
                        <img :src="item.photo5" v-if="item.photo5 != ''">
                    </td>
                    <td></td>
                    <td :style="[item.photo6 == '' ? { 'visibility' : 'hidden' } : {}]">
                        <img :src="item.photo6" v-if="item.photo6 != ''">
                    </td>
                </tr>
            </table>


        </div>


        <div class="related_product_section write" v-if="mode == 'write'">

            <div>ACCESSORIES:</div>

            <table>
                <tr>
                    <td>
                    <div :class="['itembox', (item.photo3 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_3'"  @change="onFileChangeImage($event, item, 3)"  v-show="item.photo3 == ''">
                                <img v-if="item.photo3" :src="item.photo3"/>
                                <span @click="clear_photo(item, 3)">x</span>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>
                    <div :class="['itembox', (item.photo4 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_4'"  @change="onFileChangeImage($event, item, 4)"  v-show="item.photo4 == ''">
                                <img v-if="item.photo4" :src="item.photo4"/>
                                <span @click="clear_photo(item, 4)">x</span>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>
                    <div :class="['itembox', (item.photo5 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_5'"  @change="onFileChangeImage($event, item, 5)"  v-show="item.photo5 == ''">
                                <img v-if="item.photo5" :src="item.photo5"/>
                                <span @click="clear_photo(item, 5)">x</span>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td>
                    <div :class="['itembox', (item.photo6 !== '' ? 'chosen' : '')]">
                            <div class="photo">
                                <input type="file" :id="'photo_' + item.id + '_6'"  @change="onFileChangeImage($event, item, 6)" v-show="item.photo6 == ''">
                                <img v-if="item.photo6" :src="item.photo6"/>
                                <span @click="clear_photo(item, 6)">x</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>


        <div class="footer_section read" v-if="mode != 'write'">
            <div>
                Tel No: <span>{{reserved.Tel}}</span> / Email: <span>{{reserved.Email}}</span> / Website: <span>{{reserved.Website}}</span>
            </div>
            <div>
                Copyright © <span>{{reserved.Copyright}}</span> Feliix <span>{{reserved.Feliix}}</span> All Rights Reserved — Note: <span>{{reserved.Note}}</span>
            </div>
        </div>

        <div class="footer_section write" v-if="mode == 'write'">
            <div>
                Tel No: <input type="text" v-model="reserved.Tel"> / Email: <input type="text" v-model="reserved.Email"> / Website: <input type="text" v-model="reserved.Website">
            </div>
            <div>
                Copyright © <input type="text" style="width: 40px;" v-model="reserved.Copyright"> Feliix <input type="text" style="width: 100px;" v-model="reserved.Feliix"> All Rights Reserved — Note: <input type="text" style="width: 500px;" v-model="reserved.Note">
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

<script src="js/product_spec_sheet.js"></script>

<script>
    $(".btn").click(function () {

        if ($("#collapseme").hasClass("show")) {
            $("#collapseme").removeClass("show");
        } else {
            $("#collapseme").addClass("show");
        }
    });

    window.onafterprint = (event) => {
        app.show_title = true;
    };
</script>


</html>
