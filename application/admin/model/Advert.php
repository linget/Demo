<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
* 
*/
class Advert extends Model
{
	protected $table = 'advert_material';
	/**
	 * [advert_list 素材列表]
	 */
	function advert_list($where = '')
	{
		if ($where) {
			return Db::name($this->table)->where($where)->find();
	
		}
		return Db::name($this->table)->paginate(15);
	}

	/**
	 * [user_insert 素材添加]
	 * @param  [type] $data    数据
	 */
	function advert_insert($data){
		$res_id = Db::name($this->table)->insertGetId($data);
		return $res_id; 
	
	}

	/* 获取素材编号（唯一） */
	function find_numb()
	{
		$info = Db::name($this->table)->order('c_id desc')->select();
		if ($info) {
			$numb = $info[0]['c_number']+1;
			$numb = str_pad($numb,6,"0",STR_PAD_LEFT);
		}else{
			$numb = str_pad(1,6,"0",STR_PAD_LEFT);
		}
		return $numb;
	}

	/* 素材删除 */
	function advert_del($where){
		$res = Db::name($this->table)->where($where)->find();
		if ($res) 
		{
			$file = $res['c_url'];
			//删除数据库
			$del = Db::name($this->table)->where($where)->delete();
			//删除文件
			$del_2 = @unlink(ROOT_PATH .$file);
			if ($del || $del_2) 
			{
				return true;exit;
			}
		}
		return false;
	}
	/* 素材更新 */
	function advert_edit($data,$where){
		return Db::name($this->table)->where($where)->update($data);
	}
}
?>