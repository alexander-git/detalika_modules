<?php

namespace detalika\picking\widgets\assets;

use yii\web\View;
use yii\web\AssetBundle;
use detalika\picking\assets\BackendAsset;
use detalika\picking\assets\EventsAsset;

class PickingButtonAsset extends AssetBundle
{
    public $sourcePath = '@detalika/picking/widgets/assets/pickingButton';
    
    public $css = [
 
    ];
    
    public $js = [
        'PickingButton.js',
        'PickingButtonBackend.js',
        'PickingButtonScript.js',
    ];
    
    public $depends = [        
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        BackendAsset::class,
        EventsAsset::class,
    ];
    
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}