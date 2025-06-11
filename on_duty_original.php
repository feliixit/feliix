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
<title>ON DUTY</title>
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

<!-- css -->
<link rel="stylesheet" type="text/css" href="css/default.css"/>
<link rel="stylesheet" type="text/css" href="css/ui.css"/>
<link rel="stylesheet" type="text/css" href="css/case.css"/>
<link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>

<!-- jQuery和js載入 -->
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js" ></script>
<script type="text/javascript" src="js/rm/realmediaScript.js"></script>
<!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
<script>
$(function(){
    $('header').load('include/header.php');
})
</script>

</head>

<style>
    body {
        background-color: #F0F0F0;
    }
    #app {
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
    #video {
        background-color: #000000;
    }
    #canvas {
        display: none;
    }
    li {
        display: inline;
        padding: 5px;
    }
</style>

<body class="primary">
 	
<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id='app' class="mainContent">
        <!-- Blocks -->
        <div class="block A focus">
            <h6>On-Duty</h6>
            <div class="box-content">
                <!-- 表單樣式 -->
                <div class="title">
                </div>
                <div class="formbox">
                    <dl>
                        <dt>Employee Name</dt>
                        <dd><input type="text" placeholder="" v-model="name" :readonly="true"></dd>
                        <dt>Date</dt>
                        <dd><input type="text" placeholder="" v-model="today" ></dd>
                        <dt>Type</dt>
                        <dd>
                            <select name="" id="" v-model="type">
                                <option value="A">On Duty</option>
                                <option value="B">Off Duty</option>
                                
                            </select>
                        </dd>
                        <dt>Location</dt>
                        <dd>
                            <select name="" id="" v-model="location">
                                <option value="A">Antel Office</option>
                                <option value="T">Taiwan Office</option>
                                <option value="B">Shangri-La Store</option>
                                <option value="C">Caloocan Warehouse</option>
                                <option value="D">Installation</option>
                                <option value="E">Client Meeting</option>
                                <option value="F">Others</option>
                            </select>
                        </dd>

                        
                        <dt v-if="showExtra">Further Explanation</dt>
                        <dd v-if="showExtra"><input type="text" placeholder="" v-model="explanation"></dd>
                        
                        <dt v-if="showPhoto">Location Photo</dt>
                        <dd v-if="showPhoto"><input type="file" id="file" ref="file" v-on:change="onChangeFileUpload()" accept="image/*" capture="camera"></dd>

                        <dt>Remarks</dt>
                        <dd><textarea placeholder="" v-model="remark"></textarea></dd>
                     <!--   <hr>
                        <dt>Time In</dt>
                        <dd><input type="text" placeholder="" v-model="time" :readonly="true"></dd>  -->
                        <hr>
                        <dt v-if="showPhoto">Photo Taken Time</dt>
                        <dd v-if="showPhoto"><input type="text" placeholder="" v-model="photo_time" :readonly="true"></dd>
                        <dt v-if="showPhoto">Photo Taken GPS</dt>
                        <dd v-if="showPhoto"><input type="text" placeholder="" v-model="photo_gps" :readonly="true"></dd>
                        <p id="map-link" style="font-size: 20px; font-weight: 500;" v-if="showPhoto"></p>
                    </dl>
                    <div class="btnbox">
                        <a class="btn" @click="reset">Reset</a>
                        <a class="btn" @click="upload" :disabled="submit">Submit</a>
                    </div>
                </div>
                <!-- 表單樣式 -->
            </div>
        </div>



    </div>
</div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/npm/exif-js.js"></script>
<script defer src="js/npm/sweetalert2@9.js"></script>
<script defer src="js/on_duty_original.js"></script>
</html>
