<?php
/**
 * crontab调用从生消息表转移数据至消息表
 * User: CLEVO
 * Date: 2016/8/9
 * Time: 14:33
 */

namespace Views\Controller;

use Think\Controller\RestController;
use Think\Log;
use Views\Model\MessageModel;

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
        $Message = new MessageModel();

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
            "valid_time" => 7,//C('EXPIRE_DAYS') 默认为7天
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
            "vip" => "1",
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
                Log::record("DistributeController: Update Raw false: sql->" . $Raw->getLastSql(), Log::ERR);
            }
            // 生成新的消息表数据
            $insert_trans["title"] = $trans1["rid"];
            $insert_trans["content"] = $trans1["content"];
            $insert_trans["content_all"] = $trans1["content"];
            $tempContent = strip_tags($trans1["content"]);
            $tempContent = preg_replace('/\s+/','',$tempContent);
            $insert_trans["content_all_md5"] = md5($tempContent);
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
            $filter_ad_pattern = '/(资格证|冷柜|二维码|从业资格证|增值税|苯|转让|饮料|钢材|双驱|发票)/';
            $is_ad = preg_match($filter_ad_pattern, $content);
            if ($insert_trans['type'] == "wx_mp") {
                $insert_trans['vip'] = "3";
            } elseif ($insert_trans['type'] == "plain") {
                $insert_trans['vip'] = "3";
            } else {
                $insert_trans['vip'] = "1";
            }
            // 从vip微信号上接收的都是vip
            if ($insert_trans['sender_wx'] == "cjkzywl") {
                $insert_trans['vip'] = $this->vipSet($origin);
                $insert_trans['type'] = 'plain';
                $check = $Message->add_by_md5($insert_trans, true);
            } else {
                if($is_ad){
                    $check = false;
                    Log::record("###ad###", Log::INFO);
                }else{
                    $check = $Message->add_by_md5($insert_trans, false);
                }
            }
            if ($check === false) {
                Log::record("DistributeController: Add Messages false: sql->" . $Message->getLastSql(), Log::ERR);
            } elseif ($check === 0) {
                Log::record("DistributeController: duplicate message: md5->" . $insert_trans['content_all_md5'], Log::INFO);
            } else {

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

    private function vipSet($phoneNumber)
    {
        $whereClient['invalid_id'] = 0;
        $whereClient['phone_number'] = $phoneNumber;
        $payingClient = M('ck_paying_client')->where($whereClient)->order('update_time desc')->find();
        if ($payingClient) {
            switch ($payingClient['pay_type']) {
                case '年费':
                    $vip = '8';
                    break;
                case '半年费':
                    $vip = '7';
                    break;
                case '季费':
                    $vip = '6';
                    break;
                case '月费':
                    $vip = '5';
                    break;
                default:
                    $vip = '5';
                    break;
            }
        } else {
            $tempVip = M('ck_temp_vip')->where($whereClient)->order('update_time desc')->find();
            if ($tempVip) {
                $vip = '5';
            } else {
                $vip = '3';
            }
        }
        return $vip;
    }

}