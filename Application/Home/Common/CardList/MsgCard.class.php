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

    function getTitle()
    {
        return $this->_message['title'];
    }

    function toLi()
    {
        // TODO: Implement toLi() method.
        return "<li>".$this->_message['title']."</li>";
    }


}