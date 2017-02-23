<?php
/**
 * crontab调用从生消息表转移数据至消息表
 * User: CLEVO
 * Date: 2016/8/9
 * Time: 14:33
 */

namespace Views\Controller;

use Think\Controller\RestController;

class DistributeController extends RestController
{
    public function index()
    {
        echo "distribute works";
        $temp = D('Raw')->find(1);
        echo $temp['send_time'];
        echo "<br>";
        echo $this->dateAfter($temp['send_time'], 3);
    }

    public function create($recorder)
    {

        $Raw = D('Raw');
        $Message = D('Message');

        $map['status'] = 0;
        $transferring = $Raw->where($map)->select();
        $count = count($transferring);
        $now = time();
        $insert_trans = array(
            "title" => null,
            "origin" => null,
            "category" => null,
            "publisher_rid" => null,
            "publish_time" => $now,
            "level" => 1,
            "valid_time" => 3, // 默认为3天
            "via_type" => 2,
            "times_number" => 1,
            "type" => null,
            "content" => null,
            "content_all" => null,
            "status" => 0,
            "remark" => null,
            "invalid_id" => 0,
            "handler" => null,
            "recorder" => $recorder,
            "owner" => null,
            "sender_wx" => null,
            "sender" => null,
        );
        $update_trans = array(
            "id" => -1,
            "status" => 10,
        );
        for ($i = 0; $i < $count; $i++) {
            $trans1 = $transferring[$i];
            // 根据id更新生消息表状态
            $update_trans["id"] = $trans1["id"];
            $check = $Raw->save($update_trans);

            if ($check == false) {
                //TODO 写日志，通知开发
            }
            // 生成新的消息表数据
            $insert_trans["title"] = $trans1["rid"];
            $insert_trans["content"] = $trans1["content"];
            $insert_trans["content_all"] = $trans1["content"];
            $insert_trans["sender"] = $trans1["sender"];
            $insert_trans["type"] = $trans1["type"];
            $insert_trans["owner"] = $trans1["owner"];
            $insert_trans["sender_wx"] = $trans1["sender_wx"];
            $insert_trans["remark"] = $trans1['remark'];
            // 算出message的有效期
            $insert_trans["deadline"] = $this->dateAfter($trans1["send_time"], $insert_trans["valid_time"]);

            $content = $trans1['content'];
            $mode = '/([0-9]{11})|(\+86[0-9]{11})/'; //正则，必须写在反斜杠里面
            preg_match($mode, $content, $match);
            $origin = "未知";
            if ($match) {
                $origin = $match[0];
            }
            $insert_trans['origin'] = $origin;
            $insert_trans['phone_number'] = $origin;
//            var_dump($insert_trans);
            $check = $Message->add($insert_trans);
            if ($check == false) {
                //TODO 写日志，通知开发
            }
        }
        // 只有转移数量大于0时，才记录
        if ($count > 0) {
            $record = array(
                'message' => 'Raw_to_Msg:' . $count,
                'type' => 'Raw_to_Msg',
                'remark' => '' . $count,
            );
            D('Distribute')->add($record);
        }
    }

    /**
     * @param $baseDate String time-string
     * @param $interval int
     * @return string
     */
    private function dateAfter($baseDate, $interval)
    {
        $time = strtotime($baseDate) + $interval * 86400;
        return date("Y-m-d H:i:s", $time);
    }

}