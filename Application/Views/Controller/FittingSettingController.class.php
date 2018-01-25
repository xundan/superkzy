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
        if(''){
            dump(1);
        }else{
            dump(2);
        }
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
            $where['group_id'] = $subInfo['group_id'];
            $records = M('ck_fitting_setting')->where($where)->select();
            if($records){
                $whereFreight['area_start_id'] = array();
                $whereFreight['area_end_id'] = array();
                foreach($records as $record){
                    array_push($whereFreight['area_start_id'],array('like',substr($record['area_start_id'],0,4)."%"));
                    array_push($whereFreight['area_end_id'],array('like',substr($record['area_end_id'],0,4)."%"));
                }
                array_push($whereFreight['area_start_id'],'OR');
                array_push($whereFreight['area_end_id'],'OR');
                $month = date('Y-m-d',(time()-24*3600*30));
                $whereFreight['record_time'] = array('gt',$month);
                $whereFreight['invalid_id'] = 0;
                $result = M('ck_freight')->where($whereFreight)->count();
                echo $result;
            }else{
                echo self::FAIL;
            }
        }else{
            echo self::FAIL;
        }
    }

    /**
     * 信息删除
     */
    public function infoDelAction(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['id'] = $subInfo['id'];
        $data['invalid_id']=2;
        $result=M('ck_fitting_setting')->where($where)->save($data);
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
}

