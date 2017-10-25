<?php
namespace app\lib\org;
/**
* 航班接口类
*/
class Airinfo
{
	
	function __construct()
	{
		# code...
	}

	/**
	 * [air_info 获取航班数据]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	function  air_info($params)
	{
		$Send = new \app\lib\org\Send();
		$url = 'http://10.210.11.11/hp/public/index.php/Api/Air/getAirInfo';
		$param = json_encode($params,true);
		$result = $Send ->curl_request($url,$param);
		return $result;
	}
}
?>