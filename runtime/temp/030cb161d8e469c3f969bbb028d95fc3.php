<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"F:\project\pointsmall\public/../application/admin\view\order\index_edit.html";i:1640252241;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('system.title'); ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/layui/css/layui.css"  media="all">
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script src="/static/layui/layui.js" charset="utf-8"></script>
    <script src="/static/js/jquery-3.4.1.min.js" charset="utf-8"></script>
</head>
<body>
<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form" lay-filter="MemberEedit" method="post">
        <div class="layui-form-item">
            <label class="layui-form-label">订单ID：</label>
            <div class="layui-input-block">
                <input type="text" name="order_id" value="<?php echo $list['order_id']; ?>" placeholder="<?php echo $list['order_id']; ?>" id="order_idorder_id" readonly="readonly" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">订单编号：</label>
            <div class="layui-input-block">
                <input type="text" name="order_sn" value="<?php echo $list['order_sn']; ?>" placeholder="<?php echo $list['order_sn']; ?>" id="order_sn" readonly="readonly" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">收货人姓名：</label>
            <div class="layui-input-block">
                <input type="text" name="consignee" value="<?php echo $list['consignee']; ?>" placeholder="<?php echo $list['consignee']; ?>" id="consignee"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">收货人手机号：</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="<?php echo $list['phone']; ?>" placeholder="<?php echo $list['phone']; ?>" id="phone"  class="layui-input"/>
            </div>
        </div>
<!--        <div class="layui-form-item">-->
<!--            <label class="layui-form-label">付款状态：</label>-->
<!--            <div class="layui-input-block">-->
<!--                <input type="text" name="pay_status" value="<?php echo $list['pay_status']; ?>" placeholder="<?php echo $list['pay_status']; ?>" id="pay_status"  class="layui-input"/>-->
<!--            </div>-->
<!--        </div>-->
        <div class="layui-form-item">
            <label class="layui-form-label">所在地区：</label>
            <div class="layui-input-inline" style="margin-left:160px;">
                <select id="province" name="province" lay-filter="province">
                    <option value="">请选择</option>
                    <?php if(is_array($province_list) || $province_list instanceof \think\Collection || $province_list instanceof \think\Paginator): $i = 0; $__LIST__ = $province_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['region_id'] == $list['province_id']): ?>
                        <option selected value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select id="city" name="city" lay-filter="city">
                    <?php if($city_list != ''): if(is_array($city_list) || $city_list instanceof \think\Collection || $city_list instanceof \think\Paginator): $i = 0; $__LIST__ = $city_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['region_id'] == $list['city_id']): ?>
                            <option selected value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                            <?php else: ?>
                            <option value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                            <?php endif; endforeach; endif; else: echo "" ;endif; else: ?>
                        <option value="">请选择</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select id="district" name="district" lay-filter="district">
                    <?php if($district_list != ''): if(is_array($district_list) || $district_list instanceof \think\Collection || $district_list instanceof \think\Paginator): $i = 0; $__LIST__ = $district_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['region_id'] == $list['district_id']): ?>
                            <option selected value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                            <?php else: ?>
                            <option value="<?php echo $vo['region_id']; ?>"><?php echo $vo['region_name']; ?></option>
                            <?php endif; endforeach; endif; else: echo "" ;endif; else: ?>
                    <option value="">请选择</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">详细地址：</label>
            <div class="layui-input-block">
                <input type="text" name="address" value="<?php echo $list['address']; ?>" placeholder="<?php echo $list['address']; ?>" id="address"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">快递公司：</label>
            <div class="layui-input-inline" style="margin-left:160px;">
                <select id="shipping_id" name="shipping_id" lay-filter="shipping_id">
                    <?php if(is_array($courier_company) || $courier_company instanceof \think\Collection || $courier_company instanceof \think\Paginator): $i = 0; $__LIST__ = $courier_company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['shipping_id'] == $list['shipping_id']): ?>
                    <option selected value="<?php echo $vo['shipping_id']; ?>"><?php echo $vo['shipping_name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $vo['shipping_id']; ?>"><?php echo $vo['shipping_name']; ?></option>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">快递单号：</label>
            <div class="layui-input-block">
                <input type="text" name="tracking_number" value="<?php echo $list['tracking_number']; ?>" placeholder="<?php echo $list['tracking_number']; ?>" id="tracking_number"  class="layui-input"/>
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" id="member_subit" lay-submit="" lay-filter="member_subit">提交</button>
            </div>
        </div>
    </form>
</div>
<style>
    .layui-form-label{
        width: 120px;
        margin-bottom: -80px;
    }
    .layui-input-block {
        width: 600px;
        margin-left:160px;
        min-height: 36px
    }
    .layui-input-inline {
        width: 200px;
        min-height: 36px
    }
</style>
<script>
    layui.use(['form', 'layedit'], function(){
        var form = layui.form
            ,layer = layui.layer
        form.render();

        form.on('submit(member_subit)', function (data) {
            console.log()
            $.ajax({
                url: "<?php echo url('Order/index_edit_do'); ?>",
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
                        layer.msg("修改失败");
                    }
                }
            });
            return false;
        });
        form.on('select(province)',function (data) {
            var id = data.value;
            $("#city").empty();
            $("#district").empty();
            form.render("select");
            if (id != ''){
                $.ajax({
                        url:"<?php echo url('Order/get_area_list'); ?>",
                        dataType:'json',
                        type:'POST',
                        data:'id='+id,
                        success: function (data) {
                            if (data.status == 1){
                                $("#city").append(data.data);
                                form.render('select')
                            }
                        }
                    }
                )
            }
        });
        form.on('select(city)',function (data) {
            var id = data.value;
            $("#district").empty();
            form.render("select");
            if (id != ''){
                $.ajax({
                        url:"<?php echo url('Order/get_area_list'); ?>",
                        dataType:'json',
                        type:'POST',
                        data:'id='+id,
                        success: function (data) {
                            if (data.status == 1){
                                $("#district").append(data.data);
                                form.render('select')
                            }
                        }
                    }
                )
            }
        });
    });
</script>
</body>
</html>
