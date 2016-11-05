<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/21
 * Time: 17:48
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class TradeSearchController extends ComController
{
    private $publicStr='';
    public function trade_search(){
//        $chaxun = $this->search_method();
        $str = '区';
        $where['detail_area_start'] = array('like','%'.$str.'%');
        $condition['detail_area_start'] = $chaxun;
        $res = M('messages')->where($where)->select();
        $temp = array('column_name','column_type');
//        $queryString = $this->search_method();
//        $publicStr = $this->search_method();
//        search_method();
        $column = M('messages')->getDbFields();
        $publicStr[0] = 'a';
        $publicStr[1] = 'b';
        $publicStr[2] = 'c';
        $publicStr[3] = 'dd';
        foreach ($publicStr as $value) {
            foreach ($column as $item) {
                $condition[$item] = $value;
            }
        }
//        dump($condition);
//        $res = M('messages')->getBytitle('su_orders',2);
//        $lastSql = M()->getLastSql();
//        dump($lastSql);
////        dump($_COOKIE);
//        $str = "select * from ck_messages where origin = 0";
//        $res = M()->query($str);
//        dump($res);
//        $lastSql = M()->getLastSql();
//        dump($lastSql);
//        如果没有刷新或筛选条件为空则输出对应标签全部信息



        $this->display();
    }
//对字符串进行处理
    public function search_method(){
        $subInfo = I("post.","",'strip_tags');
//        dump($subInfo);
        $tempStr = $this->arrange_input($subInfo['search_input']);
        $tempStr = explode(" ",$tempStr);
//        dump($tempStr);
        $tempStrSerialize = serialize($tempStr);
//        dump($tempStrSerialize);
        $str = 'asdasd';
        $ta['name'] = 'wa';
        $ta['token'] = 'bs';
        $_SESSION['user_info'] = $ta;
        cookie($_SESSION['user_info'],$tempStr,3600*24*365);
        cookie('a',$tempStr,3600*24*365,'ck_');
        cookie($str,$tempStr,3600*24*365);
//        cookie($str,null);
        $_COOKIE['d'] = unserialize($_COOKIE['d']);
        cookie(null);
//        dump($_COOKIE);
        $queryString = "";
        foreach($tempStr as $item){
            $queryString .= " and like '".$item." '";
        }
        dump($queryString);
        $publicStr = $tempStr;
        $this->redirect('trade_search');
//        return $tempStr;
//        return $queryString;
    }
//    查询数组
//    public function query_array(){
//        foreach
//    }


}