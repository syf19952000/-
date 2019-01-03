<?php
namespace app\home\controller;

use think\Controller;
use app\index\controller\Commen;  // 继承index/commen类，来遍历显示侧边栏
use app\home\model\Article as Articleaa;
use app\home\model\Aaguanlian;
use think\Session;
use think\Db;
use think\Request;

require_once PAY_PATH."WxPayException.php";
require_once PAY_PATH."WxPayConfig.php";
require_once PAY_PATH."WxPayData.php";

// 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法
// 每个接口有默认超时时间*（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）

// 参考网址  https://blog.csdn.net/kolinhu/article/details/81061722



class Wxpayapi extends Commen
{

	

	public function index()
	{
		return $this->fetch();
	}



	// 对应 html 页面中第2种 js
	// public function get_paysign($volume_info){

	// 	vendor('pay.WxPayApi');
	// 	vendor('pay.WxPayJsApiPay');
	// 	$tools = new \JsApiPay();     // 引入微信js-sdk 支付 api
	// 	$openid = Session::get('front_pay_openid');
	// 	$payinfo = Db::name('red_envelope_pay_account')->find();  // 获取公众号应用

	// 	define("PARTERKEY",$payinfo['parterkey']);
	// 	// 此处省略多行
	// }
	



	

   

		
}


?>