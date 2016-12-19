<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:36
 */

namespace Home\Common\CardList;


use Org\Util\ArrayList;

class WhereConditions
{
    private $_whereConditions = null;

    function __construct($whereCond = null)
    {
        if (!$whereCond) {
//            $this->_whereConditions = array("'1'"=>array('eq',"1"));
            $this->_whereConditions = array();
        } else {
            $this->_whereConditions = $whereCond;
        }
    }

    /**
     *
     * @return mixed where子句
     */
    function toJson()
    {
        return json_encode($this->getWhereConditions());
    }

    /**
     * 添加运算的where条件
     * @param $column string 字段名
     * @param $operator string 表达式 EQ,NEQ,GT,EGT,LT,ELT,LIKE,BETWEEN,IN,EXP
     * @param $val mixed 值
     * @param string $bool_operator 如果之前该字段有条件，与之前条件的关系AND,OR,XOR
     * @return array object 键值表
     */
    function pushCond($column, $operator, $val, $bool_operator = "AND")
    {
        if ($bool_operator == "AND" || $bool_operator == "OR" || $bool_operator == "XOR") {
            if ($this->getV($column)) {
                $new_val = array();
                array_push($new_val, $this->getV($column));
                array_push($new_val, array($operator, $val));
                array_push($new_val, $bool_operator);
                return $this->_whereConditions[$column] = $new_val;
            } else {
                return $this->_whereConditions[$column] = array($operator, $val);
            }
        } else {
            return false;
        }
    }


    public function popCond(){

    }

    function getV($key)
    {
        if (array_key_exists($key, $this->_whereConditions)) {
            return $this->_whereConditions[$key];
        } else {
            return false;
        }
    }

    /**
     * 删除AND运算的where片段
     * @param string $and 字符串
     * @return mixed
     */
    function removeAnd($and)
    {
        $index = $this->getWhereConditions()->indexOf($and);
        if ($index === false) return false;
        return $this->getWhereConditions()->remove($index);
    }

    /**
     * @return null
     */
    public function getWhereConditions()
    {
        return $this->_whereConditions;
    }

    /**
     * @param null $whereConditions
     */
    public function setWhereConditions($whereConditions)
    {
        $this->_whereConditions = $whereConditions;
    }


}