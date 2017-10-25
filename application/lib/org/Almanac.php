<?php
namespace app\lib\org;
use think\Db;
/**
* 第三黄黄历查询类
* @auth zjl 
* 2017-10-13
*/
class Almanac
{

	/**
	 * [index 获取万年历]
	 * @return [type] [description]
	 */
	public function index(){
		$date = date('Y-m-d');//当天
        //数据库获取当天数据
        $sql = "select * from almanac where calendar='".$date."' limit 1";
        $result = Db::connect('db_config4')->query($sql);
        if ($result) { return $result[0];}//有数据直接返回
        
        $line = new \app\lib\org\Online();
        //无数据-判断网络
        if($line->internet())
        {
            //有外网没有数据--
            $res = self::almanac_juhe();

            if (!empty($res)) 
            {
               self::insert_almanac($res);
               $result = Db::connect('db_config4')->query($sql);
            }

        }else
        {
            //无网无数据--
            $date = date('Y-m-d');
            $week_arr =['0'=>"日",'1'=>"一",'2'=>"二",'3'=>"三",'4'=>"四",'5'=>"五",'6'=>"六"];
            $week = "星期".$week_arr[date("w",strtotime($v['date']))];
            $result = ['almanac'=>'网络异常,数据仅提供参考。','date'=>$date,'week'=>$week];
        }
		return $result;
	}

	/**
	 * [perpetual_almanac 万年历接口--聚合数据]
	 * @return [type] [description]
	 */
	static public function almanac_juhe()
	{
		$url = 'http://v.juhe.cn/calendar/day?date=%s&key=%s';
		$date = date('Y-n-j');
		$key = 'd875d30625418e5238a7b904dc1c55c4';
		$_url = sprintf($url,$date,$key);
		$send = new \app\lib\org\Send();
		$result = $send->curl_request2($_url);
		if ($result['reason'] == 'Success') 
		{
			//数据处理与存储
			$res = $result['result']['data'];
			$week_arr = ['0'=>"日",'1'=>"一",'2'=>"二",'3'=>"三",'4'=>"四",'5'=>"五",'6'=>"六"];
			$week = "星期".$week_arr[date("w",strtotime($res['date']))];
			$data = ['calendar'=>$res['date'],'lunar_calendar'=>$res['lunarYear'].$res['lunar'],'weekday'=>$week,'suit'=>$res['suit'],'avoid'=>$res['avoid']];

			//返回
			return $data;
		}
		return false;
	}

	//当天黄历存储
	static public function insert_almanac($info = [])
	{
	  $nb = 0;
	  $result = [];
	  $date = date('Y-m-d');
      $is = Db::connect('db_config4')->query("select * from almanac where calendar='".$date."' ");
      if ($is) 
      {
          $result = db("almanac","db_config4")->where("calendar='".$date."'")->update($info);
          continue;
      }else
      {
        $result = db("almanac","db_config4")->insert($info);
      }
	}


}
?>