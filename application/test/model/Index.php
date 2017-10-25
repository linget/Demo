<?php
namespace app\test\model;
use think\Model;
use think\Db;
use think\Session;
class Index extends Model
{
    //获取主页内容设置
    function get_val()
    {

        return Db::name('screen_main a')->join('t_screen b','a.c_screenid = b.c_type')->select();
    }
    
    /**
     * 查询道路评价数据表
     * @return type
     */
    public function getRoadEva() {
        return Db::name('road_evalute')->order('c_month')->select();
    }
    /**
     * 存入道路评价数据表
     * @param type $data
     * @return type
     */
    public function insertRoadEva($data) {
        return Db::name('road_evalute')->insertAll($data);
    }
    
    /**
     * 更新道路评价数据表
     * @param type $data
     * @return type
     */
    public function updateRoadEva($id,$value) {
        return Db::name('road_evalute') ->where('c_id',$id) ->setField('c_value', $value);
    }
}