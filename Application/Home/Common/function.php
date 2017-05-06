<?php

/**
 * 设置有效期限，从当前时间开始算起
 * @return string
 */
function set_deadline()
{
//    $expire_days = 7;// C('EXPIRE_DAYS');
//    $str = '+'.$expire_days.' day';
    return date('Y-m-d H:i:s',strtotime('+7 day'));
}
/**
 * 获取地址id或地址名
 * @param $str string 地址名
 * @return mixed    地址id
 */
function get_area_id($str){
    $where['name'] = $str;
    $id = M('districts')->field('id')->where($where)->find();
    return $id['id'];
}

/**
 * @param $id int  地址id
 * @return mixed    地址名
 */
function get_area_name($id){
    $where['id'] = $id;
    $name = M('districts')->field('name')->where($where)->find();
    return $name['name'];
}
