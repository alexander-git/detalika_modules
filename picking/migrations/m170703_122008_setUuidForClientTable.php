<?php

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m170703_122008_setUuidForClientTable extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('picking_profile_users')
            ->delete()
            ->dropColumn('clients_profile_id', $this->bigInteger())
            ->addColumn('clients_profile_id', 'UUID')
            ->createIndex('clients_profile_id');
    }
}
