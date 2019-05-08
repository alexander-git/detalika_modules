<?php

class m170201_102347_authModuleAdaptation extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $profiles = $i->table('clients_profiles');
        $profiles->addColumn('gravatar_email', $this->string())
            ->addForeignColumn('user')
            ->addColumn('gravatar_id', $this->string())
            ->addColumn('website', $this->string())
            ->addColumn('bio', $this->text())
            ->addColumn('timezone', $this->string(40))
//            ->addColumn('phone', $this->string())
//            ->update(['phone' => ''])
        ;
    }
}