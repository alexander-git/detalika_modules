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

class ClientCarProfile extends ActiveRecord
{
    public static function tableName()
    {
        return OuterTables::getTableName('requestsClientCarProfiles');
    }

    public static function getFieldName($fieldName)
    {
        return OuterTables::getFieldName('requestsClientCarProfiles', $fieldName);
    }

    public static function className()
    {
        $dependencies = \yii::$container->get(OuterDependenciesInterface::class);
        return $dependencies->getRequestsClientCarProfilesModelClass();
    }
}