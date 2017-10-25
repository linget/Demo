<?php
namespace app\weiwin\controller;
use think\Request;
use think\Cache;
/* 微信消息接口 */
class Weixin 
{
    private $_token= '';
    private $_data = [];
    
    public function index()
    {
        $token = Request::instance()->param('token');
        $this->_token = isset($token)?$token:'';//'667ACBF1D816537CB642BD5A546A7A7B';//Request::instance()->param('token');
        $wechatObj = new \app\lib\org\Wechat($this->_token);
       
      

        /* 绑定公众平台 */
        //$wechatObj->verify_token($this->_token);bug
        
        /* 消息回复测试 */
        //$wechatObj->responseMsg();
       
        $this->_data = $wechatObj->getMsg();
        $ToUserName  = isset($this->_data['ToUserName'])?$this->_data['ToUserName']:'';
        $FromUserName= isset($this->_data['FromUserName'])?$this->_data['FromUserName']:'';
       
        list($content,$type) = $this->replay($this->_data);
        /* 返回消息 */
        if ($content != '' && $type !='') 
        {
            $wechatObj->response($content,$type,$FromUserName,$ToUserName);
        }
    }

    public function replay($data=null)
    {
         $this->create_menu();//菜单创建
        $MsgType = isset($data['MsgType'])?$data['MsgType']:'';
        $Content = isset($data['Content'])?$data['Content']:'';

        /*按消息类型分类*/
        switch ($MsgType) {
            case 'event':
                     //事件类型
                    $Event  = isset($data['Event'])?$data['Event']:'';
                    $key  = isset($data['EventKey'])?$data['EventKey']:'';
                    $result = $this->home($Event,$key);
                break;
            case 'text':
                    //文本类型
                    $result = $this->keywords($Content);
                break;
            case 'image':
                    //图片类型
                # code...
                break;
            case 'location':
                    //地址类型
                # code...
                break; 
            case 'voice':
                    //语音类型
                # code...
                break;             
            case 'video':
                    //视屏类型
                # code...
                break;             
            case 'link':
                    //链接消息
                # code...
                break;         
            default:
                $result = "unknown msg type: ".$MsgType;
                break;
        }

        /* 文本消息测试 */
       // return array($data['Content'], 'text');
        
        /* 图文消息测试 */
      /*  $res['title'] = 'this is articles';

        $res['keyword'] =  $data['Content'];

        $res['pic'] = 'http://e.hiphotos.bdimg.com/wisegame/pic/item/9e1f4134970a304e1e398c62d1c8a786c9175c0a.jpg';

        $res['url'] = 'http://www.baidu.com';
        return array(
                array(
                    array(
                    $res['title'],
                    $res['keyword'],
                    $res['pic'],
                    $res['url']
                    )
                ),
             'news'
            );*/

           


        /* 自定义菜单事件 */
       /* if('CLICK' == $Event){
            $key = $data['EventKey'];
            return array(
             array(
                array(
                    'saber'.$key,
                    'welcome',
                    'http://img1.gamersky.com/image2014/10/20141014lj_09/gamersky_04small_08_201410151457BC0.jpg',
                    'http://www.bilibili.com'
                    )
                ),
             'news'
            );
        }*/
        
        if (!is_array($result)) { exit();}
        return $result;
    }


    /* 事件判断，回复处理 */
    public function home($Event,$key)
    {
        $content = [];
        switch ($Event) 
        {
             case 'subscribe':
                //查看是订阅行程提醒
                
               // $is_subscribe = ??
                //关注事件V1001_yuzheng
                $content = array(
                    array(
                         array(
                         '欢迎订阅',
                         'welcome',
                         'http://img1.gamersky.com/image2014/10/20141014lj_09/gamersky_04small_08_201410151457BC0.jpg',
                         'http://www.getGoogle.com') 
                        ),'news'
                    );
             break;
             case 'unsubscribe':
                //取消关注
                 $content = array(
                    array(
                         array(
                         '取消订阅',
                         '......',
                         'http://img1.gamersky.com/image2014/10/20141014lj_09/gamersky_04small_08_201410151457BC0.jpg',
                         'http://www.getGoogle.com') 
                        ),'news'
                    );
                 break;
            case 'CLICK':
                //点击菜单
                if ($key == 'V1001_yuzheng') 
                {
                    $content = array(
                    array(
                         array(
                         '订房',
                         '如需订房，请点击图文消息，查看详情',
                         'http://zhihuijingang.com/Demo/public/images/20150715_04.jpg',
                         'http://zhihuijingang.com/Demo/public/index.php/weiwin/reservation/index.html') 
                        ),'news'
                    );
                }
                break;
            case 'scancode_push':
                //扫码事件推送
                break;
             default:
                 $content = "receive a new event: ".$Event;
                 break;
         } 
        return  $content;
    }

    /*关键字判断与回复*/
    function keywords($keyword)
    {
        $content = [];
        switch ($keyword) {
            case '首页':
                    $content = array(
                    array(
                         array(
                         '关键字saber',
                         'welcome',
                         'http://img1.gamersky.com/image2014/10/20141014lj_09/gamersky_04small_08_201410151457BC0.jpg',
                         'http://www.getGoogle.com') 
                        ),'news'
                    );
                break;
                case '订房':
                    $content = array(
                    array(
                         array(
                         '订房',
                         '如需订房，请点击图文消息，查看详情',
                         'http://zhihuijingang.com/Demo/public/images/20150715_04.jpg',
                         'http://zhihuijingang.com/Demo/public/index.php/weiwin/reservation/index.html') 
                        ),'news'
                    );
                    break;
                case '订餐':
                    $content = array(
                    array(
                         array(
                         '订餐-吃货',
                         'welcome',
                         'http://img1.gamersky.com/image2014/10/20141014lj_09/gamersky_04small_08_201410151457BC0.jpg',
                         'http://www.getGoogle.com') 
                        ),'news'
                    );
                break;
            default:
                 $content = "receive a new keyword: ".$keyword;
                break;
        }
        return  $content;
    }

    /* 测试创建菜单 效果1-3天内*/
    function create_menu(){
        $Menu = new \app\lib\org\Menu();
        $data = [
             'button' =>[
                    [
                    'type' =>'click',
                    'name' =>'订房',
                    'key'  =>'V1001_yuzheng'
                    ],
                    [
                    'name'=>'交通',
                    'sub_button' =>[[
                                    'type' =>'view',
                                    'name' =>'定制查询',
                                    'url'  =>'http://zhihuijingang.com/Demo/public/index.php/weiwin/plane/index.html'
                                ]]
                    ],
                    [
                        'type' =>'view',
                        'name' =>'出行须知',
                        'url'  =>'http://zhihuijingang.com/Demo/public/index.php/index/Moreinfo/trafficnotice'
                    ]
                    /*[
                    "type"=>"view",
                    "name"=>"关于",
                    "url"=>"http://mp.weixin.qq.com/s?__biz=MzIxNTY5MjAwOQ==&mid=100000009&idx=1&sn=9460eebe0f487200e32cd9bea1d6c01c&chksm=1795268f20e2af9938815eed198c5539bc5635424f2a6319d6de1350e5803204e26de79586b3#rd"
                    ]*/
                ]
            ];
        
        $res = $Menu->create_menu($data);
       // file_put_contents('./log.txt',date('Y-m-d H:i:s',time()).'res22::'.print_r($res,true));

    }
}

?>
