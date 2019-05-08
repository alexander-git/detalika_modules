<?php

namespace detalika\picking\assets;

use yii\web\AssetBundle;
use yii\web\View;

class BackendAsset extends AssetBundle
{
    
    public $sourcePath = '@detalika/picking/assets/backend';
        
    public $css = [
        
    ];
    
    public $js = [
        'Backend.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];   
}