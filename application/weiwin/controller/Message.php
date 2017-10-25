<?php
namespace app\admin\controller;
use think\Session;
use think\Db;
class Message extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    //消息模板
    public function index()
    {
        echo "string";die;
        return $this->fetch('Message/index');
    }

}
