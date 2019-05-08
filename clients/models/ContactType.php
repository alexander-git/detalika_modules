<?php

namespace detalika\clients\models;

use yii\helpers\ArrayHelper;

use execut\crudFields\Behavior;
use kartik\detail\DetailView;

use detalika\clients\models\base\ContactType as BaseContactType;

class ContactType extends BaseContactType
{
    protected static $idsByTypes = [];
    public static function getIdByType($type) {
        if (!isset(self::$idsByTypes[$type])) {
            self::$idsByTypes[$type] = self::find()->andWhere(['type' => $type])->select('id')->createCommand()->queryScalar();
        }

        return self::$idsByTypes[$type];
    }

    public function __toString() 
    {
        return $this->name;
    }
    
    public static function findByPk($id)
    {
        return ContactType::findOne(['id' => $id]);
    }
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fields' => [
                'class' => Behavior::className(),
                'fields' => [
                    'name' => [
                        'attribute' => 'name',
                    ],
                    'type' => [], // Заглушка. Будет переопределён в getFormFields.
                ],
            ],
        ]); 
    }
    
    public function getFormFields()  
    {
        $fields = $this->getBehavior('fields')->getFormFields();
        
        $fields['type'] = [
            'attribute' => 'type',
            'value' => function($form, $widget) {
                return $widget->model->typeName;
            },
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'items'=> self::getTypesArray(),
        ];
        
        return $fields;
    }       
}