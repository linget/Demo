<?php
namespace app\index\controller;

use think\Request;

class Emptys {
      
     /**访问接口不存在
     * @return $data
     */
    function _empty() {
            //header ( "HTTP/1.0 404 Not Found" ); // 使HTTP返回404状态码
            $this->data['code'] = 1002;
            $this->data['msg'] = 'api does not exit!';
            $this->data['result'] = '';
            exit(json_encode($this->data,true)) ;
    }
     // 404
     public function index() {
         $this->data['code'] = 1002;
         $this->data['msg'] = 'api does not exit!';
         $this->data['result'] = '';
         exit(json_encode($this->data,true)) ;
     }

 }
?>