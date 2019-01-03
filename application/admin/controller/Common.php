<?php 
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Request;

Class Common extends Controller
{
	public function __construct(Request $request = null)
	{
		parent::__construct($request);
		if(!Session::get('id') || !Session::get('name'))
		{
			echo '<script>';  
                    echo "var destination = 'http://'+window.location.host+'/login';"; //如果没有sessionid,name就返回login页面
                    echo "window.top.location = destination;";
            echo '</script>';
		}
	}
}    



?>