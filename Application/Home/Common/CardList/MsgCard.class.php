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
    function createMsgCardArray($messages){
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

    function toLi()
    {
        // TODO: Implement toLi() method.
        return "<li>".$this->_message['title']."</li>";
    }

    function toSimple(){

    }

}