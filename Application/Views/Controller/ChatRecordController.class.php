<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/27
 * Time: 22:46
 */

namespace Views\Controller;

use Think\Controller\RestController;
use Think\Log;


class ChatRecordController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    // 字段暂时没有用上
    protected $_wx_all = array("cjkzy001",// u"一圈",
        "cjkzy003",// u"三圈",
        "cjkzy005",// u"五圈",
        "cjkzy007",// u"煤炭交易平台",
        "mgzdz456",// u"非煤炭类交易平台",
        "cjkzywl",// u"物流信息平台",
        "cjkzyshan",// u"陕西",
        "cjkzy012",// u"山西",
        "cjkzynmg",// u"内蒙古",
        "cjkzyhb",// u"京津冀",
        "cjkzyhn",// u"河南",
        "cjkzysd",// u"山东",
        "cjkzyjs",// u"江苏安徽",
        "cjkzyhh",// u"湖南湖北",
        "cjkzyxn",// u"西南",
        "cjkzyxb",// u"西北",
        "cjkzybgd",// u"东北",
        "cjkzynf",// u"南方",
        "test",// u"测试",
      );

    public function index()
    {
        echo "ChatRecord api works";
    }

    //　记录一条历史消息
    Public function record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx = $object2['self_wx'];
                    $client_name = $object2['client_name'];
                    $content = $object2['content'];
                    $isme = $object2['isme'];
                    $type = $object2['type'];
                    $remark = $object2['remark'];

                    $insert = $this->createChatRecord($self_wx, $client_name,
                        $content, $isme, $type, $remark);
                    if ($insert) {
                        $data['result_code'] = "201";
                        $data['reason'] = "新建或修改数据成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = $client_name;
                        $data['result'] = $remark;
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

    // 拉取还未发送出去的消息
    Public function unsent_record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx = $object2['self_wx'];


                    $fetch = $this->fetch_unsent_record($self_wx);
                    if ($fetch) {
                        $data['result_code'] = "201";
                        $data['reason'] = "获取数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $fetch["id"];
                        $data['name']=$fetch["client_name"];
                        $data['word']=$fetch["content"];
                        $data['result'] = "success";
//                        $data['result'] = utf8_encode(json_encode($fetch));
                        Log::record("The response data: $data", Log::NOTICE);
                    } elseif($fetch===null) {
                        $data = $this->noMoreDataResponse($data);
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    // 按id重置信息的状态，用以标记已经发出的信息
    Public function status()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $id = $object2['id'];
                    $status = $object2['status'];

                    $update = $this->update_status($id, $status);
                    if ($update) {
                        $data['result_code'] = "201";
                        $data['reason'] = "更新数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $id;
                        $data['result'] = "update status to ".$status;
                    } elseif($update===0) {
                        $data = $this->noMoreDataResponse($data);
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);

                }

                $this->response($data, 'json');
                break;
        }
    }

    // 按self_wx与client_name重置type，用以标记已完成的转人工
    // 测试json:{"self_wx":"test","client_name":"test2","type":"plain"}
    Public function set_type()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx= $object2['self_wx'];
                    $client_name= $object2['client_name'];
                    $r_type = $object2['type'];

                    $update = $this->update_type($self_wx, $client_name, $r_type);
                    if ($update) {
                        $data['result_code'] = "201";
                        $data['reason'] = "更新数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $update;
                        $data['result'] = "update status to ".$r_type;
                    } elseif($update===0) {
                        $data = $this->noMoreDataResponse($data);
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);

                }

                $this->response($data, 'json');
                break;
        }
    }

    // 获取某微信号对某个客户的聊天记录
    Public function distinct_record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx = $object2['self_wx'];
                    $client_name = $object2['client_name'];

                    $fetch = $this->fetch_distinct_record($self_wx, $client_name);
                    if ($fetch) {
                        $record_list = "";
                        foreach ($fetch as $a_record){
                            if ($a_record){
                                if ($a_record["isme"]){
                                    $record_list .= "我:";
                                }else{
                                    $record_list .= $a_record["client_name"].":";
                                }
                                $record_list .= $a_record["content"]."<br>";
                            }
                        }
                        $data['result_code'] = "201";
                        $data['reason'] = "获取数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = count($fetch);
                        $data['result'] = $record_list;
                    } elseif($fetch===null) {
                        $data = $this->noMoreDataResponse($data);
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    // 取出所有的待转人工列表
    // 测试json:{"wx_list":["test","test2"]}
    Public function all_distinct_record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();
                if ($object2) {
                    $wx_list = $object2['wx_list'];
                    if ($wx_list){
                        $wx_in = $this->assemble_wx_in_sql($wx_list);
                        $fetch = $this->fetch_all_distinct_record($wx_in);

                        if ($fetch) {
                            $data['result_code'] = "201";
                            $data['reason'] = "获取数据成功";
                            $data['error_code'] = 0;
                            $data['message_id'] = count($fetch);
                            $data['result'] = $fetch;
                        } elseif($fetch===null) {
                            $data = $this->noMoreDataResponse($data);
                        } else{
                            $data = $this->dbErrorResponse($data);
                        }
                    }else{
                        $data = $this->noMoreDataResponse($data);
                    }

                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    /**
     * 拉取时间区间内的所有record
     * @param $time int 时间戳 起始时间 秒数
     */
    public function all_by_time($time=0)
    {
        if ($time==0){
            $time = time();
        }
        $start_time = $time-24*60*60;
        date_default_timezone_set("Asia/Shanghai");
        $date1_str = date("Y-m-d H:i:s", $start_time); // h是12小时制，H是24小时制
        $date2_str = date("Y-m-d H:i:s", $time);

        $records = $this->get_records_by_interval($date1_str, $date2_str);

        $next_time = $time+24*60*60;
        $url_prev = U('Views/ChatRecord/all_by_time') . "?time=".$start_time;
        $url_next = U('Views/ChatRecord/all_by_time') . "?time=".$next_time;
//        $url_delete = U('Views/DisplayMessages/delete')."?id=$id";
        $this->assign("prev", $url_prev);
        $this->assign("time_now",($date1_str.'<=>'.$date2_str));
        $this->assign("next", $url_next);

        $this->assign('records', $records);

        $this->display();
    }

    /**
     * 获取时段内的所有通话记录（每个会话前十条），并拼装好html标签
     * @param $date1_str
     * @param $date2_str
     * @return string
     */
    private function get_records_by_interval($date1_str, $date2_str){
        $all = "";
        $fetch = $this->fetch_all_distinct_record_by_interval($date1_str,$date2_str, $this->_wx_all);
        foreach ($fetch as $item) {
            $chat = $this->fetch_distinct_record_reverse($item['self_wx'],$item['client_name']);
            if ($chat) {
                $record_list = "<hr><h3 class='title'><strong>".$item['self_wx']."与".$item['client_name']."的会话：</strong></h3><br/>";
                $count = 0;
                foreach ($chat as $a_record){
                    if ($count>=10) break; // 每个会话只显示最近十条
                    if ($a_record){
                        $record_list.="<span class='record_time'>".$a_record["record_time"]."</span> ";
                        if ($a_record["isme"]){
                            $record_list .= "<span class='self_wx'>".$item['self_wx']."</span>:";
                        }else{
                            $record_list .= "<span class='client_name'>".$a_record["client_name"]."</span>:";
                        }
                        $record_list .= "<br/><span class='content'>".$a_record["content"]."</span><br/>";
                        $count++;
                    }
                }
                $all .= $record_list."<br><br>";
            }
        }
        return $all;
    }

    /**
     * 按时段获取所有会话，没有取内容
     * @param $date1
     * @param $date2
     * @param $wx_in
     * @return mixed
     */
    private function fetch_all_distinct_record_by_interval($date1, $date2, $wx_in)
    {
        $where['invalid_id'] = 0;
        $where['type'] = 'plain';
        $where['isme'] = 0;
        $where['self_wx']=array("IN", $wx_in);
        $where['record_time'] = array("BETWEEN", array($date1, $date2));
        $fetch = D('ChatRecord')->field('content',true)->where($where)->group('client_name')->order('record_time asc')->select();
        return $fetch;
    }

    /**
     * 获取[7天内]的所有会话，没有取内容，只为了取会话者
     * @param $wx_in
     * @return mixed
     */
    private function fetch_all_distinct_record($wx_in)
    {
        $s_date = date("Y-m-d",strtotime("-7 day"));
        $fetch = D('ChatRecord')->field('content',true)->where("isme=0 AND type='plain' AND invalid_id=0 AND record_time>'$s_date' AND self_wx IN "
            .$wx_in)->group('client_name')->order('record_time asc')->select();
        return $fetch;
    }

    /**
     * 时间正序获取所有会话
     * @param $self_wx
     * @param $client_name
     * @return mixed
     */
    private function fetch_distinct_record($self_wx, $client_name)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'client_name'=> $client_name,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->order('record_time asc')->select();
        return $find;
    }

    /**
     * 倒序获取所有会话
     * @param $self_wx
     * @param $client_name
     * @return mixed
     */
    private function fetch_distinct_record_reverse($self_wx, $client_name)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'client_name'=> $client_name,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->order('record_time desc')->select();
        return $find;
    }

    /**
     * 获取未发送的消息
     * @param $self_wx
     * @return mixed
     */
    private function fetch_unsent_record($self_wx)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'isme' => 1,
            'status' =>0,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->order('record_time asc')->find();
        return $find;
    }

    /**
     * 拼装ChatRecord对象
     * @param $self_wx
     * @param $client_name
     * @param $content
     * @param $isme
     * @param $type
     * @param $remark
     * @return mixed
     */
    private function createChatRecord($self_wx, $client_name, $content, $isme, $type, $remark)
    {

        //解决短时间内重复调用问题
//        $duplicate_data = D('Raw')->where("rid = '$title'")->find();
//        if ($duplicate_data) {
//            return $duplicate_data;
//        }

        $rawAttribute = array(
            'self_wx' => $self_wx,
            'client_name' => $client_name,
            'content' => $content,
            'isme' => $isme,
            'type' => $type,
            'remark' => $remark,
            'status' => 0,
            'invalid_id' => 0,
        );
        $insert = D('ChatRecord')->add($rawAttribute);
        return $insert;
    }

    /**
     * 按id更新对话状态status
     * @param $id
     * @param $status
     * @return bool
     */
    private function update_status($id, $status)
    {
        $data['status']=$status;
        $insert = D('ChatRecord')->where('id=%d',$id)->save($data);
        return $insert;
    }

    /**
     * 按对话者更新对话类型（type)
     * @param $self_wx
     * @param $client_name
     * @param $r_type
     * @return bool
     */
    private function update_type($self_wx, $client_name, $r_type)
    {
        $attribute = array(
            'self_wx' => $self_wx,
            'client_name' => $client_name,
        );
        $data['type']=$r_type;
        $insert = D('ChatRecord')->where($attribute)->save($data);
        return $insert;
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
     * 拼装sql的in子句
     * @param $wx_list
     * @return string
     */
    private function assemble_wx_in_sql($wx_list)
    {
        $wx_in = "(";
        foreach ($wx_list as $wx) {
            $wx_in .= "'$wx',";
        }
        $wx_in .= "'suffix')";
        return $wx_in;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function noMoreDataResponse($data)
    {
        $data['result_code'] = "202";
        $data['reason'] = "没有更多数据了";
        $data['error_code'] = 0;
        $data['message_id'] = 0;
        $data['result'] = "";
        return $data;
    }
}