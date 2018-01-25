<?php
namespace Views\Controller;

use Think\Controller;
use Views\Model\MessageModel;

class MsgCheckController extends Controller
{
    const LENGTH = 30;
    const STATE_CHECKING = 1;
    const STATE_CHECKED = 2;
    const STATE_ABORTED = 3;
    const STATE_CHECKING_EX_GROUP = 4;
    const STATE_CHECKED_EX_GROUP = 5;

    public function test()
    {
        header("Content-Type:text/html; charset=utf-8");
//        dump(time());
//        dump(Date('Y-m-d H:i:s'));
//        M('ck_check_flow')->order('id desc')->find();
//        dump(M()->getLastSql());
//        dump(M('ck_messages')->order('id asc')->find());
//        $a = $this->fetchMsg(200);
//        dump(M()->getLastSql());
//        dump($a);
//        if($a){
//            dump(2);
//        }
//        dump(count($a));
//        $subInfo = I('post.', '', 'trim,strip_tags');
//        $msgData = $this->fetchMsg('', session('cur_user')['msg_step'], $subInfo['group']);
//        dump(M()->getLastSql());


//        $where2['id'] =2;
//        $result2 = M('ck_messages')->where($where2)->find();
//        dump($result2);

//        $checkFlow = array(
//            'uid' => session('cur_user')['id'],
//            'msg_start_id' => 400,
//            'msg_end_id' => 500,
//            'status' => 1,
//            'abort_id' => 0,
//            'stop_time' => 0
//        );
//        M('ck_check_flow')->add($checkFlow);
        $FC = A('FC');
        dump($FC->getDistanceByAddress('中国,陕西省,榆林市,榆阳区,金鸡滩煤矿', '中国,山东省,济宁市,微山县'));
    }

    /**
     * 审核信息页面
     */
    public function show()
    {
//        if (true) {
        if ($_SESSION['cur_user']) {
            $cur_user = $_SESSION['cur_user'];
            $this->assign('cur_user', $cur_user);
            $this->display();
        } else {
            $this->reLog();
        }
    }

    /**
     * 拉取信息方法
     * 同时改变用户拉取流程表操作
     */
    public function getData()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $returnArr = array();
        if (session('cur_user')) {
//        if (true) {
            //用户已登录状态
            //取得上次记录流程
            $where['uid'] = session('cur_user')['id'];
            $checkRecord = M('ck_check_flow')->where($where)->order('id desc')->find();
            //去ck_check_flow表里取上一次拉取的位置
            if ($subInfo['id']) {
                //如果前台传id进来，说明是手动点击拉取
                if ($subInfo['group'] == 'on') {
                    //前台正常拉取所有信息
                    $whereLast['status'] = array('in', array($this::STATE_CHECKING, $this::STATE_CHECKED));
                } else {
                    //前台需要拉取非群消息
                    $whereLast['status'] = array('in', array($this::STATE_CHECKING_EX_GROUP, $this::STATE_CHECKED_EX_GROUP));
                }
                $checkLast = M('ck_check_flow')->where($whereLast)->order('id desc')->find();
                $lastMsgId = $checkLast['msg_end_id'];
            } else {
                //前台没传id进来，则为第一次拉取/清除数据后的第一次拉取/换号登陆拉取
                $lastMsgId = '';
            }
            //拉取信息
            $msgData = $this->fetchMsg($lastMsgId, session('cur_user')['msg_step'], $subInfo['group']);
            $returnArr['msgCount'] = count($msgData);
            if ($returnArr['msgCount'] > 0) {
                //成功取出数据，输出到前台时把该操作流程记录进ck_check_flow表
                $checkFlow = array(
                    'uid' => session('cur_user')['id'],
                    'msg_start_id' => $msgData[0]['id'],
                    'msg_end_id' => $msgData[$returnArr['msgCount'] - 1]['id'],
                    'status' => ($subInfo['group'] == 'off') ? $this::STATE_CHECKING_EX_GROUP : $this::STATE_CHECKING,
//                    'record_time' => Date('Y-m-d H:i:s'),
                    'abort_id' => 0,
                    'stop_time' => 0
                );
                M('ck_check_flow')->add($checkFlow);
                $returnArr['feedback'] = '信息拉取成功';
                $returnArr['data'] = $msgData;
            } else {
                //已经没有数据可以拉取了
                $returnArr['feedback'] = '数据库已经没有信息啦！';
            }
            //将上次审核状态设为结束，并更新stop_time,如果有的话
            if ($checkRecord && $checkRecord['status'] != $this::STATE_ABORTED) {
                if ($checkRecord['status'] == $this::STATE_CHECKING) {
                    //上次拉取的是所有信息
                    $checkRecordUpdate['status'] = $this::STATE_CHECKED;
                } else {
                    //上次拉取的是非群消息
                    $checkRecordUpdate['status'] = $this::STATE_CHECKED_EX_GROUP;
                }
                if ($subInfo['stop_time']) {
                    $checkRecordUpdate['stop_time'] = $subInfo['stop_time'];
                }
                $checkRecordUpdate['id'] = $checkRecord['id'];
                M('ck_check_flow')->save($checkRecordUpdate);
            } else {
            }
            //输出结果到前台
            echo json_encode($returnArr, true);
        } else {
            //读不着用户session，返回登录界面
            $this->reLog();
        }
    }

    /**
     * 拉取信息函数
     * @param null $id 拉取信息的开始位置
     * @param null $count 一次拉取信息数量
     * @param null $group 是否拉取群信息
     * @return mixed    返回拉取结果数组
     */
    public function fetchMsg($id = null, $count = null, $group = null)
    {
        if (!$count) {
            $count = $this::LENGTH;
        }
        //拉取条件
        $where['status'] = 0;
        $where['invalid_id'] = 0;
        //id条件
        if ($id) {
            $where['id'] = array('gt', $id);
        }
        //释放后的id加入id条件
        $whereAbort['status'] = $this::STATE_ABORTED;
        $checkRecord = M('ck_check_flow')->where($whereAbort)->order('update_time desc')->select();
        if ($checkRecord) {
            $idCond = array();
            foreach ($checkRecord as $item) {
                //将释放的id加入id条件
                array_push($idCond, array('between', array($item['abort_id'], $item['msg_end_id'])));
                //将该item的status字段设为完成
                $itemUpdate['id'] = $item['id'];
                $itemUpdate['status'] = $this::STATE_CHECKED;
                M('ck_check_flow')->save($itemUpdate);
            }
            if ($id) {
                array_push($idCond, array('gt', $id));
            }
            array_push($idCond, 'or');
            $where['id'] = $idCond;
        }
        if ($group == 'off') {
            //只拉取非群信息
            $where['type'] = array('in', array('plain', 'wx_mp'));
        } else {
            //拉取所有信息
            $where['type'] = array('in', array('plain', 'group', 'wx_mp'));
        }
        $resultMsg = M('ck_messages')->where($where)->order('id asc')->limit($count)->select();
        return $resultMsg;
    }

    /**
     * 审核提交操作
     */
    public function check_sub()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        // 检查是否已经有了前缀
        $content = $subInfo['content'];
        $prefix = substr($subInfo['content'], 0, 12);
        if (($prefix != "【" . $subInfo['tag'] . "】")) {
            $content = "【" . $subInfo['tag'] . "】" . $content;
        }
        $update_trans = array(
            "id" => $subInfo['id'],
            "category" => $subInfo['tag'],
            "content" => $content,
            "handler" => $subInfo['handler'],
            "status" => 102,
            "content_all" => $content,
            "invalid_id" => 0
        );
        $result = D('Message')->save($update_trans);
        if ($result) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }

    /**
     * 删除群信息操作
     */
    public function delete()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $update_delete = array(
            "id" => $subInfo['id'],
            "handler" => $subInfo['handler'],
            "status" => -1,
            "invalid_id" => 2,
        );
        if (D('Message')->save($update_delete)) echo 'deleted';
    }

    /**
     * 删除所有未审核的群消息
     */
    public function delete_all()
    {
        $Msg = new MessageModel();
        if ($Msg->del_all_group_msg()) echo 'deleted';
    }

    /**
     * 释放放弃信息，剩余信息进拉取空间，并改变改用户状态
     */
    public function release()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['uid'] = session('cur_user')['id'];
        $checkRecord = M('ck_check_flow')->where($where)->order('update_time desc')->find();
        $checkRecord['status'] = $this::STATE_ABORTED;
        $checkRecord['abort_id'] = $subInfo['id'];
        $checkRecord['stop_time'] = $subInfo['stop_time'];
        $result = M('ck_check_flow')->save($checkRecord);
        if ($result) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }

    /**
     *获取未审核信息条数
     */
    public function uncheckCount()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $id = $subInfo['id'];
        $result = D('Message')->where("invalid_id=0 AND type in ('plain','group','wx_mp') AND status=0 AND id>" . $id)
            ->select();
        if ($result) {
//            return count($result);
            echo count($result);
        } else {
//            return 0;
            echo 0;
        }
    }

    /**
     * 运费提交操作
     */
    public function freightSubmit()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');

        $data['recorder'] = $subInfo['recorder'];
        $data['message_id'] = $subInfo['message_id'];
        $data['phone_number'] = $subInfo['phone_number'];
        $data['area_start_id'] = $subInfo['area_start_id'];
        $data['area_start_name'] = $subInfo['area_start_name'];
        $data['area_start_detail'] = $subInfo['area_start_detail'];
        $data['area_start_merger_name'] = $subInfo['area_start_merger_name'];
        $data['area_end_id'] = $subInfo['area_end_id'];
        $data['area_end_name'] = $subInfo['area_end_name'];
        $data['area_end_detail'] = $subInfo['area_end_detail'];
        $data['area_end_merger_name'] = $subInfo['area_end_merger_name'];
        $data['freight_price'] = $subInfo['freight_price'];
        $data['invalid_id'] = 0;

        $FC = A('FC');
        $data['distance'] = $FC->getDistanceByAddress($subInfo['area_start_merger_name'], $subInfo['area_end_merger_name']);

        //运费估算
        $returnArr['freight_forecast'] = $this->freightForecast($subInfo['area_start_id'],$subInfo['area_end_id'],$data['distance']);

        if ($subInfo['id']) {
            $data['id'] = $subInfo['id'];
            $result = M('ck_freight')->save($data);
        } else {
            $result = M('ck_freight')->add($data);
        }
        if ($result) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "success";
            $returnArr['id'] = $result;
            echo json_encode($returnArr);
        }
    }

    /**
     * 运费修改界面
     */
    public function freightModify()
    {
        $this->display();
    }

    /**
     * 运费查询
     */
    public function freightSearch()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');
        if ($subInfo['id']) {
            $where['id'] = $subInfo['id'];
            $result = M('ck_freight')->where($where)->find();
            if ($result) {
                $whereMsg['id'] = $result['message_id'];
                $resultMsg = M('ck_messages')->where($whereMsg)->field('content')->find();

                $returnArr['status'] = 'success';
                $returnArr['freight_data'] = $result;
                $returnArr['content'] = $resultMsg['content'];
            } else {
                $returnArr['status'] = 'no-freight';
            }
        } else {
            $returnArr['status'] = 'no-id';
        }
        echo json_encode($returnArr);
    }

    /**
     * 删除非法运费
     */
    public function freightDelete()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');
        if ($subInfo['id']) {
            $data['id'] = $subInfo['id'];
            $data['invalid_id'] = 2;
            $result = M('ck_freight')->save($data);
            if ($result) {
                $returnArr['status'] = 'success';
            } else {
                $returnArr['status'] = 'failure';
            }
        } else {
            $returnArr['status'] = 'no-id';
        }
        echo json_encode($returnArr);
    }

    /**
     * 地址检测函数
     */
    public function area_check()
    {
        $area_name = I('post.area_name', '', 'trim');
        if ($area_name == '天津') {
            $area_name = '天津市';
        }
        $where['name|short_name'] = $area_name;
        $result = M('ck_districts')->where($where)->find();
        if ($result) {
            echo json_encode($result);
            exit;
        } else {
            echo 0;
            exit;
        }
    }

    public function freightForecast($start_id,$end_id,$distance,$level_type = 3){
        $forecast = null;
        //起止地所属组数
        $whereFit['area_start_id'] = array('like',substr($start_id,0,$level_type*2)."%");
        $whereFit['area_end_id'] = array('like',substr($end_id,0,$level_type*2)."%");
        $group_id = M('ck_fitting_setting')->where($whereFit)->field('group_id')->find();
        if($group_id){
            //取当前时间公式
            $whereFormula['group_id'] = $group_id;
            $resultFormula = M('ck_freight_fitting')->where($whereFormula)->order('record_time desc')->find();
            if($resultFormula){
                $forcast = $resultFormula['a']*$distance+$resultFormula['b'];
            }
        }
        return $forecast;
    }

    /**
     * 返回重新登录
     */
    public function reLog()
    {
        header("Content-Type:text/html; charset=utf-8");//解决乱码
        $this->redirect('StaffsLogin/Login', '', 3, "您并没有登录，正在返回登录...");
    }

}