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

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    if($decoded->data->limited_access == true)
                header( 'location:index' );

    $database = new Database();
    $db = $database->getConnection();

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    $access6 = false;

    if (trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR'
        || trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER' || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR'
        || trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'ENGINEERING MANAGER'
        || trim(strtoupper($position)) == 'OPERATIONS MANAGER')
    {
        $access6 = true;
    }

    $query = "SELECT * FROM access_control WHERE edit_basic LIKE '%" . $username . "%' ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $access6 = true;
    }


    if ($access6 == false)
        header('location:index');
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="Bookmark" href="images/favicon.ico" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="images/iosicon.png"/>

<!-- SEO -->
<title>Management of Employee Basic Info</title>
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
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

<style>

    div.btnbox a.btn {
        width: 120px;
    }

    a.btn.red {
        background-color: var(--pri01a)!important;
    }

    a.btn.red:hover {
        background-color:var(--pri01b)!important;
    }

    body input.alone.cyan[type=radio]::before, .block input.cyan[type=radio]+Label::before {
        font-size: 25px;
        color: var(--cyan01);
    }

    .bodybox .mask {
        position: absolute;
        background: rgba(0, 0, 0, 0.5);
        width: 100%;
        height: 100%;
        top: 0;
        z-index: 1;
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
        width: 65%;
        margin: auto;
        border: 3px solid var(--cyan01);
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
        border-bottom: 2px solid var(--cyan01);
    }

    .modal .modal-content .modal-header h6 {
        color: var(--cyan01);
        border-bottom: none;
        padding: 0;
    }

    .modal .modal-content .modal-header a {
        color: var(--cyan01);
        font-size: 20px;
    }

    .modal .modal-content .box-content {
        padding: 20px 25px 25px;
        border-bottom: 2px solid var(--cyan01);
    }

    .modal .box-content ul li:nth-of-type(even) {
        margin-bottom: 15px;
    }

    .modal .modal-content .box-content select,
    .modal .modal-content .box-content input[type="date"] {
        border: 2px solid var(--cyan01);
        width: 300px;
        padding: 8px 35px 8px 15px;
    }

    .modal .modal-content .box-content input[type="text"] {
        width: 100%;
    }

    .modal .box-content ul li.content {
        padding: 3px 0;
        font-weight: 500;
        font-size: 18px;
        text-align: left;
        border-bottom: 2px solid var(--cyan01);
        vertical-align: middle;
        height: 35px;
    }

    .modal .box-content div.subtitle {
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 10px;
        text-decoration: 1px underline;
        color: var(--cyan01);
    }

    .modal .box-content .info_sheet {
        width: 100%;
        margin: 0 6px;
    }


    .modal .box-content .info_sheet tr td {
        width: 100%;
        font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
        font-size: 16px;
        font-weight: 400;
        padding: 4px 0;
        height: 32px;
    }

    .modal .box-content .info_sheet tr td.underline {
        border-bottom: 1px solid black;
    }

    .modal .box-content .info_sheet tr td span.caption {
        font-weight: 700;
    }

    @media screen and (min-width: 0px) and (max-width: 767px) {
        #my-content { display: none; }  /* hide it on small screens */
    }

    @media screen and (min-width: 768px) and (max-width: 1024px) {
        #my-content { display: block; }   /* show it elsewhere */
    }

</style>

</head>


<body class="cyan">
 	
<div class="bodybox">
    <div class="mask" :ref="'mask'" style="display: none;"></div>

    <!-- header -->
	<header>header</header>
    <!-- header end -->

    <div class="mainContent" id="app">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag B focus">Basic Info</a>
        </div>

        <!-- Blocks -->
        <div class="block B focus">
            <h6>Management of Employee Basic Info</h6>

            <div class="box-content">

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Employee Name</li>
                        <li>Department</li>
                        <li>Position</li>
                        <li>Updated Time</li>
                    </ul>
                    <ul v-for='(record, index) in user_records' :key="index">
                        <li>
                            <input type="radio" name="record_id" class="alone cyan" value="1" @click="uncheck(record.id)"
                                   v-model="record.is_checked">
                        </li>
                        <li>{{record.username}}</li>
                        <li>{{record.department}}</li>
                        <li>{{record.title}}</li>
                        <li>{{record.updated_at }}</li>
                    </ul>

                </div>

                <div class="btnbox">
                    <a class="btn" @click="viewRecord()">View</a>
                    <a class="btn" @click="editRecord()" v-if="edit_emp">Edit</a>
                    <a class="btn" @click="resetRecord()" v-if="edit_emp">Reset</a>
                </div>

            </div>


            <!-- Input Modal start -->
            <div id="Modal_input" class="modal" style="display: none;">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Basic Info</h6>
                        <a href="javascript: void(0)" @click="cancel_input()">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <ul>
                            <li><b>Employee Number:</b></li>
                            <li>
                                <input type="text" v-model="record.emp_number">
                            </li>

                            <li><b>First Name:</b></li>
                            <li class="content">{{ record.first_name }}</li>

                            <li><b>Middle Name:</b></li>
                            <li class="content">{{ record.middle_name }}</li>

                            <li><b>Surname:</b></li>
                            <li class="content">{{ record.surname }}</li>

                            <li><b>Date Hired:</b></li>
                            <li>
                                <input type="date" v-model="record.date_hired">
                            </li>

                            <li><b>Regularization Date:</b></li>
                            <li>
                                <input type="date" v-model="record.regular_hired">
                            </li>

                            <li><b>Employment Status:</b></li>
                            <li>
                                <select v-model="record.emp_status">
                                    <option value="probation">PROBATION</option>
                                    <option value="regular">REGULAR</option>
                                </select>
                            </li>

                            <li><b>Company:</b></li>
                            <li>
                                <input type="text" v-model="record.company">
                            </li>

                            <!-- 系統根據這位使用者在 user 資料表中已經設定的部門，把它載入到這個欄位中，管理人員無法修改此欄位的值  -->
                            <li><b>Department:</b></li>
                            <li>
                                <select name="department" id="department" disabled="true">
                                    <option :value="record.department">{{record.department}}</option>
                                </select>
                            </li>

                            <!-- 系統根據這位使用者在 user 資料表中已經設定的職稱，把它載入到這個欄位中，管理人員無法修改此欄位的值  -->
                            <li><b>Position Title:</b></li>
                            <li>
                                <select name="title" id="title" disabled="true">
                                    <option :value="record.title">{{record.title}}</option>
                                </select>
                            </li>

                            <li><b>Employee Category:</b></li>
                            <li>
                                <select v-model="record.emp_category">
                                    <option value="staff">STAFF</option>
                                    <option value="rank">RANK & FILE</option>
                                    <option value="senior">SENIOR</option>
                                    <option value="assistant">ASSISTANT DEPARTMENT MANAGER</option>
                                    <option value="department">DEPARTMENT MANAGER</option>
                                </select>
                            </li>

                            <!-- 系統會載入目前網站上已經註冊且 status=1 的使用者名稱當作 option  -->
                            <li><b>Next Level Manager/Superior:</b></li>
                            <li>
                                <select v-model="record.superior">
                                    <option value=""></option>
                                    <template v-for='(rc, index) in user_records'>
                                        <option :value="rc.username" v-if="rc.username != record.username">{{rc.username}}</option>
                                    </template>
                                </select>
                            </li>

                        </ul>

                        <div class="btnbox">
                            <a class="btn red" @click="cancel_input">Cancel</a>
                            <a class="btn" @click="save_input">Save</a>
                        </div>

                    </div>

                </div>

            </div>
            <!-- Input Modal end -->



            <!-- View Modal start -->
            <div id="Modal_view" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Basic Info</h6>
                        <a href="javascript: void(0)" @click="close_view">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <table class="info_sheet">

                            <!-- 以下欄位載入值時，系統都需要把載入的值轉換成英文大寫，再放入欄位中  -->
                            <tbody>
                            <tr>
                                <td>
                                    <span class="caption">Employee Number</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.emp_number }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">First Name</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.first_name }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Middle Name</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.middle_name }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Surname</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.surname }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <!-- 日期載入值的格式為： 完整月份英文 日, 四位數西元年分，例如： DECEMBER 12, 2023  -->
                            <tr>
                                <td>
                                    <span class="caption">Date Hired</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.date_hired }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Regularization Date</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.regular_hired }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Employment Status</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.emp_status }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.company }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Department</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.department }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Position Title</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.title }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Employee Category</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.emp_category }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Next Level Manager/Superior</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="underline">
                                    <span class="content">{{ view_data.superior}} </span>
                                </td>
                            </tr>

                            </tbody>

                        </table>

                        <div class="btnbox" style="margin-bottom: -20px;">
                            <a class="btn red" @click="close_view">Close</a>
                        </div>

                    </div>

                </div>

            </div>
            <!-- View Modal end -->


        </div>
        
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/sweetalert2@9.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script defer src="js/employee_basic_info.js"></script>
</html>
