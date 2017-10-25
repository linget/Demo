<?php
namespace app\test\service;
class Train 
{
	private  $ak;
    private	 $header;
    private  $send;

    public function __construct(){
    	$this->ak = '667ACBF1D816537CB642BD5A546A7A7B';
    	$this->header = array("apikey:".$this->ak);
    	$this->send = new \app\lib\org\Send();
    }

    //获取航班统计数据
  public function search_info($param = null){
    if ($param) 
    {
      $param = json_encode($param,JSON_UNESCAPED_UNICODE);
    }
    $send = new \app\lib\org\Send();
    $ak = '667ACBF1D816537CB642BD5A546A7A7B';
    $header = array("apikey:$ak");
    $url = "http://10.210.11.11/hp/public/index.php/Api/Train/Statis";
    $result = $this->send->curl_request2($url,$param,$this->header);
    // var_dump($result['result']);die;
     return self::pares($result['result']);
  }

  //统计数据处理
  static public function pares($arr)
  {
  	$json_info = [
  	'0'=>['name'=>'出发车次','value'=>'0'],
  	'1'=>['name'=>'到达车次','value'=>'0'],
  	'2'=>['name'=>'早点车次数量','value'=>'0'],
  	'3'=>['name'=>'晚点车次数量','value'=>'0'],
  	
  	];//统计图

  	foreach ($arr as $key => $value) 
  	{
  		if(!isset($value['REMARK'])){ continue;}
  		switch ($value['REMARK']) 
  		{
  			case '早点':
  				$json_info['2']['value'] += $value['COUNTS'];
  				break;
  			case '晚点':
  				$json_info['3']['value'] += $value['COUNTS'];
  				break;
  			case '正点':
  				if ($value['STARTSTATION'] == 'AOH') { 
  					$json_info['0']['value'] += $value['COUNTS'];
  				}elseif ($value['TERMINALSTATION'] == 'AOH') {
  					$json_info['1']['value'] += $value['COUNTS'];
  				}
  				break;
  			default:
  				continue;
  				break;
  		}
  	}
  	ksort($json_info);
  	$json = array_values($json_info);//js统计图数据
  	return ['info'=>$json_info,'json'=>$json];

  }

  //高铁数据获取
  public function getInfo($param = ''){
    if ($param) {
      $param = json_encode($param,JSON_UNESCAPED_UNICODE);
    }
    $url = 'http://10.210.11.11/hp/public/index.php/api/train/Statisdata';
    $result = $this->send->curl_request2($url,$param,$this->header);

    return self::paresinfo($result['result']);
  }

  //高铁数据处理
  public function paresinfo($arr){
     if(empty($arr)){ return ['info'=>'','topcity'=>''];}

    $new = [];$city = ['0'=>[],'1'=>[]];
    $default = ['name'=>"上海虹桥",'value'=>70,'REMARK_XML'=>"正点"];

    //到发数据分离
    foreach ($arr as $key => $value) 
    {
      $number = 150;//延误程度
      if (!isset($value['FROMCITY'])||!isset($value['TOCITY'])||!isset($value['FLIGHT_NO'])||!isset($value['REMARK'])) {
        continue;
      }
      if ($value['REMARK'] == '晚点') { $number = 300;}

      if (strpos('G', $value['FLIGHT_NO'])) 
      {
         $value['ICON'] = '/test/img/speedrail.png';
      }else{
         $value['ICON'] = '/test/img/motorcar.png';
      }


      if ($value['FROMCITY'] == '上海虹桥') 
      {
          //出发
          $value['CITY'] = $value['TOCITY'];
          $new['1'][] = $value;
          $city['1'][] = ['name'=>$value['TOCITY'],'value'=>$number,'REMARK_XML'=>$value['REMARK']];
      }elseif ($value['TOCITY'] == '上海虹桥') {
        //到达
        $value['CITY'] = $value['FROMCITY'];   
        $new['0'][] = $value;
        $city['0'][] = ['name'=>$value['FROMCITY'],'value'=>$number,'REMARK_XML'=>$value['REMARK']];
      }
      
    }

    if (empty($city['1'])) {
      $city['1']['0'] = $default;
    }elseif (empty($city['0'])) {
      $city['0']['0'] = $default;
    }

    //态势数据筛选
    $topcity['1'] = topcity($city['1']);
    $topcity['0'] = topcity($city['0']);
    return ['info'=>$new,'topcity'=>$topcity];
  }
}
