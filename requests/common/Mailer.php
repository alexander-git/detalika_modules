<?php

namespace detalika\requests\common;

use Yii;
use detalika\requests\helpers\ModuleHelper;

class Mailer 
{
    private static $MAILS_FOLDER = '@detalika/requests/mails';
    
    /**
     * @param string $toEmail
     * @param integer $requestId
     * @return type
     */
    public function sendRequestCreatedEmail(
        $toEmail, 
        $requestId,
        $clientName = null,
        $clientEmail = null,
        $clientPhone = null
    ) {
        $module = ModuleHelper::getModule();
        $moduleId = $module->id;
        $fromEmail = $module->serviceFromEmail;
        
        $requestUrl = Yii::$app->urlManager->createAbsoluteUrl([
            $moduleId. '/request/update', 
            'id' => $requestId,
        ]);
        
        $subject = "Создан новый запрос №$requestId";
        $viewPath = self::$MAILS_FOLDER.'/requestCreated';
        
        $success = Yii::$app->mailer->compose($viewPath, [
                'requestId' => $requestId,
                'requestUrl' => $requestUrl,
                'clientName' => $clientName,
                'clientEmail' => $clientEmail,
                'clientPhone' => $clientPhone,
            ])
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setSubject($subject)
            ->send();


        return $success;
    }
    
    public function sendRequestProcessedEmail($toEmail, $requestId)
    {
        $module = ModuleHelper::getModule();
        $moduleId = $module->id;
        $fromEmail = $module->serviceFromEmail;
        
        $requestUrl = Yii::$app->urlManager->createAbsoluteUrl([
            $moduleId. '/user/request/update', 
            'id' => $requestId,
        ]);
        
        $subject = "Ваш запрос №$requestId обработан";
        $viewPath = self::$MAILS_FOLDER.'/requestProcessed';
        
        $success = Yii::$app->mailer->compose($viewPath, [
                'requestId' => $requestId,
                'requestUrl' => $requestUrl,
            ])
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setSubject($subject)
            ->send();


        return $success;
    }
    
}