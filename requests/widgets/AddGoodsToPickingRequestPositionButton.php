<?php

namespace detalika\requests\widgets;

use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

use detalika\requests\helpers\OuterDependenciesTrait;
use detalika\requests\controllers\RequestsApiController;
use detalika\requests\models\base\RequestPosition;
use detalika\requests\widgets\assets\AddGoodsToPickingRequestPositionButtonAsset;


class AddGoodsToPickingRequestPositionButton extends Widget
{
    use OuterDependenciesTrait;
  
    const STATE_ADD = 'add';
    const STATE_REMOVE = 'remove';
    
    public $buttonTextAdd = 'Добавить';
    public $buttonTextRemove = 'Удалить';
    const CSS_CLASSESS_ADD = ['btn','btn-success'];
    const CSS_CLASSESS_REMOVE = ['btn', 'btn-danger'];
 
    public static $autoIdPrefix = 'addGoodsToPickingRequestPositionButton-';
    public static $positionCache = [];
    
    /**
     * Если начальное состояние кнопки не задано, оно будет определено
     * автоматически. При этом потребуются запросы к базе данных.
     * @var string 
     */
    public $state = null;
    public $goodIds;

    public $options = [];
    
    public function init()
    {
        parent::init();
        if (
            empty($this->goodIds) ||
            (is_array($this->goodIds) && count($this->goodIds) === 0)
        ) {
            throw new InvalidConfigException('Нужно установить goodIds.');
        }
    }
    
    public function run()
    {
        if (!is_array($this->goodIds)) {
            $this->goodIds = [$this->goodIds];
        }

        $buttonId = $this->getId(); 

        $dependencies = self::getOuterDependenciesStatic(); 
        $requestsModuleId = $dependencies->getModuleId();
        $addGoodsUrl = Url::to(['/' . $requestsModuleId .'/requests-api/add-goods-to-picking-request-position']);
        $removeGoodsUrl = Url::to(['/' . $requestsModuleId .'/requests-api/remove-goods-from-picking-request-position']);
        
        if ($this->state === null) {
            $this->state = $this->getButtonInitialState();
        }
        
        if ($this->state === self::STATE_ADD) {
            $buttonText = $this->buttonTextAdd;
            $cssClasses =  self::CSS_CLASSESS_ADD;
        } else {
            $buttonText = $this->buttonTextRemove;
            $cssClasses = self::CSS_CLASSESS_REMOVE;
        }

        $cssClasses []= 'add-to-request';

        $options = ArrayHelper::merge([
            'class' => $cssClasses,
            'id' => $buttonId,
        ], $this->options);
        
        $html = Html::button($buttonText, $options);
        
        $view = $this->view;
        AddGoodsToPickingRequestPositionButtonAsset::register($view);
        $buttonAddText = $this->buttonTextAdd;
        $buttonRemoveText = $this->buttonTextRemove;
        
        $config = Json::htmlEncode([
            'buttonId' => $buttonId,
            'state' => $this->state,
            'goodIds' => $this->goodIds,
            'addGoodsUrl' => $addGoodsUrl,
            'removeGoodsUrl' => $removeGoodsUrl,
            'buttonTextAdd' => $buttonAddText,
            'buttonTextRemove' => $buttonRemoveText,
        ]);
        
$jsCode = <<<JS
    AddGoodsToPickingRequestPositionButtonScript.run($config);
JS;
        $view->registerJs($jsCode, View::POS_READY);
        
        return $html;
    }
    
    private function getButtonInitialState()
    {
        $requestPositionId = RequestsApiController::getPickingRequestPositionIdForCurrentUser();
        if ($requestPositionId === null) {
            return self::STATE_ADD;
        }

        if (empty(self::$positionCache[$requestPositionId])) {
            $pickingRequestPosition = self::$positionCache[$requestPositionId] = RequestPosition::find()
                ->with('children')
                ->andWhere(['id' => $requestPositionId])->one();
        } else {
            $pickingRequestPosition = self::$positionCache[$requestPositionId];
        }

        $children = $pickingRequestPosition->children;
        $existGoodIds = [];
        foreach ($children as $child) {
            if (!empty($child->goods_good_id)) {
                $existGoodIds []= (int) $child->goods_good_id;
            }
        }
        
        // Выясним есть ли товры из настройки виджета в предках позиции 
        // находящейся в "подборе".
        $haveAtLeastOne = false;
        $haveAll = true;
        foreach ($this->goodIds as $goodId) {
            if (in_array((int) $goodId, $existGoodIds)) {
                $haveAtLeastOne = true;
            } else {
                $haveAll = false;
            }
        }
        
        if ($haveAll) {
            // У позиции есть все предки со всеми товарами - ставим 
            // кнопку на удаление.
            return self::STATE_REMOVE;
        }
        
        if ($haveAtLeastOne && !$haveAll) {
            //  У позиции есть все предки с товарами частично - ставим 
            //  кнопку на удаление.
            return self::STATE_REMOVE;
        }
        
        if (!$haveAtLeastOne) {
            //  У позиции есть нет ни одного предка с товаром из 
            //  списка - ставим  кнопку на добавление.
            return self::STATE_ADD;
        }
        
        // Сюда код не должен доходить.
        throw new \LogicException();
    }
}