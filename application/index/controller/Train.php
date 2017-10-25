<?php
namespace app\index\controller;

class Train extends Base
{
    protected $rule = [
                ['start','number|min:0','start must be number|Start minimum from 0!'],
                ['end','number|min:1','end must be number!|End minimum from 1!'],
                ['isFromSha','number|between:0,1','IsFromSha must be number!|IsFromSha must be between 0~1!'],
                 ['language','in:zh_cn,en,zh-cn','Language must be Chinese or English!'],
                ['trainNo','alphaNum','TrainNo must be alphaNum!'],
                ['startstation','alpha','Startstation Address code error!'],
                ['terninalstation','alpha','Terninalstation Address code error!'],
                ['date','date','Date error!']
            ];
//    public function __construct(){
//        parent::__construct();
//    }

    /**
     * 获取列车信息(从上海虹桥)
     * @return 1小时前4小时内出发列车
    */
    public function getTrainInfo(){
        $params = json_decode(file_get_contents("php://input"),true);
        //航班参数校验
        $verf = parent::verify($this->rule,$params);
        if ($verf) { return $verf;}

        $start = $params['start'] ? $params['start'] :'0';
        $end = $params['end'] ? $params['end'] :'24';
        $isFromSha = $params['isFromSha'] ? $params['isFromSha'] :'';//默认出发
        $language =isset($params['language']) ? $params['language'] :'zh_cn';

        $trainserver = new \app\index\service\Air();
       

        $res = $trainserver->TraininfosAll($start,$end,$isFromSha,$language);
        if ($res) {
            $this->data['code'] = '1000';
            $this->data['msg'] = '__OK';
            $this->data['result'] = $res;
            
        }else{
                $this->data['code'] = '1003';
                $this->data['msg'] = 'Internal error !';
                $this->data['result'] = '';
                
            }
        return json_encode($this->data,true);
     }

     /**
     * 条件查询车次
     * @param string train_no 车号
     * @param string startstation 始发地
     * @param string terninalstation 目的地
     * @param string date 时间
     * @param int  start,end数据区间
     * @return array()
     */
    public function Trainsearch(){
            $params = json_decode(file_get_contents("php://input"),true);

            //航班参数校验
            $verf = parent::verify($this->rule,$params);
            if ($verf) { return $verf;}

            $train_no = $params['trainNo'] ? $params['trainNo'] :'';
            $startstation = $params['startstation'] ? $params['startstation'] :'';
            $terninalstation = $params['terninalstation'] ? $params['terninalstation'] :'';
            $date = $params['date'] ? $params['date']:date('Y-m-d',time());
            $start = $params['start'] ? $params['start'] :'0';
            $end = $params['end'] ? $params['end'] :'24';
            $language = isset($params['language']) ? $params['language'] :'zh_cn';

            $trainserver = new \app\index\service\Air();
            $res = $trainserver->getTrainSearch($train_no,$startstation,$terninalstation,$date,$start,$end,$language);
            if ($res) {
                $this->data['code'] = '1000';
                $this->data['msg'] = '__OK';
                $this->data['result'] = $res;
               
            }else{
                $this->data['code'] = '1003';
                $this->data['msg'] = 'Internal error !';
                $this->data['result'] = '';
                
            }
        return json_encode($this->data,true);
    }
    /*
     * Mysql库查询数据
     */
    public function getTrain()
    {
        $params = json_decode(file_get_contents("php://input"),true);
        $fromCity=$params['fromCity']?$params['fromCity']:'';
        $toCity=$params['toCity']?$params['toCity']:'';
        $trainNo=$params['trainNo']?$params['trainNo']:'';
        $tDate=$params['tDate']?$params['tDate']:date('Y-m-d',strtotime("-1 day"));
        $obj=new \app\index\service\Air();
        $res=$obj->getTrain($fromCity, $toCity, $trainNo, $tDate);

        if ($res) {
                $this->data['code'] = '1000';
                $this->data['msg'] = '__OK';
                $this->data['result'] = $res;
            }else{
                $this->data['code'] = '1003';
                $this->data['msg'] = 'Internal error !';
                $this->data['result'] = '';    
            }
        return json_encode($this->data,true);
    }
    
    public function test()
    {
         $obj=new \app\index\service\Air();
        $res=$obj->getTrain('杭州东', '北京', 'G42', '2017-3-7');
        halt($res);
    }
 }
?>