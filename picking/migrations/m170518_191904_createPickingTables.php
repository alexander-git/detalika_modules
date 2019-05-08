<?php

use yii\db\Migration;

class m170518_191904_createPickingTables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $areFieldsPairInTableUnique = true;
        
        /*
        // Если нужно добавить внешние ключи.
        $clientsProfilesTable = 'clients_profiles';
        $clientsProfilesIdField = 'id';
        
        $requestsRequestPositionsTable = 'requests_request_positions';
        $requestsRequestPositionsIdField = 'id';
        
        $usersTable = 'user'; 
        $usersIdField = 'id'; 
        */
        
        $profileUsersTable = '{{%picking_profile_users%}}';
        $requestPositionUsersTable = '{{%picking_request_position_users%}}';
        
        $this->createTable($profileUsersTable, [
            'id' => $this->primaryKey(),
            'created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
            'clients_profile_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
        ], $tableOptions);
       
        $this->createIndex(
            'picking_profile_users_clients_profile_id_user_id_i',
            $profileUsersTable,
            ['clients_profile_id', 'user_id'],
            $areFieldsPairInTableUnique  
        );
        
        /*
        // Если нужно добавить внешние ключи.
        $this->addForeignKey(
            'picking_profile_users_clients_profile_id_fk',
            $profileUsersTable,
            'clients_profile_id',
            $clientsProfilesTable,
            $clientsProfilesIdField
        );

        $this->createIndex(
            'picking_profile_users_clients_profile_id_i',
            $profileUsersTable,
            'clients_profile_id'
        );
        
        $this->addForeignKey(
            'picking_profile_users_user_id_fk',
            $profileUsersTable,
            'user_id',
            $usersTable,
            $usersIdField
        );
        
        $this->createIndex(
            'picking_profile_users_user_id_i',
            $profileUsersTable,
            'user_id'
        );
        */
        
        $this->createTable($requestPositionUsersTable, [
            'id' => $this->primaryKey(),
            'created' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated' => $this->dateTime(),
            'requests_request_position_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
        ], $tableOptions);
        
        $this->createIndex(
            'picking_request_position_users_rqeuests_request_position_id_user_id_i',
            $requestPositionUsersTable,
            ['requests_request_position_id', 'user_id'],
           $areFieldsPairInTableUnique  
        );
        
        /*
        $this->addForeignKey(
            'picking_request_position_users_requests_request_position_id_fk',
             $requestPositionUsersTable,
            'requests_request_position_id',
            $requestsRequestPositionsTable,
            $requestsRequestPositionsIdField
        );

        $this->createIndex(
            'picking_profile_users_clients_profile_id_i',
             $requestPositionUsersTable,
            'clients_profile_id'
        );
        
        $this->addForeignKey(
            'picking_request_position_users_user_id_fk',
            $requestPositionUsersTable,
            'user_id',
            $usersTable,
            $usersIdField
        );
        
        $this->createIndex(
            'picking_request_position_users_user_id_i',
             $requestPositionUsersTable,
            'user_id'
        );
        */
    }

    public function safeDown()
    {
        $profileUsersTable = '{{%picking_profile_users%}}';
        $requestPositionUsersTable = '{{%picking_request_position_users%}}';
        
        $this->dropTable($profileUsersTable);
        $this->dropTable($requestPositionUsersTable);
    }
}
