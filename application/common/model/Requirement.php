<?php


namespace app\common\model;
class Requirement extends \think\Model {
    public $requirement_status = array(
        0=>'草稿',
        8=>'已撤回',
        9=>'已删除',
        10=>'已发布',
    );

}

