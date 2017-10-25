<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Business extends Model
{
    public function getInfo($code)
    {
        $map=[
            'code'=>$code,
            'review'=>1
        ];
        $res=db('yz_business_info','db_config2')->where($map)->find();
        return $res;
    }
}
