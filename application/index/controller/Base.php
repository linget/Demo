<?php
namespace app\index\controller;
use think\Validate;
//身份认证基本控制器
class Base extends Emptys
{
    protected $data = array('code'=>'','msg'=>'','result'=>'');

    function _initialize()
    {
        self::index();
    } 


    /**
     * 获取自定义的header数据 apikey
     * 并验证  
     * @return $key
     */
    function index()
    {
        $api_key = '';
        foreach($_SERVER as $key=>$value)
        {
            if(substr($key, 0, 5) === 'HTTP_')
            {
                $key = substr($key, 5);
                $key = str_replace('_', ' ', $key);
                $key = str_replace(' ', '-', $key);
                $key = strtolower($key);
     
                if ($key == 'apikey') 
                {
                    $api_key = $value;
                    break;
                }

            }
        }
         $this->verify_key($api_key);
   }
   
   /** 
    * 验证key,成功则添加访问记录,失败直接返回错误信息
    * @param $apikey
    * @return 
    */
    function verify_key($apikey)
    {

            $Indexserver = new \app\index\service\IndexService();
            $result_id   = $Indexserver->verify_key($apikey);
            if ($result_id) 
            {
                $this->api_logs($result_id);
            }else
            {
                $this->data['code'] = 1001;
                $this->data['msg']  = 'apikey is error !';
                exit(json_encode($this->data,true)) ;
            }

    }
    /*添加访问记录*/
    function api_logs($id)
    {
        if($id){        
            $api_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
            //$access_url = $_SERVER["HTTP_REFERER"];来路域名
            $data = array(
                'client_ip' => $this->get_client_ip(),
                'api_url'  => $api_url,
                'key_id'  => $id,
                'addtime'  =>date('Y-m-d H:i:s',time())
                );
            db('api_log','db_config2')->insert($data);
        }
        
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    function get_client_ip($type = 0) 
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            //代理
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) 
               unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) 
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
   


    /**
     * [verify 参数校验]
     * @param  array() $rule [校验规则]
     * @param  array() $data [校验数据]
     * @return array()/false    
     */
    public function verify($rule,$data){
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            if(!$result){         
                $this->data['code'] = 1003;
                $this->data['msg']  = $validate->getError();
               return json_encode($this->data,true);
            }
            return false;
    }
}