<?php
/**
 * 微信后台调用处理小消息
 * User: CLEVO
 * Date: 2016/8/24
 * Time: 17:29
 */

namespace Views\Controller;

use Think\Controller\RestController;
use Think\Log;

class PullMessagesController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    public function index()
    {
        echo "pull available";
    }

    /**
     * 拉取时间区间内的所有message，返回一个由所有小消息内容拼装的字符串
     * @param $date1 int 时间戳 起始时间 秒数
     * @param $date2 int 时间戳 结束时间
     */
    public function allByTime($date1, $date2)
    {
//        http://localhost/superkzy/index.php/Views/PullMessages/allByTime?date1=1471041647&date2=1471128047
        $data = array(
            "result_code" => "105",
            "reason" => "超时，请检查调用",
            "result" => "FAIL",
            "message" => null,
            "error_code" => 10005,
        );
        // 时间顺序不能错，而且不能拉三天以上的消息
        if (($date2 - $date1 < 0) || ($date2 - $date1 > 3 * 24 * 60 * 60 * 1000)) {
            $this->response($data, 'json');
            Log::record('PullMessages:allByTime($date1,$date2):有人尝试非法拉取消息。', 'WARN');
            return;
        }
        switch ($this->_method) {
            case 'get': // get请求处理代码
                date_default_timezone_set("Asia/Shanghai");
                $date1_str = date("Y-m-d H:i:s", $date1); // h是12小时制，H是24小时制
                $date2_str = date("Y-m-d H:i:s", $date2);
                $msg = $this->get_message_by_interval($date1_str, $date2_str);
                if ($msg) {
                    $data['message'] = $msg;
                    $data['result_code'] = "201";
                    $data['reason'] = "获取成功";
                    $data['result'] = "OK";
                    $data['error_code'] = 0;
                } elseif ($msg === null) {
                    $data['message'] = "参数错误";
                    $data['result_code'] = "500";
                    $data['reason'] = "参数错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10500;
                } elseif ($msg === "") { // 没有消息了
                    $data['message'] = "没有更多的消息了";
                    $data['result_code'] = "404";
                    $data['reason'] = "没有更多的消息了";
                    $data['result'] = "OK";
                    $data['error_code'] = 10404;
                } else { // 其他错误
                    $data['message'] = "内部错误";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10501;
                }
                $this->response($data, 'json');
                break;
        }
    }

    private function get_message_by_interval($date1, $date2){
        $str = "";
        $str .= $this->get_message_by_interval_category($date1, $date2, "供应");
        $str .= $this->get_message_by_interval_category($date1, $date2, "求购");
        $str .= $this->get_message_by_interval_category($date1, $date2, "找车");
        $str .= $this->get_message_by_interval_category($date1, $date2, "其他");
        return $str;
    }

    private function get_message_by_interval_category($date1, $date2, $category)
    {
        $where['invalid_id'] = 0;
        $where['type'] = 'plain';
        $where['status'] = 102;
        $where['category']=$category;
        $where['record_time'] = array("BETWEEN", array($date1, $date2));
        $results = D('message')->field("content")->where($where)->select();
        $results_str = "";
        foreach ($results as $row) {
            $results_str .= $row["content"];
        }
//        $results_str = D('message')->getLastSql();
        return $results_str;
    }

    /**
     * 按微信号拉取消息，已拉过的消息不能再拉
     * @param $wx string 微信号
     */
    public function pull($wx)
    {
        $data = array(
            "result_code" => "105",
            "reason" => "超时，请检查调用",
            "result" => "FAIL",
            "message" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $msg = $this->get_message($wx);
                if ($msg) {
                    $data['message'] = $msg['content'];
                    $data['result_code'] = "201";
                    $data['reason'] = "获取成功";
                    $data['result'] = "OK";
                    $data['error_code'] = 0;
                } elseif ($msg === 0) { // 关系表有数据，但是信息表没有该id
                    $data['message'] = "";
                    $data['result_code'] = "505";
                    $data['reason'] = "找不到该消息，请检查数据库";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10405;
                } elseif ($msg === null) { // wx为空
                    $data['message'] = "";
                    $data['result_code'] = "500";
                    $data['reason'] = "参数错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10500;
                } elseif ($msg === false) { // 没有消息了
                    $data['message'] = "";
                    $data['result_code'] = "404";
                    $data['reason'] = "没有更多的消息了";
                    $data['result'] = "OK";
                    $data['error_code'] = 10404;
                } else { // 其他错误
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10501;
                }
                $this->response($data, 'json');
                break;
        }
    }

    // 有些不标准，get方法修改msg2wx关系表状态，以后应改为post方法
    Public function used($wx, $msg_id)
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get':
                $update = $this->set_used($wx, $msg_id);
                if ($update) {
                    $data['result_code'] = "201";
                    $data['reason'] = "修改数据成功";
                    $data['error_code'] = "0";
                    $data['result'] = 'OK';
                    $data['message'] = "wx:" . $wx . ",msg_id:" . $msg_id;
                } elseif ($update === false) { //更新出错
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10501;
                } else { // 更新行数为0
                    $data['message'] = "";
                    $data['result_code'] = "404";
                    $data['reason'] = "没有可更新的数据";
                    $data['result'] = "OK";
                    $data['error_code'] = 10404;
                }
                $this->response($data, 'json');
                break;
        }
    }


    /**
     * @param $wx
     * @return string
     */
    private function get_message($wx)
    {
        //此处不能瞎echo，否则GLOBAL解析json会出错
//        echo "start ";
        if (!$wx) return null;
        $where['invalid_id'] = 0;
        $where['wx'] = $wx;
        $relation = D("RelationM2W")->where($where)->find();

        if ($relation) {
            $msg_id = $relation['msg_id'];
            $where1['invalid_id'] = 0;
            $where1['type'] = "plain";
            $where1['id'] = $msg_id;
            $msg = D('message')->where($where1)->find();
            if ($msg === null) {
                // 消息找不到，说明审核有异步操作，导致先审核过了，又被别人审核删了，保险删掉此消息，应有log
                $update_data['invalid_id'] = 3;
                $where2['invalid_id'] = 0;
                $where2['msg_id'] = $msg_id;
                D('RelationM2W')->where($where2)->save($update_data);
                Log::record('PullMessages:get_message(' . $wx . '):小消息' . $msg_id . '找不到了，被删除。', 'WARN');
                return false;
            }
            // 用了之后就删
            if ($msg) {
                $this->set_used($wx, $msg_id);
            }
            return $msg;
        }
        return false;
    }

    /**
     * @param $wx
     * @param $msg_id
     * @return bool
     */
    private function set_used($wx, $msg_id)
    {
        $update_data['invalid_id'] = 100;
        $where['invalid_id'] = 0;
        $where['msg_id'] = $msg_id;
        $where['wx'] = $wx;
        $update = D('RelationM2W')->where($where)->save($update_data);
        // TODO 此处应该更新小消息表中的update_time
        return $update;
    }


}