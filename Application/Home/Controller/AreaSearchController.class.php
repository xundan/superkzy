<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/21
 * Time: 10:12
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class AreaSearchController extends ComController
{
    public function area_search(){
        //最近常用地区 从cookie中读取

        //热点地区 is_hot字段判断

        //地区列表 默认A开始
        $where['pinyin']  = array('like', 'A%');
//        $where['pinyin']  = 'Beijing';
        $default_area = M('districts')->where($where)->select();
//        dump($default_area);
        $result = $default_area;
//        dump($result);
        $this->assign('result',$result);
        $this->display();
    }
}