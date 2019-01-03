<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;

Class Article extends Controller
{
	public function index()
	{
		// 读取文章表，遍历显示
		$data = Db::name('article')->select();
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
				// $this->redirect('admin/index/index');           // 或者这样
				$this->success('成功 ');
			}else{
				// $this->success('失败',url(''));
				$this->success('失败');

			}
		}
	}

	public function delete()
	{
		echo '<pre>';
		// 获取到链接里的id
		$data = $_GET; 
		$id = $data['delete_id'];  
		// $url = $_SERVER['HTTP_REFERER'];  // 这个不带后面的参数 http://www.ceshi.com/article
		// 进行删除操作
		$result = DB::name('article')->where('id',$id)->delete();
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
		$data2 = DB::name('article')->where('id',$id)->find();
		$this->assign('id',$id);
		$this->assign('data',$data2);
		return $this->fetch('edit');
	}

	public function edit()
	{
		echo '<pre>';
		$data = $_POST;
		$id = $data['art_id'];
		// update(['a'=>$a,'b'=>$b]);
		if(empty($data['art_title']) || empty($data['art_content']))
		{
			$this->success('标题或内容不能为空');
		}else{
			$datb['art_title'] = $data['art_title'];
			$datb['art_content'] = $data['art_content'];
			$datb['art_author'] = $data['art_author'];
			$datb['art_chao'] = $data['art_chao'];

			$result = DB::name('article')->where('id',$id)->update($datb);
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
		$result = DB::name('article')->where('article_title','like','%'.$data['search'].'%')->select();
		$this->assign('data',$result);
		// 怎么让显示那个页面
		// return 返回的话，css,js都不能用了，不 return 的话，就是空白页
		return $this->aa();
		//return $this->fetch('searchzhanshi');
	}



















}

?>