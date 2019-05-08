<?php

use yii\db\Migration;

class m161229_175757_createCitiesTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%auth_cities}}', [
            'name' => 'string NOT NULL PRIMARY KEY',
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auth_cities}}');
    }

}
