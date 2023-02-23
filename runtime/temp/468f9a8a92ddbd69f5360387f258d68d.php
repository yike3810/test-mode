<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\role\index.html";i:1640144822;}*/ ?>
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
                <button type="button" class="layui-btn" id="roleadd">+添加</button>
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
                    table.reload('role', {
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
            function bindTableToolbarFunction() {
                var table = layui.table;
                $('#roleadd').on("click", function () {
                    //iframe层-父子操作
                    layer.open({
                        type: 2,
                        title: '添加角色',
                        area: ['1000px', '560px'],
                        fixed: false, //不固定
                        maxmin: true,
                        content: "<?php echo url('Role/roleadd'); ?>?ID=",
                        cancel: function () {
                            table.reload('role', {});
                        }
                    });
                });
            }
            //第一个实例
            table.render({
                elem: '#demo'
                , toolbar: '#toolbarDemo'
                , defaultToolbar: ['filter']
                , url: "<?php echo url('Role/getRole_list'); ?>" //数据接口
                , page: true //开启分页
                , limit: 15
                , limits: [15, 20, 30, 40, 50, 60, 70, 80, 90]
                , id: 'role'
                , cols: [[ //表头
                    {field: 'ID', title: '编号', width: 200, fixed: 'left'}
                    , {field: 'Rolename', title: '角色名称', width: 200}
                    , {field: 'Description', title: '角色说明', width: 400}
                    , {field: 'Status', title: '状态', width: 150, templet: '#status'}
                    , {field: 'Dtime', title: '日期', width: 200}
                    , {field: 'operating1', title: '操作', minWidth: 200, toolbar: '#barDemo'}
                ]],
                done: function (res, curr, count) {
                    // 绑定表格工具栏按钮的触发事件
                    bindTableToolbarFunction();
                }
            });
            table.on('tool(test)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）
                if (layEvent === 'roleedit') {
                    var ID = obj.data.ID;
                    layer.open({
                        type: 2,
                        title: '修改角色信息',
                        shadeClose: true,
                        shade: false,
                        maxmin: true, //开启最大化最小化按钮
                        area: ['60%', '60%'],
                        content: "<?php echo url('Role/roleedit'); ?>?ID=" + ID,
                        cancel: function () {
                            table.reload('role', {});
                        }
                    });
                } else if (layEvent === 'roledel') {
                    layer.confirm('确定要删除该角色吗？', function (index) {
                        var ID = obj.data.ID;
                        $.ajax({
                            url: "<?php echo url('Role/roledel'); ?>",
                            dataType: 'json',
                            type: 'POST',
                            data: 'ID=' + ID,
                            success: function (data) {
                                console.log(data)
                                if (data.s == 'ok') {
                                    layer.open({
                                        title: '操作信息'
                                        , content: '删除角色成功'
                                    });
                                    table.reload('role', {});
                                } else {
                                    layer.open({
                                        title: '操作信息'
                                        , content: data.s
                                    });
                                }
                            }
                        });
                    });
                }
            });
        });
    </script>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="roleedit">修改</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="roledel">删除</a>
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
