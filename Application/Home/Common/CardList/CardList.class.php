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
    const ACCURATE = 0;
    const SIMILAR = 1;
    const OTHER = 2;
    const END = 3;
    protected $_stage = self::ACCURATE; //0:精确 1:模糊 2:其他 3:最后

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
                if ($message['category'] == '供应') { // 供应
                    $msgCard = new WxCoalSellMsgCard($message);
                } elseif ($message['category'] == '其他') { // 司机找活
                    $msgCard = new WxCarGiveMsgCard($message);

                } elseif ($message['category'] == '求购') { // 求购
                    $msgCard = new WxCoalBuyMsgCard($message);

                } elseif ($message['category'] == '找车') { // 找车
                    $msgCard = new WxCarNeedMsgCard($message);

                } else {
                    $msgCard = new MsgCard($message);
                }
            } elseif ($message['type'] == 'web') { // 是网站来源
                if ($message['category'] == '供应') { // 供应
                    $msgCard = new CoalSellMsgCard($message);
                } elseif ($message['category'] == '其他') { // 司机找活
                    $msgCard = new CarGiveMsgCard($message);

                } elseif ($message['category'] == '求购') { // 求购
                    $msgCard = new CoalBuyMsgCard($message);

                } elseif ($message['category'] == '找车') { // 找车
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

    /**
     * @param $messages mixed 需要合并的信息
     */
    public function appendMessage($messages){
        $this->_array = array_merge($this->_array, $this->createMsgCardArray($messages));
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
    /**
     * 添加一个模糊标识
     */
    public function addSimilar(){
        $tip['type'] = "tips";
        $tip['title'] = "模糊结果";
        $tip['content'] = "没有更多的精确消息，现在为您显示近似的消息。";
        array_push($this->_array, new TipCard($tip));
        $this->_stage = self::SIMILAR;
    }
    /**
     * 添加一个其他标识
     */
    public function addOther(){
        $tip['type'] = "tips";
        $tip['title'] = "其他结果";
        $tip['content'] = "没有更多的近似消息，下面为你展示其他推荐消息。";
        array_push($this->_array, new TipCard($tip));
        $this->_stage = self::OTHER;
    }
    /**
     * 添加一个结尾标识
     */
    public function addEnd(){
        $tip['type'] = "tips";
        $tip['title'] = "结束";
        $tip['content'] = "没有更多的信息了。";
        array_push($this->_array, new TipCard($tip));
        $this->_stage = self::END;
    }


    /**
     * @return int
     */
    public function getStage()
    {
        return $this->_stage;
    }

    /**
     * @param int $stage
     */
    public function setStage($stage)
    {
        $this->_stage = $stage;
    }

    /**
     * @return bool 返回现在CardList的状态是不是精确查询
     */
    public function atAccurate(){
        if ($this->_stage == self::ACCURATE){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool 返回现在CardList的状态是不是模糊查询
     */
    public function atSimilar(){
        if ($this->_stage == self::SIMILAR){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @return bool 返回现在CardList的状态是不是其他查询
     */
    public function atOther(){
        if ($this->_stage == self::OTHER){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @return bool 返回现在CardList的状态是不是结束
     */
    public function atEnd(){
        if ($this->_stage == self::END){
            return true;
        }else{
            return false;
        }
    }

    public function getCount(){
        return count($this->_array);
    }

    public function notFull(){
        return count($this->_array)<C('DEFAULT_ROW');
    }

    public function isFull(){
        return count($this->_array)>=C('DEFAULT_ROW');
    }
}