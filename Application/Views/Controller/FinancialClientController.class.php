<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/2/28
 * Time: 16:51
 */

namespace Views\Controller;
use Think\Controller\RestController;
use Think\Log;
use Views\Model\FinancialClientModel;

class FinancialClientController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    //　记录一条历史消息
    Public function create()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $phone_number = $object2['phone_number'];
                    $type = $object2['type'];
                    $status = $object2['status'];

                    $insert = $this->createFinancialClient($phone_number, $type,
                        $status);
                    if ($insert) {
                        $data['result_code'] = "201";
                        $data['reason'] = "新建或修改数据成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = $phone_number.$type;
                        $data['result'] = "OK";
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

    private function createFinancialClient($phone_number, $type, $status)
    {
        $FinancialClient = new FinancialClientModel();
        $client = array(
            'phone_number' => $phone_number,
            'type' => $type,
            'status' => $status,
            'invalid_id' => 0,
        );
        $insert = $FinancialClient->add($client);
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
//        $result1 = $GLOBALS['HTTP_RAW_POST_DATA'];
        $result1 = $_POST;
        Log::record("result:".$result1,Log::ERR);
        Log::record("result_json:".json_encode($result1),Log::ERR);
        $object2 = json_decode($result1, true);
        return $result1;
    }

    public function test(){
        $result1 = $_POST;
//        $object2 = json_decode($result1, true);
        Log::record(json_encode($result1),Log::ERR);
    }

    public function show(){

        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }

    public function show_wl(){
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }

    public function show_tp(){
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }

}