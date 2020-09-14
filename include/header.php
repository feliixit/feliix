<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ( !isset( $jwt ) ) {
  header( 'location:index' );
}

include_once '../api/config/core.php';
include_once '../api/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../api/libs/php-jwt-master/src/ExpiredException.php';
include_once '../api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../api/libs/php-jwt-master/src/JWT.php';
include_once '../api/config/database.php';
use \Firebase\JWT\JWT;

    $access1 = false;
    $access2 = false;

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

        // 2. 針對 Query and Export的內容，只有 Glendon Wendell Co 和 Kristel Tan 和Thalassa Wren Benzon 和 Dennis Lin有權限可以進入和看到
        
        if($user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41)
            $access2 = true;

        // 可以存取Expense Recorder的人員名單如下：Dennis Lin(2), Glendon Wendell Co(4), Kristel Tan(6), Kuan(3), Mary Jude Jeng Articulo(9), Thalassa Wren Benzon(41)
        if($user_id == 1 || $user_id == 4 || $user_id == 6 || $user_id == 2 || $user_id == 41 || $user_id == 3 || $user_id == 9)
            $access3 = true;

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
            <li class="photo" style="background-image: url(../images/iosicon.png)"></li>
            <li class="name"><?= isset($username) ? $username : "" ?> <br /> <b style="font-size: 26px;"><?= isset($position) ? $position : "" ?></b></li>
        </ul>
        <ul class="menu">
            <li class="sec01">
                <a class="uni" href="attendance">Office<br>Attendance</a>
            </li>
            <li class="sec02">
                <a class="uni">Process<br>Management</a>
            </li>
            <?php 
                if($access1 == true || $access2 == true || $access3 == true)
                {
            ?>
            <li class="gray05">
                <a class="uni">Admin<br>Section</a>
                <?=($access1 == true) ? '<a class="list" href="ammend">Verify and Review</a>' : '' ?>
                <?=($access2 == true) ? '<a class="list" href="query_export">Query and Export</a>' : '' ?>
                <?=($access3 == true) ? '<a class="list" href="expense_recorder">Expense Recorder</a>' : '' ?>
            </li>
            <?php 
                }
            ?>
            <li class="cyan01" style="border: 3px solid var(--cyan01);">
                <a class="uni" href="admin/user">System<br>Section</a>
            </li>
        </ul>
        <ul class="menu">
            <li class="pri01a">
                <a class="uni">Employee<br>Attendance</a>
                <a class="list" href="on_duty">Punch In/Out</a>
                <a class="list" href="apply_for_leave">Leave</a>
                <a class="list" href="">Query/Ammend</a>
            </li>
            <li class="sec03">
                <a class="uni">Payment<br>Request/Claim</a>
            </li>
            <li class="gray02">
                <a class="uni">Profile<br>Section</a>
            </li>
            
        </ul>
    </div>
    <!--  menu list  -->
    <div class="footer"><a class="logout" href="index" onclick="logout();">Log out</a></div>
    
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