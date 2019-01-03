<?php
namespace app\home\controller;

use think\Controller;

use app\index\controller\Commen;  
use app\home\model\Cate as Category;
use think\Session;
use think\Db;
use think\Request;

class Cate extends Commen
{


	function index(){

		$list=DB::name('cate')->field("id,name,pid,path,concat(path,'-',id) as bpath")->order('bpath')->select();
		 // 从数据库遍历显示到前台
		foreach($list as $key=>$value){
		 	$list[$key]['count']=count(explode('-',$value['bpath']));
		}
		$this->assign('alist',$list);
		return $this->fetch('');
 	}


 	//添加栏目
 	function add()
 	{
 		// echo '<pre>';
 		$data = $_POST;
		$pid=isset($_POST['pid'])?(int)$_POST['pid']:0;
		if($pid==0){
			$path =0;
		}else{
		 	$list=DB::name('cate')->where("id=$pid")->find();
			$path =$list['path'].'-'.$list['id'];//子类的path为父类的path加上父类的id
		}
		 // 数据库有字段 id,name,pid,path
		 $datb = [
		 	'name' => $data['name'],
		 	'pid'  => $data['pid'],
		 	'path' => $path

		 ];
		 $result = DB::name('cate')->insert($datb);
		 if($result)
		 {
		 	$this->success('添加分类成功');
		 }else{
		 	$this->success('添加分类失败');
		 }
    }





}
?>