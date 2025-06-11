<?php include 'check.php';?>
<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

    <!-- SEO -->
    <title>Office Item Inventory Check</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all" />
    <meta name="author" content="FELIIX" />

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX" />
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website" />
    <meta property="og:description" content="FELIIX" />
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css" type="text/css">

    
    

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js" defer></script>



    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');
            dialogshow($('.list_function .popupblock a.inserting'),$('.list_function .dialog.d-add'));
            dialogshow($('.list_function .popupblock a.filtering'),$('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .popupblock a.sorting'),$('.list_function .dialog.d-sort'));
          
        })
    </script>


    <!-- CSS for current webpage -->
    <style type="text/css">
        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
            color: #0056b3;
        }

        body.fourth {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif !important;
        }

        body.fourth .mainContent>.block,
        body.fourth .mainContent>.block h6,
        body.fourth .mainContent>.block .tablebox,
        body.fourth .mainContent>.block .tablebox>ul>li,
        body.fourth .mainContent>.block .tablebox2,
        body.fourth .mainContent>.block .formbox,
        body.fourth .mainContent>.block .formbox dd,
        body.fourth .mainContent>.tags a {
            border-color: var(--fth03);
        }

        body.fourth header nav a, body.fourth header nav a:link {
            color: #000;
        }

        body.fourth header nav a:hover {
            color: #333;
        }

        body.fourth header nav ul.info {
            margin-bottom: 0;
        }

        body.fourth header nav ul.info b {
            font-weight: bold;
        }

        body.fourth .mainContent > .tags a {
            background-color: var(--fth02);
            margin-right: 5px;
            color: rgb(33, 37, 41);
        }

        body.fourth .mainContent > .tags a:first-of-type {
            margin-right: none;
        }

        body.fourth .mainContent > .tags a.focus {
            background-color: var(--fth01);
            color: white;
        }

        body.fourth .list_function {
            margin-top: 15px;
        }

        body.fourth .list_function .popupblock {
            position: relative;
            float: left;
            margin-right: 10px;
        }

        .list_function a.inserting, .list_function a.filtering, .list_function a.sorting, .list_function a.exporting {
            width: 30px;
            height: 30px;
            background-color: var(--fth04);
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url(images/ui/btn_filter_white.svg);
        }

        .list_function a.inserting {
            background-image: url(images/ui/btn_add_green.svg);
        }

        .list_function a.sorting {
            background-image: url(images/ui/btn_sort_white.svg);
        }

        .list_function a.exporting {
            background-image: url(images/ui/btn_export_white.svg);
        }

        .block .function input[type="month"] {
            border: 2px solid var(--sec03);
        }

        body.fourth .list_function .pagenation{
            padding-top: 5px;
        }

        .tableframe .tablebox ul li:nth-of-type(1) input[type='text'] {
            width: 90%;
            border-color: #1e6ba8;
            background-color: white;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(4) {
            color: #212529;
            min-width: 0;
            width: 170px;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(5),
        .tableframe .tablebox.lv1 li:nth-of-type(6),
        .tableframe .tablebox.lv1 li:nth-of-type(7) {
            width: 170px;
        }

        .tableframe .tablebox ul li:nth-of-type(8) {
            width: 180px;
        }

        .tableframe .tablebox ul li:nth-of-type(8) button {
            border: 2px solid black;
            width: 34px;
            box-sizing: border-box;
            padding: 6px;
            margin: 0 3px;
            font-size: 18px;
        }

        .tableframe .tablebox ul li:nth-of-type(8) button i {
            padding: 0;
            color: #000;
        }

        ul.dropdown-menu.inner li {
            display: block;
            border-right: none;
            padding: 5px;
        }

        .dropdown-menu > .bs-searchbox > input[type='search'] {
            border: 1px solid #ced4da;
        }

        .bootstrap-select.show-tick .dropdown-menu .selected span.check-mark {
            top: 15px;
        }

        .bootstrap-select > .dropdown-toggle:after {
            margin-right: 3px;
        }

    </style>


</head>

<body class="fourth">

    <div id="app" class="bodybox" style="min-height: 150vh;">

        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag A focus">Inventory Check</a>
                <a class="tag B" href="office_item_inventory_replenish_mgt">Inventory Replenishment</a>
                <a class="tag C" href="office_item_inventory_modify_mgt">Inventory Modification</a>
                <a class="tag D" href="office_item_inventory_change_history">Inventory Change History</a>
            </div>
            <!-- Blocks -->
            <div class="block A focus">

                <div class="list_function">

                    <!-- 建立新庫存檢查單 -->
                    <div class="popupblock">
                        <a class="inserting" id="btn_insert"></a>
                        <div id="insert_dialog" class="dialog d-add"><h6>Create New Inventory Check:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt>Name of Inventory Check</dt>
                                    <dd><input type="text" placeholder="" v-model="check_name" style="margin-bottom: 15px;"></dd>
                                </dl>
                                <div class="btnbox">
                                    <a class="btn small" @click="clear()">Cancel</a>
                                    <a class="btn small green" @click="approve()">Create</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 篩選 -->
                    <div class="popupblock">
                        <a class="filtering" id="btn_filter"></a>
                        <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt style="margin-bottom:-15px;">Ticket No.</dt>
                                    <div class="half">
                                        <dt>From</dt>
                                        <dd><input type="number" min="1" step="1" v-model="fil_request_no_lower"></dd>
                                    </div>
                                    <div class="half">
                                        <dt>To</dt>
                                        <dd><input type="number" min="1" step="1" v-model="fil_request_no_upper"></dd>
                                    </div>

                                    <dt style="margin-top: 5px;">Name of Inventory Check</dt>
                                    <dd>
                                        <input type="text" v-model="fil_keyword">
                                    </dd>
                                    
                                    <dt>Status</dt>
                                    <dd>
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="No status selected" id="tag01" v-model="fil_status">

                                            <option value=""></option>
                                            <option value="1">PHASE 1: Create Checking List by Checker</option>
                                            <option value="2">PHASE 2: Inventory Count by Checker</option>
                                            <option value="3">PHASE 3: Review by Approver</option>
                                            <option value="4">PHASE 4: Inventory Check Completed</option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Creator</dt>
                                    <dd>
                                        <select v-model="fil_creator">
                                            <option value="">
                                            <option v-for="item in creators" :value="item.username"
                                                    :key="item.username">
                                                {{ item.username }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Checker</dt>
                                    <dd>
                                        <select v-model="fil_checker">
                                            <option value="">
                                            <option v-for="item in checkers" :value="item.username"
                                                    :key="item.username">
                                                {{ item.username }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Approver</dt>
                                    <dd>
                                        <select v-model="fil_approver">
                                            <option value="">
                                            <option v-for="item in approvers" :value="item.username"
                                                    :key="item.username">
                                                {{ item.username }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-bottom:-18px;">Created Time</dt>
                                    <div class="half">
                                        <dt>from</dt>
                                        <dd><input type="date" v-model="fil_date_start"></dd>
                                    </div>

                                    <div class="half">
                                        <dt>to</dt>
                                        <dd><input type="date" v-model="fil_date_end"></dd>
                                    </div>

                                    <dt style="margin-bottom:-18px;">Last Updated Time</dt>
                                    <div class="half">
                                        <dt>from</dt>
                                        <dd><input type="date" v-model="fil_update_start"></dd>
                                    </div>

                                    <div class="half">
                                        <dt>to</dt>
                                        <dd><input type="date" v-model="fil_update_end"></dd>
                                    </div>
                                                               
                                </dl>

                                <div class="btnbox"><a class="btn small" @click="filter_clear()">Cancel</a><a
                                        class="btn small" @click="filter_remove()">Clear</a> <a class="btn small green"
                                                                                                @click="filter_apply(1)">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 排序 -->
                    <div class="popupblock">
                        <a class="sorting" id="btn_sort"></a>
                        <div id="sort_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <div class="half">
                                        <dt>1st Criterion</dt>
                                        <dd>
                                            <select v-model="od_factor1">
                                                <option value="0"></option>
                                                <option value="1">
                                                    Ticket No.
                                                </option>
                                                <option value="2">
                                                    Created Time
                                                </option>
                                                <option value="3">
                                                    Last Updated Time
                                                </option>
                                            </select>
                                        </dd>
                                    </div>

                                    <div class="half">
                                        <dt></dt>
                                        <dd>
                                            <select v-model="od_factor1_order">
                                                <option value="1">
                                                    Ascending
                                                </option>
                                                <option value="2">
                                                    Descending
                                                </option>
                                            </select>
                                        </dd>
                                    </div>

                                    <div class="half">
                                        <dt>2nd Criterion</dt>
                                        <dd>
                                            <select v-model="od_factor2">
                                                <option value="0"></option>
                                                <option value="1">
                                                    Ticket No.
                                                </option>
                                                <option value="2">
                                                    Created Time
                                                </option>
                                                <option value="3">
                                                    Last Updated Time
                                                </option>                        
                                            </select>
                                        </dd>
                                    </div>

                                    <div class="half">
                                        <dt></dt>
                                        <dd>
                                            <select v-model="od_factor2_order">
                                                <option value="1">
                                                    Ascending
                                                </option>
                                                <option value="2">
                                                    Descending
                                                </option>
                                            </select>
                                        </dd>
                                    </div>

                                </dl>
                                <div class="btnbox"><a class="btn small" @click="order_clear">Cancel</a><a
                                        class="btn small" @click="order_remove">Clear</a><a class="btn small green"
                                                                                            @click="filter_apply(1)">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- 這個頁面暫時不需要 匯出功能
                    <div class="popupblock">
                        <a class="exporting" id="btn_export" @click="export_petty_list()"></a>
                    </div>
                    -->


                    <!-- 分頁 -->
                    <div class="pagenation">
                        <a class="prev" :disabled="page == 1" @click="pre_page(); filter_apply();">Prev 10</a>

                        <a class="page" v-for="pg in pages_10" @click="page=pg; filter_apply();" v-bind:style="[pg == page ? { 'background':'rgb(30, 107, 168)', 'color': 'white'} : { }]">{{ pg }}</a>

                        <a class="next" :disabled="page == pages.length" @click="nex_page(); filter_apply();">Next 10</a>
                    </div>
                </div>

                <div class="tableframe">
                    <div class="tablebox lv1">
                        <ul class="head">
                            <li>Ticket No.</li>
                            <li>Name of Inventory Check</li>
                            <li>Phase</li>
                            <li>Created Time</li>
                            <li>Last Updated Time</li>
                            <li>Checker</li>
                            <li>Approver</li>
                            <li>Action</li>
                        </ul>

                       
                        <ul v-for='(record, index) in displayedRecord' :key="index">
                            <li>{{ record.request_no  }}</li>
                            <li>
                                <a v-show="record.is_edited == 1" :class="record.followup == 'Y' ? 'red' : ''" v-bind:href="'office_item_inventory_check?id=' + record.id" target="_blank">{{record.check_name }}</a>
                                <input name="check_name" type="text" v-show="record.is_edited == 0" v-model="check_name" maxlength="1024">
                            </li>
                            <li>{{ record.desc }}</li>
                            <li>{{ record.created_at }}<br>{{record.create_by}}</li>
                            <li>{{ record.updated_at }}<br>{{record.updated_by}}</li>
                            <li>{{ record.check_at }}<br>{{record.checker}}</li>
                            <li>{{ record.approval_at }}<br>{{record.approver}}</li>
                            <li>
                                <!-- 修改名字 -->
                                <button v-show="record.is_edited == 1" @click="editRow(record)"><i class="fas fa-edit"></i></button>

                                <!-- 複製庫存檢查單 -->
                                <button v-show="record.is_edited == 1" @click="duplicateRow(record)"><i class="fas fa-copy"></i></button>

                                <!-- 刪除庫存檢查單 -->
                                <button v-show="record.is_edited == 1" @click="deleteRow(record)"><i class="fas fa-trash"></i></button>

                                <button v-show="record.is_edited == 0" @click="confirmRow(record)"><i class="fas fa-check"></i></button>
                                <button v-show="record.is_edited == 0" @click="cancelRow(record)"><i class="fas fa-times"></i></button>

                            </li>
                        </ul>

                        <!-- 表格內記錄的格式範例
                        <ul>
                            <li>IC-00001</li>
                            <li>Regular Check in July</li>
                            <li>Completed</li>
                            <li>2024-07-31 10:38:04<br>Dennis Lin</li>
                            <li>2024-07-31 12:00:00<br>Kristel Tan</li>
                            <li>Dennis Lin</li>
                            <li>Kristel Tan</li>
                            <li>

                                <button  @click="editRow(receive_records)"><i class="fas fa-edit"></i></button>
                                <button  @click="duplicateRow(receive_records)"><i class="fas fa-copy"></i></button>
                                <button  @click="deleteRow(receive_records)"><i class="fas fa-trash"></i></button>

                            </li>
                        </ul>
                        --!>

                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>
<script defer src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/office_item_inventory_check_mgt.js"></script>

</html>