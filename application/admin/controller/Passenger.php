<?php
namespace app\admin\controller;
/**
* 
*/
class Passenger extends Base
{
	private $where = '';
	function __construct()
	{
		parent::__construct();
	    $this->model = new \app\admin\model\Passenger();
	    $this->param['stime'] = isset($this->param['stime'])?$this->param['stime']:'';
		$this->param['otime'] = isset($this->param['otime'])?$this->param['otime']:'';
		$this->param['c_number'] = isset($this->param['c_number'])?$this->param['c_number']:'';
	}
	/**
	 * [index 旅客列表]
	 */
	function index()
	{
		if($_POST){
			if($this->param['stime']&&$this->param['otime'])
		    {
		        $this->where['c_addtime'] = [['egt',$this->param['stime'].'%'],['elt',$this->param['otime'].'%']];
		    }elseif($this->param['stime'])
		    {
		        $this->where['c_addtime'] = ['gt',$this->param['stime']];
		    }
			$c_number = $_POST['c_number'];
			$this->where['c_number'] = ['like','%'.$c_number.'%'];
		}

		$info = $this->model->user_list($this->where,'c_time desc');

		$page = $info->render();
		$this->assign('page',$page);

		$this->assign('post',$this->param);
		$this->assign('data',$info);
		return view();
	}
	/**
	 * [index 旅客历史记录]
	 */
	function olds()
	{

		if($_POST){
			if($this->param['stime']&&$this->param['otime'])
		    {
		        $this->where['c_addtime'] = [['egt',$this->param['stime'].'%'],['elt',$this->param['otime'].'%']];
		    }elseif($this->param['stime'])
		    {
		        $this->where['c_addtime'] = ['gt',$this->param['stime']];
		    }
		    $this->where['c_number'] = ['like','%'.$c_number.'%'];
		}
		$this->where['c_time'] = ['lt',date('Y-m-d',time())];
		$order ='c_addtime desc';
		$info = $this->model->user_list($this->where,$order);

		$page = $info->render();
		$this->assign('page',$page);

		$this->assign('post',$this->param);
		$this->assign('data',$info);
		return view();
	}

	/**
	 * [export 导出数据]
	 */
	function export()
	{
		$service = new \app\admin\service\Upload();
		$service->index();
	}
}
?>