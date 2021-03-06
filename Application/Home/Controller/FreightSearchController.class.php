<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/1
 * Time: 16:28
 */

namespace Home\Controller;

use Home\Common\CardList\WhereConditions;
use Think\Controller;
use Home\Model\MessagesModel;
use Home\Common\CardList\CardList;
use Think\Log;

class FreightSearchController extends Controller
{
    const countRow = 10;
    private $__resultReply = array();
    private $__existArray = array();
    public function show(){
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());

        $where = array();
        $subInfo = I('post.', '', 'strip_tags,trim');
        //下拉刷新
        if($subInfo['isAjax'] == 1){
            //ajax刷新请求数据
            $where = $this->setWhere();
            $result['data'] = $this->getOrder($where);
            $resultNum = count($result['data']);
            if($resultNum < self::countRow){
                $result['end_tag'] = 'end';
            }else{
                $result['end_tag'] = 'continue';
            }
//            if($resultNum == 0 && $subInfo['area_start'] && $subInfo['area_end']){
//                $result['forecast'] = 1;
//                $distance = $this->getDistance($subInfo['area_start'],$subInfo['area_end']);
//                $result['freight_forecast'] = $this->freightForecast($subInfo['area_start'],$subInfo['area_start'],$distance);
//            }
            echo json_encode($result);
            return;
        }else{
            //页面提交
//            $where = $this->setWhere();
//            $result = $this->getOrder($where);
//            $this->assign('message',$result);
//            if(count($result) == 0){
//                $this->assign('none','empty');
//            }
        }
        $this->display();
    }

    public function show_more(){
        $where = array();
        $subInfo = I('post.', '', 'strip_tags,trim');
        $where = $this->setWhere();
        $start = ($subInfo['page']-1)*self::countRow;
        $result['data'] = $this->getOrder($where,$start);
        $result['page'] = $subInfo['page'];
        if(count($result['data']) < self::countRow){
            $result['end_tag'] = 'end';
        }else{
            $result['end_tag'] = 'continue';
        }
        echo json_encode($result);
        return;
    }

    public function VoteCount(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        if($subInfo['vote'] == 'upvote'){

        }
    }

    private function setResultReply($data=array()){
        $this->__resultReply = $data;
    }

    private function getResultReply(){
        return $this->__resultReply;
    }

    private function getLevelType($id){
        $result = M('districts')->field('level_type')->where(array('id'=>$id))->find();
        return $result['level_type'];
    }

    private function setWhere(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        $where = array();
        $where['invalid_id'] = 0;
        if($subInfo['area_start']){
            $levelType = $this->getLevelType($subInfo['area_start']);
            $where['area_start_id'] = array('like',substr($subInfo['area_start'],0,$levelType*2)."%");
        }
        if($subInfo['area_end']){
            $levelType = $this->getLevelType($subInfo['area_end']);
            $where['area_end_id'] = array('like',substr($subInfo['area_end'],0,$levelType*2)."%");
        }
        $month = date('Y-m-d',(time()-24*3600*30));
        $where['record_time'] = array('gt',$month);
        return $where;
    }

    private function getOrder($where=null,$start=0,$count=self::countRow){
        $result = M('freight')->where($where)->limit($start,self::countRow)->order('record_time desc')->select();
        return $result;
    }

    private function getDistance($start_id,$end_id){
        $where['id'] = $start_id;
        $area_start = M('districts')->where($where)->find();
        $area_start_merger_name = $area_start['merger_name'];
        $where['id'] = $end_id;
        $area_end = M('districts')->where($where)->find();
        $area_end_merger_name = $area_end['merger_name'];
        $FC = A('Views/FC');
        $distance = $FC->getDistanceByAddress($area_start_merger_name, $area_end_merger_name);
        return $distance;
    }

    private function freightForecast($start_id, $end_id, $distance, $level_type = 2)
    {
        $forecast = 0;
        //起止地所属组数
        $whereFit['area_start_id'] = array('like', substr($start_id, 0, $level_type * 2) . "%");
        $whereFit['area_end_id'] = array('like', substr($end_id, 0, $level_type * 2) . "%");
        $group_id = M('fitting_setting')->where($whereFit)->field('group_id')->find();
        if ($group_id['group_id']) {
            //取当前时间公式
            $whereFormula['group_id'] = $group_id['group_id'];
            $resultFormula = M('freight_fitting')->where($whereFormula)->order('record_time desc')->find();
            if ($resultFormula) {
                $forecast = $resultFormula['a'] * $distance + $resultFormula['b'];
            }
        }
        return (int)$forecast;
    }


}