<?php

namespace detalika\picking\widgets;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

use detalika\picking\widgets\assets\PickingButtonAsset;


class PickingButton extends Widget
{
    const BUTTON_TEXT_ON = 'Подбор идёт';
    const BUTTON_TEXT_OFF = 'Начать подбор';
    const CSS_CLASSESS_ON = 'btn btn-success';
    const CSS_CLASSESS_OFF = 'btn btn-default';
    
    public $isPickingOn = false;
    /**
     * Используется для того чтобы отличать кнопки разного типа на одной 
     * странице, так как кнопки посылают события друг-другу.
     * Например, для отличия кнопок подбора для клиента от кнопок подбора
     * позиции запроса. 
     * @var string  
     */
    public $type = 'default';
    public $startPickingUrl;
    public $stopPickingUrl;
    
    public function init()
    {
        parent::init();
        if (empty($this->id)) {
            throw new InvalidConfigException('Нужно установить id.');
        }
        if (empty($this->startPickingUrl)) {
            throw new InvalidConfigException('Нужно установить startPickingUrl.');
        } 
        if (empty($this->stopPickingUrl)) {
            throw new InvalidConfigException('Нужно установить stopPickingUrl.');
        } 
    }
    
    public function run()
    {
        // Id должно быть задано при инициализации виджета.
        $buttonId = $this->id; 

        if (is_array($this->startPickingUrl)) {
            $startPickingUrl = Url::to($this->startPickingUrl);
        } else {
            $startPickingUrl = $this->startPickingUrl;
        }
        
        if (is_array($this->stopPickingUrl)) {
            $stopPickingUrl = Url::to($this->stopPickingUrl);
        } else {
            $stopPickingUrl = $this->stopPickingUrl;
        }
        
        if ($this->isPickingOn) {
            $buttonText = self::BUTTON_TEXT_ON;
            $cssClass =  self::CSS_CLASSESS_ON;
        } else {
            $buttonText =  self::BUTTON_TEXT_OFF;
            $cssClass = self::CSS_CLASSESS_OFF; 
        }
        
        $html = Html::button($buttonText, [
            'class' => $cssClass,
            'id' => $buttonId,
        ]);
        
        $view = $this->view;
        PickingButtonAsset::register($view);
        
        $config = Json::htmlEncode([
            'buttonId' => $buttonId,
            'startPickingUrl' => $startPickingUrl,
            'stopPickingUrl' => $stopPickingUrl,
            'isPickingOn' => $this->isPickingOn,
            'type' => $this->type
        ]);
        
$jsCode = <<<JS
    PickingButtonScript.run($config);
JS;
        $view->registerJs($jsCode, View::POS_READY);
        
        return $html;
    }
}