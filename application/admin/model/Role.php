<?php
namespace app\admin\model;
use think\Db;
use think\Model;

class Role extends Model
{
    protected $table    = 'admin_role';//用户角色
    protected $other  = 'admin_role_function';//角色功能表
 
    /* 角色列表 */
    function r_list(){
        return Db::name($this->table)->select();
    }

    /* 查询 */
    function r_search($where){
        return Db::name($this->table)->where($where)->find();
    } 

    /* 角色添加 */
    function r_insert($data){
         return Db::name($this->table)->insertGetId($data);
    }

    /* 角色编辑 */
    function r_save($data,$where){
         return Db::name($this->table)->where($where)->update($data);
    }

    /* 角色删除 */
    function r_del($where= ''){
        return Db::name($this->table)->where($where)->delete();
    }

    /* 获取用户权限 */
    function usr_rol($param){
        if (is_array($param)) 
        {
            //获取角色
            $res = Db::name('admin_users a')->join('t_admin_user_role b','a.c_id = b.c_userid','left')->where($param)->field('b.c_roleid')->select();
            $role_id = $res[0]['c_roleid'];
        }else{
            $role_id = $param;
        }
       
        
        //获取角色权限
        $rol = Db::name('admin_role_function')->where(['c_roleid'=>$role_id])->field('c_functionid')->select();
        return ['c_roleid'=>$role_id ,'rol'=>$rol];
    }


    /* 获取所有权限 */
    function rol_all(){
        $ar['c_pid'] = 0;
        $Model_1 =Db::name('admin_menu')->where($ar)->select();//一级菜单

        foreach ($Model_1 as $key => $value) 
        {
                $whr['c_menuid'] = $value['c_id'];
                $Model_1[$key]['son'] = Db::name('admin_function a')->where($whr)->field('a.*')->select();//二级权限
                foreach ($Model_1[$key]['son'] as $k => $v) 
                {
                   $wh['a.c_menuid'] = $v['c_sonid'];
                   $Model_1[$key]['son'][$k]['Grandson'] = Db::name('admin_function a')->where($wh)->field('a.*')->select();//->join('t_admin_role_function b','a.c_id=b.c_functionid','LEFT')
                }
        }
        
        return $Model_1;
    }

    /* 角色分配权限 */
    function rol_change($rol_id,$rol_functionid){
        Db::startTrans();//开启事务
        $where['c_roleid'] = $rol_id;
        $olds = Db::name($this->other)->where($where)->field('c_id')->select();//过去角色权限
        $c_ids = '';
        foreach ($olds as $key => $value) 
        {
            $c_ids[] = $value['c_id'];
        }
        //删除过去权限
        $whr['c_id'] = ['in',$c_ids];
        if (!empty($olds)) 
        {
           $res = Db::name($this->other)->where($whr)->delete();
           if (!$res) 
           {
                Db::rollback();//回滚
                return ['type'=> false,'msg'=>'原权限删除失败,请重新尝试...！','url'=>url('choice_list',['c_id'=>$rol_id])];
           }
        }
        if(!$rol_functionid){
            return ['type'=> false,'msg'=>'请至少添加一个权限！','url'=>url('choice_list',['c_id'=>$rol_id])];
        }
        //重写权限
        foreach ($rol_functionid as $k => $v) 
        {
            $data[$k]['c_functionid'] = $v;
            $data[$k]['c_roleid']     = $rol_id;
        }
        $is = Db::name($this->other)->insertAll($data);
        if (!$is) 
        {
            Db::rollback();//回滚
            return ['type'=> false,'msg'=>'添加权限错误，请重新添加...！','url'=>url('choice_list',['c_id'=>$rol_id])];
        }else{
            Db::commit();//提交
             return ['type'=> true,'msg'=>'恭喜，权限添加成功...！','url'=>url('choice_list',['c_id'=>$rol_id])];
        }

    }
}

?>