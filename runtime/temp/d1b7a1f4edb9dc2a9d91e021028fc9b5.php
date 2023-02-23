<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"F:\project\pointsmall\public/../application/admin\view\news\push_news.html";i:1636941641;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>推送线索|<?php echo config('system.title'); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
<body>
<div class="layui-fluid" style="margin-top:20px;">
	<div class="layui-row">
		<div class="layui-col-md4">
			<div id="tree"></div>
			<div class="layui-col-md3" style="margin-top:20px;text-align:center;">
				<button type="button" class="layui-btn" id="find_news" data-type="reload">一键推送</button>
    		</div>
    	</div>
		<div class="layui-col-md8">
			<span class="layui-breadcrumb">
			  <a><cite>线索管理</cite></a>
			  <a><cite>已发布线索</cite></a>
			  <a><cite>推送线索</cite></a>
			  <a><cite>已推送列表</cite></a>
			</span>
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
		layui.use('form', function(){
			  var form = layui.form; //导航的hover效果、二级菜单等功能，需要依赖element模块
			  
			});
		layui.use('element', function(){
			  var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
			  element.render();
			  //监听导航点击
			  element.on('nav(demo)', function(elem){
			    //console.log(elem)
			    layer.msg(elem.text());
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
          showCheckbox:true,
	      onlyIconControl:true,
		  data:<?php echo $clues_tree_list; ?>,
		  id: 'demoId', //定义索引
		  click: function(obj){
			    //console.log(obj.data); //得到当前点击的节点数据
			    //console.log(obj.state); //得到当前节点的展开状态：open、close、normal
			    //console.log(obj.elem); //得到当前节点元素
			    //console.log(obj.data.children); //当前节点下是否有子节点
			  	//obj.data.id
              $('#keywords').val('');
              $('#publish_time1').val('');
              $('#publish_time2').val("");

              // layui.use('table', function(){
			  //   	  var table = layui.table;
			  //
			  //   	  //第一个实例
			  //   	  table.render({
			  //   	    elem: '#demo'
			  //   	    ,url: "<?php echo url('News/getPushNewsList'); ?>" //数据接口
			  //   	    ,where:{region_id: obj.data.id,news_id:'<?php echo \think\Request::instance()->param('news_id'); ?>'}
			  //   	    ,page: true //开启分页
			  //   	    ,limit:15
			  //   	    ,limits:[15,20,30,40,50,60,70,80,90]
			  //   	    ,id:'news'
			  //   	    ,cols: [[ //表头
				// 			  {field: 'i', title: '序号',  fixed: 'left',minWidth:60,width:80,templet: '#idTpl'}
				// 			  ,{field: 'region_name_s', minWidth:60,width:280,title: '所在地区'}
				// 			  ,{field: 'department_name',minWidth:60,width:230, title: '单位名称', }
				// 			  ,{field: 'operating1',minWidth:60,title: '操作',toolbar: '#barDemo'}
			  //   	    ]]
			  //   	  });
			  //
			  //   	});
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
        	var tree = layui.tree;
        	var checkData = tree.getChecked('demoId');
        	var news_id = '<?php echo \think\Request::instance()->param('news_id'); ?>';
        	console.log(checkData);
			$.ajax({
				url:"<?php echo url('News/push_news_do'); ?>",
				dataType:'json',
				type:'POST',
				data:{news_id:news_id,region_id:checkData},
				success: function(data) {
					if (data.s=='ok') {
						layer.open({
							  title: '操作信息'
							  ,content: '推送成功'
							}); 
						table.reload('news');
					}else {
						layer.open({
							  title: '操作信息'
							  ,content: '推送失败'
						});  
					}
				}
			});
        });
        //第一个实例
    	  table.render({
    	    elem: '#demo'
    	    ,url: "<?php echo url('News/getPushNewsList'); ?>" //数据接口
    	    ,page: true //开启分页
    	    ,limit:15
    	    ,limits:[15,20,30,40,50,60,70,80,90]
    	    ,id:'news'
    	    ,where:{news_id:'<?php echo \think\Request::instance()->param('news_id'); ?>'}
    	    ,cols: [[ //表头
				  {field: 'i', title: '序号',  fixed: 'left',minWidth:60,width:80,templet: '#idTpl'}
				  ,{field: 'region_name_s', minWidth:60,width:280,title: '所在地区'}
				  ,{field: 'department_name',minWidth:60,width:230, title: '单位名称', }
				  ,{field: 'operating1',minWidth:80,title: '操作',toolbar: '#barDemo'}
    	    ]]
    	  });
    	  table.on('tool(test)', function(obj){ //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
    		  var data = obj.data; //获得当前行数据
    		  var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
    		  var tr = obj.tr; //获得当前行 tr 的 DOM 对象（如果有的话）
    		 
    		  if(layEvent === 'detail'){        console.log(obj.data);
                  // if(obj.data.type==1){
                  //     window.open("<?php echo url('News/detail'); ?>?id="+obj.data.news_id);
                  // }else if(obj.data.type==2){
                  //     window.open("<?php echo url('News/detailimage'); ?>?id="+obj.data.news_id);
                  // }else if(obj.data.type==3){
                  //     window.open("<?php echo url('News/detailvideo'); ?>?id="+obj.data.news_id);
                  // }else if(obj.data.type==4){
                  //     window.open("<?php echo url('News/detailurl'); ?>?id="+obj.data.news_id);
                  // }
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
    		  } else if(layEvent === 'del'){ //删除
    		    layer.confirm('确定要删除该数据吗', function(index){
    		      obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
    		      layer.close(index);
    		      //向服务端发送删除指令
                    var push_id	=	obj.data.push_id;
					$.ajax({
						url:"<?php echo url('News/pushNewsDel'); ?>",
						dataType:'json',
						type:'POST',
						data:'push_id='+push_id,
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
            		  }else if(layEvent === 'viewChooseRecord'){
                  var news_id	=obj.data.news_id;
                  layer.open({
                      type: 2,
                      title: '选用记录',
                      shadeClose: true,
                      shade: false,
                      maxmin: true, //开启最大化最小化按钮
                      area: ['90%', '90%'],
                      content: "<?php echo url('News/choose_list'); ?>?news_id="+news_id
                  });
              }else if(layEvent === 'pushNews'){
                  var news_id	=obj.data.news_id;
                  layer.open({
                      type: 2,
                      title: '推送线索',
                      shadeClose: true,
                      shade: false,
                      maxmin: true, //开启最大化最小化按钮
                      area: ['90%', '90%'],
                      content: "<?php echo url('News/pushNews'); ?>?news_id="+news_id
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
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="imageTpl">
		{{#  if(d.image !='' ){ }}
			<img style="width:56px;height:31px;" src="/uploads/news/{{ d.image }}" />
		{{# } else{ }}  
			  
  		{{#  } }}
	</script>
	<script type="text/html" id="idTpl">
		{{ d.LAY_INDEX }}
	</script>
	<!-- LAY_TABLE_INDEX  从0开始  d.LAY_INDEX从1开始第二页继续编号 -->
</div>
</body>
</html>
