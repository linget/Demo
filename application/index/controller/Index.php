<?php
namespace app\index\controller;
use think\Request;
use think\Cookie;
use think\Controller;
class Index extends controller
{
    function __construct()
    {
       
        parent::__construct();     
        $this->Air = new \app\index\service\Air();
        $this->Train = new \app\index\service\Train();
        $this->nowtime = date('H:i',time());//时间初始化
        $this->configure = new \app\index\model\Index();

        $config = $this->configure->get_val();//页面配置获取
        
        $this->c_pagetime = $config[0]['c_pagetime'];// 翻页时间
        $this->c_language_type =  $config[0]['c_language'];// 设置语言
        $this->c_isFromSha = $config[0]['c_isfromsha'];// 出发到达
        $this->auto_type = 1;//是否切换内容,默认切换
        $this->current_Page = 1;//默认第一页
    }

    //主页
    public function index()
    {

        $name = '';
        $temp = ['c_language'=>3,'c_isfromsha'=>2];//显示设置条件
        setcook($temp);
        
        $this->isFromSha = Cookie::get('isFromSha');
        $this->language = Cookie::get('language');
        $data = ['pages'=>'1','numb'=>NUMB,'isFromSha'=>$this->isFromSha,'language'=>$this->language];

        //接口数据
        $result = $this->Air->air_list($data);
        $info = json_encode($result['info'],JSON_UNESCAPED_UNICODE);

        //分页数据
        $pages_info  = $result['pages'];
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);
        $this->assign('count_Page',$count_Page);
        
        $this->assign('info',$info);
        $this->assign('time',$this->nowtime);
        //页面配置信息
        $this->assign('c_pagetime',$this->c_pagetime);
        $this->assign('c_language_type',$this->c_language_type);
        $this->assign('c_isFromSha',$this->c_isFromSha);
        $this->assign('auto_type',$this->auto_type);
        $this->assign('current_Page',$this->current_Page);

        return view();
    }

    //航班列表
    public function air_list()
    {

        $name = '';$auto_type=0;//不切换交通信息
        $temp = ['c_language'=>3,'c_isfromsha'=>2];//显示设置条件
        setcook($temp);
        
        $this->isFromSha = Cookie::get('isFromSha');
        $this->language = Cookie::get('language');
        $data = ['pages'=>'1','numb'=>NUMB,'isFromSha'=>$this->isFromSha,'language'=>$this->language];

        //接口数据
        $result = $this->Air->air_list($data);
        $info = json_encode($result['info'],JSON_UNESCAPED_UNICODE);
        //分页数据`
        $pages_info  = $result['pages'];
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);
        $this->assign('count_Page',$count_Page);
        $this->assign('info',$info);
        $this->assign('time',$this->nowtime);
        //页面配置信息
        $this->assign('c_pagetime',$this->c_pagetime);
        $this->assign('c_language_type',$this->c_language_type);
        $this->assign('c_isFromSha',$this->c_isFromSha);
        $this->assign('auto_type',$auto_type);
        $this->assign('current_Page',$this->current_Page);
        return view();
    }

    //高铁列表
    public function train_list()
    {

        $name = '';$auto_type=0;//不切换交通信息
        $temp = ['c_language'=>3,'c_isfromsha'=>2];//显示设置条件
        setcook($temp);
        
        $this->isFromSha = Cookie::get('isFromSha');
        $this->language = Cookie::get('language');
        $data = ['pages'=>'1','numb'=>NUMB,'isFromSha'=>$this->isFromSha,'language'=>$this->language];

        //接口数据
        $result = $this->Train->Train_list($data);
        $info = json_encode($result['info'],JSON_UNESCAPED_UNICODE);
        
        //分页数据
        $pages_info  = $result['pages'];
        $count_Page  = isset($pages_info['count_Page'])?$pages_info['count_Page']:'1';

        $this->assign("isFromSha", $this->isFromSha);
        $this->assign("language", $this->language);
        $this->assign('count_Page',$count_Page);
        $this->assign('info',$info);
       
        $this->assign('time',$this->nowtime);
        //页面配置信息
        $this->assign('c_pagetime',$this->c_pagetime);
        $this->assign('c_language_type',$this->c_language_type);
        $this->assign('c_isFromSha',$this->c_isFromSha);
        $this->assign('auto_type',$auto_type);
        $this->assign('current_Page',$this->current_Page);
        return view();
    }
    /**
     * [more_list 列表数据翻页]
     * @return [type] [description]
     */
    function morelist()
    {
        if(!$_POST){
            return false;
        }
        $next = $_POST['current_Page'];
        $language = $_POST['language'];
        $isFromSha = $_POST['isFromSha'];
        $type = $_POST['type'];

        $data = ['pages'=>$next,'numb'=>NUMB,'isFromSha'=>$isFromSha,'language'=>$language];
        if ($type == 'airlist') {
            $result = $this->Air->air_list($data);
        }else{
            $result = $this->Train->Train_list($data);
        }  
        echo  json_encode($result,true);
    }

    //系统时间更新
    function nowtime(){
        $now = '';
        $now = date('H:i',time());
        echo $now;
    }


    function writ(){
        $files = $this->getFiles();
        $arr = [];
        $nb = 0;
        foreach ($files as $key => $value) {
            $arr['NB'] = $nb;
            $k = '';
            $k = mb_substr($key,0,2);
            $arr['"'.$k.'"'] = '"/images/logo/'.$value['0'].'",';
            $nb++;
        }
    }

    //图片地址
    public  function getFiles($path = '../logo/',$child=true){
        header("Content-type:text/html;charset=utf-8");

        $files=array();        
        if(!$child){
            if(is_dir($path)){
                $dp = dir($path); 
            }else{
                echo "null";
            }
            while ($file = $dp ->read()){  
                if($file !="." && $file !=".." && is_file($path.$file)){  
                   $files[] = $file;
                }  
            }           
            $dp->close();
        }else{
            $this->scanfiles($files,$path);
        }              
       
        return $files;
    }
    /**
    *@param $files 结果
    *@param $path 路径
    *@param $childDir 子目录名称
    */
    public function scanfiles(&$files,$path,$childDir=true){
        $dp = dir($path); 

        while ($file = $dp ->read()){  
            if($file !="." && $file !=".."){ 
                if(is_file($path.$file)){//当前为文件
                     $files[]= $file;
                     
                }else{//当前为目录  

                     /*$filen= mb_substr($file, 0,2);*/
                     $this->scanfiles($files[$file],$path.$file.DIRECTORY_SEPARATOR,$file);
                }               
            } 
        }
        $dp->close();
    }

    //批量移动
    function movedir(){
        $result = $this->getFiles();
        $arr = [];
        $path = "D:/wamp/www/cordis5";
        //降维
        foreach ($result as $key => $value) {
            $arr[$key] = $value['0'];

            if(empty($value)) continue;
            if (is_array($value)) {

                //copy($path."/logo/".iconv( "gb2312","utf-8", $key).'/'.$value['0'],"/images/logo/".$value['0']);
               rename("../logo/".$key.'/'.$value['0'], "images/logo/".$value['0']);
            }else{
               // copy($path."/logo/".iconv( "gb2312","utf-8", $key).'/'.$value,"images/logo/".$value);
               rename("../logo/".$key.'/'.$value, "images/logo/".$value);
            }           
        }
        echo "OK";
    }

}
