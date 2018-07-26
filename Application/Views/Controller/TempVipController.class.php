<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2018/3/14
 * Time: 9:26
 */

namespace Views\Controller;


use Think\Controller;
use Think\Model;

class TempVipController extends Controller
{
    const SUCCESS = 1;
    const FAIL = -1;

    public function test(){
        dump(time());
        dump(date("Y-m-d", time()));
        dump(date("Y-m-d", strtotime('+4 day', time())));
        $whereClient['phone_number'] = 1234567;
        $whereClient['invalid_id'] = 0;

        $a = M('ck_temp_vip')->where($whereClient)->order('update_time desc')->find();
        dump($a);
    }

    public function show()
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
        $data['wx_name'] = $subInfo['wx_name'];
        $data['wx_id'] = $subInfo['wx_id'];
        $data['phone_number'] = $subInfo['phone_number'];
        $data['payday'] = date("Y-m-d", time());
        //计算到期时间
        $data['out_of_service_time'] = date("Y-m-d", strtotime('+4 day', time()));
        $result = M('ck_temp_vip')->add($data);
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
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = array();
        if ($subInfo['from'] == '全部') {
        } else {
            $where['from'] = $subInfo['from'];
        }
        if ($subInfo['from'] == '全部') {
            $result = M('ck_temp_vip')->query("SELECT * FROM `ck_temp_vip` a WHERE NOT EXISTS (SELECT * from `ck_temp_vip` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`)");
        } else {
            $result = M('ck_temp_vip')->query("SELECT * FROM `ck_temp_vip` a WHERE NOT EXISTS (SELECT * from `ck_temp_vip` b WHERE b.`phone_number` = a.`phone_number` AND b.`payday` > a.`payday`) AND a.`from` = '" . $subInfo['from'] . "'");
        }
        $returnArray = array();
        if ($result) {
            $returnArray['data'] = $result;
            $returnArray['msg'] = self::SUCCESS;
            //用户计数
            $where['invalid_id'] = 0;
            $resultCountValidTemp = M('ck_temp_vip')->where($where)->group('phone_number')->select();
            $tempPhone = array();
            foreach ($resultCountValidTemp as $val) {
                array_push($tempPhone, $val['phone_number']);
            }
            $resultCountValid = count($resultCountValidTemp);
            $where['invalid_id'] = 99;
            $resultCountInvalid = count(M('ck_temp_vip')->where($where)->group('phone_number')->select());
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
        $result = M('ck_temp_vip')->where($where)->order('payday desc')->select();
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

        $result = M('ck_temp_vip')->where($where)->save($data);
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
}