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

            $position = $decoded->data->position;
            $department = $decoded->data->department;
            
if($decoded->data->limited_access == true)
header( 'location:index' );
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

            $access6 = false;

            // QOUTE AND PAYMENT Management
            if(trim(strtoupper($department)) == 'SALES')
            {
                if(trim(strtoupper($position)) == 'ASSISTANT CUSTOMER VALUE DIRECTOR'
                || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'LIGHTING')
            {
                if(trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }

                if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'OFFICE')
            {
                if(trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'DESIGN')
            {
                if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ENGINEERING')
            {
                if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($department)) == 'ADMIN')
            {
                if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
                {
                    $access6 = true;
                }
            }


            if(trim($department) == '')
            {
                if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
                {
                    $access6 = true;
                }
            }

            if(trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER')
            {
                $access6 = true;
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
    <title>Performance Dashboard</title>
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

        .block .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .block .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table thead th, .list_table tbody td{
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
            color: rgb(255,165,0);
            font-size: 25px;
        }

        .list_table tfoot tr th{
            border-right: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tfoot tr th:first-of-type{
            text-align: right;
        }

        .list_table tfoot tr th:nth-of-type(2){
            border-right: none;
        }

        .promotion{
            margin-top: 40px;
            border: 2px solid #2F9A57;
            border-radius: 20px;
            padding: 30px 40px 5px;
        }

        .promotion ul li:nth-of-type(1){
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .promotion ul li:nth-of-type(2){
            font-size: 20px;
            margin-bottom: 5px!important;
        }

        .promotion li a{
            color: blue;
            text-decoration: underline;
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
            <a class="tag C focus">Performance Dashboard</a>
            <a class="tag A" href="performance_review">Performance Review</a>
            <a class="tag D" href="template_library">Template Library</a>
            <?php if($access6 == true) echo('<a class="tag B" href="template_management?kw=&pg=1">Template Management</a>'); ?>

        </div>
        <!-- Blocks -->
        <div class="block C focus">
            <h6>Performance Dashboard</h6>

            <div class="box-content" v-if="record.length == 0">
                <h5> No result of performance review  was found!!</h5>
            </div>

            <div class="box-content" v-if="record.length > 0">
                <ul>
                    <li><b>Name:</b></li>
                    <li class="content">{{ views.employee }}</li>

                    <li><b>Position:</b></li>
                    <li class="content">{{ views.title }}</li>

                    <li><b>Department:</b></li>
                    <li class="content">{{ views.department }}</li>

                    <li><b>Supervisor:</b></li>
                    <li class="content">{{ views.manager }}</li>

                    <li><b>Review Period (Latest):</b></li>
                    <li class="content" v-if="views.period == '0'">{{ views.review_month }} ~ {{ views.review_next_month }}</li>
                    <li class="content" v-if="views.period == '1'">{{ views.review_month }}</li>
                </ul>

                <table class="list_table" style="margin-top:40px;">
                    <thead>
                    <tr>
                        <th colspan="3">PART I: SELF-IMPROVEMENT SKILLS</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for='(record, index) in views.agenda'>
                        <td>
                            {{ record.category }}
                        </td>
                        <td>
                            {{ record.criterion }}
                        </td>
                        <td>
                            <template v-if="record.mag_score == '-1'">N/A</template>
                            <template v-if="record.mag_score != '-1'" v-for="n in parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">★</template><template v-if="record.mag_score != '-1'" v-for="n in 10 - parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">☆</template>
                        </td>
                    </tr>

                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="2">AVERAGE</th>
                        <th>{{ mag_avg == 0 ? "N/A" : (mag_avg / 1).toFixed(1) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">SUBTOTAL (60%)</th>
                        <th>{{ mag_avg == 0 ? "N/A" : ( mag_avg * 0.6 ).toFixed(1) }}</th>
                    </tr>
                    </tfoot>
                </table>

                <table class="list_table" style="margin-top: 40px;">
                    <thead>
                    <tr>
                        <th colspan="3">PART II: BASIC SKILLS</th>

                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for='(record, index) in views.agenda1'>
                        <td>
                            {{ record.category }}
                        </td>
                        <td>
                            {{ record.criterion }}
                        </td>
                        <td>
                            <template v-if="record.mag_score == '-1'">N/A</template>
                            <template v-if="record.mag_score != '-1'" v-for="n in parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">★</template><template v-if="record.mag_score != '-1'" v-for="n in 10 - parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">☆</template>
                        </td>
                    </tr>

                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="2">AVERAGE</th>
                        <th>{{ mag_avg1 == 0 ? "N/A" : (mag_avg1 / 1).toFixed(1) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">SUBTOTAL (40%)</th>
                        <th>{{ mag_avg1 == 0 ? "N/A" : ( mag_avg1 * 0.4 ).toFixed(1) }}</th>
                    </tr>
                    </tfoot>
                </table>

                <table class="list_table" style="margin-top: 40px;" v-if="views.agenda2 === undefined ? false : views.agenda2.length > 0">
                    <thead>
                    <tr>
                        <th colspan="3">PART III: BONUS</th>

                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for='(record, index) in views.agenda2'>
                        <td>
                            {{ record.category }}
                        </td>
                        <td>
                            {{ record.criterion }}
                        </td>
                        <td>
                            <template v-if="record.mag_score == '-1'">N/A</template>
                            <template v-if="record.mag_score != '-1'" v-for="n in parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">★</template><template v-if="record.mag_score != '-1'" v-for="n in 10 - parseInt((record.mag_score > -1) ? record.mag_score : 0, 10)">☆</template>
                        </td>
                    </tr>

                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="2">AVERAGE</th>
                        <th>{{ mag_avg2 == 0 ? "N/A" : (mag_avg2 / 1).toFixed(1) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">SUBTOTAL (10%)</th>
                        <th>{{ mag_avg2 == 0 ? "N/A" : ( mag_avg2 * 0.1 ).toFixed(1) }}</th>
                    </tr>
                    </tfoot>
                </table>

                <ul style="margin-top: 30px;">
                    <li><b>TOTAL:</b></li>
                    <li class="content" style="font-weight: 700;">{{ (mag_avg == 0 && mag_avg1 == 0) ? "N/A" : ( mag_avg * 0.6 + mag_avg1 * 0.4 + (views.agenda2 !== undefined ? parseFloat((mag_avg2 * 0.1).toFixed(1)) : 0.0 ) ).toFixed(1) }}</li>
                </ul>

                <div class="promotion" v-if="view_promotion == true">
                    <ul>
                        <li>Congratulations!! Your recent performance meets the requirement of
                            position promotion. Before you submit the promotion request, please view the performance
                            criteria of your target position first (in Tab <a href="template_library">Template Library</a>) and make sure you are willing to bear the related
                            responsibility.
                        </li>


                        <li>Choose Your Target Position for Promotion:</li>
                        <li>
                            <select v-model="department">
                                <option v-for="(item, index) in position" :value="item.did" :key="item.department">{{ item.department }}</option>
                            </select>

                            <select v-model="title_id">
                                <option v-for="(item, index) in title" :value="item.tid" :key="item.title">{{ item.title }}</option>
                            </select>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn green">Submit Promotion Request</a>
                    </div>
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
<script src="js/performance_dashboard.js"></script>
</html>