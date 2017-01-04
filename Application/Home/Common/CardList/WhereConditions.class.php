<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 19:36
 */

namespace Home\Common\CardList;


class WhereConditions
{
    const FULL = -1;
    const INIT = -2;
    private $_whereConditions = array();
    private $_page = 1;
    private $_asc = "record_time desc";
    private $_exist_arr = array();
    private $_last_count = self::INIT;


    function __construct($whereCond = null, $page = null, $asc = null, $exist_arr = null, $last_count = self::INIT)
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
        if (!$column) return false;
        if (!$operator) return false;
        if ($val === null) return false;//sql会报错，所以直接返回
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
            } else {

                // 对area_start 和 area_end 的特殊处理，使其交替弹出
                if ($column == "area_start" || $column == "area_end") {
                    if ($column == "area_start") {
                        $other = "area_end";
                    } else {
                        $other = "area_start";
                    }
                    $val2 = $this->getV($other);
                    if (count($val) == 2 && count($val2) == 3)
                        return $this->popCond($other);
                }

                // pop 逻辑
                // column对应值是数组且不为空
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
                return $this->popCond($key);
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
     * 在MessagesModel->findWhereWithoutExist()前调用，进行退格检查与处理。
     * @return bool whereCond是否进行了退格
     */
    public function preSQL()
    {
        if ($this->getLastCount() == self::FULL) { // 说明上次拉取的是全额的消息
//            $this->ascPage();   // 已经用了exists数组排除已有数据，不用增长页数了
            return false;
        } else if ($this->getLastCount() == self::INIT) {
            $this->resetLastCount();
            return false;
        } else { // 否则退一格约束
            $this->resetPage();
            $this->popCond();
            return true;
        }
    }

    /**
     * 完成一次查询后对WhereCondition进行处理
     * @param $messages mixed 需要在下次查询中排除的信息
     * @param $card_counts int 本次查询有没有满额，没有满额下次要退格约束（见上面的preSQL()），
     *                      （之所以没有messages的数量代替，是因为$card_counts里面还会包含TipsCard的数量。)
     * @return int
     */
    public function postSQL($messages, $card_counts)
    {
        $this->updateExist($messages);
        if ($card_counts < C('DEFAULT_ROW')) { // 说明已经到查询极限了
            $this->setLastCount($card_counts);
        } else { // 说明是满的
            $this->setLastCount(self::FULL);
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
        $this->_last_count = self::FULL;
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
        return ($this->_last_count == self::FULL);
    }

    public function isExhausted()
    {
        if (count($this->_whereConditions) == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function pushSearchCond($column, $val)
    {
        $real_var = trim($val);
        if (!$real_var) return false;//sql会报错，所以直接返回
        $query = $this->search_method($real_var);
        if (count($query)) {
            return $this->_whereConditions[$column] = $query;
        } else {
            return false;
        }
    }

    //对字符串进行处理
    private function search_method($queryString)
    {
        $tempStr = $this->arrange_input($queryString);
        $tempStr = explode(" ", $tempStr);
        $query = array();
        foreach ($tempStr as $item) {
            $query[] = array('like', '%' . $item . '%');
        }
        return $query;
    }

    /**
     * 查询输入框输入内容整理
     * @param $str string       输入字符串
     * @return mixed|string     拆分数组
     */
    private function arrange_input($str)
    {
        $tempStr = trim($str);
        $tempStr = preg_replace("/\\s{1,}/", " ", $tempStr);
        return $tempStr;
    }
}