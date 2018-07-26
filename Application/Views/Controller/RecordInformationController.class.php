<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19
 * Time: 14:19
 */

namespace Views\Controller;

use Think\Controller;
use Think\Log;

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
        $subInfo['id'] = '1';
        $subInfo['weix_name'] = 'wp1874619828';
        $subInfo['weix_user'] = 1;
        $subInfo['weix_tel'] = 1;
        $subInfo['weix_comp'] = 1;
        $subInfo['weix_qun'] = '山西二群';
        $where = [];
        $where['id'] = $subInfo['id'];
        $where['weix_name'] = $subInfo['weix_name'];
        $result = M('ck_find_user')->where($where)->find();
        if ($result) {
            $temp = array();
            $temp['id'] = $subInfo['id'];
            $temp['weix_name'] = $subInfo['weix_name'];
            $temp['weix_user'] = $subInfo['weix_user'];
            $temp['weix_tel'] = $subInfo['weix_tel'];
            $temp['weix_comp'] = $subInfo['weix_comp'];
            $temp['weix_qun'] = $subInfo['weix_qun'];
            $result_revise = M('ck_find_user')->save($temp);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        } else {
            echo 'failure1';
        }
        dump($result_revise);
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
        $returnArr['data'] = array();
        if ($result_name) {
            foreach ($result_name as $user) {
                $where_check['uid'] = $user['id'];
                $where_check['record_time'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
                $where_check['status'] = array('in', [2, 3, 5, 6]);
                $result_check = M('ck_check_flow')->where($where_check)->select();
                if ($result_check) {
                    $msg_count = 0;
                    $time_count = 0;
                    foreach ($result_check as $item) {
                        $msg_count += $item['count_check'];
                        $time_start = (int)strtotime($item['record_time']);
                        Log::record('time_start:' . $time_start);
                        $time_end = (int)strtotime($item['end_time']);
                        Log::record('time_end:' . $time_end);
//                        if($item['end_time'] == $item['update_time']){
//                            continue;
//                        }
                        $time_count += ($time_end - $time_start - (int)$item['stop_time']);
                        Log::record('time_count:' . $time_count);
                    }
                    $where_freight['recorder'] = $user['id'];
                    $where_freight['record_time'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
                    $result_freight = M('ck_freight')->where($where_freight)->select();
                    if ($result_freight) {
                        $freight_count = count($result_freight);
                    } else {
                        $freight_count = 0;
                    }
                    Log::record('***time***' . $time_count);
                    $hour = floor($time_count / 3600);
                    $minute = floor(($time_count - 3600 * $hour) / 60);
                    $second = floor((($time_count - 3600 * $hour) - 60 * $minute) % 60);
                    $time_count_str = $hour . '小时' . $minute . '分' . $second . '秒';
                    $time_count_min = $time_count / 60;
                    $msg_count_all = $freight_count * 8 + $msg_count;
                    $returnArr['msg'] = self::SUCCESS;
                    $returnUser['user_name'] = $user['user_name'];
                    $returnUser['msg_count'] = $msg_count;
                    $returnUser['freight_count'] = $freight_count;
                    $returnUser['time_count'] = $time_count_str;
                    $returnUser['msg_count_all'] = $msg_count_all;
                    $round = $msg_count_all / $time_count_min;
                    $returnUser['efficiency'] = round($round, 2);
                    array_push($returnArr['data'], $returnUser);
                } else {
                    $returnArr['msg'] = self::FAIL;
                }
            }
        } else {
            $returnArr['msg'] = 2;
        }
        echo json_encode($returnArr);
    }

    //会员入群预览
    public function findIn()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $returnUser = [];
        $returnArr = array();
        $returnArr['data'] = array();
        $where['weix_name'] = $subInfo['weix_name'];
        $result = M('ck_find_user')->where($where)->select();
        if ($result) {
            foreach ($result as $item) {
                if($item['weix_qun'] == $subInfo['weix_qun']){
                    $returnUser['id'] = $item['id'];
                    $returnUser['weix_name'] = $item['weix_name'];
                    $returnUser['weix_user'] = $item['weix_user'];
                    $returnUser['weix_tel'] = $item['weix_tel'];
                    $returnUser['weix_comp'] = $item['weix_comp'];
                    $returnUser['weix_qun'] = $item['weix_qun'];
                    $returnUser['record_time'] = $item['record_time'];
                    if ($item['invalid_id'] == 1) {
                        $returnUser['invalid_id'] = '已删除';
                    } else {
                        $returnUser['invalid_id'] = '在群内';
                    }
                    $returnArr['msg'] = 'yes';
                    array_push($returnArr['data'], $returnUser);
                }else{
                    $returnUser['id'] = $item['id'];
                    $returnUser['weix_name'] = $item['weix_name'];
                    $returnUser['weix_user'] = $item['weix_user'];
                    $returnUser['weix_tel'] = $item['weix_tel'];
                    $returnUser['weix_comp'] = $item['weix_comp'];
                    $returnUser['weix_qun'] = $item['weix_qun'];
                    $returnUser['record_time'] = $item['record_time'];
                    if ($item['invalid_id'] == 1) {
                        $returnUser['invalid_id'] = '已删除';
                    } else {
                        $returnUser['invalid_id'] = '在群内';
                    }
                    $returnArr['msg'] = 'differ';
                    array_push($returnArr['data'], $returnUser);
                }
            }
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
        return;
    }

    //入群会员删除
    public function findDel()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $del = [];
        $where['weix_name'] = $subInfo['weix_name'];
        $where['weix_qun'] = $subInfo['weix_qun'];
        $result = M('ck_find_user')->where($where)->select();
        if ($result) {
            foreach ($result as $item) {
                $del['id'] = $item['id'];
                $del['invalid_id'] = 1;
                $result_revise = M('ck_find_user')->save($del);
            }
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        }
    }

    //恢复删除会员
    public function findRenew()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $del = [];
        $where['weix_name'] = $subInfo['weix_name'];
        $where['weix_qun'] = $subInfo['weix_qun'];
        $result = M('ck_find_user')->where($where)->select();
        if ($result) {
            foreach ($result as $item) {
                $del['id'] = $item['id'];
                $del['invalid_id'] = 0;
                $result_revise = M('ck_find_user')->save($del);
            }
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        }
    }

    //增加会员入群
    public function findTyp()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $data['weix_name'] = $subInfo['weix_name'];
        $data['weix_user'] = $subInfo['weix_user'];
        $data['weix_tel'] = $subInfo['weix_tel'];
        $data['weix_comp'] = $subInfo['weix_comp'];
        $data['weix_qun'] = $subInfo['weix_qun'];
        $result = M('ck_find_user')->add($data);
        if ($result) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }

    //会员信息查询
    public function findSel()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $returnUser = [];
        $returnArr = array();
        $returnArr['data'] = array();
        if ($subInfo['mas'] == '微信号') {
            $where['weix_name'] = $subInfo ['num'];
        } else if ($subInfo['mas'] == '姓名') {
            $where['weix_user'] = $subInfo ['num'];
        } else if ($subInfo['mas'] == '电话') {
            $where['weix_tel'] = $subInfo ['num'];
        } else if ($subInfo['mas'] == '公司') {
            $where['weix_comp'] = $subInfo ['num'];
        } else if ($subInfo['mas'] == '群名') {
            $where['weix_qun'] = $subInfo ['num'];
        }
        $result = M('ck_find_user')->where($where)->select();
        if (!$result) {
            $returnArr['msg'] = 'no';
        } else {
            foreach ($result as $item) {
                $returnUser['id'] = $item['id'];
                $returnUser['weix_name'] = $item['weix_name'];
                $returnUser['weix_user'] = $item['weix_user'];
                $returnUser['weix_tel'] = $item['weix_tel'];
                $returnUser['weix_comp'] = $item['weix_comp'];
                $returnUser['weix_qun'] = $item['weix_qun'];
                $returnUser['record_time'] = $item['record_time'];
                if ($item['invalid_id'] == 1) {
                    $returnUser['invalid_id'] = '已删除';
                } else {
                    $returnUser['invalid_id'] = '在群内';
                }
                $returnArr['msg'] = 'yes';
                array_push($returnArr['data'], $returnUser);

            }
        }
        echo json_encode($returnArr);
        return;
    }

    //会员信息修改
    public function  findChange(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['id'] = $subInfo['id'];
        $where['weix_name'] = $subInfo['weix_name'];
        $result = M('ck_find_user')->where($where)->find();
        if ($result) {
            $temp = array();
            $temp['id'] = $subInfo['id'];
            $temp['weix_name'] = $subInfo['weix_name'];
            $temp['weix_user'] = $subInfo['weix_user'];
            $temp['weix_tel'] = $subInfo['weix_tel'];
            $temp['weix_comp'] = $subInfo['weix_comp'];
            $temp['weix_qun'] = $subInfo['weix_qun'];
            $result_revise = M('ck_find_user')->save($temp);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        } else {
            echo 'failure1';
        }
    }
}