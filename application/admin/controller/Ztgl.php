<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Ztgl extends Common
{
    public function index(){
        $html = $this->quanxian();//print_r($a);exit;
        return view('index',$html);
        //return view();
    }

    /******获取数据并入库******/
    public function getData(){

    	//或者get参数（网站域名和专题目录参数）
    	$canshu = request()->get();

		$url = $canshu['yuming'].'/list_zt.php?lujing='.$canshu['lujing'];
		//echo $url;exit;
		
		//请求获取的域名参数，取得返回的json值
		$content = file_get_contents($url);

		$data = json_decode($content,true);

		//判断获取的数据是否为空
		if($data){

			$old_data = Db::name('ztgl')->select();
			foreach($data as $k => $v){
				foreach($old_data as $k1 => $v1){
					if($v['webaddress'] === $v1['webaddress']){
						unset($data[$k]);
						break;
						 //$aar = array_values($arr);
					}
				}
				
			}

			//print_r($data);exit;
			if($data){

				foreach($data as $k => $v){
					$data[$k]['tuiguang'] = $data[$k]['tuiguang'] === '是' ? 1 : 2;
				}
				//入库操作
				Db::name('ztgl')->insertAll($data);
				echo json_encode(array(
					'code' => 1,
					'infect' => count($data)
				));
			}else{
				echo json_encode(array(
					'code' => 2
				));
			}

		}else{
			echo json_encode(array(
					'code' => 3
				));
		}
		
    }

    /******取出数据列表******/
    public function ztglList(){

    	$fenye = request()->get();
    	
    	$fenye_begin = ($fenye['page']-1)*$fenye['limit'];

    	$fenye_tiaoshu = $fenye['limit'];

    	$resul = Db::name('ztgl')->limit(''.$fenye_begin .','.$fenye_tiaoshu.'')->select();

    	foreach($resul as $k => $v){

    		$resul[$k]['tuiguang'] = $resul[$k]['tuiguang'] === 1 ? '是' : '否';

    	}

    	
    	$data_count = Db::name('ztgl')->select();
    	//exit;

    	$resul_data = array(
    		'code' => 0,
    		'msg' => '',
    		'count' => count($data_count),
    		'data' => $resul
    	);

		$resul_data_json = json_encode($resul_data );

    	echo $resul_data_json;
    }

     /******添加数据******/
    public function ztglAdd(){

    	if (request()->isPost()){

    		$add_data = request()->post();

    		$add_data['tuiguang'] = $add_data['tuiguang'] === '是' ? 1 : 2;

    		$add_data =array_diff_key($add_data, ['id' => '', "action" => ""]);

    		 //dump($add_data);

    		 //exit;

    		$res=Db::name('ztgl')->insert($add_data);

    		if($res){
    			echo '添加成功';
    		}else{
    			echo '添加失败';
    		}
    	}
    
    }

     /******编辑数据******/
    public function ztglEdit(){
    	
    	if (request()->isPost()){

    		$edit_data = request()->post();

    		$edit_data['tuiguang'] = $edit_data['tuiguang'] === '是' ? 1 : 2;

    		$edit_data =array_diff_key($edit_data, ["action" => ""]);

    		$res=Db::table('wzgl_ztgl')->update($edit_data);

    		if($res >= 1){
    			echo '修改成功';
    		}else{
    			echo '修改失败，记录相同';
    		}
    	}
    }

	/******删除数据******/
	public function ztglDel(){

		if (request()->isPost()){

			//dump(request()->post());exit;

			$del_data = request()->post();

			//$res=Db::table('wzgl_ztgl')->where('id='.$del_data['id'])->delete();

			$res=Db::table('wzgl_ztgl')->delete($del_data['id']);

			if($res >= 1){
				echo '删除成功';
			}else{
				echo '删除失败';
			}
		}
	}

    /******取出搜索数据列表******/
    public function ztglSearchList(){

    	//获取参数
    	$fenye = request()->get();

    	//搜索参数处理
    	$where = '1';
    	if($fenye['ziduan']==='webtitle' || $fenye['ziduan']==='webaddress' || $fenye['ziduan']==='beizhu'){
    		if($fenye['tuiguang']==='是' || $fenye['tuiguang']==='否'|| $fenye['tuiguang']===''){
    			if($fenye['fenlei']==='咨询页' || $fenye['fenlei']==='病种页' || $fenye['fenlei']==='其他' || $fenye['fenlei']===''){
    				$keyword = $fenye['keyword']==='' ? '' : ' and '.$fenye['ziduan'].' like \'%'.$fenye['keyword'].'%\'';
    				if($fenye['tuiguang']===''){
	    				$tuiguang = '';
	    			}else{
	    				$tuiguang = $fenye['tuiguang'] === '是' ? ' and tuiguang=1' : ' and tuiguang=2';
	    			}
    				$fenlei = $fenye['fenlei'] === '' ? '' : ' and fenlei=\''.$fenye['fenlei'].'\'';
    				$where .= $keyword.$tuiguang.$fenlei;
    				//echo $where;exit;
    			}else{
    				echo '提交参数异常';exit;
    			}
    		}else{
    			echo '提交参数异常';exit;
    		}
    	}else{
    		echo '提交参数异常';exit;
    	}
    	$where = $where==='1' ? 1 : $where;

    	//分页参数处理
    	$fenye_begin = ($fenye['page']-1)*$fenye['limit'];
    	$fenye_tiaoshu = $fenye['limit'];

    	//查询数据库
    	$resul = Db::name('ztgl')->where($where)->limit(''.$fenye_begin .','.$fenye_tiaoshu.'')->select();

    	//“是否推广”参数转换处理
    	foreach($resul as $k => $v){

    		$resul[$k]['tuiguang'] = $resul[$k]['tuiguang'] === 1 ? '是' : '否';

    	}

    	//查询总记录数
    	$data_count = Db::name('ztgl')->where($where)->select();
    	//exit;

    	//构造返回数组
    	$resul_data = array(
    		'code' => 0,
    		'msg' => '',
    		'count' => count($data_count),
    		'data' => $resul
    	);

    	//转换json数据格式
		$resul_data_json = json_encode($resul_data );

		//返回json格式数据
    	echo $resul_data_json;
    }
}
