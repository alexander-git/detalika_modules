<?php

namespace detalika\auth\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

class ClientTypeProcessingScriptAsset extends AssetBundle
{
    public $sourcePath = '@detalika/auth/assets/clientTypeProcessingScript';
    
    public $css = [
        
    ];
    
    public $js = [
        'ClientTypeProcessingScript.js',
    ];
    
    public $depends = [
        JuiAsset::class,
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];
    
}
