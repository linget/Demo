<?php
namespace app\home\controller;
use think\Controller;
class Socketest extends Controller{

    /*function __construct(){
        parent::__construct();
        $this->sock_servive = new \app\lib\org\Sock();
    }*/
    /**
     * [index 网络故障静态页]
     */
    public function index()
    {
        return view();
    }

    public function test2()
    {
        return view();
    }
}

?>