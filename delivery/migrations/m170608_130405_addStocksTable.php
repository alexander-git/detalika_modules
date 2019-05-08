<?php

use detalika\delivery\common\InnerTables;

class m170608_130405_addStocksTable extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $stocksTableName = InnerTables::getTableName('stocks');
        
        $i->table($stocksTableName)->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
            'address' => $this->string(),
            'work_time' => $this->string(),
            'ext_uuid' => $this->string(),
            'visible' => $this->boolean()->notNull()->defaultValue('true'),
        ]));
        
        $i->createIndex('delivery_stocks_uk', $stocksTableName, 'ext_uuid', true);
    }
}
