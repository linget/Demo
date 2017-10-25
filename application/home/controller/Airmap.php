<?php
namespace app\home\controller;
use think\Request;
use think\Cookie;
class Airmap extends Base
{
    private $language = '';
    private $Air = '';
    private $isFromSha = '';
    function __construct()
    {
        parent::__construct();
        $this->param = Request::instance()->param();
        $this->Air = new \app\home\service\Air();
        $this->round_arrs = $this->air_custom();//客户定制当天航班号
    }

    //航班态势页
    public function index()
    {
        $round_info = '';$round_total = '';
        $where = ['c_type'=>1,'c_istrue'=>1];//显示设置条件
        $whr = ['c_type'=>1];//走马灯条件
        $name = '_index';
        //iPS后台设置
        $temp = get_views($where);
        //获取设置并设置cookie
        setcook($name,$temp);
        $this->assign("temp", $temp); 
        
        $this->isFromSha = Cookie::get('isFromSha_index');
        $this->language = Cookie::get('language_index');


    	$data  = ['pages'=>'1','numb'=>'16','isFromSha'=>$this->isFromSha,'language'=>$this->language];


        $arrays = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$this->isFromSha ,'language'=>$this->language];
        //接口数据
    	$result = $this->Air->airinfo($data);
        $info = $result['info'];
        
        //分页数据
        $pages_info  = $result['pages'];

        $current_Page= isset($pages_info['current_Page'])?$pages_info['current_Page']:'1';
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';
       
        //态势图数据
        $arr = $result['arr'];
        
        $citys = json_encode($arr['citys']);
        $route_citys = json_encode($arr['json_info']);
        $effect = json_encode($arr['effect'],JSON_UNESCAPED_UNICODE);
        
        //时间
        $time = date('H:i',time());

        

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);

        $this->assign('citys',$citys);
        $this->assign('route_citys',$route_citys);
         $this->assign('effect',$effect);

        $this->assign('count_Page',$count_Page);
        $this->assign('current_Page',$current_Page);

        $this->assign('time',$time);
    	$this->assign('info',$info);
    	return $this->fetch();
	}

    // 获取航班分页数据
    function getMore()
    {
        if(!$_POST){
            return false;
        }
        $next = $_POST['current_Page'];
        $language =$_POST['language'];
        $isFromSha =$_POST['isFromSha'];
        

        $numb = '16';
        $data = ['arr'=>$this->round_arrs,'pages'=>$next,'numb'=>$numb,'isFromSha'=>$isFromSha,'language'=>$language];/*$language*/
       
        $result = $this->Air->airmore($data); 

        echo  json_encode($result);
        
    }


    /**
     * [get_views 获取后台IPS设置]
     * @param $where c_type 1/2 ,航班/火车
     * @param $type 0/1,0为显示设置，1为走马灯样式
     */
    function get_views($where,$type='')
    {
        $model = new \app\admin\model\Screen();
        $arr = $model->search($where,$type);
        return $arr;
    }


    /* 获取客户定制航班号 */
    function air_custom()
    {
      $model = new \app\admin\model\Passenger();
      $info = $model->get_airs();
      $arr = [];
      foreach ($info as $key => $value) {
          $arr[] = $value['c_number'];
      }
      return $arr;
    }


}

