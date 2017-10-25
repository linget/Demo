<?php
namespace app\lib\org;
use think\db;
/**
* 股票数据查询类
* @auth zjl 
* 2017-10-13
*/
class Shares
{
	/**
	 * [index 获取股指/沪深]
	 * @return array() 
	 */
	public function index()
	{
		$date = date('Y-m-d');//当天
        //数据库获取当天数据
        $sql = "select * from shares where date_format(time,'%Y-%m-%d')='".$date."' ";
        $result = Db::connect('db_config4')->query($sql);//历史数据
        //if ($result) { return $result[0];}//有数据直接返回
        $line = new \app\lib\org\Online();
        //无数据-判断网络
        if($line->internet())
        {
            //有外网没有数据--
            $res = self::shares();
            if (!empty($res)) 
            {
               self::insert_shares($res);
               $result = Db::connect('db_config4')->query($sql);
            }

        }else
        {
            //无网无数据--
            $date = date('Y-m-d');
            $week_arr =['0'=>"日",'1'=>"一",'2'=>"二",'3'=>"三",'4'=>"四",'5'=>"五",'6'=>"六"];
            $week = "星期".$week_arr[date("w")];
            $result = ['shares'=>'网络异常,数据仅提供参考。','date'=>$date,'week'=>$week];
        }

		return $result;
	}

	/**
	 * [shares 获取股票接口数据]
	 * @param [str] $[gid] 股票编号，上海股市以sh开头，深圳股市以sz开头如：sh601009
	 * @param [str] $[key] 	APP Key
	 * @param str $[type] 	0代表上证指数，1代表深证指数
	 * @return [type] 
	 */
	static public function shares(){
		$key = '13a4ae5b349905a33edd55b4a3c56ca6';
		$gid = '';
		$type = ['0'=>0,'1'=>1];
		$url = 'http://web.juhe.cn:8080/finance/stock/hs?gid=%s&key=%s&type=%s';
		$send = new \app\lib\org\Send();

		for($i=0;$i<2;)
		{
			$tp = $type[$i];
			$_url =sprintf($url,$gid,$key,$tp);
			$result = $send->curl_request2($_url);
			if ($result['reason'] == 'SUCCESSED!') 
			{
				//数据处理与存储
				$data[$i] = $result['result'];
			}
			$i++;
		}
		
		if (!empty($data)) {
			//返回
			return $data;
		}else{
			return false;
		}
		
	}


	//当天股指存储
	static public function insert_shares($info = [])
	{
	  $nb = 0;
	  $result = [];
	  $date = date('Y-m-d');

	  foreach ($info as $key => $value) 
	  {

	  	$is = Db::connect('db_config4')->query("select * from shares where name= '".$value['name']."' and date_format(time,'%Y-%m-%d')='".$date."' ");

  	    if ($is) 
        {
        	$result = db("shares","db_config4")->where(" name= '".$value['name']."' and date_format(time,'%Y-%m-%d')='".$date."'")->update($value);
        }else
        {
        	$result = db("shares","db_config4")->insert($value);
        }
	  }
     
      
	}
}
?>