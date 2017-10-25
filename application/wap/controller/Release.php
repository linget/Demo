<?php
namespace app\wap\controller;
use think\Request;
use think\Cache;
/* 消息发布控制器 */
class Release extends Base
{

  public function index()
  {
    return view('Release/index');
  }

  public function send_msg()
  {
    if ($_POST) 
    {
      $type = isset($_POST['msgtype'])?$_POST['msgtype']:'text';//默认文本
      $msg = isset($_POST['message'])?$_POST['message']:'';//发送内容

      $weixin = isset($_POST['weixin'])?$_POST['weixin']:'';//微信 
      $screen = isset($_POST['screen'])?$_POST['screen']:'';//大屏
      $voice = isset($_POST['voice'])?$_POST['voice']:'';//语音
      $tv = isset($_POST['tv'])?$_POST['tv']:'';//有限
      //微信
      if($weixin){
        $result = $this->wx_massmsg($type,$msg);
        $numb_ok = 0;
        $numb_def = 0;
        for ($j=0; $j < count($result); $j++) 
        { 
          if($result[$j])
          {//成功发送
            $numb_ok++;
          }else{
            //失败发送
            $numb_def++;
          }
        }
      }
     
     //大屏
     if ($screen) 
     {
        $ms = urlencode($msg);
        //$sm = json_encode($msg,JSON_UNESCAPED_UNICODE);
        //加入前端缓存
        Cache::set('releasemsg',$ms,3600);
     }

     //语音
     if ($voice) 
     {
        $result = $this->text2voice($msg);//文字转语音
        $path = $this->create_voice_file($result);//创建音频文件
        Cache::set('voiceurl',$path,3600);
     }
     
      if ($numb_ok) 
      {
        $mess = '发送成功!';//.$numb_ok."人"."失败".$numb_def."人";
        $this->success($mess,url('Release/send_msg'));
      }else{
         $mess = '发送失败!';//.$numb_def."人";
         $this->error($mess,ACTION_NAME);
      }
    }
    return view('Release/send_msg');
  }

      //微信群发消息
    function wx_massmsg($type,$msg)
    {
      switch ($type) {
        case 'text':
            $result = $this->textmsg($msg);
          break;
        
        default:
          # code...
          break;
      }

      return $result;
    }

    //发送文本消息
    public function textmsg($msg)
    {
      //标签群发接口4条/月
      //$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=%s';
      
      //openid列表群发接口4条/月
      //$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=%s';
      
      //客服消息24h内有交互
      //1.获取用户
      $wxmod = new \app\admin\model\Wxuser();
      $userinfo = $wxmod->get_userlist('c_openid');
      $Kfwechat = new \app\weiwin\service\Kfwechat();
      $res = [];
      //发送消息
      foreach ($userinfo as $key => $value) {
          $data = [
            "touser"=>$value['c_openid'],
            "msgtype"=>"text",
            "text"=>[
            "content"=>$msg
                ]
            ];
            $res[$key] = $Kfwechat->send_msg($data);
        }
        return $res;

      //获取accesstoken
     /* $Weshare = new \app\weiwin\service\Weshare();
      $access_token = $Weshare->get_access_token();
      $url = sprintf($url,$access_token);
      $data = [
        'filter' => [
           'is_to_all'=>true,//是否发发送全部用户
           'tag_id'=>''
        ],
        'text' => ['content'=>$msg],
        'msgtype' =>'text'
        ];*/
       // $msg = json_encode($data,JSON_UNESCAPED_UNICODE);

        //发送
       /* $Weshare = new \app\weiwin\service\Weshare();
        $res = $Weshare->curl_request($url,$msg);*/
        //return $res;
    }

    function findrelease(){
      $res = ['result'=>false,'releasemsg'=>0];
      $clr = isset($_POST['releasemsg'])?$_POST['releasemsg']:'';
      $cah = Cache::get('releasemsg');
      $voice = Cache::get('voiceurl');
      if($clr&& $cah){
         $res = ['result'=>'ok','cache'=>$cah,'voiceurl'=>$voice];
         return json_encode($res,true);
      }
       return json_encode($res,true);
    }


    /* 控制前端刷新 */
    function layer()
    {
      
      return view('Release/layer');
    }

    function setfresh()
    {

      $res = ['result'=>false];
      if ($_POST) 
      {
        $clr = isset($_POST['clr'])?$_POST['clr']:'';
        if ($clr) {
           Cache::set('refreshnow',1,1.5);

           $res = ['result'=>'ok'];
           return json_encode($res,true);
        }
      }
      
      return json_encode($res,true);
    }

    //清除更新标记
    function clearfresh(){
      $clr = isset($_POST['clr'])?$_POST['clr']:'';
      $res = ['result'=>false];
      $clear = Cache::clear();
      if ($clr&&$clear) 
      {
         $res = ['result'=>'ok'];
         return json_encode($res,true);
      }
      return json_encode($res,true);
    }

        //查询改变状态
    function findrefresh()
    {
      $res = ['result'=>false,'cache'=>0];
      $clr = isset($_POST['refreshnow'])?$_POST['refreshnow']:'';
      $cah = Cache::get('refreshnow');
      if($clr&&$cah){
         $res = ['result'=>'ok','cache'=>$cah];
         return json_encode($res,true);
      }
       return json_encode($res,true);
    }

    /**
     * [send_voice 文字转音频]
     * @param  [type] $data [文字]
     * @return [type]       [音频文件】
     */
    function text2voice($data)
    {
      $data = ['tex'=>$data];
      $arr = json_encode($data,true);
      $url = 'http://10.210.11.11/hp/public/index.php/Api/Tts/voice';
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 500);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
      $output = curl_exec($ch);
      curl_close($ch);
      return $output;
    }

    //创建音频文件
    function create_voice_file($data){
      $dirname = '/Demo/public/voice/'.date('Y-m-d').'/';
      $filename = strtotime('now').'.mp3';
      $path = $dirname.$filename;
      
      if(file_exists('D:wamp/www'.$dirname) ==false)
      {
        mkdir('D:wamp/www'.$dirname,0777,true);
      }
      //创建成功 
      $fp = @fopen('D:wamp/www'.$path, 'w+');
      if ($fp) 
      {
        fwrite($fp, $data);
        fclose($fp);
      }else
      {
        //创建文件失败
        file_put_contents('/Demo/public/log.txt', date('Y-m-d H:i:s').'\n 创建音频文件失败!');
      }
      return $path;
    }


    function mac(){
      return view();
    }
}
