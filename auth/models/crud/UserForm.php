<?php

namespace detalika\auth\models\crud;

class UserForm extends User 
{  
    public function getFormFields()  
    {
        return [
            [
                'displayOnly' => true,
                'attribute' => 'id',
            ],
            [
                'attribute' => 'email'
            ],
        ];
    }  
}
