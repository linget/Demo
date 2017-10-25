<?php
namespace app\lib\org;
use think\Db;
/**
* 获取地点经纬度
*/
class Place extends Controller
{
	


	/**
	 * [城市经纬度获取]  10w/天 
	 * @param  array $city  城市名
	 * @return array 
	 */
	public function place_itude($address = '上海虹桥机场')
	{
		$ak = '9KzQ6GurreKwmTHzZseW9Re71ges48Nd';
		$link ='http://api.map.baidu.com/place/v2/suggestion?query=%s&region=%s&output=json&ak=%s';
		$url =	sprintf($link,$address,$address,$ak);
		
		$Weshare = new \app\lib\org\Weshare();
		$result = $Weshare->curl_request($url);
		$arr = $result['result'][0];//默认以首个结果为准
		
		var_dump($arr);
		if ($arr) 
		{
			$data = ['c_name' => $arr['name'],'c_city' => $arr['city'],'c_lat'  => $arr['location']['lat'],'c_lng'  => $arr['location']['lng'],'c_addtime'  => time()];
			Db::name('t_place')->insert($data);
			return $data;
		}
		return false;
		
	}
}

?>