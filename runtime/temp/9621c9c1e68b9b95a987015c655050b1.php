<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\index\main.html";i:1649726654;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php echo config('system.title'); ?></title>
  <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
  <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
  <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
  <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
<script>
window.onload=function() {
	//退出登录
	function quit() {
		$('body #quit').click(function(event) {
			event.preventDefault();
			if (confirm('确定要退出吗？')) {
				window.top.location.href='quit';
			}
		});
	}
	quit();
	$('#home').click(function() {
		window.top.location.href="<?php echo url('Index/web'); ?>";
	});
    $('#main #user_save').click(function() {
		popload('修改密码',580,230,"<?php echo url('User/uedit'); ?>");
		addDiv($('#iframe_pop'));
	});
	//一键清空缓存
	$('#main #cache').click(function() {
		if (confirm('确定要清空所有缓存吗？')) {
			wintq('正在清除缓存，请稍后...',4,60000,0,'');
			$.ajax({
				url:"<?php echo url('Admin/clearcache'); ?>",
				dataType:'JSON',
				type:'POST',
				data:'clear=ok',
				success: function(data) {
					if (data.s=='ok') {
						wintq('更新缓存成功！',1,1000,1,'');
					}else {
						wintq(data.s,3,1000,1,'');
					}
				}
			});
		}
	});
	//跟单提醒
	function remind() {
		popload('新消息提醒',500,300,'__APP__/Public/remind/','z');
		addDiv($('#iframe_pop'));
	}
	$('#Remindh').click(function() {
		remind();
	});
	//事务提醒
	function Link() {
		$.ajax({
			url:'__APP__/Common/link',
			dataType:"json",
			type:'POST',
			data:'link='+Math.random(),
			success: function(data){
				if (data.s > 0) {
					$('#Remindh').css('background','url(/static/image/sessi.gif) no-repeat left top');
					$('#Remindh').text(data.s);
					$('#Remindh').attr('title','当前有：'+data.s+'个客户需要跟进');
				}else {
					$('#Remindh').css('background','url(/static/image/sess.png) no-repeat left top');
				}
			}
		});
	}
	//setInterval(Link,90*60);
	//Link();
}
$(function() {
	for (i=0; i<$('#left dl').length; i++) {
		$dldd=$('#left dl').eq(i);
		if ($dldd.find('dd').length < 1) {
			$dldd.remove();
		}
	}
	$('#ul li .a').click(function() {
		$('#ul li .a').removeClass('lia');
		$('#ul li .a').not($(this)).children('img').attr("src","/static/image/plus.png");
		$(this).addClass('lia');
		$('#ul li dl').stop().slideUp('fast');
		var $dl = $(this).parents('li').find('dl');
		if($(this).children('img').attr("src")=="/static/image/minus.png"){
			$(this).children('img').attr("src","/static/image/plus.png");
		}else{
			$(this).children('img').attr("src","/static/image/minus.png");
		}

		$dl.stop().slideToggle('fast');
		$dl.find('a').click(function() {
			$('#ul li dl dd a').removeClass('dla');
			$(this).addClass('dla');
		});
	});
	$('#ul li dl').eq(0).stop().slideToggle('fast');
	$('#ul li .a').eq(0).children('img').attr("src","/static/image/minus.png");
	$('#ul li .a').eq(0).addClass('lia');
});
</script>
<style>
.layui-layout-admin .layui-logo{
	font-size: 18px;
}
</style>
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <a href="<?php echo url('Index/web'); ?>"><div class="layui-logo"><?php echo config('system.title'); ?></div></a>
 	<!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-left">
      <li class="layui-nav-item">
        <a href="javascript:;">切换模块</a>
        <dl class="layui-nav-child">
        <?php if(is_array($mod_list) || $mod_list instanceof \think\Collection || $mod_list instanceof \think\Paginator): $i = 0; $__LIST__ = $mod_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <dd><a id="<?php echo $vo['id']; ?>" href="<?php if($vo['url']): ?><?php echo url('Index/main'); ?>?module=<?php echo $vo['mod']; else: ?>javascript:void(0);<?php endif; ?>"><?php echo $vo['mod_text']; ?></a></dd>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </dl>
      </li>
    </ul>
    <!-- 头部区域（可配合layui已有的水平导航） -->
    <ul class="layui-nav layui-layout-right">
      <li class="layui-nav-item"><a href="<?php echo url('Index/messageList'); ?>" target="c" lay-text="消息中心">
<!--        <i class="layui-icon layui-icon-notice"></i>-->
<!--        &lt;!&ndash; 如果有新消息，则显示小圆点 &ndash;&gt;-->
<!--        <span class="layui-badge-dot"></span>-->
<!--      </a></li>-->
      <li class="layui-nav-item">
        <a href="javascript:;">
          <img src="/static/image/default_avatar1.png"
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

  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree"  lay-filter="test">
      <?php $j = 0;if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?>
        <li class="layui-nav-item <?php if($j == 0): ?>layui-nav-itemed<?php endif; ?>">
          <a href="<?php if($li['ModuleUrl'] == ''): ?>javascript:;<?php else: ?><?php echo url($li['ModuleUrl']); endif; ?>" target="c"><?php echo $li['ModuleName']; ?></a>
          <dl class="layui-nav-child">
          <?php $s = 0;if(is_array($volist) || $volist instanceof \think\Collection || $volist instanceof \think\Paginator): if( count($volist)==0 ) : echo "" ;else: foreach($volist as $s=>$vo): if($li['ID'] == $vo['Sid']): ?>
            	<dd <?php if($j == 0 && $s == 0): ?>class="layui-this"<?php endif; ?>><a href="<?php if($vo['ModuleUrl'] == ''): ?>javascript:;<?php else: ?><?php echo url($vo['ModuleUrl']); endif; ?>" <?php if($vo['open_method'] == 0): ?>target="c"<?php else: ?>target="_blank"<?php endif; ?>><?php echo $vo['ModuleName']; ?></a></dd>
            	<?php $s++;endif; endforeach; endif; else: echo "" ;endif; ?>
          </dl>
        </li>
       <?php $j++;endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </div>
  </div>

  <div class="layui-body" style="overflow-y: hidden;padding-left:15px;">
    <!-- 内容主体区域 -->
    <iframe id="ifdd" name="c" height="100%" width="100%" border="0" frameborder="0" src="<?php echo url($list[0]['ModuleUrl']); ?>">浏览器不支持嵌入式框架，或被配置为不显示嵌入式框架。</iframe>
  </div>

  <div class="layui-footer">
    <!-- 底部固定区域 -->
    © 甘肃新媒体集团 &bull; 九色鹿技术公司
    <div style="float:right;margin-right:30px;">
<!--      <a href="/static/CluesInstructionofAdmin.pdf" target="_blank">系统使用手册</a>-->
    </div>
  </div>
</div>
<script src="/static/layui/layui.all.js"></script>
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