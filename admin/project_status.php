<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
<!-- 共用資料 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

<!-- favicon.ico iOS icon 152x152px -->
<link rel="shortcut icon" href="../images/favicon.ico" />
<link rel="Bookmark" href="../images/favicon.ico" />
<link rel="icon" href="../images/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="../images/iosicon.png"/>

<!-- SEO -->
<title>FELIIX template</title>
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
<link rel="stylesheet" type="text/css" href="../css/default.css"/>
<link rel="stylesheet" type="text/css" href="../css/ui.css"/>
<link rel="stylesheet" type="text/css" href="../css/case.css"/>
<link rel="stylesheet" type="text/css" href="../css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="../js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="../js/rm/realmediaScript.js"></script>
<script type="text/javascript" src="../js/main.js" defer></script>

<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

</head>

<style>
   @media screen and (min-width: 0px) and (max-width: 767px) {
    #my-content { display: none; }  /* hide it on small screens */
    }

    @media screen and (min-width: 768px) and (max-width: 1024px) {
    #my-content { display: block; }   /* show it elsewhere */
    }
</style>

<body class="cyan">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="project_category">Project Category</a>
            <a class="tag B" href="project_client_type">Project Client Type</a>
            <a class="tag C" href="project_priority">Project Priority</a>
            <a class="tag D focus">Project Status</a>
            <a class="tag E"  href="project_stage">Project Stage</a>
        </div>
        <!-- Blocks -->
        <div class="block D focus">
            <h6>Project Status</h6>
            <div class="box-content">
                <div class="box-content"  v-if="!isEditing">
                    <ul>
                        <li><b>Project Status</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="project_status" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width:100%" ></li>
                        
                    </ul>

                    <div>
                        <div>
                            <button type="button" @click="cancelReceiveRecord($event)"><p>CLEAR</p></button>
                            <button type="button" @click="createReceiveRecord()"><p>ADD</p></button>
                        </div>
                    </div>
                </div>

                <div class="box-content" v-else>
                    <ul>
                        <li>
                            <b>Project Status</b>
                        </li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="record.project_status" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width: 100%"></li>
                        
                    </ul>

                    <div>
                        <div>
                            <button type="button" @click="cancelReceiveRecord($event)"><p>CANCEL</p></button>
                            <button type="button" @click="editReceiveRecord($event)"><p>SAVE</p></button>
                        </div>
                    </div>
                </div>

                    

                <div class="tablebox">
                    <ul class="head">
                    <li><i class="micons">view_list</i></li>
                        <li style="font-size:10px;">Project Priority</li>
   
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.index" :true-value="1" v-model:checked="record.is_checked"></li>
                        <li style="font-size:10px;">{{record.project_status}}</li>
                       
                    </ul>
                    
                </div>
                <div class="btnbox">
                    <a class="btn" @click="editRecord()">Modify</a>
                    <a class="btn" @click="deleteRecord()">Delete</a>
                </div>
            </div>
        </div>
        
    </div>
</div>
</body>
<script defer src="../js/npm/vue/dist/vue.js"></script> 
<script defer src="../js/axios.min.js"></script> 
<script defer src="../js/npm/sweetalert2@9.js"></script>
<script defer src="../js/admin/project_status.js"></script>
</html>
