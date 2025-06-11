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
    <title>Office Item Inventory Change History</title>
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

        .tableframe .tablebox.lv1 li:nth-of-type(1) {
            width: 180px;
        }

         .tableframe .tablebox.lv1 li:nth-of-type(2) {
            width: 130px;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(3) {
            width: 170px;
        }

        .tableframe .tablebox ul.content li:nth-of-type(3) img {
            max-width: 100px;
            max-height: 100px;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(5) {
            width: 200px;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(6) {
            width: 260px;
        }

        .tableframe .tablebox ul.content li:nth-of-type(6) a {
            text-decoration: none;
            color: #0056b3;
            font-weight: 700;
        }

        .tableframe .tablebox.lv1 li:nth-of-type(7),
        .tableframe .tablebox.lv1 li:nth-of-type(8),
        .tableframe .tablebox.lv1 li:nth-of-type(9) {
            width: 150px;
        }

        .tableframe .tablebox ul.content li:nth-of-type(8) span.green {
            color: green;
        }

        .tableframe .tablebox ul.content li:nth-of-type(8) span.red {
            color: red;
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
                <a class="tag A" href="office_item_inventory_check_mgt">Inventory Check</a>
                <a class="tag B" href="office_item_inventory_replenish_mgt">Inventory Replenishment</a>
                <a class="tag C" href="office_item_inventory_modify_mgt">Inventory Modification</a>
                <a class="tag D focus">Inventory Change History</a>
            </div>
            <!-- Blocks -->
            <div class="block D focus">

                <div class="list_function">

                    <!-- 篩選 -->
                    <div class="popupblock">
                        <a class="filtering" id="btn_filter"></a>
                        <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                            <div class="formbox">
                                <dl>
                                    <dt style="margin-top: 2px;">Main Category</dt>
                                    <dd>
                                        <select v-model='lv1' v-on:change="getLevel2()">
                                            <option value="">----- Main Category -----</option>
                                            <!-- Main Category 的選項內容格式為：Main Category(Code)，例如像是：OFFICE SUPPLIES(01) -->
                                            <option :value="item.code" v-for="(item, index) in level1">{{ item.category }}({{ item.code }})
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Sub Category</dt>
                                    <dd>
                                        <select v-model='lv2' v-on:change="getLevel3()">
                                            <option value="">----- Sub Category -----</option>
                                            <!-- Sub Category 的選項內容格式為：Sub Category(Code)，例如像是：BALLPEN(01)。當使用者選擇不同的 Main Category 時，Sub Category 的 select 只會載入特定 Main Category 底下的 Sub Category 到 select 裡面 -->
                                            <option :value="item.code" v-for="(item, index) in level2">{{ item.category }}({{ item.code }})
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Brand</dt>
                                    <dd>
                                        <select v-model='lv3' v-on:change="getLevel4()">
                                            <option value="">----- Brand -----</option>
                                            <!-- Brand 的選項內容格式為：Brand(Code)，例如像是：HP(01)。當使用者在某一層的的 select 選擇了某一個值之後，下一層的 select 只會載入階層架構下該節點的子節點 到 select 裡面 -->
                                            <option :value="item.code" v-for="(item, index) in level3">{{ item.category }}({{ item.code }})
                                            </option>
                                        </select>
                                    </dd>

                                    <dt style="margin-top: 2px;">Description</dt>
                                    <dd>
                                        <select v-model='lv4'>
                                            <option value="">----- Description -----</option>
                                            <option :value="item.code" v-for="(item, index) in level4">{{ item.category }}({{ item.code }})
                                            </option>
                                        </select>
                                    </dd>


                                    <dt style="margin-top: 5px;">Code</dt>
                                    <dd>
                                        <input type="text" v-model="fil_keyword">
                                    </dd>


                                    <dt style="margin-bottom:-18px;">Executed Time</dt>
                                    <div class="half">
                                        <dt>from</dt>
                                        <dd><input type="date" v-model="fil_date_start"></dd>
                                    </div>

                                    <div class="half">
                                        <dt>to</dt>
                                        <dd><input type="date" v-model="fil_date_end"></dd>
                                    </div>


                                    <dt>Executor</dt>
                                    <dd>
                                        <select v-model="fil_approver">
                                            <option value="">
                                            <option v-for="item in approvers" :value="item.username"
                                                    :key="item.username">
                                                {{ item.username }}
                                            </option>
                                        </select>
                                    </dd>

                                    <dt>Reason</dt>
                                    <dd>
                                        <select class="selectpicker" multiple data-live-search="true" data-size="8"
                                            data-width="100%" title="No status selected" id="tag01" v-model="fil_status">

                                            <option value=""></option>
                                            <option value="1">Office Item Application</option>
                                            <option value="2">Inventory Check</option>
                                            <option value="3">Inventory Replenishment</option>
                                            <option value="4">Inventory Modification</option>
                                        </select>
                                    </dd>

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
                                                    Executed Time
                                                </option>
                                                <option value="2">
                                                    Reason
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
                                                    Executed Time
                                                </option>
                                                <option value="2">
                                                    Reason
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


                    <div class="popupblock">
                        <a class="exporting" id="btn_export" @click="export_petty_list()"></a>
                    </div>



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
                            <li>Executed Time</li>
                            <li>Code</li>
                            <li>Image</li>
                            <li>Full Description</li>
                            <li>Executor</li>
                            <li>Reason</li>
                            <li>Qty Before</li>
                            <li>Qty Change</li>
                            <li>Qty After</li>
                        </ul>

                        <!-- 表格內記錄的格式範例 -->
                        <ul class="content" v-for="(item,index) in displayedRecord" :key="index">
                            <li>{{ item.created_at }}</li>
                            <li>{{ item.code }}</li>
                            <li>
                                <a :href="baseURL + item.photo" target="_blank" v-if="item.photo">
                                    <img :src="baseURL + item.photo" v-if="item.photo">
                                </a>
                            </li>
                            <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>
                            <li>{{ item.created_by }}</li>
                            <li>
                                {{ item.action }}<br>
                                <a :href="item.url" target="_blank" v-if="item.act_1">{{ item.act_1 }}</a><br>
                                {{ item.act_2 }}
                            </li>
                            <li>{{ item.qty_before }}</li>
                            <li><span :class="[(item.qty >= 0 ? 'green' : 'red')]">
                                {{ item.qty }}
                            </span></li>
                            <li>{{ item.qty_after }}</li>
                        </ul>
                        
<!--
                        <ul class="content">
                            <li>{{動作執行時間}}</li>
                            <li>{{ item.code1 + item.code2 + item.code3 + item.code4 }}</li>
                            <li>
                                <a :href="item.url" target="_blank" v-if="item.url">
                                    <img :src="item.url" v-if="item.url">
                                </a>
                            </li>
                            <li>{{ item.cat1 }} >> {{ item.cat2 }} >> {{ item.cat3 }} >> {{ item.cat4 }}</li>
                            <li>{{動作執行者}}</li>
                            <li>
                                {{作業流程種類}}<br>
                                <a :href="item.url" target="_blank" v-if="item.url">{{作業流程的單號}}</a><br>
                                {{作業流程的動作敘述}}
                            </li>
                            <li>{{作業執行前的庫存數量}}</li>


                            <li>
                                 數量變化，如果是正的或零，表示成 +數字，而且數字為綠色，則下方的 <span> 結構需要創造出來 
                                <span class="green" v-if="">
                                    +{{數量變化}}
                                </span>

                                數量變化，如果是負，表示成 -數字(不過數量變化本來就有負號了)，而且數字為紅色，則下方的 <span> 結構需要創造出來 
                                <span class="red" v-if="">
                                    {{數量變化}}
                                </span>
                            </li>

                            <li>{{作業執行後的庫存數量}}</li>
                        </ul>
    -->

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
<script src="js/office_item_inventory_change_history.js"></script>

</html>