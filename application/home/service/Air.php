<?php
namespace app\home\service;
ini_set("error_reporting","E_ALL & ~E_NOTICE");
class Air 
{
    private $language = '';
    function __construct()
    {
  
        $language = cookie('language');
        if (!$language) 
        {
            $_COOKIE['language'] = 'zh_cn';
        }
        $this->language = cookie('language');

    }

    /**
     * [airinfo 航班接口获取数据]
     * @param  array（） $data []
     * @return [航班数据,航班分页数据,态势图数据]
     */
    public function airinfo($data = null,$arrays=null)
    {
    	$result= $this->air_info($data);
    	$info  = arr_fil($result['result']);
      $pages = isset($result['total']) ? $result['total'] :'';
      $isFromSha = isset($pages['isFromSha']) ? $pages['isFromSha'] :''; 
      /* 态势图数据 */
      $late = $this->get_round($arrays);
      if(empty($late['result'])){ $late['result'] = $info;}
      //$late['result']
      $arr = $this->hand_data($late['result'],$isFromSha);
    	return ['info'=>$info,'pages'=>$pages,'arr'=>$arr];
	}

    /* 获取航班分页数据 */
    function airmore($data,$arrays)
    {
        
        //航班总数据
        $result = $this->air_info($data);
        $pages = $result['total'];
        $isFromSha = $pages['isFromSha'];
        $info  = $result['result'];
        ksort($info);

        //$info  = arr_fil($result['result']);
        //态势图数据
        $late = $this->get_round($arrays);
         if(empty($late['result'])){ $late['result'] = $info;}
        $arr = $this->hand_data($late['result'],$isFromSha);
        //$arr = $this->hand_data($info,$isFromSha);
      
        return ['info'=>$info,'pages'=>$pages,'arr'=>$arr];
    }

    /**
     * [hand_data 态势图数据处理]
     * @param  [type] $info 总航班数据
     * @return array（json_info 态势图航线数据，citys 态势图地址数据）
     */
    function hand_data($info,$isFromSha = '')
    {
        $citys = [];
        if (!$info) {
            return;
        }
        $now = strtotime(date("H:i",time()));
        $end = strtotime(date("H:i",time()+3600));


        foreach ($info as $key => $value) 
        {
          if(isset($value['ADDR'])&&$value['ADDR'] !='国内'){ continue;}/*屏蔽国外,空态势数据*/

          if(empty($value['TOCITY'])||empty($value['FROMCITY'])){ continue;}
          
          if ($isFromSha == '1') 
          {
            if (strtotime($value['STARTTIME'])<$now || strtotime($value['STARTTIME'])>$end) { continue;}
          }else{
            if (strtotime($value['ENDTIME'])>$end || strtotime($value['ENDTIME'])<$now) { continue;}
          }

          //城市
          $all_citys[$key] = [$value['FROMCITY'],$value['TOCITY']];

          $status = '';
          switch ($value['REMARK_XML']) 
          {
            case '正常':
                $status = '30';
                continue;
              break;
            case 'Normal':
                $status = '30';
                continue;
              break;
            case '延误':
                $status = '60';
              break;
            case 'Delayed':
                $status = '60';
              break;
            case '取消':
                $status = '90';
              break;
            case 'Cancel':
                $status = '90';
              break;              
            default:
                $status = '90';
              break;
          }
          //航线
          if($status){ $json_info[] = ['FROMCITY'=>$value['FROMCITY'], 'TOCITY'=>$value['TOCITY'],'value'=>$status];}
          //城市
          //$all_citys[$key] = [$value['FROMCITY'],$value['TOCITY']];
          
          
        }
        //城市
        $citys = arr_unique($all_citys);

        // var_dump($info);var_dump($json_info);die;
        return ['json_info'=>$json_info,'citys'=>$citys];
    }



    /**
     * [air_list 航班列表数据]
     * @return [type] [description]
     */
    function air_list($data){
        $result= $this->air_info($data);
        $pages = $result['total'];
        $info  = $result['result'];
        return ['info'=>$info,'pages'=>$pages];
    }


    /**
     * [air_info 航班接口]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function air_info($params)
    {
        $Send = new \app\lib\org\Send();
        $url = 'http://zhihuijingang.com/cordis/public/index.php/api/air/getAirInfo';
        $param = json_encode($params,true);
        $result = $Send->curl_request($url,$param);
        return $result;
    }

    /* 获取走马灯数据 */
    function get_round($params)
    {
        $Send = new \app\lib\org\Send();
        $url = 'http://zhihuijingang.com/cordis/public/index.php/api/air/round_info';
        $param = json_encode($params,true);
        $result = $Send->curl_request($url,$param);
        $result = $result['result'];
        if(count($result)==1&&$result !='')
        {

          foreach ($result as $key => $value) 
          {
            $result[$key+1] = $value;
            $result[$key+2] = $value;
          }
        }
        return $result;
    }
  
    /**
     * [local_info 读取本地数据]
     * @param  [type] $data [源数据]
     * @return [type]
     */
    function local_info($param)
    {
      $numb = isset($param['numb'])?$param['numb']:12;
      $isFromSha = isset($param['isFromSha'])?$param['isFromSha']:'1';
      $language = isset($param['language'])?$param['language']:'zh_cn';
      $pages = isset($param['pages'])?$param['pages']:1;
      $start = ($pages-1)*$numb;
      $end = $pages*$numb;

      $info = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/Demo/public/data.json');
      $info = json_decode($info,JSON_UNESCAPED_UNICODE);

      $air_info = $info['airlist'][$isFromSha];
      //$count = count($air_info);

      $res = [];
      $j =0;

      $time = strtotime(date('H:i'));
      $endtime = strtotime(date('H:i',time()+3600*3));
      //筛选3小时数据--出发
      foreach ($air_info as $key => $value) 
      {
         $sta = strtotime($value['STARTTIME']);
        if ($sta>$time && $sta<$endtime) 
          {
             $res[$j] = $value;
             $j++;
          }else{
            continue;
          }
      }

      //筛选条数（页数）
      $count = count($res);
      $k = 0;
      for ($i =0; $i < $count; $i++) 
      {    
        if ($i>=$start && $i<$end) 
        {
          $result[$k] = $res[$i];
          $k++;
        }else
        {
          continue;
        }
      }
       /* 态势图数据 */
      $arr = $this->hand_data($result,$isFromSha);

      $total = ['current_Page' => $pages,'count' => $count,'isFromSha' => $isFromSha,'count_Page' => intval($count/$numb),'language' => $language];

      return ['info'=>$result,'pages'=>$total,'arr'=>$arr];
    }
}
