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

<body class="cyan">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="user">User</a>
            <a class="tag F" href="user_profile">User Profile</a>
            <a class="tag B" href="department">Department</a>
            <a class="tag C focus">Postion</a>
            <a class="tag D" href="leave_flow">Leave Flow</a>
            <a class="tag E" href="expense_flow">Expense Flow</a>
        </div>
        <!-- Blocks -->
        <div class="block C focus">
            <h6>Position Management
                <div class="function" style="padding-top:10px;">
                    Choose Apartment: <select v-model="department_id">
                        <option v-for="item in departments" :value="item.id" :key="item.department">
                            {{ item.department }}
                        </option>
                    </select>
                </div>
            </h6>
            
            <div class="box-content">
                <div class="box-content"  v-if="!isEditing">
                    <ul>
                        <li><b>Position Name</b></li>
                        <li><input type="text" v-model="title" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width:100%"></li>
                    </ul>
                    
                    <div style="padding-top:10px;">
                        <div>
                            <button type="button" @click="cancelReceiveRecord($event)"><p>CLEAR</p></button>
                            <button type="button" @click="createReceiveRecord()"><p>ADD</p></button>
                        </div>
                    </div>
                </div>

                <div class="box-content" v-else>
                    <ul>
                        <li><b>Position Name</b></li>
                        <li><div class="function"><input type="text" v-model="record.title" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width:100%"></li>
                    </ul>
                    
                    <div style="padding-top:10px;">
                        <div>
                            <button type="button" @click="cancelReceiveRecord($event)"><p>CANCEL</p></button>
                            <button type="button" @click="editReceiveRecord($event)"><p>SAVE</p></button>
                        </div>
                    </div>
                </div>

                    

                <div class="tablebox">
                    <ul class="head">
                    <li><i class="micons">view_list</i></li>
                        <li>Position Name</li>
                     
                        
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.index" :true-value="1" v-model:checked="record.is_checked"></li>
                        <li>{{record.title}}</li>
                        
                        
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
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> 
<script defer src="../js/axios.min.js"></script> 
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="../js/admin/position.js"></script>
</html>
