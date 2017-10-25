<?php
namespace app\test\controller;
use think\Cookie;
class Index extends Base
{
    function __construct()
    {
       
        parent::__construct();     
        $this->model = new \app\test\model\Index();
        $this->air =new \app\test\service\Air();
        $this->train = new \app\test\service\Train();
    }

    public function index()
    {
        $url = [];
        $result = $this->model->get_val();
        $host = "http://".$_SERVER['HTTP_HOST'];
        
        $model = new \app\admin\model\Release();
        $arr = $model->find_data(null,'c_id desc');
        $content = $arr['c_content'];
        $data = explode('|', $content);
        if (in_array('航班', $data)) 
        {
            $url['1'] = '/Demo/public/index.php/home/airs/index.html';
        }

        if (in_array('图片', $data)) {
            $url['2'] = '/Demo/public/index.php/home/others/index.html';
        }

        if (in_array('列车', $data)) {
            $url['3'] = '/Demo/public/index.php/home/trains/index.html';
        }

        if (in_array('视频', $data)) {
            $url['4'] = '/Demo/public/index.php/home/others/vedio.html';
        }
        if (!empty($url)) 
        {
            $i=1;
            $urls = [];
            foreach ($url as $key => $value) {
                $urls[$i] = $value;
                $i++;
            }
        }else{
            $urls = [
                '1'=>'/Demo/public/index.php/home/airs/index.html',
                '2'=>'/Demo/public/index.php/home/others/index.html',
                '3'=>'/Demo/public/index.php/home/trains/index.html',
                '4'=>'/Demo/public/index.php/home/others/vedio.html'
            ];
        }



        //var_dump($urls);die;

/*        $url = [
        '1'=>'/Demo/public/index.php/home/airs/index.html',
        '2'=>'/Demo/public/index.php/home/others/index.html',
        '3'=>'/Demo/public/index.php/home/trains/index.html',
        '4'=>'/Demo/public/index.php/home/others/vedio.html'*/
        // '4'=>'/Demo/public/index.php/home/others/index/i/4',
        // '5'=>'/Demo/public/index.php/home/airs/index/isFromSha_index/1/language_index/en',
        // '6'=>'/Demo/public/index.php/home/others/index/i/6',
        // '7'=>'/Demo/public/index.php/home/airs/index/isFromSha_index/0/language_index/en'
        //'2'=>'/Demo/public/index.php/home/airs/air_list.html',
        //'3'=>'/Demo/public/index.php/home/Trains/index.html',
        //'4'=>'/Demo/public/index.php/home/Trains/train_list.html',
        
    /*    ];*/
        
        
       /* foreach ($result as $key => $value) {
                $variable_url[] = $url[$value['c_type']];
        }*/

        
        $variable_url = json_encode($urls,JSON_UNESCAPED_UNICODE);
        $this->assign('variable_url',$variable_url);
        $this->assign('url',$urls);

        return view();
	}
        function test(){
            //黄历
  /*      $almanac = new \app\lib\org\Almanac();
        $alm = $almanac->index();
        $dateArr = explode('-',$alm['calendar']);
        $almanceInfo = [];
        $almanceInfo['id'] = $alm['id'];
        $almanceInfo['month'] = $dateArr[0].'年'.$dateArr[1].'月';
        $almanceInfo['day'] = $dateArr[2];
        $almanceInfo['weekday'] = $alm['weekday'];
        $almanceInfo['lunar'] = mb_substr($alm['lunar_calendar'], 0, 3,'utf-8');
        $almanceInfo['days'] = mb_substr($alm['lunar_calendar'], 3, 4,'utf-8');
        $almanceInfo['suit'] = $this->explodeInfo($alm['suit']);
        $almanceInfo['avoid'] = $this->explodeInfo($alm['avoid']);
        $this->assign('almanceInfo', json_encode($almanceInfo)); */
            return view();
        }
        public function websocket() {
            return view();
        }
        public function getsocket() {
            return "555";
        }
        public function style_1() {
            return view();
        }
        public function style_2() {
            return view();
        }
        public function style_3() {
            return view();
        }
        public function style_4 () {
            return view();
        }
        public function infomation() {
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
        $dateArr = explode('-',$alm['calendar']);
        $almanceInfo = [];
        $almanceInfo['id'] = $alm['id'];
        $almanceInfo['month'] = $dateArr[0].'年'.$dateArr[1].'月';
        $almanceInfo['day'] = $dateArr[2];
        $almanceInfo['weekday'] = $alm['weekday'];
        $almanceInfo['lunar'] = mb_substr($alm['lunar_calendar'], 0, 3,'utf-8');
        $almanceInfo['days'] = mb_substr($alm['lunar_calendar'], 3, 4,'utf-8');
        $almanceInfo['suit'] = $this->explodeInfo($alm['suit']);
        $almanceInfo['avoid'] = $this->explodeInfo($alm['avoid']);
        $this->assign('almanceInfo', json_encode($almanceInfo));
       
        //股指
        $sha = new \app\lib\org\Shares();
        $shares = $sha->index();
        $this->assign("variable", $variable);
        $this->assign("now", $now); 
        $this->assign("sha", $shares); 
        $this->assign('weather',$weather);
            $roadBlock = $this->getRoadChart();
            $roadChart =  $this->changeBlock($roadBlock);
//            var_dump($roadChart);die;
//            $roadChart ="test";
            $mapArr = $this->getMapInfo();
           //整合航班高铁
             //接口数据--航班统计
        
  /*      $air_statis = $this->air->search_info();
        $statis_air = $air_statis['info'];
        $statis_airjson = $air_statis['json'];

        //高铁统计
        
        $train_statis = $this->train->search_info();
        $statis_train = $train_statis['info'];
        $statis_trainjson = $train_statis['json'];
        
        $this->assign("statis_air", $statis_air);
        $this->assign("statis_airjson", $statis_airjson);
        $this->assign("statis_train", $statis_train);
        $this->assign("statis_trainjson", $statis_trainjson); */




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







            
            $roadList=$this->getRoadDes($mapArr);
            $eveArr = $this->getMapEvalute($mapArr);
            $this->assign("eveArr", $eveArr);
            $this->assign("roadList", $roadList);
            $this->assign("roadChart", json_encode($roadChart));
            return view();
        }
        /**
         * 市内交通信息
         */
        public function getMapInfo() {
            $urlArr = array(
                'http://restapi.amap.com/v3/traffic/status/circle?key=45d04849c72312edf7288c2b84882777&location=121.31296,31.194018&radius=3000',
                'http://restapi.amap.com/v3/traffic/status/circle?key=45d04849c72312edf7288c2b84882777&location=121.302151,31.189984&radius=3000',
                'http://restapi.amap.com/v3/traffic/status/circle?key=45d04849c72312edf7288c2b84882777&location=121.339766,31.196099&radius=3000',
                'http://restapi.amap.com/v3/traffic/status/circle?key=45d04849c72312edf7288c2b84882777&location=121.320205,31.193935&radius=3000'
            );
            $resArr = array();
            foreach ($urlArr as $key => $value) {
                $resArr[$key] = $this->set_post($value);
            }
            return $resArr;
        }
        /**
         * 交通数据解析，获取道路评价
         * @param type $arr 多条数据数组
         * @return string 交通评级汇总数组
         */
        public function getMapEvalute($arr) {
            $evaArr =array();
            foreach ($arr as $key => $value) {
                $map = json_decode($value,TRUE);
                if($map["infocode"] == 10000) {
                    $evaArr[$key] =$map["trafficinfo"]["evaluation"]["description"];
                } else {
                    $evaArr[$key] = "暂无数据";
                }
            }
            return $evaArr;
        }
        
        /**
         * 道路状况
         * @param type $arr
         */
        public function getRoadDes($arr) {
            $str = "";
            foreach ($arr as $key => $value) {
                $map = json_decode($value,TRUE);
                if($map["infocode"] == 10000) {
                    $str .=$map["trafficinfo"]["description"];
                }
            }
            $strArr = explode("；", str_replace("。", "；", $str));
            $noRepArr = array_unique($strArr);
            $resArr = $this->makeDimension($noRepArr);
            return $resArr;
        }
        
        /**
         * 获得直线图道路拥堵数据
         * @return type
         */
        public function getRoadChart() {
            $roadChart = $this->model->getRoadEva();
            if(empty($roadChart)) {
                $data = [
                    ['c_type' => 'com', 'c_month' => '1', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '2', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '3', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '4', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '5', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '6', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '7', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '8', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '9', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '10', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '11', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '12', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '13', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '14', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '15', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '16', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '17', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '18', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '19', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '20', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '21', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '22', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '23', 'c_value' => null],
                    ['c_type' => 'com', 'c_month' => '0', 'c_value' => null]
                ];
                $this->model->insertRoadEva($data);
            } else {
                $hour = date('G');
                //保证下一个小时是空值
                if($hour == 23) {
                    if(!empty($roadChart[0]['c_value'])) {
                        $this->model->insertRoadEva(['c_id' => $roadChart[0]['c_id'],'c_value' => null]);
                    }
                } else {
                    if(!empty($roadChart[$hour+1]['c_value'])) {
                        $this->model->insertRoadEva(['c_id' => $roadChart[$hour+1]['c_id'],'c_value' => null]);
                    }
                }
                //存储拥堵值
                if(empty($roadChart[$hour]['c_value'])) {
                    $mapArr = $this->getMapInfo();
                    $block =$this->getBlocked($mapArr);
                    $this->model->updateRoadEva($roadChart[$hour]['c_id'],  round($block, 2));
                    $roadChart = $this->model->getRoadEva();
                }
            }
            return $roadChart;
        }
        /**
         * 一维转二维
         * @param type $arr
         * @return type
         */
        public function makeDimension($arr) {
            $newArr = array();
            $count = 0;
            foreach ($arr as $value) {
                if($value != '') {
                     $midArr = explode("：", $value);
                     $secArr = array('road' => $midArr[0],'description' => $midArr[1]);
                     $newArr[$count] = $secArr;
                     $count++;
                }  
            }
            return $newArr;
        }
        
        /**
         * 计算拥堵指数
         * @param type $arr
         * @return type
         */
        public function getBlocked($arr) {
            $sum = 0;
            $count = 0;
            foreach ($arr as $value) {
                $map = json_decode($value,TRUE);
                if($map["infocode"] == 10000) {
                    $sum +=$map["trafficinfo"]["evaluation"]["congested"] +$map["trafficinfo"]["evaluation"]["blocked"];
                    $count++;
                } 
            }
            if($count === 0) {
                return $sum;
            } else {
                return $sum/$count;
            }
        }
        
        /**
         * 获取全歼参数起点
         * @param type $hour
         * @return int
         */
        public function change24($hour) {
            if($hour <5) {
                switch ($hour) {
                    case 4: return 22;
                    case 3: return 21;
                    case 2: return 20;
                    case 1: return 19;
                    case 0: return 18;
                }
            } else {
                return $hour-5;
            }
        }
        
        /**
         * 折线图拥堵指标转换后输出数据
         * @param type $arr
         * @return type
         */
        public function changeBlock($arr) {
            $hour =  date('G');
            $dataH =  $this->change24($hour);
            $date0 = $dataH.'时';
            $date1 = ($dataH+1).'时';
            $date2 = ($dataH+2).'时';
            $date3 = ($dataH+3).'时';
            $date4 = ($dataH+4).'时';
            $date5 = ($dataH+5).'时';
            return [[$date0,$date1,$date2,$date3,$date4,$date5],[round($arr[$dataH]['c_value'],2),round($arr[$dataH+1]['c_value'],2),round($arr[$dataH+2]['c_value'],2),round($arr[$dataH+3]['c_value'],2),round($arr[$dataH+4]['c_value'],2),round($arr[$dataH+5]['c_value'],2)]];
        }
        
         //获取当天天气信息
    public function weather()
    {
        $model = new \app\lib\org\Weather();
        $info = $model->index();
        return $info;
    }
    
    //最多取5个宜或者忌
    public function explodeInfo($info) {
        $arr = explode('.',$info);
        $resArr = array();
        $count = 0;
        foreach ($arr as $value) {
            if($value != '') {
                $resArr[$count] = $value;
                $count++ ;
            }
            if($count > 4) {
                break;
            }
        }
        return $resArr;
    }

    public function set_post($url){
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);
            curl_close($ch);
            return $result;
        }
        
        
        
        //整合部分
         //获取当天天气信息
//    public function weather()
//    {
//        $model = new \app\home\model\Weather();
//        $info = $model->index();
//        return $info;
//    }


    public function get_airInfo()
    {
        //航班延误数据
        $air_info = $this->air->getInfo();

        return json_encode($air_info,JSON_UNESCAPED_UNICODE);
    }


    public function get_trainInfo()
    {
        //高铁延误数据
        $train_info = $this->train->getInfo();
        
        return json_encode($train_info,JSON_UNESCAPED_UNICODE);
    }



    public function header1(){
        $air_statis = $this->air->search_info();
        $statis_air = $air_statis['info'];
        $statis_airjson = $air_statis['json'];
        $this->assign("statis_air", $statis_air);
        $this->assign("statis_airjson", $statis_airjson);
        return view();
    }

    public function header2(){
        $train_statis = $this->train->search_info();
        $statis_train = $train_statis['info'];
        $statis_trainjson = $train_statis['json'];
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
}
