<?php

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m170607_143830_dropUserUniqueKey extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->dropIndex('user_unique_email', 'user', 'email', true);
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
