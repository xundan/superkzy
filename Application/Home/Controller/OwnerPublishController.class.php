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
    public function owner_publish()
    {
        $user = session('user_info');
        $phone_number = ($user['phone_number']) ? $user['phone_number'] : "";
        $this->assign("phone_number", $phone_number);
        $this->display();
    }

    public function order_coal_sell_modify()
    {
        $this->display();
    }

    public function coal_sell_modify_action()
    {
        $a = new MessagesModel();
        $a->updateMessage();
    }

    public function order_coal_buy_modify()
    {
        $this->display();
    }

    public function coal_buy_modify_action()
    {
        $a = new MessagesModel();
        $a->updateMessage();
    }

    public function order_car_need_modify()
    {
        $this->display();
    }

    public function car_need_modify_action()
    {
        $a = new MessagesModel();
        $a->updateMessage();
    }

    public function order_car_give_modify()
    {
        $this->display();
    }

    public function car_give_modify_action()
    {
        $a = new MessagesModel();
        $a->updateMessage();
    }

    public function car_give_action()
    {
        $module_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        if (!$this->AuthCheck($module_name)) {
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);
            exit;
        } else {
            $a = new MessagesModel();
            $a->addInto();
        }
    }

    public function car_need_action()
    {

//        Log::record("错误3:$subInfo".json_encode($subInfo));

        $module_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        if (!$this->AuthCheck($module_name)) {
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);
            exit;
        } else {
            $a = new MessagesModel();
            $a->addInto();
        }
    }


    public function coal_sell_action()
    {
        $module_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        if (!$this->AuthCheck($module_name)) {
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);
            exit;
        } else {
            $a = new MessagesModel();
            $a->addInto();
        }
    }

    public function coal_buy_action()
    {
        $module_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        if (!$this->AuthCheck($module_name)) {
            $returnArr = array();
            $returnArr['status'] = 0;
            $returnArr['msg'] = "无权限发布";
            echo json_encode($returnArr);
            exit;
        } else {
            $a = new MessagesModel();
            $a->addInto();
        }
    }

    public function overdue()
    {

//        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;
//
//        if(!$this->AuthCheck($module_name)){
//            $returnArr = array();
//            $returnArr['status'] = 0;
//            $returnArr['msg'] = "无权限发布";
//            echo json_encode($returnArr);exit;
//        }else{
        $Message = new MessagesModel();
        $post = I('post.', '', 'trim,strip_tags');
        $msg_id = $post['id'];
        $data = $Message->updateMessageState($msg_id, 99);
        echo json_encode($data);
        return;
//        }
    }

    /**
     * 手动删除message
     */
    public function refill()
    {

        //        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;
//
//        if(!$this->AuthCheck($module_name)){
//            $returnArr = array();
//            $returnArr['status'] = 0;
//            $returnArr['msg'] = "无权限发布";
//            echo json_encode($returnArr);exit;
//        }else{
        $Message = new MessagesModel();
        $post = I('post.', '', 'trim,strip_tags');
        $msg_id = $post['id'];
        $data = $Message->updateMessageState($msg_id, 0, true);
        echo json_encode($data);
        return;
//        }
    }


    private function AuthCheck($module_name)
    {
        $Auth = new \Think\Auth();
        $user = session('user_info');
//        $where['uid'] = $user['uid'];
//        $user = M('user')->where($where)->find();
        if (!$Auth->check($module_name, $user['uid'])) {
            return false;
        }
        return true;
    }

}