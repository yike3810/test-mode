<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;

class Transfer extends Controller
{
    public function index()
    {

        $order = new \app\common\model\Order;
        $order -> getexchangepointsresult();
        return $this->fetch();
    }

}
