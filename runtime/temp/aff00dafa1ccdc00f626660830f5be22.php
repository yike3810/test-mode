<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"F:\project\pointsmall\public/../application/index\view\index\pointsintroduction.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>积分说明</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/points-details.css">
    <!-- 引入jquery-->
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="/static/js/vue.js"></script>
    <script src="/static/js/vant.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
<!--    <script src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=3.2.0"></script>-->
<!--引入自定义组件-->
    <script charset="utf-8" type="text/javascript" src="/static/js/pointsmall/goodslist.js"></script>
</head>

<style type="text/css">
    * {
        -webkit-overflow-scrolling: touch;
    }

    html, body {
        font-size: 16px;
        min-width: 100%;

        /*background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAXcAAAAFCAYAAAC+XayAAAABF0lEQVRYhe2YUQ6CQAxEO8RPOYBX94B6APy2hriVtlshfELmJSaL7ZTt7LIkYLzrQ0Su0sD8g2yylTNIK1YACxVxdANDvzULHVyOj6Gsp1HuLn71RUVR1Pjl1zUGm4NVga9pudq3BvNj0QK5TS09B+I9e1+/vmn2ZUMHiyVdmGMawHzJGnm7HB/ofcxr2XvVa7Lv2Se4Oce404SF6r0ue9/pl91CW+95P1f7eK9faQrNr9j/ovvv2X8PZPd+s941rUN/RmjqNw2rvRcu6ucyeFT+7/OL53OrVrVP3ZxXj8oQ1yK+UttpirPhdRGR26qWEELI0Rjnl+fEZSOEkFMxzYf7yDUlhJBTMc6fZZ7+mzshhJCDI/L6ALZNPRNgitl+AAAAAElFTkSuQmCC");*/
        background-position: center;
        background-size: 100%;
        padding-bottom: 30px;
        /*background-color: #e5e5e5;*/
    }

    .h100 {
        height: 100%;
    }
    .fuzhi {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    table{
        border-spacing: 0;
        border: 0.5px solid rgba(200,200,200,0.5);
    }
    td{
        border: 0.5px solid rgba(200,200,200,0.5);
    }


</style>

<body ontouchstart>
<div id="detail">
<!--    导航栏-->
<!--ios_status ==1 表示ios登录，不显示头部-->
    <template v-if="ios_status == '0'">
        <div class="title" style="border-bottom:1px solid #e5e5e5;" v-if="client_status != '1'">
            <div class="titleleft" @click="onClickLeft">
                <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
            </div>
            <div class="titlecenter">
                积分说明
            </div>
            <div class="titleright"></div>
        </div>
    </template>
<!--    导航栏结束-->
<!--商品详情开始-->
<!--    商品轮播开始:autoplay="3000"-->

<!--    商品轮播结束-->
    <div class="center" v-html="integraldescription" >

    </div>
</div>
<script>

    var vant = window.vant;
    // Vue.use(Dialog);
    Vue.use(vant);
    var detail = new Vue({
        el: '#detail',
        data() {
            return {
                ios_status:'<?php echo $ios_status; ?>',
                integraldescription:`<?php echo $integraldescription; ?>`,
                client_status: '<?php echo $client_status; ?>',
            };
        },
        mounted() {

        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },


        },
    });
</script>
</body>
</html>