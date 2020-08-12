<?php include 'check.php';?>
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
<title>FELIIX template pc</title>
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
    toggleme($('.list_function .new_project a.add'),$('.list_function .dialog'),'show');
    $('.tablebox').click(function(){
        $('.list_function .dialog').removeClass('show');
    })
    
})
</script>

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
                <div class="new_project">
                    <a class="add"></a>
                    <div class="dialog">
                        <h6>Create New Project:</h6>
                        <div class="formbox">
                            <dl>
                                <dt>Project Name / Client Name</dt>
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
                                    <dd><input type="text" placeholder="" disabled></dd>
                                </div>
                                <dt>Reason for Estimated Closing Probability</dt>
                                <dd><textarea placeholder="" v-model="reason"></textarea></dd>
                            </dl>
                            <div class="btnbox">
                                <a class="btn small" @click="clear">Cancel</a>
                                <a class="btn small green" @click="approve">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filter -->
                <div class="filter">
                    <select name="" id="" v-model="fil_project_category">
                        <option value="">Project Category</option>
                        <option v-for="item in priorities" :value="item.id" :key="item.priority">
                            {{ item.priority }}
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
                    <select name="" id="">
                        <option value="">Current Stage</option>
                        <option value="C">Client</option>
                        <option value="P">Proposal</option>
                        <option value="AC">A Meeting / Close Deal</option>
                        <option value="O">Order</option>
                        <option value="E">Execution Plan</option>
                        <option value="D">Delivery</option>
                        <option value="I">Installation</option>
                        <option value="CA">Client Feedback / After Service</option>
                    </select>
                </div>
                <!-- 分頁 -->
                <div class="pagenation">
                    <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>
                  
                    <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>
                  
                    <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
                </div>
            </div>
            <!-- list -->
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
                   <li>Recent Message</li>
               </ul>
               <ul v-for='(receive_record, index) in displayedPosts'>
                   <li>{{ receive_record.category }}</li>
                   <li><i class="ct01">{{ receive_record.client_type }}</i></li>
                   <li><i class="pt01">{{ receive_record.priority }}</i></li>
                   <li>{{ receive_record.project_name }}</li>
                   <li>{{ receive_record.project_status }}</li>
                   <li>{{ receive_record.estimate_close_prob }}</li>
                   <li>{{ receive_record.username }}</li>
                   <li>{{ receive_record.created_at }} ~ </li>
                   <li></li>
                   <li></li>
               </ul>
              
           </div>
           <!-- list end -->
           <div class="list_function">
               <!-- 分頁 -->
               <div class="pagenation">
                  <a class="prev" :disabled="page == 1" @click="page < 1 ? page = 1 : page--">Previous</a>
                  <a class="page" v-for="pg in pages" @click="page=pg">{{ pg }}</a>
                  <a class="next" :disabled="page == pages.length" @click="page++">Next</a>
              </div>
           </div>
        </div>
    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="js/project01.js"></script>
</html>
