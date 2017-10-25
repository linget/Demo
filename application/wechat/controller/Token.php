<?php
namespace app\wechat\controller;
use think\Log;
class Token
{
    //获取微信基础access_token
    public function gettoken()
    {
        if(cache('access_token')){
            $token=cache('access_token');
        }  else {
            $appid=  config('appid');
            $appsecret=config('appsecret');
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $res=  $this->curl_post($url);
            $arr=  json_decode($res, true);
            $token=$arr['access_token'];
            cache('access_token', $token, 6500);
        }
        return $token;
    }
    //页面授权获取openid
    public function getOpenid($code)
    {
        $appid=  config('appid');
        $appsecret=config('appsecret');
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code ";
        $res=  $this->curl_post($url);
        $arr=  json_decode($res,true);
        if($arr){
            $openid=$arr['openid'];
        }else{
            $openid='';
        }
        return $openid;
    }
    public function getSignPackage() 
   {
    $appid=  config('appid');
    $jsapiTicket = $this->getJsApiTicket();
    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);
    $signPackage = array(
      "appid"     => $appid,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }
    //生成签名的随机串
    private function createNonceStr($length = 16) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
        return $str;
  }
  //获得jsapi_ticket
  private function getJsApiTicket() 
   {
      if(cache('jsapi_ticket')){
            $jsapi_ticket=cache('jsapi_ticket');
        }else{
            $access_token= $this->gettoken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$access_token";
            $res=  $this->curl_post($url);
            $arr=  json_decode($res, true);
            $jsapi_ticket=$arr['ticket'];
            cache('jsapi_ticket', $jsapi_ticket, '6500');
        }
      return $jsapi_ticket;
  }
    private function curl_post($url)
    {
        $ch=  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=  curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
}

