<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 16:13
 */

namespace Home\Controller;
use Home\Model\MessagesModel;
use Think\Controller;
use Think\Model;
use Think\Log;

header("Content-type: text/html; charset=utf-8");

class OwnerPublishController extends Controller
{
    public function owner_publish(){
        $user = session('user_info');
        $phone_number = ($user['phone_number'])?$user['phone_number']:"";
        $this->assign("phone_number",$phone_number);
        $this->display();
    }

    public function car_give_action(){
        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;

        if(!$this->AuthCheck($module_name)){
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);exit;
        }else{
            $a = new MessagesModel();
            $a->addInto();
        }
    }

    public function car_need_action(){

//        Log::record("错误3:$subInfo".json_encode($subInfo));

        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;

        if(!$this->AuthCheck($module_name)){
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);exit;
        }else{
            $a = new MessagesModel();
            $a->addInto();
        }
    }


    public function coal_sell_action(){

        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;

        if(!$this->AuthCheck($module_name)){
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);exit;
        }else{
            $a = new MessagesModel();
            $a->addInto();
        }
    }



    public function coal_buy_action(){

        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;

        if(!$this->AuthCheck($module_name)){
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);exit;
        }else{
            $a = new MessagesModel();
            $a->addInto();
        }
    }

    private function AuthCheck($module_name){
        $Auth = new \Think\Auth();
        $user = session('user_info');
//        $where['uid'] = $user['uid'];
//        $user = M('user')->where($where)->find();
        if(!$Auth->check($module_name,$user['uid'])){
           return false;
        }
        return true;
    }

}