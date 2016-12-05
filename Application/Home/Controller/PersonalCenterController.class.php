<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/15
 * Time: 15:58
 */

namespace Home\Controller;

use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class PersonalCenterController extends ComController
{
    public function personal_center()
    {
        $user_info = $_SESSION['user_info'];
        $this->assign('user_info', $user_info);
        $user_role = session('user_info')['role_id'] == null ? "游客" : session('user_info')['role_id'];
        $this->assign('user_role', $user_role);
        $this->display();
    }

    public function owner_data()
    {
        $this->display();
    }

    public function owner_data_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $user['uid'] = session('user_info')['uid'];
        $user['nickname'] = $subInfo['nickname'];
//        $user['phone_number'] = $subInfo['phone_number'];
        // TODO 改变电话需要验证
        $user['sex'] = $subInfo['sex'];
        $user['birthday'] = $subInfo['birthday'];
        $company['name'] = $subInfo['supply_company'];
        $user['company_id'] = $this->save_company($company);
        // TODO district回调还没做
//        $user['district_id'] = 0;
        $user['area_detail'] = $subInfo['area_detail'];
        $user['invite_id'] = $subInfo['invite_id'];
        //插入
        $mres = M('User')->save($user);
        if ($mres) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "修改成功";
            echo json_encode($returnArr);
        } else {
            $returnArr['status'] = 0;
            $returnArr['msg'] = "修改失败";
            echo json_encode($returnArr);
            exit;
        }
    }

    public function owner_work_exp_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $user['uid'] = session('user_info')['uid'];
        $company['name'] = $subInfo['company_name'];
        $user['work_time'] = $subInfo['work_time'];
        $user['owner_department'] = $subInfo['owner_department'];
        $user['owner_position'] = $subInfo['owner_position'];
        $user['work_description'] = $subInfo['work_description'];
        $user['company_id']=$this->save_company($company);
        //插入
        $mres = M('User')->save($user);
        if ($mres) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "修改成功";
            echo json_encode($returnArr);
        } else {
            $returnArr['status'] = 0;
            $returnArr['msg'] = "修改失败";
            echo json_encode($returnArr);
            exit;
        }
    }

    private function save_company($company)
    {
        $Comp=M('company');
        $res = $this->fetch_company($company);
        if ($res){
            // 目前$company只有name字段，如果有新字段增加，则放开下面注释
            // 同时fetch_company方法也要改
            $Comp->where('id='.$res['id'])->save($company);
            return $res['id'];
        }else{
            // added是新插入字段的id
            $added = $Comp->add($company);
            return $added;
        }
    }

    private function fetch_company($company){
        $data['name']=$company['name'];
        $res = M('Company')->where($data)->find();
        if ($res){
            return $res;
        }else{
            // todo LOG HERE.
            return false;
        }
    }

    public function driver_data()
    {
        $this->display();
    }

    public function driver_data_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $user['uid'] = session('user_info')['uid'];
        $user['nickname'] = $subInfo['nickname'];
//        $user['phone_number'] = $subInfo['phone_number'];
        // TODO 改变电话需要验证
        $user['sex'] = $subInfo['sex'];
        $user['id_card'] = $subInfo['id_card'];
        $user['drive_card'] = $subInfo['drive_card'];
        $car['plate_number'] = $subInfo['plate_number'];
        $car['type_id'] = $subInfo['type_id'];
        $car['carrying_capacity'] = $subInfo['carrying_capacity'];
        $user['car_id'] = $this->save_car($car);
//        $user['invite_id'] = $subInfo['invite_id'];
        //插入
        $mres = M('User')->save($user);
        if ($mres) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "修改成功";
            echo json_encode($returnArr);
        } else {
            $returnArr['status'] = 0;
            $returnArr['msg'] = "修改失败";
            echo json_encode($returnArr);
            exit;
        }
    }

    public function driver_work_exp_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        //回调数组
        $returnArr = array();
        //煤炭产品属性
        $user['uid'] = session('user_info')['uid'];
        $user['work_time_start'] = $subInfo['work_time_start'];
        $user['work_time_end'] = $subInfo['work_time_end'];
        $user['buy_car_time'] = $subInfo['buy_car_time'];
        $user['work_description'] = $subInfo['work_description'];
        //插入
        $mres = M('User')->save($user);
        if ($mres) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "修改成功";
            echo json_encode($returnArr);
        } else {
            $returnArr['status'] = 0;
            $returnArr['msg'] = "修改失败";
            echo json_encode($returnArr);
            exit;
        }
    }


    private function save_car($car)
    {
        $Car_info=M('car_info');
        $res = $this->fetch_car($car);
        if ($res){
            $Car_info->where('id='.$res['id'])->save($car);
            return $res['id'];
        }else{
            // added是新插入字段的id
            $added = $Car_info->add($car);
            return $added;
        }
    }

    private function fetch_car($car){
        $data['plate_number']=$car['plate_number'];
        $res = M('car_info')->where($data)->find();
        if ($res){
            return $res;
        }else{
            // todo LOG HERE.
            return false;
        }
    }


//    public function demo(){
//        $company['name']="hue2";
//        var_dump(M('Company')->where($company)->find());
////        echo $this->save_company($company);
//        echo M('company')->add($company);
//    }
}