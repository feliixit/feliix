<?php include 'check.php';?>
<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="../images/favicon.ico" />
    <link rel="Bookmark" href="../images/favicon.ico" />
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="../images/iosicon.png" />

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
    <link rel="stylesheet" type="text/css" href="../css/default.css" />
    <link rel="stylesheet" type="text/css" href="../css/ui.css" />
    <link rel="stylesheet" type="text/css" href="../css/case.css" />
    <link rel="stylesheet" type="text/css" href="../css/mediaqueries.css" />
    <link rel="stylesheet" href="../css/vue-select.css" type="text/css">

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="../js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="../js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="../js/main.js" defer></script>

    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
    $(function() {
        $('header').load('include/header.php');
    })
    </script>

    <style>
    body.cyan .mainContent {
        min-height: 130vh;
    }

    body.cyan .block div.box-content {
        border-bottom: 2px solid var(--cyan01);
    }

    body.cyan .block div.box-content:last-of-type {
        border-bottom: none;
    }

    body.cyan .block div.box-content li>div {
        text-align: left;
        font-size: 16px;
        width: 100%;
    }

    .vs__dropdown-toggle {
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
                                <v-select v-model="payess1" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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

                                <v-select v-model="payess2" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="payess3" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="payess4" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="payess5" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="payess6" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="payess7" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                        <li><b>Recorder of PO from Office Team</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="payess8" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access1" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access2" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access3" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access4" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access5" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access6" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="access7" :options="payees" attach chips label="payeeName" multiple>
                                </v-select>
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
                                <v-select v-model="knowledge" :options="payees" attach chips label="Name" multiple>
                                </v-select>
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
                                <v-select v-model="vote1" :options="payees" attach chips label="Name" multiple>
                                </v-select>
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
                                <v-select v-model="vote2" :options="payees" attach chips label="Name" multiple>
                                </v-select>
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
                                <v-select v-model="schedule_confirm" :options="payees" attach chips label="Name"
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
                                <v-select v-model="halfday" :options="payees" attach chips label="Name" multiple>
                                </v-select>
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
                                <v-select v-model="tag_management" :options="payees" attach chips label="Name" multiple>
                                </v-select>
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
                                <v-select v-model="soa" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(22)">Cancel</a>
                        <a class="btn" @click="save(22)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Transmittal Scanned Copy</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="transmittal" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(23)">Cancel</a>
                        <a class="btn" @click="save(23)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Management of Employee Data Sheet: Edit and Reset</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="edit_emp" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(24)">Cancel</a>
                        <a class="btn" @click="save(24)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Management of Employee Basic Info: View and Edit and Reset</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="edit_basic" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(25)">Cancel</a>
                        <a class="btn" @click="save(25)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Coding System of Office Items Catalog</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="office_items" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(26)">Cancel</a>
                        <a class="btn" @click="save(26)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Office Items Approver</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="office_item_approve" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(27)">Cancel</a>
                        <a class="btn" @click="save(27)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Office Items Releaser</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="office_item_release" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(28)">Cancel</a>
                        <a class="btn" @click="save(28)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>User with Limited Access (Meeting Calendar and Product Catalog)</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="limited_access" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(29)">Cancel</a>
                        <a class="btn" @click="save(29)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Checker of Office Items Inventory Check</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="inventory_checker" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(30)">Cancel</a>
                        <a class="btn" @click="save(30)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Approver of Office Items Inventory Check</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="inventory_approver" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(31)">Cancel</a>
                        <a class="btn" @click="save(31)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Frozen of Office Items Releasing as well as Office Items Inventory Replenishment and Modification</b></li>
                        <br>
                        <li>
                            <div>
                                <select style="margin-bottom: 8px;" v-model="frozen_office">
                                    <option value="">No</option>
                                    <option value="Y">Yes</option>
                                </select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(32)">Cancel</a>
                        <a class="btn" @click="save(32)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Viewing and Duplicating Access of Office Furniture Quotation</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="quotation_control" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(33)">Cancel</a>
                        <a class="btn" @click="save(33)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>View Cost Price of Lighting Product</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="cost_lighting" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(34)">Cancel</a>
                        <a class="btn" @click="save(34)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>View Cost Price of System Furniture Product</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="cost_furniture" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(35)">Cancel</a>
                        <a class="btn" @click="save(35)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Create and Delete (Leadership Assessment)</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="leadership_assessment" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(36)">Cancel</a>
                        <a class="btn" @click="save(36)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Project02: Special Agreement</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="special_agreement" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(37)">Cancel</a>
                        <a class="btn" @click="save(37)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Access of user</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="for_user" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(38)">Cancel</a>
                        <a class="btn" @click="save(38)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Access of user profile</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="for_profile" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(39)">Cancel</a>
                        <a class="btn" @click="save(39)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Product Add and Edit</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="product_edit" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(40)">Cancel</a>
                        <a class="btn" @click="save(40)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Product Duplicate</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="product_duplicate" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(41)">Cancel</a>
                        <a class="btn" @click="save(41)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Product Delete</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="product_delete" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(42)">Cancel</a>
                        <a class="btn" @click="save(42)">Save</a>
                    </div>

                </div>

                <div class="box-content">
                    <ul>
                        <li><b>Admin of Inventory Modification</b></li>
                        <br>
                        <li>
                            <div>
                                <v-select v-model="inventory_modify" :options="payees" attach chips label="Name" multiple></v-select>
                            </div>
                        </li>
                    </ul>

                    <div class="btnbox">
                        <a class="btn" @click="cancel(43)">Cancel</a>
                        <a class="btn" @click="save(43)">Save</a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</body>
<script defer src="../js/npm/vue/dist/vue.js"></script>
<script defer src="../js/axios.min.js"></script>
<script defer src="../js/npm/sweetalert2@9.js"></script>
<script defer src="../js/admin/access_control.js"></script>
<script src="../js/vue-select.js"></script>

</html>