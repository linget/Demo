<?php
namespace app\wechat\controller;

class Message
{
    public function getModel()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        print_r($token);
        $url="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=$token";
         $res=  $this->curl_post($url);
         $content=json_decode($res, true);
          print_r($content);die;
    }
    //$code 1 火车 2 航班
    public function sendMes($mess,$users,$code,$section=null)
    {
        $hour="";
        if(!empty($section)){
            switch ($section)
            {
            case 7200:
                $hour="2小时";
                break;
            case 3600:
                $hour="1小时";
                break;
            case 900:
                $hour="半小时";
                break; 
            }
        }
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$token";
        foreach ($users as $value)
        {
            if($code ==2){
                $data=$this->makePlaneData($mess, $value['openid'],$hour);
            }  else {
                $data=  $this->makeTrainData($mess, $value['openid'], $hour);
            }
            if(empty($data)){
                continue;
            }
            $this->curl_post($url,$data);
    //        $res=  $this->curl_post($url,$data);
     //       $content=json_decode($res, true);
     //        print_r($content);
        }
//        return $content;
    }
    //航班数据转换
    private function makePlaneData($mess,$openid,$hour)
    {
        switch ($mess['state'])
        {
            case "取消":
            case "延误":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"6WIsSt_8DePmJ2KDKxzzg0xUAIJsBKX-ijcCnTPl0T4",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的航班'.$mess["state"].'了。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["plane_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["plane_time"]." ".$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["plane_time"]." ".$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的航班'.$mess["plane_id"].''.$mess["state"].'了，您可以咨询航空公司确认。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
             break;
            case "已起飞":
            case "已到达":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"6WIsSt_8DePmJ2KDKxzzg0xUAIJsBKX-ijcCnTPl0T4",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的航班'.$mess["state"].'。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["plane_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的航班'.$mess["plane_id"].''.$mess["state"].'了。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
                break;
            case "正常":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"6WIsSt_8DePmJ2KDKxzzg0xUAIJsBKX-ijcCnTPl0T4",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的航班'.$mess["state"].'。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["plane_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的航班'.$mess["plane_id"].''.$mess["state"].'，'.$hour.'后将出发，请提前做好准备。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
                break;
         default :
             $data=null;
             break;
        }
        return $data;
    }
    //火车数据转换
    private function makeTrainData($mess,$openid,$hour)
    {
         switch ($mess['state'])
        {
            case "取消":
            case "晚点":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"T05z6aE1nBA5CMK9MpWu4ftqNNYO7UBjP-tttyuKoo0",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的火车'.$mess["state"].'了。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["train_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["train_time"]." ".$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["train_time"]." ".$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的火车'.$mess["train_id"].''.$mess["state"].'了，您可以咨询航空公司确认。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
             break;
            case "已出发":
            case "已到达":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"T05z6aE1nBA5CMK9MpWu4ftqNNYO7UBjP-tttyuKoo0",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的火车'.$mess["state"].'。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["train_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的火车'.$mess["train_id"].''.$mess["state"].'了。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
                break;
            case "正点":
                 $data=' {
           "touser":"'.$openid.'",
           "template_id":"T05z6aE1nBA5CMK9MpWu4ftqNNYO7UBjP-tttyuKoo0",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的火车'.$mess["state"].'。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$mess["train_id"].'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.$mess["leave_address"].'",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"'.$mess["arrive_address"].'",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"'.$mess["leave_time"].'",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"'.$mess["arrive_time"].'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的火车'.$mess["train_id"].''.$mess["state"].'，'.$hour.'后将出发，请提前做好准备。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
                break;
         default :
             $data=null;
             break;
        }
        return $data;
        
    }

    public function test()
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$token";
        $data=' {
           "touser":"o6ZCrv5Jjm4tlkc4gNN_A3PFNVYA",
           "template_id":"T05z6aE1nBA5CMK9MpWu4ftqNNYO7UBjP-tttyuKoo0",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的火车正常。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"G6",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"上海虹桥",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"北京首都",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"6:30",
                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"9:20",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的火车G6正常了。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
            $res=  $this->curl_post($url,$data);
            $content=json_decode($res, true);
             print_r($content);
    }

    private function curl_post($url,$data=null)
    {
        $ch=  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

