<?php
namespace app\index\controller;

class Air extends Base
{

    protected $rule = [
                ['start','number|min:0','start must be number|Start minimum from 0!'],
                ['end','number|min:1','end must be number!|End minimum from 1!'],
                ['isFromSha','number|between:0,1','IsFromSha must be number!|IsFromSha must be between 0~1!'],
                ['language','in:zh_cn,en,zh-cn','Language must be Chinese or English!'],
                ['flightNo','alphaNum','FlightNo must be alphaNum!'],
                ['startstation','alpha','Startstation Address code error!'],
                ['terninalstation','alpha','Terninalstation Address code error!'],
                ['date','date','Date error!']
            ];
	public function _initialize()
   {

       parent::_initialize();
   }


    /**
     * 航班信息接口
     * @return 当前时间1小时前4小时内起飞航班
     */
    public function getAirInfo(){           
            $params = json_decode(file_get_contents("php://input"),true);
           //航班参数校验
            $verf = parent::verify($this->rule,$params);
            if ($verf) { return $verf;}

            $start = $params['start'] ? $params['start'] :'0';
            $end = $params['end'] ? $params['end'] :'24';
            $isFromSha = $params['isFromSha'] ? $params['isFromSha'] :'';
            $language = isset($params['language']) ? $params['language'] :'zh_cn';


            $Service = new \app\index\service\Air();
            $res = $Service->AirinfosAll($start,$end,$isFromSha,$language);
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
     * 条件查询航班信息
     * @param string flightNo 航班号
     * @param string startstation 起飞地点
     * @param string terninalstation 降落地点
     * @param string date 运行日
     * @return array()
     */
    public function AirSearch(){
        $params = json_decode(file_get_contents("php://input"),true);

        // $params = ['flightNo'=>'','startstation'=>'','terninalstation'=>'','start'=>1,'end'=>2,'isFromSha'=>1,'language'=>'en','date'=>'2012-10-12'];
        //航班参数校验
        $verf = parent::verify($this->rule,$params);
        if ($verf) { return $verf;}

        $flightNo = $params['flightNo'] ? $params['flightNo'] :'';
        $startstation = $params['startstation'] ? $params['startstation'] :'';
        $terninalstation = $params['terninalstation'] ? $params['terninalstation'] :'';
        $date = $params['date'] ? $params['date'] :date('Y-m-d',time());//默认当天
        $start = $params['start'] ? $params['start'] :'0';
        $end = $params['end'] ? $params['end'] :'24';
        $language = isset($params['language']) ? $params['language'] :'zh_cn';

        $SerchServer = new \app\index\service\Air();
        $res = $SerchServer->getAirSearch($flightNo, $startstation,$terninalstation,$date,$start,$end,$language);
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
     * 查询mysql数据库数据
     */
    public function getFlight()
    {
        $params=  json_decode(file_get_contents("php://input"),true);
        $fromCity=$params['fromCity'] ? $params['fromCity']:'';
        $toCity=$params['toCity'] ? $params['toCity']:'';
        $flightNo=$params['flightNo'] ? $params['flightNo']:'';
        $fDate=$params['fDate'] ? $params['fDate']:date('Y-m-d',time());
        $objServer=new \app\index\service\Air();
        $res=$objServer->getFlightList($fromCity, $toCity, $flightNo, $fDate);
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
}