<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"F:\project\pointsmall\public/../application/admin\view\goodsclass\edit.html";i:1640144821;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>修改信息</title>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <!-- 配置文件 -->
    <script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/static/ueditor/ueditor.all.js"></script>
    <style>
        .layui-input {
            width: 60%;
        }
        .required {
            color: red;
            padding-right: 5px;
        }
    </style>
</head>
<body>
<form class="layui-form layui-col-md11" id="activity" enctype="multipart/form-data" style="margin-top: 30px">
    <div class="layui-form-item">

    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 100px"><i class="required">*</i>商品类别名称</label>
        <div class="layui-input-block">
            <input type="text" name="category_name" id="category_name" value="<?php echo $result['category_name']; ?>" placeholder="请输入" autocomplete="off" class="layui-input" lay-verify="required">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"  style="width: 100px;text-align: center">是否启用</label>
        <div class="layui-input-block">
            <input type="radio" name="is_enable" value="1" title="是" <?php if(1 == $result['is_enable']): ?>checked<?php endif; ?>>
            <input type="radio" name="is_enable" value="0" title="否" <?php if(0 == $result['is_enable']): ?>checked<?php endif; ?>>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <input type="hidden" name="attachment_info" id="attachment_info">
            <input type="hidden" name="category_id" id="id" value="<?php echo $result['category_id']; ?>">
            <button class="layui-btn sub" lay-submit lay-filter="sub" id="sub" style="margin-left: 20px">保存</button>
        </div>
    </div>
    <!-- 更多表单结构排版请移步文档左侧【页面元素-表单】一项阅览 -->
</form>
<script src="/static/layui/layui.all.js"></script>
<script>
    layui.use('form', function () {
        var form = layui.form;
        form.render();
        //监听提交
        form.on('submit(sub)', function (data) {
            console.log(data.field);
            $.ajax({
                url: "<?php echo url('edit_do'); ?>",
                dataType: 'json',
                type: 'POST',
                data: data.field,
                success: function (data)
                {
                    if (data.s=='ok') {
                        layer.msg("修改成功",{time: 600},function(){
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.location.reload();
                            parent.layer.close(index); //再执行关闭
                        });
                    } else {
                        layer.msg("该商品已存在");
                    }
                }

            });
            return false;
        });
    });
</script>
</body>
</html>
