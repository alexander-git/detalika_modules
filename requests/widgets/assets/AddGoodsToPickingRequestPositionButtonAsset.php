<?php

namespace detalika\requests\widgets\assets;

use yii\web\View;
use yii\web\AssetBundle;
use detalika\requests\assets\BackendAsset;
use detalika\requests\assets\EventsAsset;

class AddGoodsToPickingRequestPositionButtonAsset extends AssetBundle
{
    public $sourcePath = '@detalika/requests/widgets/assets/addGoodsToPickingRequestPositionButton';
    
    public $css = [
 
    ];
    
    public $js = [
        'AddGoodsToPickingRequestPositionButton.js',
        'AddGoodsToPickingRequestPositionButtonBackend.js',
        'AddGoodsToPickingRequestPositionButtonScript.js',
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