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

        // for creator
        $creators = array();
        $query = "select distinct u.id, u.username from knowledge k left join user u on k.create_id = u.id WHERE u.status = 1 and k.status = 1 ORDER BY username ";
        // $query = "select distinct u.id, u.username from  user u WHERE u.status = 1  ORDER BY username ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $creators[] = array(
                "id" => $row['id'],
                "username" => $row['username'],
            );
        }
        
        // for updater
        $updaters = array();
        $query = "select distinct u.id, u.username from knowledge k left join user u on k.updated_id = u.id WHERE u.status = 1 and k.status = 1 ORDER BY username ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $updaters[] = array(
                "id" => $row['id'],
                "username" => $row['username'],
            );
        }

        // for tags
        $tags = array();
        $query = "select tag from tags where status <> -1 ORDER BY sn ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = array(
                "tag" => $row['tag'],
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
    <title>Knowledge List</title>
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

            dialogshow($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'));

            $('.container').click(function(){
                $('.list_function .dialog').removeClass('show');
            })
            
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

        .mainContent {
            min-height: calc(100vh + 100px);
        }

        .mainContent > .block {
            display: block;
            width: 92vw;
            margin: 30px auto 0;
            border: none;
        }

        .block .formbox ul > li {
            display: list-item;
            padding: 0;
            font-weight: 400;
            border-right: none;
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
            margin: 15px auto 0;
            background-color: #EBEBEB;
            display: flex;
            align-items: stretch;
            padding: 25px 0;
            justify-content: space-evenly;
            flex-wrap: wrap;
            max-width: 100%;
            min-height: calc(100vh - 150px);
        }

        .itembox {
            width: 320px;
            height: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 25px 5px;
        }

        .itembox li:nth-of-type(1) {
            width: 220px;
            height: 290px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .itembox li:nth-of-type(1) img {
            width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .itembox li:nth-of-type(2) {
            color: #8B8B89;
            padding: 15px 0 0;
            font-size: 12px;
            font-weight: 400;
            margin-bottom: -3px;
        }

        .itembox li:nth-of-type(3) {
            font-weight: 500;
            font-size: 16px;
        }

        .itembox li:nth-of-type(3) a{
            color: black;
            text-decoration: none;
        }

        .itembox li:nth-of-type(4) {
            color: #8B8B89;
            font-size: 12px;
            font-weight: 400;
            padding: 7px 0 0;
        }

        .itembox li:nth-of-type(5) {
            color: #8B8B89;
            font-size: 12px;
            font-weight: 400;
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
                                <dt>Creator</dt>
                                <dd>
                                    <div style="text-align: left; font-size: 12px;">
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" id="creator" v-model="fil_creator">
                                        <?php foreach ($creators as $user) { ?>
                                                <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </dd>

                                <dt style="margin-bottom:-18px;">Created Time</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd>
                                        <input type="date" v-model="fil_create_from">
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>to</dt>
                                    <dd>
                                        <input type="date" v-model="fil_create_to">
                                    </dd>
                                </div>

                                <dt>Title</dt>
                                <dd>
                                    <input type="text" v-model="fil_title">
                                </dd>

                                <dt>Category</dt>
                                <dd>
                                    <div style="text-align: left; font-size: 12px;">
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                                data-width="100%" id="tag" v-model="fil_tag">
                                            <?php foreach ($tags as $tag) { ?>
                                                <option value="<?php echo $tag["tag"]; ?>"><?php echo $tag["tag"]; ?></option>
                                            <?php } ?>
                                            </select>
                                    </div>
                                </dd>

                                <dt>Type</dt>
                                <dd>
                                    <select v-model="fil_type">
                                        <option value=""></option>
                                        <option value="file">File</option>
                                        <option value="link">Web Text</option>
                                        <option value="video">Web Video</option>
                                    </select>
                                </dd>

                                <dt>Viewing Way</dt>
                                <dd>
                                    <select v-model="fil_watch">
                                        <option value=""></option>
                                        <option value="read">Read</option>
                                        <option value="watch">Watch</option>
                                    </select>
                                </dd>

                                <dt style="margin-bottom:-18px;">Required Time Length (in minutes)</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd>
                                        <input type="number" v-model="fil_dur_from">
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>to</dt>
                                    <dd>
                                        <input type="number" v-model="fil_dur_to">
                                    </dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a
                                    class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green"
                                                                                            @click="apply_filters(1)">Apply</a>
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
                                                Required Time Length (in minutes)
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
                                                Required Time Length (in minutes)
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
                       v-bind:style="[pg == page ? { 'background':'#7ACCC8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>

        </div>
        <div class="container">

            <!-- 利用迴圈 套用 ul=itembox 這個結構，來建立出每一則知識的區塊 -->
            <ul class="itembox" v-for="(rec, index) in receive_records" @click="select(rec.link);">
                <li :style='{"background-color":colors[index%7]}'>
                    <img :src="rec.cover">
                </li>

                <li>
                    Title:
                </li>

                <li>
                    <a :href="rec.link" target="_blank" v-if="(rec.type == 'link' || rec.type == 'video') && rec.link != ''">{{rec.title}}</a>
                    <template v-if="(rec.type == 'link' || rec.type == 'video') && rec.link == ''">{{rec.title}}</template>
                    <a :href="encodeURI(rec.attach)" v-if="rec.type == 'file' && rec.attach != ''">{{rec.title}}</a>
                    <template v-if="rec.type == 'file' && rec.attach == ''">{{rec.title}}</template>
                </li>

                <li>
                    <span class="category">{{rec.category.join(', ')}}</span>
                    <span> // </span>
                    <span class="duration">{{ rec.duration_str }} {{ rec.watch }}</span>
                </li>

                <li>
                    ({{rec.created_by}} at {{rec.created_at.substr(0,10)}})
                </li>
            </ul>

        </div>


    </div>


</div>

</body>

<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/knowledge_display.js"></script>

</html>
