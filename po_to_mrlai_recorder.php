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

$show_salary_slip_mgt = false;

//if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
//    $access2 = true;
$query = "SELECT * FROM access_control WHERE payess8 LIKE '%" . $username . "%' ";
$stmt = $db->prepare( $query );
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
$show_salary_slip_mgt = true;
}

// 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41), Stefanie Mika C. Santos(99)
// 為了測試先加上testmanager(87) by BB
if($show_salary_slip_mgt != true)
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
    <title>Recorder of PO from Office Team</title>
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

        .panel-body {
            border: 3px solid rgb(222, 226, 230);
            border-top: none;
            padding: 20px 20px 0;

        }

        .panel-body .tb_add_record {

        }

        .panel-body .tb_add_record > ul {
            list-style-type: none;
            padding-left: 0px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(1) {
            display: table-cell;
            text-align: center;
            width: 230px;
            font-size: 13px;
            font-weight: 400;
            height: 38px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(2) {
            display: table-cell;
            text-align: left;
            padding-left: 10px;
            height: 38px;
        }

        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="date"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) input[type="text"],
        .panel-body .tb_add_record > ul > li:nth-of-type(2) select {
            width: 380px;
        }

        .tb_items {
            padding: 0 40px;
            width: 100%;
            margin-bottom: 30px;
        }

        .tb_items table {
            width: 100%;
        }

        .tb_items tr th, .tb_items tr td {
            width: 150px;
        }

        .tb_items tr th:nth-of-type(1), .tb_items tr td:nth-of-type(1) {
            width: 500px;
        }

        .tb_items tr:nth-of-type(1) td {
            padding: 0 20px 10px;

        }

        .tb_items tr:nth-of-type(1) td input[type="text"] {
            border: none;
            border-bottom: 1px solid black;
            border-radius: 0;
        }

        .tb_items tr:nth-of-type(1) td input[type="number"] {
            border: none;
            border-bottom: 1px solid black;
            border-radius: 0;
        }

        .tb_items tr:nth-of-type(1) td .checkbox_free {
            position: relative;
        }

        .tb_items tr:nth-of-type(1) td .checkbox_free::after {
            position: absolute;
            content: "FREE";
            top: -5px;
            right: -32px;
            font-weight: 500;
            font-size: 13px;
        }

        .tb_items i {
            font-size: 24px;
            color: #206766;
            margin: 0 5px;
            cursor: pointer;
        }

        .tb_items tr:nth-of-type(2) th, .tb_items tr:nth-of-type(n+3) td {
            padding: 5px;
            border: 2px solid rgb(222, 225, 230);
        }

        .tb_items tr:nth-of-type(n+3) td:nth-of-type(1) {
            text-align: left;
            padding: 5px 10px;
        }

        #panelchecked table tr.deleted td {
            text-decoration: line-through;
            text-decoration-color: red;

        }

        .header {
            background-color: rgb(7, 220, 237);
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header button:focus {
            outline: none !important;
        }

        #app .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
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
    <div class="mask"></div>
    <div class="header">
        <div style="display: flex; align-items: center;">
            <a href="default" style="margin-left: 42px; transform: scaleX(1.4); text-decoration: none;">
                <span style="color: white; font-size: 22px; font-weight: 600;">&#9776;</span>
            </a>

            <a href="default" style="margin-left: 25px;">
                <img src="images/ui/logo_light.svg" style="height: 32px;">
            </a>
        </div>

        <button :class="[is_viewer == '1'? 'hide' : '']"
                style="border: none; margin-left:0.5vw; font-weight:700; font-size:x-large; background-color: rgb(7,220,237); color: white; padding: 0.5rem 0.5rem 0.5rem 0.5rem; float:right; margin-right:1rem; "
                data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne"><i class="fas fa-plus-square fa-lg"></i>
        </button>
    </div>


    <div style="margin-top:2.5vh; margin-left:1.5vw; margin-bottom:3vh;">


        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:98.5%;">

            <div class="panel panel-default">

                <div class="panel-heading" role="tab" id="headingOne"
                     style="border: 3px solid rgb(222,226,230); padding:0.5% 0 0.2% 1%;">

                    <h4 class="panel-title">

                    <span
                            style="font-size: 18px;">Add Record</span>

                    </h4>
                </div>

                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne"
                     :ref="'collapseOne'">

                    <div class="panel-body">

                        <div class="tb_add_record">

                            <ul>
                                <li>
                                    <label>Date</label>
                                </li>

                                <li>
                                    <input type="date" class="form-control" v-model="sales_date">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Company</label>
                                </li>

                                <li>
                                    <select class="form-control"  v-model="company">
                                        <option></option>
                                        <option>Buildrite</option>
                                        <option>HFNAC</option>
                                        <option>Mandaue Foam / PhilFoam Furnishing Industries, Inc.</option>
                                        <option>Muebles De Oficina</option>
                                        <option>Office Essentials</option>
                                        <option>Queens Arts and Trends Corp</option>
                                        <option>Sylpauljoyce Corporation</option>
                                        <option>Other (Specify in Notes)</option>
                                    </select>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Client</label>
                                </li>

                                <li>
                                    <textarea class="form-control" style="width: calc( 100vw - 400px);" v-model="client" maxlength="256"></textarea>
                                </li>
                            </ul>

                            <div class="tb_items">
                                <table>
                                    <tr>
                                        <td>
                                            <textarea class="form-control" style="resize: none;" v-model="product_name"></textarea>
                                        </td>

                                        <td>
                                            <input type="number" class="form-control" v-model="qty" min="0" step="1" oninput="this.value|=0">
                                        </td>

                                        <td>
                                            <input type="number" class="form-control" v-model="price">
                                        </td>

                                        <td>
                                            <i class="fas fa-plus-circle" v-if="!item_editing" id="add_item"
                                               @click="add_plus_detail()"></i>
                                            <i class="fas fa-times-circle" v-if="item_editing" style="color: indianred;"
                                               @click="clear_item()"></i>
                                            <i class="fas fa-check-circle" v-if="item_editing" @click="save_item()"></i>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Actions</th>
                                    </tr>

                                    <tr v-for="(item, index) in payments">
                                        <td style="text-align: left; white-space: pre-line;">{{item.product_name}}</td>
                                        <td>{{item.qty}}</td>
                                        <td>{{item.price}}</td>
                                        <td>
                                            <i class="fas fa-edit" @click="edit_plus_detail(item.id)"></i>
                                            <i class="fas fa-trash-alt" @click="del_plus_detail(item.id)"></i>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                            <ul>
                                <li>
                                    <label>Total Amount</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="total_amount">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>Outside Sales</label>
                                </li>

                                <li>
                                    <select class="form-control" v-model="sales_name">
                                        <option value=""></option>
                                        <option v-for="(item, index) in payees">{{ item }}</option>
                                    </select>
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>P.O. Number</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="po"  maxlength="128">
                                </li>
                            </ul>

                            <ul>
                                <li>
                                    <label>DR#</label>
                                </li>

                                <li>
                                    <input type="text" class="form-control" v-model="dr"  maxlength="128">
                                </li>
                            </ul>



                            <ul>
                                <li>
                                    <label>Notes</label>
                                </li>

                                <li>
                                    <textarea class="form-control" style="width: calc( 100vw - 400px);" v-model="note"  maxlength="256"></textarea>
                                </li>
                            </ul>

                            <!--
                            <ul>
                                <li>
                                    <label>Record Color</label>
                                </li>

                                <li>
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
                                </li>
                            </ul>
                            -->

                        </div>

                        <div style="margin-left:6vw; margin-top:2vh; margin-bottom:1.5vh;">

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700" v-on:click="reset()">
                                Reset
                            </button>

                            <button class="btn btn-secondary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="reset()">Cancel
                            </button>

                            <input type="file" id="excelFile" accept=".xls,.xlsx" style="display:none;"
                                   v-on:change="uploadExcel">

                            <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    onclick="document.getElementById('excelFile').click()">Upload PO
                            </button>

                            <button class="btn btn-primary" style="width:10vw; font-weight:700; margin-left:2vw;"
                                    data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne" v-on:click="apply()">Save
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div style="margin-top:2vh; margin-bottom:1vh;">

            <input type="date" v-model="start_date">&nbsp; to &nbsp;<input type="date" v-model="end_date">

            <select style="width: 10vw; margin-left: 1vw;" v-model="comp">
                <option value="">All Companies</option>
                <option></option>
                <option>Buildrite</option>
                <option>HFNAC</option>
                <option>Mandaue Foam / PhilFoam Furnishing Industries, Inc.</option>
                <option>Muebles De Oficina</option>
                <option>Office Essentials</option>
                <option>Queens Arts and Trends Corp</option>
                <option>Sylpauljoyce Corporation</option>
                <option>Other (Specify in Notes)</option>
            </select>

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

            <table class="table table-sm table-bordered" style="width:97vw;">

                <thead class="thead-light">

                <tr>

                    <th class="text-nowrap" style="width:6vw;">Date</th>

                    <th class="text-nowrap" style="width:7vw;">Company</th>

                    <th class="text-nowrap" style="width:20vw;">Client</th>

                    <th class="text-nowrap" style="width:20vw;">Product Name</th>

                    <th class="text-nowrap" style="width:4vw;">Qty</th>

                    <th class="text-nowrap" style="width:5vw;">Unit Price</th>

                    <th style="width:5vw;">Amount</th>

                    <th class="text-nowrap" style="width:8vw;">Total Amount</th>

                    <th class="text-nowrap" style="width:5vw;">Outside Sales</th>

                    <th class="text-nowrap" style="width:5vw;">P.O. Number</th>

                    <th class="text-nowrap" style="width:5vw;">DR#</th>

                    <th class="text-nowrap" style="width:12vw;">Notes</th>

                    <th class="text-nowrap" style="width:6vw;">Action</th>

                </tr>

                </thead>

                <tbody>
                <template v-for='(row, i) in items'>
                    <tr v-for='(item, j) in row.payment' :class="[row.status == '-1'? 'deleted' : '']">
                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.sales_date }}</td>

                         <td v-if="j == 0" :rowspan="row.payment.length">{{ row.company }}</td>

                        <td style="text-align: left;" v-if="j == 0" :rowspan="row.payment.length">{{ row.client }}</td>

                        <td style="text-align: left; white-space: pre-line;">{{ item.product_name }}</td>

                        <td>{{ item.qty == "" ? "" : Number(item.qty).toLocaleString() }}</td>

                        <td>{{ item.price == "" ? "" : Number(item.price).toLocaleString() }}</td>

                        <td>{{ item.free != "" ? "FREE" : Number((item.price == "" ? 0 : item.price ) * (item.qty == ""
                            ? 0 : item.qty )).toLocaleString() }}
                        </td>

                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.total_amount == "" ? "" :
                            Number(row.total_amount).toLocaleString() }}
                        </td>

                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.sales_name }}</td>

                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.po }}</td>

                        <td v-if="j == 0" :rowspan="row.payment.length">{{ row.dr }}</td>

                        <td v-if="j == 0" :rowspan="row.payment.length" style="white-space: pre-line;">{{ row.note }}</td>

                        <td v-if="j == 0" :rowspan="row.payment.length">
                            <button v-if="row.status != -1" @click="deleteRecord(row.id)"><i aria-hidden="true"
                                                                                             class="fas fa-times">
                                                                                            </i>
                            </button>

                            <button data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" v-on:click="edit(row.id)">
	<i class="fas fa-edit"></i>
</button>
                        </td>
                    </tr>
                </template>

                </tbody>

                <thead class="thead-light">

                <tr>
                    <th colspan="7">Total</th>
                    <th style="text-align: right;">{{ Number(allBalance).toLocaleString() }}</th>
                    <th style="text-align: center;" colspan="5"></th>
                </tr>

                </thead>

            </table>

            <br><br>


        </div>


    </div>


</div>


</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/npm/exif-js.js"></script>
<script src="js/moment.js"></script>
<script src="js/vue-select.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>
<script src="js/a076d05399.js"></script>
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

    function uploadFile() {
        let fileInput = document.getElementById("excelFile");
        let file = fileInput.files[0];

        if (!file) {
            alert("請選擇 Excel 檔案");
            return;
        }

        let formData = new FormData();
        formData.append("file", file);

        fetch("upload.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("output").textContent = JSON.stringify(data, null, 4);
        })
        .catch(error => console.error("Error:", error));
    }

</script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script defer src="js/store_sales_recorder_lai.js"></script>

</html>
