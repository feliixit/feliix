<?php include 'check.php';?>
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
            $user_id = "";
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;

            $GLOBALS['username'] = $decoded->data->username;
            //$GLOBALS['position'] = $decoded->data->position;
            //$GLOBALS['department'] = $decoded->data->department;

            if(!is_numeric($user_id))
                header( 'location:index' );

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
    <meta charset='utf-8'/>
    <title>
        Personal Datebook Calendar
    </title>

    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

    <link rel="stylesheet" href="css/vue-select.css" type="text/css">

    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.1/main.min.css'>
    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.0/main.min.css'>
    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.css'>
    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.css'>


    <script src='js/@fullcalendar/core@4.3.1/main.min.js'></script>
    <script src='js/@fullcalendar/daygrid@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.js'></script>

    <script src="js/moment.js"></script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #task_calendar {
            max-width: 90%;
            margin: 40px auto 30px;
        }

        .fc-daygrid-event {
            white-space: initial !important;
        }

        .fc-event-title {
            display: inline !important;
        }

        .fc-day-grid-event .fc-content {
            white-space: inherit;
        }

        .fc-button-group>.fc-button {
            font-size: 14px;
        }

        #filter {
            display: flex;
            padding: 35px 5vw 5px;
        }

        #filter input {
            width: 200px;
            margin: 0 20px 0 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow: visible;
            font-family: inherit;
            box-sizing: border-box;
        }

        #filter select {
            width: 250px;
            display: block;
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            word-wrap: normal;
            text-transform: none;
            margin: 0 30px 0 0;
            font-family: inherit;
            box-sizing: border-box;
            overflow: visible !important;
        }

        #filter button.btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            -webkit-appearance: button;
            text-transform: none;
            overflow: visible;
            margin: 0;
            font-family: inherit;
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        #filter button.btn:hover {
            color: #fff;
            background-color: #0069d9;
            border-color: #0062cc;
            text-decoration: none;
        }

        #addmeeting-form, #editmeeting-form {
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 300;
            max-width: 90%;
            margin: 0 auto 40px;
        }

        #addmeeting-form fieldset, #editmeeting-form fieldset {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px 30px;
        }

        #addmeeting-form legend, #editmeeting-form legend {
            margin-left: 10px;
            font-size: 24px;
            padding: 0 5px;
        }

        #addmeeting-form input, #editmeeting-form input {
            width: 160px;
            margin-right: 10px;
            height: 35px;
        }

        #addmeeting-form input[type="text"], #editmeeting-form input[type="text"], #addmeeting-form input[type="file"], #editmeeting-form input[type="file"] {
            width: 500px;
        }

        .meetingform-buttons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .meetingform-buttons a {
            margin: 0 20px;
            width: 80px;
            text-align: center;
            color: white;
        }

        .meetingform-buttons a:hover {
            color: white;
        }

        .meetingform-buttons_edit {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .meetingform-buttons_edit a {
            margin: 0 20px;
            width: 80px;
            text-align: center;
            color: white;
        }

        .meetingform-buttons_edit a:hover {
            color: white;
        }

        .meetingform-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .meetingform-item label {
            color: #00811e;
            font-size: 14px;
            font-weight: 500;
            width: 100px;
        }

        .meetingform-item input,
        .meetingform-item select,
        .meetingform-item textarea {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
        }

        .meetingform-item input:disabled,
        .meetingform-item select:disabled,
        .meetingform-item textarea:disabled {
            border: 1px solid #707070;
            font-size: 14px;
            outline: none;
            opacity: 1;
        }

        .meetingform-item.colorpicker {
            margin-top: 17px;
        }

        .meetingform-item.colorpicker > div {
            display: flex;
            align-items: center;
        }

        .meetingform-item.colorpicker > div > div {
            display: inline-block;
            margin: 0 20px -10px 0;
            position: relative;
            min-height: 1.5rem;
        }

        .meetingform-item.colorpicker > div > div > input[type='radio'] {
            border: none;
            margin-right: -3px!important;
        }

        .meetingform-item.colorpicker > div > div > input[type='radio']::before {
            color: #6c757d;
            font-size: 18px;
        }

        .meetingform-item.colorpicker > div > div > label {
            width: 18px;
            height: 18px;
            margin-left: 2px;
        }

        .meetingform-item.colorpicker > div > div > label > span {
            position: absolute;
            font-size: 14px;
            top: 4px;
            left: 25px;
        }

        .meetingform-item.colorpicker > div > div.custom-control:first-of-type {
            margin-left: 0;
        }

        .meetingform-item.colorpicker > div > div.custom-control > label.custom-control-label {
            width: 18px;
            height: 18px;
            margin-left: 2px;
        }

        .meetingform-item.colorpicker > div > div.custom-control:last-of-type > label.custom-control-label {
            font-size: 14px;
            width: 40px;
        }

        .meetingform-item.colorpicker input[type=radio]+Label::before {
            content: "";
        }

        .meetingform-item.colorpicker > div > input[type='color'] {
            margin-left: 5px;
            width: 30px !important;
            padding: 2px;
            margin-bottom: 5px;
        }

        #addnotes-form, #editnotes-form {
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 300;
            max-width: 90%;
            margin: 0 auto 40px;
        }

        #addnotes-form fieldset, #editnotes-form fieldset {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px 30px;
        }

        #addnotes-form legend, #editnotes-form legend {
            margin-left: 10px;
            font-size: 24px;
            padding: 0 5px;
        }

        #addnotes-form input, #editnotes-form input {
            width: 160px;
            margin-right: 10px;
            height: 35px;
        }

        #addnotes-form input[type="text"], #editnotes-form input[type="text"], #addnotes-form input[type="file"], #editnotes-form input[type="file"] {
            width: 500px;
        }

        .file-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .file-element {
            margin-bottom: 5px;
            margin-left: 105px;
        }

        .file-element input[type="checkbox"] + label::before {
            color: #007bff;
            font-size: 20px;
        }

        .file-element input[type="checkbox"]:disabled + label::before {
            color: rgba(127, 189, 255, 0.8);
        }

        .file-element a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }

        .file-element a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .fc-daygrid-event {
            white-space: initial !important;
        }

        .fc-event-title {
            display: inline !important;
        }

        .select_disabled {
            pointer-events:none;
            color: #bfcbd9;
            cursor: not-allowed;
            background-image: none;
            background-color: #eef1f6;
            border-color: #d1dbe5;
        }

        #vs1__combobox, #vs2__combobox, #vs3__combobox, #vs4__combobox, #vs5__combobox, #vs6__combobox, #vs7__combobox, #vs8__combobox, #vs9__combobox, #vs10__combobox, #vs11__combobox, #vs12__combobox {
            border: 1px solid #707070;
            border-radius: 0;
        }

        #vs1__listbox, #vs2__listbox, #vs3__listbox, #vs4__listbox, #vs5__listbox, #vs6__listbox, #vs7__listbox, #vs8__listbox, #vs9__listbox, #vs10__listbox, #vs11__listbox, #vs12__listbox {
            border: none;
            border-radius: 0;
            margin-top: 0;
        }

        #vs1__listbox li, #vs2__listbox li, #vs3__listbox li, #vs4__listbox li, #vs5__listbox li, #vs6__listbox li, #vs7__listbox li, #vs8__listbox li, #vs9__listbox li, #vs10__listbox li, #vs11__listbox li, #vs12__listbox li {
            border-right: 2px solid #707070;
            font-size: 12px;
        }

        div.order_for_approval {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 20px 30px 10px;
            font-family: "M PLUS 1p", Arial, Helvetica, "LiHei Pro", 微軟正黑體, "Microsoft JhengHei", 新細明體, sans-serif;
            font-weight: 300;
            max-width: 90%;
            margin: 0 auto 40px;
            position: relative;
        }

        div.order_for_approval > span {
            display: inline-block;
            position: absolute;
            font-size: 22px;
            background: white;
            top: -17px;
            left: 45px;
            padding: 0 10px;
        }

        div.order_for_approval > a {
            font-size: 16px;
            color: rgb(243, 112, 88);
            display: block;
            min-width: 180px;
            margin: 5px 0;
        }

        @media (min-width: 576px) {

            .modal-xl {
                max-width: 90vw;
            }
        }

        @media (min-width: 992px) {
            .modal-xl {
                max-width: 800px;
            }

        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }


    </style>

</head>

<body>


<div style="background: rgb(2,106,167); padding: 0.5vh; height:7.5vh;">

    <a href="default" style="margin-left:1vw; position: relative; top:-10%;"><span style="color: white;">&#9776;</span></a>

    <a href="default"><span
            style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span></a>

</div>

<div id="filter">

    <input type="month" class="form-control" id="sdate">
    <input type="month" class="form-control" id="edate">

    <select  v-model="user_id">
        <option v-for="user in users" :value="user.id">{{user.username}}</option>
    </select>

    <button class="btn btn-primary" @click="getInitial()">Query</button>
</div>


<div id='task_calendar'></div>


<div id='app' style='padding-bottom: 20px;'>

    <!-- 會議事件被點擊時，要展示會議內容的表單 -->
    <form id="editmeeting-form" style="display: none;">
        <fieldset disabled>
            <legend style="max-width: 250px;">Meeting Information</legend>

            <div class="meetingform-item">
                <label>Subject:</label>
                <input type="text" id="oldSubject">
            </div>

            <div class="meetingform-item">
                <label>Project:</label>
                <input type="text" id="oldProject"
                       placeholder="Project name will be added ahead of subject if filled in">
            </div>

            <div class="meetingform-item colorpicker">
                <label>Color:</label>
                <div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_orange"
                               value="#FECC28" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_red"
                               value="#4EB5BB" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_purple"
                               value="#009858" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_green"
                               value="#A671AD" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_blue"
                               value="#F19DB4" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_teal"
                               value="#141415" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color" id="old_sc_color_other"
                               value="1" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_other"><span>Other</span></label>
                    </div>

                    <input type="color" class="form-control" id="old_sc_color">

                </div>

            </div>

            <div class="meetingform-item">
                <label>Creator:</label>
                <input type="text" id="oldCreator">
            </div>

            <div class="meetingform-item">
                <label>Attendee:</label>
                <v-select id="oldAttendee" :options="users" attach chips label="username" v-model="old_attendee"
                          multiple></v-select>
            </div>

            <div class="meetingform-item">
                <label>Time:</label>
                <input type="date" id="oldDate">
                <input type="time" id="oldStartTime">
                <input type="time" id="oldEndTime">
            </div>

            <div class="meetingform-item">
                <label>Location:</label>
                <input type="text" id="oldLocation">
            </div>

            <div class="meetingform-item">
                <label>Content:</label>
                <textarea style="flex-grow: 1; resize: none;" rows="3" id="oldContent"></textarea>

            </div>

            <div class="meetingform-item" id="upload_input">
                <label>File:</label>
                <input type="file" ref="file_old" id="fileload_old" name="file_old[]"
                       onChange="onChangeFileUploadOld(event)" multiple>
            </div>

            <div class="file-container" id="sc_product_files_old">


            </div>

            <input id="sc_product_files_hide" style="display: none;" value="">


        </fieldset>
        <div class="meetingform-buttons_edit">
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')"
               id="btn_close">Close</a>
            <!-- 在這個頁面，會議事件被點擊時，只需要展示內容，使用者只能關閉表單，除此之外無法作任何其他動作
            <a class="btn small" id="btn_delete">Delete</a>
            <a class="btn small green" id="btn_edit">Edit</a>
            <a class="btn small" id="btn_cancel">Cancel</a>
            <a class="btn small green" id="btn_save">Save</a> -->
        </div>


    </form>

</div>

<div id='memo' style='padding-bottom: 20px;'>

    <!-- 新增 個人記事 的表單 -->
    <form id="addnotes-form" style="display: none;">
        <fieldset>
            <legend style="max-width: 95px; padding: 0 15px;">Notes</legend>

            <div class="meetingform-item">
                <label>Subject:</label>
                <input type="text" id="newSubject">
            </div>

            <div class="meetingform-item colorpicker">
                <label>Color:</label>
                <div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_orange"
                               value="#FECC28" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_red"
                               value="#4EB5BB" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_purple"
                               value="#009858" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_green"
                               value="#A671AD" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_blue"
                               value="#F19DB4" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_teal"
                               value="#141415" onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="new_sc_color" id="new_sc_color_other" value="1"
                               onchange="new_enable_forOther(this);">
                        <label for="new_sc_color_other"><span>Other</span></label>
                    </div>

                    <input type="color" class="form-control" id="new_sc_color">

                </div>

            </div>

            <div class="meetingform-item">
                <label>Time:</label>
                <input type="date" id="newDate">
                <input type="time" id="newStartTime">
                <input type="time" id="newEndTime">
            </div>

            <div class="meetingform-item">
                <label>Location:</label>
                <input type="text" id="newLocation">
            </div>

            <div class="meetingform-item">
                <label>Content:</label>
                <textarea style="flex-grow: 1; resize: none;" rows="3" id="newContent"></textarea>

            </div>

            <div class="meetingform-item" id="upload_input">
                <label>File:</label>
                <input type="file" ref="file" id="fileload" name="file[]" onChange="onChangeFileUpload(event)" multiple>
            </div>

            <div class="file-container" id="sc_product_files">

            </div>

            <input id="sc_product_files_hide" style="display: none;" value="">


            <div class="meetingform-buttons">
                <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addnotes-form')">Close</a>

                <a class="btn small green" id="btn_add">Add</a>
            </div>

        </fieldset>
    </form>


    <!-- 修改 個人記事 的表單 -->
    <form id="editnotes-form" style="display: none;">
        <fieldset disabled>
            <legend style="max-width: 95px; padding: 0 15px;">Notes</legend>

            <div class="meetingform-item">
                <label>Subject:</label>
                <input type="text" id="oldSubject_note">
            </div>

            <div class="meetingform-item colorpicker">
                <label>Color:</label>
                <div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_orange"
                               value="#FECC28" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_red"
                               value="#4EB5BB" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_purple"
                               value="#009858" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_green"
                               value="#A671AD" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_blue"
                               value="#F19DB4" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_teal"
                               value="#141415" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div>
                        <input type="radio" class="alone" name="old_sc_color_note" id="old_sc_color_other_note"
                               value="1" onchange="old_enable_forOther(this);">
                        <label for="old_sc_color_other_note"><span>Other</span></label>
                    </div>

                    <input type="color" class="form-control" id="old_sc_color_note">

                </div>

            </div>

            <div class="meetingform-item">
                <label>Creator:</label>
                <input type="text" id="oldCreator_note">
            </div>

            <div class="meetingform-item">
                <label>Time:</label>
                <input type="date" id="oldDate_note">
                <input type="time" id="oldStartTime_note">
                <input type="time" id="oldEndTime_note">
            </div>

            <div class="meetingform-item">
                <label>Location:</label>
                <input type="text" id="oldLocation_note">
            </div>

            <div class="meetingform-item">
                <label>Content:</label>
                <textarea style="flex-grow: 1; resize: none;" rows="3" id="oldContent_note"></textarea>

            </div>

            <div class="meetingform-item" id="upload_input">
                <label>File:</label>
                <input type="file" ref="file_old" id="fileload_old_note" name="file_old[]"
                       onChange="onChangeFileUploadOldNote(event)" multiple>
            </div>

            <div class="file-container" id="sc_product_files_old_note">


            </div>

            <input id="sc_product_files_hide_note" style="display: none;" value="">


        </fieldset>
        <div class="meetingform-buttons_edit">
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editnotes-form')"
               id="btn_close">Close</a>
            <a class="btn small" id="btn_delete">Delete</a>
            <a class="btn small green" id="btn_edit">Edit</a>
            <a class="btn small" id="btn_cancel">Cancel</a>
            <a class="btn small green" id="btn_save">Save</a>
        </div>


    </form>

    <!-- 具有 Order for Taiwan (Role 3) 權限的人，才能看到以下區塊 -->
    <div class="order_for_approval" v-if="access3">
        <span>Order for Approval</span>
        <template v-for="(od, index) in orders">
            <a v-if="od.order_type == 'taiwan'" v-bind:href="'order_taiwan_p2?id=' + od.id" target="_blank">• {{ od.serial_name }}  {{ od.od_name }}</a>
            <a v-if="od.order_type == 'stock'" v-bind:href="'order_taiwan_stock_p2?id=' + od.id" target="_blank">• {{ od.serial_name }}  {{ od.od_name }}</a>
            <a v-if="od.order_type == 'sample'" v-bind:href="'order_taiwan_sample_p2?id=' + od.id" target="_blank">• {{ od.serial_name }}  {{ od.od_name }}</a>
            <a v-if="od.order_type == 'mockup'" v-bind:href="'order_taiwan_mockup_p2?id=' + od.id" target="_blank">• {{ od.serial_name }}  {{ od.od_name }}</a>
        </template>
    </div>

</div>


</body>


<script>

    var calendar_task;
    var eventObj;
    var event_array_task = [];

    var my_id = 0;
    var my_level = 0;
    var my_department = "";

    function CanClose(uid, creator_id, level, creator_level, department){
        let can_close = false;

        if(department === 'Lighting')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }
                

            if(level === "LIGHTING VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" || creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'Office Systems')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "OFFICE SPACE VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" || creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'AD')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "OPERATIONS MANAGER")
            {
                if(creator_level === "ASSISTANT OPERATIONS MANAGER")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'DS')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "BRAND MANAGER")
            {
                if(creator_level === "ASSISTANT BRAND MANAGER")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'LT_T')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }
                

            if(level === "LIGHTING VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" || creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT LIGHTING VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR LIGHTING VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'OS_T')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "OFFICE SPACE VALUE CREATION DIRECTOR" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" || creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR" )
            {
                if(creator_level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR OFFICE SPACE VALUE CREATION SUPERVISOR" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "CUSTOMER VALUE SUPERVISOR" || creator_level === "SENIOR CUSTOMER VALUE SUPERVISOR")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'SLS')
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "CUSTOMER VALUE DIRECTOR" || level === "STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && creator_level != "CHIEF ADVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT CUSTOMER VALUE DIRECTOR" || level === "ASSISTANT STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "STORE MANAGER")
                {
                    can_close = true;
                }
            }

            if(level === "SENIOR CUSTOMER VALUE SUPERVISOR" || level === "CUSTOMER VALUE SUPERVISOR" || level === "JR. ACCOUNT EXECUTIVE" || level === "SR. STORE SALES EXECUTIVE" || level === "STORE SALES EXECUTIVE")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "STORE MANAGER" && 
                   creator_level != "ASSISTANT CUSTOMER VALUE DIRECTOR" && 
                   creator_level != "ASSISTANT STORE MANAGER" && 
                   creator_level != "SENIOR CUSTOMER VALUE SUPERVISOR" && 
                   creator_level != "CUSTOMER VALUE SUPERVISOR" && 
                   creator_level != "JR. ACCOUNT EXECUTIVE" && 
                   creator_level != "SR. STORE SALES EXECUTIVE" && 
                   creator_level != "STORE SALES EXECUTIVE")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'ENG' || level === 'LIGHTING VALUE CREATION DIRECTOR' || level === 'OFFICE SPACE VALUE CREATION DIRECTOR')  // 20220321 for service leave
        {
            if(level === "MANAGING DIRECTOR" || level === "CHIEF ADVISOR")
            {
                if(creator_level === "MANAGING DIRECTOR" || creator_level === "CHIEF ADVISOR")
                {
                    can_close = false;
                }
                else
                    can_close = true;
            }

            if(level === "ENGINEERING MANAGER" || level === 'LIGHTING VALUE CREATION DIRECTOR' || level === 'OFFICE SPACE VALUE CREATION DIRECTOR')  // 20220321 for service leave
            {
                if(creator_level === "ASSISTANT ENGINEERING MANAGER")
                {
                    can_close = true;
                }
            }
        }

        if(creator_id === uid)
            can_close = true;

        return can_close;
    }

    function hideWindow(target) {
        $(target).hide();
    }

    
    $(document).on("click", "#btn_save", function () {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate_note").val() + " " + $("#oldStartTime_note").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate_note").val() + " " + $("#oldEndTime_note").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            memo.warning('Start time must less than End time!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        if ($("#oldDate_note").val() === '') {
            memo.warning('Please select Date!');
            return;
        }

        if ($("#oldEndTime_note").val() === '') {
            memo.warning('Please select End time!');
            return;
        }

        if ($("#oldStartTime_note").val() === '') {
            memo.warning('Please select Start time!');
            return;
        }

        if ($("#oldSubject_note").val() === '') {
            memo.warning('Please enter subject!');
            return;
        }

        var names = memo.old_attendee.map(function (item) {
            return item['username'];
        });

        // if (names.toString().trim() === '') {
        //     memo.warning('Please select attendee!');
        //     return;
        // }

        if ($("#oldContent_note").val().trim() === '') {
            memo.warning('Please enter content!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        //表單變成不可修改
        $('#editnotes-form > fieldset').prop('disabled', true);
        //$("oldAttendee").prop('disabled', true);
        $("#oldAttendee_note").addClass("select_disabled");

        //##修改後的內容 update到資料庫
        var id = eventObj.id.substring(1);

        var file_elements = document.getElementsByName("file_elements_old_note");

        var attach = "";
        var remove = "";
        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;

        token = localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject_note").val().trim());
        form_Data.append('project_name', '');
        form_Data.append('message', $("#oldContent_note").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('location', $("#oldLocation_note").val());
        form_Data.append('start_time', $("#oldDate_note").val() + "T" + $("#oldStartTime_note").val());
        form_Data.append('end_time', $("#oldDate_note").val() + "T" + $("#oldEndTime_note").val());
        form_Data.append('is_enabled', true);

        var Color_Other = "";
        var Color = "";
        if(document.getElementById("old_sc_color_other_note").checked)
            Color = document.getElementById("old_sc_color_note").value;
        else
            Color_Other = "";

        var colors = document.getElementsByName("old_sc_color_note");
        for(var i=0; i<colors.length; i++)
        {
            if(colors[i].checked && colors[i].value != "1")
                Color = colors[i].value;
        }

        form_Data.append("color", Color);
        form_Data.append("color_other", Color_Other);

        // if(document.getElementById("old_sc_color").checked)
        //     form_Data.append("color_other", $("#old_sc_color").val().trim());
        // else
        //     form_Data.append("color_other", "");

        if($("#old_sc_color_note").val().trim() == "" && !document.getElementById("old_sc_color_note").checked)
        {
            Swal.fire({
                text: JSON.stringify("Please choose color for meeting."),
                icon: "warning",
                confirmButtonText: "OK",
            });
            return;
        }

        form_Data.append("text_color", "white");

        var item = 0;
        for(let i = 0;i < file_elements.length; i++)
        {
            if(file_elements[i].checked)
            {
                attach += file_elements[i].value + ",";
                for( var j = 0; j < memo.attachments.length; j++ ){
                    let file = memo.attachments[j];
                    if(file.name === file_elements[i].value)
                    {
                        form_Data.append('files[' + item++ + ']', file);
                        break;
                    }
                }
            }
            else
            {
                remove += "'" + file_elements[i].value + "',";
            }
        }

        if(attach !== "")
            attach = attach.slice(0, -1);

        if(remove !== "")
            remove = remove.slice(0, -1);

        form_Data.append('remove', remove);

        var _func = memo;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_notes",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);

                //_func.notify_mail(id, 2);

                var obj_meeting = {
                    title: $("#oldSubject_note").val().trim(),
                    project_name: '',
                    color: Color,
                    color_other: Color_Other,
                    text_color: "white",
                    attendee: names.toString().trim(),
                    items: _func.old_attendee,
                    start: $("#oldDate_note").val() + "T" + $("#oldStartTime_note").val(),
                    end: $("#oldDate_note").val() + "T" + $("#oldEndTime_note").val(),
                    content: $("#oldContent_note").val(),
                    attach:attach,
                    //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
                    creator: "<?php echo $GLOBALS['username'] ?>",
                    location: $("#oldLocation_note").val(),
                };
                $("#oldCreator_note").val(obj_meeting.creator);

                var title = $("#oldSubject_note").val().trim();
                // if($("#oldProject_note").val().trim() !== "")
                //     title = '[ ' + $("#oldProject").val().trim() + ' ] ' + $("#oldSubject").val().trim();

                //把修改後的會議資訊 update 到日曆上
                eventObj.setStart(obj_meeting.start);
                eventObj.setEnd(obj_meeting.end);
                eventObj.setProp("title", title);
                eventObj.setProp("borderColor", obj_meeting.color);
                eventObj.setProp("backgroundColor", obj_meeting.color);
                eventObj.setExtendedProp("description", obj_meeting);

                refreshFileList(attach);

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });


        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

        });

    $(document).on("click", "#btn_cancel", function () {

        //表單變成不可修改
        $('#editnotes-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee_note").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject_note").val(obj_meeting.title);
        $("#oldProject_note").val(obj_meeting.project_name);
        $("#old_sc_color_note").val(obj_meeting.color);
        //$("#oldProject").val(obj_meeting.text_color);
        $("#old_sc_color_other_note").val(obj_meeting.color_other);
        $("#oldCreator_note").val(obj_meeting.creator);
        $("#oldAttendee_note").val(obj_meeting.attendee);
        $("#oldLocation_note").val(obj_meeting.location);
        $("#oldDate_note").val(obj_meeting.start.split("T")[0]);
        $("#oldStartTime_note").val(obj_meeting.start.split("T")[1]);
        $("#oldEndTime_note").val(obj_meeting.end.split("T")[1]);
        $("#oldContent_note").val(obj_meeting.content);
        
        $("#fileload_old_note").val("");
        
        var container = $("#sc_product_files_old_note");
        container.empty();

        if(obj_meeting.attach !== "")
        {
            var files = obj_meeting.attach.split(",");
            files.forEach((element) => {
                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + element + '" name="file_elements_old_note" value="' + element + '" checked disabled>' +
                    '<label for="' + element + '">' + 
                        '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                    '</label>' +
                '</div>';

                $(elm).appendTo(container);
            });
        }

        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

        });

        $(document).on("click", "#btn_delete", function () {

            var _memo = memo;
            if ($("#oldCreator_note")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
                memo.warning('Only note creator can execute this action!');
                return;
            }

            Swal.fire({
                title: "Delete",
                text: "Are you sure to delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {

                    $("#editnotes-form").hide();

                    //##從資料庫中刪除該會議
                    var id = eventObj.id.substring(1);

                    token = localStorage.getItem('token');
                    var form_Data = new FormData();
                    form_Data.append('jwt', token);
                    form_Data.append('action', 7);

                    form_Data.append('id', id);

                    //DELETE table_name WHERE ID=id;
                    $.ajax({
                        url: "api/work_calender_notes",
                        type: "POST",
                        contentType: 'multipart/form-data',
                        processData: false,
                        contentType: false,
                        data: form_Data,

                        success: function (result) {
                            console.log(result);

                            //從日曆中刪除該會議
                            eventObj.remove();

                            //_memo.notify_mail(id, 3);
                        },

                        // show error message to user
                        error: function (xhr, resp, text) {

                        }
                    });

                } else {

                }
            });


            });

    $(document).on("click", "#btn_edit", function () {

        if ($("#oldCreator_note")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            memo.warning('Only note creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editnotes-form > fieldset').prop('disabled', false);
        $("#oldCreator_note").prop('disabled', true);

        $("#oldAttendee_note").removeClass("select_disabled");

        //$("oldAttendee").prop('disabled', false);
        var file_elements = document.getElementsByName("file_elements_old_note");

        var item = 0;
        for(let i = 0;i < file_elements.length; i++)
        {
            file_elements[i].disabled = false;

        }

        //按鈕也會改變
        $("#btn_close").hide();
        $("#btn_delete").hide();
        $("#btn_edit").hide();
        $("#btn_cancel").show();
        $("#btn_save").show();

        });

    $(document).on("click", "#btn_add", function () {
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            memo.warning('Start time must less than End time!');
            return;
        }

        //##任一欄位如果為空則提示欄位不得為空
        if ($("#newDate").val() === '') {
            memo.warning('Please select Date!');
            return;
        }

        if ($("#newEndTime").val() === '') {
            memo.warning('Please select End time!');
            return;
        }

        if ($("#newStartTime").val() === '') {
            memo.warning('Please select Start time!');
            return;
        }

        if ($("#newSubject").val() === '') {
            me.warning('Please enter subject!');
            return;
        }

        var names = memo.attendee.map(function (item) {
            return item['username'];
        });

        // if (names.toString().trim() === '') {
        //     memo.warning('Please select attendee!');
        //     return;
        // }

        if ($("#newContent").val().trim() === '') {
            memo.warning('Please enter content!');
            return;
        }

        var file_elements = document.getElementsByName("file_elements");

        var attach = "";
        for(let i = 0;i < file_elements.length; i++)
        {
            if(file_elements[i].checked)
            {
                attach += file_elements[i].value + ",";
            }
        }

        if(attach !== "")
            attach = attach.slice(0, -1);

        //##obj_meeting 內容寫入資料庫
        //資料庫欄位 (ID, meeting_data)  其中ID為自動計數
        //INSERT table_name (meeting_data) VALUES (obj_meeting)
        //##將該obj_meeting在資料庫給的id返回回來，並設定到前端的id變數
        //##寄送通知信件給會議參與者
        token = localStorage.getItem('token');
        var form_Data = new FormData();
    
        form_Data.append('action', 2);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#newSubject").val().trim());
        form_Data.append('project_name', "");
        form_Data.append('message', $("#newContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('location', $("#newLocation").val().trim());
        form_Data.append('start_time', $("#newDate").val() + "T" + $("#newStartTime").val());
        form_Data.append('end_time', $("#newDate").val() + "T" + $("#newEndTime").val());
        form_Data.append('is_enabled', true);
        form_Data.append('created_by', "<?php echo $GLOBALS['username'] ?>");
        

        var color = "";
             
        if(document.getElementById("new_sc_color_other").checked)
        {
                //form_Data.append("color_other", $("#new_sc_color").val());
                color = $("#new_sc_color").val();
        }
        else
            form_Data.append("color_other", "");

        var color = "";
            
            if(document.getElementById("new_sc_color_other").checked)
            {
                    //form_Data.append("color_other", $("#new_sc_color").val());
                    color = $("#new_sc_color").val();
            }
                else
                    form_Data.append("color_other", "");
    
                var colors = document.getElementsByName("new_sc_color");
                
                for(var i=0; i<colors.length; i++)
                {
                    if(colors[i].checked)
                        color = colors[i].value;
                }
                if(color == "" && !document.getElementById("new_sc_color_other").checked)
                {
                    Swal.fire({
                        text: JSON.stringify("Please choose color for schedule."),
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
    
                form_Data.append("color", color);
    
                form_Data.append("text_color", "white");
    
    
    
            var file_elements = document.getElementsByName("file_elements");
            var item = 0;
            for(let i = 0;i < file_elements.length; i++)
            {
                if(file_elements[i].checked)
                {
                    for( var j = 0; j < memo.attachments.length; j++ ){
                    let file = memo.attachments[j];
                    if(file.name === file_elements[i].value)
                    {
                        form_Data.append('files[' + item++ + ']', file);
                        break;
                    }
                    }
                }
                    
            }
    
            var _memo = memo;
    
    
            //DELETE table_name WHERE ID=id;
            $.ajax({
                url: "api/work_calender_notes",
                type: "POST",
                contentType: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: form_Data,
    
                success: function(response) {
                    var obj = JSON.parse(response);
            
                    //##寄送通知信件給會議參與者,告知修改後訊息
                    //_memo.notify_mail(obj.id, 1);
    
                    var title = $("#newSubject").val().trim();
                    //if($("#newProject").val().trim() !== "")
                    //    title = '[ ' + $("#newProject").val().trim() + ' ] ' + $("#newSubject").val().trim();
    
    
                    //把新增會議 呈現於日曆上
                    if(obj.id != 0)
                    {
                        var obj_meeting = {
                            id: obj.id,
                            title: $("#newSubject").val().trim(),
                            project_name: '',
                            color: color,
                            color_other: color,
                            text_color: "white",
                            attendee: names.toString().trim(),
                            items: _memo.attendee,
                            start: $("#newDate").val() + "T" + $("#newStartTime").val(),
                            end: $("#newDate").val() + "T" + $("#newEndTime").val(),
                            location: $("#newLocation").val(),
                            content: $("#newContent").val(),
                            attach:attach,
                            //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
                            creator: "<?php echo $GLOBALS['username'] ?>"
                        };
    
                        calendar_task.addEvent({
                            id: 'n' + obj.id,
                            title: title,
                            borderColor : obj_meeting.color,
                            backgroundColor : obj_meeting.color,
                            start: obj_meeting.start,
                            end: obj_meeting.end,
                            description: obj_meeting
                        });
                    }
    
                },
    
                // show error message to user
                error: function(xhr, resp, text) {
    
                }
            });
    
            $("#addnotes-form").hide();
    
        });
     

        function onChangeFileUpload(target) {
        
        var fileTarget = $("#fileload");
        var container = $("#sc_product_files");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (memo.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                memo.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                memo.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }

    function refreshFileList(attach) {
        $('#sc_product_files_old_note').empty();

        var container = $("#sc_product_files_old_note");

        if(attach !== "")
        {
            var files = attach.split(",");
            files.forEach((element) => {
                var elm = '<div class="file-element">' +
                    '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                    '<label for="' + element + '">' + 
                        '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                    '</label>' +
                    '</div>';

                $(elm).appendTo(container);
            });
        }
    }

    function onChangeFileUploadOld(target) {
        
        var fileTarget = $("#fileload_old");
        var container = $("#sc_product_files_old");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (memo.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                memo.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements_old" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                memo.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }

    function onChangeFileUploadOldNote(target) {
        
        var fileTarget = $("#fileload_old_note");
        var container = $("#sc_product_files_old_note");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (memo.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                memo.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements_old_note" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                memo.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }

    function new_enable_forOther(selector){
        if(selector.value != "1")
            document.getElementById("new_sc_color").disabled = true;
        else
            document.getElementById("new_sc_color").disabled = false;
        
        console.log(selector.value);
    }

    function old_enable_forOther(selector){
        if(selector.value != "1")
            document.getElementById("old_sc_color").disabled = true;
        else
            document.getElementById("old_sc_color").disabled = false;
        
        console.log(selector.value);
    }

    $(document).ready(function () {
    // get today's date
    var begin = new Date();
    var today = new Date();
    // get previous 6 months's date
    var sixMonthsAgo = new Date(today.setMonth(today.getMonth() - 6));
    var netmonth = new Date(begin.getFullYear(), begin.getMonth()+2, 0)

   // let _id = "";
    // let uri = window.location.href.split("?");
    //   if (uri.length >= 2) {
    //     let vars = uri[1].split("&");

    //     let tmp = "";
    //     vars.forEach(async function(v) {
    //       tmp = v.split("=");
    //       if (tmp.length == 2) {
    //         switch (tmp[0]) {
    //           case "id":
    //             _id = tmp[1];
    //             break;
    //           default:
    //             console.log(`Too many args`);
    //         }
    //       }
    //     });
    //   }
 
    $('#sdate').val(sixMonthsAgo.toISOString().slice(0,7));
    $('#edate').val(netmonth.toISOString().slice(0,7));

   // $.fn.modal.Constructor.prototype._enforceFocus = function() {};

});

    document.addEventListener('DOMContentLoaded', function() {

                var calendarT1 = document.getElementById('task_calendar');

                let clickCnt = 0;
                let _app1 = app1;
                let _memo = memo;
                

                // event_array_task = [];
                // //將Task從資料庫中加入array
                // //需要讀出task的 (1)專案名稱 (2)task名稱 (3) task 的due date (4) task 所在的 project03_other頁面網址 (5) task 在日曆中的顏色 (6) task 的 creator
                // //event的對應格式請參考下方的events範例

                // /* 會議加入array的格式如下： */
                // var token = localStorage.getItem('token');

                // localStorage.getItem('token');
                // var form_Data = new FormData();
                // form_Data.append('jwt', token);
                // form_Data.append('uid', filter.user_id);
                // form_Data.append('sdate', $('#sdate').value);
                // form_Data.append('edate', $('#edate').value);

                // $.ajax({
                //     url: "api/project03_other_task_calendar_idv",
                //     type: "POST",
                //     contentType: 'multipart/form-data',
                //     processData: false,
                //     contentType: false,
                //     data: form_Data,

                //     success: function(result) {
                //         console.log(result);
                //         var obj = JSON.parse(result);
                //         if (obj !== undefined) {
                //             var arrayLength = obj.length;
                //             for (var i = 0; i < arrayLength; i++) {
                //                 //console.log(obj[i]);

                //                 var obj_meeting = {
                //                     id: obj[i].id,
                //                     title: obj[i].title,
                //                     //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                //                     start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                //                     backgroundColor: obj[i].color,
                //                     borderColor: obj[i].color,
                //                     extendedProps: {
                //                         create_id:obj[i].create_id,
                //                         category:obj[i].category,
                //                         level:obj[i].level,
                //                         task_status:obj[i].task_status,
                //                         stage_id:obj[i].stage_id,
                //                     },
                //                 };

                //                 event_array_task.push(obj_meeting);
                //             }

                //             if(arrayLength > 0)
                //             {
                //                 my_level = obj[0].my_l;
                //                 my_id = obj[0].my_i;
                //                 my_department = obj[0].my_d;
                //             }
                //         }



                        

                        // var calendar_task = new FullCalendar.Calendar(calendarT1, {

                        //     plugins: [ 'dayGrid' ],
                        //     timeZone: 'UTC',
                        //     defaultView: 'dayGridMonth',

                        //     contentHeight: 'auto',

                        //     titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        //         month: '2-digit',
                        //         year: 'numeric',
                        //         day: '2-digit'
                        //     },

                        //     header: {
                        //         left: 'prev,next addEventButton',
                        //         center: 'title',
                        //         right: 'dayGridMonth, timeGridWeek',
                        //     },

                            calendar_task = new FullCalendar.Calendar(calendarT1, {
                                plugins: [ 'dayGrid', 'timeGrid' ],
                                timeZone: 'UTC',
                                defaultView: 'dayGridMonth',

                                contentHeight: 'auto',

                                titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                                    month: '2-digit',
                                    year: 'numeric',
                                    day: '2-digit'
                                },

                                header: {
                                    left: 'prev,next today addEventButton',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek',
                                },

 
                            //Individual按鈕：只顯示出Creator、Assignee或Collaborator是當前使用者的task在日曆上
                            //Lighting按鈕：只顯示出專案的category為Lighting的task在日曆上
                            //Office Systems按鈕：只顯示出專案的category為Office Systems的task在日曆上
                            //all按鈕：顯示出所有的task在日曆上
                            customButtons: {
                                addEventButton: {
                                    text: 'Add Notes',
                                    click: function () {
                                        $('#addnotes-form').trigger("reset");
                                        $('#editnotes-form').hide();
                                        $("#editmeeting-form").hide();
                                        $('#addnotes-form').show();

                                        _memo.old_attendee = [];
                                        _memo.attendee = [];
                                        _memo.attachments = [];

                                        _memo.users = _memo.users_org;

                                        $('#newProject').val(_memo.project_name);
                                        $('#fileload').val('');
                                        $('#sc_product_files').empty();
                                        $('#newProject').attr("placeholder", _memo.project_name);

                                    }
                                },

                            },

                            events: event_array_task,

                            eventRender: function (info) {

                                //wired listener to handle click counts instead of event type
                                info.el.addEventListener('click', function() {
                                        clickCnt++;         
                                    if (clickCnt === 1) {
                                        oneClickTimer = setTimeout(function() {
                                            clickCnt = 0;

                                            if(info.event.id.startsWith("t"))
                                            {
                                                var id = info.event.id.substring(1);

                                                if(info.event.extendedProps.category == 'AD')
                                                    window.open('task_management_AD?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'DS')
                                                    window.open('task_management_DS?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'LT_T')
                                                    window.open('task_management_LT?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'OS_T')
                                                    window.open('task_management_OS?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'SLS')
                                                    window.open('task_management_SLS?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'ENG')
                                                    window.open('task_management_SVC?sid=' + id, "_blank");
                                                else if(info.event.extendedProps.category == 'C')
                                                    window.open('project03_client_v2?sid=' + info.event.extendedProps.stage_id, "_blank");
                                                else
                                                    window.open('project03_other?sid=' + info.event.extendedProps.stage_id, "_blank");
                                            }

                                            if(info.event.id.startsWith("m"))
                                            {
                                                var id = info.event.id.substring(1);
                                                
                                                $('#editmeeting-form').trigger("reset");
                                                $('#addmeeting-form').hide();
                                                $('#addnotes-form').hide();
                                                $('#editnotes-form').hide();
                                                $('#editmeeting-form > fieldset').prop('disabled', true);

                                                $("#oldAttendee").addClass("select_disabled");
                                                $('#sc_product_files').empty();
                                                _app1.attachments = [];

                                                //取得點擊的meeting資訊並載入表單
                                                eventObj = info.event;
                                                var obj_meeting = eventObj.extendedProps.description;

                                                if (obj_meeting === undefined)
                                                    return;

                                                $("#oldSubject").val(obj_meeting.title);

                                                // $("#old_sc_color").val(info.event.extendedProps.description.color);
                                                // //$("#oldTextColor").val(info.event.extendedProps.description.text_color);
                                                // $("#old_sc_color_other").val(info.event.extendedProps.description.color_other);

                                                document.getElementById("old_sc_color").value = info.event.extendedProps.description.color;

                                                if(info.event.extendedProps.description.color_other != "")
                                                {
                                                    document.getElementById("old_sc_color").value = info.event.extendedProps.description.color_other;
                                                    document.getElementById("old_sc_color_other").checked = true;
                                                }
                                                else
                                                {
                                                    document.getElementById("old_sc_color").value = "#000000";
                                                    document.getElementById("old_sc_color_other").checked = false;
                                                }

                                                if(info.event.extendedProps.description.color != "")
                                                {
                                                    var checked = 0;
                                                    var colors = document.getElementsByName("old_sc_color");

                                                    for(var i = 0; i < colors.length; i++)
                                                    {
                                                        if(colors[i].value == info.event.extendedProps.description.color)
                                                        {
                                                            checked = 1;
                                                            colors[i].checked = true;
                                                        }
                                                    }

                                                    if(checked == 0 && info.event.extendedProps.description.color_other == "")
                                                    {
                                                        document.getElementById("old_sc_color").value = info.event.extendedProps.description.color;
                                                        document.getElementById("old_sc_color_other").checked = true;
                                                    }
                                                }
                                                
                                        
                                                $('#oldProject').attr("placeholder", obj_meeting.project_name);

                                                $("#oldCreator").val(info.event.extendedProps.description.creator);
                                                $("#oldAttendee").val(info.event.extendedProps.description.items);
                                                $("#oldLocation").val(info.event.extendedProps.description.location);
                                                _app1.old_attendee = info.event.extendedProps.description.items;
                                                $("#oldDate").val(obj_meeting.start.split("T")[0]);
                                                $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
                                                $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
                                                $("#oldContent").val(obj_meeting.content);

                                                _app1.users = _app1.users_org.concat(_app1.old_attendee);

                                                _app1.users = _app1.users.filter((value, index, self) =>
                                                    index === self.findIndex((t) => (
                                                    t.username === value.username && t.id === value.id
                                                    ))
                                                )

                                                _app1.users.sort(function (a, b) {
                                                    return a.username.toLowerCase().localeCompare(b.username.toLowerCase());
                                                });

                                                var container = $("#sc_product_files_old");
                                                container.empty();

                                                if(obj_meeting.attach !== "")
                                                {
                                                    var files = obj_meeting.attach.split(",");
                                                    files.forEach((element) => {
                                                        var elm = '<div class="file-element">' +
                                                            '<input type="checkbox" id="' + element + '" name="file_elements_old" value="' + element + '" checked disabled>' +
                                                            '<label for="' + element + '">' + 
                                                                '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                                                            '</label>' +
                                                        '</div>';
                                    
                                                        $(elm).appendTo(container);
                                                    });
                                                }

                                                //設定出現和隱藏按鈕，和出現視窗
                                                $("#btn_close").show();
                                                $("#btn_delete").show();
                                                $("#btn_edit").show();
                                                $("#btn_cancel").hide();
                                                $("#btn_save").hide();
                                                $("#editmeeting-form").show();
                                            }

                                            if(info.event.id.startsWith("n"))
                                            {
                                                var id = info.event.id.substring(1);
                                                
                                                $('#editnotes-form').trigger("reset");
                                                $('#addmeeting-form').hide();
                                                $('#addnotes-form').hide();
                                                $('#editmeeting-form').hide();
                                                $('#editnotes-form > fieldset').prop('disabled', true);

                                                $("#oldAttendee").addClass("select_disabled");
                                                $('#sc_product_files').empty();

                                                

                                                _memo.attachments = [];

                                                //取得點擊的meeting資訊並載入表單
                                                eventObj = info.event;
                                                var obj_meeting = eventObj.extendedProps.description;

                                                if (obj_meeting === undefined)
                                                    return;

                                                $("#oldSubject_note").val(obj_meeting.title);

                                                // $("#old_sc_color").val(info.event.extendedProps.description.color);
                                                // //$("#oldTextColor").val(info.event.extendedProps.description.text_color);
                                                // $("#old_sc_color_other").val(info.event.extendedProps.description.color_other);

                                                document.getElementById("old_sc_color_note").value = info.event.extendedProps.description.color;

                                                if(info.event.extendedProps.description.color_other != "")
                                                {
                                                    document.getElementById("old_sc_color_note").value = info.event.extendedProps.description.color_other;
                                                    document.getElementById("old_sc_color_other_note").checked = true;
                                                }
                                                else
                                                {
                                                    document.getElementById("old_sc_color_note").value = "#000000";
                                                    document.getElementById("old_sc_color_other_note").checked = false;
                                                }

                                                if(info.event.extendedProps.description.color != "")
                                                {
                                                    var checked = 0;
                                                    var colors = document.getElementsByName("old_sc_color_note");

                                                    for(var i = 0; i < colors.length; i++)
                                                    {
                                                        if(colors[i].value == info.event.extendedProps.description.color)
                                                        {
                                                            checked = 1;
                                                            colors[i].checked = true;
                                                        }
                                                    }

                                                    if(checked == 0 && info.event.extendedProps.description.color_other == "")
                                                    {
                                                        document.getElementById("old_sc_color_note").value = info.event.extendedProps.description.color;
                                                        document.getElementById("old_sc_color_other_note").checked = true;
                                                    }
                                                }
                                                
                                        
                                                //$('#oldProject_note').attr("placeholder", obj_meeting.project_name);

                                                $("#oldCreator_note").val(info.event.extendedProps.description.creator);
                                                //$("#oldAttendee").val(info.event.extendedProps.description.items);
                                                $("#oldLocation_note").val(info.event.extendedProps.description.location);
                                                _memo.old_attendee = info.event.extendedProps.description.items;
                                                $("#oldDate_note").val(obj_meeting.start.split("T")[0]);
                                                $("#oldStartTime_note").val(obj_meeting.start.split("T")[1]);
                                                $("#oldEndTime_note").val(obj_meeting.end.split("T")[1]);
                                                $("#oldContent_note").val(obj_meeting.content);

                                                _memo.users = _memo.users_org.concat(_memo.old_attendee);

                                                _memo.users = _memo.users.filter((value, index, self) =>
                                                    index === self.findIndex((t) => (
                                                    t.username === value.username && t.id === value.id
                                                    ))
                                                )

                                                _memo.users.sort(function (a, b) {
                                                    return a.username.toLowerCase().localeCompare(b.username.toLowerCase());
                                                });

                                                var container = $("#sc_product_files_old_note");
                                                container.empty();

                                                if(obj_meeting.attach !== "")
                                                {
                                                    var files = obj_meeting.attach.split(",");
                                                    files.forEach((element) => {
                                                        var elm = '<div class="file-element">' +
                                                            '<input type="checkbox" id="' + element + '" name="file_elements_old_note" value="' + element + '" checked disabled>' +
                                                            '<label for="' + element + '">' + 
                                                                '<a href="https://storage.googleapis.com/feliiximg/' + element + '" target="_blank">' + element + '</a>' + 
                                                            '</label>' +
                                                        '</div>';
                                    
                                                        $(elm).appendTo(container);
                                                    });
                                                }

                                                if ($("#oldCreator_note")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
                                                    $("#btn_delete").hide();
                                                    $("#btn_edit").hide();
                                                }
                                                else
                                                {
                                                    $("#btn_delete").show();
                                                    $("#btn_edit").show();
                                                }

                                                //設定出現和隱藏按鈕，和出現視窗
                                                $("#btn_close").show();
                                                $("#btn_cancel").hide();
                                                $("#btn_save").hide();
                                                $("#editnotes-form").show();
                                            }



                                        }, 400);
                                        
                                    } else if (clickCnt === 2) {
                                        clearTimeout(oneClickTimer);
                                        clickCnt = 0;

                                        if(info.event.extendedProps.task_status != 2)
                                        {
                                            if(CanClose(my_id, info.event.extendedProps.create_id, my_level, info.event.extendedProps.level, info.event.extendedProps.category))
                                            {
                                                var token = localStorage.getItem('token');

                                                localStorage.getItem('token');
                                                var form_Data = new FormData();
                                                form_Data.append('id', info.event.id.substring(1));
                                                form_Data.append('category', info.event.extendedProps.category);

                                                let _info = info;

                                                $.ajax({
                                                    url: "api/project03_other_task_calendar_close_task",
                                                    type: "POST",
                                                    contentType: 'multipart/form-data',
                                                    processData: false,
                                                    contentType: false,
                                                    data: form_Data,

                                                    success: function(result) {
                                                        console.log(result);
                                                    
                                                        for (let i=0; i<event_array_task.length; i++) {
                                                            if(event_array_task[i].id === _info.event.id && event_array_task[i].extendedProps.category === _info.event.extendedProps.category)
                                                            {
                                                                event_array_task[i].backgroundColor = 'green';
                                                                event_array_task[i].borderColor = 'green';
                                                                event_array_task[i].extendedProps.task_status = 2;

                                                                calendar_task.removeAllEvents();
                                                                calendar_task.addEventSource(event_array_task);
                                                            }
                                                        }
                                                        
                                                    }
                                                });
                                            }
                                            else
                                            {
                                                console.log(info.event.extendedProps);
                                            }
                                        }
                                    
                                    }          
                                });
                                },

                            editable: false,
                          
                            
                        });

                        calendar_task.render();
                    });



</script>

<script defer src="js/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/exif-js.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/individual_calendar.js"></script>

</html>