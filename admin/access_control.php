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
<link rel="stylesheet" href="../css/vue-select.css" type="text/css">

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

    <style>
        body.cyan .mainContent {
            min-height: 130vh;
        }
        
        body.cyan .block div.box-content{
            border-bottom: 2px solid var(--cyan01);
        }

        body.cyan .block div.box-content:last-of-type{
            border-bottom: none;
        }

        body.cyan .block div.box-content li>div{
            text-align: left;
            font-size: 16px;
            width: 100%;
        }

        .vs__dropdown-toggle{
            border: 2px solid #18BAC7;
        }

        .vs__open-indicator {
            fill: #18BAC7;
        }
    </style>

</head>

<body class="cyan">

<div class="bodybox">
    <!-- header -->
	<header>header</header>
    <!-- header end -->
    <div id="app" class="mainContent" id="mainContent">
        <!-- tags js在 main.js -->
        <div class="tags">
            <a class="tag A" href="">Admin Section</a>
        </div>
        <!-- Blocks -->
        <div class="block A focus">
            <h6>Admin Section</h6>

            <div class="box-content" style="display:none;">
                <ul>
                    <li><b>Section Link</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess1"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                    <a class="btn" @click="cancel(1)">Cancel</a>
                    <a class="btn" @click="save(1)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Attendance (Query and Export)</b></li>
                    <br>
                    <li>
                        <div>
                            
                            <v-select v-model="payess2"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                    <a class="btn" @click="cancel(2)">Cancel</a>
                    <a class="btn" @click="save(2)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Leaves (Query and Export)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess3"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(3)">Cancel</a>
                    <a class="btn" @click="save(3)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Salary Recorder</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess4"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(4)">Cancel</a>
                    <a class="btn" @click="save(4)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Salary Management</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess5"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(5)">Cancel</a>
                    <a class="btn" @click="save(5)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Salary Slip Management</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess6"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(6)">Cancel</a>
                    <a class="btn" @click="save(6)">Save</a>
                </div>
            </div>
            
            <div class="box-content">
                <ul>
                    <li><b>Store Sales Recorder</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess7"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(7)">Cancel</a>
                    <a class="btn" @click="save(7)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Recorder of PO to Mr. Lai</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="payess8"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(8)">Cancel</a>
                    <a class="btn" @click="save(8)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 1)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access1"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(9)">Cancel</a>
                    <a class="btn" @click="save(9)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 2)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access2"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(10)">Cancel</a>
                    <a class="btn" @click="save(10)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 3)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access3"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(11)">Cancel</a>
                    <a class="btn" @click="save(11)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 4)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access4"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(12)">Cancel</a>
                    <a class="btn" @click="save(12)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 5)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access5"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(13)">Cancel</a>
                    <a class="btn" @click="save(13)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 6)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access6"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(14)">Cancel</a>
                    <a class="btn" @click="save(14)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Order for Taiwan (Role 7)</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="access7"
                                              :options="payees"
                                              attach
                                              chips
                                              label="payeeName"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(15)">Cancel</a>
                    <a class="btn" @click="save(15)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Knowledge Mgt</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="knowledge"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(16)">Cancel</a>
                    <a class="btn" @click="save(16)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Voting Topic Management Level 1</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="vote1"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(17)">Cancel</a>
                    <a class="btn" @click="save(17)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Voting Topic Management Level 2</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="vote2"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(18)">Cancel</a>
                    <a class="btn" @click="save(18)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Schedule Calendar Confirm</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="schedule_confirm"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(19)">Cancel</a>
                    <a class="btn" @click="save(19)">Save</a>
                </div>
            </div>

            <div class="box-content">
                <ul>
                    <li><b>Manager Halfday Planning</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="halfday"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(20)">Cancel</a>
                    <a class="btn" @click="save(20)">Save</a>
                </div>

            </div>

            <div class="box-content">
                <ul>
                    <li><b>Tag Management</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="tag_management"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(21)">Cancel</a>
                    <a class="btn" @click="save(21)">Save</a>
                </div>

            </div>

            <div class="box-content">
                <ul>
                    <li><b>SOA Subtotal Edit Function</b></li>
                    <br>
                    <li>
                        <div>
                        <v-select v-model="soa"
                                              :options="payees"
                                              attach
                                              chips
                                              label="Name"
                                              multiple></v-select>
                        </div>
                    </li>
                </ul>

                <div class="btnbox">
                <a class="btn" @click="cancel(22)">Cancel</a>
                    <a class="btn" @click="save(22)">Save</a>
                </div>

            </div>


        </div>
    </div>
</div>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script defer src="../js/axios.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script defer src="../js/admin/access_control.js"></script>
<script src="../js/vue-select.js"></script>
</html>