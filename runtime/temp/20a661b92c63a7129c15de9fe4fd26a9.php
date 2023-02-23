<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"F:\project\pointsmall\public/../application/index\view\details\placeorder_detail.html";i:1647935213;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>商品详情</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/placeorder-details.css">
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
        background-color: #f1f1f1;
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
<div id="placeorder">
<!--    导航栏-->
    <!--ios_status ==1 表示ios登录，不显示头部-->
    <template v-if="ios_status == '0'">
        <div class="title" v-if="client_status != '1'">
            <div class="titleleft" @click="onClickLeft">
                <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
            </div>
            <div class="titlecenter">
                确认订单
            </div>
            <div class="titleright"></div>
        </div>
    </template>
<!--    导航栏结束-->
<!--订单详情开始-->
    <div class="center">
        <div class="content">
            <van-row style="padding: 5px 0">
                <van-col span="6" class="content_title">收货人：</van-col>
                <van-col span="18">{{address_list.consignee}}&nbsp;&nbsp;{{address_list.phone}}</van-col>
            </van-row>
            <van-row style="padding: 5px 0">
                <van-col span="6" class="content_title">收货地址：</van-col>
                <van-col span="16" @click="onaddress">{{address_list.addressAll}}</van-col>
                <van-col span="2" class="content_ico" @click="onaddress"><van-icon name="arrow" size="20"/></van-col>
            </van-row>
        </div>
    </div>
    <div class="center">
        <div class="content">
            <van-row style="padding: 5px 0" @click="ondetails">
                <van-col span="5" class="goodsdhimg">
<!--                    <img src="/static/image/ybh/banner_jkzg.jpg"/>-->
                    <img :src="'/uploads/goods/'+ pointsgoods_info.goods_image" >
                </van-col>
                <van-col span="16" offset="1">
                    <div style="height: 50px">{{pointsgoods_info.goods_name}}</div>
                    <div style="margin-top: 30px">&times;{{pointsgoods_info.goods_num}}</div>
                </van-col>
                <van-col span="2" class="content_ico" >
                    <van-icon name="arrow" size="20"/>
                </van-col>
            </van-row>
            <van-row style="padding: 15px 0 5px ">
                <van-col span="12" class="content_title">商品原价：</van-col>
                <van-col span="12" class="content_ico" style="color: black">￥{{pointsgoods_info.goods_price}}</van-col>
            </van-row>
            <van-row style="padding: 5px 0">
                <van-col span="12" class="content_title">会员积分：</van-col>
                <van-col span="12" class="content_ico" style="color: black">{{pointsgoods_info.all_point}}</van-col>
            </van-row>
        </div>
    </div>
    <div class="center">
        <div class="content">
            <van-row style="padding: 5px 0">
                <van-col span="6" class="content_title">配送方式</van-col>
                <van-col span="16" style="text-align: right">快递 包邮（专属权益）</van-col>
                <van-col span="2" class="content_ico">
                    <van-icon name="arrow" size="20"/>
                </van-col>
            </van-row>
        </div>
    </div>
<!--订单详情开始-->
    <div class="goodsbottom">
        <div class="bottompoints">
            <span>合计</span><span>{{pointsgoods_info.all_point}}积分</span>
        </div>
        <van-button class="bottom" @click="onexchange" :disabled="disable" style="padding: 0 0 ">去兑换 </van-button>
    </div>
</div>
<script>
    // var vConsole = new VConsole();
    var vant = window.vant;
    Vue.use(vant);
    var placeorder = new Vue({
        el: '#placeorder',
        data() {
            return {
                detailshow:false,
                goods_id:'<?php echo $goods_id; ?>',
                goods_num:'<?php echo $goods_num; ?>',
                address_list:<?php echo $address_list; ?>,//地址列表
                pointsgoods_info:<?php echo $pointsgoods_info; ?>,//商品属性
                disable:false,
                client_status: '<?php echo $client_status; ?>',
                ios_status:'<?php echo $ios_status; ?>',
            };
        },
        mounted() {
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            onaddress(){
                //管理地址
                window.location.href = '<?php echo url("Address/Manageaddress"); ?>?goods_id='+this.goods_id + '&goods_num=' + this.goods_num;
            },
            onexchange(){
                //兑换提交
                this.disable = true;
                let jsons = {
                    'province_id': this.address_list.province_id,
                    'city_id': this.address_list.city_id,
                    'district_id': this.address_list.district_id,
                    'address': this.address_list.address,
                    'phone': this.address_list.phone,
                    'consignee': this.address_list.consignee,
                    'goods_id': this.goods_id,
                    'goods_num': this.pointsgoods_info.goods_num,
                    'all_point': this.pointsgoods_info.all_point,
                };
                axios.post("<?php echo url('Details/submitExchange'); ?>", jsons)
                    .then(function (response) {
                        if (response.data.status == 1) {
                            vant.Toast.success({
                                message: '兑换成功',
                                onClose: function () {
                                    window.location.href = '<?php echo url("Orderlist/index"); ?>';
                                }
                            });
                        } else {
                            vant.Toast(response.data.message);
                        }
                    })
                    .catch(function (error) {
                        vant.Toast(error);
                    });
            },
            ondetails(){
                //商品详情
                window.location.href = '<?php echo url("Details/index"); ?>?goods_id='+this.pointsgoods_info.goods_id;
            },
        },
    });
</script>
</body>
</html>