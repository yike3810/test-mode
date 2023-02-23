<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"F:\project\pointsmall\public/../application/index\view\orderlist\goodslist.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>积分兑换</title>
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
        background-color: #fffbf9;
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
    .content{
        padding: 10px 0;
        border-top: 1px solid rgba(218, 218, 218, 0.6);
        text-align: center;
        background-color: #FFFFFF;
        box-shadow:  0px 5px 5px -1px #d0caca5c;
        position: sticky;
        top: 50px;
        overflow-y: hidden;
        z-index: 9999999;
    }
    .content img{
        width: 20px;
        height: 20px;
        display: inline-block;
        vertical-align: middle;
    }
    .content span{
        display: inline-block;
        vertical-align: middle;
    }

</style>
<body ontouchstart>
<div id="goodslist">
<!--    导航栏-->
    <!--ios_status ==1 表示ios登录，不显示头部-->
    <template v-if="ios_status == '0'">
        <div class="title" v-if="client_status != '1'">
            <div class="titleleft" @click="onClickLeft" >
                <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
            </div>
            <div class="titlecenter">
                积分兑换
            </div>
            <div class="titleright"></div>
        </div>
    </template>
<!--&lt;!&ndash;    导航栏结束&ndash;&gt; -->
    <van-row class="content"
             <?php if($ios_status == '1'): ?>
             style="top: 0"
             <?php elseif($client_status == '1'): ?>
             style="top: 0"
             <?php else: endif; ?>
             >
        <van-col span="12">
            <img src="/static/image/jfsc/banner_left.png" alt="">
            <span>积分<span style="color:#ffa200;font-size: 14px;padding: 0 5px;line-height: 16px;">{{points}}</span></span>
        </van-col>
        <van-col  span="12" @click="onSwipe(member_id)">
            <img src="/static/image/jfsc/banner_right.png" alt="">
            <span>兑换记录</span>
        </van-col>
    </van-row>
<!--    商品列表开始-->
    <div class="center" style="background-color: unset">
    <van-pull-refresh v-model="uploading" success-text="刷新成功"
                      @refresh="onRefresh();">
        <van-list
                v-model="loading"
                :finished="finished"
                finished-text="没有更多了"
                @load="onLoad()"
        >

        <div class="goodsdetail">
<!--                商品图片-->
                <jsl-component-goodslist :list="list"></jsl-component-goodslist>
        </div>
        </van-list>
    </van-pull-refresh>
    </div>
<!--    商品列表结束-->
</div>
<script>
    var vant = window.vant;
    Vue.use(vant);
    var goodslist = new Vue({
        el: '#goodslist',
        data() {
            return {
                loading: false,
                uploading: false,
                finished: true,//是否全部加载完毕
                list:[],
                points:'<?php echo $points; ?>',
                member_id:'<?php echo $member_id; ?>',
                client_status: '<?php echo $client_status; ?>',
                ios_status:'<?php echo $ios_status; ?>',
            };
        },
        mounted() {
            $(function () {
                goodslist.onRefresh();
            })
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            onRefresh: function () {
                this.uploading = true;
                // 清空列表数据
                this.finished = false;
                this.page = 1;
                this.list = [];
                // 重新加载数据
                this.loading = true;
                if (this.loading) {
                    this.onLoad();
                }
                this.uploading = false;
            },
            onLoad: function () {
                let data = {
                    'active': this.active,
                    'page': this.page,
                    // 'keywords': this.value,
                    'id':this.id,
                };
                thiss = this;
                axios.post("<?php echo url('Orderlist/getGoodslist'); ?>" ,data)
                    .then((res) => {
                        //参数说明：number:每页获取的条数判断，最好与后台一致，不可大于后台设置值
                        number = 6;
                        if (res.data.length < number) {
                            this.finished = true;
                        }
                        thiss.page++;
                        thiss.list = thiss.list.concat(res.data);
                        thiss.loading = false;
                    });
            },
            onSwipe: function (a) {
                window.location.href = '<?php echo url("Orderlist/index"); ?>?member_id=' + a;
            },
        },
    });
</script>
</body>
</html>