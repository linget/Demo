<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Train extends Model
{
    public function getTrainList($fromCity=null,$toCity=null,$trainNo=null,$tDate=null)
    {
        $cont =  $this->train_info($fromCity, $toCity, $trainNo, $tDate);
        $res=$cont['result'];
        if(empty($res)){
            return '';
        }
        foreach ($res as &$record) { 
            if($record["FROMCITY"]=="上海虹桥"){
                    if(strcmp($record["RUN_DATE"],date("Y-m-d",time()))<0 OR strcmp($record["STARTTIME"],date("H:i",time()))<0){
                        $record["REMARK"]="已出发";
                        }
            }else{    
                if(strcmp($record["RUN_DATE"],date("Y-m-d",time()))<0 OR strcmp($record["ENDTIME"],date("H:i",time()))<0){
                        $record["REMARK"]="已到达";   
                }
            }
        }
        return $res;
    }
    //大屏定制，信息存储
    public function setScreen($trainOn, $fDate,$leave_address,$arrive_address, $openid = null, $business_id = null)
    {
        $data=[
          'openid'=>$openid,
          'kind_id'=>'1',
          'code_name'=>$trainOn,
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
    public function setMessage($phone, $trainOn, $fDate,$leave_address,$arrive_address,  $openid = null, $business_id = null)
    {
        $data=[
          'openid'=>$openid,
          'phone'=>$phone,
          'kind_id'=>'1',
          'code_name'=>$trainOn,
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
    public function setWechat($trainOn, $fDate,$leave_address,$arrive_address,  $openid = null, $follow = 0, $business_id = null)
    {
        $data=[
          'openid'=>$openid,
          'follow'=>$follow,  
          'kind_id'=>'1',
          'code_name'=>$trainOn,
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
    //有大屏定制，更新大屏定制中间表状态
    public function middleScreen($business)
    {
        $res=db('yz_middle_screen','db_config2')->where('business',$business)->field('state')->find();
        if($res['state']==0){
            db('yz_middle_screen','db_config2')->where('business',$business)->setField('state', 1);
        }
    }
     /*
     * 高铁订阅表
     */
    public function subscribeTrain($trainOn, $fDate,$leave_address,$arrive_address)
    {
       $map['train_id']=$trainOn;
       $map['train_time']=$fDate;
       $fid=db('yz_subscribe_train','db_config2')->where($map)->find();
       if(!empty($fid)){
            return 1;
        }
       $arr= $this->getTrainList($leave_address,$arrive_address, $trainOn, $fDate);
       if(empty($arr)){
            return '';
       }
       $data=[
           'train_id'=>$trainOn,
           'train_time'=>$fDate,
           'leave_time'=>$arr[0]['STARTTIME'],
           'arrive_time'=>$arr[0]['ENDTIME'],
           'leave_address'=>$leave_address,
           'arrive_address'=>$arrive_address,
           'state'=>$arr[0]['REMARK'],
            'time'=>time(),
           'notice_time'=>time()
       ];
       $res=db('yz_subscribe_train','db_config2')->insert($data);
       return $res;
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
    public function train_info($fromCity=null,$toCity=null,$trainNo=null,$tDate=null)
    {
        $url = 'http://zhihuijingang.com/Demo/public/index.php/myapi/Train/getTrain';
        $apikey = '667ACBF1D816537CB642BD5A546A7A7B';
        $data = array(
            'fromCity' => $fromCity,   
            'toCity' => $toCity,    
            'trainNo' => $trainNo,
            'tDate' => $tDate  
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
}
