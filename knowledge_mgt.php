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

        $database = new Database();
        $db = $database->getConnection();

        // for creator
        $creators = array();
        $query = "select distinct u.id, u.username from knowledge k left join user u on k.create_id = u.id WHERE u.status = 1 and k.status = 1 ORDER BY username ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $creators[] = array(
                "id" => $row['id'],
                "username" => $row['username'],
            );
        }
        
        // for updator
        $updators = array();
        $query = "select distinct u.id, u.username from knowledge k left join user u on k.updated_id = u.id WHERE u.status = 1 and k.status = 1 ORDER BY username ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $updators[] = array(
                "id" => $row['id'],
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
        $(function(){
            $('header').load('include/header.php');

            dialogshow($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'));

            $('.container').click(function(){
                $('.list_function .dialog').removeClass('show');
            })
            
        })
    </script>

    <style>
        body.primary header > .headerbox {
            background-color: #006BA6;
        }

        .mainContent > .block {
            display: block;
            width: 92vw;
            margin: 30px auto 0;
            border: none;
        }

        .list_function .dialog{
            text-align: left;
        }

        .dialog .formbox .half{
            width: 48%;
        }

        .list_function .new_project a.add {
            width: 40px;
            height: 40px;
            line-height: 30px;
        }

        .list_function .new_project a.filter {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 40px;
            height: 40px;
            line-height: 30px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.sort {
            font-size: 0;
            background-color: var(--fth04);
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 40px;
            height: 40px;
            line-height: 30px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .pagenation {
            margin-top: 9px;
        }

        .list_function .pagenation a {
            color: #7ACCC8;
            border-color: #7ACCC8;
        }

        .list_function .pagenation a:hover {
            background-color: #7ACCC8;
            color: #FFF;
        }

        .container {
            width: 92vw;
            min-height: calc(100vh - 150px);
            margin: 15px auto 0;
            background-color: #EBEBEB;
        }

        .container ul {
            width: 100%;
            display: flex;
        }

        .container ul li {
            width: 25%;
            padding: 15px 10px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container ul.head li {
            background-color: #7ACCC8;
            font-weight: 500;
        }

        .container ul li {
            border-bottom: 2px solid black;
            border-right: 2px solid black;
        }

        .container ul:nth-of-type(1) li {
            border-top: 1px solid black;
        }

        .container ul li:first-of-type {
            border-left: 1px solid black;
        }

        .container ul li:last-of-type {
            border-right: 1px solid black;
        }

        .container ul:last-of-type li {
            border-bottom: 1px solid black;
        }

        .container ul li:last-of-type i {
            font-size: 24px;
            margin: 0 4px;
            cursor: pointer;
        }

        .container ul li:last-of-type a {
            font-size: 24px;
            margin: 0 4px;
            cursor: pointer;
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

        <div class="block">
            <div class="list_function">

                <!-- 點擊後，跳轉到 knowledge_add.php 去建立新知識 -->
                <div class="new_project">
                    <a class="add" title="Create New Knowledge" href="knowledge_add" target="_blank"></a>
                </div>


                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Title</dt>
                                <dd>
                                    <input type="text" v-model="title">
                                </dd>

                                <dt>Creator</dt>
                                <dd>
                                    <div style="text-align: left; font-size: 12px;">
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                                data-width="100%" id="creator" v-model="creator">
                                                <?php foreach ($creators as $user) { ?>
                                                    <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                                <?php } ?>
                                            </select>
                                    </div>
                                </dd>

                                <dt>Updater</dt>
                                <dd>
                                    <div style="text-align: left; font-size: 12px;">
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" id="updater" v-model="updater">
                                        
                                            <?php foreach ($updators as $user) { ?>
                                                <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                            <?php } ?>

                                        </select>
                                            
                                    </div>
                                </dd>

                                <dt style="margin-bottom:-18px;">Created Time</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd>
                                        <input type="date">
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>to</dt>
                                    <dd>
                                        <input type="date">
                                    </dd>
                                </div>

                                <dt style="margin-bottom:-18px;">Last Updated Time</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd>
                                        <input type="date">
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>to</dt>
                                    <dd>
                                        <input type="date">
                                    </dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a
                                    class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green"
                                                                                            @click="apply_filters()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 排序 -->
                <div class="new_project">
                    <a class="sort"></a>
                    <div id="order_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt1">
                                            <option value=""></option>
                                            <option value="1">
                                                Title
                                            </option>
                                            <option value="2">
                                                Creator
                                            </option>
                                            <option value="3">
                                                Updater
                                            </option>
                                            <option value="4">
                                                Created Time
                                            </option>
                                            <option value="5">
                                                Last Updated Time
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord1">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt>2nd Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt2">
                                            <option value=""></option>
                                            <option value="1">
                                                Title
                                            </option>
                                            <option value="2">
                                                Creator
                                            </option>
                                            <option value="3">
                                                Updater
                                            </option>
                                            <option value="4">
                                                Created Time
                                            </option>
                                            <option value="5">
                                                Last Updated Time
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord2">
                                            <option value="1">
                                                Ascending
                                            </option>
                                            <option value="2">
                                                Descending
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_orders()">Cancel</a><a
                                    class="btn small" @click="clear_orders()">Clear</a> <a class="btn small green"
                                                                                           @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>

        </div>

        <div class="container">

            <ul class="head">
                <li>TITLE</li>
                <li>CREATED ON</li>
                <li>LAST UPDATE</li>
                <li>ACTIONS</li>
            </ul>

            <ul v-for='(receive_record, index) in displayedPosts'>
                <li>{{ receive_record.title }}</li>
                <li>{{ receive_record.created_at }} {{ receive_record.created_by }}</li>
                <li>{{ receive_record.updated_at }} {{ receive_record.updated_by }}</li>
                <li>
                    <a class="fas fa-edit" :href="'knowledge_add?id=' + receive_record.id" target="_blank"></a>
                    <i class="fas fa-trash" @click="deleteRow(receive_record)"></i>
                </li>
            </ul>


        </div>


    </div>


</div>

</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/knowledge_mgt.js"></script>
</html>
