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
        $url = "http://www.kuaimei56.com/index.php/Views/QueryRest/q_text";
        $params = array(
//            "kw"=>'\u6c42\u8d2d', //求购
            "kw"=>"kkk", //求购
            "self"=>"PHPUnit",
            "user"=>"test"
        );
        $res = $this->http_post_json($url,json_encode($params));
//        $res = $this->uCurl($url,"POST",$params);
//        $res = $this->myCurlPost($url,$params);
        $this->assertEquals($res['status'],"200");
//        $this->assertEquals(json_encode($res),"200");
        $response_obj = $res['result'];
        $this->assertEquals($response_obj['result_code'],"201");
//        $this->assertEquals(json_encode($res),"201");
    }


    function uCurl( $url,$method,$params=array(),$header=''){
        $curl = curl_init();//初始化CURL句柄
        $timeout = 15;
        curl_setopt($curl, CURLOPT_URL, $url);//设置请求的URL
        curl_setopt($curl, CURLOPT_HEADER, false);// 不要http header 加快效率
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if($header==''){
//            $header [] = "Accept-Language: zh-CN;q=0.8";
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";
            $header[] = "Pragma: "; // browsers keep this blank.
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );
        }else{
            curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );
        }

        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);//设置连接等待时间
        switch ($method){
            case "GET" :
                curl_setopt($curl, CURLOPT_HTTPGET, true);break;
            case "POST":
                curl_setopt($curl, CURLOPT_POST,true);
//                curl_setopt($curl, CURLOPT_NOBODY, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS,$params);break;//设置提交的信息
            case "PUT" :
                curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "PUT");

                curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($params));break;
            case "DELETE":
                curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS,$params);break;
        }

        $data = curl_exec($curl);//执行预定义的CURL
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);//获取http返回值
        curl_close($curl);
        $res = json_decode($data,true);//var_dump($res);
        return ['status'=>$status,'result'=>$res];
    }

    function myCurlPost($url,$params){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //执行命令
        $data = curl_exec($curl);//执行预定义的CURL
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);//获取http返回值
        curl_close($curl);
        $res = json_decode($data,true);//var_dump($res);
        return ['status'=>$status,'result'=>$res];
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