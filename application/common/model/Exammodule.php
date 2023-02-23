<?php
namespace app\common\model;
class Exammodule extends \think\Model {
    protected $name = "exam_module";
    //添加当前时间
    protected function Dtime() {
        return date('Y-m-d H:i:s');
    }
    //管理员ID
    protected function uid() {
        return session('ThinkUser.ID');
    }
    public $module_array = array(
        'member' => array(64),//志愿者
        'service' => array(65),//服务队
        'item' => array(68),//点单管理
        'system' => array(1,2,3),//系统配置
        'department' => array(62),//部门管理
        'data' => array(75),//数据统计
        'screen' => array(80),//可视化
        'store' => array(88),//积分兑换管理
        'exam' => array(110),//活动管理
    );
    public function getModuleCompetence($module){
        return true;
        $admin = new \app\admin\controller\Admin;
        if($module == "member"){
            $flag = $admin->userauth3(132);
        }else if($module == "item"){
            $flag = $admin->userauth3(143) || $admin->userauth3(148)
                ||$admin->userauth3(151) ||$admin->userauth3(154) ||$admin->userauth3(156)
                ||$admin->userauth3(161) ||$admin->userauth3(164) ||$admin->userauth3(166)
                ||$admin->userauth3(168) ;
        }else if($module == "activity"){
            $flag = $admin->userauth3(202)| $admin->userauth3(207)
                ||$admin->userauth3(151) ||$admin->userauth3(212) ||$admin->userauth3(215)
                ||$admin->userauth3(217) ||$admin->userauth3(219) ||$admin->userauth3(221)
                ||$admin->userauth3(223) ||$admin->userauth3(225) ||$admin->userauth3(227);
        }else if($module == "service"){
            $flag = $admin->userauth3(127);
        }else if($module == "department"){
            $flag = $admin->userauth3(138);
        }else if($module == "data"){
            $flag = $admin->userauth3(172)||$admin->userauth3(174)||$admin->userauth3(176);
        }else if($module == "store"){
            $flag = $admin->userauth3(182)||$admin->userauth3(187)||$admin->userauth3(191)
                ||$admin->userauth3(194)||$admin->userauth3(196)||$admin->userauth3(199);
        }else if($module == "system"){
            $flag = $admin->userauth3(2)||$admin->userauth3(7)||$admin->userauth3(12)
                ||$admin->userauth3(180);
        }else if($module == "article"){
            $flag = $admin->userauth3(170);
        }else{
            $flag = true;
        }
        return $flag;
    }
    public function getModuleList(){
        return array(
            array('mod'=>'exam','icon'=>'exam.png','url'=>true),
            array('mod'=>'data','icon'=>'index_12.png','url'=>true),
        );
    }
    public function getModuleListByCompetence(){
        $data = $this->getModuleList();
        foreach ($data as $k=>$v){
            if(!$this->getModuleCompetence($v['mod'])){
                unset($data[$k]);
            }
        }
        return array_values($data);
    }
}
?>