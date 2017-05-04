<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/22
 * Time: 11:08
 */

namespace Views\Model;
use Think\Model\RelationModel;

class CoalPriceContentModel extends RelationModel
{
    protected $autoAddRelations = TRUE;
    protected $tablePrefix = 'ck_';
    protected $tableName = 'coal_price_content';
    protected $_link = array(
        'DetailedIndex' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'CoalPriceDetailedIndex',
            'foreign_key' => 'content_id',
            'mapping_name'=> 'DetailedIndex',
        ),
        'Msg' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'CoalPriceMessage',
            'foreign_key' => 'message_id',
            'mapping_name' => 'Msg',
            'autoAddRelations' => 'True'
        )
    );
}