<?php
namespace app\lib\org;
/* 微信消息处理接口 */
class Wechat
{
  private $_data = array();
  public function __construct($token = null) {
      if ($token) {
          $this->verify_token($token);
      }else{
          $this->_data = xmltoarray(file_get_contents("php://input"));
      }
  }


  /* token验证 */
	public function verify_token($token)
	{
		$echoStr = isset($_GET["echostr"])?$_GET["echostr"]:'';

		if($this->checkSignature($token))
		{
			  //ob_clean();	
        	echo $echoStr;
        	exit;
   	}

			
	}

	  /* 测试用例（可删除） */
    public function responseMsg()
    {
		$postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"])?$GLOBALS["HTTP_RAW_POST_DATA"]:file_get_contents("php://input");
      	
		if (!empty($postStr))
		{  
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";             
				if(!empty($keyword))
                {
	                $msgType = "text";
	                $contentStr = "Welcome world!";
	                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                echo $resultStr;
                }else{
                	echo "";exit;
                }
        }else {
        	echo "";exit;
        }
    }

 /**
   * [response 消息回复]
   * @param array() $content 消息内容
   * @param string $type 消息类型
   * @return msg
   */
  public function response($content , $type = 'text',$FromUserName,$ToUserName)
  {
      /* 消息基础数据 */
    	$this->_data = [
    		'ToUserName'=>$this->_data['FromUserName'],
	    	'FromUserName'=>$this->_data['ToUserName'],
	    	'CreateTime'=>NOW_TIME,
	    	'MsgType'=>$type
    	];

      /* 按类型处理消息 */
      $this->{$type}($content);
      
      /* 数组转xml */
      $xml = new \SimpleXMLElement('<xml></xml>');
      $this->data2xml($xml, $this->_data);
      die($xml->asXML());
  }

  /**
   * [checkSignature 签名对比]
   * @param  string $token
   * @return        
   */
	public function checkSignature($token)
	{
      $signature = isset($_GET["signature"])?$_GET["signature"]:'';
      $timestamp = isset($_GET["timestamp"])?$_GET["timestamp"]:'';
      $nonce = isset($_GET["nonce"])?$_GET["nonce"]:'';

  		$data = array($token,$timestamp, $nonce);
  		sort($data);
  		$sign = sha1(implode($data));

  		return $sign == $signature ? true:false;

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
  public function news($params)
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

  /**
   * [data2xml 生成xml]
   * @param  [type] $xml  obj
   * @param  [type] $data 待处理数据
   * @return xml
   */
  private function data2xml($xml, $data, $item = 'item') 
  {

        foreach ($data as $key => $value) {

            is_numeric($key) && ($key = $item);

            if (is_array($value) || is_object($value)) {

                $child = $xml->addChild($key);

                $this->data2xml($child, $value, $item);

            } else {

                if (is_numeric($value)) {

                    $child = $xml->addChild($key, $value);

                } else {

                    $child = $xml->addChild($key);

                    $node = dom_import_simplexml($child);

                    $node->appendChild($node->ownerDocument->createCDATASection($value));

                }

            }

        }

  }

}

?>
