<?php

use yii\db\Migration;
use yii\db\Expression;

use detalika\clients\OuterTables;

class m170228_151137_modifyProfilesTable extends Migration
{
    public function safeUp()
    {
        $shopsTable = OuterTables::getTableName('shops');
        $shopsIdField = OuterTables::getFieldName('shops', 'id');
        $sourcesTable = OuterTables::getTableName('sources');
        $sourcesIdField = OuterTables::getFieldName('sources', 'id');
        
        $profilesTable = '{{%clients_profiles%}}';
        
        // Создадим в профиле столбец с id источника и скопируем для
        // возможных профилей первый источник из уже существуюющих
        // в таблице clients_profile_sources.
        $this->addColumn($profilesTable, 'source_id', $this->bigInteger());
        $this->createIndex(
            'clients_profiles_source_id_i',
            $profilesTable,
            'source_id'
        );
        $this->addForeignKey(
            'clients_profiles_source_id_fk',
            $profilesTable,
            'source_id',
            $sourcesTable ,
            $sourcesIdField
        );

        $profileSources = $this->db
            ->createCommand("SELECT * FROM clients_profile_sources")
            ->queryAll();
        $profileIds = [];
        foreach ($profileSources as $profileSource) {
            $profileId = (int) $profileSource['clients_profile_id'];
            $sourceId = (int) $profileSource['source_id'];
            if (!in_array($profileId, $profileIds)) {
                $this->db
                    ->createCommand()
                    ->update($profilesTable, ['source_id' => $sourceId], ['id' => $profileId])
                    ->execute();
                $profileIds []= $profileId;
            }
        }

        // Создадим в профиле столбец с id магазина и скопируем для
        // возможных профилей первый магазин из уже существуюющих
        // в таблице clients_profile_shops.
        $this->addColumn($profilesTable, 'shop_id', $this->bigInteger());
        $this->createIndex(
            'clients_profiles_shop_id_i',
            $profilesTable,
            'shop_id'
        );
        $this->addForeignKey(
            'clients_profiles_shop_id_fk',
            $profilesTable,
            'shop_id',
            $shopsTable ,
            $shopsIdField
        );

        $profileShops= $this->db
            ->createCommand("SELECT * FROM clients_profile_shops")
            ->queryAll();
        $profileIds2 = [];
        foreach ($profileShops as $profileShop) {
            $profileId = (int) $profileShop['clients_profile_id'];
            $shopId = (int) $profileShop['shop_id'];
            if (!in_array($profileId, $profileIds2)) {
                $this->db
                    ->createCommand()
                    ->update($profilesTable, ['shop_id' => $shopId], ['id' => $profileId])
                    ->execute();
                $profileIds2 []= $profileId;
            }
        }
        
        $this->dropTable('{{%clients_profile_sources%}}');
        $this->dropTable('{{%clients_profile_shops%}}');
    }

    public function safeDown()
    {
        $shopsTable = OuterTables::getTableName('shops');
        $shopsIdField = OuterTables::getFieldName('shops', 'id');
        $sourcesTable = OuterTables::getTableName('sources');
        $sourcesIdField = OuterTables::getFieldName('sources', 'id');
        
        $this->createTable('{{%clients_profile_sources}}', [
            'id' => $this->primaryKey(),
            'clients_profile_id' => $this->integer()->notNull(),
            'source_id' => $this->integer()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ]);

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
                
        $this->addForeignKey(
            'clients_profile_sources_source_id_fk', 
            '{{%clients_profile_sources}}', 
            'source_id', 
            $sourcesTable, 
            $sourcesIdField  
        );
        
        
       $this->createTable('{{%clients_profile_shops}}', [
            'id' => $this->primaryKey(),
            'clients_profile_id' => $this->integer()->notNull(),
            'shop_id' => $this->integer()->notNull(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'updated' => $this->dateTime(),
        ]);
        
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
            $shopsIdField 
        );
        
        $profiles = $this->db
            ->createCommand("SELECT * FROM clients_profiles")
            ->queryAll();
        
        foreach ($profiles as $profile) {
            $profileId = (int) $profile['id'];
            if (!empty($profile['source_id']))  {
                $this->db
                    ->createCommand()
                    ->insert('{{%clients_profile_sources}}', [
                        'clients_profile_id' =>  $profileId,
                        'source_id' => (int) $profile['source_id'],
                        'created' => (new Expression('NOW()')),
                        'updated' => null,
                    ])->execute();;
            }
            
            if (!empty($profile['shop_id']))  {
                $this->db
                    ->createCommand()
                    ->insert('{{%clients_profile_shops}}', [
                        'clients_profile_id' =>  $profileId,
                        'shop_id' => (int) $profile['shop_id'],
                        'created' => (new Expression('NOW()')),
                        'updated' => null,
                    ])->execute();
            }
        }
        
        $this->dropColumn('{{%clients_profiles%}}', 'source_id');
        $this->dropColumn('{{%clients_profiles%}}', 'shop_id');
    }
}
