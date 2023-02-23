<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\project\pointsmall\public/../application/admin\view\news\recycle.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>回收站|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <div id="tree" class="layui-col-md2"></div>
        <div class="layui-col-md10" style="margin-top:20px;">
            <form class="layui-form">
                <div class="layui-row" style="margin-top:20px;" >
                    <div class="layui-form-inline layui-col-md3">
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-block">
                            <input type="text" name="keywords" id="keywords" placeholder="请输入" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-inline layui-col-md3">
                        <label class="layui-form-label time-form-label">删除时间</label>
                        <div class="layui-input-block dform-label">
                            <input type="text" name="publish_time1" id="publish_time1" placeholder="开始 " autocomplete="off" class="layui-input ">
                        </div>
                    </div>
                    <div class="layui-form-inline layui-col-md3">
                        <label class="layui-form-label cform-label">到</label>
                        <div class="layui-input-block dform-label">
                            <input type="text" name="publish_time2" id="publish_time2" placeholder=" 结束" autocomplete="off" class="layui-input timeinput">
                        </div>
                    </div>
                    <div class="layui-form-inline layui-col-md3">
                        <div class="layui-input-block">
                            <button type="button" class="layui-btn" id="find_news" data-type="reload">检索</button>
                        </div>
                    </div>
                </div>
            </form>

            <table id="demo" lay-filter="test"></table>
        </div>
    </div>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time1'
                ,type: 'datetime'
            });
        });
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //时间选择器
            laydate.render({
                elem: '#publish_time2'
                ,type: 'datetime'
            });
        });
        layui.use('tree', function(){
            var tree = layui.tree;

            //渲染
            var inst1 = tree.render({
                elem: '#tree',  //绑定元素
                showCheckbox:false,
                onlyIconControl:true,
                data:<?php echo $clues_tree_list; ?>,
                click: function(obj){
                    var nodes=document.getElementsByClassName("layui-tree-txt");
                    for (var i=0;i<nodes.length;i++){
                        if(nodes[i].innerHTML === obj.data.title){
                            nodes[i].style.color="#009688";
                            nodes[i].style.fontWeight="bold";
                        }else{
                            nodes[i].style.color="#555";
                            nodes[i].style.fontWeight="normal";
                        }
                    }
                    //console.log(obj.data); //得到当前点击的节点数据
                    //console.log(obj.state); //得到当前节点的展开状态：open、close、normal
                    //console.log(obj.elem); //得到当前节点元素
                    //console.log(obj.data.children); //当前节点下是否有子节点
                    //obj.data.id
                    $('#keywords').val('');
                    $('#publish_time1').val('');
                    $('#publish_time2').val("");
                    layui.use('table', function(){
                        var table = layui.table;

                        //第一个实例
                        table.render({
                            elem: '#demo'
                            ,url: "<?php echo url('News/getrecycle'); ?>" //数据接口
                            ,where:{region_id: obj.data.id}
                            ,page: true //开启分页
                            ,limit:15
                            ,limits:[15,20,30,40,50,60,70,80,90]
                            ,id:'news'
                            ,cols: [[ //表头
                                {field: 'news_id', title: 'ID', width:120, fixed: 'left'}
                                ,{field: 'title', title: '标题', width:430}
                                ,{field: 'type_name', title: '类型	', width:150}
                                ,{field: 'status_name', title: '状态', width:150}
                                // ,{field: 'image', title: '标题图', width:80,templet: '#imageTpl'}
                                ,{field: 'view', title: '阅读量', width: 120, }
                                ,{field: 'delete_time', title: '删除时间', width:200, }
                                ,{field: 'operating1', title: '操作', toolbar: '#barDemo',minWidth:150}
                            ]]
                        });

                    });
                }
            });
        });
        layui.use('table', function(){
            var table = layui.table;
            var $ = layui.$, active = {
                reload: function(){
                    //执行重载
                    table.reload('news', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            keywords: $('#keywords').val(),
                            publish_time1: $('#publish_time1').val(),
                            publish_time2: $('#publish_time2').val(),
                        }
                    }, 'data');
                }
            };
            $('#find_news').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

            //第一个实例
            table.render({
                elem: '#demo'
                ,url: "<?php echo url('News/getrecycle'); ?>" //数据接口
                ,page: true //开启分页
                ,limit:15
                ,limits:[15,20,30,40,50,60,70,80,90]
                ,id:'news'
                ,cols: [[ //表头
                    {field: 'news_id', title: 'ID', minWidth:120, width:120, fixed: 'left'}
                    ,{field: 'title', title: '标题', width:430}
                    ,{field: 'type_name', title: '类型	', width:150}
                    // ,{field: 'classname', title: '栏目', width:120, }
                    ,{field: 'status_name', title: '状态', width:150}
                    // ,{field: 'image', title: '标题图', width:120,templet: '#imageTpl'}
                    ,{field: 'view', title: '阅读量', width: 120, }
                    ,{field: 'delete_time', title: '删除时间', width:200, }
                    ,{field: 'operating1', title: '操作', toolbar: '#barDemo',minWidth:150}
                ]]
            });
            table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

                if(layEvent === 'detail'){ //查看
                    //do somehing
                } else if(layEvent === 'del'){ //删除
                    layer.confirm('确定要删除该数据吗', function(index){
                        obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        layer.close(index);
                        //向服务端发送删除指令
                        var news_id	=	obj.data.news_id;
                        $.ajax({
                            url:"<?php echo url('News/newsdel'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'news_id='+news_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '删除成功'
                                    });
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '删除失败'
                                    });
                                }
                            }
                        });
                    });
                } else if(layEvent === 'edit'){ //编辑
                    console.log(obj.data);
                    if(obj.data.type==1){
                        window.open("<?php echo url('News/edit'); ?>?id="+obj.data.id);
                    }else if(obj.data.type==2){
                        window.open("<?php echo url('News/editimage'); ?>?id="+obj.data.id);
                    }else if(obj.data.type==3){
                        window.open("<?php echo url('News/editvideo'); ?>?id="+obj.data.id);
                    }else if(obj.data.type==4){
                        window.open("<?php echo url('News/editurl'); ?>?id="+obj.data.id);
                    }
                } else if(layEvent === 'tijiao'){
                    layer.confirm('确定要提交该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/tijiao'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '提交成功'
                                    });
                                    obj.update({
                                        status_name: '已提交'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '提交失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'success'){
                    layer.confirm('确定要审核通过该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/chenggong'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '审核成功'
                                    });
                                    obj.update({
                                        status_name: '审核通过'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '审核失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'reject'){
                    layer.confirm('确定要驳回该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/reject'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '驳回成功'
                                    });
                                    obj.update({
                                        status_name: '已驳回'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '驳回失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'publish'){
                    layer.confirm('确定要发布该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/publish'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '发布成功'
                                    });
                                    obj.update({
                                        status_name: '已发布'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '发布失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'revoke'){
                    layer.confirm('确定要撤回该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/revoke'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'news_id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '撤回成功'
                                    });
                                    obj.update({
                                        status_name: '已撤回'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '撤回失败'
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'chedishanchu'){
                    layer.confirm('确定要彻底删除该线索吗？', function(index){
                        var news_id	=	obj.data.news_id;
                        $.ajax({
                            url:"<?php echo url('News/chedishanchu'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'news_id='+news_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '删除成功'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: data.s
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'restore'){
                    layer.confirm('确定要还原该线索吗？', function(index){
                        var news_id	=	obj.data.news_id;
                        $.ajax({
                            url:"<?php echo url('News/restore'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'news_id='+news_id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '还原成功'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: data.s
                                    });
                                }
                            }
                        });
                    });
                }else if(layEvent === 'tuijian'){
                    layer.confirm('确定要推荐该稿件吗？', function(index){
                        var id	=	obj.data.id;
                        $.ajax({
                            url:"<?php echo url('News/tuijian'); ?>",
                            dataType:'json',
                            type:'POST',
                            data:'id='+id,
                            success: function(data) {
                                if (data.s=='ok') {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐成功'
                                    });
                                    table.reload('news', {});
                                }else {
                                    layer.open({
                                        title: '操作信息'
                                        ,content: '推荐失败'
                                    });
                                }
                            }
                        });
                    });
                }
            });
        });
        function ToUrl(x)
        {
            if(x==1){
                var url = "<?php echo url('News/add'); ?>";
            }else if(x==2){
                var url = "<?php echo url('News/addimage'); ?>";
            }else if(x==3){
                var url = "<?php echo url('News/addvideo'); ?>";
            }else if(x==4){
                var url = "<?php echo url('News/addurl'); ?>";
            }
            location.href=url;
            return false;
        }
    </script>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="chedishanchu">彻底删除</a>
        <a class="layui-btn layui-btn-xs" lay-event="restore">还原</a>
    </script>
    <script type="text/html" id="imageTpl">
        {{#  if(d.image !='' ){ }}
        <img style="width:56px;height:31px;" src="/uploads/news/{{ d.image }}" />
        {{# } else{ }}

        {{#  } }}
    </script>

</div>
</body>
</html>
