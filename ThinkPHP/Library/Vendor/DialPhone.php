<?php
function sendCode($phone,$code)
{
    include "TopSdk.php";
    $c = new TopClient;
    $c->appkey = '23425802';
    $c->secretKey = C('VERIFY_SECRETKEY');
//    $c->secretKey = 'bfc7dd30439f8421c066cc9cdf40555a';
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName('迅单网络');
    $req->setSmsParam("{\"code\":$code,\"product\":\"超级矿资源\"}");
    $req->setRecNum("$phone");
    $req->setSmsTemplateCode("SMS_12951206");
    $resp = $c->execute($req);
}


function sendCode_forKMW($phone,$code)
{
    include "TopSdk.php";
    $c = new TopClient;
    $c->appkey = '23425802';
    $c->secretKey = C('VERIFY_SECRETKEY');

    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName('迅单网络');
    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"快煤网\"}");
    $req->setRecNum("$phone");
    $req->setSmsTemplateCode("SMS_12951206");
    $resp = $c->execute($req);
    return $resp;
}

function setExpiringSMS($phone,$data)
{
    include_once "TopSdk.php";
    $c = new TopClient;
    $c->appkey = '23425802';
    $c->secretKey = C('VERIFY_SECRETKEY');
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName('超级矿资源');
    $req->setSmsParam(json_encode($data));
    $req->setRecNum("$phone");
    $req->setSmsTemplateCode("SMS_67055003");
    $resp = $c->execute($req);
    return $resp;
}

function getRecordPages($phone,$date){
    $c = new TopClient;
    $c->appkey = '23425802';
    $c->secretKey = C('VERIFY_SECRETKEY');
    $req = new AlibabaAliqinFcSmsNumQueryRequest;
    $req->setRecNum($phone);
    $req->setQueryDate($date);
    $req->setCurrentPage("1");
    $req->setPageSize("50");
    $resp = $c->execute($req);
    return $resp;
}

?>