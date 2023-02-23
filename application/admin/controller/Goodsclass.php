<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Session;

class Goodsclass extends Admin
{
    public function index()
    {
        $keywords = input('request.keywords');

        $where_d = array();
        if($keywords!=""){
            $where_d['category_name'] = array("LIKE","%$keywords%");
        }
        $pointsgoodscategory = new \app\common\model\Pointsgoodscategory;
        $list = $pointsgoodscategory->order("sort ASC")->paginate();
        $department_list= $list->toArray();
        $this->assign('department_list',$department_list);
        $this->assign('list',$list);
        $this->assign('pointsgoodscategory',$pointsgoodscategory);
        return $this->fetch();
    }
    //新增商品类别
    public function add(){
        parent::win_userauth(356);
        $pointsgoodscategory = new \app\common\model\Pointsgoodscategory;
        return $this->fetch();
    }
    public function getGoodsclassList()
    {
        parent::userauth2(355);
        $pointsgoodscategory = new \app\common\model\Pointsgoodscategory;
        $keywords = input('request.keywords');
        $limit    = input('request.limit');
        $page     = input('request.page');
        $where_d = array();
        if($keywords!=""){
            $where_d['category_name'] = $keywords;
        }
        $where_d['limit'] = $limit;
        $where_d['page']  = $page;
        $department_list = $pointsgoodscategory->getgoodscategoryList($where_d);
//        $list = $pointsgoodscategory->where($where_d)->order('sort asc')->paginate($limit);
//        $department_list= $list;

        $result = array("code" => 0, "count" => $department_list['total'], "data" => $department_list['data']);
        echo json_encode($result);
        exit;
    }
    //添加处理
    public function add_do() {
        //验证用户权限
        parent::win_userauth(356);
        if (request()->isPost()) {
            $data	=	$member_data	= array();
            $data['category_name']    	= input('post.category_name');
            $data['is_enable']       	= input('post.is_enable');
            $pointsgoodscategory = new \app\common\model\Pointsgoodscategory;
            $goods_category_info = $pointsgoodscategory->where(["category_name" => $data['category_name']])->find();
            if(!empty($goods_category_info)){
//                $this->error("该商品类别已存在!");
                return array('s'=>'该商品类别已存在');
            }
            if ($pointsgoodscategory->save($data)) {
                $goods_category_id = $pointsgoodscategory->category_id;
                parent::operating(request()->path(),0,'新增商品类别：'.$data['service_name']);
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'新增失败：'.$pointsgoodscategory->getError());
                return array('s'=>'新增失败');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            $this->error('非法请求');
        }
    }
    //修改商品类别资料
    public function edit() {
        parent::win_userauth(357);
        $category_id = input('category_id');
        if ($category_id=='' || !is_numeric($category_id)) {
//            parent::operating(request()->path(),1,'参数错误');
            $this->error('参数ID类型错误，请关闭本窗口');
        }
        //查出相应数据
        $pointsgoodscategory = new \app\common\model\Pointsgoodscategory;
        $result = $pointsgoodscategory->where(["category_id" =>$category_id])->find();
        if (!$result) {
            parent::operating(request()->path(),1,'没有找到数据：'.$category_id);
            $this->error('不存在你要修改的数据，请关闭本窗口');
        }
//        $activity_category_info=$pointsgoodscategory->where("id= '{$parent_id}'")->find();
//        $this->assign('dmenu_list',$dmenu_list);
        $this->assign('result',$result);
        return $this->fetch();
    }
    //修改商品类别资料处理
    public function edit_do() {
        //验证用户权限
        parent::win_userauth(357);
        if (request()->isPost()) {
            $data=array();
            //活动类别资料信息
            $data['category_id']              	= input('category_id');
            $data['category_name']    	= input('post.category_name');
            $data['is_enable']       	= input('post.is_enable');

            $pointsgoodscategory	=   new \app\common\model\Pointsgoodscategory;
            $goods_category_info = $pointsgoodscategory->where(["category_name" => $data['category_name'], "category_id"=>["neq",$data['category_id']]] )->find();
            if(!empty($goods_category_info)){
                $this->error("该商品类别已存在!");
            }
            try{
                $pointsgoodscategory->save($data,"category_id='{$data['category_id']}'");
                parent::operating(request()->path(),0,'更新活动类别信息：'.$data['category_name']);
                return array('s'=>'ok');
            }catch(\Exception $e){
                $this->error($e->getCode());
                parent::operating(request()->path(),1,'更新失败：'.$data['service_name']);
            }
            $this->success('商品类别资料更新成功',url('Goodsclass/edit','category_id='.$data['category_id']));
        }else {
            parent::operating(request()->path(),1,'非法请求');
            $this->error('非法请求');
        }
    }
    //
    public function enable() {
        parent::userauth(359);
        if (request()->isPost()) {
            $data = $content_data = array();
            $category_id = input('category_id');
            $data['is_enable'] = input('post.is_enable');
            $pointsgoodscategory	=   new \app\common\model\Pointsgoodscategory;
            $pointsgoodscategory ->save($data,"id=".$category_id);
            return array('s'=>'ok');
        }
    }
    //删除活动类别资料到回收站
    public function service_del() {
        parent::userauth(358);
        //判断是否是ajax请求
        if (request()->isAjax()) {
            $category_id  =   input('category_id');
            if ($category_id=='' || !is_numeric($category_id)) {
//                parent::operating(request()->path(),1,'参数错误');
                return array('s'=>'参数ID类型错误');
            }else {
                $pointsgoodscategory =   new \app\common\model\Pointsgoodscategory();
                $where  =   array('category_id'=>$category_id);
                if ($pointsgoodscategory->where($where)->value('category_id')) {
                    $pointsgoodscategory->where($where)->delete();
//                    parent::operating(request()->path(),0,'删除成功：'.$id);
                    return array('s'=>'ok');
                }else {
                    parent::operating(request()->path(),1,'删除失败：'.$this->getError());
                    return array('s'=>$this->getError());
                }
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            $this->error('非法请求');
        }
    }
    //批量删除活动类别资料到回收站
    public function service_indel() {
        //验证用户权限
//        parent::userauth(205);
        if (request()->isAjax()) {
            if (!$delid=explode(',',input('post.delid'))) {
                return array('s'=>'请选中后再删除');
            }
            //将最后一个元素弹出栈
            array_pop($delid);
            $id=join(',',$delid);
            $pointsgoodscategory  =   new \app\common\model\Pointsgoodscategory();
            $map['id'] = array('in',$id);
            if ($pointsgoodscategory->where($map)->delete()) {
                parent::operating(request()->path(),0,'删除成功：'.$id);
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'删除失败：'.$id);
                return array('s'=>'删除失败');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            $this->error('非法请求');
        }
    }
    //批量删除
    public function service_c_indel() {
        //验证用户权限
        //parent::userauth(131);
        if (request()->isAjax()) {
            if (!$delid=explode(',',input('post.delid'))) {
                return array('s'=>'请选中后再删除');
            }
            //将最后一个元素弹出栈
            array_pop($delid);
            $id=join(',',$delid);
            $pointsgoodscategory  =   new \app\common\model\Pointsgoodscategory();
            if ($pointsgoodscategory->where('id','IN',$id)->delete()) {
                parent::operating(request()->path(),0,'删除成功');
                return array('s'=>'ok');
            }else {
                parent::operating(request()->path(),1,'删除失败');
                return array('s'=>'删除失败');
            }
        }else {
            parent::operating(request()->path(),1,'非法请求');
            R('Public/errjson',array('非法请求'));
        }
    }
    public function saveCols() {
        //验证用户权限
        //parent::userauth(255);
        //判断是否是ajax请求
        if (request()->isAjax()) {
            $field 	 	 = input('post.field');
            $value 	 	 = input('post.value');
            $category_id 	 = input('post.categoryid');

//            $position_id = input('post.position_id');
            $pointsgoodscategory  =   new \app\common\model\Pointsgoodscategory();
            $goodscategory_info =$pointsgoodscategory->where(['category_id'=>$category_id])->find();
            if ($pointsgoodscategory->save(array($field=>$value),"category_id=".$category_id)) {
                if($field == "sort"){
                    if($goodscategory_info['sort']>$value){
                        //排序往前调整
                        $id_batch = $pointsgoodscategory->where("sort>='{$value}'AND sort<='{$goodscategory_info['sort']}'AND  category_id!='{$category_id}'")->column('category_id');
                        if(!empty($id_batch)) {
                            db('points_goods_category')->where('category_id', 'IN', $id_batch)->setInc('sort');
                        }
                    }
                    if($goodscategory_info['sort']<$value){
                        //排序往后调整
                        $id_batch = $pointsgoodscategory->where("sort<='{$value}'AND sort>='{$goodscategory_info['sort']}'AND  category_id!='{$category_id}'")->column('category_id');
                        if(!empty($id_batch)){
                            db('points_goods_category')->where('category_id', 'IN',$id_batch)->where("sort >=2")->setDec('sort');
                        }
                    }
                }
//                parent::operating(request()->path(),0,'推荐信息修改成功');
                return array('s'=>'ok');
            }else {
//                parent::operating(request()->path(),1,'推荐信息修改失败');
                return array('s'=>'数据不存在');
            }
        }else {
//            parent::operating(request()->path(),1,'非法请求');
            return array('s'=>'非法请求');
        }
    }
}
