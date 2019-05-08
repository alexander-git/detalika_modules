<?php

namespace detalika\picking;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'detalika\picking\controllers';
 
    public function init()
    {
        parent::init();
        
        $container = \Yii::$container;
        if (!$container->has(OuterDependenciesInterface::class)) {
            $container->set(OuterDependenciesInterface::class, OuterDependenciesDefault::class);
        }
    }
}