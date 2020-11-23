
    var eventObj;

    $(document).on("click", "#btn_arrange", function() {


        $('#meeting').show();
    });


    document.addEventListener('DOMContentLoaded', function() {

        let calendarEl = document.getElementById('calendar');

        //##從資料庫中撈出已存在的會議，以便後續初始化到日曆中
        //SELECT ID, meeting_data FROM table_name;

        /* 會議加入array的格式如下：
        var event = {
          id: 資料庫中的會議 id,
          title: 資料庫中的 meeting_data.title,
          start: 資料庫中的 meeting_data.start,
          end: 資料庫中的 meeting_data.end,
          description: 資料庫中的 meeting_data
        };
        event_array.push(event);
       */

        /*
        var event = 
            {
      title: '昨天的活動',
      start: moment().subtract(1, 'days').format('YYYY-MM-DD'),
      end: moment().add(14, 'days').format('YYYY-MM-DD'),
      color: 'lightBlue'
    }; */

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

            success: function(result) {
                console.log(result);
                var obj = JSON.parse(result);
                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            attendee: obj[i].attendee.trim(),
                            items:obj[i].items,
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),	
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: obj[i].id,
                            title: obj[i].subject.trim(),
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
                        right: 'dayGridMonth,timeGridWeek'
                    },

                    //Add Meeting被點擊的方法
                    customButtons: {
                        addEventButton: {
                            text: 'Add Meeting',
                            click: function() {
                                $('#addmeeting-form').trigger("reset");
                                $('#editmeeting-form').hide();
                                $('#addmeeting-form').show();
                                
                                _app1.old_attendee = [];
                                _app1.attendee = [];

                            }
                        }
                    },


                    //日曆上meeting被點擊的方法
                    eventClick: function(info) {
                        $('#editmeeting-form').trigger("reset");
                        $('#addmeeting-form').hide();
                        $('#editmeeting-form > fieldset').prop('disabled', true);
                        $("#oldAttendee").addClass("select_disabled");
                        

                        //取得點擊的meeting資訊並載入表單
                        eventObj = info.event;
                        var obj_meeting = eventObj.extendedProps.description;

                        if (obj_meeting === undefined)
                            return;

                        $("#oldSubject").val(obj_meeting.title);
                        $("#oldCreator").val(info.event.extendedProps.description.creator);
                        $("#oldAttendee").val(info.event.extendedProps.description.items);
                        _app1.old_attendee = info.event.extendedProps.description.items;
                        $("#oldDate").val(obj_meeting.start.split("T")[0]);
                        $("#oldStartTime").val(obj_meeting.start.split("T")[1]);
                        $("#oldEndTime").val(obj_meeting.end.split("T")[1]);
                        $("#oldContent").val(obj_meeting.content);

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
                $("#meeting").hide();

            },

            // show error message to user
            error: function(xhr, resp, text) {

            }
        });


        


    });


    $(document).on("click", "#btn_edit", function() {

        if($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>")
        {
            app1.warning('Only meeting creator can execute this action!');
            return;
        }

        //表單變成可以修改
        $('#editmeeting-form > fieldset').prop('disabled', false);
        $("#oldCreator").prop('disabled', true);

        $("#oldAttendee").removeClass("select_disabled");
  
        //$("oldAttendee").prop('disabled', false);


        //按鈕也會改變
        $("#btn_close").hide();
        $("#btn_delete").hide();
        $("#btn_edit").hide();
        $("#btn_cancel").show();
        $("#btn_save").show();

    });


    $(document).on("click", "#btn_cancel", function() {

        //表單變成不可修改
        $('#editmeeting-form > fieldset').prop('disabled', true);
        // $("oldAttendee").prop('disabled', true);
        $("#oldAttendee").addClass("select_disabled");

        //修改到一半的內容也會放棄並載入原先未修改的內容
        var obj_meeting = eventObj.extendedProps.description;
        $("#oldSubject").val(obj_meeting.title);
        $("#oldCreator").val(obj_meeting.creator);
        $("#oldAttendee").val(obj_meeting.attendee);
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

    $(document).on("click", "#btn_save", function() {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#oldDate").val() + " " + $("#oldStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#oldDate").val() + " " + $("#oldEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

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

        var names = app1.old_attendee.map(function(item) {
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
        var obj_meeting = {
            title: $("#oldSubject").val().trim(),
            attendee: names.toString().trim(),
            items: app1.old_attendee,
            start: $("#oldDate").val() + "T" + $("#oldStartTime").val(),
            end: $("#oldDate").val() + "T" + $("#oldEndTime").val(),
            content: $("#oldContent").val(),
            //creator: "創建人的系統名字" + " " + "按下save鈕的日期時間(小時:分即可)"
            creator: "<?php echo $GLOBALS['username'] ?>"
        };
        $("#oldCreator").val(obj_meeting.creator);

        //##利用 id變數到資料庫中update裡面舊的obj_meeting
        // UPDATE table_name  SET meeting_data = obj_meeting WHERE ID = id;

        token = localStorage.getItem('token');
        var form_Data = new FormData();
      
        form_Data.append('action', 3);

        form_Data.append('id', id);
        form_Data.append('jwt', token);
        form_Data.append('subject', $("#oldSubject").val().trim());
        form_Data.append('message', $("#oldContent").val());
        form_Data.append('attendee', names.toString());
        form_Data.append('start_time', $("#oldDate").val() + "T" + $("#oldStartTime").val());
        form_Data.append('end_time', $("#oldDate").val() + "T" + $("#oldEndTime").val());
        form_Data.append('is_enabled', true);
   
        var _func = app1;

        //DELETE table_name WHERE ID=id;
        $.ajax({
            url: "api/work_calender_meetings",
            type: "POST",
            contentType: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: form_Data,

            success: function(result) {
                console.log(result);
           
                _func.notify_mail(id, 2);
            },

            // show error message to user
            error: function(xhr, resp, text) {

            }
        });

        //##寄送通知信件給會議參與者,告知修改後訊息


        //把修改後的會議資訊 update 到日曆上
        eventObj.setStart(obj_meeting.start);
        eventObj.setEnd(obj_meeting.end);
        eventObj.setProp("title", obj_meeting.title);
        eventObj.setExtendedProp("description", obj_meeting);

        //按鈕也會改變
        $("#btn_cancel").hide();
        $("#btn_save").hide();
        $("#btn_close").show();
        $("#btn_delete").show();
        $("#btn_edit").show();

    });

    $(document).on("click", "#btn_delete", function() {

        if($("#oldCreator")[0].value !== "<?php echo $GLOBALS['username'] ?>")
        {
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

                    success: function(result) {
                        console.log(result);
                        
                        app1.notify_mail(id, 3);
                    },

                    // show error message to user
                    error: function(xhr, resp, text) {

                    }
                });


                //從日曆中刪除該會議
                eventObj.remove();
              
            } else {
          
            }
          });

        

    });


    $(document).on("click", "#btn_add", function() {

        //##任一欄位如果為空則提示欄位不得為空
        //結束時間須晚於開始時間
        let start = moment($("#newDate").val() + " " + $("#newStartTime").val(), "YYYY/MM/DD HH:mm");
        let end = moment($("#newDate").val() + " " + $("#newEndTime").val(), "YYYY/MM/DD HH:mm");

        var isafter = moment(end).isAfter(start);

        if (isafter !== true) {
            app1.warning('Start time must less than End time!');
            return;
        }

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

        var names = app1.attendee.map(function(item) {
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


        // if 所有欄位都不果為空  且 結束時間須晚於開始時間，則做以下動作
        var obj_meeting = {
            title: $("#newSubject").val().trim(),
            attendee: names.toString().trim(),
            items: app1.attendee,
            start: $("#newDate").val() + "T" + $("#newStartTime").val(),
            end: $("#newDate").val() + "T" + $("#newEndTime").val(),
            content: $("#newContent").val(),
            items:app1.attendee,
            //creator: "創建人的系統名字" + " " + "按下Add按鈕的日期時間(小時:分即可)"
            creator: "<?php echo $GLOBALS['username'] ?>"
        };

        var id = app1.addMeetings($("#newSubject").val().trim(),
            $("#newContent").val(),
            names.toString(),
            $("#newDate").val() + "T" + $("#newStartTime").val(),
            $("#newDate").val() + "T" + $("#newEndTime").val(),
            "<?php echo $GLOBALS['username'] ?>"
        );

        //##obj_meeting 內容寫入資料庫
        //資料庫欄位 (ID, meeting_data)  其中ID為自動計數
        //INSERT table_name (meeting_data) VALUES (obj_meeting)


        //##將該obj_meeting在資料庫給的id返回回來，並設定到前端的id變數



        //##寄送通知信件給會議參與者
        //???


        //把新增會議 呈現於日曆上
        calendar.addEvent({
            id: id,
            title: obj_meeting.title,
            start: obj_meeting.start,
            end: obj_meeting.end,
            description: obj_meeting
        });

        $("#addmeeting-form").hide();

    });


    function hideWindow(target) {

        $(target).hide();

        if (target == "#meeting") {
            $("#addmeeting-form").hide();
            $("#editmeeting-form").hide();
        }

    }