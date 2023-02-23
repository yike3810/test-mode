<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"F:\project\pointsmall\public/../application/admin\view\goods\edit.html";i:1667640018;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>修改信息</title>
    <link rel="stylesheet" type="text/css" href="/static/css/content.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css"/>
    <link rel="stylesheet" type="text/css" href="/static/layui-v2.6.8/css/layui.css"/>
    <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/Public.js"></script>
    <script type="text/javascript" src="/static/js/winpop.js"></script>
    <script type="text/javascript" src="/static/js/check.js"></script>
    <script type="text/javascript" src="/static/layui-v2.6.8/layui.js"></script>
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
<style>
    .layui-table img {
        max-width: 100%;
    }
    .layui-table td, .layui-table th {
        text-align: center;
    }
</style>
<body>
<div class="layui-row" style="margin-top:25px;">
    <form class="layui-form layui-col-md10" id="activity" enctype="multipart/form-data">
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>商品名称：</label>
            <div class="layui-input-block">
                <input type="text" name="goods_name" required lay-verify="required" value="<?php echo $result['goods_name']; ?>"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>原价：</label>
            <div class="layui-input-block">
                <input type="number" min="1" name="goods_price" value="<?php echo $result['goods_price']; ?>" required lay-verify="required"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>兑换所需积分：</label>
            <div class="layui-input-block">
                <input type="number" min="1" name="goods_points" value="<?php echo $result['goods_points']; ?>" required lay-verify="required"
                       autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>是否推荐：</label>
            <div class="layui-input-block">
                <input type="radio" name="goods_commend" value="0" title="否" class="status" <?php if($result['goods_commend'] == 0): ?>checked<?php endif; ?>/>
                <input type="radio" name="goods_commend" value="1" title="是" class="status" <?php if($result['goods_commend'] == 1): ?>checked<?php endif; ?>/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品条码：</label>
            <div class="layui-input-block">
                <input type="text" name="barcode" value="<?php echo $result['barcode']; ?>" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品封面图片：</label>
            <div class="layui-input-block">
                <button class="layui-btn" type="button" id="image" name="goods_image">上传</button>
                <div class="layui-upload-list">
                    <img class="layui-upload-img" id="previewimg" src="/uploads/goods/<?php echo $result['goods_image']; ?>"
                         style="width: 200px; height:220px">
                    <p id="previewText"></p>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">商品详情图片：</label>
            <div class="layui-input-block">
                <button type="button" class="layui-btn layui-btn-normal" id="testList">选择多文件</button>
                <button type="button" class="layui-btn" id="testListAction">开始上传</button>
                <input type="hidden" name="goods_id" id="meeting_id" value="<?php echo $result['goods_id']; ?>">
                <input type="hidden" name="image_info2" id="image_info2">
                <input type="hidden" name="image_have" id="image_have">
                <div class="layui-upload-list" style="max-width: 1000px;">
                    <table class="layui-table">
                        <colgroup>
                            <col width="150">
                            <col width="80">
                            <col width="240">
<!--                            <col width="80">-->
                            <col width="190">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>图片</th>
                            <th>大小</th>
                            <th>上传进度</th>
<!--                            <th>排序</th>-->
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="demoList">
                        <?php if($image_list == ''): ?>
                        <tr class="tr"><td class="tc" colspan="5">暂无数据，等待添加～！</td></tr>
                        <?php else: if(is_array($image_list) || $image_list instanceof \think\Collection || $image_list instanceof \think\Paginator): $i = 0; $__LIST__ = $image_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <td>
                                <img class="layui-upload-img"  src="/uploads/goods/<?php echo $vo['image_path']; ?>"
                                     style="width: 100%; height:100px">
                            </td>
                            <td>未知</td>
                            <td><div class="layui-progress" lay-filter="progress-demo-' + index + '"><div class="layui-progress-bar" lay-percent="100%"></div></div></td>
<!--                            <td class="modify" data-title="<?php echo $vo['image_id']; ?>"><?php echo $vo['sort']; ?></td>-->
                            <td>
                                <a href="<?php echo $vo['image_id']; ?>" class="del"><button class="layui-btn layui-btn-xs layui-btn-danger">删除</button></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>库存：</label>
            <div class="layui-input-block">
                <input type="number" min="1" name="goods_storage" value="<?php echo $result['goods_storage']; ?>" required
                       lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>是否限制兑换数量：</label>
            <div class="layui-input-block">
                <input type="radio" name="goods_islimit" value="0" title="否" class="status" <?php if($result['goods_islimit'] == 0): ?>checked<?php endif; ?>/>
                <input type="radio" name="goods_islimit" value="1" title="是" class="status" <?php if($result['goods_islimit'] == 1): ?>checked<?php endif; ?>/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">限制兑换数量：</label>
            <div class="layui-input-block">
                <input type="number" min="1" name="goods_limitnum" value="<?php echo $result['goods_limitnum']; ?>"
                        autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>商品类型：</label>
            <div class=" layui-input-block" >
                <select name="category_name" lay-verify="required"  >
                    <?php if(is_array($clist) || $clist instanceof \think\Collection || $clist instanceof \think\Paginator): $i = 0; $__LIST__ = $clist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;if($vo['id'] == $result['category_id']): ?>
                            <option selected value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>类型：</label>
            <div class="layui-input-block">
                <input type="radio" name="type" value="1" title="实物" class="status" <?php if($result['type'] == 1): ?>checked<?php endif; ?>/>
                <input type="radio" name="type" value="2" title="优惠券" class="status" <?php if($result['type'] == 2): ?>checked<?php endif; ?>/>
<!--                <input type="radio" name="type" value="3" title="卡密" class="status" <?php if($result['type'] == 3): ?>checked<?php endif; ?>/>-->
<!--                <input type="radio" name="type" value="4" title="充值" class="status" <?php if($result['type'] == 4): ?>checked<?php endif; ?>/>-->
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><i class="required">*</i>商品描述：</label>
            <div class="layui-input-block">
                <script id="container" name="goods_desc" lay-verify="required" type="text/plain">
                        <?php echo $result['goods_desc']; ?>

                </script>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn sub" lay-submit lay-filter="sub" id="sub" style="margin-left: 20px">提交
                </button>
                <input type="hidden" name="goods_id" id="goods_id" value="<?php echo $result['goods_id']; ?>">
                <input type="hidden" name="image_info" id="image_info" value="<?php echo $result['goods_image']; ?>">
<!--                <input type="hidden" name="image_info2" id="image_info2" value="">-->
            </div>
        </div>
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
    layui.use(['form', 'upload', 'element'], function () {
        var form = layui.form, upload = layui.upload, element = layui.element
        var F_info = [];
        var F_name = [];
        form.render();
        //创建一个编辑器
        // var editIndex = layedit.build('LAY_demo_editor');
        //监听提交
        form.on('submit(sub)', function (data) {
            // $("#image_info2").val(array);
            $.ajax({
                url: "<?php echo url('edit_do'); ?>",
                dataType: 'json',
                type: 'POST',
                data: data.field,
                success: function (data) {
                    if (data.s == "请输入限制兑换数量") {
                        layer.msg("请输入限制兑换数量 !", {icon: 0});
                    } else {
                        if (data.s == 'ok') {
                            layer.msg("修改成功", {}, function () {
                                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                                parent.location.reload();
                                parent.layer.close(index); //再执行关闭
                            });
                        } else {
                            layer.msg(data.s);
                        }
                    }
                }
            });
            return false;
        });
        //执行实例
        var uploadInst = upload.render({
            elem: '#image' //绑定元素
            , url: "<?php echo url('Goods/upload'); ?>" //上传接口
            , accept: 'images',
            auto: true,
            //bindAction: '#sub', // 提交按钮
            choose: function (obj) {
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    $('#previewimg').attr('src', result); //图片链接（base64）
                    $('#image-div').removeClass("layui-hide"); //图片链接（base64）
                    //$('#image-div').addClass("layui-show"); //图片链接（base64）
                });
            },
            done: function (res) {
                //上传完毕回调
                if (res.code == 0) {
                    //do something （比如将res返回的图片链接保存到表单的隐藏域）
                    layer.msg("图片上传失败");
                } else {
                    layer.msg("图片上传成功");
                    $("#image_info").val(res.savename);
                }
            },
            error: function () {
                //请求异常回调
                layer.msg("上传失败");

            }
        });
        //多图片上传
        //演示多文件列表
        var uploadListIns = upload.render({
            elem: '#testList'
            , elemList: $('#demoList') //列表元素对象
            , url: "<?php echo url('Goods/upload'); ?>"
            , accept: 'images'
            , multiple: true
            , number: 0
            , auto: false
            , bindAction: '#testListAction'
            , choose: function (obj) {
                var that = this;
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                // var F_name = [];
                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<tr id="upload-' + index + '">'
                        , '<td>' + '<img src="'+ result +'" alt="'+ file.name +'" class="layui-upload-img" style="width: 100%; height:100px">' + '</td>'
                        , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                        , '<td><div class="layui-progress" lay-filter="progress-demo-' + index + '"><div class="layui-progress-bar" lay-percent=""></div></div></td>'
                        // , '<td></td>'
                        , '<td>'
                        , '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        , '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        , '</td>'
                        , '</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    // F_name.push(file.name);
                    $("#image_have").val(file.name);

                    that.elemList.append(tr);
                    element.render('progress'); //渲染新加的进度条组件
                });
            }
            , done: function (res, index, upload) { //成功的回调
                var that = this;

                //if(res.code == 0){ //上传成功
                if (res.code == 0) {
                    //do something （比如将res返回的图片链接保存到表单的隐藏域）
                    layer.msg("文件上传失败");
                }else{
                    // layer.msg("文件上传成功");
                    F_info.push(res.savename);
                    $("#image_info2").val(F_info);
                }
                var tr = that.elemList.find('tr#upload-' + index)
                    , tds = tr.children();
                tds.eq(4).html(''); //清空操作
                delete this.files[index]; //删除文件队列已经上传成功的文件
                return;
                //}
                this.error(index, upload);
            }
            , allDone: function (obj) { //多文件上传完毕后的状态回调
                // console.log('ddd',obj)
            }
            , error: function (index, upload) { //错误回调
                var that = this;
                layer.msg("上传失败");
                var tr = that.elemList.find('tr#upload-' + index)
                    , tds = tr.children();
                tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
            , progress: function (n, elem, e, index) { //注意：index 参数为 layui 2.6.6 新增
                element.progress('progress-demo-' + index, n + '%'); //执行进度条。n 即为返回的进度百分比
            }
        });
        //删除多图
        $('#demoList .del').click(function(event) {
            event.preventDefault();
            if (!confirm('确定要删除该数据吗？')) {
                return false;
            }
            var image_id=$(this).attr('href').replace(/[^0-9]/ig,"");
            if (image_id=='' || isNaN(image_id)) {
                layer.msg("ID参数不正确")
                // wintq('ID参数不正确',3,1000,1,'');
                return false;
            }else {
                // wintq('正在删除，请稍后...',4,20000,0,'');
                $.ajax({
                    url:"<?php echo url('Goods/imagedel'); ?>",
                    dataType:'json',
                    type:'POST',
                    data:'post=ok&image_id='+image_id,
                    success: function(data) {
                        if (data.s=='ok') {
                            layer.msg("删除成功")
                            location.reload();
                            // wintq('删除成功',1,1500,1,'?');
                        }else {
                            layer.msg("删除失败")
                            // wintq(data.s,3,1500,1,'');
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>
