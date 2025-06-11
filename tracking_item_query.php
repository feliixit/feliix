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

            if($decoded->exp < time())
            {
                header( 'location:index' );
            }

            if($decoded->data->limited_access == true)
                header( 'location:index' );
            
            $user_id = $decoded->data->id;

$GLOBALS['position'] = $decoded->data->position;
$GLOBALS['department'] = $decoded->data->department;

if($GLOBALS['department'] == 'Lighting' || $GLOBALS['department'] == 'Office' || $GLOBALS['department'] == 'Sales'){
$test_manager = "1";
}

//  ('Kuan', 'Dennis Lin', 'dereck', 'Ariel Lin', 'Kristel Tan');
if($user_id == 48 || $user_id == 2 || $user_id == 1 || $user_id == 6 || $user_id == 3 || $user_id == 89 || $user_id == 129 || $user_id == 137 || $user_id == 138 || $user_id == 148 || $user_id == 191 || $user_id == 195)
$test_manager = "1";
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
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Query of Tracking Code</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

          <link rel="stylesheet" href="css/chosen/chosen.min.css">

    <!-- jQuery和js載入 -->
    <!-- <script defer src="//code.jquery.com/jquery-1.11.3.min.js"></script> -->

    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js" defer></script>
    <script defer src="js/chosen/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');
            // toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
            // toggleme($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'),'show');
            // toggleme($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'),'show');

            dialogshow($('.list_function .new_project a.add'), $('.list_function .dialog.d-add'));
            dialogshow($('.list_function .new_project a.filter'), $('.list_function .dialog.d-filter'));
            dialogshow($('.list_function .new_project a.sort'), $('.list_function .dialog.d-sort'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })
    </script>
    <style>
        .list_function .new_project {
            margin-top: -15px;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
        }

        body.fourth .bodybox {
            min-height: 120vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .list_function .new_project a.filter {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_filter.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .list_function .new_project a.sort {
            font-size: 0;
            background-color: #00811e;
            background-image: url(images/ui/btn_sort.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 46px;
            height: 46px;
            line-height: 36px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        div.query_result {
            overflow-x: auto;
            margin-top: 10px;
        }

        div.query_result > h5 {
            font-size: 28px;
        }

        div.query_result > h5 > span {
            color: red;
            font-weight: 500;
        }

        #tb_tracking_codes {
            width: 100%;
            margin: 5px 0 10px 0;
        }

        #tb_tracking_codes thead th {
            background-color: var(--fth01);
            border: 1px solid var(--fth03);
            font-size: 14px;
            color: #FFF;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        #tb_tracking_codes tbody td {
            border: 1px solid var(--fth03);
            vertical-align: top;
            padding: 10px;
        }

        #tb_tracking_codes tbody tr:nth-of-type(even) {
            background-color: #F6F6F6;
        }


        #tb_tracking_codes tbody tr td:nth-of-type(1) {
            width: 30%;
        }

        #tb_tracking_codes tbody tr td:nth-of-type(2) {
            width: 30%;
        }

        #tb_tracking_codes tbody tr td:nth-of-type(3) {
            width: 40%;
        }

        #tb_tracking_codes tbody tr td:nth-of-type(3) img {
            max-width: 100px;
            max-height: 100px;
        }

        #tb_tracking_codes tbody tr td ul {
            margin-bottom: 5px;
        }

        #tb_tracking_codes ul li {
            display: table-cell;
            text-decoration: none;
            text-align: left;
            font-size: 16px;
        }

        #tb_tracking_codes ul li:first-of-type {
            font-weight: 600;
            padding: 1px 7px 1px 5px;
            max-width: 230px;
            vertical-align: top;
        }

        #tb_tracking_codes ul li:nth-of-type(2) span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
        }

        #tb_tracking_codes ul li:nth-of-type(2) span + span{
            margin-left: 5px;
        }

        #tb_tracking_codes tbody td ul li:nth-of-type(2) a {
            color: #007bff;
        }

        #video_area {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #video_area #video {
            border: 1px solid gray;
            width: 60%;
            height: 60%;
        }

        #video_area > div {
            margin-top: 12px;
        }

        #video_area > div > a.btn {
            width: 130px!important;
            margin: 0 20px 0 20px!important;
        }

        #filter_dialog {
            width: 1000px;
        }

        #codebox {
            width: 88%;
        }

        @media screen and (max-width: 640px) {
            #filter_dialog {
                width: calc(100vw - 24px);
            }

            #codebox {
                width: 82%;
            }

            .dialog .formbox .half:nth-of-type(odd)
                margin-Specificity: (0,4,0)
            {

            body.gray .mainContent {
                padding-top: 475px;
            }


        }

        

    </style>
</head>

<body class="fourth">

<div class="bodybox">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent">
        <!-- mainContent為動態內容包覆的內容區塊 -->
        <div class="block">
            <div class="list_function">
                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Query Condition:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Tracking Code (Use Semicolon to Separate Multiple Tracking Codes)</dt>
                                <dd>
                                    <input id="codebox" type="text" v-model="fil_tracking">
                                    <a class="btn small green" style="margin-left: 2% !important;" id="startButton">Scan</a>
                                </dd>

                                <div id="video_area" style="display: none;">
                                    <video id="video"></video>

                                    <div>
                                        <a class="btn small orange" id="resetButton">Stop</a>
                                        <a id="switchCameraButton" class="btn small blue">Switch Camera</a>
                                    </div>
                                </div>



                                <dt>Product ID</dt>
                                <dd>
                                    <input type="text" v-model="fil_prod_id" id="prod_id">
                                </dd>

                                <dt>Product Code</dt>
                                <dd>
                                    <input type="text" v-model="fil_prod_code">
                                </dd>

                                <dt>Inventory Pool</dt>
                                <dd>
                                    <select v-model="fil_pool">
                                        <option value=""></option>
                                        <option value="Project Pool">Project Pool</option>
                                        <option value="Stock Pool">Stock Pool</option>
                                    </select>
                                </dd>

                                <dt>Related Project</dt>
                                <dd>
                                    <select v-model="fil_project_related">
                                        <option v-for="(item, index) in projects" :value="item.id">{{ item.project_name}}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Location</dt>
                                <dd>
                                    <select v-model="fil_location">
                                        <option value=""></option>
                                        <option value="Caloocan">Caloocan</option>
                                        <option value="Makati">Makati</option>
                                    </select>
                                </dd>

                                <dt>Used as Sample?</dt>
                                <dd>
                                    <select v-model="fil_sample">
                                        <option value=""></option>
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </dd>

                                <dt>Status of Tracking Code</dt>
                                <dd>
                                    <select v-model="fil_status">
                                        <option value=""></option>
                                        <option value="0">On Hand</option>
                                        <option value="1">Lost</option>
                                        <option value="2">Delivered to Client</option>
                                        <option value="3">Scrapped</option>
                                        <option value="-1">Voided</option>
                                    </select>
                                </dd>

                                <dt>Which Order?</dt>
                                <dd>
                                    <select v-model="fil_order">
                                        <option v-for="(item, index) in orders" :value="item.id">{{ item.od_name}}</option>
                                    </select>
                                </dd>

                                <dt style="margin-bottom:-18px;">Created Date of Tracking Code (i.e., Received Date of Item by Warehouse)</dt>
                                <div class="half">
                                    <dt>from</dt>
                                    <dd><input type="date" v-model="fil_date_from"></dd>
                                </div>

                                <div class="half" style="margin-left: 1%;">
                                    <dt>to</dt>
                                    <dd><input type="date" v-model="fil_date_to"></dd>
                                </div>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a
                                    class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green"
                                                                                            @click="apply_filters(1)">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 排序 -->
                <div class="new_project">
                    <a class="sort"></a>
                    <div id="order_dialog" class="dialog d-sort"><h6>Sort Function:</h6>
                        <div class="formbox">
                            <dl>
                                <div class="half">
                                    <dt>1st Criterion</dt>
                                    <dd>
                                        <select v-model="od_opt1">
                                            <option value=""></option>
                                            <option value="1">
                                                Tracking Code
                                            </option>
                                            <option value="2">
                                                Created Date of Tracking Code
                                            </option>
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord1">
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
                                        <select v-model="od_opt2">
                                            <option value=""></option>
                                            <option value="1">
                                                Tracking Code
                                            </option>
                                            <option value="2">
                                                Created Date of Tracking Code
                                            </option>          
                                        </select>
                                    </dd>
                                </div>

                                <div class="half">
                                    <dt></dt>
                                    <dd>
                                        <select v-model="od_ord2">
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
                            <div class="btnbox"><a class="btn small" @click="cancel_orders()">Cancel</a><a
                                    class="btn small" @click="clear_orders()">Clear</a> <a class="btn small green"
                                                                                           @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>

                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>

                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
            <!-- list -->

            <div class="query_result">
                <h5>Result: <span>{{total}}</span> Tracking Code(s) Found</h5>

                <table id="tb_tracking_codes" class="table  table-sm table-bordered" >

                    <thead>
                    <tr>
                        <th>Tracking Code Info</th>
                        <th>Inventory Info</th>
                        <th>Product Info</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(item, index) in receive_records" :key="index">
                        <td>
                            <ul>
                                <li>Tracking Code:</li>
                                <li>{{ item.format_bar }}</li>
                            </ul>

                            <ul>
                                <li>Status:</li>
                                <li>{{ item.status_text }}</li>
                            </ul>

                            <ul>
                                <li style="width: 135px;">Purchased thru Which Order:</li>
                                <li><a :href="item.order_url" target="_blank">{{item.order_name}}</a></li>
                            </ul>

                            <ul>
                                <li>Created:</li>
                                <li>{{ item.created_at }} ({{ item.created_by }})</li>
                            </ul>

                            <ul>
                                <li>Updated:</li>
                                <li v-show="item.updated_by != null">{{ item.updated_at }} ({{ item.updated_by }})</li>
                            </ul>
                        </td>

                        <td>
                            <ul>
                                <li>Inventory Pool:</li>
                                <li>{{ item.which_pool }}</li>
                            </ul>

                            <ul>
                                <li style="width: 140px;">Related Project:</li>
                                <li><a :href="'project02?p=' + item.project_id" target="_blank">{{ item.project_name }}</a></li>
                            </ul>

                            <ul>
                                <li>Location:</li>
                                <li>{{ item.location }}</li>
                            </ul>

                            <ul>
                                <li>Sample:</li>
                                <li>{{ item.as_sample }}</li>
                            </ul>
                        </td>

                        <td>
                            <img>

                            <ul>
                                <li>Product ID:</li>
                                <li>{{ item.product_id }}</li>
                            </ul>

                            <ul>
                                <li>Product Code:</li>
                                <li><a :href="'product_display?id=' + item.product_id" target="_blank">{{ item.code }}</a></li>
                            </ul>

                            <ul>
                                <li>Brand:</li>
                                <li>{{ item.brand }}</li>
                            </ul>

                            <!-- 列出 brief -->
                            <ul>
                                <li style="padding: 1px 3px;"></li>
                                <li style="white-space: break-spaces; font-weight: 300;">{{ item.listing }}</li>
                            </ul>

                            <!-- 列出 listing -->
                            <ul>
                                <li style="padding: 1px 3px;"></li>
                                <li style="white-space: break-spaces; font-weight: 500;">{{ item.remark }}</li>
                            </ul>
         
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <!-- list end -->
            <div class="list_function">
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>
                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()"
                       v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script>
<script defer src="js/axios.min.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/tracking_item_query.js"></script>
<script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>
</html>

<script type="text/javascript">
    window.addEventListener('load', function () {
      let selectedDeviceId;
      let videoInputDevices = [];
      let currentDeviceIndex = 0;

      const hints = new Map();
      const formats = [ZXing.BarcodeFormat.CODE_128];
      const CHARSET = 'utf-8';
      hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, formats);
      hints.set(ZXing.DecodeHintType.CHARACTER_SET, CHARSET);
      hints.set(ZXing.DecodeHintType.TRY_HARDER, true);
      hints.set(ZXing.DecodeHintType.PURE_BARCODE, false);

      const codeReader = new ZXing.BrowserMultiFormatReader();
      console.log('ZXing code reader initialized');

      codeReader.listVideoInputDevices()
        .then((devices) => {
          videoInputDevices = devices;
          selectedDeviceId = videoInputDevices[0].deviceId;
          
          document.getElementById('startButton').addEventListener('click', () => {
            document.getElementById('video_area').style.display = 'flex';
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                //console.log(result);
                //alert(result.text);

                if(result.text.length == 16){
                    codeReader.reset();
                    document.getElementById('video_area').style.display = 'none';
                    app.fil_tracking += ";" + result.text;
                }
                
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
                document.getElementById('result').textContent = err;
              }
            });
            console.log(`Started continuous decode from camera with id ${selectedDeviceId}`);
          });

          document.getElementById('resetButton').addEventListener('click', () => {
            codeReader.reset();
            document.getElementById('video_area').style.display = 'none';

            console.log('Reset.');
          });

          document.getElementById('switchCameraButton').addEventListener('click', () => {
            codeReader.reset();
            currentDeviceIndex = (currentDeviceIndex + 1) % videoInputDevices.length;
            selectedDeviceId = videoInputDevices[currentDeviceIndex].deviceId;
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                if(result.text.length == 16){
                    codeReader.reset();
                    document.getElementById('video_area').style.display = 'none';
                    app.fil_tracking += ";" + result.text;
                }
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
                document.getElementById('result').textContent = err;
              }
            });
            console.log(`Switched to camera with id ${selectedDeviceId}`);
          });
        })
        .catch((err) => {
          console.error(err);
        });
    });
  </script>