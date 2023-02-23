<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"F:\project\consumer-coupon\public/../application/admin\view\coupon\index.html";i:1675732986;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>树形列表|<?php echo config('system.title'); ?></title>
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
            <div class="layui-row" style="margin-top:20px;position: center" >
                <div class="layui-form-inline layui-col-md4">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" id="keywords" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-inline layui-col-md1">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                    </div>
                </div>
            </div>
        </form>
        <script type="text/html" id="toolbarDemo">
            <button type="button" class="layui-btn" id="add">发放消费券</button>
        </script>
        <table id="demo" lay-filter="test"></table>
    </div>
    <script>
        layui.use([ 'form','table'], function(){
            var table = layui.table;
            var form = layui.form;
            form.render();
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    table.reload('unassigned', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            keywords: $('#keywords').val(),
                            activity_status: $('#activity_status').val(),
                        }
                    }, 'data');
                }
            };
            $('#find_news').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
            function bindTableToolbarFunction() {
                var table = layui.table;
                $('#add').on("click", function () {
                    //iframe层-父子操作
                    layer.open({
                        type: 2,
                        area: ['1000px', '650px'],
                        title: '添加商品',
                        fixed: false, //不固定
                        maxmin: true,
                        content: "<?php echo url('Coupon/addcoupon'); ?>?coupon_id=",
                        cancel: function () {
                            table.reload('news', {});
                        }
                    });
                });
            }
            //第一个实例
            table.render({
                elem: '#demo'
                ,toolbar: '#toolbarDemo'
                ,defaultToolbar: ['filter']
                ,url: "<?php echo url('Coupon/getCouponList'); ?>" //数据接口
                ,page: true //开启分页
                ,limit:15
                ,limits:[15,20,30,40,50,60,70,80,90]
                ,id:'unassigned'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', minWidth:70, width:70, fixed: 'left'}
                    ,{field: 'log_id', title: '批次', width:100}
                    ,{field: 'coupon_code', title: '消费券码', width:120}
                    ,{field: 'coupon_name', title: '名称', width:130}
                    ,{field: 'commodity_id', title: '商家', minWidth:130}
                    ,{field: 'coupon_status', title: '消费券状态', width:130}
                    ,{field: 'real_name', title: '领取人', width:130}
                    ,{field: 'receive_started_at', title: '领取开始时间', width:170}
                    ,{field: 'receive_ended_at', title: '领取结束时间', width:170}
                    ,{field: 'use_started_at', title: '使用开始时间', width:170}
                    ,{field: 'use_ended_at', title: '使用开始时间', width:170}
                    ,{field: 'operating', title: '操作', minWidth:220,toolbar: '#barDemo'}
                ]],
                done: function(res, curr, count){
                    // 绑定表格工具栏按钮的触发事件
                    bindTableToolbarFunction();
                }
            });
            //监听性别操作
            form.on('switch(enable)', function(obj){
                var subject_id = this.value;
                var check  = obj.elem.checked;
                var status = check ? '1' : '0';
                // layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
                var msg = '确定' + (status == 1 ? '开启' : '关闭') + '该行业?';
                layui.use([ 'form','table'], function(){
                    $.ajax({
                        url: "<?php echo url('News/enable'); ?>",
                        type:'POST',
                        dataType: "json",
                        data:'subject_id='+subject_id+'&is_enable='+status,

                    });
                    // layer.close(index);
                });
            });

            table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

                if(layEvent === 'del'){ //删除
                    layer.confirm('确定要删除该数据吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('Activitylist/activitylist_del'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
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
                        window.location.reload();
                    });
                } else if(layEvent === 'edit'){
                    //编辑
                    // console.log(obj.data);
                    var url = "<?php echo url('Service/edit'); ?>?id="+obj.data.id;
                    layer.open({
                        type: 2,
                        title:'修改活动信息',
                        area: ['75%', '90%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('unassigned', {});
                        }
                    });
                } else if(layEvent === 'detail'){
                    // console.log(obj.data);
                    var url = "<?php echo url('Item/view2'); ?>?id="+obj.data.id;
                    layer.open({
                        type: 2,
                        title:'查看活动信息',
                        area: ['75%', '90%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('unassigned', {});
                        }
                    });
                } else if(layEvent === 'recommend'){ //删除
                    layer.confirm('确定要推荐该活动吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        // console.log(obj);
                        //向服务端发送删除指令
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('Activitylist/recommend_do'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐成功'
                                    });
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐失败'
                                    });
                                }
                            },
                        });
                        window.location.reload();
                    });
                }else if(layEvent === 'distribution'){
                    //编辑
                    // console.log(obj.data);
                    var url = "<?php echo url('Item/distribution'); ?>?id="+obj.data.id;
                    layer.open({
                        type: 2,
                        title:'分配志愿者',
                        area: ['55%', '80%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('unassigned', {});
                        }
                    });
                }else if(layEvent === 'view'){
                    //编辑
                    var url = "<?php echo url('Item/view2'); ?>?id="+obj.data.id;
                    layer.open({
                        type: 2,
                        title:'分配志愿者',
                        area: ['41%', '95%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('unassigned', {});
                        }
                    });
                }else if(layEvent === 'return'){ //删除
                    layer.confirm('确定要驳回吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('Item/return_do'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '驳回成功'
                                    });
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '驳回失败'
                                    });

                                }
                            },
                        });
                        window.location.reload();
                    });
                }
            });
        });


    </script>
    <script type="text/html" id="barDemo">
        <!--        <a class="layui-btn layui-btn-xs" lay-event="distribution">分配志愿者</a>-->
        <a class="layui-btn layui-btn-xs" lay-event="view">查看</a>
        <!--        <a class="layui-btn layui-btn-xs" lay-event="reject">上报</a>-->
        <!--        <a class="layui-btn layui-btn-danger layui-btn-xs"  lay-event="return">驳回</a>-->
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.member_avatar !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/member/{{ d.member_avatar }}" />
        {{# } else{ }}
        {{#  } }}
    </script>
</div>
</body>
</html>
