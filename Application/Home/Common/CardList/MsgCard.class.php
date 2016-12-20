<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:32
 */

namespace Home\Common\CardList;


class MsgCard extends Card
{
    protected $_message = null;

    function __construct($message)
    {
        $this->_message = $message;
    }

    /**
     * 针对一个message数组，拼装并返回一个对应的MsgCard数组
     * @param $messages
     * @return array
     */
    function createMsgCardArray($messages)
    {
        $resultArray = array();
        foreach ($messages as $message) {
            $msgCard = new MsgCard($message);
            array_push($resultArray, $msgCard);
        }
        return $resultArray;
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

    function toLi()
    {
        $li_str = "";
        $message = $this->_message;
        if ($message['type'] == 'plain') { // R
            $li_str = "<li>" . $message['content'] . "</li>";
        } else {
            if ($message['category'] == 0) { // 供应
                $li_str = "<li>" . $this->_message['content'] . "</li>";
                $li_str = "";
            } elseif ($message['category'] == 1) { // 司机找活
                $li_str = "<li>" . $this->_message['content'] . "</li>";
            } elseif ($this->_message['category'] == 2) { // 求购
                $li_str = "";
            } elseif ($this->_message['category'] == 3) { // 找车
                $li_str = "<li>" . $this->_message['content'] . "</li>";
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

    protected function getPersonalUrl()
    {
        $role_id = $this->_message['role_id'];
        $personal_page = "";
        if ($role_id == 1) {
            $personal_page = U('PersonalCenter/driver_data', array('uid' => $this->_message['uid']));
        } elseif ($role_id == 2) {
            $personal_page = U('PersonalCenter/owner_data', array('uid' => $this->_message['uid']));
        } else {
        }
        return $personal_page;
    }

    protected function getOrderDetailUrl(){
        // 返回一个静态文件
        return __ROOT__.'/Public/home/OrderFromWX.html';
    }
}