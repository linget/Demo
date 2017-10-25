<?php
namespace app\weiwin\controller;
use think\Controller;
use think\Session;
use think\Request;
/*用户授权*/
class Base extends Controller
{


    public function _initialize() 
    {
        //父类构造函数
        self::index();
    }

    public function index()
    {
        $this->set_openid();
        //保留用户信息
        $wx_info = Session::get('wxinfos');
        $wx_info = isset($wx_info)?$wx_info:'';
        if ($wx_info) 
        {
            $this->save_info($wx_info);
        }
    }

    //获取用户openid
    public function set_openid()
    {
        $this->appId = "wxb9834f1df9c98901";;
        $this->appSecret = "8347d5c6cce014b3f43b9140ee2a2446";
        $openid = Session::get('openid');
        if ($openid) 
        {
            return $openid;exit();//return $openid;
        }
        $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $scope = 'snsapi_userinfo';//snsapi_userinfo

        if ($this->is_weixin()) 
        {
            
            $REDIRECT_URI =trim($url);
            $is_code = Request::instance()->has('code','get');
            if ($is_code)
            {
                $code=Request::instance()->param('code');

                $info=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appId."&secret=".$this->appSecret."&code=".$code."&grant_type=authorization_code");
                $info=json_decode($info,true);    
                
                $openid=$info['openid'];
                $token = $info['access_token'];
                $wxinfo = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid.'&lang=zh_CN');
                
                $wxinfos = json_decode($wxinfo,true);
                $data['c_openid'] = isset($wxinfos['openid'])?$wxinfos['openid']:$wxinfos['wecha_id'];
                $data['c_nickname'] =isset($wxinfos['nickname'])?$wxinfos['nickname']:'1';
                $data['c_sex'] =  isset($wxinfos['sex'])?$wxinfos['sex']:'2';

                $data['c_province'] = isset($wxinfos['province'])?$wxinfos['province']:'1';
                $data['c_city'] = isset($wxinfos['city'])?$wxinfos['city']:'1';
                $data['c_country'] = isset($wxinfos['country'])?$wxinfos['country']:'1';

                $data['c_headimgurl'] = isset($wxinfos['headimgurl'])?$wxinfos['headimgurl']:$wxinfos['face'];

                Session::set('openid',$wxinfos['openid']);
                Session::set('wxinfos',$data);
                return $openid;//return $openid;
            }else{
                header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appId.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=123456#wechat_redirect');
            }

        }
    }


    //是否为微信浏览器
    private function is_weixin()
    { 
     if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!== false) 
     {         
        return true;
     }else
     {
        //不是微信
        return false;
     }  

    }

    /**
     * [save_info 微信用户信息]
     * @param  [type] $data 保存授权用户信息
     */
    function save_info($data)
    {
        if (!$data) {   return;}
        $wx = new \app\admin\model\Wxuser();
        $where['c_openid'] = Session::get('openid');
        $is_exist = $wx ->get_user($where);
        if (!$is_exist) 
        {
            //尚未存在则保存
            $wx ->user_insert($data);
        }
    }
}
