<?php
namespace app\admin\controller;
use \think\Request;
class Advert extends Base
{
  private  $model = '';

  function __construct()
  {
    parent::__construct();
    $this->model = new \app\admin\model\Advert();
    $this->c_number = $this->model->find_numb();
  }

  /**
   * [material_list 素材列表]
   */
  function material_list()
  {
      $info = $this->model->advert_list();

      $page = $info->render();
      $this->assign('page',$page);

      $this->assign('info',$info);  
      return view();
  }

  /**
   * [material_add 素材添加]
   */
  function material_add()
  {
    $title = '素材添加';
    if ($_POST) 
    {
        $url = self::uploads();
        $data = [
          'c_number' => $_POST['c_number'],
          'c_title' => $_POST['c_title'],
          'c_url' => $url,
          'c_addtime' => date('Y-m-d H:i:s')
        ];
      
        $res = $this->model->advert_insert($data);
        parent::out($res,'添加成功！','添加失败',url('material_list'));
    }
    $info = ['c_title'=>'','c_url'=>"/public/images/guanggao_02.jpg"];
    $this->assign('info',$info);
    $this->assign('c_number',$this->c_number);
    $this->assign('title',$title);
    $this->assign('variable',ACTION_NAME);
    return view();
  }

  /**
   * [material_add 素材编辑]
   */
  function material_edit()
  {
    if ($_POST) {
        $where['c_number'] = $_POST['c_number'];
        $info = $this->model->advert_list($where);
        if($info['c_url']){
          //删除历史图片
          @unlink(ROOT_PATH.$info['c_url']);
        }
        //上传新图片
        $url = self::uploads();
        $data = [
          'c_title' => $_POST['c_title'],
          'c_url' => $url
        ];

        $res = $this->model->advert_edit($data,$where);
        parent::out($res,'编辑成功！','编辑失败',url('material_list'));
    }else{
        $title = '素材编辑';
        $where['c_id'] = $this->param['c_id'];
        $info = $this->model->advert_list($where);
        if(!$info['c_url'])
        {
        $info['c_url'] = "/public/images/guanggao_02.jpg";
        }
    }

    $this->assign('info',$info);
    $this->assign('title',$title);
    $this->assign('variable',ACTION_NAME);
    return view('material_add');
  }

  function material_del()
  {
    $where['c_id'] = $this->param['c_id'];
    $res = $this->model->advert_del($where);
    parent::out($res,'删除成功！','删除失败!',url('material_list'));
  }

  /*上传图片*/
  function uploads()
  {
    $file = request()->file('photoimg');
    $path =  ROOT_PATH.'public' . DS . 'uploads';
    $info = $file->validate(['ext'=>'jpg,png,gif'])->move($path);
    $name = $info->getFilename();
     if ($info) {
          $name = $info->getFilename();
          $url  = "/public/uploads/".date('Ymd').'/'.$name;
          return $url;
        }else{
           return $file->getError();exit;
        }
  }
}
