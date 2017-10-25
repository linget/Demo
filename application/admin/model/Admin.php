<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Admin extends Model
{
    protected $table           = 'admin_function';
    protected $menu            = 'admin_menu';//管理数据表
    /* 新增 */
    public function menu_insert($data)
    {
    	return Db::name($this->table)->insertGetId($data);
    }

    /* 更新 */
    public function function_edit($data,$where)
    {
    	return Db::name($this->table)->where($where)->update($data);
    }

    /* 删除 */
    public function menu_del($where)
    {
        return Db::name($this->table)->where($where)->delete();
    }

    /* 二级功能列表 */
    public function function_list($where = ['c_pid'=>0])
    {
        

        if(!$where['c_pid'])
        {
            $menu = Db::name($this->menu)->where($where)->select();
            $nub = 0;
            foreach ($menu as $key => $value) {
                $menu[$key]['child'] = Db::name($this->menu)->where(['c_pid'=>$value['c_id']])->select();
                $menu[$key]['numb'] = ++$nub;
            }
        }else{
           
            $menu = Db::name($this->menu)->where($where)->select();
            $nub = 0;
            foreach ($menu as $key => $value) {
                $menu[$key]['child'] = Db::name($this->table)->where(['c_menuid'=>$value['c_id']])->select();
                $menu[$key]['numb'] = ++$nub;
            }
        }
    	return $menu;
    }

    /* 三级功能列表 */
    public function details_list($where)
    {
            return Db::name($this->table)->where($where)->paginate(15);
    }

    /* 功能查询 */
    public function details_search($where)
    {
            $menu = Db::name($this->table)->where($where)->find();
            return $menu;
    }

    /**
     * [change_pass 修改密码]
     */
    function change_pass($data,$where){
        return Db::name('admin_users')->where($where)->update($data);
    }
}

?>