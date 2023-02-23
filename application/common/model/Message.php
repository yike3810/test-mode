<?php


namespace app\common\model;


class Message extends \think\Model
{
    protected $name = 'message';
    public $Message_list=array(
        0=>"未读",
        1=>"已读",

    );
}