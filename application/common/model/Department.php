<?php
namespace app\common\model;
class Department extends \think\Model {
	//自动验证
	//110县直属部门
	//111乡镇村
    /*
     * 获取市级id值
     * 获取方法 $department_mode =  new \app\common\model\Department;不需要手动初始化
     * $department_mode->city_level_region_id;//这一行就可以
     * */
    public $city_level_region_id = array(3879,3880,3881,3882,3883,3884,3885,3886,3887,3888,3889,3890,3891,3892);//市级id集合
    public $city_level_region_id_str = "3879,3880,3881,3882,3883,3884,3885,3886,3887,3888,3889,3890,3891,3892";//市级id集合
    public $province_city_level_region_id = array(0,3879,3880,3881,3882,3883,3884,3885,3886,3887,3888,3889,3890,3891,3892);//省级+市级id集合
	public function getTownshipTreeList($department_type){
		$where =  $data = array();
		$where['department_type'] = $department_type;
		$where['parent_id'] = 0;
		$department_list = $this->where($where)->column('');
		$str = "";
		foreach ($department_list as $k=>$v){
			$result = array();
			$result['text'] = $v['department_name'];
			$array = $this->where("parent_id='{$v['department_id']}'")->column('');
			$result['children'][] = array('text'=>'请选择');
			foreach ($array as $key=>$value){
				$new_array = array();
				$new_array['text'] = $value['department_name'];
				$result['children'][] = $new_array;
			}
			$data[] = $result;
		}
		return $data;
	}
    public $is_admin_array = array(
        0 =>"否",
        1 =>"是",
    );
	/*
	 * 部门角色id----department_role_id
	 * PC端获取方式 session('Department.department_role_id');
	 * wap端获取方式 $this->member_info['department_role_id'];
	 *  1.省级管理员
        2.省级媒体
        3.省级其他单位
        4.省级媒体子账号
        5.市级管理员
        6.市级媒体
        7.市级其他单位
        8.市级媒体子账号
        9.县级管理员
        10.县级媒体
        11.县级其他单位
        12.县级媒体子账号

	    13.兰州新区管理员
        14.兰州新区媒体
        15.兰州新区其他单位
        16.兰州新区媒体子账号

        20.央媒
        21.央媒子账号
	 * */
	/*
	 * 返回角色id
	 *@param integer       $member_id 账号id
     * @return integer     部门角色id----department_role_id
	 * */
    public function getDepartmentRoleId($member_id){
        $member = new Member();
        $department = new Department();
        $region = new Region();
        $member_info = $member->where("member_id='{$member_id}'")->find();
        $department_info  = $department->where("department_id='{$member_info['department_id']}'")->find();
        if($department_info['district']!=0){
            $region_id = $department_info['district'];
        }elseif($department_info['city']!=0){
            $region_id = $department_info['city'];
        }elseif($department_info['province']!=0){
            $region_id = $department_info['province'];
        }
        $region_info = $region->where("region_id='{$region_id}'")->find();
        if($region_info['region_type'] == 2 && $department_info['is_admin'] == 1 && $department_info['city'] == 3878){
            //省级管理员
            return 1;
        }elseif($region_info['region_type'] == 2 && $department_info['industry_id'] == 1 && $member_info['parent_id'] == 0 && $department_info['city'] == 3878){
            //省级媒体
            return 2;
        }elseif($region_info['region_type'] == 2 && $department_info['industry_id'] != 1 && $department_info['is_admin'] != 1 && $department_info['city'] == 3878){
            //省级其他单位
            return 3;
        }elseif($region_info['region_type'] == 2 && $department_info['industry_id'] == 1 && $member_info['parent_id'] != 0 && $department_info['city'] == 3878){
            //省级媒体子账号
            return 4;
        }elseif($region_info['region_type'] == 3 && $department_info['is_admin'] == 1 && in_array($region_info['region_id'],$this->city_level_region_id)){
            //市级管理员
            return 5;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] == 1 && $member_info['parent_id'] == 0 && in_array($region_info['region_id'],$this->city_level_region_id)){
            //市级媒体
            return 6;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] != 1 && $department_info['is_admin'] != 1 && in_array($region_info['region_id'],$this->city_level_region_id)){
            //市级其他单位
            return 7;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] == 1 && $member_info['parent_id'] != 0 && in_array($region_info['region_id'],$this->city_level_region_id)){
            //市级媒体子账号
            return 8;
        }elseif($region_info['region_type'] == 3 && $department_info['is_admin'] == 1){
            //县级管理员
            return 9;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] == 1 && $member_info['parent_id'] == 0){
            //县级媒体
            return 10;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] != 1 && $department_info['is_admin'] != 1){
            //县级其他单位
            return 11;
        }elseif($region_info['region_type'] == 3 && $department_info['industry_id'] == 1 && $member_info['parent_id'] != 0){
            //县级媒体子账号
            return 12;
        }elseif($department_info['city'] == 3893 && $department_info['is_admin'] == 1){
            //兰州新区管理员
            return 13;
        }elseif($department_info['city'] == 3893 && $department_info['industry_id'] == 1  && $member_info['parent_id'] == 0){
            //兰州新区媒体
            return 14;
        }elseif($department_info['city'] == 3893 && $department_info['industry_id'] != 1 && $department_info['is_admin'] != 1){
            //兰州新区其他单位
            return 15;
        }elseif($department_info['city'] == 3893 && $department_info['industry_id'] == 1 && $member_info['parent_id'] != 0){
            //兰州新区媒体子账号
            return 16;
        }elseif($department_info['province'] == 3876 && $member_info['parent_id'] == 0){
            //央媒
            return 20;
        }elseif($department_info['province'] == 3876 && $member_info['parent_id'] != 0){
            //央媒子账号
            return 21;
        }
    }
}
