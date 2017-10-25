<?php
namespace app\admin\controller;

class Message extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Msgtemplate();
    }

    //消息模板
    public function index()
    {
        $info =$this->model->Mgs_list(null);
       foreach ($info as $key => $value) {
            $content = '';
            $content = json_decode($value['c_content'],JSON_UNESCAPED_UNICODE);
            if ($value['c_msgtype'] == 'text') {
               $value['c_content'] = $content['content'];
            }else{
                $value['c_content'] = $content;
            }
            $info[$key] = $value;
       }
        $page = $info->render();
        $this->assign('page',$page);
        $this->assign('info',$info);
        return $this->fetch('Message/index');
    }

    //模板添加
    public function msg_add()
    {
        if ($_POST) 
        {
            $time = date('Y-m-d H:i:s', time());
            if ($_POST['msgtype'] == 'text') {
                $content = ['content'=> $_POST['content']];
                $content = json_encode($content,JSON_UNESCAPED_UNICODE);
                $data = [
                    'c_title'=> $_POST['title'],
                    'c_description'=> $_POST['description'],
                    'c_msgtype'=> $_POST['msgtype'],
                    'c_content'=> $content,
                    'c_addtime'=> $time
                ];
            }elseif ($_POST['msgtype'] == 'news') 
            {
                $picurl = self::uploads();
                $article = [
                    'title' => $_POST['article_title'],
                    'description'=> $_POST['article_desciption'],
                    'url'=> $_POST['article_url'],
                    'picurl'=> $picurl
                ];
                $content = json_encode($article,JSON_UNESCAPED_UNICODE);
                $data = [
                    'c_title'=> $_POST['title'],
                    'c_description'=> $_POST['description'],
                    'c_msgtype'=> $_POST['msgtype'],
                    'c_content'=> $content,
                    'c_addtime'=> $time
                ];
            }
            $res = $this->model->msg_insert($data);
            if ($res) 
            {
                $this->success('添加成功！',url('Message/index'));
            }else{
                $this->error('添加失败！',ACTION_NAME);
            }
        }

        $typeinfo = [['type'=>'text','name'=>'文本'],['type'=>'news','name'=>'图文']];
        $this->assign('typeinfo',$typeinfo);
        return $this->fetch('Message/msg_add');
    }

    //模板编辑
    public function msg_edit()
    {

        return $this->fetch('Message/msg_edit');
    }

    //模板删除
    public function msg_del()
    {

    }

      /*上传图片*/
      function uploads()
      {
        //删除历史图片 @unlink(ROOT_PATH.$info['c_url']);
        $file = request()->file('article_picurl');
        $path =  ROOT_PATH.'public' . DS . 'uploads';
        $info = $file->validate(['size'=>307200,'ext'=>'jpg,png'])->move($path);//小于300k
        $name = $info->getFilename();
         if ($info) {
              $name = $info->getFilename();
              $url  = 'http://'.$_SERVER['SERVER_NAME']."/Demo/public/uploads/".date('Ymd').'/'.$name;
              return $url;
            }else{
               return $file->getError();exit;
            }
      }


    //微信消息返回
    function response_msg()
    {
        if ($_POST) 
        {
            $openid = $_POST['openid'];
            $time = date('Y-m-d H:i:s', time());
            if ($_POST['msgtype'] == 'text') {
                $msg = [
                'touser'=> $openid,
                "msgtype"=>"text",
                "text" =>[
                    'content'=> $_POST['content']
                    ]
                ];
            }elseif ($_POST['msgtype'] == 'news') 
            {
                $pic = self::uploads();
                $picurl = 'http://'.$_SERVER['SERVER_NAME'].'/Demo'.$pic;
                $article = [
                    'title' => $_POST['article_title'],
                    'description'=> $_POST['article_desciption'],
                    'url'=> $_POST['article_url'],
                    'picurl'=> $picurl
                ];
                $msg = [
                'touser'=> $openid,
                "msgtype"=>"news",
                "news" =>[
                        "articles"=>[
                            $article
                        ]
                    ]
                ];
                
            }
            //发送消息实体
            $Kfwechat = new \app\weiwin\service\Kfwechat();
            $res = $Kfwechat->send_msg($msg);
            
             if ($res) 
            {
                $this->success('发送成功！',url('Message/response_msg',['openid'=>$openid]));
            }else{
                 $this->error('发送失败！',url('Message/response_msg',['openid'=>$openid]));
            }
        }else
        {
            $openid = isset($this->param['openid'])?$this->param['openid']:'';
            $where['c_id'] = isset($this->param['id'])?$this->param['id']:'';
            $Reservation =  new \app\weiwin\model\Reservation();
            $info = $Reservation->search_info($where);
            if(empty($info['c_openid'])&&empty($openid)){
              $this->error('用户尚未授权,无法回复','Reservation/room_order');
            }else{
              $openid = $info['c_openid'];
            }
            $this->assign('openid',$openid);
        }

        $typeinfo = [['type'=>'text','name'=>'文本'],['type'=>'news','name'=>'图文']];
        $this->assign('typeinfo',$typeinfo);
        return $this->fetch('Message/response_msg');
    }
}
