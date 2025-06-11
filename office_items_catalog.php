<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    if($decoded->exp < time())
{
header( 'location:index' );
}

$user_id = $decoded->data->id;
$username = $decoded->data->username;

$position = $decoded->data->position;
$department = $decoded->data->department;


if($decoded->data->limited_access == true)
                header( 'location:index' );

$database = new Database();
$db = $database->getConnection();


$access6 = false;

$query = "SELECT * FROM access_control WHERE office_items LIKE '%" . $username . "%' ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $access6 = true;
    }


    // if ($access6 == false)
    //     header('location:index');

}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

header('location:index');
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
    <title>Office Items Catalog</title>
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


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
        })



    </script>

    <style>
        body.gray   header > .headerbox{
            background-color: #707071;
        }

        body.gray select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.gray header nav a, body.gray header nav a:link{
            color: #000;
        }

        body.gray header nav a:hover{
            color: #333;
        }

        body.gray header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.gray li>input, body.gray td>input {
            background-color: #fff;
            border: 1px solid #ced4da;
        }

        body.gray header nav ul.info{
            margin-bottom: 0;
        }

        body.gray header nav ul.info b{
            font-weight: bold;
        }

        body.gray .mainContent > .tags a {
            background-color: #E0E0E0;
            border-color: #707071;
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            color: #000000;
        }

        body.gray .mainContent > .block {
            border-color: #707071;
            border-left: none;
            border-right: none;
            border-bottom: none;
        }

        body.gray .mainContent > .tags a.focus {
            background-color: #707071;
            color: #FFFFFF;
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

        .mainContent ul {
            margin: 0;
            border-bottom: 1px solid #E2E2E2;
            background-color: #F7F7F7;
            display: flex;
            align-items: center;
        }

        .mainContent ul.variation_list {
            align-items: flex-start;
            border-bottom: none;
            background-color: #F0F0F0;
            margin-bottom: 20px;
        }

        .mainContent ul.variation_list h6 {
            text-align: center;
        }

        .mainContent ul.variation_list li:first-of-type {
            min-width: 170px;
        }

        .mainContent ul.variation_list select {
            margin-bottom: 10px;
        }

        .mainContent ul li {
            display: table-cell;
            text-decoration: none;
            padding: 10px;
        }

        .mainContent ul li:first-of-type {
            width: 20vw;
            min-width: 150px;
            text-align: center;
            flex-grow: 0;
            flex-shrink: 0;
        }

        .mainContent ul li:nth-of-type(2) {
            flex-grow: 1;
            flex-shrink: 1;
        }

        .mainContent ul li > input[type='text'] + i{
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

        #tb_main_category {
            width: 100%;
        }

        #tb_main_category thead tr th {
            min-width: 100px;
        }

        #tb_main_category thead tr th:first-of-type {
            min-width: 50px;
        }

       #tb_main_category thead tr th:last-of-type {
            min-width: 130px;
        }

        #tb_main_category i{
            font-size: 24px;
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

        .heading-and-btn {
            padding: 0 0 10px;
            border-bottom: none;
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

        .heading-and-btn ul li:nth-of-type(1) input {
            border:none;
            border-bottom: 2px solid #C0C0C0;
            border-radius: 0;
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


        .list_function {
            width: 100%;
            padding: 0 10px 10px;
        }

        .list_function > .left_function {
            float: left;
            margin: 3px 20px 0 0;
            display: flex;
            align-items: center;
        }


        .list_function > .left_function > select {
            font-size: 14px;
            font-weight: 500;
            padding: 3px 25px 3px 10px;
            border-radius: 3px;
            width: 200px;
            height: 29px;
            border: 1px solid #999;
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
            margin-left: 10px;
        }

        .list_function > .left_function > select:nth-of-type(1) {
            margin-left: 0;
        }

        .list_function > .left_function > select:nth-of-type(4) {
            width: 320px;
        }

        .list_function > .left_function > button {
            height: 29px;
            width: 29px;
            padding: 2px;
            margin: 0 5px;
            cursor: pointer;
            vertical-align: middle;
            border: 1px solid #999;
            border-radius: 5px;
            background-color: #fff;
        }

        .block .tablebox {
            width: 100%;
            padding: 5px 10px 10px;
            border: none;
        }

        div.tablebox table {
            width: 100%;
        }

        div.tablebox thead tr th {
            background-color: #BBB;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            font-weight: 500;
            color: #333;
            min-width: 50px;
            border: 1px solid #999999;
            border-left: none;
        }

        div.tablebox thead tr th:first-of-type {
            border-left: 1px solid #999999;
        }

        div.tablebox tbody tr:nth-of-type(2n) td {
            background-color: #DDD;
        }

        div.tablebox tbody tr:hover:nth-of-type(2n) td {
            background-color: var(--orange01);
        }

        div.tablebox tbody tr td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            font-weight: 300;
            color: #333333;
            min-width: 50px;
            border-right: 1px solid #AAB3C1;
            border-bottom: 1px solid #AAB3C1;
            height: 100px;
        }

        div.tablebox tbody tr td:first-of-type {
            border-left: 1px solid #AAB3C1;
        }

        div.tablebox tbody tr td:nth-of-type(1), div.tablebox tbody tr td:nth-of-type(3), div.tablebox tbody tr td:nth-of-type(5), div.tablebox tbody tr td:nth-of-type(7) {
            width: 70px;
        }

        div.tablebox tbody tr td:nth-of-type(2), div.tablebox tbody tr td:nth-of-type(4), div.tablebox tbody tr td:nth-of-type(6), div.tablebox tbody tr td:nth-of-type(9) {
            width: 250px;
        }

        div.tablebox tbody tr td:nth-of-type(8) {
            width: 400px;
        }

        div.tablebox tbody tr td:nth-of-type(10) {
            width: 240px;
        }

        div.tablebox tbody tr td:nth-of-type(10) img {
            max-width: 100px;
            max-height: 100px;
        }

        div.tablebox tbody tr td:nth-of-type(11) {
            width: 100px;
        }

    </style>

</head>

<body class="gray">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>

    <!-- header -->
    <header>header</header>
    <!-- header end -->

    <div class="mainContent" id="app">

        <div class="tags">
            <a class="tag E focus">Catalog</a>
<?php if($access6 == true) { ?>
            <a class="tag A" href="office_items_main_category">Main Category</a>
            <a class="tag B" href="office_items_sub_category">Sub Category</a>
            <a class="tag C" href="office_items_brand">Brand</a>
            <a class="tag D" href="office_items_description">Description</a>
<?php } ?>
        </div>

        <!-- Blocks -->
        <div class="block E focus">

            <div class="heading-and-btn" style="padding-top: 15px;">

                <ul>
                    <li>
                        <h4>Office Items Catalog</h4>
                    </li>
                </ul>

            </div>

            <div class="list_function">

                <div class="left_function">
                    <select v-model='lv1' v-on:change="getLevel2()">
                        <option value="">----- Main Category -----</option>
                        <!-- Main Category 的選項內容格式為：Main Category(Code)，例如像是：OFFICE SUPPLIES(01) -->
                        <option :value="item.code" v-for="(item, index) in level1">{{ item.category }}({{ item.code }})</option>
                    </select>

                    <select v-model='lv2' v-on:change="getLevel3()">
                        <option value="">----- Sub Category -----</option>
                        <!-- Sub Category 的選項內容格式為：Sub Category(Code)，例如像是：BALLPEN(01)。當使用者選擇不同的 Main Category 時，Sub Category 的 select 只會載入特定 Main Category 底下的 Sub Category 到 select 裡面 -->
                        <option :value="item.code" v-for="(item, index) in level2">{{ item.category }}({{ item.code }})</option>
                    </select>

                    <select v-model='lv3' v-on:change="getLevel4()">
                        <option value="">----- Brand -----</option>
                        <!-- Brand 的選項內容格式為：Brand(Code)，例如像是：HP(01)。當使用者在某一層的的 select 選擇了某一個值之後，下一層的 select 只會載入階層架構下該節點的子節點 到 select 裡面 -->
                        <option :value="item.code" v-for="(item, index) in level3">{{ item.category }}({{ item.code }})</option>
                    </select>

                    <select v-model='lv4'>
                        <option value="">----- Description -----</option>
                        <option :value="item.code" v-for="(item, index) in level4">{{ item.category }}({{ item.code }})</option>
                    </select>

                    <button style="margin-left: 20px;" @click="filter_apply_new()"><i aria-hidden="true" class="fas fa-filter"></i></button>
                    <button @click="print()"><i aria-hidden="true" class="fas fa-file-export"></i></button>
                    <button @click="clear()" style="width: 50px;">Clear</button>

                </div>

                <!-- 分頁功能 -->
                <!-- 這個頁面需要做分頁，每一頁 20 筆資料  -->
                <div class="pagenation">
                    <a class="prev" style="color:#707071;" :disabled="page == 1" @click="pre_page(); filter_apply_new();">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply_new();" v-bind:style="[pg == page ? { 'background':'#707071', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" style="color:#707071;" :disabled="page == pages.length" @click="nex_page(); filter_apply_new();">Next 10</a>
                </div>
               

            </div>


            <div class="tablebox">
                <table id="showUser1">
                    <thead>
                    <tr>
                        <th colspan="2">
                            MAIN CATEGORY
                        </th>
                        <th colspan="2">
                            SUB CATEGORY
                        </th>
                        <th colspan="2">
                            BRAND
                        </th>
                        <th colspan="2">
                            DESCRIPTION
                        </th>
                        <th>
                            CODE
                        </th>
                        <th>
                            IMAGE
                        </th>
                        <th>
                            QTY
                        </th>
                    </tr>
                    </thaed>

                    <tbody>

                    <tr v-for="(item, index) in items">
                        <td>{{ item.code1 }}</td>
                        <td>{{ item.cat1 }}</td>
                        <td>{{ item.code2 }}</td>
                        <td>{{ item.cat2 }}</td>
                        <td>{{ item.code3 }}</td>
                        <td>{{ item.cat3 }}</td>
                        <td>{{ item.code4 }}</td>
                        <td>{{ item.cat4 }}</td>
                        <td>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</td>
                        <td>
                            <a :href="item.url" target="_blank" v-if="item.url">
                                <img :src="item.url" v-if="item.url">
                            </a>
                        </td>
                        <td>{{ item.qty }}</td>
                    </tr>


                    </tbody>

                </table>


            </div>
        </div>


    </div>


</div>
</div>

</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/npm/exif-js.js"></script>
<script src="js/moment.js"></script>
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>
<script src="js/a076d05399.js"></script>
<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>
<script defer src="js/office_items_catalog.js"></script>

</html>
