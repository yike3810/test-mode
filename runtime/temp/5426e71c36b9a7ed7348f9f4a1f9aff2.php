<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"F:\project\pointsmall\public/../application/admin\view\user\add.html";i:1657696380;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>添加用户|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/Public.js"></script>
    <script type="text/javascript" src="/static/js/winpop.js"></script>
    <script type="text/javascript" src="/static/js/check.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>

</head>
<body>
<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form layui-col-md10" id="activity" enctype="multipart/form-data">
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>用户角色：</label>
            <div class=" layui-input-block" >
                <select name="roleid" lay-verify="required">
                    <?php if(is_array($volist) || $volist instanceof \think\Collection || $volist instanceof \think\Paginator): $i = 0; $__LIST__ = $volist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $vo['ID']; ?>"><?php echo $vo['Rolename']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
<!--            <div class="layui-form-mid layui-word-aux">* 选择用户角色，分配权限</div>-->
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>用户名：</label>
            <div class="layui-input-block">
                <input type="text" name="username" required  lay-verify="required" placeholder="请输入用户名：2～12位英文数字组合" autocomplete="off" class="layui-input">
            </div>
<!--            <div class="layui-form-mid layui-word-aux">* 2～12位英文数字组合</div>-->
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>密码：</label>
            <div class="layui-input-block">
                <input type="password" name="password" required lay-verify="required|pass" placeholder="请输入密码： 8-32位，必须包含大写字母、小写字母和数字" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>姓名：</label>
            <div class="layui-input-block">
                <input type="text" name="name" required  lay-verify="required" placeholder="请输入姓名" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>电话号码：</label>
            <div class="layui-input-block">
                <input type="text" name="phone" required  lay-verify="required" placeholder="请输入电话号码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">Email：</label>
            <div class="layui-input-block">
                <input type="text" name="email"  placeholder="请输入邮箱：如：admin@qq.com" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">上级领导：</label>
            <div class=" layui-input-block" >
                <select name="leader_id" >
                    <option value="">请选择</option>
                    <?php if(is_array($user_list) || $user_list instanceof \think\Collection || $user_list instanceof \think\Paginator): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $vo['ID']; ?>"><?php echo $vo['Username']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
<!--            <div class="layui-form-mid layui-word-aux">选择直接领导</div>-->
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">用户说明：</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入内容" maxlength="30" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="0" title="正常"checked>
                <input type="radio" name="status" value="1" title="锁定">
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo">提交</button>
            </div>
        </div>
    </form>
    <style>
        .layui-form-label{
            width: 140px;
            margin-bottom: -80px;
        }
        .layui-input-block {
            margin-left:160px;
            min-height: 36px
        }
        .required {
            color: red;
            padding-right: 5px;
        }
    </style>
    <script>
        //Demo
        layui.use('form', function(){
            var form = layui.form;
            form.render();
            form.verify({
                //我们既支持上述函数式的方式，也支持下述数组的形式
                //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
                pass: [
                    /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/
                    ,'密码必须8-32位，必须包含大写字母、小写字母和数字'
                ]
            });
            //监听提交
            form.on('submit(formDemo)', function(data){
                $.ajax({
                    url:"<?php echo url('useradd_do'); ?>",
                    dataType:'json',
                    type:'POST',
                    data:data.field,
                    success: function(data) {
                        if (data.s=='ok') {
                            layer.msg("添加成功",{},function(){
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.location.reload();
                                parent.layer.close(index); //再执行关闭
                            });
                        }else {
                            layer.msg("添加失败");
                        }
                    }
                });


                // layer.msg(JSON.stringify(data.field));
                return false;
            });
        });
    </script>



</div>
</body>
</html>
