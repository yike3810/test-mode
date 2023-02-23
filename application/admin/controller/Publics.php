<?php
namespace app\admin\controller;

use think\Controller;

class Publics extends Controller
{
    public function index(){
        return $this->fetch('public/error1');
    }
}