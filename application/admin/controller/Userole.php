<?php
namespace app\admin\controller;
use think\Request;
class Userole extends Base
{
  private  $model = '';
  private  $id = '';

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\Role();
  }

  	/* 角色列表 */
	public function role_list()
	{

		 $info = $this->model->r_list();
		
		 $this->assign('list',$info);

	     return $this->fetch('Userole/role_list');
	}

	/**
	 * [role_add 角色添加]
	 */
	public function role_add()
	{

		if ($_POST) {
			$data = $_POST;
			$data['addtime'] = date('Y-m-d',time());
		
			$info = $this->model->r_insert($data);
			parent::out($info,'添加成功！','添加失败！','role_list');
		}
		$info = $this->model->r_list();
		$this->assign('info',$info);
		$this->assign('title','角色添加');
		$this->assign('variable',ACTION_NAME);
		return $this->fetch('Userole/role_add');
	}

	/**
	 * [role_add 角色编辑]
	 */
	public function role_edit()
	{
		$info = $this->model->r_search(['c_id'=>$this->param['c_id']]);
		if ($_POST) {
			$data = ['rolename'=>$_POST['rolename']];
			$where['c_id'] = $_POST['c_id'];
			$info = $this->model->r_save($data,$where);
			parent::out($info,'编辑成功！','编辑失败！','role_list');
		}
		
		$this->assign('info',$info);
		$this->assign('title','角色添加');
		$this->assign('variable',ACTION_NAME);
		return $this->fetch('Userole/role_add');
	}

	/**
	 * [role_del 删除角色]
	 */
	public function role_del()
	{
		$where['c_id'] = $this->param['c_id'];
		$result = $this->model->r_del($where);
		parent::out($result,'删除成功！','删除失败！','role_list');
	}

	/**
	 * [choice_list 角色功能分配 ]
	 */
	public function choice_list(){	
		$id = isset($this->param['c_id'])?$this->param['c_id']:'';
		$result  = $this->get_rolid($id);//当前用户角色及权限

		$role_id = $result['c_roleid'];
		$default = $result['rol'];
		$list = $this->model->rol_all();//所有权限
		
		$name = $this->model->r_search(['c_id'=>$role_id]);//获取角色名称
		
		$this->assign('Id',$role_id);
		$this->assign('name',$name['rolename']);
		$this->assign('default',$default);
		$this->assign('list',$list);
		return $this->fetch('Userole/choice_list');
	}

	/**
	 * 获取登录用户角色权限
	 */
	public function get_rolid($role_id = ''){
		if ($role_id) 
		{
			$result = $this->model->usr_rol($role_id);
		}else{
			$user=session('userwy');
			$user_id = $user['c_id'];
			$result = $this->model->usr_rol(['a.c_id'=>$user_id]);
		}
		
		return $result;
	}

	//角色分配方法
	public function affirm()
	{
		$c_roleid = $_POST['c_roleid'];
		$c_functionid = isset($_POST['c_functionid'])?$_POST['c_functionid']:'';
		$res = $this->model->rol_change($c_roleid,$c_functionid);//角色分配权限
		parent::out($res['type'],$res['msg'],$res['msg'],$res['url']);
		
	}
}
