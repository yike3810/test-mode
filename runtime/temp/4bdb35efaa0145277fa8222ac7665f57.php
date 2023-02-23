<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"F:\project\pointsmall\public/../application/admin\view\industry\index.html";i:1636941641;}*/ ?>
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
            <div class="layui-row" style="margin-top:20px;" >
                <div class="layui-form-inline layui-col-md3">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" id="keywords" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-inline layui-col-md3">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-row">
        <div style="">
            <script type="text/html" id="toolbarDemo">
                <div class="layui-btn-container">
                    <button type="button" class="layui-btn layui-btn-sm" id="add">+ 添加</button>
                </div>
            </script>
            <table id="demo" lay-filter="test"></table>
        </div>
    </div>
    <script type="text/html" id="switchTpl">
        {{#  if(d.industry_id !== 1 ){ }}
        <!-- 这里的 checked 的状态只是演示 -->
        <input type="checkbox" name="enable" value="{{d.industry_id}}" lay-skin="switch" lay-text="已启用|未启用" lay-filter="enable" {{  d.is_enable== 1 ? 'checked' : '' }}>
        {{#  } }}
    </script>
    <script>
        layui.use([ 'form','table'], function(){
            var table = layui.table;
            var form = layui.form;
            form.render();
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    table.reload('industry', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            keywords: $('#keywords').val(),
                            publish_time1: $('#publish_time1').val(),
                            publish_time2: $('#publish_time2').val(),
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
                ,url: "<?php echo url('Industry/getIndustry_List'); ?>" //数据接口
                ,page: true //开启分页
                ,limit:15
                ,limits:[15,20,30,40,50,60,70,80,90]
                ,id:'industry'
                ,cols: [[ //表头
                    {field: 'industry_id', title: 'ID', minWidth:80, width: 80, fixed: 'left'}
                    ,{field: 'industry_name', title: '行业名称', width:400}
                    ,{field: 'add_time', title: '录入时间', width:395, }
                    ,{field: 'is_enable', title: '是否启用', width:390,templet: '#switchTpl', unresize: true}
                    ,{field: 'operating1', title: '操作', minWidth:200, width:400,toolbar: '#barDemo'}
                ]],
                done: function(res, curr, count){
                    // 绑定表格工具栏按钮的触发事件
                    bindTableToolbarFunction();
                }
            });
            //监听性别操作
            form.on('switch(enable)', function(obj){
                var industry_id = this.value;
                var check  = obj.elem.checked;
                var status = check ? '1' : '0';
                // layer.tips(this.value + ' ' + this.name + '：'+ obj.elem.checked, obj.othis);
                var msg = '确定' + (status == 1 ? '开启' : '关闭') + '该行业?';
                layui.use([ 'form','table'], function(){
                    $.ajax({
                        url: "<?php echo url('Industry/enable'); ?>",
                        type:'POST',
                        dataType: "json",
                        data:'industry_id='+industry_id+'&is_enable='+status,
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

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm('确定要删除该数据吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        console.log(obj);
                        //向服务端发送删除指令
                        var id	=	obj.data.news_id;
                        $.ajax({
                            url:"<?php echo url('Industry/newsdel'); ?>",
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
                            }
                        });
                    });
                } else if(layEvent === 'edit'){
                    //编辑
                    console.log(obj.data);
                    var url = "<?php echo url('Industry/edit'); ?>?id="+obj.data.industry_id;
                    layer.open({
                        type: 2,
                        title:'修改',
                        area: ['40%', '80%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('industry', {});
                        }
                    });
                } else if(layEvent === 'tijiao'){
                    layer.confirm('确定要提交该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/tijiao'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
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
                }else if(layEvent === 'tuijian'){
                    layer.confirm('确定要推荐该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/tuijian'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐成功'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐失败'
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
            $('#add').on("click",function() {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    area: ['40%', '80%'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('Industry/add'); ?>?sid=",
                    cancel: function(){
                        table.reload('news', {});
                    }
                });
            });
            $('#add_image').on("click",function() {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    area: ['90%', '90%'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('News/addimage'); ?>?sid=",
                    cancel: function(){
                        table.reload('news', {});
                    }
                });
            });
            $('#add_video').on("click",function() {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    area: ['90%', '90%'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('News/addvideo'); ?>?sid=",
                    cancel: function(){
                        table.reload('news', {});
                    }
                });
            });
        }
    </script>
    <script type="text/html" id="xuhao">
        {{d.LAY_TABLE_INDEX+1}}
    </script>

    <script type="text/html" id="barDemo">
        {{#  if(d.industry_id !== 1 ){ }}
        <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
        {{#  } }}
<!--        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>-->
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.image !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/news/{{ d.image }}" />
        {{# } else{ }}

        {{#  } }}
    </script>
</div>
</body>
</html>
