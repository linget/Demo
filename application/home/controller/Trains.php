<?php
namespace app\home\controller;
use think\Request;
use think\Cookie;
class Trains extends Base
{
    private $language = '';
    private $Train = '';
    function __construct()
    {
        parent::__construct();
        $this->Train = new \app\home\service\Train();
        $this->round_arrs = $this->train_custom();//客户定制当天航班号
    }


    public function index()
    {
       $round_info = '';$round_total = '';
        $where = ['c_type'=>3,'c_istrue'=>1];//显示设置条件
        $whr = ['c_type'=>3];//走马灯条件
        $name = '_trainindex';

        //iPS后台设置
        $temp = get_views($where);
        setcook($name,$temp);
        $this->assign("temp", $temp); 
        $this->isFromSha = Cookie::get('isFromSha_trainindex');
        $this->language = Cookie::get('language_trainindex');
        $data  = ['pages'=>1,'numb'=>12,'isFromSha'=>$this->isFromSha,'language'=>$this->language];

        $arrays = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$this->isFromSha,'language'=>$this->language];
        //接口数据
        $result = $this->Train->traininfo($data,$arrays);
        //$result = $this->Train->local_info($data);
        $info = $result['info'];
        
        $weather = $this->weather();

        //分页数据
        $pages_info  = $result['pages'];

        $current_Page= isset($pages_info['current_Page'])?$pages_info['current_Page']:'1';
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';

        //态势图数据
        $arr = $result['arr'];
        $citys = json_encode($arr['citys']);
        $route_citys = json_encode($arr['json_info']);

      

        //iPS后台走马灯设置--列车
        $round = get_views($whr,ROUND);
        $this->assign("round", $round);
        
        // 走马灯数据
        $rounds = $this->Train->get_round($arrays);
        
        if ($rounds) 
        {
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

        $this->assign('weather',$weather);
        $this->assign('info',$info);
        return $this->fetch();
	}

    // 获取火车分页数据
    function getMore()
    {
        if(!$_POST){ return false;}
        $next = $_POST['current_Page'];
        $language = $_POST['language'];
        $isFromSha =  $_POST['isFromSha'];
        $numb = 12;
        $data = ['pages'=>$next,'numb'=> $numb,'isFromSha'=>$isFromSha,'language'=>$language];
       // $result = $this->Train->traininfo($data);
        $result = $this->Train->local_info($data);
        echo  json_encode($result);
    }


    /**
     * [round_more 走马灯翻页]
     * @return array() 
     */
    function round_more()
    {
        if(!$_POST){ return false;}
        $language = $_POST['language'];
        $isFromSha = $_POST['isFromSha'];

        $data = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$isFromSha,'language'=>$language];
       
        $result = $this->Train->get_round($data);  
        echo  json_encode($result);
    }

    /* 获取客户定制列车号 */
    function train_custom()
    {
      $model = new \app\admin\model\Passenger();
      $result = $model->get_trains();

      $info = [];
      foreach ($result as $key => $value) {
            $info[] = $value['c_number'];
      }

       return $info;
    }


    /**
     * [train_list 列车列表页]
     * @return [type] [description]
     */
    function train_list()
    {
        
        $round_info = '';$round_total = '';
        $numb = 13*3;
        $where = ['c_type'=>4,'c_istrue'=>1];//显示设置条件
        $whr = ['c_type'=>4];//走马灯条件
        $name = '_trainlist';

        //iPS后台设置
        $temp = get_views($where);
        setcook($name,$temp);
        $this->assign("temp", $temp); 
        $this->isFromSha = Cookie::get('isFromSha_trainlist');
        $this->language = Cookie::get('language_trainlist');

        $data = ['pages'=>'1','numb'=>$numb,'isFromSha'=>$this->isFromSha,'language'=>$this->language];
       
        //接口数据
        $result = $this->Train->Train_list($data);
        
        $info = $result['info'];
        //分页数据
        $pages_info  = $result['pages'];
        $current_Page= isset($pages_info['current_Page'])?$pages_info['current_Page']:'1';
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';




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
  
        $numb = 13*3;
        $data = ['pages'=>$next,'numb'=>$numb,'isFromSha'=>$isFromSha,'language'=>$language];
        $result = $this->Train->Train_list($data);
       echo  json_encode($result);
    }

    //获取当天天气信息
    public function weather()
    {
        $model = new \app\home\model\Weather();
        $info = $model->index();
        return $info;
    }

}
