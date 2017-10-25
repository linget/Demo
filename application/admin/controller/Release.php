<?php
namespace app\admin\controller;
use think\Request;
use think\Cookie;
class Release extends Base
{
    function __construct()
    {
     parent::__construct();
     $this->model = new \app\admin\model\Release();
     $this->param = Request::instance()->param();
     $this->param['stime'] = isset($this->param['stime'])?$this->param['stime']:'';
     $this->param['otime'] = isset($this->param['otime'])?$this->param['otime']:'';
     $this->param['name'] = isset($this->param['name'])?$this->param['name']:'';
    }

    public function index()
    {
    	$info = $this->model->list_table2();

      $this->param['c_genre'] = isset($this->param['c_genre'])?$this->param['c_genre']:'';//可删除
      $this->assign('post',$this->param);
      $this->assign('info',$info);
      $this->assign('page',$info->render());
    	return $this->fetch('Release/index');
    }

    //播放内容详情
    public function play_details()
    {

      if ($_POST) 
      {
        $time = date('Y-m-d H:i:s',time());
        $content = '';

        //交通数据
        $Traffic_air = isset($_POST['c_url']['air'])?$_POST['c_url']['air']:'';
        $Traffic_train = isset($_POST['c_url']['train'])?$_POST['c_url']['train']:'';

        //资源
        $files_img = isset($_POST['c_file']['img'])?$_POST['c_file']['img']:'';
        $files_vedio = isset($_POST['c_file']['vedio'])?$_POST['c_file']['vedio']:'';

        if ($Traffic_air) { $content.= '航班|';}
        if ($Traffic_train) { $content.= '列车|';}
        if ($files_img) { $content.= '图片|';}
        if ($files_vedio) { $content.= '视频';}
        $time = isset($_POST['c_time'])?$_POST['c_time']:'';
        $c_time = json_encode($time);

        $data = [
          'c_name'=> $_POST['c_name'],
          'c_url'=>'http://'.$_SERVER['HTTP_HOST'].'/Demo/public/index.php',
          'c_content'=> $content,
          'c_time' => $c_time
        ];

        $res = $this->model->insert_table2($data);

        if ($res) {
          $this->success('添加成功！',url('Release/index'));
        }else{
          $this->error('添加失败！',url('Release/index'));
        }
        
      }
      return $this->fetch('Release/play_details');
    }

    //发布编辑
    function edit_details()
    {
       $where['c_id'] = $this->param['c_id'];
       $info = $this->model->find_data($where);
       $this->assign('info',$info);
       return $this->fetch('Release/edit_details');
    }

    //预约列表
    public function play_add()
    {
      if ($_POST) 
      {
          if ($_POST['name']) 
          {
             $where['c_name'] = $_POST['name'];
          }
          if ($_POST['stime']&&$_POST['otime']) 
          {
              $where['c_agreetime'] = [['egt',$_POST['stime'].'%'],['elt',$_POST['otime'].'%']];
          }elseif ($_POST['stime']||$_POST['otime']) {
              $where['c_agreetime'] = isset($_POST['stime'])? $_POST['stime']: $_POST['otime'];
          }
      }
      $sn = self::get_sn();

    /*$where['c_ishandle'] = 1;
     
     $info = $this->model->find_order($where);
     $page = $info->render();
     $this->assign('page',$page);
     $this->assign('info',$info);*/
     $this->assign('sn',$sn);
     $this->assign('variable',ACTION_NAME);
     $this->assign('param',$this->param);
     return $this->fetch('Release/play_add');
    }

    //生成唯一编号
    public function get_sn()
    {
      $sn = strtolower(md5(uniqid(mt_rand(), true).'yz'));
      return $sn;
    }

    //编辑预约
    function order_edit()
    {
      if ($_POST) 
      {

        $where['c_id'] = $_POST['id'];
        $data = [
          'c_name'=>$_POST['c_name'],
          'c_sex'=>$_POST['c_sex'],
          'c_phone'=>$_POST['c_phone'],
          'c_agreetime'=>$_POST['c_agreetime'],
          'c_endtime'=>$_POST['c_endtime'],
          'c_roomtype'=>$_POST['c_roomtype']
          ];
        $res = $this->model->update_order($where,$data);
        if ($res) 
        {
            $this->success('修改成功！',url('room_order'));
        }else{
            $this->error('修改失败！',ACTION_NAME);
        }
      }else
      {
        $where['c_id'] = $this->param['id'];
        $sex = [['c_sex'=>'0','name'=>'女'],['c_sex'=>'1','name'=>'男']];
        $info = $this->model->find_order($where);
        $this->assign('info',$info[0]);
        $this->assign('sexinfo',$sex);
      }  
      return $this->fetch('Reservation/order_edit');
    }

    //删除预约
    function order_del(){
         $where['c_id'] = $this->param['id'];
         $res = $this->model->del_order($where);
         if ($res) 
        {
            $this->success('删除成功！',url('room_order'));
        }else{
            $this->error('删除失败！',url('room_order'));
        }
    }

    //新增预约
    function order_add()
    {
      if ($_POST) 
      {
        $time = date('Y-m-d H:i:s',time());
         $data = [
                'c_name'=>$_POST['c_name'],
                'c_sex'=>$_POST['c_sex'],
                'c_phone'=>$_POST['c_phone'],
                'c_agreetime'=>$_POST['c_agreetime'],
                'c_endtime'=>$_POST['c_endtime'],
                'c_roomtype'=>$_POST['c_roomtype'],
                'c_num'=>$_POST['c_num'],
                'c_addtime'=>$time
            ];
            //插入数据
            $res = $this->model->add_order($data);
            if ($res) 
            {
                $this->success('预约成功！',url('room_order'));
            }else{
                $this->error('预约失败！',ACTION_NAME);
            }
      }
       $sex = [['c_sex'=>'0','name'=>'女'],['c_sex'=>'1','name'=>'男']];
       $this->assign('sexinfo',$sex);
      return $this->fetch('Reservation/order_add');
    }




    //全向发布
    function send_all()
    {
      if ($_POST) 
      {
        $type = isset($_POST['msgtype'])?$_POST['msgtype']:'text';
        $msg = $_POST['content'];
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
        $ms = urlencode($msg);
        //$sm = json_encode($msg,JSON_UNESCAPED_UNICODE);
        //加入前端缓存
        Cookie::set('releasemsg',$ms,3600);
        if ($numb_ok) 
        {
          $mess = '发送成功!';//.$numb_ok."人"."失败".$numb_def."人";
          $this->success($mess,url('Release/send_all'));
        }else{
           $mess = '发送失败!';//.$numb_def."人";
           $this->error($mess,ACTION_NAME);
        }

      }

      return view('Release/send_all');
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

    //查询用户标签id
    function get_tagid()
    {

    }
}

