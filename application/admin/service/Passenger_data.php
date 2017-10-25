<?php
namespace app\admin\service;
/**
* 获取旅客定制数据
*/
class Passenger_data
{
	
	/**
	 * [获取定制数据并写入数据库]
	 * @param  array business  定制客户来源
	 * @return array 
	 */
	public function get_data()
	{
		$params  = ['business'=>1000000];
		$Send = new \app\lib\org\Send();
		$url = 'http://zhihuijingang.com/yz/public/index.php/myapi/Business/getSubscribe';
        $param = json_encode($params,true);
        $result = $Send ->curl_request($url,$param);

        return $result['result'];
	}

}

?>