<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;

Class Login extends Controller
{
	public function index()
	{
		// dump('post.');  获取不到
		return $this->fetch();
	}


	// 后台登录，应该是验证数据库是否存有admin,pass,验证码校验那没做
	// 把用户信息存入session
	public function login()
	{
		echo '<pre>';
		$data = $_POST; 
		// 查询 user表中是否有这用户
		$result = DB::name('admin')->where('username',$data['username'])->find();
		if(!$result)
		{
			$this->success('该用户不存在');
		}else if($data['userpass'] !== $result['userpass']){   
			$this->success('密码不正确');
		}else{
			// $uname = $data['username'];
			// Session::set('uname',$uname); 
			$datb = [
						'uname' => $data['username'],		// 应该用  =>  而不是  =
						'uid'	=> $result['id']
					];
			session::set('USER_INFO',$datb);
			//$this->redirect('admin/index/index'); 	
			$this->success('登录成功','admin/index/index');

			
		}
	}

	public function loginout()
	{
		// 清除session中user
		session('uname',null);		// 重新赋值
		if(!session::get('uname'))
		{
			$this->success('退出成功 ','admin/login/index');
		}
	
	}



















}

?>