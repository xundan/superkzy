<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 11:52
 */

namespace Home\Model;

use Home\Common\CardList\WhereConditions;
use Think\Model;

class MessagesModel extends Model
{
//    protected $tableName = "messages";// 不用写表前缀

    public function findWhere(WhereConditions $cond)
    {

        return $this->where($cond->getWhereConditions())->select();
    }

}