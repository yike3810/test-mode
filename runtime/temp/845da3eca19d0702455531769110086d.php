<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"F:\project\pointsmall\public/../application/index\view\index\index.html";i:1649665455;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>积分商城</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/index.css">
    <!-- 引入jquery-->
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="/static/js/vue.js"></script>
    <script src="/static/js/vant.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
<!--    <script src="/static/js/vconsole.min.js"></script>-->
    <!--引入自定义组件-->
    <link rel="stylesheet" href="/static/css/components/lxl-popover.css">
</head>
<body ontouchstart>
<div id="index">
    <!--    顶部开始-->
    <div class="top">
<!--        <div class="titleleft" @click="onClickLeft">-->
<!--            <van-icon name="arrow-left" color="#ffffff" size="32"/>-->
<!--        </div>-->
        <div class="search">
            <input id="" type="text" value="" placeholder="请输入商品名称" @click="search()">
            <img src="/static/image/jfsc/search.png" alt="">
        </div>
        <!--        <div class="titleright"></div>-->
    </div>
    <!--    顶部结束-->
    <!--    导航栏开始-->
    <div class="banner">
        <div class="banner_left">
            <img src="/static/image/jfsc/banner_left.png" alt="">
            <span>积分</span>
            <span>{{points}}</span>
        </div>
        <div class="banner_right" @click="shuoming()">
            <img src="/static/image/jfsc/banner_right.png" alt="">
            <span>积分说明</span>
        </div>
    </div>
    <!--导航栏结束-->
    <!--    nav部分开始-->
    <div class="nav">
        <div class="nav_left">
            <p>积分兑换</p>
            <van-button type="primary" @click="duihuan()">点击详情</van-button>
        </div>
        <div class="nav_right">
            <p>兑换记录</p>
            <van-button type="primary" @click="jilu()">点击详情</van-button>
        </div>
    </div>
    <!--    nav部分结束-->
    <!--    超值秒杀开始-->
    <div class="shop_ms" style="display: none">
        <div class="shop_ms_top">超值秒杀</div>
        <div class="shop_ms_index">
            <div class="icon">
                <img src="/static/image/jfsc/icon1.png" alt="">
            </div>
            <div class="next">
                <span>海南沙田柚美味多汁不苦酸甜可口</span>
                <span>1200 积分</span>
                <s>30.00元</s>
                <span>已抢购0件</span>
                <van-button type="primary">即将开始</van-button>
            </div>
        </div>
    </div>
    <!--    超值秒杀结束-->
<!--    热门兑换开始-->
    <div class="center" style="margin-bottom: 0;margin-top: 70px">
        <div class="exchange">
            <div class="exchange_title">
                热门兑换
            </div>
            <van-list
                    v-model="loading"
                    :finished="finished"
                    finished-text="没有更多了"
            >
                <div class="exchange_next">
                    <!--                商品图片-->
                    <template  v-for="item in goodslist">
                        <div class="exchange_goods" @click="onrmdh(item.goods_id)">
                            <img :src="item.goods_image"/>
                            <div class="exchange_goods_title">{{item.goods_name}}</div>
                            <div class="exchange_goods_icon">
                                <span ><img src="/static/image/pointsmall/points.png"/><span style="color: #ffa200;">{{item.goods_points}}</span> 积分</span>
                                <span>{{item.goods_storage}}份</span>
                            </div>
                            <div class="googsexchange" @click="">立即兑换</div>
                        </div>
                    </template>
                </div>
            </van-list>
        </div>
    </div>

<!--    热门兑换结束-->
</div>
<script>
    // var vConsole = new VConsole()
    var vant = window.vant;
    Vue.use(vant);
    var detail = new Vue({
        el: '#index',
        data() {
            return {
                goodslist:<?php echo $goods_list; ?>,
                points:<?php echo $points; ?>,
                loading: false,
                finished: true,//是否全部加载完毕

            };
        },
        mounted() {
            that = this;
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            //热门兑换
            onrmdh:function (a) {
                window.location.href = '<?php echo url("Details/index"); ?>?goods_id='+a;
            },
            //积分说明
            shuoming:function (a) {
                window.location.href = '<?php echo url("Index/pointsintroduction"); ?>';
            },
            //积分兑换
            duihuan:function (a) {
                window.location.href = '<?php echo url("Orderlist/goodslist"); ?>';
            },
            //兑换记录
            jilu:function (a) {
                window.location.href = '<?php echo url("Orderlist/index"); ?>';
            },
            //搜索
            search:function (a) {
                window.location.href = '<?php echo url("Index/search"); ?>';
            },
        },
    });
</script>
</body>
</html>