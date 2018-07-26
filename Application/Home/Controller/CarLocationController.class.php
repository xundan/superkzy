<?php

namespace Home\Controller;

use Think\Controller;
use Think\Crypt\Driver\Des;
use Think\Log;

class CarLocationController extends Controller
{
//    const API_URL_PREFIX = 'https://testopen.95155.com/apis';
    const API_URL_PREFIX = 'https://zhiyunopenapi.95155.com/apis/';
    const AUTH_URL = '/login/';
    const POST_URL = '/message/custom/send?access_token=';

//    private $user = 'f14d0a8b-bff1-48c6-ba91-c77ce0c2bc67';
    private $user = '9f45e667-6a27-4797-b01a-c3cbe717d51a';
//    private $pwd = '81tP9P4zzt955Ag48952CFW6OU4L33';
    private $pwd = '1r9l0T31s2E96269J84a1qD9ZYuF17';
//    private $client_id = '11bf75ab-2208-468f-9a16-17aa7a082ad9';
    private $client_id = '870c9456-d93b-46e0-aa8a-1bb685e1c319';
    private $token;
//    private $key = '';
    private $des_key = "CTFOTRV1";//DES加密解密算法的KEY
    public $errCode = 40001;
    public $errMsg = "no access";

    public function test()
    {
//        header("Content-Type:text/html; charset=utf-8");
//        $str = 'user=f14d0a8b-bff1-48c6-ba91-c77ce0c2bc67&pwd=81tP9P4zzt955Ag48952CFW6OU4L33';
////        $str = '测试123测试asd';
//        $key = 'CTFOTRV1';
//        $des = new Des();
//        $re = $des->encrypt($str, $key); //加密
//        dump($re);
//        $result = bin2hex($re);
//        $result2 = base64_encode($re);
//        dump($result);
//        dump($result2);
////        echo bin2hex($re); //给二进制转为16进制，所谓的解决乱码
//        dump($des->decrypt($re, $key));
//        dump($des->decrypt($result, $key));
//        dump($des->decrypt(hex2bin($result), $key));
//        dump($des->decrypt($result2, $key));
//        dump($des->decrypt(base64_decode($result2), $key));

//        $a = $this->http_get('http://api.feixiaohao.com/site/daohang/');
//        dump($a);
        dump(date('Y-m-d H:i:s', time()));

    }

    public function testApi()
    {
//        $a = $this->checkAuth();
//        dump($this->getCache('carLocation_token'.$this->user));
        G('begin');

//        $a = $this->carRTCNoSearch('陕yh0009', 2);
        $a = $this->carTrackSearch('陕yh0009', '2018-04-06', '2018-04-06');
        dump($a);
        G('end');
        dump(G('begin', 'end') . 's');
    }

    /**
     * 公众号查询页面
     */
    public function mpShow()
    {
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    /**
     * 公众号查询结果展示
     */
    public function mpResultShow()
    {
        $subInfo = I('get.', '', 'trim');
        echo $subInfo;
    }

    public function searchEntry()
    {
        $this->display();
    }

    public function carOwnerCheckShow()
    {
        $this->display();
    }

    public function carOwnerCheckAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carOwnerCheck($subInfo['vclN'], $subInfo['ownerName'], $subInfo['ownerPhone']);
        if ($result) {
            echo $result['result'];//yes=通过，name=姓名不正确，phone=手机号不正确
        }
        //否则查无结果
    }

    /**
     * 车主真实性验证接口
     * @param $vclN string 车牌号
     * @param $ownerName string 车主姓名
     * @param $ownerPhone string 车主电话
     * @return bool|mixed
     */
    public function carOwnerCheck($vclN, $ownerName, $ownerPhone)
    {
        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclN=' . $vclN . '&ownerName=' . $ownerName . '&ownerPhone=' . $ownerPhone;
        $str = $this->encrypt($str);
        $result = $this->http_get(self::API_URL_PREFIX . '/checkOwnerByVclNo/' . $str . '?client_id=' . $this->client_id);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function carRTCNoShow()
    {
        $this->display();
    }

    public function carRTCNoAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carRTCNoSearch($subInfo['vclN'], $subInfo['vco']);
        if ($result) {
            echo json_encode($result['result'], true);//roadTransport:运输证号码,vdtTm:有效期
        }
        //否则查无结果
    }

    /**
     * 道路运输证查询
     * @param $vclN string 车牌号
     * @param $vco string 车牌颜色
     * @return bool|mixed
     */
    public function carRTCNoSearch($vclN, $vco)
    {
        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclN=' . $vclN . '&vco=' . $vco;
        $str = $this->encrypt($str);
//        dump(self::API_URL_PREFIX . '/vQueryRTCNo/' . $str . '?client_id=' . $this->client_id);
        $result = $this->http_get(self::API_URL_PREFIX . '/vQueryRTCNo/' . $str . '?client_id=' . $this->client_id);
//        dump($result);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
//            dump($json);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function carsLastLocationMultiShow()
    {
        $this->display();
    }

    public function carsLastLocationMultiAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carsLastLocationMultiSearch($subInfo['vclNs']);
        LOG::record('#####' . json_encode($result));
        if ($result) {
            echo json_encode($result['result'], true);
        }
        //否则查无结果
    }

    /**
     * 多车位置查询
     * @param $vclNs string 车牌号列表，以半角逗号连接
     * @return bool|mixed
     */
    public function carsLastLocationMultiSearch($vclNs)
    {
        //test data
//        $b['adr'] = "江苏省镇江市京口区大港镇金港大道镇江市政协新区工作委员会，向东北方向，150 米";
//        $b['spd'] = '0.0';
//        $b['utc'] = 1504589600000;
//        $b['vno'] = 222;
//        $b['vco'] = 2;
//        $a['result'][0] = $b;
//        return $a;

        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclNs=' . $vclNs . '&timeNearby=72';
        $str = $this->encrypt($str);
        $result = $this->http_get(self::API_URL_PREFIX . '/vLastLocationMultiV4/' . $str . '?client_id=' . $this->client_id);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function carTrackShow()
    {
        $this->display();
    }

    public function carTrackAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carTrackSearch($subInfo['vclN'], $subInfo['qryBtm'], $subInfo['qryEtm']);
        if ($result) {
            echo json_encode($result['result'], true);
        }
        //否则查无结果
    }

    /**
     * 车辆轨迹查询 (车牌号)
     * @param $vclN string 车牌号
     * @param $qryBtm string 开始时间 yyyy-MM-dd HH:mm:ss 和yyyy-MM-dd 格式
     * @param $qryEtm string 结束时间 yyyy-MM-dd HH:mm:ss 和yyyy-MM-dd 格式
     * @return bool|mixed
     */
    public function carTrackSearch($vclN, $qryBtm, $qryEtm)
    {
        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclN=' . $vclN . '&qryBtm=' . $qryBtm . '&qryEtm=' . $qryEtm;
        $str = $this->encrypt($str);
        $result = $this->http_get(self::API_URL_PREFIX . '/vHisTrack24/' . $str . '?client_id=' . $this->client_id);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function carParkShow()
    {
        $this->display();
    }

    public function carParkAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carParkSearch($subInfo['vclN'], $subInfo['vco'], $subInfo['qryBtm'], $subInfo['qryEtm'], $subInfo['parkMins']);
        if ($result) {
            echo json_encode($result['result'], true);
        }
        //否则查无结果
    }

    /**
     * 车辆停车查询 (车牌号)
     * @param $vclN string 车牌号
     * @param $vco int 车辆颜色 1：蓝 2：黄
     * @param $qryBtm string 开始时间 yyyy-MM-dd HH:mm:ss 和yyyy-MM-dd 格式
     * @param $qryEtm string 结束时间 yyyy-MM-dd HH:mm:ss 和yyyy-MM-dd 格式
     * @param $parkMins int 停靠时间 单位：分钟，默认10且大于等于3
     * @return bool|mixed
     */
    public function carParkSearch($vclN, $vco, $qryBtm, $qryEtm, $parkMins = 10)
    {
        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclN=' . $vclN . '&vco=' . $vco . '&qryBtm=' . $qryBtm . '&qryEtm=' . $qryEtm . '&parkMins=' . $parkMins;
        $str = $this->encrypt($str);
        $result = $this->http_get(self::API_URL_PREFIX . '/vQueryPark/' . $str . '?client_id=' . $this->client_id);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }

    public function carOperationDataShow()
    {
        $this->display();
    }

    public function carOperationDataAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = $this->carOperationDataSearch($subInfo['vclN'], $subInfo['vco'], $subInfo['month']);
        if ($result) {
            echo json_encode($result['result'], true);
        }
        //否则查无结果
    }

    /**
     * 车辆运营数据查询
     * @param $vclN string 车牌号
     * @param $vco int 车辆颜色 1：蓝 2：黄
     * @param $month string 查询月份
     * @return bool|mixed
     */
    public function carOperationDataSearch($vclN, $vco, $month)
    {
        //test data
//        $b['dayAvgMileage'] = 225;
//        $b['dayAvgTime'] = 12;
//        $b['operationDay'] = 2;
//        $b['operationRate'] = '50.3%';
//        $b['totalMileage'] = 1126;
//        $b['totalTime'] = 130;
//        $a['result'] = $b;
//        return $a;

        $token = $this->checkAuth();
        $str = 'token=' . $token . '&vclN=' . $vclN . '&vco=' . $vco . '&month=' . $month;
        $str = $this->encrypt($str);
        $result = $this->http_get(self::API_URL_PREFIX . '/vOperationData/' . $str . '?client_id=' . $this->client_id);
        if ($result) {
            $result = $this->decrypt($result);
            $json = json_decode($result, true);
            Log::record('返回状态码为：' . $json['status'], 'NOTICE');
            if (!$json || $json['status'] != 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            return $json;
        }
        return false;
    }


//    public function encrypt($str)
//    {
//        $key = $this->key;
//        $des = new Des();
//        $re = $des->encrypt($str, $key); //加密
//        $result = bin2hex($re);
//        return $result;
//    }

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
     * @param string $cacheName
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cacheName, $value, $expired)
    {
        S($cacheName, $value, $expired);
        return false;
    }

    /**
     * 获取缓存，按需重载
     * @param string $cacheName
     * @return mixed
     */
    protected function getCache($cacheName)
    {
        $value = S($cacheName);
        if ($value) {
            return $value;
        } else {
            return false;
        }
    }

    /**
     * 清除缓存，按需重载
     * @param string $cacheName
     * @return boolean
     */
    protected function removeCache($cacheName)
    {
        S($cacheName, null);
        return false;
    }

    /**
     * 获取token
     * @param string $user 如在类初始化时已提供，则可为空
     * @param string $pwd 如在类初始化时已提供，则可为空
     * @param string $client_id 如在类初始化时已提供，则可为空
     * @param string $token 手动指定token，非必要情况不建议用
     * @return string $token
     */
    public function checkAuth($user = '', $pwd = '', $client_id = '', $token = '')
    {
        if (!$user || !$pwd || !$client_id) {
            $user = $this->user;
            $pwd = $this->pwd;
            $client_id = $this->client_id;
        }
        if ($token) { //手动指定token，优先使用
            $this->token = $token;
            return $this->token;
        }

        $authName = 'carLocation_token' . $user;
        if ($rs = $this->getCache($authName)) {
            $this->token = $rs;
            return $rs;
        }

        $str = 'user=' . $user . '&pwd=' . $pwd;
//        dump($str);
        $str = $this->encrypt($str);
//        dump($str);
//        dump(self::API_URL_PREFIX . self::AUTH_URL . $str . '?client_id=' . $client_id);
        $result = $this->http_get(self::API_URL_PREFIX . self::AUTH_URL . $str . '?client_id=' . $client_id);
//        dump($result);
        $result = $this->decrypt($result);
//        dump($result);
        if ($result) {
            $json = json_decode($result, true);
//            dump($json);
            if (!$json || $json['status'] !== 1001) {
                $this->errCode = $json['status'];
                return false;
            }
            $this->token = $json['result'];
//            dump($this->token);
            $expire = 259200;   //3day
            $this->setCache($authName, $this->token, $expire);
            return $this->token;
        }
        return false;
    }

    /**
     * 删除验证数据
     * @param string $user
     * @return bool result
     */
    public function resetAuth($user = '')
    {
        if (!$user) $user = $this->user;
        $this->token = '';
        $authName = 'carLocation_token' . $user;
        $this->removeCache($authName);
        return true;
    }

    /*
     * 在采用DES加密算法,cbc模式,pkcs5Padding字符填充方式下,对明文进行加密函数
    */
    private function encrypt($input, $key = '', $iv = '')
    {
        if (!$key) {
            $key = $this->des_key;
        }
        if (!$iv) {
            $iv = $this->des_key;
        }
        $size = 8; //填充块的大小,单位为bite    初始向量iv的位数要和进行pading的分组块大小相等!!!
        $input = $this->pkcs5_pad($input, $size);  //对明文进行字符填充
        $td = mcrypt_module_open(MCRYPT_DES, '', 'cbc', '');    //MCRYPT_DES代表用DES算法加解密;'cbc'代表使用cbc模式进行加解密.
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);    //对$input进行加密
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = bin2hex($data);   //对加密后的密文进行转16进制
        return $data;
    }

    /*
     * 在采用DES加密算法,cbc模式,pkcs5Padding字符填充方式,对密文进行解密函数
    */
    private function decrypt($crypt, $key = '', $iv = '')
    {
        if (!$key) {
            $key = $this->des_key;
        }
        if (!$iv) {
            $iv = $this->des_key;
        }
        $crypt = $this->hex2bin($crypt);   //16进制转2进制流
        $td = mcrypt_module_open(MCRYPT_DES, '', 'cbc', '');    //MCRYPT_DES代表用DES算法加解密;'cbc'代表使用cbc模式进行加解密.
        mcrypt_generic_init($td, $key, $iv);
        $decrypted_data = mdecrypt_generic($td, $crypt);    //对$input进行解密
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $decrypted_data = $this->pkcs5_unpad($decrypted_data); //对解密后的明文进行去掉字符填充
        $decrypted_data = rtrim($decrypted_data);   //去空格
        return $decrypted_data;
    }

    /*
     * 对明文进行给定块大小的字符填充
    */
    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /*
     * 对解密后的已字符填充的明文进行去掉填充字符
    */
    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        return substr($text, 0, -1 * $pad);
    }

    /*
     * 16进制转二进制流
     */
    private function hex2bin($str)
    {
        $len = strlen($str) / 2;
        $re = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = $i * 2;
            $re .= chr(hexdec(substr($str, $pos, 1)) << 4) | chr(hexdec(substr($str, $pos + 1, 1)));
        }
        return $re;
    }

}