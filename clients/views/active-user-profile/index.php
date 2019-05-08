<?php
$profileContent = \detalika\clients\widgets\ActiveUserProfileEdit::widget()
    . \detalika\auth\widgets\PasswordChange::widget();
$ordersContent = '';
echo $profileContent;