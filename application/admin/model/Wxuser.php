<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
* 
*/
class Wxuser extends Model
{
	protected $table = 'wxuser';
	/**
	 * [user_list 用户列表]
	 */
	function user_list($where)
	{
		return Db::name($this->table)->where($where)->paginate(15);
	}

	/**
	 * [get_name 获取用户]
	 */
	function get_user($where){
		return Db::name($this->table)->where($where)->find();
	}

	/**
	 * [get_name 获取用户]
	 */
	function get_userlist($fields,$where= null){
		if ($where) {
			return Db::name($this->table)->where($where)->field($fields)->select();
		}
		return Db::name($this->table)->field($fields)->select();
	}

	/**
	 * [user_insert 用户添加]
	 * @param  [type] $data    用户数据
	 * @param  [type] $role_id 用户角色
	 */
	function user_insert($data){
		$data['c_addtime'] = date('Y-m-d H:i:s',time());
		$user = Db::name($this->table)->insert($data);
		return $user;	
	}

	/**
	 * [user_save 更新用户信息]
	 */
	function user_save($data,$where){
		if ($where&&$data) 
		{
			$user = Db::name($this->table)->where(['c_id'=>$where['a.c_id']])->update($data);
			if($user){
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * [user_delete 删除管理员]
	 */
	function user_delete($where){
		if(!$where) { return false;}
		$b = Db::name('admin_user_role')->where(['c_userid'=>$where['c_id']])->delete();
		return Db::name($this->table)->where($where)->delete();
	}

	/* 修改用户登录权限 */
	function chage_role($where,$value)
	{
		return Db::name($this->table)->where($where)->setField('c_state',$value);
	}
}
?>