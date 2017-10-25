<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**
* 
*/
class Passenger extends Model
{
	protected $table = 'passenger';
	/**
	 * [user_list 旅客列表]
	 * @param  $where 条件
	 * @param  $order 排序规则
	 */
	function user_list($where = '',$order = '')
	{
		if ($where) 
		{
			return Db::name($this->table)->where($where)->order($order)->paginate(15);
		}
		return Db::name($this->table)->order($order)->paginate(15);
	}

	/**
	 * [get_airs 获取当天提醒航班号]
	 * 并采集数据
	 */
	function get_airs()
	{
		self::insert_data();
		$where['c_time'] = date('Y-m-d',time());
		$where['c_type'] = 1;
		return Db::name($this->table)->where($where)->select();
	}

	/**
	 * [get_airs 获取当天提醒列车号]
	 * 并采集数据
	 */
	function get_trains()
	{
		self::insert_data();
		$where['c_time'] = date('Y-m-d',time());
		$where['c_type'] = 2;
		return Db::name($this->table)->where($where)->select();
	}


	function search($where='')
	{
		return Db::name($this->table)->where($where)->select();
	}

	//数据采集
	static public function insert_data()
	{
		$data = [];
		$service = new  \app\admin\service\Passenger_data();//抓取类
		$result = $service->get_data();
		$now = date('Y-m-d H:i:s',time());
		$table = 'passenger';

		if(!$result){ return ;}
		foreach ($result as $key => $value) 
		{
			$numb = $value['kind_id'];
			if($numb== 1){ $numb=2;}else{ $numb=1;}//状态切换1/2(航班/列车)
			//判断是否存在
			$where['c_number'] = $value['code_name'];
			$is = Db::name($table)->where($where)->find();
			if(!$is){
				$data[] = ['c_number'=>$value['code_name'],'c_time'=>$value['code_time'],'c_type'=>$numb,'c_addtime'=>$now];
			}
			continue;
		}

		if($data)
		{
			Db::name($table)->insertAll($data);
		}
		
	}
}
?>