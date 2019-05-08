<?php
use \execut\yii\migration\Inverter;
use \execut\yii\migration\Migration;
class m170602_171101_addTypeColumnToRequestStatusTable extends Migration
{
    public function initInverter(Inverter $i)
    {
        $requestStatusTable = 'requests_request_statuses';
        $this->table($requestStatusTable)
            ->addColumn('type', 'string')
            ->update([
                'type' => 'processed',
            ])
            ->alterColumnSetNotNull('type');
    }
}
