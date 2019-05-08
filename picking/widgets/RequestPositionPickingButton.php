<?php

namespace detalika\picking\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use detalika\picking\helpers\OuterDependenciesHelper;

class RequestPositionPickingButton extends Widget
{
    use UserIdTrait;
    
    public $requestPositionId;
    public $isPickingOn = false;

    public function init()
    {
        parent::init();
        if (empty($this->requestPositionId)) {
            throw new InvalidConfigException('Нужно установить clientProfileId.');
        }
        if (empty($this->userId)) {
            throw new InvalidConfigException('Нужно установить userId.');
        } 
    }
    
    public function run()
    {
        $dependencies = OuterDependenciesHelper::getOuterDependencies();
        $pickingModuleId = $dependencies->getModuleId();
        
        $userId = $this->getUserId();
        
        $startPickingRoute = [
            '/' . $pickingModuleId . '/picking/request-position-start-picking', 
            'requestPositionId' => $this->requestPositionId,
            'userId' => $userId,
        ];
        
        $stopPickingRoute = [
             '/' . $pickingModuleId . '/picking/request-position-stop-picking', 
            'requestPositionId' => $this->requestPositionId,
            'userId' => $userId,  
        ];
        
        return PickingButton::widget([
            'isPickingOn' => $this->isPickingOn,
            'startPickingUrl' => Url::to($startPickingRoute),
            'stopPickingUrl' => Url::to($stopPickingRoute),
            'id' => 'requestPosition-' . $this->requestPositionId,
            'type' => 'requestPosition'
        ]);
    }
}