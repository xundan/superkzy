<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/5/11
 * Time: 23:03
 */

namespace Home\Controller;

use Think\Controller;

class ActivityPromotionController extends ComController
{
    const CHANCE = 1;
    const CHANCE_FUNC_SWITCH = false;
    const ACTIVITY_FLAG = false;

    /**
     * 奖项数组
     * id奖品id
     * prize 奖品名
     * v 中奖权重
     * v总和越大越平衡
     */
    private $_prize_arr = array(
        '0' => array('id' => 1, 'prize' => 'iPhone7Plus中国红', 'v' => 0),
        '1' => array('id' => 2, 'prize' => '黄金VIP1天', 'v' => 86),
        '2' => array('id' => 3, 'prize' => '20元手机话费', 'v' => 2),
        '3' => array('id' => 4, 'prize' => '黄金VIP7天', 'v' => 10),
        '4' => array('id' => 5, 'prize' => 'iPhone7Plus中国红', 'v' => 0),
        '5' => array('id' => 6, 'prize' => '20元手机话费', 'v' => 2),
    );

//    private $prize_arr = array(
//        '0' => array('id'=>1,'prize'=>'iPhone7Plus中国红','v'=>100),
//        '1' => array('id'=>2,'prize'=>'黄金VIP1天','v'=>0),
//        '2' => array('id'=>3,'prize'=>'20元手机话费','v'=>0),
//        '3' => array('id'=>4,'prize'=>'黄金VIP7天','v'=>0),
//        '4' => array('id'=>5,'prize'=>'iPhone7Plus中国红','v'=>0),
//        '5' => array('id'=>6,'prize'=>'20元手机话费','v'=>0),
//    );

    public function turn_plate_lottery()
    {
        if (self::ACTIVITY_FLAG) {
            $user = session('user_info');
            if (self::CHANCE_FUNC_SWITCH) {
                //用户抽奖次数
                $detect = $this->duplication_detect();
                if ($detect === true) {
                    //用户第一次发信息
                    //设置抽奖次数
                    $user['chance'] = self::CHANCE;
                } elseif ($detect) {
                    $user['chance'] = $detect['count'];
                } else {
                    //数据库错误
                }
            }
            $this->assign('prize_arr', json_encode($this->_prize_arr));
            $this->assign('user', $user);
            $this->display();
        } else {
            $this->display('over');
        }

    }

    /**
     * 箱子取球中奖概率算法
     * @param $proArr
     * @return int|string
     */
    private function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }


    private function set_prize_arr($prize)
    {
        $this->_prize_arr = $prize;
    }

    private function get_prize_arr()
    {
        return $this->_prize_arr;
    }

    /**
     * 每次前端页面的请求，PHP循环奖项设置数组，
     * 通过概率计算函数get_rand获取抽中的奖项id。
     * 将中奖奖品保存在数组$res['win']中，
     * 而剩下的未中奖的信息保存在$res['lose']中，
     */
    public function get_prize_item()
    {
        $detect = $this->duplication_detect();
        if ($detect === true) {
            //用户第一次抽奖
            $prize_arr = $this->_prize_arr;
            $arr = array();
            foreach ($prize_arr as $key => $val) {
                $arr[$val['id']] = $val['v'];
            }
            $rid = $this->get_rand($arr); //根据概率获取奖项id
            session('prize_id', $rid);

            $res['win'] = $prize_arr[$rid - 1]['prize']; //中奖项
            unset($prize_arr[$rid - 1]); //将中奖项从数组中剔除，剩下未中奖项
            shuffle($prize_arr); //打乱数组顺序
            $pr = array();
            for ($i = 0; $i < count($prize_arr); $i++) {
                $pr[] = $prize_arr[$i]['prize'];
            }
            $res['lose'] = $pr;
            //中奖信息存入数据库
            $data['uid'] = $_SESSION['user_info']['uid'];
            $data['prize_id'] = $rid;
            $data['prize_name'] = $this->_prize_arr[$rid - 1]['prize'];
            $result = M('activity_2017_05')->data($data)->add();
            if ($result) {
                //输出中奖项
                echo json_encode($rid);
                return;
            } else {
                //数据库错误
            }

        } else if ($detect === false) {
            //数据库错误
        } else {
            //用户重复抽奖
            if (self::CHANCE_FUNC_SWITCH) {
                //抽奖次数-1
                $data['count'] = $detect['count'] - 1;
                $where['id'] = $detect['id'];
                $temp = M('acivity_2017_05')->where($where)->save($data);
                if ($temp) {
                    $returnArr['status'] = 2;
                    $returnArr['msg'] = "用去一次抽奖次数";
                    echo json_encode($returnArr);
                    return;
                } else {
                }
            } else {
                $returnArr['status'] = 1;
                $returnArr['msg'] = "重复抽奖";
                echo json_encode($returnArr);
                return;
            }
        }
    }

    public function feedback_action()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');
        $data['uid'] = $_SESSION['user_info']['uid'];
        $data['prize_id'] = session('prize_id');
        $data['prize_name'] = $this->prize_arr[session('prize_id') - 1]['prize'];
        $data['contact'] = $subInfo['contact'];
        $where['uid'] = $_SESSION['user_info']['uid'];
//        if(empty($subInfo))
//            return;
        $fres = M('activity_2017_05')->where($where)->save($data);
        if ($fres) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "提交成功";
            echo json_encode($returnArr);
        } else {
            $returnArr['status'] = 0;
            $returnArr['msg'] = "提交失败";
            M("product")->delete($fres);
            echo json_encode($returnArr);
            return;
        }
    }

    private function duplication_detect()
    {
        //判断重复
        $where = array();
        $where['uid'] = session('user_info')['uid'];
        $temp = M('activity_2017_05')->where($where)->find();
        if ($temp) {
            //重复信息
            return $temp;
        } else if ($temp === false) {
            return false;
        } else {
            return true;
        }
    }

}

