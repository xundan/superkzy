<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/9/1
 * Time: 15:18
 */

namespace Home\Controller;

use Think\Controller;
use Home\Model\MessagesModel;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessageHistoryModel;

class MessageShareController extends Controller
{
    const COUNT_ROW = 10;

    public function index()
    {
        echo 'Hello!';
    }

    public function supplyAll($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('supplyAll', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function supplyAllMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('supplyAll', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function supplyDlm($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('supplyDlm', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function supplyDlmStatic()
    {
        echo __ROOT__;
//        echo __METHOD__;
        echo '<br/>';
        $this->buildHtml('supplyDlm.html', './', 'MessageShare/supplyDlm');
        echo 2;
    }

    public function supplyDlmMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('supplyDlm', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function buy($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('buy', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function buyMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('buy', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function searchCar($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('searchCar', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function searchCarMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('searchCar', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function carSource($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('carSource', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function carSourceMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('carSource', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function vipYear($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipYear', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function vipYearMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipYear', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function vipSeason($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipSeason', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function vipSeasonMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipSeason', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function vipMonth($date = null)
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipMonth', $date);
        //下拉刷新
        if ($subInfo && $subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            echo json_encode($result);
            return;
        } else {
            //AssembleData
            $result = $msg->findWhere($where);
            $this->assign('resultMsg', $result);
        }
        $this->display();
    }

    public function vipMonthMore()
    {
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipMonth', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function allMore(){
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('all', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function carMore(){
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('car', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    public function vipAllMore(){
        //获取Ajax数据
        $subInfo = I('get.', '', 'trim,strip_tags');
        //确定消息模型
        $date = $subInfo['date'];
        $msg = $this->msgModelCreator($date);
        //设置条件
        $where = $this->setWhere('vipAll', $subInfo['date'], $subInfo['page']);
        if ($subInfo['isAjax'] == 1) {
            //AssembleData
            $result['data'] = $msg->findWhere($where);
            if (count($result['data']) < self::COUNT_ROW) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        }
    }

    private function msgModelCreator($date)
    {
        if ($date) {
            if ($date < date("Y-m-d", time())) {
                $msg = new MessageHistoryModel();
            } else {
                $msg = new MessagesModel();
            }
        } else {
            $msg = new MessagesModel();
        }
        return $msg;
    }

    private function setWhere($category, $date = null, $page = 1)
    {
        if ($category) {
            $where = new WhereConditions();
            $where->pushCond('status', 'EQ', 102);
            $where->pushCond('invalid_id', 'EQ', 0);
            if ($date) {
                $where->pushCond('update_time', 'between', array($date, date('Y-m-d', strtotime($date) + 24 * 3600)));
            } else {
                $where->pushCond('update_time', 'GT', date("Y-m-d", time()));
            }
            $where->setPage($page);
            switch ($category) {
                case 'supplyAll':
                    $where->pushCond('category', 'EQ', '供应');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'supplyDlm':
//                    $where->pushCond('kind', 'EQ', '动力煤');
                    $where->pushCond('category', 'EQ', '供应');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'buy':
                    $where->pushCond('category', 'EQ', '求购');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'searchCar':
                    $where->pushCond('category', 'EQ', '找车');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'carSource':
                    $where->pushCond('category', 'EQ', '车源');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'vipYear':
                    $where->pushCond('vip', 'in', array('8', '7'));
                    $where->pushCond('type', 'in', array('plain', 'wx_mp'));
                    break;
                case 'vipSeason':
                    $where->pushCond('vip', 'EQ', '6');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp'));
                    break;
                case 'vipMonth':
                    $where->pushCond('vip', 'EQ', '5');
                    $where->pushCond('type', 'in', array('plain', 'wx_mp'));
                    break;
                case 'all':
                    break;
                case 'car':
                    $where->pushCond('category', 'in', array('车源', '找车'));
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                case 'vipAll':
                    $where->pushCond('vip', 'in', array('8', '7', '6', '5'));
                    $where->pushCond('type', 'in', array('plain', 'wx_mp', 'group'));
                    break;
                default:
                    return null;
            }
            return $where;
        } else {
            return null;
        }
    }

    public function vipSet()
    {
        echo '----start----<br/>';
        $whereClient['invalid_id'] = 0;
        $payingClient = M('paying_client')->where($whereClient)->select();
        $vipYear = array();
        $vipHalfYear = array();
        $vipSeason = array();
        $vipMonth = array();
        if ($payingClient) {
            foreach ($payingClient as $item) {
                switch ($item['pay_type']) {
                    case '年费':
                        array_push($vipYear, $item);
                        break;
                    case '半年费':
                        array_push($vipHalfYear, $item);
                        break;
                    case '季费':
                        array_push($vipSeason, $item);
                        break;
                    case '月费':
                        array_push($vipMonth, $item);
                        break;
                    default:
                        break;
                }
            }
            $whereRecommendMsg = array();
            $whereRecommendMsg['status'] = 102;
            $whereRecommendMsg['invalid_id'] = 0;
            $whereRecommendMsg['type'] = array('in', array('plain', 'wx_mp'));
            $whereRecommendMsg['record_time'] = array('GT', date("Y-m-d"));

            //先把所有信息VIP设为3
            $data['vip'] = 3;
            $resultModify = M('messages')->where($whereRecommendMsg)->save($data);
            echo "重置条数：" . $resultModify . "<br/>";

            if ($vipYear) {
                $vipNumber = array();
                foreach ($vipYear as $temp) {
                    array_push($vipNumber, $temp['phone_number']);
                }
                echo '年费客户号码：<br/>';
                foreach ($vipNumber as $item) {
                    echo $item . "<br/>";
                }
                $whereRecommendMsg['phone_number'] = array('in', $vipNumber);
                $data['vip'] = 8;
                $resultYear = M('messages')->where($whereRecommendMsg)->save($data);
                if ($resultYear === false) {
                    echo '----Year error----<br/>';
                } else {
                    echo '年费修改条数：' . $resultYear . "<br/>";
                }
            } else {
                echo '----vipYear null----<br/>';
            }

            if ($vipHalfYear) {
                $vipNumber = array();
                foreach ($vipHalfYear as $temp) {
                    array_push($vipNumber, $temp['phone_number']);
                }
                echo '半年费客户号码：<br/>';
                foreach ($vipNumber as $item) {
                    echo $item . "<br/>";
                }
                $whereRecommendMsg['phone_number'] = array('in', $vipNumber);
                $data['vip'] = 7;
                $resultHalfYear = M('messages')->where($whereRecommendMsg)->save($data);
                if ($resultHalfYear === false) {
                    echo '----HalfYear error----<br/>';
                } else {
                    echo '半年费修改条数：' . $resultHalfYear . "<br/>";
                }
            } else {
                echo '----vipHalfYear null----<br/>';
            }

            if ($vipSeason) {
                $vipNumber = array();
                foreach ($vipSeason as $temp) {
                    array_push($vipNumber, $temp['phone_number']);
                }
                echo '季费客户号码：<br/>';
                foreach ($vipNumber as $item) {
                    echo $item . "<br/>";
                }
                $whereRecommendMsg['phone_number'] = array('in', $vipNumber);
                $data['vip'] = 6;
                $resultSeason = M('messages')->where($whereRecommendMsg)->save($data);
                if ($resultSeason === false) {
                    echo '----Season error----<br/>';
                } else {
                    echo '季费修改条数：' . $resultSeason . "<br/>";
                }
            } else {
                echo '----vipSeason null----<br/>';
            }

            if ($vipMonth) {
                $vipNumber = array();
                foreach ($vipMonth as $temp) {
                    array_push($vipNumber, $temp['phone_number']);
                }
                echo '月费客户号码：<br/>';
                foreach ($vipNumber as $item) {
                    echo $item . "<br/>";
                }
                $whereRecommendMsg['phone_number'] = array('in', $vipNumber);
                $data['vip'] = 5;
                $resultMonth = M('messages')->where($whereRecommendMsg)->save($data);
                if ($resultMonth === false) {
                    echo '----Month error----<br/>';
                } else {
                    echo '月费修改条数：' . $resultMonth . "<br/>";
                }
            } else {
                echo '----vipMonth null----<br/>';
            }
        } else {
            echo '----payingClient error----<br/>';
        }

        echo '----end----<br/>';
    }


}