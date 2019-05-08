<?php

namespace detalika\requests\models\user\search;

use detalika\requests\common\CurrentUser;
use detalika\requests\models\search\ClientCarSearch as BaseClientCarSearch;
use detalika\requests\models\outer\ClientProfile;
use yii\db\ActiveQuery;
use yii\db\Expression;

class ClientCarSearch extends BaseClientCarSearch
{
    use UserNavigationFactoryTrait;

    /**
     * @param ActiveQuery $query
     */
    protected function addOptionalConditionsToQuery($query)
    {
        parent::addOptionalConditionsToQuery($query);
        
        $clientCarTableName =  self::tableName();
//        $clientsProfileTable = ClientProfile::tableName();

//        $clientsProfileIdField = ClientProfile::getFieldName('id');
//        $clientsProfileIdFieldFull = $clientsProfileTable . '.' .$clientsProfileIdField;

        $currentClientProfileId = CurrentUser::instance()->getClientProfileId();
        $query->andFilterWhere([
            'IN',
            'requests_client_car_profiles.clients_profile_id',
            new Expression($currentClientProfileId),
        ]);
        $query->andFilterWhere([$clientCarTableName . '.visible' => true]);
    }
    
    public function getGridColumns() 
    {
        $columns = parent::getGridColumns();
   
        unset($columns['id']);
        unset($columns['visible']);
        unset($columns['clientNameFilter']);
        unset($columns['name']);
        unset($columns['cars_mark_id']);
        unset($columns['cars_model_id']);
        unset($columns['cars_modification_id']);
        unset($columns['cars_modifications_engine_id']);
        unset($columns['manufacture_year']);
        unset($columns['vin_number']);
        unset($columns['created']);
        unset($columns['updated']);
                    
        // Сделаем столбец с полным именем первым.
        $newColumns = [];
        $newColumns['carFullName'] = [
            'attribute' => 'carFullName',
        ];
        
        foreach ($columns as $key => $value) {
            $newColumns[$key] = $value;
        }
        
        return $newColumns;
    }
}