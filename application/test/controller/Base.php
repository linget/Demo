<?php
namespace app\test\controller;
use think\Controller;
class Base extends Controller{
    function __construct(){
      
       parent::__construct();
       self::index();
    }
    

    public function index()
    {
        self::verify_url();//网络测试
    }

    /**
     * [index 网络测试]
     */
    public function verify_url()
    {
        $url = 'http://www.baidu.com';
        $check = @fopen($url, 'r');
        if (!$check) 
        {
            //echo "<script>window.location.href='http://localhost/Demo/public/index.php';</script>";
        }

    }
}
?>