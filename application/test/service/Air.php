<?php
namespace app\test\service;
class Air 
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
  public function search_info($param= null){
    if ($param) 
    {
      $param = json_encode($param,JSON_UNESCAPED_UNICODE);
    }
    
    $url = "http://10.210.11.11/hp/public/index.php/Api/Air/Statis";
    $result = $this->send->curl_request2($url,$param,$this->header);


    return self::pares($result['result']);
  }

  //统计数据处理
  static public function pares($arr)
  {
  	$json_info = [
  	'0'=>['name'=>'离港班次','value'=>'0'],
  	'1'=>['name'=>'到港班次','value'=>'0'],
  	'2'=>['name'=>'延误航班数量','value'=>'0'],
  	'3'=>['name'=>'取消航班数量','value'=>'0']
  	];//统计图

    $new = [];
  	foreach ($arr as $key => $value) 
  	{
      $isFromSha = '';
  		if(!isset($value['REMARK'])||!$value['FROMCITY']||!$value['TOCITY']){ continue;}
      if ($value['FROMCITY']['zh_cn'] == '上海虹桥'||$value['FROMCITY']['en']=='Shanghai Hongqiao') {
        $isFromSha = '1';
      }elseif ($value['TOCITY']['zh_cn']=='上海虹桥'||$value['TOCITY']['en']=='Shanghai Hongqiao') {
        $isFromSha = '0';
      }
      
  		switch ($value['REMARK']) 
  		{
        //正常
  			case 'REG':
              if ($isFromSha =='1') 
              {
                $json_info['0']['value'] += $value['COUNTS'];
              }elseif ($isFromSha=='0') {
                $json_info['1']['value'] += $value['COUNTS'];
              }
  			
  				break;
        //延误  
  			case 'DFI':
  				$json_info['2']['value'] += $value['COUNTS'];
  				break;

  			case 'DEL':
  				$json_info['2']['value'] += $value['COUNTS'];
  				break;
        //取消 
  			case 'CAN':
  				$json_info['3']['value'] += $value['COUNTS'];
  				break;	
  			default:
  				continue;
  				break;
  		}
      $new[$isFromSha] = $json_info;
  	}
  	ksort($json_info);
  	$json = array_values($json_info);//js统计图数据

  	return ['info'=>$json_info,'json'=>$json];
  }

  //航班数据获取
  public function getInfo($param = ''){
  	if ($param) {
      $param = json_encode($param,JSON_UNESCAPED_UNICODE);
    }
  	$url = 'http://10.210.11.11/hp/public/index.php/api/air/Statisdata';
  	$result = $this->send->curl_request2($url,$param,$this->header);

    return self::paresinfo($result['result']);
  }

  //航班数据处理
  static public function paresinfo($arr = null)
  {
    if(empty($arr)){ return ['info'=>'','topcity'=>''];}

    $new = [];$city = ['0'=>[],'1'=>[]];
    $default = ['name'=>"上海虹桥",'value'=>70,'REMARK_XML'=>"正常"];

    //到发数据分离
    foreach ($arr as $key => $value) 
    {
      $number = 150;//延误程度
      if (!isset($value['FROMCITY'])||!isset($value['TOCITY'])||!isset($value['FLIGHT_NO'])||!isset($value['REMARK_XML'])) {
        continue;
      }
      if ($value['REMARK_XML'] == '取消'||$value['REMARK_XML'] == 'Cancel') {
         $number = 300;
      }


      $value['ICON'] = $value['flight_info']['0']['flight_logo'];
      if ($value['FROMCITY'] == '上海虹桥') 
      {
          //出发
          $value['CITY'] = $value['TOCITY'];         
          $new['1'][] = $value;
          $city['1'][] = ['name'=>$value['TOCITY'],'value'=>$number,'REMARK_XML'=>$value['REMARK_XML']];
      }
      if ($value['TOCITY'] == '上海虹桥') {
        //到达
        $value['CITY'] = $value['FROMCITY'];
        $new['0'][] = $value;
        $city['0'][] = ['name'=>$value['FROMCITY'],'value'=>$number,'REMARK_XML'=>$value['REMARK_XML']];
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
