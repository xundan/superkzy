<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:28
 */

namespace Home\Common\CardList;


class TipCard extends Card
{

    protected $_tip = "";

    function __construct($tip)
    {
        $this->_tip = $tip;
    }

    function getTitle()
    {
        return $this->_tip['title'];
    }

    function toLi()
    {
        return "<p style='color: red;font-size: 12px;text-align: center'>".$this->_tip['content']."</p>";
    }
}