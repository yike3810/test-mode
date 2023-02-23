<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"F:\project\consumer-coupon\public/../application/admin\view\index\main.html";i:1675731563;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo config('service.service_title'); ?></title>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <style>
        .layui-header .layui-logo{
            width: 280px;
            font-size: 18px;
        }
    </style>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <a href="<?php echo url('Member/index'); ?>" target="c"><div class="layui-logo"><?php echo config('service.service_title'); ?></div></a>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="/uploads/member/<?php echo session('Service.service_avatar'); ?>"
                         <?php if($app_name == 'jcq-wenming'): ?>
                         onerror="javascript:this.src='/static/image/jinchuan.png';" class="layui-nav-img">
                    <?php else: ?>
                    onerror="javascript:this.src='/static/image/default_avatar.png';" class="layui-nav-img">
                    <?php endif; ?>
                    <?php echo session('Service.service_name'); ?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a  href="javascript:;" id="uedit">基本资料</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="<?php echo url('Index/quit'); ?>">退出</a></li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <li class="layui-nav-item layui-nav-itemed">
                    <a class="" href="<?php echo url('Commodity/index'); ?>" target="c">商家管理</a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?php echo url('Commodity/index'); ?>" target="c">商家列表</a></dd>

                    </dl>
                </li>
                <li class="layui-nav-item layui-nav-itemed">
                    <a href="<?php echo url('Coupon/index'); ?>" target="c">消费券管理</a>
                    <dl class="layui-nav-child">
                        <dd class="layui-this"><a href="<?php echo url('Coupon/index'); ?>" target="c">全部券</a></dd>
                        <dd><a href="<?php echo url('Coupon/received'); ?>" target="c">领取记录</a></dd>
                        <dd><a href="<?php echo url('Coupon/used'); ?>" target="c">使用记录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-body" style="overflow-y:hidden;padding-left:15px;">
        <!-- 内容主体区域 -->
        <iframe id="ifdd" name="c" height="100%" width="100%" border="0" frameborder="0" src="<?php echo url('Item/unassigned'); ?>">浏览器不支持嵌入式框架，或被配置为不显示嵌入式框架。</iframe>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © 甘肃新媒体集团
    </div>
</div>

<script src="/static/layui/layui.all.js"></script>
<script>
    $('#uedit').click(function() {
        //iframe层-父子操作
        layer.open({
            type: 2,
            title:'修改资料',
            area: ['40%', '60%'],
            fixed: false, //不固定
            maxmin: false,
            content: "<?php echo url('User/uedit'); ?>",
            cancel: function(){

            }
        });
    });
</script>
</body>
