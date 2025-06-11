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

            $position = $decoded->data->position;
            $department = $decoded->data->department;
            
        }
        catch (Exception $e){

            header( 'location:index' );
        }

        $database = new Database();
        $db = $database->getConnection();

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
    <title>Leadership Assessment</title>
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
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script src="js/moment.js"></script>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>

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

        body.green {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

         a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.green header nav a, body.green header nav a:link {
            color: #000;
        }

        body.green header nav a:hover {
            color: #333;
        }

        body.green header nav ul.info {
            margin-bottom: 0;
        }

        body.green header nav ul.info b {
            font-weight: bold;
        }

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
            color: white;
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
            background-color: var(--fth04);
            background-image: url(images/ui/btn_add_green.svg);
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

        .list_function .searching input[type=month] {
            border: 2px solid #2F9A57;
            background-color: transparent;
            vertical-align: middle;
        }


        .list_function .searching i {
            color: #2F9A57;
            font-size: 22px;
        }

        .list_function .pagenation {
            float: none;
        }

        .list_function .pagenation a {
            color: #2F9A57;
            border-color: #2F9A57;
        }

        .list_function .pagenation a:hover {
            background-color: #2F9A57;
            color: #FFF;
        }


        body input.alone.green[type=radio]::before {
            font-size: 25px;
            color: #2F9A57;
        }

        .bodybox .mask {
            position: absolute;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        .modal {
            display: none;
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 2;
        }

        .modal .modal-content {
            width: 90%;
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .modal .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 25px 15px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .modal-content .modal-header h6 {
            color: #2F9A57;
            border-bottom: none;
            padding: 0;
        }

        .modal .modal-content .modal-header a {
            color: #2F9A57;
            font-size: 20px;
        }

        .modal .modal-content .box-content {
            padding: 20px 25px 25px;
            border-bottom: 2px solid #2F9A57;
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=month]{
            border: 2px solid #2F9A57;
            width: 100%;
            padding: 8px 35px 8px 15px;
        }

        .modal .modal-content .box-content textarea{
            border: 1px solid black;
            width: 100%;
            resize: none;
        }

        .modal .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

        .modal .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
            height: 27.8px;
        }

        ul.question {
            display: table-row;
            border-bottom: 1px solid black;
        }

        ul.question li {
            display: table-cell;
            width: 7.5%;
            font-weight: 400;
            border-bottom: 1px solid #E2E2E2;
            padding: 20px 10px;
        }

        ul.question.header li {
            font-size: 14px;
            font-weight: 600;
            padding: 10px;
            vertical-align: bottom;
        }

        ul.question.footer li {
            border-bottom: none;
            font-size: 14px;
            font-weight: 600;
            padding: 10px;
            vertical-align: top;
        }

        ul.question li:nth-of-type(1) {
            width: 40%;
            padding-left: 15px;
        }

        ul.question li > input.alone.green[type=radio]::before {
            font-size: 18px;
            margin-right: 3px;
        }


        ul.open_ended {
            width: 100%;
            margin-bottom: 30px;

        }

        ul.open_ended li:nth-of-type(1) {
            font-weight: 400;
            margin-bottom:10px;
        }

        ul.open_ended li:nth-of-type(2) textarea {
            width: 60%!important;
            height: 100px!important;
            border-color: #C9C9C9!important;
            resize: none!important;
        }

        div.button_area.type1 {
            margin-top: 15px;
        }

        div.button_area.type2 {
            margin-top: 50px;
            display: flex;
            justify-content: end;
            align-items: center;
            border-top: 1px solid #E2E2E2;
            border-bottom: 1px solid #E2E2E2;
            padding: 10px;
        }

        div.button_area.type2 > div {
            margin-right: 15px;
            font-size: 15px;
        }

        div.button_area.type3 {
            margin-top: 50px;
            display: flex;
            justify-content: start;
            align-items: center;
            padding: 10px;
        }

        div.button_area.type3 > div {
            margin-right: 15px;
            font-size: 0 0 5px 5px;
        }


        button.btn.dropdown-toggle.btn-light {
            background-color: #FFF;
            border: 2px solid #2F9A57;
            border-radius: 0;
            height: 42px;
        }

        button.btn.dropdown-toggle.btn-light div.filter-option {
            color: black;
            font-weight: 500;
            line-height: 28px;
        }

        .dropdown-toggle::after {
            color: #2F9A57!important;
        }

        li.outsider_info {
            margin-bottom: 10px;
        }

        li.outsider_info > span {
            margin-left: 10px;
        }

        li.outsider_info > input[type="text"]:nth-of-type(1) {
            width: 35%;
            margin-left: 15px;
        }

        li.outsider_info > input[type="text"]:nth-of-type(2) {
            width: 56%;
            margin-left: 10px;
            float: right;
        }

        #part1 a.btn.small, #part2 a.btn.small, #part3 a.btn.small, #part4 a.btn.small, #part5 a.btn.small {
            padding-bottom: 5px;
            font-size: 14px;
            color: white;
        }


        #Modal_4 .box-content .table_of_contents {
            width: 100%;
            margin-bottom: 40px;
        }

        #Modal_4 .box-content .table_of_contents th {
            border: 1px solid #C0C0C0;
            background: #C0C0C0;
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 20px;
        }

        #Modal_4 .box-content .table_of_contents tr td {
            width: 33%;
            padding: 5px;
            border-right: 1px solid #C0C0C0;
            border-bottom: 1px solid #C0C0C0;
            text-align: center;
            font-size: 18px;
        }

        #Modal_4 .box-content .table_of_contents tr td:nth-of-type(1) {
            border-left: 1px solid #C0C0C0;
        }

        #Modal_4 .box-content .table_of_contents tr td a {
            color: #007bff;
        }

        #Modal_4 .box-content .table_of_contents tr td a:hover {
            color: #0056b3;
        }

        #Modal_4 .box-content .category_result .title {
            font-weight: 700;
            width: 100%;
            color: white;
            background: black;
            padding: 10px 15px;
            font-size: 28px;
        }

        #Modal_4 .box-content .category_result h5 {
            margin: 20px 0;
        }

        #Modal_4 .box-content .category_result h5 span {
            margin-right: 20px;
        }

        #Modal_4 .box-content .category_result p {
            font-size: 18px;
            font-weight: 300;
        }

        #Modal_4 .box-content .category_result p.hint {
            margin-top: 25px;
            margin-bottom: 5px;
        }

        #Modal_4 .box-content .category_result .graph {
            width: 100%;
            height: 300px;
            border: 1px solid black;
        }

        #Modal_4 .box-content .category_result table {
            width: 100%;
            margin-bottom: 50px;
        }

        #Modal_4 .box-content .category_result thead tr th {
            width: 10%;
            padding: 8px;
            color: white;
            background: black;
            text-align: center;
            border-top: 2px solid #C0C0C0;
            border-bottom: 2px solid #C0C0C0;
            border-right: 2px solid #C0C0C0;
        }

        #Modal_4 .box-content .category_result thead tr th:nth-of-type(1) {
            width: 40%;
            border: 2px solid #C0C0C0;
        }

        #Modal_4 .box-content .category_result tbody tr td {
            width: 10%;
            padding: 8px;
            text-align: center;
            border-bottom: 2px solid #C0C0C0;
            border-right: 2px solid #C0C0C0;
        }

        #Modal_4 .box-content .category_result tbody tr td:nth-of-type(1) {
            width: 40%;
            border: 2px solid #C0C0C0;
            text-align: left;
        }

        #Modal_4 .box-content .category_result tfoot tr th:nth-of-type(1) {
            text-align: center;
            font-size: 14px;
            font-weight: 300;
            padding: 5px;
        }

        #Modal_4 .box-content .category_result.final ul li {
            margin: 2px 0;
        }

        #Modal_4 .box-content .category_result.final thead tr th {
            width: 8%;
        }

        #Modal_4 .box-content .category_result.final thead tr th:nth-of-type(1) {
            width: 42%;
        }

        #Modal_4 .box-content .category_result.final thead tr th:nth-of-type(8) {
            width: 10%;
        }

        #Modal_4 .box-content .category_result.final tbody tr td {
            width: 8%;
        }

        #Modal_4 .box-content .category_result.final tbody tr td:nth-of-type(1) {
            width: 42%;
        }

        #Modal_4 .box-content .category_result.final tbody tr td:nth-of-type(8) {
            width: 10%;
            color: white;
            font-weight: 700;
        }

        #Modal_4 .box-content .category_result ul {
            list-style: disc;
            padding-left: 35px;
            margin-top: -10px;
        }


        #Modal_4 .box-content .category_result.appendix thead tr th {
            width: 12%;
        }

        #Modal_4 .box-content .category_result.appendix thead tr th:nth-of-type(1) {
            width: 8%;
        }

        #Modal_4 .box-content .category_result.appendix thead tr th:nth-of-type(2) {
            width: 68%;
        }

        #Modal_4 .box-content .category_result.appendix tbody tr td {
            width: 12%;
        }

        #Modal_4 .box-content .category_result.appendix tbody tr td:nth-of-type(1) {
            width: 8%;
            text-align: center;
        }

        #Modal_4 .box-content .category_result.appendixl tbody tr td:nth-of-type(2) {
            width: 68%;
            text-align: left;
        }

        #Modal_4 .box-content .category_result.appendixl tbody tr td:nth-of-type(3) {
            font-weight: 600;
        }

        #Modal_4 .box-content .category_result.appendix table {
            margin-top: 30px;
        }

        #Modal_4 .cat1 {
            background: #2F9A57!important;
            color: white;
        }

        #Modal_4 .cat2 {
            background: #DFBA40!important;
            color: white;
        }

        #Modal_4 .cat3 {
            background: #284294!important;
            color: white;
        }

        #Modal_4 .cat4 {
            background: #286BEC!important;
            color: white;
        }

        #Modal_4 .cat5 {
            background: #B12C28!important;
            color: white;
        }

        #Modal_4 .cat6 {
            background: #5F328B!important;
            color: white;
        }

        #Modal_4 .gray {
            background: #CCCCCC!important;
        }

    </style>

</head>

<body class="green">

<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display:none"></div>
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Leadership Assessment</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Leadership Assessment</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create"  @click="open_review"></a>

                            <div class="searching">
                                <input type="month" v-model="sdate">
                                <input type="month" v-model="edate">
                                <input type="text" style="width: 380px;" placeholder="Search for Name, Position or Department Here" v-model="keyword">
                                <button style="border: none;" @click="search(1)"><i class="fas fa-search-plus"></i></button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" :disabled="page == 1"  @click="pre_page(); filter_apply();">Prev 10</a>
                            <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[page==pg ? { 'background':'#2F9A57', 'color': 'white'} : { }]" >{{ pg }}</a>
                            <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Status</li>
                        <li>Assessed Employee Name</li>
                        <li>Position (Department)</li>
                        <li>Duration<br>(Created Date to Completed Date)</li>
                    </ul>

                    <ul v-for='(record, index) in displayedRecord' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone green" :value="record.id" v-model="proof_id">
                        </li>
                        <li>{{ record.status_desc }}</li>
                        <li>{{ record.employee }}</li>
                        <li>{{ record.title }} ({{ record.department }})</li>
                        <li class="content">{{ record.created_at }} ~ {{ record.user_complete_at }}</li>
                    </ul>

                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">
                    <a class="btn green" @click="execute()">Execute</a>
                    <a class="btn green" @click="view()">View Result</a>
                    <a class="btn" style="color: rgb(255, 255, 255);" @click="remove()">Delete</a>
                </div>

            </div>


                <!-- 領導力評估的第一階段，選擇誰要被評估 -->
                <div id="Modal_1" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Create New Leadership Assessment</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(1)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>

                        <!-- Choose employee to be assessed -->
                        <div class="box-content" style="border-bottom: none;">
                            <ul>
                                <li><b>Assessed Employee Name</b></li>
                                <li>
                                    <select v-model="employee">
                                        <option v-for="(item, index) in employees" :value="item" :key="item.id">{{ item.username }}</option>
                                    </select>
                                </li>
                            </ul>

                        </div>

                        <div class="btnbox" style="padding-top: 0;">
                            <a class="btn green" :disabled="submit == true" @click="add_review">Create</a>
                        </div>

                    </div>

                </div>


                <!-- 領導力評估的第二階段，被評估人員需要哪些人要協助回答問卷 -->
                <div id="Modal_2" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Choose Respondent for Your Leadership Assessment</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(2)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>

                        <!-- 針對不同職權類別，分別選擇問卷回答者 -->
                        <div class="box-content" style="border-bottom: none;">
                            <ul>
                                <li><b>Direct Report</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Please Choose Two Persons..." id="direct" v-model="direct_access">

                                        <?php foreach ($user_results as $user) { ?>
                                        <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                        <?php } ?>

                                    </select>
                                </li>


                                <li><b>Manager</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Please Choose Two Persons..." id="manager" v-model="manager_access">

                                        <?php foreach ($user_results as $user) { ?>
                                        <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                        <?php } ?>

                                    </select>
                                </li>


                                <li><b>Peer</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Please Choose Two Persons..." id="peer" v-model="peer_access">

                                        <?php foreach ($user_results as $user) { ?>
                                        <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                        <?php } ?>

                                    </select>
                                </li>


                                <li><b>Others (Choose from Selector or Input Name and E-MAIL)</b></li>
                                <li>
                                    <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="Please Choose Two Persons..." id="other" v-model="other_access">

                                        <?php foreach ($user_results as $user) { ?>
                                        <option value="<?php echo $user["username"]; ?>"><?php echo $user["username"]; ?></option>
                                        <?php } ?>

                                    </select>
                                </li>

                                <li class="outsider_info">
                                    <span><b>Person 1</b></span>
                                    <input type="text" placeholder="Name" v-model="outsider_name1">
                                    <input type="text" placeholder="E-Mail" v-model="outsider_email1">
                                </li>

                                <li class="outsider_info">
                                    <span><b>Person 2</b></span>
                                    <input type="text" placeholder="Name" v-model="outsider_name2">
                                    <input type="text" placeholder="E-Mail" v-model="outsider_email2">
                                </li>
                            </ul>

                        </div>

                        <div class="btnbox" style="padding-top: 0;">
                            <a class="btn green" :disabled="submit == true" @click="save_respondent()">Submit</a>
                        </div>

                    </div>

                </div>



                <!-- 領導力評估的第三階段，讓每個被選中的人和評估者本人填寫問卷，這裡是問卷本體內容所在 Modal -->
                <div id="Modal_3" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Leadership Assessment</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(3)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <div class="box-content" style="border-bottom: none;">

                            <div id="part1" v-show="period == 1">
                                <div style="padding-left: 3px;">
                                    <p style="font-weight: 400; margin-bottom: 15px;">This assessment will take approximately 10 minutes to complete.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">Today, you have been asked to rate {{record.employee}}.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">This survey is designed to provide an evaluation of this individual&#39;s behaviors as he/she leads and relates to others. Please answer the following questions as accurately as possible. Your responses to the questions will be submitted anonymously and combined with other raters&#39; scores for reporting purposes.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">Thank you for your time.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">Please click &lt;CONTINUE&gt;.</p>
                                </div>

                                <div style="background: #FFF3CD; font-weight: 300; padding: 15px; border-radius: 5px; margin-top: 35px;">Note: If you are this leader&#39;s only direct manager, your scores will be shown separately; however, your open-ended comments will be combined with all others</div>

                                <div style="margin-top: 15px;">
                                    <a class="btn small blue" @click="to_next(2)">CONTINUE</a>
                                </div>
                            </div>


                            <div id="part2" v-show="period == 2">
                                <div style="padding-left: 3px;">
                                    <p style="font-weight: 400; margin-bottom: 15px;">You will rate {{record.employee}} on various behaviors by choosing a number, 1 through 7 to represent whether you agree that this person displays that particular attribute.</p>

                                    <div style="margin-bottom: 15px;">

                                        <ul class="question header">
                                            <li></li>
                                            <li>Not Observed</li>
                                            <li>Very Strongly Disagree</li>
                                            <li>Strongly Disagree</li>
                                            <li>Disagree</li>
                                            <li>Neither Agree nor Disagree</li>
                                            <li>Agree</li>
                                            <li>Strongly Agree</li>
                                            <li>Very Strongly Agree</li>
                                        </ul>

                                        <ul class="question">
                                            <li>A good role model.</li>
                                            <li><input type="radio" name="demo" class="alone green"> 0</li>
                                            <li><input type="radio" name="demo" class="alone green"> 1</li>
                                            <li><input type="radio" name="demo" class="alone green"> 2</li>
                                            <li><input type="radio" name="demo" class="alone green"> 3</li>
                                            <li><input type="radio" name="demo" class="alone green"> 4</li>
                                            <li><input type="radio" name="demo" class="alone green"> 5</li>
                                            <li><input type="radio" name="demo" class="alone green"> 6</li>
                                            <li><input type="radio" name="demo" class="alone green"> 7</li>
                                        </ul>

                                    </div>


                                    <p style="font-weight: 400; margin-bottom: 15px;">This is a sample only - you do not need to choose an answer on this page. Instead, simply note the rating scale below and then click CONTINUE to reach the first rating page:</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">1 = Very Strongly Disagree with this statement, i.e. this person IS NOT &quot;A good role mmodel.&quot;</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">7 = Very Strongly Agree with this statement, i.e. this person IS &quot;A good role mmodel.&quot;</p>

                                </div>

                                <div style="margin-top: 15px;">
                                    <a class="btn small blue" @click="to_next(3)">CONTINUE</a>
                                </div>
                            </div>


                            <div id="part3" v-show="period == 3">
                                <div style="padding-left: 3px;">
                                    <p style="font-weight: 400; margin-bottom: 15px;">In this Leadership Assessment, you will be considering your typical interaction/experience with this leader.</p>
                                    <p style="font-weight: 400; margin-bottom: 30px;">If you have not observed a particular item in the assessment:</p>
                                    <p style="font-weight: 400; margin-bottom: 30px;">First, consider whether you can predict his/her likely response based on your interactions in similar circumstances with him or her.</p>
                                    <p style="font-weight: 400; margin-bottom: 30px;">Try to avoid using the &quot;Not Observed&quot; choice.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">If you have regular contact with this leader, you should have few, if any, items marked &quot;Not Observed&quot; on the survey.</p>
                                </div>

                                <div style="margin-top: 15px;">
                                    <a class="btn small blue" @click="to_next(4)">CONTINUE</a>
                                </div>
                            </div>

                            <!-- 問卷的 第一頁 到 第八頁，都會使用 part4 結構，然後一頁一頁進行，因此需要一個變數來記錄現在使用者進行到第幾頁 -->
                            <div id="part4" v-show="period > 3 && period < 12">
                                <div style="padding-left: 3px;">
                                    <h3>{{ record.employee }}</h3>
                                    <p style="font-weight: 400; margin-bottom: 15px;">Please answer all questions then click at the bottom of page to move to the next page</p>

                                    <div>

                                        <ul class="question header">
                                            <li></li>
                                            <li>Not Observed</li>
                                            <li>Very Strongly Disagree</li>
                                            <li>Strongly Disagree</li>
                                            <li>Disagree</li>
                                            <li>Neither Agree nor Disagree</li>
                                            <li>Agree</li>
                                            <li>Strongly Agree</li>
                                            <li>Very Strongly Agree</li>
                                        </ul>

                                        <!-- 會用以下的 ul 結構，來把每一頁的 8 個問題依照 page_sequence 依序列出來 -->
                                        <ul class="question" v-for="(item, index) in question">
                                            <li>{{ item.question }}</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="0"> 0</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="1"> 1</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="2"> 2</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="3"> 3</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="4"> 4</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="5"> 5</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="6"> 6</li>
                                            <li><input type="radio" :name="'question_' + item.id" class="alone green" value="7"> 7</li>
                                        </ul>

                                        <ul class="question footer">
                                            <li></li>
                                            <li>Not Observed</li>
                                            <li>Very Strongly Disagree</li>
                                            <li>Strongly Disagree</li>
                                            <li>Disagree</li>
                                            <li>Neither Agree nor Disagree</li>
                                            <li>Agree</li>
                                            <li>Strongly Agree</li>
                                            <li>Very Strongly Agree</li>
                                        </ul>

                                    </div>

                                </div>

                                <div class="button_area type2">
                                    <div>Page {{ period - 3 }} of 9</div>
                                    <a class="btn small blue" @click="save_answer(period)">CONTINUE</a>
                                </div>
                            </div>

                            <!-- 第九頁 格式和前面八頁不同，問卷的第九頁會使用 part5 結構 -->
                            <div id="part5" v-show="period == 12">
                                <div style="padding-left: 3px;">
                                    <h3>{{ record.employee }}</h3>
                                    <p style="font-weight: 400; margin-bottom: 15px;">The following open-ended questions complete this Leadership Assessment. Each question has a 2000 character limit.</p>
                                    <p style="font-weight: 400; margin-bottom: 15px;">If you do not wish to include written comments, leave the text boxes blank and click &quot;SUBMIT SURVEY.&quot;</p>

                                    <div style="margin-top: 40px;">
                                        <ul class="open_ended">
                                            <li>What are this person&#39;s greatest strengths when it comes to relating to and leading others?<span> ({{comment1.length}}/{{ 2048 }})</span></li>
                                            <li><textarea v-model="comment1" :maxlength="2048" show-word-limit></textarea></li>
                                        </ul>

                                        <ul class="open_ended">
                                            <li>
                                            What are this person&#39;s greatest struggles when it comes to relating to and leading others?<span> ({{comment2.length}}/{{ 2048 }})</span></li>
                                            <li><textarea v-model="comment2" :maxlength="2048" show-word-limit></textarea></li>
                                        </ul>

                                        <ul class="open_ended">
                                            <li>
                                            What are this person&#39;s strengths and struggles in relation to Developing Others? (Any other comments you wiish to make can be put in this section also.)<span> ({{comment3.length}}/{{ 2048 }})</span></li>
                                            <li><textarea v-model="comment3" :maxlength="2048" show-word-limit></textarea></li>
                                        </ul>
                                    </div>

                                </div>

                                <div class="button_area type3">
                                    <div>Page 9 of 9</div>
                                    <a class="btn small blue" @click="complete_answer(period)">SUBMIT SURVEY</a>
                                </div>
                            </div>


                        </div>

                    </div>

                </div>



            <!-- 領導力評估的第四階段，所有人都完成問卷填寫，這裡是問卷計算結果所在 Modal -->
            <div id="Modal_4" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Result of Leadership Assessment</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(4)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- General description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Assessed Employee Name:</b></li>
                                <li class="content">{{ record.employee }}</li>

                                <li><b>Employee Position:</b></li>
                                <li class="content">{{ record.title }}</li>

                                <li><b>Employee Department:</b></li>
                                <li class="content">{{ record.department }}</li>

                                <li><b>Chosen Direct Report:</b></li>
                                <li class="content">{{ direct_access[0] }}, {{ direct_access[1] }}</li>

                                <li><b>Chosen Manager:</b></li>
                                <li class="content">{{ manager_access[0] }}, {{ manager_access[1] }}</li>

                                <li><b>Chosen Peer:</b></li>
                                <li class="content">{{ peer_access[0] }}, {{ peer_access[1] }}</li>

                                <li><b>Chosen Others:</b></li>
                                <li class="content">{{ other1 }}, {{ other2 }}</li>

                                <li><b>Duration (Created Date to Completed Date):</b></li>
                                <li class="content">{{ record.created_at }} ~ {{ record.user_complete_at }}</li>
                            </ul>

                        </div>


                        <!-- Result of Leadship Assessment start -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="table_of_contents" v-show="record.status == 2">

                                <thead>
                                <tr>
                                    <th colspan="3">REPORT FORMAT</th>
                                </tr>
                                </thead>

                                <tbody>

                                <!-- 使用者在前段點擊某一個連結，則該名稱的 div 會顯示出來，而且其他名稱的 div 都會隱藏起來 -->
                                <tr>
                                    <td><a @click="set_section('PRODUCTION')">PRODUCTION</a></td>
                                    <td><a @click="set_section('PERMISSION')">PERMISSION</a></td>
                                    <td><a @click="set_section('PINNACLE-SELF')">PINNACLE-SELF</a></td>
                                </tr>

                                <tr>
                                    <td><a @click="set_section('PINNACLE-OTHERS')">PINNACLE-OTHERS</a></td>
                                    <td><a @click="set_section('POSITION')">POSITION</a></td>
                                    <td><a @click="set_section('PEOPLE DEVELOPMENT')">PEOPLE DEVELOPMENT</a></td>
                                </tr>

                                <tr>
                                    <td><a @click="set_comment('WRITTEN COMMENTS')">WRITTEN COMMENTS</a></td>
                                    <td><a @click="set_appendix('APPENDIX')">APPENDIX</a></td>
                                </tr>

                                </tbody>

                            </table>



                            <!-- 第一類別 Production -->
                            <div class="category_result" v-if="section == 'PRODUCTION'">

                                <div class="title cat1">PRODUCTION</div>

                                <p>The third level of leadership is about getting results with a team. Every organization, and therefore every leader, must achieve results in order to grow and expand. Results-oriented behaviors include setting the vision, thinking strategically, making decisions and initiating action to achieve that vision. Producing results in the production level means casting the vision and holding others accountable. In this level, people follow you because of what you've done for the organization.</p>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart1" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat1">PRODUCTION</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <!-- 把所有屬於 PRODUCTION 類別的問題結果，依照 Overall average 由大而小、由上而下列出 -->
                                    <!-- Overall average = 除了自己以外，其他這一題有填寫的，他們的分數平均值 -->
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                    </tr>



                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 第二類別 PERMISSION -->
                            <div class="category_result" v-if="section == 'PERMISSION'">

                                <div class="title cat2">PERMISSION</div>

                                <p>At this level of leadership, people follow you because they have given you permission to develop a relationship with them. Great leaders inspire and motivate other leaders to perform with excellence. In order to motivate and produce the best results, a leader takes a genuine interest in others. Great leaders train, coach, mentor, encourage and empower! The scores in this level reflect your relationship competencies as part of your core leadership attributes.</p>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart2" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat2">PERMISSION</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                    </tr>


                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 第三類別 PINNACLE-SELF -->
                            <div class="category_result" v-if="section == 'PINNACLE-SELF'">

                                <div class="title cat3">PINNACLE-SELF</div>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart3" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat3">PINNACLE-SELF</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 第四類別 PINNACLE-OTHERS -->
                            <div class="category_result" v-if="section == 'PINNACLE-OTHERS'">

                                <div class="title cat4">PINNACLE-OTHERS</div>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart4" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat4">PINNACLE-OTHERS</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 第五類別 POSITION -->
                            <div class="category_result" v-if="section == 'POSITION'">

                                <div class="title cat5">POSITION</div>

                                <p>This is the first level of leadership where people follow you because they have to – because of the position you hold. You serve in your position because of the promise you have shown as a leader. Others are watching you in this level to see if the walk matches the talk. The items below provide feedback on how you are perceived relating to areas like trustworthiness and commitment to the team, as well as how you are viewed as a role model. The scores in this level provide a reflection of how others view your attitude towards them individually and as a team.</p>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart5" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat5">POSITION</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 第六類別 PEOPLE DEVELOPMENT -->
                            <div class="category_result final" v-if="section == 'PEOPLE DEVELOPMENT'">

                                <div class="title cat6">PEOPLE DEVELOPMENT</div>

                                <p>Great leaders embrace developing others both personally and professionally. Highly effective leaders realize that time invested in developing others has an immeasurable impact on leading organizational success and employee satisfaction. Using the other levels of leadership (Position, Permission and Production) together helps leaders effectively develop others. At this level of leadership, you use your experience and knowledge to reproduce your own skills in the lives of others. When you do this, people follow you because of what you've done for them.</p>

                                <h5 style="margin-bottom: 5px;">
                                    <span>Overall Average: {{ overall_avg }}</span>
                                    <span>Direct Report: {{ direct_report }}</span>
                                    <span>Manager: {{ manager }}</span>
                                    <span>Peer: {{ peer }}</span>
                                    <span>Others: {{ other }}</span>
                                    <span>Self: {{ self }}</span>
                                </h5>

                                <canvas id="chart6" class="graph"></canvas>

                                <p class="hint">The table shows each of the items in this category, ranked highest to lowest score based on Overall Average.</p>

                                <table>
                                    <thead>
                                    <tr>
                                        <th class="cat6">PEOPLE DEVELOPMENT</th>
                                        <th>Overall Avg</th>
                                        <th>Direct Report</th>
                                        <th>Manager</th>
                                        <th>Peer</th>
                                        <th>Others</th>
                                        <th>Self</th>
                                        <th>Category</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, indx) in section_answers">
                                        <td>{{ item.question }}</td>
                                        <td>{{ item.average }}</td>
                                        <td>{{ item.direct }}</td>
                                        <td>{{ item.manager }}</td>
                                        <td>{{ item.peer }}</td>
                                        <td>{{ item.other }}</td>
                                        <td>{{ item.self }}</td>
                                        <td :class="[ item.css_class ]">{{ item.category }}</td>
                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="8">
                                            1 = Very Strongly Disagree | 2 = Strongly Disagree | 3 = Disagree | 4 = Neither Agree nor Disagree | 5 = Agree | 6 = Strongly Agree | 7 = Very Strongly Agree
                                        </th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </div>



                            <!-- 手寫區結果 WRITTEN COMMENTS -->
                            <div class="category_result final" v-if="section == 'WRITTEN COMMENTS'">

                                <div class="title">WRITTEN COMMENTS</div>

                                <!-- 第一題手寫題題目 -->
                                <h5>What are this person's greatest strengths when it comes to relating to and leading others?</h5>

                                <ul>
                                    <li v-for="(item, idx) in section_answers_comment1">{{ item }}</li>
                                </ul>

                                <!-- 第二題手寫題題目 -->
                                <h5>What are this person's greatest struggles when it comes to relating to and leading others?</h5>

                                <ul>
                                    <li v-for="(item, idx) in section_answers_comment2">{{ item }}</li>
                                </ul>

                            </div>



                            <!-- 附錄:總結區 APPENDIX -->
                            <div class="category_result appendix" v-if="section == 'APPENDIX'">

                                <div class="title">APPENDIX --- Leadership Attributes Summary</div>

                                <table>
                                    <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Average</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <!-- 排名 1到10 的，Rank需要套用 class="gray"，Category欄位需要套用該項目所屬目錄的顏色 cat1...cat6 -->
                                    <tr v-for="(item, idx) in section_answers">
                                        <td :class="[(idx > 53 || idx < 10 ? 'gray' : '')]">{{ idx + 1 }}</td>
                                        <td>{{ item.question }}</td>
                                        <td :class="[(idx > 53 || idx < 10 ? item.css_class : '')]">{{ item.category }}</td>
                                        <td>{{ item.average }}</td>
                                    </tr>


                                    </tbody>

                                </table>

                                        </div>

                        </div>

                    </div>
                    <!-- Result of Leadship Assessment finish -->

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
<script src="js/leadership_assessment.js"></script>
<script src="js/chart/chart.js"></script>
</html>