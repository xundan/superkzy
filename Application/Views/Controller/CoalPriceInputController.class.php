<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/20
 * Time: 11:07
 */

namespace Views\Controller;

use Think\Controller;
use Think\Model;
use Think\Log;

class CoalPriceInputController extends Controller
{
    public function coal_price_input(){
        $this->display();
    }

    public function coal_price_input_refinery_name_query(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        $refineryName = $subInfo['refinery_name'];
        $result = D('CoalPriceMessage')->where(array('refinery_name'=>$refineryName))->select();
        echo json_encode($result);
    }

    public function coal_price_input_query(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        $messageId = $subInfo['message_id'];
        $result = D('CoalPriceContent')->relation(true)->where(array('message_id'=>$messageId))->select();
//        $result = D('CoalPriceMessage')->relation(true)->where(array('message_id'=>$messageId))->where('invalid_id = 0')->select();
        echo json_encode($result);
    }

    public function coal_price_input_action(){
        $subInfo = I('post.', '', 'strip_tags,trim');

        $kindCount = $subInfo['kind_count'];
        $indexCount = $subInfo['index_count'];

        $data = array();
        //构造信息数据
        $Msg['area_name'] = $subInfo['area_name'];
        $Msg['refinery_name'] = $subInfo['refinery_name'];
        $Msg['supply_company'] = $subInfo['supply_company'];
        $Msg['supply_company_level'] = $subInfo['supply_company_level'];
        $Msg['phone_number'] = $subInfo['phone_number'];
        //插入信息数据
        $message_id = D('CoalPriceMessage')->add($Msg);
        $data['Msg'] = array(
            'area_name' => $subInfo['area_name'],
            'refinery_name' => $subInfo['refinery_name'],
            'supply_company' => $subInfo['supply_company'],
            'phone_number' => $subInfo['phone_number']
        );
        //循环插入类别数据
        for($i=1;$i<=$kindCount;$i++){
            $data['DetailedIndex'] = array();
            $data['kind_name'] = $subInfo['kind'.$i. '_name'];
            $data['price'] = $subInfo['kind'.$i. '_price'];
            $data['tax'] = $subInfo['kind'.$i. '_tax'];
            $data['message_id'] = $message_id;
            //循环插入详细指标数据
            for($k=1;$k<=$indexCount;$k++){
                array_push($data['DetailedIndex'],array(
                    'index_name' => $subInfo['index'.$k. '_name'],
                    'index_value' => $subInfo['kind'.$i.'_index'.$k],
                ));
            }
            $result = D('CoalPriceContent')->relation(true)->add($data);
        }
    }

    public function coal_price_input_update(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        //echo json_encode($subInfo);
        $kindCount = $subInfo['kind_count'];
        $indexCount = $subInfo['index_count'];
        $messageId = $subInfo['message_id_input'];

        //信息表数据组成
        $Msg = array();
        $Msg['area_name'] = $subInfo['area_name'];
        $Msg['refinery_name'] = $subInfo['refinery_name'];
        $Msg['supply_company'] = $subInfo['supply_company'];
        $Msg['supply_company_level'] = $subInfo['supply_company_level'];
        $Msg['phone_number'] = $subInfo['phone_number'];

        //事务开启
        $model = new Model();
        $model->startTrans();

        //事务提交标志
        $flag = true;
        $flagArray = array();

        //信息表数据更新
        $msgResult = D('CoalPriceMessage')->where(array('message_id'=>$messageId))->save($Msg);
        array_push($flagArray,$msgResult);

        //备用方法 暴力的采用删除再添加记录，主键会不连续及产生垃圾数据
//        $contentResult = D('CoalPriceContent')->relation(true)->where(array('message_id'=>$messageId))->delete();
//        for($i=1;$i<=$kindCount;$i++){
//            $data['DetailedIndex'] = array();
//            $data['kind_name'] = $subInfo['kind'.$i. '_name'];
//            $data['price'] = $subInfo['kind'.$i. '_price'];
//            $data['tax'] = $subInfo['kind'.$i. '_tax'];
//            $data['message_id'] = $messageId;
//            for($k=1;$k<=$indexCount;$k++){
//                array_push($data['DetailedIndex'],array(
//                    'index_name' => $subInfo['index'.$k. '_name'],
//                    'index_value' => $subInfo['kind'.$i.'_index'.$k],
//                ));
//            }
//            $contentResult = D('CoalPriceContent')->relation(true)->add($data);
//        }

        //类别表数据更新
            //查找数据库已有类别的个数
                //先把原有类别更新了，设计上保证前台更新的类别数永远会比数据库的多或相等
        $contendIdResult = D('CoalPriceContent')->field('content_id')->where(array('message_id'=>$messageId))->select();
        $existContentCount = count($contendIdResult);
//        $i = 1;
        foreach($contendIdResult as $keyIndex => $value){
            $i = $keyIndex+1;
            $data = array();
            //依次更新一条原有数据
            $content_id = $value['content_id'];
            $data['kind_name'] = $subInfo['kind'.$i.'_name'];
            $data['price'] = $subInfo['kind'.$i.'_price'];
            $data['tax'] = $subInfo['kind'.$i.'_tax'];
            $data['message_id'] = $messageId;
            $contentResult = D('CoalPriceContent')->where(array('content_id'=>$content_id))->save($data);
            array_push($flagArray,$contentResult);

            //详细指标表数据更新
                //查询该类别查找详细指标个数
                    //先把原有详细指标更新了，设计上保证前台更新的详细指标数永远会比数据库的多或相等
            $indexIdResult = D('CoalPriceDetailedIndex')->where(array('content_id'=>$content_id))->select();
            Log::record('indexID:'.json_encode($indexIdResult), Log::DEBUG);
            $existIndexCount = count($indexIdResult);
//            $m = 1;
            foreach($indexIdResult as $key => $val){
                $index = array();
                $m = $key+1;
                //依次更新一条原有数据
                $detailed_index_id = $val['detailed_index_id'];
                $index['index_name'] = $subInfo['index'.$m.'_name'];
                $index['index_value'] = $subInfo['kind'.$i.'_index'.$m];
                $index['content_id'] = $content_id;
                $indexResult = D('CoalPriceDetailedIndex')->where(array('detailed_index_id'=>$detailed_index_id))->save($index);
                array_push($flagArray,$indexResult);
//                $m++;
            }

            //新增详细指标插入代替更新
            if($indexCount > $existIndexCount){
                for($n = $existIndexCount+1;$n<=$indexCount;$n++){
                    $index = array();
                    $index['index_name'] = $subInfo['index'.$n.'_name'];
                    $index['index_value'] = $subInfo['kind'.$i.'_index'.$n];
                    $index['content_id'] = $content_id;
                    $indexResult = D('CoalPriceDetailedIndex')->add($index);
                    array_push($flagArray,$indexResult);
                }
            }else{}

//            $i++;
        }
        //新增的类别插入代替更新
        if($kindCount > $existContentCount){
            for($j = $existContentCount+1;$j<=$kindCount;$j++){
                $data = array();
                $data['DetailedIndex'] = array();
                $data['kind_name'] = $subInfo['kind'.$j.'_name'];
                $data['price'] = $subInfo['kind'.$j.'_price'];
                $data['tax'] = $subInfo['kind'.$j.'_tax'];
                $data['message_id'] = $messageId;
                for($k=1;$k<=$indexCount;$k++){
                    array_push($data['DetailedIndex'],array(
                        'index_name' => $subInfo['index'.$k. '_name'],
                        'index_value' => $subInfo['kind'.$j.'_index'.$k],
                    ));
                }
                $contentResultTemp = D('CoalPriceContent')->relation(true)->add($data);
                array_push($flagArray,$contentResultTemp);
            }
        }else{}

        //验证数据库是否产生错误
        foreach($flagArray as $val){
            if($val === false){
                $flag = false;
                break;
            }
        }

        $returnArray = array();
        if($flag){
            $model->commit();
            $returnArray['msg'] = '成功';
        }else{
            $model->rollback();
            $returnArray['msg'] = '数据库产生错误';
        }
        echo json_encode($returnArray);
    }
}