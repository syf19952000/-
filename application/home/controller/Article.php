<?php
namespace app\home\controller;

use think\Controller;
use app\index\controller\Commen;  // 继承index/commen类，来遍历显示侧边栏
use app\home\model\Article as Articleaa;
use app\home\model\Aaguanlian;
use think\Session;
use think\Db;
use think\Request;
use think\Page;

class Article extends Commen
{

	

	public function index()
	{
		return $this->fetch();
	}

	public function ajaxsearch()
	{
		// 同时搜索标题和内容来展示搜索结果,可以用 合并数组 或者 like查询 中间用 or
		$data = $_POST; 
		$search = $data['name']; 
		
		// 第二种
		$map['art_title|art_content'] = array('like', '%' . $search . '%');
		$result = DB::name('article')->where($map)->select();
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

	// 提交表单实现搜索
	public function search()
	{
		$data = $_POST['search'];    // 获取到的关键词
		$result = DB::name('article')->where('art_title','like','%'.$data.'%')->paginate(8);
		$this->assign('data',$result);    // 这样样式就又变了
		return $this->fetch('searchart_list');
	}



	public function biaoqian()
	{
		$biaoqian = DB::name('biaoqian')->select();
		$this->assign('biaoqian',$biaoqian);
		return $this->fetch();
	}

	// 标签 列表的展示
	public function zhanshibiao()
	{
		// echo '<pre>';
		$data = $_GET;
		$biaoqian_id = $data['biaoqian_id'];   // 点击下一页的时候，链接就携带page了

		$datc = DB::name('biaoqian_link')->where('biaoqian_id',$biaoqian_id)->select();
		if(empty($datc))
		{
			return $this->fetch('article_listnull');
		}
		$art_ids = '';
		foreach($datc as $v)
		{
			$art_ids .= $v['art_id'].',';
		}
		$art_ids = rtrim($art_ids,',');    // 查询出来的所有 art_ids
		// 我诗词 表 都加了标签库，只要分别从两个表查谁的art_id在里面，再合并就好了


		$needle = ",";				//判断是否包含a这个字符
	    $tmparray = explode($needle,$art_ids);
	    $count = count($tmparray);
	    if($count>1){
	   		$where = " art_id in (".$art_ids.")";
	    } else{
	    	$where = " art_id = ".$art_ids;    
	    }

		$a = DB::name('article')->where($where)->select();
		$b = DB::name('article_ci')->where($where)->select();
		// $a = Db::name('article')->where($where)->order('id asc')->paginate(5);  		// 分页展示
		$c = array_merge((array)$a,(array)$b);
	
		if($data['page']){   
       		 $page = $data['page'];
		}else{
			$page = '0';
		}

		// 分页
        $count = count($c);  // 数据总条数
        $pagenum = 1;
        $pagesize = 6 ;         // 每页的数据数
        $pagecount = ceil($count/$pagesize);    // 总页数 ,尾页
        // 当$page = 0 的时候，$page应该加1，
        $page2 = $page + 1;
        //$start = ($page-1)*$pagesize;     // 计算每次分页的开始位置，当是第一页的时候，$page=1,当时第二页的时候，$page还是1
        $start = ($page2-1)*$pagesize;  
        $pagedata = array();
        $pagedata = array_slice($c,$start,$pagesize);  // 为什么总是重复几条数

        $this->assign('pagecount',$pagecount-1);
		$this->assign('page',$page);   
        $this->assign('data',$pagedata);
		$this->assign('biaoqian_id',$biaoqian_id);

		return $this->fetch('biaoqian_list');

	}


	public function guanyu()
	{
		return $this->fetch();
	}

	public function createpage()
	{
		// echo '<pre>';
		// 只显示风格标签
		// $list=DB::name('cate')->where('pid',53)->select();
		// $this->assign('alist',$list);
		// 把诗词歌赋填上，还是用户发表文章的时候只选择是不是诗词歌赋
		$biaoqian = DB::name('biaoqian')->select();
		$this->assign('biaoqian',$biaoqian);
		
		return $this->fetch();
	}

	// 展示不同 type 的文章 ，这只是查的 article 表，因为那些标签都是文章表的
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
			return $this->fetch('article_listnull');
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
		$a = Db::name('article')->where($where)->order('id asc')->paginate(8);  		// 分页展示
		// var_dump($a);
		$this->assign('data',$a);
		return $this->fetch('article_list');
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



    public function display_only()	// 现在评论列表显示的都一样
	{
		$udata = session::get('USER_INFO');
		$data = $_GET;
		$id = $data['id'];					// article表的id,查关联表的type_id
		// var_dump($data);
		// die;
		$data2 = DB::name('article')->where('id',$id)->find();			
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

	 	// 怎么让用户如果已收藏的情况下，进来，图标就是亮的
	 	// 决定收藏的颜色  这种方式是通过查询数据库 里决定颜色 
	 	$ff = DB::name('collection')->where('uid',$udata['uid'])->where('art_id',$art_id)->select();		// 应该判断是不是当前用户
	 	// die;
	 	if($ff)
	 	{
	 		$color =5;
	 	}else{
	 		$color =9;
	 	}
	 	$this->assign('color',$color);

	 	// echo '<pre>';	
	 	// 展示标签列表
	 	$biao_art = DB::name('biaoqian_link')->where('art_id',$art_id)->select();  // 可能有多个标签
	 	foreach($biao_art as &$v)
	 	{
	 		$biao_name = DB::name('biaoqian')->where('id',$v['biaoqian_id'])->find();
	 		$v['biaoqian_name'] = $biao_name['biaoqian_name'];
	 	}
	 	
		$this->assign('biaoqian',$biao_art);

		// echo '<pre>';
		// var_dump($data2);
	 // 	die;
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

    // 用户未登陆的情况下，不能发表评论
    // 添加子评论
    public function add_comment()
	{
		// echo '<pre>';
		$data = $_POST;
		// var_dump($data);
		// die;
		$uname1 = session::get('USER_INFO');
		$uname = $uname1['uname'];
		$uid = $uname1['uid'];

		if($uid)
		{
			$datb['content'] = $data['content'];
			$datb['uid']	 = $uid;								
			$datb['parent_id']  = $data['comment_id'];
			$datb['art_id']     = $data['art_id'];           // 文章表的art_id
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
		// // 明明art_id 是2，可加进来的是34，第一条添加的art_id正确，第二次发表空评论，添加的art_id就是第一次发表空评论的id
		// die;
		$uname1 = session::get('USER_INFO');
		$uname = $uname1['uname'];
		$uid = $uname1['uid'];

		$datb['content'] = $data['message'];
		$datb['uid']	 = $uid;								
		$datb['parent_id']  = 0;
		// 现在改成添加 文章的art_id 而不是 id
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
	

    // 我是应该添加文章那把分类的那个拿过去，还是单独做标签
    // 想实现分行，在插入数据库的时候，通过符号来加一个<br>标签
	public function insert()
	{
		echo '<pre>';
		$data = $_POST;	// 获取的三级联动的值自己就传过来了,如果不选择分类，pid这个字段就不会传过来
		// var_dump($data);
		// die;

		$session = session::get('USER_INFO');
		$uid = $session['uid'];
		$article      = new Articleaa();		
		$aaguanlian   = new Aaguanlian();
		// 插入 guanlian 表数据
		$cc['uid'] 		= $uid;
		if(isset($data['pid']))
		{
			$cc['type_id'] 	= $data['pid'];  // 用户输入的类别
		}else{
			// $cc['type_id'] 	= null;			// 否则默认是
			$cc['type_id'] 	= null;			// 现在没有选择，插进来的是73,应该是下拉列表的默认值

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
    	$content = str_replace($newline, "<br />",$data['content']); //内容

    	$art_translate 	= str_replace($newline, "<br />",$data['art_translate']); //译文
    	$art_zhu 		= str_replace($newline, "<br />",$data['art_zhu']); //注释
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
		$res = $article->insert($datb);
		if($res)
		{
			$this->success('发布成功');	//	不写跳转地址，跳回原页面  发布页面，如果想让用户跳到 详情页呢
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

	public function detail_collection()  // detail 页面调这个方法，但是数据库取消了，颜色却没变
	{
		// 这是为啥呢，detail页面点击图标之后，数据库删除数据了，但颜色还有，调detail方法也是查询数据库显示的啊
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
		// echo '<pre>';		// 有这个，html的样式就全乱了不知道为啥
		// $article = new Articleaa();
		// $data = $article->findall();

		$list = Db::name('article')->order('id asc')->paginate(8);		
		$this->assign('data', $list);
		return $this->fetch('article_list');	// view下article下的  名.html
		
	}

	

	public function edit()
	{
		// 如何让 点击修改时跳到对应修改页面
		// 定义当。。时，找什么
		// echo '111';
		$url = $_GET;
		$art_id = $url['art_id'];
		$data = DB::name('aaguanlian')->where('id',$art_id)->find();
		$type_id = $data['type_id'];

		if($type_id >=81 && $type_id <=519 )
		{
			$type_id = "81-519";
		}

		switch ($type_id) {
			case '73':
				$link = 'article/';
				$data2 = DB::name('article')->where('art_id',$art_id)->find();
				break;
			case '74':
			// 如果用户选择词牌名的话，那存进去的type_id 就不是 74而是一个范围
				$link = 'ci/';
				$data2 = DB::name('article_ci')->where('art_id',$art_id)->find();
				break;
			case '78':
				$link = 'ju/';
				$data2 = DB::name('ju')->where('art_id',$art_id)->find();
				break;
			case '79':
				$link = 'sing/';
				$data2 = DB::name('sing')->where('art_id',$art_id)->find();				
				break;
			case '80':
				$link = 'topic/';
				$data2 = DB::name('topic')->where('art_id',$art_id)->find();
				break;
			case '81-519':
				$link = 'ci/';
				$data2 = DB::name('article_ci')->where('art_id',$art_id)->find();
				break;
			default:
				# code...
				break;
		}
		// echo '<pre>';
		// 显示原标签，
		$yuan_biao = DB::name('biaoqian_link')->where('art_id',$art_id)->select();
		if($yuan_biao)
		{
			$biaoqian_ids = '';
			foreach($yuan_biao as $v)
			{
				$biaoqian_ids .= $v['biaoqian_id'].',';
			}

			$biaoqian_ids = rtrim($biaoqian_ids,',');    // 查询出来的所有 biaoqian_ids

			$needle = ",";				//判断是否包含a这个字符
		    $tmparray = explode($needle,$biaoqian_ids);
		    $count = count($tmparray);
		    if($count>1){
		   		$whereyuan = " id in (".$biaoqian_ids.")";
		    } else{
		    	$whereyuan = " id = ".$biaoqian_ids;    
		    }

			$yuanbiaoqian = DB::name('biaoqian')->where($whereyuan)->select();  // 查出来原标签名

		}else{
			$yuanbiaoqian = '';
		}

		// var_dump($yuanbiaoqian);
		// die;
		$this->assign('yuanbiaoqian',$yuanbiaoqian);

		// 显示原词牌名
		$yuan_cipai = DB::name('aaguanlian')->where('id',$art_id)->find();
		$yuan_ci = DB::name('cate')->where('id',$yuan_cipai['type_id'])->find();

		$this->assign('yuancipai',$yuan_ci);
		

		// 遍历词牌名显示
		$where['pid'] = array('in','74,81,85,114,492,559');
		$list = DB::name('cate')->where($where)->field("id,name,pid,path,concat(path,'-',id) as bpath")->order('bpath')->select();
		// 把诗词歌赋填上，还是用户发表文章的时候只选择是不是诗词歌赋
		foreach($list as $key=>$value){
		 	$list[$key]['count']=count(explode('-',$value['bpath']));
		}
		
		$biaoqian = DB::name('biaoqian')->select();
		$this->assign('biaoqian',$biaoqian);

		$this->assign('alist',$list);
	

		// 在这统一赋值的 $art_id
		$this->assign('data',$art_id);  // 这赋值art_id
		$this->assign('data2',$data2);  // 这赋值整个表单

        return $this->fetch($link . 'editpage');
	}

	public function editpage()
	{
		// echo '<pre>';
		$data = $_POST;
		// var_dump($data);
		$session = session::get('USER_INFO');
		$uid = $session['uid'];
		$article      = new Articleaa();
		// 标签库的更改，我是是先清除标签，还是更改标签还是直接就不接受标签了
		$art_id = $data['art_id'];
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
								$this->success('修改成功');	//	不写跳转地址，跳回原页面
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
							$this->success('修改成功');	//	不写跳转地址，跳回原页面
						}	
					}
				}
			}	
		}

		// 修改 诗词歌赋的类别，它这是有默认值的
		$ci['type_id'] = $data['pid'];
		$cipai = DB::name('aaguanlian')->where('id',$data['art_id'])->update($ci);

		
		// 替换 content 中的 空格 为 <br>
    	$newline = array("\r\n","\n","\r");
    	$content = str_replace($newline, "<br />",$data['content']); 

    	$art_translate 	= str_replace($newline, "<br />",$data['art_translate']); //译文
    	$art_zhu 		= str_replace($newline, "<br />",$data['art_zhu']); //注释
    	$art_shang 		= str_replace($newline, "<br />",$data['art_shang']); //赏析
    	$art_back 		= str_replace($newline, "<br />",$data['art_back']); //创作背景
	
		$datb['art_title'] 		= $data['title'];
		$datb['art_content'] 	= $content;
		$datb['art_author']		= $data['author'];

		$datb['art_translate']	= $art_translate;
		$datb['art_zhu']		= $art_zhu;
		$datb['art_shang']		= $art_shang;
		$datb['art_back']		= $art_back;
	
		
		$where = "art_id = ".$data['art_id'];

		$res = $article->update_my($where,$datb);
		if($res)
		{
			$this->success('修改成功');	//	不写跳转地址，跳回原页面
		}	
		

		// return $this->fetch('');  // 这样写报错	
	}


	public function detail()
	{
		// 如何让 点击修改时跳到对应修改页面
		// 定义当。。时，找什么
		$udata = session::get('USER_INFO');
		$uid = $udata['uid'];
		$url = $_GET;
		$art_id = $url['art_id'];
		$data = DB::name('aaguanlian')->where('id',$art_id)->find();
		$type_id = $data['type_id'];

		if($type_id >=81 && $type_id <=519 )
		{
			$type_id = "81-519";
		}

		if($type_id >=75 && $type_id <=77)
		{
			$type_id = "73";
		}
		switch ($type_id) {
			case '73':
				$link = '/article/';   // 让他走不同控制器下的display_only方法	
				$aa = DB::name('article')->where('art_id',$art_id)->find();
				break;
			case '74':
			// 如果用户选择词牌名的话，那存进去的type_id 就不是 74而是一个范围
				$link = '/ci/';		
				$aa = DB::name('article_ci')->where('art_id',$art_id)->find();
				break;
			case '78':
				$link = '/ju/';
				$aa = DB::name('ju')->where('art_id',$art_id)->find();
				break;
			case '79':
				$link = '/sing/';	
				$aa = DB::name('sing')->where('art_id',$art_id)->find();
				break;
			case '80':
				$link = '/topic/';
				$aa = DB::name('topic')->where('art_id',$art_id)->find();			
				break;
			case '81-519':
				$link = '/ci/';
				$aa = DB::name('article_ci')->where('art_id',$art_id)->find();
				break;
			default:
				# code...
				break;
		}
		// 显示音频地址
		if($aa['video_src'])
	 	{
	 		$video_src = explode('.',$aa['video_src']);
	 		$video_zhui = $video_src[1];

	 		if($video_zhui == 'mp3')
	 		{
	 			$video_mp3 = $aa['video_src'];
	 		}else{
	 			$video_mp3 = '';
	 		}

	 		if($video_zhui == 'mp4')
	 		{
	 			$video_mp4 = $aa['video_src'];
	 		}else{
	 			$video_mp4 = '';
	 		}
	 		$this->assign('videomp3',$video_mp3); 
			$this->assign('videomp4',$video_mp4); 
	 	}else{
	 		$video_mp3 = '';
	 		$video_mp4 = '';
	 		$this->assign('videomp3',$video_mp3); 
			$this->assign('videomp4',$video_mp4); 
	 	}			



		
		// 提示未定义变量 aa
		// var_dump($type_id); // 因为之前没有判断歌赋其他的type_id
		// die;
		$id = $aa['id'];
		//获取评论总数 这的art_id 是文章表的 id
		//发表 名句评论的时候，存进去的 art_id 是1
		$num = DB::name('comment')->where('art_id',$id)->count(); 
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
					if($v['art_id'] == $art_id)  // 这是遍历的 art_id 都存在
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

	 	// 决定收藏的颜色  这种方式是通过查询数据库 里决定颜色 
	 	$ff = DB::name('collection')->where('uid',$udata['uid'])->where('art_id',$art_id)->select();		// 应该判断是不是当前用户
	 	// die;
	 	if($ff)
	 	{
	 		$color =5;
	 	}else{
	 		$color =9;
	 	}



	 	// 查头像
		$img = DB::name('user')->where('id',$uid)->find();
		$touxiang = $img['touxiang'];

		// 展示标签列表
	 	$biao_art = DB::name('biaoqian_link')->where('art_id',$art_id)->select();  // 可能有多个标签
	 	foreach($biao_art as &$v)
	 	{
	 		$biao_name = DB::name('biaoqian')->where('id',$v['biaoqian_id'])->find();
	 		$v['biaoqian_name'] = $biao_name['biaoqian_name'];
	 	}
		$this->assign('biaoqian',$biao_art);
		$this->assign('touxiang',$touxiang);
	 	$this->assign('color',$color);
		$this->assign('data',$aa);
		// dispaly_only 方法 显示 xiangqing页
        return $this->fetch($link . 'detail');  // 应该让用户进有收藏的那个页面吧，而不是编辑,这个展示的是页面而不是进方法
	}



	// 收藏列表页 删除收藏
	public function delete_collection()
	{
		// echo '<pre>';
		$data = $_POST;
		$art_id = $data['id'];   // 收藏的 art_id
		
		$where['art_id'] = $art_id;
		$result = DB::name('collection')->where($where)->delete();	
		// var_dump($result);
		// die;
		if($result)
		{
			$returnData = ['code'=>3, 'info'=>'取消成功'];
		}else{
			$returnData = ['code'=>4, 'info'=>'取消收藏失败'];
		}
		
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}


	// 我的发布列表页 删除发布，删除我的发布的时候，也要把文章下面的评论删掉
	public function delete_fabu()
	{
		// echo '<pre>';
		$data = $_POST;
		$art_id = $data['id'];   // 发布的 art_id,
		
		// 根据它的 type 进不同的表查数据
		$result = DB::name('aaguanlian')->where('id',$art_id)->find();	
		$type_id = $result['type_id'];

		if($type_id >=81 && $type_id <=519 )
		{
			$type_id = "81-519";
		}

		switch ($type_id) {
			case '73':
				$data = DB::name('article')->where('art_id',$art_id)->delete();
				break;
			case '74':
			// 如果用户选择词牌名的话，那存进去的type_id 就不是 74而是一个范围
				$data = DB::name('article_ci')->where('art_id',$art_id)->delete();
				break;
			case '78':
				$data = DB::name('ju')->where('art_id',$art_id)->delete();
				break;
			case '79':
				$data = DB::name('sing')->where('art_id',$art_id)->delete();				
				break;
			case '80':
				$data = DB::name('topic')->where('art_id',$art_id)->delete();
				break;
			case '81-519':
				$data = DB::name('article_ci')->where('art_id',$art_id)->delete();
				break;
			default:
				# code...
				break;
		}
		// 删除我的发布的时候，也要把文章下面的评论删掉
		// 可是有的评论写的好好的，舍不得删掉
		
		$data2 = DB::name('comment')->where('art_id',$art_id)->delete();

		if($data)
		{
			$returnData = ['code'=>3, 'info'=>'删除成功'];
		}else{
			$returnData = ['code'=>4, 'info'=>'删除失败'];
		}
		
		return json_encode($returnData,JSON_UNESCAPED_UNICODE);	
	}


}


?>