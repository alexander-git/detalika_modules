<?php

namespace detalika\requests\actions;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class MassCreateAction extends Action
{
    /**
     * Модель должна иметь методы getFormFields() и save().
     * @var \yii\base\Model 
     */
    public $formModelClass;
    public $redirectUrl = null;
    
    public $viewPath = '@detalika/requests/actions/views/massCreate';
    
    public function run()
    {
        $formModelClass = $this->formModelClass;
        $model = new $formModelClass();
                
        $request = Yii::$app->request;
        $post = $request->post();
        
        if ($request->isPost && isset($post['ajax']) && $post['ajax'] !== null) {
            $model->load($post);   
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        
        if ($model->load($post)) {
            if ($model->validate() && $model->save()) {
                $redirectUrl = $this->getRedirectUrl($model);
                return Yii::$app->response->redirect($redirectUrl);
            } 
        }
        
        return $this->controller->render($this->viewPath, [
            'model' => $model
        ]);
    }
    
    
    private function getRedirectUrl($model)
    {
        if ($this->redirectUrl === null) {
            return Yii::$app->request->referrer;
        }
        
        if (is_string($this->redirectUrl)) {
            return $this->redirectUrl;
        }
        
        if (is_array($this->redirectUrl)) {
            return Url::to($this->redirectUrl);
        }
        
        if (is_callable($this->redirectUrl)) {
            return call_user_func($this->redirectUrl, $model);
        }
    }
}