<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 1/9/17
 * Time: 2:44 PM
 */

namespace detalika\clients;

use yii\filters\AccessControl;
use yii\web\Application;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'detalika\clients\controllers';

    public $adminRole = 'admin';
    public function init()
    {
        parent::init();
        $container = \Yii::$container;
        if (!$container->has(OuterDependenciesInterface::class)) {
            $container->set(OuterDependenciesInterface::class, (new OuterDependenciesDefault()));
        }
    }
}