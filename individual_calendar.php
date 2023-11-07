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

    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.css'>
    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/core@4.3.0/main.min.css'>
    <script src='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js'></script>

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
            margin-right: 20px;
        }

        #filter select {
            width: 250px;
            margin-right: 30px;
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

    <button class="btn btn-primary" onclick="app.getInitial()">Query</button>
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

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_orange"
                               value="#FECC28" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_red"
                               value="#4EB5BB" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_purple"
                               value="#009858" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_green"
                               value="#A671AD" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_blue"
                               value="#F19DB4" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_teal"
                               value="#141415" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_other"
                               value="1" onchange="old_enable_forOther(this);">
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

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_orange"
                               value="#FECC28" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_red"
                               value="#4EB5BB" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_purple"
                               value="#009858" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_green"
                               value="#A671AD" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_blue"
                               value="#F19DB4" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="new_sc_color" id="new_sc_color_teal"
                               value="#141415" onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="new_sc_color_other" value="1"
                               onchange="new_enable_forOther(this);">
                        <label class="custom-control-label" for="new_sc_color_other">Other </label>
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
                <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addmeeting-form')">Close</a>

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
                <input type="text" id="oldSubject">
            </div>

            <div class="meetingform-item colorpicker">
                <label>Color:</label>
                <div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_orange"
                               value="#FECC28" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_orange"
                               style="background-color: #FECC28;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_red"
                               value="#4EB5BB" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_red"
                               style="background-color: #4EB5BB;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_purple"
                               value="#009858" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_purple"
                               style="background-color: #009858;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_green"
                               value="#A671AD" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_green"
                               style="background-color: #A671AD;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_blue"
                               value="#F19DB4" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_blue"
                               style="background-color: #F19DB4;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_teal"
                               value="#141415" onchange="old_enable_forOther(this);">
                        <label class="custom-control-label" for="old_sc_color_teal"
                               style="background-color: #141415;"></label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="old_sc_color" id="old_sc_color_other"
                               value="1" onchange="old_enable_forOther(this);">
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
            <a class="btn small" id="btn_delete">Delete</a>
            <a class="btn small green" id="btn_edit">Edit</a>
            <a class="btn small" id="btn_cancel">Cancel</a>
            <a class="btn small green" id="btn_save">Save</a>
        </div>


    </form>

</div>


</body>


<script>

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
                

            if(level === "LIGHTING MANAGER" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT LIGHTING MANAGER" || creator_level === "SR. LIGHTING DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT LIGHTING MANAGER" )
            {
                if(creator_level === "SR. LIGHTING DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "SR. LIGHTING DESIGNER" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
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

            if(level === "OFFICE SYSTEMS MANAGER" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT OFFICE SYSTEMS MANAGER" || creator_level === "SR. OFFICE SYSTEMS DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT OFFICE SYSTEMS MANAGER" )
            {
                if(creator_level === "SR. OFFICE SYSTEMS DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "SR. OFFICE SYSTEMS DESIGNER" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
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
                

            if(level === "LIGHTING MANAGER" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT LIGHTING MANAGER" || creator_level === "SR. LIGHTING DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT LIGHTING MANAGER" )
            {
                if(creator_level === "SR. LIGHTING DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "SR. LIGHTING DESIGNER" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
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

            if(level === "OFFICE SYSTEMS MANAGER" || level === "OPERATIONS MANAGER" )
            {
                if(creator_level === "ASSISTANT OFFICE SYSTEMS MANAGER" || creator_level === "SR. OFFICE SYSTEMS DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT OFFICE SYSTEMS MANAGER" )
            {
                if(creator_level === "SR. OFFICE SYSTEMS DESIGNER"  || creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
                {
                    can_close = true;
                }
            }

            if(level === "SR. OFFICE SYSTEMS DESIGNER" )
            {
                if(creator_level === "JR. ACCOUNT EXECUTIVE" || creator_level === "ACCOUNT EXECUTIVE" || creator_level === "SR. ACCOUNT EXECUTIVE")
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

            if(level === "SALES MANAGER" || level === "STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && creator_level != "CHIEF ADVISOR")
                {
                    can_close = true;
                }
            }

            if(level === "ASSISTANT SALES MANAGER" || level === "ASSISTANT STORE MANAGER")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "SALES MANAGER" && 
                   creator_level != "STORE MANAGER")
                {
                    can_close = true;
                }
            }

            if(level === "SR. ACCOUNT EXECUTIVE" || level === "ACCOUNT EXECUTIVE" || level === "JR. ACCOUNT EXECUTIVE" || level === "SR. STORE SALES EXECUTIVE" || level === "STORE SALES EXECUTIVE")
            {
                if(creator_level !== "MANAGING DIRECTOR" && 
                   creator_level != "CHIEF ADVISOR" && 
                   creator_level != "SALES MANAGER" && 
                   creator_level != "STORE MANAGER" && 
                   creator_level != "ASSISTANT SALES MANAGER" && 
                   creator_level != "ASSISTANT STORE MANAGER" && 
                   creator_level != "SR. ACCOUNT EXECUTIVE" && 
                   creator_level != "ACCOUNT EXECUTIVE" && 
                   creator_level != "JR. ACCOUNT EXECUTIVE" && 
                   creator_level != "SR. STORE SALES EXECUTIVE" && 
                   creator_level != "STORE SALES EXECUTIVE")
                {
                    can_close = true;
                }
            }
        }

        if(department === 'ENG' || level === 'LIGHTING MANAGER' || level === 'OFFICE SYSTEMS MANAGER')  // 20220321 for service leave
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

            if(level === "ENGINEERING MANAGER" || level === 'LIGHTING MANAGER' || level === 'OFFICE SYSTEMS MANAGER')  // 20220321 for service leave
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
                let my_id = 0;
                let my_level = 0;
                let my_department = "";

                let event_array_task = [];
                //將Task從資料庫中加入array
                //需要讀出task的 (1)專案名稱 (2)task名稱 (3) task 的due date (4) task 所在的 project03_other頁面網址 (5) task 在日曆中的顏色 (6) task 的 creator
                //event的對應格式請參考下方的events範例

                /* 會議加入array的格式如下： */
                var token = localStorage.getItem('token');

                localStorage.getItem('token');
                var form_Data = new FormData();
                form_Data.append('jwt', token);
                form_Data.append('uid', 1);

                $.ajax({
                    url: "api/project03_other_task_calendar_dep",
                    type: "POST",
                    contentType: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: form_Data,

                    success: function(result) {
                        console.log(result);
                        var obj = JSON.parse(result);
                        if (obj !== undefined) {
                            var arrayLength = obj.length;
                            for (var i = 0; i < arrayLength; i++) {
                                //console.log(obj[i]);

                                var obj_meeting = {
                                    id: obj[i].id,
                                    title: obj[i].title,
                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                    backgroundColor: obj[i].color,
                                    borderColor: obj[i].color,
                                    extendedProps: {
                                        create_id:obj[i].create_id,
                                        category:obj[i].category,
                                        level:obj[i].level,
                                        task_status:obj[i].task_status,
                                        stage_id:obj[i].stage_id,
                                    },
                                };

                                event_array_task.push(obj_meeting);
                            }

                            if(arrayLength > 0)
                            {
                                my_level = obj[0].my_l;
                                my_id = obj[0].my_i;
                                my_department = obj[0].my_d;
                            }
                        }
/*
                        let FullCalendarActions = {
                            currentTime: null,
                            isDblClick: function () {
                                let prevTime =
                                typeof FullCalendarActions.currentTime === null
                                    ? new Date().getTime() - 1000000
                                    : FullCalendarActions.currentTime;
                                FullCalendarActions.currentTime = new Date().getTime();
                                return FullCalendarActions.currentTime - prevTime < 500;
                            },
                        }
*/
                        var calendar_task = new FullCalendar.Calendar(calendarT1, {

                            plugins: [ 'dayGrid' ],
                            timeZone: 'UTC',
                            defaultView: 'dayGridMonth',

                            contentHeight: 'auto',

                            titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                                month: '2-digit',
                                year: 'numeric',
                                day: '2-digit'
                            },

                            header: {
                                left: 'prev,next addEventButton',
                                center: 'title',
                                right: 'individual,admin,design,lighting,furniture,sls,eng,tw,overall dayGridMonth,timeGridWeek',
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
                                        $('#addnotes-form').show();

                                        _app1.old_attendee = [];
                                        _app1.attendee = [];
                                        _app1.attachments = [];

                                        _app1.users = _app1.users_org;

                                        $('#newProject').val(_app1.project_name);
                                        $('#fileload').val('');
                                        $('#sc_product_files').empty();
                                        $('#newProject').attr("placeholder", _app1.project_name);

                                    }
                                },


                                individual: {
                                    text: 'Individual',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('uid', 1);

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;

                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                admin: {
                                    text: 'AD',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'ad');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/task_management_AD?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                design: {
                                    text: 'DS',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'ds');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/task_management_DS?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },


                                lighting: {
                                    text: 'LT',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'lt');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                furniture: {
                                    text: 'OS',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'os');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                sls: {
                                    text: 'SLS',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'sls');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                eng: {
                                    text: 'ENG',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'eng');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                tw: {
                                    text: 'TW',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();

                                        form_Data.append('category', 'tw');

                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                },

                                overall: {
                                    text: 'All',
                                    click: function() {

                                        //刪除當前在日曆上的所有任務資訊
                                        calendar_task.removeAllEvents();

                                        //從資料庫中取出符合當前條件的任務

                                        let temp = [];
                                        //將符合條件的任務加入到日曆中
                                        // task status = Pending，則該任務顏色為 gray
                                        // task status = Close，則該任務顏色為 green
                                        // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                                        // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                                        var token = localStorage.getItem('token');

                                        localStorage.getItem('token');
                                        var form_Data = new FormData();


                                        $.ajax({
                                            url: "api/project03_other_task_calendar_dep",
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
                                                        //console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                            extendedProps: {
                                                                create_id:obj[i].create_id,
                                                                category:obj[i].category,
                                                                level:obj[i].level,
                                                                task_status:obj[i].task_status,
                                                                stage_id:obj[i].stage_id,
                                                            },
                                                        };

                                                        temp.push(obj_meeting);
                                                    }

                                                    if(arrayLength > 0)
                                                    {
                                                        my_level = obj[0].my_l;
                                                        my_id = obj[0].my_i;
                                                        my_department = obj[0].my_d;
                                                    }
                                                }

                                                event_array_task = temp;
                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                }
                            },

                            events: event_array_task,

                            eventRender: function (info) {

                                //wired listener to handle click counts instead of event type
                                info.el.addEventListener('click', function() {
                                        clickCnt++;         
                                    if (clickCnt === 1) {
                                        oneClickTimer = setTimeout(function() {
                                            clickCnt = 0;
                                            if(info.event.extendedProps.category == 'AD')
                                                window.open('https://feliix.myvnc.com/task_management_AD?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'DS')
                                                window.open('https://feliix.myvnc.com/task_management_DS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'LT_T')
                                                window.open('https://feliix.myvnc.com/task_management_LT?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'OS_T')
                                                window.open('https://feliix.myvnc.com/task_management_OS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'SLS')
                                                window.open('https://feliix.myvnc.com/task_management_SLS?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'ENG')
                                                window.open('https://feliix.myvnc.com/task_management_SVC?sid=' + info.event.id, "_blank");
                                            else if(info.event.extendedProps.category == 'C')
                                                window.open('https://feliix.myvnc.com/project03_client_v2?sid=' + info.event.extendedProps.stage_id, "_blank");
                                            else
                                                window.open('https://feliix.myvnc.com/project03_other?sid=' + info.event.extendedProps.stage_id, "_blank");
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
                                                form_Data.append('id', info.event.id);
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
                                                console.log(info.event.extendedProps);
                                        }
                                    
                                    }          
                                });
                            },

                            

                            editable: false,
                          
                            
                        });

                        calendar_task.render();
                    },
                });
            });


</script>

<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/individual_calendar.js"></script>

</html>