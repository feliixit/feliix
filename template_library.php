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

            $GLOBALS['position'] = $decoded->data->position;
            $GLOBALS['department'] = $decoded->data->department;
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

    <!-- SEO -->
    <title>Template Library</title>
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
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');
        })

        function ShowTemplate() {
            $(".chosen_template").toggle();
        }
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">
        /* -------------------------- */
        /* body.green Style (Yellow) */
        /* -------------------------- */
        body.green .mainContent>.block,
        body.green .mainContent>.block h6,
        body.green .mainContent>.block .tablebox,
        body.green .mainContent>.block .tablebox>ul>li,
        body.green .mainContent>.block .tablebox2,
        body.green .mainContent>.block .formbox,
        body.green .mainContent>.block .formbox dd,
        body.green .mainContent>.tags a {
            border-color: #2F9A57;
        }

        body.green .mainContent>.block h6 {
            color: #2F9A57;
        }

        body.green .mainContent>.block .tablebox>ul.head>li,
        body.green .mainContent>.tags a {
            background-color: #E5F7EB;
        }

        body.green .mainContent>.tags a.focus {
            background-color: #2F9A57;
        }

        body.green .mainContent>.block .tablebox {
            border-top: 2px solid #2F9A57;
            border-left: 2px solid #2F9A57;
            width: 100%;
        }

        body.green .mainContent>.block .tablebox>ul>li {
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #2F9A57;
            border-right: 2px solid #2F9A57;
            font-weight: 500;
            font-size: 16px;
            vertical-align: middle;
        }

        body.green .mainContent>.block .tablebox ul.head,
        body.green .mainContent>.block .formbox li.head {
            background-color: #2F9A57;
            font-weight: 800;
        }

        body.green .mainContent>.block .tablebox ul.head li {
            font-weight: 800;
        }

        body.green input.alone[type=radio]::before,
        body.green input.alone[type=checkbox]::before,
        body.green input[type=checkbox]+Label::before,
        body.green input[type=radio]+Label::before {
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

        .block .tablebox li>a {
            text-decoration: none;
            color: #2F9A57;
            cursor: pointer;
            margin: 3px 6px 3px 0;
        }

        .block .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }


        .position_description {
            border: 2px solid #2F9A57;
            max-width: 750px;
            margin-bottom: 35px;
        }

        .position_description thead th {
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle;
            background-color: #E5F7EB;
        }

        .position_description tbody td {
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle;
        }

        .position_description tbody tr td:first-of-type {
            font-weight: 600;
            min-width: 140px;
        }

        .position_description tbody tr:nth-of-type(2) td:nth-of-type(2) {
            text-align: left;
            padding-left: 30px;
        }

        .position_description tbody tr:nth-of-type(2) td:nth-of-type(2) li {
            list-style-type: disc;
            margin-bottom: 0px;
        }

        .rating_table {
            width: 80%;
        }

        .rating_table th {
            font-weight: 700;
            text-align: left;
        }

        .rating_table tr td:nth-of-type(odd) {
            font-weight: 700;
            width: 10%;
            text-align: center;
        }

        .rating_table tr td:nth-of-type(even) {
            font-weight: 500;
            width: 40%;
        }

        .box-content textarea {
            border: 1px solid black;
            width: 100%;
            resize: none;
        }

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table thead th,
        .list_table tbody td {
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tbody tr td:first-of-type {
            font-weight: 600;
            min-width: 240px;
            background-color: #E5F7EB;
        }

        .list_table tbody tr td:nth-of-type(2) {
            text-align: left;
            min-width: 550px;
        }

        .list_table tbody tr td:nth-of-type(3) {
            min-width: 110px;
            color: #999;
        }

        .list_table tbody tr td:nth-of-type(4) {
            text-align: left;
            min-width: 300px;
            color: #999;
        }

        .list_table tbody tr.supervisor td:nth-of-type(1) {
            font-weight: 400;
            min-width: 110px;
            background-color: white;
        }

        .list_table tbody tr.supervisor td:nth-of-type(2) {
            text-align: left;
            min-width: 300px;
        }

        .box-content .list_table tbody select {
            padding: 10px;
            width: 75px;
        }

        .box-content .list_table tbody input[type="text"] {
            width: 100%;
            font-size: 16px;
        }

        .list_table tfoot tr th {
            border-right: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tfoot tr th:first-of-type {
            text-align: right;
        }

        .list_table tfoot tr th:nth-of-type(2) {
            border-right: none;
        }

        .list_table tfoot tr th:nth-of-type(2) span:first-of-type {
            color: #999;
        }

        .block .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .chosen_template {
            margin-top: 35px;
            overflow-x: auto;
        }
    </style>

</head>

<body class="green">

    <div class="bodybox">
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div id="app" class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag C" href="performance_dashboard">Performance Dashboard</a>
                <a class="tag A" href="performance_review">Performance Review</a>
                <a class="tag D focus">Template Library</a>
                <a class="tag B" href="template_management?kw=&pg=1">Template Management</a>
            </div>
            <!-- Blocks -->
            <div class="block D focus">
                <h6>Template Library</h6>

                <div class="box-content">
                    <ul>
                        <li><b>Choose Position:</b></li>
                        <li>
                            <select v-model="department">
                                <option v-for="(item, index) in position" :value="item.did" :key="item.department">{{ item.department }}</option>
                            </select>

                            <select v-model="title_id">
                                <option v-for="(item, index) in title" :value="item.tid" :key="item.title">{{ item.title }}</option>
                            </select>
                        </li>

                        <li><a class="btn green" @click="view_template()">View Template</a></li>
                    </ul>

                    <!-- Chosen Template start -->
                    <div class="chosen_template" v-if="view_detail == true">

                        <table class="position_description">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        {{ title_info.title }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Salary Range</td>
                                    <td>{{ library.salary }}</td>
                                </tr>

                                <tr>
                                    <td>KPIs</td>
                                    <td>
                                        <ul>
                                            <template v-for='(record, index) in library.kpi'>
                                                <li>
                                                    {{ record }}
                                                </li>
                                            </template>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <h5 v-if="template.length == 0">No template was found!!</h5>

                        <table v-if="template.length > 0" class="rating_table">
                            <thead>
                                <tr>
                                    <th colspan="4">Rating Scale</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>10</td>
                                    <td>Exceptional</td>
                                    <td>5</td>
                                    <td>Average</td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Perfect</td>
                                    <td>4</td>
                                    <td>Below Average</td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Very Good</td>
                                    <td>3</td>
                                    <td>Needs Improvement</td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Good</td>
                                    <td>2</td>
                                    <td>Poor</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>OK</td>
                                    <td>1</td>
                                    <td>Unacceptable</td>
                                </tr>
                            </tbody>
                        </table>

                        <table v-if="template.length > 0"  class="list_table" style="margin-top:30px;">
                            <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for='(item, index) in template[0].agenda' :key="index">
                                    <td>
                                        {{ item.category }}
                                    </td>
                                    <td>
                                        {{ item.criterion }}
                                    </td>
                                    <td>
                                        <select name="grade" @change="on_grade_change($event)" ref="grade">
                                            <option>10</option>
                                            <option>9</option>
                                            <option>8</option>
                                            <option>7</option>
                                            <option>6</option>
                                            <option>5</option>
                                            <option>4</option>
                                            <option>3</option>
                                            <option>2</option>
                                            <option>1</option>
                                        </select>
                                    </td>
                                    <td><input type="text"></td>
                                </tr>

                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="2">AVERAGE</th>
                                    <th>{{ (avg / 1).toFixed(1) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="2">SUBTOTAL (60%)</th>
                                    <th>{{ ( avg * 0.6 ).toFixed(1) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>


                        <table v-if="template.length > 0"  class="list_table" style="margin-top: 40px;">
                            <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                    <th colspan="2">Feedback</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for='(item, index) in template[0].agenda1' :key="index">
                                    <td>
                                        {{ item.category }}
                                    </td>
                                    <td>
                                        {{ item.criterion }}
                                    </td>
                                    <td>
                                        <select name="grade1" @change="on_grade1_change($event)" ref="grade1">
                                            <option>10</option>
                                            <option>9</option>
                                            <option>8</option>
                                            <option>7</option>
                                            <option>6</option>
                                            <option>5</option>
                                            <option>4</option>
                                            <option>3</option>
                                            <option>2</option>
                                            <option>1</option>
                                        </select>
                                    </td>
                                    <td><input type="text"></td>
                                </tr>


                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="2">AVERAGE</th>
                                    <th>{{ (avg1 / 1).toFixed(1) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="2">SUBTOTAL (40%)</th>
                                    <th>{{ ( avg1 * 0.4 ).toFixed(1) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>


                        <ul v-if="template.length > 0"  style="margin-top: 30px;">
                            <li><b>TOTAL:</b></li>
                            <li class="content" style="font-weight: 700;">{{ ( avg * 0.6 + avg1 * 0.4 ).toFixed(1) }}</li>

                            <li style="margin-top: 40px;"><b>Noteworthy accomplishment</b></li>
                            <li><textarea rows="5"></textarea></li>

                            <li><b>What is your opinion about the progress of your objective in the past two months? What
                                    ability, attitude, or method makes you deliver this progress? Anything else can be done or
                                    changed to make you execute better?</b></li>
                            <li><textarea rows="5"></textarea></li>

                            <li><b>What is your planning objective for the next two months? What is your role and
                                    responsibility in the objective? How do you define your success and failure in the
                                    objective?</b></li>
                            <li><textarea rows="5"></textarea></li>

                            <li><b>What are your career goals? Did the current job arrangement fit your career goals? If
                                    not, any suggestions?</b></li>
                            <li><textarea rows="5"></textarea></li>

                            <li><b>Other comments</b></li>
                            <li><textarea rows="5"></textarea></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script src="js/template_library.js"></script>

</html>