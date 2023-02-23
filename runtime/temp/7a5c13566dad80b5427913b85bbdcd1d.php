<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"F:\project\pointsmall\public/../application/index\view\address\manageaddress.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>收货地址</title>
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
    <!--    <script src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=3.2.0"></script>-->
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
        background-color: #ffffff;
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

    table {
        border-spacing: 0;
        border: 0.5px solid rgba(200, 200, 200, 0.5);
    }

    td {
        border: 0.5px solid rgba(200, 200, 200, 0.5);
    }

    .van-address-list {
        padding: 12px 0 100px;
    }

    .van-address-list__bottom {
        filter: drop-shadow(0px -5px 5px #e8e8e8);
        padding: 10px 16px;
    }

    .van-button--large {
        width: 50%;
    }

    .defaultDel {
        font-size: 14px;
        padding: 8px 0;
        display: flex;
        justify-content: space-between;
    }

    /*商品底部开始*/
    .goodsbottom {
        height: 60px;
        /*box-shadow:  0px -5px 5px -1px #d0caca5c;*/
        filter: drop-shadow(0px -5px 5px #e8e8e8);
        z-index: 999;
        width: 100%;
        background-color: white;
        position: fixed;
        left: 0;
        bottom: 0;
        display: flex;
        align-items: center;
    }

    .goodsbottom .bottom {
        margin: 10px 20px;
        padding: 8px 10px;
        width: 100%;
        text-align: center;
        background-color: #ee2e2a;
        color: #ffffff;
        border-radius: 20px;
    }

    /*商品底部结束*/
</style>

<body ontouchstart>
<div id="manageaddress">
    <!--    导航栏-->
    <!--ios_status ==1 表示ios登录，不显示头部-->
    <template v-if="ios_status == '0'">
    <div class="title" v-if="client_status != '1'">
        <div class="titleleft" @click="onClickLeft">
            <van-icon name="arrow-left" color="#2c2c2c" size="26"/>
        </div>
        <div class="titlecenter">
            我的收货地址
        </div>
        <div class="titleright" style="width: 10%" v-if="manageadd == false" @click="manageadd = true">管理</div>
        <div class="titleright" style="width: 10%" v-if="manageadd == true" @click="manageadd = false">完成</div>
    </div>
    </template>
    <!--    导航栏结束-->
    <!--收货地址开始-->
    <van-pull-refresh v-model="uploading" success-text="刷新成功"
                      @refresh="onRefresh();">
        <van-list
                v-model="loading"
                :finished="finished"
                finished-text=""
                @load="onLoad()"
        >
        <van-radio-group v-model="defaultaddress">
            <template v-for="item in list">
                <div class="center">
                    <div class="content" >
                        <van-row style="padding: 5px 0" @click="onExchange(item.id)">
                            <van-col span="18">{{item.name}}&nbsp;&nbsp;{{item.tel}}
                                <van-tag round type="primary" color="#ee0a24" v-if="item.isDefault == 'true'">默认</van-tag>
                            </van-col>
                        </van-row>
                        <van-row style="padding: 5px 0">
                            <van-col span="22" style="font-size: 14px" @click="onExchange(item.id)">
                                {{item.address}}
                            </van-col>
                            <van-col span="2" class="content_ico" @click="onEdit(item.id)">
                                <van-icon name="edit" size="20"/>
                            </van-col>
                        </van-row>
                        <van-row style="padding: 5px 0;font-size: 14px" v-if="manageadd == true">
                            <van-col span="16" @click="onDefault(item.id)">
                                <van-radio :name="item.id" checked-color="#ee0a24">设置为默认地址</van-radio>
                            </van-col>
                            <van-col offset="6" span="2" @click="onDel(item.id)">删除</van-col>
                        </van-row>
                    </div>
                </div>
            </template>
        </van-radio-group>
        </van-list>
    </van-pull-refresh>
    <div class="goodsbottom">
        <div class="bottom" @click="onAdd">+新增收货地址</div>
    </div>
    <!--    以下为vant的组件使用示例-->
    <!--    <div>-->
    <!--        <van-address-list-->
    <!--                v-model="chosenAddressId"-->
    <!--                :list="list"-->
    <!--                default-tag-text="默认"-->
    <!--                :add-button-text="'+新增收货地址'"-->
    <!--                @add="onAdd"-->
    <!--                @edit="onEdit"-->
    <!--                @click-item="onExchange"-->
    <!--        />-->
    <!--        <template #item-bottom>-->
    <!--            <van-row style="padding: 5px 0;font-size: 14px">-->
    <!--                <van-col offset="2" span="10" @click="onDefault">设置为默认地址</van-col>-->
    <!--                <van-col offset="10" span="2" @click="onDel"><van-icon name="delete-o" /></van-col>-->
    <!--            </van-row>-->
    <!--        </template>-->
    <!--    </div>-->
</div>
<script>

    var vant = window.vant;
    Vue.use(vant);
    var manageaddress = new Vue({
        el: '#manageaddress',
        data() {
            return {
                manageadd: false,//管理按钮开关
                loading: false,
                uploading: false,
                finished: true,//是否全部加载完毕
                list:[],
                defaultaddress:<?php echo $address_info['address_id']; ?>,
                chosenAddressId: '1',
                goods_id: '<?php echo $goods_id; ?>',
                goods_num: '<?php echo $goods_num; ?>',
                client_status: '<?php echo $client_status; ?>',
                ios_status:'<?php echo $ios_status; ?>',
            };
        },
        mounted() {
            $(function () {
                manageaddress.onRefresh();
            })
        },
        methods: {
            onClickLeft() {
                window.history.go(-1);
            },
            onAdd() {
                //添加
                window.location.href = '<?php echo url("Address/add"); ?>';
            },
            onEdit(item, index) {
                //修改
                window.location.href = '<?php echo url("Address/edit"); ?>?address_id=' + item;
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
                axios.post("<?php echo url('Address/getDZlist'); ?>" ,data)
                    .then((res) => {
                        //参数说明：number:每页获取的条数判断，最好与后台一致，不可大于后台设置值
                        number = 6;
                        if (res.data.list.length < number) {
                            this.finished = true;
                        }
                        thiss.page++;
                        thiss.list = thiss.list.concat(res.data.list);
                        thiss.defaultaddress = res.data.address_info['address_id'];
                        thiss.loading = false;
                    });
            },
            onExchange(item) {
                //兑换
                if(this.goods_id != ''){
                    window.location.href = '<?php echo url("Details/placeorder_detail"); ?>?address_id=' + item +'&goods_id=' + this.goods_id + '&goods_num=' + this.goods_num;
                }
            },
            onDefault(a) {
                //设置为默认
                let that = this;
                axios.post("<?php echo url('Address/defaultaddress'); ?>?address_id=" + a)
                    .then((res) => {
                        if (res.data.status == 1) {
                            // vant.Toast({
                            //     'message':res.data.message,
                            // });
                            that.manageadd = false;
                            that.onRefresh();
                        } else {
                            //     失败
                            vant.Toast(res.data.message);
                            return false;
                        }
                    });
            },
            onDel(a) {
                //删除
                that = this;
                vant.Dialog.confirm({
                    message: '确定要彻底删除？',
                })
                    .then(() => {
                        // on confirm
                        axios.post("<?php echo url('Address/deladdress'); ?>?address_id=" + a)
                            .then((res) => {
                                if (res.data.status == 1) {
                                    vant.Toast({
                                        'message': res.data.msg,
                                        onClose() {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    //     失败
                                    vant.Toast(res.data.msg);
                                    return false;
                                }

                            });
                    })
                    .catch(() => {
                        // on cancel
                    });
            },
        },
    });
</script>
</body>
</html>