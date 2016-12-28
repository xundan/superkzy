<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/21
 * Time: 17:48
 */

namespace Home\Controller;
use Think\Controller;
use Home\Common\CardList\CoalBuyMsgCard;
header("Content-type: text/html; charset=utf-8");

class TradeSearchController extends ComController
{
    private $publicStr='';
    public function trade_search(){
        $message['title']="123";
        $message['content']="alksdjf;lasdjkf;laksdfl;kjasdlkfjlas;df";
        $message['user_name']="xuncl";
        $message['phone_number']="12341234123";
        $message['category']=2;
        $message['in_collection']="已收藏";
        $message['coal_type_name']="喷吹煤";
        $message['price']="1234";
        $message['coal_trait_name']="无烟煤";
        $message['area_start']="北京";
        $message['coal_granularity']="块煤";
        $message['quantity']="20000";
        $message['publish_time']="2000/2/2";

        $msg_card = new CoalBuyMsgCard($message);
        $this->assign("li",$msg_card->toLi());



//        如果没有刷新或筛选条件为空则输出对应标签全部信息

        //将当前的页面存入cookie中last_url字段，方便跳回

        //test search
//        $a['aaa'] = 9999;
//        $b = false;
//        $c['name'] = '';
//        if($c['name']){
//            dump(1);
//        }else{
//            dump(2);
//        }
//        $lists = M('messages')->where($a)->order('record_time desc')->select();
////        dump($lists);
//        if(empty($lists)){
//            dump(3);
////            dump($lists);
////            $this->assign('list',$lists);
//        }else{
//            dump(4);
//        }
//
//        $page = '1';
//        dump($page);
//        $d = (int)$page;
//        dump($d);
//        $this->display();
//        exit;
//

        //查询数组
        $where = array();
        //查询起始位置
        $beginStr = 0;
        //获取所有post进来的数据
        $subInfo = I('post.','','trim,strip_tags');
//        dump($subInfo);
        //获取标签号判断是求购或供应来拼接查询字段
        if(isset($subInfo['select_category'])){
            //如果标签有提交不为空
            if ($subInfo['select_category'] == 2) {
                //求购标签
                $where['category'] = 2;
            } elseif ($subInfo['select_category'] == 0) {
                //供应标签
                $where['category'] = 0;
            } else {
                //其他值理论上没有，若有也置为2即求购
                $where['category'] = 2;
            }
        }else{
            //若select_category没有设置即为初始进入或刷新，标签则为求购
            $where['category'] = 2;
        }
        //筛选条件拼接查询字段
        if($subInfo['filter_kind'] || $subInfo['filter_granularity'] || $subInfo['filter_heat_min'] || $subInfo['filter_heat_max']){
            //如果用户进行了筛选条件提交
            //去产品表里找符合条件的产品id
            if($subInfo['filter_kind']){
                $product['kind_id'] = array('in',$subInfo['filter_kind']);
            }else{
            }
            if($subInfo['filter_granularity']){
                $product['granularity_id'] = array('in',$subInfo['filter_granularity']);
            }else{
            }
            if($subInfo['filter_heat_min']){
                $product['heat_value_min'] = array('egt',$subInfo['filter_heat_min']);
            }else{
            }
            if($subInfo['filter_heat_max']){
                $product['heat_value_max'] = array('elt',$subInfo['filter_heat_max']);
            }else{
            }
            $productResult = M('product')->where($product)->field('id')->select();
            if($productResult){
                foreach($productResult as $item){
                    $productId[] = $item['id'];
                }
                $where['product_id'] = array('in',$productId);
            }elseif($productResult === false){
                //数据库出错
                $this->display('Common:505');
            }else{
                //查询结果为空
                //TODO 提示为空并显示更多信息

            }
        }
        //搜索框查询条件拼接
        if($subInfo['search_input']){
            //若搜索框输入有意义值不为空、null、false
            $query = $this->search_method($subInfo['search_input']);
            $where['content_all'] = $query;
        }
        //滚动翻页
        //判断当前页面
        $page = '';
        if($subInfo['page']){
            $page = (int)$subInfo['page'];
        }else{
            $page = 1;
        }
        //$countRow 每次展示和刷新的条数，默认为10
        $countRow = 5;
        $beginStr=($page-1)*$countRow;
        //查询数据库
//        dump($subInfo);
//        dump($page);
//        dump($$subInfo['page']);
//        dump($where);
//        dump($beginStr);
        $lists = M('messages')->where($where)->order('record_time desc')->limit($beginStr,5)->select();
        if($lists){
            //能取到数据,则将页数+1返回给前台
            $next = $page+1;
            $this->assign('list',$lists);
            $this->assign('next_page',$next);
        }elseif(empty($lists)){
            //数据内容为空
            $next = $page;

        }elseif($lists === false){
            //数据库出错
            $this->display('Common:505');
            exit;
        }
        if($subInfo['isAjax']){
            //判断是否是ajax刷新请求
            $returnT=array();
            $returnT['next_page']=$next;
            $returnT['data']=$lists;
            $returnT['msg']='success';
            $returnT['test']=$subInfo;
            echo json_encode($returnT);
            exit;
        }
        $this->display();
    }

    public function message_filter(){
//        $coalKind = M('coal_kind')->select();
//        $coalTrait = M('coal_trait')->select();
//        $coalGranularity = M('coal_granularity')->select();
//        $this->assign('coalKind',$coalKind);
//        $this->assign('coalTrait',$coalTrait);
//        $this->assign('coalGranularity',$coalGranularity);
        $this->display();
    }

//对字符串进行处理
    public function search_method($queryString){
        $tempStr = $this->arrange_input($queryString);
        $tempStr = explode(" ", $tempStr);
        foreach ($tempStr as $item) {
            $query[] = array('like', '%' . $item . '%');
        }
        return $query;
    }


}