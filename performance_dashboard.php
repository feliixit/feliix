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
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">


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
            <a class="tag B" href="template_management?kw=&pg=1">Template Management</a>

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
                    <li class="content">{{ views.review_month }}</li>
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
                            <template v-for="n in parseInt(record.mag_score, 10)">★</template><template v-for="n in 10 - parseInt(record.mag_score, 10)">☆</template>
                        </td>
                    </tr>

                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="2">AVERAGE</th>
                        <th>{{ (mag_avg / 1).toFixed(1) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">SUBTOTAL (60%)</th>
                        <th>{{ ( mag_avg * 0.6 ).toFixed(1) }}</th>
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
                            <template v-for="n in parseInt(record.mag_score, 10)">★</template><template v-for="n in 10 - parseInt(record.mag_score, 10)">☆</template>
                        </td>
                    </tr>

                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="2">AVERAGE</th>
                        <th>{{ (mag_avg1 / 1).toFixed(1) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">SUBTOTAL (40%)</th>
                        <th>{{ ( mag_avg1 * 0.4 ).toFixed(1) }}</th>
                    </tr>
                    </tfoot>
                </table>

                <ul style="margin-top: 30px;">
                    <li><b>TOTAL:</b></li>
                    <li class="content" style="font-weight: 700;">{{ ( mag_avg * 0.6 + mag_avg1 * 0.4 ).toFixed(1) }}</li>
                </ul>

                <div class="promotion">
                    <ul>
                        <li>Congratulations!! Your recent performance meets the requirement of
                            position promotion. Before you submit the promotion request, please view the performance
                            criteria of your target position first (in Tab <a href=" ">Template Library</a>) and make sure you are willing to bear the related
                            responsibility.
                        </li>


                        <li>Choose Your Target Position for Promotion:</li>
                        <li>
                            <select>
                                <option>Department Name</option>
                            </select>

                            <select>
                                <option>Position Name</option>
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
<script src="js/performance_dashboard.js"></script>
</html>