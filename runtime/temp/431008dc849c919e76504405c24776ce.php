<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"F:\project\pointsmall\public/../application/index\view\orderlist\index.html";i:1647934475;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>兑换记录</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/points-newslist.css">
    <!-- 引入jquery-->
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="/static/js/vue.js"></script>
    <script src="/static/js/vant.min.js"></script>
    <script src="/static/js/axios.min.js"></script>
    <script src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=3.2.0"></script>
    <!--引入自定义组件-->
    <link rel="stylesheet" href="/static/css/components/lxl-popover.css">
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
        background-color: #fbf5f3;
    }

</style>
<body ontouchstart>
<div id="list">
    <!--    标题栏-->
    <div class="header" v-if="ios_status == '0'">
        <div class="title" v-if="client_status != '1'">
            <div class="titleleft" @click="onClickLeft">
                <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
            </div>
            <div class="titlecenter">
                兑换记录
            </div>
            <div class="titleright"></div>
        </div>
    </div>
    <!--    标题栏结束-->
    <!--    菜单栏-->
    <van-row>
        <van-col span="24">
            <van-tabs v-model="activeName" @click="onNewslist">
                <van-tab title="全部" name="a"></van-tab>
                <van-tab title="待发货" name="b"></van-tab>
                <van-tab title="待收货" name="c"></van-tab>
                <van-tab title="已完成" name="d"></van-tab>
            </van-tabs>
        </van-col>
    </van-row>
    <!--下拉刷新-->
    <van-pull-refresh v-model="uploading"
                      success-text="刷新成功"
                      style="width: 100%"
                      @refresh="onRefresh();">
        <!--            懒加载-->
        <van-list
                v-model="loading"
                :finished="finished"
                finished-text="没有更多了"
                @load="onLoad()"
        >
            <template v-for="item in order_list">
                <div class="exchange_shop" @click="detail(item.order_id)">
                    <van-row>
                        <div class="exchange_node">
                            <van-col span="22" offset="1">
                                <span>{{item.order_sn}}</span>
                                <span class="red_text"
                                      v-if="[1,0].includes(item.order_status) && [0].includes(item.shipping_status)">待发货</span>
                                <span class="red_text"
                                      v-if="[1].includes(item.order_status) && [1].includes(item.shipping_status)">待收货</span>
                                <span class="red_text" v-if="[10].includes(item.order_status)">已完成</span>
                            </van-col>
                        </div>
                        <div class="border_a">
                            <template v-for="item in item.order_goods">
                                <div class="exchange_body">
                                    <van-col span="22" offset="1">
                                        <div class="exchange_body1">
                                            <div class="exchange_image">
                                                <img :src="'/uploads/goods/' + item.goods_img">
                                            </div>
                                            <div class="exchange_message">
                                                <div>
                                                    <span>{{item.goods_name}}</span>
                                                    <!--                                                    <span>坚果礼盒锦礼138型</span>-->
                                                </div>
                                                <div>
                                                    <span class="red_text2">{{item.goods_points}}</span><span>积分</span>
                                                    <span class="number">x{{item.goods_num}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </van-col>
                                </div>
                            </template>
                        </div>
                        <div class="exchange_sum">
                            <div>
                                <span>共{{item.total_goods}}件商品</span><span>合计:</span><span>{{item.total_points}}</span><span>积分</span>
                            </div>
                        </div>
                    </van-row>
                </div>
            </template>
        </van-list>
    </van-pull-refresh>

</div>
<script>
    var vant = window.vant;
    Vue.use(vant);
    var list = new Vue({
        el: '#list',
        data() {
            return {
                activeName: 'a',
                order_list: [],
                list_show: 0,
                waitreceivelist: [],
                waitdeliverlist: [],
                finishedlist: [],
                page: 1,
                uploading: '',
                loading: false,
                finished: true,//是否全部加载完毕
                client_status: '<?php echo $client_status; ?>',
                ios_status:'<?php echo $ios_status; ?>',
            };
        },
        mounted() {
            $(function () {
                list.onRefresh()
            })
        },
        methods: {
            onLoad: function () {
                let that = this;
                let json = {
                    'activeName': this.activeName,
                    'page': this.page,
                    'order_id': this.order_id
                };
                axios.post("<?php echo url('Orderlist/getrecordlist'); ?>", json)
                    .then((res) => {
                        console.log(res.data)
                        number = 5;
                        if (res.data.length < number) {
                            this.finished = true;
                        }
                        that.order_list = that.order_list.concat(res.data);
                        that.page++;
                        that.loading = false;
                    });
            },
            onRefresh: function () {
                this.uploading = true;
                // 清空列表数据
                this.finished = false;
                this.page = 1;
                this.order_list = [];
                // 重新加载数据
                this.loading = true;
                if (this.loading) {
                    this.onLoad();
                }
                this.uploading = false;
            },
            onNewslist: function (name) {
                sessionStorage.setItem("active", name);
                this.onRefresh();

            },
            detail: function (order_id) {
                window.location.href = '<?php echo url("details/order_detail"); ?>?order_id=' + order_id;
            },
            onClickLeft() {
                window.history.go(-1);
            },
        },
    });
</script>
</body>
</html>