<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"F:\project\pointsmall\public/../application/admin\view\user\edit.html";i:1657696549;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>修改用户信息|<?php echo config('system.title'); ?></title>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"/>
    <script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/static/ueditor/ueditor.all.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
    <script type="text/javascript" src="/static/js/Public.js"></script>
    <script type="text/javascript"></script>
</head>
<body>

<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form layui-col-md10" id="activity" enctype="multipart/form-data">

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>用户角色：</label>
            <div class="layui-input-block">
                <select name="roleid" id="roleid" lay-filter="aihao">
                    <?php if(is_array($volist) || $volist instanceof \think\Collection || $volist instanceof \think\Paginator): $i = 0; $__LIST__ = $volist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['ID'] == $result['Roleid']): ?>
                    <option selected value="<?php echo $vo['ID']; ?>"><?php echo $vo['Rolename']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $vo['ID']; ?>"><?php echo $vo['Rolename']; ?></option>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>用户名：</label>
            <div class="layui-input-block">
                <input type="text" name="username" value="<?php echo $result['Username']; ?>" required lay-verify="required"
                       placeholder="请输入用户名：2～12位英文数字组合" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码：</label>
            <div class="layui-input-block">
                <input type="text" name="password" onblur="onPassup()" placeholder="不填写则不修改" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>姓名：</label>
            <div class="layui-input-block">
                <input type="text" name="name" required  placeholder="请填写姓名"
                       value="<?php echo $result['name']; ?>" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>电话号码：</label>
            <div class="layui-input-block">
                <input type="text" name="phone" required  placeholder="请填写电话号码"
                       value="<?php echo $result['phone']; ?>" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">E-mail：</label>
            <div class="layui-input-block">
                <input type="text" name="email"   placeholder="请填写电子邮箱：如：admin@qq.com"
                       value="<?php echo $result['Email']; ?>" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item ">
            <label class="layui-form-label">上级领导：</label>
            <div class="layui-input-block">
                <select name="leader_id" id="leader_id" lay-filter="aihao" value="<?php echo $result['Competence']; ?>">
                    <option value="">请选择</option>
                    <?php if(is_array($user_list) || $user_list instanceof \think\Collection || $user_list instanceof \think\Paginator): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['ID'] == $result['leader_id']): ?>
                        <option selected="selected" value="<?php echo $vo['ID']; ?>"><?php echo $vo['Username']; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $vo['ID']; ?>"><?php echo $vo['Username']; ?></option>
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">用户说明：</label>
            <div class="layui-input-block">
                <textarea class="layui-textarea " name="description" lay-verify="content" id="LAY_demo_editor"><?php echo $result['Description']; ?></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>权限管理：</label>
            <div class="layui-input-block">
                <div class="layui-input-inline">
                    <input type="checkbox" name="switch" lay-skin="switch" lay-text="ON|OFF" <?php if($switch == 1): ?>checked<?php endif; ?>>
                </div>
            </div>
        </div>
        <div class="layui-form-item allcheck">
            <?php if(is_array($aulist) || $aulist instanceof \think\Collection || $aulist instanceof \think\Paginator): $i = 0; $__LIST__ = $aulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <label class="layui-form-label label-name" style="cursor: pointer"><?php echo $vo['Cname']; ?>：</label>
                <div class="layui-input-block">
                    <?php if(is_array($slist) || $slist instanceof \think\Collection || $slist instanceof \think\Paginator): $i = 0; $__LIST__ = $slist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;if($s['Sid'] == $vo['ID']): ?>
                            <input style="float: left" type="checkbox" title="<?php echo $s['Cname']; ?>"  name="comp[]" value="<?php echo $s['ID']; ?>" <?php if(in_array(($s['ID']), is_array($result['Competence'])?$result['Competence']:explode(',',$result['Competence']))): ?>checked<?php endif; ?> />
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" <?php if($result['Status'] == 1): ?>checked<?php endif; ?>
                title="锁定"/>
                <input type="radio" name="status" value="0" <?php if($result['Status'] == 0): ?>checked<?php endif; ?>
                title="正常">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn sub" lay-submit lay-filter="sub" id="sub">提交</button>
            </div>
        </div>
        <input type="hidden" name="ID" id="ID" value="<?php echo $result['ID']; ?>">
    </form>
</div>
<style>
    .layui-form-label {
        width: 140px;
        margin-bottom: -80px;
    }

    .layui-input-block {
        margin-left: 160px;
        min-height: 36px
    }

    .required {
        color: red;
        padding-right: 5px;
    }
</style>
<script>
    function onPassup(){
        //密码校验
        var oPassword = document.getElementsByName("password")[0];
        var oValue = oPassword.value;
        if (oValue ==""){
        }else {
            if (!/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/.test(oValue)) {
                layer.msg('密码必须8-32位，必须包含大写字母、小写字母和数字', {icon: 5});
                return false;
            }
        }
    }
    //Demo
    layui.use('form', function () {
        var form = layui.form;
        form.render();
        //监听提交
        form.on('submit(sub)', function (data) {
            var formData = data.field;
            $.ajax({
                url: "<?php echo url('useredit_do'); ?>",
                dataType: 'json',
                type: 'POST',
                data: formData,
                success: function (data) {
                    // console.log("data", data)
                    if (data.s == 'ok') {
                        layer.msg("修改成功", {}, function () {
                        });
                    }else if (data.s == 'nopass'){
                        layer.msg('密码必须8-32位，必须包含大写字母、小写字母和数字', {icon: 5});
                    } else {
                        layer.msg("修改失败");
                    }
                }

            });
            return false;
        });
    });
    $('input[name="switch"]').next().click(function () {
        if($('input[name="switch"]').prop("checked")){
            // console.log($('.allcheck').find('input').prop("checked"))
            $('.allcheck').find("input").prop("checked", true)
            $('.allcheck').find("input").next().addClass('layui-form-checked')
        }else {
            $('.allcheck').find("input").prop("checked", false)
            $('.allcheck').find("input").next().removeClass('layui-form-checked')
        }
        // $('.allcheck').children("input").prop("checked", true)
        // $('.allcheck').next().children("input").next().addClass('layui-form-checked')
    });
    $('.label-name').click(function () {
        // $(“input[name=‘ck’]”).prop(“checked”,
        let status = false;
        for (let i = 0; i < $(this).next().children("input").length; i++) {
            if (!$($(this).next().children("input")[i]).prop("checked")) {
                status = true;
            }
        }
        if (status) {
            $(this).next().children("input").prop("checked", true)
            $(this).next().children("input").next().addClass('layui-form-checked')
        } else {
            $(this).next().children("input").prop("checked", false)
            $(this).next().children("input").next().removeClass('layui-form-checked')
            $('input[name="switch"]').prop("checked",false);
            $('input[name="switch"]').next().removeClass('layui-form-onswitch');
        }
    })
</script>
</body>
</html>
