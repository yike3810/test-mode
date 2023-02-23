<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"F:\project\pointsmall\public/../application/admin\view\index\index.html";i:1644288720;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>登录 - <?php echo config('system.title'); ?></title>
<link rel="stylesheet" type="text/css" href="/static/css/loginww.css"  />
<link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
<script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/static/js/Public.js"></script>
<script type="text/javascript" src="/static/js/winpop.js"></script>
    <script>
        //监听事件 event参数是键盘事件监听
        $(document).keyup(function(event){
            //判断回车键的CODE
            if(event.keyCode == 13){
                // 模拟点击按钮
                $(".button").click();
            }
        });

$(function() {
	$('.button').click(function(event) {
		event.preventDefault();
		var username=$('#username').val();
		var password=$('#password').val();
		var code=$('#code').val();
		if (!/^[a-zA-Z0-9_-]|[\u4e00-\u9fa5]{2,16}$/.test(username)) {
			wintq('请输入正确的用户名',2,2000,1,'');
			$('#username').focus();
			return;
		}
		if (password.length<6) {
			wintq('请输入6位数以上的密码',2,2000,1,'');
			$('#password').focus();
			return;
		}
		wintq('正在登录，请稍后...',4,20000,0,'');
		$.ajax({
			url:"<?php echo url('Index/login'); ?>",
			dataType:"json",
			type:'POST',
			cache:false,
			data:'username='+username+'&password='+password+'&code='+code,
			success: function(data) {
                if (data.s=='ok') {
                    wintq('登录成功',1,2000,0,"<?php echo url('index/web'); ?>");
                }else {
                    $("#verify").attr('src','<?php echo captcha_src(); ?>?'+Math.random());
                    wintq(data.s,3,2000,1,'');
                }
            }
		});
	});
	$('#verify').attr('src','<?php echo captcha_src(); ?>?'+Math.random());
	$('#verify').click(function() {
		$(this).attr('src','<?php echo captcha_src(); ?>?'+Math.random());
	});
    if(window !=top){
        top.location.href=location.href;
    }
});
</script>
</head>
<body>

<div id="Layer1" style="position:absolute; width:100%; height:100%; background-color: snow; z-index:-1" >
    <img src="/static/image/exam.jpg" height="100%" width="100%"/>
</div>

<div class="login_box">
    <div class="login">
        <div class="login_name">
            <p><?php echo config('system.title'); ?></p>
        </div>
        <input name="username"  id="username" type="text" placeholder="用户名" tabindex="1">
        <input name="password"  type="password" id="password"  placeholder="密码" tabindex="2" />
        <input name="code" tabindex="3" style="width:180px;margin-right: 20px;" id="code" type="text"  value="验证码" onfocus="this.value=''" onblur="if(this.value==''){this.value='验证码'}"> <img src="" alt="captcha" border="0" id="verify" width="84" height="38"/>
        <input value="登录" style="width:100%;"  type="button" class="button" >

    </div>
    <div class="copyright">甘肃新媒体集团  版本v1.0</div>
</div>
</body>
</html>
