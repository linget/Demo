<?php
namespace app\admin\service;
/**
* 获取地点经纬度
*/
class Place
{
	
	function __construct(argument)
	{
		# code...
	}

	/**
	 * [城市经纬度获取]
	 * @param  array $city  城市名
	 * @return array 
	 */
	public function place_itude($city = '上海')
	{
		$ak = '9KzQ6GurreKwmTHzZseW9Re71ges48Nd';
		$link ='http://api.map.baidu.com/place/v2/suggestion?query=%s&region=%x&output=json&ak=%s';
		$url =	sprintf($link,$city,'全国',$ak);

		$Weshare = new \app\lib\org\Weshare();

		$result = $Weshare->curl_request($url);
		var_dump($result);die;
	}
}

?>