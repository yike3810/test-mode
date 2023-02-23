<?php


namespace app\common\model;
class Videocategory extends \think\Model
{
    protected $name = 'video_category';
    public $enable_array =	array(
        0 =>"关闭",
        1 =>"开启",
    );
}