<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Личный кабинет';
$this->params['breadcrumbs'][] = $this->title;


$attributes = [];
$attributes []= [
    'label' => 'Тип Клиента',
    'attribute' => 'clientType.name',
];

if ($profile->isCarParkClientType || $profile->isCarServiceClientType) {
    $attributes []= 'company_name';
}


$attributes []= 'user.email';
$attributes []= 'phone';
$attributes []= 'city';
$attributes []= 'delivery_address';
    
?>
<h3><?= $profile->fullName ?></h3>

<p>
    <?= Html::a('Общая информация', ['/user/profile/show', 'id' => Yii::$app->user->identity->id], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Настройки', ['/auth/settings/profile'], ['class' => 'btn btn-primary']) ?>
</p>

<?= DetailView::widget([
    'model' => $profile,
    'attributes' => $attributes,
]) ?>

<p>
    <?= Html::a('Редактировать', ['/auth/settings/profile'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Сменить пароль', ['/auth/settings/password-change'], ['class' => 'btn btn-primary']) ?>
</p>
  