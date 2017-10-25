<?php
namespace app\lib\org;
use think\Db;
class Weather
{

    public function index()
    {
        $date = date('Y-m-d');//当天
        //数据库获取当天数据
        $sql = "select w.*,c.icon,c.code from weather as w left join weather_code as c on w.img_id=c.id where w.date='".$date."'  limit 1";
        $result = Db::connect('db_config4')->query($sql);

        if ($result) { return $result[0];}//有数据直接返回
        //无数据-判断网络
        if(self::internet())
        {
            //有外网没有数据--天气接口--ali
            $res = self::weather_ali();

            if (!empty($res[0])) 
            {
               self::insert_weather($res);
               $result = Db::connect('db_config4')->query($sql);
            }else
            {
                //nowapi接口
                $res = self::weather_nowapi();
                if ($res) 
                {
                    self::insert_weather($res);
                    $result = Db::connect('db_config4')->query($sql);
                }
            }

        }
        
        if (!$result) 
        {
            //无网无数据--显示天气异常，数据仅参考
            $date = date('Y-m-d');
            $week_arr =['0'=>"日",'1'=>"一",'2'=>"二",'3'=>"三",'4'=>"四",'5'=>"五",'6'=>"六"];
            $week = "星期".$week_arr[date("w",strtotime($v['date']))];
            $result = ['weather'=>'网络异常,数据仅提供参考。','date'=>$date,'week'=>$week];
        }
          return isset($result[0])?$result[0]:$result;
    }


   //阿里云api集市--天气
  static public function weather_ali()
  {
    //{"cityid":"24","parentid":"0","citycode":"101020100","city":"上海"}
    $host = "http://jisutqybmf.market.alicloudapi.com";
    $path = "/weather/query";
    $method = "GET";
    $appcode = "3d12c9771adb4d138d1dc09170467492";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "cityid=24";
    $url = $host . $path . "?" . $querys;

    $result = self::curl_request($url,$data="",$headers,$method);
    if ($result['msg'] != 'ok') { return false;}
    $date = date('Y-m-d H:i:s');                                       
    $info = $result['result'];
    //数据处理
    for ($i=0; $i < 7; $i++) 
    { 
       if ($i == 0) 
       {
           //当天详细信息
            $data[$i] = [
            'date' => $info['date'],
            'week' => $info['week'],
            'tmp_max' => $info['temphigh'],
            'tmp_min' => $info['templow'],
            'weather' => $info['weather'],
            'img_id' => $info['img'],//图标id
            'windspeed' => $info['windspeed'],//风速
            'windrect' => $info['winddirect'],//风向
            'windpower' => $info['windpower'],//风力
            'pm2_5' => $info['aqi']['ipm2_5'],//pm2.5
            'quality' => $info['aqi']['quality'],//空气质量
            'humidity' => $info['humidity'],//湿度
            'pressure' => $info['pressure'],//气压
            'others' => $info['aqi']['aqiinfo']['affect'],//空气质量-备注
            'addtime' => $date
            ];
      }else{
          //其余6天简易信息
            $data[$i] = [
            'date' => $info['daily'][$i]['date'],
            'week' => $info['daily'][$i]['week'],
            'tmp_max' => $info['daily'][$i]['day']['temphigh'],
            'tmp_min' => $info['daily'][$i]['night']['templow'],
            'weather' => $info['daily'][$i]['day']['weather'],
            'img_id' => $info['daily'][$i]['day']['img'],//图标id
            'windrect' => $info['daily'][$i]['day']['winddirect'],//风向
            'windpower' => $info['daily'][$i]['day']['windpower'],//风力
            'addtime' => $date
            ];
      }
    }

    return $data;
    
  }

  //nowapi--天气
  static public function weather_nowapi()
  {
    $app = 'weather.future';
    $weaid = 36;//上海

    $appkey = '28410';
    $sign = '12d21171cfba5c79298a07084bcd1c18';
    $_url = 'http://api.k780.com/?app=%s&weaid=%u&appkey=%s&sign=%s&format=json';//获取7天天气--详细，温度风力湿度
     $url = sprintf($_url,$app,$weaid,$appkey,$sign);
    $result = self::curl_request($url);
    if (!$result['success']) { return false;}
    $data = [];
    $date = date('Y-m-d H:i:s');
    
    //数据处理
    foreach ($result['result'] as $k => $v) 
     {
        $data[$k] = [
          'date' => $result['result'][$k]['days'],
          'week' => $v['week'],
          'tmp_max' => $v['temp_high'],
          'tmp_min' => $v['temp_low'],
          'weather' => $v['weather'],
          'img_id' => intval($v['weaid'])-1,
          'icon' => $v['weather_icon'],
          'windrect' => $v['wind'],//风向
          'windpower' => $v['winp'],//风力
          'addtime' => $date
          ];
     }

    return $data;
  }

  /**
   * [curl请求支持http~https]
   * @param  [type] $url  地址
   * @param  string $data 数据
   * @param  http $type 类型 http/https
   * @return array()       
   */
  static public function curl_request($url,$data = "",$headers = null,$method = null){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_TIMEOUT, 500);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//最大请求时间

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FAILONERROR, false);//忽略错误http400状态以下

      if ($method) {
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      }
      if ($headers) {
        //curl_setopt($ch, CURLOPT_HEADER, true);//查看返回http头部信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      }

      if(1 == strpos("$".$url, "https://")) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      }

      if (!$data) {
        $output = curl_exec($ch);
        if (!$output) {
            $output = file_get_contents($url);
          }      
      }else{
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
      }

    return json_decode($output,true);
  }

  //aliyun数据-写入数据库
  static public function insert_weather($info = [])
  {
    $nb = 0;
    $result = [];
    $date = date('Y-m-d H:i:s');

    foreach ($info as $key => $value) 
    {
      $is = Db::connect('db_config4')->query("select * from weather where date='".$value['date']."' ");
      if ($is) 
      {
          $result = db("weather","db_config4")->where("date='".$value['date']."'")->update($value);
          continue;
      }else
      {
        $result = db("weather","db_config4")->insert($value);
      }
      
    }

  }

  //判断是否可访问外网
  static public function internet()
  {
    $url = "http://www.baidu.com/";
    //$fp = @fopen($url,'r');
    $fp = get_headers($url);
    if (preg_match('/200/',$fp[0]))  
    { 
      return true;
    }else{
      return false;  
    }
  }
}