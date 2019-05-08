<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/8/17
 * Time: 3:06 PM
 */

namespace detalika\delivery;


use yii\filters\AccessControl;

class Module extends \detalika\base\Module
{
    public $adminPermission = 'admin';
    public $translationCategoryPrefix = 'modules/delivery';
    
    public function init()
    {
        parent::init();
        $container = \Yii::$container;
        if (!$container->has(OuterDependenciesInterface::class)) {
            $container->set(OuterDependenciesInterface::class, (new OuterDependenciesDefault()));
        }
    }

    public function behaviors()
    {

        if (!(\yii::$app instanceof Application)) {
            return [];
        }

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['@'],
                        'matchCallback' => [$this, 'checkAccess'],
                    ]
                ],
            ],
        ];
    }

    public function checkAccess($rule, $action)
    {
        $user = \Yii::$app->user->identity;
        if (method_exists($user, 'getIsAdmin')) {
            return $user->getIsAdmin();
        }

        return  \Yii::$app->user->can($this->adminPermission);
    }
}