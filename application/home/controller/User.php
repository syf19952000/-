<?php
namespace app\home\controller;

use app\index\controller\Commen; 
use think\Controller;
use think\Session;
use think\Db;
use think\Request;
use think\Validate;

class User extends Commen
{
	public function index()
	{
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();
		$this->assign('data',$result);
		return $this->fetch();
	}

	public function myindex()
	{
		// echo '<pre>';
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();
		$this->assign('data',$result);
		return $this->fetch();
	}


	// article 包括诗歌赋     73  74
	// ju      名句			78
	// sing    歌词	79
	// topic   话题	80
	// ci      词	74
	// 问题是，像那些风花雪月之类的小标签我难不成要自己写，还是建在一张表，那用户发表的时候就要能选择标签
	// 还有作者，作者用户可以输入，我要是自己填，那我分类标签的作者怎么在页面中显示，我自己写？插入guanlian 表，那我数据插入哪张表
	// 现在只有上面几张表，让用户自己添加标签的话，怎么根据标签展示内容呢，对啊，把风花雪月做成标签，搜索标签，在关联表添加标签？
	
	// 从 关联表中取id，如果type_id = 某个值，就查那张表
	// 展示个人发布的
	public function myarticle()
	{
		return $this->fetch('myarticle');
	}

	public function myarticlelist()
	{
		// echo '<pre>';
		$data = $_GET;
		$uname=session::get('USER_INFO');
		$uid = $uname['uid'];
		// 第二页的时候，就没有art_id参数了，如果没有art_id,就从上一个链接取,那我第三页的时候呢，难不成还从第二页取
		
	
		$art_id = $data['art_id'];

	
		$result = DB::name('aaguanlian')->where('uid',$uid)->select();
		$ids = '';
		foreach($result as $v)
		{
			$ids .= $v['id'].',';
		}
		$ids = rtrim($ids,',');
		// $where = "art_id in ".$ids;  // 查询4张表中 这个分类的数据,查询关联表中，用户发表的所有 id ,

		if(isset($data['page'])){   
       		 $page = $data['page'];
		}else{
			$page = '0';
		}
	
		// 分页
        
		switch ($art_id) {
			case '73':
				$list = DB::name('article')->where('art_id','in',$ids)->select();
				break;
			case '74':
				$list = DB::name('article_ci')->where('art_id','in',$ids)->select();
				break;
			case '78':
				$list = DB::name('ju')->where('art_id','in',$ids)->select();  
				break;
			case '79':
				$list = DB::name('sing')->where('art_id','in',$ids)->select();  
				break;
			case '80':
				$list = DB::name('topic')->where('art_id','in',$ids)->select(); 
				break;
			default:
				# code...
				break;
		}

		$count = count($list);  // 数据总条数
       
        $pagenum = 1;
        $pagesize = 9 ;         // 每页的数据数
        $pagecount = ceil($count/$pagesize);    // 总页数 ,尾页
        // 当$page = 0 的时候，$page应该加1，
        $page2 = $page + 1;
        $start = ($page2-1)*$pagesize;  
        $pagedata = array();
		$pagedata = array_slice($list,$start,$pagesize);  

		
        $this->assign('pagecount',$pagecount-1);
		$this->assign('page',$page);   
        $this->assign('data',$pagedata);
		$this->assign('art_id',$art_id);

		// $this->assign('data',$list);   
		return $this->fetch('myarticle_list');
	}

	public function mycollection()
	{
		// echo '<pre>';
		$uname=session::get('USER_INFO');
		$uid = $uname['uid'];
		
		// 收藏表有 art_id
		// 怎么让根据 不同的type_id 查询不同的表，或者直接查每张表，看谁的art_id 在 ids 中
		// 收藏直接从收藏表就可以了，但是展示具体页面的时候还是得从类别里面取
		
		$arr = DB::name('collection')->where('uid',$uid)->select();
		$art_ids = '';
		foreach($arr as $v)
		{
			$art_ids .= $v['art_id'].',';
		}
		$art_ids = rtrim($art_ids,',');				// 这是所有的art_ids
		$where['id'] = array('in',$art_ids);

		// 是因为我收藏表里一条收藏也没了
		$data = '';  $data1 = '';  $data2 = '';  $data3 = '';  $data4 = '';
		// 怎么让根据不同的type_id进不同的表查，链接带art_id吧，但是展示的时候也是要选择出来展示到页面的
		$result = DB::name('aaguanlian')->where($where)->select();   // 取得关联表中所有art_ids 的数据
		foreach($result as $v)
		{
			$wh['art_id'] = array('in',$art_ids);
			if($v['type_id'] = 73)
			{
				$data = DB::name('article')->where($wh)->select();   // 多条数据
			}
			if($v['type_id'] = 74)
			{
				$data1 = DB::name('article_ci')->where($wh)->select();   // 多条数据
			}
			if($v['type_id'] = 78)
			{
				$data2 = DB::name('ju')->where($wh)->select();   // 多条数据
			}
			if($v['type_id'] = 79)
			{
				$data3 = DB::name('sing')->where($wh)->select();   // 多条数据
			}
			if($v['type_id'] = 80)
			{
				$data4 = DB::name('topic')->where($wh)->select();   // 多条数据
			}
		}

		// 合并数组
		$collection = array_merge((array)$data,(array)$data1,(array)$data2,(array)$data3,(array)$data4);
	    // dump($collection);die;

		$this->assign('collection',$collection);   // 这赋值的
		return $this->fetch('mycollection');
	}


	// 展示个人信息
	public function mygai()
	{
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();
		$this->assign('data',$result);
		return $this->fetch('myindex_gai');
	}

	// 执行修改操作
	public function xiugai()
	{
		echo '<pre>';
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$data = $_POST;		
		// var_dump($data);
		// die;
		// 应该进行验证数据是否规范,使用validate验证
		$result = $this->validate(
		    [
		        'name'  => $data['username'],
		        'email' => $data['email'],
		        'mobile' => $data['phone'],
		    ],
		    [
		        'name'  => 'require|max:25',
		        'email'   => 'email',
		        'mobile' =>'/^1\d{10}$/',
		    ]);
		
		if(true !== $result){
		    // 验证失败 输出错误信息
		    // dump($result);
		    $this->success('格式不正确');
		}else{
			$cc['username'] = $data['username'];
			$cc['mobile']	= $data['phone'];
			$cc['email']	= $data['email'];
			// $cc['touxiang'] = $this->img();
			$result = DB::name('user')->where('username',$uname1)->update($cc);
			$this->success('修改成功');
		}
	}


	// 展示原头像
	public function gaixiang()
	{
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();
		$img = $result['touxiang'];
		$this->assign('data',$img);
		return $this->fetch('myindex_gaixiang');
	}

	// 执行修改头像
	public function gaixiang_zhi()
	{
		$a = $this->img();
		$data['touxiang'] = $a;
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->update($data);  // 我为什么不把uid存进去呢，用的时候还得查它的uid
		if($result)
		{
			$this->success('修改成功');
		}
	}

	// 展示原密码
	public function gai_pass()
	{
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();
		$this->assign('data',$result);
		return $this->fetch('myindex_gaipass');
	}

	// 执行修改密码
	public function xiu_pass()
	{
		echo '<pre>';
		$uname=session::get('USER_INFO');
		$uname1 = $uname['uname'];
		$result = DB::name('user')->where('username',$uname1)->find();

		$data = $_POST;
		if($data['password'] != $result['userpass'])
		{
			$this->success('原密码不正确');
		}else{
			// 执行修改
			$datb['userpass'] = md5($data['password1']);
			$aa = DB::name('user')->where('username',$uname1)->update($datb);
			if($aa)
			{
				$this->success('修改密码成功');
			}else{
				$this->success('修改密码失败');
			}
		}
	}

	// 上传图像
	public function img()
	{
		// var_dump( $_FILES["img"]);	// name=img
		$dir = $_SERVER['DOCUMENT_ROOT'].'/static/imgs';    // 上传到的文件夹
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir = $dir .'/'.date('Y');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir .'/'.date('m');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir .'/'. date('d');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $imgLink = $dir .'/'. $_FILES["img"]["name"];
        move_uploaded_file($_FILES["img"]["tmp_name"], $imgLink);
        $imgLink = str_replace($_SERVER['DOCUMENT_ROOT'], '', $imgLink);
        return $imgLink;
	}


	public function ajaxsearch()
	{
		// echo '<pre>';
		// die;
		// 同时搜索标题和内容来展示搜索结果,可以用 合并数组 或者 like查询 中间用 or
		$data = $_POST; 
		$search = $data['name']; 
		$url = $data['art_id'];
		$art_id = explode('=',$url);
		$art_id = $art_id[1];

		// 第二种
		$map['art_title|art_content'] = array('like', '%' . $search . '%');
		// 我这要查对应的表,诗歌赋那，分类页还要单独拿出来展示吗，现在只有5张表的分类
		switch ($art_id) {
			case '73':
				$result = DB::name('article')->where($map)->select();
				break;
			case '74':
				$result = DB::name('article_ci')->where($map)->select();
				break;
			case '78':
				$result = DB::name('ju')->where($map)->select();
				break;
			case '79':
				$result = DB::name('sing')->where($map)->select();
				break;
			case '80':
				$result = DB::name('topic')->where($map)->select();
				break;
			
			default:
				# code...
				break;
		}

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