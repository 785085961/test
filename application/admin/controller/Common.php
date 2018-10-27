<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
 
class Common extends Controller
{
	/**
	权限判断
	**/
	public function _initialize()
	{
		//检查是否登录
		 if(!session('userid')){
			 $this->error('请先登录！',url('/admin/login'));
		 }else{
		 	//检查是否有对应权限
		 	$power = json_decode(session('power'));
		 	$controller = strtolower(request()->controller());
		 	$aa = false;
		 	foreach($power as $k => $v){
		 		if($controller===$v){
		 			$aa = true;
		 			break;
		 		}else{
		 			continue;
		 		}
		 	}
		 	if(!$aa){
		 		$this->error('没有权限！');
		 	}
		 }

	}

	/**
	权限判断，根据不同权限输出不同内容
	**/
	public function quanxian(){
		
		//检查是否有对应权限
	 	$power = json_decode(session('power'));
	 	$returnHtml = array();
	 	$returnHtml['userNavHtml'] = '';
	 	$returnHtml['leftNavHtml'] = '';
	 	//用户html
	 	if(in_array('user',$power)){
	 		$returnHtml['userNavHtml'] = '<li class="layui-nav-item"><a id="user" href="javascript:;">用户管理</a></li>'
					      .'<li class="layui-nav-item"><a id="role" href="javascript:;">角色管理</a></li>'
					      .'<li class="layui-nav-item"><a id="power" href="javascript:;">权限管理</a></li>';
	 	}
	 	//选中状态
	 	$controller = strtolower(request()->controller());
	 	//专题管理html
	 	if(in_array('ztgl',$power)){
	 		if($controller==='ztgl'){
	 			$layuiNav = 'layui-nav-itemed';
	 			$layuiNavChild = 'layui-this';
	 		}else{
	 			$layuiNav = '';
	 			$layuiNavChild = '';
	 		}
	 		$returnHtml['leftNavHtml'] .= '<li class="layui-nav-item '.$layuiNav.'">'
								          .'<a  href="javascript:;">专题管理</a>'
								          .'<dl class="layui-nav-child">'
								            .'<dd class="'.$layuiNavChild.'"><a href="/admin/ztgl">专题列表</a></dd>'
								          .'</dl>'
								        .'</li>';
	 	}
	 	//优化站html
	 	if(in_array('yhz',$power)){
	 		if($controller==='yhz'){
	 			$layuiNav = 'layui-nav-itemed';
	 			$layuiNavChild = 'layui-this';
	 		}else{
	 			$layuiNav = '';
	 			$layuiNavChild = '';
	 		}
	 		$returnHtml['leftNavHtml'] .= '<li class="layui-nav-item '.$layuiNav.'">'
								          .'<a  href="javascript:;">优化站管理</a>'
								          .'<dl class="layui-nav-child">'
								            .'<dd class="'.$layuiNavChild.'"><a href="/admin/yhz">列表</a></dd>'
								          .'</dl>'
								        .'</li>';
	 	}
	 	return $returnHtml;

	}

}