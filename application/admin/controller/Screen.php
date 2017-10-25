<?php
namespace app\admin\controller;
use think\Request;

/**
* Ips管理控制器
*/
class Screen extends Base
{
	private $c_type = [['c_state'=>0,'c_name'=>'请选择'],['c_state'=>1,'c_name'=>'航班态势'],['c_state'=>2,'c_name'=>'航班列表'],['c_state'=>3,'c_name'=>'列车态势'],['c_state'=>4,'c_name'=>'列车列表']];
	private $types = 1;
	function __construct()
	{
		parent::__construct();
		$this->model = new \app\admin\model\Screen();
	}

	/**
	 * [screen_add 显示设置]
	 */
	public function index(){
		 //查询时间
	    $this->param['stime'] =  isset($_POST['stime'])?trim($_POST['stime']):'';
	    $this->param['otime'] =  isset($_POST['otime'])?trim($_POST['otime']):'';

	    $info = $this->model->lists();
	    $c_type = ['0'=>'未选择','1'=>'航班态势','2'=>'航班列表','3'=>'列车态势','4'=>'列车列表'];
	  	
	    $this->assign('c_type',$c_type);
	    $this->assign('data',$info);
		$this->assign('post',$this->param);
		return view();
	}

	/**
	 * [screen_add 添加设置]
	 */
	public function screen_add()
	{
		
		if($_POST)
		{
			$data = array_filter($_POST);
			//语言设置
			$chi = isset($_POST['c_language']['chinaese'])?$_POST['c_language']['chinaese']:'';
			$eng = isset($_POST['c_language']['english'])?$_POST['c_language']['english']:'';
			//状态
			$out = isset($_POST['c_isfromsha']['out'])?$_POST['c_isfromsha']['out']:'';
			$arrive = isset($_POST['c_isfromsha']['arrive'])?$_POST['c_isfromsha']['arrive']:'';
			
			$data['c_language'] = $this->set_language($chi,$eng);
			$data['c_isfromsha']= $this->set_isfromsha($out,$arrive);
			$data['c_addtime'] = date('Y-m-d H:i:s',time());
			$info = $this->model->adds($data);
			parent::out($info,'添加成功！','添加失败!',url('index'));
		}
		$info = ['c_id'=>'','c_type'=>'','c_pagetime'=>'','c_roltime'=>'','c_fontsize'=>'','c_fontcolor'=>'','c_language'=>'','c_isfromsha'=>'','c_istrue'=>''];


		$this->assign('info',$info);
		$this->assign('variable',ACTION_NAME);
		$this->assign('c_type',$this->c_type);
		return view();
	}

	/**
	 * [screen_edit 编辑设置]
	 */
	function screen_edit()
	{
		$where['c_id'] = $this->param['c_id'];
		$info = $this->model->search($where);
		if ($_POST) 
		{
			//语言设置
			$chi = isset($_POST['c_language']['chinaese'])?$_POST['c_language']['chinaese']:'';
			$eng = isset($_POST['c_language']['english'])?$_POST['c_language']['english']:'';

			$out = isset($_POST['c_isfromsha']['out'])?$_POST['c_isfromsha']['out']:'';
			$arrive = isset($_POST['c_isfromsha']['arrive'])?$_POST['c_isfromsha']['arrive']:'';

			$_POST['c_istrue'] = isset($_POST['c_istrue'])?$_POST['c_istrue']:'';

			$c_language = $this->set_language($out,$arrive);
			$c_isfromsha = $this->set_isfromsha($out,$arrive);
			//更新数据
			$data = ['c_type'=>$_POST['c_type'],'c_pagetime'=>$_POST['c_pagetime'],'c_roltime'=>$_POST['c_roltime'],'c_fontsize'=>$_POST['c_fontsize'],'c_fontcolor'=>$_POST['c_fontcolor'],'c_language'=>$c_language,'c_isfromsha'=>$c_isfromsha,'c_istrue'=>$_POST['c_istrue']];
			$whr['c_id'] = $_POST['c_id'];
			$info = $this->model->updates($data,$whr);
			parent::out($info,'编辑成功！','编辑失败!',url('index'));
		}

		$this->assign('c_type',$this->c_type);
		$this->assign('info',$info);
		$this->assign('variable',ACTION_NAME);
		return view('screen_add');
	}

	/**
	 * [screen_edit 删除设置]
	 */
	function screen_del()
	{
		$where['c_id'] = $this->param['c_id'];
		$info = $this->model->del($where);
		parent::out($info,'删除成功！','删除失败!',url('index'));
	}

	/**
	 * [round 走马灯设置]
	 */
	function round(){
		
		$info = $this->model->lists($this->types);
		$c_type = ['0'=>'未选择','1'=>'航班态势','2'=>'航班列表','3'=>'列车态势','4'=>'列车列表'];
	  	
	    $this->assign('c_type',$c_type);
	    $this->assign('data',$info);
		$this->assign('post',$this->param);

		return view();
	}

	/**
	 * [round_add 添加走马灯设置]
	 */
	function round_add()
	{
		$info = ['c_id'=>'','c_type'=>'','c_font_family'=>'','c_font_size'=>'','c_font_color'=>''];
		if($_POST)
		{
			$info = $this->model->adds($_POST,$this->types);
			parent::out($info,'添加成功！','添加失败!',url('round'));
		}
		$this->assign('info',$info);
		$this->assign('variable',ACTION_NAME);
		$this->assign('c_type',$this->c_type);
		return view();
	}

	/**
	 * [round_edit 编辑走马灯设置]
	 */
	function round_edit()
	{
		$where['c_id'] = $this->param['c_id'];
		$info = $this->model->search($where,$this->types);
		if ($_POST) 
		{
			$data = ['c_type'=>$_POST['c_type'],'c_font_family'=>$_POST['c_font_family'],'c_font_size'=>$_POST['c_font_size'],'c_font_color'=>$_POST['c_font_color']];
			$whr['c_id'] = $_POST['c_id'];
			$info = $this->model->updates($data,$whr,$this->types);
			parent::out($info,'编辑成功！','编辑失败!',url('round'));
		}

		$this->assign('c_type',$this->c_type);
		$this->assign('info',$info);
		$this->assign('variable',ACTION_NAME);
		return view('round_add');
	}

	/**
	 * [round_del 删除走马灯设置]
	 */
	function round_del()
	{
		$where['c_id'] = $this->param['c_id'];
		$info = $this->model->del($where,$this->types);
		parent::out($info,'删除成功！','删除失败!',url('round'));
	}

	/**
	 * [set_languages 语言设置]
	 * @param  $chi 中文
	 * @param  $eng 英文
	 */
	function set_language($chi='',$eng='')
	{
		if($chi&&$eng)
			{
				$c_language = 3;
			}elseif ($eng) {
				$c_language = $eng;
			}else{
				$c_language = $chi;
			}
		return $c_language;
	}

		/**
	 * [set_languages 语言设置]
	 * @param  $chi 中文
	 * @param  $eng 英文
	 */
	function set_isfromsha($out='',$arrive='')
	{
		if($out&&$arrive)
			{
				$c_isfromsha = '2';
			}elseif ($out) {
				$c_isfromsha = '1';
			}else{
				$c_isfromsha = '0';
			}
		return $c_isfromsha;
	}

	//主页设置
	function main()
	{
		//获取确认使用页面
		$whr = ['c_istrue'=>1];
		$result = $this->model->lists('',$whr);
		$c_type = ['0'=>'未选择','1'=>'航班态势','2'=>'航班列表','3'=>'列车态势','4'=>'列车列表'];
		$olds_info =$this->model->search_main();

		if ($_POST) {
			foreach ($_POST['c_screenid'] as $key => $value) {
				$data[$key]['c_screenid'] = $value;
				$data[$key]['c_time'] = date('Y-m-d H:i:s');
			}
			$del = $this->model->del_main();
		
			$res = $this->model->adds_all($data);
			parent::out($res,'添加成功！','添加失败!',url('main'));
		}

		$this->assign('olds_info',$olds_info);
		$this->assign('info',$result);
		$this->assign('c_type',$c_type);
		return view();
	}
}
?>