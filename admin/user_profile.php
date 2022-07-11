<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';


use \Firebase\JWT\JWT;

$access6 = false;

try {
        // decode jwt
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;
            $username = $decoded->data->username;


            // 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41), Stefanie Mika C. Santos(99)
            if($username == "Dennis Lin" ||  $username == "Kristel Tan" ||  $username == "dereck" || $username == "Kuan" || $username == "Stefanie Mika C. Santos")
            {
                $access6 = true;
            }

            if($access6 == false)
                header('location:../default');

        }
        catch (Exception $e){

            header('location:../index');
        }


        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        //    header( 'location:index.php' );
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){

        header( 'location:../index' );
    }

?>

<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="../images/favicon.ico"/>
    <link rel="Bookmark" href="../images/favicon.ico"/>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="../images/iosicon.png"/>

    <!-- SEO -->
    <title>User Profile Management</title>
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
    <link rel="stylesheet" type="text/css" href="../css/default.css"/>
    <link rel="stylesheet" type="text/css" href="../css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="../css/case.css"/>
    <link rel="stylesheet" type="text/css" href="../css/mediaqueries.css"/>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="../js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="../js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })
    </script>

</head>

<style>

    .box-content > ul > li:nth-of-type(even){
        padding-bottom: 20px;
    }

    .box-content > ul > li input[type='text'], .box-content > ul > li textarea{
        width: 100%;
    }

    .box-content div div button{
        min-width: 95px;
    }

    .box-content ul li img.photo {
        width: 110px;
        height: 110px;
        background-repeat: no-repeat;
        background-size: cover;
        border-radius: 90px;
        display: inline-block;
        background-color: var(--gray03);
        margin-right: 30px;
        background-position: center;
    }

    .tablebox > ul > li:nth-of-type(n+2) {
        font-size:10px;
    }

    .tablebox ul li a.man {
        width: 40px;
        height: 40px;
        background-size: cover;
        background-position: center;
        vertical-align: middle;
        border-radius: 50px;
        transition: .5s;
        cursor: default;
    }

    @media screen and (min-width: 0px) and (max-width: 767px) {
        #my-content {
            display: none;
        }

        /* hide it on small screens */
    }

    @media screen and (min-width: 768px) and (max-width: 1024px) {
        #my-content {
            display: block;
        }

        /* show it elsewhere */
    }
</style>

<body class="cyan">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="user">User</a>
            <a class="tag F focus">User Profile</a>
            <a class="tag B" href="department">Department</a>
            <a class="tag C" href="position">Position</a>
            <a class="tag D" href="leave_flow">Leave Flow</a>
            <a class="tag E" href="expense_flow">Expense Flow</a>
        </div>
        <!-- Blocks -->
        <div class="block F focus">
            <h6>User Profile Management</h6>
            <div class="box-content">
                <div class="box-content" v-if="isEditing">
                    <ul>
                        <li>
                            <b>Employee Name</b>
                        </li>
                        <li>
                            <input type="text" v-model="record.username" maxlength="255" readonly>
                        </li>

                        <li>
                            <b>Photo</b>
                        </li>
                        <li style="display: flex; align-items: center;">
                            <img class="photo" v-if="pic_url !== ''" :src="pic_url">
                         
                            <input id="photo" type="file" accept="image/*" style="width: calc( 100% - 140px);" @change="onFileChange($event)">
                        </li>

                        <li>
                            <b>Contact Number</b>
                        </li>
                        <li>
                            <input type="text" v-model="tel" maxlength="255">
                        </li>

                        <li>
                            <b>Address</b>
                        </li>
                        <li>
                            <textarea rows=2 v-model="address" maxlength="255" style="resize: none;"></textarea>
                        </li>
                    </ul>

                    <div>
                        <div>
                            <button type="button" @click="cancelReceiveRecord($event)"><p>CANCEL</p></button>
                            <button type="button" @click="editReceiveRecord($event)"><p>SAVE</p></button>
                        </div>
                    </div>
                </div>


                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Employee Name</li>
                        <li>Photo</li>
                        <li>Contact Number</li>
                        <li style="max-width: 35%;">Address</li>
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.index" :true-value="1"
                                   v-model:checked="record.is_checked"></li>
                        <li>{{record.username}}</li>
                        <li v-if="record.pic_url !== ''"><a class="man" :style="'background-image: url(../images/man/' + record.pic_url + ');'"></a></li>
                        <li v-if="record.pic_url == ''"><a class="man"></a></li>
                        <li>{{record.tel}}</li>
                        <li>{{record.address}}</li>
                    </ul>
                

                </div>
                <div class="btnbox">
                    <a class="btn" @click="editRecord()">Modify Profile</a>
                    <a class="btn" @click="RemoveReceiveRecord()">Remove Photo</a>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="../js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="../js/admin/user_profile.js"></script>
</html>
