<?php include 'check.php';?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8' />
    <title>
        Task Due Date Calendar
    </title>

    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />

    <link rel="stylesheet" href="css/vue-select.css" type="text/css">

    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.1/main.min.css'>
    <link rel='stylesheet' href='css/@fullcalendar/core@4.3.0/main.min.css'>
<script src='js/@fullcalendar/core@4.3.1/main.min.js'></script>
<script src='js/@fullcalendar/daygrid@4.3.0/main.min.js'></script>

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

        <a href="default"><span style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span></a>

    </div>


    <div id='task_calendar'></div>


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
                                left: 'prev,next today',
                                center: 'title',
                                right: 'individual,admin,design,lighting,furniture,sls,eng,tw,overall',
                            },

                            //Individual按鈕：只顯示出Creator、Assignee或Collaborator是當前使用者的task在日曆上
                            //Lighting按鈕：只顯示出專案的category為Lighting的task在日曆上
                            //Office Systems按鈕：只顯示出專案的category為Office Systems的task在日曆上
                            //all按鈕：顯示出所有的task在日曆上
                            customButtons: {
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

<script defer src="js/npm/vue/dist/vue.js"></script>
<script src="js/vue-select.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/exif-js.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>



</html>