<?php

use yii\helpers\Html;

$phoneEmailParts = [];
if (!empty($clientPhone)) {
    $phoneEmailParts []= $clientPhone;
} 
if (!empty($cleintEmail)) {
    $phoneEmailParts [] = $clientEmail;
}

$phoneEmail = implode(' ', $phoneEmailParts);
if (!empty($phoneEmail)) {
    $phoneEmail = '(' . $phoneEmail . ')';
}

?>
 
Пользователь <?=$clientName?> <?=$phoneEmail ?> создал <?=Html::a("запрос №$requestId", $requestUrl)?>.