<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19
 * Time: 14:19
 */

namespace Views\Controller;

use Think\Controller;

class RecordInformationController extends Controller
{
    const SUCCESS = 1;
    const FAIL = 0;

    public function recordInformation()
    {
        $this->display();
    }

    public function test()
    {
        $subInfo['date_start'] = '2018-01-17';
        $where_check['record_time'] = array('like', $subInfo['date_start'] . "%");
        $result_check = M('ck_check_flow')->where($where_check)->select();
        dump($result_check);
    }

    //信息展示


    public function infoShowAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];

        if ($subInfo['name'] == '全部') {
        } else {
            $where['user_name'] = $subInfo['name'];
        }
        $result_name = M('ck_staffs')->where($where)->select();
        $returnArr = array();
        if ($result_name) {
            foreach ($result_name as $user) {
                $where_check['uid'] = $user['id'];
                $where_check['record_time'] = array('like', $subInfo['date_start'] . "%");
                $result_check = M('ck_check_flow')->where($where_check)->select();
                if ($result_check) {
                    $msg_count = 0;
                    $time_count = 0;
                    foreach ($result_check as $item) {
                        if ($item['abort_id'] != 0) {
                            $msg_count += ($item['abort_id'] - $item['msg_start_id'] + 1);
                        } else {
                            $msg_count += ($item['msg_end_id'] - $item['msg_start_id'] + 1);
                        }
                        $time_start = strtotime($item['record_time']);
                        $time_end = strtotime($item['update_time']);
                        $time_count += ($time_end - $time_start - $item['stop_time']);
                    }
                    $hour = floor($time_count / 3600);
                    $minute = floor(($time_count - 3600 * $hour) / 60);
                    $second = floor((($time_count - 3600 * $hour) - 60 * $minute) % 60);
                    $time_count_str = $hour . '小时' . $minute . '分' . $second . '秒';
                    $where_freight['recorder'] = $user['id'];
                    $where_freight['record_time'] = array('like', $subInfo['date_start'] . "%");
                    $result_freight = M('ck_freight')->where($where_freight)->select();
                    $freight_count = count($result_freight);
                    $msg_count_all = $freight_count + $msg_count;
                    $returnArr['msg'] = self::SUCCESS;
                    $returnUser['user_name'] = $user['user_name'];
                    $returnUser['msg_count'] = $msg_count;
                    $returnUser['freight_count'] = $freight_count;
                    $returnUser['time_count'] = $time_count_str;
                    $returnUser['msg_count_all'] = $msg_count_all;
                    array_push($returnArr['data'],$returnUser);
                }else{
                    $returnArr['msg'] = self::FAIL;
                }
            }
        }else{
            $returnArr['msg'] = self::FAIL;
        }
        echo json_encode($returnArr);
    }
}