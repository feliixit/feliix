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
    $access10 = false;
    $access11 = false;
    $access12 = false;
    $access13 = false;

    $limited_access = false;

    $access_office_item = true;
    

$pic_url = "man6.jpg";

try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $username = $decoded->data->username;
        $position = $decoded->data->position;
        $department = $decoded->data->department;

        $user_id = $decoded->data->id;

        $leave_level = $decoded->data->leave_level;

        $valid_date = new DateTime('2022-11-29');
        $pre_valid_date = new DateTime('2022-12-01');
        $all_valid_date = new DateTime('2023-01-01');
        $today = new DateTime();
            
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

        $access10 = false;
        //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
        //    $access2 = true;
        $query = "SELECT * FROM access_control WHERE payess8 LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access10 = true;
        }

        $access11 = false;
        //if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 3 || $user_id == 41)
        //    $access2 = true;
        $query = "SELECT * FROM access_control WHERE (vote1 LIKE '%" . $username . "%' or vote2 LIKE '%" . $username . "%') ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access11 = true;
        }

        $office_item_approver_releaser = false;
        $query = "SELECT * FROM access_control WHERE (office_item_approve LIKE '%" . $username . "%' or office_item_release LIKE '%" . $username . "%') ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $office_item_approver_releaser = true;
        }

        $office_inventory_approver_releaser = false;
        $query = "SELECT * FROM access_control WHERE (inventory_checker LIKE '%" . $username . "%' or inventory_approver LIKE '%" . $username . "%') ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $office_inventory_approver_releaser = true;
        }

        $for_user = false;
        
        $query = "SELECT * FROM access_control WHERE for_user LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $for_user = true;
        }

        $for_profile = false;

        $query = "SELECT * FROM access_control WHERE for_profile LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $for_profile = true;
        }

        $access3 = false;
        if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41 || $user_id == 3 || $user_id == 9 || $user_id == 87 || $user_id == 99 || $user_id == 190 || $user_id == 143 || $user_id == 146 || $user_id == 154 || $user_id == 198)
            $access3 = true;

        // 5. 針對 Expense Application Report 的內容，權限控管如下
        if($user_id == 1 || $user_id == 2 || $user_id == 3 || $user_id == 6 || $user_id == 41 || $user_id == 88 || $user_id == 89 || $user_id == 95 || $user_id == 146 || $user_id == 179 || $user_id == 190 || $user_id == 198)
            $access5 = true;

        
        // QOUTE AND PAYMENT Management
        if(trim(strtoupper($department)) == 'SALES')
        {
            if(trim(strtoupper($position)) == 'CUSTOMER VALUE COORDINATOR'
            || trim(strtoupper($position)) == 'JR. ACCOUNT EXECUTIVE'
            || trim(strtoupper($position)) == 'CUSTOMER VALUE SUPERVISOR'
            || trim(strtoupper($position)) == 'SENIOR CUSTOMER VALUE SUPERVISOR'
            || trim(strtoupper($position)) == 'ASSISTANT CUSTOMER VALUE DIRECTOR'
            || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'LIGHTING')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR')
            {
                $access6 = true;
            }
        }

        if(trim(strtoupper($department)) == 'OFFICE')
        {
            if(trim(strtoupper($position)) == 'ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR')
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

        if(trim(strtoupper($department)) == 'ENGINEERING')
        {
            if(trim(strtoupper($position)) == "ENGINEERING MANAGER")
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

        // SALES DASHBOARD
        $dashboard = false;
        if(trim(strtoupper($department)) == 'SALES')
        {

            $dashboard = true;
        }
       
        if($username == "Kristel Tan" || $username == "Kuan" || $username == "Dennis Lin" || $username == "Marie Kayla Patricia Dequina" || $username == "Gina Donato" || $username == "Aiza Eisma" || $username == "Johmar Maximo" || $username == "Stephanie De dios")
        {
            $dashboard = true;
        }

        if($user_id == 1 || $user_id == 99 || $user_id == 41 || $user_id == 9 || $user_id == 190 || $user_id == 198 || $user_id = 153)
            $access6 = true;

        $pic_url = $decoded->data->pic_url;

        if($pic_url == "")
            $pic_url = "avatar.svg";


        //HR & Admin Section Access
        // access12 is for Employee Data Sheet
        // access13 is for Employee Basic Info
        if(trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR' || trim(strtoupper($position)) == 'VALUE DELIVERY MANAGER'
        || trim(strtoupper($position)) == 'CUSTOMER VALUE DIRECTOR' || trim(strtoupper($position)) == 'LIGHTING VALUE CREATION DIRECTOR' || trim(strtoupper($position)) == 'OFFICE SPACE VALUE CREATION DIRECTOR'
        || trim(strtoupper($position)) == 'ENGINEERING MANAGER' || trim(strtoupper($position)) == 'OPERATIONS MANAGER')
        {
                $access12 = true;
                $access13 = true;

        }

        $query = "SELECT * FROM access_control WHERE edit_basic LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $access13 = true;
        }

        $query = "SELECT * FROM access_control WHERE limited_access LIKE '%" . $username . "%' ";
        $stmt = $db->prepare( $query );
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $limited_access = true;
        }

        // $query = "SELECT * FROM access_control WHERE office_items LIKE '%" . $username . "%' ";
        // $stmt = $db->prepare( $query );
        // $stmt->execute();
        // while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //     $access_office_item = true;
        // }


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
            if($user_id != 94 && !$limited_access)
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
                <a class="list" href="../quotation_mgt">Quotation Creation and Management</a>
                <a class="list" href="../quotation_pageless_mgt">Quotation Creation and Management (Pageless)</a>
                <a class="list" href="../quotation_eng_pageless_mgt">Quotation Creation and Management (Engineering)</a>
                <a class="list" href="../work_schedule_eng_mgt">Engineering Work Schedule (Engineering)</a>
                <a class="list" href="../approval_form_mgt">Approval Form Management</a>
                <a class="list" href="../approval_form_pageless_mgt">Approval Form Management (Pageless)</a>
                <a class="list" href="../soa_form_mgt">SOA Management (Page/Pageless)</a>
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
                <a class="list" href="../inquiry_mgt">Inquiry Management</a>
                <a class="list" href="../order_mgt">Order Management</a>
                <a class="list" href="../price_comparison_mgt">Price Comparison Management</a>
                <a class="list" href="../schedule_calendar">Schedule Calendar</a>
                <a class="list" href="../task_calendar">Task Due Date Calendar</a>
                <a class="list" href="../meeting_calendar">Meeting Calendar</a>
                <a class="list" href="../individual_calendar">Personal Datebook Calendar</a>
                <a class="list" href="../signature_codebook">Signature Codebook</a>
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
                <a class="list" href="../task_management_SVC">Engineering Department</a>
            </li>

            <li class="sec03">
                <a class="uni">Inventory<br>Management</a>
                <a class="list" href="../transmittal_mgt">Transmittal Management</a>
                <a class="list" href="../old_inventory_register">Registry and Tracking Code for Old Inventory</a>
                <a class="list" href="../inventory_modify_mgt">Inventory Modification Management</a>
                <a class="list" href="../tracking_item_query">Query of Tracking Code</a>
                <?=($office_inventory_approver_releaser == true) ? '<a class="list" href="../office_item_inventory_check_mgt">Office Items Inventory Management</a>' : '' ?>
            </li>

            <li class="sec02">
                <a class="uni">Performance<br>Evaluation</a>
                <a class="list" href="../performance_dashboard">Performance Evaluation</a>
                <a class="list" href="../leadership_assessment">Leadership Assessment</a>
            </li>
            <?php 
                if($access1 == true || $access2 == true || $access3 == true || $access4 == true || $access8 == true || $access9 == true || $access10 == true)
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
                <?=($access10 == true) ? '<a class="list" href="../po_to_mrlai_recorder">Recorder of PO to Mr. Lai</a>' : '' ?>
                <?=($office_item_approver_releaser == true) ? '<a class="list" href="../office_item_reviewing">Office Item Application Review</a>' : '' ?>
            </li>
            <?php 
                }
            ?>

            <?php
                if($access12 == true)
                {
            ?>
            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni">HR & Admin<br>Section</a>
                <?php
                    if($access12 == true)
                    {
                ?>
                <a class="list" href="../employee_data_sheet">Employee Data Sheet</a>
                <?php
                    }
                ?>

                <?php
                    if($access13 == true)
                    {
                ?>
                <a class="list" href="../employee_basic_info">Employee Basic Info</a>
                <?php
                    }
                ?>
            </li>
            <?php
                }
            ?>

<?php 
                if($access5 == true || $access7 == true || $dashboard == true)
                {
            ?>
            <li class="red01" style="border: 3px solid var(--red01);">
                <a class="uni">Report<br>Section</a>
                <?php 
                    if($access5 == true || $access7 == true)
                    {
                ?>
                <a class="list" href="../expense_application_report">Expense Application Tracker</a>
                <?php 
                    }
                ?>
                <?php 
                    if($access7 == true)
                    {
                ?>
                <a class="list" href="../monthly_sales_report">Monthly Sales Report</a>
                <?php 
                    }
                ?>
                <?php 
                    if($dashboard == true)
                    {
                ?>
                <a class="list" href="../sales_dashboard">Sales Dashboard</a>
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
                <a class="list" href="../apply_for_leave_v2">Leave</a>
                
                <a class="list">Query/Ammend</a>
            </li>
            <li class="sec02">
                <a class="uni">Payment Request/Claim<br>Office Item Request<br> and Salary Slip</a>
                <a class="list" href="../apply_for_expense">Expense Apply/Liquidate</a>
                <a class="list" href="../apply_for_office_item">Office Item Request</a>
                <a class="list" href="../salary_slip">Salary Slip</a>
            </li>
            <li class="gray02">
                <a class="uni">Product<br>Database</a>
                <a class="list" href="../product_catalog_code">Product Catalog</a>
                <a class="list" href="../tag_mgt">Tag Management</a>
                <a class="list" href="../spec_sheet_mgt">Specification Sheet Management</a>
                <a class="list" href="../frequently_used_options">Attribute’s Frequently Used Options</a>
                <a class="list" href="../electrical_materials_catalog">Electrical Materials Catalog</a>
                <a class="list" href="../electrical_tools_catalog">Electrical Tools and Equipments Catalog</a>
                <?php
                    if($access_office_item == true)
                    {
                ?>
                <a class="list" href="../office_items_catalog">Office Items Catalog</a>
                <?php
                    }
                ?>
            </li>
            <li class="sec02">
                <a class="uni">Let's Vote</a>
                <a class="list" href="../voting_system">Voting System</a>
                <?=($access11 == true) ? '<a class="list" href="../voting_topic_mgt">Voting Topic Management</a>' : '' ?>
            </li>
            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni">Knowledge<br>Library</a>
                <a class="list" href="../knowledge_display">Knowledge List</a>
                <a class="list" href="../knowledge_mgt">Knowledge Management</a>
            </li>

            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni">Personal<br>Section</a>
                <a class="list" href="../individual_data_sheet">Employee Data Sheet</a>
            </li>
            <?php 
            if($for_user == true || $for_profile == true)
            {
            ?>
            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni">System<br>Section</a>
                <?=($for_user == true) ? '<a class="list" href="user">User</a>' : '' ?>
                <?=($for_profile == true) ? '<a class="list" href="user_profile">User Profile</a>' : '' ?>
            </li>
            <?php 
                }
            ?>
        </ul>
        <?php
            }
            else if($limited_access)
            {
        ?>
        <ul class="menu">
            <li class="sec03">
                <a class="uni">Project<br>Management</a>
                <a class="list" href="../meeting_calendar">Meeting Calendar</a>
            </li>
        </ul>
        <ul class="menu">
                <li class="gray02">
                    <a class="uni">Product<br>Database</a>
                    <a class="list" href="../product_catalog_code">Product Catalog</a>
                    <a class="list" href="../tag_mgt">Tag Management</a>
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
    <div class="footer"><a class="logout" onclick="logout();">Log out</a></div>
    
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
        setCookie('jwt', '', -1); 
        setCookie('uid', '', -1); 

        window.location.href = "../index";
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

</script>