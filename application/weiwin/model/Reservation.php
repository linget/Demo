<?php
namespace app\weiwin\model;
use think\Db;
use think\Model;
class Reservation extends Model
{
	protected $table = 'room';

    //预约插入
    public function add_order($data)
    {
        return Db::name($this->table)->insert($data);
    }

    //预约查询
    public function find_order($where = null)
    {
    	if(!$where)
    	{
    		$where['c_agreetime'] = ['egt',date('Y-m-d',time())];
    		return Db::name($this->table)->where($where)->paginate(15);
    	}
    	
    	return Db::name($this->table)->where($where)->paginate(15);
    }

    //预约更新
    public function update_order($where,$data)
    {
        return Db::name($this->table)->where($where)->update($data);
    }

    //预约删除
    public function del_order($where)
    {
        return Db::name($this->table)->where($where)->delete();
    }

    //查询预约信息
    public function search_info($where)
    {
        return Db::name($this->table)->where($where)->find();
    }
}

?>