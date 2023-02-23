<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"F:\project\pointsmall\public/../application/admin\view\goods\detailimage.html";i:1640144822;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>查看商品图片|<?php echo config('system.title'); ?></title>
      <link rel="stylesheet" type="text/css" href="/static/css/content.css"  />
      <link rel="stylesheet" type="text/css" href="/static/css/public.css"  />
      <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css"  />
      <script type="text/javascript" src="/static/js/jquery-3.1.1.min.js"></script>
      <script type="text/javascript" src="/static/js/Public.js"></script>
      <script type="text/javascript" src="/static/js/winpop.js"></script>
      <script type="text/javascript" src="/static/js/check.js"></script>
      <script type="text/javascript" src="/static/layui/layui.all.js"></script>
</head>
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
      .line{
          margin: 15px 20px;
      }
      .detailstitle {
          margin: 10px 0;
          background: url(/static/image/pointsmall/spxq.png) no-repeat left;
          background-size: contain;
          font-size: 25px;
          text-indent: 10px;
      }
      .content img{
          width: 100%;
      }
</style>
<body>
<div class="layui-row" style="margin-top:25px;">
    <div class="layui-container">
        <div class="layui-row">
            <div class="layui-carousel" id="test1" lay-filter="test1" style="margin: 0 auto">
                <div carousel-item>
                    <?php if(is_array($goodsImage_list) || $goodsImage_list instanceof \think\Collection || $goodsImage_list instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsImage_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <div><img class="layui-upload-img <?php if($vo == ''): ?>layui-hide<?php endif; ?>" id="previewimg" src="/uploads/goods/<?php echo $vo; ?>" style="width: 100%; height:100%;object-fit:contain;"></div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>
        </div>
        <div class="layui-row line">
            <span style="font-size: 18px"><?php echo $goodsInfo['goods_name']; ?></span>
        </div>
        <div class="layui-row line" style="font-size: 16px">
            <span style="color: #ffa200;font-size: 20px;letter-spacing: 1.5px;"><?php echo $goodsInfo['goods_points']; ?></span>  积 分
        </div>
        <div class="layui-row line">
            <div class="detailstitle">
                商品详情
            </div>
        </div>
        <div class="layui-row line content" ><?php echo $goodsInfo['goods_desc']; ?>
        </div>
    </div>

    <script>
    //Demo
    layui.use(['carousel', 'form'], function() {
        var carousel = layui.carousel
            , form = layui.form;
        //常规轮播
        carousel.render({
            elem: '#test1'
            , arrow: 'always'
            ,width:'100%'
            ,height:'360px'
            ,autoplay:false
        });
    });
  </script>
</div>
</body>
</html>
