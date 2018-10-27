<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class User extends Common
{

    public function GetRole(){

        $data = Db::name('role')->select();

        echo json_encode($data);
    }

    public function GetPower(){

        $data = Db::name('power')->select();

        echo json_encode($data);
    }

    public function ListUser()
    {
        //分页处理
        $fenye = request()->get();
        
        $fenye_begin = ($fenye['page']-1)*$fenye['limit'];

        $fenye_tiaoshu = $fenye['limit'];

        $resul = Db::name('user')->limit(''.$fenye_begin .','.$fenye_tiaoshu.'')->select();
        
        foreach($resul as $k => $v){
                //角色处理
                $resul[$k]['role_name'] = Db::name('role')->where('id',$v['role_id'])->value("role_name");
                //权限处理
                $power_id_array= explode(",", $v['power_id']);
                array_splice($power_id_array, -1, 1);
                $aa='';
                foreach($power_id_array as $k1 =>$v1){
                    $aa .= Db::name('power')->where('id',$v1)->value("power_name").',';
                }
                $resul[$k]['power_name'] =  $aa;
                //去除密码等字段
                unset($resul[$k]['pass_word']);
                //unset($resul[$k]['role_id']);
                //unset($resul[$k]['power_id']);
        }

        $data_count = Db::name('user')->select();
        //exit;

        $resul_data = array(
            'code' => 0,
            'msg' => '',
            'count' => count($data_count),
            'data' => $resul
        );

        //print_r($resul_data);exit;
        $resul_data_json = json_encode($resul_data );

        echo  $resul_data_json;
    }

    public function AddUser(){

        if (request()->isPost()){

            $add_data = request()->post();

            //创建时间
            $createt_time = date('y-m-d h:i:s',time());

            $add_data['createt_time'] = $createt_time;

            //md5加密密码
            $add_data['pass_word'] = substr(md5($add_data['pass_word']),0,25);

            //权限修改
            if(input('?post.power_id')){
                $quanxian='';
                foreach($add_data['power_id'] as $k => $v){
                     $quanxian .= $k.',';
                }
                 $add_data['power_id'] = $quanxian;
            }
            
            $add_data =array_diff_key($add_data, ['id' => '']);
            //print_r($add_data);exit;

            $res=Db::name('user')->insert($add_data);

            if($res){
                echo '添加成功';
            }else{
                echo '添加失败';
            }
        }
    }

    public function EditUser(){

        if (request()->isPost()){

            $edit_data = request()->post();

            //md5加密密码
            if(input('?post.pass_word')){
                $edit_data['pass_word'] = substr(md5($add_data['pass_word']),0,25);
            }

            //权限修改
            if(input('?post.power_id')){
                $quanxian='';
                foreach($edit_data['power_id'] as $k => $v){
                     $quanxian .= $k.',';
                }
                 $edit_data['power_id'] = $quanxian;
            }

            //print_r($edit_data);exit;
            //$edit_data =array_diff_key($edit_data, ["action" => ""]);

            //$res=Db::table('wzgl_user')->update($edit_data);
            $res=db('user')->update($edit_data);

            if($res >= 1){
                echo '修改成功';
            }else{
                echo '修改失败，记录相同';
            }
        }
    }

    public function DelUser(){

        if (request()->isPost()){

            //dump(request()->post());exit;

            $del_data = request()->post();

            //$res=Db::table('wzgl_ztgl')->where('id='.$del_data['id'])->delete();

            $res=db('user')->delete($del_data['id']);

            if($res >= 1){
                echo '删除成功';
            }else{
                echo '删除失败';
            }
        }
    }
}
