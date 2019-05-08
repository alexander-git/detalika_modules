<?php

use detalika\requests\OuterTables;
use detalika\requests\helpers\ModuleHelper;

class m170311_110422_additionsToRequests  extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {        
        $module = ModuleHelper::getModule();
                
        $outerGoodsTable = OuterTables::getTableName('goods');
        $outerDetailsArticlesTable = OuterTables::getTableName('detailsArticles');
        $outerDelivaryPartnersTable = OuterTables::getTableName('deliveryPartners');
        
        //requests_request_positions.
        $requestsPositionsTable = $i->table('requests_request_positions');
        $requestsPositionsTable->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'name' => $this->string()->defaultValue(null),
            'good_name' => $this->string()->defaultValue(null),
            'price' => $this->bigInteger(),
            'quantity' => $this->bigInteger(),
            'link_to_search' => $this->string()->defaultValue(null),
            'parent_id' => $this->bigInteger()->defaultValue(null),
        ]));
        $requestsPositionsTable->addForeignColumn('requests_requests')
            ->alterColumnSetNotNull('requests_request_id');
        $requestsPositionsTable->addForeignColumn('requests_request_position_statuses', false, null, 'requests_request_position_status_id')
            ->alterColumnSetNotNull('requests_request_position_status_id');
        if ($module->isCreateForeignKeyToDetailsArticlesTable !== false) {
            $requestsPositionsTable->addForeignColumn($outerDetailsArticlesTable, false, null, 'goods_article_id'); 
        } else {
            $requestsPositionsTable->addColumn('goods_article_id', $this->bigInteger());
        } 
        
        if ($module->isCreateForeignKeyToGoodsTable) {
            $requestsPositionsTable->addForeignColumn($outerGoodsTable, false, null, 'goods_good_id');
        } else {
            $requestsPositionsTable->addColumn('goods_good_id', $this->bigInteger());
        }
        
        if ($module->isCreateForeignKeyDeliveryPartnersTable) {
            $requestsPositionsTable->addForeignColumn($outerDelivaryPartnersTable, false, null, 'delivery_partner_id');
        } else {
            $requestsPositionsTable->addColumn('delivery_partner_id', $this->bigInteger());
        }
        
        //requests_request_messages.
        $messagesTable = $i->table('requests_request_messages');        
        $messagesTable->addForeignColumn('requests_request_positions', false, null, 'requests_request_position_id');
    }
}
