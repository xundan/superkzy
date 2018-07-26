<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/3/28
 * Time: 9:51
 */

namespace Views\Controller;


use Think\Controller;
use Views\Model\MessageModel;

class MessageStatisticController extends Controller
{
    const SUCCESS = 1;
    const FAIL = 0;
    const COUNT_ROW = 20;

    public function msg_inquiry()
    {
        $this->display();
    }

    public function test()
    {
        $subInfo['search'] = "他们";
        $subInfo['date_start'] = "20180606";
        $subInfo['date_end'] = "20180607";
//        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = array();
        $where['content'] = array('like', '%' . $subInfo['search'] . '%');
        $where['remark'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
        $result = M('ck_display')->where($where)->order('id desc')->select();
        if ($result){
            $returnArr['msg'] = 'success';
        } else {
            $returnArr['msg'] = 'fall';
        }
        $returnArr['data'] = $result;
        dump($returnArr);
    }

    public function test2()
    {
        M()->execute('DELETE FROM `ck_display_test` WHERE `id` not in (SELECT MAX(`id`) FROM (SELECT * FROM `ck_display_test`) AS a GROUP BY `content_md5`)');
        dump(M()->getLastSql());
    }


    public function user_add_action()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['create_time'] = array("BETWEEN", array($start, $end));
        $result['user_count'] = count(M('ck_user')->where($where)->select());
        $where['phone_number'] = array('exp', 'is not null');
        $result['user_reg_count'] = count(M('ck_user')->where($where)->select());
        $result['user_unreg_count'] = $result['user_count'] - $result['user_reg_count'];
        echo json_encode($result);
        return;
    }

    public function feedback_action()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result = M('ck_feedback')->join('LEFT JOIN ck_user ON ck_user.uid=ck_feedback.uid')->where($where)->select();
        echo json_encode($result);
        return;
    }

    public function message_add_action()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $Msg = new MessageModel();
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result['wx'] = count($Msg->where($where)->where("type = '%s'", 'plain')->select());
        $result['group'] = count($Msg->where($where)->where("type = '%s'", 'group')->select());
        $result['wx_mp'] = count($Msg->where($where)->where("type = '%s'", 'wx_mp')->select());
        $result['web'] = count($Msg->where($where)->where("type = '%s'", 'web')->select());
        echo json_encode($result);
        return;
    }

    public function financial_client_action()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result = M('ck_financial_client')->where($where)->select();
        echo json_encode($result);
        return;
    }

    public function lottery_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $wherecoal = [];
        $returnUser = [];
        $returnArr = array();
        $returnArr['data'] = array();
        $result = M('ck_coal_cooperate')->where($where)->order('id desc')->select();
        if ($result) {
            foreach ($result as $item) {
                $wherecoal['uid'] = $item['uid'];
                $returnUser['id'] = $item['id'];
                $returnUser['record_time'] = $item['record_time'];
                $returnUser['content'] = $item['content'];
                $resultadd = M('ck_user')->where($wherecoal)->select();
                if ($resultadd) {
                    foreach ($resultadd as $user) {
                        $returnUser['user_name'] = $user['user_name'];
                        $returnUser['phone_number'] = $user['phone_number'];
                        $returnUser['heading_url'] = $user['heading_url'];
                        $returnArr['msg'] = 'yes';
                        array_push($returnArr['data'], $returnUser);
                    }
                } else {
                    $returnArr['msg'] = 'no';
                    echo self::FAIL;
                }
            }
        } else {
            $returnArr['msg'] = 'no';
            echo self::FAIL;
        }
        echo json_encode($returnArr);
        return;
    }

    public function TimestampToTime($date)
    {
        date_default_timezone_set("Asia/Shanghai");
        $result = date("Y-m-d H:i:s", $date);
        return $result;
    }

    public function statistic_all()
    {
        $Msg = new MessageModel();
        $this->assign("res", $Msg->statistics_by_day());
        $this->display();
    }

    public function all_chart()
    {
        $this->display();
    }

    public function statistics()
    {
        $Msg = new MessageModel();
        $data = array();
        array_push($data, $Msg->all_statistics_by_day());
        array_push($data, $Msg->plain_statistics_by_day());
        array_push($data, $Msg->group_statistics_by_day());
        array_push($data, $Msg->wx_mp_statistics_by_day());
        array_push($data, $Msg->web_statistics_by_day());
        echo json_encode($data);
    }

    public function history_search($s_date = null, $e_date = null, $kw, $cc = "供应")
    {

        if (!$s_date) $s_date = date("Y-m-d", strtotime("-7 day"));
        if (!$e_date) $e_date = date("Y-m-d");

        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d", strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d", strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d", strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d", strtotime("+7 day", $this_e_time));

        $Msg = new MessageModel();

        $tableArr = M()->query("show tables like 'ck_messages_2%'");
        $tableNameArr = array_map('end', $tableArr);

        $sqlStrArr = array();
        foreach ($tableNameArr as $item) {
            $sqlStrArr[] = "select phone_number,content,type,sender,update_time from " . $item . " where content like '%" . $kw . "%' and content not like '%" . $kw . "元%' and content not like '%" . $kw . "吨%' and category='" . $cc . "' and update_time>'" . $s_date . "' and update_time<'" . $e_date . "'";
        }

//        $res = $Msg->get_all_history($kw, $cc, $s_date, $e_date);
//        $this->field("phone_number,content,type,sender,update_time")->where("content like '%$kw%' and content not like '%$kw"
//            . "元%' and content not like '%$kw" . "吨%' and category='$cc' and update_time>'$s_date' and update_time<'$e_date'")->order("id desc")->select();

        $sqlStr = implode($sqlStrArr, ' union ');
        $res = M()->query($sqlStr);

        $this->assign("res", $res);
        $this->assign("kw", $kw);
        $this->assign("cc", $cc);
        $this->assign("s_date", $s_date);
        $this->assign("e_date", $e_date);
        $this->assign("prev_s_date", $prev_s_date);
        $this->assign("next_s_date", $next_s_date);
        $this->assign("prev_e_date", $prev_e_date);
        $this->assign("next_e_date", $next_e_date);

        $this->display();
//        foreach($res as $message){
//            echo "<br>";
//            var_dump($message);
//            echo "<br>";
//        }
    }

    //信息获取
    public function displayMsg()
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $subInfo = I('get.', '', 'trim,strip_tags');
        if ($subInfo['isAjax']) {
            //下拉刷新
            $page = $subInfo['page'];
            $where = array();
            if ($subInfo['searchInput']) {
                $where['content'] = array('like', '%' . $subInfo['searchInput'] . '%');
            }
            $result = $this->getMsg($where, $page);
            if (count($result) < self::COUNT_ROW) {
                $returnArr['end_tag'] = 'end';
            } else {
                $returnArr['end_tag'] = 'continue';
            }
            $returnArr['data'] = $result;
            $returnArr['msg'] = 'success';
            echo json_encode($returnArr);
            exit;
        } else {
            //页面初始化
            $where = array();
            $result = $this->getMsg($where);
            $this->assign('data', $result);
        }
        $this->display();
    }

    public function displayMsgMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //设置条件
        $page = $subInfo['page'];
        $where = array();
        if ($subInfo['searchInput']) {
            $where['content'] = array('like', '%' . $subInfo['searchInput'] . '%');
        }
        $result = $this->getMsg($where, $page);
        if (count($result) < self::COUNT_ROW) {
            $returnArr['end_tag'] = 'end';
        } else {
            $returnArr['end_tag'] = 'continue';
        }
        $returnArr['data'] = $result;
        $returnArr['msg'] = 'success';
        echo json_encode($returnArr);
    }

    public function getMsg($where, $page = 1)
    {
        $countRow = self::COUNT_ROW;
        $beginStr = ($page - 1) * $countRow;
        $msg = M('ck_display')->where($where)->limit($beginStr, $countRow)->order('id desc')->select();
        return $msg;
    }

    public function searchMsg()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        if ($subInfo['searchInput']) {
            $where['content'] = array('like', '%' . $subInfo['searchInput'] . '%');
            $result = M('ck_display')->where($where)->order('id desc')->select();
            if (!$result) {
                echo self::FAIL;
                exit;
            }
            $returnArr['data'] = $result;
            $returnArr['msg'] = 'success';
            echo json_encode($returnArr);
        } else {
            echo self::FAIL;
        }
    }

    public function pushMsgShow()
    {
        $this->display();
    }

    public function pushMsgAction()
    {
        $subInfo = I('post.', '', 'trim');
        $uid = $subInfo['uid'];
        $content = $subInfo['content'];
//        $result = $GLOBALS['HTTP_RAW_POST_DATA'];
//        $object = json_decode($result, true);

        $where['uid'] = $uid;
        $user = M('ck_display_user')->where($where)->find();
        if ($user) {
            $data['user_name'] = $user['user_name'];
        } else {
            //找不到用户
            $data['user_name'] = 'Unknown';
        }
        $data['uid'] = $uid;
        $data['content'] = $content;
        $data['content_md5'] = md5($content);
//        $remark = substr($content,0,8);
        if (preg_match('/\d{8}/', $content, $match)) {
            $remark = $match[0];
            $data['remark'] = $remark;
            $data['record_time'] = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1-$2-$3 00:00:00', $remark);
        } else {
            $data['remark'] = date('Ymd');
            $date['record_time'] = date('Y-m-d H:i:s');
        }
        $add = M('ck_display')->add($data);
        if ($add) {
            echo 1;
        } else {
            echo 2;
            //插入数据库失败
        }
    }

    public function leave()
    {
        $this->display();
    }

    public function userSearch()
    {
        $subInfo = I('post.', '', 'trim');
        $user_name = $subInfo['user_name'];
        $whereUser['user_name'] = array('like', '%' . $user_name . '%');
        $whereUser['invalid_id'] = 0;
        $resultUser = M('ck_display_user')->where($whereUser)->select();
        if ($resultUser) {
            echo json_encode($resultUser);
        } else {
            echo 0;
        }
    }

    public function leaveAction()
    {
        $subInfo = I('post.', '', 'trim');
        $user_name = $subInfo['user_name'];
        $whereLeave['leave_time'] = array('like', Date('Y-m-d') . '%');
        $whereLeave['user_name'] = $user_name;
        $alreadyLeave = M('ck_display_leave')->where($whereLeave)->find();
        if ($alreadyLeave) {
            //当天已请假
            echo 'exist';
            exit;
        }
        $whereUser['user_name'] = $user_name;
        $user = M('ck_display_user')->where($whereUser)->find();
        if ($user) {
            $data['uid'] = $user['uid'];
            $data['user_name'] = $user_name;
            $data['leave_time'] = $subInfo['leave_time'];
            $data['deadline'] = $subInfo['deadline'];
            $addResult = M('ck_display_leave')->add($data);
            if ($addResult) {
                echo 'success';
            } else {
                echo 'fail';
            }
        } else {
            echo 'user_name not exist';
        }

    }

    public function displayDel()
    {
        $this->display();
    }

    //提取名单
    public function displayDelAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where_time = [];
        $returnArr = array();
        $result_uid = array();
        $returnArr['data'] = array();
        $whereFinished['record_time'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
        $resultFinished = M('ck_display')->where($whereFinished)->group('uid')->select();
        if ($resultFinished) {
//            $uidArray = array();
//            foreach ($result_time as $item) {
//                array_push($uidArray, $item['uid']);
//            }
            $uidsFinished = array_column($resultFinished, 'uid');
//            $whereLeave['leave_time'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
//            $whereLeave['deadline'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
//
//            $resultLeave = M('ck_display_leave')->where($whereLeave)->select();
            $resultLeave = M()->query("select * from `ck_display_leave` where `leave_time` between '" . $subInfo['date_start'] . "' and '" . $subInfo['date_end'] .
                "' or `deadline` between '" . $subInfo['date_start'] . "' and '" . $subInfo['date_end'] .
                "' or (`leave_time` < '" . $subInfo['date_start'] . "' and `deadline` > '" . $subInfo['date_end'] . "')");
            if ($resultLeave) {
                $uidsLeave = array_column($resultLeave, 'uid');
                $uidsFinished = array_merge($uidsFinished, $uidsLeave);
            }
            $whereUnfinished['invalid_id'] = 0;
            $whereUnfinished['uid'] = array('not in', $uidsFinished);
            $resultUnfinished = M('ck_display_user')->where($whereUnfinished)->order('uid asc')->select();
            if ($resultUnfinished) {
                foreach ($resultUnfinished as $user) {
                    $result_uid['uid'] = $user['uid'];
                    $result_uid['user_name'] = $user['user_name'];
                    $returnArr['msg'] = self::SUCCESS;
                    array_push($returnArr['data'], $result_uid);
                }
            } else {
                $returnArr['msg'] = 2;
            }
        } else {
            $returnArr['msg'] = self::FAIL;
        }
        echo json_encode($returnArr);
    }

    //时间关键词搜索
    public function displayNo()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = array();
        $where['content'] = array('like', '%' . $subInfo['search'] . '%');
        $where['remark'] = array('between', array($subInfo['date_start'], $subInfo['date_end']));
        $result = M('ck_display')->where($where)->order('id desc')->select();
        if ($result){
            $returnArr['msg'] = 'success';
        } else {
            $returnArr['msg'] = 'fall';
        }
        $returnArr['data'] = $result;
        echo json_encode($returnArr);
    }
}