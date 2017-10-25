<?php
namespace app\home\model;
use think\Model;
use think\Db;
class Wify extends Model
{

	public function getInfo($data = [])
	{
        $second = isset($data['second'])?$data['second']:'16';//30
        $start = date('Y-m-d H:i:s',time()-intval($second));
        $end = date('Y-m-d H:i:s');
        $where['addtime'] = ['between',[$start,$end]];
        $sql = "select * from wf_details  where addtime between '".$start."'and '".$end."' order by addtime desc";
        $info = Db::connect('db_config3')->query($sql);
        return $info;
	}

}