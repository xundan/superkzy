<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/13
 * Time: 19:36
 */

namespace Views\Controller;


use Views\Model\MessageModel;
use Think\Controller\RestController;

class QueryRestController extends RestController
{
    protected $allowMethod = array('get','post'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    //　记录一条历史消息
    Public function q_text()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();
//                print_r($object2);
                if ($object2) {
                    $kw = $object2['kw'];
                    $user = $object2['user'];
                    $self = $object2['self'];

                    $this->saveRecord($kw, $user, $self);

                    $result = $this->q_text_do($kw);
                    if ($result) {
                        $data['result_code'] = "201";
                        $data['reason'] = "获取成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = null;
                        $data['result'] = $result;
                    } else {
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
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
     * 以文本形式返回查询结果
     * @param null $kw
     * @return bool|string
     */
    public function q_text_do($kw = null)
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
            return $res;
        }
        return false;
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

        $Msg = new MessageModel();

        // 如果审核细化，可以打开并修改此方法：
//            $data = $Msg->selectQuery($categoryArr,$granularityArr,$kindArr,$digitsArr);
        $data = $Msg->selectSearch($categoryArr, array_merge($granularityArr, $kindArr, $digitsArr),$page);
        return $data;
    }

    /**
     * @return array
     */
    private function defaultResponse()
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message_id" => null,
            "error_code" => 10005,
        );
        return $data;
    }

    private function defaultGetAction()
    {
        if ($this->_type == 'html') {
            echo 'html';
        } elseif ($this->_type == 'xml') {
            echo 'xml';
        }
        echo '<br>restful url is correct.';
    }

    /**
     * @return mixed
     */
    private function decodeJSONFromBody()
    {
        $result1 = $GLOBALS['HTTP_RAW_POST_DATA'];
        $object2 = json_decode($result1, true);
        return $object2;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function dbErrorResponse($data)
    {
        // TODO 数据库操作失败，通知开发人员
        $data['result_code'] = "106";
        $data['reason'] = "数据库操作错误";
        $data['error_code'] = 10006;
        $data['message_id'] = "";
        $data['result'] = "";
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function internalErrorResponse($data)
    {
        //TODO 数据为空，网络错误，客户端错误，通知开发人员
        $data['result_code'] = "500";
        $data['reason'] = "内部错误";
        $data['message_id'] = null;
        $data['error_code'] = 10500;
        $data['result'] = null;
        return $data;
    }
}