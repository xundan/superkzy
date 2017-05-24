<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/28
 * Time: 12:05
 */

namespace Home\Controller;

use Think\Controller;

class CoalPriceSearchController extends Controller
{
    const countRow = 10;
    private $_asc = "vip desc,update_time desc";

    /**
     * 第一次进入渲染页面
     */
    public function coal_price_search()
    {
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
            $cm = D('Views/CoalPriceMessage')->where($where)->limit($start, $count)->order($this->_asc)->select();
        } else {
            $cm = D('Views/CoalPriceMessage')->where('invalid_id = 0')->limit($start, $count)->order($this->_asc)->select();
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


}