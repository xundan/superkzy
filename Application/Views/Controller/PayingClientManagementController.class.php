<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/7/27
 * Time: 16:25
 */

namespace Views\Controller;

use Think\Controller;
use Think\Model;

class PayingClientManagementController extends Controller
{
    const SUCCESS = 1;
    const FAIL = -1;

    public function payingClientInfoInput()
    {
        $this->display();
    }

    /**
     * 信息录入表单提交方法
     */
    public function infoInputAction()
    {
        $subInfo = I('post.', '', 'trim');
        $data['from'] = $subInfo['from'];
        $data['handler'] = $subInfo['handler'];
        $data['wx_name'] = $subInfo['wx_name'];
        $data['wx_id'] = $subInfo['wx_id'];
        $data['phone_number'] = $subInfo['phone_number'];
        $data['payday'] = $subInfo['payday'];
        $data['pay_type'] = $subInfo['pay_type'];
        $plusTimeString = '';
        switch ($data['pay_type']) {
            case '月费':
                $plusTimeString = '+1 month';
                break;
            case '季费':
                $plusTimeString = '+3 month';
                break;
            case '半年费':
                $plusTimeString = '+6 month';
                break;
            case '年费':
                $plusTimeString = '+1 year';
                break;
        }
        $data['money'] = $subInfo['money'];
        //计算到期时间
        $data['out_of_service_time'] = date("Y-m-d", strtotime($plusTimeString, strtotime($data['payday'])));
        if (strtotime($data['out_of_service_time']) < time()) {
            $data['invalid_id'] = 99;
        } else {
            $data['invalid_id'] = 0;
        }
        $result = M('ck_paying_client')->add($data);
        if ($result) {
            echo self::SUCCESS;
        } else {
            echo self::FAIL;
        }
//        echo json_encode($a,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 付费客户信息展示方法
     */
    public function infoShowAction()
    {
//        $where['invalid_id'] = array('neq','2');
//        $result = M('ck_paying_client')->order('phone_number asc,payday desc')->where($where)->select();
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = array();
        if ($subInfo['from'] == '全部') {
        } else {
            $where['from'] = $subInfo['from'];
        }
        if ($subInfo['handler'] == '全部') {
        } else {
            $where['handler'] = $subInfo['handler'];
        }
//        $where['invalid_id'] = 0;
//        $result = M('ck_paying_client')->where($where)->order('phone_number asc,payday desc')->select();
//        $result = M('ck_paying_client')->where($where)->order('id asc')->group('phone_number')->select();
        if ($subInfo['from'] == '全部' && $subInfo['handler'] == '全部') {
            $result = M('ck_paying_client')->query("SELECT * FROM `ck_paying_client` a WHERE NOT EXISTS (SELECT * from `ck_paying_client` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`)");
        } else if ($subInfo['from'] == '全部') {
            $result = M('ck_paying_client')->query("SELECT * FROM `ck_paying_client` a WHERE NOT EXISTS (SELECT * from `ck_paying_client` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`) AND a.`handler` = '" . $subInfo['handler'] . "'");
        } else if ($subInfo['handler'] == '全部') {
            $result = M('ck_paying_client')->query("SELECT * FROM `ck_paying_client` a WHERE NOT EXISTS (SELECT * from `ck_paying_client` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`) AND a.`from` = '" . $subInfo['from'] . "'");
        } else {
            $result = M('ck_paying_client')->query("SELECT * FROM `ck_paying_client` a WHERE NOT EXISTS (SELECT * from `ck_paying_client` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`) AND a.`from` = '" . $subInfo['from'] . "' AND a.`handler` = '" . $subInfo['handler'] . "'");
        }
        $returnArray = array();
        if ($result) {
            $returnArray['data'] = $result;
            $returnArray['msg'] = self::SUCCESS;
            //用户计数
            $where['invalid_id'] = 0;
            $resultCountValidTemp = M('ck_paying_client')->where($where)->select();
            $tempPhone = array();
            foreach($resultCountValidTemp as $val){
                array_push($tempPhone,$val['phone_number']);
            }
            $resultCountValid = count($resultCountValidTemp);
            $where['invalid_id'] = 99;
            if($tempPhone){
                $where['phone_number'] = array('not in',$tempPhone);
            }
            $resultCountInvalid = count(M('ck_paying_client')->where($where)->group('phone_number')->select());
            $returnArray['count']['valid'] = $resultCountValid;
            $returnArray['count']['invalid'] = $resultCountInvalid;
            $returnArray['count']['xxx'] = $tempPhone;
        } else {
            $returnArray['msg'] = self::FAIL;
        }
//        $returnArray = M()->getLastSql();
        echo json_encode($returnArray);
    }

    /**
     * 单个特定用户付费记录
     */
    public function payingRecord()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = array();
        $where['phone_number'] = $subInfo['info_phone_number'];
        $where['invalid_id'] = array('neq', 2);
        $result = M('ck_paying_client')->where($where)->order('payday desc')->select();
        $returnArray = array();
        if ($result) {
            $returnArray['data'] = $result;
            $returnArray['msg'] = self::SUCCESS;
        } else {
            $returnArray['msg'] = self::FAIL;
        }
        echo json_encode($returnArray);
    }

    /**
     * 客户表记录状态修改
     */
    public function infoModifyAction()
    {
        $subInfo = I('post.', '', 'trim');
        $where['id'] = $subInfo['info_id'];
        $data = array();
        if ($subInfo['modify_flag'] == 2) {
            $data['invalid_id'] = 2;
        } else if ($subInfo['modify_flag'] == 1) {
            $data['invalid_id'] = 0;
        } else if ($subInfo['modify_flag'] == 99) {
            $data['invalid_id'] = 99;
        }

        $result = M('ck_paying_client')->where($where)->save($data);
        $returnArr = array();
        if ($result === false) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = '数据库错误';
        } else {
            $returnArr['status'] = 2;
            $returnArr['msg'] = '操作成功';
        }
        echo json_encode($returnArr);
        return;
    }

    /**
     * 客户表内容修改
     */
    public function infoContentModifyAction()
    {
        $subInfo = I('post.', '', 'trim');
        $where['id'] = $subInfo['info_id'];
        $data = array();
        $data['from'] = $subInfo['info_from'];
        $data['handler'] = $subInfo['info_handler'];
        $data['wx_name'] = $subInfo['info_wx_name'];
        $data['wx_id'] = $subInfo['info_wx_id'];
        $data['phone_number'] = $subInfo['info_phone_number'];
        $data['payday'] = $subInfo['info_payday'];
        $data['pay_type'] = $subInfo['info_pay_type'];
        $data['money'] = $subInfo['info_money'];
        $data['out_of_service_time'] = $subInfo['info_out_of_service_time'];
        $data['invalid_id'] = $subInfo['info_invalid_id'];

        $result = M('ck_paying_client')->where($where)->save($data);
        $returnArr = array();
        if ($result === false) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = '数据库错误';
        } else {
            $returnArr['status'] = 2;
            $returnArr['msg'] = '操作成功';
        }
        echo json_encode($returnArr);
        return;
    }

    /**
     * 计算指定时间收费总额
     */
    public function SumAction()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['payday'] = array("BETWEEN", array($start, $end));
        if ($post['from'] == '全部') {
        } else {
            $where['from'] = $post['from'];
        }
        if ($post['handler'] == '全部') {
        } else {
            $where['handler'] = $post['handler'];
        }
        $result = M('ck_paying_client')->where($where)->sum('money');
        echo json_encode($result);
        return;
    }

    /**
     * 时间戳转时间函数，用于比较查询
     * @param $date
     * @return bool|string
     */
    public function TimestampToTime($date)
    {
        date_default_timezone_set("Asia/Shanghai");
        $result = date("Y-m-d H:i:s", $date);
        return $result;
    }

    /**
     * 中奖结果展示
     */
    public function lotteryResultShow()
    {
        $result = M('ck_activity_paying_client_2017_08')->join('LEFT JOIN ck_paying_client ON ck_paying_client.id=ck_activity_paying_client_2017_08.paying_client_id')->select();
        echo json_encode($result);
    }

    public function queryByPhone(){
        $subInfo = I('post.','','trim,strip_tags');
        if($subInfo['phone_number']){
            $where['phone_number'] = $subInfo['phone_number'];
            $result = M('ck_paying_client')->where($where)->field('id,wx_name')->find();
            if($result){
                echo json_encode($result);
                exit;
            }else{
                echo 'failure';
                exit;
            }
        }else{
            echo 'failure';
            exit;
        }
    }

}