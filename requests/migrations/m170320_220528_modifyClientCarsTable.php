<?php

use detalika\requests\OuterTables;
use detalika\requests\helpers\ModuleHelper;

class m170320_220528_modifyClientCarsTable extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {        
        $module = ModuleHelper::getModule();
                
        $outerProfilesTable = OuterTables::getTableName('profiles');
        
        $clientCarsTable = $i->table('requests_client_cars');
        $clientCarsTable->addColumn('comment', $this->text());
        
        if ($module->isCreateForeignKeyToProfilesTable) {
            $clientCarsTable->dropForeignColumn($outerProfilesTable);
        } else {
            $clientCarsTable->dropColumn('clients_profile_id', $this->bigInteger()->defaultValue(null));
        }
        
        $clientCarProfilesTable = $i->table('requests_client_car_profiles')->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
        ]));
        $clientCarProfilesTable->addForeignColumn('requests_client_cars', true, null, 'requests_client_car_id');
        
        if ($module->isCreateForeignKeyToProfilesTable) {
            $clientCarProfilesTable->addForeignColumn($outerProfilesTable, true, null, 'clients_profile_id');
        } else {
            $clientCarProfilesTable->addColumn('clients_profile_id', $this->bigInteger()->defaultValue(null)); 
        }
    }
}