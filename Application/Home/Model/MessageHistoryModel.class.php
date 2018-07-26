<?php

namespace Home\Model;

use Think\Model;
use Home\Common\CardList\WhereConditions;

class MessageHistoryModel extends Model
{
    protected $tablePrefix = 'ck_';
    protected $tableName = "messages_history";

    private $_message = null;

    public function __construct()
    {
        $tableSuffix = date('Ym', time());
        $tableTo = $this->tablePrefix . $this->tableName . '_' . $tableSuffix;
        $day = (int)substr(date('Ymd',time()),6,2);
        if($day == 1){
            $result = M()->query('show tables like "' . $tableTo . '"');
            if ($result) {
                $this->tableName = $this->tableName . '_' . $tableSuffix;
            } else {
                $tableFromSuffix = date('Ym', strtotime('-1 month'));
                $tableFrom = $this->tablePrefix . $this->tableName . '_' . $tableFromSuffix;
                $whereUpdateTime = date('Y-m-d', strtotime('-1 week'));
                M()->execute('create table ' . $tableTo . ' like ' . $tableFrom);
                M()->execute('insert into ' . $tableTo . ' select * from ' . $tableFrom . ' where update_time > "' . $whereUpdateTime . '"');
                $this->tableName = $this->tableName . '_' . $tableSuffix;
            }
        }else{
            $this->tableName = $this->tableName . '_' . $tableSuffix;
        }
        parent::__construct();
    }

    public function findWhere(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->limit($beginStr, $countRow)->order($asc)->select();
        return $this->_message;
    }

}