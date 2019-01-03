<?php
namespace app\admin\controller;

use think\Controller;

// use app\home\model\Aaguanlian;
// use app\home\model\Cate as Category;
use think\Session;
use think\Db;
use think\Request;

class Biao extends controller
{

	public function index()
	{
		// $data = DB::name('biaoqian')->select();
		$data = Db::name('biaoqian')->order('id asc')->paginate(9);

		// var_dump($data);
		// die;
		$this->assign('biaoqian',$data);
		return $this->fetch('');
	}

	public function edit_page()
	{
		$url = $_GET;
		$id = $url['edit_id'];
		$data = DB::name('biaoqian')->where('id',$id)->find();
		$this->assign('data',$data);
		return $this->fetch('edit');
	}

	public function edit()
	{
		echo '<pre>';
		$data = $_POST;
		$bb['biaoqian_name'] = $data['biaoqian_name'];
		$result = DB::name('biaoqian')->where('id',$data['id'])->update($bb);
		if($result)
		{
			$this->success('修改成功',url('admin/Biao/index'));		// 返回到了修改页面,就是跳不到首页,因为我没加 url
		}else{
			$this->success('修改成功',url('admin/Biao/index'));
		}
	
	}

	// 无刷新删除
	public function delete()
	{
		$id = $_POST['id'];
		// var_dump($data);
		// die;
		$data2 = DB::name('biaoqian')->where('id',$id)->delete();

		if($data2)
		{
			$returnData = ['code'=>3, 'info'=>'删除成功'];
		}else{
			$returnData = ['code'=>4, 'info'=>'删除失败'];
		}
		
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}
	

	public function add_page()
	{
		return $this->fetch('add');
	}

	//添加 标签
 	function add()
 	{
 		// echo '<pre>';
 		$data = $_POST;
		// 如果已经存在则不能再进行添加
		if($name = DB::name('biaoqian')->where('biaoqian_name',$data['biaoqian_name'])->find() )
		{
		 	$this->success('此标签已存在');
		}else{
			$bb['biaoqian_name'] = $data['biaoqian_name'];
			$result = DB::name('biaoqian')->insert($bb);
			if($result)
			{
			 	$this->success('添加分类成功',url('admin/Biao/index'));
			}else{
			 	$this->success('添加分类失败',url('admin/Biao/index'));
			}
		}	
    }

	
    // 搜索
    public function search()
    {
    	$data = $_POST;
    	$search = $data['name'];
	
		// 现在的 查询好像不是模糊查询 ，应该是 精确的名字
    	// $where['biaoqian_name'] = array('like',$search);
    	// $result = DB::name('biaoqian')->where('biaoqian_name',$search)->select();
    	// $result = DB::name('biaoqian')->where($where)->select();
    	$result = DB::name('biaoqian')->where('biaoqian_name','like',"%".$search."%")->select();  // 这方法可以

    	// var_dump($result);
    	// die;
    	if($result)
		{
			$returnData = ['code'=>3, 'shuju'=>$result,'info'=>'成功'];
		}else{
			$returnData = ['code'=>4, 'shuju'=>$result,'info'=>'失败'];
		}
		
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
		echo $returnData;
		die;
    	
    } 

}
?>