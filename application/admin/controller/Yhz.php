<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Yhz extends Common
{
    public function Index(){

        $html = $this->quanxian();
        return view('index',$html);
        
    }

   
}
