<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"F:\project\pointsmall\public/../application/index\view\details\index.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>商品详情</title>
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
        background-color: #e5e5e5;
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
        <div class="title" v-if="client_status != '1'">
            <div class="titleleft" @click="onClickLeft">
                <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
            </div>
            <div class="titlecenter">
                商品详情
            </div>
            <div class="titleright"></div>
        </div>
    </template>
<!--    导航栏结束-->
<!--商品详情开始-->
<!--    商品轮播开始:autoplay="3000"-->
    <div class="goodslb">
            <template>
                <van-swipe class="my-swipe" :autoplay="3000" indicator-color="white">
                    <van-swipe-item>
                        <img :src="'/uploads/goods/'+goods_message.goods_image"/>
                        <div class="indicate"><span>1/{{goods_banner.length+1}}</span></div>
                    </van-swipe-item>
                    <template v-for="(item,index) in goods_banner">
                        <van-swipe-item>
                            <img :src="'/uploads/goods/'+item.image_path"/>
                            <div class="indicate"><span>{{index+2}}/{{goods_banner.length+1}}</span></div>
                        </van-swipe-item>
                    </template>
                </van-swipe>
            </template>
        </div>
<!--    商品轮播结束-->
    <div class="center">
        <div class="goods">
            <div class="goodstitle">
                {{goods_message.goods_name}}
            </div>
            <div class="goodspoints">
                <span>{{goods_message.goods_points}}</span>&nbsp;&nbsp;积分
            </div>
            <div class="goodscarriage">
                运费&nbsp;<span>0</span>&nbsp;元
            </div>
        </div>
    </div>
    <div class="center">
        <div class="goodsdetail">
            <div class="detailstitle">
                商品详情
            </div>
            <div class="detailscontent"  v-html="goods_message.goods_desc">
            </div>
        </div>
    </div>
<!--商品详情结束-->
<!--    好物推荐开始-->
    <div class="center" style="margin-bottom: 0">
        <div class="goodsdetail">
            <div class="detailshwtj">
                好物推荐
            </div>
            <jsl-component-goodslist :list="list"></jsl-component-goodslist>
        </div>
    </div>
<!--    好物推荐结束-->
<!--    判断当前商品是否下架1：表示上架 0：表示下架-->
    <template v-if="goods_message.goods_show==1">
        <div class="goodsbottom">
            <div class="bottom" @click="detailshow = true">立即兑换</div>
        </div>
    </template>
<!--    当商品已下架则显示商品已下架，不能兑换-->
    <template v-else>
        <div class="goodsbottom">
            <div class="bottom">该商品已下架！！</div>
        </div>
    </template>
<!--    立即兑换弹窗-->
    <van-popup
            v-model="detailshow"
            closeable
            position="bottom"
            :style="{ height: '40%' }"
    >
        <div class="center">
<!--            商品列表-->
            <div class="goodsdh">
                <div class="goodsdhimg">
                    <img :src="'/uploads/goods/'+goods_message.goods_image"/>
                </div>
                <div class="goodsdhdetail">
                    <div class="goodspoints">
                        <span>{{goods_message.goods_points}}积分</span>
                    </div>
                    <div class="goodstitle">
                        {{goods_message.goods_name}}
                    </div>

                </div>
            </div>
            <van-row class="goodsnum">
                <van-col span="12">购买数量<span class="goodsnumtext">库存{{goods_message.goods_storage}}件</span></van-col>
                <van-col span="12" >
                    <van-stepper v-model="goods_num"
                                 input-width="40px"
                                 button-size="32px"
                                 min="0"
                                 :max="maxnum"
                                 integer
                                 style="float: right"/>
                </van-col>
            </van-row>
<!--            该行仅在商品数量限制时显示-->
            <van-row class="goodsnum" v-if="goods_message.goods_islimit==1">
                <van-col span="24" style="font-size: 14px;color: #acaca2;">每个会员仅限兑换 {{goods_message.goods_limitnum}} 件该商品,还可以兑换{{goods_message.goods_limitnum-goods_count}}件</van-col>
            </van-row>
<!--            兑换-->
                <div class="goodsbottom">
                    <div class="bottom" @click="onexchange">立即兑换 </div>
                </div>
        </div>
    </van-popup>
</div>
<script>

    var vant = window.vant;
    // Vue.use(Dialog);
    Vue.use(vant);
    var detail = new Vue({
        el: '#detail',
        data() {
            return {
                goods_banner:<?php echo $goods_banner; ?>,
                goods_message:<?php echo $goods_message; ?>,
                detailshow:false,
                goods_num:'',//商品数量
                goods_id:'',//商品id
                list: <?php echo $list; ?>,//好物推荐列表  商品按id排序 仅限4条
                maxnum: "5",//商品兑换数量
                client_status: '<?php echo $client_status; ?>',
                goods_count:<?php echo $goods_count; ?>,
                ios_status:'<?php echo $ios_status; ?>',
            };
        },
        mounted() {
            // that = this;
            $(function () {
                if (detail.goods_message.goods_islimit==1){
                    detail.maxnum = detail.goods_message.goods_limitnum;
                }else {
                    detail.maxnum = detail.goods_message.goods_storage;
                }
                if (detail.goods_message.goods_storage>0){
                    detail.goods_num = 1;
                }else {
                    detail.goods_num = 0;
                }
                detail.goods_id = detail.goods_message.goods_id;
            })
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            onmin(){
                console.log(this.goods_num)
            },
            onexchange(){
                //兑换
                if(this.goods_num==0) {
                    vant.Dialog.alert({
                        title: '温馨提示',
                        message: '商品数量不符合规范',
                    }).then(() => {

                    });
                }else if (detail.goods_message.goods_limitnum-detail.goods_count<=0&&detail.goods_message.goods_islimit==1){
                    vant.Dialog.alert({
                        title: '温馨提示',
                        message: '该商品兑换已达上限',
                    }).then(() => {

                    });
                }else if (this.goods_num>detail.goods_message.goods_limitnum-detail.goods_count){
                    let mumber = detail.goods_message.goods_limitnum-detail.goods_count
                    let msg = '还可以兑换'+mumber+'件'
                    vant.Dialog.alert({
                        title: '温馨提示',
                        message: msg,
                    }).then(() => {

                    });
                }else {
                    window.location.href = '<?php echo url("Details/Placeorder_detail"); ?>?goods_id='+this.goods_id +"&goods_num=" + this.goods_num;
                }

            },
        },
    });
</script>
</body>
</html>