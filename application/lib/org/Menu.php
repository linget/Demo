<?php
namespace app\lib\org;

use think\Cache;
/* 自定义菜单操作 */
class Menu 
{
    /* 创建 */
    function create_menu($data){
        $access_token = Cache::get('access_token');
        $Weshare = new  \app\weiwin\service\Weshare();

        if (empty($access_token)) 
        {
           $access_token = $Weshare->get_access_token();
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;

        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $result = $Weshare->curl_request($url,$json,$type=1);

        return $result;exit;
        /*if($result['errmsg'] == 'ok'){
            return true;
        }*/
    }
}

?>
