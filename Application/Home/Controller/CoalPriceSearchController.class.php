<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/28
 * Time: 12:05
 */

namespace Home\Controller;

use Think\Controller;

class CoalPriceSearchController extends Controller
{
    const countRow = 2;
    public function coal_price_search(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        //下拉刷新
        if($subInfo['isAjax'] == 1){
            if($subInfo['select_category'] == 'all'){
                $result['data'] = $this->getOrder();
                echo json_encode($result);
                return;
            }elseif($subInfo['select_category'] == 'search'){

            }else{}
        }else{
        //查询界面提交或页面载入
            //查询界面提交
            if($subInfo['select_category'] == 'search'){
                $where['refinery_name'] = $subInfo['refinery_name'];
                $result = $this->getOrder($where);
                $this->assign('message',$result);
            }else{
                //页面载入显示
                $result = $this->getOrder();
                $this->assign('message',$result);
            }
        }
        $this->display();
    }

    public function coal_price_search_more(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $start = ($subInfo['page']-1)*self::countRow;
        $where['invalid_id'] = 0;
        $result['data'] = $this->getOrder($where,$start,self::countRow);
        $result['page'] = $subInfo['page']; // 把page送回去，作为校验
        echo json_encode($result);
        return;
    }

    public function refinery_search(){
        $this->display();
    }

    public function area_search(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        if($subInfo['area_name'] == '全部'){
            $where['invalid_id'] = 0;
        }else{
            $where['area_name'] = $subInfo['area_name'];
            $where['invalid_id'] = 0;
        }

        $result['refinery_name'] = D('Views/CoalPriceMessage')->where($where)->group('refinery_name')->select();
        $result['count'] = count($result['refinery_name']);
        echo json_encode($result);
    }

    public function coal_price_detail($message_id=3){
        $where['message_id'] = $message_id;
        $result = $this->getOrder($where,0,1);
        $this->assign('message',$result);
        $this->display();
    }

    private function getOrder($where=null,$start=0,$count=self::countRow){
        if($where){
            $cm =  D('Views/CoalPriceMessage')->where($where)->limit($start,$count)->select();
        }else{
            $cm =  D('Views/CoalPriceMessage')->limit($start,$count)->select();
        }
        foreach ($cm as &$item) {
            $cc = D('Views/CoalPriceContent')->where(array('message_id'=>$item['message_id']))->select();
            foreach ($cc as &$value) {
                $cdi = D('Views/CoalPriceDetailedIndex')->where(array('content_id'=>$value['content_id']))->select();
                $value['detailed_index']=$cdi;
            }
            $item['content'] = $cc;
        }
        return $cm;
    }


}