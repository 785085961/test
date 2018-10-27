<?php
/**********************
遍历当前目录,筛选html文件
***********************/
header('Content-Type: text/html; charset=utf-8');

function my_dir($dir) {
    $files = array();
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
                if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                    $files[$file] = my_dir($dir."/".$file);
                } else { //不然就将文件的名字存入数组；
                    $files[] = $file;
                }
 
            }
        }
        closedir($handle);
        return $files;
    }
}

function findHtml($dir,$file='html',$obj='/zt/'){

	$reule=array(
		'webaddress' => array(),
		'webtitle' => array(),
		'tuiguang' => array()
	);

	if(is_array($dir)){

		foreach($dir as $k => $v){
			if(is_array($v)){
				//$k.=$k;
				$aa = $obj.$k.'/';
				$reule1 = findHtml($v,'html',$aa);
				$reule['webaddress'] = array_merge($reule['webaddress'],$reule1['webaddress']);
				$reule['webtitle'] = array_merge($reule['webtitle'],$reule1['webtitle']);
				$reule['tuiguang'] = array_merge($reule['tuiguang'],$reule1['tuiguang']);
			}else{
				if(strpos($v,'.html') !== false || strpos($v,'.htm') !== false ){
				
					/***webaddress写入数组****/
					$aa = $obj.$v;
					$encode = mb_detect_encoding($aa, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
					//$contents = mb_convert_encoding($contents, 'UTF-8', $encode);
					//if($encode === 'EUC-CN'){
					//	$aa = mb_convert_encoding($aa, 'UTF-8', $encode);
					//}
					array_push($reule['webaddress'],mb_convert_encoding($aa, 'UTF-8', $encode));
					
					/***webtitle写入数组****/
					$filename = dirname(__FILE__).$aa;
					$handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
					
					//通过filesize获得文件大小，将整个文件一下子读到一个字符串中
					$contents = fread($handle, filesize ($filename));
					

					
					$encode = mb_detect_encoding($contents, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
					$contents = mb_convert_encoding($contents, 'UTF-8', $encode);

					$postb=strpos($contents,'<title>')+7;
					$poste=strpos($contents,'</title>');
					$length=$poste-$postb;
					$title = substr($contents,$postb,$length);
					
					fclose($handle);
					//$title = $title==='' ? '无' :  $title;
					//$title = iconv("utf-8","gbk",$title);
					
					array_push($reule['webtitle'],$title);
				
					/****默认推广（否）***/
					array_push($reule['tuiguang'],'否');
					
				}
			}
		}
		
	}
	
	return $reule;
}

/*****重组数据***/
function recombinedData($data){

	$reule=array();
	foreach($data as $k => $v){
		foreach($v as $kk => $vv){
			$reule[$kk][$k] = $vv;
		}
	}
	return $reule;
	
}

$lujing = $_GET['lujing'];     //专题路径,(相对网站根目录)

$ceshi = my_dir(dirname(__FILE__).$lujing);

/*echo "<pre>";
print_r(recombinedData(findHtml($ceshi,'html','/zt/'))); 
echo "</pre>";*/

//print_r(recombinedData(findHtml($ceshi,'html',$lujing)));
echo json_encode(recombinedData(findHtml($ceshi,'html',$lujing)));

