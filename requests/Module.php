<?php

namespace detalika\requests;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'detalika\requests\controllers';
 
    public $isCreateForeignKeyToCarsMarksTable = true;
    public $isCreateForeignKeyToCarsModelsTable = true;
    public $isCreateForeignKeyToCarsModificationsTable  = true;
    public $isCreateForeignKeyToCarsModificationsEnginesTable  = true;
    public $isCreateForeignKeyToProfilesTable  = true;
    public $isCreateForeignKeyToUsersTable  = true;
    public $isCreateForeignKeyToGoodsTable  = true;
    public $isCreateForeignKeyToDetailsArticlesTable  = true;
    public $isCreateForeignKeyDeliveryPartnersTable  = true; 
    
    public $serviceFromEmail = 'admin@example.com';
    
    public function init()
    {
        parent::init();
        $container = \Yii::$container;
        if (!$container->has(OuterDependenciesInterface::class)) {
            $container->set(OuterDependenciesInterface::class, (new OuterDependenciesDefault()));
        }
    }
}