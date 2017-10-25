<?php
namespace app\index\model;
use think\Model;
use think\Db;
use think\Session;
class Index extends Model
{
    //获取主页内容设置
    function get_val()
    {

        return Db::name('screen_main a')->join('t_screen b','a.c_screenid = b.c_type')->select();
    }
}