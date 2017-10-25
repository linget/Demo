<?php
namespace app\lib\org;
/**
* 判断外网是否链接
* @auth zjl 
* 2017-10-13
*/
class Online
{

	//判断是否可访问外网
    public function internet()
	{
		$url = "http://www.baidu.com/";
		//$fp = @fopen($url,'r');
		$fp = @get_headers($url);
		if (preg_match('/200/',$fp[0]))  
		{ 
		  return true;
		}else{
		  return false;  
		}
	}
}
?>