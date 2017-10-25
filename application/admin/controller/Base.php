<?php
namespace app\admin\controller;
use think\Validate;
use think\Controller;
use think\Request;
class Base extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->param = Request::instance()->param();
        $auth = new \app\admin\controller\Index();
        $auth->AdminSession();
    }
    public function index()
    {
    	try {
    		
    	} catch (Exception $e) {
    		
    	}
    }

    /**
     * [verify 数据校验]
     * @param data 校验数据
     * @param rule 校验规则
     * @return msg/false
     */
    function verify($data,$rule)
    {
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if (!$result) 
        {
            $msg = $validate->getError();
            return $msg;
        }
        return false;
    }

    function out($info,$success_msg,$error_msg,$url){
        if ($info) 
        {
            $this->success($success_msg,$url);
        }else{
            $this->error($error_msg,$url);
        }
    }
}
