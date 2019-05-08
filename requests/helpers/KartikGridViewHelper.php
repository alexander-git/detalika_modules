<?php

namespace detalika\requests\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class KartikGridViewHelper
{
    private function __construct()
    {
        
    }
    
    public static function getUpdateButtonHtml($url)
    {
        // Взято из  kartik\grid\ActionColumn.
        $options = [];
        $title = Yii::t('kvgrid', 'Update');
        $icon = '<span class="glyphicon glyphicon-pencil"></span>';
        $label = ArrayHelper::remove($options, 'label', $icon);
        $options = array_replace_recursive(['title' => $title, 'data-pjax' => '0'], $options);

        return Html::a($label, $url, $options);
    }
    
    public static function getDeleteButtonHtml($url)
    {
        // Частично взято из  kartik\grid\ActionColumn.
        $options = [];
        $title = Yii::t('kvgrid', 'Delete');
        $icon = '<span class="glyphicon glyphicon-trash"></span>';
        $label = ArrayHelper::remove($options, 'label', $icon);
        $msg = ArrayHelper::remove($options, 'message', Yii::t('kvgrid', 'Are you sure to delete this item?'));
        $defaults = [
            'title' => $title, 
            'data-pjax' => 'false',
            'data-confirm' => $msg,
        ];
        $options = array_replace_recursive($defaults, $options);
    
        return Html::a($label, $url, $options);
    }
    
    public static function getListButton($url, $title = '', $targetBlank = true)
    {
        $options = [];
        if ($targetBlank) {
            $options['target'] = '_blank';
        }
           
        $icon = '<span class="glyphicon glyphicon-th-list"></span>';
        $options = array_replace_recursive(['title' => $title, 'data-pjax' => '0'], $options);
        
        return Html::a($icon, $url, $options);
    }
    
    
}