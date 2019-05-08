<?php
class m170109_115217_baseTables extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $i->table('clients_types')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
        ]));

        $profilesTable = $i->table('clients_profiles')->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->defaultValue('true')->notNull(),
            'name' => $this->string(),
            'company_name' => $this->string(),
            'patronymic' => $this->string(),
            'surname' => $this->string(),
            'city' => $this->string(),
            'delivery_address' => $this->string(),
            'comments' => $this->string(),
        ]))
            ->addForeignColumn('clients_types');

        $i->table('clients_contacts_types')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
        ]));

        $i->table('clients_contacts')->create(array_merge($this->defaultColumns(), [
            'value' => $this->string()->notNull(),
            'is_main' => $this->boolean()->notNull()->defaultValue('false'),
        ]))
            ->addForeignColumn('clients_profiles')
            ->addForeignColumn('clients_contacts_types');

        $i->table('clients_cards_types')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
        ]));

        $i->table('clients_cards')->create(array_merge($this->defaultColumns(), [
            'name' => $this->string()->notNull(),
        ]))
            ->addForeignColumn('clients_profiles')
            ->addForeignColumn('clients_cards_types');

        $isCreateOtherTables = false;
        if ($isCreateOtherTables) {
            $i->table('sources')->create(array_merge($this->defaultColumns(), [
                'name' => $this->string()->notNull(),
            ]));

            $i->table('shops')->create(array_merge($this->defaultColumns(), [
                'name' => $this->string()->notNull(),
            ]));
        }

        $profilesTable->addForeignColumn('sources');
        $profilesTable->addForeignColumn('shops');
    }
}