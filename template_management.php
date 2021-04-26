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
    <title>Template Management</title>
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
            }else if (target == 2) {
                $("#Modal_2").toggle();
            }else if (target == 3) {
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
            background-image: url(images/ui/icon_form_select_arrow_black.svg);
        }

        body.green a.btn.green {
            background-color: #2F9A57;
        }

        body.green a.btn.green:hover {
            background-color: #A9E5BF;
        }

        .block.A .box-content ul:first-of-type li:nth-of-type(even) {
            padding-bottom: 10px;
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
        }

        .list_function .searching input {
            font-size: 15px;
            padding: 4px 7px;
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
            height: calc(100vh - 40px);
            margin: auto;
            border: 3px solid #2F9A57;
            padding: 20px 0 0;
            background-color: white;
            max-height: 850px;
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

         .modal .box-content ul li:nth-of-type(even) {
            margin-bottom: 15px;
        }

         .modal .box-content ul li.content {
            padding: 3px 0 0;
            font-weight: 500;
            text-align: left;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .modal .modal-content .box-content select, .modal .modal-content .box-content input[type=text] {
            border: 1px solid black;
            width: 100%;
            padding: 8px 35px 8px 15px;
        }

        .list_table {
            border: 2px solid #2F9A57;
            width: 100%;
        }

        .list_table th, .list_table td{
            border: 2px solid #2F9A57;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            vertical-align: middle
        }

        .list_table tr td:first-of-type {
            font-weight: 600;
            width: 25%;
            background-color: #E5F7EB;
        }

        .list_table tr td:nth-of-type(2) {
            text-align: left;
        }

        .list_table tr td:nth-of-type(3) {
            width: 110px;
            line-height: 2;
        }

        .list_table td i {
            font-size: 25px;
        }

        .list_table td i:nth-of-type(odd) {
            margin-right: 5px;
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
            <a class="tag A">Performance Review</a>
            <a class="tag B focus">Template Management</a>

        </div>
        <!-- Blocks -->
        <div class="block B focus">
            <h6>Template Management of Performance Review</h6>

            <div class="box-content">

                <div class="title">
                    <div class="list_function">

                        <div class="front">
                            <a class="create" href="javascript: void(0)" onclick="ToggleModal(1)"></a>

                            <div class="searching">
                                <input type="text" placeholder="Searching Keyword Here">
                                <button style="border: none;"><i class="fas fa-search-plus"></i></button>
                            </div>
                        </div>

                        <div class="pagenation">
                            <a class="prev" disabled="disabled">Previous</a>
                            <a class="page">1</a>
                            <a class="next">Next</a>
                        </div>
                    </div>
                </div>

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Position</li>
                        <li>Version</li>
                        <li>Created Time</li>
                        <li>Updated Time</li>
                        <li>Times Cited</li>
                    </ul>

                    <ul>
                        <li>
                            <input type="radio" class="alone green">
                        </li>
                        <li>Office Admin Associate (Admin)</li>
                        <li>A</li>
                        <li>2021/03/12 15:50</li>
                        <li>2021/03/12 15:50</li>
                        <li>3</li>
                    </ul>
                </div>

                <div class="btnbox" style="display: flex; justify-content: center;">

                    <a class="btn green" href="javascript: void(0)" onclick="ToggleModal(2)">Detail</a>
                    <a class="btn green">Duplicate</a>
                    <a class="btn green" href="javascript: void(0)" onclick="ToggleModal(3)">Edit</a>
                    <a class="btn">Delete</a>
                </div>


                <div id="Modal_1" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Create Template</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(1)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li>
                                    <select>
                                        <option>Department</option>
                                        <option>Admin</option>
                                    </select>

                                    <select style="margin-top: 5px;">
                                        <option>Position</option>
                                        <option>Jr. Office Admin Associate</option>
                                        <option>Office Admin Associate</option>
                                        <option>Sr. Office Admin Associate</option>
                                        <option>Assistant Office Admin Associate</option>
                                        <option>Operations Manager</option>
                                    </select>
                                </li>

                                <li><b>Version Name</b></li>
                                <li><input type="text" required style="width:100%"></li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content">

                            <ul>
                                <li><b>Category</b></li>
                                <li>
                                    <select>
                                        <option>PART I: SELF-IMPROVEMENT SKILLS</option>
                                        <option>PART II: BASIC SKILLS</option>
                                    </select>
                                </li>

                                <li><b>Sub Category</b></li>
                                <li><input type="text" required style="width:100%"></li>

                                <li><b>Criterion</b></li>
                                <li><input type="text" required style="width:100%"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green">Add Criterion</a>
                                <a class="btn" style="display: none;">Cancel</a>
                                <a class="btn green" style="display: none;">Update Criterion</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in agenda'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.question }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up" @click="set_up(index, record.id)"></i>
                                        <i class="fas fa-arrow-alt-circle-down" @click="set_down(index, record.id)"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Attendance1 and Punctuality
                                    </td>
                                    <td>
                                        Arrives on time in office
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works well with their manager
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works collabroratively with fellow teammates
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Always cordial and willing to help other coworkers.
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Takes initiative and demonstrates "team player" behavior
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Possess skill at planning, organizing and prioritizing workload
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Willing to take additional responsibilities
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Proactively shares ideas, methods, solutions that may help the team
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Asks questions and seek guidance if need be
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Effectively communicates with other colleauges
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Corteous with other people related to the company
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr>
                                    <td>
                                        Scheduling
                                    </td>
                                    <td>
                                        Consolidates schedule and assigns service
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely processing of store rental, bills, office rental payment
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely advice of payments received
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Make sure that office supplies are enough and office is kept clean
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Accomplishes weekly report/ inventory
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Creates good impression for clients
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Takes notes on important details, forwards to right department
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Issuance and processing of approval
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Processing of liquidation and reimbursements
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Documentation
                                    </td>
                                    <td>
                                        Make expense reports up to date
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Bank Transactions
                                    </td>
                                    <td>
                                        Timely deposits and withdrawal from banks
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>
                                </tbody>

                            </table>

                        </div>
                        
                        <!-- Button to save template -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green">Create Template</a>
                            </div>
                        </div>

                    </div>

                </div>



                <div id="Modal_2" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Template Detail</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(2)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li class="content">Office Admin Associate (Admin)</li>

                                <li><b>Version Name</b></li>
                                <li class="content">A</li>

                                <li><b>Created Time</b></li>
                                <li class="content">Dennis Lin at 2021/03/12 15:50</li>

                                <li><b>Updated Time</b></li>
                                <li class="content">Dennis Lin at 2021/03/12 15:50</li>

                                <li><b>Times Cited</b></li>
                                <li class="content">3</li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content" style="border-bottom: none;">

                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="2">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td>
                                        Attendance and Punctuality
                                    </td>
                                    <td>
                                        Arrives on time in office
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works well with their manager
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works collabroratively with fellow teammates
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Always cordial and willing to help other coworkers.
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Takes initiative and demonstrates "team player" behavior
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Possess skill at planning, organizing and prioritizing workload
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Willing to take additional responsibilities
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Proactively shares ideas, methods, solutions that may help the team
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Asks questions and seek guidance if need be
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Effectively communicates with other colleauges
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Corteous with other people related to the company
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="2">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr>
                                    <td>
                                        Scheduling
                                    </td>
                                    <td>
                                        Consolidates schedule and assigns service
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely processing of store rental, bills, office rental payment
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely advice of payments received
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Make sure that office supplies are enough and office is kept clean
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Accomplishes weekly report/ inventory
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Creates good impression for clients
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Takes notes on important details, forwards to right department
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Issuance and processing of approval
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Processing of liquidation and reimbursements
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Documentation
                                    </td>
                                    <td>
                                        Make expense reports up to date
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Bank Transactions
                                    </td>
                                    <td>
                                        Timely deposits and withdrawal from banks
                                    </td>
                                </tr>
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                <div id="Modal_3" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h6>Edit Template</h6>
                            <a href="javascript: void(0)" onclick="ToggleModal(3)"><i class="fa fa-times fa-lg"
                                                                                     aria-hidden="true"></i></a>
                        </div>


                        <!-- Template general description -->
                        <div class="box-content">

                            <ul>
                                <li><b>Applicable Position</b></li>
                                <li>
                                    <select>
                                        <option>Department</option>
                                        <option>Admin</option>
                                    </select>

                                    <select style="margin-top: 5px;">
                                        <option>Position</option>
                                        <option>Jr. Office Admin Associate</option>
                                        <option>Office Admin Associate</option>
                                        <option>Sr. Office Admin Associate</option>
                                        <option>Assistant Office Admin Associate</option>
                                        <option>Operations Manager</option>
                                    </select>
                                </li>

                                <li><b>Version Name</b></li>
                                <li><input type="text" required style="width:100%"></li>
                            </ul>

                        </div>


                        <!-- Criterion content -->
                        <div class="box-content">

                            <ul>
                                <li><b>Category</b></li>
                                <li>
                                    <select>
                                        <option>PART I: SELF-IMPROVEMENT SKILLS</option>
                                        <option>PART II: BASIC SKILLS</option>
                                    </select>
                                </li>

                                <li><b>Sub Category</b></li>
                                <li><input type="text" required style="width:100%"></li>

                                <li><b>Criterion</b></li>
                                <li><input type="text" required style="width:100%"></li>

                            </ul>

                            <div class="btnbox">
                                <a class="btn green">Add Criterion</a>
                                <a class="btn" style="display: none;">Cancel</a>
                                <a class="btn green" style="display: none;">Update Criterion</a>
                            </div>


                            <table class="list_table" style="margin-top: 15px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART I: SELF-IMPROVEMENT SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr v-for='(record, index) in agenda'>
                                    <td>
                                        {{ record.category }}
                                    </td>
                                    <td>
                                        {{ record.question }}
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works well with their manager
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Works collabroratively with fellow teammates
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Teamwork/Interpersonal relations
                                    </td>
                                    <td>
                                        Always cordial and willing to help other coworkers.
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Takes initiative and demonstrates "team player" behavior
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Possess skill at planning, organizing and prioritizing workload
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Dependability
                                    </td>
                                    <td>
                                        Willing to take additional responsibilities
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Proactively shares ideas, methods, solutions that may help the team
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Asks questions and seek guidance if need be
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Effectively communicates with other colleauges
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Communication
                                    </td>
                                    <td>
                                        Corteous with other people related to the company
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                </tbody>

                            </table>


                            <table class="list_table" style="margin-top: 30px; margin-bottom:20px;">

                                <thead>
                                <tr>
                                    <th colspan="3">PART II: BASIC SKILLS</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr>
                                    <td>
                                        Scheduling
                                    </td>
                                    <td>
                                        Consolidates schedule and assigns service
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely processing of store rental, bills, office rental payment
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Company Transactions
                                    </td>
                                    <td>
                                        Timely advice of payments received
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Make sure that office supplies are enough and office is kept clean
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Office
                                    </td>
                                    <td>
                                        Accomplishes weekly report/ inventory
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Creates good impression for clients
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Phone Inquiries
                                    </td>
                                    <td>
                                        Takes notes on important details, forwards to right department
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Issuance and processing of approval
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Petty cash & reimbursements
                                    </td>
                                    <td>
                                        Processing of liquidation and reimbursements
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Documentation
                                    </td>
                                    <td>
                                        Make expense reports up to date
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Bank Transactions
                                    </td>
                                    <td>
                                        Timely deposits and withdrawal from banks
                                    </td>
                                    <td>
                                        <i class="fas fa-arrow-alt-circle-up"></i>
                                        <i class="fas fa-arrow-alt-circle-down"></i>
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                </tr>
                                </tbody>

                            </table>

                        </div>


                        <!-- Button to save template -->
                        <div class="modal-footer">
                            <div class="btnbox">
                                <a class="btn green">Update Template</a>
                            </div>
                        </div>

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
<script src="js/template_management.js"></script>
</html>