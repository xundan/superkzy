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
    public function msg_inquiry(){
        $this->display();
    }

    public function user_add_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['create_time'] = array("BETWEEN", array($start, $end));
        $result['user_count'] = count(M('ck_user')->where($where)->select());
        $where['phone_number'] = array('exp','is not null');
        $result['user_reg_count'] = count(M('ck_user')->where($where)->select());
        $result['user_unreg_count'] = $result['user_count']-$result['user_reg_count'];
        echo json_encode($result);
        return;
    }

    public function feedback_action(){
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

    public function message_add_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $Msg = new MessageModel();
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result['wx'] = count($Msg->where($where)->where("type = '%s'",'plain')->select());
        $result['group'] = count($Msg->where($where)->where("type = '%s'",'group')->select());
        $result['wx_mp'] = count($Msg->where($where)->where("type = '%s'",'wx_mp')->select());
        $result['web'] = count($Msg->where($where)->where("type = '%s'",'web')->select());
        echo json_encode($result);
        return;
    }

    public function financial_client_action(){
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

    public function lottery_action(){
        $result = M('ck_activity_2017_05')->join('LEFT JOIN ck_user ON ck_user.uid=ck_activity_2017_05.uid')->select();
        echo json_encode($result);
        return;
    }

    public function TimestampToTime($date){
        date_default_timezone_set("Asia/Shanghai");
        $result = date("Y-m-d H:i:s", $date);
        return $result;
    }

    public function statistic_all(){
        $Msg = new MessageModel();
        $this->assign("res",$Msg->statistics_by_day());
        $this->display();
    }

    public function all_chart(){
        $this->display();
    }
    public function statistics(){
        $Msg = new MessageModel();
        $data = array();
        array_push($data, $Msg->all_statistics_by_day());
        array_push($data, $Msg->plain_statistics_by_day());
        array_push($data, $Msg->group_statistics_by_day());
        array_push($data, $Msg->wx_mp_statistics_by_day());
        array_push($data, $Msg->web_statistics_by_day());
        echo json_encode($data);
    }

    public function history_search($s_date=null,$e_date=null,$kw,$cc="供应"){

        if(!$s_date) $s_date = date("Y-m-d",strtotime("-7 day"));
        if(!$e_date) $e_date = date("Y-m-d");

        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Msg = new MessageModel();

        $res = $Msg->get_all_history($kw,$cc,$s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("kw",$kw);
        $this->assign("cc",$cc);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);

        $this->display();
//        foreach($res as $message){
//            echo "<br>";
//            var_dump($message);
//            echo "<br>";
//        }
    }
}