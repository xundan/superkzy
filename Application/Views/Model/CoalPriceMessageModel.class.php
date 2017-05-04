<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/21
 * Time: 11:31
 */

namespace Views\Model;
use Think\Model\RelationModel;
use Think\Model;

class CoalPriceMessageModel extends RelationModel
{
    protected $tablePrefix = 'ck_';
    protected $tableName = 'coal_price_message';
//    protected $_link = array(
//        'Content' => array(
//            'mapping_type' => self::HAS_MANY,
//            'class_name' => 'CoalPriceContent',
//            'foreign_key' => 'message_id',
//            'mapping_name' => 'Content',
//        )
//    );
}


