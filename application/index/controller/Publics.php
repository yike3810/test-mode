<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;

class Publics extends Controller
{
    public function errors()
    {
        return $this->fetch('public/error');
    }

}
