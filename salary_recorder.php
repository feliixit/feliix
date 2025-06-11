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
            // decode jwt
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            $user_id = $decoded->data->id;
            $username = $decoded->data->username;

            if($decoded->data->limited_access == true)
            header( 'location:index' );
        
            $database = new Database();
            $db = $database->getConnection();

            $access_attendance = false;

            //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
            //    $access2 = true;
            $query = "SELECT * FROM access_control WHERE salary LIKE '%" . $username . "%' ";
            $stmt = $db->prepare( $query );
            $stmt->execute();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $access_attendance = true;
            }

            // 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41), Stefanie Mika C. Santos(99)
            // 為了測試先加上testmanager(87) by BB
            if($access_attendance != true) 
            {
                header( 'location:index' );
            }

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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Daily Expense</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/hierarchy-select.min.css" type="text/css">
    <link rel="stylesheet" href="css/vue-select.css" type="text/css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>

    <style>
        th {
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle !important;
            font-size: small;
        }

        .red {
            color: #ff0000;
        }

        .orange {
            color: #ffa500;
        }

        .green {
            color: #00B000;
        }

        .blue {
            color: #0000ff;
        }

        .hide {
            display: none;
        }

    </style>

    <style>
        div.record_color {
            display: flex;
            align-items: center;
            height: 100%;
        }

        div.record_color > label {
            width: 18px;
            height: 18px;
            margin-bottom: 0;
            margin-left: 3px;
        }

        div.record_color > input:not(:first-child) {
            margin-left: 15px;
        }

        .custom-control-label::before {
            top: 0.75rem !important;
        }

        .custom-control-label::after {
            top: 0.75rem !important;
        }

        tr.deleted {
            position: relative;
        }

        tr.deleted > td:first-of-type::before {
            content: "";
            width: 99%;
            height: 1px;
            background-color: red;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

    </style>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/hierarchy-select.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>


</head>

<body>


<div id="app">
    <div style="background: rgb(2,106,167); padding: 0.5vh; height:7.5vh;">
        <a href="default" style="margin-left:1vw; position: relative; top:-10%;"><span
                style="color: white;">&#9776;</span></a>

        <a href="default"><span
                style="margin-left:1vw; font-weight:700; font-size:xx-large; color: white;">FELIIX</span></a>

        <button :class="[is_viewer == '1'? 'hide' : '']"
                style="border: none; margin-left:0.5vw; font-weight:700; font-size:x-large; background-color:rgb(2,106,167); color: white; padding: 0.5rem 0.5rem 0.5rem 0.5rem; float:right; margin-right:1rem;"
                data-toggle="collapse" data-parent="#accordion" href="#collapseOne" @click="reset()"
                aria-expanded="true" aria-controls="collapseOne"><i class="fas fa-plus-square fa-lg"></i></button>

    </div>


    <div style="margin-top:2.5vh; margin-left:1.5vw; margin-bottom:3vh;">


        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:98.5%;">

            <div class="panel panel-default">

                <div class="panel-heading" role="tab" id="headingOne"
                     style="border: 3px solid rgb(222,226,230); padding:0.5% 0 0.2% 1%;">

                    <h4 class="panel-title">

                    <span
                            style="font-size: 18px;">Add & Edit Record</span>

                    </h4>
                </div>

                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne"
                     :ref="'collapseOne'">

                    <div class="panel-body" style="border: 3px solid rgb(222,226,230); border-top:none;">


                        <table style="margin-left:1vw; line-height: 5vh;" class="table-hover">

                            <div style="margin-bottom: -1.5vh;">&nbsp</div>

                            <tr>
                                <td style="width:15vw;">
                                    <label>Date</label>
                                </td>

                                <td style="text-align: left;"><input type="date"
                                                                     class="form-control custom-control-inline"
                                                                     style="width:15vw;" id="todays-date" readonly></td>

                            </tr>

                            <!--
                            <tr>
                                <td>
                                    <label>Account</label>
                                </td>

                                <td style="text-align: left;">
                                    <select class="form-control" style="width:15vw;" v-model="account">

                                        <option value="1">Office Petty Cash</option>
                                        <option value="3">Online Transactions</option>
                                        <option value="2">Security Bank</option>
                                    </select>
                                </td>

                            </tr>
                            -->

                            <tr>
                                <td>
                                    <label>Operation Type</label>
                                </td>

                                <td style="text-align: left;">
                                    <select class="form-control" style="width:15vw;" v-model="operation_type">

                                        <option value="1">Cash In</option>
                                        <option value="2">Cash Out</option>
                                    </select>
                                </td>

                            </tr>


                            <tr>
                                <td>
                                    <label>Category</label>
                                </td>

                                <td style="text-align: left;">
                                    <select class="form-control" style="width:25vw;" v-model="category">
                                        <option>Absent</option>
                                        <option>Bonus</option>
                                        <option>Borrow Money</option>
                                        <option>Commission</option>
                                        <option>Half day</option>
                                        <option>Late</option>
                                        <option>Night Differencial</option>
                                        <option>Overtime</option>
                                        <option>Pagibig</option>
                                        <option>Philhealth</option>
                                        <option>Return Money</option>
                                        <option>Salary</option>
                                        <option>SSS</option>
                                        <option>Tax Deductions</option>
                                        <option>Undertime</option>
                                        <option>Other</option>
                                    </select>
                                </td>

                            </tr>


                            <!--
                            <tr v-if="category == 'Marketing' || category == 'Office Needs' || category == 'Others' || category ==  'Projects' || category == 'Store'">
                                <td>
                                    <label >Sub Category</label>
                                </td>

                                <td style="text-align: left;">
                                    <select class="form-control" style="width:25vw;"v-model="sub_category">
                                        <option>Allowance</option>
                                        <option>Commission</option>
                                        <option>Delivery</option>
                                        <option>Maintenance</option>
                                        <option>Meals</option>
                                        <option>Misc</option>
                                        <option>Others</option>
                                        <option>Outsource</option>
                                        <option>Petty cash</option>
                                        <option>Products</option>
                                        <option>Supplies</option>
                                        <option>Tools and Materials</option>
                                        <option>Transportation</option>
                                    </select>
                                </td>

                            </tr>

                            <tr v-if="category == 'Projects'">
                                <td style="width: 15vw;">
                                    <label>Project Name</label>
                                </td>
                                <td style="text-align: left;">
                                    <input type="text" class="form-control custom-control-inline" style="width: 25vw;" v-model="project_name">
                                </td>
                            </tr>


                            <tr id="relatedaccount">
                                <td>
                                    <label>Related Account</label>
                                </td>

                                <td style="text-align: left;">
                                    <select class="form-control" style="width:15vw;" v-model="related_account">
                                        <option value="None">None</option>
                                        <option>Office Petty Cash</option>
                                        <option>Online Transactions</option>
                                        <option>Security Bank</option>
                                    </select>
                                </td>

                            </tr>

                            -->

                            <tr id="payee">
                                <td>
                                    <label>Staff Name</label>
                                </td>

                                <td style="text-align: left;">

                                    <!--
                                    <div class="" style="width:15vw;">
                                        <v-select v-model="payee"
                                                  :options="payees"
                                                  attach
                                                  chips
                                                  label="payeeName"
                                                  multiple></v-select>
                                    </div>
                                    -->


                                    <select v-model="payee" class="form-control" style="width:15vw; display: inline-block;">
                                        <option :value="item" v-for="item in payees">{{ item }}</option>
                                        <option value="Other">Other</option>
                                    </select>

                                    <input type="text" class="form-control"
                                           style="width:15vw; margin-left: 10px; display: inline-block;" v-if="payee == 'Other'" v-model="payee_other">

                                </td>


                            </tr>


                            <tr>
                                <td style="width:15vw;">
                                    <label>Paid/Received Date</label>
                                </td>

                                <td style="text-align: left;"><input type="date"
                                                                     class="form-control custom-control-inline"
                                                                     style="width:15vw;" v-model="paid_date"></td>

                            </tr>


                            <tr>
                                <td style="width:15vw;">
                                    <label>Amount</label>
                                </td>

                                <td style="text-align: left;"><input type="text"
                                                                     class="form-control custom-control-inline"
                                                                     style="width:15vw;" v-model="amount"></td>

                            </tr>

                            <tr>
                                <td>
                                    <label>Details</label>
                                </td>

                                <td style="text-align: left; width:70vw;"><textarea class="form-control" rows="2"
                                                                                    :ref="'detail'"
                                                                                    style="width:77vw;"
                                                                                    v-html="details.replaceAll('&lt;br&gt;', '\n')"></textarea>
                                </td>

                            </tr>


                            <tr>
                                <td>
                                    <label>Remarks</label>
                                </td>

                                <td style="text-align: left;"><input type="text" class="form-control"
                                                                     style="width:77vw;" v-model="remarks">
                                </td>

                            </tr>

                            <tr>
                                <td>
                                    <label>Photos</label>
                                </td>

                                <td style="text-align: left;"><input type="file" ref="file0"
                                                                     @change="onChangeFileUpload($event,0)" multiple>
                                </td>

                            </tr>

                            <tr>
                                <td></td>
                                <td>
                                    <div id="sc_product_files"
                                         style="display: flex; flex-direction:column; text-align: left;">
                                        <div class="custom-control custom-checkbox"
                                             v-for="(item,index) in pic_url_array" :key="index">
                                            <div class="custom-control custom-checkbox" style="padding-top: 1%;">
                                                <input type="checkbox" class="custom-control-input" :id="item.id"
                                                       v-model="item.is_checked" name="file_elements"
                                                       :value="item.pic_url">
                                                <label class="custom-control-label" :for="item.id">
                                                    <a :href="baseURL + item.pic_url"
                                                       target="_blank">{{item.pic_url}}</a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label>Record Color</label>
                                </td>

                                <td style="text-align: left;">
                                    <div class="record_color">
                                        <input type="radio" name="record_color" id="record_color_black" value="x"
                                               v-model="is_marked" checked="checked">
                                        <label for="record_color_black" style="background-color: black;"></label>

                                        <input type="radio" name="record_color" id="record_color_red" value="1"
                                               v-model="is_marked">
                                        <label for="record_color_red" style="background-color: red;"></label>

                                        <input type="radio" name="record_color" id="record_color_orange" value="2"
                                               v-model="is_marked">
                                        <label for="record_color_orange" style="background-color: orange;"></label>

                                        <input type="radio" name="record_color" id="record_color_green" value="3"
                                               v-model="is_marked">
                                        <label for="record_color_green" style="background-color: green;"></label>

                                        <input type="radio" name="record_color" id="record_color_blue" value="4"
                                               v-model="is_marked">
                                        <label for="record_color_blue" style="background-color: blue;"></label>

                                    </div>
                                </td>

                            </tr>


                        </table>

                        <div style="margin-left:6vw; margin-top:2vh; margin-bottom:1.5vh;">

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700" v-on:click="reset()">
                                Reset
                            </button>

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="reset()">Cancel
                            </button>

                            <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="add(1,edd)">Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div style="margin-top:2vh; margin-bottom:1vh;">

            <input type="date" v-model="start_date">&nbsp; to &nbsp;<input type="date" v-model="end_date">

            <select style="width:10vw; margin-left:1vw;" v-model="select_date_type">
                <option value="0" seleted>Date</option>
                <option value="1">Paid/Received Date</option>
            </select>

            <!--
            <select style="width:10vw; margin-left:1vw;" v-model="account">
                <option value="0" seleted>All</option>
                <option value="1">Office Petty Cash</option>
                <option value="3">Online Transactions</option>
                <option value="2">Security Bank</option>
            </select>
            -->

            <select style="width:10vw; margin-left:1vw;" v-model="category">
                <option value="" seleted>All</option>
                <option>Absent</option>
                <option>Bonus</option>
                <option>Borrow Money</option>
                <option>Commission</option>
                <option>Half day</option>
                <option>Late</option>
                <option>Night Differencial</option>
                <option>Overtime</option>
                <option>Pagibig</option>
                <option>Philhealth</option>
                <option>Return Money</option>
                <option>Salary</option>
                <option>SSS</option>
                <option>Tax Deductions</option>
                <option>Undertime</option>
                <option>Other</option>
            </select>

            <!--
            <select style="width:10vw; margin-left:1vw;" v-if="category == 'Marketing' || category == 'Office Needs' || category == 'Others' || category ==  'Projects' || category == 'Store'" v-model="sub_category">
                <option>Allowance</option>
                <option>Commission</option>
                <option>Delivery</option>
                <option>Maintenance</option>
                <option>Meals</option>
                <option>Misc</option>
                <option>Others</option>
                <option>Outsource</option>
                <option>Petty cash</option>
                <option>Products</option>
                <option>Supplies</option>
                <option>Tools and Materials</option>
                <option>Transportation</option>
            </select>
            -->

            <input type="text" v-model="keyword" style="width:15vw; margin-left:1vw;"
                   placeholder="Searching Keyword Here">

            <select class="hide" v-model="perPage" v-on:change="getRecords(this)">
                <option v-for="size in inventory" :value="size.id">{{size.name}}</option>
            </select>

            <button style="margin-left:1.5vw;" v-on:click="getRecords"><i class="fas fa-filter"></i></button>&ensp;
            <button v-on:click="printRecord"><i class="fas fa-file-export"></i></button>&ensp;


            <ul class="pagination pagination-sm hide" style="float:right; margin-right:1.5vw;">
                <li class="page-item" :disabled="page == 1" @click="page < 1 ? page = 1 : page--"
                    v-on:click="getRecords"><a class="page-link">Previous</a></li>

                <li class="page-item" v-for="pg in pages" @click="page=pg" :class="[page==pg ? 'active':'']"
                    v-on:click="getRecords"><a class="page-link">{{ pg }}</a></li>

                <li class="page-item" :disabled="page == pages.length" @click="page++" v-on:click="getRecords"><a
                        class="page-link">Next</a></li>
            </ul>

        </div>


        <div id="panelchecked">

            <table class="table table-sm table-bordered table-hover" style="width:97vw;">

                <thead class="thead-light">

                <tr>

                    <th class="text-nowrap" style="width:6vw;">Date</th>

                    <th class="text-nowrap" style="width:7vw;">Category</th>

                    <th class="text-nowrap" style="width:20vw;">Details</th>

                    <th class="text-nowrap" style="width:4vw;">Photos</th>

                    <th class="text-nowrap" style="width:10vw;">Staff Name</th>

                    <th style="width:8vw;">Paid / Received Date</th>

                    <th class="text-nowrap" style="width:5vw;">Cash In</th>

                    <th class="text-nowrap" style="width:5vw;">Cash Out</th>

                    <th class="text-nowrap" style="width:12vw;">Remarks</th>

                    <th class="text-nowrap" style="width:6vw;">Actions</th>


                </tr>

                </thead>

                <tbody>
                <tr v-for='item in items' v-if="item.account == 1"
                    :class="[(item.is_enabled == '0' ? 'deleted' : ''), (item.is_marked == '1' ? 'red' : ''), (item.is_marked == '2' ? 'orange' : ''), (item.is_marked == '3' ? 'green' : ''), (item.is_marked == '4' ? 'blue' : '')]">
                    <td>{{item.created_at | dateString('YYYY-MM-DD')}}</td>

                    <td>{{item.category}}<span v-if="item.sub_category != ''">>>{{item.sub_category}}</span></td>

                    <!--
                   <td><span v-if="item.category == 'Projects'">{{item.project_name}}</span></td>
                   -->

                    <td style="text-align: left;"><span v-html="item.details.replace('&lt;br&gt;', '<br />')"></span>
                    </td>

                    <td v-if="item.pic_url != ''">
                        <a v-for="pic in item.pic_url" :href="`${mail_ip}${pic}`" target="_blank">
                            <i v-if="pic.endsWith('.jpg') || pic.endsWith('.png') || pic.endsWith('.jpeg')"
                               class="fas fa-image fa-lg" style="display:block; margin: 0.5em;">
                            </i>
                            <i v-else="pic.endsWith('.jpg')" class="fas fa-file fa-lg"
                               style="display:block; margin: 0.5em;">
                            </i>
                        </a>
                    </td>

                    <td v-else>
                    </td>

                    <td>{{item.payee}} {{ (item.payee_other != '' ? ': ' + item.payee_other : '') }}</td>

                    <td>{{item.paid_date}}</td>

                    <td style="text-align: right;">{{ item.cash_in.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                        }}
                    </td>

                    <td style="text-align: right;">{{item.cash_out.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g,
                        '$1,')}}
                    </td>


                    <td style="text-align: left;">{{item.remarks}}</td>


                    <td class="text-nowrap" v-if="is_viewer == '1'">
                        <button v-if="item.created_by != 'SYSTEM' && 1==0"><i class="fas fa-lock"
                                                                              :class="[item.is_locked == '1'? 'red' : '']"
                                                                              v-on:click="lockRecord(item.id)"></i>
                        </button>
                    </td>
                    <td class="text-nowrap" v-else-if="item.is_locked == '0'">
                        <button v-if="item.created_by != 'SYSTEM'  && 1==0" data-toggle="collapse"
                                data-parent="#accordion" href="#collapseOne"
                                aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(item.id)"><i
                                class="fas fa-edit"></i>
                        </button>


                        <button v-if="item.created_by != 'SYSTEM'  && 1==0" data-toggle="modal"
                                data-target="#exampleModalScrollable" v-on:click="edit(item.id)"><i
                                class="fas fa-project-diagram"></i>
                        </button>


                        <button v-if="item.created_by != 'SYSTEM' && item.is_enabled == 1"
                                v-on:click="deleteRecord(item.id)"><i class="fas fa-times"></i></button>

                    </td>
                    <td class="text-nowrap" v-else>
                    </td>

                </tr>
                </tbody>

                <thead class="thead-light">

                <tr>
                    <th colspan="4">Total</th>
                    <th style="text-align: center;" colspan="2"><!--Beginning Balance: 0.00--></th>
                    <th style="text-align: right;">
                        {{accountOneCashIn.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}
                    </th>
                    <th style="text-align: right;">
                        {{accountOneCashOut.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}}
                    </th>
                    <th style="text-align: center;" colspan="2">
                        Net Cash Flow: {{accountOneBalance.toFixed(2).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g,
                        '$1,')}}
                    </th>
                </tr>

                </thead>

            </table>

        </div>

    </div>
</div>


</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/npm/exif-js.js"></script>
<script src="js/moment.js"></script>
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script> 
<script src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/a076d05399.js"></script> 
<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<script>

    $(document).ready(function () {
        var today = new Date();
        var dd = ("0" + (today.getDate())).slice(-2);
        var mm = ("0" + (today.getMonth() + 1)).slice(-2);
        var yyyy = today.getFullYear();
        today = yyyy + '-' + mm + '-' + dd;
        $("#todays-date").attr("value", today);
        $("#todays_date").attr("value", today);
    });

</script>


<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/add_or_edit_price_record_salary.js"></script>

</html>
