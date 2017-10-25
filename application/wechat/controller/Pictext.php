<?php
namespace app\wechat\controller;
use think\Log;
class Pictext
{
    //获取素材列表media_id
    public function getIdList()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$token";
        $data='{
            "type":"news",
            "offset":"0",
            "count":"8"
           }';
        $res=  $this->curl_post($url,$data);
        $content=json_decode($res, true);
        $list=array();
        foreach ($content['item'] as $value)
        {
            $list[]=$value['media_id'];
        }
        print_r($list);die;
    }
    //获取具体图文信息
    public function getInfo()
    {
        $media_id="kxOXXdfW2eS7et5pdJxHCS9jyRrBN4roICh6WfHQL4w";
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=$token";
        $data='{"media_id":"'.$media_id.'"}';
        $res=  $this->curl_post($url, $data);
         $content=json_decode($res, true);
         print_r($content);die;
    }
    //长链接转为短连接
    public function getShortUrl()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/shorturl?access_token=$token";
        $data="{\"action\":\"long2short\",\"long_url\":\"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb9834f1df9c98901&redirect_uri=http%3A%2F%2Fzhihuijingang.com%2Fyz%2Fpublic%2Findex.php%2Findex%2FPlane%2Findex%2Fbsn%2F1000001&response_type=code&scope=snsapi_base&state=1#wechat_redirect\"}";
         $res=  $this->curl_post($url, $data);
         $content=json_decode($res, true);
         print_r($content);die;
    }
    //保存出图片
    public function getImage($mediaId)
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$mediaId";
        $fileinfo=  $this->curl_down($url);
        $filename=time().rand(1,20).".jpg";
        $this->saveWxFile($filename, $fileinfo["body"]);
    }
    //保存出语言
    public function getVocie($mediaId)
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$mediaId";
        $fileinfo=  $this->curl_down($url);
        $filename=time().rand(1,20).".mp3";
        $this->saveWxFile($filename, $fileinfo["body"]);
    }
    //保存出语言
    public function getVideo($mediaId)
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="http://api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$mediaId";
        $fileinfo=  $this->curl_down($url);
        $filename=time().rand(1,20).".mp4";
        $this->saveWxFile($filename, $fileinfo["body"]);
    }
    //上传图片
    public function upImage()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $type="image";
        $filepath=LOG_PATH.'my123.jpg';
        $filedata=array("media" => new \CURLFile($filepath));
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$token&type=$type";
        $reslut=  $this->curl_post($url, $filedata);
        return $reslut;
    }
    private function curl_post($url,$data)
    {
        $ch=  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
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
    //保存图片
    private function saveWxFile($filename,$content)
    {
        $newFile=  fopen(LOG_PATH.$filename, "w");
        if($newFile !==false){
            if(false !==  fwrite($newFile, $content)) {
                fclose($newFile);
            }
        }
    }
}

