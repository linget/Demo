<?php
/*header("Content-Type: text/html; charset=utf8");
*/
namespace app\weiwin\org;
use think\Controller;
/**
* 微信消息接收
*/
class Wechats extends Controller
{
    private $_data = array();
	function __construct($token = null){
		if ($token) {
            $this->checkSignature($token);
          }else{
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
    		'ToUserName'=>$this->_data['ToUserName'],
	    	'FromUserName'=>$this->_data['FromUserName'],
	    	'CreateTime'=>NOW_TIME,
	    	'MsgType'=>$type
    	];
    	$msg = new Msg();

    	$res = $msg->{$type}($content);

    	$data = array_merge($data,$res);
    	$data['FuncFlag'] = $flag;
    	$result = arraytoxml($data);
    	return $result;
    }

/*	 //回复文本消息
    private function transmitText($object, $content)
    {
         if (!isset($content) || empty($content)){
             return "";
         }
 
         $xmlTpl = "<xml>
	     <ToUserName><![CDATA[%s]]></ToUserName>
	     <FromUserName><![CDATA[%s]]></FromUserName>
	     <CreateTime>%s</CreateTime>
	     <MsgType><![CDATA[text]]></MsgType>
	     <Content><![CDATA[%s]]></Content>
	 	</xml>";
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
 
         return $result;
     }

     //回复图文消息
      private function transmitNews($object, $newsArray)
     {
         if(!is_array($newsArray)){
             return "";
         }
         $itemTpl = "<item>
             <Title><![CDATA[%s]]></Title>
             <Description><![CDATA[%s]]></Description>
             <PicUrl><![CDATA[%s]]></PicUrl>
             <Url><![CDATA[%s]]></Url>
         </item>";
         $item_str = "";
         foreach ($newsArray as $item){
             $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
         }
	    $xmlTpl = "<xml>
	     <ToUserName><![CDATA[%s]]></ToUserName>
	     <FromUserName><![CDATA[%s]]></FromUserName>
	     <CreateTime>%s</CreateTime>
	     <MsgType><![CDATA[news]]></MsgType>
	     <ArticleCount>%s</ArticleCount>
	     <Articles>$item_str</Articles>
	 	</xml>";
 
         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
         return $result;
     }*/

	//接入验证
	function checkSignature($token = TOKEN)
	{
		$signature = Request::instance()->param['signature'];
		$timestamp = Request::instance()->param['timestamp'];
		$nonce = Request::instance()->param['nonce'];
		$echostr = Request::instance()->param['echostr'];
		$tmpArr = [$token, $timestamp, $nonce];
		sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature)
        {
            echo $echoStr;
            exit;
        }
	}
}
?>