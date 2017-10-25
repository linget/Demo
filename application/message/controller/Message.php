<?php
namespace app\message\controller;
use think\Log;
class Message
{
    public function test()
    {
        $obj=new \app\message\service\Traffic();
        $list=$obj->getOpenid(2, 'ZH3966', '2017-1-17');
        halt($list);
    }
    //特殊提醒  code 2 航班  1火车
    public function send($data,$code)
    {
        $objTra=new \app\message\service\Traffic();
        if($code==2){
            $phoneList=$objTra->getPhone('2', $data['plane_id'], $data['plane_time']);
            $openidList=$objTra->getOpenid('2', $data['plane_id'], $data['plane_time']);
        }  else {
            $phoneList=$objTra->getPhone('1', $data['train_id'], $data['train_time']);
            $openidList=$objTra->getOpenid('1', $data['train_id'], $data['train_time']);
            Log::write('ceshizhong');
            Log::write($openidList);
        }
         $objSd=new \app\wechat\controller\Message();
        if($openidList){
             $objSd->sendMes($data, $openidList,$code);
        }
        if($phoneList){
            
        }
        
    }
    //常规小时提醒
    public function sendTime($data,$section,$code)
    {
        $objTra=new \app\message\service\Traffic();
        if($code==2){
            $phoneList=$objTra->getPhone('2', $data['plane_id'], $data['plane_time']);
            $openidList=$objTra->getOpenid('2', $data['plane_id'], $data['plane_time']);
        }  else {
            $phoneList=$objTra->getPhone('1', $data['train_id'], $data['train_time']);
            $openidList=$objTra->getOpenid('1', $data['train_id'], $data['train_time']);
        }
         $objSd=new \app\wechat\controller\Message();
        if($openidList){
             $objSd->sendMes($data, $openidList,$code,$section);
        }
        if($phoneList){
            
        }
    }
    public function sendNull($data,$code)
    {
//        $objTra=new \app\message\service\Traffic();
//        if($code==2){
//            $phoneList=$objTra->getPhone('2', $data['plane_id'], $data['plane_time']);
//            $openidList=$objTra->getOpenid('2', $data['plane_id'], $data['plane_time']);
//        }  else {
//            $phoneList=$objTra->getPhone('1', $data['train_id'], $data['train_time']);
//            $openidList=$objTra->getOpenid('1', $data['train_id'], $data['train_time']);
//        }
//         $objSd=new \app\wechat\controller\Message();
//        if($openidList){
//             $objSd->sendMes($data, $openidList,$code);
//        }
//        if($phoneList){
//            
//        }
        file_put_contents('./log.txt', '抱歉，数据有误，无法提供服务！'); 
    }
}

