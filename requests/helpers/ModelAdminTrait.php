<?php

namespace detalika\requests\helpers;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use kartik\grid\BooleanColumn;
use kartik\detail\DetailView;

use detalika\requests\helpers\Select2Helper;

trait ModelAdminTrait 
{
    protected function getDateColumn(
        $attribute, 
        $dateFormat = 'Y-m-d', 
        $dateTimeFormatYii = 'php:Y-m-d H:i:s', 
        $datesSeparator = ' - '
    ) {
        return [
            'attribute' => $attribute,
            'format' => ['date', $dateTimeFormatYii],
            'filterType' => GridView::FILTER_DATE_RANGE, 
            'filterWidgetOptions' => $this->getDataRangeFilterWidgetOptions($dateFormat, $datesSeparator), 
        ];
    }
        
    protected function getDataRangeFilterWidgetOptions(
        $dateFormat = 'Y-m-d', 
        $datesSeparator = ' - '
    ) {
        return [
            'model' => $this,
            'presetDropdown' => false,
            'convertFormat' => true,
            'pluginOptions'=> [                                          
                'locale' => [
                    'format' => $dateFormat,
                    'separator' => $datesSeparator,
                ],
                'opens'=> 'left'
            ]
        ];
    }
    
    protected function addDateIntervalConditionsToField(
        $query, 
        $fieldName,
        $dateFormat = 'Y-m-d', 
        $datesSeparator = ' - '
    ) {
        $t = self::tableName(); 
        if (
            !empty($this->$fieldName) && 
            strpos($this->$fieldName, $datesSeparator) !== false
        ) {
            $timeFormat = 'H:i:s';
            $dateTimeFormat = $dateFormat .' '. $timeFormat;
            
            list($from, $to) = explode($datesSeparator, $this->$fieldName);
            $from = $from . ' 00:00:00';
            $to = $to . ' 23:59:59';
            $fromUtc = DateTimeHelper::convertToUtc($from, $dateTimeFormat);
            $toUtc = DateTimeHelper::convertToUtc($to, $dateTimeFormat);

            $query->andFilterWhere(['>=', $t . '.' . $fieldName, $fromUtc])
                ->andFilterWhere(['<=', $t . '.' . $fieldName, $toUtc]); 
        }
    
        return $query;
    }
    
    protected function getBooleanColumn($attribute)
    {
        return [
            'attribute' => $attribute,
            'class' => BooleanColumn::className(),
            'trueLabel' => 'Да',
            'falseLabel' => 'Нет',
        ];
    }
    
    protected function getBooleanField($attribute) 
    {
        return [
            'attribute' => $attribute,
            'type' => DetailView::INPUT_CHECKBOX,
            'format' => 'boolean',
            'options' => [
                'label' => null, 
            ],    
        ];
    }
    
    protected function getActionColumn()
    {
        return  [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
        ];
    }
    
    protected function getSelectField($attribute, $items, $valueFieldName = null)
    {
        if ($valueFieldName === null) {
            $valueFieldName = $attribute;
        }
        
        return [
            'attribute' => $attribute,
            'value' => function($form, $widget) use ($valueFieldName) {
                return ArrayHelper::getValue($widget->model, $valueFieldName);
            },
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'items'=> $items,
        ];
    }
    
    protected function getSelectColumn($attribute, $items, $valueFieldName = null) 
    {
        if ($valueFieldName === null) {
            $valueFieldName = $attribute;
        }
        
        return [
            'attribute' => $attribute,
            'value' => function($model, $key, $index, $colum) use ($valueFieldName) {
                return ArrayHelper::getValue($model, $valueFieldName);
            },
            'filter' => $items,
        ];
    }
    
    protected function getSelect2AjaxField($attribute, $url, $initialValue = '') 
    {
        return  [
            'attribute' => $attribute,
            'type' => DetailView::INPUT_SELECT2,
            'value' => $initialValue,
            'widgetOptions' => [
                'options' => [
                    'placeholder' => '',
                ],
                'initValueText' => $initialValue,
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => Select2Helper::getStandartAjaxDataJs(),
                    ],
                ],
            ],
        ]; 
    }
     
    protected function getSelect2AjaxColumn(
        $attribute, 
        $url, 
        $relationModelClass,
        $relationIdFieldName = 'id',
        $relationValueFieldName = 'name', 
        $modelFieldName = null,
        $select2Placeholder = ''
    ) {  
        if (!empty($this->$attribute)) {  
            $currentValue = $this->$attribute;
            
            if (is_array($currentValue)) {
                $models = $relationModelClass::find()
                    ->andWhere(['in', $relationIdFieldName, $currentValue])
                    ->all();
            } else {
                $models = $relationModelClass::find()
                    ->andWhere([$relationIdFieldName => $currentValue])
                    ->all();
            }
            
            $initValueText = ArrayHelper::map($models, $relationIdFieldName, $relationValueFieldName);
        } else {
            $initValueText  = '';
        }
        
        if ($modelFieldName === null) {
            $modelFieldName = $attribute;    
        }
        
        return [
            'attribute' => $attribute,
            'format' => 'raw',
            'value' => function($model, $key, $index, $column) use ($modelFieldName) {
                return $model->$modelFieldName;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => [
                    'placeholder' => $select2Placeholder,
                ],
                'initValueText' => $initValueText,
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => Select2Helper::getStandartAjaxDataJs(),
                    ],
                ],
            ],
        ];
    }
        
    protected function addWhereCondtionToMultipleSelectField(
        $query, 
        $fieldFullName, 
        $value
    ) {
        if (!empty($value)) {
            if (is_array($value)) {
                $query->andFilterWhere(['in', $fieldFullName, $value]);
            } else {
                $query->andFilgerWhere([$fieldFullName => $value]);
            }
        }
    }
    
    // Используется для правильного обновления при множественном вводе когда
    // удаляются все записи связной модели. Так как MultipleInput в этом случае
    // самостаяетльно ничего не отправляет, то SaveRelationsBehavior не поймет
    // что модель изменилась. Для корректной работы добавим постое поле с
    // именем связной модели, тогда будет отправлен пустой массив и 
    // SaveRelationsBehavior сделает то, что нужно.
    protected function getStubForMultipleInputCorrectWork($attributeName)
    {
        return Html::input('hidden', Html::getInputName($this, $attributeName));
    }
    
}