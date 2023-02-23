<?php
namespace app\common\model;
class Subject extends \think\Model {
    protected $name = 'subject';
    public $subjectlist = array(
        0=>'未启用',
        1=>'启用',
    );
}
