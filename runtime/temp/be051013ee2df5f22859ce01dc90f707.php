<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"F:\project\pointsmall\public/../application/admin\view\news\chooserecords.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>树形列表|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  >
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  >
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" media="all">
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
    <style>
        .detail_news:hover{
            cursor:pointer;
        }
    </style>
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
                    <div class="layui-form-inline layui-col-md2">
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
        layui.use('form', function(){
            var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
            form.render(); //更新全部
        });
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
		  data:<?php echo $clues_tree_lists; ?>,
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
			    // console.log(obj.data); //得到当前点击的节点数据
			    //console.log(obj.state); //得到当前节点的展开状态：open、close、normal
			    //console.log(obj.elem); //得到当前节点元素
			    //console.log(obj.data.children); //当前节点下是否有子节点
			  	//obj.data.id
              $('#keywords').val('');
              $('#subject').val('');
              $('#publish_time1').val('');
              $('#publish_time2').val("");

              layui.use('table', function(){
			    	  var table = layui.table;
			    	  
			    	  //第一个实例
			    	  table.render({
			    	    elem: '#demo'
			    	    ,url: "<?php echo url('News/getrecordsList'); ?>" //数据接口
			    	    ,where:{department_id: obj.data.id}
			    	    ,page: true //开启分页
			    	    ,limit:15
			    	    ,limits:[15,20,30,40,50,60,70,80,90]
			    	    ,id:'news'
			    	    ,cols: [[ //表头
                              {field: 'news_id', title: 'ID', minWidth:60, width:60, fixed: 'left'}
                              ,{field: 'title', title: '线索标题', width:400, toolbar: '#barDemo_t'}
                              ,{field: 'department_name', title: '线索来源', width:300}
                              ,{field: 'source', title: '选用单位', width:300,}
                              ,{field: 'choose_time', title: '选用时间', width:330, }

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
                        subject: $('#subject').val(),
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
    	    ,url: "<?php echo url('News/getrecordsList'); ?>" //数据接口
    	    ,page: true //开启分页
    	    ,limit:15
    	    ,limits:[15,20,30,40,50,60,70,80,90]
    	    ,id:'news'
              ,cols: [[ //表头
                  {field: 'news_id', title: 'ID', minWidth:60, width:60, fixed: 'left'}
                  ,{field: 'title', title: '线索标题', width:400, toolbar: '#barDemo_t'}
                  ,{field: 'department_name', title: '线索来源', width:300}
                  ,{field: 'source', title: '选用单位', width:300,}
                  ,{field: 'choose_time', title: '选用时间', width:330, }

              ]]
    	  });
    	  table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
    		  var data = obj.data; //获得当前行数据
    		  var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    		  var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）

    		  if(layEvent === 'detail'){        console.log(obj.data);

                  if(obj.data.type==1){
                      var url = "<?php echo url('News/detail'); ?>?id="+obj.data.news_id;
                  }else if(obj.data.type==2){
                      var url = "<?php echo url('News/detailimage'); ?>?id="+obj.data.news_id;
                  }else if(obj.data.type==3){
                      var url = "<?php echo url('News/detailvideo'); ?>?id="+obj.data.news_id;
                  }else if(obj.data.type==4) {
                      var url = "<?php echo url('News/detailurl'); ?>?id=" + obj.data.news_id;
                  }
                  layer.open({
                      type: 2,
                      title:'新闻线索',
                      area: ['80%', '90%'],
                      fixed: false, //不固定
                      maxmin: true,
                      content: url,
                      cancel: function(){
                          table.reload('news', {});
                      }
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
    <script type="text/html" id="barDemo_t">
        <a class="detail_news" lay-event="detail">{{d.title}}</a>
    </script>
    <script type="text/html" id="barDemo">
        {{#  if(d.status == 10 ){ }}
<!--        <a class="layui-btn layui-btn-xs" lay-event="revoke">撤稿</a>-->
        {{#  } }}
        {{#  if(d.status !== 9){ }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
        <a class="layui-btn layui-btn-xs" lay-event="viewChooseRecord">选用记录</a>
        <a class="layui-btn layui-btn-xs" lay-event="viewRecord">阅读记录</a>
        <a class="layui-btn layui-btn-xs" lay-event="detail">查看</a>
        {{#  if(d.is_recommend !== 1){ }}
        <a class="layui-btn layui-btn-xs" lay-event="recommend">设为重点线索</a>
        {{#  } }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="pushNews">推送</a>
        {{#  } }}
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
