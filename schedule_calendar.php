<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'/>
    <title>
        Schedule Calendar
    </title>

   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">


    <link href='https://unpkg.com/fullcalendar@5.1.0/main.min.css' rel='stylesheet'/>
    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

    <style>

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 90%;
            margin: 2% auto;
        }

        .add {
            display: flex;
            justify-content: center;
            margin-bottom: 1%;
        }

        .add__input {
            width: 86%;
            border: none;
            border-bottom: 3px solid rgb(222,225,230);
            margin-right: 1%;
            font-size: medium;
        }


        i {
            display: block;
            font-size: x-large;
            color: #206766;
        }

        i:hover {
            opacity: 0.7;
        }

        .messageboard {
            width: 90%;
            border: 1.5px solid rgb(222,225,230);
            border-radius: 10px;
            padding: 1%;
            margin-left:5%;
            margin-bottom: 16px;
        }

        .message__item {
            padding: 5px;
            margin-bottom: 0.5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid rgb(222,225,230);

        }
 
        .message__item input{
            background-color: white;
        }

        .message__item__input {
            width: 100%;
            border: none;
            font-size: medium;
            padding: 5px;
        }

        .table__item{

            padding: 3pt;
            border: 2px solid rgb(222,225,230);

        }




    </style>

</head>
<body>

<div style="background: rgb(2,106,167); padding: 0.5vh; height:7.5vh;">
    <a href="" style="margin-left:1vw; position: relative; top:-10%;" ><span style="color: white;">&#9776;</span></a>

    <span style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span>


</div>



<div id='calendar'></div>

<div id='msg'>
<div class="messageboard" id="messageboard">
    <h3>Message Board</h3>
	<div v-for="(msg, i) in messages" class="message__item">
	<div>
	<input v-if="msg.id == edit" class="add__input" style="width:100%" v-model="msg.message" maxlength="100">
	<div v-else-if="msg.id != edit && msg.updated_at == null" class="message__item__input">{{ msg.message }} (created by {{ msg.created_by }} at {{ msg.created_at }})</div>
	<div v-else class="message__item__input">{{ msg.message }} (edited by {{ msg.updated_by }} at {{ msg.updated_at }})</div>
	</div>
	<div v-if="msg.created_by == user" style="align-items:end; display: flex;">
	<i class="fas fa-pencil-alt" @click="edit_msg(msg.id, msg.message)"style="padding-right: 10%;"></i>
	<i class="fas fa-trash-alt" @click="deleteMessages(msg.id)"></i>
	</div>
	</div>
</div>


<div class="add">
    <input class="add__input" type="text" placeholder="Type Message Here" maxlength="100" v-model="txt">
    <div><i class="fas fa-plus-circle" @click="addMessages(txt)" id="add_message"></i></div>
</div>

</div>






<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="exampleModalScrollable">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myLargeModalLabel"></h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn_close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>


            <div class="modal-body">


                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Title</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_title">

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Color</label>
                    </div>

                    <div class="col-10">


                        <input type="color" class="form-control" style="width:15%;" id="sc_color">

                    </div>

                </div>

                <br>

                <div id="last_editor" style="display:none;">
                    <div class="row">
                        <div class="col-2 align-self-center" style="text-align: center;">

                            <label>Editor</label>
                        </div>

                        <div class="col-10">


                            <input type="text" class="form-control" style="width:40%;" disabled id="sc_editor">

                        </div>


                    </div>

                    <br>

                </div>



                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Date</label>
                    </div>

                    <div class="col-10">


                        <input type="date" class="form-control" style="width:40%;" id="sc_date">

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Project</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_project">

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Sales Executive</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_sales">

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Project-in-charge</label>
                    </div>

                    <div class="col-10">


                        <input type="text" class="form-control" style="width:90%;" id="sc_incharge">

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Time</label>
                    </div>

                    <div class="col-10">

                        <input type="checkbox" class="form-control" id="sc_time" checked> all-day
                        <input type="time" class="form-control" style="width:20%; margin-left:5%; margin-right:1%;" id="sc_stime" disabled> to <input type="time" class="form-control" style="width:20%; margin-left:1%;" id="sc_etime" disabled>

                    </div>

                </div>

                <br><hr>

                <div class="row">

                    <div class="col-12" style="text-align: center;">

                        <table style="width:100%;" id="agenda_table">

                            <tr>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_location"></td>
                                <td style="padding-bottom: 10pt;"><input type="text" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_agenda"></td>
                                <td style="padding-bottom: 10pt;"><input type="time" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_appointtime"></td>
                                <td style="padding-bottom: 10pt;"><input type="time" class="form-control" style="border:none; border-bottom: 1px solid black; border-radius: 0;" id="sc_tb_endtime"></td>
                                <td style="padding-bottom: 10pt;"><i class="fas fa-plus-circle" id="add_agenda"></i></td>

                            </tr>


                            <tr>
                                <th class="table__item" style="width:35%;">Location</th>
                                <th class="table__item" style="width:30%;">Agenda</th>
                                <th class="table__item" style="width:10%;">Appoint Time</th>
                                <th class="table__item" style="width:10%;">End Time</th>
                                <th class="table__item" style="width:15%;">Actions</th>

                            </tr>



                        </table>
                    </div>



                </div>

                <hr><br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Installer needed</label>
                    </div>

                    <div class="col-10">


                        <input type="checkbox" name="sc_Installer_needed" value="AS"> AS
                        <input type="checkbox" name="sc_Installer_needed" style="margin-left:1%;" value="RM"> RM
                        <input type="checkbox" name="sc_Installer_needed" style="margin-left:1%;" value="RS"> RS
                        <input type="checkbox" name="sc_Installer_needed" style="margin-left:1%;" value="CJ"> CJ
                        <input type="checkbox" name="sc_Installer_needed" style="margin-left:1%;" value="JO"> JO


                    </div>

                </div>

                <br>

                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Things to Bring</label>
                    </div>

                    <div class="col-10">

                        <div>
                            From <input type="text" class="form-control" style="width:30%; margin-left:1%;"
                                        placeholder="Location" id="sc_location1">
                        </div>

                        <div>

                            <textarea class="form-control" style="width:90%; margin-top:1%; resize:none; overflow:auto;"
                                      onkeyup="autogrow(this);" id="sc_things"></textarea>
                        </div>

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Products to Bring</label>
                    </div>

                    <div class="col-10">

                        <div>
                            From <input type="text" class="form-control" style="width:30%; margin-left:1%;"
                                        placeholder="Location" id="sc_location2">
                        </div>

                        <div style="margin-top: 1%;">

                            <textarea class="form-control" style="width:90%; resize:none; overflow:auto;"
                                      onkeyup="autogrow(this);" id="sc_products"></textarea>
                        </div>

                        <div id="upload_input" style="display: flex; align-items: center; margin-top:1%;">
                            Upload PO/Quote <input type="file" onChange="onChangeFileUpload(event)" class="form-control" style="width:70%; margin-left:1%;" multiple>
                        </div>
						<div  id="sc_product_files" style="display: flex; align-items: center; margin-top:1%;">
                        </div>
						<input  id="sc_product_files_hide" style="display: none;" value="">
                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Service</label>
                    </div>

                    <div class="col-10">


                        <Select class="form-control" style="width:40%;" id="sc_service">
                            <option value="0">Choose One</option>
                            <option value="1">innova</option>
                            <option value="2">avanza gold</option>
                            <option value="3">avanza gray</option>
                            <option value="4">L3001</option>
                            <option value="5">L3002</option>
                            <option value="6">Grab</option>
                        </Select>

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Driver</label>
                    </div>

                    <div class="col-10">


                        <Select class="form-control" style="width:40%;" id="sc_driver1">
                            <option value="0">Choose One</option>
                            <option value="1">MG</option>
                            <option value="2">AY</option>
                            <option value="3">EV</option>
                            <option value="4">JB</option>
                        </Select>

                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Back-up Driver</label>
                    </div>

                    <div class="col-10">


                        <Select class="form-control" style="width:40%;" id="sc_driver2">
                            <option value="0">Choose One</option>
                            <option value="1">MG</option>
                            <option value="2">AY</option>
                            <option value="3">EV</option>
                            <option value="4">JB</option>
                        </Select>

                    </div>

                </div>

                <br>


                <div class="form-inline row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Photoshoot Request</label>
                    </div>

                    <div class="col-10">


                        <input type="radio" name="sc_Photoshoot_request" value="Yes"> Yes
                        <input type="radio" name="sc_Photoshoot_request" style="margin-left:1%;" value="No"> No
                    </div>

                </div>

                <br>


                <div class="row">
                    <div class="col-2 align-self-center" style="text-align: center;">

                        <label>Notes</label>
                    </div>

                    <div class="col-10">

                        <textarea class="form-control" style="width:90%; resize:none; overflow:auto;"
                                      onkeyup="autogrow(this);" id="sc_notes"></textarea>

                    </div>

                </div>

                <hr>

                <br>

                <div style="margin-left:6vw;">

                    <button class="btn btn-secondary" style="font-weight:700; margin-left:2vw;"
                            id="btn_reset">Reset Schedule
                    </button>

                    <button class="btn btn-primary" style="width:8vw; font-weight:700; margin-left:2vw;"
                            id="btn_add">Add
                    </button>

                    <button class="btn btn-secondary" style="font-weight:700; margin-left:2vw;"
                            id="btn_duplicate">Duplicate Schedule
                    </button>

                    <button class="btn btn-primary" style="width:8vw; font-weight:700; margin-left:2vw;"
                            id="btn_edit">Edit
                    </button>

                    <button class="btn btn-danger" style="width:8vw; font-weight:700; margin-left:2vw;"
                            id="btn_delete">Delete
                    </button>

                    <button class="btn btn-secondary" style="width:8vw; font-weight:700; margin-left:2vw;"
                            id="btn_cancel">Cancel
                    </button>

                    <button class="btn btn-primary" style="width:8vw; font-weight:700; margin-left:2vw;" id="btn_save">
                        Save
                    </button>

                </div>

                <br>


            </div>


        </div>

    </div>

</div>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script>
<script defer src="js/work_calender.js?v=2020112603"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.js"></script>
<script src='https://unpkg.com/fullcalendar@5.1.0/main.min.js'></script>
<script src='https://fullcalendar.io/js/fullcalendar-2.1.1/fullcalendar.min.js'></script>
</html>
