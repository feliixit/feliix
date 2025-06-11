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

            $GLOBALS['position'] = $decoded->data->position;
            $GLOBALS['department'] = $decoded->data->department;

            if($decoded->data->limited_access == true)
                header( 'location:index' );
            
            // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
            $test_manager = $decoded->data->test_manager;

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="Bookmark" href="images/favicon.ico" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="images/iosicon.png"/>

<!-- SEO -->
<title>Project Management (Sales)</title>
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

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
    // toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
    // toggleme($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'),'show');
    // toggleme($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'),'show');

    dialogshow($('.list_function .new_project a.add'),$('.list_function .dialog.d-add'));
    dialogshow($('.list_function .new_project a.filter'),$('.list_function .dialog.d-filter'));
    dialogshow($('.list_function .new_project a.sort'),$('.list_function .dialog.d-sort'));

    $('.tablebox').click(function(){
        $('.list_function .dialog').removeClass('show');
    })
    
})
</script>
<style>
    .tablebox.lv1 li:nth-of-type(10) a {color:var(--fth01);}

    .list_function .new_project {
            margin-top: -15px;
        }

        body.fourth .mainContent > .block {
            margin-top: 20px;
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

        .list_function .new_project a.sort  {
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

            <!-- 
                <div class="new_project">
                    <a class="add"></a>
                    
<?php 
  if ($test_manager[0]  == "1")
  {
?>
                    <div id="insert_dialog" class="dialog d-add">
                        <h6>Create New Project:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Name</dt>
                                <dd><input type="text" placeholder="" v-model="project_name"></dd>
                                <dt>Project Category</dt>
                                <dd>
                                    <select v-model="project_category">
                                      <option v-for="item in categorys" :value="item.id" :key="item.category">
                                          {{ item.category }}
                                      </option>
                                    </select>
                                </dd>
                                <div class="half">
                                    <dt>Client Type</dt>
                                    <dd>
                                        <select v-model="client_type">
                                          <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                                              {{ item.client_type }}
                                          </option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Priority</dt>
                                    <dd>
                                        <select v-model="priority">
                                          <option v-for="item in priorities" :value="item.id" :key="item.priority">
                                              {{ item.priority }}
                                          </option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>Project Status</dt>
                                    <dd>
                                        <select v-model="status">
                                          <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                                              {{ item.project_status }}
                                          </option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt></dt>
                                    <dd v-if="showExtra"><input type="text" placeholder="" v-model="special_note"></dd>
                                </div>
                                <dt>Estimated Closing Probability</dt>
                                <dd>
                                    <select v-model="probability">
                                      <option value="0">0</option>
                                      <option value="10">10</option>
                                      <option value="20">20</option>
                                      <option value="30">30</option>
                                      <option value="40">40</option>
                                      <option value="50">50</option>
                                      <option value="60">60</option>
                                      <option value="70">70</option>
                                      <option value="80">80</option>
                                      <option value="90">90</option>
                                      <option value="100">100</option>
                                    </select>
                                </dd>
                                <dt>Reason for Estimated Closing Probability</dt>
                                <dd><textarea placeholder="" v-model="reason"></textarea></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="clear">Cancel</a>
                                <a class="btn small green" @click="approve">Create</a>
                            </div>
                        </div>
                      </div>
<?php
  }
?>
                    
                </div>
-->

                
                <!-- 篩選 -->
                <div class="new_project">
                    <a class="filter"></a>
                    <div id="filter_dialog" class="dialog d-filter"><h6>Filter Function:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Category</dt>
                                <dd>
                                    <select  v-model="fil_project_category">
                                    <option value=""></option>
                                    <option v-for="item in categorys" :value="item.id" :key="item.category">
                                        {{ item.category }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Client Type</dt>
                                <dd>
                                    <select v-model="fil_client_type">
                                    <option value=""></option>
                                    <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                                        {{ item.client_type }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Priority</dt>
                                <dd>
                                    <select v-model="fil_priority">
                                    <option value=""></option>
                                    <option v-for="item in priorities" :value="item.id" :key="item.priority">
                                        {{ item.priority }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Project Status</dt>
                                <dd>
                                    <select v-model="fil_status">
                                    <option value=""></option>
                                    <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                                        {{ item.project_status }}
                                    </option>
                                    <option value="downpayment">Verified Downpayment</option>
                                    <option value="without">Without Verified Downpayment</option>
                                    </select>
                                </dd>

                                <dt>Project Creator</dt>
                                <dd>
                                    <select v-model="fil_creator">
                                    <option value=""></option>
                                    <option v-for="item in creators" :value="item.username" :key="item.username">
                                        {{ item.username }}
                                    </option>
                                    </select>
                                </dd>

                                <dt>Current Stage</dt>
                                <dd>
                                    <select v-model="fil_stage">
                                    <option value=""></option>
                                    <option value="Empty">Empty</option>
                                    <option v-for="item in stages" :value="item.stage" :key="item.stage">
                                        {{ item.stage }}
                                    </option>
                                    </select>
                                </dd>


                                <dt style="margin-bottom:-18px;">Estimated Closing Prob.</dt>
                                <div class="half">
                                    <dt>lower bound</dt>
                                    <dd>
                                        <select v-model="fil_lower">
                                            <option value=""></option>
                                            <option value="0">0</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
                                            <option value="50">50</option>
                                            <option value="60">60</option>
                                            <option value="70">70</option>
                                            <option value="80">80</option>
                                            <option value="90">90</option>
                                            <option value="100">100</option>
                                        </select>
                                    </dd>
                                </div>
                                <div class="half">
                                    <dt>upper bound</dt>
                                    <dd>
                                        <select v-model="fil_upper">
                                            <option value=""></option>
                                            <option value="0">0</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="40">40</option>
                                            <option value="50">50</option>
                                            <option value="60">60</option>
                                            <option value="70">70</option>
                                            <option value="80">80</option>
                                            <option value="90">90</option>
                                            <option value="100">100</option>
                                        </select>
                                    </dd>
                                </div>


                                <dt>Keyword (only for project name and recent message)</dt>
                                <dd><input type="text" v-model="fil_keyword"></dd>

                            </dl>
                            <div class="btnbox"><a class="btn small" @click="cancel_filters()">Cancel</a><a class="btn small" @click="clear_filters()">Clear</a> <a class="btn small green" @click="apply_filters(1)">Apply</a>
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
                                                Execution Period -- Start Date
                                            </option>
                                            <option value="2">
                                                Execution Period -- End Date
                                            </option>
                                            <option value="3">
                                                Estimated Closing Prob.
                                            </option>
                                            <option value="4">Client Stage's Recent Message</option>
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
                                                Execution Period -- Start Date
                                            </option>
                                            <option value="2">
                                                Execution Period -- End Date
                                            </option>
                                            <option value="3">
                                                Estimated Closing Prob.
                                            </option>
                                            <option value="4">Client Stage's Recent Message</option>
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
                            <div class="btnbox"><a class="btn small" @click="cancel_orders()">Cancel</a><a class="btn small" @click="clear_orders()">Clear</a> <a class="btn small green" @click="apply_orders()">Apply</a>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Filter 
                <div class="filter">
                    <select name="" id="" v-model="fil_project_category">
                        <option value="">Project Category</option>
                        <option v-for="item in categorys" :value="item.id" :key="item.category">
                            {{ item.category }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_client_type">
                        <option value="">Client Type</option>
                        <option v-for="item in client_types" :value="item.id" :key="item.client_type">
                            {{ item.client_type }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_priority">
                        <option value="">Priority</option>
                        <option v-for="item in priorities" :value="item.id" :key="item.priority">
                            {{ item.priority }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_status">
                        <option value="">Status</option>
                        <option v-for="item in statuses" :value="item.id" :key="item.project_status">
                            {{ item.project_status }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_stage">
                        <option value="">Current Stage</option>
                        <option value="Empty">Empty</option>
                        <option v-for="item in stages" :value="item.stage" :key="item.stage">
                            {{ item.stage }}
                        </option>
                    </select>
                    <select name="" id="" v-model="fil_creator">
                        <option value="">Project Creator</option>
                        <option v-for="item in creators" :value="item.username" :key="item.username">
                            {{ item.username }}
                        </option>
                    </select>
                </div>

                    -->


                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>
                  
                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()" v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                  
                    <a class="next" :disabled="page == pages.length" @click="nex_page(); apply_filters()">Next 10</a>
                </div>
            </div>
            <!-- list -->
            <div class="tableframe">
              <div class="tablebox lv1">
                 <ul class="head">
                     <li>Project Category</li>
                     <li>Client Type</li>
                     <li>Priority</li>
                     <li>Project Name</li>
                     <li>Status</li>
                     <li>Estimated Closing Prob.</li>
                     <li>Project Creator</li>
                     <li>Execution Period</li>
                     <li>Current Stage</li>
                     <li>Client Stage's<br>Recent Message</li>
                 </ul>
                 <ul v-for='(receive_record, index) in receive_records'>
                     <li>{{ receive_record.category }}</li>
                     <li><i v-bind:class="receive_record.pct_class">{{ receive_record.client_type }}</i></li>
                     <li><i v-bind:class="receive_record.pp_class">{{ receive_record.priority }}</i></li>
                     <li><a v-bind:href="'project02?p='+ receive_record.id">{{ receive_record.project_name }}</a></li>
                     <li>{{ receive_record.project_status }}</li>
                     <li>{{ receive_record.estimate_close_prob }}</li>
                     <li>{{ receive_record.username }}</li>
                     <li>{{ receive_record.created_at }} ~ {{ receive_record.updated_at }}</li>
                     <li>{{ receive_record.stage }}</li>
                     <li style="text-align: left;" v-if="receive_record.recent[0].last_client_stage_id != 0"><a v-bind:href="'project03_client?sid=' + receive_record.recent[0].last_client_stage_id" target="_blank">{{ receive_record.recent[0].last_client_message}} <br> ({{ receive_record.recent[0].username }} at {{ receive_record.recent[0].last_client_created_at}})</a></li>
                   <li v-else></li>
                 </ul>
                
             </div>
           </div>
           <!-- list end -->
           <div class="list_function">
               <!-- 分頁 -->
               <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="pre_page(); apply_filters()">Prev 10</a>
                  
                    <a class="page" v-for="pg in pages_10" @click="page=pg; apply_filters()" v-bind:style="[pg == page ? { 'background':'#1e6ba8', 'color': 'white'} : { }]">{{ pg }}</a>
                  
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
<script defer src="js/project01_sls.js?rand=<?php echo uniqid(); ?>"></script>
</html>
