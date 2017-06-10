<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/2
 * Time: 22:09
 */

namespace Home\Controller;


use Home\Model\MessagesModel;
use Think\Controller;

header("Content-Type: text/html; charset=UTF-8");
class QueryController extends Controller
{
    public function q($kw=null)
    {
        if ($kw === null){

        }else{
            $str = $kw;
            $categoryArr = $this->extractParam($str,"/(求购|供应|求车|车源)/");
            $granularityArr = $this->extractParam($str,"/(块煤|原煤|籽煤|沫煤|面煤)/");
            // 所有关键字
            $kindArr = $this->extractParam($str,"/(动力煤|喷吹煤|炼焦煤|焦炭|气化煤|煤泥|气煤|电煤)/");
            $digitsArr = $this->extractParam($str,"/(\\d+)/");
            $residue = $str;

            $Msg = new MessagesModel();
//            $data = $Msg->selectQuery($categoryArr,$granularityArr,$kindArr,$digitsArr);
            $data = $Msg->selectSearch($categoryArr,array_merge($granularityArr,$kindArr,$digitsArr));
//            var_dump($data);
            $this->assign("data",$data);
        }

        $this->display();
    }



    /**
     * 查询输入框输入内容整理
     * @param $str string       输入字符串
     * @return string           替换分隔符后返回的字符串
     */
    private function arrange_input($str)
    {
        $tempStr = trim($str);
        $tempStr = preg_replace("/[\\s,，]{1,}/", " ", $tempStr);

        return $tempStr;
    }

    /**
     * 按照正则取出符合正则的关键字数组，并返回，并且把源字符串作删除关键字处理
     * @param $kwString string 输入字符串，注意这里传的是指针，会把改动保存下来
     * @param $pattern string 关键字
     * @return array 截取关键字的数组
     */
    public function extractParam(&$kwString, $pattern)
    {
        $kwString = $this->arrange_input($kwString);
        $category_pattern = $pattern;
        $category = array();
        $match = array();
        $res = preg_match_all($category_pattern, $kwString, $match);
        if ($res) {
            $category = $match[0];
        }
        $kwString = trim(preg_replace($category_pattern, "", $kwString));
        return $category;
    }
}
