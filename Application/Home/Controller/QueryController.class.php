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
    public function q($kw = null)
    {
        if ($kw === null) {

        } else {
            $data = $this->prepareList($kw, 1);
            $this->assign("data", $data);
            $this->assign("kw", $kw);
        }
        $this->display();
    }

    /**
     * 以文本形式返回查询结果
     * @param null $kw
     * @return bool|string
     */
    public function q_text($kw = null, $user=null, $self=null)
    {
        if ($kw === null) {

        } else {
            $data = $this->prepareList($kw, 1);
            $res = "$kw:<br><br>";
            foreach($data as $row){
                $content = $this->formatRow($row);
                $res .= $content."<br><br>";
            }
//            echo $res;
            if($user){
                $this->saveRecord($kw,$user,$self);
            }
            return $res;
        }
        return false;
    }

    private function saveRecord($kw,$user,$self){
        $map["last_record"]=$kw;
        $map["user_id"]=$user;
        $data["last_record"]=$kw;
        $data["user_id"]=$user;
        $data["self"]=$self;
        $data["status"]=0;
        $data["invalid_id"]=0;
        $data["remark"]="query_plain";
        $res = M("ck_query_record")->where($map)->find();
        if ($res){
            $data["id"]=$res["id"];
            return M("ck_query_record")->save($data);
        }else{
            return M("ck_query_record")->add($data);
        }

    }

    /**
     * 规范化化每行数据成文本
     * @param $row
     * @return mixed
     */
    private function formatRow($row){
        $content = "web".$row['id'];
        if ($row['type']=='web'){

        }else{
            $content = $row['content'];
        }
        return $content;
    }

    public function qAjax()
    {
        $post = I('post.', '', 'trim');
        $kw = $post['kw'];
        if ($kw === null) {
        } else {
            $data = $this->prepareList($kw, 1);
            echo json_encode($data);
        }
    }


    /**
     * 查询输入框输入内容整理
     * @param $str string       输入字符串
     * @return string           替换分隔符后返回的字符串
     */
    private function arrangeInput($str)
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
        $kwString = $this->arrangeInput($kwString);
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

    /**
     * 上拉加载
     */
    public function qMore()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $kw = $post['kw'];
        $page = $post['page'];
        // 按页面读取查询
        if ($kw === null) {
        } else {
            $data = $this->prepareList($kw, $page);
//            $data['page'] = $page; // 把page送回去，作为校验
            echo json_encode($data);
        }
        return;
    }

    /**
     * @param $kw
     * @return array
     */
    private function prepareList($kw, $page)
    {
        $str = $kw;
        $categoryArr = $this->extractParam($str, "/(求购|供应|求车|车源)/");
        $granularityArr = $this->extractParam($str, "/(块煤|原煤|籽煤|沫煤|面煤)/");
        // 所有关键字
        $kindArr = $this->extractParam($str, "/(动力煤|喷吹煤|炼焦煤|焦炭|气化煤|煤泥|气煤|电煤)/");
        $digitsArr = $this->extractParam($str, "/(\\d+)/");
        $residue = $str;

        $Msg = new MessagesModel();

        // 如果审核细化，可以打开并修改此方法：
//            $data = $Msg->selectQuery($categoryArr,$granularityArr,$kindArr,$digitsArr);
        $data = $Msg->selectSearch($categoryArr, array_merge($granularityArr, $kindArr, $digitsArr),$page);
        return $data;
    }

}
