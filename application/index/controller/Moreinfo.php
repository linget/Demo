<?php
namespace app\index\controller;
use think\Controller;

class Moreinfo extends Controller
{
    public function about()
    {
        return $this->fetch();
    }
    //出行须知列表
    public function trafficnotice()
    {
        return $this->fetch();
    }
    //根据id查询到具体出行须知内容
    public function noticeinfo()
    {
        $id=  input('param.id');
        $obj=new \app\index\model\Moreinfo();
        $info=$obj->getNoticeContent($id);
        $this->assign('info',$info);
        return $this->fetch();
    }
    public function noticephone()
    {
        $obj=new \app\index\model\Moreinfo();
        $info=$obj->getPhone();
        $this->assign('info',$info);
        return $this->fetch();
    }
}

