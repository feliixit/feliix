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
            $GLOBALS['position'] = $decoded->data->position;
            $GLOBALS['department'] = $decoded->data->department;

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
        Meeting Calendar
    </title>

    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap/4.5.0/bootstrap.min.css">

    <link rel="stylesheet" href="css/vue-select.css" type="text/css">

    <link rel="stylesheet" type="text/css" href="css/fullcalendar@5.1.0/main.min.css">


    <script type="text/javascript" src="js/fullcalendar@5.1.0/main.min.js"></script>
    <script src="js/moment.js"></script>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <style>

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        body{
            height: 130vh;
        }

        #calendar {
            max-width: 90%;
            margin: 40px auto;
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

        .meetingform-item.colorpicker > div > div.custom-control {
            display: inline-block;
            margin-left: 20px;
            margin-bottom: 10px;
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


<div id='calendar'></div>

<div id='app' style='padding-bottom: 20px;'>
<form id="addmeeting-form" style="display: none;">
    <fieldset>
        <legend style="max-width: 250px;">Meeting Information</legend>

        <div class="meetingform-item">
            <label>Subject:</label>
            <input type="text" id="newSubject">
        </div>

        <div class="meetingform-item">
            <label>Project:</label>
            <input type="text" id="newProject" placeholder="Project name will be added ahead of subject if filled in">
        </div>

        <div class="meetingform-item colorpicker">
            <label>Color:</label>
            <div>

                <div class="custom-control custom-radio">
                    <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_orange" value="#FECC28" onchange="new_enable_forOther(this);" >
                    <label class="custom-control-label" for="new_sc_color_orange" style="background-color: #FECC28;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_red" value="#4EB5BB" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_red" style="background-color: #4EB5BB;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_purple" value="#009858" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_purple" style="background-color: #009858;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_green" value="#A671AD" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_green" style="background-color: #A671AD;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_blue" value="#F19DB4" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_blue" style="background-color: #F19DB4;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_teal" value="#141415" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_teal" style="background-color: #141415;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" id="new_sc_color_other" value="1" onchange="new_enable_forOther(this);" >
                     <label class="custom-control-label" for="new_sc_color_other">Other </label>
                </div>

                <input type="color" class="form-control" id="new_sc_color">

            </div>

        </div>

        <div class="meetingform-item">
            <label>Attendee:</label>
            <v-select id="newAttendee" :options="users" attach chips label="username" v-model="attendee"
                      multiple></v-select>
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
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addmeeting-form')">Close</a>

            <a class="btn small green" id="btn_add">Add</a>
        </div>

    </fieldset>
</form>


<form id="editmeeting-form" style="display: none;">
    <fieldset disabled>
        <legend style="max-width: 250px;">Meeting Information</legend>

        <div class="meetingform-item">
            <label>Subject:</label>
            <input type="text" id="oldSubject">
        </div>

        <div class="meetingform-item">
            <label>Project:</label>
            <input type="text" id="oldProject" placeholder="Project name will be added ahead of subject if filled in">
        </div>

        <div class="meetingform-item colorpicker">
            <label>Color:</label>
            <div>

                <div class="custom-control custom-radio">
                    <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_orange" value="#FECC28" onchange="old_enable_forOther(this);" >
                    <label class="custom-control-label" for="old_sc_color_orange" style="background-color: #FECC28;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_red" value="#4EB5BB" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_red" style="background-color: #4EB5BB;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_purple" value="#009858" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_purple" style="background-color: #009858;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_green" value="#A671AD" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_green" style="background-color: #A671AD;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_blue" value="#F19DB4" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_blue" style="background-color: #F19DB4;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_teal" value="#141415" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_teal" style="background-color: #141415;"></label>
                </div>

                <div class="custom-control custom-radio">
                     <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_other" value="1" onchange="old_enable_forOther(this);" >
                     <label class="custom-control-label" for="old_sc_color_other">Other </label>
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
            <input type="file" ref="file_old" id="fileload_old" name="file_old[]" onChange="onChangeFileUploadOld(event)" multiple>
        </div>

        <div class="file-container" id="sc_product_files_old">


        </div>

        <input id="sc_product_files_hide" style="display: none;" value="">


        </fieldset>
        <div class="meetingform-buttons_edit">
            <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')"
               id="btn_close">Close</a>
            <a class="btn small" id="btn_delete">Delete</a>
            <a class="btn small green" id="btn_edit">Edit</a>
            <a class="btn small" id="btn_cancel">Cancel</a>
            <a class="btn small green" id="btn_save">Save</a>
        </div>

    
</form>
</div>

</body>


<script>
    var eventObj;

    document.addEventListener('DOMContentLoaded', function () {

        let calendarEl = document.getElementById('calendar');

        let _app1 = app1;
        let event_array = [];
        /* 會議加入array的格式如下： */
        var token = localStorage.getItem('token');

        localStorage.getItem('token');
        var form_Data = new FormData();
        form_Data.append('jwt', token);
        form_Data.append('action', 1);

        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            color: obj[i].color.trim(),
                            color_other: obj[i].color_other.trim(),
                            text_color: obj[i].text_color.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: obj[i].id,
                            title: title,
                            borderColor: obj[i].color.trim(),
                            backgroundColor: obj[i].color.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        event_array.push(obj_meeting);
                    }
                }

                //初始化 fullcalendar 物件
                calendar = new FullCalendar.Calendar(calendarEl, {

                    contentHeight: 'auto',

                    titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                        month: '2-digit',
                        year: 'numeric',
                        day: '2-digit'
                    },

                    headerToolbar: {
                        left: 'prev,next addEventButton',
                        center: 'title',
                        right: 'individual,overall dayGridMonth,timeGridWeek'
                    },

                    //Add Meeting被點擊的方法
                    customButtons: {
                        individual: {
                            text: 'Individual',
                            click: function () {
                                calendar.removeAllEvents();
                                hideWindow('#editmeeting-form');

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                               
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('jwt', token);
                                form_Data.append('action', 11);
               

                                $.ajax({
                                    url: "api/work_calender_meetings",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function(result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                console.log(obj[i]);

                                                var title = "";
                                                if (obj[i].project_name.trim() === '')
                                                    title = obj[i].subject.trim();
                                                else
                                                    title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                                                var attach = "";
                                                for (var j = 0; j < obj[i].attach.length; j++) {
                                                    attach += obj[i].attach[j].filename + ",";
                                                }

                                                if (attach !== "")
                                                    attach = attach.slice(0, -1);

                                                var obj_description = {
                                                    title: obj[i].subject.trim(),
                                                    project_name: obj[i].project_name.trim(),
                                                    color: obj[i].color.trim(),
                                                    color_other: obj[i].color_other.trim(),
                                                    text_color: obj[i].text_color.trim(),
                                                    attendee: obj[i].attendee.trim(),
                                                    items: obj[i].items,
                                                    attach: attach,
                                                    location: obj[i].location.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    content: obj[i].message.trim(),
                                                    creator: obj[i].created_by.trim(),
                                                };

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: title,
                                                    borderColor: obj[i].color.trim(),
                                                    backgroundColor: obj[i].color.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    description: obj_description,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }

                                        event_array = temp;
                                        calendar.addEventSource(temp);

                                    }
                                });
                            }
                        },
                        
                        overall: {
                            text: 'All',
                            click: function () {
                                calendar.removeAllEvents();
                                hideWindow('#editmeeting-form');

                                //從資料庫中取出符合當前條件的任務

                                let temp = [];
                               
                                var token = localStorage.getItem('token');

                                localStorage.getItem('token');
                                var form_Data = new FormData();

                                form_Data.append('jwt', token);
                                form_Data.append('action', 1);

                                $.ajax({
                                    url: "api/work_calender_meetings",
                                    type: "POST",
                                    contentType: 'multipart/form-data',
                                    processData: false,
                                    contentType: false,
                                    data: form_Data,

                                    success: function(result) {
                                        //console.log(result);
                                        var obj = JSON.parse(result);
                                        if (obj !== undefined) {
                                            var arrayLength = obj.length;
                                            for (var i = 0; i < arrayLength; i++) {
                                                console.log(obj[i]);

                                                var title = "";
                                                if (obj[i].project_name.trim() === '')
                                                    title = obj[i].subject.trim();
                                                else
                                                    title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                                                var attach = "";
                                                for (var j = 0; j < obj[i].attach.length; j++) {
                                                    attach += obj[i].attach[j].filename + ",";
                                                }

                                                if (attach !== "")
                                                    attach = attach.slice(0, -1);

                                                var obj_description = {
                                                    title: obj[i].subject.trim(),
                                                    project_name: obj[i].project_name.trim(),
                                                    color: obj[i].color.trim(),
                                                    color_other: obj[i].color_other.trim(),
                                                    text_color: obj[i].text_color.trim(),
                                                    attendee: obj[i].attendee.trim(),
                                                    items: obj[i].items,
                                                    attach: attach,
                                                    location: obj[i].location.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    content: obj[i].message.trim(),
                                                    creator: obj[i].created_by.trim(),
                                                };

                                                var obj_meeting = {
                                                    id: obj[i].id,
                                                    title: title,
                                                    borderColor: obj[i].color.trim(),
                                                    backgroundColor: obj[i].color.trim(),
                                                    start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                                                    end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                                                    description: obj_description,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }

                                        event_array = temp;
                                        calendar.addEventSource(temp);

                                    }
                                });
                            }
                        },

                        addEventButton: {
                            text: 'Add Meeting',
                            click: function () {
                                $('#addmeeting-form').trigger("reset");
                                $('#editmeeting-form').hide();
                                $('#addmeeting-form').show();

                                _app1.old_attendee = [];
                                _app1.attendee = [];
                                _app1.attachments = [];

                                _app1.users = _app1.users_org;

                                $('#newProject').val(_app1.project_name);
                                $('#fileload').val('');
                                $('#sc_product_files').empty();
                                $('#newProject').attr("placeholder", _app1.project_name);

                            }
                        }
                    },


                    //日曆上meeting被點擊的方法
                    eventClick: function (info) {
                        $('#editmeeting-form').trigger("reset");
                        $('#addmeeting-form').hide();
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

                    },

                    editable: false,
                    events: event_array

                });

                calendar.render();

            },

            // show error message to user
            error: function (xhr, resp, text) {

            }
        });


    });


    $(document).on("click", "#btn_edit", function () {

        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editmeeting-form > fieldset').prop('disabled', false);
        $("#oldCreator").prop('disabled', true);

        $("#oldAttendee").removeClass("select_disabled");

        //$("oldAttendee").prop('disabled', false);
        var file_elements = document.getElementsByName("file_elements_old");

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


    $(document).on("click", "#btn_cancel", function () {

        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject").val(obj_meeting.title);
        $("#oldProject").val(obj_meeting.project_name);
        $("#old_sc_color").val(obj_meeting.color);
        //$("#oldProject").val(obj_meeting.text_color);
        $("#old_sc_color_other").val(obj_meeting.color_other);
        $("#oldCreator").val(obj_meeting.creator);
        $("#oldAttendee").val(obj_meeting.attendee);
        $("#oldLocation").val(obj_meeting.location);
        $("#oldDate").val(obj_meeting.start.split("T")[0]);
        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
        $("#oldContent").val(obj_meeting.content);
        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_save", function () {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate").val() + " " + $("#oldStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate").val() + " " + $("#oldEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        if ($("#oldDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#oldEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#oldStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#oldSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.old_attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#oldContent").val().trim() === '') {
            app1.warning('Please enter content!');
            return;
        }

        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        //$("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //##修改後的內容 update到資料庫
        var id = eventObj.id;

        var file_elements = document.getElementsByName("file_elements_old");

        var attach = "";
        var remove = "";
        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;

        token = localStorage.getItem('token');
        var form_Data = new FormData();

        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject").val().trim());
        form_Data.append('project_name', $("#oldProject").val().trim());
        form_Data.append('message', $("#oldContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('location', $("#oldLocation").val());
        form_Data.append('start_time', $("#oldDate").val() + "T" + $("#oldStartTime").val());
        form_Data.append('end_time', $("#oldDate").val() + "T" + $("#oldEndTime").val());
        form_Data.append('is_enabled', true);

        var Color_Other = "";
        var Color = "";
        if(document.getElementById("old_sc_color_other").checked)
            Color = document.getElementById("old_sc_color").value;
        else
            Color_Other = "";

        var colors = document.getElementsByName("old_sc_color");
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

        if($("#old_sc_color").val().trim() == "" && !document.getElementById("old_sc_color").checked)
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
                for( var j = 0; j < app1.attachments.length; j++ ){
                    let file = app1.attachments[j];
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

        var _func = app1;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function (result) {
                console.log(result);

                _func.notify_mail(id, 2);

                var obj_meeting = {
                    title: $("#oldSubject").val().trim(),
                    project_name: $("#oldProject").val().trim(),
                    color: Color,
                    color_other: Color_Other,
                    text_color: "white",
                    attendee: names.toString().trim(),
                    items: _func.old_attendee,
                    start: $("#oldDate").val() + "T" + $("#oldStartTime").val(),
                    end: $("#oldDate").val() + "T" + $("#oldEndTime").val(),
                    content: $("#oldContent").val(),
                    attach:attach,
                    //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
                    creator: "<?php echo $GLOBALS['username'] ?>",
                    location: $("#oldLocation").val(),
                };
                $("#oldCreator").val(obj_meeting.creator);

                var title = $("#oldSubject").val().trim();
                if($("#oldProject").val().trim() !== "")
                    title = '[ ' + $("#oldProject").val().trim() + ' ] ' + $("#oldSubject").val().trim();

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

    $(document).on("click", "#btn_delete", function () {

        var _app1 = app1;
        if ($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>") {
            app1.warning('Only meeting creator can execute this action!');
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

                $("#editmeeting-form").hide();

                //##從資料庫中刪除該會議
                var id = eventObj.id;

                token = localStorage.getItem('token');
                var form_Data = new FormData();
                form_Data.append('jwt', token);
                form_Data.append('action', 7);

                form_Data.append('id', id);

                //DELETE table_name WHERE ID=id;
                $.ajax({
                    url: "api/work_calender_meetings",
                    type: "POST",
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: form_Data,

                    success: function (result) {
                        console.log(result);

                        //從日曆中刪除該會議
                        eventObj.remove();

                        _app1.notify_mail(id, 3);
                    },

                    // show error message to user
                    error: function (xhr, resp, text) {

                    }
                });

            } else {

            }
        });


    });


    $(document).on("click", "#btn_add", function () {
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

        //##任一欄位如果為空則提示欄位不得為空
        if ($("#newDate").val() === '') {
            app1.warning('Please select Date!');
            return;
        }

        if ($("#newEndTime").val() === '') {
            app1.warning('Please select End time!');
            return;
        }

        if ($("#newStartTime").val() === '') {
            app1.warning('Please select Start time!');
            return;
        }

        if ($("#newSubject").val() === '') {
            app1.warning('Please enter subject!');
            return;
        }

        var names = app1.attendee.map(function (item) {
            return item['username'];
        });

        if (names.toString().trim() === '') {
            app1.warning('Please select attendee!');
            return;
        }

        if ($("#newContent").val().trim() === '') {
            app1.warning('Please enter content!');
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
        form_Data.append('project_name', $("#newProject").val().trim());
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
                for( var j = 0; j < app1.attachments.length; j++ ){
                let file = app1.attachments[j];
                if(file.name === file_elements[i].value)
                {
                    form_Data.append('files[' + item++ + ']', file);
                    break;
                }
                }
            }
                
        }

        var _app1 = app1;


        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function(response) {
                var obj = JSON.parse(response);
        
                //##寄送通知信件給會議參與者,告知修改後訊息
                _app1.notify_mail(obj.id, 1);

                var title = $("#newSubject").val().trim();
                if($("#newProject").val().trim() !== "")
                    title = '[ ' + $("#newProject").val().trim() + ' ] ' + $("#newSubject").val().trim();


                //把新增會議 呈現於日曆上
                if(obj.id != 0)
                {
                    var obj_meeting = {
                        id: obj.id,
                        title: $("#newSubject").val().trim(),
                        project_name: $("#newProject").val().trim(),
                        color: color,
                        color_other: color,
                        text_color: "white",
                        attendee: names.toString().trim(),
                        items: _app1.attendee,
                        start: $("#newDate").val() + "T" + $("#newStartTime").val(),
                        end: $("#newDate").val() + "T" + $("#newEndTime").val(),
                        location: $("#newLocation").val(),
                        content: $("#newContent").val(),
                        attach:attach,
                        //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
                        creator: "<?php echo $GLOBALS['username'] ?>"
                    };

                    calendar.addEvent({
                        id: obj.id,
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

        $("#addmeeting-form").hide();

    });


    function hideWindow(target) {
        $(target).hide();
    }

    
    function onChangeFileUpload(target) {
        
        var fileTarget = $("#fileload");
        var container = $("#sc_product_files");

        for (i = 0; i < fileTarget[0].files.length; i++) {
            // remove duplicate
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }

    function refreshFileList(attach) {
        $('#sc_product_files_old').empty();

        var container = $("#sc_product_files_old");

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
            if (app1.attachments.indexOf(fileTarget[0].files[i]) == -1 ||
                app1.attachments.length == 0) 
            {
                var fileItem = Object.assign(fileTarget[0].files[i]);

                var elm = '<div class="file-element">' +
                                    '<input type="checkbox" id="' + fileTarget[0].files[i].name + '" name="file_elements_old" value="' + fileTarget[0].files[i].name + '" checked>' +
                                    '<label for="' + fileTarget[0].files[i].name + '">' + 
                                        '<a>' + fileTarget[0].files[i].name + '</a>' + 
                                    '</label>' +
                                '</div>';
            
                $(elm).appendTo(container);

                app1.attachments.push(fileItem);
            }
            else
            {
                fileTarget[0].value = "";
            }
        }
    }
</script>

<script>

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

</script>

<script defer src="js/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/exif-js.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script type="text/javascript" src="js/meeting_calendar.js" defer></script>

</html>
