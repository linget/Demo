<?php
namespace app\admin\controller;
class User extends Base
{
  private  $model = '';

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\User();
  }

  /**
   * [user_list 用户列表]
   */
  function user_list()
  {
    $where = '';
    $c_username = isset($_POST['c_username'])?trim($_POST['c_username']):'';//账号
    $c_fullname = isset($_POST['c_fullname'])?trim($_POST['c_fullname']):'';//名称
    $this->param['c_genre'] = isset($_POST['c_genre'])?trim($_POST['c_genre']):'';//角色
    //注册时间
    $this->param['stime'] =  isset($_POST['stime'])?trim($_POST['stime']):'';
    $this->param['otime'] =  isset($_POST['otime'])?trim($_POST['otime']):'';

    if($_POST)
    {
        $where['a.c_username'] = ['like',"%{$c_username}%"];
        $where['a.c_fullname'] = ['like',"%{$c_fullname}%"];
      if($this->param['stime']&&$this->param['otime'])
      {
        $where['a.c_addtime'] = [['egt',$this->param['stime'].'%'],['elt',$this->param['otime'].'%']];
      }elseif($this->param['stime'])
      {
        $where['a.c_addtime'] = ['gt',$this->param['stime']];
      }
      $where = array_filter($where);
    }

    $info = $this->model->user_list($where);

    $page = $info->render();
    $this->assign('c_username',$c_username);
    $this->assign('c_fullname',$c_fullname);
    $this->assign('post',$this->param);
    $this->assign('data',$info);
    $this->assign('page',$page);
    return view('User/user_list');
  }

  /**
   * [user_add 用户添加]
   */
  function user_add()
  {
    $state = [['c_state'=>0,'state_name'=>'禁止登陆'],['c_state'=>1,'state_name'=>'允许登录']];
    $Role = new \app\admin\model\Role();
    $role_list = $Role->r_list();//角色列表

    $username = $this->model->get_name();//分配账号
    $act = isset($_POST['act'])?trim($_POST['act']):'';
    $pass = md5('123456');
    if ($act == 'insert') {
        $data = [
          'c_username' => $_POST['c_username'],
          'c_password' => $pass,
          'c_fullname' => $_POST['c_fullname'],
          'c_email' => $_POST['c_email'],
          'c_phone' => $_POST['c_phone'],
          'c_state' => $_POST['c_state'],
          'c_address' => $_POST['c_address'],
          'c_addtime' => date('Y-m-d H:i:s',time())
        ];
        $result = $this->model->user_insert($data,$_POST['c_roleid']);
        parent::out($result,'添加成功！','添加失败!',url('user_list'));
    }

    $this->assign('username',$username);
    $this->assign('c_roleid',$role_list);
    $this->assign('state',$state);
    return view('User/user_add');
  }

  /**
   * [user_edit 用户编辑]
   */
  function user_edit()
  {
    $c_id = isset($this->param['c_id'])?$this->param['c_id']:'';
    $state = [['c_state'=>0,'state_name'=>'禁止登陆'],['c_state'=>1,'state_name'=>'允许登录']];
    $Role = new \app\admin\model\Role();
    $role_list = $Role->r_list();//角色列表 
    $username = $this->model->get_name();
    $info = $this->model->get_info(['a.c_id'=>$c_id]);//用户信息

    if ($_POST) {
      $where['a.c_id'] = $_POST['c_id'];
      $data = [
          'c_username' => $_POST['c_username'],
          'c_fullname' => $_POST['c_fullname'],
          'c_email' => $_POST['c_email'],
          'c_phone' => $_POST['c_phone'],
          'c_state' => $_POST['c_state'],
          'c_address' => $_POST['c_address'],
      ];
      $result = $this->model->user_save($data,$where,$_POST['c_roleid']);
      parent::out($result,'编辑成功！','编辑失败!',url('user_list'));
    }


    $this->assign('info',$info);
    $this->assign('username',$username);
    $this->assign('c_roleid',$role_list);
    $this->assign('state',$state);
    return view('User/user_edit');
  }

  /**
   * [user_del 管理员删除]
   */
  function user_del()
  {
    $where['c_id'] = $this->param['c_id'];

    $result = $this->model->user_delete($where);
    parent::out($result,'删除成功！','删除失败!',url('user_list'));
  }


  /**
   * [password_change 密码设置/重置]
   */
  function password_change()
  {
    $where['a.c_id'] = $_POST['id'];

    $user_info = $this->model->get_info($where);
    if ($user_info) 
    {
      $str ="ABCDEFGHIGKLMNOPQRSTUVWXYZabcdefghigklmnopqrstuvwxyz";
      $res = mb_substr(str_shuffle($str),1,4);
      $pass = mt_rand(0,10000);
      $data['c_password'] = md5($res.$pass);
      $result = $this->model->user_save($data,$where,$user_info['c_id']);
      if($result)
      {
        return json_encode($res.$pass,true);
      }
      return false;
    }
    return false;
  }

  /* 更改等登陆状态 */
    public function commodityTake()
  {
    $where['c_id'] = $this->param['c_id'];
    $c_state = $this->param['c_state'];
    if($c_state == 0||$c_state == 2)
    {
      $value=1;
    }
    elseif($c_state == 1)
    {
      $value=2;
    }
      $b = $this->model->chage_role($where,$value);
      if($b){
        $data['error']='1000';
      }else{
        $data['error']='1001';
      }
      return json_encode($data,true);
  }
}
