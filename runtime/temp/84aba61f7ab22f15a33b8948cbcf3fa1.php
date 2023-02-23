<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"F:\project\pointsmall\public/../application/admin\view\dmenu\index.html";i:1640144822;}*/ ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>用户管理|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"/>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"/>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
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
                <div class="layui-btn-container">
                    <button type="button" class="layui-btn layui-btn-sm" id="add">+ 添加</button>
                </div>
            </script>
            <table id="demo" lay-filter="test"></table>
        </div>
    </div>
    <script>
        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time1'
                , type: 'datetime'
            });
        });
        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time2'
                , type: 'datetime'
            });
        });

        layui.use('table', function () {
            var table = layui.table;
            var $ = layui.$, active = {
                reload: function () {
                    //执行重载
                    table.reload('dmenu', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        , where: {
                            keywords: $('#keywords').val(),
                        }
                    }, 'data');
                }
            };
            $('#find_news').on('click', function () {
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
            //第一个实例
            table.render({
                elem: '#demo'
                , toolbar: '#toolbarDemo'
                , defaultToolbar: ['filter']
                , url: "<?php echo url('Dmenu/getdemenu_list'); ?>" //数据接口
                , page: true //开启分页
                , limit: 15
                , limits: [15, 20, 30, 40, 50, 60, 70, 80, 90]
                , id: 'dmenu'
                , cols: [[ //表头
                //       {field:'checkbox', type:'checkbox',checked:'true', width:'5%'}
                     {field: 'ID', title: '编号', width: 100, }
                    , {field: 'MenuName', title: '菜单名称', toolbar: '#barDemoo', width: 250}
                    , {field: 'Description', title: '菜单说明', width: 250}
                    , {field: 'Username', title: '操作用户', width: 250}
                    , {field: 'Sortid', title: '排序ID', width: 250}
                    , {field: 'Dtime', title: '时间', width: 250, }
                    , {field: 'operating1', title: '操作', minWidth: 120, toolbar: '#barDemo'}
                ]],
                done: function(res, curr, count){
                    // 绑定表格工具栏按钮的触发事件
                    bindTableToolbarFunction();
                }
            });
            table.on('tool(test)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）
                if (layEvent === 'edit') {
                    var ID = obj.data.ID;
                    layer.open({
                        type: 2,
                        title: '修改下拉菜单信息',
                        shadeClose: true,
                        shade: false,
                        maxmin: true, //开启最大化最小化按钮
                        area: ['60%', '60%'],
                        content: "<?php echo url('dmenuedit'); ?>?ID=" + ID,
                        cancel: function(){
                            table.reload('dmenu', {});
                        }
                    });
                } else if (layEvent === 'del') {
                    layer.confirm('确定要删除该菜单吗？', function (index) {
                        var ID = obj.data.ID;
                        $.ajax({
                            url: "<?php echo url('Dmenu/del'); ?>",
                            dataType: 'json',
                            type: 'POST',
                            data: 'ID=' + ID,
                            success: function (data) {
                                console.log(data)
                                if (data.s == 'ok') {
                                    layer.open({
                                        title: '操作信息'
                                        , content: '删除成功'
                                    });
                                    table.reload('dmenu', {});
                                } else {
                                    layer.open({
                                        title: '操作信息'
                                        , content: data.s
                                    });
                                }
                            }
                        });
                    });
                }else if (layEvent === 'detail') {
                    console.log(obj.data);
                    if(obj.data.ID==72){
                        var url = "<?php echo url('Dmenu/detailsex'); ?>?id="+obj.data.ID;
                    }else if(obj.data.ID==109){
                        var url = "<?php echo url('Dmenu/detaildepartment'); ?>?id="+obj.data.ID;
                    }else if(obj.data.ID==112){
                        var url = "<?php echo url('Dmenu/detailservice'); ?>?id="+obj.data.ID;
                    }
                    layer.open({
                        type: 2,
                        title:'下拉菜单',
                        area: ['85%', '80%'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: url,
                        cancel: function(){
                            table.reload('dmenu', {});
                        }
                    });
                }
            });
        });
        function bindTableToolbarFunction() {
            var table = layui.table;
            $('#add').on("click", function () {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    area: ['950px', '560px'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('Dmenu/add'); ?>?ID=",
                    cancel: function () {
                        table.reload('dmenu', {});
                    }
                });
            });
        }
    </script>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="detail">查看</a>
        <a class="layui-btn  layui-btn-xs" lay-event="edit">修改</a>
       <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="barDemoo">
        <a class="detail_news" style="cursor: pointer" lay-event="detail">{{d.MenuName}}</a>
    </script>
    <script type="text/html" id="status">
        {{#  if(d.Status == '0'  ){ }}
        <i class="layui-icon layui-icon-ok" style="color: #009688;font-size: 20px"></i>
        {{# } else{ }}
        <i class="layui-icon layui-icon-close" style="color: #009688;font-size: 20px"></i>
        {{#  } }}
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.image !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/news/{{ d.image }}"/>
        {{# } else{ }}

        {{#  } }}
    </script>
</div>
</body>
</html>


