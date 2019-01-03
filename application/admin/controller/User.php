<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;

Class User extends Controller
{
	public function index()
	{
		// 读取文章表，遍历显示
		$data = Db::name('user')->order('id asc')->paginate(9);
		$this->assign('data',$data);
		return $this->fetch();
	}

	public function create()
	{
		echo '<pre>';
		if(request()->isPost()){ // 把表单改成post方式提交的
			$data = $_POST;   // 这样可以获取到
			// 接收到之后插入数据库
			$res = Db::name('user')->insert($data);  
			// 插入数据库之后跳转页面
			if($res)
			{
				// $this->success('登录成功',url('adminpage'));  // 这样写的话，它是找login.php中的adminpage方法 
				
				// $this->success('登录成功',url('admin/index/index')); // 会跳转，但是登录成功的提示是一个单独的页面
				$this->redirect('admin/index/index');           // 或者这样
			}else{
				$this->success('登录失败',url(''));
			}
		}
	}

	public function delete()
	{
		echo '<pre>';
		$data = $_GET; 
		$id = $data['delete_id'];  
		// $url = $_SERVER['HTTP_REFERER'];  // 这个不带后面的参数 http://www.ceshi.com/article
		// 进行删除操作
		$result = DB::name('user')->where('id',$id)->delete();
		if($result)
		{
			$this->success('删除成功');  // 不写跳转链接的话，自动跳回上一个页面
		}else{
			$this->success('删除失败');
		}
	}

	public function editpage()
	{
		$data = $_GET;
		$id = $data['edit_id'];
		$data2 = DB::name('user')->where('id',$id)->find();
		$this->assign('id',$id);
		$this->assign('data',$data2);
		return $this->fetch('user_edit');
	}

	public function edit()
	{
		echo '<pre>';
		$data = $_POST;
		$id = $data['id'];
		// update(['a'=>$a,'b'=>$b]);
		if(empty($data['name']) || empty($data['pass']))
		{
			$this->success('用户名或密码不能为空');
		}else{
			$result = DB::name('user')->where('id',$id)->update([
				'username'=>$data['name'],
				'userpass'=>$data['pass'],
				'mobile'=>$data['mobile'],
				'email'=>$data['email']
			]);
			if($result)
			{
				$this->success('编辑成功');
			}else{
				$this->success('编辑失败');
			}
		}	
	}

	public function aa()
	{
		return $this->fetch('index');
	}

	public function search()
	{
		echo '<pre>';
		// 搜索文章标题
		$data = $_POST;
		// 进行搜索，模糊查询,ajax查询的话，之后怎么让它显示在页面
		//$result = DB::name('article')->where('article_title','like','%'.$data['a'].'%')->select();
		$result = DB::name('user')->where('article_title','like','%'.$data['search'].'%')->select();
		$this->assign('data',$result);
		// 怎么让显示那个页面
		// return 返回的话，css,js都不能用了，不 return 的话，就是空白页
		return $this->aa();
		//return $this->fetch('searchzhanshi');
	}


	public function usersearch()
	{
		$search = $_POST;
		$search = $search['search'];
		// 模糊查询 用户名，手机号，电子邮箱
		// $where = [
		// 	['username','like','%'.$search.'%'],
		// 	['mobile','like','%'.$search.'%'],
		// ];
		
		$where['username']  = ['like','%'.$search.'%'];
		$where['mobile']	= ['like','%'.$search.'%'];
		$where['email']	= ['like','%'.$search.'%'];

		// $data = DB::name('user')->whereOr($where)->select();
		$data = DB::name('user')->whereOr($where)->order('id asc')->paginate(8);
		$this->assign('data',$data);
		return $this->fetch('index');
	}

















}

?>