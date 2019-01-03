<?php
namespace app\home\controller;

use think\Controller;
use app\index\controller\Commen;  // 继承index/commen类，来遍历显示侧边栏
use app\home\model\Article as Articleaa;
use app\home\model\Aaguanlian;
use think\Session;
use think\Db;
use think\Request;

class Article extends Commen
{

	

	public function index()
	{
		return $this->fetch();
	}

	public function guanyu()
	{
		return $this->fetch();
	}

	public function createpage()
	{
		// 只显示风格标签
		// $list=DB::name('cate')->field("id,name,pid,path,concat(path,'-',id) as bpath")->order('bpath')->select();
		$list=DB::name('cate')->where('pid',53)->select();
		 // 从数据库遍历显示到前台
		// foreach($list as $key=>$value){
		//  	$list[$key]['count']=count(explode('-',$value['bpath']));
		// }
		// echo '<pre>';
		// var_dump($list);
		// die;
		$this->assign('alist',$list);
		// return $this->fetch('');
		return $this->fetch();
	}


	// 用来遍历展示各个分类标签
	public function chaodai()
	{
		return $this->fetch('chaodai');
	}

	// public function comment()
	// {
	// 	echo '<pre>';
	// 	$data = $_POST;
	// 	$uname1 = session::get('USER_INFO');
	// 	$uname = $uname1['uname'];
	// 	$uid = $uname['uid'];
	// 	// var_dump($data);
	// 	// die;
	// 	// 插入数据库
	// 	if(!$aa = DB::name('comment')->where('art_id',$data['article_id'])->find())
	// 	{
	// 		$datb['content'] = $data['content'];
	// 		$datb['uid']	 = $uid;						// uid
	// 		$datb['art_id']  = $data['article_id'];
	// 		$datb['pid']     = 0;
	// 		$result = DB::name('comment')->insert($datb);
	// 	}else{
	// 		$datb['content'] = $data['content'];
	// 		$datb['uid']	 = $uid;								// uid
	// 		$datb['art_id']  = $data['article_id'];
	// 		$datb['pid']     = $aa['id'];
	// 		$result = DB::name('comment')->insert($datb);
	// 	}
	// 	// 从数据库查询显示
	// 	$bb = DB::name('comment')->where('art_id',$data['article_id'])->select();
	// 	foreach($bb as $k=>$v)
	// 	{
	// 		if ($v['pid'] == 0) 
	//     	{
	// 	        foreach ($bb as $b) 
	// 	        {
	// 	            if ($b['pid'] == $v['id']) 
	// 	            {
	// 	                foreach ($List as $c) 
	// 	                {
	// 	                    if ($c['pid'] == $b['id']) 
	// 	                    {
	// 	                        $b['children'][] = $c;
	// 	                    }
	// 	                }
	// 	                $v['children'][] = $b;
	// 	            }
	//         	}
 //        		$sub[] = $v;
 //    		}
	// 	}
	// 	// $this->pagedata['commentList'] = $sub;
	// 	return $sub;
	// }

	// 截取过来的评论代码
	 public function comment222()
	 { 
	 	 // echo '<pre>';
	 	 $num = DB::name('comment')->count(); 
	 	 //获取评论总数
	 	 $this->assign('num',$num); 
	 	 $data=array(); 
	 	 $data=$this->getCommlist();
	 	 //获取评论列表 
	 	 $this->assign("commlist",$data); 
	 	 return $this->fetch('xiangqing'); 
	} 


	/**
    *添加评论
    */ 
   public function addComment()
   { 
    	$data=array(); 
   	if((isset($_POST["comment"]))&&(!empty($_POST["comment"])))
   	{ 
   		$cm = json_decode($_POST["comment"],true);
   		//通过第二个参数true，将json字符串转化为键值对数组
   		 $cm['create_time']=date('Y-m-d H:i:s',time());
   		 $newcm = M('comment'); 
   		 $id = $newcm->add($cm); 
   		 $cm["id"] = $id; 
   		 $data = $cm; 
   		 $num = M('comment')->count();
   		 //统计评论总数 
   		 $data['num']= $num; 
   	}else{ 
   		$data["error"] = "0"; 
   	} echo json_encode($data); } 



    public function display_only()	// 现在评论列表显示的都一样
	{
		$uname = session::get('USER_INFO');
		$data = $_GET;
		$id = $data['id'];					// article表的id,查关联表的type_id
		$article = new Articleaa();
		$where = 'id ='.$id;
		$data2 = $article->find($where);

		// 展示相关文章，同一个朝代的
		$art = DB::name('article')->where('id',$id)->find();
		// 查询 art_id
		$art_id = $art['id'];									
		$aa = DB::name('aaguanlian')->where('id',$art_id)->find();
		// 查询 type_id
		$type_id = $aa['type_id'];
		// 查询guanlilan表中，所有type_id的id
		$bb = DB::name('aaguanlian')->where('type_id',$type_id)->limit(4)->select();
		$ids = '';
		foreach($bb as $v)
		{
			$ids .= $v['id'].',';
		}
		$ids = rtrim($ids,',');
		$where = " id in (".$ids.")";
		$cc = DB::name('article')->where($where)->select();


		//获取评论总数
		$num = DB::name('comment')->where('art_id',$id)->count(); 
	 	$this->assign('num',$num); 
	 	// 显示评论列表,应该是pid是0的，id是传过来的id的遍历   $id 就是文章表中的id
	 	$data4=array(); 
	 	$hh = DB::name('comment')->where('art_id',$id)->order('id asc')->find(); // 取id最小的一条，取出来
	 	// $parent_id = $hh['id'];  // id
	 	$parent_id = $hh['parent_id'];
	 	$data4=$this->getCommlist($parent_id );

	 	 //获取评论列表 
	 	$this->assign("commlist",$data4);      // 在这显示的评论列表


		$this->assign('data2',$cc);
		$this->assign('data',$data2);
		return $this->fetch('xiangqing');		// view下article下的  名.html
	}

	 /**
    *递归获取评论列表
    */ 
    // protected function getCommlist($parent_id = 0,&$result = array())
   protected function getCommlist($parent_id,&$result = array())
   { 
   		$arr = DB::name('comment')->where('parent_id',$parent_id)->order("create_time desc")->select();
   		// var_dump($parent_id);

   		if(empty($arr))
   		{ 
   			return array(); 
   		} 
   		foreach ($arr as $cm) 
   		{ 
   			$thisArr=&$result[]; 
   			$cm["children"] = $this->getCommlist($cm["id"],$thisArr);
   			$thisArr = $cm;
   		 } 
   			
   		return $result;
    }


    // 添加子评论
    public function add_comment()
	{
		// echo '<pre>';
		$data = $_POST;
		$uname1 = session::get('USER_INFO');
		$uname = $uname1['uname'];
		$uid = $uname1['uid'];

		$datb['content'] = $data['content'];
		$datb['uid']	 = $uid;								
		$datb['parent_id']  = $data['comment_id'];
		$datb['art_id']     = $data['art_id'];           // 文章id
		$datb['nickname']     = $uname;
		$datb['create_time'] = date('Y-m-d H:i:s');

		$result = DB::name('comment')->insert($datb);
		if($result)
		{
			$returnData = ['code'=>3, 'info'=>'评论成功'];
		}else{
			$returnData = ['code'=>4, 'info'=>'评论失败'];
		}
		// $returnData = json_encode($shanchu, JSON_UNESCAPED_UNICODE); 
		// var_dump(json_encode($returnData));
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}

	// 添加pid=0的评论
	public function add_nullcomment() 
	{
		echo '<pre>';
		$data = $_POST;

		// var_dump($data);
		// die;
		$uname1 = session::get('USER_INFO');
		$uname = $uname1['uname'];
		$uid = $uname1['uid'];

		$datb['content'] = $data['message'];
		$datb['uid']	 = $uid;								
		$datb['parent_id']  = 0;
		$datb['art_id']     = $data['art_id'];           // 文章表的id,现在插入的是文章表的art_id
		$datb['nickname']     = $uname;
		$datb['create_time'] = date('Y-m-d H:i:s');

		$result = DB::name('comment')->insert($datb);
		if($result)
		{
			$this->success('发布成功');
		}else{
			$this->success('发布失败');
		}

		
	}


		

    // 我是应该添加文章那把分类的那个拿过去，还是单独做标签
	public function insert()
	{
		// echo '<pre>';
		$data = $_POST;	// 获取的三级联动的值自己就传过来了,如果不选择分类，pid这个字段就不会传过来
		$session = session::get('USER_INFO');
		$uid = $session['uid'];
		$article      = new Articleaa();		
		$aaguanlian   = new Aaguanlian();
		// 插入 guanlian 表数据
		$cc['uid'] 		= $uid;
		if(isset($data['pid']))
		{
			$cc['type_id'] 	= $data['pid'];
		}else{
			$cc['type_id'] 	= null;
		}
		$aa = $aaguanlian->insert($cc);	
		// 获取插入这个表的id
		$art_id = DB::name('aaguanlian')->getLastInsID();

		$datb['art_title'] 		= $data['title'];
		$datb['art_content'] 	= $data['content'];
		$datb['create_time']	= date('y-m-d h:i:s',time());;	// 发布时间
		$datb['art_author']		= $data['author'];
		$datb['art_id']			= $art_id;
		// 插入 article 表
		$res = $article->insert($datb);
		if($res)
		{
			$this->success('发布成功');	//	不写跳转地址，跳回原页面
		}	
		


	}



	
	



	// 展示文章列表
	public function display_all()	
	{
		// echo '<pre>';		// 有这个，html的样式就全乱了不知道为啥
		$article = new Articleaa();
		$data = $article->findall();
		$this->assign('data',$data);
		return $this->fetch('article_list');	// view下article下的  名.html
		
	}

	// 展示各个朝代表
	public function zhanshi()
	{
		$article = new Articleaa();
		// where 条件拼接传过来的id或者name
		$data = $_GET;
		$type_id = $data['type_id'];
		// 从关联表查出type_id是那个的所有id,再查询文章表，或者插入文章表type_id
		$datc = DB::name('aaguanlian')->where('type_id',$type_id)->select();
		$ids = '';
		foreach($datc as $v)
		{
			$ids .= $v['id'].',';
		}
		$ids = rtrim($ids,',');
		$where = " id in (".$ids.")";
		$a = DB::name('article')->where($where)->select();
		$this->assign('data',$a);
		return $this->fetch('article_list');
	}





}


?>