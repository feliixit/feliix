<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once '../../api/config/core.php';
include_once '../../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../api/libs/php-jwt-master/src/JWT.php';
include_once '../../api/config/database.php';
use \Firebase\JWT\JWT;

$access1 = false;
$access2 = false;
$access3 = false;
$access4 = false;
$access5 = false;
$access6 = false;
$access7 = false;
$access8 = false;
$access9 = false;

$pic_url = "man6.jpg";

try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $username = $decoded->data->username;
        $position = $decoded->data->position;
        $department = $decoded->data->department;

        $user_id = $decoded->data->id;
            
        // 1. 針對 Verify and Review的內容，只有 1st Approver 和 2nd Approver有權限可以進入和看到
        
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM leave_flow WHERE uid = " . $user_id;
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access1 = true;
        }

        $query = "SELECT * FROM expense_flow WHERE uid = " . $user_id;
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access4 = true;
        }

        // Glendon Wendell Co and Kuan
        if($user_id == 3 || $user_id == 41)
            $access1 = true;

        // 2. 針對 Query and Export的內容，只有 Glendon Wendell Co 和 Kristel Tan 和Thalassa Wren Benzon 和 Dennis Lin有權限可以進入和看到
        // 改從 access control
        $access2 = false;
        //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
        //    $access2 = true;
        $query = "SELECT * FROM access_control WHERE payess2 LIKE '%" . $username . "%' OR payess3 LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access2 = true;
        }

        $access8 = false;
        //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
        //    $access2 = true;
        $query = "SELECT * FROM access_control WHERE salary LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access8 = true;
        }

        $access9 = false;
        //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
        //    $access2 = true;
        $query = "SELECT * FROM access_control WHERE payess7 LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access9 = true;
        }

        $access3 = false;
        if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41 || $user_id == 3 || $user_id == 9 || $user_id == 87 || $user_id == 99)
            $access3 = true;

        // 5. 針對 Reporting Section的內容，只有 Kristel Tan 和Thalassa Wren Benzon 和 Dennis Lin有權限可以進入和看到
        if($user_id == 1 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 4 || $user_id == 9 || $user_id == 41 || $user_id == 99)
            $access5 = true;

        
        // QOUTE AND PAYMENT Management
        if(trim(strtoupper($department)) == 'SALES')
        {
            if(trim(strtoupper($position)) == 'JR. ACCOUNT EXECUTIVE' 
            || trim(strtoupper($position)) == 'ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'SR. ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'ASSISTANT SALES MANAGER'
            || trim(strtoupper($position)) == 'SALES MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'LIGHTING')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING MANAGER' || trim(strtoupper($position)) == 'LIGHTING MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'OFFICE')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT OFFICE SYSTEMS MANAGER' || trim(strtoupper($position)) == 'OFFICE SYSTEMS MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'DESIGN')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT BRAND MANAGER' || trim(strtoupper($position)) == 'BRAND MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'SERVICE')
        {
            if(trim(strtoupper($position)) == "ENGINERING MANAGER")
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'ADMIN')
        {
            if(trim(strtoupper($position)) == 'OPERATIONS MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'TW')
        {
            if(trim(strtoupper($position)) == 'SUPPLY CHAIN MANAGER')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == '')
        {
            if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR')
            {
                $access6 = true;
                $access7 = true;
            }
        }

        if($username == "Glendon Wendell Co")
        {
            $access7 = true;
        }

        if($user_id == 1 || $user_id == 99 || $user_id == 41 )
            $access6 = true;

        $pic_url = $decoded->data->pic_url;

        if($pic_url == "")
            $pic_url = "avatar.svg";
        //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
        //    header( 'location:index.php' );
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        header( 'location:index' );
    }

?>

<!-- 主選單 -->
<!-- header -->
<div class="headerbox">
    <a class="before-micons menu"></a>
    <a class="logo"></a>
</div>
<!-- header end -->
<!-- 選單 -->
<nav>
    <div class="headerbox"><b class="logo"></b></div>
    <!--  menu list  -->
    <div class="middle">
        <ul class="info">
            <!-- 大頭照可由此處style修改 -->
            <li class="photo" style="background-image: url(../images/man/<?=$pic_url ?>)"></li>
            <li class="name"><?= isset($username) ? $username : "" ?> <br /> <b style="font-size: 26px;"><?= isset($position) ? $position : "" ?></b></li>
        </ul>
        <?php
            if($user_id != 94)
            {
        ?>
        <ul class="menu">
            <li class="sec01">
                <a class="uni" href="../attendance">Office<br>Attendance</a>
            </li>
            <li class="sec03">
                <a class="uni">Project<br>Management</a>
                <a class="list" href="../project01">Project Management</a>
                <a class="list" href="../project01_disapproved">Project Management (Disapproved)</a>
                <a class="list" href="../project01_sls">Project Management (Sales)</a>
                <?php 
                if($access6 == true)
                {
                ?>
                    <a class="list" href="../project_grouping">Project Grouping</a>
                    <a class="list" href="../quotation_and_payment_mgt?fc=&fs=&ft=&fal=&fau=&fpl=&fpu=&fk=&of1=&ofd1=&of2=&ofd2=&pg=1">Quotation and Payment Mgt.</a>
                    <a class="list" href="../quotation_and_payment_mgt_v2?fc=&fs=&ft=&fal=&fau=&fpl=&fpu=&fk=&of1=&ofd1=&of2=&ofd2=&pg=1">Quotation and Payment Mgt. Ver.2</a>
                <?php 
                }
                ?>
                <a class="list" href="../schedule_calendar">Schedule Calendar</a>
                <a class="list" href="../task_calendar">Task Due Date Calendar</a>
                <a class="list" href="../meeting_calendar">Meeting Calendar</a>
            </li>
            <!--
            <li class="sec02">
                <a class="uni">Process<br>Management</a>
            </li>
-->
            <li class="sec03">
                <a class="uni">Task<br>Management</a>
                <a class="list" href="../task_management_AD">Admin Department</a>
                <a class="list" href="../task_management_DS">Design Department</a>
                <a class="list" href="../task_management_LT">Lighting Department</a>
                <a class="list" href="../task_management_OS">Office Systems Department</a>
                <a class="list" href="../task_management_SLS">Sales Department</a>
                <a class="list" href="../task_management_SVC">Service Department</a>
            </li>

            <li class="sec02">
                <a class="uni">Performance<br>Evaluation</a>
                <a class="list" href="../performance_dashboard">Performance Evaluation</a>
            </li>
            <?php 
                if($access1 == true || $access2 == true || $access3 == true || $access4 == true || $access8 == true || $access9 == true)
                {
            ?>
            <li class="gray05" style="border: 3px solid var(--black01);">
                <a class="uni">Admin<br>Section</a>
                <?=($access1 == true) ? '<a class="list" href="../ammend">Verify and Review</a>' : '' ?>
                <?=($access2 == true) ? '<a class="list" href="../query_export">Query and Export</a>' : '' ?>
                <?=($access4 == true) ? '<a class="list" href="../expense_checking">Expense Review</a>' : '' ?>
                <?=($access3 == true) ? '<a class="list" href="../expense_recorder">Expense Recorder</a>' : '' ?>
                <?=($access8 == true) ? '<a class="list" href="../salary_recorder">Salary Recorder</a>' : '' ?>
                <?=($access9 == true) ? '<a class="list" href="../store_sales_recorder">Store Sales Recorder</a>' : '' ?>
            </li>
            <?php 
                }
            ?>
            <?php 
                if($access5 == true || $access7 == true)
                {
            ?>
            <li class="red01" style="border: 3px solid var(--red01);">
                <a class="uni">Report<br>Section</a>
                <a class="list" href="../expense_application_report">Expense Application Tracker</a>
                <?php 
                    if($access7 == true)
                    {
                ?>
                <a class="list" href="../monthly_sales_report">Monthly Sales Report</a>
                <?php 
                    }
                ?>
            </li>
            <?php 
                }
            ?>
        </ul>
        <ul class="menu">
            <li class="pri01a">
                <a class="uni">Employee<br>Attendance</a>
                <a class="list" href="../on_duty">Punch In/Out</a>
                <a class="list" href="../apply_for_leave">Leave</a>
                <a class="list">Query/Ammend</a>
            </li>
            <li class="sec02">
                <a class="uni">Payment Request/Claim<br> and Salary Slip</a>
                <a class="list" href="../apply_for_expense">Expense Apply/Liquidate</a>
                <a class="list" href="../salary_slip">Salary Slip</a>
            </li>
            <li class="gray02">
                <a class="uni">Profile<br>Section</a>
            </li>
            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni" href="user">System<br>Section</a>
            </li>
        </ul>
        <?php
            }
            else
            {
        ?>
        <ul class="menu">
            <li class="sec03 focus">
                <a class="uni">Project<br>Management</a>
                <a class="list" href="../schedule_calendar">Schedule Calendar</a>
            </li>
        </ul>
        <?php
            }
        ?>
    </div>
    <!--  menu list  -->
    <div class="footer"><a class="logout" href="../index" onclick="logout();">Log out</a></div>
    
</nav>
<!-- 選單 end	-->
<!-- 主選單end -->
<script>
    $(function(){
        toggleme($('header a.menu'),$('body'),'MenuOn');
        toggleme($('header nav .headerbox'),$('body'),'MenuOn');
        $('header nav .middle ul.menu li').click(function(){
            $(this).toggleClass('focus');
        })
    });

    function logout() {
        var res = document.cookie;
            var multiple = res.split(";");
            for(var i = 0; i < multiple.length; i++) {
               var key = multiple[i].split("=");
               document.cookie = key[0]+" =; expires = Thu, 01 Jan 1970 00:00:00 UTC";
            }
        
        localStorage.token = "";
    }
</script>