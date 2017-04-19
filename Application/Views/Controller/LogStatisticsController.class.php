<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/4/13
 * Time: 18:51
 */

namespace Views\Controller;


use Think\Controller;

class LogStatisticsController extends Controller
{
    public function index(){
        $today = date("Y-m-d");
//        $this->show($today);
        $this->redirect('LogStatistics/show', array('date' => $today));
    }

    public function show($date){

        $Logs = D("Log");
        $res = $Logs->all_by_date($date);
        $this->assign("res",$res);
        $this->assign("date",$date);
        $this->display();
    }

    public function user_detail($uid,$date)
    {
        $Logs = D("Log");
        $details = $Logs->detail_by_uid($uid,$date);
        $this->assign("details",$details);
        $this->display();
    }
}