<?php

use yii\db\Migration;

use detalika\clients\OuterTables;

class m170116_001017_createProfileShopsTable extends Migration
{    
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_profile_shops}}', [
            'id' => $this->primaryKey(),
            'clients_profile_id' => $this->integer()->notNull(),
            'shop_id' => $this->integer()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);
        
        $this->createIndex(
            'clients_profile_shops_clients_profile_id_i', 
            '{{%clients_profile_shops}}',  
            'clients_profile_id'
        );
        $this->addForeignKey(
            'clients_profile_shops_clients_profile_id_fk', 
            '{{%clients_profile_shops}}', 
            'clients_profile_id', 
            '{{%clients_profiles}}', 
            'id'
        );
        
        $shopsTable = OuterTables::getTableName('shops');
        $idField = OuterTables::getFieldName('shops', 'id');
        $this->createIndex(
            'clients_profile_shops_source_id_i', 
            '{{%clients_profile_shops}}',  
            'shop_id'
        );
        $this->addForeignKey(
            'clients_profile_shops_shop_id_fk', 
            '{{%clients_profile_shops}}', 
            'shop_id', 
            $shopsTable, 
            $idField 
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%clients_profile_shops}}');
    }
}
