    var eventObj;
    var icon_function_enable = true;

    document.addEventListener('DOMContentLoaded', function () {


        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {

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

            //Add Schedule被點擊的方法
            customButtons: {
                addEventButton: {
                    text: 'Add Schedule',
                    click: function () {

                        document.getElementById("myLargeModalLabel").innerText = "Add Schedule";
                        document.getElementById("last_editor").style.display = "none";
                        document.getElementById("btn_reset").style.display = "inline";
                        document.getElementById("btn_add").style.display = "inline";
                        document.getElementById("btn_duplicate").style.display = "none";
                        document.getElementById("btn_edit").style.display = "none";
                        document.getElementById("btn_delete").style.display = "none";
                        document.getElementById("btn_cancel").style.display = "none";
                        document.getElementById("btn_save").style.display = "none";

                        resetSchedule();
                        Change_Schedule_State(false, true);
                        icon_function_enable = true;

                        $('#exampleModalScrollable').modal('toggle');
                    }
                }
            },


            //Schedule被點擊的方法
            eventClick: function (info) {


                document.getElementById("myLargeModalLabel").innerText = "Schedule Details";
                eventObj = info.event;
                resetSchedule();
                var sc_content = eventObj.extendedProps.description;



                document.getElementById("sc_title").value = sc_content.Title;
                document.getElementById("sc_color").value = sc_content.Color;

                //設定最後編輯者資訊
                document.getElementById("sc_editor").value = "Dennis Lin at 2020/07/23 10:30";
                document.getElementById("last_editor").style.display = "inline";

                document.getElementById("sc_date").value = sc_content.Date;
                document.getElementById("sc_time").checked = sc_content.Allday;
                document.getElementById("sc_stime").value = sc_content.Starttime;
                document.getElementById("sc_etime").value = sc_content.Endtime;
                document.getElementById("sc_project").value = sc_content.Project;
                document.getElementById("sc_sales").value = sc_content.Sales_Executive;
                document.getElementById("sc_incharge").value = sc_content.Project_in_charge;

                var installer = sc_content.Installer_needed.split(",");

                for (i = 0; i < 5; i++) {
                    document.getElementsByName("sc_Installer_needed")[i].checked = false;}

                for( i=0; i<installer.length;i++){

                    if(installer[i] == "AS")
                        document.getElementsByName("sc_Installer_needed")[0].checked = true;

                    if(installer[i] == "RM")
                        document.getElementsByName("sc_Installer_needed")[1].checked = true;

                    if(installer[i] == "RS")
                        document.getElementsByName("sc_Installer_needed")[2].checked = true;

                    if(installer[i] == "CJ")
                        document.getElementsByName("sc_Installer_needed")[3].checked = true;

                    if(installer[i] == "JO")
                        document.getElementsByName("sc_Installer_needed")[4].checked = true;

                }

                //加入Agenda內容(先刪除未儲存的)
                var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

                for (i = agenda_object.length - 1; i > 1; i--) {

                    agenda_object[i].remove();
                }

                for (i = 0; i < sc_content.Agenda.length; i++) {
                    addAgendaitem(sc_content.Agenda[i].location, sc_content.Agenda[i].agenda, sc_content.Agenda[i].appointtime, sc_content.Agenda[i].endtime);
                }

                document.getElementById("sc_location1").value = sc_content.Location_Things_to_Bring;
                document.getElementById("sc_things").value = sc_content.Things_to_Bring;
                document.getElementById("sc_location2").value = sc_content.Location_Products_to_Bring;
                document.getElementById("sc_products").value = sc_content.Products_to_Bring;
                document.getElementById("sc_service").value = sc_content.Service;
                document.getElementById("sc_driver1").value = sc_content.Driver;
                document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;


                if( sc_content.Photoshoot_Request == "Yes"){
                    document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
                }
                if( sc_content.Photoshoot_Request == "No"){
                    document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
                }

                document.getElementById("sc_notes").value = sc_content.Notes;

                Change_Schedule_State(true, sc_content.Allday);
                icon_function_enable = false;

                document.getElementById("btn_reset").style.display = "none";
                document.getElementById("btn_add").style.display = "none";
                document.getElementById("btn_duplicate").style.display = "inline";
                document.getElementById("btn_edit").style.display = "inline";
                document.getElementById("btn_delete").style.display = "inline";
                document.getElementById("btn_cancel").style.display = "none";
                document.getElementById("btn_save").style.display = "none";

                $('#exampleModalScrollable').modal('toggle');


            },


            eventDrop: function (info) {
                eventObj = info.event;

                alert(info.event.title + " has changed Date.\n\nDatabase should be updated too.");


            },

            editable: true,

        });

        calendar.render();
    });


    $("button[id='btn_add']").click(function () {


        var selected = [];
        $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
            selected.push($(this).val());
        });

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");
        var agenda_content = [];

        for(i=2; i<agenda_object.length;i++){
            agenda_content.push(
                {
                    location: agenda_object[i].getElementsByTagName("input")[0].value,
                    agenda: agenda_object[i].getElementsByTagName("input")[1].value,
                    appointtime: document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[2].value,
                    endtime: document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[3].value
                }
            );
        }


        var sc_content = {
            Date: document.getElementById("sc_date").value,
            Title: document.getElementById("sc_title").value,
            Color: document.getElementById("sc_color").value,
            Allday: document.getElementById("sc_time").checked,
            
            Starttime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_stime").value,
            Endtime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_etime").value,
            Project: document.getElementById("sc_project").value,
            Sales_Executive: document.getElementById("sc_sales").value,
            Project_in_charge: document.getElementById("sc_incharge").value,
            Agenda: agenda_content,
            Installer_needed: selected.join(),
            Location_Things_to_Bring: document.getElementById("sc_location1").value,
            Things_to_Bring: document.getElementById("sc_things").value,
            Location_Products_to_Bring: document.getElementById("sc_location2").value,
            Products_to_Bring: document.getElementById("sc_products").value,
            Service: document.getElementById("sc_service").value,
            Driver: document.getElementById("sc_driver1").value,
            Back_up_Driver: document.getElementById("sc_driver2").value,
            Photoshoot_Request: $('input[name=sc_Photoshoot_request]:checked').val(),
            Notes:  document.getElementById("sc_notes").value,
            is_enable :true
        };



        if(sc_content.Allday) {

            calendar.addEvent({
                title: sc_content.Title,
                start: sc_content.Date,
                allDay: sc_content.Allday,
                description: sc_content,
                borderColor: sc_content.Color,
                backgroundColor: sc_content.Color,

            });
            sc_content.Starttime = document.getElementById("sc_date").value +' 00:00:00';
            sc_content.Endtime = document.getElementById("sc_date").value +' 23:59:59';
            
        } else {

            if ((sc_content.Starttime != "") && (sc_content.Endtime != "") && (sc_content.Endtime >= sc_content.Starttime)) {

                calendar.addEvent({

                    title: sc_content.Title,
                    start: sc_content.Date + "T" + sc_content.Starttime,
                    end: sc_content.Date + "T" + sc_content.Endtime,
                    allDay: sc_content.Allday,
                    description: sc_content,
                    borderColor: sc_content.Color,
                    backgroundColor: sc_content.Color,

                });
            }
        }
        app.addMain(agenda_content,sc_content);

        $('#exampleModalScrollable').modal('toggle');

        resetSchedule();


    });


    $("button[id='btn_close']").click(function () {
        resetSchedule();
    });

    $("button[id='btn_reset']").click(function () {
        resetSchedule();
    });

    function resetSchedule() {

        document.getElementById("sc_date").value = "";
        document.getElementById("sc_time").checked = true;
        document.getElementById("sc_stime").value = "";
        document.getElementById("sc_stime").disabled = true;
        document.getElementById("sc_etime").value = "";
        document.getElementById("sc_etime").disabled = true;
        document.getElementById("sc_title").value = "";
        document.getElementById("sc_color").value = "";
        document.getElementById("sc_project").value = "";
        document.getElementById("sc_sales").value = "";
        document.getElementById("sc_incharge").value = "";

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";


        $("input[name='sc_Installer_needed']").each(function (i, v) {
            $(v).prop('checked', false);
        });

        document.getElementById("sc_location1").value = "";
        document.getElementById("sc_things").value = "";
        document.getElementById("sc_location2").value = "";
        document.getElementById("sc_products").value = "";
        document.getElementById("sc_service").value = "0";
        document.getElementById("sc_driver1").value = "0";
        document.getElementById("sc_driver2").value = "0";

        $("input[name='sc_Photoshoot_request']").each(function (i, v) {
            $(v).prop('checked', false);
        });

        document.getElementById("sc_notes").value = "";

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

        for(i=agenda_object.length-1; i>1;i--){

            agenda_object[i].remove();
        }

    }


    function Change_Schedule_State(status, time_status) {

        document.getElementById("sc_title").disabled = status;
        document.getElementById("sc_color").disabled = status;
        document.getElementById("sc_date").disabled = status;
        document.getElementById("sc_project").disabled = status;
        document.getElementById("sc_sales").disabled = status;
        document.getElementById("sc_incharge").disabled = status;

        if (status == false) {

            if (time_status == true) {
                document.getElementById("sc_time").disabled = false;
                document.getElementById("sc_stime").disabled = true;
                document.getElementById("sc_etime").disabled = true;
            }
            else{
                document.getElementById("sc_time").disabled = false;
                document.getElementById("sc_stime").disabled = false;
                document.getElementById("sc_etime").disabled = false;
            }

        } else {
            document.getElementById("sc_time").disabled = status;
            document.getElementById("sc_stime").disabled = status;
            document.getElementById("sc_etime").disabled = status;
        }

        document.getElementById("sc_tb_location").disabled = status;
        document.getElementById("sc_tb_agenda").disabled = status;
        document.getElementById("sc_tb_appointtime").disabled = status;
        document.getElementById("sc_tb_endtime").disabled = status;

        document.getElementById("add_agenda").disabled = status;




        document.getElementsByName("sc_Installer_needed")[0].disabled = status;
        document.getElementsByName("sc_Installer_needed")[1].disabled = status;
        document.getElementsByName("sc_Installer_needed")[2].disabled = status;
        document.getElementsByName("sc_Installer_needed")[3].disabled = status;
        document.getElementsByName("sc_Installer_needed")[4].disabled = status;

        document.getElementById("sc_location1").disabled = status;
        document.getElementById("sc_things").disabled = status;
        document.getElementById("sc_location2").disabled = status;
        document.getElementById("sc_products").disabled = status;
        document.getElementById("sc_service").disabled = status;
        document.getElementById("sc_driver1").disabled = status;
        document.getElementById("sc_driver2").disabled = status;

        document.getElementsByName("sc_Photoshoot_request")[0].disabled = status;
        document.getElementsByName("sc_Photoshoot_request")[1].disabled = status;

        document.getElementById("sc_notes").disabled = status;

    }


    $(document).on('click', '#btn_edit', function () {

        //調整最下方出現按鈕
        document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_edit").style.display = "none";
        document.getElementById("btn_delete").style.display = "none";
        document.getElementById("btn_cancel").style.display = "inline";
        document.getElementById("btn_save").style.display = "inline";

        document.getElementById("last_editor").style.display = "none";

        //切換元件成為可修改狀態
        Change_Schedule_State(false, eventObj.extendedProps.description.Allday);
        icon_function_enable = true;

    });


    $(document).on('click', '#btn_delete', function () {

        eventObj.remove();
        $('#exampleModalScrollable').modal('toggle');

    });


    $(document).on('click', '#btn_duplicate', function () {

        var sc_content = eventObj.extendedProps.description;

        if(sc_content.Allday) {

            calendar.addEvent({
                title: sc_content.Title,
                start: sc_content.Date,
                allDay: sc_content.Allday,
                description: sc_content,
                borderColor: sc_content.Color,
                backgroundColor: sc_content.Color,

            });
        } else {

            if ((sc_content.Starttime != "") && (sc_content.Endtime != "") && (sc_content.Endtime >= sc_content.Starttime)) {

                calendar.addEvent({

                    title: sc_content.Title,
                    start: sc_content.Date + "T" + sc_content.Starttime,
                    end: sc_content.Date + "T" + sc_content.Endtime,
                    allDay: sc_content.Allday,
                    description: sc_content,
                    borderColor: sc_content.Color,
                    backgroundColor: sc_content.Color,

                });
            }
        }

        $('#exampleModalScrollable').modal('toggle');

        resetSchedule();

    });


    $(document).on('click', '#btn_cancel', function () {

        var sc_content = eventObj.extendedProps.description;

        document.getElementById("sc_title").value = sc_content.Title;
        document.getElementById("sc_color").value = sc_content.Color;

        //設定最後編輯者資訊
        document.getElementById("sc_editor").value = "Dennis Lin at 2020/07/23 10:30";
        document.getElementById("last_editor").style.display = "inline";

        document.getElementById("sc_date").value = sc_content.Date;
        document.getElementById("sc_time").checked = sc_content.Allday;
        document.getElementById("sc_stime").value = sc_content.Starttime;
        document.getElementById("sc_etime").value = sc_content.Endtime;
        document.getElementById("sc_project").value = sc_content.Project;
        document.getElementById("sc_sales").value = sc_content.Sales_Executive;
        document.getElementById("sc_incharge").value = sc_content.Project_in_charge;

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";

        var installer = sc_content.Installer_needed.split(",");

        for(i=0; i<5; i++){
            document.getElementsByName("sc_Installer_needed")[i].checked = false;}

        for (i = 0; i < installer.length; i++) {

            if (installer[i] == "AS")
                document.getElementsByName("sc_Installer_needed")[0].checked = true;

            if (installer[i] == "RM")
                document.getElementsByName("sc_Installer_needed")[1].checked = true;

            if (installer[i] == "RS")
                document.getElementsByName("sc_Installer_needed")[2].checked = true;

            if (installer[i] == "CJ")
                document.getElementsByName("sc_Installer_needed")[3].checked = true;

            if (installer[i] == "JO")
                document.getElementsByName("sc_Installer_needed")[4].checked = true;

        }

        //加入Agenda內容(先刪除未儲存的)
        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

        for (i = agenda_object.length - 1; i > 1; i--) {

            agenda_object[i].remove();
        }

        for (i = 0; i < sc_content.Agenda.length; i++) {
            addAgendaitem(sc_content.Agenda[i].location, sc_content.Agenda[i].agenda, sc_content.Agenda[i].appointtime, sc_content.Agenda[i].endtime);
        }

        document.getElementById("sc_location1").value = sc_content.Location_Things_to_Bring;
        document.getElementById("sc_things").value = sc_content.Things_to_Bring;
        document.getElementById("sc_location2").value = sc_content.Location_Products_to_Bring;
        document.getElementById("sc_products").value = sc_content.Products_to_Bring;
        document.getElementById("sc_service").value = sc_content.Service;
        document.getElementById("sc_driver1").value = sc_content.Driver;
        document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;


        if (sc_content.Photoshoot_Request == "Yes") {
            document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
        }
        if (sc_content.Photoshoot_Request == "No") {
            document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
        }

        document.getElementById("sc_notes").value = sc_content.Notes;

        Change_Schedule_State(true, sc_content.Allday);
        icon_function_enable = false;

        document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_edit").style.display = "inline";
        document.getElementById("btn_delete").style.display = "inline";
        document.getElementById("btn_cancel").style.display = "none";
        document.getElementById("btn_save").style.display = "none";


    });


    $(document).on('click', '#btn_save', function () {


        var selected = [];
        $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
            selected.push($(this).val());
        });

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");
        var agenda_content = [];

        for (i = 2; i < agenda_object.length; i++) {
            agenda_content.push(
                {
                    location: agenda_object[i].getElementsByTagName("input")[0].value,
                    agenda: agenda_object[i].getElementsByTagName("input")[1].value,
                    appointtime: agenda_object[i].getElementsByTagName("input")[2].value,
                    endtime: agenda_object[i].getElementsByTagName("input")[3].value
                }
            );
        }


        var sc_content = {
            Date: document.getElementById("sc_date").value,
            Title: document.getElementById("sc_title").value,
            Color: document.getElementById("sc_color").value,
            Allday: document.getElementById("sc_time").checked,
            Starttime: document.getElementById("sc_stime").value,
            Endtime: document.getElementById("sc_etime").value,
            Project: document.getElementById("sc_project").value,
            Sales_Executive: document.getElementById("sc_sales").value,
            Project_in_charge: document.getElementById("sc_incharge").value,
            Agenda: agenda_content,
            Installer_needed: selected.join(),
            Location_Things_to_Bring: document.getElementById("sc_location1").value,
            Things_to_Bring: document.getElementById("sc_things").value,
            Location_Products_to_Bring: document.getElementById("sc_location2").value,
            Products_to_Bring: document.getElementById("sc_products").value,
            Service: document.getElementById("sc_service").value,
            Driver: document.getElementById("sc_driver1").value,
            Back_up_Driver: document.getElementById("sc_driver2").value,
            Photoshoot_Request: $('input[name=sc_Photoshoot_request]:checked').val(),
            Notes: document.getElementById("sc_notes").value
        };


        if (sc_content.Allday) {

            eventObj.setStart(sc_content.Date + "T00:00");
            eventObj.setEnd(sc_content.Date + "T00:00");
            eventObj.setAllDay(sc_content.Allday);
            eventObj.setProp("title", sc_content.Title);
            eventObj.setProp("borderColor", sc_content.Color);
            eventObj.setProp("backgroundColor", sc_content.Color);
            eventObj.setExtendedProp("description", sc_content);

        } else {

            if ((sc_content.Starttime != "") && (sc_content.Endtime != "") && (sc_content.Endtime >= sc_content.Starttime)) {

                eventObj.setAllDay(sc_content.Allday);
                eventObj.setStart(sc_content.Date + "T" + sc_content.Starttime);
                eventObj.setEnd(sc_content.Date + "T" + sc_content.Endtime);
                eventObj.setProp("title", sc_content.Title);
                eventObj.setProp("borderColor", sc_content.Color);
                eventObj.setProp("backgroundColor", sc_content.Color);
                eventObj.setExtendedProp("description", sc_content);
            } else {

                sc_content.Allday = eventObj.extendedProps.description.Allday;
                sc_content.Starttime = eventObj.extendedProps.description.Starttime;
                sc_content.Endtime = eventObj.extendedProps.description.Endtime;

                if (eventObj.extendedProps.description.Allday) {

                    eventObj.setStart(sc_content.Date + "T00:00");
                    eventObj.setEnd(sc_content.Date + "T00:00");
                    eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                    eventObj.setProp("title", sc_content.Title);
                    eventObj.setProp("borderColor", sc_content.Color);
                    eventObj.setProp("backgroundColor", sc_content.Color);
                    eventObj.setExtendedProp("description", sc_content);

                } else {
                    eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                    eventObj.setStart(sc_content.Date + "T" + eventObj.extendedProps.description.Starttime);
                    eventObj.setEnd(sc_content.Date + "T" + eventObj.extendedProps.description.Endtime);
                    eventObj.setProp("title", sc_content.Title);
                    eventObj.setProp("borderColor", sc_content.Color);
                    eventObj.setProp("backgroundColor", sc_content.Color);
                    eventObj.setExtendedProp("description", sc_content);
                }

                document.getElementById("sc_time").checked = eventObj.extendedProps.description.Allday;
                document.getElementById("sc_stime").value = eventObj.extendedProps.description.Starttime;
                document.getElementById("sc_etime").value = eventObj.extendedProps.description.Endtime;

            }

        }

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";

        Change_Schedule_State(true, eventObj.extendedProps.description.Allday);
        icon_function_enable = false;

        document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_edit").style.display = "inline";
        document.getElementById("btn_delete").style.display = "inline";
        document.getElementById("btn_cancel").style.display = "none";
        document.getElementById("btn_save").style.display = "none";


    });


    $("input[id='sc_time']").change(function () {

        if (this.checked) {
            document.getElementById("sc_stime").disabled = true;
            document.getElementById("sc_etime").disabled = true;
            document.getElementById("sc_stime").value = "";
            document.getElementById("sc_etime").value = "";
        } else {
            document.getElementById("sc_stime").disabled = false;
            document.getElementById("sc_etime").disabled = false;

        }
    });


    var addevent = document.getElementById('add_message');
    var addinput = document.getElementsByClassName('add__input')[0];
    var messageboard = document.getElementsByClassName('messageboard')[0];
    var controlModify = true;


    addevent.onclick = function () {
        var addcontent = addinput.value.trim();
        if (addcontent != "") {
            addMessageitem(addcontent);
            addinput.value = "";
        }
    };

    function addMessageitem(content) {
        var messageItem = document.createElement("div");
        var messageItemText = document.createElement("input");
        var IconsModify = document.createElement("i");
        var IconsTrash = document.createElement("i");

        //添加class
        messageItem.className = "message__item";
        messageItemText.className = "message__item__input";
        messageItemText.disabled = true;
        messageItemText.value = content + ' by ' + app.name + ' at ' + new Date();
        IconsModify.className = "fas fa-pencil-alt";
        IconsModify.style.cssText = "margin-right:1%; margin-left:1%;";
        IconsTrash.className = "fas fa-trash-alt";


        //添加元素，形成父子關係
        messageItem.appendChild(messageItemText);
        messageItem.appendChild(IconsModify);
        messageItem.appendChild(IconsTrash);
        //添加到頁面上
        messageboard.appendChild(messageItem);
        app.addMessages(content);

        //增加修改方法
        IconsModify.onclick = function (ev) {
            if (controlModify == true) {
                controlModify = false;

                messageItemText.value = messageItemText.value.split("(")[0].trim();
                messageItemText.disabled = false;
                messageItemText.focus();

                var counter = 0;
                ev = window.event || ev;
                ev.stopPropagation ? ev.stopPropagation() : ev.cancelBubble = true;
                document.onclick = function () {

                    messageItemText.disabled = true;
                    controlModify = true;

                    if (counter == 0) {
                        messageItemText.value = messageItemText.value + ' by ' + app.name + ' at ' + new Date();
                        counter = counter + 1;
                    }
                }


            }
        }

        //增加刪除方法
        IconsTrash.onclick = function () {
            messageItem.parentNode.removeChild(messageItem);
        }
    }


    function autogrow(textarea) {

        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight, adjustedHeight);

        if (adjustedHeight > textarea.clientHeight) {
            textarea.style.height = (adjustedHeight + 2) + 'px';
        }

    }


    var addagenda = document.getElementById('add_agenda');

    addagenda.onclick = function () {

        if(icon_function_enable) {
            addAgendaitem(document.getElementById('sc_tb_location').value.trim(), document.getElementById('sc_tb_agenda').value.trim(), document.getElementById('sc_tb_appointtime').value, document.getElementById('sc_tb_endtime').value);
            document.getElementById('sc_tb_location').value = "";
            document.getElementById('sc_tb_agenda').value = "";
            document.getElementById('sc_tb_appointtime').value = "";
            document.getElementById('sc_tb_endtime').value = "";
        }
    };



    var controlEdit = true;

    function addAgendaitem(location, agenda, appointtime, endtime) {

        var agendaItem = document.createElement("tr");
        var td_1 = document.createElement("td");
        var td_2 = document.createElement("td");
        var td_3 = document.createElement("td");
        var td_4 = document.createElement("td");
        var td_5 = document.createElement("td");
        var input_1 = document.createElement("input");
        var input_2 = document.createElement("input");
        var input_3 = document.createElement("input");
        var input_4 = document.createElement("input");
        var Icon_1 = document.createElement("i");
        var Icon_2 = document.createElement("i");
        var Icon_3 = document.createElement("i");
        var Icon_4 = document.createElement("i");


        //添加class
        td_1.className = "table__item";
        td_2.className = "table__item";
        td_3.className = "table__item";
        td_4.className = "table__item";
        td_5.className = "table__item";

        input_1.type = "text";
        input_2.type = "text";
        input_3.type = "time";
        input_4.type = "time";
        input_1.className = "form-control";
        input_2.className = "form-control";
        input_3.className = "form-control";
        input_4.className = "form-control";
        input_1.disabled = true;
        input_2.disabled = true;
        input_3.disabled = true;
        input_4.disabled = true;
        input_1.style.cssText = "border: none; background-color: white;";
        input_2.style.cssText = "border: none; background-color: white;";
        input_3.style.cssText = "border: none; background-color: white;";
        input_4.style.cssText = "border: none; background-color: white;";
        input_1.value = location;
        input_2.value = agenda;
        input_3.value = appointtime;
        input_4.value = endtime;

        Icon_1.className = "fas fa-arrow-alt-circle-up";
        Icon_2.className = "fas fa-arrow-alt-circle-down";
        Icon_3.className = "fas fa-edit";
        Icon_4.className = "fas fa-trash-alt";
        Icon_2.style.cssText = "margin-left: 2%;";
        Icon_3.style.cssText = "margin-left: 2%;";
        Icon_4.style.cssText = "margin-left: 2%;";


        //添加元素，形成父子關係
        td_1.appendChild(input_1);
        td_2.appendChild(input_2);
        td_3.appendChild(input_3);
        td_4.appendChild(input_4);
        td_5.appendChild(Icon_1);
        td_5.appendChild(Icon_2);
        td_5.appendChild(Icon_3);
        td_5.appendChild(Icon_4);
        agendaItem.appendChild(td_1);
        agendaItem.appendChild(td_2);
        agendaItem.appendChild(td_3);
        agendaItem.appendChild(td_4);
        agendaItem.appendChild(td_5);


        //添加到頁面上
        document.getElementById('agenda_table').appendChild(agendaItem);

        //增加刪除方法
        Icon_4.onclick = function () {
            if (icon_function_enable) {
                agendaItem.parentNode.removeChild(agendaItem);
            }
        }

        //增加修改方法
        Icon_3.onclick = function (ev) {

            if (icon_function_enable) {
                if (controlEdit == true) {
                    controlEdit = false;

                    input_1.disabled = false;
                    input_2.disabled = false;
                    input_3.disabled = false;
                    input_4.disabled = false;
                    input_1.focus();


                    ev = window.event || ev;
                    ev.stopPropagation ? ev.stopPropagation() : ev.cancelBubble = true;

                    document.onclick = function (event) {

                        if ((event.target != input_1) && (event.target != input_2) && (event.target != input_3) && (event.target != input_4)) {
                            input_1.disabled = true;
                            input_2.disabled = true;
                            input_3.disabled = true;
                            input_4.disabled = true;
                            controlEdit = true;
                        }
                    }
                }
            }
        }


        //增加上移方法
        Icon_1.onclick = function () {

            if (icon_function_enable) {
                if (agendaItem.rowIndex != 2) {

                    var node1 = agendaItem;
                    var node2 = agendaItem.previousElementSibling;

                    var _parent = node1.parentNode;
                    //獲取兩個結點的相對位置
                    var _t1 = node1.nextSibling;
                    var _t2 = node2.nextSibling;

                    //將node2插入到原來node1的位置
                    if (_t1) _parent.insertBefore(node2, _t1);
                    else _parent.appendChild(node2);
                    //將node1插入到原來node2的位置
                    if (_t2) _parent.insertBefore(node1, _t2);
                    else _parent.appendChild(node1);

                }
            }
        }

        //增加下移方法
        Icon_2.onclick = function () {

            if (icon_function_enable) {

                var node1 = agendaItem;
                var node2 = agendaItem.nextElementSibling;

                var _parent = node1.parentNode;
                //獲取兩個結點的相對位置
                var _t1 = node1.nextSibling;
                var _t2 = node2.nextSibling;

                //將node2插入到原來node1的位置
                if (_t1) _parent.insertBefore(node2, _t1);
                else _parent.appendChild(node2);
                //將node1插入到原來node2的位置
                if (_t2) _parent.insertBefore(node1, _t2);
                else _parent.appendChild(node1);

            }
        }
    }
var app = new Vue({

    data:{
        name:'',
        today:'',
    },
    created () {
      this.getMonthDay();
      this.getUserName();
  },
     methods:{
         
        addMain:function(details,main){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
                  form_Data.append('jwt', token);
                  form_Data.append('title', main.Title);
                  form_Data.append('start_time', main.Starttime);
                  form_Data.append('end_time', main.Endtime);
                  form_Data.append('color', main.Color);
                  form_Data.append('text_color', 'white');
                  form_Data.append('project', main.Project);
                  form_Data.append('sales_executive', main.Sales_Executive);
                  form_Data.append('project_in_charge', main.Project_in_charge);
                  form_Data.append('installer_needed', main.Installer_needed);
                  form_Data.append('installer_needed_location', main.Location_Things_to_Bring);
                  form_Data.append('things_to_bring', main.Things_to_Bring);
                  form_Data.append('things_to_bring_location', main.Location_Products_to_Bring);
                  form_Data.append('products_to_bring', main.Products_to_Bring);
                  form_Data.append('products_to_bring_files', main.products_to_bring_files);
                  form_Data.append('service', main.Service);
                  form_Data.append('driver', main.Driver);
                  form_Data.append('back_up_driver', main.Back_up_Driver);
                  form_Data.append('photoshoot_request', main.Photoshoot_Request);
                  form_Data.append('notes', main.Notes);
                  form_Data.append('is_enabled', main.is_enabled);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/work_calender_main',
                      data: form_Data
                  })
                      .then(function (response) {
                          console.log(details);
                          console.log(response);
                          console.log(response.data[0]);
                          _this.addDetails(response.data[0],details);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                      //this.upload();
                      //this.reload();
          },
        
        addDetails:function(mainId,addDetails){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
          for (var i = 0; i < addDetails.length; i++) {
                  form_Data.append('jwt', token);
                  form_Data.append('work_calendar_main_id', mainId);
                  form_Data.append('location', addDetails[i].location);
                  form_Data.append('agenda', addDetails[i].agenda);
                  form_Data.append('appoint_time', addDetails[i].appointtime);
                  form_Data.append('end_time', addDetails[i].endtime);
                  form_Data.append('is_enabled', addDetails[i].is_enabled);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/work_calender_detail',
                      data: form_Data
                  })
                      .then(function (response) {
                          //this.addDetails(response.data[0]);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                      //this.upload();
                     // this.reload();
          }
        },
        addMessages:function(message){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
                  form_Data.append('jwt', token);
                  form_Data.append('message', message);
                  form_Data.append('is_enabled', true);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/work_calender_message',
                      data: form_Data
                  })
                      .then(function (response) {
                          //this.addDetails(response.data[0]);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                      //this.upload();
                     // this.reload();
          },
    getUserName: function() {
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

    form_Data.append('jwt', token);

    axios({
      method: 'post',
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      url: 'api/on_duty_get_myname',
      data: form_Data
    })
    .then(function(response) {
            //handle success
            _this.name = response.data.username;
            _this.is_manager = response.data.is_manager;
            _this.manager_leave = response.data.manager_leave;
            _this.al_credit = response.data.annual_leave;
            _this.sl_credit = response.data.sick_leave;
            _this.is_viewer = response.data.is_viewer;

          })
    .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            })
          });
  },
  getMonthDay:function(){
      let _this = this;
      var today = new Date();
      var first = new Date();
    var dd = ("0" + (today.getDate())).slice(-2);
    var mm = ("0" + (today.getMonth() + 1)).slice(-2);
    var yyyy = today.getFullYear();
    var HH = new Date().getHours();
    var mm = new Date().getMinutes();
    var ss = new Date().getSeconds();
    today = yyyy + '-' + mm + '-' + dd;
    first = yyyy + '-' + mm + '-01';
    _this.file_day = yyyy + mm + dd;
    _this.start_date = first;
    _this.end_date = today;
  },
     },
    
});