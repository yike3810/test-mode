<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"F:\project\pointsmall\public/../application/index\view\address\add.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>新增收货地址</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/vant2.12.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/addaddress.css">
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
    table{
        border-spacing: 0;
        border: 0.5px solid rgba(200,200,200,0.5);
    }
    td{
        border: 0.5px solid rgba(200,200,200,0.5);
    }
    .van-address-list {
        padding: 12px 0 100px;
    }
    .van-address-list__bottom {
        filter: drop-shadow(0px -5px 5px #e8e8e8);
        padding: 10px 16px;
    }

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
                新增收货地址
            </div>
            <div class="titleright"></div>
        </div>
    </template>
    <!--    导航栏结束-->
    <!--收货地址开始-->
    <div class="center">
        <van-row>
            <van-field v-model="consignee" required type="text" name="收货人" maxlength="255"
                       label="收货人"
                       placeholder="请输入收货人姓名"/>
        </van-row>
        <van-row>
            <van-field v-model="phone" required type="text" name="手机号码" maxlength="255"
                        label="手机号码"
                        placeholder="请输入手机号码"/>
        </van-row>
        <van-row>
            <van-field
                    v-model="fieldValue"
                    is-link
                    readonly
                    required
                    label="所在地区"
                    placeholder="选择省/市/县"
                    @click="addressshow = true"
            />
        </van-row>
        <van-popup v-model="addressshow" round position="bottom">
            <div>
                <van-cascader
                        v-model="cascaderValue"
                        title="请选择所在地区"
                        :options="options"
                        @close="addressshow = false"
                        @finish="onFinish"
                />
            </div>
        </van-popup>
        <van-row>
            <van-field  v-model="address" required type="textarea" name="详细地址" maxlength="255"
                        label="详细地址"
                        placeholder="请输入详细地址"/>
        </van-row>
        <van-row>
            <van-cell center title="设为默认收货地址" required>
                <template #right-icon>
                    <van-switch v-model="is_default" size="24" active-color="#ee0a24" inactive-color="#dcdee0"/>
                </template>
            </van-cell>
        </van-row>
    </div>
    <div class="goodsbottom">
        <div class="bottom" @click="onsubmit">保存</div>
    </div>
</div>
<script>

    var vant = window.vant;
    Vue.use(vant);
    var manageaddress = new Vue({
        el: '#manageaddress',
        data() {
            return {
                is_default:false,//默认地址
                addressshow:false,
                fieldValue: '',//所选地区值
                cascaderValue: '',//级联选择器
                fieldNames: [],//省市县暂存数组
                options:<?php echo $regionlist; ?>,
                province_id:'',//省
                city_id:'',//市
                district_id:'',//县
                address:'',//详细地址
                phone:'',//手机号码
                consignee:'',//收货人
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
            onFinish({ selectedOptions }) {
                this.addressshow = false;
                this.fieldValue = selectedOptions.map((option) => option.text).join('/');
                this.fieldNames = selectedOptions.map((option) => option.value);
                this.province_id = this.fieldNames[0];
                this.city_id = this.fieldNames[1];
                this.district_id = this.fieldNames.length == 3?this.fieldNames[2]:'0';
            },
            onsubmit(){
                let reg = /^1[3456789]{1}\d{9}$/;
                if (this.real_name== '') {
                    vant.Toast("请填写收货人姓名");
                    return false;
                }
                if (this.phone == '') {
                    this.$toast("请输入手机号");
                    return false;
                } else if (this.phone != '' & !reg.test(this.phone)) {
                    vant.Toast('手机号有误');
                    return false;
                }
                if (this.fieldValue == '') {
                    vant.Toast("请选择收货地区");
                    return false;
                }
                if (this.address == '') {
                    vant.Toast("请填写详细地址");
                    return false;
                }else {
                    let jsons = {
                        'consignee': this.consignee,
                        'address': this.address,
                        'phone': this.phone,
                        'province_id': this.province_id,
                        'city_id': this.city_id,
                        'district_id': this.district_id,
                        'is_default': this.is_default,
                    };
                    axios.post("<?php echo url('address/add_do'); ?>", jsons)
                        .then((res) => {
                            if (res.data.status == 1) {
                                vant.Toast({
                                    'message': res.data.message,
                                    onClose(){
                                        window.history.go(-1);
                                    }
                                });
                            } else {
                                //     失败
                                vant.Toast(res.data.message);
                                return false;
                            }
                        });
                }
            },

        },
    });
</script>
</body>
</html>