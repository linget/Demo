<?php
namespace app\home\controller;
use think\Controller;
class Offline extends Controller{

    /**
     * [index 获取当天演示数据]
     */
    public function index()
    {
        $send = new \app\lib\org\Send();
        $url = 'http://10.210.11.11/hp/public/index.php/api/offline/getinfo';
        $data = ['time'=>'','language'=>'zh_cn','isFromSha'=>1,'type'=>''];
        $json = json_encode($data);
        $arr = $send->curl_request($url,$json);

        //数组去空
        $info = $arr['result']['zh_cn'];
        foreach ($info as $key => $value) 
        {
            foreach ($value as $k => $v) 
            {
                foreach ($v as $k3 => $v3) 
                 {
                    if(empty($v3['FLIGHT_NO'])||empty($v3['ADDR'])||empty($v3['FROMCITY'])||empty($v3['TOCITY'])||empty($v3['REMARK_XML'])){
                        unset($value[$k]);
                    }
                }
            }
            
        }
        //$savearr = $arr['result']['zh_cn'];
        $savearr = json_encode($info,JSON_UNESCAPED_UNICODE);


        file_put_contents('D:/wamp/www/Demo/public/data.json', $savearr);
        //var_dump($savearr);die;

        return view();
    }

}

?>