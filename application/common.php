<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Cookie;
use think\Log;
// 应用公共文件
 define('TOKEN','667ACBF1D816537CB642BD5A546A7A7B');
 define('ROUND',1);
 define('NOW_TIME', time());
    /**
     * [get_views 获取后台IPS设置]
     * @param $where c_type 1/2/3/4 ,航班态势/航班列表/火车态势/火车列表
     * @param $where c_istrue 1/0 是/否 是否启用 
     * @param $type 0/1,0为显示设置，1为走马灯样式
     */
    function get_views($where,$type='')
    {
        $model = new \app\admin\model\Screen();
        $arr = $model->search($where,$type);
        return $arr;
    }

/* 设置cookie */
    function setcook($name,$temp)
    {
        if ($temp['c_language']==1||$temp['c_language']==3) {
            $language = "zh_cn";
        }else{
            $language = "en";
        }
        if ($temp['c_isfromsha'] != 0) {
            $isFromSha = 1;
        }else{
            $isFromSha = 0;
        }
        if(Cookie::get('language'.$name) =='' && Cookie::get('isFromSha'.$name) =='')
        {
            Cookie::set('language'.$name,$language,3600*24);
            Cookie::set('isFromSha'.$name,$isFromSha,3600*24);
        }
    }

//光圈数据处理--态势图
  function process($effect){
      $new = [];
      $count = count($effect);

      foreach ($effect as $key => $value) {

         foreach ($value as $k => $v) {
           if (!in_array($k,$new)) 
           {
              $new[$k] = $v;
           }elseif (in_array($k,$new)&&$new[$k]<$v) {
             $new[$k]=$v;
           }{
            continue;
           }
         }

      }

        foreach ($new as $kk => $vv) {
          $arr[] = ['name'=>$kk,'value'=>$vv]; 
        }
     
        return $arr;
    }

    /* 数组去重 */
 function arr_unique($arr){
        $new = [];
        foreach ($arr as $key => $value) {
                foreach ($value as $k => $v) 
                {
                    if(!in_array($v, $new)){
                        $new[]=$v;
                    }
                }
        }
        return $new;
    }

    /* 数组去空 */
 function arr_fil($arr){

        foreach($arr as $k=>$v){
          $arr[$k] = array_filter($v);
      }
      return $arr;
    }
/**
 * [insert_log 日志记录]
 * @param  [type] $msg [返回记录]
 * @param  [type] $log [记录说明]
 */
function insert_log($msg,$log)
{
    
   $data = [
          'msg' => $msg,
          'log' => $log,
          'addtime' => date('Y-m-d H:i:s',time())
        ];
        Log::record($data);
        Log::save(); 
    }
    
function xmltoarray($xml)
{
  //禁止引用外部xml实体
  libxml_disable_entity_loader(true);
  $result = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
  return $result;
}

/**
 * [arraytoxml 数组转xml]
 * @param  [type] $arr 
 * @return xml   array  
 */
function arraytoxml($arr,$type,$num=1)
{

 if ($type==null||$num==1) {
     $xml = '<xml>';
  }else{
    $xml = '';
  }
  foreach ($arr as $k => $v) 
  {
    if (is_array($v)&&$num<2) 
    {
      $num++;
      $xml .="<".$k.">".arraytoxml($v,$type,$num)."</".$k.">";
      continue;
    }elseif (is_array($v)&&$num>1) {
      $num++;
      $xml .="<".$type.">".arraytoxml($v,$type,$num)."</".$type.">";;
      continue;
    }
    elseif(is_numeric($v))
    {
      $xml .="<".$k.">".$v."</".$k.">";
      continue;
    }else
    {
      $xml .="<".$k."><![CDATA[".$v."]]></".$k.">";
      continue;
    }
  }

if ($type==null||$num==2) {
     $xml .= '</xml>';
  }else{
    $xml .= '';
  }
  // file_put_contents('./logs.txt',$xml);
  return $xml;
}



  //获取最高峰5个城市
function topcity($city)
{

  $result = [];$res = [];  
  foreach ($city as $key => $value) 
  {
    if (empty($value['name'])||empty($value['value'])) { continue;}

    if(!empty($result)&&!empty($result[$value['name']]))
    {
       if ($value['REMARK_XML'] == 'Cancel'||$value['REMARK_XML'] == '取消') 
      {
        $result[$value['name']] = $value['value'];
      }

    }else
    {
      $result[$value['name']] =$value['value'];
    }

  }

  arsort($result);
  //array_splice($result, 5); 
  
  foreach ($result as $key => $value) {
     $res[] =  ['name'=>$key,'value'=>$value];
   } 
     return $res;
}