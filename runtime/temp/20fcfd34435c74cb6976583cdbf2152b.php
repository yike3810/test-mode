<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\project\pointsmall\public/../application/index\view\index\search.html";i:1644288649;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>积分商城</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="/static/css/vant.css">
    <link rel="stylesheet" href="/static/css/wap-news.css">
    <link rel="stylesheet" href="/static/css/pointsmall/search.css">
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
        background-color: #fffbf9;
    }
</style>
<body ontouchstart>
<div id="search">
    <!--    顶部开始-->
    <div class="top" style="z-index: 999">
            <div class="bar_back" @click="onClickLeft()">
                <van-icon name="arrow-left" color="#ffffff" size="32"/>
            </div>
            <div class="search">
                <input v-model="keywords" type="text" value="" placeholder="请输入商品名称" @click="searchinput()">
            </div>
            <div class="bar_right" @click="onSearch">
                搜索
            </div>
    </div>
</div>
<div id="index">
    <template v-if="list_show == 0">
        <div class="jsl-search" style="">
            <div class="jsl-search-keywords" style="">
                历史搜索
            </div>
            <div class="jsl-search-image" style="" @click="deltag">
                <img src="/static/image/delete1.png" alt="">
            </div>
        </div>
        <div class="jsl-search-items">
            <template v-for="item in search_list">
                <van-tag size="medium" round type="primary" @click="onTag(item)" text-color="#595656" style="color:white;background-color:rgb(249 227 225);margin:10px 10px ">
                    {{item}}
                </van-tag>
            </template>
        </div>
    </template>

    <template v-if="list_show == 1">
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
    </template>

</div>
<script>
    // 此文件放一些公共方法
    //引入字体
    document.write('<link rel="stylesheet" href="http://at.alicdn.com/t/font_2685163_ivzbp377qso.css">');


    var vant = window.vant;
    Vue.use(vant);
    $(document).ready(function () {
        var search = new Vue({
            el: '#search',
            data() {
                return {
                    keywords: '',
                };
            },
            mounted() {
                if (localStorage.getItem("keywords") != null) {
                    this.keywords = localStorage.getItem("keywords");
                }
            },
            methods: {
                onClickLeft() {
                    localStorage.removeItem("keywords");
                    window.history.go(-1);
                },
                searchinput: function (a) {
                    index.list_show = 0;
                },
                onSearch: function () {
                    // console.log(this.keywords)
                    // if(index.search_list.length > 5){
                    //     index.search_list.shift();
                    // }
                    if(this.keywords != ''){
                        if (index.search_list.includes(this.keywords)){
                        }else {
                            index.search_list.push(this.keywords);
                        }
                        localStorage.setItem("search_list", index.search_list);
                        localStorage.setItem("keywords", this.keywords);
                        index.list_show = 1;
                        index.onRefresh();
                    }
                },


            },
        });

        var index = new Vue({
            el: '#index',
            data() {
                return {
                    keywords: '',
                    goodslist: [],
                    loading: false,
                    finished: true,//是否全部加载完毕
                    uploading: false,
                    list:[],
                    list_show: 0,
                    search_list: [],

                };
            },
            mounted() {
                // $(function () {
                //     index.onRefresh();
                // })

            },
            created: function () {
                if(localStorage.getItem("search_list") != null){
                    this.search_list = localStorage.getItem("search_list").split(",");
                }
                // console.log(this.search_list)
            },
            methods: {
                onTag:function (a){
                    localStorage.setItem("keywords", a);
                    this.keywords = a;
                    search.keywords = a;
                    this.list_show = 1;
                    search.onSearch();
                },
                deltag:function(){
                    localStorage.clear('');
                    window.location.reload();
                },

                onRefresh: function () {
                    this.keywords = localStorage.getItem("keywords");
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
                        'keywords': this.keywords,
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


            },
        });
    })

</script>
</body>
</html>