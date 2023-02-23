<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"F:\project\pointsmall\public/../application/admin\view\order\order_detail.html";i:1640334144;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"/>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script src="/static/layui/layui.js" charset="utf-8"></script>
    <script src="/static/js/jquery-3.4.1.min.js" charset="utf-8"></script>
</head>
<body>
<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form" lay-filter="MemberEedit" method="post">
<!--        <div class="layui-form-item">-->
<!--            <label class="layui-form-label">kkkk订单ID：</label>-->
<!--            <div class="layui-input-block">-->
<!--                <input type="text" name="order_id" value="<?php echo $list['order_id']; ?>" placeholder="<?php echo $list['order_id']; ?>"-->
<!--                       id="order_idorder_id" readonly="readonly" class="layui-input"/>-->
<!--            </div>-->
<!--        </div>-->
        <div class="layui-form-item">
            <label class="layui-form-label">订单编号：</label>
            <div class="layui-input-block">
                <input type="text" name="order_sn" value="<?php echo $list['order_sn']; ?>" placeholder="<?php echo $list['order_sn']; ?>"
                       id="order_sn" readonly="readonly" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">会员名：</label>
<!--            <div class="layui-input-block">-->
<!--                <input type="text" name="order_sn" value="<?php echo $list['member_name']; ?>" placeholder=""-->
<!--                       id="" readonly="readonly" class="layui-input"/>-->
<!--            </div>-->
            <div class="layui-input-block">
                <input type="text" name="order_sn" value="<?php echo $list['phone']; ?>" placeholder=""
                       id="" readonly="readonly" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">收货人姓名：</label>
            <div class="layui-input-block">
                <input type="text" name="consignee" value="<?php echo $list['consignee']; ?>" placeholder="<?php echo $list['consignee']; ?>"
                       id="consignee" readonly="readonly" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">收货人手机号：</label>
            <div class="layui-input-block">
                <input type="text" name="phone" value="<?php echo $list['phone']; ?>" placeholder="<?php echo $list['phone']; ?>" id="phone"
                       readonly="readonly"    class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">所在地区：</label>
            <div class="layui-input-block">
                <input type="text" name="region_name" readonly="readonly" value="<?php echo $list['location']; ?>" disabled class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">详细地址：</label>
            <div class="layui-input-block">
                <input type="text" name="address" readonly="readonly" value="<?php echo $list['address']; ?>" placeholder="<?php echo $list['address']; ?>" id="address"
                       class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">兑换商品：</label>
            <div class="layui-input-block">
                <input type="text" name="goods_name" value="<?php echo $list['goods_name']; ?>" placeholder="<?php echo $list['goods_name']; ?>" id="goods_name"
                       readonly="readonly"     class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品类型：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['type_name']; ?>"
                       readonly="readonly"      placeholder="<?php echo $list['type_name']; ?>"  class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">兑换数量：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['goods_num']; ?>" placeholder="<?php echo $list['goods_num']; ?>"
                       readonly="readonly"   class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">消耗积分：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['all_point']; ?>" placeholder="<?php echo $list['all_point']; ?>"
                       readonly="readonly"   class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">下单时间：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['add_time']; ?>" placeholder="<?php echo $list['add_time']; ?>"
                       readonly="readonly"   class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">订单状态：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['order_status']; ?>" placeholder="<?php echo $list['order_status']; ?>"
                       readonly="readonly"    class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">付款状态：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['pay_status']; ?>" placeholder="<?php echo $list['pay_status']; ?>"
                       readonly="readonly"    class="layui-input"/>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">发货状态：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['shipping_status']; ?>" placeholder="<?php echo $list['shipping_status']; ?>"
                       readonly="readonly"   class="layui-input"/>
            </div>
        </div>
        <?php if($list['shipping_status'] == '已发货'): ?>
        <div class="layui-form-item">
            <label class="layui-form-label">发货时间：</label>
            <div class="layui-input-block">
                <input type="text" name="" value="<?php echo $list['shipping_time']; ?>" placeholder="<?php echo $list['shipping_time']; ?>"
                       readonly="readonly"   class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">快递公司：</label>
            <div class="layui-input-block">
                <input type="text" name="shipping_name" value="<?php echo $list['shipping_name']; ?>"
                       readonly="readonly"  placeholder="<?php echo $list['shipping_name']; ?>" id="shipping_name" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">快递单号：</label>
            <div class="layui-input-block">
                <input type="text" name="tracking_number" value="<?php echo $list['tracking_number']; ?>"
                       readonly="readonly"  placeholder="<?php echo $list['tracking_number']; ?>" id="tracking_number" class="layui-input"/>
            </div>
        </div>
        <?php else: endif; ?>
    </form>
</div>
<style>
    .layui-form-label {
        width: 120px;
        margin-bottom: -80px;
    }

    .layui-input-block {
        width: 600px;
        margin-left: 160px;
        min-height: 36px
    }
</style>
<script>
    layui.use(['form', 'laydate'], function () {
        var form = layui.form, laydate = layui.laydate;
        form.render();
        laydate.render({
            elem: '#date'
        });
    });
</script>
</body>
</html>
