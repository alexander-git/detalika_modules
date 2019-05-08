<?php

use yii\db\Migration;

class m170131_133142_dropNotNullInUser extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->alterColumn('user', 'username', 'DROP not null');
    }

    public function safeDown()
    {
        $this->alterColumn('user', 'username', 'SET not null');
    }
}
