<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"F:\project\pointsmall\public/../application/admin\view\order\order_fahuo.html";i:1640334144;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/layui/css/layui.css"  media="all">
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <script type="text/javascript" src="/static/js/common.js"></script>
</head>
<body>
<form class="layui-form" lay-filter="MemberEedit" method="post" style="margin-top:25px;">
    <div class="layui-form-item">
        <label class="layui-form-label">快递公司：</label>
        <div class="layui-input-block">
            <select lay-filter="shipping_id" name="shipping_id">
                <option value="1" <?php if($list['shipping_id'] == '1'): ?>selected="selected"<?php endif; ?>>圆通</option>
                <option value="2" <?php if($list['shipping_id'] == '2'): ?>selected="selected"<?php endif; ?>>申通</option>
                <option value="3" <?php if($list['shipping_id'] == '3'): ?>selected="selected"<?php endif; ?>>顺丰</option>
                <option value="4" <?php if($list['shipping_id'] == '4'): ?>selected="selected"<?php endif; ?>>韵达</option>
                <option value="5" <?php if($list['shipping_id'] == '5'): ?>selected="selected"<?php endif; ?>>中通</option>
                <option value="6" <?php if($list['shipping_id'] == '6'): ?>selected="selected"<?php endif; ?>>EMS</option>
                <option value="7" <?php if($list['shipping_id'] == '7'): ?>selected="selected"<?php endif; ?>>百世</option>
                <option value="8" <?php if($list['shipping_id'] == '8'): ?>selected="selected"<?php endif; ?>>德邦</option>
                <option value="9" <?php if($list['shipping_id'] == '9'): ?>selected="selected"<?php endif; ?>>京东</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item" >
        <label class="layui-form-label">快递单号：</label>
        <div class="layui-input-block">
            <input type="text" name="tracking_number" value="<?php echo $list['tracking_number']; ?>" placeholder="<?php echo $list['tracking_number']; ?>" id="tracking_number" class="layui-input"/>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" id="member_subit" lay-submit="" lay-filter="member_subit">确定发货</button>
        </div>
    </div>
    <input type="hidden" name="order_id" id="order_id" value="<?php echo $list['order_id']; ?>">
</form>
<style>
    .layui-form-label{
        width: 120px;
        margin-bottom: -80px;
    }
    .layui-input-block {
        width: 400px;
        margin-left:160px;
        min-height: 36px
    }
</style>
<script src="/static/layui/layui.js" charset="utf-8"></script>
<script src="/static/js/jquery-3.4.1.min.js" charset="utf-8"></script>
<script>

    layui.use('form', function(){
        var form = layui.form,layer = layui.layer
        form.render();

        form.on('submit(member_subit)', function (data) {
            console.log(data.field);
            $.ajax({
                url: "<?php echo url('Order/deliver_goods'); ?>",
                dataType: 'json',
                type: 'POST',
                data: data.field,
                success: function (data)
                {
                    if (data.s=='ok') {
                        layer.msg("发货成功",{time: 600},function(){
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            parent.location.reload();
                            parent.layer.close(index); //再执行关闭
                        });
                    } else {
                        layer.msg("发货失败");
                    }
                }

            });
            return false;
        });
    });
</script>

</body>
</html>
