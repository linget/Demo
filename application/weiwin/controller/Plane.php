<?php
namespace app\weiwin\controller;
use think\Log;
use think\Session;
class Plane extends Base
{
    public function index()
    {
        if(!session('openid')){
            $code=  input('get.code');
        if($code){
            $wechat=new \app\wechat\controller\Token();
            $openid=$wechat->getOpenid($code);
            session('openid',$openid);
        }
       }
       if(!session('business')){
            $bsn=  input('param.bsn');
            session('business',$bsn);
        }
        return $this->fetch();
    }
    public function planelist()
    {
        $fx=  input('param.fx');
        if($fx){
            $this->fxplane($fx);
            return $this->fetch('planelist');
        }  else {
        $fDate=input('post.new_date');
        if($fDate){

//            //分享接口参数配置
            $jsobj=new \app\wechat\controller\Token();
            $jspackage=$jsobj->getSignPackage();
            $obj=new \app\weiwin\model\Plane();
        $fromCity=input('post.plane_city_kai')?input('post.plane_city_kai'):'';
        $toCity=  input('post.plane_city_dao')?input('post.plane_city_dao'):'';
        $flightNo=input('post.plane_no')?input('post.plane_no'):'';
        $flag="0";
        if($flightNo) {
            $flag="1";
        }
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$flightNo.'_'.$fDate;
        session('traffic',$traffic);
        $info=$obj->getFlightInfo($fromCity, $toCity, $flightNo, $fDate);

        //第一条演示数据
        $ar = [[
          'FLIGHT_NO' =>  'MU9377',
          'ORIGIN_AIRPORT_IATA' =>  'SHA',
          'FROMCITY' =>  '上海虹桥',
          'AIRLINE_IATA' => '中国东方航空公司',
          'ATD' => '07:14',
          'STD' => '07:00',
          'STA' => '09:20',
          'ATA' => '09:06',
          'ETA' => '09:06',
          'ETD' => null,
          'STARTTIME' => '07:14',
          'DEST_AIRPORT_IATA' => 'PEK' ,
          'TOCITY' => '北京首都' ,
          'ENDTIME' => '09:06',
          'RECENT_ABNORMAL_STATUS' => 'DEP',
          'REMARK' => 'DFI',
          'REMARK_XML' => '延误' ,
          'AIRLINE_IATA_EN' => 'CHINA EASTEN']];

        $info = array_merge($ar,$info);
        $this->assign('jspackage',$jspackage);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('flightNo',$flightNo);
        $this->assign('fDate',$fDate);
        $this->assign('flag',$flag);
        $this->assign('info', $info);
        return $this->fetch();
        }  else {
            $this->prenextdate();
        }
        return $this->fetch();
      }
    }
    public function prenextdate()
    {
        $jsobj=new \app\wechat\controller\Token();
        $jspackage=$jsobj->getSignPackage();
        $this->assign('jspackage',$jspackage);
        $kind=input('param.fdate');
        $traffic_new=  session('traffic');
        $params=  explode('_', $traffic_new);
        $flag=isset($params[0])?$params[0]:'';
        $fromCity=isset($params[1])?$params[1]:'';
        $toCity=  isset($params[2])?$params[2]:'';
        $flightNo=isset($params[3])?$params[3]:'';
        $params[4] = isset($params[4])?$params[4]:'';
        if($kind==1){
            $fDate=strtotime($params[4])-86400;
        }  elseif($kind==2) {
            $fDate=strtotime($params[4])+86400;;
        } else {
            $fDate=strtotime($params[4]);
        }
        $fDate_new=date('Y-m-d',  $fDate);
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$flightNo.'_'.$fDate_new;
        session('traffic',$traffic);
        $obj=new \app\weiwin\model\Plane();
        $info=$obj->getFlightInfo($fromCity, $toCity, $flightNo, $fDate_new);

        //第一条演示数据
        $ar = [[
          'FLIGHT_NO' =>  'MU9377',
          'ORIGIN_AIRPORT_IATA' =>  'SHA',
          'FROMCITY' =>  '上海虹桥',
          'AIRLINE_IATA' => '中国东方航空公司',
          'ATD' => '07:14',
          'STD' => '07:00',
          'STA' => '09:20',
          'ATA' => '09:06',
          'ETA' => '09:06',
          'ETD' => null,
          'STARTTIME' => '07:14',
          'DEST_AIRPORT_IATA' => 'PEK' ,
          'TOCITY' => '北京首都' ,
          'ENDTIME' => '09:06',
          'RECENT_ABNORMAL_STATUS' => 'DEP',
          'REMARK' => 'DFI',
          'REMARK_XML' => '延误' ,
          'AIRLINE_IATA_EN' => 'CHINA EASTEN']];

        $info = array_merge($ar,$info);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('flightNo',$flightNo);
        $this->assign('fDate',$fDate_new);
        $this->assign('flag',$flag);
        $this->assign('info', $info);
        return $this->fetch('planelist');
    }
    //分享链接
    public function fxplane($fx)
    {
        $jsobj=new \app\wechat\controller\Token();
        $jspackage=$jsobj->getSignPackage();
        $this->assign('jspackage',$jspackage);
        $params=  explode('_', $fx);
        $flag=1;
        $flightNo=$params[0];
        $fDate=$params[1];
        $fromCity='';
        $toCity= '';
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$flightNo.'_'.$fDate;
        session('traffic',$traffic);
        $obj=new \app\weiwin\model\Plane();
        $info=$obj->getFlightInfo($fromCity, $toCity, $flightNo, $fDate);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('flightNo',$flightNo);
        $this->assign('fDate',$fDate);
        $this->assign('flag',$flag);
        $this->assign('info', $info);
        
    }

    public function message()
    {
        // $bsn=new \app\index\common\paramters();
        // $business_id= $bsn->getBusiness();
        $dp='';$wx='';$dx='';
        $business_id='1000000';            
        $openid=  session('openid');
        $dzkind=  cookie('dzkind');
        $planekey=  cookie('planekey');
        if(empty($dzkind) || empty($planekey)){
            return $this->fetch('message');
        }
        $kind= explode('_', $dzkind);
        $planeinfo=  explode('_', $planekey);
        $obj=new \app\weiwin\model\Plane();
        if($kind[0]==1){
           $dp=$obj->setScreen($planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3], $openid,$business_id) ;
        }
        if($kind[1]==1){
            $follow=$obj->checkFollow($openid);
            $wx=$obj->setWechat($planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3], $openid, $follow, $business_id);
            $this->assign('kind','wx'); 
        }
        if($kind[2]==1){
            $phone=  session('phone_yzm');
            $dx=$obj->setMessage($phone, $planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3], $openid, $business_id);
             $this->assign('kind','dx'); 
        }
        if(!$dp && !$wx && !$dx){
            return $this->fetch('messageerror');
        }else{
            //file_put_contents('./log.txt', print_r($planeinfo,true));
            $obj->subscribePlane($planeinfo[0], $planeinfo[1]);
            self::send_successmsg($planeinfo[0]);
        }
       return  $this->fetch('plane/message');
    }
    //定制失败页面
    public function messageerror()
    {
        return $this->fetch();
    }

    //发送验证码
    public function setyzm()
    {
        $phone=input('post.phone');
   //     $yzm=  rand(10000, 99999);
        $yzm='12121';
        session('yzm',$yzm).session_cache_expire(30);
        session('phone_yzm',$phone).session_cache_expire(30);
        echo '1';                                           //正确为1，目前cookie是测试方便，最终要删掉
    }
    //验证验证码
    public function checkyzm()
    {
        $yzm=  input('post.yzm');
        $phone=input('post.phone');
        $yzm_ok=session('yzm');
        $phone_yzm_ok=  session('phone_yzm');


        if($yzm==$yzm_ok &&$phone==$phone_yzm_ok){
            
            echo '1';
        }else{
            echo '0';
        }
    }
    public function test()
    {
        cookie('test','mytest');
         //分享接口参数配置
            $jsobj=new \app\wechat\controller\Token();
            $jspackage=$jsobj->getSignPackage();
            $this->assign('jspackage',$jspackage);
            return $this->fetch();
    }

    //订阅提示消息发送
    function send_successmsg($flightNo = null)
    {
        $planekey = cookie('planekey');
        if (empty($flightNo)&&$planekey) {
            $arr = explode('_',$planekey);
            $flightNo = $arr[0];
        }
        $service =new \app\weiwin\service\Kfwechat();
        $openid = session('openid');
/*        $data = [
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>[
            "content"=>"您订阅".$flightNo."航班成功！我们将持续为您带来该班次的最新动态"
            ]
        ];*/
        $data = [
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>[
            "content"=>"您订阅".$flightNo."航班成功！我们将持续为您带来该班次的最新动态"
            ]
        ];
        $res1 = $service->send_msg($data);//发送定制成功提醒
       /* if ($res1) 
        {
           //发送延误提醒
           $this->test2($openid);
           //发送订房推送
            $this->roommsg($openid);
        }*/
    }
    function insertmac(){
        if ($_POST) 
        {
            $data['mac'] = isset($_POST['mac'])?$_POST['mac']:'';
            $where['openid'] = Session::get('openid');
            $where['time'] = ['like',date('Y-m-d')."%"];
            if (!empty($data)&&!empty($where)) 
            {
               $model = new \app\weiwin\model\Plane();
               $res = $model->insertmac($where,$data);
               if ($res) {  return $this->redirect('plane/message');}
            }
            return $this->redirect('plane/message');
        }
        
    }

}

