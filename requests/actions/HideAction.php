<?php

namespace detalika\requests\actions;

use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class HideAction extends Action
{
    public $modelClass;
    
    public $isNeedValidateModel = true;
    public $isNeedSetSuccessFlashMessage = true;
    public $idParamName = 'id';
   
    public function run()
    {
        $id = Yii::$app->request->get($this->idParamName);
        if ($id === null) {
            throw new BadRequestHttpException();
        }
        
        $model = $this->findModel($id);
        if ($model->visible) {
            $model->visible = false;
            if ($model->save($this->isNeedValidateModel)) {
                if ($this->isNeedSetSuccessFlashMessage) {
                    $flashMessage = 'Record #' . $model->id . ' is successfully deleted';
                    Yii::$app->session->setFlash('kv-detail-success', $flashMessage); 
                }
            }
        }
        
        return Yii::$app->response->redirect(Yii::$app->request->referrer);
    }
    
    private function findModel($id)
    {
        $modelClass = $this->modelClass;
        
        $model = $modelClass::find()
            ->where([$this->idParamName => $id])
            ->one();
        
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        
        return $model;
    }
}