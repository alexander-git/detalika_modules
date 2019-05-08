<?php

namespace detalika\delivery\helpers;

use yii\web\JsExpression;

class Select2Helper 
{
    public static function getStandartAjaxDataJs()
    {
        return (new JsExpression(<<<JS
function(params) {
    return {
        'term' : params.term,  
    };
}
JS
        ));    
    }

    private function __construct()
    {
        
    }
}