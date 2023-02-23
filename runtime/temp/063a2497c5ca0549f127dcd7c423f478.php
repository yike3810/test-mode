<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"F:\project\pointsmall\public/../application/index\view\details\order_detail.html";i:1639018914;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>商品详情</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/order_detail.css">
    <!-- 引入jquery-->
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="/static/js/vue.js"></script>
    <script src="/static/js/vant.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
    <!--引入自定义组件-->
    <script charset="utf-8" type="text/javascript" src="/static/js/pointsmall/global.js"></script>
    <script charset="utf-8" type="text/javascript" src="/static/js/pointsmall/goodslist.js"></script>
</head>
<style type="text/css">
    * {
        -webkit-overflow-scrolling: touch;
    }
    html, body {
        font-size: 16px;
        min-width: 100%;
        background-position: center;
        background-size: 100%;
        padding-bottom: 10px;
        background-color: #fff9f6;
    }
</style>
<body ontouchstart>
<div id="order">
    <!--    导航栏-->
    <div class="title">
        <div class="titleleft" @click="onClickLeft">
            <van-icon name="arrow-left" color="#7b7b7b" size="32"/>
        </div>
        <div class="titlecenter">
            订单详情
        </div>
        <div class="titleright"></div>
    </div>
    <!--    导航栏结束-->
    <!--    物流管理-->
    <div class="logistics">
        <div class="logistics_left">
            <img src="/static/image/jfsc/wuliu_logo.png" alt="">
        </div>
        <div class="logistics_center">
            <template v-if="order_info.shipping_status == 1">
                <span>正在运输</span>
                <span>快递公司：{{order_info.shipping_name}}  快递单号：{{order_info.tracking_number}}</span>
            </template>
            <template v-if="order_info.shipping_status == 0">
                <span>待发货</span>
                <span>快递正在等待揽收</span>
            </template>
            <template v-if="order_info.order_status == 10">
                <span>交易成功</span>
                <span>快递公司：{{order_info.shipping_name}}  快递单号：{{order_info.tracking_number}}</span>
            </template>

        </div>
        <div class="logistics_right">
<!--            <van-button type="primary">查看物流</van-button>-->
        </div>
    </div>
    <!--    物流管理结束-->
    <!--    联系地址-->
    <div class="address">
        <div class="address_left">
            <img src="/static/image/jfsc/dingwei.png" alt="">
        </div>
        <div class="address_center">
            <span>{{order_info.consignee}}
                <i>{{order_info.phone}}</i>
            </span>
            <span>{{order_info.addressAll}}</span>
        </div>

    </div>
    <!--    联系地址结束-->
    <!--    nav开始-->
    <div class="navs">
        <div class="nav_top">
            <img src="/static/image/jfsc/jifen_logo.png" alt="">
            <span>积分兑换</span>
        </div>
        <div class="nav_center" @click="onDetail">
            <div class="nav_center_left">
                <img :src="'/uploads/goods/'+ order_info.goods_image">
            </div>
            <div class="nav_center_right">
                <span>{{order_info.goods_name}}</span>
                <span>{{order_info.goods_points}}积分</span>
                <span>&times;{{order_info.goods_num}}</span>
            </div>
        </div>
        <div class="nav_bottom">
            <div class="nav_bottom_right">
            <span>共{{order_info.goods_num}}件商品</span>
            <span>合计：<i>{{order_info.goods_points}}积分</i></span>
            </div>
        </div>
    </div>
    <!--nav结束-->
    <!--    订单信息-->
    <div class="news">
        <div class="news_top">
            <span>订单信息</span>
        </div>
        <div class="news_bottom">
            <div class="number">
                <span>订单编号:</span>
                <span>{{order_info.order_sn}}</span>
                <span  @click="onCopy">复制</span>
            </div>
            <div>
                <span>创建时间:</span>
                <span>{{order_info.add_time}}</span>
            </div>
<!--            <div>-->
<!--                <span>付款时间:</span>-->
<!--                <span>{{order_info.pay_time}}</span>-->
<!--            </div>-->
            <div>
                <span>发货时间:</span>
                <span>{{order_info.shipping_time}}</span>
            </div>
        </div>
    </div>
    <!--    订单信息结束-->
    <!--            热门兑换-->
    <div class="exchange">
        <div class="exchange_title">
            热门兑换
        </div>
            <div class="goodsdetail">
                <!--                商品图片-->
                <jsl-component-goodslist :list="list"></jsl-component-goodslist>
            </div>
    </div>
    <!--            热门兑换结束-->
</div>
<script>
    var vant = window.vant;
    Vue.use(vant);
    var order = new Vue({
        el: '#order',
        data() {
            return {
                list: <?php echo $list; ?>,//好物推荐列表  商品按id排序 仅限4条
                order_info: <?php echo $order_info; ?>,//订单详情
                message: "<?php echo $order_info['order_sn']; ?>",//复制信息
            };
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            onDetail() {
                //商品详情
                window.location.href = '<?php echo url("Details/index"); ?>?goods_id='+this.order_info.goods_id;
            },
            onCopy: function (val) {
                this.$copyText(this.message)
                    .then(function (e) {
                        vant.Toast('复制成功');
                    })
                    .catch(function (e){
                        vant.Toast('复制失败');
                    })
            }
        },
    });
</script>
</body>
</html>