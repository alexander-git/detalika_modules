<?php

use yii\db\Migration;

use detalika\clients\OuterTables;

class m170116_000947_createProfileSourcesTable extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%clients_profile_sources}}', [
            'id' => $this->primaryKey(),
            'clients_profile_id' => $this->integer()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex(
            'clients_profile_sources_clients_profile_id_i', 
            '{{%clients_profile_sources}}',  
            'clients_profile_id'
        );
        $this->addForeignKey(
            'clients_profile_sources_clients_profile_id_fk', 
            '{{%clients_profile_sources}}', 
            'clients_profile_id', 
            '{{%clients_profiles}}', 
            'id'
        );
        
        $this->createIndex(
            'clients_profile_sources_source_id_i', 
            '{{%clients_profile_sources}}',  
            'source_id'
        );
        
        $sourcesTable = OuterTables::getTableName('sources');
        $idField = OuterTables::getFieldName('sources', 'id');
        
        $this->addForeignKey(
            'clients_profile_sources_source_id_fk', 
            '{{%clients_profile_sources}}', 
            'source_id', 
            $sourcesTable, 
            $idField 
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%clients_profile_sources}}');
    }
}
