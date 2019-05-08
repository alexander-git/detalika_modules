<?php

namespace detalika\clients\models;

use yii\helpers\ArrayHelper;

use kartik\detail\DetailView;
use execut\crudFields\Behavior;

use detalika\clients\models\base\Type as BaseType;

class Type extends BaseType
{
    protected static $_individualId = null;
    public function __toString() 
    {
        return $this->name;
    }
    
    public static function findByPk($id)
    {
        return Type::findOne(['id' => $id]);
    }

    public static function findIndividualId() {
        if (self::$_individualId === null) {
            self::$_individualId = self::find()->isIndividual()->select('id')->createCommand()->queryScalar();
        }

        return self::$_individualId;
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['fields'] = [
            'class' => Behavior::className(),
            'fields' => [
                'name' => [
                    'attribute' => 'name',
                ],
                'type' => [], // Заглушка. Будет переопределён в getFormFields.
            ],
        ];

        return $behaviors;
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