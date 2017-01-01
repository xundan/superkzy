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
    private $_whereConditions = array();
    private $_page = 1;
    private $_asc = "record_time desc";
    private $_exist_arr = array();
    private $_last_count = -1;


    function __construct($whereCond = null, $page = null, $asc = null, $exist_arr = null, $last_count = -1)
    {
        if ($whereCond) {
            $this->_whereConditions = $whereCond;
        }
        if ($page) {
            $this->_page = $page;
        }
        if ($asc) {
            $this->_asc = $asc;
        }
        if ($exist_arr) {
            $this->_exist_arr = $exist_arr;
        }
        $this->_last_count = $last_count;
    }

    /**
     *
     * @return mixed where子句
     */
    function toJson()
    {
        $data['conditions'] = json_encode($this->_whereConditions);
        $data['page'] = $this->_page;
        $data['asc'] = $this->_asc;
        $data['exists'] = $this->_exist_arr;
        $data['last_count'] = $this->_last_count;
        return json_encode($data);
    }

    static function parseJson($json)
    {
        $data = json_decode($json, true);
        return new WhereConditions(json_decode($data['conditions'], true), $data['page'], $data['asc'], $data['exists'], $data['last_count']);
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

    public function preSQL()
    {
        $eoa = $this->getLastCount();

        if ($eoa < 0) { // 说明上次拉取的是全额的消息
            $this->ascPage();
        } else { // 否则退一格约束
            $this->resetPage();
            $this->popCond();
        }
    }

    public function postSQL($messages)
    {
        $this->updateExist($messages);
        $counts = count($messages);
        if ($counts < C('DEFAULT_ROW')) { // 说明已经到查询极限了
            $this->_last_count = $counts;
        } else { // 说明是满的
            $this->_last_count = -1;
        }
        return $this->getLastCount();
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

    /**
     */
    public function resetPage()
    {
        $this->_page = 1;
    }

    /**
     */
    public function ascPage()
    {
        $this->_page = $this->_page + 1;
    }

    /**
     * 记录已经查出那些数组
     * @param $messages mixed 查询出来的数组
     */
    public function updateExist($messages)
    {
        foreach ($messages as $message) {
            array_push($this->_exist_arr, $message["id"]);
        }
    }

    public function getExist()
    {
        return $this->_exist_arr;
    }

    public function resetExist()
    {
        $this->_exist_arr = array();
    }

    /**
     * @return int
     */
    public function getLastCount()
    {
        return $this->_last_count;
    }

    /**
     */
    public function resetLastCount()
    {
        $this->_last_count = -1;
    }

    /**
     * @param int $last_count
     */
    public function setLastCount($last_count)
    {
        $this->_last_count = $last_count;
    }

    public function isLastCountFull()
    {
        return ($this->_last_count == -1);
    }

    public function isExhausted(){
        if (count($this->_whereConditions) == 0){
            return true;
        }else{
            return false;
        }
    }
}