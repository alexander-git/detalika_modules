<?php

class m170214_110152_initialStructure extends \execut\yii\migration\Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $i->table('delivery_partners')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
            'visible' => $this->boolean()->notNull()->defaultValue('true'),
        ]));
    }
}
