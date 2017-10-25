<?php
namespace app\wechat\controller;
use think\Log;
class Menu
{
    public function getCommonMenu()
    {
        $appid=  config('appid');
        $obj=new \app\wechat\controller\Token();
        $access_token=$obj->gettoken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $menu='{ 
            "button":[
              {
                "name":"交通",
                "sub_button":[
                {
                 "type":"view",
                 "name":"定制查询",
                 "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3A%2F%2Fzhihuijingang.com%2Fyz%2Fpublic%2Findex.php%2Findex%2FPlane%2Findex%2Fbsn%2F1000001&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
                },
                {
                 "type":"view",
                 "name":"出行须知",
                 "url":"http://zhihuijingang.com/yz/public/index.php/index/Moreinfo/trafficnotice"
                }
               ]
              },
              {
               "type":"view",
                 "name":"酒店介绍",
                 "url":"http://mp.weixin.qq.com/s?__biz=MzIxNTY5MjAwOQ==&mid=100000012&idx=1&sn=f9aabb3af90e3f2042f51f1b1bd5dd1c&chksm=1795268a20e2af9c5f82b1a5508ff2a79778a17b59f10014aa440bd413a64d66736983d30f74#rd "
              },
              {
              "type":"view",
              "name":"关于",
              "url":"http://mp.weixin.qq.com/s?__biz=MzIxNTY5MjAwOQ==&mid=100000009&idx=1&sn=9460eebe0f487200e32cd9bea1d6c01c&chksm=1795268f20e2af9938815eed198c5539bc5635424f2a6319d6de1350e5803204e26de79586b3#rd "
             }
            ]
        }';
        $res=  $this->curl_post($url, $menu);
        var_dump($res);
    }
    public function setConditional()
    {
        $appid=  config('appid');
        $obj=new \app\wechat\controller\Token();
        $access_token=$obj->gettoken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".$access_token;
        $menu='{ 
            "button":[
              {
                "name":"交通",
                "sub_button":[
                {
                 "type":"view",
                 "name":"定制查询",
                 "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3A%2F%2Fzhihuijingang.com%2Fyz%2Fpublic%2Findex.php%2Findex%2FPlane%2Findex%2Fbsn%2F1000001&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
                },
                {
                 "type":"view",
                 "name":"出行须知",
                 "url":"http://zhihuijingang.com/yz/public/index.php/index/Moreinfo/trafficnotice"
                }
               ]
              },
              {
               "type":"view",
                 "name":"朗虹酒店",
                 "url":"http://mp.weixin.qq.com/s?__biz=MzIxNTY5MjAwOQ==&mid=100000012&idx=1&sn=f9aabb3af90e3f2042f51f1b1bd5dd1c&chksm=1795268a20e2af9c5f82b1a5508ff2a79778a17b59f10014aa440bd413a64d66736983d30f74#rd "
              },
              {
              "type":"view",
              "name":"关于",
              "url":"http://mp.weixin.qq.com/s?__biz=MzIxNTY5MjAwOQ==&mid=100000009&idx=1&sn=9460eebe0f487200e32cd9bea1d6c01c&chksm=1795268f20e2af9938815eed198c5539bc5635424f2a6319d6de1350e5803204e26de79586b3#rd "
             }
            ],
            "matchrule":{
            "tag_id":"100",
            "sex":"",
            "country":"",
            "province":"",
            "city":"",
            "client_platform_type":"",
            "language":""
            }
        }';
        $res=  $this->curl_post($url, $menu);
        var_dump($res);
    }
    public function getMenu()
    {
        $obj=new \app\wechat\controller\Token();
        $access_token=$obj->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=$access_token";
        $res=  $this->http_post($url);
        $cont=  json_decode($res,true);
        halt($cont);
    }

    public function clearMenu()
    {
        $obj=new \app\wechat\controller\Token();
        $access_token=$obj->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
        $res=  $this->http_post($url);
        var_dump($res);
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
    private function http_post($url)
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

