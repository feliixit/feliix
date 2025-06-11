<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    $access7 = false;

    // 針對 sales_dashboard.php 這個頁面，需要新加入兩位使用者有權限可以看到和存取 sales_dashboard 這個分頁，但新加入的這兩個人不能看到 "Sales Dashboard 1 這個分頁
    if($username == "Marie Kayla Patricia Dequina" || $username == "Gina Donato" || $username == "Aiza Eisma" || $username == "Stephanie De dios")
    {
        $access7 = true;
    }

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    // $access6 = false;

    // if(trim($department) == '')
    // {
    //     if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
    //     {
    //         $access6 = true;
    //     }
    // }

    // if($username == "Glendon Wendell Co")
    // {
    //     $access6 = true;
    // }

    if($access7 == true)
        header( 'location:index' );

}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header('location:index');
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
    <title>FELIIX template</title>
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
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"/>

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');

            dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
            dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
            dialogshow($('.list_function a.filtering'), $('.list_function .dialog.d-filter'));
            //dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            //dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
            dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
            dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
            dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
            dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function () {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function () {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function () {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>

    <style scoped>
        header .headerbox {

            background-color: #94BABB;

        }
    </style>

    <!-- CSS for current webpage -->
    <style type="text/css">

        body.fifth .mainContent > .tags a {
            background-color: #DFEAEA;
            border: 2px solid #94BABB;
            border-bottom: none;
        }

        body.fifth .mainContent > .tags a.focus {
            background-color: #94BABB;
        }

        body.fifth .mainContent > .block, body.fifth .mainContent > .block.focus {
            border: none;
            border-top: 2px solid #94BABB;
            margin-bottom: 0;
        }

        body.fifth .list_function .popupblock {
            position: relative;
            float: left;
            margin-right: 10px;
        }

        body.fifth .list_function .popupblock dl div.half:nth-of-type(odd) {
            width: 48.5%;
        }

        .list_function a.filtering, .list_function a.exporting {
            width: 30px;
            height: 30px;
            background-color: #94BABB;
            background-size: contain;
            background-repeat: no-repeat;
            background-image: url(images/ui/btn_filter_white.svg);
        }


        .list_function a.exporting {
            background-image: url(images/ui/btn_export_white.svg);
        }

        .list_function .dialog {
            border-color: #94BABB;
        }

        .list_function .dialog::before {
            border-color: transparent transparent transparent #94BABB;
        }

        .list_function .dialog h6, .list_function .dialog dt {
            color: #000000;
        }

        .list_function .dialog dt {
            margin-top: 15px;
            padding-bottom: 0px;
        }

        table.spantable {
            border: 2px solid #94BABB;
            border-radius: 12px;
            border-collapse: separate;
            font-size: 14px;
            width: 100%;
            margin: 5px 0 50px;
        }

        table.spantable tr.emphasize {
            font-weight: 500;
        }

        table.spantable th, table.spantable td {
            border-right: 1px solid #94BABB;
            border-bottom: 1px solid #94BABB;
            padding: 5px;
            text-align: center;
        }

        table.spantable thead tr:first-of-type th {
            border-top-left-radius: 9px;
            border-top-right-radius: 9px;
        }

        table.spantable thead tr th:last-of-type {
            border-right: none;
        }

        table.spantable tbody tr td:last-of-type {
            border-right: none;
        }

        table.spantable tbody tr:last-of-type td {
            border-bottom: none;
        }

        table.spantable th {
            background-color: #DFEAEA;
        }

        table.spantable td.money {
            text-align: right;
            min-width: 125px;
        }
    </style>

<script>
        $(function() {
            $('header').load('include/header.php');
            
          
              //  dialogshow($('.list_function a.add.red'), $('.list_function .dialog.r-add'));
              //  dialogshow($('.list_function a.edit.red'), $('.list_function .dialog.r-edit'));
      
        //    dialogshow($('.list_function a.filtering'), $('.list_function .dialog.d-filter'));
            //dialogshow($('.list_function a.add.blue'), $('.list_function .dialog.d-add'));
            //dialogshow($('.list_function a.edit.blue'), $('.list_function .dialog.d-edit'));
            // left block Reply
           // dialogshow($('.btnbox a.reply.r1'), $('.btnbox .dialog.r1'));
          //  dialogshow($('.btnbox a.reply.r2'), $('.btnbox .dialog.r2'));
          //  dialogshow($('.btnbox a.reply.r3'), $('.btnbox .dialog.r3'));
          //  dialogshow($('.btnbox a.reply.r4'), $('.btnbox .dialog.r4'));
            // 套上 .dialogclear 關閉所有的跳出框
            $('.dialogclear').click(function() {
                dialogclear()
            });
            // 根據 select 分類
            $('#opType').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType").val();
                $('.dialog.r-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType2').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType2").val();
                $('.dialog.d-edit').removeClass('edit').removeClass('del').addClass(f);
            })
            $('#opType3').change(function() {
                //console.log('Operation Type:'+$("#opType").val());
                var f = $("#opType3").val();
                $('.dialog.r-add').removeClass('add').removeClass('dup').addClass(f);
            })

            $('.selectbox').on('click', function() {
                $.fancybox.open({
                    src: '#pop-multiSelect',
                    type: 'inline'
                });
            });
        })
    </script>

</head>

<body class="fifth">

<div id="app" class="bodybox" style="min-height: 150vh;">
    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
                <a class="tag A" href="sales_dashboard">Sales Dashboard</a>
                <a class="tag B focus" >Sales Dashboard 1</a>
            </div>

        <!-- Blocks -->
        <div class="block B focus">

            <div class="list_function" style="margin-top: 10px;">

                <!-- 篩選 -->
                <div class="popupblock">
                    <a class="filtering" id="btn_filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt style="margin-bottom: -15px;">Period of Time</dt>
                                <div class="half">
                                    <dt>From</dt>
                                    <dd><input type="month" id="start" name="start"></dd>
                                </div>
                                <div class="half">
                                    <dt>To</dt>
                                    <dd><input type="month" id="end" name="end"></dd>
                                </div>

                                <dt style="margin-top: 5px;">Non-Archived or Archived Porjects</dt>
                                <dd>
                                    <select v-model="fil_archive">
                                        <option value="0">Non-Archived Projects</option>
                                        <option value="1">Archived Projects</option>
                                        <option value="a">All</option>
                                    </select>
                                </dd>

                                <dt>Sales Person</dt>
                                <dd>
                                    <select v-model="fil_creator">
                                        <option value="">All</option>
                                        <option v-for="item in creators" :value="item.username"
                                                :key="item.username">
                                            {{ item.username }}
                                        </option>
                                    </select>
                                </dd>

                                <dt>Category</dt>
                                <dd>
                                    <select v-model="fil_category">
                                        <option value="">All</option>
                                        <option value="2">Lighting</option>
                                        <option value="1">Office Systems</option>
                                    </select>
                                </dd>


                            </dl>

                            <div class="btnbox"><a class="btn small" @click="filter_clear()">Cancel</a><a
                                    class="btn small" @click="filter_remove()">Clear</a> <a class="btn small green"
                                                                                            @click="filter_apply()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 匯出 -->
                <div class="popupblock">
                    <a class="exporting" id="btn_export" @click="export_petty()"></a>
                </div>


            </div>

            <div style="width: 100%; overflow-x: auto;">

                <table class="spantable">

                    <thead>
                    <tr>
                        <th colspan="8">
                            {{ date }}
                        </th>
                    </tr>

                    <tr>
                        <th>Sales Person</th>
                        <th>Classification</th>
                        <th>Category</th>
                        <th>Project Name</th>
                        <th>Created Time</th>
                        <th>Est. Closing Prob.</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <template v-for="(item, i) in receive_records">
                        <!-- 結構會是：每個銷售人員，在 Classification 欄位裡面會分成三大類 Yet Close-Deal, Close-Deal, Disapproved，每一大類底下會在分列出 lighting 的專案 和 office furniture 的專案 -->
                        <!-- 在 Classification 欄位中，除了列出 每一大類的名稱，也需要列出該大類底下所包含的專案個數，例如「 Yet Close-Deal: 14」 -->
                        <tr v-for="(it, j) in item.yet_lighting_array">
                            
                            <!-- yet close deal -->

                            <td v-if="j == 0" :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{
                                item.username }}
                            </td>
                            <td v-if="j == 0" :rowspan="item.yet_lighting_array.length + item.yet_office_array.length">Yet Close-Deal: {{ item.yet_lighting_array.length + item.yet_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.yet_lighting_array.length">Lighting</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                        <tr v-for="(it, j) in item.yet_office_array">
                            <td v-if="item.yet_lighting_array.length == 0 && j==0"
                                :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{ item.username }}
                            </td>
                            <td v-if="item.yet_lighting_array.length == 0 && j==0"
                                :rowspan="item.yet_lighting_array.length + item.yet_office_array.length">Yet Close-Deal: {{ item.yet_lighting_array.length + item.yet_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.yet_office_array.length">Office Systems</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                        <!-- close deal -->

                        <tr v-for="(it, j) in item.close_lighting_array">
                            <td v-if="item.yet_lighting_array.length + item.yet_office_array == 0 && j == 0" :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{
                                item.username }}
                            </td>
                            <td v-if="j == 0" :rowspan="item.close_lighting_array.length + item.close_office_array.length">Close-Deal: {{ item.close_lighting_array.length + item.close_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.close_lighting_array.length">Lighting</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                        <tr v-for="(it, j) in item.close_office_array">
                            <td v-if="item.yet_lighting_array.length + item.yet_office_array  + item.close_lighting_array.length == 0 && j==0"
                                :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{ item.username }}
                            </td>
                            <td v-if="item.close_lighting_array.length == 0 && j==0"
                                :rowspan="item.close_lighting_array.length + item.close_office_array.length">Close-Deal: {{ item.close_lighting_array.length + item.close_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.close_office_array.length">Office Systems</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                        <!-- disapprove -->

                        <tr v-for="(it, j) in item.disapprove_lighting_array">
                            <td v-if="item.yet_lighting_array.length + item.yet_office_array + item.close_lighting_array.length + item.close_office_array.length == 0 && j == 0" :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{
                                item.username }}
                            </td>
                            <td v-if="j == 0" :rowspan="item.disapprove_lighting_array.length + item.disapprove_office_array.length">Disapproved: {{ item.disapprove_lighting_array.length + item.disapprove_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.disapprove_lighting_array.length">Lighting</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                        <tr v-for="(it, j) in item.disapprove_office_array">
                            <td v-if="item.yet_lighting_array.length + item.yet_office_array + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length == 0 && j==0"
                                :rowspan="item.yet_lighting_array.length + item.yet_office_array.length + item.close_lighting_array.length + item.close_office_array.length + item.disapprove_lighting_array.length + item.disapprove_office_array.length">{{ item.username }}
                            </td>
                            <td v-if="item.disapprove_lighting_array.length == 0 && j==0"
                                :rowspan="item.disapprove_lighting_array.length + item.disapprove_office_array.length">Disapproved: {{ item.disapprove_lighting_array.length + item.disapprove_office_array.length }}</td>
                            <td v-if="j == 0" :rowspan="item.disapprove_office_array.length">Office Systems</td>
                            <td>{{ it.project_name }}</td>
                            <td>{{ it.created_at.substring(0, 10) }}</td>
                            <td>{{ it.estimate_close_prob }}</td>
                            <td class="money">{{ it.final_amount == 0 ? "" :
                                Number(it.final_amount).toLocaleString(undefined, {minimumFractionDigits:
                                2,maximumFractionDigits: 2}) }}
                            </td>
                            <td>
                                <a class="btn small green" v-if="it.archive == 0" @click="archive(it.pid)">Archive</a>
                                <a class="btn small" v-if="it.archive == 1" @click="unarchive(it.pid)">Unarchive</a>
                            </td>
                        </tr>

                    </template>


                    </tbody>

                </table>


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
<script src="js/sales_dashboard_1.js"></script>

</html>