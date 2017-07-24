<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/6
 * Time: 16:28
 */
require_once('C:/wamp/www/superkzy/Application/Home/Controller/QueryController.class.php');

class QueryControllerTest extends PHPUnit_Framework_TestCase
{

    public function testExtractParam(){
        $QueryController = new \Home\Controller\QueryController();
        $str = "找求购，供b应 车s源";
        $this->assertEquals(array("求购"),$QueryController->extractParam($str,"/(求购|供应|求车|车源)/"));
        $this->assertEquals("找 供b应 车s源",$str); // 参数str的值应该剥去掉已搜的关键字
        $str = "找求购，供应  车源";
        $this->assertEquals(array("求购","供应","车源",),$QueryController->extractParam($str,"/(求购|供应|求车|车源)/"));
        $this->assertEquals("找",$str);
        $str = "求购面煤，硫0.4灰8，热量5700，水9。电话17736773323";
        $this->assertEquals(array("0.4","8","5700","9","17736773323"),$QueryController->extractParam($str,"/([\\d.]+)/"));
        $this->assertEquals("求购面煤 硫灰 热量 水。电话",$str);
    }

    public function testRestfulAPI(){
//        $url = "http://www.kuaimei56.com/index.php/Views/QueryRest/q_text";
        $url = "http://localhost/superkzy/index.php/Views/QueryRest/q_text";
        $params = array(
//            "kw"=>'\u6c42\u8d2d', //求购
            "kw"=>"kkk", //求购
            "self"=>"PHPUnit",
            "user"=>"test"
        );
        $res = $this->http_post_json($url,json_encode($params));
        $this->assertEquals($res['status'],"200");
        $response_obj = $res['result'];
        $this->assertEquals($response_obj['result_code'],"201");
    }


    function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

//        return array($httpCode, $response);
        $res = json_decode($response,true);//var_dump($res);
        return ['status'=>$httpCode,'result'=>$res];
    }
}