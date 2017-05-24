<?php

/**
 * 设置有效期限，从当前时间开始算起
/**
 * 设置有效期限
 * @return int
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

/**
 * @param $str string 需要转化的字符串
 * @return mixed 转化后的字符串
 */
function unicode2utf8($str) { // unicode编码转化，用于显示emoji表情
    $str = '{"result_str":"' . $str . '"}'; // 组合成json格式
    $str_array = json_decode ( $str, true ); // json转换为数组，利用 JSON 对 \uXXXX 的支持来把转义符恢复为 Unicode 字符
    return $str_array ['result_str'];
}

/**
 * @param $array array 需要排序的含有中文的数组
 * @return bool 成功操作返回true
 */

function utf8_array_sort(&$array) {
    if(!isset($array) || !is_array($array)) {
        return false;
    }
    foreach($array as $k=>$v) {
        $array[$k] = iconv('UTF-8', 'GBK//IGNORE',$v);
    }
    sort($array);
    foreach($array as $k=>$v) {
        $array[$k] = iconv('GBK', 'UTF-8//IGNORE', $v);
    }
    return true;
}

