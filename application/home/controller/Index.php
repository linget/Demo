<?php
namespace app\home\controller;
class Index extends Base
{
    function __construct()
    {
       
        parent::__construct();     
        $this->model = new \app\home\model\Index();
    }

    public function index()
    {
        $url = [];
        $result = $this->model->get_val();
        $host = "http://".$_SERVER['HTTP_HOST'];
        
        $model = new \app\admin\model\Release();
        $arr = $model->find_data(null,'c_id desc');
        $content = $arr['c_content'];
        $data = explode('|', $content);
        if (in_array('航班', $data)) 
        {
            $url['1'] = '/Demo/public/index.php/home/airs/index.html';
        }

        if (in_array('图片', $data)) {
            $url['2'] = '/Demo/public/index.php/home/others/index.html';
        }

        if (in_array('列车', $data)) {
            $url['3'] = '/Demo/public/index.php/home/trains/index.html';
        }

        if (in_array('视频', $data)) {
            $url['4'] = '/Demo/public/index.php/home/others/vedio.html';
        }
        if (!empty($url)) 
        {
            $i=1;
            $urls = [];
            foreach ($url as $key => $value) {
                $urls[$i] = $value;
                $i++;
            }
        }else{
            $urls = [
                '1'=>'/Demo/public/index.php/home/airs/index.html',
                '2'=>'/Demo/public/index.php/home/others/index.html',
                '3'=>'/Demo/public/index.php/home/trains/index.html',
                '4'=>'/Demo/public/index.php/home/others/vedio.html'
            ];
        }

        
        //var_dump($urls);die;

/*        $url = [
        '1'=>'/Demo/public/index.php/home/airs/index.html',
        '2'=>'/Demo/public/index.php/home/others/index.html',
        '3'=>'/Demo/public/index.php/home/trains/index.html',
        '4'=>'/Demo/public/index.php/home/others/vedio.html'*/
        // '4'=>'/Demo/public/index.php/home/others/index/i/4',
        // '5'=>'/Demo/public/index.php/home/airs/index/isFromSha_index/1/language_index/en',
        // '6'=>'/Demo/public/index.php/home/others/index/i/6',
        // '7'=>'/Demo/public/index.php/home/airs/index/isFromSha_index/0/language_index/en'
        //'2'=>'/Demo/public/index.php/home/airs/air_list.html',
        //'3'=>'/Demo/public/index.php/home/Trains/index.html',
        //'4'=>'/Demo/public/index.php/home/Trains/train_list.html',
        
    /*    ];*/
        
        
       /* foreach ($result as $key => $value) {
                $variable_url[] = $url[$value['c_type']];
        }*/

        
        $variable_url = json_encode($urls,JSON_UNESCAPED_UNICODE);
        $this->assign('variable_url',$variable_url);
        $this->assign('url',$urls);

        return view();
	}


}
