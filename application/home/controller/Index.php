<?php
namespace app\home\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;

class Index extends controller
{
	public function index()
	{
		return $this->fetch();
	}
}


?>