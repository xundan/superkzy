<?php
namespace Home\Controller;

use Home\Common\CardList\WhereConditions;
use Think\Controller;

//header("Content-type: text/html; charset=utf-8");

class IndexController extends Controller
{
    public function index()
    {


        echo '123';

//        $a = new WhereConditions(null);
//        $a->pushCond("area_start","eq","榆林");
//        $a->pushCond("area_end","eq","郑州");
//        $a->pushCond("id","gt","5");
//        echo $a->toJson();
//        $Msg = D('messages');
//        $messages = $Msg ->findWhere($a);
//        var_dump($messages);
//        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');

    }
}