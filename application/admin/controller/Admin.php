<?php
namespace app\admin\controller;
use think\Request;

class Admin extends Base
{
  private  $model = '';
  private  $id = '';

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\Admin();
    $this->id = isset($this->param['Id'])?$this->param['Id']:'';//菜单id
  }

  /**
   * [function_list 一级功能列表]
   */
  function function_list()
  {
    $info  = $this->model->function_list();
    $this->assign('data',$info);
    return $this->fetch('Admin/function_list');
  }

  /**
   * [function_details 二级功能列表]
   */
  function function_details()
  {
      $where['c_pid'] = $this->id;
      $info  = $this->model->function_list($where);


      $this->assign('data',$info);
      return $this->fetch('Admin/function_details');
  }

  /**
   * [details_three 三级功能列表]
   * @return [type] 
   */
  function details_three(){
 
    $info = $this->model->details_list(['c_menuid'=>$this->param['c_sonid']]);
    $page = $info->render(); 

    $result = $this->model->details_search(['c_sonid'=>$this->param['c_sonid']]);

    $menu = $this->model->details_search(['c_id'=>$this->id]);
    $this->assign('page',$page);
    $this->assign('data',$info);
    $this->assign('menu_id', $result['c_menuid']);
    return $this->fetch('Admin/details_three');
  }

  /**
   * [function_add 三级功能添加]
   */
  function function_add()
  {

    if ($_POST) 
    {
      
      $data = ['c_functionname'=>$_POST['c_functionname'],'c_functionkey'=>$_POST['c_functionkey'],'c_menuid'=>$_POST['c_sonid']];
      $info  = $this->model->menu_insert($data);
      parent::out($info,'添加成功！','添加失败',url('function_details',['Id'=>$_POST['c_menuid']]));
    }

    $this->assign('c_sonid',$this->param['c_sonid']);
    $this->assign('c_menuid',$this->param['c_menuid']);
    $this->assign('variable',ACTION_NAME);
    return $this->fetch('Admin/function_add');
  }

  /**
   * [function_edit 三级功能编辑]
   */
  function function_edit()
  {

    $info = $this->model->details_search(['c_id'=>$this->param['c_id']]);

    if ($_POST) 
    {
      $data = ['c_functionname'=>$_POST['c_functionname'],'c_functionkey'=>$_POST['c_functionkey']];
      $where = ['c_id'=>$_POST['c_id']];
     
      $info  = $this->model->function_edit($data,$where);
      parent::out($info,'编辑成功！','编辑失败',url('details_three',['c_sonid'=>$_POST['c_menuid']]));
    }


    $this->assign('info',$info);
    $this->assign('revise','details_edit');

    $this->assign('variable',ACTION_NAME);
    return $this->fetch('Admin/function_add');
  }

  /**
   * [function_del 三级功能删除]
   */
  function function_del()
  {
    $info  = $this->model->menu_del(['c_id'=>$this->param['c_id']]);
    parent::out($info,'删除成功！','删除失败',url('details_three',['c_sonid'=>$this->param['c_menuid']]));
  }

  /**
   * [serect 修改密码]
   */
  function serect(){
    $admin=session('userwy');

    $this->assign('c_id',$admin['c_id']);
    $this->assign('c_username',$admin['c_username']);
    $this->assign('c_fullname',$admin['c_fullname']);
    $this->assign('c_password',$admin['c_password']);
    $this->display();
    return view('Admin/serect');
  }
  /**
   * [serect 修改密码方法]
   */
  function serectedit()
  {
    $_POST['c_password2']=md5(trim($_POST['c_password2']));
    $rules = [
        ['c_password2','require|confirm:c_password1','原密码必须填写！|原始密码不正确！'],
        ['c_password','require','新密码必须填写!'],
        ['c_password4','require|confirm:c_password','确认密码必须填写！|确认密码不一致！'],
    ];
    $msg = parent::verify($_POST,$rules);
    if($msg)
    {
      $this->error($msg);
    }
    $where['c_id'] = $_POST['c_id'];
    $data['c_password'] = md5(trim($_POST['c_password']));
    $result = $this->model->change_pass($data,$where);
    parent::out($result,'密码修改成功','密码修改失败',url('Index/right'));
  }
}
