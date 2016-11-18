<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 16:13
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class OwnerPublishController extends ComController
{
    public function owner_publish(){
        $coalKind = M('coal_kind')->select();
        $coalTrait = M('coal_trait')->select();
        $coalGranularity = M('coal_granularity')->select();
//        dump($coalKind);
//        dump($coalTrait);
//        dump($coalGranularity);
        $this->assign('coalKind',$coalKind);
        $this->assign('coalTrait',$coalTrait);
        $this->assign('coalGranularity',$coalGranularity);
        $this->display();
    }

    public function coal_sell_action(){
//        dump($_POST);
//        $area_start = I('post.start_area','','strip_tags')?I('post.start_area','','strip_tags'):"";
//        $area_end = I('post.start_area','','strip_tags')?I('post.start_area','','strip_tags'):"";
//        $area_end = I('post.start_area','','strip_tags')?I('post.start_area','','strip_tags'):"";
        $subInfo = I('post.','','strip_tags,trim');
//        dump($subInfo);exit;
//        echo $subInfo;
//        echo $_POST['content'];
//        $a = json_decode($_POST);
//        return $a;
//        return $_POST['content'];
//        return $subInfo;
//        exit;
        $returnArr = array();
        //煤炭产品属性
        $product['kind_id'] = $subInfo['option0_coal_kind'];
        $product['trait_id'] = $subInfo['option0_coal_trait'];
        $product['granularity_id'] = $subInfo['option0_coal_granularity'];
        $product['heat_value_max'] = $subInfo['sell_heat_value_max'];
        $product['heat_value_min'] = $subInfo['sell_heat_value_min'];
        $product['water'] = $subInfo['water'];
        $product['ash'] = $subInfo['ash'];
        $product['sulfur'] = $subInfo['sulfur'];
        $product['volatile'] = $subInfo['volatile'];
        $presTemp = M('product')->where($product)->find();
        if(!empty($presTemp)){//重复
            $data['product_id'] = $presTemp['id'];
        }elseif($presTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询炒作失败";
            echo json_encode($returnArr);
        }

        $pres = M('product')->add($product);
        if($pres){
            $data['product_id'] = $pres;
        }else{
            echo '插入操作失败';exit;
        }
        //消息属性
        $data['category'] = 0;  //货源为0
        $data['publisher_rid'] = $_SESSION['user_info']['uid'];
        $data['publish_time'] = time();
        $data['deadline'] = $this->set_deadline();  //TODO
        $data['valid_time'] = '';   //TODO
        $data['times_number'] += 1; //TODO
        $data['record_time'] = time();  //TODO
//        $data['updater_id'] = '';   //TODO
        $data['phone_number'] = $subInfo['sell_phone'];
        $data['price'] = $subInfo['sell_price'];
//        $data['place_origin_id'] = '';    //TODO
        $data['supply_company'] = $subInfo['supply_company'];
        $data['quantity'] = $subInfo['sell_quantity'];
        $data['content'] = $subInfo['content'];
        //content_all字段拼接用于查询
        $data['content_all'] = $subInfo['content'];
        //判断消息是否拆分
        if(empty($subInfo['content'])){
            if($subInfo['']){
                $data['formatted'] = 0;
            }else{
                $data['formatted'] = 1;
            }
        }
        //对content_all字段进行MD5编码用于快速查重
        $data['content_all_md5'] = md5($data['content_all']);
        //插入前查重
        $mresTemp = M('messages')->where($data['content_all_md5'])->find();
        if(!empty($mresTemp)){//重复
            $returnArr['status'] = 2;
            $returnArr['msg'] = "数据重复";
            echo json_encode($returnArr);
        }elseif($mresTemp === false){
            echo '查询操作失败';
        }else{
            //插入
            $mres = M('messages')->data($data)->add();
            if ($mres) {
                $returnArr['status'] = 1;
                $returnArr['msg'] = "发布成功";
                echo json_encode($returnArr);
            } else {
                $returnArr['status'] = 0;
                $returnArr['msg'] = "发布失败。";
                echo json_encode($returnArr);
                M("product")->delete($pres);
            }
        }
    }


}