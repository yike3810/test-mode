<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"F:\project\pointsmall\public/../application/admin\view\competence\add.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>添加新权限|<?php echo config('system.title'); ?></title>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"/>
    <script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/ueditor/ueditor.all.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
    <script type="text/javascript" src="/static/js/Public.js"></script>
    <script type="text/javascript"></script>
</head>
<body>
<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form layui-col-md10" id="activity" enctype="multipart/form-data">

        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>权限分类：</label>
            <div class="layui-input-block">
                <select name="sid" id="sid" lay-filter="aihao">
                    <option value="0">顶级目录</option>
                    <?php if(is_array($volist) || $volist instanceof \think\Collection || $volist instanceof \think\Paginator): if( count($volist)==0 ) : echo "" ;else: foreach($volist as $key=>$vo): ?>
                    <option value="<?php echo $vo['ID']; ?>"><?php echo $vo['Cname']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>权限名称：</label>
            <div class="layui-input-block">
                <input type="text" name="cname" required  lay-verify="required" placeholder="输入如：用户管理" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text" >
            <label class="layui-form-label"><i class="required">*</i>权限说明：</label>
            <div class="layui-input-block">
                <textarea class="layui-textarea " name="description" lay-verify="content" id="LAY_demo_editor"></textarea>
            </div>
        </div>

        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="锁定">
                <input type="radio" name="status" value="0" title="正常">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn sub" lay-submit lay-filter="sub" id="sub" >提交</button>
            </div>
        </div>
    </form>
</div>
<style>
    .layui-form-label{
        width: 140px;
        margin-bottom: -80px;
    }
    .layui-input-block {
        margin-left:160px;
        min-height: 36px
    }
    .required {
        color: red;
        padding-right: 5px;
    }
    </style>
<script>
    //Demo
    layui.use('form', function () {
        var form = layui.form;
        form.render();
        //监听提交
        form.on('submit(sub)', function (data) {
            console.log(data.field);
            $.ajax({
                url: "<?php echo url('cadd_do'); ?>",
                dataType: 'json',
                type: 'POST',
                data: data.field,
                success: function (data)
                {
                    if (data.s=='ok') {
                        layer.msg("添加成功",{},function(){
                        });
                    } else {
                        layer.msg("添加失败");
                    }
                }

            });
            return false;
        });
    });
</script>

</body>
</html>
