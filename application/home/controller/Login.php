<?php
namespace app\home\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;

class login extends controller
{
	public function index()
	{
		// echo  '111';
		// die;
		return $this->fetch();
	}

	public function regpage()
	{
		return $this->fetch('reg');
	}


	public function login()
	{
		echo '<pre>';
		$data = $_POST;
		$result = DB::name('user')->where('username',$data['username'])->find();
		// var_dump($result);
		// die;
		if(!$result['username'])
		{
			$this->success('用户未注册');   					// 应该是ajax提示吧
		}else if($data['password'] !== $result['userpass'])
		{
			$this->success('密码不正确');
		}else{
			//把用户信息存储到session中
			$datb = [
						'uname' => $data['username'],		// 应该用  =>  而不是  =
						'uid'	=> $result['id']
					];
			session::set('USER_INFO',$datb);
			// var_dump(session::get());
			// die;
			$this->success('登录成功','home/index/index');
		}
	}

	public function loginout()
	{
		// 清除session中user
		session('uname',null);
		dump(session);
		die;
	}

	public function reg()
	{
		echo '<pre>';
		$data = $_POST;
		$data2 = [
					'username' =>$data['username'],
					'userpass' =>md5($data['password']),
					'email'    =>$data['email']
				];
		if($a = DB::name('user')->where('username',$data['username'])->find())		// 判断用户名是否已注册
		{
			$this->success('用户名已被注册');   					// 应该是ajax提示吧
		}else if(!$data['password'])
		{
			$this->success('密码不能为空');
		}else if($data['password'] !== $data['password2'])
		{
			$this->success('2次密码不一致');
		}else{
			$result = DB::name('user')->insert($data2);
			$this->success('注册成功','home/login/index');		// 注册成功，跳到登录页
		}	
	}










}


?>