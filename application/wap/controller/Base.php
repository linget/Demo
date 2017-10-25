<?php
namespace app\wap\controller;
use think\Controller;
use think\Validate;
class Base extends controller
{
	public function __construct(){
		parent::__construct();
	}
    public function index()
    {
    	
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
}
