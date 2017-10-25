<?php
namespace app\index\service;

class IndexService 
{
	//生成apikey
	static public function create_apikey(){
		$charid = strtoupper(md5(uniqid(mt_rand(), true).'shuniu'));
	    $key =
	    substr($charid, 0, 8).
	    substr($charid, 8, 4).
	    substr($charid,12, 8).
	    substr($charid,16, 4).
	    substr($charid,20,8);
	    $date = date('Y-m-d H:i:s',time());

	    //$sql = "INSERT INTO api_key (`key`,`create_time`)VALUES('$key','$date')";


	    $val = ['key' => $key,'create_time' => $date];
	    $res = db('api_key','db_config2')->insert($val);
	   // $r = M('')->db(1,"MYSQL")->execute($sql);
	   	return $res;
	    /*if($r){
	    	return $key;
	    }*/
	    
	}

	//验证apikey
	public function verify_key($key){
		//$sql = 'SELECT id from api_key where `key` ="'.$key.'" limit 1';
		$id = db('api_key','db_config2')->where(array('key'=>$key))->field('id')->find();
		 if ($id) {
		 	return $id;
		 }else{
			return false;
		 }
	}

	/**
     * [get_time 获取查询时间范围]
     * @return array()
     */
    public function get_time()
    {
        $time = array();
        $time['now_time'] = date("H:i",time()-3600);
        $time['end_time'] = date("H:i",time()+4*3600);

        if(date("H",time()) == "00") 
        { 
            $time['now_time'] = "00:00";
        }
        if($time['now_time'] >= "19:00")
        {
            $time['end_time'] = "24:00";
        }
        
        return $time;
    }
}
?>