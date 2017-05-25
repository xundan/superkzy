<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:32
 */

namespace Home\Common\CardList;


use Home\Model\MessagesModel;

class MsgCard extends Card
{
    protected $_message = null;

    function __construct($message)
    {
        $this->_message = $message;
    }



    function getTitle()
    {
        return $this->_message['title'];
    }

    function hashCode()
    {

    }

    function equals(Card $card)
    {

    }

    // 基本已经属于抽象方法
    function toLi()
    {
        $li_str = "";
        $message = $this->_message;
        if ($message['type'] == 'plain'||$message['type'] == 'wx_mp'||$message['type'] == 'group') { // R
            $li_str = "<li>" . $message['content'] . "</li>";
        } else {
            if ($message['category'] == '供应') { // 供应
            } elseif ($message['category'] == '车源') { // 司机找活
            } elseif ($this->_message['category'] == '求购') { // 求购
            } elseif ($this->_message['category'] == '找车') { // 找车
            } else {
                // todo log here.
            }
        }
        return $this->replacePublicString($li_str);

    }

    protected function replacePublicString($str)
    {
        $real_public_str = __ROOT__ . '/Public';
        return str_replace("__PUBLIC__", $real_public_str, $str);
    }

    /**
     * 需要先在buildUpMessage()里toUser()
     * @return string
     */
    protected function getPersonalUrl()
    {
        $role_id = $this->_message['user']['role_id'];
        $personal_page = "";
        if ($role_id == 1) {
            $personal_page = U('PersonalCenter/driver_data', array('uid' => $this->_message['publisher_rid']));
        } elseif ($role_id == 2) {
            $personal_page = U('PersonalCenter/owner_data', array('uid' => $this->_message['publisher_rid']));
        } else {
        }
        return $personal_page;
    }

    protected function getOrderDetailUrl(){
        // 返回一个静态文件
        return __ROOT__.'/Public/home/OrderFromWX.html';
    }

    protected function buildUpMessage(){
        $Msg = new MessagesModel();
        $this->_message = $Msg->toAll($this->_message, session("user_info")['uid']);
        return $this->_message;
    }

    protected function buildCollection(){
        $Msg = new MessagesModel();
        $this->_message = $Msg->toCollection($this->_message, session("user_info")['uid']);
        return $this->_message;
    }

    protected function getImgName(){
        $type = $this->_message['type'];
        $data['img']="none.png";
        $data['name']="未知来源";
        if ($type=="plain"){
            $data['img']="from_wx.png";
            $data['name']="来自微信";
        }elseif($type=="group"){
            $data['img']="cjkzy_icon.png";
            $data['name']="其他来源";
        }else{}
        return $data;
    }

}