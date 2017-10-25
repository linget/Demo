<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
class Menu extends Base
{
  private  $model = '';
  private  $id = '';
  protected  $rule = [
              ['c_mname','require','菜单名称必须填写！']
              ];

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\Menu();
    $this->id = isset($this->param['id'])?$this->param['id']:'';

  }
  /* 菜单列表 */
  public function menu_list()
  {
    
    $info = $this->model->menu_list();
    $page = $info->render();

    $this->assign('info',$info);
    $this->assign('revise','menu_list');
    $this->assign('page',$page);
    return $this->fetch('Menu/menu_list');
   }

   /* 子菜单 */
   function menu_child()
   {
      $info = $this->model->menu_search(['c_pid'=>$this->id]);
      $page = $info->render();

     
      $this->assign('id',$this->id);
      $this->assign('info',$info);
      $this->assign('page',$page);
      return $this->fetch('Menu/menu_child');
   }

  /* 菜单添加 */
  public function menu_add(){
      if($_POST)
      {
        $data[ 'c_mname'] = $_POST['c_mname'];
        $data[ 'c_murl'] = $_POST['c_murl'];
        
        if($msg = parent::verify($_POST,$this->rule))
        {
          $this->error($msg,'menu_list');exit;
        }

        $info = $this->model->menu_insert($data);
        
        if ($info) 
        {
          return $this->success('添加成功！','menu_list');exit;
        }else
        {
          return $this->error('添加失败！','menu_list');exit;
        }
      }
       $this->assign('revise','menu_add');
       $this->assign('title','菜单添加');
       return $this->fetch('Menu/menu_add');
    }

   function child_add(){
      $pid = isset($this->param['c_pid'])?$this->param['c_pid']:'';//父级id
      if($_POST)
      {
        $data['c_pid'] = $pid;
        $data['c_mname'] = $_POST['c_mname'];
        $data['c_murl'] = $_POST['c_murl'];
      
        $info = $this->model->child_insert($data);
        parent::out($info,'添加成功！','添加失败',url('menu_child',['id'=>$pid]));
      }
       $this->assign('revise','child_add');
       $this->assign('id',$this->param['c_pid']);
       $this->assign('title','菜单添加');
       return $this->fetch('Menu/menu_add');
    }

  /* 菜单编辑 */
  public function menu_edit(){
       $pid = isset($this->param['c_pid'])?$this->param['c_pid']:'';//父级id
       $revise = 'menu_list';
       if ($pid) {
          $revise = url('menu_child',['id'=>$pid]);
       }
       

      if (empty($this->id)) 
      {
        $this->error('参数错误！','menu_list');
      }
      if ($_POST) 
      {
   
        $data = ['c_mname' => $_POST['c_mname'],'c_murl' => $_POST['c_murl']];
        $result = $this->model->menu_update($data,['c_id'=>$this->id]);
         if ($result) 
        {
          return $this->success('修改成功！',$revise);exit;
        }else
        {
          return $this->error('修改失败！',$revise);exit;
        }
       
      }
      $info = $this->model->menu_search(['c_id'=>$this->id],$type=1);


      $this->assign('info',$info);
      $this->assign('id',$this->id);
      return $this->fetch('Menu/menu_edit');
    }


  /* 菜单删除 */
  public function menu_del(){
        if ($this->id) 
        {
          $where['c_id'] = $this->id;
          $this->model->menu_del($where);

          $this->success('删除成功','menu_list');
        }else{
          $this->error('参数错误！','menu_list');
        }       
    }
}
