<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/4/13
 * Time: 18:51
 */

namespace Views\Controller;


use Think\Controller;
use Views\Model\LogModel;

class LogStatisticsController extends Controller
{
    public function index(){
        $today = date("Y-m-d",strtotime("-1 day"));
        $this->redirect('LogStatistics/show', array('date' => $today));


    }

    public function show($date){
        // 计算前后一天
        $this_time = strtotime($date);
        $prev_date = date("Y-m-d",strtotime("-1 day", $this_time));
        $next_date = date("Y-m-d",strtotime("+1 day", $this_time));

        $Logs = new LogModel();
        $res = $Logs->all_by_date($date);
        $this->assign("res",$res);
        $this->assign("date",$date);
        $this->assign("prev_date",$prev_date);
        $this->assign("next_date",$next_date);
        $this->display();
    }

    public function show_ajax($date){
        $Logs = new LogModel();
        $res = $Logs->all_by_date($date);
        echo json_encode($res);
    }

    public function user_detail($uid,$date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_uid($uid,$date);
        $user = $details['user'];
        unset($details['user']);
        $this->assign("details",$details);
        $this->assign("date",$date);
        $this->assign('user_info',$user);
        $this->display();
    }

    public function user_detail_ajax($uid,$date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_uid($uid,$date);
        unset($details['user']);
        echo json_encode($details);
    }

    public function page($date){

        $this_time = strtotime($date);
        $prev_date = date("Y-m-d",strtotime("-1 day", $this_time));
        $next_date = date("Y-m-d",strtotime("+1 day", $this_time));

        $Logs = new LogModel();
        $res = $Logs->group_page_by_title($date);
        $this->assign("res",$res);
        $this->assign("date",$date);
        $this->assign("prev_date",$prev_date);
        $this->assign("next_date",$next_date);
        $this->display();
    }

    public function page_ajax($date){
        $Logs = new LogModel();
        $res = $Logs->group_page_by_title($date);
        echo json_encode($res);
    }
}