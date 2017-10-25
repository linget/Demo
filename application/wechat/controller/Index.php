<?php
namespace app\wechat\controller;
use think\Log;
define("TOKEN", "hangbangaotie");
class Index
{
    public function __construct() {
        $this->valid();
    }
    public function index()
    {
        $wechat=new \app\wechat\common\Wechat(TOKEN);
        $data=$wechat->request();
        if($data){
            $this->send($data);
        }  else {
            $wechat->replyText("测试中！");
            exit;
        }
    }
    private function send($data)
    {
        $wechat=new \app\wechat\common\Wechat(TOKEN);
        switch ($data['MsgType'])
        {
            case 'text':
                $obj=new \app\wechat\service\Index();
                $obj->writeTxt($data['FromUserName'].":".$data['Content']);
                $res=  $this->getSubscribe("o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA");
                $wechat->replyText($res);
                break;
            case 'image':
                 $obj=new \app\wechat\service\Index();
                $obj->writeTxt($data['MediaId']);
                $imgobj=new \app\wechat\controller\Pictext();
                $imgobj->getImage($data['MediaId']);
                $wechat->replyText($data['MediaId']);
                 break;
             case 'voice':
                 $obj=new \app\wechat\service\Index();
                $obj->writeTxt($data['MediaId']);
                $imgobj=new \app\wechat\controller\Pictext();
                $imgobj->getVocie($data['MediaId']);
                $wechat->replyText($data['MediaId']);
                 break;
             case 'video':
                 $obj=new \app\wechat\service\Index();
                $obj->writeTxt($data['MediaId']);
                $imgobj=new \app\wechat\controller\Pictext();
                $imgobj->getVideo($data['MediaId']);
                $wechat->replyText($data['MediaId']);
                 break;
           case 'event':
               $this->eventMsg($data);
               break;
            default : 
                $wechat->replyText("欢迎来到煜圳，我们会为提供贴心交通信息服务！");
                break;
        }
    }
    /*
     * 时间回复
     * @ $data 微信传过来的包
     */
    private function eventMsg($data)
    {
        $wechat=new \app\wechat\common\Wechat(TOKEN);
        switch($data['Event'])
        {
            case 'subscribe':
                $objin=new \app\wechat\service\Index();
                $objin->setOpenid($data['FromUserName']);
                $wechat->replyText("欢迎来到煜圳，我们会为提供贴心交通信息服务！");
                break;
            case 'unsubscribe':
                $objout=new \app\wechat\service\Index();
                $objout->outOpenid($data['FromUserName']);
                break;
             default : 
                $wechat->replyText("欢迎来到煜圳，我们会为提供贴心交通信息服务！");
                break;
        }
    }
    //验证
    public function valid()
    {
        $echostr=  input('echostr');
        if($echostr){
            if($this->checkSignature()){
            echo $echostr;
            exit;
        }    
        } 
    }
    //验证
    private function checkSignature()
    {
        $signature=  input('get.signature');
        $timestamp=input('get.timestamp'); 
        $nonce=input('get.nonce');
        $token=TOKEN;
        $tmpArr=array($token,$timestamp,$nonce);
        sort($tmpArr,SORT_STRING);
        $tmpStr=implode($tmpArr);
        $tmpStr=  sha1($tmpStr);
        if($tmpStr==$signature){
            return true;
        }  else {
            return false ;    
        }
    }
    private function getSubscribe($openid)
    {
        $obj=new \app\wechat\service\Index();
        $res=$obj->getSubscribe($openid);
        $str="您成功订阅：\n";
        if($res){
            foreach ($res as $value)
            {
                if($value['kind_id']==1){
                    $str .="列车号：".$value['code_name']."\n";
                    $str .="出发城市：".$value['leave_address']."\n";
                    $str .="到达城市：".$value['arrive_address']."\n";
                    $str .="出发时间：".$value['code_time']." ".$value['leave_time']."\n";
                    $str .="到达时间：".$value['code_time']." ".$value['arrive_time']."\n";
                    $str .="列车状态：".$value['state']."\n\n";
                }else{
                    $str .="航班号：".$value['code_name']."\n";
                    $str .="出发城市：".$value['leave_address']."\n";
                    $str .="到达城市：".$value['arrive_address']."\n";
                    $str .="出发时间：".$value['code_time']." ".$value['leave_time']."\n";
                    $str .="到达时间：".$value['code_time']." ".$value['arrive_time']."\n";
                    $str .="航班状态：".$value['state']."\n\n";
                }
                
            }
            return $str;
        }  else {
            $str="欢迎来到煜圳，我们会为提供贴心交通信息服务！";
            return $str;
        }
    }

    public function test()
    {
//        $obj=new \app\wechat\service\Index();
//        $obj->outOpenid("teerte");
        $res=  $this->getSubscribe("o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA");
        print_r($res);die;
    }
}

