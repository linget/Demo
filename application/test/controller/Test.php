<?php
namespace app\test\controller;
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
        $this->air =new \app\test\service\Air();
        $this->train = new \app\test\service\Train();
    }

    //航班态势页
    public function index()
    {
        $isFromSha = 1;//默认从上海虹桥出发
        
        Cookie::set('changetype','air');
        Cookie::set('isFromSha',$isFromSha);
        //航班延误数据
        $info = $this->air->getInfo();
        $air_info = isset($info['info'][$isFromSha])?$info['info'][$isFromSha]:'';
        

         //态势数据
        $topcity = isset($info['topcity'][$isFromSha])?$info['topcity'][$isFromSha]:'';

        //高铁数据
        $traininfo = $this->train->getInfo();

        $train_info = isset($traininfo['info'][$isFromSha])?$traininfo['info'][$isFromSha]:'';
        //态势数据

        $topcity_train = isset($traininfo['topcity'][$isFromSha])?$traininfo['topcity'][$isFromSha]:'';
        
        //天气
        $weather = $this->weather();
        

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


        //接口数据--航班统计
        $air_statis = $this->air->search_info();
        $statis_air = $air_statis['info'];
        $statis_airjson = json_encode($air_statis['json'],JSON_UNESCAPED_UNICODE);

        //高铁统计
        
        $train_statis = $this->train->search_info();
        $statis_train = $train_statis['info'];
        $statis_trainjson = json_encode($train_statis['json'],JSON_UNESCAPED_UNICODE);
        //$result = $this->Air->local_info($data);

        
        // 走马灯数据
        
        $this->assign("topcity", json_encode($topcity,JSON_UNESCAPED_UNICODE));
        $this->assign("air_info", $air_info);

        $this->assign("statis_air", $statis_air);
        $this->assign("statis_airjson", $statis_airjson);
        $this->assign("statis_train", $statis_train);
        $this->assign("statis_trainjson", $statis_trainjson);

        
        $this->assign("variable", $variable);
        $this->assign("now", $now); 
        $this->assign("sha", $shares); 
        $this->assign('weather',$weather);

        return $this->fetch();
    }

    //获取当天天气信息
    public function weather()
    {
        $model = new \app\home\model\Weather();
        $info = $model->index();
        return $info;
    }

    //航班高铁数据接口
    public function get_airInfo()
    {
        //航班延误数据
        $air_info = $this->air->getInfo();

        return $air_info;
    }
    public function get_trainInfo()
    {
        //高铁延误数据
        $train_info = $this->train->getInfo();
        
        return $train_info;
    }

    //航班高铁统计接口
    public function statis_air(){
        $air_statis = $this->air->search_info();
         return $air_statis;
    }

    public function statis_train(){
        $train_statis = $this->train->search_info();
         return $train_statis;
    }





















    public function header1(){
        $air_statis = $this->air->search_info();
        $statis_air = $air_statis['info'];
        $statis_airjson = json_encode($air_statis['json'],JSON_UNESCAPED_UNICODE);

        $this->assign("statis_air", $statis_air);
        $this->assign("statis_airjson", $statis_airjson);
        return $this->fetch();
    }

    public function header2(){
        $train_statis = $this->train->search_info();
        $statis_train = $train_statis['info'];
        $statis_trainjson = json_encode($train_statis['json'],JSON_UNESCAPED_UNICODE);
        

        $this->assign("statis_train", $statis_train);
        $this->assign("statis_trainjson", $statis_trainjson);
        return view();
    }

    public function map()
    {
        //航班延误数据
        $info = $this->air->getInfo();

        //态势数据
        $topcity = $info['topcity'];
        // 走马灯数据
        
        $this->assign("topcity", json_encode($topcity,JSON_UNESCAPED_UNICODE));

        return $this->fetch();
    }

    public function lists()
    {
          //航班延误数据
        $info = $this->air->getInfo();
        $air_info = $info['info'];
        $this->assign("air_info", $air_info);
        return $this->fetch();
    }

    public function infomation(){
        //航班延误数据
        $info = $this->air->getInfo();
        $air_info = $info['info'];

        //态势数据
        $topcity = $info['topcity'];

        //天气
        $weather = $this->weather();
        

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


        //接口数据--航班统计
        
        $air_statis = $this->air->search_info();
        $statis_air = $air_statis['info'];
        $statis_airjson = json_encode($air_statis['json'],JSON_UNESCAPED_UNICODE);
       
        //高铁统计
        
        $train_statis = $this->train->search_info();
        $statis_train = $train_statis['info'];
        $statis_trainjson = $train_statis['json'];
        $statis_trainjson = json_encode($train_statis['json'],JSON_UNESCAPED_UNICODE);
        //$result = $this->Air->local_info($data);


       
        //iPS后台走马灯设置

        
        // 走马灯数据
        
        $this->assign("topcity", json_encode($topcity,JSON_UNESCAPED_UNICODE));
        $this->assign("air_info", $air_info);

        $this->assign("statis_air", $statis_air);
        $this->assign("statis_airjson", $statis_airjson);
        $this->assign("statis_train", $statis_train);
        $this->assign("statis_trainjson", $statis_trainjson);

        
        $this->assign("variable", $variable);
        $this->assign("now", $now); 
        $this->assign("sha", $shares); 
        $this->assign('weather',$weather);

        return $this->fetch();
    }
}


