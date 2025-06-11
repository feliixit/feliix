<?php
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$uid = (isset($_COOKIE['uid']) ?  $_COOKIE['uid'] : null);
if ($jwt === NULL || $jwt === '') {
    setcookie("userurl", $_SERVER['REQUEST_URI']);
    header('location:index');
}

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
include_once 'api/project03_is_creator.php';

use \Firebase\JWT\JWT;

try {
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    $user_id = $decoded->data->id;
    $username = $decoded->data->username;

    $position = $decoded->data->position;
    $department = $decoded->data->department;

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );

    if($decoded->data->limited_access == true)
                header( 'location:index' );

    $access6 = false;

    if (trim($department) == '') {
        if (trim(strtoupper($position)) == 'OWNER' || trim(strtoupper($position)) == 'MANAGING DIRECTOR' || trim(strtoupper($position)) == 'CHIEF ADVISOR') {
            $access6 = true;
        }
    }

    if ($username == "Glendon Wendell Co") {
        $access6 = true;
    }

    if ($access6 == false)
        header('location:index');
}
// if decode fails, it means jwt is invalid
catch (Exception $e) {

    header('location:index');
}

?>
<!DOCTYPE html>
<html>

<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover" />

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="Bookmark" href="images/favicon.ico" />
    <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/iosicon.png" />

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
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="stylesheet" type="text/css" href="css/ui.css" />
    <link rel="stylesheet" type="text/css" href="css/case.css" />
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css" />
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous" />

    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script src="js/moment.js"></script>

    <!-- Chart js載入 -->
    <script src="js/chart/chart.js"></script>

    <!-- import CSS -->
    <link rel="stylesheet" href="css/element-ui/theme-chalk/index.css">



    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function() {
            $('header').load('include/header.php');

        })
    </script>

    <style scoped>
        header .headerbox {

            background-color: #94BABB;

        }
    </style>

    <!-- CSS for current webpage -->
    <style type="text/css">
        body.fifth .mainContent>.tags a {
            background-color: #DFEAEA;
            border: 2px solid #94BABB;
            border-bottom: none;
        }

        body.fifth .mainContent>.tags a.focus {
            background-color: #94BABB;
        }

        body.fifth .mainContent>.block,
        body.fifth .mainContent>.block.focus {
            border: 2px solid #94BABB;
            border-bottom: none;
            margin-bottom: 0;
        }

        body.fifth .mainContent>.block h6 {
            color: #94BABB;
            border-bottom-color: #94BABB;
        }

        body.fifth .mainContent>.block .box-content {
            border-bottom: 2px solid #94BABB;
        }

        .chart_function {
            width: 80%;
            margin: 40px auto 0;
            display: flex;
            justify-content: center;
        }

        .chart_function input[type=month] {
            border: 2px solid #94BABB;
            padding: 5px;
            background-color: transparent;
            margin-right: 20px;
            height: 43px;

        }

        .chart_function select {
            border: 2px solid #94BABB;
            padding: 5px;
            background-color: transparent;
            height: 43px;
            background-image: url(images/ui/icon_form_select_arrow_grassgreen.svg);
        }

        .chart_function a.btn {
            background-color: #94BABB;
            margin-left: 60px;
            height: 43px;
        }
    </style>


</head>

<body class="fifth">

    <div class="bodybox" style="min-height: 150vh;">
        <!-- header -->
        <header>header</header>
        <!-- header end -->
        <div class="mainContent">
            <!-- tags js在 main.js -->
            <div class="tags">
                <a class="tag A" href="monthly_sales_report">Monthly Sales Report</a>
                <a class="tag B" href="monthly_cash_flow_report">Monthly Cash Flow Report</a>
                <a class="tag C" href="monthly_new_project_report">Monthly New Project Report</a>
                <a class="tag D focus">Chart</a>
                <a class="tag E" href="monthly_xxxxx">xxxxx</a>
            </div>
            <!-- Blocks -->
            <div class="block D focus">

                <h6>Chart: Sales v.s. Expense</h6>
                <div class="box-content">
                    <div style="width: 80%; margin: auto; min-width: 450px;">
                        <canvas id="chart1"></canvas>
                    </div>
                    <div class="chart_function">
                        <input type="month" id="c1_start_time" value="">
                        <input type="month" id="c1_end_time" value="">
                        <a class="btn" onclick="refreshData1();">Refresh</a>
                    </div>
                </div>


                <h6>Chart: Amount and Percentage of Sales Persons</h6>
                <div class="box-content">
                    <div style="width: 50%; margin: auto; min-width: 350px;">
                        <canvas id="chart2"></canvas>
                    </div>
                    <div class="chart_function">
                        <input type="month" id="c2_start_time" value="">
                        <input type="month" id="c2_end_time" value="">
                        <a class="btn" onclick="refreshData2();">Refresh</a>
                    </div>
                </div>


                <h6>Chart: Ongoing v.s. Disapproved Projects</h6>
                <div class="box-content">
                    <div style="width: 80%; margin: auto; min-width: 450px;">
                        <canvas id="chart3"></canvas>
                    </div>
                    <div class="chart_function">
                        <input type="month" id="c3_start_time" value="">
                        <input type="month" id="c3_end_time" value="">
                        <select id="c3_type">
                            <option value="1">Number of Projects</option>
                            <option value="2">Total Amount of Projects</option>
                            <option value="3">Average Amount of Project</option>
                        </select>

                        <a class="btn" onclick="refreshData3();">Refresh</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="app">
    </div>
</body>

<script>

var chart1;
var chart2;
var chart3;

window.addEventListener("load", () => loadEdit(), false);

async function loadEdit() {
    // do the await things here.
    await app.setChart1Data();
    
    var labels, colors, data1, data2, data3;

    //chart1
    //Query data from database and assign data to variables
    labels = app.chart1_title;
    data1 = app.chart1_data1;
    data2 = app.chart1_data2;
    data3 = app.chart1_data1;

    //Initialize and render chart1
    chart1 = new Chart(document.getElementById('chart1').getContext('2d'), {
        data: {
            labels: labels,
            datasets: [{
                    type: 'bar',
                    label: 'Sales Bar',
                    data: data1,
                    backgroundColor: 'rgba(36, 170, 232, 1.0)',
                    borderColor: 'rgba(36, 170, 232, 1.0)',
                    borderWidth: 1
                },
                {
                    type: 'bar',
                    label: 'Expense Bar',
                    data: data2,
                    backgroundColor: 'rgba(255, 177, 193, 0.2)',
                    borderColor: 'rgba(255, 177, 193, 1.0)',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Sales Line',
                    data: data3,
                    backgroundColor: 'rgba(36, 170, 232, 1.0)',
                    borderColor: 'rgba(36, 170, 232, 1.0)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


    //chart2
    //Query data from database and assign data to variables
    await app.setChart2Data();

    labels = app.chart2_title;
    data1 = app.chart2_data1;

    //background color for chart2
    colors = ['rgb(255, 99, 132)', 'rgb(255, 159, 64)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(54, 162, 235)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207)'];


    //Initialize and render chart2
    chart2 = new Chart(document.getElementById('chart2').getContext('2d'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{

                label: 'Sales',
                data: data1,
                backgroundColor: colors,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Sales Pie Chart'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {

                            if (context.chart.config.type == 'pie') {

                                var label = context.chart.data.labels[context.dataIndex] || '';

                                if (label) {
                                    label += ': ';
                                }

                                if (context.parsed !== null) {
                                    label += (context.parsed).toFixed(2).toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",") + ' ( ';

                                    var sum = 0.0;
                                    for (var i = 0; i < context.chart.data.datasets[0].data.length; i++) {

                                        if (context.chart.getDataVisibility(i)) {
                                            sum += context.chart.data.datasets[0].data[i];
                                        }
                                    }

                                    label += (context.parsed * 100 / sum).toFixed(2) + '% )';

                                }
                                return label;
                            }
                        }
                    }
                }
            }
        }
    });

    

    //chart3
    //Query data from database and assign data to variables

    await app.setChart3Data();

    labels = app.chart3_title;
    data1 = app.chart3_data1_count;
    data2 = app.chart3_data2_count;
    data3 = app.chart3_data3_count;


    //Initialize and render chart3
    chart3 = new Chart(document.getElementById('chart3').getContext('2d'), {
        data: {
            type: 'bar',
            labels: labels,
            datasets: [{
                    type: 'bar',
                    label: 'Finished Projects',
                    data: data1,
                    backgroundColor: 'rgba(15, 140, 220, 1.0)',
                    borderColor: 'rgba(15, 140, 220, 1.0)',
                    borderWidth: 1,
                    stack: 'Stack 0'
                },
                {
                    type: 'bar',
                    label: 'Ongoing Projects',
                    data: data2,
                    backgroundColor: 'rgba(153, 217, 234, 1.0)',
                    borderColor: 'rgba(153, 217, 234, 1.0)',
                    borderWidth: 1,
                    stack: 'Stack 0'
                },
                {
                    type: 'bar',
                    label: 'Disapproved Projects',
                    data: data3,
                    backgroundColor: 'rgba(255, 177, 193, 0.2)',
                    borderColor: 'rgba(255, 177, 193, 1.0)',
                    borderWidth: 1,
                    stack: 'Stack 1'
                }

            ]
        },
        options: {
            indexAxis: 'y',
            elements: {
                bar: {
                    borderWidth: 2,
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        },
    });

}

// document.addEventListener('DOMContentLoaded', function() {
    
// });


    async function refreshData1() {

        var start_time = document.getElementById("c1_start_time").value;
        var end_time = document.getElementById("c1_end_time").value;

        //Query data from database


        await app.setChart1Data(start_time, end_time);
    
        var labels, colors, data1, data2, data3;

        //chart1
        //Query data from database and assign data to variables
        labels = app.chart1_title;
        data1 = app.chart1_data1;
        data2 = app.chart1_data2;
        data3 = app.chart1_data1;


        chart1.data.labels = labels;
        chart1.data.datasets[0].data = data1;
        chart1.data.datasets[1].data = data2;
        chart1.data.datasets[2].data = data3;


        chart1.update();

    };

    async function refreshData2() {

        var start_time = document.getElementById("c2_start_time").value;
        var end_time = document.getElementById("c2_end_time").value;

        //Query data from database
        await app.setChart2Data(start_time, end_time);

        //Reassign new data into variables
        labels = app.chart2_title; //assign new labels
        data1 = app.chart2_data1; //assign new data

        chart2.data.labels = labels;
        chart2.data.datasets[0].data = data1;

        chart2.update();

    };

    async function refreshData3() {

        var start_time = document.getElementById("c3_start_time").value;
        var end_time = document.getElementById("c3_end_time").value;

        var type = document.getElementById("c3_type").value;

        //Query data from database
        await app.setChart3Data(start_time, end_time);

        //Reassign new data into variables
        labels = app.chart3_title; //assign new labels

        if(type == 2)
        {
            data1 = app.chart3_data1_amount; //assign new data
            data2 = app.chart3_data2_amount; //assign new data
            data3 = app.chart3_data3_amount; //assign new data
        }

        if(type == 1)
        {
            data1 = app.chart3_data1_count; //assign new data
            data2 = app.chart3_data2_count; //assign new data
            data3 = app.chart3_data3_count; //assign new data
        }

        if(type == 3)
        {
            data1 = app.chart3_data1_avg; //assign new data
            data2 = app.chart3_data2_avg; //assign new data
            data3 = app.chart3_data3_avg; //assign new data
        }

        chart3.data.labels = labels;
        chart3.data.datasets[0].data = data1;
        chart3.data.datasets[1].data = data2;
        chart3.data.datasets[2].data = data3;

        chart3.update();

    };
</script>



<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>
<script defer src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>
<script src="js/chart.js"></script>

</html>