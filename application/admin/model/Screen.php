<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
* 
*/
class Screen extends Model
{
	protected $table = 'screen';
	protected $other = 'round';//走马灯配置
	/**
	 * [user_list 用户列表]
	 */
	function lists($type='',$whr='')
	{
		if($type)
		{
			return Db::name($this->other)->where($whr)->select();
		}
		return Db::name($this->table)->where($whr)->select();
	}


	/**
	 * [adds 添加设置]
	 * @param  [type] $data    设置数据
	 */
	function adds($data,$type=''){
		if($type){
			return Db::name($this->other)->insertGetId($data);
		}
		return Db::name($this->table)->insertGetId($data);
	}

	/**
	 * [adds 编辑设置]
	 * @param   $data    设置数据
	 * @param   $whr     条件
	 * @param   $type    1/0 走马灯/页面
	 */
	function updates($data,$whr,$type=''){
		if($type){
			return Db::name($this->other)->where($whr)->update($data);
		}
		return Db::name($this->table)->where($whr)->update($data);
	}


	/**
	 * [search 查询设置]
	 */
	function search($where,$type=''){
		if($type){
			return Db::name($this->other)->where($where)->find();
		}
		return Db::name($this->table)->where($where)->find();
	}

	/**
	 * [user_delete 删除配置]
	 */
	function del($where,$type=''){
		if($type){
			return Db::name($this->other)->where($where)->delete();
		}
		return Db::name($this->table)->where($where)->delete();
	}

	/**
	 * [search_language 查询语言配置]
	 */
/*	function search_language()
	{
		return Db::name($this->table)->where($where)->delete();
	}*/

	//添加多条数据
	function adds_all($data)
	{
		return Db::name('screen_main')->insertAll($data);
	}

	//获取主页设置
	function search_main()
	{
		return Db::name('screen_main')->select();
	}

	//清楚原有设置
	function del_main()
	{
		$olds = Db::name('screen_main')->field('c_screenid')->select();	
		if ($olds) {
			$sql = "truncate table t_screen_main";
			return Db::query($sql);
		}
		return '';
	}
}
?>