<?php include 'check.php';?>
<!DOCTYPE html>
<html>
<head>
    <!-- 共用資料 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, min-width=640, user-scalable=0, viewport-fit=cover"/>

    <!-- favicon.ico iOS icon 152x152px -->
    <link rel="shortcut icon" href="images/favicon.ico"/>
    <link rel="Bookmark" href="images/favicon.ico"/>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="images/iosicon.png"/>

    <!-- SEO -->
    <title>Product Display</title>
    <meta name="keywords" content="FELIIX">
    <meta name="Description" content="FELIIX">
    <meta name="robots" content="all"/>
    <meta name="author" content="FELIIX"/>

    <!-- Open Graph protocol -->
    <meta property="og:site_name" content="FELIIX"/>
    <!--<meta property="og:url" content="分享網址" />-->
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="FELIIX"/>
    <!--<meta property="og:image" content="分享圖片(1200×628)" />-->
    <!-- Google Analytics -->

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/case.css"/>
    <link rel="stylesheet" type="text/css" href="css/mediaqueries.css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css"
          href="css/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="css/fontawesome/v5.7.0/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">


    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="js/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js"></script>
    <script type="text/javascript" src="js/tagsinput.js"></script>
    <script src="js/bootstrap-select.js"></script>


    <!-- 這個script之後寫成aspx時，改用include方式載入header.htm，然後這個就可以刪掉了 -->
    <script>
        $(function () {
            $('header').load('include/header.php');

            dialogshow($('.list_function .new_function a.filter'), $('.list_function .dialog.A'));
            dialogshow($('.list_function .new_function a.sort'), $('.list_function .dialog.B'));

            $('.tablebox').click(function () {
                $('.list_function .dialog').removeClass('show');
            })

        })

    </script>

    <style>

        body.gray header > .headerbox {
            background-color: #707071;
        }

        a, a:link, a:visited, a:active, a:hover, area {
            text-decoration: none;
            cursor: pointer;
        }

        body.gray header nav a, body.gray header nav a:link {
            color: #000;
        }

        body.gray header nav a:hover {
            color: #333;
        }

        body.gray header nav {
            font-family: 'M PLUS 1p', Arial, Helvetica, 'LiHei Pro', "微軟正黑體", 'Microsoft JhengHei', "新細明體", sans-serif;
        }

        body.gray header nav ul.info {
            margin-bottom: 0;
        }

        body.gray header nav ul.info b {
            font-weight: bold;
        }

        body.gray select {
            background-image: url(../images/ui/icon_form_select_arrow_gray.svg);
        }

        .NTD_price {
            display: block;
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 130px;
        }

        .bodybox .mask {
            position: fixed;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 1;
            display: none;
        }

        .upper_section {
            margin: 20px 15vw 0;
            border: 2px solid rgb(225, 225, 225);
            display: flex;
        }

        .imagebox {
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .imagebox .selected_image {
            padding: 20px;
            text-align: center;
            width: 300px;
            height: 300px;
        }

        .imagebox .selected_image img {
            object-fit: contain;
            width: 100%;
            height: 100%;
        }

        .imagebox .image_list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 0 10px 20px 10px;
        }

        .imagebox .image_list img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin: 5px 10px;
            cursor: pointer;
            border: 2px solid #ced4da;
        }

        .imagebox .image_list img:hover {
            border-color: #F0502F;
        }

        .infobox {
            width: 50%;
        }

        .infobox .basic_info {
            border-bottom: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            padding: 30px 20px 5px;
        }

        .infobox .basic_info div.last_order_history button {
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .infobox .basic_info div.last_order_history span {
            font-size: 16px;
            font-weight: 500;
            color: red;
        }

        .infobox .basic_info div.last_order_history span a {
            color: blue;
        }

        .infobox .basic_info span.phasedout{
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
        }

        .infobox .basic_info span.phasedout1{
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 3px;
            border-radius: 10px;
            cursor: pointer;
        }

        .infobox .basic_info div.tags {
            margin-bottom: 0.5rem;
        }

        .infobox .basic_info div.tags span {
            background-color: #5bc0de;
            color: #fff;
            font-size: 14px;
            display: inline-block;
            font-weight: 600;
            border-radius: 5px;
            padding: 0 7px;
            margin: 0 3px;
        }

        .infobox .basic_info div.tags span:first-of-type {
            margin-left: 0;
        }

        .infobox .basic_info div.tags span:last-of-type {
            margin-right: 0;
        }

        .infobox .price_stock {
            margin-bottom: 0;
            margin-left: 20px;
            padding: 15px 20px;
        }

        .infobox .price_stock > li {
            color: rgb(83, 132, 155);
            font-size: 18px;
            font-weight: 600;
            padding: 8px 0;
        }

        .infobox .price_stock > li span:nth-of-type(1) {
            font-size: 22px;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .price_stock > li span:nth-of-type(2) {
            font-size: 16px;
            font-weight: 400;
            color: #6C757D;
            display: inline-block;
            margin-left: 10px;
        }

        .infobox .variants {
            border-top: 2px solid rgb(225, 225, 225);
            margin-left: 20px;
            margin-top: 1rem;
            padding: 0 20px;
        }

        .infobox .variants li {
            color: #212529;
            font-size: 16px;
            margin-left: 15px;
            margin-bottom: 3px;
        }

        .infobox .variants li:nth-of-type(odd) {
            margin-bottom: 15px;
        }

        .infobox .variants li:nth-of-type(1) {
            font-weight: 700;
            margin-left: 0;
            margin-bottom: 5px;
        }

        .infobox .variants .dropdown-menu li {
            margin-left: 0;
        }

        .infobox .variants .btn-light {
            background-color: #fff;
            border: 1px solid #ced4da;
            outline: none;
        }

        .infobox .variants .btn-light:focus {
            outline: none !important;
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
        }

        .middle_section {
            margin: 0 15vw;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
        }

        div.upper_section.product_set {
            background: rgba(255,255,0,0.1);
        }

        div.upper_section.product_set .infobox .basic_info {
            border-bottom: none;
        }

        div.upper_section.product_set .infobox .product_set_desc {
            font-size: 16px;
            margin-left: 22px;
            padding: 10px 20px 15px;
            border-top: 2px solid rgb(225,225,225);
        }

        div.upper_section.product_set .infobox .product_set_desc span {
            font-weight: 500;
        }

        .middle_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .middle_section table {
            margin: 5px 20px;
            width: calc(100% - 40px);
        }

        .middle_section tbody tr:nth-of-type(n+2) {
            border-top: 1px solid rgb(225, 225, 225);
        }

        .middle_section tbody tr td:nth-of-type(odd) {
            color: #B3B3B3;
            padding: 10px;
            width: 20%;
        }

        .middle_section tbody tr td:nth-of-type(even) {
            width: 30%;
        }

        .middle_section span.phasedout2{
            font-size: 16px;
            font-weight: 500;
            background-color: red;
            color: white;
            display: inline-block;
            margin-bottom: 3px;
            padding: 0 7px 2px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .phasedout2:hover{
            cursor: pointer;
        }

        .lower_section {
            margin: 0 15vw;
            border: 2px solid rgb(225, 225, 225);
            border-top: none;
            text-align: left;
        }

        .lower_section h5 {
            background-color: #E0E0E0;
            text-align: center;
            padding: 5px 0 8px;
            margin-bottom: 0;
        }

        .lower_section p {
            margin: 15px 20px;
        }

        .lower_section .desc_imgbox {
            width: 100%;
            padding: 0 15px;
            margin: 10px 0 20px;
        }

        .lower_section .desc_imgbox img {
            width: calc(50% - 8px);
            margin: 5px 0;
        }

        .lower_section .desc_imgbox img:nth-of-type(odd) {
            margin-right: 10px;
        }

        .lower_section .desc_imgbox img:nth-of-type(odd) {
            margin-right: 10px;
        }

        .lower_section p.supporting_attachment {
            font-weight: 500;
        }

        .lower_section p.supporting_attachment span a.attch {
            color: var(--fth05);
            transition: .3s;
            margin: 0 15px 0 0;
            font-size: 15px;
        }

        .row.custom {
            margin: 5px 0 0 0;
        }

        .col.custom {
            width: 24%;
            padding-left: 5px;
            padding-right: 5px;
            text-align: center;
        }

        .col.custom > img {
            height: 150px;
            width: 150px;
            object-fit: contain;
        }

        .col.custom > div > a {
            text-decoration: none;
            color: blue;
            cursor: pointer;
            font-size: 16px;
            padding: 5px 10px;
        }

        button.btn_switch, button.btn_switch_sales {
            position: fixed;
            right: 10px;
            top: 10px;
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 700;
            background-color: rgba(7, 220, 237, 0.8);
            z-index: 999;
        }

        button.btn_switch_sales {
            right: 70px;
            background-color: rgba(255, 0, 0, 0.5);
        }

        .carousel-control-next, .carousel-control-prev {
            opacity: 0.7;
            top: 35%;
            width: 4%;
        }

        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5L4.25 4l2.5-2.5L5.25 0z'/%3e%3c/svg%3e") !important;
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23e0e0e0' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5L3.75 4l-2.5 2.5L2.75 8l4-4-4-4z'/%3e%3c/svg%3e") !important;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
            }

            .mainContent {
                padding: 0;
                background-color: #FFF !important;
                zoom: 85%;
                margin: 1px 0px 0px 7px;
                overflow-y: hidden;
            }

            .upper_section {
                margin: 0 15vw 0;
            }

            .noPrint {
                display: none;
            }

            .change_page{
                page-break-after: always;
            }
        }

        @page {
            size: A4 portrait;
            margin: 20px 0 0;
        }

    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <!-- header -->
    <header class="noPrint">header</header>
    <!-- header end -->

    <button @click="toggle_price()" class="btn_switch noPrint" v-show="show_ntd === true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="bi bi-toggles"><path d="M4.5 9a3.5 3.5 0 1 0 0 7h7a3.5 3.5 0 1 0 0-7h-7zm7 6a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm-7-14a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm2.45 0A3.49 3.49 0 0 1 8 3.5 3.49 3.49 0 0 1 6.95 6h4.55a2.5 2.5 0 0 0 0-5H6.95zM4.5 0h7a3.5 3.5 0 1 1 0 7h-7a3.5 3.5 0 1 1 0-7z"></path></svg></button>
    <button @click="toggle_price_sales()" class="btn_switch_sales noPrint"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="bi bi-toggles"><path d="M4.5 9a3.5 3.5 0 1 0 0 7h7a3.5 3.5 0 1 0 0-7h-7zm7 6a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm-7-14a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm2.45 0A3.49 3.49 0 0 1 8 3.5 3.49 3.49 0 0 1 6.95 6h4.55a2.5 2.5 0 0 0 0-5H6.95zM4.5 0h7a3.5 3.5 0 1 1 0 7h-7a3.5 3.5 0 1 1 0-7z"></path></svg></button>

    <div class="mainContent">

        <!-- 如果載入的產品為 Product Set 子類別的產品，則需要使用下面的結構來顯示 Product Set 產品的基本資訊 -->
        <div class="upper_section product_set noPrint" v-if="sub_category == '10020000'">

            <div class="infobox">
                <div class="basic_info">
                    <h3>ID: {{ pid }}</h3>
                    <h3 style="word-break: break-all;">{{code}}</h3>
                    <h6>{{ category}} >> {{ sub_category_name}}</h6>
                    <div class="tags">
                        <span v-for="(it, index) in tags">{{ it }}</span>
                    </div>
                </div>
            </div>

            <div class="infobox">
                <ul class="price_stock">

                    <li id="print_srp" :class="[print_option.srp == 'true' ? '' : 'noPrint']"  :style="[show_srp == true ? {} : {'display':'none'}]">
                        Standard Retail Price: <span>{{price}}</span>
                    </li>

                    <li id="print_qp" :class="[print_option.qp == 'true' ? '' : 'noPrint']">
                        Quoted Price: <span>{{quoted_price}}</span>
                    </li>

                </ul>

                <ul class="supporting_attachment">
                                            <li></li>
                                            <li>
                                                <span v-if="product_ics.length > 0">IES File</span>
                                                <span v-if="product_skp.length > 0">SketchUp File</span>
                                                <span v-if="product_manual.length > 0">Supporting File</span>
                                                <span v-if="is_replacement_product.length > 0">Replacement Product</span>
                                            </li>
                                        </ul>

                <!-- 如果這個 Product Set 的 Description 是空值，則整個 <div class="product_set_desc"> 都不用被建立出來 -->
                <div class="product_set_desc">
                    <span>Description: </span> {{ description }}
                </div>

                <div class="btnbox">
                        <button class="btn btn-secondary" @click="print_option_page()">Export Setting</button>
                        <button class="btn btn-info" @click="print_page()">Export</button>
                </div>


            </div>

        </div>


        <!-- 如果載入的產品為 Product Set 子類別的產品，則需要使用原本整個 product_display_code 的結構，來一個一個顯示其中的 Product 1、Product 2、Product 3 -->
        <template v-if="sub_category == '10020000'" v-for="(set, set_index) in product_set">
            <div class="upper_section">

                <div class="imagebox">
                    <div class="selected_image">
                        <img :src="set.url == '' ? null : set.url">
                    </div>
                    <div class="image_list">
                        <img v-if="set.url1" :src="set.url1" @click="change_url_set(set, 1)"/>
                        <img v-if="set.url2" :src="set.url2" @click="change_url_set(set, 2)"/>
                        <img v-if="set.url3" :src="set.url3" @click="change_url_set(set, 3)"/>
                        <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                    </div>

                </div>


                <div class="infobox">
                    <div class="basic_info">

                        <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                        <div class="last_order_history noPrint" v-if="set.is_last_order != ''">

                            <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                            <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                            <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                            <button @click="last_order_info(set.is_last_order)" v-if="set.last_have_spec">Last Order History</button>
                            <span v-if="set.last_order_name != ''">Last Ordered: {{ set.last_order_at }} at <a :href="set.last_order_url">{{ set.last_order_name }}</a></span>
                        </div>

                        <!-- 網頁載入時，調整成需要根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span>結構要顯示哪一個 -->
                        <!-- 但當使用者選擇到某一個停產的子規格組合時，下方的 <span> 結構就會變成要顯示第一個<span>結構；若使用者切換到另一個沒有停產的子規格組合，則沒有任何<span>結構需要顯示出來 -->
                        <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，下方的 <span> 結構要顯示哪一個，則又回到第一個註解的判斷方式 -->
                        <span class="phasedout" v-if="set.out == 'Y' && set.out_cnt == 0">Phased Out</span>
                        <span class="phasedout1" v-if="set.out_cnt == 1" @click="PhaseOutAlert_set(set.phased_out_text)">1 variant is phased out</span>
                        <span class="phasedout1" v-if="set.out_cnt > 1" @click="PhaseOutAlert_set(set.phased_out_text)">{{ set.out_cnt }} variants are phased out</span>

                        <h3 :class="[print_option.pid == 'true' ? '' : 'noPrint']">ID: {{ set.pid }}</h3>
                        <h3 style="word-break: break-all;">{{set.code}}</h3>
                        <h6 :class="[print_option.brand == 'true' ? '' : 'noPrint']">{{set.brand}}</h6>
                        <h6>{{ set.category}} >> {{ set.sub_category_name}}</h6>
                        <div class="tags">
                            <span v-for="(it, index) in set.tags">{{ it }}</span>
                        </div>
                    </div>

                    <ul class="price_stock">

                        <li class="NTD_price" v-if="show_ntd == true && toggle == true">
                            Cost Price: <span>{{ set.price_ntd }}</span><span>{{ set.str_price_ntd_change }}</span>
                        </li>

                        <li :class="[print_option.srp == 'true' ? '' : 'noPrint']">
                            Standard Retail Price: <span>{{set.price}}</span><span>{{ set.str_price_change }}</span>
                        </li>

                        <li :class="[print_option.qp == 'true' ? '' : 'noPrint']">
                            Quoted Price: <span>{{set.quoted_price}}</span><span>{{ set.str_quoted_price_change }}</span>
                        </li>

                    </ul>

                    <ul class="variants" v-if="set.variation1_value.length > 0">
                        <li>
                            Variants
                        </li>
                        <li v-if="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                            {{ set.variation1 !== 'custom' ? set.variation1 + ': ' : set.variation1_custom + ': '}} <template v-for="(item, index) in set.variation1_value">{{ (index + 1 !== set.variation1_value.length) ? item + ', ' : item}} </template>
                        </li>
                        <li v-show="set.variation1_value[0] !== '' && set.variation1_value[0] !== undefined">
                            <select class="form-control" v-model="set.v1" @change="change_v_set(set)">
                                <option value=""></option>
                                <option v-for="(item, index) in set.variation1_value" :value="item" :key="item">{{item}}
                                </option>
                            </select>
                        </li>
                        <li v-if="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                            {{ set.variation2 !== 'custom' ? set.variation2 + ': ' : set.variation2_custom + ': ' }} <template v-for="(item, index) in set.variation2_value">{{ (index + 1 !== set.variation2_value.length) ? item + ', ' : item}} </template>
                        </li>
                        <li v-show="set.variation2_value[0] !== '' && set.variation2_value[0] !== undefined">
                            <select class="form-control" v-model="set.v2" @change="change_v_set(set)">
                                <option value=""></option>
                                <option v-for="(item, index) in set.variation2_value" :value="item" :key="item">{{item}}
                                </option>
                            </select>
                        </li>
                        <li v-if="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                            {{ set.variation3 !== 'custom' ? set.variation3 + ': ' : set.variation3_custom + ': ' }} <template v-for="(item, index) in set.variation3_value">{{ (index + 1 !== set.variation3_value.length) ? item + ', ' : item}} </template>
                        </li>
                        <li v-show="set.variation3_value[0] !== '' && set.variation3_value[0] !== undefined">
                            <select class="form-control" v-model="set.v3" @change="change_v_set(set)">
                                <option value=""></option>
                                <option v-for="(item, index) in set.variation3_value" :value="item" :key="item">{{item}}
                                </option>
                            </select>
                        </li>

                        <li v-if="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                            {{ set.variation4 !== 'custom' ? set.variation4 + ': ' : set.variation4_custom + ': ' }} <template v-for="(item, index) in set.variation4_value">{{ (index + 1 !== set.variation4_value.length) ? item + ', ' : item}} </template>
                        </li>
                        <li v-show="set.variation4_value[0] !== '' && set.variation4_value[0] !== undefined">
                            <select class="form-control" v-model="set.v4" @change="change_v_set(set)">
                                <option value=""></option>
                                <option v-for="(item, index) in set.variation4_value" :value="item" :key="item">{{item}}
                                </option>
                            </select>
                        </li>

                        <template v-for="(item, index) in set.accessory_infomation" v-if="show_accessory">
                            <li>{{ item.category }}</li>
                            <li>
                                <select class="selectpicker" data-width="100%" :id="set.id + 'tag'+index">
                                    <option :data-thumbnail="set.detail.url" v-for="(detail, index) in item.detail[0]">
                                        {{detail.code}}
                                    </option>
                                </select>
                            </li>
                        </template>

                    </ul>

                    <div class="btnbox noPrint">
                        <button class="btn btn-info" @click="goto_sheet_set(set)" >Spec. Sheet</button>
                    </div>

                </div>

                </div>


                <div class="middle_section" v-if="set.specification.length > 0">
                <h5>Specification</h5>

                <table>
                    <tbody>
                    <template v-for="(item, index) in set.specification">
                        <tr>
                            <td>
                                {{item.k1}}
                            </td>
                            <td>
                                {{item.v1}}
                            </td>
                            <td>
                                {{item.k2}}
                            </td>
                            <td> {{item.v2}}</td>
                        </tr>
                    </template>

                    </tbody>

                </table>

                </div>

                <div class="middle_section" v-if="set.related_product.length > 0">
                <h5>Related Products</h5>

                <div id="carouselExampleControls" class="carousel slide">

                    <div class="carousel-inner">

                        <div v-for='(g, groupIndex) in set.groupedItems'
                            :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                            <div class="row custom">
                                <div class="col custom" v-for='(it, index) in g'>
                                    <img :src="baseURL + it.photo1" :alt="'No Product Picture'">
                                    <div>
                                        <a :href="'product_display_code?id=' + it.id">
                                            {{ it.code }}
                                        </a>
                                    </div>
                                    <div>
                                        <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                        <span class="phasedout2" v-if="it.out == 'Y' && it.phased_out_cnt == 0">Phased Out</span>
                                        <span class="phasedout2" v-if="it.phased_out_cnt == 1" @click="RelatedPhaseOutAlert(it.phased_out_text)">1 variant is phased out</span>
                                        <span class="phasedout2" v-if="it.phased_out_cnt > 1" @click="RelatedPhaseOutAlert(it.phased_out_text)">{{ it.phased_out_cnt }} variants are phased out</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                </div>

                <div class="middle_section" v-if="set.replacement_product.length > 0">
                <h5>Replacement Product</h5>

                <div id="carouselExampleControls_replacement" class="carousel slide">

                    <div class="carousel-inner">

                        <div v-for='(g, groupIndex) in set.groupedItems_replacement'
                            :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                            <div class="row custom">
                                <div class="col custom" v-for='(it, index) in g'>
                                    <img :src="baseURL + it.photo1" :alt="'No Product Picture'">
                                    <div>
                                        <a :href="'product_display_code?id=' + it.id">
                                            {{ it.code }}
                                        </a>
                                    </div>
                                    <div>
                                        <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                        <span class="phasedout2" v-if="it.out == 'Y' && it.phased_out_cnt == 0">Phased Out</span>
                                        <span class="phasedout2" v-if="it.phased_out_cnt == 1" @click="RelatedPhaseOutAlert(it.phased_out_text)">1 variant is phased out</span>
                                        <span class="phasedout2" v-if="it.phased_out_cnt > 1" @click="RelatedPhaseOutAlert(it.phased_out_text)">{{ it.phased_out_cnt }} variants are phased out</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                </div>


                <div class="lower_section" v-if="(set.notes != null && set.notes != '') || set.description != '' || set.product_ics.length > 0 || set.product_skp.length > 0 || set.product_manual.length > 0">
                    <h5>Description</h5>
                    <p style="white-space: break-spaces;">{{ set.description }}</p>

                    <p v-if="set.notes != null && set.notes != ''">Notes: {{ set.notes }}</p>

                    <!-- 如果當前產品是 Lighting 產品，而且該產品有上傳 IES 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
                    <p class="supporting_attachment" v-if="set.product_ics.length > 0">IES File:
                        <span v-for="(item, index) in set.product_ics">
                            <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                        </span>
                    </p>

                    <!-- 如果當前產品是 Office 產品，而且該產品有上傳 SketchUp 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
                    <p class="supporting_attachment" v-if="set.product_skp.length > 0">SketchUp File:
                        <span v-for="(item, index) in set.product_skp">
                            <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                        </span>
                    </p>

                    <!-- 無論當前產品是 Lighting 或 Office 產品，只要該產品有上傳 Supporting File 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
                    <p class="supporting_attachment" v-if="set.product_manual.length > 0">Supporting File:
                        <span v-for="(item, index) in set.product_manual">
                            <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                        </span>
                    </p>


                    <!--
                    <div class="desc_imgbox">
                        <img src="images/realwork.png">
                        <img src="images/realwork.png">
                        <img src="images/wash_hands.png">
                        <img src="images/realwork.png">
                    </div>
                    -->
                </div>

                <div class="change_page"></div>

        </template>

        <!-- 如果載入的產品為 單一產品，則還是使用原本整個 product_display_code 的結構，來顯示當前這一個 單一產品 -->

        <div class="upper_section" v-if="sub_category != '10020000'">

            <div class="imagebox">
                <div class="selected_image">
                    <img :src="url">
                </div>
                <div class="image_list">
                    <img v-if="url1" :src="url1" @click="change_url(url1)"/>
                    <img v-if="url2" :src="url2" @click="change_url(url2)"/>
                    <img v-if="url3" :src="url3" @click="change_url(url3)"/>
                    <!-- <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)"> -->
                </div>

            </div>


            <div class="infobox">
                <div class="basic_info">

                    <!-- 網頁載入時，if「這個產品的主產品在 product_category 資料表 last_order 欄位有值」或者「它的任何一個子規格在 product 資料表 last_order 欄位有值」，就需要顯示下面的 <div class="last_order_history"> -->
                    <div class="last_order_history noPrint"  v-if="is_last_order != ''">

                    <!-- 在網頁載入時 或 當使用者還沒選擇任何一個子規格組合時，只會顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                    <!-- 當使用者選擇了一個子規格組合時(也就是每個維度選項都選擇了)，只會顯示下方 <span> 結構來列出該子規格最後訂購日期和相關訂單，但是不會顯示下方的 <button> 結構 -->
                    <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，則會只顯示下方的 <button> 結構，但是不會顯示下方 <span> 結構 -->
                    <button @click="last_order_info(is_last_order)" v-if="last_have_spec">Last Order History</button>
                    <span v-if="last_order_url != ''">Last Ordered: {{ last_order_at }} at <a :href="last_order_url">{{ last_order_name }}</a></span>
                    </div>

                    <!-- 網頁載入時，調整成需要根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span>結構要顯示哪一個 -->
                    <!-- 但當使用者選擇到某一個停產的子規格組合時，下方的 <span> 結構就會變成要顯示第一個<span>結構；若使用者切換到另一個沒有停產的子規格組合，則沒有任何<span>結構需要顯示出來 -->
                    <!-- 但當使用者本來選擇了某一個子規格組合，後來變成沒有選擇任何子規格時，下方的 <span> 結構要顯示哪一個，則又回到第一個註解的判斷方式 -->
                    <span class="phasedout" v-if="out == 'Y' && out_cnt == 0">Phased Out</span>
                    <span class="phasedout1" v-if="out_cnt == 1" @click="PhaseOutAlert()">1 variant is phased out</span>
                    <span class="phasedout1" v-if="out_cnt > 1" @click="PhaseOutAlert()">{{ out_cnt }} variants are phased out</span>

                    <h3 id="print_id" :class="[print_option.pid == 'true' ? '' : 'noPrint']">ID: {{ pid }}</h3>
                    <h3 style="word-break: break-all;">{{code}}</h3>
                    <h6 id="print_brand" :class="[print_option.brand == 'true' ? '' : 'noPrint']">{{brand}}</h6>
                    <h6>{{ category}} >> {{ sub_category_name}}</h6>
                    <div class="tags">
                        <span v-for="(it, index) in tags">{{ it }}</span>
                    </div>
                </div>

                <ul class="price_stock">

                    <li class="NTD_price" v-if="show_ntd == true && toggle == true">
                        Cost Price: <span>{{ price_ntd }}</span><span>{{ str_price_ntd_change }}</span>
                    </li>

                    <li id="print_srp" :style="[show_srp == true ? {} : {'display':'none'}]" :class="[print_option.srp == 'true' ? '' : 'noPrint']">
                        Standard Retail Price: <span>{{price}}</span><span>{{ str_price_change }}</span>
                    </li>

                    <li id="print_qp" :class="[print_option.qp == 'true' ? '' : 'noPrint']">
                        Quoted Price: <span>{{quoted_price}}</span><span>{{ str_quoted_price_change }}</span>
                    </li>

                </ul>

                <ul class="variants" v-if="variation1_value.length > 0">
                    <li>
                        Variants
                    </li>
                    <li v-if="variation1_value[0] !== '' && variation1_value[0] !== undefined">
                        {{ variation1 !== 'custom' ? variation1 + ': ' : variation1_custom + ': '}} <template v-for="(item, index) in variation1_value">{{ (index + 1 !== variation1_value.length) ? item + ', ' : item}} </template>
                    </li>
                    <li v-show="variation1_value[0] !== '' && variation1_value[0] !== undefined">
                        <select class="form-control" v-model="v1" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation1_value" :value="item" :key="item">{{item}}
                            </option>
                        </select>
                    </li>
                    <li v-if="variation2_value[0] !== '' && variation2_value[0] !== undefined">
                        {{ variation2 !== 'custom' ? variation2 + ': ' : variation2_custom + ': ' }} <template v-for="(item, index) in variation2_value">{{ (index + 1 !== variation2_value.length) ? item + ', ' : item}} </template>
                    </li>
                    <li v-show="variation2_value[0] !== '' && variation2_value[0] !== undefined">
                        <select class="form-control" v-model="v2" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation2_value" :value="item" :key="item">{{item}}
                            </option>
                        </select>
                    </li>
                    <li v-if="variation3_value[0] !== '' && variation3_value[0] !== undefined">
                        {{ variation3 !== 'custom' ? variation3 + ': ' : variation3_custom + ': ' }} <template v-for="(item, index) in variation3_value">{{ (index + 1 !== variation3_value.length) ? item + ', ' : item}} </template>
                    </li>
                    <li v-show="variation3_value[0] !== '' && variation3_value[0] !== undefined">
                        <select class="form-control" v-model="v3" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation3_value" :value="item" :key="item">{{item}}
                            </option>
                        </select>
                    </li>
                    <li v-if="variation4_value[0] !== '' && variation4_value[0] !== undefined">
                        {{ variation4 !== 'custom' ? variation4 + ': ' : variation4_custom + ': ' }} <template v-for="(item, index) in variation4_value">{{ (index + 1 !== variation4_value.length) ? item + ', ' : item}} </template>
                    </li>
                    <li v-show="variation4_value[0] !== '' && variation4_value[0] !== undefined">
                        <select class="form-control" v-model="v4" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation4_value" :value="item" :key="item">{{item}}
                            </option>
                        </select>
                    </li>

                    <template v-for="(item, index) in accessory_infomation" v-if="show_accessory">
                        <li>{{ item.category }}</li>
                        <li>
                            <select class="selectpicker" data-width="100%" :id="'tag'+index">
                                <option :data-thumbnail="detail.url" v-for="(detail, index) in item.detail[0]">
                                    {{detail.code}}
                                </option>
                            </select>
                        </li>
                    </template>

                </ul>

                <div class="btnbox noPrint">
                    <button class="btn btn-secondary" @click="print_option_page()">Export Setting</button>
                    <button class="btn btn-info" @click="print_page()">Export</button>
                    <button class="btn btn-info" @click="goto_sheet()" >Spec. Sheet</button>
                </div>

            </div>

        </div>


        <div class="middle_section" v-if="specification.length > 0 && sub_category != '10020000'">
            <h5>Specification</h5>

            <table>
                <tbody>
                <template v-for="(item, index) in specification">
                    <tr>
                        <td>
                            {{item.k1}}
                        </td>
                        <td>
                            {{item.v1}}
                        </td>
                        <td>
                            {{item.k2}}
                        </td>
                        <td> {{item.v2}}</td>
                    </tr>
                </template>

                </tbody>

            </table>

        </div>

        <div class="middle_section" v-if="related_product.length > 0 && sub_category != '10020000'">
            <h5>Related Products</h5>

            <div id="carouselExampleControls" class="carousel slide">

                <div class="carousel-inner">

                    <div v-for='(g, groupIndex) in groupedItems'
                         :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                        <div class="row custom">
                            <div class="col custom" v-for='(item, index) in g'>
                                <img :src="baseURL + item.photo1" :alt="'No Product Picture'">
                                <div>
                                    <a :href="'product_display_code?id=' + item.id">
                                        {{ item.code }}
                                    </a>
                                </div>
                                <div>
                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="RelatedPhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="RelatedPhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>

        <div class="middle_section" v-if="replacement_product.length > 0 && sub_category != '10020000'">
            <h5>Replacement Product</h5>

            <div id="carouselExampleControls_replacement" class="carousel slide">

                <div class="carousel-inner">

                    <div v-for='(g, groupIndex) in groupedItems_replacement'
                         :class="['carousel-item', (groupIndex == 0 ? 'active' : '')]">
                        <div class="row custom">
                            <div class="col custom" v-for='(item, index) in g'>
                                <img :src="baseURL + item.photo1" :alt="'No Product Picture'">
                                <div>
                                    <a :href="'product_display_code?id=' + item.id">
                                        {{ item.code }}
                                    </a>
                                </div>
                                <div>
                                    <!-- 網頁載入時，對於每一個相關產品，會根據「該產品是否停產」以及「有多少子規格停產」，來決定下面三個<span class="phasedout2">結構要顯示哪一個 -->
                                    <span class="phasedout2" v-if="item.out == 'Y' && item.phased_out_cnt == 0">Phased Out</span>
                                    <span class="phasedout2" v-if="item.phased_out_cnt == 1" @click="RelatedPhaseOutAlert(item.phased_out_text)">1 variant is phased out</span>
                                    <span class="phasedout2" v-if="item.phased_out_cnt > 1" @click="RelatedPhaseOutAlert(item.phased_out_text)">{{ item.phased_out_cnt }} variants are phased out</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls_replacement" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls_replacement" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>

        <div class="lower_section" v-if="((notes != null && notes != '') || description != '' || product_ics.length > 0 || product_skp.length > 0 || product_manual.length > 0) && sub_category != '10020000'">
            <h5>Description</h5>
            <p style="white-space: break-spaces;">{{ description }}</p>

            <p v-if="notes != null && notes != ''">Notes: {{ notes }}</p>

            <!-- 如果當前產品是 Lighting 產品，而且該產品有上傳 IES 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
            <p class="supporting_attachment" v-if="product_ics.length > 0">IES File:
                <span v-for="(item, index) in product_ics">
                    <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                </span>
            </p>

            <!-- 如果當前產品是 Office 產品，而且該產品有上傳 SketchUp 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
            <p class="supporting_attachment" v-if="product_skp.length > 0">SketchUp File:
                <span v-for="(item, index) in product_skp">
                    <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                </span>
            </p>

            <!-- 無論當前產品是 Lighting 或 Office 產品，只要該產品有上傳 Supporting File 檔案，則下面的 <p> 結構就要建立出來，並一一列出檔案在 <span> -->
            <p class="supporting_attachment" v-if="product_manual.length > 0">Supporting File:
                <span v-for="(item, index) in product_manual">
                    <a :href="baseURL + item.gcp_name" target="_blank" class="attch">{{item.filename}}</a>
                </span>
            </p>

            <!--
            <div class="desc_imgbox">
                <img src="images/realwork.png">
                <img src="images/realwork.png">
                <img src="images/wash_hands.png">
                <img src="images/realwork.png">
            </div>
            -->
        </div>


    </div>
</div>


</body>


<script src="js/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="js/npm/sweetalert2@9.js"></script>

<script src="js/vue-i18n/vue-i18n.global.min.js"></script>
<script src="js/element-ui@2.15.14/index.js"></script>
<script src="js/element-ui@2.15.14/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="js/element-ui@2.15.14/lib/index.js"></script>

<script src="js/product_display_code.js"></script>

<script>
    $(".btn").click(function () {

        if ($("#collapseme").hasClass("show")) {
            $("#collapseme").removeClass("show");
        } else {
            $("#collapseme").addClass("show");
        }
    });
</script>


</html>
