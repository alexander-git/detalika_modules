<?php

namespace detalika\delivery\helpers;

trait StandartAttributesTrait
{
    use ModelAdminTrait;
    
    protected function getVisibleColumn()
    {
        return $this->getBooleanColumn('visible');
    }
    
    protected function getVisibleField()
    {
        return $this->getBooleanField('visible');
    }
    
    protected function getCreatedColumn(
        $dateFormat = 'Y-m-d', 
        $dateTimeFormatYii = 'php:Y-m-d H:i:s', 
        $datesSeparator = ' - '
    ) {
        return $this->getDateColumn('created', $dateFormat, $dateTimeFormatYii, $datesSeparator);
    }
    
    protected function getUpdatedColumn(
        $dateFormat = 'Y-m-d', 
        $dateTimeFormatYii = 'php:Y-m-d H:i:s', 
        $datesSeparator = ' - '
    ) {
        return $this->getDateColumn('updated', $dateFormat, $dateTimeFormatYii, $datesSeparator);
    }
    
    protected function getCreatedField()
    {
        return [
            'attribute' => 'created',
            'displayOnly' => true,
            'visible' => !$this->isNewRecord,
        ];
    }
    
    protected function getUpdatedField()
    {
        return [
            'attribute' => 'updated',
            'displayOnly' => true,
            'visible' => !$this->isNewRecord,
        ];
    }
        
    protected function addDateIntervalConditionsToStandartAttributes(
        $query, 
        $dateFormat = 'Y-m-d', 
        $datesSeparator = ' - '
    ) {
        $this->addDateIntervalConditionsToField($query, 'created', $dateFormat, $datesSeparator);
        $this->addDateIntervalConditionsToField($query, 'upadated', $dateFormat, $datesSeparator);
        return $query;
    }
}

