<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/28/17
 * Time: 1:45 PM
 */

namespace detalika\picking\crudFields;


use detalika\clients\common\CurrentUser;
use detalika\picking\models\base\ProfileUser;
use detalika\picking\OuterDependenciesInterface;
use detalika\picking\PickingApi;
use detalika\picking\widgets\ProfilePickingButton;
use execut\crudFields\Plugin;

class ClientPickingButtonPlugin implements Plugin
{
    public function getFields()
    {
        /**
         * @var OuterDependenciesInterface $deps
         */
        $deps = \yii::$container->get(OuterDependenciesInterface::class);
        if (!$deps->canCurrentUserPicking()) {
            return [];
        }

        return [
            'pickingProfileUser' => [
                'column' => [
                    'attribute' => 'pickingProfileUser',
                    'label' => 'Подбор',
                    'format' => 'raw',
                    'value' => function($model, $key, $index, $column) {
                        return $this->renderPickingButton($model);
                    },
                ],
                'field' => function ($model) {
                    if ($model->isNewRecord) {
                        return false;
                    }

                    return [
                        'label' => 'Подбор',
                        'format' => 'raw',
//                        'displayOnly' => true,
                        'value' => function ($form, $grid) {
                            return $this->renderPickingButton($grid->model);
                        },
                    ];
                },
            ],
        ];
    }

    public function renderPickingButton($model)
    {
        $checkedProfileId = $model->id;

        $user = \yii::$app->user;
        if (!$user) {
            return false;
        }

        $userId = $user->id;
        if ($userId === null) {
            return false;
        }

        $isPickingOn = ProfileUser::find()
                ->andWhere([
                    'user_id' => $userId,
                    'clients_profile_id' => $checkedProfileId,
                ])->count() > 0;

        return ProfilePickingButton::widget([
            'isPickingOn' => $isPickingOn,
            'clientProfileId' => $model->id,
        ]);
    }

    public function getRelations() {
        return [];
    }

    public function rules() {
        return [];
    }
}