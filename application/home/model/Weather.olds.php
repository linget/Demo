<?php
namespace app\home\model;
use think\Model;
use think\Db;
class Weather extends Model
{
    protected $tableName = "weather";
    protected $pk = 'id';
	/**
	 * 获取天气信息,无当天数据在保存一份
	 * @return array
	 * @author ryz <609873271@qq.com>
	 * @date 2015-02-01
	 * **/
	public function getWeatherInfo()
	{
        $where['date'] = date("Y-m-d",time());
	    $info = Db::name($this->tableName)->where($where)->find();

        
        if($info){
            return $info;exit;//本地有当天数据则返回
        }

        return $this->get_weather();
	}

    function get_weather(){
        $res = $this->weather_api();
        if($res)
        {
            /* 有 */
            $jo = json_decode($res, true);
            $date = date("Y-m-d",time());
            $code = $jo['HeWeather data service 3.0'][0]['daily_forecast'][0]['cond']['code_d'];
            $tmp_min = $jo['HeWeather data service 3.0'][0]['daily_forecast'][0]['tmp']['min'];
            $tmp_max = $jo['HeWeather data service 3.0'][0]['daily_forecast'][0]['tmp']['max'];
                 
            $info = Db::name('WeatherCode')->where(['code'=>$code])->find();
                //插入天气数据
            $data=[
                'max_tmp'=>$tmp_max,
                'min_tmp'=>$tmp_min,
                'en'=>$info["en"],
                'cn'=>$info["cn"],
                'icon'=>$info["icon"],
                'date'=>$date,
            ];

                Db::name($this->tableName)->insert($data);
                //天气数据
                $info["max_tmp"] = $tmp_max;
                $info["min_tmp"] = $tmp_min;
        } 
        else
        {
            /* 无,获取当月最近一天数据 */
            $date = date("Y-m",time());
            $where['date'] = array('like',$date."%");
            $month_info = Db::name($this->tableName)->where($where)->order('date desc')->limit('0,1')->select();
            $info = $month_info[0];

            /* 重新插入并返回 */
            $info['date'] = date('Y-m-d',time());
            $info['id'] = '';
            Db::name($this->tableName)->insert($info);
        }
        return $info;
    }

    /* 接口--获取天气数据 */
    function weather_api()
    {
        $ch = curl_init();
        $url = 'http://apis.baidu.com/heweather/weather/free?city=shanghai';
        $header = array(
            'apikey: 4d3599bea4f9d71d52145500cffb8803',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求

        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}