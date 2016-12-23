<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:19
 */
namespace Home\Common\CardList;

class CardList
{

    protected $_array = null;

    function __construct($messages)
    {
        $this->_array = $this->createMsgCardArray($messages);
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
            if ($message['type'] == 'plain') { // 是微信来源
                $msgCard = new MsgCard($message);
            } elseif ($message['type'] == '') { // 是网站来源
                if ($message['category'] == 0) { // 供应
                    $msgCard = new CoalSellMsgCard($message);
                } elseif ($message['category'] == 1) { // 司机找活
                    $msgCard = new CarGiveMsgCard($message);

                } elseif ($message['category'] == 2) { // 求购
                    $msgCard = new CoalBuyMsgCard($message);

                } elseif ($message['category'] == 3) { // 找车
                    $msgCard = new CarNeedMsgCard($message);

                } else {
                    $msgCard = new MsgCard($message);
                }
            } elseif ($message['type'] == 'tips') {
                $msgCard = new TipCard($message);
            }
            array_push($resultArray, $msgCard);
        }
        return $resultArray;
    }

    public function getObjectArray(){
        return $this->_array;
    }

    public function toLiArray(){
        $li_str_array = array();
        foreach($this->_array as $obj){
            if ($obj instanceof Card){
                $li_str = $obj->toLi();
                array_push($li_str_array,$li_str);
            }
        }
        return $li_str_array;
    }

    public function toJSON()
    {
        return "";
    }
}