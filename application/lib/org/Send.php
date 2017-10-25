<?php
namespace app\lib\org;
/**
* 数据发送类
* * @auth zjl 
* 2017-10-13
*/
class Send
{

	/**
	 * 内部交通请求专用
	 * [curl请求 支持http~https]
	 * @param $url  地址
	 * @param $data 数据
	 * @param $type 类型默认http
	 * @return array（）
	 */
	public function curl_request($url, $data=null, $type=null){
		$result = [];
		$ak = '667ACBF1D816537CB642BD5A546A7A7B';
		$header = array("apikey:$ak"); //秘钥
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		if ($type) {
			//证书规避
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		//数据
		if(!$data)
		{
			$json = curl_exec($ch);
			if (!$json) 
			{
				$json = file_get_contents($url);
			}
		}else
		{
			curl_setopt($ch, CURLOPT_POST,true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$json = curl_exec($ch);
		}
		
		$result = json_decode($json,true);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * 通用json格式
	 * [curl请求 支持http~https]
	 * @param $url  地址
	 * @param $data 数据
	 * @param $type 类型默认http
	 * @return array（）
	 */
	 public function curl_request2($url,$data = "",$headers = null,$method = null)
	 {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_TIMEOUT, 500);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//最大请求时间

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FAILONERROR, false);//忽略错误http400状态以下

      if ($method) {
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      }
      if ($headers) {
        //curl_setopt($ch, CURLOPT_HEADER, true);//查看返回http头部信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      }

      if(1 == strpos("$".$url, "https://")) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      }

      if (!$data) {
        $output = curl_exec($ch);
        if (!$output) {
            $output = file_get_contents($url);
          }      
      }else{
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
      }

    return json_decode($output,true);
  }
}
?>