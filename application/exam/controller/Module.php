<?php
namespace app\exam\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Session;
use think\Db;
class Module extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
	public function getTpl() {
       getTpl();
	}
}
