<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2018/6/8
 * Time: 16:37
 */

namespace Home\Controller;

use Think\Controller;
use Think\Log;

class WxPayController extends Controller
{
    public function test()
    {
//        header("Content-Type:text/html; charset=utf-8");
//        $this->assign('openId', 'o5DarwOHSeo3C2JgqT2Oa1CkXghQ');
//        $where['openId'] = 'o5DarwOHSeo3C2JgqT2Oa1CkXghQ';
//        $where['order_state'] = 2;
//        $record = M('wxpay_order')->where($where)->order('update_time desc')->select();
//        $this->assign('record_data', $record);
//        $this->display();
//        dump($this->test2($a));
//        header('content-type:text/event-stream');
//        header('cache-control:no-cache');
//        while (true)
//        {
//            echo 'hello world';
//            ob_flush();
////            flush();
//            sleep(1);
//        }
//        socket_create();
//        require_once 'WS.php';
//        $ws->send($ws,'asd');
//        $subInfo['date_start'] = '2018-07-11';
//        $subInfo['date_end'] = '2018-07-15';
//        $resultLeave = M()->query("select * from `ck_display_leave` where `leave_time` between '" . $subInfo['date_start'] . "' and '" . $subInfo['date_end'] .
//            "' or `deadline` between '" . $subInfo['date_start'] . "' and '" . $subInfo['date_end'] .
//            "' or (`leave_time` < '" . $subInfo['date_start'] . "' and `deadline` > '" . $subInfo['date_end'] . "')");
//        dump(M()->getLastSql());
//        dump($resultLeave);

    }

    public function test2(){
//        return 'aaa';
        $str = 'aaa';
        $b = json_encode($str);
        dump($str);
        dump($b);
        require_once 'WS.php';
    }

    public function getOpenid()
    {
        vendor('WxPay.lib.JsApiPay');
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        return $openId;
    }

    public function testPay()
    {
        $openId = $this->getOpenid();
        $this->assign('openid', $openId);
        $this->display();
    }

    public function setOrder()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');
        $order_no = C('WX_PAY_CONFIG')['MCHID'] . date("YmdHis");
        $openId = $subInfo['openId'];
        switch ($subInfo['payType']) {
            case 5:
                $total_fee = 1;
                $goods = '月费会员';
                break;
            case 6:
                $total_fee = 2;
                $goods = '季费会员';
                break;
            case 7:
                $total_fee = 3;
                $goods = '半年费会员';
                break;
            case 8:
                $total_fee = 4;
                $goods = '年费会员';
                break;
            default:
                $total_fee = 1;
                $goods = '月费会员';
        }
        $data = array(
            'order_no' => $order_no,
            'openId' => $openId,
            'phone_number' => $subInfo['phone_number'],
            'goods' => $goods,
            'total_fee' => $total_fee,
            'order_state' => 1
        );
        $attach = M('wxpay_order')->add($data);
        $jsApiParameters = $this->wxPay($openId, $goods, $order_no, $total_fee, $attach);
        echo $jsApiParameters;
    }

    public function wxPay($openId, $goods, $order_no, $total_fee, $attach)
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        vendor('WxPay.lib.log');
//        vendor('WxPay.lib.WxPay', '', '.JsApiPay.php');
        vendor('WxPay.lib.JsApiPay');

        //初始化日志
        $logHandler = new \CLogFileHandler(LOG_PATH . date('Y-m-d') . '.log');
        $log = \Log::Init($logHandler, 15);

        //获取用户openid
        $tools = new \JsApiPay();
//        if (empty($openId)) {
//            $openId = $tools->GetOpenid();
//        }
        $log->INFO('openId:' . $openId);

        //统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($goods);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($order_no);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url('https://www.xuncl.com/index.php/Home/WxPay/notify');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);

        Log::record('$$$:' . json_encode($order));
        $log->INFO(json_encode($order));
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $log->INFO('###' . $jsApiParameters . '###');
        return $jsApiParameters;
    }

    public function notify()
    {
        vendor('WxPay.lib.notify');
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }

    public function nativePay(){
        $url = $this->wxNativePay();
        $this->assign('url',urlencode($url));
        $this->display();
    }

    public function wxNativePay(){
        vendor('WxPay.lib.WxPay','','.NativePay.php');
        $notify = new \NativePay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("https://www.xuncl.com/index.php/Home/WxPay/notify");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");
        $result = $notify->GetPayUrl($input);
        $url = $result["code_url"];
        return $url;
    }

    public function payRecord()
    {
//        dump($_GET['openId']);
        $this->assign('openId', $_GET['openId']);
        $where['openId'] = $_GET['openId'];
        $where['order_state'] = 2;
        $record = M('wxpay_order')->where($where)->order('update_time desc')->select();
        $this->assign('record_data', $record);
        $this->display();
    }
}