<?php

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m170616_074601_addClientsSources extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('clients_sources')
            ->create($this->defaultColumns([
                'name' => $this->string()->notNull(),
                'visible' => $this->boolean()->defaultValue('true'),
            ]));
        $sourcesTable = OuterTables::getTableName('sources');
        $i->table('clients_profiles')
            ->dropForeignColumn($sourcesTable)
            ->addForeignColumn('clients_sources');
    }
}
