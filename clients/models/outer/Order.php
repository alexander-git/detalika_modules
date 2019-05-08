<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/22/17
 * Time: 11:26 AM
 */

namespace detalika\clients\models\outer;


use detalika\clients\OuterDependenciesInterface;
use detalika\clients\OuterTables;
use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    public static function tableName()
    {
        return OuterTables::getTableName('orders');
    }

    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('orders', $fieldName);
    }

    public static function className()
    {
        $dependencies = \yii::$container->get(OuterDependenciesInterface::class);
        return $dependencies->getOrdersModelClass();
    }
}