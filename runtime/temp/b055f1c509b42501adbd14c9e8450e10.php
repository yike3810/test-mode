<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\leader\add.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>增加信息</title>
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
	<form class="layui-form layui-col-md11" id="activity" enctype="multipart/form-data">
		<div class="layui-form-item">
		
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label"><i class="required">*</i>领导名称</label>
			<div class="layui-input-block">
				<input type="text" name="leader_name" id="leader_name" placeholder="请输入" autocomplete="off" class="layui-input" lay-verify="required">
			</div>
		</div>

		<div class="layui-form-item">
			<label class="layui-form-label">排序ID</label>
			<div class="layui-input-block">
				<input type="text" name="sortid" id="sortid" placeholder="请输入" value="<?php echo $sortid; ?>" autocomplete="off" class="layui-input" >
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<input type="hidden" name="attachment_info" id="attachment_info">
				<input type="hidden" name="image_info" id="image_info">
				<button class="layui-btn sub" lay-submit lay-filter="sub" id="sub">保存</button>
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
					url: "<?php echo url('add_do'); ?>",
					dataType: 'json',
					type: 'POST',
					data: data.field,
					success: function (data)
					{
						if (data.s=='ok') {
							layer.msg("添加成功",{},function(){
								var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
								parent.location.reload();
								parent.layer.close(index); //再执行关闭
							});
						} else {
							layer.msg("该领导已存在");
						}
					}

				});
				return false;
			});
		});
	</script>
</body>
</html>