<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header( 'location:../index' );
}

include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';
include_once '../api/config/database.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    if($decoded->data->limited_access == true)
    header( 'location:../index' );

    $database = new Database();
    $db = $database->getConnection();

    $for_user = false;

    $query = "SELECT * FROM access_control WHERE `for_user` LIKE '%" . $username . "%' ";
    $stmt = $db->prepare( $query );
    $stmt->execute();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $for_user = true;
    }

    if ($for_user == false)
        header( 'location:../index' );
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header( 'location:../index' );
}

?>
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
            <a class="tag A focus">User</a>
            <a class="tag B" href="department">Department</a>
            <a class="tag C" href="position">Position</a>
            <a class="tag D" href="leave_flow">Leave Flow</a>
            <a class="tag E" href="expense_flow">Expense Flow</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>User Management</h6>
            <div class="box-content">
                <div class="box-content"  v-if="!isEditing">
                    <ul>
                        <li><b>Employee Name</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="username" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li><b>Email</b></li>
                        <li style="padding-bottom:10px;"><input type="email" v-model="email" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="255" style="width:100%"></li>
                        <li><b>Password</b></li>
                        <li style="padding-bottom:10px;"><input type="password" v-model="password" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="255" style="width:100%"></li>
                        <li><b>Comfirm Password</b></li>
                        <li style="padding-bottom:10px;"><input type="password" v-model="password1" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="255" style="width:100%"></li>
                        <li><b>Department</b> </li>
                        <li style="padding-bottom:10px;">
                            <select v-model="apartment_id">
                            <option v-for="item in departments" :value="item.id" :key="item.department">
                                {{ item.department }}
                            </option>
                            </select>
                        </li>
                        <li><b>Position</b></li>
                        <li style="padding-bottom:10px;">
                            <select v-model="title_id">
                            <option v-for="item in positons" :value="item.id" :key="item.title">
                                {{ item.title }}
                            </option>
                            </select>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input  type="checkbox" name="status" id="status" :true-value="1" v-model:checked="status" @change="updateStatus">
                            <label for="status">Enabled</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input  type="checkbox" name="is_admin" id="is_admin" :true-value="1" v-model:checked="is_admin" @change="updateIsAdmin">
                            <label for="is_admin">Is Admin</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="need_punch" id="need_punch" :true-value="1" v-model:checked="need_punch" @change="updateNeedPunch">
                            <label for="need_punch">Need Punch</label>
                        </li>
                        <li><b>Yearly Vacation</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="annual_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li><b>Yearly Sick Leave</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="sick_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li><b>Manager Leave</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="manager_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="is_manager" id="is_manager" :true-value="1" v-model:checked="is_manager" @change="updateIsManager">
                            <label for="is_manager">Is Manager</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="test_manager" id="test_manager" :true-value="1" v-model:checked="test_manager" @change="updateTestManager">
                            <label for="test_manager">Project Mgt and Expense Recorder Access</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="head_of_department" id="head_of_department" :true-value="1" v-model:checked="head_of_department" @change="updateHeadOfDepartment">
                            <label for="head_of_department">Leave w/o Approval</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="is_viewer" id="is_viewer" :true-value="1" v-model:checked="is_viewer" @change="updateIsViewer">
                            <label for="is_viewer">Is Viewer</label>
                        </li>

                        <li><b>Leave Level</b></li>
                        <li style="padding-bottom: 10px;">
                            <select v-model="leave_level">

                                <option value="A">A: Regular Employee</option>

                                <option value="B">B: Assistant Manager</option>

                                <option value="C">C: Manager</option>

                            </select>
                        </li>
                        <li><b>Yearly Credit: Service Incentive Leave (SIL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;" v-model="sil">
                        </li>

                        <li><b>Yearly Credit: Vacation Leave / Sick Leave (VL/SL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;"  v-model="vl_sl"
                                                                 placeholder="">
                        </li>

                        <li><b>Yearly Credit: Vacation Leave (VL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;"  v-model="vl"
                                                                 placeholder="">
                        </li>

                        <li><b>Yearly Credit: Sick Leave (SL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"  v-model="sl"
                                                                 onblur="this.placeholder = ''" style="width: 100%;">
                        </li>
                        
                        <li><b>Yearly Credit: Manager Halfday Planning</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"  v-model="halfday"
                                                                 onblur="this.placeholder = ''" style="width: 100%;">
                        </li>

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
                            <b>Employee Name</b>
                        </li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="record.username" required onfocus="this.placeholder = ''"  maxlength="255" onblur="this.placeholder = ''" style="width: 100%"></li>
                        
                        <li>
                            <b>Email</b>
                        </li>
                        <li style="padding-bottom:10px;"><input type="email" v-model="record.email" required onfocus="this.placeholder = ''" onblur="this.placeholder = ''" maxlength="255" style="width: 100%"></li>
                       
                        <li>
                            <b>Department</b> 
                        </li>
                        <li style="padding-bottom:10px;">
                            <select v-model="ed_apartment_id">
                            <option v-for="item in departments" :value="item.id" :key="item.department">
                                {{ item.department }}
                            </option>
                            </select>
                        </li>

                        <li>
                            <b>Position</b>
                        </li>
                        <li style="padding-bottom:10px;">
                            <select v-model="record.title_id">
                            <option v-for="item in positons" :value="item.id" :key="item.title">
                                {{ item.title }}
                            </option>
                            </select>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input  type="checkbox" name="status" id="status" :true-value="1" v-model:checked="record.status" @change="updateStatus">
                            <label for="status">Enabled</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input  type="checkbox" name="is_admin" id="is_admin" :true-value="1" v-model:checked="record.is_admin" @change="updateIsAdmin">
                            <label for="is_admin">Is Admin</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="need_punch" id="need_punch" :true-value="1" v-model:checked="record.need_punch" @change="updateNeedPunch">
                            <label for="need_punch">Need Punch</label>
                        </li>
                        <li><b>Yearly Vacation</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="record.annual_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li><b>Yearly Sick Leave</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="record.sick_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li><b>Manager Leave</b></li>
                        <li style="padding-bottom:10px;"><input type="text" v-model="record.manager_leave" required onfocus="this.placeholder = ''"  maxlength="5" onblur="this.placeholder = ''" style="width:100%" ></li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="is_manager" id="is_manager" :true-value="1" v-model:checked="record.is_manager" @change="updateIsManager">
                            <label for="is_manager">Is Manager</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="test_manager" id="test_manager" :true-value="1" v-model:checked="record.test_manager" @change="updateTestManager">
                            <label for="test_manager">Project Mgt and Expense Recorder Access</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="head_of_department" id="head_of_department" :true-value="1" v-model:checked="record.head_of_department" @change="updateHeadOfDepartment">
                            <label for="head_of_department">Leave w/o Approval</label>
                        </li>
                        <li style="padding-bottom:10px;">
                            <input type="checkbox" name="is_viewer" id="is_viewer" :true-value="1" v-model:checked="record.is_viewer" @change="updateIsViewer">
                            <label for="is_viewer">Is Viewer</label>
                        </li>

                        
                        <li><b>Leave Level</b></li>
                        <li style="padding-bottom: 10px;">
                            <select v-model="record.leave_level">

                                <option value="A">A: Regular Employee</option>

                                <option value="B">B: Assistant Manager</option>

                                <option value="C">C: Manager</option>

                            </select>
                        </li>
                        <li><b>Yearly Credit: Service Incentive Leave (SIL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;" v-model="record.sil">
                        </li>

                        <li><b>Yearly Credit: Vacation Leave / Sick Leave (VL/SL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;"  v-model="record.vl_sl"
                                                                 placeholder="">
                        </li>

                        <li><b>Yearly Credit: Vacation Leave (VL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"
                                                                 onblur="this.placeholder = ''" style="width: 100%;"  v-model="record.vl"
                                                                 placeholder="">
                        </li>

                        <li><b>Yearly Credit: Sick Leave (SL)</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"  v-model="record.sl"
                                                                 onblur="this.placeholder = ''" style="width: 100%;">
                        </li>

                        <li><b>Yearly Credit: Manager Halfday Planning</b></li>
                        <li style="padding-bottom: 10px;"><input type="text" required="required"
                                                                 onfocus="this.placeholder = ''" maxlength="5"  v-model="record.halfday"
                                                                 onblur="this.placeholder = ''" style="width: 100%;">
                        </li>
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
                        <li style="font-size:10px;">Employee Name</li>
                        <li style="font-size:10px;">Email</li>
                        <li style="font-size:10px;">Department</li>
                        <li style="font-size:10px;">Position</li>
                        <li style="font-size:10px;">Status</li>
                    </ul>
                    <ul v-for='(record, index) in displayedPosts' :key="index">
                        <li><input type="checkbox" name="record_id" class="alone" :value="record.index" :true-value="1" v-model:checked="record.is_checked"></li>
                        <li style="font-size:10px;">{{record.username}}</li>
                        <li style="font-size:10px;">{{record.email}}</li>
                        <li style="font-size:10px;">{{record.department}}</li>
                        <li style="font-size:10px;">{{record.title}}</li>
                        <li style="font-size:10px;">{{ (record.status == 1) ? "Y" : "N" }}/{{ (record.is_admin == '1') ? "Y" : "N" }}/{{ (record.need_punch == 1) ? "Y" : "N" }}/{{ (record.is_manager == 1) ? "Y" : "N" }}/{{ (record.head_of_department == 1) ? "Y" : "N" }}</li>
                        
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
<script defer src="../js/admin/user.js"></script>
</html>
