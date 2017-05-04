<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/24
 * Time: 9:10
 */

namespace Views\Model;
use Think\Model\RelationModel;


class CoalPriceDetailedIndexModel extends RelationModel
{
    protected $tablePrefix = 'ck_';
    protected $tableName = 'coal_price_detailed_index';
//    protected $_link = array(
//        'Index' => array(
//            'mapping_type' => self::HAS_ONE,
//            'class_name' => 'CoalPriceIndex',
//            'foreign_key' => 'index_id',
//            'mapping_name' => 'Index'
//        )
//    );

}