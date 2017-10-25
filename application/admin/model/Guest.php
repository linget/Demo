<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Guest extends Model
{
    //获取短信用户
    public function getMessage($num)
    {
        $res=db('yz_message_users','db_config2')->alias('a')->join('yz_business b','a.business_id=b.code')->where('a.state',1)->field('a.id,a.phone,a.kind_id,a.code_name,a.code_time,b.name')->paginate($num);
        return $res;
    }
    //获取微信用户
    public function getWechat($num)
    {
        $res=db('yz_wechat_users','db_config2')->alias('a')->join('yz_business b','a.business_id=b.code')->where('a.state',1)->field('a.id,a.kind_id,a.code_name,a.code_time,b.name')->paginate($num);
        return $res;
    }
    //获取大屏用户
    public function getScreen($num)
    {
        $res=db('yz_screen','db_config2')->alias('a')->join('yz_business b','a.business_id=b.code')->where('a.state',1)->field('a.id,a.kind_id,a.code_name,a.code_time,b.name')->paginate($num);
        return $res;
    }

    //获取微信用户定制航班/列车
    public function get_wx($where)
    {
        $res=db('yz_wechat_users','db_config2')->where($where)->field('openid,kind_id,code_name,code_time,state')->order('id desc,code_name desc')->find();
        return $res;
    }

    //获取实时状态
   public function get_state($where,$type)
   {
        $res = '';
        if ($type==2) 
        {
            //航班
            $res=db('yz_subscribe_plane','db_config2')->where($where)->field('plane_id,plane_time,state')->find();
        }elseif ($type == 1) {
            //高铁
            $res=db('yz_subscribe_train','db_config2')->where($where)->field('train_id,train_time,state')->find();
        }
        return $res;
   }
}

