<?php

use detalika\requests\OuterTables;
use detalika\requests\helpers\ModuleHelper;
    
class m170209_220154_createBaseTables extends \execut\yii\migration\Migration
{

    public function initInverter(\execut\yii\migration\Inverter $i)
    {        
        $module = ModuleHelper::getModule();
                
        $outerCarsMarksTable = OuterTables::getTableName('carsMarks');
        $outerCarsModelsTable = OuterTables::getTableName('carsModels');
        $outerCarsModificationsTable = OuterTables::getTableName('carsModifications');
        $outerCarsModificationsEnginesTable = OuterTables::getTableName('carsModificationsEngines');
        $outerProfilesTable = OuterTables::getTableName('profiles');
        $outerUsersTable = OuterTables::getTableName('users');
        $outerGoodsTable = OuterTables::getTableName('goods');
        $outerDetailsArticlesTable = OuterTables::getTableName('detailsArticles');
        $outerDelivaryPartnersTable = OuterTables::getTableName('deliveryPartners');
        
        // requests_client_cars.
        $clientCarsTable = $i->table('requests_client_cars');
        $clientCarsTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'name' => $this->string()->defaultValue(null),
            'manufacture_year' => $this->integer()->notNull(),
            'vin_number' => $this->string()->notNull(),
        ]));
        
        if ($module->isCreateForeignKeyToProfilesTable) {
            $clientCarsTable->addForeignColumn($outerProfilesTable, true, null, 'clients_profile_id');
        } else {
            $clientCarsTable->addColumn('clients_profile_id', $this->bigInteger()->defaultValue(null)); 
        }
        
        if ($module->isCreateForeignKeyToCarsMarksTable) {
            $clientCarsTable->addForeignColumn($outerCarsMarksTable, false, null, 'cars_mark_id'); 
        } else {
            $clientCarsTable->addColumn('cars_mark_id', $this->bigInteger()->defaultValue(null)); 
        }
        
        if ($module->isCreateForeignKeyToCarsModelsTable) {
            $clientCarsTable->addForeignColumn($outerCarsModelsTable, false, null, 'cars_model_id');
        } else {
            $clientCarsTable->addColumn('cars_model_id', $this->bigInteger()->defaultValue(null));
        }
        
        if ($module->isCreateForeignKeyToCarsModificationsTable) {
           $clientCarsTable->addForeignColumn($outerCarsModificationsTable, false, null, 'cars_modification_id'); 
        } else {
         $clientCarsTable->addColumn('cars_modification_id', $this->bigInteger()->defaultValue(null));
        }
        
        if ($module->isCreateForeignKeyToCarsModificationsEnginesTable) {
             $clientCarsTable->addForeignColumn($outerCarsModificationsEnginesTable, false, null, 'cars_modifications_engine_id');
        } else {
            $clientCarsTable->addColumn('cars_modifications_engine_id', $this->bigInteger()->defaultValue(null));
        }
        

        // requests_request_statuses.
        $i->table('requests_request_statuses')->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'name' => $this->string()->notNull(),
        ]));

        
        //requests_equests.
        $requestsTable = $i->table('requests_requests');
        $requestsTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
        ]));
        $requestsTable->addForeignColumn('requests_request_statuses', true, null, 'requests_request_status_id');
        $requestsTable->addForeignColumn('requests_client_cars', true);
        if ($module->isCreateForeignKeyToProfilesTable) {
            $requestsTable->addForeignColumn($outerProfilesTable, true, null, 'clients_profile_id');
        } else {
            $requestsTable->addColumn('clients_profile_id', $this->bigInteger()->defaultValue(null));
        }
        
        
        //requests_request_messages.
        $messagesTable = $i->table('requests_request_messages');
        $messagesTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'text' => $this->text(),
        ]));
        $messagesTable->addForeignColumn('requests_requests', true);
        if ($module->isCreateForeignKeyToUsersTable) {
            $messagesTable->addForeignColumn($outerUsersTable, true, null, 'user_id');
        } else {
            $messagesTable->addColumn('user_id', $this->bigInteger()->defaultValue(null));   
        }
        
        //requests_request_articles.
        $articlesTable = $i->table('requests_request_articles');
        $articlesTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'name' => $this->string()->defaultValue(null),
        ]));
        $articlesTable->addForeignColumn('requests_requests', true);
        if ($module->isCreateForeignKeyToDetailsArticlesTable !== false) {
            $articlesTable->addForeignColumn($outerDetailsArticlesTable, false, null, 'goods_article_id'); 
        } else {
            $articlesTable->addColumn('goods_article_id', $this->bigInteger()->defaultValue(null));
        } 
        
        
        //requests_request_goods.
        $goodsTable = $i->table('requests_request_goods');
        $goodsTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'price' => $this->bigInteger(),
            'quantity' => $this->bigInteger(),
            'name' => $this->string()->defaultValue(null),
        ]));
        $goodsTable->addForeignColumn('requests_requests')
            ->alterColumnSetNotNull('requests_request_id');
        if ($module->isCreateForeignKeyToGoodsTable) {
            $goodsTable->addForeignColumn($outerGoodsTable, false, null, 'goods_good_id');
        } else {
            $goodsTable->addColumn('goods_good_id', $this->bigInteger());
        }

        if ($module->isCreateForeignKeyDeliveryPartnersTable) {
            $goodsTable->addForeignColumn($outerDelivaryPartnersTable, false, null, 'delivery_partner_id');
        } else {
            $goodsTable->addColumn('delivery_partner_id', $this->bigInteger());
        }

        $goodsTable->alterColumnSetNotNull('delivery_partner_id');
    }
}