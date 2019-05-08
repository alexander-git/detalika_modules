<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 1/9/17
 * Time: 4:55 PM
 */

namespace detalika\clients;


use yii\web\Controller;

class CrudController extends Controller
{
    public function init() {
        parent::init();
        if (\Yii::$app->request->getIsAjax()) {
            $this->layout = false;
        }
    }

    public function getPost($key = null, $defaultValue = null) {
        return \Yii::$app->request->post($key, $defaultValue);
    }

    public function getFiles($key = null, $defaultValue = null) {
        if ($key !== null && isset($_FILES[$key])) {
            return $_FILES[$key];
        } else if (empty($_FILES)) {
            return $defaultValue;
        }

        return $_FILES;
    }

    public function getGet($key = null, $defaultValue = null) {
        if (is_array($key)) {
            $result = [];
            foreach ($key as $paramKey) {
                if (is_array($defaultValue)) {
                    if (isset($defaultValue[$paramKey])) {
                        $currentDefaultValue = $defaultValue;
                    } else {
                        $currentDefaultValue = null;
                    }
                } else {
                    $currentDefaultValue = $defaultValue;
                }

                $result[$paramKey] = \Yii::$app->request->get($paramKey, $currentDefaultValue);
            }

            return $result;
        }
        return \Yii::$app->request->get($key, $defaultValue);
    }

    public function isAjax() {
        return \yii::$app->request->isAjax;
    }

    public function isPjax() {
        return \yii::$app->request->isPjax;
    }

    public function setResponseFormat($format) {
        \yii::$app->response->format = $format;
    }

}