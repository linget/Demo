<?php
namespace app\message\service;
use think\Db;
class Traffic
{
    //获取订阅者的电话
    public function getPhone($kind_id,$code_name,$code_time)
    {
        $map['kind_id']=$kind_id;
        $map['code_name']=$code_name;
        $map['code_time']=$code_time;
        $map['state']=1;
        $list=  db('yz_message_users','db_config2')->where($map)->field('phone')->select();
        if(empty($list)){
            return false;
        }  else {
            return $list;    
        }
    }
    //获取订阅者的openid
    public function getOpenid($kind_id,$code_name,$code_time)
    {
        $map['a.kind_id']=$kind_id;
        $map['a.code_name']=$code_name;
        $map['a.code_time']=$code_time;
        $map['a.state']=1;
        $list=  db('yz_wechat_users','db_config2')->alias('a')->join('yz_follow b','a.openid=b.openid')->where($map)->field('a.openid')->select();
        if(empty($list)){
            return false;
        }  else {
            return $list;    
        }
    }
    //获取飞机订阅表
    public function getPlaneSubscribe()
    {
        $list=  db('yz_subscribe_plane','db_config2')->field('id,plane_id,plane_time,leave_time,arrive_time,leave_address,arrive_address,state,notice_time')->select();
        return $list;
    }
    //获取火车订阅表
    public function getTrainSubscribe()
    {
        $list=  db('yz_subscribe_train','db_config2')->field('id,train_id,train_time,leave_time,arrive_time,leave_address,arrive_address,state,notice_time')->select();
        return $list;
    }
    //更新订阅表
    public function updatePlaneSubscribe($data)
    {
        $res=  db('yz_subscribe_plane','db_config2')->update($data);
        return $res;
    }
    //更新火车订阅表
    public function updateTrainSubscribe($data)
    {
         $res=  db('yz_subscribe_train','db_config2')->update($data);
        return $res;
    }
    //删除订阅信息
    public function deletePlaneSubscribe($id)
    {
        $res=db('yz_subscribe_plane','db_config2')->delete($id);
        return $res;
    }
    //删除火车订阅信息
    public function deleteTrainSubscribe($id)
    {
        $res=db('yz_subscribe_train','db_config2')->delete($id);
        return $res;
    }
    //短信、微信、大屏订阅状态改为2，过期
    public function usersState($map)
    {
        db('yz_message_users','db_config2')->where($map)->update(['state'=>2]);
        db('yz_wechat_users','db_config2')->where($map)->update(['state'=>2]);
        db('yz_screen','db_config2')->where($map)->update(['state'=>2]);
    }
}

