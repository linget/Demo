<?php
namespace app\message\controller;

class Infomation
{
    public function changeData()
    {
        $this->changePlane();
        $this->changeTrain();
        echo 'ok';
    }
    //航班数据处理
    public function changePlane()
    {
        $objData=new \app\message\service\Traffic();
        $oldData=$objData->getPlaneSubscribe();
        if(empty($oldData)){
            return ;
        }
        $objPlane=new \app\index\model\Plane();


        foreach ($oldData as $value)
        {
            if($value['state']== "已到达"||$value['state']== "已起飞"){
                $objData->deletePlaneSubscribe($value['id']);
                $map['code_name']=$value['plane_id'];
                $map['code_time']=$value['plane_time']; 
                $objData->usersState($map);
                continue;
            }
            $plane=$objPlane->getFlightInfo('', '', $value['plane_id'], $value['plane_time']);
            //file_put_contents('./log.txt', print_r($value,true));
            if(empty($plane)){
                continue;
            }
            if($plane[0]['REMARK_XML'] == $value['state']){ 
                if($plane[0]['STARTTIME']!= $value['leave_time'] || $plane[0]['ENDTIME']!= $value['arrive_time']){
                    $data['id']=$value['id'];
                    $data['leave_time']=$plane[0]['STARTTIME'];
                    $data['arrive_time']=$plane[0]['ENDTIME'];
                    $objData->updatePlaneSubscribe($data);                     //正常状态，出发离开时间变化，更新数据时间
                   
                }
                $sectionDay=time()-strtotime($value['plane_time']);
                $leave_str=$value['plane_time']." ".$value['leave_time'];
                $sectionNotice=  strtotime($leave_str)-time();
                if($sectionDay > 86400 || $sectionDay<0){
                    continue;                                                //不是一天内数据不用发消息
                }
                if($sectionNotice<8100 && $sectionNotice>0){                                    //距离起飞时间2h15min内,发送提示信息
                    $data['id']=$value['id'];
                    $data['plane_id']=$value['plane_id'];
                    $data['plane_time']=$value['plane_time'];
                    $data['leave_time']=$plane[0]['STARTTIME'];
                    $data['arrive_time']=$plane[0]['ENDTIME'];
                    $data['leave_address']=$value['leave_address'];
                    $data['arrive_address']=$value['arrive_address'];
                    $data['state']=$plane[0]['REMARK_XML'];
                    $data['notice_time']=$value['notice_time'];
                    $this->setTimeNotice($data);
                }
            }else {                                                           //状态不同数据处理
                $data['id']=$value['id'];
                $data['plane_id']=$value['plane_id'];
                $data['plane_time']=$value['plane_time'];
                $data['leave_time']=$plane[0]['STARTTIME'];
                $data['arrive_time']=$plane[0]['ENDTIME'];
                $data['leave_address']=$value['leave_address'];
                $data['arrive_address']=$value['arrive_address'];
                $data['state']=$plane[0]['REMARK_XML'];
                $data['notice_time']=   time();
                $this->differentState($data);
            }
        }   
    }
    //火车数据处理
    public function changeTrain()
    {
        $objData=new \app\message\service\Traffic();
        $oldData=$objData->getTrainSubscribe();
        if(empty($oldData)){
            return ;
        }
        $objTrain=new \app\index\model\Train();
         foreach ($oldData as $value)
         {
              if($value['state']== "已到达"||$value['state']== "已出发"){
                $objData->deleteTrainSubscribe($value['id']);
                $map['code_name']=$value['train_id'];
                $map['code_time']=$value['train_time']; 
                $objData->usersState($map);
                continue;
            }
            if(empty($value['leave_time']) || is_null($value['leave_time'])){     //时间为空，删除订阅数据
                $objData->deleteTrainSubscribe($value['id']);
                $map['code_name']=$value['train_id'];
                $map['code_time']=$value['train_time']; 
                $objData->usersState($map);
                $objMes=new \app\message\controller\Message();
                $objMes->sendNull($map,1);
                continue;
            }
            $train=$objTrain->getTrainList($value['leave_address'], $value['arrive_address'], $value['train_id'], $value['train_time']);
             if(empty($train)){
                continue;
            }
            if($train[0]['REMARK'] == $value['state']){
                if($train[0]['STARTTIME']!= $value['leave_time'] || $train[0]['ENDTIME']!= $value['arrive_time']){
                    $data['id']=$value['id'];
                    $data['leave_time']=$train[0]['STARTTIME'];
                    $data['arrive_time']=$train[0]['ENDTIME'];
                    $objData->updatePlaneSubscribe($data);                     //正常状态，出发离开时间变化，更新数据时间
                   
                }
                $sectionDay=time()-strtotime($value['train_time']);
                $leave_str=$value['train_time']." ".$value['leave_time'];
                $sectionNotice=  strtotime($leave_str)-time();
                if($sectionDay > 86400 || $sectionDay<0){
                    continue;                                                //不是一天内数据不用发消息
                }
                if($sectionNotice<8100 && $sectionNotice>0){                                    //距离起飞时间2h15min内,发送提示信息
                    $data['id']=$value['id'];
                    $data['train_id']=$value['train_id'];
                    $data['train_time']=$value['train_time'];
                    $data['leave_time']=$train[0]['STARTTIME'];
                    $data['arrive_time']=$train[0]['ENDTIME'];
                    $data['leave_address']=$value['leave_address'];
                    $data['arrive_address']=$value['arrive_address'];
                    $data['state']=$train[0]['REMARK'];
                    $data['notice_time']=$value['notice_time'];
                    $this->trainTimeNotice($data);
                }
            }  else {
                var_dump($train);die;
                $data['id']=$value['id'];
                $data['train_id']=$value['train_id'];
                $data['train_time']=$value['train_time'];
                $data['leave_time']=$train[0]['STARTTIME'];
                $data['arrive_time']=$train[0]['ENDTIME'];
                $data['leave_address']=$value['leave_address'];
                $data['arrive_address']=$value['arrive_address'];
                $data['state']=$train[0]['REMARK'];
                $data['notice_time']=   time();
                $this->trainState($data);
            }
         }
    }

    //飞机状态不同数据处理
     public function differentState($data)
    {
         if(empty($data)){
             return ;
         }
         $objMes=new \app\message\controller\Message();
         $objTrf=new \app\message\service\Traffic();
        switch ($data['state']){
        case "取消":
            $objMes->send($data,2);
            $objTrf->deletePlaneSubscribe($data['id']);
            $map['code_name']=$data['plane_id'];
            $map['code_time']=$data['plane_time']; 
            $objTrf->usersState($map);                             //更改定制用户状态
            break;
        case "延误":
            $objMes->send($data,2);
            $objTrf->updatePlaneSubscribe($data);
           break;
        case "已起飞":
            $objMes->send($data,2);
            $objTrf->deletePlaneSubscribe($data['id']);
            $map['code_name']=$data['plane_id'];
            $map['code_time']=$data['plane_time']; 
            $objTrf->usersState($map);
            break;
        case "已到达":
            $objMes->send($data,2);
            $objTrf->deletePlaneSubscribe($data['id']);
            $map['code_name']=$data['plane_id'];
            $map['code_time']=$data['plane_time']; 
            $objTrf->usersState($map);
            break;
        default :
           break;
        }
         
    }
    //卡点提醒，属于区间
    public function setTimeNotice($data)
    {
        $objMes=new \app\message\controller\Message();
        $objTrf=new \app\message\service\Traffic();
        $leave_str=$data['plane_time']." ".$data['leave_time'];
        $sectionNow=strtotime($leave_str) - time();
        $sectionNotiec=strtotime($leave_str)-$data['notice_time'];
        if($sectionNow<8100 && $sectionNow>=6300 && $sectionNotiec>8100 ){   //2小时
            $objMes->sendTime($data, '7200',2);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updatePlaneSubscribe($map);
        }
        if($sectionNow<4500 && $sectionNow>=2700 && $sectionNotiec>4500){  //1小时
            $objMes->sendTime($data, '3600',2);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updatePlaneSubscribe($map);
        }
        if($sectionNow<2700 && $sectionNow>=900 && $sectionNotiec>2700){   //30分钟
            $objMes->sendTime($data, '900',2);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updatePlaneSubscribe($map);
        }
    }
    //火车不同状态处理
    public function trainState($data)
    {
         if(empty($data)){
             return ;
         }
         $objMes=new \app\message\controller\Message();
         $objTrf=new \app\message\service\Traffic();
        switch ($data['state']){
        case "取消":
            $objMes->send($data,1);
            $objTrf->deleteTrainSubscribe($data['id']);
            $map['code_name']=$data['train_id'];
            $map['code_time']=$data['train_time']; 
            $objTrf->usersState($map);                             //更改定制用户状态
            break;
        case "晚点":
            $objMes->send($data,1);
            $objTrf->updateTrainSubscribe($data);
           break;
        case "已出发":
            $objMes->send($data,1);
            $objTrf->deleteTrainSubscribe($data['id']);
            $map['code_name']=$data['train_id'];
            $map['code_time']=$data['train_time']; 
            $objTrf->usersState($map);
            break;
        case "已到达":
            $objMes->send($data,1);
            $objTrf->deleteTrainSubscribe($data['id']);
             $map['code_name']=$data['train_id'];
            $map['code_time']=$data['train_time']; 
            $objTrf->usersState($map);
            break;
        default :
           break;
        }
    }
    //火车卡点发消息
    public function trainTimeNotice($data)
    {
        $objMes=new \app\message\controller\Message();
        $objTrf=new \app\message\service\Traffic();
        $leave_str=$data['train_time']." ".$data['leave_time'];
        $sectionNow=strtotime($leave_str) - time();
        $sectionNotiec=strtotime($leave_str)-$data['notice_time'];
        if($sectionNow<8100 && $sectionNow>=6300 && $sectionNotiec>8100 ){   //2小时
            $objMes->sendTime($data, '7200',1);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updateTrainSubscribe($map);
        }
        if($sectionNow<4500 && $sectionNow>=2700 && $sectionNotiec>4500){  //1小时
            $objMes->sendTime($data, '3600',1);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updateTrainSubscribe($map);
        }
        if($sectionNow<2700 && $sectionNow>=900 && $sectionNotiec>2700){   //30分钟
            $objMes->sendTime($data, '900',1);
            $map['id']=$data['id'];
            $map['notice_time']=  time();
            $objTrf->updateTrainSubscribe($map);
        }
    }

    public function test()
    {
//       $objTrain=new \app\index\model\Train();
//       $train=$objTrain->getTrainList('', '', 'G6', '2017-1-11');
//        $objPlane=new \app\index\model\Plane();
//        $plane=$objPlane->getFlightInfo('', '', 'ZH1532', '2017-1-12');
//        $str=  strtotime("2017-1-11 12:30");
//        print_r($plane);die;
    //    echo '151515';
        sleep(3);
        echo 'ok2';
        
    }
}

