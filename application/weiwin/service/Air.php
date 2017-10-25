<?php
namespace app\home\service;

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
    public function airinfo($data = null)
    {
    	$result= $this->air_info($data);
    	$info  = $result['result'];
      $pages = isset($result['total']) ? $result['total'] :'';
      $isFromSha = isset($pages['isFromSha']) ? $pages['isFromSha'] :''; 
     
      /* 态势图数据 */
      $arr = $this->hand_data($info,$isFromSha);

    	return ['info'=>$info,'pages'=>$pages,'arr'=>$arr];
	}

    /* 获取航班分页数据 */
    function airmore($data)
    {
        
        //航班总数据
        $result = $this->air_info($data);
        $pages = $result['total'];
        $isFromSha = $pages['isFromSha'];
        
        $info  = $result['result'];
        //态势图数据
        $arr = $this->hand_data($info,$isFromSha);
      
        return ['info'=>$info,'pages'=>$pages,'arr'=>$arr];
    }

    /**
     * [hand_data 态势图数据处理]
     * @param  [type] $info 总航班数据
     * @return array（json_info 态势图航线数据，citys 态势图地址数据）
     */
    function hand_data($info,$isFromSha = '')
    {
        $status ='';
        foreach ($info as $key => $value) 
        {
          if($value['ADDR'] !='国内'||empty($value['TOCITY'])||empty($value['FROMCITY'])){ continue;}/*屏蔽国外,空态势数据*/

          $all_citys[] = [$value['FROMCITY'],$value['TOCITY']];
          switch ($value['REMARK_XML']) 
          {
            case '正常':
                $status = '30';
              break;
            case 'Normal':
                $status = '30';
              break;
              case '延误':
                $status = '60';
              break;
              case 'Delayed':
                $status = '60';
              break;            
            default:
                $status = '90';
              break;
          }

          //航线
          $json_info[] = ['FROMCITY'=>$value['FROMCITY'], 'TOCITY'=>$value['TOCITY'],'value'=>$status];
        }
        //城市
        $citys = arr_unique($all_citys);

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
        $url = 'http://10.210.11.11/hp/public/index.php/Api/Air/getAirInfo';
        $param = json_encode($params,true);
        $result = $Send->curl_request($url,$param);

        return $result;
    }

    /* 获取走马灯数据 */
    function get_round($params)
    {
        $Send = new \app\lib\org\Send();
        $url = 'http://10.210.11.11/hp/public/index.php/Api/Air/round_info';
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
     * [hand_scatterdata 散点图态势数据处理]
     * @param  [type] $data [源数据]
     * @return [type]
     */
    function hand_scatterdata($data){
      //按地址统计，排序
      
    }
}
