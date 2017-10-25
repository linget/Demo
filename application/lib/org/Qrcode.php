<?php
namespace app\lib\org;

use think\Cache;
/* 二维码操作 */
class Qrcode 
{
    /* 创建 */
    function create_qrcode($data){
        
        $access_token = Cache::get('access_token');
        $Weshare = new \app\lib\org\Weshare();
        if (empty($access_token)) 
        {
           $access_token = $Weshare->getToken();
        }
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $arr = $Weshare->getTicket($access_token,$data);
        $ticket = urlencode($arr['ticket']);

        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;

        $result = $Weshare->curl_request($url,'',$type=1);

        return $result;exit;
        /*if($result['errmsg'] == 'ok'){
            return true;
        }*/
    }
}

?>
