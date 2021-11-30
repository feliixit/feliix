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
    <title>FELIIX template</title>
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
          href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css">
    <link rel="stylesheet" type="text/css" href="css/tagsinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">



    <!-- jQuery和js載入 -->
    <script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/rm/realmediaScript.js"></script>
    <script type="text/javascript" src="js/main.js" defer></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
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

        body.gray select {
            background-image: url(images/ui/icon_form_select_arrow_gray.svg);
        }

        .NTD_price {
            display: block;
        }

        .btnbox > button, .heading-and-btn button {
            margin: 0 10px;
            width: 80px;
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
        }

        .imagebox .selected_image img {
            max-width: 300px;
            max-height: 300px;
        }

        .imagebox .image_list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 0 10px 20px 20px;
        }

        .imagebox .image_list img {
            max-width: 100px;
            max-height: 100px;
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

        .infobox .basic_info div.tags{
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
        }

        .infobox .price_stock {
            border-bottom: 2px solid rgb(225, 225, 225);
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
            margin-left: 20px;
            padding: 0 20px;
        }

        .infobox .variants li {
            color: #212529;
            font-size: 16px;
            margin-left: 15px;
        }

        .infobox .variants li:nth-of-type(odd) {
            margin-bottom: 15px;
        }

        .infobox .variants li:nth-of-type(1) {
            font-weight: 700;
            margin-left: 0;
            margin-bottom: 5px;
        }

        .infobox .variants .dropdown-menu li{
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

        .lower_section .desc_imgbox{
            width: 100%;
            padding: 0 15px;
            margin: 10px 0 20px;
        }

        .lower_section .desc_imgbox img{
            width: calc(50% - 8px);
            margin: 5px 0;
        }

        .lower_section .desc_imgbox img:nth-of-type(odd){
            margin-right: 10px;
        }


    </style>

</head>

<body class="gray">

<div class="bodybox" id="app">

    <!-- header -->
    <header>header</header>
    <!-- header end -->
    <div class="mainContent">

        <div class="upper_section">

            <div class="imagebox">
                <div class="selected_image">
                    <img :src="url">
                </div>
                <div class="image_list">
                    <img v-if="url1" :src="url1" @click="change_url(url1)"/>
                    <img v-if="url2" :src="url2" @click="change_url(url2)"/>
                    <img v-if="url3" :src="url3" @click="change_url(url3)"/>
                    <img v-for="(item, index) in variation_product" v-if="item.url" :src="item.url" @click="change_url(item.url)">
                </div>

            </div>


            <div class="infobox">
                <div class="basic_info">
                    <h3>{{code}}</h3>
                    <h6>{{brand}}</h6>
                    <h6 v-if="category == 'Lighting'">{{ category}}</h6>
                    <h6 v-if="category != 'Lighting'">{{ category}} >> {{ sub_category_name}}</h6>
                    <div class="tags">
                        <span v-for="(it, index) in tags">{{ it }}</span>
                    </div>
                </div>

                <ul class="price_stock">

                    <li class="NTD_price">
                        Cost Price: <span>{{ price_ntd }}</span><span></span>
                    </li>

                    <li>
                        Retail Price: <span>{{price}}</span><span></span>
                    </li>

                    <li>
                        Quoted Price: <span>{{quoted_price}}</span><span></span>
                    </li>

                </ul>

                <ul class="variants">
                    <li>
                        Select:
                    </li>
                    <li v-if="variation1_value[0] !== '' && variation1_value[0] !== undefined">
                        {{ variation1 !== 'custom' ? variation1 : variation1_custom}}
                    </li>
                    <li v-show="variation1_value[0] !== '' && variation1_value[0] !== undefined">
                        <select class="form-control" v-model="v1" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation1_value" :value="item" :key="item">{{item}}</option>
                        </select>
                    </li>
                    <li v-if="variation2_value[0] !== '' && variation2_value[0] !== undefined">
                        {{ variation2 !== 'custom' ? variation2 : variation2_custom }}
                    </li>
                    <li v-show="variation2_value[0] !== '' && variation2_value[0] !== undefined">
                        <select class="form-control" v-model="v2" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation2_value" :value="item" :key="item">{{item}}</option>
                        </select>
                    </li>
                    <li v-if="variation3_value[0] !== '' && variation3_value[0] !== undefined">
                        {{ variation3 !== 'custom' ? variation3 : variation3_custom }}
                    </li>
                    <li v-show="variation3_value[0] !== '' && variation3_value[0] !== undefined">
                        <select class="form-control" v-model="v3" @change="change_v()">
                            <option value=""></option>
                            <option v-for="(item, index) in variation3_value" :value="item" :key="item">{{item}}</option>
                        </select>
                    </li>

                    <template v-for="(item, index) in accessory_infomation" v-if="show_accessory">
                        <li>{{ item.category }}</li>
                        <li>
                            <select class="selectpicker" data-width="100%" :id="'tag'+index">
                                <option :data-thumbnail="detail.url" v-for="(detail, index) in item.detail[0]">{{detail.code}}</option>
                            </select>
                        </li>
                    </template>

                </ul>

                <div class="btnbox">
                    <button class="btn btn-info">Add</button>
                </div>

            </div>

        </div>


        <div class="middle_section">
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


        <div class="lower_section">
            <h5>Description</h5>
            <p>
                {{ description }}
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



<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="js/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="//unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
<script src="//unpkg.com/element-ui"></script>
<script src="//unpkg.com/element-ui/lib/umd/locale/en.js"></script>

<!-- Awesome Font for current webpage -->
<script src="js/a076d05399.js"></script>

<script>
    ELEMENT.locale(ELEMENT.lang.en)
</script>

<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>

<script src="js/product_display.js"></script>

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
