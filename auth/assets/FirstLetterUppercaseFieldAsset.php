<?php

namespace detalika\auth\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\View;

class FirstLetterUppercaseFieldAsset extends AssetBundle
{
    public $sourcePath = '@detalika/auth/assets/firstLetterUppercaseField';
    
    public $css = [
        
    ];
    
    public $js = [
        'FirstLetterUppercaseField.js',
    ];
    
    public $depends = [
        JuiAsset::class,
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}
