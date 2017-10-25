<?php
namespace app\wechat\service;
use think\Db;
class Index
{
    //存储openid
    public function setOpenid($openid)
    { 
        $id=  db('yz_follow','db_config2')->where('openid',$openid)->field('id')->find();
        if(empty($id)){
            $data=[
            'openid'=>$openid,
            'state'=>'1',
            'time'=>  time()
        ];
        $res=db('yz_follow','db_config2')->insert($data);
        }  else {
            $data=[
            'state'=>'1',
            'time'=>  time()
        ];
          $res=db('yz_follow','db_config2')->where('openid',$openid)->update($data);
        }
        return $res;
    }
    //取消关注时间
    public function outOpenid($openid)
    {
        $data=[
            'state'=>'0',
            'cancel_time'=>  time()
        ];
          $res=db('yz_follow','db_config2')->where('openid',$openid)->update($data);
        return $res;
    }
    //获取订阅信息
    public function getSubscribe($openid)
    {
        $map['openid']=$openid;
        $map['state']=1;
        $res=db('yz_wechat_users','db_config2')->where($map)->field('kind_id,code_name,code_time')->select();
        if(empty($res)){
            return false;
        }
        $data=array();
        $num=0;
        $objPlane=new \app\index\model\Plane();
        $objTrain=new \app\index\model\Train();
        foreach ($res as $value)
        {
            if($value['kind_id']==1){
                $train=$objTrain->getTrainList('', '', $value['code_name'], $value['code_time']);
                if(empty($train)){
                    continue;
                }
                 $data[$num]['kind_id']=1;
                 $data[$num]['code_name']=$value['code_name'];
                 $data[$num]['code_time']=$value['code_time'];
                 $data[$num]['leave_time']=$train[0]['STARTTIME'];
                 $data[$num]['arrive_time']=$train[0]['ENDTIME'];
                 $data[$num]['leave_address']=$train[0]['FROMCITY'];
                 $data[$num]['arrive_address']=$train[0]['TOCITY'];
                 $data[$num]['state']=$train[0]['REMARK'];
                 $num++;
            }  else {
                 $plane=$objPlane->getFlightInfo('', '', $value['code_name'], $value['code_time']);
                 if(empty($plane)){
                     continue;
                 }
                 $data[$num]['kind_id']=2;
                 $data[$num]['code_name']=$value['code_name'];
                 $data[$num]['code_time']=$value['code_time'];
                 $data[$num]['leave_time']=$plane[0]['STARTTIME'];
                 $data[$num]['arrive_time']=$plane[0]['ENDTIME'];
                 $data[$num]['leave_address']=$plane[0]['FROMCITY'];
                 $data[$num]['arrive_address']=$plane[0]['TOCITY'];
                 $data[$num]['state']=$plane[0]['REMARK_XML'];
                 $num++;
            }
            if($num>5){                                                  //数量太多，微信系统会超时，无法提供服务
                break;
            }
        }
        if(empty($data)){
            return false;
        }
        return $data;
    }
    public function writeTxt($str)
    {
        $newFile=  fopen(LOG_PATH."newtxt", "a");
        fwrite($newFile, $str);
        fwrite($newFile, "\r\n");
        fclose($newFile);
    }
}

