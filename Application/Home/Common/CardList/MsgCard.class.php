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

    function hashCode(){

    }

    function equals(Card $card){

    }

    function toLi()
    {
        $li_str = "";
        $message = $this->_message;
        if ($message['type']=='plain'){
            $li_str = "<li>".$message['content']."</li>";
        }else{
            if($message['category']==0){ // 供应
                $li_str = "<li>".$this->_message['content']."</li>";
                $li_str = "";
            }elseif($message['category']==1){ // 司机找活
                $li_str = "<li>".$this->_message['content']."</li>";
            }elseif($this->_message['category']==2){ // 求购
                $li_str = "<li class=\"weui_panel weui_panel_access\" style=\";border-radius: 5px\">
    <div style=\"position: absolute;right: 0px;\">
        < img src=\"__PUBLIC__/home/images/buy.png\" width=\"50px\">
    </div>
    <div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
        < a href=\" \">
            <div class=\"weui_media_hd\">
                < img src=\"__PUBLIC__/home/images/headimg_background.png\" class=\"weui_media_appmsg_thumb\">
            </div>
        </ a>
        <div class=\"weui_media_bd\">
            < a href=\"#\">
                <h4 class=\"weui_media_title\">{名字} < img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
            </ a>
            <p class=\"weui_media_desc\">
                < img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 10px;height: 15px\">
                {}
            </p >
        </div>
        <div class=\"weui_media_bd text-center\" style=\"\">
            < a href=\"tel:15153151316\" class=\"\">
                <h4>
                    < img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                    <span style=\"\">拨打电话</span>
                </h4>
            </ a>
            <div>
                <button class=\"btn btn-xs btn-default\" style=\"padding-left: 20%;padding-right: 20%;border-color: #04bfc6;color: #04bfc6\">
                    收藏
                </button>
            </div>
        </div>
    </div>
    <!--<div class=\"panel-body\" style=\"padding:0\">-->
    <table class=\"table table-condensed\" style=\"margin: 0\">
        <tbody>
        <tr>
            <td>
                煤炭种类:{}
            </td>
            <td>
                {$order['price']}元/吨
            </td>
        </tr>
        <tr>
            <td>
                煤炭品质:{}
            </td>
            <td>
                产地:{$order['area_start']}
            </td>
        </tr>
        <tr>
            <td>
                煤炭粒度:{}
            </td>
            <td>
                吨数:{}
            </td>
        </tr>";
            }elseif($this->_message['category']==3){ // 找车
                $li_str = "<li>".$this->_message['content']."</li>";
            }else{
                // todo log here.
            }
        }
        return $li_str;

    }

//    function toSimple(){ // sometimes naive
//        if ($this->_message['type']=='plain'){ //微信的消息
//            $new_message['title']=$this->_message['title'];
//            $new_message['phone_number']=$this->_message['origin'];
//            $new_message['content']=$this->_message['content'];
//            $this->_message=$new_message;
//        }else{
//            if($this->_message['category']==0){
//
//            }
//        }
//    }

}