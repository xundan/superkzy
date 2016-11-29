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
        $this->assign('coalKind',$coalKind);
        $this->assign('coalTrait',$coalTrait);
        $this->assign('coalGranularity',$coalGranularity);
//        dump($_COOKIE);
        $curl = CONTROLLER_NAME.'/'.ACTION_NAME;
        cookie('lasturl',$curl);
        $this->assign('curl',$curl);
        $this->display();
    }

    public function need_car_action(){
        //读取post数据
        $subInfo = I('post.','','strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $product['granularity_id'] = $subInfo['option3_coal_granularity'];
        $presTemp = M('product')->where($product)->find();
        if($presTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询产品表失败";
            echo json_encode($returnArr);
            exit;
        }elseif(!empty($presTemp)){
            //重复
            $data['product_id'] = $presTemp['id'];
            $pres = $presTemp['id'];
        }else{
            $pres = M('product')->add($product);
            if($pres){
                $data['product_id'] = $pres;
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "插入产品表失败";
                echo json_encode($returnArr);
                exit;
            }
        }
        //消息属性
        $data['category'] = 3;  //求车为3
        $data['publisher_rid'] = $_SESSION['user_info']['uid'];
        $data['publish_time'] = time();
//        $data['deadline'] = $this->set_deadline();  //TODO
//        $data['valid_time'] = '';   //TODO
//        $data['times_number'] += 1; //TODO
//        $data['record_time'] = time();  //TODO
//        $data['updater_id'] = '';   //TODO
//        $data['place_origin_id'] = '';    //TODO
        $data['price'] = $subInfo['need_price'];
        $data['loading_time'] = $subInfo['loading_time'];
        $data['quantity'] = $subInfo['need_quantity'];
        $data['loading_cost'] = $subInfo['loading_cost'];
        $data['unloading_cost'] = $subInfo['unloading_cost'];
        $data['phone_number'] = $subInfo['need_phone'];
        $data['content'] = $subInfo['need_content'];
        //产地转换为id
        $data['area_start'] = $this->get_area_id($subInfo['need_area_start']);
        $data['detail_area_start'] = $subInfo['need_area_start_detail'];
        $data['area_end'] = $this->get_area_id($subInfo['need_area_end']);
        $data['detail_area_end'] = $subInfo['need_area_end_detail'];
        //获取煤炭属性名
        $coal_granularity_name = $this->get_coal_granularity_title($subInfo['option3_coal_granularity']);
        //content_all字段拼接用于查询
        $data['content_all'] = $subInfo['need_area_start'].'#'.$subInfo['need_area_start_detail'].'#'.$subInfo['need_area_end']
            .'#'.$subInfo['need_area_end_detail'].'#'.$subInfo['need_quantity'].'#'.$coal_granularity_name.'#'.$subInfo['need_price']
            .'#'.$subInfo['loading_time'].'#'.$subInfo['loading_cost'].'#'.$subInfo['unloading_cost'].'#'.$subInfo['need_phone']
            .'#'.$subInfo['sell_content'];
        //判断消息是否拆分
        if(!$subInfo['need_area_start'] && !$subInfo['need_area_end']){
            $data['formatted'] = 0;
        }else{
            $data['formatted'] = 1;
        }
        //对content_all字段进行MD5编码用于快速查重
        $data['content_all_md5'] = md5($data['content_all']);
        //插入前查重
        $where['content_all_md5'] = $data['content_all_md5'];
        $mresTemp = M('messages')->where($where)->find();
        if($mresTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询信息表失败";
            echo json_encode($returnArr);exit;
        }elseif(!empty($mresTemp)){
            //重复
            $returnArr['status'] = 2;
            $returnArr['msg'] = "发布数据重复";
            echo json_encode($returnArr);exit;
        }else{
            //插入
            $mres = M('messages')->data($data)->add();
            if($mres){
                $returnArr['status'] = 1;
                $returnArr['msg'] = "发布成功";
                echo json_encode($returnArr);
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "发布失败";
                M("product")->delete($pres);
                echo json_encode($returnArr);
                exit;
            }
        }
    }


    public function coal_sell_action(){
        //读取post数据
        $subInfo = I('post.','','strip_tags,trim');
        //回调数组
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
        if($presTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询产品表失败";
            echo json_encode($returnArr);
            exit;
        }elseif(!empty($presTemp)){
            //重复
            $data['product_id'] = $presTemp['id'];
            $pres = $presTemp['id'];
        }else{
            $pres = M('product')->add($product);
            if($pres){
                $data['product_id'] = $pres;
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "插入产品表失败";
                echo json_encode($returnArr);
                exit;
            }
        }
        //消息属性
        $data['category'] = 0;  //供货为0
        $data['publisher_rid'] = $_SESSION['user_info']['uid'];
        $data['publish_time'] = time();
//        $data['deadline'] = $this->set_deadline();  //TODO
//        $data['valid_time'] = '';   //TODO
//        $data['times_number'] += 1; //TODO
//        $data['record_time'] = time();  //TODO
//        $data['updater_id'] = '';   //TODO
//        $data['place_origin_id'] = '';    //TODO
        $data['supply_company'] = $subInfo['sell_supply_company'];
        $data['price'] = $subInfo['sell_price'];
        $data['quantity'] = $subInfo['sell_quantity'];
        $data['phone_number'] = $subInfo['sell_phone'];
        $data['content'] = $subInfo['sell_content'];
        //产地转换为id
        $data['area_start'] = $this->get_area_id($subInfo['sell_area_start']);
        //获取煤炭属性名
        $coal_kind_name = $this->get_coal_kind_title($subInfo['option0_coal_kind']);
        $coal_trait_name = $this->get_coal_trait_title($subInfo['option0_coal_trait']);
        $coal_granularity_name = $this->get_coal_granularity_title($subInfo['option0_coal_granularity']);
        //content_all字段拼接用于查询
        $data['content_all'] = $coal_kind_name.'#'.$coal_trait_name.'#'.$coal_granularity_name.'#'.$subInfo['sell_supply_company']
            .'#'.$subInfo['sell_area_start'].'#'.$subInfo['sell_price'].'#'.$subInfo['sell_quantity'].'#'.$subInfo['sell_heat_value_max']
            .'#'.$subInfo['sell_heat_value_min'].'#'.$subInfo['water'].'#'.$subInfo['ash'].'#'.$subInfo['sulfur'].'#'.$subInfo['volatile']
            .'#'.$subInfo['sell_phone'].'#'.$subInfo['sell_content'];
        //判断消息是否拆分
        if(!$subInfo['option0_coal_kind'] && !$subInfo['option0_coal_trait'] && !$subInfo['option0_coal_granularity']){
            $data['formatted'] = 0;
        }else{
            $data['formatted'] = 1;
        }
        //对content_all字段进行MD5编码用于快速查重
        $data['content_all_md5'] = md5($data['content_all']);
        //插入前查重
        $where['content_all_md5'] = $data['content_all_md5'];
        $mresTemp = M('messages')->where($where)->find();
        if($mresTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询信息表失败";
            echo json_encode($returnArr);exit;
        }elseif(!empty($mresTemp)){
            //重复
            $returnArr['status'] = 2;
            $returnArr['msg'] = "发布数据重复";
            echo json_encode($returnArr);exit;
        }else{
            //插入
            $mres = M('messages')->data($data)->add();
            if($mres){
                $returnArr['status'] = 1;
                $returnArr['msg'] = "发布成功";
                echo json_encode($returnArr);
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "发布失败";
                M("product")->delete($pres);
                echo json_encode($returnArr);
                exit;
            }
        }
    }



    public function coal_buy_action(){
        //读取post数据
        $subInfo = I('post.','','strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $product['kind_id'] = $subInfo['option2_coal_kind'];
        $product['trait_id'] = $subInfo['option2_coal_trait'];
        $product['granularity_id'] = $subInfo['option2_coal_granularity'];
        $product['heat_value_min'] = $subInfo['buy_heat_value_min'];
        $presTemp = M('product')->where($product)->find();
        if($presTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询产品表失败";
            echo json_encode($returnArr);
            exit;
        }elseif(!empty($presTemp)){
            //重复
            $data['product_id'] = $presTemp['id'];
            $pres = $presTemp['id'];
        }else{
            $pres = M('product')->add($product);
            if($pres){
                $data['product_id'] = $pres;
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "插入产品表失败";
                echo json_encode($returnArr);
                exit;
            }
        }
        //消息属性
        $data['category'] = 2;  //求购为2
        $data['publisher_rid'] = $_SESSION['user_info']['uid'];
        $data['publish_time'] = time();
//        $data['deadline'] = $this->set_deadline();  //TODO
//        $data['valid_time'] = '';   //TODO
//        $data['times_number'] += 1; //TODO
//        $data['record_time'] = time();  //TODO
//        $data['updater_id'] = '';   //TODO
//        $data['place_origin_id'] = '';    //TODO
        $data['quantity'] = $subInfo['buy_quantity'];
        $data['price_min'] = $subInfo['buy_price_min'];
        $data['price_max'] = $subInfo['buy_price_max'];
        $data['pay_type'] = $this->get_pay_type_name($subInfo['buy_method']);
        $data['phone_number'] = $subInfo['buy_phone'];
        $data['content'] = $subInfo['buy_content'];
        //产地转换为id
        $data['area_start'] = $this->get_area_id($subInfo['buy_area_start']);
        //获取煤炭属性名
        $coal_kind_name = $this->get_coal_kind_title($subInfo['option2_coal_kind']);
        $coal_trait_name = $this->get_coal_trait_title($subInfo['option2_coal_trait']);
        $coal_granularity_name = $this->get_coal_granularity_title($subInfo['option2_coal_granularity']);
        //content_all字段拼接用于查询
        $data['content_all'] = $coal_kind_name.'#'.$coal_trait_name.'#'.$coal_granularity_name .'#'.$subInfo['buy_area_start']
            .'#'.$subInfo['buy_heat_value_min'].'#'.$subInfo['buy_quantity'].'#'.$subInfo['buy_price_min'].'#'.$subInfo['buy_price_max']
            .'#'.$data['pay_type'] .'#'.$subInfo['buy_phone'].'#'.$subInfo['buy_content'];
        //判断消息是否拆分
        if(!$subInfo['option2_coal_kind'] && !$subInfo['option2_coal_trait'] && !$subInfo['option2_coal_granularity']){
            $data['formatted'] = 0;
        }else{
            $data['formatted'] = 1;
        }
        //对content_all字段进行MD5编码用于快速查重
        $data['content_all_md5'] = md5($data['content_all']);
        //插入前查重
        $where['content_all_md5'] = $data['content_all_md5'];
        $mresTemp = M('messages')->where($where)->find();
        if($mresTemp === false){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "查询信息表失败";
            echo json_encode($returnArr);exit;
        }elseif(!empty($mresTemp)){
            //重复
            $returnArr['status'] = 2;
            $returnArr['msg'] = "发布数据重复";
            echo json_encode($returnArr);exit;
        }else{
            //插入
            $mres = M('messages')->data($data)->add();
            if($mres){
                $returnArr['status'] = 1;
                $returnArr['msg'] = "发布成功";
                echo json_encode($returnArr);
            }else{
                $returnArr['status'] = 0;
                $returnArr['msg'] = "发布失败";
                M("product")->delete($pres);
                echo json_encode($returnArr);
                exit;
            }
        }
    }
}