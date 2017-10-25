<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Cookie;
use think\Request;
Class Index extends Controller{




    public function index(){

        $user = Cookie::get('user');
        $pass = Cookie::get('pass');
        $this->assign('user',$user);
        $this->assign('pass',$pass);
        return $this->fetch('Index/index');
    }
   /*用户登录添加权限
    * @param username
    * @param password
    *
    */
    public function login(){
        $username = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars(trim($_POST['password']));
        $checkman = isset($_POST['checkman'])?$_POST['checkman']:'';

        if (empty($username)||empty($password)) {
            $this->error('用户名或密码为空！');die;
        }

        $info = Db::name('admin_users')->where("c_username = '$username'")->find();
        if (empty($info)) {
            $this->error('账号不存在！');die;
        }
        if ($info['c_password'] != md5($password)) {
            $this->error('密码不正确！');die;
        }

        if($info['c_state'] == 2||$info['c_state'] == 0){
            $this->error('用户已被禁止登录！');die;
        }
        
         //查询用户角色所有功能权限
        $sql = "select * from t_admin_function where c_id in(select c_functionid from t_admin_role_function where c_roleid in(select c_roleid from t_admin_user_role where c_userid =".$info['c_id']."))";

        $rolist = Db::query($sql);

        if (!$rolist) {
           $this ->error('未分配权限！');die;
        } 

        
        Session::set('sessuid',$info['c_id']);
      
        Session::set('rolelist', $rolist);  //设置session
        Session::set('userwy',$info);
        $info['c_addtime'] = date('Y-m-d H:i:s');
        Session::set('ADMIN_NAME', $info['c_fullname']);  //设置session
        if ($checkman==1) 
        {
            Cookie::set('user',$username,time()+7*24*3600);
            Cookie::set('pass',$password,time()+7*24*3600);
        }
        $this->success('登录成功','main');
        die;
    }


    //注册模块，已取消
    function userarr()
    {
        $arr=array(
                array('c_username','','用户名必须唯一',0,'unique',1),
                array('c_fullname','require','真实姓名必须填写！'),
                array('c_fennum','require','身份证号码必须填写！'),
                array('c_fennum','','身份证号码已被注册',0,'unique',1),
                array('c_phone','require','电话号码必须填写！'),
                array('c_email','require','邮箱必须填写！'),
        );
        return $arr;
     }

    //角色分配
     public function give_role($arr){
        $user_role = Db::name('admin_user_role')->add($arr);
        return $user_role;
     }


     /* 菜单加载 */
    public function left() {
        $menudata = Db::name('admin_menu')->select();
        $menuinfo = array();
        $count = 0;
        foreach ($menudata as $key => $value) {
            if ($value['c_pid'] == 0) {
                $count1 = 0;
                $child = array();
                foreach ($menudata as $key1 => $value1) {
                    if ($value1['c_pid'] == $value['c_id']) {
                        if ($this->CheckAuthority($value1['c_murl'])) {
                            $child[$count1] = $value1;
                            $count1++;
                        }
                    }
                }
                if (count($child) > 0) {
                    $menuinfo[$count]['c_mname'] = $value['c_mname'];
                    $menuinfo[$count]['child'] = $child;
                    $count++;
                }
            }
        }
        $this->assign('list', $menuinfo);
        return $this->fetch('Index/left');
    }
    
    
    
    // 后台管理员session
    public function AdminSession() {
        $admin=Session::get('userwy');
        if(empty($admin)){
            echo '<script language="javascript">top.location="'.url('Index/index').'";</script>';
        }
        $url = $_SERVER['HTTP_HOST'];
        $url = $url . $_SERVER['REQUEST_URI'];
        $rolelist = Session::get("rolelist");
        
        if ($rolelist == null) {
            $this->error('您的账号未分配任何权限', url('Index/login'));
        }
   
        $result = $this->CheckAuthority($url);
        
        $admin=Session::get('userwy');
        $userid = $admin['c_id'];
        $username = $admin['c_username'];
        $parameter = $this->GetParameter();
        
        if (!$result) {
            $this->error('您没有权限访问', url('Index/right'));
        } else {
            //写入日志
            //$this->InsertLog($userid, $username, $url, $parameter);
            return true;
        }
    }
    

    
    //获取参数
    protected function GetParameter() {
        return  Request::instance()->param();
    }
    
    //添加日志
    protected function InsertLog($userid, $username, $url, $parameter) {
    
        $data['c_userid'] = $userid;
        $data['c_username'] = $username;
        $data['c_url'] = $url;
        $data['c_parameter'] = $parameter;
        $data['c_ip'] = $this->get_client_ip();
        $data['c_addtime'] = date('Y-m-d H:i:s', time());
        $result = Db::name('Admin_log')->insert($data);
        return $result;
    }
    
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    protected function get_client_ip($type = 0) {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
    
    /* 使用权限判断 */
    protected function CheckAuthority($url) {
        $rolelist = Session::get("rolelist");
        $result = false;
        if ($rolelist == null) {
            return $result;
        }
       
        foreach ($rolelist as $key => $value) {
            $Matchingstr = "|" . $value["c_functionkey"] . "|Ui";
            if (preg_match($Matchingstr, $url)) {

                $result = true;
                break;
            }
        }
       
        return $result;
    }
    

    //退出系统
    public function signout(){
        Session::set('userwy',null);
        Session::set('ADMIN_NAME',null);
        Session::set('rolelist',null);
        $this->redirect('Index/index');
    }
    
    /* 框架加载 */
    public function main(){

        $admin=Session::get('userwy');
        if(empty($admin)){
            echo '<script language="javascript">top.location="'.url('Index/index').'";</script>';
        }

        return $this->fetch('Index/main');
    }
    public function top(){
        
        return $this->fetch('Index/top');
    }

    public function right(){
        return $this->fetch('Index/right');
    }
     /* 框架加载结束 */

    /* 退出清除cookie */
    public function cookie_del()
    {
        Cookie::set('pass',null);
        $this->redirect('Index/index');
    }
    
    
}
?>