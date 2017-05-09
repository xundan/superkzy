<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/1
 * Time: 9:13
 */

namespace Home\Controller;

use Think\Controller;
use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessagesModel;

header("Content-type: text/html; charset=utf-8");

class OwnerOrderController extends ComController
{
    public function owner_order()
    {

        $data = $this->getOrderByPage(1, "trade");
        $this->assign("trade_li_array", $data["li_array"]);
        $data = $this->getOrderByPage(1, "transport");
        $this->assign("transport_li_array", $data["li_array"]);
        $this->assign("EOA", $data["EOA"]);
        $this->display();
    }

    public function owner_order_more()
    {
        $page = I('post.page', '', 'trim,strip_tags');
        $category = I('post.select_category', '', 'trim,strip_tags');
        $data = $this->getOrderByPage($page, $category);
        echo json_encode($data);
        return;
    }
    //车源信息
    public function owner_order_transport_detail($id = null,$from=null)
    {
        $this->assign_data($id);
        $this->display();
    }
    //供货信息
    public function owner_order_trade_detail($id = null,$from=null)
    {
        $this->assign_data($id);
        $this->display();
    }
    //求购信息
    public function owner_order_trade_buy_detail($id = null,$from=null)
    {
        $this->assign_data($id);
        $this->display();
    }
    //求车信息
    public function driver_order_detail($id = null,$from=null)
    {
        $this->assign_data($id);
        $this->display();
    }

    public function wx_order_detail($id = null,$from=null){
        $this->assign_data($id);
//        $a = M('messages')->where(array('id'=>$id))->find();
//        dump($a);
        $this->display();
    }
    /**
     * 缓存数据
     * @param $id
     */
    private function assign_data($id)
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $uid = session('user_info')['uid'];
        if ($id) {
            // 如果路径里有uid
            $temp['id'] = $id;
            $MSG = new MessagesModel();
            $message = $MSG->where($temp)->find();
            $message = $MSG->toCollection($message, $uid);
            if ($message) {
                $this->assign('message', $message);
                $this->assign_user($message);
                $this->assign_area($message);
                $this->assign_product($message);
            } elseif ($message === false) {
                // TODO false说明查询出错，记录日志
                $this->display("Feedback:500");exit;

            } else {
                // TODO 查询为空，用户查到了不该到的地方，记日志
                $this->display("Feedback:404");exit;
            }
        } else {
            // TODO 查询为空，用户查到了不该到的地方，记日志
            $this->display("Feedback:404");exit;
        }
    }

    /**
     * 判断并缓存用户信息
     * @param $message
     */
    private function assign_user($message)
    {
        if ($message['publisher_rid']) {
            $user = M('User')->where(array("uid" => $message['publisher_rid']))->find();
            $this->assign('user', $user);
        } elseif ($message['sender']) {
            // 用户来自微信，展示一个静态页面+广告，显示手机号码
//            $this->display("user_from_wx");
        }
    }

    /**
     * 判断并缓存地址信息
     * @param $message
     */
    private function assign_area($message)
    {
        if ($message['area_start']) {
            $area_start = M('Districts')->where(array("id" => $message['area_start']))->find();
            $this->assign('area_start', $area_start);
        }
        if ($message['area_end']) {
            $area_end = M('Districts')->where(array("id" => $message['area_end']))->find();
            $this->assign('area_end', $area_end);
        }
    }

    /**
     * 判断并缓存产品信息
     * @param $message
     */
    private function assign_product($message)
    {
        if ($message['product_id']) {
            $product = M('Product')->where(array('id' => $message['product_id']))->find();
            if ($product) {
                if ($product['kind_id']) {
                    $coal_kind = M('coal_kind')->where(array('id' => $product['kind_id']))->find();
                    $this->assign('coal_kind_title', $coal_kind['title']);
                }
                if ($product['place_origin_id']) {
                    $place_origin = M('Districts')->where(array("id" => $product['place_origin_id']))->find();
                    $this->assign('place_origin_name', $place_origin['name']);
                }
                $this->assign('product', $product);
            }
        }
    }

    /**
     * 根据页数去数据库取得相应的消息数组
     * @param $page int 页数
     * @param $category string "trade","transport"
     * @return mixed 信息数组
     */
    private function getMessagesByPage($page, $category)
    {
        $Msg = new MessagesModel();
        $uid = session('user_info')['uid'];
//        dump($uid);
        $whereCond = new WhereConditions();
        $whereCond->setPage($page);
        $arr = D('collection')->getCollectionById($uid);
        array_push($arr,-1); // 防止in后的数组为空报错
        $whereCond->pushCond("id", "in", $arr);
        if ($category == "trade") {
            $whereCond->pushCond("category", "in", array("求购", "供应"));
        } else if($category == "transport") {
            $whereCond->pushCond("category", "in", array("找车", "车源"));
        } else {}
        $messages = $Msg->findWhere($whereCond);
        return $messages;
    }

    /**
     * 获取返回数据
     * @param $page int 页数
     * @param $category string "trade","transport"
     * @return mixed 返回数据
     */
    private function getOrderByPage($page, $category)
    {
        $data["msg"] = "success";
        $messages = $this->getMessagesByPage($page, $category);

        $counts = count($messages);
        $cards = new CardList($messages);
        if ($counts < C('DEFAULT_ROW')) {
            $data["EOA"] = $counts;
            $cards->addEnd();
        } else {
            $data["EOA"] = -1;
        }
        $data["li_array"] = $cards->toLiArray();
        $data["category"] = $category;
        return $data;
    }

    /**
     * 手动删除message
     */
    public function del_message(){
        $Message = new MessagesModel();
        $post = I('post.', '', 'trim,strip_tags');
        $msg_id = $post['id'];
        $data = $Message->updateMessageState($msg_id,2);
        echo json_encode($data);
        return;
    }
}