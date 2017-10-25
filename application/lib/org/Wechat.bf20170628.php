<?php
/*header("Content-Type: text/html; charset=utf8");
*/
namespace app\lib\org;
use think\Request;
/**
* 微信消息接收
*/
class Wechat 
{
  private $_data = [];
	function __construct($token = null)
  {
    $this->param = Request::instance()->param();
    if ($token) 
    {
      $this->checkSignature($token);
    }else{
      /*file_put_contents('./logs.txt','_data:'.print_r(file_get_contents("php://input"),true)); */
      $this->_data = xmltoarray(file_get_contents("php://input"));
    }

	 }

	/**
     * [response 消息响应]
     * @param array() $content 消息内容
     * @param string $type 消息类型
     * @return msg
     */
    public function response($content , $type = 'text',$flag = '0')
    {
      
      $data = [
    		'ToUserName'=>isset($this->_data['ToUserName'])?$this->_data['ToUserName']:'',
	    	'FromUserName'=>isset($this->_data['FromUserName'])?$this->_data['FromUserName']:'',
	    	'CreateTime'=>NOW_TIME,
	    	'MsgType'=>$type
    	];
    	$res = $this->{$type}($content);
      if(!$res){ return;}
    	$data = array_merge($data,$res);
    	$data['FuncFlag'] = $flag;
    	$result = arraytoxml($data);
    	return $result;
    }

  /* 返回接收消息 */
  public function getMsg()
  {    
    return $this->_data;
  }

  /* 文本数据处理 */
  public function text($params)
  {
      $this->_data['Content'] = $params;
  }


  /* 图文数据处理 */
  public function news($params = null)
  {
        foreach ($params as $key => $value) {
            list($data[$key]['Title'],$data[$key]['Description'],$data[$key]['PicUrl'],$data[$key]['Url']) = $value;
              if ($key >= 9) { break;}
        }
        $this->_data['ArticleCount'] = count($data);
        $this->_data['Articles'] = $data;
        
  }


  /* 音乐数据处理 */
  private function music($music) 
  {
      list($music['Title'], $music['Description'], $music['MusicUrl'], $music['HQMusicUrl']) = $music;
      $this->_data['Music'] = $music;
  }

	//接入验证
	function checkSignature($token = null)
	{
		$signature = isset($this->param['signature'])?$this->param['signature']:'';
		$timestamp = isset($this->param['timestamp'])?$this->param['timestamp']:'';
		$nonce = isset($this->param['nonce'])?$this->param['nonce']:'';
		$echostr = isset($this->param['echostr'])?$this->param['echostr']:'';
		$tmpArr = [$token, $timestamp, $nonce];
		sort($tmpArr);
    $tmpStr = sha1(implode($tmpArr));

    if($tmpStr == $signature)
    {
      echo $echoStr;
      exit();
    }

	}

}
?>