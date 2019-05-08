<?php

namespace detalika\picking\widgets;

use yii\base\Widget;
use yii\helpers\Url;

use detalika\picking\helpers\OuterDependenciesHelper;

class ProfilePickingButton extends Widget
{
    use UserIdTrait;
    
    public $clientProfileId;
    public $isPickingOn = false;

    public function init()
    {
        parent::init();
        if (empty($this->clientProfileId)) {
            throw new InvalidConfigException('Нужно установить clientProfileId.');
        }
    }
    
    public function run()
    {
        $dependencies = OuterDependenciesHelper::getOuterDependencies();
        $pickingModuleId = $dependencies->getModuleId();
        
        $userId = $this->getUserId();
        
        $startPickingRoute = [
            '/'. $pickingModuleId . '/picking/profile-start-picking', 
            'clientProfileId' => $this->clientProfileId,
            'userId' => $userId,
        ];
        
        $stopPickingRoute = [
            '/'. $pickingModuleId . '/picking/profile-stop-picking', 
            'clientProfileId' => $this->clientProfileId,
            'userId' => $userId,  
        ];
        
        return PickingButton::widget([
            'isPickingOn' => $this->isPickingOn,
            'startPickingUrl' => Url::to($startPickingRoute),
            'stopPickingUrl' => Url::to($stopPickingRoute),
            'id' => 'profile-' . $this->clientProfileId,
            'type' => 'profile'
        ]);
    }
}