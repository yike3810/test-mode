<?php
namespace app\common\model;
class Label extends \think\Model {
     protected $name = 'label';
    public $labellist = array(
        0=>'未启用',
        1=>'启用',
    );
}
