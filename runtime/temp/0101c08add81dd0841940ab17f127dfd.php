<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"F:\project\consumer-coupon\public/../application/admin\view\index\web.html";i:1649727074;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php echo config('system.title'); ?></title>
  <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
  <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
  <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
  <script type="text/javascript" src="/static/js/common.js"></script>
<script>
	
</script>
<style>
.layui-carousel a{
	/*margin:60px 50px 0;*/
	margin-left:100px;
}
.carousel-item  .bg{
	background-color:rgba(0,0,0,0);
}
.line{
	width:100%;margin-bottom:60px;float:left;
}
.cell{
	width:50%;float:left;text-align: center;
}
.layui-body{
	background: url(/static/image/reportServlet.jpg);
    background-size: 100% 100%;
    background-repeat: repeat;
    left: 0;
    width: 100%;
}
.layui-layout-admin .layui-logo{
	font-size: 18px;
}
.layui-layout-admin .layui-footer{
	left: 0;
}
.layui-carousel>[carousel-item]:before{
    content: '';
}
</style>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin" style="">
  <div class="layui-header">
    <a href="<?php echo url('Index/web'); ?>"><div class="layui-logo"><?php echo config('system.title'); ?></div></a>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="/uploads/member/<?php echo session('Service.member_avatar'); ?>"
           onerror="javascript:this.src='/static/image/default_avatar1.png';" class="layui-nav-img">
          <?php echo session('ThinkUser.Username'); ?>
        </a>
        <dl class="layui-nav-child">
          <dd><a  href="javascript:;" id="uedit">基本资料</a></dd>
        </dl>
      </li>
      <li class="layui-nav-item"><a href="<?php echo url('Index/quit'); ?>">退出</a></li>
    </ul>
  </div>
    <div class="layui-body" style="overflow-y: hidden;">
    <div id="right"  style="">
		<div class="layui-carousel" id="test1" style="background-color:rgba(0,0,0,0);position: absolute;top: 50%;left:50%;margin-top: -250px;margin-left: -600px;">
		  <div carousel-item  class="carousel-item" style='background-color:rgba(0,0,0,0);vertical-align:middle;'>
			<?php if(is_array($mod_list) || $mod_list instanceof \think\Collection || $mod_list instanceof \think\Paginator): $i = 0; $__LIST__ = $mod_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($key % 4 == 0): ?>
						<div class="bg">
				<?php endif; if($key % 2 == 0): ?>
						<div class="line">
					<?php endif; ?>
						<div class="cell">
							<a id="<?php echo $vo['id']; ?>" <?php if($key % 2 == 1): ?>style="margin-left:-100px;"<?php endif; ?> href="<?php if($vo['url']): ?><?php echo url('Index/main'); ?>?module=<?php echo $vo['mod']; else: ?>javascript:void(0);<?php endif; ?>" target="_blank"><img width="400px"  src="/static/image/<?php echo $vo['icon']; ?>"></a>
						</div>
					<?php if($key % 2 == 1): ?>
						</div>
					<?php endif; if($key % 4 == 3 || $key == count($mod_list)-1): ?>
						</div>
				<?php endif; endforeach; endif; else: echo "" ;endif; ?>
		  </div>
		</div>
		<script src="/static/layui/layui.all.js"></script>
		<script>
		layui.use('carousel', function(){
		  var carousel = layui.carousel;
		  //建造实例
		  carousel.render({
		    elem: '#test1'
		    ,width: '1200px' //设置容器宽度
			,height: '500px' //设置容器宽度
		    ,arrow: 'always' //始终显示箭头
		    //,anim: 'updown' //切换动画方式
		    ,autoplay:false
		    ,indicator:'outside'
		  });
		});
		window.onload=function() {
			$('#xcxj').click(function() {
				$.post("<?php echo url('System/getXgsyToken'); ?>", {
					
				}, function(data) {
					var ret = jsonDecode(data);
					if(ret.status == 1) {
						//window.parent.location.href=data;
						window.open(ret.url);
						return false;
					}else{
						layer.msg(ret.msg);
						return false;
					}
				});
				return false;
			});
		}
		</script>
	</div>
	  </div>
  
	  <div class="layui-footer">
	    <!-- 底部固定区域 -->
	    © 甘肃新媒体集团 &bull; 九色鹿技术公司
<!--	    <div style="float:right;margin-right:30px;"><a href="/static/CluesInstructionofAdmin.pdf" target="_blank">系统使用手册</a></div>-->
	  </div>
</div>
<script>
//JavaScript代码区域
$('#uedit').on("click",function() {
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
</html>