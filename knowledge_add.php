<?php

 date_default_timezone_set('Asia/Taipei');
 $date = date('d');
 $show0 = false;

if($date % 2 == 0)
    $show0 = true;
?>

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
    <title>Knowledge Management</title>
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
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">
    

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>
    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
    </script>

    <style>
        body.primary header > .headerbox {
            background-color: #006BA6;
        }

        .container{
            width: 92vw;
            margin: 30px auto;
            background-color: #EBEBEB;
            display: flex;
            justify-content: center;
            align-items: stretch;
            flex-wrap: wrap;
            padding: 5vw 0;
        }

        .left_box{
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            padding: 20px;
        }

        .right_box {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px 70px;
        }

        .left_box input[type="text"], .left_box select {
            width: 515px;
            border: 1px solid #555555;
            border-radius: 7px;
            margin: 14px 0;
            height: 50px;
        }

        .left_box input[type="text"]::placeholder {
            color: black;
            opacity: 1;
        }

        .left_box select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
        }

        .left_box .filebox {
            width: 515px;
            border: 1px solid #555555;
            border-radius: 7px;
            margin: 14px 0;
            height: 50px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1vw 0 9px;
        }

        .left_box .filebox span{
            font-size: 20px;
            font-weight: 400;
        }

        .left_box .filebox input[type="file"]{
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            opacity: 0;
            z-index: 2;
        }

        .left_box .durationbox {
            width: 515px;
            margin: 14px 0;
            height: 50px;
            display: flex;
            justify-content: space-between;
        }

        .left_box .durationbox input[type='text'] {
            width: 60%;
            margin: 0;
        }

        .left_box .durationbox select {
            width: 35%;
            margin: 0;
        }


        .right_box .coverimg_box {
            width: 340px;
            height: 385px;
            border: 1px solid #555555;
            border-radius: 7px;
            position: relative;
            padding: 2px;
            margin: 14px 0;
        }

        .right_box .coverimg_box img {
            width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .right_box .coverimg_box div:nth-of-type(1) {
            display: block;
            width: 200px;
            height: 55px;
            background-color: #CCC;
            border: 1px solid #555555;
            border-radius: 7px;
            position: absolute;
            text-align: center;
            vertical-align: middle;
            line-height: 55px;
            font-weight: 500;
            left: 70px;
            top: 170px;
        }

        .right_box .coverimg_box div:nth-of-type(1) input[type='file']{
            position: absolute;
            display: block;
            width: 200px;
            height: 55px;
            z-index: 2;
            top: -1px;
            opacity: 0;
        }

        .right_box .coverimg_box div:nth-of-type(2){
            display: block;
            margin: 8px auto 5px;
            width: 46px;
            height: 46px;
            border: 2px dashed #EA0029;
            border-radius: 23px;
            line-height: 37px;
            text-align: center;
            color: #EA0029;
            font-size: 30px;
            font-weight: 400;
            cursor: pointer;
            position: absolute;
            top: 167px;
            left: 150px;
        }

        .right_box .coverimg_box div:nth-of-type(1){
            display: block;
        }

        .right_box .coverimg_box.chosen div:nth-of-type(1){
            display: none;
        }

        .right_box .coverimg_box div:nth-of-type(2){
            display: none;
        }

        .right_box .coverimg_box.chosen div:nth-of-type(2){
            display: block;
        }

        .right_box button{
            width: 340px;
            height: 50px;
            background-color: #007CFB;
            border-radius: 7px;
            border: none;
            color: white;
            font-weight: 500;
            margin: 14px 0;
        }

    </style>

</head>

<body class="primary">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent" style="text-align: center;" id="app">
        <!-- mainContent為動態內容包覆的內容區塊 -->

        <div class="container">

            <div class="left_box">

                <input type="text" placeholder="Title" v-model="title">

                <!-- 在資料庫中會新建立一張資料表，我會直接在資料表中新增/修改/刪除 tag，這邊只需要把該資料表中的 tag 引入即可 -->
                <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="96%" title="Choose Category..." id="category" v-model="category">
                    <optgroup v-for="(group, index) in tags" :label="group.gtag">
                        <option v-for="tag in group.tags" :value="tag.tag">{{tag.tag}}</option>
                    </option>

                </select>


                <!-- 選擇權限的select之中，裡面第一部分的選項是固定的，是讓使用者可以一次選擇全部的人或整個部門，第二部分的選擇則是要載入當前系統已註冊且enabled=True但尚未被刪除的人進來當作選項 -->
                <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="96%" title="Choose Access..." id="access" v-model="access">

                    <optgroup label="By Group">
                        <option value="All">All</option>
                        <option value="Sales Department">Sales Department</option>
                        <option value="Lighting Department">Lighting Department</option>
                        <option value="Office Department">Office Department</option>
                        <option value="Design Department">Design Department</option>
                        <option value="Engineering Department">Engineering Department</option>
                        <option value="Admin Department">Admin Department</option>
                        <option value="Store Department">Store Department</option>
                    </optgroup>

                    <optgroup label="By Person">
                        <option v-for="user in users" :value="user.username">{{user.username}}</option>
                    </optgroup>

                </select>


                <select v-model="type">
                    <option value="">Choose Type...</option>
                    <option value="file">File</option>
                    <option value="link">Web Text</option>
                    <option value="video">Web Video</option>
                </select>

                <div class="filebox" v-show="type == 'file'">
                    <span>File</span>
                    <span><i class="fas fa-paperclip"></i></span>
                    <input type="file" id="file1">
                </div>

                <input type="text" placeholder="Website Link" v-model="link"  v-if="type == 'link' || type == 'video'">

                <div class="durationbox">
                    <select v-model="watch">
                        <option value="read">Read</option>
                        <option value="watch">Watch</option>
                    </select>

                    <input type="text" placeholder="Duration (in minutes)" v-model="duration">
                </div>

            </div>


            <div class="right_box">

                <!-- 當有圖片被選擇時，則需要在 coverimg_box 的結構中，加入 class="chosen" -->
                <div :class="['coverimg_box', (url !== null ? 'chosen' : '')]">
                    <img v-if="url" :src="url" />
                    <div>Cover Image
                        <input type="file" id="photo" name="photo"  @change="onFileChange($event)">
                    </div>

                    <div @click="clear_photo()">x</div>
                </div>

                <button v-if="id == 0" @click="save">Upload</button>
                <button v-if="id != 0" @click="edit">Save</button>

            </div>


        </div>


    </div>


</div>

</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/knowledge_add.js"></script>
</html>
