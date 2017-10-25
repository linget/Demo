<?php
namespace app\wechat\controller;
use think\Controller;
class Users extends Controller
{
    //创建标签
    public function setTag()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=$token";
        $data='{
             "tag" : {
             "name" : "朗虹酒店"                         //返回值100
             }
             }';
        $res=  $this->curl_post($url, $data);
        print_r($res);
    }
    //获得标签组
    public function getTag()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/tags/get?access_token=$token";
        $res=  $this->curl_post($url);
        print_r($res);
    }
    //移动用户到标签组
    public function moveOpenid()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=$token";
        $data='{ "openid_list": [ "o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA" ], "tagid":100 }';
        $res=  $this->curl_post($url, $data);
        print_r($res);
    }
    //用户所在的标签组
    public function tagOpenid()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=$token";
        $data='{ "openid":"o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA" }';
        $res=  $this->curl_post($url, $data);
        print_r($res);
    }
    //获取用户基本信息
    public function getInfo()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA&lang=zh_CN";
        $res=  $this->curl_get($url);
        print_r($res);
    }
    //二维表配套的ticket  临时场景
    public function getTicket()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $data='{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
        $res=  $this->curl_post($url, $data);
        $cont=  json_decode($res,true);
        print_r($cont);
    }
    //ticket换取二维码
    public function getCode1()
    {
        $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQFA8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNnBCc0kzRmdhSF8xRmtmOTFvMUgAAgTUVIBYAwSAOgkA";
        return $this->redirect($url);
    }
    //二维码存到ercode文件夹内
     public function getCode()
     {
         $str=  urlencode("gQFA8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNnBCc0kzRmdhSF8xRmtmOTFvMUgAAgTUVIBYAwSAOgkA");
         $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$str";
         $img=  $this->curl_down($url);
         print_r($img['header']);
         $rd=  time();
         $filename="ercode/".$rd.".jpg";
         $local_file=  fopen($filename, 'w');
         if(false !==  fwrite($local_file, $img["body"])){
             fclose($local_file);
         }
     }
    //post方式
    private function curl_post($url,$data=null)
    {
        $ch=  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        } 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    //get方式
    private function curl_get($url)
    {
        $ch=  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    //获取下载信息
    private function curl_down($url)
    {
         $ch=  curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_NOBODY, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $pacakage=curl_exec($ch);
        $httpinfo=  curl_getinfo($ch);
        curl_close($ch);
        return array_merge(array('body'=>$pacakage),array('header'=>$httpinfo));
        
    }
}

