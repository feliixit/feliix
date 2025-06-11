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
            $user_id = $decoded->data->id;
$username = $decoded->data->username;


if($decoded->data->limited_access == true)
header( 'location:index' );

$position = $decoded->data->position;
$department = $decoded->data->department;

// 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
$test_manager = $decoded->data->test_manager;

$access6 = true;



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
    <title>Signature Codebook</title>
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
    <script src="js/moment.js"></script>

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
            } else if (target == 2) {
                $("#Modal_2").toggle();
            } else if (target == 3) {
                $("#Modal_3").toggle();
            } else if (target == 4) {
                $("#Modal_4").toggle();
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
            background-image: url(images/ui/icon_form_select_arrow_green.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
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
            margin-bottom: 5px;
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
            background-color: var(--sec03);
            background-image: url(images/ui/btn_add_blue.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 35px;
            height: 35px;
            line-height: 35px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            margin-right: 25px;
            flex-grow: 0;
            flex-shrink: 0;
            margin-top: 5px;
        }

        .list_function .searching input {
            font-size: 15px;
            padding: 4px 7px;
            height: 34px;
            width: 201px;
            margin-top: 5px;
        }

        .list_function .searching i {
            color: var(--sec03);
            font-size: 22px;
        }

        .list_function .pagenation {
            float: none;
        }

        .list_function .pagenation a {
            color: var(--sec03);
            border-color: var(--sec03);
        }

        .list_function .pagenation a:hover {
            background-color: var(--sec03);
            color: #FFF;
        }


        body input.alone[type=radio]::before {
            font-size: 25px;
        }
        
        .itembox {
            display: inline-block;
            margin: 5px 0;
        }

        .itembox .photo {
            border: 1px dashed #3FA4F4;
            width: 90px;
            height: 90px;
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

        .itembox .photo > input[type='file'] {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
        }

        .itembox .photo > img {
            max-width: 100%;
            max-height: 100%;
        }

        .itembox.chosen .photo::before {
            content: none;
        }

        .itembox .photo > div {
            display: none;
        }

        .itembox.chosen .photo > div {
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
            position: absolute;
            top: 18px;
            right: -50px;
        }


    </style>

</head>

<body class="third">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Signature Codebook</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Signature Codebook</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create" @click="open_review"></a>

                            <div class="searching">
                                <input type="text" placeholder="Searching Keyword Here" v-model="fil_keyword">
                                <button style="border: none;" @click="search()"><i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();"
                               v-bind:style="[page==pg ? { 'background':'var(--sec03)', 'color': 'white'} : { }]">{{ pg
                                }}</a>
                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next
                                10</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Name</li>
                        <li>Position</li>
                        <li>Phone Number</li>
                        <li>Email</li>
                        <li>Signature</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone" :value="record.id"
                                   v-model="proof_id">
                        </li>
                        <li>{{ record.name }}</li>
                        <li>{{ record.position }}</li>
                        <li>{{ record.phone }}</li>
                        <li>{{ record.email }}</li>
                        <li><img v-if="record.url" :src="record.url" width="100px" height="100px"></li>
                    </ul>

                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">
                    <a class="btn green" @click="edit()">Edit</a>
                    <a class="btn green" @click="remove()">Delete</a>
                </div>

            </div>

            <div class="box-content" style="border-top: 2px solid var(--sec03); padding-top: 30px;" v-if="toggle">

                <form>
                    <ul>
                        <li><b>Name</b></li>
                        <li><input type="text" style="width:100%" v-model="signature_name"></li>

                        <li><b>Position</b></li>
                        <li><input type="text" style="width:100%" v-model="signature_position"></li>

                        <li><b>Phone Number</b></li>
                        <li><input type="text" style="width:100%" v-model="signature_phone"></li>

                        <li><b>Email</b></li>
                        <li><input type="text" style="width:100%" v-model="signature_email"></li>

                        <li><b>Signature</b></li>
                        <li>
                            <div :class="['itembox', (signature_url != '' ? 'chosen' : '')]">
                            <!-- <div class="itembox"> -->
                                <div class="photo">
                                    <input type="file" name="sig_image"
                                           @change="onSigFileChangeImage($event)"
                                           id="sig_image">
                                    <img v-if="signature_url" :src="signature_url"/>
                                    <div @click="clear_sig_photo()" v-if="signature_url">x</div>
                                </div>
                            </div>
                        </li>

                    </ul>
                    <div class="btnbox">
                        <a class="btn" @click="reset" v-if="proof_id == 0">Reset</a>
                        <a class="btn" @click="edi_reset" v-if="proof_id != 0">Reset</a>
                        <a class="btn" @click="save" v-if="proof_id == 0">Save</a>
                        <a class="btn" @click="edit_save" v-if="proof_id != 0">Save</a>
                    </div>
                </form>

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
<script src="js/signature_codebook.js"></script>
</html>