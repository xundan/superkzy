<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/5
 * Time: 9:20
 */

namespace Home\Controller;

use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessagesModel;
use Think\Controller;
use Think\Log;

abstract class SearchController extends ComController
{


    /**
     * 获取返回数据，保证每次都获取十个卡片，包括tips
     * @param $whereCond mixed 查询条件
     * @param $stage int 当前卡片列表的状态：精确、模糊、其他、结束
     * @param $category string 找车、车源、供应、求购
     * @return mixed 返回数据
     */
    public function getOrderWithoutExist(WhereConditions $whereCond, $stage, $category)
    {
        if ($stage < CardList::END) { // 如果是结束阶段就不执行下面代码了
            $Msg = new MessagesModel();
            $cards = new CardList(array());
            $cards->setStage($stage);
            $count = "0"; // 调试用，查看do/while的行为，数字表示阶段，字母表示结束一次迭代的方式。
            do {
                $isPopped = $whereCond->preSQL();
                if($cards->notFull()&&$isPopped&&$cards->atAccurate()){// 如果是第一次退格约束
                    $count.="1";
                    $cards->addSimilar();
                    if ($cards->isFull()){
                        $count.="b";
                        break;
                    }
                }
//                if($cards->notFull()&&$whereCond->isExhausted()&&$cards->atSimilar()){// 如果条件退完
//                    $count.="2";
//                    $cards->addOther();
//                    if ($cards->isFull()){
//                        $count.="c";
//                        break;
//                    }
//                }

                if($cards->notFull()&&$whereCond->isExhausted()&&$cards->atSimilar()){// 如果条件退完
                    $count.="3";
                    $count.="d[".$cards->getCount()."]";
                    $cards->addEnd($category);
                    break;
                }

                $temp_messages = $Msg->findWhereWithoutExist($whereCond, $cards->getCount(),$category);
                $cards->appendMessage($temp_messages);

//                if ($cards->atOther()&&$cards->notFull()){ //其他查询也不能满足，说明查到底了
//                    $count.="3";
//                    $count.="d[".$cards->getCount()."]";
//                    $cards->addEnd();
//                    break;
//                }
                $whereCond->postSQL($temp_messages, $cards->getCount());
                $count.="a[".$cards->getCount()."]";
            } while (!($cards->isFull() || $cards->atEnd()));
            Log::record("search msg:".$count, Log::DEBUG);
            // 把$whereCond送到前台
            $data["msg"] = "".$count;
            $data['where_cond_json'] = $whereCond->toJson();
            $data["li_array"] = $cards->toLiArray();
            $data["stage"] = $cards->getStage();
        } else {
            $data["msg"] = "success";
            $data['where_cond_json'] = $whereCond->toJson();
            $data["li_array"] = "";
            $data["stage"] = $stage;
        }

        return $data;
    }

}