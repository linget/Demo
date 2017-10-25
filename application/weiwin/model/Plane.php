<?php
namespace app\weiwin\model;
use think\Model;
use think\Db;
class Plane extends Model 
{
    //航班信息
    public function getFlightInfo($fromCity=null,$toCity=null,$flightNo=null,$fDate=null)
    {
        $cont=  $this->air_info($fromCity, $toCity, $flightNo, $fDate);
        $res=$cont['result'];
         if(empty($res)){
            return '';
        }
        foreach ($res as &$record) { 
            $record["TOCITY"]=$this->getCNFromXML($record["TOCITY"]);
            $record["FROMCITY"]=$this->getCNFromXML($record["FROMCITY"]);
            $record["AIRLINE_IATA_EN"]=$this->getENFromXML($record["AIRLINE_IATA"]);
            $record["AIRLINE_IATA"]=$this->getCNFromXML($record["AIRLINE_IATA"]);
            $record["REMARK_XML"]=$this->getCNFromXML($record["REMARK_XML"]);
            if(is_null($record["ATD"])){
                $record["ATD"]=$record["ETD"];
            }
            if(is_null($record["ATA"])){
                $record["ATA"]=$record["ETA"];
            }
        }
        if(!is_null($flightNo) && "" != trim($flightNo) && $flightNo != "null"){
            $result=  $this->splitCodeOn($res, $flightNo);
        }else{
            $content=  $this->splitCode($res);
            $result=$this->getUique($content);
        } 
         return $result;
    }

        //大屏定制，当天定制用户
    public function getScreen($strmac = null)
    {
        if (empty($strmac)) {
          //获取3小时内定制
          $sql = "SELECT * FROM (SELECT * FROM yz_screen where code_time = '".date('Y-m-d')."' order by id  desc )F group by openid order by id desc";
        }else{
          //获取wify中存在mac地址定制
          $sql = "SELECT * FROM (SELECT * FROM yz_screen where code_time = '".date('Y-m-d')."' or mac in('".$strmac."') order by id  desc )F group by openid order by id desc";
        }
        $res = db('','db_config2')->query($sql);
        return $res;
    }

    //大屏定制，信息存储
    public function setScreen($flightOn, $fDate,$leave_address,$arrive_address, $openid = null, $business_id = null)
    {
      $fDate = date('Y-m-d',strtotime($fDate));
        $data=[
          'openid'=>$openid,
          'kind_id'=>'2',
          'code_name'=>$flightOn,
          'code_time'=>$fDate,
          'leave_address'=>$leave_address,
          'arrive_address'=>$arrive_address,
          'business_id'=>$business_id,
          'state'=>'1'
        ];
        
        $res=db('yz_screen','db_config2')->insert($data);
        return $res;
    }
    //短信定制，信息存储
    public function setMessage($phone, $flightOn, $fDate,$leave_address,$arrive_address, $openid = null, $business_id = null)
    {
        $data=[
          'openid'=>$openid,
          'phone'=>$phone,
          'kind_id'=>'2',
          'code_name'=>$flightOn,
          'code_time'=>$fDate,
          'leave_address'=>$leave_address,
          'arrive_address'=>$arrive_address,
          'business_id'=>$business_id,
          'state'=>'1'
        ];
        $res=db('yz_message_users','db_config2')->insert($data);
        return $res;
    }
    //微信定制，存储信息
    public function setWechat($flightOn, $fDate,$leave_address,$arrive_address, $openid = null, $follow = 0, $business_id = null)
    {
        $fDate = date('Y-m-d',strtotime($fDate));
        $data=[
          'openid'=>$openid,
          'follow'=>$follow,  
          'kind_id'=>'2',
          'code_name'=>$flightOn,
          'code_time'=>$fDate,
          'leave_address'=>$leave_address,
          'arrive_address'=>$arrive_address,
          'business_id'=>$business_id,
          'state'=>'1'
        ];
        $res=db('yz_wechat_users','db_config2')->insert($data);
        return $res;
    }
    /*
     * 判断是否关注
     * @ return 返回关注状态
     */
    public function checkFollow($openid)
    {
        $res=db('yz_follow','db_config2')->where('openid',$openid)->field('state')->find();
        $follow=$res['state']?$res['state']:0;
        return $follow;
    }
    /*
     * 飞机订阅表
     */
    public function subscribePlane($flightOn, $fDate)
    {
        $map['plane_id']=$flightOn;
        $map['plane_time']=$fDate;
        $fid=db('yz_subscribe_plane','db_config2')->where($map)->find();
        if(!empty($fid)){
            return 1;
        }
       $arr= $this->getFlightInfo('', '', $flightOn, $fDate);
       if(empty($arr)){
            return '';
       }
       $data=[
           'logo'=>$arr[0]['AIRLINE_IATA_EN'],
           'plane_id'=>$flightOn,
           'plane_time'=>$fDate,
           'plane_name'=>$arr[0]['AIRLINE_IATA'],
           'leave_time'=>$arr[0]['STARTTIME'],
           'arrive_time'=>$arr[0]['ENDTIME'],
           'leave_address'=>$arr[0]['FROMCITY'],
           'arrive_address'=>$arr[0]['TOCITY'],
           'state'=>$arr[0]['REMARK_XML'],
            'time'=>time(),
           'notice_time'=>time()
       ];
       $res=db('yz_subscribe_plane','db_config2')->insert($data);
       return $res;
    }
    //有大屏定制，更新大屏定制中间表状态
    public function middleScreen($business)
    {
        $res=db('yz_middle_screen','db_config2')->where('business',$business)->field('state')->find();
        if($res['state']==0){
            db('yz_middle_screen','db_config2')->where('business',$business)->setField('state', 1);
        }
    }

    public function curl_post($url,$apikey,$data)
    {
        $ch=curl_init();
        $header=array("apikey:$apikey");
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res=  curl_exec($ch);
  //      var_dump($res);die;
        curl_close($ch);
        return $res;
    }
    /*
     * 根据航班查询条件，获取航班基本信息 
     * @ $fromCity 出发城市
     * @ $toCity 到达城市
     * @ $flightNo 航班号
     * @ $fDate 出发时间 格式2016-12-26
     * @ return Json 格式
     */
    public function air_info($fromCity,$toCity,$flightNo,$fDate)
    {
        $url = 'http://'.$_SERVER['SERVER_NAME'].'/Demo/public/index.php/index/Air/getFlight';
        $apikey = '667ACBF1D816537CB642BD5A546A7A7B';
        $data = array(
            'fromCity' => $fromCity,   
            'toCity' => $toCity,    
            'flightNo' => $flightNo,
            'fDate' => $fDate  
            );
        $data = json_encode($data,true);
        $json = $this->curl_post($url,$apikey,$data);
        $content = json_decode($json,true);
        return   $content;
    }
    /*
     * 解析XML，得到中文
     */
    private function getCNFromXML($strXML){
         if(!empty($strXML))
           {
             $xmlp=  xml_parser_create();
             xml_parse_into_struct($xmlp,$strXML,$arr);
             xml_parser_free($xmlp);
             return $arr[1]["value"];
           }
         return null;
    }
    /*
     * 解析xml，得到英文
     */
     private function getENFromXML($strXML){
         if(!empty($strXML))
            {
                $xmlp=  xml_parser_create();
                xml_parse_into_struct($xmlp,$strXML,$arr);
                xml_parser_free($xmlp);
                return $arr[2]["value"];
            }
         return null;
    }
    /*
     * 拆分共享航班
     */
    private function splitCode($obj)
    {
        $arr =array();
        $num=0;
        foreach ($obj as $vals)
        {
            $arr[$num]['FLIGHT_NO']=$vals['FLIGHT_NO'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN']; 
            if($vals['CODE_SHARE1']){
                $num++;
                 $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE1'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            if($vals['CODE_SHARE2']){
                $num++;
                $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE2'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            if($vals['CODE_SHARE3']){
                $num++;
                 $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE3'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            if($vals['CODE_SHARE4']){
                $num++;
                 $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE4'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            if($vals['CODE_SHARE5']){
                $num++;
                 $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE5'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            if($vals['CODE_SHARE6']){
                $num++;
                 $arr[$num]['FLIGHT_NO']=$vals['CODE_SHARE6'];
            $arr[$num]['ORIGIN_AIRPORT_IATA']=$vals['ORIGIN_AIRPORT_IATA'];
            $arr[$num]['FROMCITY']=$vals['FROMCITY'];
            $arr[$num]['AIRLINE_IATA']=$vals['AIRLINE_IATA'];
            $arr[$num]['ATD']=$vals['ATD'];
            $arr[$num]['STD']=$vals['STD'];
            $arr[$num]['STA']=$vals['STA'];
            $arr[$num]['ATA']=$vals['ATA'];
            $arr[$num]['ETA']=$vals['ETA'];
            $arr[$num]['ETD']=$vals['ETD'];
            $arr[$num]['STARTTIME']=$vals['STARTTIME'];
            $arr[$num]['DEST_AIRPORT_IATA']=$vals['DEST_AIRPORT_IATA'];
            $arr[$num]['TOCITY']=$vals['TOCITY'];
            $arr[$num]['ENDTIME']=$vals['ENDTIME'];
            $arr[$num]['RECENT_ABNORMAL_STATUS']=$vals['RECENT_ABNORMAL_STATUS'];
            $arr[$num]['REMARK']=$vals['REMARK'];
            $arr[$num]['REMARK_XML']=$vals['REMARK_XML'];
            $arr[$num]['AIRLINE_IATA_EN']=$vals['AIRLINE_IATA_EN'];
            }
            $num++;
        }
        return $arr;
    }
    /*
     * 返回航班号查询一条记录
     */
    private function splitCodeOn($obj,$flightNo)
    {
        $arr =array();
        $arr[0]['FLIGHT_NO']=$flightNo;
        $arr[0]['ORIGIN_AIRPORT_IATA']=$obj[0]['ORIGIN_AIRPORT_IATA'];
        $arr[0]['FROMCITY']=$obj[0]['FROMCITY'];
        $arr[0]['AIRLINE_IATA']=$obj[0]['AIRLINE_IATA'];
        $arr[0]['ATD']=$obj[0]['ATD'];
        $arr[0]['STD']=$obj[0]['STD'];
        $arr[0]['STA']=$obj[0]['STA'];
        $arr[0]['ATA']=$obj[0]['ATA'];
        $arr[0]['ETA']=$obj[0]['ETA'];
        $arr[0]['ETD']=$obj[0]['ETD'];
        $arr[0]['STARTTIME']=$obj[0]['STARTTIME'];
        $arr[0]['DEST_AIRPORT_IATA']=$obj[0]['DEST_AIRPORT_IATA'];
        $arr[0]['TOCITY']=$obj[0]['TOCITY'];
        $arr[0]['ENDTIME']=$obj[0]['ENDTIME'];
        $arr[0]['RECENT_ABNORMAL_STATUS']=$obj[0]['RECENT_ABNORMAL_STATUS'];
        $arr[0]['REMARK']=$obj[0]['REMARK'];
        $arr[0]['REMARK_XML']=$obj[0]['REMARK_XML'];
        $arr[0]['AIRLINE_IATA_EN']=$obj[0]['AIRLINE_IATA_EN']; 
        return $arr;
    }
    /*
     * 去重复数据
     */
    private function getUique($arr)
    {
        $num=  count($arr);
        $new_arr=array();
        for($i=0;$i<$num;$i++)
        {
            $flag=0;
            for($j=$i+1;$j<$num;$j++)
            {
                if($arr[$i]['FLIGHT_NO']==$arr[$j]['FLIGHT_NO']){
                    $flag=1;
                    break;
                }
            }
            if($flag==0){
                array_push($new_arr, $arr[$i]);
            }
        }
        return $new_arr;
    }

    function insertmac($where,$data)
    {
      $res=db('yz_screen','db_config2')->where($where)->update($data);
      return $res;
    }
} 