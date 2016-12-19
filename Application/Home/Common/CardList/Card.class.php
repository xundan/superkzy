<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:26
 */

namespace Home\Common\CardList;


abstract class Card
{
    abstract function getTitle();
    abstract function toLi();
}