<?php
namespace app\wap\model;
use think\Model;
use think\Db;
/**
* 用户登陆注册表模型
*/
class Users extends Model
{
	private $_table = 'admin_users';

	/**
	 * [check_user 用户校验]
	 * @return [type] [description]
	 */
	function check_user($user = null){
		$whr['c_username'] = $user;
		$result = Db::name($this->_table)->where($whr)->field('c_id,c_username,c_password,c_state')->find();
		return $result;
	}
}
?>