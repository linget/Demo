<?php
namespace app\home\service;
ini_set("error_reporting","E_ALL & ~E_NOTICE");
class Train 
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
    public function traininfo($data = null,$arrays = null)
    {
        $result = $this->train_info($data);
        if ($result) {
          $info  = $result['result'];
          $pages = $result['total'];
          $isFromSha = $pages['isFromSha']; 
          
          /* 态势图数据 */
          $late = $this->get_round($arrays);
          if(empty($late['result'])){ $late['result'] = $info;}

          $arr = $this->hand_data($late['result'],$isFromSha);
          /* 态势图数据 */
          //$arr = $this->hand_data($info,$isFromSha);
           

          foreach ($info as $key => $value) 
          {
            $value['TOCITY'] = isset($value['TOCITY'])?$value['TOCITY']:'';
            $value['FROMCITY'] = isset($value['FROMCITY'])?$value['FROMCITY']:'';
            if(!isset($value['TOCITY'])||!isset($value['FROMCITY']))
            {             
              unset($value);
              continue;
            }else{
              $info[$key] = $value;
            }
            
          }
          //$info  = arr_fil($info);
          return ['info'=>$info,'pages'=>$pages,'arr'=>$arr];
        }else{
            return false;
        }        
	}


    // 态势图数据处理
    function hand_data($info,$isFromSha = ''){
        if (!is_array($info)) 
        {
           return false;
        }
        foreach ($info as $key => $value) 
        {
            $status = '';
            $value['TOCITY'] = isset($value['TOCITY'])?$value['TOCITY']:'';
            $value['FROMCITY'] = isset($value['FROMCITY'])?$value['FROMCITY']:'';
            if(empty($value['TOCITY'])||empty($value['FROMCITY']))
            {             
              continue;
            }

            
            $all_citys[] = [$value['FROMCITY'],$value['TOCITY']];
              
              if ($value['REMARK'] == '正点'||$value['REMARK'] == '正常'||$value['REMARK'] == 'on time'||$value['REMARK'] == 'early'||$value['REMARK'] == ' 早点') {
                $status = '30';
              }else{
                //早点或正点
                $status = '60';
              }

              //航线数据
              $json_info[] = ['FROMCITY'=>$value['FROMCITY'], 'TOCITY'=>$value['TOCITY'],'value'=>$status];
        }

        $citys = arr_unique($all_citys);
       
        return ['json_info'=>$json_info,'citys'=>$citys];
    }



    /**
     * [train_info 高铁数据接口]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    function train_info($params)
    {
        $Send = new \app\lib\org\Send();
        $url = 'http://zhihuijingang.com/cordis/public/index.php/api/Train/getTrainInfo';
        $param = json_encode($params,true);
        $result = $Send ->curl_request($url,$param);

        return $result['result'];
    }

    /* 获取走马灯数据 */
    function get_round($params)
    {
        $Send = new \app\lib\org\Send();
        $url = 'http://zhihuijingang.com/cordis/public/index.php/api/Train/round_train';
        $param = json_encode($params,true);
        $result = $Send ->curl_request($url,$param);

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
     * [air_list 列车列表数据]
     * @return [type] [description]
     */
    function Train_list($data){
        $result= $this->train_info($data);
        $pages = $result['total'];
        $info  = $result['result'];
   
        return ['info'=>$info,'pages'=>$pages];
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

      $air_info = $info['trainlist'][$isFromSha];
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
