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


try {
    // decode jwt
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $username = $decoded->data->username;

        if($decoded->data->limited_access == true)
                header( 'location:index' );

        $database = new Database();
        $db = $database->getConnection();

        // for tags
        $tag_results = array();
        $query = "SELECT id,
                        `gtag`, 
                        tag, 
                        sn
                        FROM tags
                        WHERE status <> -1 order by sn
                        ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $gtag = "";
        $group_tags = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //group tags by gtag put tags in array
            if($gtag != $row['gtag']){
                if($gtag != ""){
                    $tag_results[] = array(
                        "gtag" => $gtag,
                        "tags" => $group_tags,
                    );
                }
                $gtag = $row['gtag'];
                $group_tags = array();
            }
            $group_tags[] = array(
                "id" => $row['id'],
                "tag" => $row['tag'],
                "sn" => $row['sn'],
            );
        }

        //add last group
        $tag_results[] = array(
            "gtag" => $gtag,
            "tags" => $group_tags,
        );


        // for users
        $user_results = array();
        $query = "SELECT username FROM user WHERE status = 1 ORDER BY username
                        ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_results[] = array(
                "username" => $row['username'],
            );
        }
        

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
    <title>Add/Edit Knowledge</title>
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
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">
    

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
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
            background-color: #7ACCC8;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.primary header nav a, body.primary header nav a:link {
            color: #000;
        }

        body.primary header nav a:hover {
            color: #333;
        }

        body.primary header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.primary header nav ul.info {
            margin-bottom: 0;
        }

        body.primary header nav ul.info b {
            font-weight: bold;
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
            padding: 0 12px 0 9px;
        }

        .left_box .filebox span{
            font-size: 16px;
            font-weight: 500;
            margin: 0 5px;
            display: block;
        }

        .left_box .filebox input[type="file"]{
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            opacity: 0;
            z-index: 2;
        }

        .left_box .filebox div:nth-of-type(1){
            font-size: 16px;
            font-weight: 500;
            margin: 0 5px;
            display: block;
            max-width: calc( 100% - 50px);
            overflow-x: hidden;
            white-space: nowrap;
        }

        .left_box .filebox div:nth-of-type(2){
            display: block;
            width: 24px;
            height: 24px;
            border: 2px dashed #EA0029;
            border-radius: 12px;
            line-height: 15px;
            text-align: center;
            color: #EA0029;
            font-size: 20px;
            font-weight: 400;
            cursor: pointer;
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

        .left_box div.dropdown.bootstrap-select.show-tick{
            max-width: 515px;
            border: 1px solid #555555;
            border-radius: 7px;
            margin: 14px 0;
            height: 50px;
        }

        .left_box button.btn.dropdown-toggle.btn-light{
            background-color: transparent;
            height: 100%;
            border-radius: 7px;
        }

        .left_box div.filter-option{
            display: flex;
            align-items: center;
        }

        .left_box div.filter-option-inner-inner{
            color: black;
            font-weight: 500;
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
                <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="100%" title="Choose Category..." id="category" v-model="category">
                    <?php foreach ($tag_results as $tags) { 
                        $opts = $tags["tags"];
                        ?>
                        <optgroup label="<?php echo $tags["gtag"]; ?>">
                            <?php foreach ($opts as $opt) { ?>
                                <option value="<?php echo $opt["tag"]; ?>"><?php echo $opt["tag"]; ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>


                <!-- 選擇權限的select之中，裡面第一部分的選項是固定的，是讓使用者可以一次選擇全部的人或整個部門，第二部分的選擇則是要載入當前系統已註冊且enabled=True但尚未被刪除的人進來當作選項 -->
                <select class="selectpicker" multiple data-live-search="true" data-size="8" data-width="100%" title="Choose Access..." id="access" v-model="access">

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
                        <?php foreach ($user_results as $user) { ?>
                            <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                        <?php } ?>
                    </optgroup>

                </select>


                <select v-model="type">
                    <option value="">Choose Type...</option>
                    <option value="file">File</option>
                    <option value="link">Web Text</option>
                    <option value="video">Web Video</option>
                </select>

                <div class="filebox" v-show="type == 'file'">
                    <!-- 沒選擇任何檔案時，下方的 span 和 input 則是 display: block; 但如果有選擇一個檔案時，下方的 span 和 input 則是 display: none; -->
                    <span v-show="filename == ''">File</span>
                    <span><i class="fas fa-paperclip" v-if="filename == ''"></i></span>
                    <input type="file" id="file1" name="file1" @change="onFileChange1($event)" v-show="filename == ''" />
                    
                    <!-- 沒選擇任何檔案時，下方的 div 則是 display: none; 但如果有選擇一個檔案時，下方的 div 則是 display: block; -->
                    <!-- 使用者點了 x 的 div 時，則代表要清空所選擇的檔案 -->
                    <div v-if="filename != ''">{{ filename }}</div>
                    <div v-if="filename != ''" @click="clear_photo1()">x</div>
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
                <div :class="['coverimg_box', ((url != null && url != '') ? 'chosen' : '')]">
                    <img v-if="url" :src="url" />
                    <div>Cover Image
                        <input type="file" accept="image/*" id="photo" name="photo"  @change="onFileChange($event)">
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
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/knowledge_add.js"></script>
</html>
