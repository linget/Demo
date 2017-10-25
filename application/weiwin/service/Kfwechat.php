<?php
namespace app\weiwin\service;
/**
* 微信客服消息
*/
class Kfwechat
{
    
    /**
     * [send_msg 发送客服消息--限48h]
     * @param  [type] $data [消息实体]
     * @return [type]       ok
     */
    function send_msg($data = [])
    {
        if(!$data)
        {
             $data = [
            "touser"=>"o6ZCrv2Y5hPpziTa_NSR17bRWio0",
            "msgtype"=>"text",
            "text"=>[
            "content"=>"Hello World"
                ]
            ];
        }
        $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $Weshare = new \app\weiwin\service\Weshare();
        $access_token = $Weshare->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;

        $send = new \app\lib\org\Send();
        $res = $send->curl_request($url,$data);
        if ($res['errmsg'] == 'ok') 
        {
            return true;
        }else{
            return false;
        }
    }


      /* 长连接转短链接接口 */
      function long2short($url)
      {
        $api_url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=%s';
        $data = ['action'=>'long2short','long_url'=>$url];
        $json = json_encode($data);

        $Weshare = new \app\weiwin\service\Weshare();
        $access_token = $Weshare->get_access_token();
        $api_url = sprintf($api_url,$access_token);
        $res = $Weshare->curl_request($api_url,$json);
        return $res;
      }
}
?>