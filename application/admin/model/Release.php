<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
*  发布数据表操作
*/
class Release extends Model
{
	protected $table = 'play_screen';
	protected $table2 = 'release';

	function list_table2(){
		return  Db::name($this->table2)->paginate(15);
		
	}

	function insert_table2($data){
		$data['c_addtime'] = date('Y-m-d H:i:s',time());
		$res = Db::name($this->table2)->insert($data);
		return $res;
	}

	public function find_data($where,$order =null){
		if (!$order) {
			return Db::name($this->table2)->where($where)->find();
		}
		return Db::name($this->table2)->where($where)->order($order)->find();
	}

	/**
	 * [user_list 用户列表]
	 */
	function Release_list($where=null)
	{
		if (!$where) {
			return Db::name($this->table)->where($where)->paginate(15);
		}
		return Db::name($this->table)->paginate(15);
	}

	/**
	 * [get_name 获取账号]
	 */
	function get_name(){
		$max = Db::name($this->table)->max('c_username');
		++$max;
		$username = str_pad($max,6,'0',STR_PAD_LEFT);
		return $username;
	}

	/**
	 * [user_insert 用户添加]
	 * @param  [type] $data    用户数据
	 * @param  [type] $role_id 用户角色
	 */
	function user_insert($data,$role_id){
		$user_id = Db::name($this->table)->insertGetId($data);
		$role = Db::name('admin_user_role')->insertGetId(['c_userid'=>$user_id,'c_roleid'=>$role_id]);
		if($user_id&&$role)
		{
			return $user_id; 
		}
		return false;	
	}

	/**
	 * [get_info 获取用户信息]
	 */
	function get_info($where)
	{
		return Db::name($this->table.' as a')->join('t_admin_user_role as b','a.c_id = b.c_userid','left')->join('t_admin_role as c','b.c_roleid=c.c_id','left')->field('a.*,b.c_userid,b.c_roleid,c.rolename')->where($where)->find();
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