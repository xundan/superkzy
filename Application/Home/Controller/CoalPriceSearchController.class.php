<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/28
 * Time: 12:05
 */

namespace Home\Controller;

use Think\Controller;

class CoalPriceSearchController extends ComController
{
    const countRow = 10;
    private $_asc = "vip desc,update_time desc";

    /**
     * 第一次进入渲染页面
     */
    public function coal_price_search()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $subInfo = I('post.', '', 'trim,strip_tags');
        //下拉刷新
        if ($subInfo['isAjax'] == 1) {
            if ($subInfo['select_category'] == 'all') {
                $result['data'] = $this->getOrder();
                echo json_encode($result);
                return;
            } elseif ($subInfo['select_category'] == 'search') {
                $where['refinery_name'] = $subInfo['refinery_name'];
                $where['invalid_id'] = 0;
                $result['data'] = $this->getOrder($where);
                if (count($result['data']) < self::countRow) {
                    $result['end_tag'] = 'end';
                } else {
                    $result['end_tag'] = 'continue';
                }
                echo json_encode($result);
                return;
            } else {
            }
        } else {
            //查询界面提交或页面载入
            //查询界面提交
            if ($subInfo['select_category'] == 'search') {
                $where['refinery_name'] = $subInfo['refinery_name'];
                $result = $this->getOrder($where);
                $this->assign('message', $result);
            } else {
                //页面载入显示
                $result = $this->getOrder();
                $this->assign('message', $result);
            }
        }
        $this->display();
    }

    /**
     * 下拉加载更多
     */
    public function coal_price_search_more()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $start = ($subInfo['page'] - 1) * self::countRow;
        $where['invalid_id'] = 0;
        $result['page'] = $subInfo['page'];
        if ($subInfo['select_category'] == 'all') {
            $result['data'] = $this->getOrder($where, $start);
            if (count($result['data']) < self::countRow) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        } elseif ($subInfo['select_category'] == 'search') {
            $where['refinery_name'] = $subInfo['refinery_name'];
            $result['data'] = $this->getOrder($where, $start);
            if (count($result['data']) < self::countRow) {
                $result['end_tag'] = 'end';
            } else {
                $result['end_tag'] = 'continue';
            }
            echo json_encode($result);
            return;
        } else {
        }
    }

    /**
     * 煤矿名搜索页面
     */
    public function refinery_search()
    {
        $this->display();
    }

    /**
     * ajax根据地区获取煤矿名
     */
    public function ajax_refinery_search()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['refinery_name'] = array('like', '%' . $subInfo['refinery_name'] . '%');
        $where['invalid_id'] = 0;
        $result = D('CoalPriceMessage')->where($where)->limit(5)->group('refinery_name')->select();
        echo json_encode($result);
        return;
    }

    /**
     * 煤矿地址搜素界面搜索地址
     */
    public function area_search()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        if ($subInfo['area_name'] == '全部') {
            $where['invalid_id'] = 0;
        } elseif ($subInfo['area_name'] == '其他') {
            $where['area_name'] = array('not in', ['鄂尔多斯', '山西', '神木', '榆阳', '府谷', '横山']);
            $where['invalid_id'] = 0;
        } else {
            $where['area_name'] = $subInfo['area_name'];
            $where['invalid_id'] = 0;
        }

        $temp = D('Views/CoalPriceMessage')->where($where)->group('refinery_name')->field('refinery_name')->select();
        $result['refinery_name'] = array();
        foreach ($temp as $val) {
            array_push($result['refinery_name'], $val['refinery_name']);
        }
        utf8_array_sort($result['refinery_name']);
        $result['count'] = count($result['refinery_name']);
        echo json_encode($result);
    }

    /**
     * @param $message_id
     * 煤炭详情界面
     */
    public function coal_price_detail($message_id)
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $where['message_id'] = $message_id;
        $result = $this->getOrder($where, 0, 1);
        $this->assign('message', $result);
        $this->display();
    }

    /**
     * @param null $where 查询条件数组
     * @param int $start 开始位置
     * @param int $count 一次取的数量
     * @return mixed
     */
    private function getOrder($where = null, $start = 0, $count = self::countRow)
    {
        if ($where) {
            if ($count == -1) {
                $cm = D('Views/CoalPriceMessage')->where($where)->order($this->_asc)->select();
            } else {
                $cm = D('Views/CoalPriceMessage')->where($where)->limit($start, $count)->order($this->_asc)->select();
            }
        } else {
            if ($count == -1) {
                $cm = D('Views/CoalPriceMessage')->where($where)->order($this->_asc)->select();
            } else {
                $cm = D('Views/CoalPriceMessage')->where('invalid_id = 0')->limit($start, $count)->order($this->_asc)->select();
            }
        }
        foreach ($cm as &$item) {
            $cc = D('Views/CoalPriceContent')->where(array('message_id' => $item['message_id']))->select();
            foreach ($cc as &$value) {
                $cdi = D('Views/CoalPriceDetailedIndex')->where(array('content_id' => $value['content_id']))->select();
                $value['detailed_index'] = $cdi;
            }
            $item['content'] = $cc;
        }
        return $cm;
    }

    /**
     * 按煤矿筛选的结果展示页面，展示用户多选的煤矿
     */
    public function coal_price_search_filter()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $subInfo = I('get.', '', 'trim,strip_tags');
        $where = array();
        if ($subInfo) {
            $where['refinery_name'] = array('in', $subInfo['refinery_name']);
        }
        G('begin');
        $result = $this->getOrder($where, 0, -1);
        G('end');
        $this->assign('message', $result);
        //已筛选的message_id传到前台用以继续筛选详细指标
        $message_ids = array();
        foreach ($result as $item) {
            array_push($message_ids, $item['message_id']);
        }
        $this->assign('message_ids', implode(',', $message_ids));

//        echo '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>';
//        echo G('begin', 'end') . 's';
//        echo '<br/>';
//        echo G('begin', 'end', 'm') . 'kb';
        $this->display();
    }

    /**
     * 用户选择已何种方式筛选
     */
    public function search_method()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    /**
     * 详细指标筛选页面
     */
    public function coal_filter($message_ids = null)
    {
        $this->assign('message_ids', $message_ids);
        $this->display();
    }

    /**
     * 展示详细指标筛选结果页面
     */
    public function filtered_result_show()
    {
        $subInfo = I('post.', '', 'trim');
        $result = $this->filtered_result($subInfo);
        $this->assign('message', $result);
        $this->display();
    }

    /**
     * @param null $input
     * @return array 查询结果
     */
    public function filtered_result($input = null)
    {
        $subInfo = I('post.', '', 'trim');
//        dump($subInfo);
        if (!$input) {
            $input = $subInfo;
        }
        //类别筛选
        $resultGranularity = array();
        $whereGranularity = array();
        if ($input['filter_granularity']) {
            $whereGranularity['kind_filter'] = array('in', $input['filter_granularity']);
        }
        $resultGranularity = M('coal_price_content')->field('content_id,message_id,kind_name,kind_filter')->where($whereGranularity)->select();
//        dump(M()->getLastSql());
//        dump($resultGranularity);
        $resultGranularityContent = array();
        foreach ($resultGranularity as $v) {
            array_push($resultGranularityContent, $v['content_id']);
        }
//        dump($resultGranularityContent);

        //热值筛选
        $resultHeatValue = array();
        $whereHeatValue = array();
        if ($input['filter_heat_min'] || $input['filter_heat_max']) {
            $whereHeatValue['index_name'] = '热量';
            $whereHeatValue['index_value'] = array();
            if ($input['filter_heat_min']) {
                array_push($whereHeatValue['index_value'], array('egt', $input['filter_heat_min']));
            }
            if ($input['filter_heat_max']) {
                array_push($whereHeatValue['index_value'], array('elt', $input['filter_heat_max']));
            }
            array_push($whereHeatValue['index_value'], array('gt', 0));
            array_push($whereHeatValue['index_value'], 'and');
        }
        $resultHeatValue = M('coal_price_detailed_index')->where($whereHeatValue)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultHeatValue);
        $resultHeatValueContent = array();
        foreach ($resultHeatValue as $v) {
            array_push($resultHeatValueContent, $v['content_id']);
        }
//        dump($resultHeatValueContent);

        //硫筛选
        $resultSulfur = array();
        $whereSulfur = array();
        if ($input['filter_sulfur']) {
            $whereSulfur['index_name'] = '硫';
            $whereSulfur['index_value'] = array();
            foreach ($input['filter_sulfur'] as $v) {
                if ($v == 1) {
                    array_push($whereSulfur['index_value'], array('between', array(0, 0.5)));
                } else if ($v == 2) {
                    array_push($whereSulfur['index_value'], array('between', array(0.5, 1.5)));
                } else if ($v == 3) {
                    array_push($whereSulfur['index_value'], array('between', array(1.5, 2)));
                } else if ($v == 4) {
                    array_push($whereSulfur['index_value'], array('gt', 2));
                }
            }
            array_push($whereSulfur['index_value'], 'or');
            $whereSulfur['index_value'] = array(array('neq', 0), $whereSulfur['index_value']);
        };
        $resultSulfur = M('coal_price_detailed_index')->field('content_id')->where($whereSulfur)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultSulfur);
        $resultSulfurContent = array();
        foreach ($resultSulfur as $v) {
            array_push($resultSulfurContent, $v['content_id']);
        }
//        dump($resultSulfurContent);

        //挥发筛选
        $resultVolatile = array();
        $whereVolatile = array();
        if ($input['filter_volatile']) {
            $whereVolatile['index_name'] = '挥发';
            $whereVolatile['index_value'] = array();
            foreach ($input['filter_volatile'] as $v) {
                if ($v == 1) {
                    array_push($whereVolatile['index_value'], array('between', array(0, 25)));
                } else if ($v == 2) {
                    array_push($whereVolatile['index_value'], array('between', array(25, 30)));
                } else if ($v == 3) {
//                    array_push($whereVolatile['index_value'], array('between', array(28, 33)));
                    array_push($whereVolatile['index_value'], array('gt', 30));
                } else if ($v == 4) {
//                    array_push($whereVolatile['index_value'], array('gt', 33));
                }
            }
            array_push($whereVolatile['index_value'], 'or');
            $whereVolatile['index_value'] = array(array('neq', 0), $whereVolatile['index_value']);
        };
        $resultVolatile = M('coal_price_detailed_index')->field('content_id')->where($whereVolatile)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultVolatile);
        $resultVolatileContent = array();
        foreach ($resultVolatile as $v) {
            array_push($resultVolatileContent, $v['content_id']);
        }
//        dump($resultVolatileContent);

        //取所有筛选条件的交集，找出类别content_id
        $resultContent = array_intersect($resultGranularityContent, $resultHeatValueContent, $resultSulfurContent, $resultVolatileContent);
//        dump($resultContent);

        //判断交集是否有值
        if ($resultContent) {
            //从类别表中选取所筛选出来的类别
            $whereContent['content_id'] = array('in', $resultContent);
            $result = M('coal_price_content')->where($whereContent)->select();

            //返回查询到的值
            $returnArray = array();
            //将该类别的详细指标插入，并统一以message_id作为键名分组
            foreach ($result as $k => $v) {
                $cdi = M('coal_price_detailed_index')->where(array('content_id' => $v['content_id']))->select();
                $v['detailed_index'] = $cdi;
                $returnArray[$v["message_id"]]['content'][] = $v;
            }
//        dump($returnArray);
            //将该键名为message_id的矿信息插入结果数组
            foreach ($returnArray as $k => $v) {
                $whereMsg['message_id'] = $k;
                $whereMsg['invalid_id'] = 0;
                $resultMsg = M('coal_price_message')->where($whereMsg)->find();
                if ($resultMsg) {
                    if ($input['message_ids']) {
                        //如果是筛选煤矿再筛选指标，则记录已筛选煤矿id并从中选择
                        $message_ids = explode(',', $input['message_ids']);
                        if (in_array($k, $message_ids)) {
                            $returnArray[$k]['msg'] = $resultMsg;
                        } else {
                            unset($returnArray[$k]);
                        }
                    } else {
                        $returnArray[$k]['msg'] = $resultMsg;
                    }
                } else {
                    unset($returnArray[$k]);
                }
            }
            //判断返回数组是否为空，为空则返回-1
            if (empty($returnArray)) {
                return -1;
            } else {
                return $returnArray;
            }
        } else {
            return -1;
        }

    }

    /**
     * 煤炭合作页面
     */
    public function coal_cooperate()
    {
        $this->display();
    }

    /**
     * 煤炭合作页面提交处理
     */
    public function coal_cooperate_record()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');
        $data['content'] = $subInfo['content'];
        $data['uid'] = session('user_info')['uid'];
        $result = M('coal_cooperate')->add($data);
        $returnArr = array();
        if($result){
            $returnArr['status'] = 1;
            $returnArr['msg'] = '插入成功';
        }else{
            $returnArr['status'] = 2;
            $returnArr['msg'] = '插入失败';
        }
        echo json_encode($returnArr);
    }

    /**
     * 煤价展示（所有煤矿）
     */
    public function coal_price_show(){
        $cm =  D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` ORDER BY CONVERT(`refinery_name` USING gbk)");
        foreach ($cm as &$item) {
            $cc = D('Views/CoalPriceContent')->where(array('message_id' => $item['message_id']))->select();
//            foreach ($cc as &$value) {
//                $cdi = D('Views/CoalPriceDetailedIndex')->where(array('content_id' => $value['content_id']))->select();
//                $value['detailed_index'] = $cdi;
//            }
            $item['content'] = $cc;
        }
//        dump($cm);
        $this->assign('message',$cm);
        $this->display();
    }

    public function coal_price_show_excel_eeds(){

    }




    public function testArea()
    {
//        $h1 = '1000';
//        $h2 = '20000';
//        $a = '6094';
//        dump($a >= $h1);
//        dump($a <= $h2);
//
//        dump(array('gt', 3));
        $a = [1, 2, 3, 4, 5];
        $b = '34';
        dump(in_array($b, $a));
        dump(explode(',', $b));
        unset($a[0]);
        dump($a);
        unset($a[1]);
        unset($a[2]);
        unset($a[3]);
        unset($a[4]);
        dump($a);
        dump(empty($a));
        dump(count($a));

        $subInfo = I('post.', '', 'trim');
        dump($subInfo['refinery_name']);
        $this->display();
    }

}