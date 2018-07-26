<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/28
 * Time: 11:02
 */

namespace Views\Model;
use Think\Model;

class RawModel extends Model
{
    protected $tablePrefix = '';
    protected $tableName = "raw_messages";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "rid",
        "content",
        "sender",
        "send_time",
        "type",
        "remark",
        "owner",
        "sender_wx",
        "status",
        '_pk'=>"id",
    );

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
                M()->execute('insert into ' . $tableTo . ' select * from ' . $tableFrom . ' where send_time > "' . $whereUpdateTime . '"');
                $this->tableName = $this->tableName . '_' . $tableSuffix;
            }
        }else{
            $this->tableName = $this->tableName . '_' . $tableSuffix;
        }
        parent::__construct();
    }
}