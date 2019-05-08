<?php

use yii\db\Migration;

class m161227_170914_changeProfileTable extends Migration
{   
    public function safeUp()
    {
        $this->update('{{%profile%}}', [
            'name' => '',
        ]);
        $this->alterColumn('{{%profile%}}','name', 'SET NOT NULL');

        $stringColumns = [
            'surname' => true,
            'phone' => true,
            'city' => true,
            'delivery_address' => false,
            'car_service_name' => false,
        ];
        foreach ($stringColumns as $column => $isNotNull) {
            $this->addColumn('{{%profile%}}', $column, $this->string());
            $this->update('{{%profile%}}', [
                $column => '',
            ]);
            if ($isNotNull) {
                $this->alterColumn('{{%profile%}}', $column, 'SET NOT NULL');
            }
        }

        //TODO# или clienttype вместо client_type?
        $this->addColumn('{{%profile%}}', 'auth_client_type_id', $this->integer());

        
        $this->addForeignKey(
            'profile_auth_client_type_id_fk',
            '{{%profile%}}', 
            'auth_client_type_id', 
            '{{%auth_client_types%}}', 
            'id'
        );
        
        $this->createIndex(
            'profile_auth_client_type_id_i', 
            '{{%profile%}}', 
            'auth_client_type_id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('profile_auth_client_type_id_fk', '{{%profile%}}');
        $this->dropIndex('profile_auth_client_type_id_i', '{{%profile%}}');
        
        $this->dropColumn('{{%profile%}}', 'auth_client_type_id');
        $this->dropColumn('{{%profile%}}', 'car_service_name');
        $this->dropColumn('{{%profile%}}', 'delivery_address');
        $this->dropColumn('{{%profile%}}', 'city');
        $this->dropColumn('{{%profile%}}', 'phone');
        $this->dropColumn('{{%profile%}}', 'surname');
        $this->alterColumn('{{%profile%}}', 'name', 'string');
    }
    
}
