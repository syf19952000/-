<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Request;
class Commen extends \think\Controller
{
	// 构造函数
	// 让有侧边栏的别的类继承这个类
    public function __construct()
    {
    	parent::__construct();
    	// 取出所有
        // $list=DB::name('cate')->field("id,name,pid,path,concat(path,'-',id) as bpath")->order('bpath')->select();
        $nei_list=DB::name('cate')->where('pid',53)->select();  // 内容分类
		$zuo_list=DB::name('cate')->where('pid',23)->select();	// 作者分类
		// 分配到 aside_my 中，展示个人中心
		// echo '<pre>';
		$user = session::get('USER_INFO');
		$uid = $user['uid'];
		$my = DB::name('user')->where('id',$uid)->find();
		
		// var_dump($my);
		// die;
		
		// $where['pid'] = array('between','1,22');
		
		// $where = " pid = array(' between ','2,22')";
		// $chao_list=DB::name('cate')->field("id,name,pid,path,concat(path,'-',id) as bpath")->where($where)->order('bpath')->select();  // 朝代分类
		 
		 // 从数据库遍历显示到前台
		// foreach($chao_list as $key=>$value){
		//  	$chao_list[$key]['count']=count(explode('-',$value['bpath']));
		// }
		// echo '<pre>';
		// var_dump($nei_list);
		// die;
		// $this->assign('list',$nei_list);
		$this->assign(array(
			'nei_list'=>$nei_list,
			'zuo_list'=>$zuo_list,
			'my_list'=>$my,
			// 'chao_list'=>$chao_list,
			));
		// 这样写的话，前台home_cate继承了commen类，页面地址就变成了 
		// D:\phpstudy\WWW\thinkphp51130\public/../application/home\view\home\else3\html\aside222.html
		// return $this->fetch('/home/else3/html/aside222');
    }






}
