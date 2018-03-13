<?php

namespace Home\Controller;

use Home\Model\RawMessagesModel;
use Think\Controller;
use Think\Log;

class KFController extends Controller
{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const AUTH_URL = '/token?grant_type=client_credential&';
    const POST_URL = '/message/custom/send?access_token=';

    private $appid = 'wxe206341dcef42267';
    private $appsecret = '5387efbc8e6c9d1027f1576ee99d412e';
    private $access_token;
    public $errCode = 40001;
    public $errMsg = "no access";

//    function __construct($options)
//    {
//        $this->token = isset($options['token']) ? $options['token'] : '';
//        $this->encodingAesKey = isset($options['encodingaeskey']) ? $options['encodingaeskey'] : '';
//        $this->appid = isset($options['appid']) ? $options['appid'] : '';
//        $this->appsecret = isset($options['appsecret']) ? $options['appsecret'] : '';
//        $this->debug = isset($options['debug']) ? $options['debug'] : false;
//        $this->logcallback = isset($options['logcallback']) ? $options['logcallback'] : false;
//    }


    private function createMpRaw($title, $content, $owner, $sender, $sender_wx)
    {
        //解决短时间内重复调用问题
        $Raw = new RawMessagesModel();
        $duplicate_data = $Raw->where("rid = '$title'")->find();
        if ($duplicate_data) {
            return $duplicate_data;
        }

        $rawAttribute = array(
            'rid' => $title,
            'content' => $content,
            'sender' => $sender,
            'type' => 'wx_mp', // 来自公众号平台
            'remark' => '0',
            'status' => 0,
            'owner' => $owner,
            'sender_wx' => $sender_wx,
        );
        $insert = $Raw->add($rawAttribute);
        return $insert;
    }


    public function test()
    {
        $a = '啊实打实的情外觉得请叫我好的';
        $b = preg_match('/(觉得啊|好的)/i', $a);
        dump($b);
        $content = '加上面微信↑↑↑↑↑↑代办道路运输从业资格证无需考试，就可轻松拿证官网可查，有档案，包真《普货》《押运》《危险》 ★免除考试7天内取证★见证付款网上包查询★二维码可扫电话18210966089微信18210966089绝对真证，诚招代理欢迎有实力的合作伙伴加入,驾驶证 大套小套驾驶证 身份证 资格证 (真实档案 可过交警 可开罚单) （车管所真实档案）补办全国车牌 电话微信同号 :18210966089 请加手机号微信加上面微信↑↑↑↑↑↑↑↑..本号推广号不回复';
        $filter_ad_pattern = '/(从业资格证|冷柜|二维码|增值税|苯|转让|饮料|钢材|双驱|发票)/';
        $is_ad = preg_match($filter_ad_pattern, $content);
        dump($is_ad);
        if ($is_ad) {
            echo 0;
        } else {
            echo 1;
        }

        $a = $this->createMpRaw('asd','从业资格证12345678912','test','test','test');
        dump($a);
    }

    public function getRev()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postArr = json_decode($postStr, true);

        Log::record('XXXXXX:' . json_encode(I('post.'), true), 'WARN');
        Log::record('open_id:' . $postArr['FromUserName'], 'WARN');

        //信息发送者open_id
        $fromUserName = $postArr['FromUserName'];
        //信息发送时间戳
        $createTime = $postArr['CreateTime'];
        //信息id
        $msgId = $postArr['MsgId'];
        //信息类型
        $msgType = $postArr['MsgType'];
        switch ($msgType) {
            case 'text':
                //文字信息
                $content = $postArr['text'];
                $replyContent = '【超级矿资源】—信息量最大的煤炭信息平台，点击关注公众号<a href="http://mp.weixin.qq.com/s/dDw3tdi8ykUuYkA6BtWQnQ">超级矿资源</a>不掉队；

【优质推广业务】加大煤炭信息发布范围，请联系<a href="tel:17083425332">17083425332(微信同号)</a>；

【高质量煤炭群】点击<a href="http://www.xuncl.com/index.php/Home/Upload/show">全国高级煤炭圈</a>，共享资源你我他。';
                $this->reply_text($fromUserName, $replyContent);
                break;
            case 'image':
                //图片信息
                $picUrl = $postArr['PicUrl'];
                $mediaId = $postArr['MediaId'];
                $this->reply_image($fromUserName, $mediaId);
                break;
            case 'miniprogrampage':
                //小程序卡片消息
                $title = $postArr['Title'];
                $appId = $postArr['AppId'];
                $pagePath = $postArr['PagePath'];
                $thumbUrl = $postArr['ThumbUrl'];
                $thumbMediaId = $postArr['ThumbMediaId'];

                $title = '';
                $description = '';
                $url = '';
                $thumb_url = '';
                $this->reply_link($fromUserName, $title, $description, $url, $thumb_url);
                break;
            case 'event':
                //进入会话事件
                if ($postArr['Event'] == 'user_enter_tempsession') {
                    $sessionFrom = $postArr['SessionFrom'];
                    $title = '';
                    $description = '';
                    $url = '';
                    $thumb_url = '';
                    $replyContent = '【超级矿资源】—信息量最大的煤炭信息平台，点击关注公众号<a href="http://mp.weixin.qq.com/s/dDw3tdi8ykUuYkA6BtWQnQ">超级矿资源</a>不掉队；

【优质推广业务】加大煤炭信息发布范围，请联系<a href="tel:17083425332">17083425332(微信同号)</a>；

【高质量煤炭群】点击<a href="http://www.xuncl.com/index.php/Home/Upload/show">全国高级煤炭圈</a>，共享资源你我他。';
                    $this->reply_text($fromUserName, $replyContent);
//                    $this->reply_link($fromUserName,$title,$description,$url,$thumb_url);
                }
                break;
        }

    }

    /**
     * @param $user string user_open_id
     * @param $content string reply_content
     * 回复文本消息
     */
    public function reply_text($user, $content)
    {
        $reply['touser'] = $user;
        $reply['msgtype'] = 'text';
        $reply['text'] = array('content' => $content);

        $replyJson = $this->json_encode($reply);
        $res = $this->reply_post($replyJson);
    }

    /**
     * @param $user string open_id
     * @param $mediaId string image_media_id
     * 回复图片
     */
    public function reply_image($user, $mediaId)
    {
        $reply['touser'] = $user;
        $reply['msgtype'] = 'image';
        $reply['image'] = array('media_id' => $mediaId);

        $replyJson = $this->json_encode($reply);
        $res = $this->reply_post($replyJson);
    }

    /**
     * @param $user string open_id
     * @param $title string title
     * @param $description string desc
     * @param $url string url
     * @param $thumb_url string image_thumb_url
     */
    public function reply_link($user, $title, $description, $url, $thumb_url)
    {

        $reply['touser'] = $user;
        $reply['msgtype'] = 'link';
        $news['title'] = $title;
        $news['description'] = $description;
        $news['url'] = $url;
        $news['thumb_url'] = $thumb_url;
        $reply['link'] = $news;

        $replyJson = $this->json_encode($reply);
        $replyJson = $this->json_encode($reply);
        $res = $this->reply_post($replyJson);

    }

    /**
     * @param $user string open_id
     * @param $title string title
     * @param $pagepath string mini_pagepath_app_json('pages/index/index')
     * @param $thumb_media_id
     */
    public function reply_miniprogrampage($user, $title, $pagepath, $thumb_media_id)
    {
        $reply['tosuer'] = $user;
        $reply['msgtype'] = 'miniprogrampage';
        $mini['title'] = $title;
        $mini['pagepath'] = $pagepath;
        $mini['thumb_media_id'] = $thumb_media_id;
        $reply['miniprogrampage'] = $mini;

        $replyJson = $this->json_encode($reply);
        $replyJson = $this->json_encode($reply);
        $res = $this->reply_post($replyJson);
    }

    /**
     * @param $postJson string 需要post的JSON的数据
     * @return array 返回状态码
     */
    public function reply_post($postJson)
    {
        $access_token = $this->checkAuth();
        $result = false;
        if ($access_token) {
            Log::record('access_token:' . $access_token, 'WARN');
            $result = $this->http_post(self::API_URL_PREFIX . self::POST_URL . $access_token, $postJson);
            Log::record('post_result:' . json_encode($result), 'WARN');
        }
        return $result;
    }

    //消息推送配置校验
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'weilelianmeng';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
//            return true;
            echo $_GET["echostr"];
        } else {
//            return false;
            echo false;
        }
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     * @return string jsonData
     */
    static function json_encode($arr)
    {
        if (count($arr) == 0) return "[]";
        $parts = array();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * GET 请求
     * @param string $url
     * @return string content
     */
    private function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
            $is_curlFile = true;
        } else {
            $is_curlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        if (is_string($param)) {
            $strPOST = $param;
        } elseif ($post_file) {
            if ($is_curlFile) {
                foreach ($param as $key => $val) {
                    if (substr($val, 0, 1) == '@') {
                        $param[$key] = new \CURLFile(realpath(substr($val, 1)));
                    }
                }
            }
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        S($cachename, $value, $expired);
        return false;
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        $value = S($cachename);
        if ($value) {
            return $value;
        } else {
            return false;
        }
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        S($cachename, null);
        return false;
    }

    /**
     * 获取access_token
     * @param string $appid 如在类初始化时已提供，则可为空
     * @param string $appsecret 如在类初始化时已提供，则可为空
     * @param string $token 手动指定access_token，非必要情况不建议用
     * @return string $access_token
     */
    public function checkAuth($appid = '', $appsecret = '', $token = '')
    {
        if (!$appid || !$appsecret) {
            $appid = $this->appid;
            $appsecret = $this->appsecret;
        }
        if ($token) { //手动指定token，优先使用
            $this->access_token = $token;
            return $this->access_token;
        }

        $authname = 'miniprogram_access_token' . $appid;
        if ($rs = $this->getCache($authname)) {
            $this->access_token = $rs;
            Log::record('XXXXXX_AT_EXIST_S:' . S($authname), 'WARN');
            Log::record('XXXXXX_AT_EXIST_S_get:' . $rs, 'WARN');
            return $rs;
        }

        $result = $this->http_get(self::API_URL_PREFIX . self::AUTH_URL . 'appid=' . $appid . '&secret=' . $appsecret);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            $this->setCache($authname, $this->access_token, $expire);
            Log::record('XXXXXX_AT_GET:' . $this->access_token . '###expire:' . $expire, 'WARN');
            return $this->access_token;
        }
        return false;
    }

    /**
     * 删除验证数据
     * @param string $appid
     * @return bool result
     */
    public function resetAuth($appid = '')
    {
        if (!$appid) $appid = $this->appid;
        $this->access_token = '';
        $authname = 'miniprogram_access_token' . $appid;
        $this->removeCache($authname);
        return true;
    }


}