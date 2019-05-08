<?php

namespace detalika\picking\assets;

use yii\web\AssetBundle;
use yii\web\View;

class EventsAsset extends AssetBundle
{
    
    public $sourcePath = '@detalika/picking/assets/events';
        
    public $css = [
        
    ];
    
    public $js = [
        'Events.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    
    public $jsOptions = [
        'postions' => View::POS_HEAD,
    ];   
}