<?php


namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Url;

class Commodity extends Admin
{
    public function index()
    {
        return $this->fetch();
    }

    public function getCommodityList(){
        $keywords = input("request.keywords",'','htmlspecialchars,addslashes,strip_tags');
        $where = array();
        if($keywords!=""){
            $where['real_name|phone|address'] = array("LIKE","%$keywords%");
        }
        $commodity = new \app\common\model\Commodity;
        $region = new \app\common\model\Region;
        $list = $commodity->where($where)->order("addtime DESC")->paginate();
        $commodity_list = $list->toArray();
        foreach ($commodity_list['data'] as $k => $v) {
            $province = $region->where(['region_id'=>$v['province_id']])->value('region_name');
            $city = $region->where(['region_id'=>$v['city_id']])->value('region_name');
            $district= $region->where(['region_id'=>$v['district_id']])->value('region_name');
            $commodity_list['data'][$k]['address']= $province.$city.$district.$v['address'];
        }
        $result = array("code"=>0,"count" => $commodity_list['total'],"data"=>$commodity_list['data']);
        echo json_encode($result);
        exit;
    }
    public function addcommodity()
    {
        $region = new \app\common\model\Region;
        $province_list = $region->where("region_type = 1")->column('');
        if ($province_list['province_id'] != ''){
            $city_list = $region->where("region_type = 2 AND parent_id = '{$province_list['province_id']}' ")->column('');
        }else{
            $city_list = array();
        }
        if ($city_list['city_id'] != ''){
            $district_list = $region->where("region_type = 3 AND parent_id = '{$city_list['city_id']}' ")->column('');
        }else{
            $district_list = array();
        }

        $this->assign('province_list',$province_list);
        $this->assign('city_list',$city_list);
        $this->assign('district_list',$district_list);

        return $this->fetch();

    }
    public function get_area_list(){
        $region = new \app\common\model\Region;
        $id = input('post.id');
        $region_list = $region->where("parent_id = '{$id}' ")->column('');
        $status = 1; $html='';
        if(!empty($region_list)){
            foreach($region_list as $key=>$value){
                $html .= "<option value='{$value['region_id']}'>{$value['region_name']}</option>";
            }
            $message = '';
        }else{
            $status = -1;
            $message = '没有找到地址';
        }
        $region_list = array("status" => $status, "data" => $html, "message" => $message);
        echo json_encode($region_list, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function addcommodity_do()
    {
        $data = array();
        if (request()->isAjax()) {
            //自动完成验证与新增
            $commodity = new \app\common\model\Commodity;
            $data['commodity_name'] = input('commodity_name');
            $data['contact'] = input('contact');
            $data['phone'] = input('phone');
            $data['addtime'] = date("Y-m-d H:i:s");
            $data['province_id'] = input('post.province');
            $data['city_id'] = input('post.city');
            $data['district_id'] = input('post.district');
            $data['address'] = input('address');
            $commodity_info2 = $commodity->where("phone ='{$data['phone']}'")->find();
            $commodity_info = $commodity->where("commodity_name = '{$data['commodity_name']}'")->find();
            if(!empty($commodity_info))
            {
                return array('s'=>'该商家已存在!');
            }
            if(!empty($commodity_info2))
            {
                //判断手机号是否唯一
                return array('s'=>'商家联系人电话不能重复');
            }
            if ($commodity->data($data)) {
                $commodity->save();
                parent::operating(request()->path(), 0, '新增成功');
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '新增失败');
                return array('s' => $commodity->getError());
            }
        }
    }

    public function edit(){
        $id = input('id');
        if ($id == '' || !is_numeric($id)) {
            $this->error('参数ID类型错误，请关闭本窗口');
        }
        $commodity_id = intval($id);
        $where['id'] = $commodity_id;
        $commodity = new \app\common\model\Commodity;
        $result = $commodity->where($where)->find();
        if (!$result) {
            parent::operating(request()->path(), 1, '没有找到数据：');
            $this->error('没有找到相关数据');
        }
        $region = new \app\common\model\Region;
        $province_list = $region->where("region_type = 1")->column('');
        if ($result['province_id'] != ''){
            $city_list = $region->where("region_type = 2 AND parent_id = '{$result['province_id']}' ")->column('');
        }else{
            $city_list = array();
        }
        if ($result['city_id'] != ''){
            $district_list = $region->where("region_type = 3 AND parent_id = '{$result['city_id']}' ")->column('');
        }else{
            $district_list = array();
        }
        $this->assign('result', $result);
        $this->assign('province_list',$province_list);
        $this->assign('city_list',$city_list);
        $this->assign('district_list',$district_list);
        return $this->fetch();
    }

    public function editcommodity_do()
    {
        $data = array();
        if (request()->isAjax()) {
            //自动完成验证与新增
            $commodity = new \app\common\model\Commodity;
            $data['commodity_name'] = input('commodity_name');
            $data['contact'] = input('contact');
            $data['phone'] = input('phone');
            $data['addtime'] = date("Y-m-d H:i:s");
            $data['province_id'] = input('post.province');
            $data['city_id'] = input('post.city');
            $data['district_id'] = input('post.district');
            $data['address'] = input('address');
            $commodity_info2 = $commodity->where("phone ='{$data['phone']}'")->find();
            $commodity_info = $commodity->where("commodity_name = '{$data['commodity_name']}'")->find();
            if(!empty($commodity_info))
            {
                return array('s'=>'该商家已存在!');
            }
            if(!empty($commodity_info2))
            {
                //判断手机号是否唯一
                return array('s'=>'商家联系人电话不能重复');
            }
            if ($commodity->data($data)) {
                $commodity->save();
                parent::operating(request()->path(), 0, '修改成功');
                return array('s' => 'ok');
            } else {
                parent::operating(request()->path(), 1, '修改失败');
                return array('s' => $commodity->getError());
            }
        }
    }

    public function commodity_del()
    {
        if (request()->isAjax()) {
            $commodity_id = input('id');
            if ($commodity_id == '' || !is_numeric($commodity_id)) {
                return array('s' => '参数ID类型错误');
            } else {
                $commodity = new \app\common\model\Commodity;
                $where = array('id' => $commodity_id);
                if ($commodity->where($where)->value('id')) {
                    $commodity->where($where)->delete();
                    return array('s' => 'ok');
                } else {
                    parent::operating(request()->path(), 1, '删除失败：' . $this->getError());
                    return array('s' => $this->getError());
                }
            }
        } else {
            parent::operating(request()->path(), 1, '非法请求');
            $this->error('非法请求');
        }
    }
}