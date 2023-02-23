<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"F:\project\pointsmall\public/../application/admin\view\role\add.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>添加角色|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"/>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"/>
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
            <label class="layui-form-label"><i class="required">*</i>角色名称：</label>
            <div class="layui-input-block">
                <input type="text" name="rolename" required lay-verify="required" placeholder="请输入角色名，如，来宾用户"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">角色说明：</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入内容" maxlength="30" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>权限管理：</label>
            <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="checkbox" name="switch" lay-skin="switch" lay-text="ON|OFF">
                    </div>
            </div>
        </div>

        <div class="layui-form-item allcheck">
            <?php if(is_array($volist) || $volist instanceof \think\Collection || $volist instanceof \think\Paginator): $i = 0; $__LIST__ = $volist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <label class="layui-form-label label-name" style="cursor: pointer"><?php echo $vo['Cname']; ?>：</label>
            <div class="layui-input-block">
                <?php if(is_array($slist) || $slist instanceof \think\Collection || $slist instanceof \think\Paginator): $i = 0; $__LIST__ = $slist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;if($s['Sid'] == $vo['ID']): ?>
                <input style="float: left" type="checkbox" title="<?php echo $s['Cname']; ?>" name="comp[]" value="<?php echo $s['ID']; ?>"/>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>状态：</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="0" title="正常" checked>
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
        //Demo
        layui.use('form', function () {
            var form = layui.form;
            form.render();
            //监听提交
            form.on('submit(formDemo)', function (data) {
                $.ajax({
                    url: "<?php echo url('roleadd_do'); ?>",
                    dataType: 'json',
                    type: 'POST',
                    data: data.field,
                    success: function (data) {
                        if (data.s == 'ok') {
                            layer.msg("添加成功", {}, function () {
                            });
                        } else {
                            layer.msg("添加失败");
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
</div>
</body>
</html>
