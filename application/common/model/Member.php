<?php
namespace app\common\model;
use think\Db;
use think\Model;

class Member extends \think\Model {
    public $member_sex_array = array(
        73=>"男",
        74=>"女",
    );

    //    参数自动完成

    /**
     * 存储数据时自动完成加密 - 手机号
     * @param $value
     * @return false|string
     */
    protected function setPhoneAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 手机号
     * @param $value
     * @return false|string
     */
    protected function getPhoneAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }

    /**
     * 存储数据时自动完成加密 - 真实姓名
     * @param $value
     * @return false|string
     */
    protected function setRealNameAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 真实姓名
     * @param $value
     * @return false|string
     */
    protected function getRealNameAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }

    /**
     * 存储数据时自动完成加密 - 真实姓名
     * @param $value
     * @return false|string
     */
    protected function setIdNumberAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_encrypt($value);
    }

    /**
     * 查询数据时自动完成解密 - 真实姓名
     * @param $value
     * @return false|string
     */
    protected function getIdNumberAttr($value)
    {
        $baseModel = new BaseModel();
        return $baseModel->sm4_decrypt($value);
    }


    /*
     * 获取会员列表
     *@param integer  		department_id 单位id
     *@param integer  		parent_id 上级单位id
     *@param integer  		department_type 部门类型
     *@param string         keywords 关键词查询（模糊匹配标题）
     * @param integer       list_rows 每页条数
     * @return array        格式：array("total"=>xx,"data"=>$hot_list)
     * 调用方式举例:$member = Member();$member->getMemberList($param)
     * */
    public function getMemberList($param =array()){
        $where = " WHERE 1 ";
        $page_size = 1;
        if($param['keywords'] !=""){
            $where.=" AND (m.phone LIKE '{$param['keywords']}%' OR m.member_name LIKE '{$param['keywords']}%'
			OR m.email LIKE '{$param['keywords']}%'  
			OR m.real_name LIKE '{$param['keywords']}%'  
			OR m.id_number LIKE '{$param['keywords']}%')";
        }
        if($param['department_type'] !=""){
            $where.=" AND d.department_type ='{$param['department_type']}'";
        }
        if($param['parent_id'] !=""){
            $where.=" AND d.parent_id ='{$param['parent_id']}'";
        }
        if($param['department_id'] !=""){
            $where.=" AND m.department_id ='{$param['department_id']}'";
        }
        if($param['real_name'] !=""){
            $where.=" AND m.real_name LIKE '{$param['real_name']}%'";
        }
        $page_config = config('paginate');
        $database_prefix = config('database.prefix');
        $page = input('param.page');
        if($param['list_rows']!=""){
            $page_config['list_rows'] = $param['list_rows'];
        }
        if($page>=2){
            $start_page = ($page-1)*$page_config['list_rows'];
        }else{
            $start_page = 0;
        }
        if($order_by_str==""){
            $order_by_str = " m.member_id DESC ";
        }
        $sql = "SELECT count(*) AS t FROM ( SELECT 1 FROM {$database_prefix}member AS m
	    LEFT JOIN {$database_prefix}department AS d	ON m.department_id = d.department_id {$where}  GROUP BY m.member_id ) AS temp ";
        $total = Db::query($sql);
        $sql = "SELECT m.*,d.department_name,d.parent_name,dm.MenuName AS department_type_name FROM {$database_prefix}member AS m
        LEFT JOIN {$database_prefix}department AS d	ON m.department_id = d.department_id
        LEFT JOIN {$database_prefix}dmenu AS dm	ON d.department_type = dm.ID
        {$where}  GROUP BY m.member_id ORDER BY {$order_by_str} ";
        $sql .= " LIMIT $start_page,{$page_config['list_rows']}";
        $member_list = Db::query($sql);
        return array("total"=>$total[0]['t'],"data"=>$member_list);
    }
}
