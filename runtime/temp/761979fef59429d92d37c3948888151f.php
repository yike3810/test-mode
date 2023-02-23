<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\goods\tree.html";i:1667640708;}*/ ?>
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
    <style>
        .detail_news:hover{
            cursor:pointer;
        }
    </style>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div style="margin-top:20px;">
            <form class="layui-form" style="">
                <div class="layui-row" style="margin-top:20px;">
                    <div class="layui-form-inline layui-col-md3">
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-block">
                            <input type="text" name="keywords" id="keywords" placeholder="请输入" autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-inline layui-col-md3">
                        <div class="layui-input-block">
                            <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                        </div>
                    </div>
                </div>
            </form>
            <script type="text/html" id="toolbarDemo">
                <button type="button" class="layui-btn" id="addgoods">+添加</button>
            </script>

            <table id="demo" lay-filter="test"></table>
        </div>
    </div>
    <script type="text/html" id="switchTpl">
        <!-- 这里的 checked 的状态只是演示 -->
        {{#  if(d.goods_state == "审核通过" ){ }}
        <input type="checkbox" name="goods_show" value="{{d.goods_id}}" lay-skin="switch"
               lay-text="上架|下架" lay-filter="goods_show" {{d.goods_show== 1 ? 'checked' : '' }}>
        {{# } else{ }}
        <input type="checkbox" name="goods_show" lay-skin="switch" lay-text="上架|下架" disabled>
        {{#  } }}

    </script>
        <script>
            layui.use(['form','table'], function(){
            var table = layui.table;
            var form = layui.form;
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    table.reload('news', {
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

                function bindTableToolbarFunction() {
                    var table = layui.table;
                    $('#addgoods').on("click", function () {
                        //iframe层-父子操作
                        layer.open({
                            type: 2,
                            area: ['1000px', '650px'],
                            title: '添加商品',
                            fixed: false, //不固定
                            maxmin: true,
                            content: "<?php echo url('Goods/addgoods'); ?>?goods_id=",
                            cancel: function () {
                                table.reload('news', {});
                            }
                        });
                    });
                }

            //第一个实例
            table.render({
                elem: '#demo'
                , toolbar: '#toolbarDemo'
                , defaultToolbar: ['filter']
                ,url: "<?php echo url('Goods/getdfhgoodslist'); ?>" //数据接口
                ,page: true //开启分页
                ,limit:15
                ,limits:[15,20,30,40,50,60,70,80,90]
                ,id:'news'
                ,cols: [[ //表头
                     {field: 'goods_id', title: 'ID',  width:80, fixed: 'left'}
                    ,{field: 'goods_name', title: '商品名称', width:120,templet: '#goodsImage'}
                    ,{field: 'goods_price', title: '原价', width:80}
                    ,{field: 'goods_points', title: '积分', width:70}
                    ,{field: 'goods_commend', title: '是否推荐', width:90}
                    ,{field: 'goods_image', title: '图片', width:100,templet: '#imageTpl'}
                    ,{field: 'goods_storage', title: '库存数', width:90}
                    ,{field: 'goods_show', title: '上架', width:120,templet: '#switchTpl', unresize: true}
                    ,{field: 'add_time', title: '添加时间', width:180}
                    ,{field: 'goods_islimit', title: '是否限制', width:100}
                    ,{field: 'goods_limitnum', title: '兑换数量', width:90}
                    ,{field: 'type', title: '类型', width:80}
                    ,{field: 'category_name', title: '商品分类', width:100}
                    ,{field: 'barcode', title: '条码', width:100}
                    ,{field: 'goods_state', title: '状态', width:100}
                    ,{field: 'operating1', title: '操作', minWidth:230,toolbar: '#barDemo'}
                ]],
                done: function(res, curr, count){
                    // 绑定表格工具栏按钮的触发事件
                    bindTableToolbarFunction();
                }
            });

            form.on('switch(goods_show)', function(obj){
                    var goods_id = this.value;
                    var check  = obj.elem.checked;
                    var status = check ? '1' : '0';
                    // layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
                    var msg = '确定' + (status == 1 ? '上架' : '下架') + '该商品?';
                    layui.use([ 'form','table'], function(){
                        $.ajax({
                            url: "<?php echo url('Goods/enable'); ?>",
                            type:'POST',
                            dataType: "json",
                            data:'goods_id='+goods_id+'&goods_show='+status,
                            success: function(data) {
                                if (data.s=='ok') {

                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: data.s
                                    });
                                }
                            }
                        });
                        // layer.close(index);
                    });
            });

            table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）
                if(layEvent === 'del'){
                    layer.confirm('确定要删除该商品吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        //向服务端发送删除指令
                        var goods_id	=	obj.data.goods_id;
                        $.ajax({
                            url:"<?php echo url('Goods/goods_del'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'goods_id='+goods_id,
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
                            }
                        });
                    });
                }else if(layEvent === 'edit') {
                    //编辑
                   console.log(obj.data.goods_id);
                   var url = "<?php echo url('Goods/edit'); ?>?goods_id="+obj.data.goods_id;
                   layer.open({
                       type: 2,
                       title:"修改商品信息",
                       area: ['90%', '90%'],
                       fixed: false, //不固定
                       maxmin: true,
                       content: url,
                       cancel: function(){
                           table.reload('news', {});
                       }
                   });
               }else if(layEvent === 'detailimage'){
                    console.log(obj.data.goods_id);
                    var url = "<?php echo url('Goods/detailimage'); ?>?goods_id="+obj.data.goods_id;
                    layer.open({
                        type: 2,
                        title:"查看商品详情",
                        area: ['60%', '80%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('news', {});
                        }
                    });
                }else if(layEvent === 'reject'){
                    layer.confirm('确定要驳回该商品吗？', function(index){
                        var goods_id	=	obj.data.goods_id;
                        $.ajax({
                            url:"<?php echo url('Goods/reject'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'goods_id='+goods_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '撤回成功'
                                    });
                                    obj.update({
                                        status_name: '已驳回'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '撤回失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'submit'){
                    layer.confirm('确定要提交该商品吗？', function(index){
                        var goods_id	=	obj.data.goods_id;
                        $.ajax({
                            url:"<?php echo url('Goods/submit'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'goods_id='+goods_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '提交成功'
                                    });
                                    obj.update({
                                        status_name: '已提交'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '提交失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'approved'){
                    layer.confirm('确定审核通过该商品吗？', function(index){
                        var goods_id	=	obj.data.goods_id;
                        $.ajax({
                            url:"<?php echo url('Goods/approved'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'goods_id='+goods_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '审核通过'
                                    });
                                    obj.update({
                                        status_name: '审核通过'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '审核通过失败'
                                    });
                                }
                            }
                        });
                    });
                }

            });
        });
            function bindTableToolbarFunction() {
            var table = layui.table;
            $('#addgoods').on("click",function() {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    area: ['60%', '50%'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('Goods/addgoods'); ?>?goods_id=",
                    cancel: function(){
                        table.reload('news', {});
                    }
                });
            });
        }
    </script>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
        {{#  if(d.goods_state == "未提交" ){ }}
        <a class="layui-btn layui-btn-xs" lay-event="submit">提交</a>
        {{#  } }}
        {{#  if(d.goods_state == "已提交" ){ }}
        <a class="layui-btn layui-btn-xs" lay-event="approved">审核通过</a>
        {{#  } }}
        {{#  if(d.goods_state == "审核通过" ){ }}
        <a class="layui-btn layui-btn-xs" lay-event="reject">撤回</a>
        {{#  } }}


    </script>
    <script type="text/html" id="goodsImage">
        <a class="detail_news" lay-event="detailimage">{{d.goods_name}}</a>
    </script>
    <script type="text/html" id="xuhao">
        {{d.LAY_TABLE_INDEX+1}}
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.goods_image !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/goods/{{d.goods_image}}" />
        {{# } else{ }}
        {{#  } }}
    </script>
</div>
</body>
</html>
