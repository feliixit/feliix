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

    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.css'>
    <link rel='stylesheet' href='https://unpkg.com/@fullcalendar/core@4.3.0/main.min.css'>
<script src='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.js'></script>
<script src='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js'></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

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
    var eventObj;

    document.addEventListener('DOMContentLoaded', function() {

                var calendarT1 = document.getElementById('task_calendar');

                let clickCnt = 0;

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
                    url: "api/project03_other_task_calendar",
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
                                console.log(obj[i]);

                                var obj_meeting = {
                                    id: obj[i].stage_id,
                                    title: obj[i].title,
                                    //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                    backgroundColor: obj[i].color,
                                    borderColor: obj[i].color,
                                };

                                event_array_task.push(obj_meeting);
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
                                right: 'individual,admin,design,lighting,furniture,overall',
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
                                            url: "api/project03_other_task_calendar",
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
                                                        console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].stage_id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                        };

                                                        temp.push(obj_meeting);
                                                    }
                                                }


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
                                    url: "api/project03_other_task_calendar",
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
                                                console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].stage_id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/task_management_AD?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }


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
                                    url: "api/project03_other_task_calendar",
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
                                                console.log(obj[i]);

                                                var obj_meeting = {
                                                    id: obj[i].stage_id,
                                                    title: obj[i].title,
                                                    //url: 'https://feliix.myvnc.com/task_management_DS?sid=' + obj[i].stage_id,
                                                    start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                    backgroundColor: obj[i].color,
                                                    borderColor: obj[i].color,
                                                };

                                                temp.push(obj_meeting);
                                            }
                                        }


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
                                            url: "api/project03_other_task_calendar",
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
                                                        console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].stage_id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                        };

                                                        temp.push(obj_meeting);
                                                    }
                                                }


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
                                            url: "api/project03_other_task_calendar",
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
                                                        console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].stage_id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                        };

                                                        temp.push(obj_meeting);
                                                    }
                                                }


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
                                            url: "api/project03_other_task_calendar",
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
                                                        console.log(obj[i]);

                                                        var obj_meeting = {
                                                            id: obj[i].stage_id,
                                                            title: obj[i].title,
                                                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                                                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                                                            backgroundColor: obj[i].color,
                                                            borderColor: obj[i].color,
                                                        };

                                                        temp.push(obj_meeting);
                                                    }
                                                }


                                                calendar_task.addEventSource(temp);

                                            }
                                        });
                                    }
                                }
                            },

                            eventRender: function (info) {
                                
                                //The obvious commented out method will then not work with
                                //single eventClick or single Click Listener
                                //info.el.addEventListener('dblclick', function () {
                                    //  alert('DOUBLE CLICK!'+info.event.title);
                                //});
                                    
                                //wired listener to handle click counts instead of event type
                                info.el.addEventListener('click', function() {
                                        clickCnt++;         
                                    if (clickCnt === 1) {
                                        oneClickTimer = setTimeout(function() {
                                            clickCnt = 0;
                                            alert('SINGLE CLICK example value grab:' + info.event.title );
                                        }, 400);
                                    } else if (clickCnt === 2) {
                                        clearTimeout(oneClickTimer);
                                        clickCnt = 0;
                                        alert('DOUBLE CLICK example value grab:' + info.event.start );
                                    
                                    }          
                                });
                            },

                            

                            editable: false,
                          
                            events: event_array_task,
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



</html>