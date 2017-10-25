<?php
namespace app\weiwin\service;
use think\Cache;
/**
* 微信凭据获取类
*/
class Weshare
{
    private $appId = "wxb9834f1df9c98901";
    private $appSecret = "8347d5c6cce014b3f43b9140ee2a2446";
    
    //获取access_token并缓存,1h更新一次
    function get_access_token(){
        $token = Cache::get('access_token');
        if(!$token)
        {
            $Send = new \app\lib\org\Send();
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $url = sprintf($url,$this->appId,$this->appSecret);
            $res = $Send->curl_request($url);
            $token = $res['access_token'];
            Cache::set('access_token',$token,3600);         
        }
         return $token;
    }
     /* 二维码 */
  function getTicket($token,$data)
  {
     $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
     $result = $this->curl_request($url,$data);
     return $result;
  }

  /* 网页开发 */
  function get_jsapi_ticket($token)
  {
     $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
     $result = $this->curl_request($url,'',$type=1);

     return $result;
  }


  /* sdk签名 */
  function get_sign()
  {

  }

  /**
   * [curl请求支持http~https]
   * @param  [type] $url  地址
   * @param  string $data 数据
   * @param  http $type 类型 http/https
   * @return array()       
   */
  public function curl_request($url,$data = '',$type = ''){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 500);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      if($type){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      }

      if (!$data) 
      {
        $output = curl_exec($ch);
          if (!$output) 
          {
            $res = file_get_contents($url);
          }      
      }else
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
      }

    curl_close($ch);
   
    return json_decode($output,true);
  }
}
?>