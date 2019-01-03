<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Request;


Class Index extends Controller
{
	//后台主页
	public function index()
	{
		return $this->fetch();// 默认fetch返回index控制器的index页面
	}

}

?>