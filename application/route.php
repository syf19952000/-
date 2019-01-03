<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//前台路由
Route::rule('home_regpage','home/login/regpage');	//注册页面
Route::rule('home_reg','home/login/reg');			//注册操作
Route::rule('index','home/login/index');   			//前台登录
Route::rule('homelogin_login','home/login/login');  // 登录操作
Route::rule('home_index','home/index/index');   		// 前台首页

Route::rule('home_indexindex','home/index/home/index');   		// 前台首页


Route::rule('home_createshi','home/article/createpage'); 		// 新建 诗 文章页面 
Route::rule('home_article','home/article/insert'); 				// 新建文章操作
Route::rule('home_articleonly','home/article/display_only');	// 展示文章详情页 
Route::rule('home_shiall','home/article/display_all');			// 展示文章列表页，所有
Route::rule('home_zhanshi','home/article/zhanshi');				// 各朝代表展示

Route::rule('home_guanyu','home/article/guanyu');	
Route::rule('home_chaodai','home/article/chaodai');				// 显示朝代标签
Route::rule('home_cate','home/cate/index');						// 无限极分类
Route::rule('home_collection','home/article/collection');		// 收藏
Route::rule('home_biaoqian','home/article/biaoqian');			// 展示所有标签
Route::rule('home_zhanshibiao','home/article/zhanshibiao');		// 各标签文章列表展示





Route::rule('home_api','home/article/req_api'); 				// 测试接口
// Route::rule('home_wxpay','home/article/wxpay'); 				// 测试微信支付
Route::rule('home_wxpayapi','home/wxpayapi/index'); 				// 测试微信支付



Route::rule('home_createci','home/ci/createci'); 				// 词
Route::rule('home_ciall','home/ci/display_all');				// 展示 词 列表页
Route::rule('home_cidetail','home/ci/display_only');			// 展示 词 详情页 




Route::rule('home_createju','home/ju/createju'); 				// 句子
Route::rule('home_juall','home/ju/display_all');				// 展示名句列表页
Route::rule('home_judetail','home/ju/display_only');			// 展示名句详情页 


Route::rule('home_createsing','home/sing/createsing'); 			// 发布歌词
Route::rule('home_singall','home/sing/display_all');			// 展示歌词列表页
Route::rule('home_singdetail','home/sing/display_only');		// 展示歌词详情页 


Route::rule('home_createtopic','home/topic/createtopic'); 		// 发布话题
Route::rule('home_topicall','home/topic/display_all');			// 展示话题列表页
Route::rule('home_topicdetail','home/topic/display_only');		// 展示话题详情页 


Route::rule('home_my','home/user/index');						// 个人中心,进去的页面展示一下小清新吧
Route::rule('home_myindex','home/user/myindex');				// 个人中心资料页面
Route::rule('home_mygai','home/user/mygai');				    // 修改资料
Route::rule('home_gaixiang','home/user/gaixiang');				// 修改头像
Route::rule('home_gaipass','home/user/gai_pass');				// 修改密码
Route::rule('home_myarticle','home/user/myarticle');			// 我的发布
Route::rule('home_mycollection','home/user/mycollection');			// 我的发布





//后台路由


//后台登录
Route::rule('admin_page','admin/index/index'); 		//返回后台主页面，后台没有注册页面，只有登录，管理员在程序中写好
Route::rule('admin_login','admin/login/index');		//调用login控制器的index方法，返回登录页面
Route::rule('admin_create','admin/login/login');
Route::rule('loginout','admin/login/loginout');		// 后台退出


//文章
Route::rule('admin_article','admin/article/index'); 				// 文章列表
Route::rule('article_displayonly','admin/article/display_only');	// 展示文章详情页
Route::rule('admin_editpage','admin/article/editpage'); 			// 展示编辑页面



Route::rule('admin_ju','admin/ju/index'); 			// 名句列表
Route::rule('admin_editju','admin/ju/editpage'); 	// 展示编辑页面
// Route::rule('admin_deleteju','admin/ju/editpage'); 	// 展示编辑页面


Route::rule('admin_sing','admin/sing/index'); 		// 音乐列表
Route::rule('admin_editsing','admin/sing/editpage'); 		// 音乐列表


Route::rule('admin_topic','admin/topic/index'); 	// 话题列表
Route::rule('admin_edittopic','admin/topic/editpage'); 	// 话题列表



//分类
Route::rule('admin_cate','admin/cate/index'); 		// 分类列表
Route::rule('admin_addcate','admin/cate/addcate');  // 添加分类页面
Route::rule('admin_add','admin/cate/add222');  		// 添加分类操作
Route::rule('admin_catesearch','admin/cate/search');  // 搜索分类


// 标签
Route::rule('admin_biao','admin/biao/index'); 		// 标签列表



//用户
Route::rule('user','admin/user/index');				//用户 user,不知道为啥，必须路由定义，user的html才访问方法 ，article就不用在路由中定义
Route::rule('user_delete','admin/user/delete');	
Route::rule('user_edit','admin/user/editpage');
Route::rule('user_editup','admin/user/edit');
Route::rule('admin_usersearch','admin/user/usersearch');		// 搜索




// 定义应用目录
// define('APP_PATH',__DIR__.'/../application/');
define('PAY_PATH',__DIR__.'/../vendor/pay/');



return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
