<?php

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m170718_132024_addExtuuidProfileField extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('user')->addColumn('clients_profile_id', 'UUID');
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
