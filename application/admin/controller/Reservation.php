<?php
namespace app\admin\controller;
use think\Request;
class Reservation extends Base
{
    function __construct()
    {
     parent::__construct();
     $this->model = new \app\weiwin\model\Reservation();
     $this->param = Request::instance()->param();
     $this->param['stime'] = isset($this->param['stime'])?$this->param['stime']:'';
     $this->param['otime'] = isset($this->param['otime'])?$this->param['otime']:'';
     $this->param['name'] = isset($this->param['name'])?$this->param['name']:'';
    }

    public function index()
    {
    	
    	return $this->fetch('Reservation/index');
    }
    //房间类型
    public function room()
    {

    	return $this->fetch('Reservation/room');
    }

    //预约列表
    public function room_order()
    {
      if ($_POST) 
      {
          if ($_POST['name']) 
          {
             $where['c_name'] = $_POST['name'];
          }
          if ($_POST['stime']&&$_POST['otime']) 
          {
              $where['c_agreetime'] = [['egt',$_POST['stime'].'%'],['elt',$_POST['otime'].'%']];
          }elseif ($_POST['stime']||$_POST['otime']) {
              $where['c_agreetime'] = isset($_POST['stime'])? $_POST['stime']: $_POST['otime'];
          }
      }
      $where['c_ishandle'] = 1;
     $info = $this->model->find_order($where);
     $page = $info->render();
     $this->assign('page',$page);
     $this->assign('info',$info);

     $this->assign('param',$this->param);
     return $this->fetch('Reservation/room_order');
    }

    //历史预约
    public function olds()
    {
        if ($_POST) 
        {
            if ($_POST['name']) 
            {
               $where['c_name'] = $_POST['name'];
            }
            if ($_POST['stime']&&$_POST['otime']) 
            {
                $where['c_agreetime'] = [['egt',$_POST['stime'].'%'],['elt',$_POST['otime'].'%']];
            }elseif ($_POST['stime']||$_POST['otime']) {
                $where['c_agreetime'] = isset($_POST['stime'])? $_POST['stime']: $_POST['otime'];
            }
        }

        $where['c_ishandle'] = 2;
        $info = $this->model->find_order($where);
        $page = $info->render();
        $this->assign('page',$page);
        $this->assign('info',$info);
        $this->assign('param',$this->param);
        return $this->fetch('Reservation/olds');
    }

    //编辑预约
    function order_edit()
    {
      if ($_POST) 
      {

        $where['c_id'] = isset($_POST['id'])?$_POST['id']:'';
        $data = [
          'c_name'=>$_POST['c_name'],
          'c_sex'=>$_POST['c_sex'],
          'c_phone'=>$_POST['c_phone'],
          'c_agreetime'=>$_POST['c_agreetime'],
          'c_endtime'=>$_POST['c_endtime'],
          'c_roomtype'=>$_POST['c_roomtype']
          ];
        $res = $this->model->update_order($where,$data);
        if ($res) 
        {
            $this->success('修改成功！',url('room_order'));
        }else{
            $this->error('修改失败！',ACTION_NAME);
        }
      }else
      {
        $where['c_id'] = isset($this->param['id'])?$this->param['id']:'';
        $sex = [['c_sex'=>'0','name'=>'女'],['c_sex'=>'1','name'=>'男']];
        $info = $this->model->find_order($where);
        $this->assign('info',$info[0]);
        $this->assign('sexinfo',$sex);
      }  
      return $this->fetch('Reservation/order_edit');
    }

    //删除预约
    function order_del(){
         $where['c_id'] = $this->param['id'];
         $res = $this->model->del_order($where);
         if ($res) 
        {
            $this->success('删除成功！',url('room_order'));
        }else{
            $this->error('删除失败！',url('room_order'));
        }
    }

    //新增预约
    function order_add()
    {
      if ($_POST) 
      {
        $time = date('Y-m-d H:i:s',time());
         $data = [
                'c_name'=>$_POST['c_name'],
                'c_sex'=>$_POST['c_sex'],
                'c_phone'=>$_POST['c_phone'],
                'c_agreetime'=>$_POST['c_agreetime'],
                'c_endtime'=>$_POST['c_endtime'],
                'c_roomtype'=>$_POST['c_roomtype'],
                'c_num'=>$_POST['c_num'],
                'c_addtime'=>$time
            ];
            //插入数据
            $res = $this->model->add_order($data);
            if ($res) 
            {
                $this->success('预约成功！',url('room_order'));
            }else{
                $this->error('预约失败！',ACTION_NAME);
            }
      }
       $sex = [['c_sex'=>'0','name'=>'女'],['c_sex'=>'1','name'=>'男']];
       $this->assign('sexinfo',$sex);
      return $this->fetch('Reservation/order_add');
    }

    //标记预约为已处理
    function is_handle()
    {
      $where['c_id'] = $this->param['id'];
      $data = ['c_ishandle'=>2];
      $res= $this->model->update_order($where,$data);
      if ($res) 
        {
            $this->success('处理成功！',url('room_order'));
        }else{
            $this->error('处理失败！',ACTION_NAME);
        }
    }

}

