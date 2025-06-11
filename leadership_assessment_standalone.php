<?php
include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/config/database.php';


use \Firebase\JWT\JWT;

$test_manager = "0";

try {

        $token = isset($_GET['token']) ? $_GET['token'] : null;
        if (!isset($token)) {
            http_response_code(401);
        
            echo "Access denied";
            die();
        }
        
        // You already filled out and submitted the survey.
        try {

            $decoded = passport_decrypt($token);
            $data = json_decode($decoded);
            $email = $data->email;
            $pid = $data->id;
        }
        // if decode fails, it means jwt is invalid
        catch (Exception $e) {
    
            http_response_code(401);
    
            echo "Access denied";
            die();
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM leadership_assessment_review WHERE pid = :pid and email = :email and status = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':pid', $pid);
        $stmt->bindParam(':email', $email);

        $stmt->execute();
        
        $num = $stmt->rowCount();
        if($num > 0) {
            http_response_code(401);
    
            echo "You already filled out and submitted the survey.";
            die();
        }

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

        body input.alone.green[type=radio]::before {
            font-size: 25px;
            color: #2F9A57;
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


    </style>

</head>

<body class="green">

<div class="bodybox">

    <div id="app" class="mainContent" style="padding-top: 20px;">

        <!-- Blocks -->
        <div class="block focus">
            <h6>Leadership Assessment</h6>

            <div class="box-content" style="border-bottom: none;">

                <div id="part1" v-show="period == 1">
                    <div style="padding-left: 3px;">
                        <p style="font-weight: 400; margin-bottom: 15px;">This assessment will take approximately 10
                            minutes to complete.</p>
                        <p style="font-weight: 400; margin-bottom: 15px;">Today, you have been asked to rate
                            {{record.employee}}.</p>
                        <p style="font-weight: 400; margin-bottom: 15px;">This survey is designed to provide an
                            evaluation of this individual&#39;s behaviors as he/she leads and relates to others. Please
                            answer the following questions as accurately as possible. Your responses to the questions
                            will be submitted anonymously and combined with other raters&#39; scores for reporting
                            purposes.</p>
                        <p style="font-weight: 400; margin-bottom: 15px;">Thank you for your time.</p>
                        <p style="font-weight: 400; margin-bottom: 15px;">Please click &lt;CONTINUE&gt;.</p>
                    </div>

                    <div style="background: #FFF3CD; font-weight: 300; padding: 15px; border-radius: 5px; margin-top: 35px;">
                        Note: If you are this leader&#39;s only direct manager, your scores will be shown separately;
                        however, your open-ended comments will be combined with all others
                    </div>

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
<script src="js/leadership_assessment_standalone.js"></script>
<script src="js/chart/chart.js"></script>
</html>