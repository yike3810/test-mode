<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\project\pointsmall\public/../application/admin\view\system\ipadd.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>添加用户|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/Public.js"></script>
    <script type="text/javascript" src="/static/js/winpop.js"></script>
    <script type="text/javascript" src="/static/js/check.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>

</head>
<body>
<div id="content">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">IP地址：</label>
            <div class="layui-input-inline">
                <input type="text" name="IP" required  lay-verify="required" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">* 输入如：202.20.1.0</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label time-form-label">开始日期：</label>
            <div class="layui-input-inline dform-label">
                <input type="text" name="StartTime" id="StartTime" required lay-verify="required"  autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">* 选择日期</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结束日期：</label>
            <div class="layui-input-inline">
                <input type="text" name="EndTime" id="EndTime" required  lay-verify="required"  autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">* 选择日期 </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">永久封禁：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="0" title="否"checked>
                <input type="radio" name="status" value="1" title="是">
            </div>
        </div>
        <div class="layui-form-item layui-form-text layui-col-md5">
            <label class="layui-form-label">描述说明：</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入内容" maxlength="30" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo">提交</button>
            </div>
        </div>
    </form>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
        laydate.render({
            elem: '#StartTime'
            ,type: 'date' //默认，可不填
        });
        });
        layui.use('laydate', function(){
            var laydate = layui.laydate;
        laydate.render({
            elem: '#EndTime'
            ,type: 'date' //默认，可不填
        });
        });
        layui.use('form', function(){
            var form = layui.form;
            form.render();
            //监听提交
            form.on('submit(formDemo)', function(data){
                $.ajax({
                    url:"<?php echo url('ipadd_do'); ?>",
                    dataType:'json',
                    type:'POST',
                    data:data.field,
                    success: function(data) {
                        if (data.s=='ok') {
                            layer.msg("添加成功",{},function(){
                            });
                        }else {
                            layer.msg("添加失败");
                        }
                    }
                });
                return false;
            });
        });
    </script>
</div>
</body>
</html>
