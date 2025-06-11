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

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );
    if($decoded->data->limited_access == true)
                header( 'location:index' );

    $access6 = false;

    if (trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR'
        || trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER' || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR'
        || trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'ENGINEERING MANAGER'
        || trim(strtoupper($position)) == 'OPERATIONS MANAGER')
    {
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
<title>Management of Employee Data Sheet</title>
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
        display: none;
    }

    div.tablebox ul.need_review li {
        background: rgba(255, 255, 0, 0.2);
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
        width: 250px;
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
    }

    .modal .box-content div.subtitle {
        font-size: 22px;
        font-weight: 500;
        margin-bottom: 10px;
        text-decoration: 1px underline;
        color: var(--cyan01);
    }

    .modal .box-content .data_sheet {
        width: 100%;
    }


    .modal .box-content .data_sheet tr td {
        width: 33.3%;
        border-left: 1px solid black;
        border-bottom: 1px solid black;
        font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
        font-size: 14px;
        font-weight: 700;
        padding: 4px 7px;
    }

    .modal .box-content .data_sheet tr:first-of-type td {
        border-top: 1px solid black;
    }

    .modal .box-content .data_sheet tr td:last-of-type {
        border-right: 1px solid black;
    }

    .modal .box-content .data_sheet tr td span.content {
        font-weight: 400;
    }

    .modal .box-content .data_sheet tr td span.content span.new_value {
        color: red;
        font-weight: 600;
    }

    .modal .box-content .data_sheet tr td span.block_title {
        font-size: 15px;
    }

    #Modal_authorize .modal-content {
        width: 40%;
        min-width: 768px;
    }

    #Modal_authorize .modal-content .modal-header h6 {
        text-align: center;
        width: 100%;
    }

    #Modal_authorize .modal-content .authorize_dialog p {
        font-size: 22px;
        text-align: justify;
    }

    #Modal_authorize .modal-content .authorize_dialog .employee_name {
        position: relative;
        border-bottom: 1px solid black;
        margin: 30px 0 10px;
        padding: 10px 20px;
        width: 55%;
        min-width: 400px;
    }

    #Modal_authorize .modal-content .authorize_dialog .employee_name.bg_gray {
        background-color: rgba(0,0,0,0.05);
    }

    #Modal_authorize .modal-content .authorize_dialog .employee_name::after {
        content: "Employee Signature";
        position: absolute;
        bottom: -27px;
        left: 5px;
        font-size: 18px;
    }

    #Modal_authorize .modal-content .authorize_dialog .employee_name #signature_name {
        width: 100%;
        height: 80px;
    }

    #Modal_authorize .modal-content .authorize_dialog .employee_name img {
        max-width: 80%;
        max-height: 80px;
    }

    #Modal_authorize .modal-content .authorize_dialog .date_signed {
        position: relative;
        border-bottom: 1px solid black;
        margin: 40px 0 60px;
        padding: 10px 20px;
        width: 55%;
        min-width: 400px;
    }

    #Modal_authorize .modal-content .authorize_dialog .date_signed.bg_gray {
        background-color: rgba(0,0,0,0.05);
    }

    #Modal_authorize .modal-content .authorize_dialog .date_signed::after {
        content: "Date Signed";
        position: absolute;
        bottom: -27px;
        left: 5px;
        font-size: 18px;
    }

    #Modal_authorize .modal-content .authorize_dialog .date_signed #signature_date {
        width: 100%;
        height: 80px;
    }

    #Modal_authorize .modal-content .authorize_dialog .date_signed img {
        max-width: 80%;
        max-height: 80px;
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
    <div class="mask" :ref="'mask'"></div>

    <!-- header -->
	<header>header</header>
    <!-- header end -->

    <div class="mainContent" id="app">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A focus">Employee Data Sheet</a>
        </div>

        <!-- Blocks -->
        <div class="block A focus">
            <h6>Management of Employee Data Sheet</h6>

            <div class="box-content">

                <div class="tablebox">
                    <ul class="head">
                        <li><i class="micons">view_list</i></li>
                        <li>Employee Name</li>
                        <li>Department</li>
                        <li>Position</li>
                        <li>Authorization Form</li>
                        <li>Updated Time</li>
                    </ul>
                    <ul v-for='(record, index) in user_records' :key="index" :class="[ record.need_review > 0 ? 'need_review' : '']">
                        <li>
                            <input type="radio" name="record_id" class="alone cyan" value="1" @click="uncheck(record.id)"
                                   v-model="record.is_checked">
                        </li>
                        <li>{{record.username}}</li>
                        <li>{{record.department}}</li>
                        <li>{{record.title}}</li>
                        <li>{{record.auth_date}}</li>
                        <li>{{record.updated_at }}</li>
                    </ul>

                </div>

                <div class="btnbox">
                    <a class="btn" @click="viewAuth()" style="width: 250px;">Authorization Form</a>
                    <a class="btn" @click="viewRecord()">View</a>
                    <a class="btn" @click="reviewRecord()">Review</a>
                    <a class="btn" @click="editRecord()" v-if="edit_emp">Edit</a>
                    <a class="btn" @click="resetRecord()" v-if="edit_emp">Reset</a>
                </div>

            </div>


            <!-- Input Modal start -->
            <div id="Modal_input" class="modal" >

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Data Sheet</h6>
                        <a href="javascript: void(0)" @click="cancel_input()">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <ul>
                            <li><b>Position:</b></li>
                            <li class="content">{{ record.department }} {{ record.department == '   ' ? '' : '>>' }} {{ record.title }}</li>

                            <li><b>Date:</b></li>
                            <li>
                                <input type="date" readonly :value="record.updated_str">
                            </li>

                            <li><b>First Name:</b></li>
                            <li>
                                <input type="text" v-model="record.first_name">
                            </li>

                            <li><b>Middle Name:</b></li>
                            <li>
                                <input type="text" v-model="record.middle_name">
                            </li>

                            <li><b>Surname:</b></li>
                            <li>
                                <input type="text"  v-model="record.surname">
                            </li>

                            <li><b>Gender:</b></li>
                            <li>
                                <select v-model="record.gender">
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </li>

                            <li><b>Present Address:</b></li>
                            <li>
                                <input type="text"  v-model="record.present_address">
                            </li>

                            <li><b>Permanent Address:</b></li>
                            <li>
                                <input type="text"  v-model="record.permanent_address">
                            </li>

                            <li><b>Telephone Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.telephone">
                            </li>

                            <li><b>Cellphone Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.cellphone">
                            </li>

                            <li><b>Email Address:</b></li>
                            <li>
                                <input type="text"  v-model="record.email">
                            </li>

                            <li><b>Date of Birth:</b></li>
                            <li>
                                <input type="date" v-model="record.birthday">
                            </li>

                            <li><b>Place of Birth:</b></li>
                            <li>
                                <input type="text"  v-model="record.birthplace">
                            </li>

                            <li><b>Civil Status:</b></li>
                            <li>
                                <input type="text"  v-model="record.civil_status">
                            </li>

                            <li><b>Citizenship:</b></li>
                            <li>
                                <input type="text"  v-model="record.citizenship">
                            </li>

                            <li><b>Height:</b></li>
                            <li>
                                <input type="text"  v-model="record.height">
                            </li>

                            <li><b>Weight:</b></li>
                            <li>
                                <input type="text"  v-model="record.weight">
                            </li>

                            <li><b>Religion:</b></li>
                            <li>
                                <input type="text"  v-model="record.religion">
                            </li>

                            <li><b>Language/Dialect Spoken:</b></li>
                            <li>
                                <input type="text"  v-model="record.language">
                            </li>

                            <li><b>Medical Condition/Allergies:</b></li>
                            <li>
                                <input type="text"  v-model="record.medical">
                            </li>

                            <li><b>Spouse:</b></li>
                            <li>
                                <input type="text"  v-model="record.spouse">
                            </li>

                            <li><b>Spouse's Occupation:</b></li>
                            <li>
                                <input type="text"  v-model="record.spouse_ocupation">
                            </li>

                            <li><b>Name of Children:</b></li>
                            <li>
                                <input type="text"  v-model="record.children">
                            </li>

                            <li><b>Father's Name:</b></li>
                            <li>
                                <input type="text"  v-model="record.father">
                            </li>

                            <li><b>Father's Occupation:</b></li>
                            <li>
                                <input type="text"  v-model="record.father_ocupation">
                            </li>

                            <li><b>Mother's Name:</b></li>
                            <li>
                                <input type="text"  v-model="record.mother">
                            </li>

                            <li><b>Mother's Occupation:</b></li>
                            <li>
                                <input type="text"  v-model="record.mother_ocupation">
                            </li>

                            <li><b>Name of Siblings:</b></li>
                            <li>
                                <input type="text"  v-model="record.siblings">
                            </li>

                            <li><b>TIN Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.tin">
                            </li>

                            <li><b>SSS Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.sss">
                            </li>

                            <li><b>Philhealth Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.philhealth">
                            </li>

                            <li><b>Pag-ibig Number:</b></li>
                            <li>
                                <input type="text"  v-model="record.pagibig">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <div class="subtitle">Person to contact in case of emergency</div>

                        <ul>
                            <li><b>Name:</b></li>
                            <li>
                                <input type="text" v-model="record.emergency_name">
                            </li>

                            <li><b>Address:</b></li>
                            <li>
                                <input type="text" v-model="record.emergency_address">
                            </li>

                            <li><b>His/Her Contact Number:</b></li>
                            <li>
                                <input type="text" v-model="record.emergency_contact">
                            </li>

                            <li><b>Relationship:</b></li>
                            <li>
                                <input type="text" v-model="record.emergency_relationship">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <div class="subtitle">Educational Background</div>

                        <ul>
                            <li><b>Elementary:</b></li>
                            <li>
                                <input type="text" v-model="record.education_elementary">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li>
                                <input type="text" v-model="record.education_elementary_year">
                            </li>

                            <li><b>High School:</b></li>
                            <li>
                                <input type="text" v-model="record.education_highschool">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li>
                                <input type="text" v-model="record.education_highschool_year">
                            </li>

                            <li><b>College:</b></li>
                            <li>
                                <input type="text" v-model="record.education_college">
                            </li>

                            <li><b>Year Graduated:</b></li>
                            <li>
                                <input type="text" v-model="record.education_college_year">
                            </li>

                        </ul>

                    </div>

                    <div class="box-content">
                        <div class="subtitle">Employment Record</div>

                        <ul>
                            <li><b>Company:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_company1">
                            </li>

                            <li><b>Position:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_position1">
                            </li>

                            <li><b>Period:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_period1">
                            </li>

                            <li><b>Company:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_company2">
                            </li>

                            <li><b>Position:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_position2">
                            </li>

                            <li><b>Period:</b></li>
                            <li>
                                <input type="text" v-model="record.employment_period2">
                            </li>
                        </ul>

                        <div class="btnbox">
                            <a class="btn red" @click="cancel_input()">Cancel</a>
                            <a class="btn" @click="save_input()">Save</a>
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
                        <h6>Employee Data Sheet</h6>
                        <a href="javascript: void(0)" @click="close_view">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <table class="data_sheet">

                            <tbody>
                            <tr>
                                <td colspan="2">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{ record.department }} {{ record.department == '   ' ? '' : '>>' }} {{ record.title}}</span>
                                </td>

                                <td>
                                    <span class="caption">Date:</span>
                                    <span class="content">{{ record.updated_str }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Name:</span>
                                    <span class="content">{{ record.first_name }} {{ record.middle_name }}  {{ record.surname }}</span>
                                </td>

                                <td>
                                    <span class="caption">Gender:</span>
                                    <span class="content">{{ record.gender == 'M' ? 'Male' : (record.gender == 'F' ? 'Female' : '') }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Present Address:</span>
                                    <span class="content">{{ record.present_address }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Permanent Address:</span>
                                    <span class="content">{{ record.permanent_address }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Telephone No:</span>
                                    <span class="content">{{ record.telephone }}</span>
                                </td>

                                <td>
                                    <span class="caption">Cellphone No:</span>
                                    <span class="content">{{ record.cellphone }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Email Address:</span>
                                    <span class="content">{{ record.email }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Date of Birth:</span>
                                    <span class="content">{{ record.birthday }}</span>
                                </td>

                                <td>
                                    <span class="caption">Place of Birth:</span>
                                    <span class="content">{{record.birthplace}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Civil Status:</span>
                                    <span class="content">{{record.civil_status}}</span>
                                </td>

                                <td>
                                    <span class="caption">Citizenship:</span>
                                    <span class="content">{{record.citizenship}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Height:</span>
                                    <span class="content">{{record.height}}</span>
                                </td>

                                <td>
                                    <span class="caption">Weight:</span>
                                    <span class="content">{{record.weight}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Religion:</span>
                                    <span class="content">{{record.religion}}</span>
                                </td>

                                <td>
                                    <span class="caption">Language/Dialect Spoken:</span>
                                    <span class="content">{{record.language}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Medical Condition/Allergies:</span>
                                    <span class="content">{{record.medical}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Spouse:</span>
                                    <span class="content">{{record.spouse}}</span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.spouse_ocupation}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name of Children:</span>
                                    <span class="content">{{record.children}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Father's Name:</span>
                                    <span class="content">{{record.father}}</span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.father_ocupation}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Mother's Name:</span>
                                    <span class="content">{{record.mother}}</span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.mother_ocupation}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name of Siblings:</span>
                                    <span class="content">{{record.siblings}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">TIN No:</span>
                                    <span class="content">{{record.tin}}</span>
                                </td>

                                <td>
                                    <span class="caption">SSS No:</span>
                                    <span class="content">{{record.sss}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Philhealth No:</span>
                                    <span class="content">{{record.philhealth}}</span>
                                </td>

                                <td>
                                    <span class="caption">Pag-ibig No:</span>
                                    <span class="content">{{record.pagibig}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Person to contact in case of emergency</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name:</span>
                                    <span class="content">{{record.emergency_name}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Address:</span>
                                    <span class="content">{{record.emergency_address}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">His/Her Contact Number:</span>
                                    <span class="content">{{record.emergency_contact}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Relationship:</span>
                                    <span class="content">{{record.emergency_relationship}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Educational Background</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Elementary:</span>
                                    <span class="content">{{record.education_elementary}}</span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_elementary_year}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">High School:</span>
                                    <span class="content">{{record.education_highschool}}</span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_highschool_year}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">College:</span>
                                    <span class="content">{{record.education_college}}</span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_college_year}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Employment Record</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company:</span>
                                    <span class="content">{{record.employment_company1}}</span>
                                </td>

                                <td style="border-left: none;">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{record.employment_position1}}</span>
                                </td>

                                <td>
                                    <span class="caption">Period:</span>
                                    <span class="content">{{record.employment_period1}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company:</span>
                                    <span class="content">{{record.employment_company2}}</span>
                                </td>

                                <td style="border-left: none;">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{record.employment_position2}}</span>
                                </td>

                                <td>
                                    <span class="caption">Period:</span>
                                    <span class="content">{{record.employment_period2}}</span>
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



            <!-- Review Modal start -->
            <div id="Modal_review" class="modal">

                <!-- Modal content -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6>Employee Data Sheet</h6>
                        <a href="javascript: void(0)" @click="close_review">
                            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                        </a>
                    </div>


                    <div class="box-content">

                        <table class="data_sheet">

                            <tbody>
                            <tr>
                                <td colspan="2">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{ record.department }} {{ record.department == '   ' ? '' : '>>' }} {{ record.title}}</span>
                                </td>

                                <td>
                                    <span class="caption">Date:</span>
                                    <span class="content">{{ record.updated_str }} <span class="new_value" v-if="record.updated_str !=r_record.updated_str"> → {{r_record.updated_str}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Name:</span>
                                    <span class="content">{{ record.full_name }} <span class="new_value" v-if="record.full_name !=r_record.full_name"> → {{r_record.full_name}} </span> </span>
                                </td>

                                <td>
                                    <span class="caption">Gender:</span>
                                    <span class="content">{{ record.gender == 'M' ? 'Male' : (record.gender == 'F' ? 'Female' : '') }}  <span class="new_value" v-if="record.gender !=r_record.gender"> → {{ r_record.gender == 'M' ? 'Male' : (r_record.gender == 'F' ? 'Female' : '') }} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Present Address:</span>
                                    <span class="content">{{ record.present_address }} <span class="new_value" v-if="record.present_address !=r_record.present_address"> → {{r_record.present_address}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Permanent Address:</span>
                                    <span class="content">{{ record.permanent_address }} <span class="new_value" v-if="record.permanent_address !=r_record.permanent_address"> → {{r_record.permanent_address}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Telephone No:</span>
                                    <span class="content">{{ record.telephone }} <span class="new_value" v-if="record.telephone !=r_record.telephone"> → {{r_record.telephone}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Cellphone No:</span>
                                    <span class="content">{{ record.cellphone }} <span class="new_value" v-if="record.cellphone !=r_record.cellphone"> → {{r_record.cellphone}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Email Address:</span>
                                    <span class="content">{{ record.email }} <span class="new_value" v-if="record.email !=r_record.email"> → {{r_record.email}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Date of Birth:</span>
                                    <span class="content">{{ record.birthday }} <span class="new_value" v-if="record.birthday !=r_record.birthday"> → {{r_record.birthday}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Place of Birth:</span>
                                    <span class="content">{{record.birthplace}} <span class="new_value" v-if="record.birthplace !=r_record.birthplace"> → {{r_record.birthplace}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Civil Status:</span>
                                    <span class="content">{{record.civil_status}} <span class="new_value" v-if="record.civil_status !=r_record.civil_status"> → {{r_record.civil_status}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Citizenship:</span>
                                    <span class="content">{{record.citizenship}} <span class="new_value" v-if="record.citizenship !=r_record.citizenship"> → {{r_record.citizenship}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Height:</span>
                                    <span class="content">{{record.height}} <span class="new_value" v-if="record.height !=r_record.height"> → {{r_record.height}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Weight:</span>
                                    <span class="content">{{record.weight}} <span class="new_value" v-if="record.weight !=r_record.weight"> → {{r_record.weight}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Religion:</span>
                                    <span class="content">{{record.religion}} <span class="new_value" v-if="record.religion !=r_record.religion"> → {{r_record.religion}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Language/Dialect Spoken:</span>
                                    <span class="content">{{record.language}} <span class="new_value" v-if="record.language !=r_record.language"> → {{r_record.language}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Medical Condition/Allergies:</span>
                                    <span class="content">{{record.medical}} <span class="new_value" v-if="record.medical !=r_record.medical"> → {{r_record.medical}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Spouse:</span>
                                    <span class="content">{{record.spouse}} <span class="new_value" v-if="record.spouse !=r_record.spouse"> → {{r_record.spouse}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.spouse_ocupation}} <span class="new_value" v-if="record.spouse_ocupation !=r_record.spouse_ocupation"> → {{r_record.spouse_ocupation}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name of Children:</span>
                                    <span class="content">{{record.children}} <span class="new_value" v-if="record.children !=r_record.children"> → {{r_record.children}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Father's Name:</span>
                                    <span class="content">{{record.father}} <span class="new_value" v-if="record.father !=r_record.father"> → {{r_record.father}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.father_ocupation}} <span class="new_value" v-if="record.father_ocupation !=r_record.father_ocupation"> → {{r_record.father_ocupation}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Mother's Name:</span>
                                    <span class="content">{{record.mother}} <span class="new_value" v-if="record.mother !=r_record.mother"> → {{r_record.mother}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Occupation:</span>
                                    <span class="content">{{record.mother_ocupation}} <span class="new_value" v-if="record.mother_ocupation !=r_record.mother_ocupation"> → {{r_record.mother_ocupation}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name of Siblings:</span>
                                    <span class="content">{{record.siblings}} <span class="new_value" v-if="record.siblings !=r_record.siblings"> → {{r_record.siblings}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">TIN No:</span>
                                    <span class="content">{{record.tin}} <span class="new_value" v-if="record.tin !=r_record.tin"> → {{r_record.tin}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">SSS No:</span>
                                    <span class="content">{{record.sss}} <span class="new_value" v-if="record.sss !=r_record.sss"> → {{r_record.sss}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Philhealth No:</span>
                                    <span class="content">{{record.philhealth}} <span class="new_value" v-if="record.philhealth !=r_record.philhealth"> → {{r_record.philhealth}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Pag-ibig No:</span>
                                    <span class="content">{{record.pagibig}} <span class="new_value" v-if="record.pagibig !=r_record.pagibig"> → {{r_record.pagibig}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Person to contact in case of emergency</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Name:</span>
                                    <span class="content">{{record.emergency_name}} <span class="new_value" v-if="record.emergency_name !=r_record.emergency_name"> → {{r_record.emergency_name}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Address:</span>
                                    <span class="content">{{record.emergency_address}} <span class="new_value" v-if="record.emergency_address !=r_record.emergency_address"> → {{r_record.emergency_address}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">His/Her Contact Number:</span>
                                    <span class="content">{{record.emergency_contact}} <span class="new_value" v-if="record.emergency_contact !=r_record.emergency_contact"> → {{r_record.emergency_contact}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="caption">Relationship:</span>
                                    <span class="content">{{record.emergency_relationship}} <span class="new_value" v-if="record.emergency_relationship !=r_record.emergency_relationship"> → {{r_record.emergency_relationship}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Educational Background</span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">Elementary:</span>
                                    <span class="content">{{record.education_elementary}} <span class="new_value" v-if="record.education_elementary !=r_record.education_elementary"> → {{r_record.education_elementary}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_elementary_year}} <span class="new_value" v-if="record.education_elementary_year !=r_record.education_elementary_year"> → {{r_record.education_elementary_year}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">High School:</span>
                                    <span class="content">{{record.education_highschool}} <span class="new_value" v-if="record.education_highschool !=r_record.education_highschool"> → {{r_record.education_highschool}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_highschool_year}} <span class="new_value" v-if="record.education_highschool_year !=r_record.education_highschool_year"> → {{r_record.education_highschool_year}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <span class="caption">College:</span>
                                    <span class="content">{{record.education_college}} <span class="new_value" v-if="record.education_college !=r_record.education_college"> → {{r_record.education_college}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Year Graduated:</span>
                                    <span class="content">{{record.education_college_year}} <span class="new_value" v-if="record.education_college_year !=r_record.education_college_year"> → {{r_record.education_college_year}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3">
                                    <span class="block_title">Employment Record</span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company:</span>
                                    <span class="content">{{record.employment_company1}} <span class="new_value" v-if="record.employment_company1 !=r_record.employment_company1"> → {{r_record.employment_company1}} </span></span>
                                </td>

                                <td style="border-left: none;">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{record.employment_position1}} <span class="new_value" v-if="record.employment_position1 !=r_record.employment_position1"> → {{r_record.employment_position1}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Period:</span>
                                    <span class="content">{{record.employment_period1}} <span class="new_value" v-if="record.employment_period1 !=r_record.employment_period1"> → {{r_record.employment_period1}} </span></span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="caption">Company:</span>
                                    <span class="content">{{record.employment_company2}} <span class="new_value" v-if="record.employment_company2 !=r_record.employment_company2"> → {{r_record.employment_company2}} </span></span>
                                </td>

                                <td style="border-left: none;">
                                    <span class="caption">Position:</span>
                                    <span class="content">{{record.employment_position2}} <span class="new_value" v-if="record.employment_position2 !=r_record.employment_position2"> → {{r_record.employment_position2}} </span></span>
                                </td>

                                <td>
                                    <span class="caption">Period:</span>
                                    <span class="content">{{record.employment_period2}} <span class="new_value" v-if="record.employment_period2 !=r_record.employment_period2"> → {{r_record.employment_period2}} </span></span>
                                </td>
                            </tr>
                            </tbody>

                        </table>

                        <div class="btnbox" style="margin-bottom: -20px;">
                            <a class="btn red" @click="do_reject_record(record)">Reject</a>
                            <a class="btn" @click="do_approve_record(record)">Approve</a>
                        </div>

                    </div>

                </div>

            </div>
            <!-- Review Modal end -->



            <!-- Authorize Modal start -->
        <div id="Modal_authorize" class="modal">

            <!-- Modal content -->
            <div class="modal-content">

                <div class="modal-header">
                    <h6>Employee Data Sheet</h6>
                </div>


                <div class="box-content">

                    <div class="authorize_dialog">
                        <p>I hereby give my consent to Feliix Inc. to collect, store, process, transfer and update personal data as necessary. Only authorized personnel are permitted and have access to the collected information and will treat under strict confidentiality.</p>
                        <p>I certify that the information given above is true and correct.</p>

                        <div class="employee_name">
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_name">
                        </div>

                        <div class="date_signed">
                            <img :src="'https://storage.googleapis.com/feliiximg/' + record.sig_date">
                        </div>

                    </div>


                    <div class="btnbox" style="margin-bottom: -20px;">
                        <a class="btn red" @click="toggle_auth()">Close</a>
                        <a class="btn red" @click="auth_reset(record.user_id)">Reset</a>
                    </div>

                </div>

            </div>

        </div>
        <!-- Authorize Modal end -->


        </div>
        
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/sweetalert2@9.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script defer src="js/employee_data_sheet.js"></script>
</html>
