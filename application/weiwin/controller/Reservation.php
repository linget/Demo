<?php
namespace app\weiwin\controller;
use think\Session;
use think\Db;
class Reservation extends Base
{
    protected  $rule = [
            ['input_name','require','名称必须填写！']
        ];
    public function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {

        $openid = Session::get('openid');
       
        if ($_POST) 
        {
            //获取数据
            $time = date('Y-m-d H:i:s',time());
            if (!empty($_POST['numb1'])) 
            {
               $numb = $_POST['numb1'];
               $roomtype = '标准单人房';
            }elseif (!empty($_POST['numb2'])) 
            {
               $numb = $_POST['numb2'];
               $roomtype = '标准双人房';
            }elseif (!empty($_POST['numb3'])) 
            {
               $numb = $_POST['numb3'];
               $roomtype = '大床房';
            }else{
                $numb = $_POST['numb4'];
               $roomtype = '商务房';
            }
            $data = [
                'c_name'=>$_POST['input_name'],
                'c_sex'=>$_POST['input_sex'],
                'c_phone'=>$_POST['input_phone'],
                'c_agreetime'=>$_POST['input_date'],
                'c_roomtype'=>$roomtype,
                'c_num'=>$numb,
                'c_endtime'=>$_POST['endtime'],
                'c_openid'=>$openid,
                'c_addtime'=>$time
            ];
            //插入
            $model = new \app\weiwin\model\Reservation();
            $res = $model->add_order($data);
            
            if ($res) 
            {
                $this->redirect('Reservation/room_success');
               // $this->success('预约成功！',url('Reservation/room_success'));
/*                 $datas = [
                    "touser"=>"$openid",
                    "msgtype"=>"text",
                    "text"=>[
                    "content"=>"预约成功！,凭手机号往前台办理入住手续！"
                        ]
                    ];*/
            }else{
                $this->redirect('Reservation/room_success');
                 //$this->error('预约失败！',url('Reservation/room_success'));
/*                $datas = [
                    "touser"=>"$openid",
                    "msgtype"=>"text",
                    "text"=>[
                    "content"=>"预约失败！,请往前台办理入住业务！"
                        ]
                    ];*/
            }
           // self::send_msg($datas);
        }
        $time = date('Y-m-d',time());
        $tomorrow = date('Y-m-d',strtotime('+1 day'));
        $this->assign('today',$time);
        $this->assign('tomorrow',$tomorrow);
        return $this->fetch();
    }

     /* 长连接转短链接接口 */
    function get_url(){
       // $Kfwechat = new \app\weiwin\service\Kfwechat();
        //$res = $Kfwechat->long2short('http://zhihuijingang.com/Demo/public/index.php/weiwin/plane/index.html');
        //var_dump($res);die;
    }

    function room_success()
    {
        $openid = Session::get('openid');
        $datas = [
                    "touser"=>"$openid",
                    "msgtype"=>"text",
                    "text"=>[
                    "content"=>"客服已收到您的订单，请耐心等待回复，谢谢！"
                        ]
                    ];
        $Kfwechat = new \app\weiwin\service\Kfwechat();
        $Kfwechat->send_msg($datas);

        return $this->fetch();
    }

}
