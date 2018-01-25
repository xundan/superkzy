<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/10
 * Time: 16:19
 */

namespace Home\Common\CardList;


class WxCarNeedMsgCard extends MsgCard
{

    function toLi()
    {
        $img_name = $this->getImgName();
        $img = $img_name['img'];
        $name = $img_name['name'];
        $message = $this->buildCollection();
        $dial_param = ''.$message['phone_number'].','.$message['id'];
        $publish_date = substr($message['update_time'],0,10);
        if($message['invalid_id'] == 99){
            $imageString = "<img src='__PUBLIC__/home/images/need_overdue.png' width='50px'>";
            $invalidImage = "<img src='__PUBLIC__/home/images/invalid.png' width='80px' style='opacity: 0.5'>";
        }else{
            $imageString = "<img src='__PUBLIC__/home/images/need.png' width='50px'>";
            $invalidImage = '';
        }
        $li_str =
            "<li class='weui_panel weui_panel_access' style='border-radius: 5px'>
    <div class='pic-layer' style='position: absolute;right: 0'>" . $imageString . "</div>
    <div class='pic-layer' style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
    <div class='card-container'>
        <div class='card-head'>
            <div class='name text-center'>
                $name
            </div>
            <div class='dial text-center'>
                <a href='tel:{$message['phone_number']}' onclick=\"ck_log('dial', '{$dial_param}')\">
                    <img src='__PUBLIC__/home/images/phone.png'>
                    <span>拨打电话</span>
                </a>
            </div>
            <div class='function_button collapse text-center'>
                <button class='btn btn-xs btn-ck' style='width: 5.5em' onclick=\"collection_switch(this,{$message['id']})\">{$message['in_collection']}</button>
            </div>
        </div>
        <div class='highlight content brief_content'>{$message['content']}</div>
        <div class='card-foot pull-right help-block'>发布时间:" . $publish_date . "</div>
    </div>
    <div class='collapse-switch'><span class='collapse-arrow'>+</span></div>
</li>";
        return $this->replacePublicString($li_str);
    }

}