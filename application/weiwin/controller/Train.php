<?php
namespace app\weiwin\controller;
class Train  extends Base
{
    public function trainlist()
    {
         $fx=  input('param.fx');
        if($fx){
            $this->fxtrain($fx);
            return $this->fetch('trainlist');
        }  else {
        $fDate=input('post.new_date');
        if($fDate){
            $jsobj=new \app\wechat\controller\Token();
            $jspackage=$jsobj->getSignPackage();
            $this->assign('jspackage',$jspackage);
            $obj=new \app\index\model\Train();
        $fromCity=input('post.train_city_kai')?input('post.train_city_kai'):'';
        $toCity=  input('post.train_city_dao')?input('post.train_city_dao'):'';
        $trainNo=input('post.train_no')?input('post.train_no'):'';
        $flag="0";
        if($trainNo) {
            $flag="1";
        }
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$trainNo.'_'.$fDate;
        session('traffic_train',$traffic);
        $info=$obj->getTrainList($fromCity, $toCity, $trainNo, $fDate);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('trainNo',$trainNo);
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
        $traffic_new=  session('traffic_train');
        $params=  explode('_', $traffic_new);
        $flag=isset($params[0])?$params[0]:'';
        $fromCity=isset($params[1])?$params[1]:'';
        $toCity=  isset($params[2])?$params[2]:'';
        $trainNo=isset($params[3])?$params[3]:'';
        $params[4] = isset($params[4])?$params[4]:'';
        if($kind==1){
            $fDate=strtotime($params[4])-86400;
        }  elseif($kind==2) {
            $fDate=strtotime($params[4])+86400;;
        } else {
            $fDate=strtotime($params[4]);
        }
        $fDate_new=date('Y-m-d',  $fDate);
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$trainNo.'_'.$fDate_new;
        session('traffic_train',$traffic);
        $obj=new \app\weiwin\model\Train();
        $info=$obj->getTrainList($fromCity, $toCity, $trainNo, $fDate_new);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('trainNo',$trainNo);
        $this->assign('fDate',$fDate_new);
        $this->assign('flag',$flag);
        $this->assign('info', $info);
        return $this->fetch('trainlist');
    }
    public function fxtrain($fx)
    {
        $jsobj=new \app\wechat\controller\Token();
        $jspackage=$jsobj->getSignPackage();
        $this->assign('jspackage',$jspackage);
        $params=  explode('_', $fx);
        $flag=1;
        $trainNo=$params[0];
        $fDate=$params[1];
        $fromCity=$params[2];
        $toCity= $params[3];
        $traffic=$flag.'_'.$fromCity.'_'.$toCity.'_'.$trainNo.'_'.$fDate;
        session('traffic',$traffic);
        $obj=new \app\weiwin\model\Train();
        $info=$obj->getTrainList($fromCity, $toCity, $trainNo, $fDate);
        $this->assign('fromCity',$fromCity);
        $this->assign('toCity',$toCity);
        $this->assign('trainNo',$trainNo);
        $this->assign('fDate',$fDate);
        $this->assign('flag',$flag);
        $this->assign('info', $info);
    }

    public function message()
    {
        // $bsn=new \app\index\common\paramters();
        // $business_id= $bsn->getBusiness();
        $business_id='1000000';                 
        $openid= session('openid');
        $dzkind=  cookie('dzkind');
        $planekey=  cookie('planekey');
        if(empty($dzkind) || empty($planekey)){
            return $this->fetch('plane/messageerror');
        }
        $kind= explode('_', $dzkind);
        $planeinfo=  explode('_', $planekey);
        $obj=new \app\weiwin\model\Train();
        if($kind[0]==1){
           $dp=$obj->setScreen($planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3],$openid,$business_id) ;
        }
        if($kind[1]==1){
            $follow=$obj->checkFollow($openid);
            $wx=$obj->setWechat($planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3], $openid, $follow, $business_id);
            $this->assign('kind','wx'); 
        }
        if($kind[2]==1){
            $phone=  session('phone_yzm');
            $dx=$obj->setMessage($phone, $planeinfo[0], $planeinfo[1], $planeinfo[2], $planeinfo[3],$openid, $business_id);
             $this->assign('kind','dx'); 
        }
        if(!$dp && !$wx && !$dx){
            return $this->fetch('plane/messageerror');
        }else{
            $obj->subscribeTrain($planeinfo[0], $planeinfo[1],$planeinfo[2], $planeinfo[3]);
        }
       return  $this->fetch('plane/message');
    }
}

