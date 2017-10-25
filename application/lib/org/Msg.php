<?php
/*消息处理基本控制器*/
namespace app\lib\org;
class Msg
{
    private $_data = array();


    public function text($params)
    {
        $this->_data['Content'] = $params;
    }


    /**
     * 图片消息
     */
    public function pic($params)
    {
         $this->_data['Image']['MediaId'] = $params;
         
    }

    /**
     * 语音消息
     */
    public function voice($params)
    {
         $this->_data['Voice']['MediaId'] = $params;
         
    } 

    /**
     * 视屏消息
     */
    public function video($params)
    {
        list($data['MediaId'],$data['Title'],$data['Description']) = $params;     
         $this->_data['Video'] = $data;
           
    }

    /**
     * 音乐消息
     */
    public function music($params)
    {
        list($data['Title'],$data['Description'],$data['MusicUrl'],$data['HQMusicUrl'],$data['ThumbMediaId']) = $params;
        $this->_data['Music'] = $data;
        
         
    }
    
    /**
     * 图文消息
     */
    public function articles($params)
    {
        foreach ($params as $key => $value) {
            list($data['Title'],$data['Description'],$data['PicUrl'],$data['Url']) = $value;

        }
        $this->_data['ArticleCount'] = count($data);
        $this->_data['Articles'] = $data;   
    }


}

