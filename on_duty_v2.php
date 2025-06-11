<?php include 'check.php';?>
<?php
    date_default_timezone_set('Asia/Taipei');
    $date = date("Y-m-d");
    $show0 = false;

    if($date < '2022-02-01')
        $show0 = true;
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

<script type="text/javascript" src="js/webcam.js"></script>
<script language="JavaScript">

function take_snapshot() {

    var real_width = document.getElementsByTagName("video")[0].srcObject.getVideoTracks()[0].getSettings().width;
	var real_height = document.getElementsByTagName("video")[0].srcObject.getVideoTracks()[0].getSettings().height;

	var scalex = real_width / 240;
	var scaley = real_height / 240;

	if (scalex <= 1 && scaley <= 1) {

		Webcam.set({
			dest_width: real_width,
			dest_height: real_height
		});

	} else {

		if (scalex >= scaley) {

			Webcam.set({
				dest_width: real_width / scalex,
				dest_height: real_height / scalex
			});

		} else {

			Webcam.set({
				dest_width: real_width / scaley,
				dest_height: real_height / scaley
			});

		}

    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            if(document.getElementById('photo_gps') !== null)
                document.getElementById('photo_gps').value = position.coords.latitude + ',' + position.coords.longitude;
        });
      }

      if(document.getElementById('photo_time') !== null)
            document.getElementById('photo_time').value = getToday() + ' ' + getTimeNow();

    Webcam.snap(function(data_uri) {
    document.getElementById('results').innerHTML = '<img id="base64image" src="'+data_uri+'"/>';
});
}


function ShowCam(){
Webcam.set({
            width: 240,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
Webcam.attach('#my_camera');
}

function uploadcomplete(event){
    document.getElementById("loading").innerHTML="";
    var image_return=event.target.responseText;
    var showup=document.getElementById("uploaded").src=image_return;
}



function getToday() {

  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
  var yyyy = today.getFullYear();

  return  yyyy + '/' + mm + '/' + dd;

  //setInterval(self.getToday, 1000 * 60)
}

function getTimeNow() {

  var today = new Date();
  var hh = String(today.getHours()).padStart(2, '0');
  var mm = String(today.getMinutes()).padStart(2, '0'); 
  var ss = String(today.getSeconds()).padStart(2, '0');

  return  hh + ':' + mm + ':' + ss;

  //setInterval(self.getTimeNow, 1000)
}


window.onload= ShowCam;
</script>

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

    input[type='date'] {
        background-color: transparent;
        padding: 8px;
        vertical-align: middle;
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
                        <dd><input type="date" v-model="today" id="duty_date" @change="change_date()"></dd>
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
                            <option value="M">Main Office</option>
                            <?php
                                if($show0 == 'true')
                                {
                            ?>       
                                <option value="A">Antel Office</option>
                            <?php
                                }
                            ?>
                                
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
                        
                        <dt>Location Photo</dt>
                        <!-- <dd v-if="showPhoto"><input type="file" id="file" ref="file" v-on:change="onChangeFileUpload()" accept="image/*" capture="camera"></dd> -->
                        <dd >
                     
                            <div id="Cam" class="container" style="display:flex; flex-direction: column; align-items: center;"><b>Webcam Preview</b>
                                <div id="my_camera"></div>
                                <form>
                                    <input type="button" value="Take Photo" onclick="take_snapshot()" style="border-radius: 0.38rem; border: 0.06rem solid rgb(112, 112, 112); font-size: 15px; margin: 0.38rem 0rem 0.48rem 0rem;">
                                </form>
                            </div>
                            <div class="container" id="Prev">
                                <div id="results"></div>
                            </div>
                            <div class="container" id="Saved">
                                <span id="loading"></span><img id="uploaded" src=""/>
                            </div>
                        </dd>

                        <dt>Remarks</dt>
                        <dd><textarea placeholder="" v-model="remark"></textarea></dd>
                     <!--   <hr>
                        <dt>Time In</dt>
                        <dd><input type="text" placeholder="" v-model="time" :readonly="true"></dd>  -->
                        <hr>
                        <dt v-if="showPhoto">Photo Taken Time</dt>
                        <dd v-if="showPhoto"><input type="text" id="photo_time" placeholder="" :readonly="true"></dd>
                        <dt v-if="showPhoto">Photo Taken GPS</dt>
                        <dd v-if="showPhoto"><input type="text" id="photo_gps" placeholder="" :readonly="true"></dd>
                        <p id="map-link" style="font-size: 20px; font-weight: 500;" v-if="showPhoto"></p>
                    </dl>
                    <div class="btnbox">
                        <a class="btn" @click="reset">Reset</a>
                        <a class="btn" @click="upload" :disabled="submit == true">Submit</a>
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
<script defer src="js/on_duty_v2.js"></script>

</html>
