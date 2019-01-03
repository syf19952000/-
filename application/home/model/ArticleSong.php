<?php
namespace app\home\model;
// namespace Home\Model;
// namespace app\index\model;
use think\Model;
use think\Session;
use think\Db;
use think\Request;

class ArticleSong extends Model
{
	protected $pk = 'id';
	protected $table = 'tp_article_song';

	public function insert($data)
	{
		$article_tang = new ArticleSong;
		$result = $this->save($data);
		return $result;
	}

	public function update_my($where,$data)
	{
		$result = $this->where($where)->update($data);
		return $result;
	}

	public function delete_my($where)
	{
		$result = $this->where($where)->delete();
		return $result;
	}

	// 返回一维数组，未找到则返回空数组
	// public function find($where=array(),$fileds="*")
	// {
	// 	return $this->field($fileds)->where($where)->find();
	// }

	public function find($where)
	{
		return $this->where($where)->find();
	}

	// 返回二维数组，未找到则返回空
	// public function findall($where=array(),$fileds="*",$order="id desc")
	// {
	// 	return $this->field($fileds)->where($where)->order($order)->select();
	// }
	// 
	public function findall($order="id desc")
	{
		// return $this->select();
		return DB::name('article_song')->order($order)->select();
	}














}

?>