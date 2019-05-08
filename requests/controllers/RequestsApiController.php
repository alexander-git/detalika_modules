<?php

namespace detalika\requests\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use detalika\requests\common\AccessControlTools;
use detalika\requests\helpers\OuterDependenciesTrait;
use detalika\requests\models\base\RequestPosition;


class RequestsApiController extends Controller
{
    use OuterDependenciesTrait;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' =>  AccessControl::className(),
                'only' => [
                    'add-goods-to-request-position', 
                    'remove-goods-from-request-position', 
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'add-goods-to-picking-request-position', 
                            'remove-goods-from-picking-request-position',                        
                        ],
                        'matchCallback' => function($rule, $action) {
                            return AccessControlTools::pickingMatchCallback($rule, $action);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add-goods-to-picking-request-position' => ['post'],
                    'remove-goods-from-picking-request-position' => ['post'],
                ],
            ],                    
        ]);
    }
        
    public function actionAddGoodsToPickingRequestPosition()
    {
        $pickingRequestPositionId = self::getPickingRequestPositionIdForCurrentUser();
        if ($pickingRequestPositionId === null) {
            return $this->getPickingRequestPositionNotSelectedJsonResponse();
        }
        
        $post = Yii::$app->request->post();
        $goodIds = $post['goodIds'];
        
        if (RequestPosition::addGoodsToRequestPosition($pickingRequestPositionId, $goodIds)) {
            return $this->getSuccessJsonResponseForPickingRequestPositionActions($pickingRequestPositionId);
        } else {
            return $this->getUnsuccessJsonResponse();
        }
    }
    
    public function actionRemoveGoodsFromPickingRequestPosition()
    {        
        $pickingRequestPositionId = self::getPickingRequestPositionIdForCurrentUser();
        if ($pickingRequestPositionId === null) {
            return $this->getPickingRequestPositionNotSelectedJsonResponse();
        }
     
        $post = Yii::$app->request->post();
        $goodIds = $post['goodIds'];
        
        if (RequestPosition::removeGoodsFromRequestPosition($pickingRequestPositionId, $goodIds)) {
            return $this->getSuccessJsonResponseForPickingRequestPositionActions($pickingRequestPositionId);
        } else {
            return $this->getUnsuccessJsonResponse();
        }
    }
    
    /**
    * Используется также в виджете AddGoodToPickingRequestPositionButton. 
    * @return integer
    */ 
    public static function getPickingRequestPositionIdForCurrentUser()
    {
        if (Yii::$app->user === null) {
            return null;
        }
        
        $userId = Yii::$app->user->id;
        
        $dependencies = self::getOuterDependenciesStatic(); 
        return $dependencies->getPickingRequestPositionIdByPickierId($userId);
    }

    
    private function getSuccessJsonResponseForPickingRequestPositionActions($pickingRequestPositionId)
    {
        $requestPosition = RequestPosition::findOne(['id' => $pickingRequestPositionId]);
        if ($requestPosition === null) {
            throw new \Exception();
        }
        
        // Используется для обновления соостояния других кнопок на странице.
        $childGoodIds = [];
        foreach ($requestPosition->children as $child) {
            if (!empty($child->goods_good_id)) {
                $childGoodIds []= (int) $child->goods_good_id;
            }
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
            'childGoodIds' => $childGoodIds,
        ];
    }
        
    private function getUnsuccessJsonResponse()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => false,
        ];
    }
        
    private function getPickingRequestPositionNotSelectedJsonResponse()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => false,
            'errorMessage' => 'Не выбрана позиция запроса для подбора',
        ];
    }
}
