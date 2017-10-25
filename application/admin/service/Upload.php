<?php
namespace app\admin\service;
use think\Session;
use think\Db;
/**
* 旅客历史纪录
*/
class Upload
{
	function __construct()
	{
		include 'PHPExcel.php';
		//include 'PHPExcel/Writer/Excel2007.php';
		//或者include 'PHPExcel/Writer/Excel5.php'; 用于输出.xls的
		include 'PHPExcel/Writer/Excel5.php';
		$this->model = new \app\admin\model\Passenger();
	}

	/**
	 * [index description]
	 * @return [type] [description]
	 */
	function index()
	{
		//获取目标数据
		$data = $this->get_data();
		$fileName = '旅客历史记录表';
		/*设置表头数据*/
		$header = ['类型','编号','提醒时间'];

		$this->getExcel($fileName,$header,$data);
	}

	/* 生成xls */
	function getExcel($fileName,$header,$data)
	{	
		//创建exl
		$objPHPExcel = new \PHPExcel();
		$column = 1;
		$fileName .= date('Y_m_d').'.xls';
		if(!$data){ return false;}
		//设置创建人
		$objPHPExcel->getProperties()->setCreator(session::get("userwy.c_fullname"));
		//设置标题
		$objPHPExcel->getProperties()->setSubject("旅客历史记录-".date('Y-m-d'));

		/*设置文本对齐方式*/
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

		//设置列表样式
		$objActSheet=$objPHPExcel->getActiveSheet();
		$objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽

		//设置边框
		$sharedStyle1=new \PHPExcel_Style();
		$sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));

		//设置表头
		$letter = ['A','B','C','D','E','F','G','H','I'];

		/*填充表格表头*/
		for($i = 0;$i < count($header);$i++) {
			$objPHPExcel->getActiveSheet()->getStyle("$letter[$i]$column")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle("$letter[$i]$column")->getFill()->getStartColor()->setARGB('FF4F81BD');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("$letter[$i]$column",$header[$i]);
		}
		//填充数据
		foreach ($data as $key => $value) {
			$key += 2;
			if($value['c_type'] ==1) 
			{ 
				$type = '航班';
			}else{
				$type = '列车';
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$key,$type);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$key,$value['c_number']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$key,$value['c_time']);
		}

		
		$write = new \PHPExcel_Writer_Excel5($objPHPExcel);
		header("Pragma: public");
		header("Expires: 0");
		ob_end_clean();//清除缓冲区,避免乱码
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition:attachment;filename=\"$fileName\"");
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}

	function get_data()
	{
		$date = date('Y-m-d',time());
		$where['c_time'] = ['elt',$date];//提醒时间当天前
		$info =	$this->model->search($where);
		return $info;
	}
}
?>