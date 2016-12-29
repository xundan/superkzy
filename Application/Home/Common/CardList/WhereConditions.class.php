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
    private $_page = 1;
    private $_asc = "record_time desc";

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

    static function parseJson($json){
        return new WhereConditions(json_decode($json));
    }

    /**
     * 添加运算的where条件，添加的顺序决定pushCond的顺序，尽量后加先要解除约束的条件
     * @param $column string 字段名
     * @param $operator string 表达式 EQ,NEQ,GT,EGT,LT,ELT,LIKE,BETWEEN,IN,EXP
     * @param $val mixed 值
     * @param string $bool_operator 如果之前该字段有条件，与之前条件的关系AND,OR,XOR
     * @return array object 键值表
     */
    public function pushCond($column, $operator, $val, $bool_operator = "AND")
    {
        if (!$operator) return false;
        if (!$val) return false;//sql会报错，所以直接返回
        if ($bool_operator == "AND" || $bool_operator == "OR" || $bool_operator == "XOR") {
            if ($this->getV($column)) { // 先判断之前对column有没有约束
                $new_val = array();
                array_push($new_val, $this->getV($column)); // 旧约束
                array_push($new_val, array($operator, $val)); // 新约束
                array_push($new_val, $bool_operator); // 约束之间的逻辑运算
                return $this->_whereConditions[$column] = $new_val; //
            } else { // 没有约束，就直接添加约束
                return $this->_whereConditions[$column] = array($operator, $val);
            }
        } else {
            return false;
        }
    }

    /**
     * 解除一条约束
     * @param $column string 表字段，如果有，就从它开始解约束
     * @return mixed 返回被接触的条件，或者0
     */
    public function popCond($column = null)
    {
        if ($column) { // 用户设置了column
            $val = $this->getV($column);
            if (empty($val) || !is_array($val)) {
                // $column对应值为空，释放掉这一对键值
                unset($this->_whereConditions[$column]);
                return 0;
            } else {// column对应值是数组且不为空
                if (count($val) == 2) { // 如果深度为1
                    return $this->popTopLevelCond($column);
                } elseif (count($val) == 3) { // 如果深度大于1
                    $operator = array_pop($val);
                    if ($operator == "AND") { // 如果本层条件为AND
                        $deleted_cond = array_pop($val);
                        $this->_whereConditions[$column] = $val[0];
                        return $deleted_cond;
                    } else { // 如果本层条件为其他
                        // 如果最顶层是OR，则释放掉这一对键值
                        return $this->popTopLevelCond($column);
                    }
                } else { // 数组成员非2非3，这种情况不可能
                    unset($this->_whereConditions[$column]);
                    return 0;
                }
            }
        } else { //如果用户没有设置column
            $keys = array_keys($this->_whereConditions);
            if (empty($keys)) { // 如果已经没有更多条件，直接初始化
                $this->_whereConditions = array();
                return 0;
            } else { // 如果有条件，pop出最后一条
                $key = array_pop($keys);
                $deleted_cond = $this->_whereConditions[$key];
                unset($this->_whereConditions[$key]);
                return $deleted_cond;
            }
        }
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

    /**
     * 删除顶层键值对，并返回被删除的结果
     * @param $column
     * @return mixed
     */
    private function popTopLevelCond($column)
    {
        $result = $this->_whereConditions[$column];
        unset($this->_whereConditions[$column]);
        return $result;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * @return string
     */
    public function getAsc()
    {
        return $this->_asc;
    }

    /**
     * @param string $asc
     */
    public function setAsc($asc)
    {
        $this->_asc = $asc;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }


}