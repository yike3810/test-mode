<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"F:\project\pointsmall\public/../application/admin\view\module\moduleadd.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>添加新模块|<?php echo config('system.title'); ?></title>
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
            <label class="layui-form-label"><i class="required">*</i>模块名称：</label>
            <div class="layui-input-block">
                <input type="text" name="mname" required  lay-verify="required" placeholder="请输入模块名称，如：系统管理" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>目录分类：</label>
            <div class="layui-input-block">
                <select name="sid" id="sid" lay-filter="aihao">
                    <option value="0">顶级目录</option>
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$l): ?>
                    <option value="<?php echo $l['ID']; ?>"><?php if($l['html'] != ''): ?>└<?php endif; ?><?php echo $l['html']; ?><?php echo $l['ModuleName']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>链接地址：</label>
            <div class="layui-input-block">
                <input type="text" name="url" required  lay-verify="required" placeholder="请填写链接地址" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text" >
            <label class="layui-form-label"><i class="required">*</i>描述说明：</label>
            <div class="layui-input-block">
                <textarea class="layui-textarea " name="description" lay-verify="content" id="LAY_demo_editor"></textarea>
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>排序ID：</label>
            <div class="layui-input-block">
                <input type="text" name="msort" required  lay-verify="required" placeholder="排序ID" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>权限绑定：</label>
            <div class="layui-input-block">
                <select name="Competence" id="Competence" lay-filter="aihao">
                    <option value="0">顶级目录</option>
                    <?php if(is_array($competence_list) || $competence_list instanceof \think\Collection || $competence_list instanceof \think\Paginator): if( count($competence_list)==0 ) : echo "" ;else: foreach($competence_list as $key=>$l): ?>
                    <option value="<?php echo $l['ID']; ?>"><?php echo $l['Cname']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item" >
            <label class="layui-form-label"><i class="required">*</i>状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="未启用">
                <input type="radio" name="status" value="0" title="启用">
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
                url: "<?php echo url('module_add_do'); ?>",
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
