<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"F:\project\pointsmall\public/../application/admin\view\order\index.html";i:1644288648;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-col-md12" style="margin-top:20px;">
        <form class="layui-form">
            <div class="layui-row" style="margin-top:20px;" >
                <div class="layui-form-inline layui-col-md3">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" id="keywords" placeholder="请输入订单编号、收货人或手机号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-inline layui-col-md1">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-row">
        <div>
            <table id="demo" lay-filter="test"></table>
        </div>
    </div>
    <script>
        layui.use([ 'form','table'], function(){
            var table = layui.table;
            var form = layui.form;
            form.render();
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    table.reload('order', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            keywords: $('#keywords').val(),
                        }
                    }, 'data');
                }
            };
            $('#find_news').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

            //第一个实例
            table.render({
                elem: '#demo'
                ,toolbar: '#toolbarDemo'
                ,defaultToolbar: ['filter']
                ,url: "<?php echo url('Order/getorderlist'); ?>" //数据接口
                ,page: true //开启分页
                ,limit:15
                ,limits:[15,20,30,40,50,60,70,80,90]
                ,id:'order'
                ,cols: [[ //表头
                    {field: 'order_id', title: '订单ID', minWidth:80, width:80,fixed:'left'}
                    ,{field: 'order_sn', title: '订单编号',  width:140}
                    ,{field: 'member_name', title: '会员名',  width:140}
                    // ,{field: 'biz_id', title: '客户端订单号', width:150}
                    ,{field: 'consignee', title: '收货人姓名', width:135}
                    ,{field: 'phone', title: '收货人手机号', width:120}
                    ,{field: 'goods_name', title: '兑换商品',  width:100}
                    ,{field: 'goods_num', title: '兑换数量',  width:90}
                    ,{field: 'all_point', title: '消耗积分', width:120}
                    ,{field: 'add_time', title: '下单时间', width:170}
                    ,{field:'location',title:'所在地区',hide:true,width:170}
                    ,{field:'address',title:'详细地址',width:280,hide:true}
                    ,{field: 'shipping_id', title: '快递公司',hide:true, width:90,toolbar:'#company'}
                    ,{field: 'tracking_number', title: '快递单号',hide:true, width:90}
                    ,{field: 'order_status', title: '订单状态', width:90,toolbar:'#status1'}
                    ,{field: 'pay_status', title: '付款状态',width:90,toolbar: '#status2'}
                    ,{field: 'shipping_status', title: '发货状态', width:90,toolbar: '#status3'}
                    ,{field: 'operating1', title: '操作', minWidth:180,toolbar: '#barDemo'}
                ]],
            });

            table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）

                if(layEvent === 'del'){ //删除
                    layer.confirm('确定要删除该数据吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        //向服务端发送删除指令
                        var order_id = obj.data.order_id;
                        $.ajax({
                            url:"<?php echo url('Order/order_del'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'order_id='+order_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '删除成功'
                                    });
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '删除失败'
                                    });
                                }
                            },
                        });
                    });
                } else if(layEvent === 'edit'){
                    //编辑
                    console.log(obj.data);
                    var url = "<?php echo url('index_edit'); ?>?order_id="+obj.data.order_id;
                    layer.open({
                        type: 2,
                        title:'修改订单信息',
                        area: ['55%', '70%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('order', {});
                        }
                    });
                } else if(layEvent === 'detail'){
                    //查看
                    console.log(obj.data);
                    var url = "<?php echo url('order_detail'); ?>?order_id="+obj.data.order_id;
                    layer.open({
                        type: 2,
                        title:'查看订单详情',
                        area: ['55%', '70%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('order', {});
                        }
                    });
                }
            });
        });
    </script>
    <script type="text/html" id="company">
        {{# if(d.shipping_id == 1){ }}
        <p>圆通</p>
        {{# }else if(d.shipping_id == 2){ }}
        <p>申通</p>
        {{# }else if(d.shipping_id == 3){ }}
        <p>顺丰</p>
        {{# }else if(d.shipping_id == 4){ }}
        <p>韵达</p>
        {{# }else if(d.shipping_id == 5){ }}
        <p>中通</p>
        {{# }else if(d.shipping_id == 6){ }}
        <p>EMS</p>
        {{# }else if(d.shipping_id == 7){ }}
        <p>百世</p>
        {{# }else if(d.shipping_id == 8){ }}
        <p>德邦</p>
        {{# }else if(d.shipping_id == 9){ }}
        <p>京东</p>
        {{# } }}
    </script>
    <script type="text/html" id="status1">
        {{# if(d.order_status == 0){ }}
        <p>待审核</p>
        {{#} else if(d.order_status == 1){ }}
        <p>已审核</p>
        {{#} else if(d.order_status == 2){ }}
        <p>已收货</p>
        {{#} else if(d.order_status == 8){ }}
        <p>异常</p>
        {{#} else if(d.order_status == 9){ }}
        <p>已取消</p>
        {{#} else if(d.order_status == 10){ }}
        <p>已完成</p>
        {{# } }}
    </script>
    <script type="text/html" id="status2">
        {{# if(d.pay_status == 0 ){ }}
        <p>未付款</p>
        {{#} else if(d.pay_status == 1){ }}
        <p>已付款</p>
        {{#} }}
    </script>
    <script type="text/html" id="status3">
        {{# if(d.shipping_status == 0){ }}
        <p>未发货</p>
        {{#} else if(d.shipping_status == 1){ }}
        <p>已发货</p>
        {{#} }}
    </script>
    <script type="text/html" id="barDemo">
        {{# if(d.shipping_status == 0){ }}
        <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
        {{# } }}
        <a class="layui-btn layui-btn-danger layui-btn-xs"  lay-event="del">删除</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs"  lay-event="detail">查看</a>
    </script>
</div>
</body>
</html>