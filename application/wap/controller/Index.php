<?php
namespace app\wap\controller;
use think\Request;
use think\Cookie;
use think\Session;

class Index extends Base
{
	private $rule = [
	        ['user','require','账号必须填写!'],
	        ['pass','require','密码必须填写!'],
        ];
	public function __construct(){
		parent::__construct();
		$this->param = Request::instance()->param();
	}
	public function index(){

        $user = Cookie::get('user');
        $pass = Cookie::get('pass');
        $this->assign('user',$user);
        $this->assign('pass',$pass);
        return $this->fetch();
    }

    public function login()
    {
    	if ($_POST) 
    	{
    		$username = htmlspecialchars(trim($this->param['user']));
    		$password = htmlspecialchars(trim($this->param['pass']));

            //参数校验
    		$verify = parent::verify($this->param,$this->rule);
    		if ($verify) 
    		{
    			$this->error($verify);
    		}

            //用户校验
            $model = new \app\wap\model\Users();
            $info = $model ->check_user($username);
            if (!$info) { $this->error('账号不存在！','Index/index');}
            if ($info['c_password'] != md5($password)) { $this->error('密码错误！','Index/index');}
            if ($info['c_state'] == 2||$info['c_state'] == 0) { $this->error('用户已被禁止登录！','Index/index');}

            //权限查询
            
            //写入session
            
            //登陆成功 
            $this->redirect('main');
    		//var_dump($info);die;

    	}
    }

   public function main()
   {
       return view('Index/main');
   }

   public function right()
   {
       return view('Index/right');
   }
}
