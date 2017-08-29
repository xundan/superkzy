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
    const SUCCESS_NEW = 1;
    const FAILURE = -1;
    const SUCCESS_USED = 2;
    const CHANCE = 1;
    const CHANCE_FUNC_SWITCH = false;
    const ACTIVITY_FLAG = true;

    /**
     * 奖项数组
     * id奖品id
     * prize 奖品名
     * v 中奖权重
     * v总和越大越平衡
     */
//    private $_prize_arr = array(
//        '0' => array('id' => 1, 'prize' => 'iPhone7Plus中国红', 'v' => 0),
//        '1' => array('id' => 2, 'prize' => '黄金VIP1天', 'v' => 86),
//        '2' => array('id' => 3, 'prize' => '20元手机话费', 'v' => 2),
//        '3' => array('id' => 4, 'prize' => '黄金VIP7天', 'v' => 10),
//        '4' => array('id' => 5, 'prize' => 'iPhone7Plus中国红', 'v' => 0),
//        '5' => array('id' => 6, 'prize' => '20元手机话费', 'v' => 2),
//    );

//    private $prize_arr = array(
//        '0' => array('id'=>1,'prize'=>'iPhone7Plus中国红','v'=>100),
//        '1' => array('id'=>2,'prize'=>'黄金VIP1天','v'=>0),
//        '2' => array('id'=>3,'prize'=>'20元手机话费','v'=>0),
//        '3' => array('id'=>4,'prize'=>'黄金VIP7天','v'=>0),
//        '4' => array('id'=>5,'prize'=>'iPhone7Plus中国红','v'=>0),
//        '5' => array('id'=>6,'prize'=>'20元手机话费','v'=>0),
//    );

    private $_prize_arr = array(
        '0' => array('id' => 1, 'prize' => '20元红包', 'v' => 100),
        '1' => array('id' => 2, 'prize' => '150元红包', 'v' => 1),
        '2' => array('id' => 3, 'prize' => '80元红包', 'v' => 8),
        '3' => array('id' => 4, 'prize' => '200元红包', 'v' => 0),
        '4' => array('id' => 5, 'prize' => '15元红包', 'v' => 880),
        '5' => array('id' => 6, 'prize' => '10元红包', 'v' => 0),
        '6' => array('id' => 7, 'prize' => '100元红包', 'v' => 1),
        '7' => array('id' => 8, 'prize' => '50元红包', 'v' => 10),
    );

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
        $data['prize_name'] = $this->_prize_arr[session('prize_id') - 1]['prize'];
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

    public function destiny()
    {
        $this->display();
    }

    /**
     * 付费客户抽奖页面
     */
    public function payingClientLottery()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    public function getPrize()
    {
        $prize_arr = $this->_prize_arr;
        $arr = array();
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['v'];
        }
        $rid = $this->get_rand($arr);
        //抽奖成功存入奖品表
        $user = session('user_info');
//        $where['open_id'] = $user['open_id'];
        $where['open_id'] = $user['open_id'];
        $resultUser = M('paying_client')->where($where)->find();
        //判断是否已经抽过再存
        $detect['phone_number'] = $resultUser['phone_number'];
        $resultDetect = M('activity_paying_client_2017_08')->where($detect)->find();
        if($resultDetect){
            echo self::FAILURE;
            exit;
        }
        $Prize['uid'] = $user['uid'];
        $Prize['paying_client_id'] = $resultUser['id'];
        $Prize['phone_number'] = $resultUser['phone_number'];
        $Prize['prize_id'] = $rid;
        $Prize['prize_name'] = $prize_arr[$rid - 1]['prize'];
        $Prize['count'] = 1;
        $resultPrize = M('activity_paying_client_2017_08')->add($Prize);
        if ($resultPrize) {
            $returnArray = array();
            $returnArray['prize_id'] = $rid;
            $returnArray['prize_name'] = $prize_arr[$rid - 1]['prize'];
            echo json_encode($returnArray);
        }
    }

    /**
     * 验证用户输入号码是否在已有名单中，伪登录
     */
    public function loginAction()
    {
        $user = session('user_info');
        $subInfo = I('post.', '', 'strip_tags,trim');
        $where['phone_number'] = $subInfo['phone_number'];
        if (!$subInfo['phone_number']) {
            echo self::FAILURE;
            exit;
        }
        //已付费但到期用户不能参与抽奖
        $whereInvalid['phone_number'] = $subInfo['phone_number'];
        $whereInvalid['invalid_id'] = 0;
        $result = M('paying_client')->where($whereInvalid)->order('phone_number asc')->find();
        if ($result) {
            //验证通过,检测结果是否有open_id
            if ($result['open_id']) {
                //用户信息在user表已经更新
                //判断该用户是否在奖品表里有数据
                $resultPrize = M('activity_paying_client_2017_08')->where($where)->find();
                if ($resultPrize) {
                    //有则说明验证通过的该用户已抽过奖
                    echo self::SUCCESS_USED;
                    exit;
                } else {
                    echo self::SUCCESS_NEW;
                    exit;
                }
            } else {
                //说明号码第一次被输入，保存更新用户表
                //号码第一次被输入不代表用户是第一次来，检测用户以前是否更新过用户表
                $userData['open_id'] = $user['open_id'];
                $userData['nickname'] = $user['nickname'];
                $resultUserOld = M('paying_client')->where($userData)->order('phone_number asc')->find();
                if ($resultUserOld) {
                    echo self::SUCCESS_USED;
                    exit;
                } else {
                    $resultUser = M('paying_client')->where($where)->save($userData);
                    if ($resultUser) {
                        //成功登录
                        echo self::SUCCESS_NEW;
                        exit;
                    } else {
                        //数据库错误
                        echo self::FAILURE;
                        exit;
                    }
                }
            }
        } else {
            //验证未通过，用户号码非付费客户
            echo self::FAILURE;
            exit;
        }
    }

    public function test()
    {
        $where['phone_number'] = '';
//        $where= array();
//        $userData['nickname'] = 'SB';
//        $a = M('paying_client')->where($where)->save($userData);
        $a = M('paying_client')->where($where)->select();
        dump(M()->getLastSql());
        if ($a) {
            dump(123123);
        } else {
            dump(555556);
        }
        dump($a);
        $b[1] = 2;
        $b[2] = 3;
        $b[3] = 4;
        dump(array_sum($b));
        dump($b);
    }

}

