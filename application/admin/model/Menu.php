<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Menu extends Model
{
    protected $table    = 'admin_menu';
    protected $func_db  = 'admin_function';//关联功能表
 


     /* 新增 */
    public function menu_insert($data)
    {
        return Db::name($this->table)->insertGetId($data);
    }

    /* 新增 */
    public function child_insert($data)
    {
            Db::startTrans();//开启事务
        try {
            $menu_id = Db::name($this->table)->insertGetId($data);
            if ($data['c_pid']) {
            /*添加功能*/
               if(!$data['c_murl']){
                $data['c_murl'] = CONTROLLER_NAME.'/'.ACTION_NAME;
               }
               $arr = ['c_functionname'=>$data['c_mname'],'c_functionkey'=>$data['c_murl'],'c_menuid'=>$data['c_pid'],'c_sonid'=>$menu_id];
               $func = Db::name($this->func_db)->insertGetId($arr);
            }

            Db::commit();//提交      
        } catch (\Exception $e) {
            Db::rollback();//回滚
        }
        return $menu_id;
    }

    /* 更新 */
    public function menu_update($data,$where)
    {

        Db::startTrans();//开启事务
        try{
            $sum = Db::name($this->table)->where($where)->update($data);
            if ($sum) 
            {
                //编辑功能
                $arr = ['c_functionname'=>$data['c_mname'],'c_functionkey'=>$data['c_murl']];
                $func = Db::name($this->func_db)->where(['c_sonid'=>$where['c_id']])->update($arr);
            }
            Db::commit();//提交
        } catch(Expection $e){
            Db::rollback();//回滚
        }
        return $sum;
    }

    /* 删除 */
    public function menu_del($where)
    {
        Db::startTrans();//开启事务
        try
        {
            $res = Db::name($this->table)->where($where)->delete();
             if ($res) 
            {
                //删除功能
                $where2 = ['c_sonid'=>$where['c_id']];
                $func = Db::name($this->func_db)->where($where2)->delete();
            Db::commit();//提交    
            }
        } catch(Expection $e){
            Db::rollback();
        }
        return $res;
    }

    /* 条件查询 */
    public function menu_search($where,$type = '')
    {
    	if ($type) 
    	{
    		return Db::name($this->table)->where($where)->find();
    	}
    	return Db::name($this->table)->where($where)->paginate(15);
    }  

    /* 一级菜单查询 */
    public function menu_list()
    {

        $info = Db::name($this->table)->where(['c_pid'=>0])->paginate(15);
      
        return $info;
    }
    
    /* 分页菜单查询 */
    public function menu_child($where)
    {
        if ($where) 
        {
            return Db::name($this->table)->where($where)->paginate(15);
        }
        return false;
    }
}

?>