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
//        $a = I('post.asd','','trim');
//        echo $a;
//        $str = '区';
//        $where['detail_area_start'] = array('like','%'.$str.'%');
//        $condition['detail_area_start'] = $chaxun;
//        $res = M('messages')->where($where)->select();
//        $temp = array('column_name','column_type');
//        $queryString = $this->search_method();
//        $publicStr = $this->search_method();
//        search_method();
//        $column = M('messages')->getDbFields();
//        $publicStr[0] = 'a';
//        $publicStr[1] = 'b';
//        $publicStr[2] = 'c';
//        $publicStr[3] = 'dd';
//        foreach ($publicStr as $value) {
//            foreach ($column as $item) {
//                $condition[$item] = $value;
//            }
//        }

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

        //查询数组
        $where=[];

        //获取标签号判断是求购或供应
        $subInfo = I('post.','','trim,strip_tags');
//        dump($subInfo);
//        dump(cookie('trade_search_filter_kind'));
        if(isset($subInfo['select_category'])){
//            dump($subInfo);
            if($subInfo['select_category'] == 2){
                $where['category'] = 2;
                //筛选条件构成
                if($subInfo['filter_kind'] || $subInfo['filter_granularity'] || $subInfo['filter_heat_min'] || $subInfo['filter_heat_max']){
                    //去产品表里找符合条件的产品id
//                    dump(2);
                    if($subInfo['filter_kind']){
                        $pro['kind_id'] = array('in',$subInfo['filter_kind']);
                    }
                    if($subInfo['filter_granularity']){
                        $pro['granularity_id'] = array('in',$subInfo['filter_granularity']);
                    }
                    if($subInfo['filter_heat_min'] && $subInfo['filter_heat_max']){
                        $pro['heat_value_min'] = array('egt',$subInfo['filter_heat_min']);
                        $pro['heat_value_max'] = array('elt',$subInfo['filter_heat_max']);
                    }elseif($subInfo['filter_heat_min']){
                        $pro['heat_value_min'] = array('egt',$subInfo['filter_heat_min']);
                    }elseif($subInfo['filter_heat_min']){
                        $pro['heat_value_max'] = array('elt',$subInfo['filter_heat_max']);
                    }
                    $proResult = M('product')->where($pro)->field('id')->select();
                    if($proResult){
//                        dump($proResult);
                        foreach($proResult as $item){
                            $proID[] = $item['id'];
                        }
                        $where['product_id'] = array('in',$proID);
                    }else{
                        //查询失败
                    }
                }
                //搜索框查询条件构成
                if($subInfo['search_input']){
                    $query = $this->search_method($subInfo['search_input']);
//                    dump($query);
                    $where['content_all'] = $query;
//                    dump($where);
                }

                $lists = M('messages')->where($where)->select();
//                $test = 353;
//                $map['id'] = array('gt',$test);
//                $lists = M('messages')->where($map)->field('id')->select();
//                dump($lists);
                if($lists){
                    $this->assign('list',$lists);
                }
            }elseif($subInfo['select_category'] == 0){
                $where['category'] = 0;
            }
        }

//        if($subInfo['search_input']){
//            dump(4);
//            dump($subInfo['search_input']);
//        }
//        else{
//        }

//        dump($where);
//        $a = 0;
        $b = 'cao';
//        $a['id'] = array('in',$b);
        $a['name'] = array(array('like','%'.$b.'%'),array('notlike','%区'),'and');
//        dump($a);
        $test = $subInfo['search_input'];
        $test = explode(' ',$test);
        foreach($test as $item){
            $temp[] = array('like','%'.$item.'%');
        }
//        dump($temp);

        $tt['name'] =$temp;

//        dump($tt);


//        $c = M('messages')->where($b)->select();
//        dump($c);

        $this->display();
    }

    public function message_filter(){
        $coalKind = M('coal_kind')->select();
        $coalTrait = M('coal_trait')->select();
        $coalGranularity = M('coal_granularity')->select();
        $this->assign('coalKind',$coalKind);
        $this->assign('coalTrait',$coalTrait);
        $this->assign('coalGranularity',$coalGranularity);
        $this->display();
    }

    public function ajax_filter(){
        $zzz = $_POST['heat_min'];
        $this->publicStr = $zzz;
//        return $zzz;
    }


//对字符串进行处理
    public function search_method($queryString){
        $tempStr = $this->arrange_input($queryString);
        $tempStr = explode(" ",$tempStr);
        foreach($tempStr as $item){
            $query[] = array('like','%'.$item.'%');
        }
        return $query;

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