<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"F:\project\pointsmall\public/../application/admin\view\set\index.html";i:1640252241;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>全局设置|<?php echo config('system.title'); ?></title>
<link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
<script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/static/layui/layui.all.js"></script>
<script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/static/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		var ue = UE.getEditor('container', {
		initialFrameWidth: '100%',
		initialFrameHeight: 400
    });
	//获取内容
	function getContent() {
	return UE.getEditor('editor').getContent();
}
});
</script>
</head>
<body>

<div class="layui-tab layui-tab-brief">
  <ul class="layui-tab-title">
<!--    <li >基本设置</li>-->
    <li class="layui-this">积分说明</li>
<!--	<li>大屏可视化设置</li>-->
  </ul>
  <div class="layui-tab-content">
  
<!--    <div class="layui-tab-item ">-->
<!--		<form class="layui-form">-->
<!--		  <div class="layui-row">-->
<!--			  <div class="layui-col-md12 layui-form-item">-->
<!--			    <label class="layui-form-label" style="width:100px;">首页一键置灰</label>-->
<!--			    <div class="layui-input-block">-->
<!--					<input type="checkbox" value="1" <?php if($parameter_list['index_put_gray'] == 1): ?>checked<?php endif; ?> name="index_put_gray" lay-skin="switch" lay-text="开启|关闭">-->
<!--			    </div>-->
<!--			  </div>-->
<!--		  </div>-->
<!--		  <div class="layui-row">-->
<!--			  <div class="layui-col-md12 layui-form-item">-->
<!--			    <div class="layui-input-block">-->
<!--			      <button class="layui-btn" lay-submit lay-filter="formBase">确认提交</button>-->
<!--			    </div>-->
<!--			  </div>-->
<!--		  </div>-->
<!--		</form>-->
<!--    </div>-->
    
    <div class="layui-tab-item layui-show">
		<form class="layui-form">
		  <div class="layui-row">
			  <div class="layui-form-item" style="width: 80%;margin-top: 50px">
				  <div class="layui-input-block">
					  <script id="container" name="points_introduction" lay-verify="required" type="text/plain">
						  <?php echo $parameterlong_list['points_introduction']; ?>
					  </script>
				  </div>
			  </div>
		  </div>
		  <div class="layui-row">
			  <div class="layui-col-md12 layui-form-item">
			    <div class="layui-input-block">
			      <button class="layui-btn" lay-submit lay-filter="formPoint">确认提交</button>
			    </div>
			  </div>
		  </div>
		</form>
	</div>
	
	<div class="layui-tab-item">
		<form class="layui-form">
		  <div class="layui-row">
			  <div class="layui-col-md5 layui-form-item">
			    <label class="layui-form-label" style="width:100px;">行政区名称</label>
			    <div class="layui-input-inline">
			      <input type="text" value="<?php echo $parameter_list['system_name']; ?>" name="system_name" placeholder="请输入" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-form-mid layui-word-aux"></div>
			  </div>
		  </div>
		  <div class="layui-row">
			  <div class="layui-col-md5 layui-form-item">
			    <label class="layui-form-label" style="width:100px;">中心坐标-经度</label>
			    <div class="layui-input-inline">
			      <input type="text" value="<?php echo $parameter_list['center_longitude']; ?>" name="center_longitude" placeholder="请输入" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-form-mid layui-word-aux"></div>
			  </div>
		  </div>
		  <div class="layui-row">
			  <div class="layui-col-md12 layui-form-item">
			    <label class="layui-form-label" style="width:100px;">中心坐标-纬度</label>
			    <div class="layui-input-inline">
			      <input type="text" value="<?php echo $parameter_list['center_latitude']; ?>" name="center_latitude" placeholder="请输入" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-form-mid layui-word-aux"></div>
			  </div>
		  </div>
		  <div class="layui-row">
			  <div class="layui-col-md12 layui-form-item">
			    <label class="layui-form-label" style="width:100px;">缩放比例</label>
			    <div class="layui-input-inline">
			      <input type="text" value="<?php echo $parameter_list['map_zoom']; ?>" name="map_zoom" placeholder="请输入" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-form-mid layui-word-aux"></div>
			  </div>
		  </div>
		  <div class="layui-row">
			  <div class="layui-col-md12 layui-form-item">
			    <div class="layui-input-block">
			      <button class="layui-btn" lay-submit lay-filter="formScreen">确认提交</button>
			    </div>
			  </div>
		  </div>
		</form>
	</div>
	
  </div>
</div>
<script>
//Demo
layui.use('form', function(){
  var form = layui.form;
  //监听提交
  form.on('submit(formBase)', function(data){
	    $.ajax({
	        url:"<?php echo url('Set/base_save'); ?>",
	        dataType:'json',
	        type:'POST',
	        data:data.field,
	        success: function(data) {
	            if (data.s=='ok') {
	            	layer.alert('保存成功！', {icon: 1});
	            }else {
	            	layer.alert(data.s, {icon: 2});
	            }
	        }
	    });
	    return false;
  });
  //监听提交
  form.on('submit(formPoint)', function(data){
    $.ajax({
        url:"<?php echo url('Set/points_introduction_save'); ?>",
        dataType:'json',
        type:'POST',
        data:data.field,
        success: function(data) {
            if (data.s=='ok') {  
            	layer.alert('保存成功！', {icon: 1});
            }else {
            	layer.alert('保存失败！', {icon: 2});
            }
        }
    });
    return false;
  });
  //监听提交
  form.on('submit(formScreen)', function(data){
    $.ajax({
        url:"<?php echo url('Set/screen_save'); ?>",
        dataType:'json',
        type:'POST',
        data:data.field,
        success: function(data) {
            if (data.s=='ok') {  
            	layer.alert('保存成功！', {icon: 1});
            }else {
            	layer.alert('保存失败！', {icon: 2});
            }
        }
    });
    return false;
  });
  form.render();
});
</script>
</body>
</html>
