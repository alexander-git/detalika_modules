<?php


class m170228_090728_addRequestPositionStatuses extends \execut\yii\migration\Migration
{

    public function initInverter(\execut\yii\migration\Inverter $i)
    {        
        $i->table('requests_request_position_statuses')->create(array_merge($this->defaultColumns(), [
            'visible' => $this->boolean()->notNull()->defaultValue(true),
            'name' => $this->string()->notNull(),
        ]));
    }
}
