<?php
namespace app\admin\controller;
class Wxuser extends Base
{
  private  $model = '';

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\Wxuser();
  }

  /**
   * [index 用户列表]
   */
  function index()
  {
    $where = '';
    $c_openid = isset($_POST['c_openid'])?trim($_POST['c_openid']):'';
    $c_nickname = isset($_POST['c_nickname'])?trim($_POST['c_nickname']):'';
    //注册时间
    $this->param['stime'] =  isset($_POST['stime'])?trim($_POST['stime']):'';
    $this->param['otime'] =  isset($_POST['otime'])?trim($_POST['otime']):'';

    if($_POST)
    {
        $where['c_openid'] = ['like',"%{$c_openid}%"];
        $where['c_nickname'] = ['like',"%{$c_nickname}%"];
      if($this->param['stime']&&$this->param['otime'])
      {
        $where['c_addtime'] = [['egt',$this->param['stime'].'%'],['elt',$this->param['otime'].'%']];
      }elseif($this->param['stime'])
      {
        $where['c_addtime'] = ['gt',$this->param['stime']];
      }
      $where = array_filter($where);
    }
    //模板消息列表
    $model = new \app\admin\model\Msgtemplate();
    $msginfo = $model->msg_all($where=null,$field = "c_id,c_title");
    $msginfo = json_encode($msginfo,JSON_UNESCAPED_UNICODE);
    //用户微信信息
    $info = $this->model->user_list($where);
    foreach ($info as $key => $value) 
    {
        $wx_info = $this->weixin_late($value['c_openid']);
        $value['code_name'] = isset($wx_info['code_name'])?$wx_info['code_name']:'';
        $value['kind_id'] = isset($wx_info['kind_id'])?$wx_info['kind_id']:'';
        $value['statu'] = isset($wx_info['statu'])?$wx_info['statu']:'';
        if ($value['kind_id']&&$value['code_name']&&!$value['statu'] ) 
        {
          $value['statu'] = '延误';
        }
        $info[$key] = $value;
    }
    $page = $info->render();

    $this->assign('msginfo',$msginfo);
    $this->assign('c_openid',$c_openid);
    $this->assign('c_nickname',$c_nickname);
    $this->assign('post',$this->param);
    $this->assign('info',$info);
    $this->assign('page',$page);
    return view('Wxuser/index');
  }


  /**
   * [user_add 用户添加]
   */
/*  function user_add()
  {
    $state = [['c_state'=>0,'state_name'=>'禁止登陆'],['c_state'=>1,'state_name'=>'允许登录']];
    $Role = new \app\admin\model\Role();
    $role_list = $Role->r_list();//角色列表

    $username = $this->model->get_name();//分配账号
    $act = isset($_POST['act'])?trim($_POST['act']):'';
    $pass = md5('123456');
    if ($act == 'insert') {
        $data = [
          'c_username' => $_POST['c_username'],
          'c_password' => $pass,
          'c_fullname' => $_POST['c_fullname'],
          'c_email' => $_POST['c_email'],
          'c_phone' => $_POST['c_phone'],
          'c_state' => $_POST['c_state'],
          'c_address' => $_POST['c_address'],
          'c_addtime' => date('Y-m-d H:i:s',time())
        ];
        $result = $this->model->user_insert($data,$_POST['c_roleid']);
        parent::out($result,'添加成功！','添加失败!',url('user_list'));
    }

    $this->assign('username',$username);
    $this->assign('c_roleid',$role_list);
    $this->assign('state',$state);
    return view('User/user_add');
  }
*/
  /**
   * [user_edit 用户编辑]
   */
  /*function user_edit()
  {
    $c_id = isset($this->param['c_id'])?$this->param['c_id']:'';
    $state = [['c_state'=>0,'state_name'=>'禁止登陆'],['c_state'=>1,'state_name'=>'允许登录']];
    $Role = new \app\admin\model\Role();
    $role_list = $Role->r_list();//角色列表 
    $username = $this->model->get_name();
    $info = $this->model->get_info(['a.c_id'=>$c_id]);//用户信息

    if ($_POST) {
      $where['a.c_id'] = $_POST['c_id'];
      $data = [
          'c_username' => $_POST['c_username'],
          'c_fullname' => $_POST['c_fullname'],
          'c_email' => $_POST['c_email'],
          'c_phone' => $_POST['c_phone'],
          'c_state' => $_POST['c_state'],
          'c_address' => $_POST['c_address'],
      ];
      $result = $this->model->user_save($data,$where,$_POST['c_roleid']);
      parent::out($result,'编辑成功！','编辑失败!',url('user_list'));
    }


    $this->assign('info',$info);
    $this->assign('username',$username);
    $this->assign('c_roleid',$role_list);
    $this->assign('state',$state);
    return view('User/user_edit');
  }
*/
  /**
   * [user_del 管理员删除]
   */
  /*function user_del()
  {
    $where['c_id'] = $this->param['c_id'];

    $result = $this->model->user_delete($where);
    parent::out($result,'删除成功！','删除失败!',url('user_list'));
  }
*/

  //业务消息推送---群推
  public function send_business()
  {
    if ($_POST) 
    {
      $openid = [];
      if (!isset($_POST['response'])) {
        $this->error('请选择发送用户！',url('Wxuser/index'));
      }
      $openid = $_POST['response'];

      $template_msg = $_POST['temp_id'];
      if (!$template_msg) 
      {
        $this->error('请选择发送消息模板！',url('Wxuser/index'));
      }
      $tempObj = new \app\admin\model\Msgtemplate();
      $wechatObj = new \app\weiwin\service\Kfwechat();

      $where['c_id']=$template_msg;//模板消息id
      $field = "c_id,c_msgtype,c_content";
      $info = $tempObj->msg_all($where,$field);
      
      $msgtype = $info[0]['c_msgtype'];
      $content = json_decode($info[0]['c_content'],true);
      $i = 0;
      foreach ($openid as $key => $value) 
      {
        if(!empty($value))
        {
          if ($msgtype == 'news') 
          {
             $msg = [
              "touser"=>$value,
              "msgtype"=>$msgtype,
              $msgtype=>[
                  "articles"=>[$content]
                ]
              ];
          }elseif ($msgtype == 'text') {
              $msg = [
              "touser"=>$value,
              "msgtype"=>$msgtype,
              $msgtype=>$content
              ];
          }
            //发送消息模板
            $res[$i] = $wechatObj->send_msg($msg);
            $i++;
        }

      }
      if ($res[0]) 
      {
        $this->success('发送成功!','Wxuser/index');
      }else{
        $this->error('发送失败!','Wxuser/index');
      }
    }
  }

  //微信订阅提醒用户延误
  public function weixin_late($openid)
  {
    //获取微信用户
    $wxuser_model = new \app\admin\model\Guest();
    $whr = ['openid'=>$openid];
    $wx_info = $wxuser_model->get_wx($whr);
    if (!$wx_info) {
      return false;
    }
    //获取状态
    $type = $wx_info['kind_id'];
    if ($type==2) 
    {
      //航班
      $where = ['plane_id'=>$wx_info['code_name'],'plane_time'=>$wx_info['code_time']];
    }elseif ($type == 1) {
      //高铁
      $where = ['train_id'=>$wx_info['code_name'],'train_time'=>$wx_info['code_time']];
    }

    $wx_state = $wxuser_model->get_state($where,$type);

    $statu = isset($wx_state['state'])?$wx_state['state']:'';
    $wx_info['statu'] = $statu;

    return $wx_info;
  }

  //延误推送-主动
  function wxuser_sendlate()
  {

    $whr['c_id'] = isset($this->param['c_id'])?$this->param['c_id']:'';
    if (!$whr['c_id']) {
      return;
    }
    //获取微信用户
    $wxuser_model = new \app\admin\model\Wxuser();
    $wx_info = $wxuser_model->get_user($whr);

    $openid = $wx_info['c_openid'];
    $this->latemsg($openid);
    $this->roommsg($openid);
    $this->redirect('Wxuser/index');
  }
          //延误模板消息
     public function latemsg($openid)
    {
        $objtk=new \app\wechat\controller\Token();
        $token=$objtk->gettoken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$token";
        $data=' {
           "touser":"'.$openid.'",
           "template_id":"6WIsSt_8DePmJ2KDKxzzg0xUAIJsBKX-ijcCnTPl0T4",
           "topcolor":"#FF0000",
           "data":{
                   "first": {
                       "value":"尊敬的旅客，您乘坐的航班延误了。",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"MU9377",
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
                       "value":"07:14 07:00",

                       "color":"#173177"
                   },
                   "keyword5": {
                       "value":"09:20 09:06",

                       "color":"#173177"
                   },
                   "remark":{
                       "value":"最新动态：您乘坐的航班MU9377延误了,请及时联系航空公司,并合理安排行程。祝您生活愉快！",
                       "color":"#173177"
                   }
           }
       }';
        $Weshare = new \app\weiwin\service\Weshare();
            $res=  $Weshare->curl_request($url,$data);
            //$content=json_decode($res, true);
           //  return $content;
    }

    //订房推送
    function roommsg($openid){
        $ser =new \app\weiwin\service\Kfwechat();
        $content = [
            "touser"=> $openid,
            "msgtype"=>"news",
            "news"=>[
                "articles"=>[
                        [ "title" => "订房推送",
                          "description" => "温馨提示：您的航班出现较长时间延误，如需订房，可点击查看详情",
                          "url" => "http://zhihuijingang.com/Demo/public/index.php/weiwin/reservation/index.html",
                          "picurl" => " http://zhihuijingang.com/Demo/public/images/20150715_04.jpg"]
                    ]
                ]
            ];
        $res3=  $ser->send_msg($content);
    }
}
