<?php
namespace app\home\controller;
use think\Request;
use think\Cookie;
class Airs extends Base
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

        $this->isFromSha = Request::instance()->param('isFromSha_index');
        $this->language = Request::instance()->param('language_index');
        if(empty($this->isFromSha)||empty($this->language))
        {

            $this->isFromSha = Cookie::get('isFromSha_index');
            $this->language = Cookie::get('language_index');
        }
        

    	$data  = ['pages'=>1,'numb'=>12,'isFromSha'=>$this->isFromSha,'language'=>$this->language];
        $arrays = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$this->isFromSha ,'language'=>$this->language];
        //接口数据
        
        $result = $this->Air->local_info($data);
    	//$result = $this->Air->airinfo($data);


        $info = $result['info'];
        //分页数据
        $pages_info  = $result['pages'];

        $current_Page= isset($pages_info['current_Page'])?$pages_info['current_Page']:'1';
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';
        //态势图数据
        $arr = $result['arr'];
        
        $citys = json_encode($arr['citys']);
        $route_citys = json_encode($arr['json_info']);
        

        //iPS后台走马灯设置
        $round = get_views($whr,ROUND);
        $this->assign("round", $round);
        
        // 走马灯数据
        $rounds = $this->Air->get_round($arrays);

        if($rounds){
            $round_info = $rounds['result']; //数据 
            $round_total=  $rounds['total']; //分页信息
        }


        $round_page = isset($round_total['current_Page'])?$round_total['current_Page']:'1';
        $round_Page_count = isset($round_total['count_Page'])?$round_total['count_Page']:'1';

        $this->assign("round_info", $round_info);
        $this->assign("round_page", $round_page);
        $this->assign("round_Page_count", $round_Page_count);

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);

        $this->assign('citys',$citys);
        $this->assign('route_citys',$route_citys);

        $this->assign('count_Page',$count_Page);
        $this->assign('current_Page',$current_Page);

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
        $language = $_POST['language'];
        $isFromSha = $_POST['isFromSha'];
        
        $numb = 12;
        $data = ['arr'=>$this->round_arrs,'pages'=>$next,'numb'=>$numb,'isFromSha'=>$isFromSha,'language'=>$language];/*$language*/
        $result = $this->Air->local_info($data);
        //$result = $this->Air->airmore($data);
        echo  json_encode($result);  
    }

    /**
     * [air_list 航班列表页]
     * @return [type] [description]
     */
    function air_list()
    {
        $round_info = '';$round_total = '';
    	$where = ['c_type'=>2,'c_istrue'=>1];//显示设置条件
        $whr = ['c_type'=>2];//走马灯条件
        $name = '_airlist';
        //iPS后台设置
        $temp = $this->get_views($where);
        //获取设置并设置cookie
        setcook($name,$temp);
        $this->assign("temp", $temp);
        
        $this->isFromSha = Cookie::get('isFromSha_airlist');
        $this->language = Cookie::get('language_airlist');
        $numb = 39;
       
        $data = ['pages'=>'1','numb'=>$numb,'isFromSha'=>$this->isFromSha,'language'=>$this->language];

        //接口数据
        $result = $this->Air->air_list($data);
        $info = $result['info'];

        //分页数据
        $pages_info  = $result['pages'];
        $current_Page= isset($pages_info['current_Page'])?$pages_info['current_Page']:'1';
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';


        //iPS后台走马灯设置
        $round = $this->get_views($whr,ROUND);
        $this->assign("round", $round);


        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);

        $this->assign('count_Page',$count_Page);
        $this->assign('current_Page',$current_Page);

        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * [more_list 列表数据翻页]
     * @return [type] [description]
     */
    function morelist()
    {
        if(!$_POST){
            return false;
        }
        $next = $_POST['current_Page'];
        $language =$_POST['language'];
        $isFromSha =$_POST['isFromSha'];

        $numb = 39;
        $data = ['arr'=>$this->round_arrs,'pages'=>$next,'numb'=>$numb,'isFromSha'=>$isFromSha,'language'=>$language];
        $result = $this->Air->air_list($data);
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

    /**
     * [round_more 走马灯翻页]
     * @return array() 
     */
    function round_more()
    {
        if(!$_POST){
            return false;
        }
        $language = $_POST['language'];
        $isFromSha = $_POST['isFromSha'];
        $data = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$isFromSha,'language'=>$language];
       
        $result = $this->Air->get_round($data);  
        echo  json_encode($result);
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

    //获取航班坐标数据
    function  coordinate()
    {
        $file_name = "city.json";
        $path = $_SERVER['DOCUMENT_ROOT'].'/public/js/'.$file_name;
        echo $path;
        if (file_exists($path)) 
        {
           $f = fopen($path, "r");
           $str = fread($f, filesize($path));
           //  {xxx:[],xxx:[],xxx:[]}
           
           dump($str);
        }else{
           
        }
        die;
    }

    //获取高铁坐标数据
        //航班态势页
    public function index_scatter()
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

        $data  = ['pages'=>'1','numb'=>'12','isFromSha'=>$this->isFromSha,'language'=>$this->language];
        $arrays = ['arr'=>$this->round_arrs,'isFromSha'=>$this->isFromSha ,'language'=>$this->language];
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
        

        //iPS后台走马灯设置
        $round = get_views($whr,ROUND);
        $this->assign("round", $round);
        
        // 走马灯数据
        $rounds = $this->Air->get_round($arrays); 

        if($rounds){
            $round_info = $rounds['result']; //数据 
            $round_total=  $rounds['total']; //分页信息
        }
        $round_page = isset($round_total['current_Page'])?$round_total['current_Page']:'1';
        $round_Page_count = isset($round_total['count_Page'])?$round_total['count_Page']:'1';
        

        $this->assign("round_info", $round_info);
        $this->assign("round_page", $round_page);
        $this->assign("round_Page_count", $round_Page_count);

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);

        $this->assign('citys',$citys);
        $this->assign('route_citys',$route_citys);

        $this->assign('count_Page',$count_Page);
        $this->assign('current_Page',$current_Page);

        $this->assign('info',$info);
        return $this->fetch();
    }

        //获取高铁坐标数据
        //航班态势页
    public function index_heart()
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

        $data  = ['pages'=>'1','numb'=>'12','isFromSha'=>$this->isFromSha,'language'=>$this->language];
        $arrays = ['arr'=>$this->round_arrs,'isFromSha'=>$this->isFromSha ,'language'=>$this->language];
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
        

        //iPS后台走马灯设置
        $round = get_views($whr,ROUND);
        $this->assign("round", $round);
        
        // 走马灯数据
        $rounds = $this->Air->get_round($arrays); 

        if($rounds){
            $round_info = $rounds['result']; //数据 
            $round_total=  $rounds['total']; //分页信息
        }
        $round_page = isset($round_total['current_Page'])?$round_total['current_Page']:'1';
        $round_Page_count = isset($round_total['count_Page'])?$round_total['count_Page']:'1';
        

        $this->assign("round_info", $round_info);
        $this->assign("round_page", $round_page);
        $this->assign("round_Page_count", $round_Page_count);

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);

        $this->assign('citys',$citys);
        $this->assign('route_citys',$route_citys);

        $this->assign('count_Page',$count_Page);
        $this->assign('current_Page',$current_Page);

        $this->assign('info',$info);
        return $this->fetch();
    }
    function test(){
        $data  = ['pages'=>'1','numb'=>'12','isFromSha'=>1,'language'=>'zh_cn'];
        $result = $this->Air->local_info($data);
       // var_dump($result);
       die;
    }
}

