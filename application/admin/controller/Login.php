<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;

class Login extends Controller
{

    public function Index(){

         if(session('userid')){

           $this->redirect('/admin/ztgl');

        }else{
            return view();
        }

    }

    //登陆接口
   public function Login(Request $request)
    {
        header('Access-Control-Allow-Origin:*');
        if($request->isPost()){
            $data=input('post.');

            $result = $this->validate($data,
                [
                    'user_name'  => 'require',
                    'pass_word'   => 'require'
                ],
                [
                    'user_name.require'  =>  '请输入用户名',
                    'pass_word.require'   => '请输入密码'
                ]
            );
            if(true !== $result){
                // 验证失败 输出错误信息
                //dump($result);
                $this->error($result);
            }

            if(!captcha_check($data['verifyCode'])) {
                // 校验失败
                $this->error('验证码不正确');
            }



            //
            $password = substr(md5($data['pass_word']),0,25);
            $result=Db::name('user')->where('user_name',$data['user_name'])->where('pass_word',$password)->find();
            $power_id_array= explode(",", $result['power_id']);
            array_splice($power_id_array, -1, 1);
            foreach($power_id_array as $k => $v){
                $power_str = db('power')->where('id',$v)->find();
                $power_str1[$k] = $power_str['url_name'];
            }
            //print_r(json_encode($power_str1));exit;
            if($result){
                session('userid', $result['id']);
                session('username', $result['user_name']);
                session('nickname', $result['nick_name']);
                session('power', json_encode($power_str1));
                $this->success('登陆成功！', '/admin/ztgl');
            }else{
                 $this->error('用户名或者密码错误！');
                //return json(['status' => 'error','msg' => '用户名或者密码错误！']);
            }
        }
    }

    //登出接口
    public function LoginOut()//退出登陆
    {
         //销毁session
        session(null);
        //跳转页面
        $this->success('退出成功',url('/admin/login'));//跳转到登录页面
    }

}
