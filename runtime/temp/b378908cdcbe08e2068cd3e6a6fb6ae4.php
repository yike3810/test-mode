<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\user\uedit.html";i:1657677784;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>修改密码|<?php echo config('system.title'); ?></title>
<link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
<link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
<script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/static/js/Public.js"></script>
<script type="text/javascript" src="/static/js/winpop.js"></script>
<script type="text/javascript" src="/static/js/check.js"></script>
<script>
$(document).ready(function() {
	$('.button').click(function() {
		var password=$('#dl dd .qtext').eq(0).val();
		var email=$('#dl dd .qtext').eq(1).val();
		
		// if (!tcheck(password,'6,18','请输入6~18位数的密码','length')) { return false; }
		if (!tcheck(email,'email','请填写正确的邮箱地址')) { return false; }

        if (password!='') {
            if (!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/.test(password)) {
                wintq('密码必须8-32位，必须包含大写字母、小写字母和数字', 2, 1000, 1, '');
                return false;
            }
        }
		wintq('正在修改，请稍后...',4,20000,0,'');
		$.ajax({
			url:"<?php echo url('User/uedit_do'); ?>",
			dataType:'json',
			type:'POST',
			data:'password='+password+'&email='+email,
			success: function(data) {
				if (data.s=='ok') {
					wintq('修改成功',1,2000,0,'');
					//关闭父窗口的元素
					setTimeout(function() {
						$(window.parent.document).find("#iframe_pop, #zbody").remove();
					},2000);
				}else {
					wintq(data.s,3,1000,1,'');
				}
			}
		});
	});
});
</script>
</head>
<body>
<div id="content">
	<dl id="dl">
    	<dt>修改个人资料</dt>
        <dd>
        	<span class="dd_left">用户名：</span>
        	<span class="dd_right"><?php echo $result['Username']; ?></span>
        </dd>
        <dd>
        	<span class="dd_left">密码：</span>
        	<span class="dd_right"><input type="password" class="qtext" maxlength="18" /><font>* 密码必须8-32位，必须包含大写字母、小写字母和数字</font></span>
        </dd>
        <dd>
        	<span class="dd_left">电子邮箱：</span>
        	<span class="dd_right"><input type="text" class="qtext" value="<?php echo $result['Email']; ?>" maxlength="30" /><font>* 如：admin@qq.com</font></span>
        </dd>
        <dd><input type="button" class="button" value="提 交" /></dd>
    </dl>
</div>
</body>
</html>