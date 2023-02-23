<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"F:\project\pointsmall\public/../application/admin\view\competence\index.html";i:1640144824;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>权限管理|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"/>
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"/>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div style="margin-top:20px;">
            <form class="layui-form" style="">
                <div class="layui-row" style="margin-top:20px;">
                    <div class="layui-form-inline layui-col-md3">
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-block">
                            <input type="text" name="keywords" id="keywords" placeholder="请输入" autocomplete="off"
                                   class="layui-input" value="<?php echo input('param.keywords'); ?>"/>
<!--                            <input name="keywords" id="keywords" type="text" class="ctext" size="30" value="<?php echo input('param.keywords'); ?>"/>-->
                        </div>
                    </div>
                    <div class="layui-form-inline layui-col-md3">
                        <div class="layui-input-block">
                            <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="layui-row">


                <table class="layui-table" id="demo" lay-filter="test" style="min-width: 1000px">
                    <colgroup>
                        <col width="150" >
                        <col width="250">
                        <col width="250">
                        <col>
                        <col width="100">
                        <col width="200">
                        <col width="200">
                    </colgroup>
                    <thead>
                    <tr>
                        <th colspan="7">
                            <button type="button" class="layui-btn layui-btn-sm" id="cadd">+ 添加</button>
                        </th>
                    </tr>
                    <tr>
                        <th>编号</th>
                        <th>权限分类</th>
                        <th>权限名称</th>
                        <th>权限说明</th>
                        <th>状态</th>
                        <th>日期</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($volist['total'] == 0): ?>
                    <tr class="tr">
                        <td class="tc" colspan="7">暂无数据，等待添加～！</td>
                    </tr>
                    <?php endif; ?>
<!--                    <?php if($sidlist['total'] == 0): ?>-->
<!--                    <tr class="tr">-->
<!--                        <td class="tc" colspan="7">暂无数据，等待添加～！</td>-->
<!--                    </tr>-->
<!--                    <?php endif; ?>-->
                    <!--顶级数据-->
                    <?php if(is_array($volist['data']) || $volist['data'] instanceof \think\Collection || $volist['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $volist['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr class="tr tr2">
                            <td class="tc"><?php echo $vo['ID']; ?></td>
                            <td class="tc"><?php echo $vo['Cname']; ?></td>
                            <td><?php echo $vo['Cname']; ?></td>
                            <td><?php echo $vo['Description']; ?></td>
                            <td class="tc">
                                <?php if($vo['Status'] == 0): ?>
                                <i class="layui-icon layui-icon-ok" style="color: #009688;font-size: 20px"></i>
                                <?php else: ?>
                                <i class="layui-icon layui-icon-close" style="color: #009688;font-size: 20px"></i>
                                <?php endif; ?>
                            </td>
                            <td class="tc"><?php echo $vo['Dtime']; ?></td>
                            <td class="tc fixed_w">
                                <a class="layui-btn layui-btn-xs cedit" href="<<?php echo $vo['ID']; ?>>" >修改</a>
                                <a class="layui-btn layui-btn-danger layui-btn-xs cdel" href="<<?php echo $vo['ID']; ?>>">删除</a>
                            </td>
                        </tr>
                    <!--二级数据-->
                        <?php if(is_array($sidlist) || $sidlist instanceof \think\Collection || $sidlist instanceof \think\Paginator): $i = 0; $__LIST__ = $sidlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;if($s['Sid'] == $vo['ID']): ?>
                        <tr class="tr">
                            <td class="tc"><?php echo $s['ID']; ?></td>
                            <td></td>
                            <td><?php echo $s['Cname']; ?></td>
                            <td><?php echo $s['Description']; ?></td>
                            <td class="tc">
                                <?php if($s['Status'] == 0): ?>
                                <i class="layui-icon layui-icon-ok" style="color: #009688;font-size: 20px"></i>
                                <?php else: ?>
                                <i class="layui-icon layui-icon-close" style="color: #009688;font-size: 20px"></i>
                                <?php endif; ?>
                            </td>
                            <td class="tc"><?php echo $s['Dtime']; ?></td>
                            <td class="tc fixed_w">
                                <a class="layui-btn layui-btn-xs cedit" href="<<?php echo $s['ID']; ?>>" >修改</a>
                                <a class="layui-btn layui-btn-danger  layui-btn-xs cdel" href="<<?php echo $s['ID']; ?>>">删除</a>
                            </td>
                        </tr>
                        <?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>

                <div id="page" class="layui-box layui-laypage layui-laypage-default"><?php echo $lists->render(); ?></div>
            </div>
        </div>
    </div>
    <script>
        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time1'
                , type: 'datetime'
            });
        });
        layui.use('laydate', function () {
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time2'
                , type: 'datetime'
            });
        });

        layui.use('table', function () {
            var table = layui.table;
            var $ = layui.$, active = {
                // reload: function () {
                //     //执行重载
                //     console.log('hguygyghghghu')
                //     table.reload('competence', {
                //         page: {
                //             curr: 1 //重新从第 1 页开始
                //         }
                //         , where: {
                //             keywords: $('#keywords').val(),
                //         }
                //     }, 'data');
                // }
            };
            $('#find_news').on('click', function () {
                console.log('121345468894648674878')
                where: {
                                keywords: $('#keywords').val()
                            }
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });
            $('#cadd').on("click", function () {
                //iframe层-父子操作
                layer.open({
                    type: 2,
                    title:'添加权限',
                    area: ['1000px', '560px'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: "<?php echo url('Competence/cadd'); ?>?ID=",
                    cancel: function () {
                        window.location.reload();
                    }
                });
            });
            $('.cedit').on("click", function (event) {
                //iframe层-父子操作
                event.preventDefault();
                var id=$(this).attr('href').replace(/[^0-9]/ig,"");
                console.log(id)
                layer.open({
                    type: 2,
                    area: ['1000px', '560px'],
                    fixed: false, //不固定
                    title:'修改权限信息',
                    maxmin: true,
                    content: "<?php echo url('Competence/cedit'); ?>?id="+id,
                    end: function () {
                        window.location.reload();
                    }
                });
            });
            $('.cdel').on("click", function (event) {
                //iframe层-父子操作
                event.preventDefault();
                var id=$(this).attr('href').replace(/[^0-9]/ig,"");
                console.log(id)
                $.ajax({
                    url: "<?php echo url('Competence/cdel'); ?>",
                    dataType: 'json',
                    type: 'POST',
                    data: 'id=' + id,
                    success: function (data) {
                        console.log(data)
                        if (data.s == 'ok') {
                            layer.open({
                                title: '操作信息'
                                , content: '删除成功'
                            });
                            window.location.reload();
                        } else {
                            layer.open({
                                title: '操作信息'
                                , content: data.s
                            });
                        }
                    }
                });
            });
        });
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.image !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/news/{{ d.image }}"/>
        {{# } else{ }}

        {{#  } }}
    </script>
</div>
</body>
</html>
