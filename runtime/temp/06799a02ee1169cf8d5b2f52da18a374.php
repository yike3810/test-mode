<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\project\pointsmall\public/../application/index\view\public\error.html";i:1638261247;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>积分商城</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <!-- 引入jquery-->
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="/static/js/vue.js"></script>
    <script src="/static/js/vant.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
    <!--    <script src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=3.2.0"></script>-->
    <!--引入自定义组件-->
    <link rel="stylesheet" href="/static/css/components/lxl-popover.css">
</head>
<style>
    * {
        -webkit-overflow-scrolling: touch;
    }

    html, body {
        font-size: 16px;
        min-width: 100%;
        background-position: center;
        background-size: 100%;
        padding-bottom: 30px;
        background-color: #fff9f6;
    }
    .van-icon::before {
        font-weight: 600;
    }
    .title{
        height: 40px;
        padding: 5px;
        display: flex;
        background-color: #fff;
        align-items:center;
        text-align: center;
        justify-content:space-between;
        box-shadow: 0 2px 5px 1px #eee9e6;
    }
    .title .titleleft{
        width: 5%;
    }
    .title .titleright{
        width: 5%;
    }
    .title .titlecenter{
        /*width: 70%;*/
        font-size: 22px;
        letter-spacing: 1.5px;
    }
    .content{
        display: flex;
    }
    .center{

        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
    }
    .center img{
        /*width: 200px;*/
        /*height: 180px;*/
        filter: blur(3px);

    }
    .center p{
        display: block;
        font-size: 22px;
        text-align: center;
        color: #73c9ff;
        filter: blur(1px);

    }
    .center p:nth-child(3){
        font-size: 14px;
        margin-top: -10px;
        color: #a8dcfc;
    }

</style>
<body ontouchstart>
<div id="errors">
    <!--    顶部开始-->
    <div class="title">
        <div class="titleleft" @click="onClickLeft">
            <van-icon name="arrow-left" color="#7b7b7b" size="36"/>
        </div>
        <div class="titlecenter">
            积分商城
        </div>
        <div class="titleright"></div>
    </div>
    <div class="content">
    <div class="center">
        <img src="/static/image/jfsc/errors.png"/>
        <p>当前网络貌似出了点问题</p>
        <p>可能打开方式不对，换个手指试试~</p>
    </div>
    </div>
<!--    顶部结束-->
</div>
<script>
    var vant = window.vant;
    Vue.use(vant);
    var detail = new Vue({
        el: '#errors',
        data() {
            return {};
        },
        mounted() {
            that = this;

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