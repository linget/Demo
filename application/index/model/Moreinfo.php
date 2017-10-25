<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Moreinfo extends Model
{
    /*
     * 获取出行须知信息
     */
    public function getNotice()
    {
        $res=  db('yz_notice_code','db_config2')->field('id,name')->select();
        return $res;
    }
    /*
     * 根据id查询具体出行须知内容
     */
    public function getNoticeContent($id)
    {
        $res=  db('yz_notice_code','db_config2')->where('id',$id)->find();
        return $res;
    }
    public function getPhone()
    {
        $res=db('yz_notice_phone','db_config2')->select();
        return $res;
    }
}

