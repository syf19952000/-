<?php
namespace app\home\controller;

use think\Controller;
use app\index\controller\Commen;  // 继承index/commen类，来遍历显示侧边栏
use app\home\model\Article as Articleaa;
use app\home\model\Aaguanlian;
use think\Session;
use think\Db;
use think\Request;

class Ci extends Commen
{
	public function index()
	{
		return $this->fetch();
	}

	public function guanyu()
	{
		return $this->fetch();
	}

	public function createci()
	{
		// 遍历词牌名
		$where['pid'] = array('in','74,81,85,114,492,559');
		$list = DB::name('cate')->where($where)->field("id,name,pid,path,concat(path,'-',id) as bpath")->order('bpath')->select();
		// 把诗词歌赋填上，还是用户发表文章的时候只选择是不是诗词歌赋
		foreach($list as $key=>$value){
		 	$list[$key]['count']=count(explode('-',$value['bpath']));
		}
		// echo '<pre>';
		// var_dump($list);
		// die;
		$biaoqian = DB::name('biaoqian')->select();
		$this->assign('biaoqian',$biaoqian);
		$this->assign('alist',$list);
		return $this->fetch();
	}

	public function ajaxsearch()
	{
		// 同时搜索标题和内容来展示搜索结果,可以用 合并数组 或者 like查询 中间用 or
		$data = $_POST; 
		$search = $data['name']; 
		
		// 第二种
		$map['art_title|art_content'] = array('like', '%' . $search . '%');
		$result = DB::name('article_ci')->where($map)->select();
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

	// 用来遍历展示各个分类标签
	public function chaodai()
	{
		return $this->fetch('chaodai');
	}

	// 分页
	public function pagedate()
	{	
		$list = Db::name('article')->order('id asc')->paginate(2);
		
		$this->assign('list', $list);

		return$this->fetch();
	}

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
	   	} 
	   		echo json_encode($data); 
	} 

	// 删除评论
	public function delete_comment()
	{
		// echo '<pre>';
		$data = $_POST;
		$id = $data['id'];   // 评论id
		// var_dump($data);
		// die;
		$uname1 = session::get('USER_INFO');
		$uname = $uname1['uname'];
		$uid = $uname1['uid'];
		// 判断当前uid是否是数据库的uid
		$uuuid = DB::name('comment')->where('id',$id)->find();
		$uid2 = $uuuid['uid'];
		if($uid != $uid2)
		{
			
			$returnData = ['code'=>5, 'info'=>'您非评论人，不能进行删除操作'];
		}else{
			$result = DB::name('comment')->delete($id);
			
			if($result)
			{
				$returnData = ['code'=>3, 'info'=>'删除成功'];
			}else{
				$returnData = ['code'=>4, 'info'=>'删除失败'];
			}
		}
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}

    public function display_only()	// 现在评论列表显示的都一样
	{
		$udata = session::get('USER_INFO');
		$data = $_GET;
		$id = $data['id'];					// article表的id,查关联表的type_id
		// var_dump($data);
		// die;
		$data2 = DB::name('article_ci')->where('id',$id)->find();
		$art_id = $data2['art_id'];
		
		//获取评论总数
		$num = DB::name('comment')->where('art_id',$data2['art_id'])->count(); 
	 	$this->assign('num',$num); 
	 	$data4=array(); 
	 	// 这种方法得到评论列表所有的评论
	 	$data4 = $this->getCommlist();
	 	
	 	// 这个方法 把 将某个键值相同的合并到一个数组中，如何取其中 art_id 为 1 的
	 	$result =   array();
		foreach($data4 as $k=>$v){
		    $result[$v['art_id']][]    =   $v;
		} 
		// var_dump($result);
		$arr = array();
		foreach($result as $val)  // 此时的$val就是$result同 art_id的一个大数组，我要的就是这个，怎么判断它的art_id
		{
				foreach($val as $v)
				{
					if($v['art_id'] == $art_id)  
					{
						// echo '<hr>';  
						$arr = $val;
					}
				}
		}
		// 把 $img 的头像 属性，增加到 $arr数组里
	 	$uids = array();
	 	foreach($arr as &$v)
		{
			$img = DB::name('user')->where('id',$v['uid'])->find();  
			$v['touxiang'] = $img['touxiang'];
			foreach($v['children'] as &$n)
			{
				$img2 = DB::name('user')->where('id',$n['uid'])->find();  
				$n['touxiang'] = $img2['touxiang'];
				foreach($n['children'] as &$f)
				{
					$img3 = DB::name('user')->where('id',$f['uid'])->find();  
					$f['touxiang'] = $img3['touxiang'];
				}

			}
		}
	 	//获取评论列表 
	 	$this->assign("commlist",$arr);  

	 	$ff = DB::name('collection')->where('uid',$udata['uid'])->where('art_id',$art_id)->select();		// 应该判断是不是当前用户
	 	// die;
	 	if($ff)
	 	{
	 		$color =5;
	 	}else{
	 		$color =9;
	 	}
	 	$this->assign('color',$color);

	 	// 展示标签列表
	 	$biao_art = DB::name('biaoqian_link')->where('art_id',$art_id)->select();  // 可能有多个标签
	 	foreach($biao_art as &$v)
	 	{
	 		$biao_name = DB::name('biaoqian')->where('id',$v['biaoqian_id'])->find();
	 		$v['biaoqian_name'] = $biao_name['biaoqian_name'];
	 	}
		$this->assign('biaoqian',$biao_art);
		// $this->assign('data2',$cc);
		$this->assign('data',$data2);
		return $this->fetch('xiangqing');		// view下article下的  名.html
	}

	 /**
    *递归获取评论列表
    */ 
    // protected function getCommlist($parent_id = 0,&$result = array())
   protected function getCommlist($parent_id = 0,&$result = array())
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

		if($uid)
		{
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

		}else{
			$returnData = ['code'=>5, 'info'=>'用户未登陆的情况下，不能发表评论'];
		}
		
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}

	// 添加pid=0的评论
	public function add_nullcomment() 
	{
		echo '<pre>';
		$data = $_POST;
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


		
	public function insert()
	{
		// echo '<pre>';
		$data = $_POST;	// 获取的三级联动的值自己就传过来了,如果不选择分类，pid这个字段就不会传过来
		// echo '<pre>';
  // 		var_dump($data);
  // 		die;
		$session = session::get('USER_INFO');
		$uid = $session['uid'];
		$article      = new Articleaa();		
		$aaguanlian   = new Aaguanlian();
		// 插入 guanlian 表数据
		$cc['uid'] 		= $uid;
		if(isset($data['pid']))     // 插入词牌名，type_id
		{
			$cc['type_id'] 	= $data['pid'];
		}else{
			// $cc['type_id'] 	= null;	// 没有选择词牌名时，默认是
			$cc['type_id'] 	= '74';	// 没有选择词牌名时，默认是
			
		}
		$aa = $aaguanlian->insert($cc);	
		// 获取插入这个表的id
		$art_id = DB::name('aaguanlian')->getLastInsID();

		// 如果标签为空，不插入标签表
		if($data['biaoqian_id'])
		{
		 	// 当 $count= 2 时就是只有一个值，$arr 最后一个下标是空的
			$arr = explode(',',$data['biaoqian_id']);
			$count = count($arr);  

			$result = array_pop($arr);  // 这是那个空的下标
			foreach($arr as $v)
			{
				$biaoqian['biaoqian_id'] = $v;
				$biaoqian['art_id'] = $art_id;
				// var_dump($biaoqian);
				// die;
				$biao = DB::name('biaoqian_link')->insert($biaoqian);
			}
		}

		// 替换 content 中的 空格 为 <br>
    	$newline = array("\r\n","\n","\r");
    	$content = str_replace($newline, "<br />",$data['content']); 
		
		$art_translate 	= str_replace($newline, "<br />",$data['art_translate']); //译文
    	$art_zhu 		= str_replace($newline, "<br />",$data['zhushi']); //注释
    	$art_shang 		= str_replace($newline, "<br />",$data['art_shang']); //赏析
    	$art_back 		= str_replace($newline, "<br />",$data['art_back']); //创作背景
		

		$datb['art_title'] 		= $data['title'];
		$datb['art_content'] 	= $content;
		$datb['create_time']	= date('y-m-d h:i:s',time());;	// 发布时间
		$datb['art_author']		= $data['author'];
		$datb['art_id']			= $art_id;

		$datb['art_translate']	= $art_translate;
		$datb['art_zhu']		= $art_zhu;
		$datb['art_shang']		= $art_shang;
		$datb['art_back']		= $art_back;

		// 插入 article 表
		$res = DB::name('article_ci')->insert($datb);
		if($res)
		{
			$this->success('发布成功');	//	不写跳转地址，跳回原页面
		}	
	}


	// 当图标变颜色的时候，点击是取消收藏，当图片没颜色的时候点击是收藏
	// 查询数据库是否存在，如果不存在则加入，存在则删除，前台也变样式
	public function collection()
	{
		$data = $_POST;
		$uname1 = session::get('USER_INFO');
		$uid = $uname1['uid'];
		$result = DB::name('collection')->where('art_id',$data['art_id'])->select();
		if($result)
		{
			$aa = DB::name('collection')->where('art_id',$data['art_id'])->delete();
			$returnData = ['code'=>4, 'info'=>'取消收藏'];
		}else{
			$bb['art_id'] 	= $data['art_id']; 	// 对应文章表的id
			$bb['uid'] 		= $uid ;
			$bb = DB::name('collection')->insert($bb);
			$returnData = ['code'=>3, 'info'=>'收藏成功'];
		}
		// header('Content-Type:application/json; charset=utf-8'); 
		$data3 = json_encode($returnData,JSON_UNESCAPED_UNICODE);    //这样也正确
		return $data3;
		// return json_encode($returnData);	// 这样返回格式正确
	}

	
	



	// 展示文章列表
	public function display_all()	
	{
		$list = Db::name('article_ci')->order('id asc')->paginate(5);		
		$this->assign('data', $list);

		// $this->assign('data',$data);
		return $this->fetch('ci_list');	// view下article下的  名.html	
	}

	// 展示不同 type 的文章
	public function zhanshi()
	{
		$article = new Articleaa();
		// where 条件拼接传过来的id或者name
		$data = $_GET;
		$type_id = $data['type_id'];
		// 从关联表查出type_id是那个的所有id,再查询文章表，或者插入文章表type_id
		$datc = DB::name('aaguanlian')->where('type_id',$type_id)->select();
		if(empty($datc))
		{
			return $this->fetch('articleci_listnull');
		}
		$ids = '';
		foreach($datc as $v)
		{
			$ids .= $v['id'].',';
		}
		$ids = rtrim($ids,',');

		$needle = ",";				//判断是否包含a这个字符
	    $tmparray = explode($needle,$ids);
	    $count = count($tmparray);
	    if($count>1){
	   		$where = " art_id in (".$ids.")";
	    } else{
	    	$where = " art_id = ".$ids;
	    }

		// $a = DB::name('article')->where($where)->select();
		$a = Db::name('article_ci')->where($where)->order('id asc')->paginate(5);  		// 分页展示
		// var_dump($a);
		$this->assign('data',$a);
		return $this->fetch('articleci_list');
	}


	public function editpage()
	{
		// echo '<pre>';
		$data = $_POST;	
		$session = session::get('USER_INFO');
		$uid = $session['uid'];

		$art_id = $data['art_id'];

	
		// 修改词牌名,如果原先没有词牌名呢
		if(isset($data['pid']))
		{
			$cccc['type_id'] = $data['pid'];
			$cipai = DB::name('aaguanlian')->where('id',$art_id)->update($cccc);
			if($cipai)
			{
				$this->success('修改成功');	//	不写跳转地址，跳回原页面
			}	
		}

		// 修改标签库
		if($data['biaoqian_id'])
		{
		 	// 当 $count= 2 时就是只有一个值，$arr 最后一个下标是空的
			$arr = explode(',',$data['biaoqian_id']);
			$count = count($arr);  

			$result = array_pop($arr);  // 这是那个空的下标
			// 先删除 art_id 的，再添加
			foreach($arr as $v)
			{
				if($cunzai = DB::name('biaoqian_link')->where('art_id',$art_id)->find())
				{
					// echo '222';
					if($delete = DB::name('biaoqian_link')->where('art_id',$art_id)->delete())
					{
						// echo'444';
						foreach($arr as $v)
						{
							// echo '555';
							$biaoqian['biaoqian_id'] = $v;
							$biaoqian['art_id'] = $art_id;   // 这个未定义
							$biao = DB::name('biaoqian_link')->insert($biaoqian);
							if($biao)
							{
								$this->success('修改成功');	
							}	
						}
					}
				}else{
					foreach($arr as $v)
					{
					// echo '333';
						$biaoqian['biaoqian_id'] = $v;
						$biaoqian['art_id'] = $art_id;   // 这个未定义
						$biao = DB::name('biaoqian_link')->insert($biaoqian);
						if($biao)
						{
							$this->success('修改成功');	
						}	
					}
				}
			}	
		}

		// // 替换 content 中的 空格 为 <br>
    	$newline = array("\r\n","\n","\r");
    	$content = str_replace($newline, "<br />",$data['content']); 

    	$art_translate 	= str_replace($newline, "<br />",$data['art_translate']); //译文
    	$art_zhu 		= str_replace($newline, "<br />",$data['zhushi']); //注释
    	$art_shang 		= str_replace($newline, "<br />",$data['art_shang']); //赏析
    	$art_back 		= str_replace($newline, "<br />",$data['art_back']); //创作背景

		$datb['art_title'] 		= $data['title'];
		$datb['art_content'] 	= $content;	
		$datb['art_author']		= $data['author'];
		
		$datb['art_translate']	= $art_translate;
		$datb['art_zhu']		= $art_zhu;
		$datb['art_shang']		= $art_shang;
		$datb['art_back']		= $art_back;
			
		$res = DB::name('article_ci')->where('art_id',$art_id)->update($datb);
		if($res)
		{
			$this->success('修改成功');	//	不写跳转地址，跳回原页面
		}	
		
	
		// return $this->fetch('');
	}
}


?>