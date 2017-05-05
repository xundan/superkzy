<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/5/5
 * Time: 10:03
 */

namespace Views\Controller;


use Think\Controller\RestController;
use Think\Log;
use Views\Model\LogModel;

class LogPrepareController extends RestController
{

    public function index()
    {
        echo "LogPrepare works.<br>";
//        $Log = new LogModel();
//        $data['id']=1;
//        $data['duration']=0;
//        echo $Log->save($data);
    }

    public function yesterday()
    {
        $yesterday=date("Y-m-d",strtotime("-1 day"));
        $this->day($yesterday);
    }

    /**
     * @param $day string 日期
     */
    public function day($day)
    {
        $Log = new LogModel();

        $res = $Log->update_duration($day);
        if ($res===0) Log::record('LogPrepare:['.$day.'] has 0 user to prepare.', Log::INFO);
        if ($res===false) Log::record('LogPrepare:['.$day.'] update failed.', Log::ERR);
    }
}