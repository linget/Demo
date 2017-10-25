<?php
namespace app\home\controller;
use think\Request;
use think\Cookie;
class Test extends Base
{
    private $language = '';
    private $Air = '';
    private $isFromSha = '';
    function __construct()
    {
        parent::__construct();
        $this->param = Request::instance()->param();
        $this->Air = new \app\home\service\Air();
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
        //天气
        $weather = $this->weather();
        //var_dump($weather);die;

        //时间
        $now = date('H:i');//时间初始化
        if (date('H')<=12) {
            $variable = 'AM';
        }else{
            $variable = 'PM';
        }

        //黄历
        $almanac = new \app\lib\org\Almanac();
        $alm = $almanac->index();

        //股指
        $sha = new \app\lib\org\Shares();
        $shares = $sha->index();
        //var_dump($shares);die;


        $data  = ['pages'=>1,'numb'=>12,'isFromSha'=>$this->isFromSha,'language'=>$this->language];
        $arrays = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>'','isFromSha'=>$this->isFromSha ,'language'=>$this->language];
        //接口数据$this->isFromSha
        
        //$result = $this->Air->local_info($data);
        $result = $this->Air->airinfo($data, $arrays);


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

   
        
        $this->assign("variable", $variable);
        $this->assign("now", $now); 

        $this->assign("sha", $shares); 

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
        $arrays = ['arr'=>$this->round_arrs,'pages'=>'','numb'=>$numb,'isFromSha'=>$isFromSha ,'language'=>$language];

        //$result = $this->Air->local_info($data);
        $result = $this->Air->airmore($data,$arrays);
        echo  json_encode($result,JSON_UNESCAPED_UNICODE);  
    }


    //获取当天天气信息
    public function weather()
    {
        $model = new \app\home\model\Weather();
        $info = $model->index();
        return $info;
    }
}

