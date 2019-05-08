<?php

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m170703_123038_setUuidForClient extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('requests_request_positions')
            ->delete();
        $i->table('requests_request_articles')
            ->delete();
        $i->table('requests_request_goods')
            ->delete();
        $i->table('requests_request_messages')
            ->delete();
        $i->table('requests_requests')
            ->delete()
            ->dropColumn('clients_profile_id', $this->bigInteger())
            ->addColumn('clients_profile_id', 'UUID')
            ->createIndex('clients_profile_id');
        $i->table('requests_client_car_profiles')
            ->delete()
            ->dropColumn('clients_profile_id', $this->bigInteger())
            ->addColumn('clients_profile_id', 'UUID')
            ->createIndex('clients_profile_id');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
