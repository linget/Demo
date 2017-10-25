<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Msgtemplate extends Model
{
    protected $table    = 'msgtemplate';
 
     /* 新增 */
    public function msg_insert($data)
    {
        return Db::name($this->table)->insert($data);
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
    public function Mgs_list($where,$type = '')
    {
    	if ($type) 
    	{
    		return Db::name($this->table)->where($where)->find();
    	}
    	return Db::name($this->table)->where($where)->paginate(15);
    }  

    //获取消息模板列表
    public function msg_all($where = null,$field=null)
    {
        if ($where&&$field) 
        {
            return Db::name($this->table)->where($where)->field($field)->select();
        }elseif ($field) 
        {
            return Db::name($this->table)->field($field)->select();
        }
        return Db::name($this->table)->select();
    }
}

?>