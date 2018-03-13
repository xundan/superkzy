<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/12
 * Time: 15:02
 */

namespace Views\Controller;

use Think\Controller;


class FittingSettingController extends Controller
{
    const SUCCESS = 1;
    const FAIL = -1;
    const ANOTHER = 0;

    public function fitting_setting()
    {
        $this->display();
    }

    public function test(){
        $a = '1,2,3,5';
        $b = explode(',',$a);
    }

    /**
     * 信息录入
     */
    public function infoInputAction()
    {
        $subInfo = I('post.', '', 'trim');
        $data['area_start'] = $subInfo['origin'];
        $data['area_start_id'] = $subInfo['start_id'];
        $data['area_end'] = $subInfo['endless'];
        $data['area_end_id'] = $subInfo['end_id'];
        $add=M('ck_fitting_setting')->where($data)->find();
        if($add){
            echo self::ANOTHER;
        }else{
            if($subInfo['group_id']){
                $data['group_id'] = $subInfo['group_id'];
                if($subInfo['group_name']){
                    $data['group_name'] = $subInfo['group_name'];
                }else{
                    $temp = M('ck_fitting_setting')->where('group_id = %d',$subInfo['group_id'])->find();
                    if($temp){
                        $data['group_name'] = $temp['group_name'];
                    }
                }
            }
            $result = M('ck_fitting_setting')->add($data);
            if ($result) {
                echo self::SUCCESS;
            } else {
                echo self::FAIL;
            }
        }
    }

    /**
     * 信息展示
     */
    public function infoShowAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $returnArr = array();
        $where=array();
        if($subInfo['group_id']){
            $where['group_id'] = $subInfo['group_id'];
        }else{
        }
        $result = M('ck_fitting_setting')->where($where)->order('group_id asc')->select();
        if($result){
            $returnArr['msg'] = 'yes';
            $returnArr['data'] = $result;
        }else{
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
    }

    /**
     * 组运费条数查询
     */
    public function freightCount(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        if($subInfo['group_id']){
            if($subInfo['group_id'] == '#'){
                $where['group_id'] = array('exp','is not null');
            }else if(strpos($subInfo['group_id'],',')){
                $groupArr = explode(',',$subInfo['group_id']);
                $where['group_id'] = array('in',$groupArr);
            }else{
                $where['group_id'] = $subInfo['group_id'];
            }
            $records = M('ck_fitting_setting')->where($where)->select();
            if($records){
//                $whereFreight['area_start_id'] = array();
//                $whereFreight['area_end_id'] = array();
                $month = date('Y-m-d',(time()-24*3600*30));
                $sum = 0;
                foreach($records as $record){
                    $startLevel = M('ck_districts')->field('level_type')->where(array('id'=>$record['area_start_id']))->find();
                    $endLevel = M('ck_districts')->field('level_type')->where(array('id'=>$record['area_end_id']))->find();
//                    array_push($whereFreight['area_start_id'],array('like',substr($record['area_start_id'],0,$startLevel['level_type']*2)."%"));
//                    array_push($whereFreight['area_end_id'],array('like',substr($record['area_end_id'],0,$endLevel['level_type']*2)."%"));
                    $whereFreight['area_start_id'] = array('like',substr($record['area_start_id'],0,$startLevel['level_type']*2)."%");
                    $whereFreight['area_end_id'] = array('like',substr($record['area_end_id'],0,$endLevel['level_type']*2)."%");
                    $whereFreight['record_time'] = array('gt',$month);
                    $whereFreight['invalid_id'] = 0;
                    $count = M('ck_freight')->where($whereFreight)->count();
                    $sum += $count;
                }
//                array_push($whereFreight['area_start_id'],'OR');
//                array_push($whereFreight['area_end_id'],'OR');
//                $month = date('Y-m-d',(time()-24*3600*30));
//                $whereFreight['record_time'] = array('gt',$month);
//                $whereFreight['invalid_id'] = 0;
//                $result = M('ck_freight')->where($whereFreight)->count();
//                $sql = M()->getLastSql();
//                echo $sql;
//                echo $result;
                echo $sum;
            }else{
                echo self::FAIL;
            }
        }else{
            $month = date('Y-m-d',(time()-24*3600*30));
            $whereFreight['record_time'] = array('gt',$month);
            $whereFreight['invalid_id'] = 0;
            $result = M('ck_freight')->where($whereFreight)->count();
            echo $result;
        }
    }

    /**
     * 信息删除
     */
    public function infoDelAction(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['id'] = $subInfo['id'];
//        $data['invalid_id']=2;
        $result=M('ck_fitting_setting')->where($where)->delete();
        if($result=== false){
            echo self::FAIL;
        }else{
            echo self::SUCCESS;
        }
    }

    /**
     * 信息恢复
     */
    public function infoRecAction(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['id'] = $subInfo['id'];
        $data['invalid_id']=0;
        $result=M('ck_fitting_setting')->where($where)->save($data);
        if($result=== false){
            echo self::FAIL;
        }else{
            echo self::SUCCESS;
        }
    }

    /**
     * 合并组
     */
    public function areaMerge(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['id'] = array('in',$subInfo['mergeArr']);
        if($subInfo['group_id']){
            //直接用提交的组信息更新组信息
            $data['group_id'] = $subInfo['group_id'];
            $data['group_name'] = $subInfo['group_name'];
            $result = M('ck_fitting_setting')->where($where)->save($data);
        }else{
            //没有组id从设置表找上一次组id，自增并更新组信息
            $whereConf['k'] = 'fitting_group_id';
            $resultConf = M('sys_conf')->where($whereConf)->find();
            $newConf = (int)$resultConf['v'] + 1;
            $data['group_id'] = $newConf;
            $data['group_name'] = $subInfo['group_name'];
            $result = M('ck_fitting_setting')->where($where)->save($data);
            //自增
            $dataConf['v'] = $newConf;
            M('sys_conf')->where($whereConf)->save($dataConf);
        }
        if($result === false){
            echo self::FAIL;
        }else{
            echo self::SUCCESS;
        }
    }

    /**
     * 省到省信息条数展示
     */
    public function P2P(){
        $proArr = M('ck_districts_for_freight')->where('level_type = 1')->select();
        foreach($proArr as $itemS){
            foreach($proArr as $itemE){
                $where['area_start_id'] = array('like',substr($itemS['id'],0,2)."%");
                $where['area_end_id'] = array('like',substr($itemE['id'],0,2)."%");
                $month = date('Y-m-d',(time()-24*3600*30));
                $whereFreight['record_time'] = array('gt',$month);
                $whereFreight['invalid_id'] = 0;
                $count = M('ck_freight')->where($where)->count('id');
                dump($itemS['name']."=>".$itemE['name'].":   ".$count);
            }
        }
    }

    /**
     * 临时条数
     */
    public function sx2sx(){
        //14
        $sArr = M('ck_districts_for_freight')->where('level_type = 2 and id like "14%"')->select();
        foreach($sArr as $itemS){
            foreach($sArr as $itemE){
                $where['area_start_id'] = array('like',substr($itemS['id'],0,4)."%");
                $where['area_end_id'] = array('like',substr($itemE['id'],0,4)."%");
                $month = date('Y-m-d',(time()-24*3600*30));
                $where['record_time'] = array('gt',$month);
                $where['invalid_id'] = 0;
                $count = M('ck_freight')->where($where)->count('id');
                dump($itemS['name']."=>".$itemE['name'].":   ".$count);
            }
        }
    }

    public function sx2hb(){
        //14 13
        $s1Arr = M('ck_districts_for_freight')->where('level_type = 2 and id like "14%"')->select();
        $s2Arr = M('ck_districts_for_freight')->where('level_type = 2 and id like "13%"')->select();
        foreach($s1Arr as $itemS){
            foreach($s2Arr as $itemE){
                $where['area_start_id'] = array('like',substr($itemS['id'],0,4)."%");
                $where['area_end_id'] = array('like',substr($itemE['id'],0,4)."%");
                $month = date('Y-m-d',(time()-24*3600*30));
                $where['record_time'] = array('gt',$month);
                $where['invalid_id'] = 0;
                $count = M('ck_freight')->where($where)->count();
                dump($itemS['name']."=>".$itemE['name'].":   ".$count);
            }
        }
    }

    public function sx2sd(){
        //14 13
        $s1Arr = M('ck_districts_for_freight')->where('level_type = 2 and id like "14%"')->select();
        $s2Arr = M('ck_districts_for_freight')->where('level_type = 2 and id like "37%"')->select();
        foreach($s1Arr as $itemS){
            foreach($s2Arr as $itemE){
                $where['area_start_id'] = array('like',substr($itemS['id'],0,4)."%");
                $where['area_end_id'] = array('like',substr($itemE['id'],0,4)."%");
                $month = date('Y-m-d',(time()-24*3600*30));
                $where['record_time'] = array('gt',$month);
                $where['invalid_id'] = 0;
                $count = M('ck_freight')->where($where)->count();
                dump($itemS['name']."=>".$itemE['name'].":   ".$count);
            }
        }
    }

    public function deleteNear(){
        $result = M('ck_fitting_setting')->select();
        $FC = A('FC');
        foreach($result as $item){
            $whereS['id'] = $item['area_start_id'];
            $s = M('ck_districts_for_freight')->where($whereS)->find();
            $whereE['id'] = $item['area_end_id'];
            $e = M('ck_districts_for_freight')->where($whereE)->find();
            $distance = $FC->getDistanceByAddress($s['merger_name'], $e['merger_name']);
            if($distance>100000){
                dump($item['area_start']."=>".$item['area_end'].":  ".$distance);
            }
        }
    }
}

